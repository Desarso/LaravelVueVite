$(document).ready(function() {
    var c = new Checklist();
    c.initGrid();
    fixKendoGridHeight();

    $("#export").click(function(e) {
        var grid = $("#gridChecklists").data("kendoGrid");
        grid.saveAsExcel();
    });

    $(".k-grid-options").click(function(e) { // note that the class is k-grid-new since the name of the command is new
        e.preventDefault();
        window.location = 'config-checklistoptions';
    });

    $(".k-grid-metrics").click(function(e) { // note that the class is k-grid-new since the name of the command is new
        e.preventDefault();
        window.location = 'config-metrics';
    });

    $(".k-grid-data").click(function(e) { // note that the class is k-grid-new since the name of the command is new
        e.preventDefault();
        window.location = 'config-checklistdata';
    });

});



var Checklist = /** @class */ (function() {
    function Checklist() {}
    Checklist.prototype.initGrid = function() {
        $("#gridChecklists").kendoGrid({
            excel: {
                fileName: "Whagons Checklists.xlsx",
            },
            dataSource: {
                transport: {
                    read: {
                        url: "getChecklists",
                        type: "get",
                        dataType: "json"
                    },
                    create: {
                        url: "api/createChecklist",
                        type: "post",
                        dataType: "json",
                    },
                    update: {
                        url: "api/updateChecklist",
                        type: "post",
                        dataType: "json"
                    },
                    destroy: {
                        url: "api/deleteChecklist",
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
                            send_by_email: { editable: true, field: "send_by_email", type: "boolean", defaultValue: false },
                            enabled: { editable: true, field: "enabled", type: "boolean", defaultValue: true },
                        }
                    }
                },
            },

            editable: {
                mode: "popup"
            },
            toolbar: [{ template: kendo.template($("#toolbartemplate").html()) },/* { name: "data", text: " &nbsp;" + locale("Data"), iconClass: "fad fa-book-open commandIconOpacity" },*/ { name: "metrics", text: " &nbsp;" + locale("Metrics"), iconClass: "fad fa-ruler-combined commandIconOpacity" }],
            sortable: true,
            reorderable: true,
            resizable: true,
            navigatable: true,
            pageable: {
                refresh: true,
                pageSizes: true,
                buttonCount: 5,
            },
            noRecords: true,
            messages: {
                noRecords: "Create your first Checklist!"
            },
            filterable: true,
            columns: [

                { command: { name: "edit", text: { edit: " " }, iconClass: "fad fa-pen commandIconOpacity" }, title: " ", width: "55px", media: "(min-width: 850px)" },
                { command: { name: "chkoptions", text: "", click: showChecklistOptions, iconClass: "fad fa-list-ol commandIconUserSpots" }, title: " ", width: "55px" },

                {
                    field: "name",
                    title: locale("Name"),
                    width: "400px",
                    media: "(min-width: 350px)",
                    filterable: {
                        multi: true,
                        search: true
                    }
                },

                {
                    field: "description",
                    title: locale("Description"),
                    width: "300px",
                    media: "(min-width: 850px)",
                    filterable: {
                        multi: true,
                        search: true
                    }
                },
                {
                    field: "send_by_email",
                    title: "Enviar por email",
                    template: "#= send_by_email ? 'SI' : 'NO' #"
                },
                {
                    field: "enabled",
                    title: "Habilitado",
                    hidden: true
                },
                { command: { name: "destroy", text: " ", iconClass: "fad fa-trash commandIconDelete" }, title: " ", width: "80px", media: "(min-width: 850px)" }

            ],
        }).data("kendoGrid");

    };

    return Checklist;
}());

function showChecklistOptions(e) {
    e.preventDefault();
    let row = this.dataItem($(e.currentTarget).closest("tr"));
    let url = "config-checklistoptions?id=" + row.id;
    document.location.href = url;
}