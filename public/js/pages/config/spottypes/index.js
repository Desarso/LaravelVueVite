$(document).ready(function() {

    var st = new SpotType();
    st.initGrid();
    fixKendoGridHeight();

    $("#export").click(function(e) {
        var grid = $("#gridSpotType").data("kendoGrid");
        grid.saveAsExcel();
    });

    $(".k-grid-spots").click(function(e) { // note that the class is k-grid-new since the name of the command is new
        e.preventDefault();
        window.location = 'config-spots';
    });

});



var SpotType = /** @class */ (function() {
    function SpotType() {}

    SpotType.prototype.initGrid = function() {
        window.grid = $("#gridSpotType").kendoGrid({
            excel: {
                fileName: "Whagons Spot Types.xlsx",

            },
            dataSource: {
                transport: {
                    read: {
                        url: "getSpotTypes",
                        type: "get",
                        dataType: "json"
                    },
                    create: {
                        url: "api/createSpotType",
                        type: "post",
                        dataType: "json",
                    },
                    update: {
                        url: "api/updateSpotType",
                        type: "post",
                        dataType: "json"
                    },

                    destroy: {
                        url: "api/deleteSpotType",
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
                            islodging: { editable: true, field: "islodging", type: "boolean" }
                        }
                    }
                },
            },

            editable: {
                mode: "popup"
            },
            toolbar: [{ template: kendo.template($("#toolbartemplate").html()) }],
            reorderable: true,
            resizable: true,
            sortable: true,
            pageable: {
                refresh: true,
                pageSizes: true,
                buttonCount: 5
            },
            filterable: true,

            columns: [
                { command: { name: "edit", text: { edit: " " }, iconClass: "fad fa-pen commandIconOpacity" }, title: " ", width: "80px", media: "(min-width: 850px)" },
                {
                    field: "name",
                    title: locale('Name'),
                    width: "300px",
                    template: "#=formatSpotTypeName(name, islodging)#",
                    media: "(min-width: 350px)",
                    filterable: {
                        multi: true,
                        search: true
                    }
                },
                {
                    field: "description",
                    title: locale('Description'),
                    width: "300px",
                    editor: textAreaEditor,
                    media: "(min-width: 450px)",
                    filterable: false
                },
                {
                    field: "islodging",
                    title: locale('Lodging'),
                    template: "#=formatYesNo(islodging)#",
                    editor: checkBoxEditor,
                    width: "200px",
                    media: "(min-width: 450px)",
                    filterable: {
                        multi: true,
                        search: true
                    }

                },
                { command: { name: "destroy", text: " ", iconClass: "fad fa-trash commandIconDelete" }, title: " ", width: "80px", media: "(min-width: 850px)" }
            ],
        }).data("kendoGrid");
    };

    return SpotType;
}());


function formatSpotTypeName(name, islodging) {
    if (islodging == 1) return " <i title='Room or space for renting!' class='fad fa-bed text-dark'></i> <strong>" + name + "</strong>";
    return name;
}