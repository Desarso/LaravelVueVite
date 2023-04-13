//import { locale } from "moment";

$(document).ready(function() {
    var tt = new TicketType();
    tt.initGrid();
    fixKendoGridHeight();

    $("#export").click(function(e) {
        var grid = $("#gridTicketTypes").data("kendoGrid");
        grid.saveAsExcel();
    });

    $(".k-grid-items").click(function(e) { // note that the class is k-grid-new since the name of the command is new
        window.location = 'config-items';
    });

});


var TicketType = /** @class */ (function() {
    function TicketType() {}

    TicketType.prototype.initGrid = function() {
        window.grid = $("#gridTicketTypes").kendoGrid({
            excel: {
                fileName: "Whagons Ticket Types.xlsx",

            },
            dataSource: {
                transport: {
                    read: {
                        url: "getTicketTypes",
                        type: "get",
                        dataType: "json"
                    },
                    create: {
                        url: "api/createTicketType",
                        type: "post",
                        dataType: "json",
                    },
                    update: {
                        url: "api/updateTicketType",
                        type: "post",
                        dataType: "json"
                    },
                    destroy: {
                        url: "api/deleteTicketType",
                        type: "delete",
                    }
                },
                pageSize: 20,
                schema: {
                    model: {
                        id: "id",
                        fields: {
                            id: { type: "number", editable: false, nullable: true },
                            name: { editable: true, field: "name", type: "string", validation: { required: true } },
                            description: { editable: true, field: "description", type: "string" },
                            idteam: { editable: true, field: "idteam", type: "number", validation: { required: true } },
                            icon: { editable: true, field: "icon", type: "string", defaultValue: "fad fa-exclamation-circle" },
                            color: { editable: true, field: "color", type: "string", defaultValue: "#fd774d" },
                            iscleaningtask: { editable: true, field: "iscleaningtask", type: "boolean" },
                            template: { field: "template" }
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
                    field: "name",
                    title: locale("Name"),
                    width: "300px",
                    template: "#=formatTicketTypeName(name, description, icon, color, iscleaningtask, template)#",
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
                    width: "300px",
                    media: "(min-width: 450px)",
                    filterable: false
                },
                {
                    field: "idteam",
                    title: locale("Team"),
                    width: "200px",
                    values: window.teams,
                    template: "#=formatTeam(idteam)#",
                    media: "(min-width: 450px)",
                    filterable: {
                        multi: true,
                        search: true
                    }
                },
                {
                    field: "icon",
                    title: locale("Icon"),
                    width: "150px",
                    hidden: true,
                    media: "(min-width: 450px)"
                },

                {
                    field: "color",
                    title: locale("Color"),
                    width: "150px",
                    hidden: true,
                    media: "(min-width: 450px)",
                    editor: colorPickerEditor
                },
                {
                    field: "iscleaningtask",
                    title: locale("Cleaning Task"),
                    hidden: true,
                    editor: checkBoxEditor,
                    media: "(min-width: 450px)"
                },
                {
                    field: "showingrid",
                    title: "Mostrar en grilla",
                    hidden: true,
                    editor: checkBoxEditor,
                    media: "(min-width: 450px)"
                },
                { command: { name: "destroy", text: " ", iconClass: "fad fa-trash commandIconDelete" }, title: " ", width: "80px", media: "(min-width: 850px)" }
            ],
        }).data("kendoGrid");
    };

    return TicketType;
}());

function colorPickerEditor(container, options) {
    let input = $("<input/>");
    input.attr("name", options.field);
    input.appendTo(container);
    input.kendoColorPicker({
        value: options.model.color,
        buttons: false
    });
}

function formatTeam(idteam) {
    let team = global_teams.find(o => o.value === idteam);
    if (typeof team == "undefined") return "N/A";
    return "<i class='fas fa-hard-hat' style='color:" + team.color + "'></i> " + "<span>" + team.text + "</span>";
}


function formatTicketTypeName(name, description, icon, color, iscleaningtask, template) {
    let result = "";
    description = description == null ? '' : description;

    result += "<div style='display:inline-block; vertical-align: middle; margin-right: 5px;'>"
    result += "<i class='" + icon + "' style='font-size: 1.3em; color:" + color + "'></i>";
    result += "</div>";
    result += "<div style='display:inline-block; vertical-align: middle'>";
    result += name;
    // result += "<br><small style='opacity: 0.6'>" + description + "</small>";
    if (iscleaningtask == 1)
        result += "<span style='margin-left: 5px;'title='Ticket Type used by the Cleaning Module' class='badge badge-light'>Limpieza & Desinfección</span>";
    if (template != null)
        result += '  <i class="fas fa-ballot-check" title="Task type has a template" style="opacity: 0.7"></i>';
    result += "</div>";
    return result;
}