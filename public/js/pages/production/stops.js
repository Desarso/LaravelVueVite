$(document).ready(function() {
    var ps = new ProductionStop();
    ps.initGrid();
    fixKendoGridHeight();

    $("#export").click(function(e) {
        var grid = $("#gridProductionStops").data("kendoGrid");
        grid.saveAsExcel();
    });
});


var ProductionStop = /** @class */ (function() {
    function ProductionStop() {}
    ProductionStop.prototype.initGrid = function() {
        $("#gridProductionStops").kendoGrid({
            excel: {
                fileName: "Whagons Production Stops.xlsx",
            },
            dataSource: {
                transport: {
                    read: {
                        url: "getProductionStops",
                        type: "get",
                        dataType: "json"
                    },
                    create: {
                        url: "api/createProductionStop",
                        type: "post",
                        dataType: "json",
                    },
                    update: {
                        url: "api/updateProductionStop",
                        type: "post",
                        dataType: "json"
                    },
                    destroy: {
                        url: "api/deleteProductionStop",
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
                            idtype: { editable: true, field: "idtype", type: "number", defaultValue: 1, validation: { required: { message: "Tipo es requerido" } } },
                            idteam: { editable: true, field: "idteam", type: "number" },
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
                    editor: textAreaEditor,
                    hidden: true,
                    width: "200px",
                    media: "(min-width: 850px)",
                    filterable: false,
                },

                {
                    field: "idteam",
                    title: locale("Team"),
                    //template: "#=formatDuration(warmup_duration)#",
                    values: global_teams,
                    width: "180px",
                    media: "(min-width: 450px)",
                    filterable: false,
                },

                {
                    field: "idtype",
                    title: locale("Type"),
                    width: "130px",
                    //values: window.types,
                    media: "(min-width: 450px)",
                    filterable: {
                        multi: true,
                        search: true
                    }
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

    return ProductionStop;
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