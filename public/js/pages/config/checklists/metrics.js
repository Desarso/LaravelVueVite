$(document).ready(function() {
    var m = new Metric();
    m.initGrid();
    fixKendoGridHeight();

    $("#export").click(function(e) {
        var grid = $("#gridMetrics").data("kendoGrid");
        grid.saveAsExcel();
    });

});

var Metric = /** @class */ (function() {
    function Metric() {}
    Metric.prototype.initGrid = function() {
        $("#gridMetrics").kendoGrid({
            excel: {
                fileName: "Whagons Metrics.xlsx",
            },
            dataSource: {
                transport: {
                    read: {
                        url: "getMetrics",
                        type: "get",
                        dataType: "json"
                    },
                    create: {
                        url: "api/createMetric",
                        type: "post",
                        dataType: "json",
                    },
                    update: {
                        url: "api/updateMetric",
                        type: "post",
                        dataType: "json"
                    },
                    destroy: {
                        url: "api/deleteMetric",
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
                            symbol: { editable: true, field: "symbol", type: "string" },
                            enabled: { editable: true, field: "enabled", type: "boolean", defaultValue: true },

                        }
                    }
                },
            },

            editable: {
                mode: "popup"
            },
            toolbar: [{ template: kendo.template($("#toolbartemplate").html()) }],
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

            columns: [
                { command: { name: "edit", text: { edit: " " }, iconClass: "fad fa-pen commandIconOpacity" }, title: " ", width: "80px", media: "(min-width: 850px)" },
                {
                    field: "name",
                    title: locale("Name"),
                    template: "#=formatMetricName(name, enabled)#",
                    width: "300px",
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
                    width: "200px",
                    media: "(min-width: 850px)",
                    filterable: {
                        multi: true,
                        search: true
                    }
                },
                {
                    field: "symbol",
                    title: locale("Symbol"),
                    width: "130px",
                    media: "(min-width: 200px)",
                    filterable: {
                        multi: true,
                        search: true
                    }
                },
                {
                    field: "enabled",
                    title: locale("Enabled"),
                    hidden: true,
                    template: "#=formatYesNo(enabled)#",
                    media: "(min-width: 450px)",
                    editor: checkBoxEditor,
                },
                { command: { name: "destroy", text: " ", iconClass: "fad fa-trash commandIconDelete" }, title: " ", width: "80px", media: "(min-width: 850px)" }

            ],
        }).data("kendoGrid");
    };

    return Metric;
}());




function formatMetricName(name, /*description,*/ enabled) {
    let result = "";
    //description = description == null ? '' : description;

    result += "<div style='display:inline-block; vertical-align: middle'>";
    if (enabled == 0) {
        //result += "<i style='color: tomato;' class='fa fa-ban'></i> ";
        result += "<strong style='opacity: 0.3; text-decoration: line-through'>" + name + "</strong>";
        result += "</div>";
    } else {
        result += name;
        result += "</div>";
    }
    return result;
}