$(function() {

    initDateRangePicker();
    initDropDownListStatus();
    initDropDownListTeam();
    initDropDownListSpot();
    initDropDownListItem();

    initGridSpotTickets();
    initGridItemTickets();

});

function initDropDownListItem() {
    dropDownListItem = $("#dropDownListItem").kendoDropDownList({
        optionLabel: "-- Seleccione --",
        dataTextField: "name",
        dataValueField: "id",
        dataSource: window.global_items,
        height: 400,
        change: changeFilter
    }).data("kendoDropDownList");
}

function initDropDownListStatus() {
    dropDownListStatus = $("#dropDownListStatus").kendoDropDownList({
        optionLabel: "-- Seleccione --",
        dataTextField: "text",
        dataValueField: "value",
        dataSource: window.global_statuses,
        height: 400,
        change: changeFilter,
    }).data("kendoDropDownList");
}

function changeFilter() {
    gridItemTickets.dataSource.read();
    gridSpotTickets.dataSource.read();
}

function initGridSpotTickets() {
    gridSpotTickets = $("#gridSpotTickets").kendoGrid({
        dataSource: {
            transport: {
                read: {
                    url: "getDataSpotTickets",
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
                { field: "total", aggregate: "sum" },
                { field: "pendint", aggregate: "sum" },
                { field: "progress", aggregate: "sum" },
                { field: "paused", aggregate: "sum" },
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
                            row.cells[1].value = parseInt(row.cells[1].value.replace(/(<([^>]+)>)/gi, ""));
                            row.cells[2].value = parseInt(row.cells[2].value.replace(/(<([^>]+)>)/gi, ""));
                            row.cells[3].value = parseInt(row.cells[3].value.replace(/(<([^>]+)>)/gi, ""));
                            row.cells[4].value = parseInt(row.cells[4].value.replace(/(<([^>]+)>)/gi, ""));
                            row.cells[5].value = parseInt(row.cells[5].value.replace(/(<([^>]+)>)/gi, ""));
                            break;

                        case "data":
                            let user = getSpot(row.cells[0].value);
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
        dataBound: function() {

        },
        detailInit: detailInitSpot,
        detailExpand: function(e) {
            let parent = this.dataItem(e.masterRow);
            $("#idparentspot").val(parent.id);

            var grid = e.sender;
            var rows = grid.element.find(".k-master-row").not(e.masterRow);

            rows.each(function(e) {
                grid.collapseRow(this);
            });
        },
        columns: [{
                field: "id",
                title: "Lugar",
                width: "250px",
                template: "#=formatSpot(id)#",
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

function detailInitSpot(e) {
    $("#idparentspot").val(e.data.id);

    $("<div id='gridTickets'></div>").appendTo(e.detailCell).kendoGrid({
        dataSource: {
            transport: {
                read: {
                    url: "getSpotTickets",
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
        columns: [{
                field: "idstatus",
                title: "Estado",
                template: function(dataItem) {
                    let status = global_statuses.find((item) => { return item.value == dataItem.idstatus; });
                    return '<i class="fa fa-circle font-small-3 mr-50" style="color:' + status.color + '"></i>' + status.text;
                },
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

function initGridItemTickets() {
    gridItemTickets = $("#gridItemTickets").kendoGrid({
        dataSource: {
            transport: {
                read: {
                    url: "getDataItemTickets",
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
                { field: "total", aggregate: "sum" },
                { field: "pendint", aggregate: "sum" },
                { field: "progress", aggregate: "sum" },
                { field: "paused", aggregate: "sum" },
                { field: "finished", aggregate: "sum" }
            ]
        },
        toolbar: ["pdf"],
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
            template: "<div class='alert alert-danger mt-1 alert-validation-msg' role='alert'><i class='feather icon-info mr-1 align-middle'></i><span>No hay tareas</span></div>"
        },
        dataBound: function() {

        },
        detailInit: detailInitItem,
        detailExpand: function(e) {
            let parent = this.dataItem(e.masterRow);
            $("#idparentitem").val(parent.id);

            var grid = e.sender;
            var rows = grid.element.find(".k-master-row").not(e.masterRow);

            rows.each(function(e) {
                grid.collapseRow(this);
            });
        },
        columns: [{
                field: "id",
                title: "Ítem",
                width: "250px",
                template: function(dataItem) {
                    return global_items.find(o => o.id === parseInt(dataItem.id)).name;
                },
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

function detailInitItem(e) {
    $("#idparentitem").val(e.data.id);

    $("<div id='gridTickets'></div>").appendTo(e.detailCell).kendoGrid({
        dataSource: {
            transport: {
                read: {
                    url: "getItemTickets",
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
            template: "<div class='alert alert-danger mt-1 alert-validation-msg' role='alert'><i class='feather icon-info mr-1 align-middle'></i><span>No hay datos</span></div>"
        },
        pageable: {
            refresh: true,
            pageSizes: true,
            buttonCount: 5
        },
        columns: [{
                field: "idstatus",
                title: "Estado",
                template: function(dataItem) {
                    let status = global_statuses.find((item) => { return item.value == dataItem.idstatus; });
                    return '<i class="fa fa-circle font-small-3 mr-50" style="color:' + status.color + '"></i>' + status.text;
                },
                filterable: false
            },
            {
                field: "idteam",
                title: "Equipo",
                values: window.global_teams,
                filterable: false
            },
            {
                field: "idspot",
                title: "Lugar",
                values: window.global_spots,
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

function getFilters() {
    return {
        start: $('#dateRangePicker').data('daterangepicker').startDate.format('YYYY-MM-DD'),
        end: $('#dateRangePicker').data('daterangepicker').endDate.format('YYYY-MM-DD'),
        idteam: dropDownListTeam.value(),
        idstatus: dropDownListStatus.value(),
        idspot: dropDownListSpot.value(),
        iditem: dropDownListItem.value(),
        idparentspot: $("#idparentspot").val(),
        idparentitem: $("#idparentitem").val()
    };
}

formatSpot = function formatSpot(idspot) {
    var spot = global_spots.find(function(e) { return e.value === idspot; });
    let spotName = spot.text;

    if (spot.idparent != 0 && spot.idparent != null) {
        return spot.text + " <br><small style='color:lightgray'>" + spot.spotparent + "</small>";
    }

    return spotName;
}