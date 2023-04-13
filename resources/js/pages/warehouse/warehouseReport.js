$(document).ready(function() {
    
    initDateRangePicker();
    initDropDownListSpotBranch();

    showGeneralData();
    initGridWarehouseReport();
});

function initGridWarehouseReport() {

    dataSourceWarehouseReport = new kendo.data.DataSource({
        transport: { 
            read: {
                url: "getWarehouseReport",
                type:"get",
                dataType:"json",
                data: function() {
                    return {
                        idspot : dropDownListSpot.value(),
                        start: $('#dateRangePicker').data('daterangepicker').startDate.format('YYYY-MM-DD'),
                        end: $('#dateRangePicker').data('daterangepicker').endDate.format('YYYY-MM-DD'),
                    };
                },
            }
        },
        schema: {
            model: {
                id:"id",
                fields: {
                    id: { type: "number", editable: false, nullable: true },
                    amount: { editable: true, field: "amount", type: "number" },
                    idspot: { editable: true, field: "idspot", type: "number" },
                    iditem: { editable: true, field: "iditem", type: "number" },
                },
            },
            total: "total",
            data: "data",
        },
        pageSize: 100,
        serverPaging: true,
        serverFiltering: true,
        serverSorting: true,
    });
    
    gridWareHouseReport = $("#gridWarehouseReport").kendoGrid({
        dataSource: dataSourceWarehouseReport,
        sortable:true,
        selectable: true,
        resizable: true,
        reorderable: true,
        filterable: true,
        selectable: "multiple cell",
        allowCopy: true,
        toolbar: ["excel"],
        messages: {
            commands: {
              excel: "Excel"
            }
        },
        excel: {
            fileName: "Warehouse Report.xlsx",
            override: true,
            filterable: true,
            title: "Excel"
        },
        pageable: {
            refresh: true,
            pageSizes: true,
            buttonCount: 5
        },
        height:"450px",
        columns: [
            {
                field: "idspot",
                title: locale("Spot"),
                values: global_spots,
                width: "150px",
                filterable: false
            },
            {
                field: "oc",
                title: locale("Code"),
                responsive: true,
                width: "90px",
                filterable: false
            },
            {
                field: "item.name",
                title: locale("Item"),
                template: "#= formatItem(item, amount, description)#",
                width: "160px",
                filterable: false
            },
            {
                field: "idpriority",
                title: "Prioridad",
                values: global_priorities,
                responsive: true,
                width: "130px",
                filterable: {
                    multi: true,
                    search: true
                }
            },
            {
                title: "Pendiente - Recibida",
                template: "#= calculateDiffDate(logs, 1, 2)# ",
                responsive: true,
                width: "180px"
            },
            {
                title: "Recibida - Generada",
                template: "#= calculateDiffDate(logs, 2, 4)#",
                responsive: true,
                width: "170px"
            },
            {
                title: "Generada - Finalizada",
                template: "#= calculateDiffDate(logs, 4, 5)#",
                responsive: true,
                width: "180px"
            }, 
        ]
    });
}


function formatItem(item, amount, description)
{
    var html = "<span span style='width:100%' class='badge badge-light text-dark'>" + description + "</span>" ;
    description != null ? description = html    : description = "";

    return "<span class=''><b>" + item.name + "</b>&nbsp;<span class='amount badge badge-success'>" + amount + "</span> <br>"+ description +"</span>"
}

function formatNoRecords()
{
    return '<br><span style="width:800px" class="badge badge-danger"> No hay resultados</span>'
}     

$("#exportEX").click(function(e) {
    var grid = $("#gridWarehouseReport").data("kendoGrid");
    grid.saveAsExcel();
});
$("#exportPDF").click(function(e) {
    var grid = $("#gridWarehouseReport").data("kendoGrid");
    grid.saveAsExcel();
});

function changeFilter()
{
    dataSourceWarehouseReport.read();
    showGeneralData();
}

function initDropDownListSpotBranch()
{
    dropDownListSpot = $("#dropDownListSpot").kendoDropDownList({
        optionLabel: "-- Seleccione --",
        dataTextField: "text",
        dataValueField: "value",
        dataSource: getUserBranches(),
        filter: "contains",
        height: 400,
        change: changeFilter
    }).data("kendoDropDownList");
}

   
function calculateDiffDate(logs, idstatus1, idstatus2)
{
    let log1 = logs.find(log => log.idstatus == idstatus1);
    let log2 = logs.find(log => log.idstatus == idstatus2);
    
    if(log1 != undefined && log2 != undefined)
    {
        let fecha1 = moment(log1.created_at);
        let fecha2 = moment(log2.created_at);

        let diff =  fecha2.diff(fecha1);
        var d  = moment.duration(diff);     

        return '<span style="width:150px" class="m-badge m-badge--success">'+ d.days() + ' días ' + d.hours() + ' hrs ' + d.minutes() + ' min' + '</span>'
     }
    else
    {
        return ''
    }
}

function showGeneralData() {
    let request = callAjax(
        'getGeneralAverage', 
        'POST', {
            idspot : dropDownListSpot.value(),
            start: $('#dateRangePicker').data('daterangepicker').startDate.format('YYYY-MM-DD'),
            end: $('#dateRangePicker').data('daterangepicker').endDate.format('YYYY-MM-DD'),
        }, 
        false
    );

    request.done(function(result) {

        formatData(result);
    }).fail(function( jqXHR, status ) {
      console.log(jqXHR);
    });
}

function formatData(respuesta)
{
    $("#total").html( "<h5>"+ respuesta['total'] +" Tickets</h5>");  
    $("#average_days").html( "<h5>"+ respuesta['average_days'] +" días</h5>");   
 
    $("#pending-received").html( "<h5>"+ respuesta['pending-received'] +" días</h5>");   
    $("#received-generated").html( "<h5>"+ respuesta['received-generated'] +" días</h5>");   
    $("#generated-finish").html( "<h5>"+ respuesta['generated-finish'] +" días</h5>");   
}