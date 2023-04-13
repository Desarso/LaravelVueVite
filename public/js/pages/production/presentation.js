$(document).ready(function() {
    var p = new Presentation();
    p.initGrid();
    fixKendoGridHeight();

    $("#export").click(function(e) {
        var grid = $("#gridPresentations").data("kendoGrid");
        grid.saveAsExcel();
    });
});


var Presentation = /** @class */ (function() {
    function Presentation() {}
    Presentation.prototype.initGrid = function() {
        $("#gridPresentations").kendoGrid({
            excel: {
                fileName: "Whagons Presentations.xlsx",
            },
            dataSource: {
                transport: {
                    read: {
                        url: "getPresentations",
                        type: "get",
                        dataType: "json"
                    },
                    create: {
                        url: "api/createPresentation",
                        type: "post",
                        dataType: "json",
                    },
                    update: {
                        url: "api/updatePresentation",
                        type: "post",
                        dataType: "json"
                    },
                    destroy: {
                        url: "api/deletePresentation",
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
                            units: { editable: true, field: "units", type: "number" },
                            idequipmenttype: { editable: true, field: "idequipmenttype", type: "number" },
                            isendproduct: { editable: true, field: "isendproduct", type: "boolean", defaultValue: true }
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
                    width: "150px",
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
                    width: "200px",
                    media: "(min-width: 850px)",
                    filterable: false,
                },
                {
                    field: "units",
                    title: locale("units"),
                    width: "100px",
                    media: "(min-width: 450px)",
                    filterable: {
                        multi: true,
                        search: true
                    }
                },

                {
                    field: "idequipmenttype",
                    title: locale("Categoría de Máquina"),
                    width: "150px",
                    editor: dropDownListEditor,
                    values: window.equipmenttypes,
                    media: "(min-width: 450px)",
                    filterable: {
                        multi: true,
                        search: true
                    }
                },


                {
                    field: "isendproduct",
                    title: locale("End Product"),
                    width: "150px",
                    template: "#=formatYesNo(isendproduct)#",
                    media: "(min-width: 850px)"

                },
                { command: { name: "destroy", text: " ", iconClass: "fad fa-trash commandIconDelete" }, title: " ", width: "80px", media: "(min-width: 850px)" }

            ],
        }).data("kendoGrid");

    };

    return Presentation;
}());


function formatName(name) {
    return '<span>' + name + '</span>';
}