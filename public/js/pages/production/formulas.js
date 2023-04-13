$(document).ready(function() {
    var pf = new ProductionFormula();
    pf.initGrid();
    fixKendoGridHeight();

    $("#export").click(function(e) {
        var grid = $("#gridProductionFormulas").data("kendoGrid");
        grid.saveAsExcel();
    });
});


var ProductionFormula = /** @class */ (function() {
    function ProductionFormula() {}
    ProductionFormula.prototype.initGrid = function() {
        $("#gridProductionFormulas").kendoGrid({
            excel: {
                fileName: "Whagons Production Formulas.xlsx",
            },
            dataSource: {
                transport: {
                    read: {
                        url: "getProductionFormulas",
                        type: "get",
                        dataType: "json"
                    },
                    create: {
                        url: "api/createProductionFormula",
                        type: "post",
                        dataType: "json",
                    },
                    update: {
                        url: "api/updateProductionFormula",
                        type: "post",
                        dataType: "json"
                    },
                    destroy: {
                        url: "api/deleteProductionFormula",
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
                            inputs: { field: "inputs" }
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
                    width: "100px",
                    media: "(min-width: 850px)",
                    filterable: false,
                },
                {
                    field: "inputs",
                    title: locale("Inputs"),
                    template: "#=formatInputs(inputs)#",
                    editor: editorMultiSelectInput,
                    width: "200px",
                    media: "(min-width: 450px)",
                    filterable: false,
                },



                { command: { name: "destroy", text: " ", iconClass: "fad fa-trash commandIconDelete" }, title: " ", width: "80px", media: "(min-width: 850px)" }

            ],
        }).data("kendoGrid");

    };

    return ProductionFormula;
}());


function formatName(name) {
    return '<span>' + name + '</span>';

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