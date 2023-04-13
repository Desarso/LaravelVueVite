$(document).ready(function() {
    var pi = new ProductionInput();
    pi.initGrid();
    fixKendoGridHeight();

    $("#export").click(function(e) {
        var grid = $("#gridProductionInputs").data("kendoGrid");
        grid.saveAsExcel();
    });
});


var ProductionInput = /** @class */ (function() {
    function ProductionInput() {}
    ProductionInput.prototype.initGrid = function() {
        $("#gridProductionInputs").kendoGrid({
            excel: {
                fileName: "Whagons Production Inputs.xlsx",
            },
            dataSource: {
                transport: {
                    read: {
                        url: "getProductionInputs",
                        type: "get",
                        dataType: "json"
                    },
                    create: {
                        url: "api/createProductionInput",
                        type: "post",
                        dataType: "json",
                    },
                    update: {
                        url: "api/updateProductionInput",
                        type: "post",
                        dataType: "json"
                    },
                    destroy: {
                        url: "api/deleteProductionInput",
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
                            idproductcategory: { editable: true, field: "idproductcategory", type: "number", nullable: true },
                            formula: { editable: true, field: "formula", type: "number", defaultValue: 1 },
                            measure: { editable: true, field: "measure", type: "string" },
                            pack_size: { editable: true, field: "pack_size", type: "number" },
                            pack_placing_duration: { editable: true, field: "pack_placing_duration", type: "number" },
                            buffer: { editable: true, field: "buffer", type: "number", nullable: true },
                            idstop: { editable: true, field: "idstop", type: "number", nullable: true },
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
                    template: '#=formatName(name)#',
                    width: "120px",
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
                    field: "idproductcategory",
                    title: locale("Category"),
                    width: "130px",
                    values: window.categories,
                    media: "(min-width: 450px)",
                    filterable: {
                        multi: true,
                        search: true
                    }
                },

                {
                    field: "formula",
                    title: locale("Formula"),
                    //template: "#=formatDuration(warmup_duration)#",
                    width: "100px",
                    media: "(min-width: 450px)",
                    filterable: false,
                },
                {
                    field: "measure",
                    title: locale("Measure"),
                    width: "100px",
                    media: "(min-width: 850px)"

                },
                {
                    field: "pack_size",
                    title: locale("Pack"),
                    width: "100px",
                    media: "(min-width: 850px)"

                },
                {
                    field: "pack_placing_duration",
                    title: locale("Placing") + " (seg)",
                    width: "120px",
                    media: "(min-width: 850px)"

                },
                {
                    field: "buffer",
                    title: locale("Buffer"),
                    width: "100px",
                    media: "(min-width: 850px)"

                },
                {
                    field: "idstop",
                    title: locale("Stop"),
                    width: "100px",
                    values: window.stops,
                    editor: dropDownListEditor,
                    media: "(min-width: 850px)"

                },


                { command: { name: "destroy", text: " ", iconClass: "fad fa-trash commandIconDelete" }, title: " ", width: "80px", media: "(min-width: 850px)" }

            ],
        }).data("kendoGrid");

    };

    return ProductionInput;
}());


function formatName(name) {
    return "<span >" + name + '</span>';
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