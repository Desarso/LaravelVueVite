 window.roleAction = "create";

 $(document).ready(function() {
     initListViewRoles();

     $(document).on("click", ".btn-edit-role", function(event) {
         $("#div-role-update-buttons").show();
         $("#div-role-create-buttons").hide();
         $("#title-modal-role").html("<i id='icon-back-role' class='fas fa-arrow-left'></i>  Editar role");
         setRole();
     });

     $(document).on("click", "#icon-back-role", function(event) {
         $("#modal-role").modal("hide");
         $("#modal-team-role").modal("show");
     });

     $(document).on("click", ".btn-delete-role", function(event) {

         Swal.fire({
             title: locale('Delete'),
             text: locale("Delete") + ' ' + window.roleSelected.name + "?",
             type: 'warning',
             buttonsStyling: true,
             showCancelButton: true,
             confirmButtonColor: '#3085d6',
             cancelButtonColor: '#d33',
             confirmButtonText: locale('Delete'),
             confirmButtonClass: 'btn btn-primary',
             cancelButtonClass: 'btn btn-danger ml-1',
             cancelButtonText: locale('Cancel'),
             buttonsStyling: false
         }).then(function(result) {
             if (result.value) deleteRole();
         });
     });

     var validator = $("#form-role").kendoValidator().data("kendoValidator");

     $("#form-role").submit(function(event) {
         event.preventDefault();

         if (validator.validate()) {
             let data = $("#form-role").serializeFormJSON();
             data['permissions'] = getPermissions();
             window.roleAction == "create" ? createRole(data) : updateRole(data);
         }
     });
 });

 function setRole() {
     $("#modal-team-role").modal("hide");
     $("#modal-role").modal("show");
     $("#txt-role-id").val(window.roleSelected.id);
     $("#txt-role-name").val(window.roleSelected.name);

     let permissions = JSON.parse(window.roleSelected.permissions);

     $.each(permissions, function(key, value) {
         $('#permission-' + key).prop('checked', value);
     });
 }

 function initListViewRoles() {
     dataRoles = new kendo.data.DataSource({
         transport: {
             read: {
                 url: "getRoles",
                 type: "GET",
                 dataType: "JSON"
             },
         },
         schema: {
             model: {
                 id: "id",
                 fields: {
                     id: { type: "number" },
                 }
             }
         },
     });

     listViewRoles = $("#listViewRoles").kendoListView({
         dataSource: dataRoles,
         template: kendo.template($("#role-template").html()),
         selectable: "single",
         height: 600,
         scrollable: "endless",
         change: function(e) {
             window.roleSelected = dataRoles.getByUid(e.sender.select().data("uid"));
             console.log(window.roleSelected);
         },
         dataBound: function(e) {
             if (this.dataSource.data().length == 0) {
                 $("#listViewRoles").append("<div class='text-center alert alert-danger mt-1 alert-validation-msg' role='alert'><span>Arrastre elementos</span> <i class='fal fa-hand-paper mr-1 align-middle'></i></div>");
             }
         }
     }).data("kendoListView");
 }

 function getPermissions() {
     let permissions = {
         create: $("#permission-create").is(":checked") ? true : false,
         edit: $("#permission-edit").is(":checked") ? true : false,
         delete: $("#permission-delete").is(":checked") ? true : false,
         verify: $("#permission-verify").is(":checked") ? true : false,
         evaluate: $("#permission-evaluate").is(":checked") ? true : false,
         escalate: $("#permission-escalate").is(":checked") ? true : false,
         changestatus: $("#permission-changestatus").is(":checked") ? true : false,
         multitask: $("#permission-multitask").is(":checked") ? true : false,
         assigntask: $("#permission-assigntask").is(":checked") ? true : false,
         setduration: $("#permission-setduration").is(":checked") ? true : false,
         editfinished: $("#permission-editfinished").is(":checked") ? true : false,
         setduedate: $("#permission-setduedate").is(":checked") ? true : false
     };

     return JSON.stringify(permissions);
 }

 $("#btn-create-role").click(function() {
     window.roleAction = "create";
     $("#form-role").submit();
 });

 function createRole(data) {
     let request = callAjax("createRole", 'POST', data, true);

     request.done(function(result) {

         if (result.success) {
             toastr.success("Rol " + result.model.name + " creado");
             $("#modal-role").modal("hide");
             $('#form-role').trigger("reset");
             listViewRoles.dataSource.read();
         }

     }).fail(function(jqXHR, status) {
         $.unblockUI();
         toastr.error("Error");
     });
 }

 $("#btn-update-role").click(function() {
     window.roleAction = "update";
     $("#form-role").submit();
 });

 function updateRole(data) {
     let request = callAjax("updateRole", 'POST', data, true);

     request.done(function(result) {

         if (result.success) {
             toastr.success("Rol " + result.model.name + " editado");
             $("#modal-role").modal("hide");
             $('#form-role').trigger("reset");
             listViewRoles.dataSource.read();
         }

     }).fail(function(jqXHR, status) {
         $.unblockUI();
         toastr.error("Error");
     });
 }

 function deleteRole() {
     let request = callAjax("deleteRole", 'POST', { "id": window.roleSelected.id }, true);

     request.done(function(result) {

         if (result.success) {
             toastr.success("Role " + result.model.name + " eliminado <button type='button' class='btn btn-light btn-sm' onclick='restoreRole()'>DESHACER</button>");
             listViewRoles.dataSource.read();
         } else {
             toastr.warning("Role " + result.model.name + " tiene relaciones activas");
         }

     }).fail(function(jqXHR, status) {
         $.unblockUI();
         toastr.error("Error");
     });
 }

 function restoreRole() {
     let request = callAjax("restoreRole", 'POST', { "id": window.roleSelected.id }, true);

     request.done(function(result) {

         if (result.success) {
             toastr.success("Rol " + result.model.name + " recuperado");
             listViewRoles.dataSource.read();
         }

     }).fail(function(jqXHR, status) {
         $.unblockUI();
         toastr.error("Error");
     });
 }