$(document).ready(function() {
    initDateRangePicker();
    initDropDownListSpot();
    initDropDownListTeam();
    initDropDownListUser();
    initDropDownListItem();
    initGridDuration();
    initGridDurationSpot();
});

function initDropDownListItem()
{
    dropDownListItem = $("#dropDownListItem").kendoDropDownList({
        optionLabel: "-- Seleccione --",
        dataTextField: "name",
        dataValueField: "id",
        dataSource: window.global_items,
        height: 400,
        change: changeFilter
    }).data("kendoDropDownList");
}

function changeFilter()
{
    gridDuration.dataSource.read();
    gridDurationSpot.dataSource.read();
}

function initGridDurationSpot()
{
    gridDurationSpot = $("#gridDurationSpot").kendoGrid({
        dataSource: {
            transport: {
                read: {
                    url: "getDataSpotDuration",
                    type: "get",
                    dataType: "json",
                    data: function() {
                        return {
                            start  : $('#dateRangePicker').data('daterangepicker').startDate.format('YYYY-MM-DD'),
                            end    : $('#dateRangePicker').data('daterangepicker').endDate.format('YYYY-MM-DD'),
                            idteam : dropDownListTeam.value(),
                            idspot : dropDownListSpot.value(),
                            iduser : dropDownListUser.value(),
                            iditem : dropDownListItem.value()
                        };
                    },
                },
            },
            pageSize: 20,
            group: {
                field: "idspot", aggregates: [ { field: "id", aggregate: "count" }, { field: "duration", aggregate: "sum" }] 
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

                console.log(row);

                switch (row.type)
                {
                    
                    case "group-header":
                            row.cells[0].value = row.cells[0].value.replace(/(<([^>]+)>)/gi, "");
                            break;

                    case "group-footer":
                            console.log(row);
                            row.cells[1].value = row.cells[1].value.replace(/(<([^>]+)>)/gi, "");
                            row.cells[7].value = row.cells[7].value.replace(/(<([^>]+)>)/gi, "");
                            break;

                        case "data":
                            
                            row.cells[4].value = formatDate(row.cells[4].value);
                            row.cells[5].value = formatDate(row.cells[5].value);
                            row.cells[6].value = formatDate(row.cells[6].value);
                            row.cells[7].value = hhmmss(row.cells[7].value);
                            break;
                
                    default:
                        break;
                }
            }
        },
        toolbar: ["excel", "pdf"],
        excel: {
            fileName: "Reporte de duraciones por spot.xlsx",
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
                field: "idspot",
                title: "Lugar",
                width: "0px",
                groupHeaderTemplate: function(dataItem) {
                    return "<b>" + dataItem.value + "</b>";
                },
                filterable: false,
                hidden: true
            },
            {
                field: "id",
                title: "Código",
                width: "50px",
                filterable: false,
                groupFooterTemplate: "<b>Tareas: #=count#</b>",
                attributes: {
                    "class": "ticket-progress"
                },
            },
            {
                field: "name",
                title: "Tarea",
                width: "120px",
                filterable: false,
                attributes: {
                    "class": "ticket-progress"
                },
            },
            {
                field: "users",
                title: "Responsables",
                width: "120px",
                filterable: false,
                attributes: {
                    "class": "ticket-progress"
                },
            },
            {
                field: "created_at",
                title: "Fecha",
                width: "80px",
                template: "#=formatDate(created_at)#",
                filterable: false,
                attributes: {
                    "class": "ticket-progress"
                },
            },
            {
                field: "startdate",
                title: "Inicio",
                width: "80px",
                template: "#=formatDate(startdate)#",
                filterable: false,
                attributes: {
                    "class": "ticket-progress"
                },
            },
            {
                field: "finishdate",
                title: "Fin",
                width: "80px",
                template: "#=formatDate(finishdate)#",
                filterable: false,
                attributes: {
                    "class": "ticket-progress"
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
                    "class": "ticket-progress"
                },
            },
            {
                field: "response",
                title: "Respuesta",
                width: "60px",
                filterable: false,
                attributes: {
                    "class": "ticket-progress"
                },
            }
        ],
    }).data("kendoGrid");
}

function initGridDuration()
{
    gridDuration = $("#gridDuration").kendoGrid({
        dataSource: {
            transport: {
                read: {
                    url: "getDataUserDuration",
                    type: "get",
                    dataType: "json",
                    data: function() {
                        return {
                            start  : $('#dateRangePicker').data('daterangepicker').startDate.format('YYYY-MM-DD'),
                            end    : $('#dateRangePicker').data('daterangepicker').endDate.format('YYYY-MM-DD'),
                            idteam : dropDownListTeam.value(),
                            idspot : dropDownListSpot.value(),
                            iduser : dropDownListUser.value(),
                            iditem : dropDownListItem.value()
                        };
                    },
                },
            },
            pageSize: 20,
            group: {
                field: "iduser", aggregates: [ { field: "id", aggregate: "count" }, { field: "duration", aggregate: "sum" }] 
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

                console.log(row);

                switch (row.type)
                {
                    
                    case "group-header":
                            row.cells[0].value = row.cells[0].value.replace(/(<([^>]+)>)/gi, "");
                            break;

                    case "group-footer":
                            row.cells[1].value = row.cells[1].value.replace(/(<([^>]+)>)/gi, "");
                            row.cells[7].value = row.cells[7].value.replace(/(<([^>]+)>)/gi, "");
                            break;

                        case "data":
                            row.cells[4].value = formatDate(row.cells[4].value);
                            row.cells[5].value = formatDate(row.cells[5].value);
                            row.cells[6].value = formatDate(row.cells[6].value);
                            row.cells[7].value = hhmmss(row.cells[7].value);
                            break;
                
                    default:
                        break;
                }
            }
        },
        toolbar: ["excel", "pdf"],
        excel: {
            fileName: "Reporte de duraciones.xlsx",
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
                field: "iduser",
                title: "Usuario",
                width: "0px",
                groupHeaderTemplate: function(dataItem) {
                    return "<b>" + dataItem.value + "</b>";
                },
                filterable: false,
                hidden: true
            },
            {
                field: "id",
                title: "Código",
                width: "50px",
                filterable: false,
                groupFooterTemplate: "<b>Tareas: #=count#</b>",
                attributes: {
                    "class": "ticket-progress"
                },
            },
            {
                field: "name",
                title: "Tarea",
                width: "120px",
                filterable: false,
                attributes: {
                    "class": "ticket-progress"
                },
            },
            {
                field: "idspot",
                title: "Lugar",
                width: "60px",
                values: window.global_spots,
                filterable: false,
                attributes: {
                    "class": "ticket-progress"
                },
            },
            {
                field: "created_at",
                title: "Fecha",
                width: "100px",
                template: "#=formatDate(created_at)#",
                filterable: false,
                attributes: {
                    "class": "ticket-progress"
                },
            },
            {
                field: "startdate",
                title: "Inicio",
                width: "100px",
                template: "#=formatDate(startdate)#",
                filterable: false,
                attributes: {
                    "class": "ticket-progress"
                },
            },
            {
                field: "finishdate",
                title: "Fin",
                width: "100px",
                template: "#=formatDate(finishdate)#",
                filterable: false,
                attributes: {
                    "class": "ticket-progress"
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
                    "class": "ticket-progress"
                },
            },
            {
                field: "response",
                title: "Respuesta",
                width: "60px",
                filterable: false,
                attributes: {
                    "class": "ticket-progress"
                },
            }
        ],
    }).data("kendoGrid");
}

function formatDate(date)
{
    return (date == null ? "-----------------------" : moment(date).format('YY-MM-DD hh:mm A'));
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