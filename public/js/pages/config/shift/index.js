window.types = [{"value" : "DAY", "text" : "Diurna"}, {"value" : "NIGHT", "text" : "Nocturna"}, {"value" : "MIX", "text" : "Mixta"}, {"value" : "DAY_OFF", "text" : "Libre"},]

$(document).ready(function() {
    initGridShift();
});

function initGridShift()
{
    gridShift = $("#gridShift").kendoGrid({
        excel: {
            fileName: "Whagons Planner.xlsx",
        },
        dataSource: {
            transport: {
                read: {
                    url: "getShifts",
                    type: "get",
                    dataType: "json"
                },
                create: {
                    url: "createShift",
                    type: "post",
                    dataType: "json",
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
                    }
                },
                update: {
                    url: "updateShift",
                    type: "post",
                    dataType: "json",
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
                    }
                },
                destroy: {
                    url: "deleteShift",
                    type: "post",
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
                    }
                },
                parameterMap: function(options, type) {
                    if (type == "create" || type == "update") {
                        console.log(options.start, options.end);
                        var start = new Date(options.start);
                        var end   = new Date(options.end);
                        options.start = kendo.toString(new Date(start), "MM/dd/yyyy HH:mm");
                        options.end   = kendo.toString(new Date(end), "MM/dd/yyyy HH:mm");
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
                        name: { editable: true, field: "name", type: "string", validation: { required: { message: "Nombre es requerido" } }},
                        idschedule: { editable: true, field: "idschedule", type: "number", validation: { required: { message: "Horario es requerido" } } },
                        idovertime: { editable: true, field: "idovertime", type: "number", validation: { required: { message: "Valor requerido" } } },
                        type: { editable: true, field: "type", type: "string", validation: { required: { message: "Tipo es requerido" } } },
                        start: { editable: true, field: "start", type: "date", format: "{HH:mm}", nullable: false, defaultValue: null },
                        end: { editable: true, field: "end", type: "date", format: "{HH:mm}", nullable: false, defaultValue: null },
                    }
                }
            },
            requestEnd: function(e) {
                if (e.type == 'destroy' || e.type == 'create' || e.type == 'update') {
                    gridShift.dataSource.read();
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
                field: "type",
                title: "Tipo",
                values: window.types,
                filterable: false,
                width: "150px"
            },
            {
                field: "idschedule",
                title: "Horario",
                values: window.schedules,
                filterable: false,
                width: "150px"
            },
            {
                field: "idovertime",
                title: "Tiempo Extra",
                values: window.overtimes,
                filterable: false,
                width: "150px"
            },
            {
                field: "start",
                title: "Entrada",
                width: "150px",
                filterable: false,
                template: "#=formatTime(start)#",
                format: "{0:HH:mm}",
                editor: timeEditor
            },
            {
                field: "end",
                title: "Salida",
                width: "150px",
                filterable: false,
                template: "#=formatTime(end)#",
                format: "{0:HH:mm}",
                editor: timeEditor
            },
            { command: { name: "edit", text: " ", iconClass: "fad fa-pen commandIconEdit" }, title: " ", width: "60px" },
            { command: { name: "destroy", text: " ", iconClass: "fad fa-trash commandIconDelete" }, title: " ", width: "60px" }
        ],
    }).data("kendoGrid");   
}

function formatTime(time)
{
    if (time == null) return '';
    return moment(time).format('HH:mm');
}
