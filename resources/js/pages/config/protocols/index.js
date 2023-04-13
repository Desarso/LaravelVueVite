window.selectedProtocol = null;

$(document).ready(function() {
    var p = new Protocol();
    p.initGrid();
    fixKendoGridHeight();

    $("#export").click(function(e) {
        var grid = $("#gridProtocols").data("kendoGrid");
        grid.saveAsExcel();
    });

    $("#btnSaveHtml").click(function() {
        saveHtml();
    });

    initEditor();

});

function initEditor() {
    editor = $("#editor").kendoEditor({
        tools: [
            "bold",
            "italic",
            "underline",
            "justifyLeft",
            "justifyCenter",
            "justifyRight",
            "insertUnorderedList",
            "createLink",
            "unlink",
            "insertImage",
            "tableWizard",
            "createTable",
            "addRowAbove",
            "addRowBelow",
            "addColumnLeft",
            "addColumnRight",
            "deleteRow",
            "deleteColumn",
            "mergeCellsHorizontally",
            "mergeCellsVertically",
            "splitCellHorizontally",
            "splitCellVertically",
            "formatting",
            {
                name: "fontName",
                items: [
                    { text: "Andale Mono", value: "Andale Mono" },
                    { text: "Arial", value: "Arial" },
                    { text: "Arial Black", value: "Arial Black" },
                    { text: "Book Antiqua", value: "Book Antiqua" },
                    { text: "Comic Sans MS", value: "Comic Sans MS" },
                    { text: "Courier New", value: "Courier New" },
                    { text: "Georgia", value: "Georgia" },
                    { text: "Helvetica", value: "Helvetica" },
                    { text: "Impact", value: "Impact" },
                    { text: "Symbol", value: "Symbol" },
                    { text: "Tahoma", value: "Tahoma" },
                    { text: "Terminal", value: "Terminal" },
                    { text: "Times New Roman", value: "Times New Roman" },
                    { text: "Trebuchet MS", value: "Trebuchet MS" },
                    { text: "Verdana", value: "Verdana" },
                ]
            },
            "fontSize",
            "foreColor",
            "backColor",
        ]
    }).data("kendoEditor");
}

var Protocol = /** @class */ (function() {
    function Protocol() {}

    Protocol.prototype.initGrid = function() {
        window.grid = $("#gridProtocols").kendoGrid({
            excel: {
                fileName: "Whagons Protocols.xlsx",

            },
            dataSource: {
                transport: {
                    read: {
                        url: "getProtocols",
                        type: "get",
                        dataType: "json"
                    },
                    create: {
                        url: "api/createProtocol",
                        type: "post",
                        dataType: "json",
                    },
                    update: {
                        url: "api/updateProtocol",
                        type: "post",
                        dataType: "json"
                    },
                    destroy: {
                        url: "api/deleteProtocol",
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
                            idtype: { editable: true, field: "idtype", type: "number", validation: { required: { message: "Categoría es requerida" } } },
                            version: { editable: true, field: "version", type: "string" },
                            code: { editable: true, field: "code", type: "string" },
                            smallimage: { editable: true, field: "smallimage", type: "string" },
                            image: { editable: true, field: "image", type: "string" },
                            html: { editable: true, field: "html", type: "string" },
                            isemergency: { editable: true, field: "isemergency", type: "boolean", defaultValue: false },
                            activated: { editable: true, field: "activated", type: "boolean", defaultValue: false },
                            reference: { editable: true, field: "code", type: "string" },
                            lan: { editable: true, field: "lan", type: "string", validation: { required: { message: "Idioma es requerido" } } },
                            qrcode: { editable: true, field: "qrcode", type: "string" },
                            enabled: { editable: true, field: "enabled", type: "boolean", defaultValue: true },
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
                buttonCount: 5,
            },
            filterable: true,

            columns: [
                { command: { name: "edit", text: { edit: " " }, iconClass: "fad fa-pen commandIconOpacity" }, title: " ", width: "80px", media: "(min-width: 850px)" },
                {
                    field: "lan",
                    title: locale("Language"),
                    width: "150px",
                    hidden: true,
                },
                {
                    field: "name",
                    title: locale("Name"),
                    width: "200px",
                    filterable: {
                        multi: true,
                        search: true
                    }
                },
                {
                    field: "idtype",
                    title: locale("Category"),
                    width: "120px",
                    values: window.categories,
                    filterable: {
                        multi: true,
                        search: true
                    }
                },
                {
                    field: "version",
                    title: locale("Version"),
                    width: "80px",
                    filterable: {
                        multi: true,
                        search: true
                    }
                },
                {
                    field: "smallimage",
                    title: locale("Small Image"),
                    width: "100px",
                    filterable: {
                        multi: true,
                        search: true
                    }
                },
                {
                    field: "image",
                    title: locale("Image"),
                    width: "100px",
                    filterable: {
                        multi: true,
                        search: true
                    }
                },
                {
                    field: "code",
                    title: locale("Code"),
                    width: "150px",
                    filterable: {
                        multi: true,
                        search: true
                    },
                    hidden: true
                },
                {
                    field: "isemergency",
                    title: locale("Emergency Protocol"),
                    width: "150px",
                    editor: checkBoxEditor,
                    hidden: true
                },
                {
                    field: "activated",
                    title: locale("Activated"),
                    width: "150px",
                    editor: checkBoxEditor,
                    hidden: true
                },
                {
                    field: "qrcode",
                    title: locale("QR Code"),
                    width: "150px",
                },
                {
                    field: "enabled",
                    title: locale("Enabled"),
                    width: "150px",
                    editor: checkBoxEditor,
                    hidden: true
                },
                { command: { text: "html", click: showDetails }, title: " ", width: "100px" },
                { command: { name: "destroy", text: " ", iconClass: "fad fa-trash commandIconDelete" }, title: " ", width: "80px", media: "(min-width: 850px)" }
            ],
        }).data("kendoGrid");
    };

    return Protocol;
}());

function showDetails(e) {
    e.preventDefault();
    editor.value("");
    window.selectedProtocol = this.dataItem($(e.currentTarget).closest("tr"));
    editor.exec("inserthtml", { value: window.selectedProtocol.html });
    $("#modalRichText").modal("show");

}

function saveHtml() {
    let data = { "id": window.selectedProtocol.id, "html": editor.value() };

    let request = callAjax('saveHtml', 'POST', data, false);

    request.done(function(result) {

        $("#gridProtocols").data("kendoGrid").dataSource.read();
        $("#modalRichText").modal("hide");

    }).fail(function(jqXHR, status) {
        //toastr.error('La acción no se puedo completar', 'Hubo un problema!');
    });
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

function formatAssetName(name, description, icon, color, enabled) {
    let result = "";

    enabled == false ? result += '<div  style="display:inline-block; vertical-align: middle; margin-right: 5px; opacity: 0.5; text-decoration: line-through;" >' :
        result += "<div style='display:inline-block; vertical-align: middle; margin-right: 5px;'>"

    result += "<i class='" + icon + "' style='font-size: 1em; color:" + color + "'></i>   ";

    result += "<strong>" + name + "</strong>";
    result += "<br><small style='opacity: 0.6'>" + description + "</small>";
    result += "</div>";
    return result;
}