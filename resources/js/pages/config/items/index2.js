$(document).ready(function() {
    var i = new Item();
    i.initGrid();
    fixKendoGridHeight();

    $("#export").click(function(e) {
        var grid = $("#gridItems").data("kendoGrid");
        grid.saveAsExcel();
    });

    $(".k-grid-tasktypes").click(function(e) { // note that the class is k-grid-new since the name of the command is new
        window.location = 'config-tasktypes';
    });

    // Create TreeView  
    window.treeView = new WHTreeView(window.spots, saveItemSpots);

});

////

var Item = /** @class */ (function() {
    function Item() {}

    Item.prototype.initGrid = function() {
        window.grid = $("#gridItems").kendoGrid({
            excel: {
                fileName: "Whagons Items.xlsx",

            },
            dataSource: {
                transport: {
                    read: {
                        url: "getItems",
                        type: "get",
                        dataType: "json"
                    },
                    create: {
                        url: "api/createItem",
                        type: "post",
                        dataType: "json",
                    },
                    update: {
                        url: "api/updateItem",
                        type: "post",
                        dataType: "json"
                    },
                    destroy: {
                        url: "api/deleteItem",
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
                            idtype: { editable: true, field: "idtype", type: "number", validation: { required: { message: "Tipo es requerido" } } },
                            idteam: { editable: true, field: "idteam", type: "number", validation: { required: { message: "Equipo es requerido" } } },
                            idchecklist: { editable: true, field: "idchecklist", type: "number", nullable: true },
                            idpriority: { editable: true, field: "idpriority", type: "number", defaultValue: 1 },
                            idprotocol: { editable: true, field: "idprotocol", type: "number", nullable: true },
                            sla: { editable: true, field: "sla", type: "number" },
                            isprivate: { editable: true, field: "isprivate", type: "boolean", defaultValue: false },
                            isglitch: { editable: true, field: "isglitch", type: "boolean", defaultValue: false },
                            enabled: { editable: true, field: "enabled", type: "boolean", defaultValue: true },
                            users: { field: "users" }
                        }
                    }
                },
                requestEnd: function(e) {
                    if (e.type == 'destroy' && !e.response.success) {
                        $("#gridItems").data("kendoGrid").dataSource.read();
                        deleteMessage(e.response.relations);
                    }
                },
            },
            editable: {
                mode: "popup"
            },
            toolbar: [{ name: "excel", text: "Exportar a Excel" }, { template: kendo.template($("#toolbartemplate").html()) }],
            excel: {
                fileName: "Whagons ítems.xlsx",
                filterable: false,
                allPages: true
            },
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
                { command: { name: "spots", text: "", click: showItemSpots, iconClass: "fad fa-store-alt commandIconUserSpots" }, title: " ", width: "100px" },

                {
                    field: "name",
                    title: locale("Name"),
                    width: "300px",
                    template: "#=formatItemName(name, description, isprivate, sla, enabled, idtype, isglitch)#",
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
                    editor: textAreaEditor,
                    hidden: true,
                    media: "(min-width: 450px)"
                },
                {
                    field: "idtype",
                    title: locale("Type"),
                    width: "250px",
                    values: global_ticket_types,
                    template: "#=formatTicketTypeName(idtype)#",
                    media: "(min-width: 450px)",
                    filterable: {
                        multi: true,
                        search: true
                    }
                },
                {
                    field: "idteam",
                    title: locale("Team"),
                    width: "200px",
                    values: global_teams,
                    template: "#=formatTeam(idteam)#",
                    media: "(min-width: 450px)",
                    filterable: {
                        multi: true,
                        search: true
                    }
                },
                {
                    field: "idchecklist",
                    title: locale("Checklist"),
                    editor: dropDownListEditor,
                    width: "200px",
                    values: window.global_checklist,
                    media: "(min-width: 450px)",
                    filterable: {
                        multi: true,
                        search: true
                    }
                },
                {
                    field: "idpriority",
                    title: locale("Priority"),
                    width: "100px",
                    hidden: true,
                    values: global_priorities,
                    media: "(min-width: 450px)",
                    filterable: {
                        multi: true,
                        search: true
                    }
                },
                {
                    field: "idprotocol",
                    title: locale("Protocol"),
                    editor: dropDownListEditor,
                    width: "150px",
                    values: window.protocols,
                    media: "(min-width: 450px)",
                    filterable: {
                        multi: true,
                        search: true
                    }
                },
                {
                    field: "users",
                    title: locale("Responsible"),
                    width: "200px",
                    values: global_users, // para que aparezca en el filtro
                    editor: editorMultiSelectUser,
                    filterable: false,
                    template: "#=formatUsers(users)#",
                    filterable: {
                        multi: true,
                        search: true
                    }

                },
                {
                    field: "sla",
                    title: locale("SLA"),
                    hidden: true,
                    media: "(min-width: 850px)"

                },
                {
                    field: "isglitch",
                    title: locale("Glitch/Essential Product"),
                    template: "#=formatYesNo(isprivate)#",
                    editor: checkBoxEditor,
                    hidden: true,
                    media: "(min-width: 850px)"

                },
                {
                    field: "isprivate",
                    title: locale("Private"),
                    template: "#=formatYesNo(isprivate)#",
                    editor: checkBoxEditor,
                    hidden: true,
                    media: "(min-width: 850px)"

                },
                {
                    field: "enabled",
                    title: locale("Enabled"),
                    template: "#=formatYesNo(enabled)#",
                    editor: checkBoxEditor,
                    hidden: true,
                    media: "(min-width: 850px)"

                },
                { command: { name: "destroy", text: " ", iconClass: "fad fa-trash commandIconDelete" }, title: " ", width: "80px", media: "(min-width: 850px)" }
            ],
        }).data("kendoGrid");
    };

    return Item;
}());

function showItemSpots(e) {
    e.preventDefault();
    window.itemSelected = this.dataItem($(e.currentTarget).closest("tr"));
    window.treeView.open(window.itemSelected.name, window.itemSelected.spots);
}

function formatUsers(users) {
    let result = "<ul class='list-unstyled users-list m-0  d-flex align-items-center'>";

    for (var i = 0; i < users.length; i++) {
        let user = getUser(users[i].value);

        if (user != undefined) {
            if (i == 4) break;

            result += "<li data-toggle='tooltip' data-popup='tooltip-custom' data-placement='bottom' data-original-title='' class='avatar pull-up'>" +
                "<img class='media-object rounded-circle' src='" + user.urlpicture + "' alt='Avatar' height='30' width='30'>" +
                "</li>";
        }
    }

    let span_more = users.length > 4 ? "<li class='d-inline-block pl-50'><span>+" + (users.length - 4) + " más</span></li>" : "";
    return result + span_more;
}

function saveItemSpots(data) {
    data["iditem"] = window.itemSelected.id;
    let request = callAjax('saveItemSpots', 'POST', data, true);
    request.done(function(result) {
        //PNotify.closeAll();
        //PNotify.success({ title: 'Exito', text: 'La acción se completó con éxito' });
        // dialog.close();
        $("#gridItems").data("kendoGrid").dataSource.read();

    }).fail(function(jqXHR, status) {
        console.log('fail saveItemSpots');
        //        PNotify.closeAll();
        //PNotify.error({ title: 'Problemas', text: 'La acción no se puedo completar' });
    });
}



////// Helpers //////





function formatItemName(name, description, isprivate, sla, enabled, idtype, isglitch) {
    let result = "";
    let isCleaning = isCleaningTicketType(idtype);
    description = description == null ? '' : description;

    if (enabled == 1)
        result += "<div style='display:inline-block; vertical-align: middle'>";
    else
        result += "<div style='opacity: 0.3; text-decoration: line-through;display:inline-block; vertical-align: middle'>";


    if (isprivate == 1)
        result += "<i class='fa fa-lock text-primary' title='Tarea privada al Team'></i> ";
    if (isglitch == 1) {
        if (isCleaning == 0)
            result += "<i class='fa fa-exclamation-triangle text-danger' style='font-size:0.7rem;opacity:1' title='Tarea representa un problema a solucionar'></i> ";
        else
            result += "<i class='fa fa-virus ' style='color: #6146D9;font-size:0.7rem;opacity:1' title='Tarea representa un problema a solucionar'></i> ";
    }
    result += name;
    result += "<br><small style='opacity: 0.6'>" + description + "</small>";
    if (sla != null && sla > 0)
        result += "<small><i class='fas fa-clock text-light' ></i> " + formatSLA(sla) + "</small>";

    result += "</div>";
    return result;
}




function formatTeam(idteam) {
    let team = global_teams.find(o => o.value === idteam);
    if (typeof team == "undefined") return "N/A";
    return "<i class='fas fa-hard-hat' style='color:" + team.color + "'></i> " + "<span>" + team.text + "</span>";
}

function formatTicketTypeName(idtype) {
    let type = global_ticket_types.find(o => o.value === idtype);
    if (typeof type == "undefined") return "N/A";

    let result = "";
    result += "<div style='display:inline-block; vertical-align: middle; margin-right: 5px;'>"
    result += "<i class='" + type.icon + "' style='opacity: 1;font-size: 1.3em; color:" + type.color + "'></i>";
    result += "</div>";
    result += "<div style='display:inline-block; vertical-align: middle'>";
    result += type.text;
    result += "</div>";
    return result;
}