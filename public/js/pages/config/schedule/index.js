$(document).ready(function() {
    initGridSchedule();
});

function initGridSchedule()
{
    gridSchedule = $("#gridSchedule").kendoGrid({
        excel: {
            fileName: "Whagons Planner.xlsx",
        },
        dataSource: {
            transport: {
                read: {
                    url: "getSchedules",
                    type: "get",
                    dataType: "json"
                },
                create: {
                    url: "createSchedule",
                    type: "post",
                    dataType: "json",
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
                    }
                },
                update: {
                    url: "updateSchedule",
                    type: "post",
                    dataType: "json",
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
                    }
                },
                destroy: {
                    url: "deleteSchedule",
                    type: "post",
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
                    }
                },
                parameterMap: function(options, type) {
                    if (type == "create" || type == "update") {
                        console.log(options.starttime, options.endtime);
                        var starttime = new Date(options.starttime);
                        var endtime   = new Date(options.endtime);
                        options.starttime = kendo.toString(new Date(starttime), "MM/dd/yyyy HH:mm");
                        options.endtime   = kendo.toString(new Date(endtime), "MM/dd/yyyy HH:mm");
                    }
                    return options;
                }
            },
            pageSize: 20,
            schema: {
                model: {
                    id: "id",
                    fields: {
                        id: { type: "number", editable: false, nullable: true },
                        name: { editable: true, field: "name", type: "string" },
                        idtype: { editable: true, field: "idtype", type: "number", validation: { required: { message: "Tipo es requerido" } } },
                        starttime: { editable: true, field: "starttime", type: "date", format: "{HH:mm}", nullable: false, defaultValue: null },
                        endtime: { editable: true, field: "endtime", type: "date", format: "{HH:mm}", nullable: false, defaultValue: null },
                        teams: { field: "teams" }
                    }
                }
            },
            requestEnd: function(e) {
                if (e.type == 'destroy' || e.type == 'create' || e.type == 'update') {
                    gridSchedule.dataSource.read();
                }
            }
        },
        editable: {
            mode: "popup"
        },
        toolbar: ["create"],
        sortable: true,
        reorderable: true,
        resizable: true,
        navigatable: true,
        pageable: {
            refresh: true,
            pageSizes: true,
            buttonCount: 5,
        },
        filterable: true,
        height: "650px",
        noRecords: {
            template: "<div class='alert alert-danger mt-1 alert-validation-msg' role='alert'><i class='feather icon-info mr-1 align-middle'></i><span>No hay datos</span></div>"
        },
        columns: [
            {
                field: "name",
                title: 'Tarea',
                width: "300px",
                filterable: false
            },
            {
                field: "idtype",
                title: "Tipo",
                values: window.scheduleTypes,
                filterable: false,
                width: "150px"
            },
            {
                field: "starttime",
                title: "Entrada",
                width: "150px",
                filterable: false,
                template: "#=formatTime(starttime)#",
                format: "{0:HH:mm}",
                editor: timeEditor
            },
            {
                field: "endtime",
                title: "Salida",
                width: "150px",
                filterable: false,
                template: "#=formatTime(endtime)#",
                format: "{0:HH:mm}",
                editor: timeEditor
            },
            {
                field: "teams",
                title: "Equipos",
                template: "#=formatTeams(teams)#",
                editor: editorMultiSelectTeam,
                filterable: false,
                width: "400px"
            },
            { command: { name: "edit", text: " ", iconClass: "fad fa-pen commandIconEdit" }, title: " ", width: "60px" },
            { command: { name: "destroy", text: " ", iconClass: "fad fa-trash commandIconDelete" }, title: " ", width: "60px" }
        ],
    }).data("kendoGrid");   
}

function formatTeams(teams)
{
    if (teams == null || teams == "" || typeof teams == 'undefined') return "";
    let html = "";
    teams.forEach((team) => {
        let result = window.global_teams.find(o => o.value === team.value);
        html += "<div class='badge badge-success' style='background-color:" + result.color + "' >" + result.text + "</div> ";
    });
    return html;
}

function formatTime(time)
{
    if (time == null) return '';
    return moment(time).format('HH:mm');
}
