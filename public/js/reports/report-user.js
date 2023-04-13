$(document).ready(function() {
    initDateRangePicker();
    initDropDownListStatus();
    initDropDownListTeam();
    initDropDownListUser();
    initDropDownListSpot();
    initGridUserReports();
    initGridUserTickets();
});

function initDropDownListStatus()
{
    dropDownListStatus = $("#dropDownListStatus").kendoDropDownList({
        optionLabel: "-- Seleccione --",
        dataTextField: "text",
        dataValueField: "value",
        dataSource: window.global_statuses,
        height: 400,
        change: changeFilter,
    }).data("kendoDropDownList");
}

function changeFilter()
{
    gridUserReports.dataSource.read();
    gridUserTickets.dataSource.read();
}

function initGridUserReports()
{
    gridUserReports = $("#gridUserReports").kendoGrid({
        dataSource: {
            transport: {
                read: {
                    url: "getDataUserReports",
                    type: "get",
                    dataType: "json",
                    data: function() {
                        return getFilters()
                    },
                },
            },
            pageSize: 20,
            schema: {
                model: {
                    id: "id",
                    fields: {
                        id: { type: "number", editable: false, nullable: true },
                    }
                }
            },
            aggregate: [
                { field: "reports_count", aggregate: "sum" }
            ]
        },
        excelExport: function(e) {

            var sheet = e.workbook.sheets[0];

            for (var i = 1; i < sheet.rows.length; i++)
            {
                var row = sheet.rows[i];

                switch (row.type)
                {
                    case "footer":
                            row.cells[1].value = row.cells[1].value.replace(/(<([^>]+)>)/gi, "");
                            break;

                        case "data":
                            let user = getUser(row.cells[0].value);
                            row.cells[0].value = user.text;
                            break;
                
                    default:
                        break;
                }
            }
        },
        toolbar: ["pdf", "excel"],
        pdf: {
            allPages: true,
            avoidLinks: true,
            paperSize: "A4",
            margin: { top: "2cm", left: "1cm", right: "1cm", bottom: "1cm" },
            landscape: true,
            repeatHeaders: true,
            scale: 0.8
        },
        excel: {
            fileName: "Reporte de tareas creadas por usuario.xlsx",
            filterable: false,
            allPages: true
        },
        editable: {
            mode: "popup"
        },
        height: "500px",
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
        dataBound: function() {

        },
        columns: [
            {
                field: "id",
                title: "Usuario",
                width: "200px",
                template: "#=formatUser(id)#",
                filterable: false
            },
            {
                field: "reports_count",
                title: "Total de reportes",
                template: "<h2 style='text-align: center; font-weight:700;'> #: reports_count # </h2>",
                aggregates: ["sum"],
                footerTemplate: "<h2 style='text-align: center; font-weight:700;'> #: sum # </h2>",
                filterable: false,
                width: "200px",
            }
        ],
    }).data("kendoGrid");
}

function initGridUserTickets()
{
    gridUserTickets = $("#gridUserTickets").kendoGrid({
        dataSource: {
            transport: {
                read: {
                    url: "getDataUserTickets",
                    type: "get",
                    dataType: "json",
                    data: function() {
                        return getFilters()
                    },
                },
            },
            pageSize: 20,
            schema: {
                model: {
                    id: "id",
                    fields: {
                        id: { type: "number", editable: false, nullable: true },
                    }
                }
            },
            aggregate: [
                { field: "total",    aggregate: "sum" },
                { field: "pendint",  aggregate: "sum" },
                { field: "progress", aggregate: "sum" },
                { field: "paused",   aggregate: "sum" },
                { field: "finished", aggregate: "sum" }
            ]
        },
        excelExport: function(e) {

            var sheet = e.workbook.sheets[0];

            for (var i = 1; i < sheet.rows.length; i++)
            {
                var row = sheet.rows[i];

                switch (row.type)
                {
                    case "footer":
                            row.cells[1].value = row.cells[1].value.replace(/(<([^>]+)>)/gi, "");
                            row.cells[2].value = row.cells[2].value.replace(/(<([^>]+)>)/gi, "");
                            row.cells[3].value = row.cells[3].value.replace(/(<([^>]+)>)/gi, "");
                            row.cells[4].value = row.cells[4].value.replace(/(<([^>]+)>)/gi, "");
                            row.cells[5].value = row.cells[5].value.replace(/(<([^>]+)>)/gi, "");
                            break;

                        case "data":
                            let user = getUser(row.cells[0].value);
                            row.cells[0].value = user.text;
                            break;
                
                    default:
                        break;
                }
            }
        },
        toolbar: ["pdf", "excel"],
        pdf: {
            allPages: true,
            avoidLinks: true,
            paperSize: "A4",
            margin: { top: "2cm", left: "1cm", right: "1cm", bottom: "1cm" },
            landscape: true,
            repeatHeaders: true,
            scale: 0.8
        },
        excel: {
            fileName: "Reporte de usuarios.xlsx",
            filterable: false,
            allPages: true
        },
        editable: {
            mode: "popup"
        },
        height: "500px",
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
        dataBound: function() {},
        detailInit: detailInitUserTicketsDetails,
        detailExpand: function(e) {
            let parent = this.dataItem(e.masterRow);
            $("#idparentuser").val(parent.id);

            var grid = e.sender;
            var rows = grid.element.find(".k-master-row").not(e.masterRow);

            rows.each(function(e) {
                grid.collapseRow(this);
            });
        },
        columns: [
            {
                field: "id",
                title: "Usuario",
                width: "250px",
                template: "#=formatUser(id)#",
                filterable: false
            },
            {
                field: "total",
                title: "Tareas",
                width: "150px",
                template: "<h2 style='font-weight:700;'> #: total # </h2>",
                aggregates: ["sum"],
                footerTemplate: "<h2 style='text-align: center; font-weight:700;'> #: sum # </h2>",
                attributes: {
                    "class": "ticket-total"
                },
                filterable: false
            },
            {
                field: "pendint",
                title: "Pendiente",
                width: "150px",
                template: "<h2 style='font-weight:700; color:rgba(234, 84, 85);'> #: pendint # </h2>",
                aggregates: ["sum"],
                footerTemplate: "<h2 style='text-align: center; font-weight:700; color:rgba(234, 84, 85);'> #: sum # </h2>",
                attributes: {
                    "class": "ticket-pendint"
                },
                filterable: false
            },
            {
                field: "progress",
                title: "En progreso",
                width: "150px",
                template: "<h2 style='font-weight:700; color:rgba(40, 199, 111);'> #: progress # </h2>",
                aggregates: ["sum"],
                footerTemplate: "<h2 style='text-align: center; font-weight:700; color:rgba(40, 199, 111);'> #: sum # </h2>",
                attributes: {
                    "class": "ticket-progress"
                },
                filterable: false
            },
            {
                field: "paused",
                title: "Pausado",
                width: "150px",
                template: "<h2 style='font-weight:700; color:rgba(255, 159, 67);'> #: paused # </h2>",
                aggregates: ["sum"],
                footerTemplate: "<h2 style='text-align: center; font-weight:700; color:rgba(255, 159, 67);'> #: sum # </h2>",
                attributes: {
                    "class": "ticket-paused"
                },
                filterable: false
            },
            {
                field: "finished",
                title: "Finalizado",
                width: "150px",
                template: "<h2 style='font-weight:700; color:rgba(120, 123, 128);'> #: finished # </h2>",
                aggregates: ["sum"],
                footerTemplate: "<h2 style='text-align: center; font-weight:700; color:rgba(120, 123, 128);'> #: sum # </h2>",
                attributes: {
                    "class": "ticket-finished"
                },
                filterable: false
            }
        ],
    }).data("kendoGrid");
}

function detailInitUserTicketsDetails(e)
{
    $("#idparentuser").val(e.data.id);

    $("<div id='gridTickets'></div>").appendTo(e.detailCell).kendoGrid({
        dataSource: {
            transport: {
                read: {
                    url: "getUserTicketsDetails",
                    type: "get",
                    dataType: "json",
                    data: function() {
                        return getFilters();
                    }
                }
            },
            pageSize: 20,
            schema: {
                model: {
                    id: "id",
                    fields: {
                        id: { editable: false, nullable: false, type: "number" },
                        idstatus: { editable: true, field: "idstatus", type: "number", nullable: false },
                        iditem: { editable: true, field: "iditem", type: "number", nullable: false },
                    }
                }
            },
        },
        editable: false,
        toolbar: [],
        scrollable: false,
        sortable: true,
        filterable: true,
        noRecords: {
            template: "<div class='alert alert-danger mt-1 alert-validation-msg' role='alert'><i class='feather icon-info mr-1 align-middle'></i><span>No hay tareas</span></div>"
        },
        pageable: {
            refresh: true,
            pageSizes: true,
            buttonCount: 5
        },
        columns: [
            {
                field: "idstatus",
                title: "Estado",
                template: function(dataItem) {
                    let status = global_statuses.find((item) => { return item.value == dataItem.idstatus; });
                    return '<i class="fa fa-circle font-small-3 mr-50" style="color:' + status.color + '"></i>' + status.text;
                },
                filterable: false

            },
            {
                field: "idspot",
                title: "Lugar",
                values: window.global_spots,
                filterable: false
            },



            
            {
                field: "idteam",
                title: "Equipo",
                values: window.global_teams,
                filterable: false
            },
            {
                field: "name",
                title: "Tarea",
                filterable: false
            },
            {
                field: "created_by",
                values: window.global_users,
                title: "Reportado por",
                filterable: false
            },
            {
                field: "created_at",
                title: "Fecha",
                template: function(dataItem) {
                    return moment(dataItem.created_at).format('YY-MM-DD hh:mm A');
                },
                filterable: false
            }
        ],
    });
}

function getFilters()
{
    return {
        start    : $('#dateRangePicker').data('daterangepicker').startDate.format('YYYY-MM-DD'),
        end      : $('#dateRangePicker').data('daterangepicker').endDate.format('YYYY-MM-DD'),
        idteam   : dropDownListTeam.value(),
        idstatus : dropDownListStatus.value(),
        idspot   : dropDownListSpot.value(),
        iduser   : dropDownListUser.value(),
        idparentuser : $("#idparentuser").val()
      };
}

function formatUser(iduser)
{
    let user = getUser(iduser);
    if (user == null) return '';

    return "<li data-toggle='tooltip' data-popup='tooltip-custom' data-placement='top' data-original-title='" + user.text + "' class='avatar pull-up'>" +
                "<img class='media-object rounded-circle' src='" + user.urlpicture + "' alt='Avatar' height='30' width='30'>" +
           "</li><strong>" + user.text + "</strong>";
}


