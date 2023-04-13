$(document).ready(function() {

    var role = new Role();
    role.initGrid();
    fixKendoGridHeight();

    $("#export").click(function(e) {
        var grid = $("#gridRoles").data("kendoGrid");
        grid.saveAsExcel();
    });

    $(".k-grid-roles").click(function(e) { // note that the class is k-grid-new since the name of the command is new
        e.preventDefault();
        window.location = 'config-roles';
    });

    $(document).on("change", ".checkbox-permission", function(event) {
        let data = { 'idrole': $(this).data('idrole'), 'permission': $(this).data('permission'), 'value': $(this).is(':checked') };
        changePermission(data);
    });

});


var Role = /** @class */ (function() {
    function Role() {}

    Role.prototype.initGrid = function() {
        window.grid = $("#gridRoles").kendoGrid({
            excel: {
                fileName: "Whagons Spot Types.xlsx",
            },
            dataSource: {
                transport: {
                    read: {
                        url: "getAllRoles",
                        type: "get",
                        dataType: "json"
                    },
                    create: {
                        url: "api/createRole",
                        type: "post",
                        dataType: "json",
                    },
                    update: {
                        url: "api/updateRole",
                        type: "post",
                        dataType: "json"
                    },

                    destroy: {
                        url: "api/deleteRole",
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
                            permissions: { editable: false, field: "permissions", type: "string" },
                        }
                    }
                },
                requestEnd: function(e) {
                    if (e.type == 'destroy' && !e.response.success) {
                        $("#gridRoles").data("kendoGrid").dataSource.read();
                        deleteMessage(e.response.relations);
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
            edit: function(e) {
                $("label[for='permissions']").hide();
                $("#div-checkbox-permission").hide();
            },
            columns: [
                { command: { name: "edit", text: { edit: " " }, iconClass: "fad fa-pen commandIconOpacity" }, title: " ", width: "60px", media: "(min-width: 850px)" },
                {
                    field: "name",
                    title: locale('Name'),
                    width: "150px",
                    media: "(min-width: 350px)",
                    filterable: {
                        multi: true,
                        search: true
                    }
                },
                {
                    field: "permissions",
                    title: locale('permissions'),
                    template: "#=formatPermissions(id, permissions)#",
                    width: "600px",
                    media: "(min-width: 450px)",
                    filterable: false
                },
                { command: { name: "destroy", text: " ", iconClass: "fad fa-trash commandIconDelete" }, title: " ", width: "80px", media: "(min-width: 850px)" }
            ],
        }).data("kendoGrid");
    };

    return Role;
}());

function formatPermissions(id, json) {
    if (json == "") return "";

    let permissions = JSON.parse(json);
    let result = "<div id='div-checkbox-permission' class='d-flex justify-content-start flex-wrap'>";

    $.each(permissions, function(key, value) {
        result += "<div class='custom-control custom-switch switch-md custom-switch-success mr-2 mb-1'>" +
            "<p class='mb-0'>" + key + "</p>" +
            "<input type='checkbox' class='checkbox-permission custom-control-input' data-idrole='" + id + "' data-permission='" + key + "' id='" + (id + "-" + key) + "' " + (value ? "checked" : "") + ">" +
            "<label class='custom-control-label' for='" + (id + "-" + key) + "'>" +

            "</label>" +
            "</div>";
    });

    return result + "</div>";
}

function changePermission(data) {
    let request = callAjax('changePermission', 'POST', data, true);

    request.done(function(result) {

        $("#gridRoles").data("kendoGrid").dataSource.read();

    }).fail(function(jqXHR, status) {
        alert("ERROR")
    });
}