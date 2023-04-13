$(document).ready(function() {
    var pb = new ProductionBreak();
    pb.initGrid();
    fixKendoGridHeight();

    $("#export").click(function(e) {
        var grid = $("#gridProductionBreaks").data("kendoGrid");
        grid.saveAsExcel();
    });
});


var ProductionBreak = /** @class */ (function() {
    function ProductionBreak() {}
    ProductionBreak.prototype.initGrid = function() {
        $("#gridProductionBreaks").kendoGrid({
            excel: {
                fileName: "Whagons Production Breaks.xlsx",
            },
            dataSource: {
                transport: {
                    read: {
                        url: "getProductionBreaks",
                        type: "get",
                        dataType: "json"
                    },
                    create: {
                        url: "api/createProductionBreak",
                        type: "post",
                        dataType: "json",
                    },
                    update: {
                        url: "api/updateProductionBreak",
                        type: "post",
                        dataType: "json"
                    },
                    destroy: {
                        url: "api/deleteProductionBreak",
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
                            duration: { editable: true, field: "duration", type: "number", validation: { required: { message: "Duración es requerida" } } },
                            dow: { field: "dow" },
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
                    width: "200px",
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
                    hidden: true,
                    editor: textAreaEditor,
                    width: "200px",
                    media: "(min-width: 850px)",
                    filterable: false,
                },

                {
                    field: "duration",
                    title: locale("Duration"),
                    width: "130px",
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
                    width: "180px",
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

    return ProductionBreak;
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


function formatProjectStatus(idstatus) {
    let s = global_statuses.find(o => o.value === idstatus);
    if (typeof s == "undefined") return "?";
    return "<span style='background-color:" + s.color + " ' class='badge badge-pill badge-success'>" + s.text + "</span>";


}


function formatProjectName(name, description, code, isprivate) {
    let result = "";
    description = description == null ? '' : description;

    result += "<div style='display:inline-block; vertical-align: middle'>";
    if (isprivate == 1)
        result += "<i class='fa fa-lock text-primary'></i> ";
    result += "<strong>" + name + "</strong>";
    result += "<br><small style='opacity: 0.6'>" + description + "</small>";

    result += "</div>";
    return result;
}