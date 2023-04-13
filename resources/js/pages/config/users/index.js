 window.userAction = "create";

 $(document).ready(function() {

     initGridUsers();
     initTooltipTeams();
     initTooltipRoles();
     initDropDownListSchedule();
     initGridTeamRole();
     initContextMenu();

     if(window.open)
     {
        $("#modal-team-role").modal("show");
     }

     window.treeView = new WHTreeView(window.spots, saveUserSpots);

     $(document).on("click", "#btn-new-config", function(event) {
         $("#div-user-create-buttons").show();
         $("#div-user-update-buttons").hide();
         $('#form-user').trigger("reset");
         gridTeamRole.dataSource.data([]);
         $("#title-modal-user").text("Crear nuevo usuario");
         $("#modal-user").modal("show");
     });

     $("#btn-new-role").click(function(e) {
         $('#form-role').trigger("reset");
         $("#div-role-update-buttons").hide();
         $("#div-role-create-buttons").show();
         $("#modal-team-role").modal("hide");
         $("#modal-role").modal("show");
         $("#title-modal-role").html("<i id='icon-back-role' class='fas fa-arrow-left'></i>  Crear role");
     });

     $("#btn-new-team").click(function(e) {
         $('#form-team').trigger("reset");
         $("#div-team-update-buttons").hide();
         $("#div-team-create-buttons").show();
         $("#modal-team-role").modal("hide");
         $("#modal-team").modal("show");
         $("#title-modal-team").html("<i id='icon-back-team' class='fas fa-arrow-left'></i>  Crear equipo");
     });

     $("#btn-config").click(function(e) {
         $("#modal-team-role").modal("show");
     });

     $("#nav-team-tab").click(function(e) {
         $("#btn-new-team").show();
         $("#btn-new-role").hide();
     });

     $("#nav-role-tab").click(function(e) {
         $("#btn-new-role").show();
         $("#btn-new-team").hide();
     });

     var validator = $("#form-user").kendoValidator().data("kendoValidator");

     $("#form-user").submit(function(event) {
         event.preventDefault();

         if (validator.validate()) {
             let data = $("#form-user").serializeFormJSON();
             data['teams'] = JSON.stringify(gridTeamRole.dataSource.data());
             data["isadmin"] = $("#isadmin").is(":checked") ? true : false;
             window.userAction == "create" ? createUser(data) : updateUser(data);
         }
     });

     $(document).on("change", ".switch-enabled", function(event) {

        let user = $(this).data('user');

        let data = { 'id': $(this).data('id'), 'enabled': $(this).is(':checked') };

        let action = $(this).is(':checked') ? "Activar" : "Inactivar";

        Swal.fire({
            title: action,
            text: "¿" + action + " usuario " + user + "?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar',
        }).then((result) => {

            if (result.value)
            {
                disableUser(data);
            }
            else
            {
                let property = $(this).is(':checked');
                $(this).prop("checked", !property); 
            }
   
        });
     });
 });

 function saveUserSpots(data) {
     data["iduser"] = window.userSelected.id;

     let request = callAjax("saveUserSpots", 'POST', data, true);

     request.done(function(result) {

         if (result.success) {
             toastr.success("Spots asignados");
             gridUsers.dataSource.read();
         } else {
             toastr.warning("Los spots no fueron asignados");
         }

     }).fail(function(jqXHR, status) {
         $.unblockUI();
         toastr.error("Error");
     });
 }

 function makeTreeViewTitle(user) {
     let urlpicture = user.urlpicture;
     (urlpicture == null || urlpicture == "") ? (urlpicture = 'https://dingdonecdn.nyc3.digitaloceanspaces.com/general/dummy.png') : urlpicture = urlpicture;

     let title = '<img class="round" width="30" height="30" src="' + urlpicture + '"> &nbsp;&nbsp;';
     title += user.firstname + ' ' + user.lastname;
     return title;
 }

 function initContextMenu() {
     $("#context-menu").kendoContextMenu({
         target: "#gridUsers",
         filter: "td .k-grid-actions",
         showOn: "click",
         select: function(e) {
             var td = $(e.target).parent()[0];
             window.userSelected = gridUsers.dataItem($(td).parent()[0]);
             var item = e.item.id;

             switch (item) {
                 case "editUser":
                     setUser();
                     break;
                 case "deleteUser":
                     confirmDeleteUser();
                     break;
                 case "selectSpaces":
                     selectSpaces();
                     break;
                 case "resetPassword":
                     confirmResetPassword();
                     break;

             };
         }
     });
 }

 function setUser() {
     $("#div-user-update-buttons").show();
     $("#div-user-create-buttons").hide();

     $('#form-user').trigger("reset");
     gridTeamRole.dataSource.data([]);

     $("#title-modal-user").text("Editar usuario");
     $("#modal-user").modal("show");
     $("#txt-user-id").val(window.userSelected.id);
     $("#txt-user-firstname").val(window.userSelected.firstname);
     $("#txt-user-lastname").val(window.userSelected.lastname);
     $("#txt-user-username").val(window.userSelected.username);
     $('#isadmin').prop('checked', window.userSelected.isadmin);
     $("#txt-user-clockin_code").val(window.userSelected.clockin_code);
     
     setTimeout(() => {
         dropDownListSchedule.value(window.userSelected.idschedule);
     }, 400);

     let dataSource = [];

     $.each(window.userSelected.roles, function(key, value) {
         dataSource.push({ idteam: value.pivot.idteam, idrole: value.pivot.idrole });
     });

     gridTeamRole.dataSource.data(dataSource);
 }

 function selectSpaces() {
     window.treeView.open(makeTreeViewTitle(window.userSelected), window.userSelected.spots);
 }

 function initGridUsers() {
     gridUsers = $("#gridUsers").kendoGrid({
         dataSource: {
             transport: {
                 read: {
                     url: "getUsers",
                     type: "get",
                     dataType: "json"
                 }
             },
             pageSize: 20,
             schema: {
                 model: {
                     id: "id",
                     fields: {
                         id: { type: "number", editable: false, nullable: true },
                         name: { editable: true, field: "name", type: "string", validation: { required: { message: "Nombre es requerido" } } },
                         enabled: { type: "boolean" }
                     }
                 }
             },
         },
         editable: false,
         toolbar: [{ template: kendo.template($("#template-search-panel").html()) }],
         reorderable: true,
         resizable: true,
         sortable: true,
         pageable: {
             refresh: true,
             pageSizes: true,
             buttonCount: 5
         },
         height: '700px',
         filterable: true,
         dataBound: function(e) {
             $('[data-toggle="tooltip"]').tooltip();
         },
         columns: [
            { command: { text: "", name: "actions", iconClass: "fas fa-ellipsis-v" }, title: " ", width: "70px" },
            {
                 field: "fullname",
                 title: locale("Name"),
                 width: "300px",
                 template: function(dataItem) {
                     return "<div class='user-photo'" +
                         "style='background-image: url(" + dataItem.urlpicture + ");'></div>" +
                         "<div class='user-fullname'>" + dataItem.fullname + "</div>";
                 },
                 filterable: {
                     multi: true,
                     search: true
                 }
             },
             {
                 field: "username",
                 title: locale("User"),
                 width: "300px",
                 filterable: false
             },
             {
                 field: "full_teams",
                 title: locale("Teams"),
                 width: "300px",
                 template: function(dataItem) {
                     let teams = "";

                     $.each(dataItem.teams, function(i, team) {
                         teams += "<span style='background-color:" + team.color + "' class='badge mb-1'>" + team.name + "</span> ";
                         if (i == 1) return false;
                     });

                     if (dataItem.teams.length > 2) teams += "<span style='color:black;' class='badge badge-light teams'>" + (dataItem.teams.length - 2) + "+</span>";

                     return teams;
                 },
                 filterable: false
             },
             {
                 field: "roles",
                 title: locale("Roles"),
                 width: "300px",
                 template: function(dataItem) {
                     let roles = "";

                     $.each(dataItem.roles, function(i, role) {
                         let team = global_teams.find(o => o.value === role.pivot.idteam);
                         roles += "<span style='background-color:" + team.color + "' class='badge mb-1'>" + role.name + "</span> ";
                         if (i == 1) return false;
                     });

                     if (dataItem.roles.length > 2) roles += "<span style='color:black;' class='badge badge-light roles'>" + (dataItem.roles.length - 2) + "+</span>";

                     return roles;
                 },
                 filterable: false
             },
             {
                field: "enabled",
                title: "Estado",
                width: "115px",
                template: function(dataItem) {
                    return "<div class='custom-control custom-switch custom-switch-success switch-lg'>" +
                                "<input id='enabled" + dataItem.id + "' type='checkbox' data-id='" + dataItem.id + "' data-user='" + dataItem.fullname + "' class='switch-enabled custom-control-input' " + (dataItem.enabled ? "checked" : "") + ">" +
                                "<label class='custom-control-label' for='enabled" + dataItem.id + "'>" +
                                "<span class='switch-text-left'>Activo</span>" +
                                "<span class='switch-text-right'>Inactivo</span>" +
                                "</label>" +
                            "</div>";
                },
                filterable: {
                    ui: function(element) {
                      element.kendoDropDownList({
                        dataTextField: 'text',
                        dataValueField: 'value',
                        dataSource: [{ text: 'Activo', value: true }, { text: 'Inactivo', value: false }]
                      })
                    }
                }
             }
         ],
     }).data("kendoGrid");

     setTimeout(() => { $("#btn-new-config").text(locale("Add user")) }, 300);
 }

 function initTooltipTeams() {
     $("#gridUsers").kendoTooltip({
         autoHide: true,
         showOn: "mouseenter",
         width: 200,
         position: "right",
         filter: ".k-grid-content span.teams",
         content: function(e) {
             var row = $(e.target).closest("tr");
             var dataItem = gridUsers.dataItem(row);

             let teams = "";

             $.each(dataItem.teams, function(i, team) {
                 if (i >= 2) teams += "<span style='background-color:" + team.color + "' class='badge mt-1'>" + team.name + "</span> ";
             });

             return teams;
         }
     });
 }

 function initTooltipRoles() {
     $("#gridUsers").kendoTooltip({
         autoHide: true,
         showOn: "mouseenter",
         width: 200,
         position: "right",
         filter: ".k-grid-content span.roles",
         content: function(e) {
             var row = $(e.target).closest("tr");
             var dataItem = gridUsers.dataItem(row);

             let roles = "";

             $.each(dataItem.roles, function(i, role) {
                 let team = global_teams.find(o => o.value === role.pivot.idteam);
                 if (i >= 2) roles += "<span style='background-color:" + team.color + "' class='badge mt-1'>" + role.name + "</span> ";
             });

             return roles;
         }
     });
 }

 function initDropDownListSchedule() {
     dropDownListSchedule = $("#dropDownListSchedule").kendoDropDownList({
         optionLabel: "Selecciona horario",
         dataTextField: "text",
         dataValueField: "value",
         dataSource: schedules,
         popup: { appendTo: $("#modal-user") },
         height: 400
     }).data("kendoDropDownList");
 }

 $("#add-team").click(function() {
     gridTeamRole.addRow();
     return false;
 });

 function initGridTeamRole() {
     gridTeamRole = $("#gridTeamRole").kendoGrid({
         dataSource: {
             data: [],
             pageSize: 20,
             schema: {
                 model: {
                     id: "id",
                     fields: {
                         id: { type: "number", editable: false, nullable: true },
                         idteam: { editable: true, field: "idteam", type: "number", nullable: true },
                         idrole: { editable: true, field: "idrole", type: "number", nullable: true },
                     }
                 }
             },
         },
         editable: true,
         toolbar: false,
         reorderable: false,
         resizable: false,
         sortable: false,
         pageable: false,
         height: '300px',
         filterable: false,
         columns: [{
                 field: "idteam",
                 title: "Equipo",
                 values: window.global_teams,
                 editor: dropDownListTeamEditor,
                 width: "190px",
                 filterable: false
             },
             {
                 field: "idrole",
                 title: "Rol",
                 width: "190px",
                 values: window.global_roles,
                 editor: dropDownListEditor,
                 filterable: false
             },
             { command: "destroy", text: "", title: "", width: "40px" }
         ],
     }).data("kendoGrid");
 }

 function dropDownListTeamEditor(container, options) {
     $('<input data-text-field="text" data-value-field="value" data-bind="value:' + options.field + '"/>')
         .appendTo(container)
         .kendoDropDownList({
             autoBind: false,
             optionLabel: " ",
             valuePrimitive: true,
             filter: "contains",
             dataSource: options.values,
             height: 400,
             popup: { appendTo: $("#modal-user") },
             open: function(e) {

             },
         });
 }

 function dropDownListEditor(container, options) {
     $('<input data-text-field="text" data-value-field="value" data-bind="value:' + options.field + '"/>')
         .appendTo(container)
         .kendoDropDownList({
             autoBind: false,
             optionLabel: " ",
             valuePrimitive: true,
             filter: "contains",
             dataSource: options.values,
             height: 400,
             popup: { appendTo: $("#modal-user") },
         });
 }

 $("#btn-create-user").click(function() {
     window.userAction = "create";
     $("#form-user").submit();
 });

 function createUser(data) {
     let request = callAjax("createUser", 'POST', data, true);

     request.done(function(result) {

         if (result.success) {
             toastr.success("Usuario " + result.model.fullname + " creado");
             $("#modal-user").modal("hide");
             $('#form-user').trigger("reset");
             gridTeamRole.dataSource.data([]);
             gridUsers.dataSource.read();
         } else {
             toastr.error(result.errors[0]);
         }

     }).fail(function(jqXHR, status) {
         $.unblockUI();
         toastr.error("Error");
     });
 }

 $("#btn-update-user").click(function() {
     window.userAction = "update";
     $("#form-user").submit();
 });

 function updateUser(data) {
     let request = callAjax("updateUser", 'POST', data, true);

     request.done(function(result) {

         if (result.success) {
             toastr.success("Usuario " + result.model.fullname + " editado");
             $("#modal-user").modal("hide");
             $('#form-user').trigger("reset");
             gridTeamRole.dataSource.data([]);
             gridUsers.dataSource.read();
         } else {
             toastr.error(result.errors[0]);
         }

     }).fail(function(jqXHR, status) {
         $.unblockUI();
         toastr.error("Error");
     });
 }

 function confirmDeleteUser() {
     Swal.fire({
         title: 'Eliminar',
         text: "¿Eliminar usuario " + window.userSelected.fullname + "?",
         type: 'warning',
         buttonsStyling: true,
         showCancelButton: true,
         confirmButtonColor: '#3085d6',
         cancelButtonColor: '#d33',
         confirmButtonText: 'Eliminar',
         confirmButtonClass: 'btn btn-primary',
         cancelButtonClass: 'btn btn-danger ml-1',
         cancelButtonText: 'Cancelar',
         buttonsStyling: false
     }).then(function(result) {
         if (result.value) deleteUser();
     });
 }

 function deleteUser() {
     let request = callAjax("deleteUser", 'POST', { "id": window.userSelected.id }, true);

     request.done(function(result) {

         if (result.success) {
             toastr.success("Usuario " + result.model.fullname + " eliminado <button type='button' class='btn btn-light btn-sm' onclick='restoreUser()'>DESHACER</button>");
             gridUsers.dataSource.read();
         } else {
             toastr.warning("Usuario " + result.model.fullname + " tiene relaciones activas");
         }

     }).fail(function(jqXHR, status) {
         $.unblockUI();
         toastr.error("Error");
     });
 }

 function restoreUser() {
     let request = callAjax("restoreUser", 'POST', { "id": window.userSelected.id }, true);

     request.done(function(result) {

         if (result.success) {
             toastr.success("Usuario " + result.model.fullname + " recuperado");
             gridUsers.dataSource.read();
         }

     }).fail(function(jqXHR, status) {
         $.unblockUI();
         toastr.error("Error");
     });
 }

 function disableUser(data)
 {
     let request = callAjax("disableUser", 'POST', data, true);

     request.done(function(result) {

         if (result.success) {

             let action = result.model.enabled == true ? " habilitado" : " deshabilitado";

             toastr.success("Usuario " + result.model.fullname + action);

             gridUsers.dataSource.read();
         }

     }).fail(function(jqXHR, status) {
         $.unblockUI();
         toastr.error("Error");
     });
 }

 function confirmResetPassword() {
     Swal.fire({
         title: 'Contraseña',
         text: "¿Restablecer contraseña de " + window.userSelected.fullname + "?",
         type: 'warning',
         buttonsStyling: true,
         showCancelButton: true,
         confirmButtonColor: '#3085d6',
         cancelButtonColor: '#d33',
         confirmButtonText: 'Aceptar',
         confirmButtonClass: 'btn btn-primary',
         cancelButtonClass: 'btn btn-danger ml-1',
         cancelButtonText: 'Cancelar',
         buttonsStyling: false
     }).then(function(result) {
         if (result.value) resetPassword();
     });
 }

 function resetPassword() {
     let request = callAjax("resetPassword", 'POST', { "id": window.userSelected.id }, true);

     request.done(function(result) {

         if (result.success) {
             toastr.success("Contraseña restablecida");
         }

     }).fail(function(jqXHR, status) {
         $.unblockUI();
         toastr.error("Error");
     });
 }