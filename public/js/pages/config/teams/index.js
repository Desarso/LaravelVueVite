teamSelected = [];

$(document).ready(function() {

    var obj = new Teams();
    obj.initGrid();

    $("#export").click(function(e) {
        var grid = $("#gridTeams").data("kendoGrid");
        grid.saveAsExcel();
    });

    $(".k-grid-users").click(function(e) { // note that the class is k-grid-new since the name of the command is new
        window.location = 'config-users';
    });
});

var Teams = (function() {
    function Teams() {}
    Teams.prototype.initGrid = function() {
        $("#gridTeams").kendoGrid({
            dataSource: {
                transport: {
                    read: {
                        url: "getTeams",
                        type: "get",
                        dataType: "json"
                    },
                    create: {
                        url: "api/createTeam",
                        type: "post",
                        dataType: "json",
                    },
                    update: {
                        url: "api/updateTeam",
                        type: "post",
                        dataType: "json"
                    },
                    destroy: {
                        url: "api/deleteTeam",
                        type: "delete",
                    }
                },
                pageSize: 20,
                schema: {
                    model: {
                        id: "id",
                        fields: {
                            id: { editable: false, nullable: false, type: "number" },
                            name: { editable: true, field: "name", type: "string", validation: { required: { message: "Nombre es requerido" } } },
                            description: { editable: true, field: "description", type: "string" },
                            color: { editable: true, field: "color", type: "string", defaultValue: "#000" },
                            users: { editable: true, field: "users" },
                            emails: { editable: true, field: "emails", type: "string" },
                            bosses: { field: "bosses" },
                        }
                    }
                },
                requestEnd: function(e) {
                    if (e.type == 'destroy' && !e.response.success) {
                        $("#gridTeams").data("kendoGrid").dataSource.read();
                        deleteMessage(e.response.relations);
                    }
                },
            },
            pageable: {
                refresh: true,
                pageSizes: true,
                buttonCount: 5,
            },
            editable: {
                mode: "popup"
            },

            toolbar: [{ template: kendo.template($("#toolbartemplate").html()) }, ],
            height: 700,
            sortable: true,
            filterable: true,
            detailInit: detailInit,
            detailExpand: function(e) {
                teamSelected = this.dataItem(e.masterRow);
                var grid = e.sender;
                var rows = grid.element.find(".k-master-row").not(e.masterRow);

                rows.each(function(e) {
                    grid.collapseRow(this);
                });
            },
            dataBound: function() {
                this.expandRow(this.tbody.find("tr.k-master-row").first());
                $('[data-toggle="tooltip"]').tooltip();
            },
            edit: function(e) {
                $("label[for='users']").hide();
                $("input[name='users']").hide();
            },
            columns: [
                { command: { name: "edit", text: { edit: " " }, iconClass: "fad fa-pen commandIconOpacity" }, title: " ", width: "100px", media: "(min-width: 850px)" },
                {
                    field: "name",
                    title: "Equipo",
                    template: "#=formatTeamName(name, color)#",
                    width: "200px",
                    filterable: {
                        multi: true,
                        search: true
                    }
                },
                {
                    field: "users",
                    title: "Usuarios",
                    template: "#=formatUser(users)#"
                },
                {
                    field: "color",
                    title: "Color",
                    width: "200px",
                    hidden: true,
                    editor: colorPickerEditor,
                },
                {
                    field: "bosses",
                    title: "Jefes",
                    values: global_users,
                    editor: editorMultiSelectUser,
                    width: "200px",
                    filterable: false,
                    hidden: true
                },
                {
                    field: "emails",
                    title: "Emails",
                    width: "200px",
                    filterable: {
                        multi: true,
                        search: true
                    }
                },
                { command: { name: "destroy", text: " ", iconClass: "fad fa-trash commandIconDelete" }, title: " ", width: "80px", media: "(min-width: 850px)" }
            ]
        });

    };
    return Teams;
}());


function editorMultiSelectUser(container, options)
{
    editorMultiSelectUser = $('<select style="width: 250px"  name="' + options.field + '"/>')
        .appendTo(container)
        .kendoMultiSelect({
            dataTextField: "text",
            dataValueField: "value",
            dataSource: global_users.filter(user => (user.enabled == true && user.deleted_at == null)),
            autoClose: true,
        }).data("kendoMultiSelect");
}

function formatUser(users) {
    let result = "<ul class='list-unstyled users-list m-0  d-flex align-items-center'>";

    for (var i = 0; i < users.length; i++) {
        let user = getUser(users[i].iduser);

        if (user != undefined) {
            if (i == 4) break;

            result += "<li data-toggle='tooltip' data-popup='tooltip-custom' data-placement='top' data-original-title='" + user.text + "' class='avatar pull-up'>" +
                "<img class='media-object rounded-circle' src='" + user.urlpicture + "' alt='Avatar' height='30' width='30'>" +
                "</li>";
        }
    }

    let span_more = users.length > 4 ? "<li class='d-inline-block pl-50'><span>+" + (users.length - 4) + " más</span></li>" : "";
    return result + span_more;
}

function formatTeamName(name, color) {
    return "<i class='fa fa-circle' style='color:" + color + "'></i> <strong>" + name + "</strong>";
}

function detailInit(e) {

    teamSelected = e.data;

    $("<div id='gridUsers'></div>").appendTo(e.detailCell).kendoGrid({
        dataSource: {
            transport: {
                read: {
                    url: "getUserTeams",
                    type: "get",
                    dataType: "json",
                    data: function() {
                        return {
                            idteam: teamSelected.id,
                        };
                    }
                },
                create: {
                    url: "api/createUserTeam",
                    type: "post",
                    dataType: "json",
                    data: function() {
                        return {
                            idteam: teamSelected.id,
                        }
                    }
                },
                update: {
                    url: "api/updateUserTeam",
                    type: "post",
                    dataType: "json"
                },
                destroy: {
                    url: "api/deleteUserTeam",
                    type: "delete"
                },
            },
            pageSize: 20,
            schema: {
                model: {
                    id: "id",
                    fields: {
                        id: { editable: false, nullable: false, type: "number" },
                        iduser: { editable: true, field: "iduser", type: "number", nullable: false },
                        idrole: { editable: true, field: "idrole", type: "number", nullable: false },
                        core_team: { editable: true, field: "core_team", type: "boolean", defaultValue: false },
                    }
                }
            },
        },
        editable: {
            mode: "popup"
        },
        toolbar: [{ name: "create", text: "Agregar Usuario" }, { name: "search", text: "Buscar" }],
        noRecords: {
            template: "<div class='alert alert-danger mt-1 alert-validation-msg' role='alert'><i class='feather icon-info mr-1 align-middle'></i><span>No hay usuarios</span></div>"
        },
        scrollable: false,
        sortable: true,
        filterable: true,
        pageable: {
            refresh: true,
            pageSizes: true,
            buttonCount: 5,
            messages: {
                display: "{2} ítems",
                empty: "No hay ítems",
                page: "Página",
                of: "de {0}",
                itemsPerPage: "ítems por página",
                first: "Ir a la primera página",
                previous: "Ir a la página anterior",
                next: "Ir a la siguiente página",
                last: "Ir a la última página",
                refresh: "Refrescar"
            }
        },
        dataBound: function() {
            $('[data-toggle="tooltip"]').tooltip();
        },
        columns: [
            {
                field: "iduser",
                title: "Usuario",
                values: global_users.filter(user => (user.enabled == true && user.deleted_at == null)),
                template: "#=formatUserTeam(iduser)#",
                width: 110,
                filterable: {
                    multi: true,
                    search: true
                }
            },
            {
                field: "idrole",
                title: "Rol",
                values: window.global_roles,
                editor: roleDropDownListEditor,
                width: 110,
                filterable: {
                    multi: true,
                    search: true
                }
            },
            {
                field: "core_team",
                title: "Equipo principal",
                template: "#=formatYesNo(core_team)#",
                editor: checkBoxEditor,
                width: 110
            },
            { command: { name: "edit", text: { edit: " " }, iconClass: "fad fa-pen commandIconOpacity" }, title: " ", width: "50px", media: "(min-width: 850px)" },
            { command: { name: "destroy", text: " ", iconClass: "fas fa-trash commandIconOpacity" }, title: " ", width: "50px" },
        ],
    });
}

function roleDropDownListEditor(container, options) {
    $('<input data-text-field="text" data-value-field="value" data-bind="value:' + options.field + '"/>')
        .appendTo(container)
        .kendoDropDownList({
            dataSource: options.values
        });
}

function colorPickerEditor(container, options) {
    let input = $("<input/>");
    input.attr("name", options.field);
    input.appendTo(container);
    input.kendoColorPicker({
        value: options.model.color,
        buttons: false
    });
}

function formatUserTeam(iduser) {
    if (iduser == 0) return "";

    let user = getUser(iduser);

    return "<li data-toggle='tooltip' data-popup='tooltip-custom' data-placement='top' data-original-title='" + user.text + "' class='avatar pull-up'>" +
        "<img class='media-object rounded-circle' src='" + user.urlpicture + "' alt='Avatar' height='30' width='30'> " +
        "</li>" + user.text;
}