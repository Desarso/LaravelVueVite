$(document).ready(function() {
    var ps = new ProductionSchedule();
    ps.initGrid();
    fixKendoGridHeight();

    $("#export").click(function(e) {
        var grid = $("#gridProductionSchedules").data("kendoGrid");
        grid.saveAsExcel();
    });
});


var ProductionSchedule = /** @class */ (function() {
    function ProductionSchedule() {}
    ProductionSchedule.prototype.initGrid = function() {
        $("#gridProductionSchedules").kendoGrid({
            excel: {
                fileName: "Whagons Production Schedules.xlsx",
            },
            dataSource: {
                transport: {
                    read: {
                        url: "getProductionSchedules",
                        type: "get",
                        dataType: "json"
                    },
                    create: {
                        url: "api/createProductionSchedule",
                        type: "post",
                        dataType: "json",
                    },
                    update: {
                        url: "api/updateProductionSchedule",
                        type: "post",
                        dataType: "json"
                    },
                    destroy: {
                        url: "api/deleteProductionSchedule",
                        type: "delete",
                    }
                },
                pageSize: 20,
                schema: {
                    model: {
                        id: "id",
                        fields: {
                            id: { type: "number", editable: false, nullable: true },
                            name: { editable: true, field: "name", type: "string", validation: { required: { message: "Nombre es requerido" } } },
                            description: { editable: true, field: "description", type: "string" },
                            start: { editable: true, field: "start", type: "date", format: "{HH:mm}", nullable: true, defaultValue: null },
                            end: { editable: true, field: "end", type: "date", format: "{HH:mm}", nullable: true, defaultValue: null },
                            duration: { editable: true, field: "duration", type: "number", validation: { required: { message: "Duración es requerida" } } },
                            dow: { field: "dow" },
                            breaks: { field: "breaks" },
                            enabled: { editable: true, field: "enabled", type: "boolean", defaultValue: true }
                        }
                    }
                },
            },

            editable: {
                mode: "popup"
            },
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
            toolbar: [{ template: kendo.template($("#toolbartemplate").html()) }],
            columns: [
                { command: { name: "edit", text: { edit: " " }, iconClass: "fad fa-pen commandIconOpacity" }, title: " ", width: "80px", media: "(min-width: 850px)" },
                {
                    field: "name",
                    title: locale("Name"),
                    template: '#=formatName(name, enabled)#',
                    width: "100px",
                    groupable: false,
                    media: "(min-width: 350px)",
                    filterable: {
                        multi: true,
                        search: true
                    }
                },
                {
                    field: "description",
                    title: locale("Description"),
                    editor: textAreaEditor,
                    width: "150px",
                    media: "(min-width: 850px)",
                    filterable: false,
                },

                {
                    field: "duration",
                    title: locale("Duration"),
                    width: "50px",
                    //values: window.types,
                    media: "(min-width: 450px)",
                    filterable: {
                        multi: true,
                        search: true
                    }
                },
                {
                    field: "dow",
                    title: locale("Day"),
                    template: "#=formatDows(dow)#",
                    editor: editorMultiSelectDOW,
                    width: "100px",
                    media: "(min-width: 450px)",
                    filterable: false,
                },
                {
                    field: "breaks",
                    title: locale("Breaks"),
                    template: "#=formatBreaks(breaks)#",
                    editor: editorMultiSelectBreak,
                    width: "100px",
                    media: "(min-width: 450px)",
                    filterable: false,
                },

                {
                    field: "enabled",
                    title: locale("Enabled"),
                    template: "#=formatYesNo(enabled)#",
                    hidden: true,
                    media: "(min-width: 850px)"

                },
                { command: { name: "destroy", text: " ", iconClass: "fad fa-trash commandIconDelete" }, title: " ", width: "80px", media: "(min-width: 850px)" }

            ],
        }).data("kendoGrid");

    };

    return ProductionSchedule;
}());





function formatName(name, enabled) {
    let result = "";
    if (enabled == 1)
        result += '<span>' + name + '</span>';
    else
        result += "<span style='opacity: 0.3'>" + name + '</span>';
    return result;
}

function formatDuration(duration) {
    let format = formatSLA(duration);
    return '<i class="fad fa-clock text-light"></i> ' + format;
}