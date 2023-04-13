var markers = [];
var map     = null;

$(document).ready(function() {
    initDateRangePicker();
    initDropDownListStatus();
    initDropDownListTeam();
    initDropDownListUser();
    initGridAttendance();
    initMap();
});

function initDropDownListItem()
{
    dropDownListItem = $("#dropDownListItem").kendoDropDownList({
        optionLabel: "-- Seleccione --",
        dataTextField: "text",
        dataValueField: "value",
        dataSource: window.cleaningItems,
        height: 400,
        change: changeFilter
    }).data("kendoDropDownList");
}

function initDropDownListStatus()
{
    dropDownListStatus = $("#dropDownListStatus").kendoDropDownList({
        optionLabel: "-- Seleccione --",
        dataTextField: "text",
        dataValueField: "value",
        dataSource: [{ text: "Trabajando", value: "WORKING"}, { text: "Disponible", value: "OUT"}],
        height: 400,
        change: changeFilter
    }).data("kendoDropDownList");
}

function changeFilter()
{
    gridAttendance.dataSource.read();
}

function initMap()
{
  var center = { lat: 9.9356284, lng: -84.1483645 };

  map = new google.maps.Map( document.getElementById('map'), { zoom: 9, center: center });
}

function initGridAttendance()
{
    gridAttendance = $("#gridAttendance").kendoGrid({
        dataSource: {
            transport: {
                read: {
                    url: "getDataAttendance",
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
            group: {
                field: "iduser", aggregates: [ { field: "punch_in", aggregate: "count" }, { field: "duration", aggregate: "sum" }] 
            },
            schema: {
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

                    case "group-footer":
                            row.cells[1].value = row.cells[1].value.replace(/(<([^>]+)>)/gi, "");
                            row.cells[5].value = row.cells[5].value.replace(/(<([^>]+)>)/gi, "");
                            break;

                        case "data":
                            row.cells[1].value = formatDate(row.cells[1].value);
                            row.cells[3].value = formatDate(row.cells[3].value);
                            row.cells[5].value = hhmmss(row.cells[5].value);
                            break;
                
                    default:
                        break;
                }
            }
        },
        toolbar: ["excel", "pdf"],
        excel: {
            fileName: "Reporte de asistencia.xlsx",
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
                field: "punch_in",
                title: "Entrada",
                width: "60px",
                template: "#=formatDate(punch_in)#",
                filterable: false,
                groupFooterTemplate: "<b>Asistencias: #=count#</b>",
                attributes: {
                    "class": "ticket-progress"
                },
            },
            {
                field: "start_location",
                title: "Lugar de entrada",
                width: "160px",
                filterable: false,
                attributes: {
                    "class": "ticket-progress"
                },
            },
            {
                field: "punch_out",
                title: "Salida",
                width: "60px",
                template: "#=formatDate(punch_out)#",
                filterable: false,
                attributes: {
                    "class": "ticket-pendint"
                },
            },
            {
                field: "end_location",
                title: "Lugar de salida",
                width: "160px",
                filterable: false,
                attributes: {
                    "class": "ticket-pendint"
                },
            },
            {
                field: "duration",
                title: "Duración (hrs)",
                width: "60px",
                template: "#=formatDuration(duration)#",
                groupFooterTemplate: "#=formatTotalDuration(sum)#",
                filterable: false,
                attributes: {
                    "class": "ticket-finished"
                },
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
    return (date == null ? "------------------" : moment(date).format('YY-MM-DD hh:mm A'));
}

function formatDuration(secs)
{
    return "<h5 style='font-weight:700; color:#2c2c2c;'>" + hhmmss(secs) + "</h5>";
}

function formatTotalDuration(secs)
{
    return "<h4 style='font-weight:700; color:#2c2c2c;'>" + hhmmss(secs) + "</h4>";
}

function hhmmss(secs)
{
    var minutes = Math.floor(secs / 60);
    secs = secs%60;
    var hours = Math.floor(minutes/60)
    minutes = minutes%60;
    return pad(hours) + ":" + pad(minutes) + ":" + pad(secs);
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

    if(dataItem.start_coordinates != null)
    {
        let start_coordinates = JSON.parse(dataItem.start_coordinates);
        let dataStart = { lat : start_coordinates.lat, lng : start_coordinates.long };
        addMarker(dataStart, "Entrada");
    }

    if(dataItem.end_coordinates != null)
    {
        let end_coordinates = JSON.parse(dataItem.end_coordinates);
        let dataStart = { lat : end_coordinates.lat, lng : end_coordinates.long };
        addMarker(dataStart, "Salida");
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