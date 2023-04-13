var markers = [];
var map     = null;

$(document).ready(function() {
    initDateRangePicker(6);
    initDropDownListStatus();
    initDropDownListTeam();
    initDropDownListUser();
    initGridLocation();
    initMap();
});

function initDropDownListStatus()
{
    dropDownListStatus = $("#dropDownListStatus").kendoDropDownList({
        optionLabel: "-- Seleccione --",
        dataTextField: "text",
        dataValueField: "value",
        dataSource: global_statuses,
        height: 400,
        change: changeFilter
    }).data("kendoDropDownList");
}

function changeFilter()
{
    gridLocation.dataSource.read();
}

function initMap()
{
  var center = { lat: 9.9356284, lng: -84.1483645 };
  {lat: 10.1353676, lng: -85.4532947}

  map = new google.maps.Map( document.getElementById('map'), { zoom: 9, center: center });
}

function initGridLocation()
{
    gridLocation = $("#gridLocation").kendoGrid({
        dataSource: {
            transport: {
                read: {
                    url: "getDataLocation",
                    type: "get",
                    dataType: "json",
                    data: function() {
                        return {
                            start    : $('#dateRangePicker').data('daterangepicker').startDate.format('YYYY-MM-DD'),
                            end      : $('#dateRangePicker').data('daterangepicker').endDate.format('YYYY-MM-DD'),
                            idteam   : dropDownListTeam.value(),
                            idstatus : dropDownListStatus.value(),
                            iduser   : dropDownListUser.value()
                        };
                    },
                },
            },
            pageSize: 20,
            serverPaging: true,
            serverFiltering: true,
            serverSorting: true,
            group: {
                field: "iduser", aggregates: [ { field: "punch_in", aggregate: "count" }, { field: "duration", aggregate: "sum" }] 
            },
            schema: {
                total: "total",
                data: "data",
                model: {
                    id: "id",
                    fields: {
                        id: { type: "number", editable: false, nullable: true },
                    }
                }
            },
        },
        excelExport: function(e) {

            var sheet = e.workbook.sheets[0];

            sheet.columns.forEach(function(column){
                delete column.width;
                column.autoWidth = true;
            });
        
            for (var i = 1; i < sheet.rows.length; i++)
            {
                var row = sheet.rows[i];

                switch (row.type)
                {
                    
                    case "group-header":
                            row.cells[0].value = row.cells[0].value.replace(/(<([^>]+)>)/gi, "");
                            break;
                    
                        case "data":
                            row.cells[1].value = global_statuses.find((item) => { return item.value == row.cells[1].value; }).text;
                            row.cells[6].value = formatDate(row.cells[6].value);
                            break;
                
                    default:
                        break;
                }
            }
        },
        toolbar: ["excel", "pdf"],
        excel: {
            fileName: "Reporte de localizaciones.xlsx",
            filterable: false,
            allPages: true
        },
        pdf: {
            allPages: true,
            avoidLinks: true,
            paperSize: "A4",
            margin: { top: "2cm", left: "1cm", right: "1cm", bottom: "1cm" },
            landscape: true,
            repeatHeaders: true,
            scale: 0.8
        },
        editable: {
            mode: "popup"
        },
        height: "600px",
        groupable: false,
        reorderable: true,
        resizable: true,
        sortable: true,
        pageable: {
            refresh: true,
            pageSizes: true,
            buttonCount: 5,
        },
        filterable: true,
        noRecords: {
            template: "<div class='alert alert-danger mt-1 alert-validation-msg' role='alert'><i class='feather icon-info mr-1 align-middle'></i><span>No hay datos</span></div>"
        },
        dataBound: function(e) {

            var data = gridLocation.dataSource.data();

            $.each(data, function(i, row) {

                let tr = $('tr[data-uid="' + row.uid + '"] ');

                console.log(row);
                
                switch(row.idstatus)
                {
                    case 1:
                        tr.css("background-color", "rgb(234, 84, 85, 0.15)");
                        break;

                    case 2:
                        tr.css("background-color", "rgb(40, 199, 111, 0.15)");
                        break;

                    case 3:
                        tr.css("background-color", "rgb(255, 159, 67, 0.15)");
                        break;

                    case 4:
                        tr.css("background-color", "rgb(132, 127, 131, 0.37)");
                        break;
                }
            });
        },
        columns: [
            {
                field: "id",
                title: "ID",
                width: "25px",
                filterable: false,
                hidden: true,
            },
            {
                field: "iduser",
                title: "Usuario",
                width: "0px",
                groupHeaderTemplate: function(dataItem) {
                    console.log(dataItem);
                    return "<b>" + dataItem.value + "</b>";
                },
                filterable: false,
                hidden: true
            },
            {
                field: "idstatus",
                title: "Acción",
                width: "80px",
                template: function(dataItem) {
                    return "<p>" + global_statuses.find(o => o.value === dataItem.idstatus).text + "</p>";
                },
                filterable: false,
            },
            {
                field: "code",
                title: "OT",
                width: "80px",
                template: function(dataItem) {
                    return "<b>" + dataItem.code + "</b>";
                },
                filterable: false,
            },
            {
                field: "task",
                title: "Tarea",
                width: "150px",
                filterable: false,
            },
            {
                field: "spot",
                title: "Lugar",
                width: "120px",
                filterable: false,
            },
            {
                field: "location",
                title: "Ubicación",
                width: "250px",
                filterable: false,
                attributes: {
                    "class": "ticket-progress"
                },
            },
            {
                field: "created_at",
                title: "Fecha",
                width: "90px",
                filterable: false,
                template: "#=formatDate(created_at)#"
            },
            {
                command: [
                    { name: "custom2", text: " ", iconClass: "fad fa-map-marked-alt", click: setMarkers}
                ],
                title: " ",
                width: "45px"
            }
            
        ],
    }).data("kendoGrid");
}

function formatDate(date)
{
    console.log(date);
    return (date == null ? "------------------" : moment(date).format('YY-MM-DD hh:mm:ss A'));
}

function pad(num)
{
    return ("0" + num).slice(-2);
}

function setMarkers(e)
{
    e.preventDefault();
    
    deleteMarkers();

    map.setZoom(8);

    $("#modal-map").modal("show");

    let dataItem = this.dataItem($(e.currentTarget).closest("tr"));

    if(dataItem.coordinates != null)
    {
        //let start_coordinates = JSON.parse(dataItem.start_coordinates);
        let dataStart = { lat : dataItem.coordinates.lat, lng : dataItem.coordinates.long };
        addMarker(dataStart, "Entrada");
    }
}

function addMarker(location, title = "")
{
    const marker = new google.maps.Marker({
      position: location,
      map: map,
      title: title,
    });
    markers.push(marker);
}

function setMapOnAll(map)
{
    for (let i = 0; i < markers.length; i++)
    {
        markers[i].setMap(map);
    }
}

function clearMarkers()
{
    setMapOnAll(null);
}

function deleteMarkers()
{
    clearMarkers();
    markers = [];
}

function formatDate(date)
{
    return (date == null ? "------------------" : moment(date).format('YY-MM-DD hh:mm A'));
}