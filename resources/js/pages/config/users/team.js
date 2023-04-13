 window.teamAction = "create";

 $(document).ready(function() {
     initListViewTeams();
     initMultiSelectBosses();
     initMultiSelectUsers();
     initColorPalette();

     $(document).on("click", ".btn-edit-team", function(event) {
         $("#div-team-update-buttons").show();
         $("#div-team-create-buttons").hide();
         $("#title-modal-team").html("<i id='icon-back-team' class='fas fa-arrow-left'></i> " + locale("Edit team"));
         setTeam();
     });

     $(document).on("click", "#icon-back-team", function(event) {
         $("#modal-team").modal("hide");
         $("#modal-team-role").modal("show");
     });

     $(document).on("click", ".btn-delete-team", function(event) {

         Swal.fire({
             title: locale('Delete'),
             text: locale("Delete") + ' ' + window.teamSelected.name + "?",
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
             if (result.value) deleteTeam();
         });
     });

     var validator = $("#form-team").kendoValidator().data("kendoValidator");

     $("#form-team").submit(function(event) {
         event.preventDefault();

         if (validator.validate()) {
             let data = $("#form-team").serializeFormJSON();
             data['color'] = colorPalette.value();
             window.teamAction == "create" ? createTeam(data) : updateTeam(data);
         }
     });
 });

 function initListViewTeams() {
     dataTeams = new kendo.data.DataSource({
         transport: {
             read: {
                 url: "getTeams",
                 type: "GET",
                 dataType: "JSON"
             },
         },
         schema: {
             model: {
                 id: "id",
                 fields: {
                     id: { type: "number" },
                     optiontype: { type: "number" },
                     name: { type: "string" },
                     icon: { type: "string" }
                 }
             }
         },
     });

     listViewTeams = $("#listViewTeams").kendoListView({
         dataSource: dataTeams,
         template: kendo.template($("#team-template").html()),
         selectable: "single",
         height: 600,
         scrollable: "endless",
         change: function(e) {
             window.teamSelected = dataTeams.getByUid(e.sender.select().data("uid"));
         },
         dataBound: function(e) {
             if (this.dataSource.data().length == 0) {
                 $("#listViewTeams").append("<div class='text-center alert alert-danger mt-1 alert-validation-msg' role='alert'><span>Arrastre elementos</span> <i class='fal fa-hand-paper mr-1 align-middle'></i></div>");
             }
         }
     }).data("kendoListView");
 }

 function setTeam() {
     $("#modal-team-role").modal("hide");
     $("#modal-team").modal("show");
     $("#txt-team-id").val(window.teamSelected.id);
     $("#txt-team-name").val(window.teamSelected.name);
     $("#txt-team-email").val(window.teamSelected.emails);
     multiSelectBosses.value(JSON.parse(window.teamSelected.bosses));
     let users = window.teamSelected.users.map(function(obj) { return obj.iduser; });
     multiSelectUsers.value(users);
     colorPalette.value(window.teamSelected.color);
 }

 function initMultiSelectBosses() {
     multiSelectBosses = $("#multiSelectBosses").kendoMultiSelect({
         placeholder: locale('Choose team bosses'),
         dataTextField: "text",
         dataValueField: "value",
         filter: "contains",
         height: 400,
         dataSource: window.global_users
     }).data("kendoMultiSelect");
 }

 function initMultiSelectUsers() {
     multiSelectUsers = $("#multiSelectUsers").kendoMultiSelect({
         placeholder: "Elije personas para el equipo",
         dataTextField: "text",
         dataValueField: "value",
         filter: "contains",
         height: 400,
         dataSource: window.global_users
     }).data("kendoMultiSelect");
 }

 function initColorPalette() {
     colorPalette = $("#colorPalette").kendoColorPalette({
         palette: ["#F3C600", "#F59D00", "#E87E04", "#D55400", "#E94B35", "#C23824", "#FF5999", "#D3D2D4", "#9C55B8", "#8F3FAF", "#478CFE", "#336DCD", "#2C97DE", "#227FBB", "#00BD9C", "#00A085", "#1ECD6E", "#1AAF5D"],
         tileSize: 30,
         value: "#F3C600",
         change: function() {
             var colorId = this.value().substring(1);
         }
     }).data("kendoColorPalette");
 }

 $("#btn-create-team").click(function() {
     window.teamAction = "create";
     $("#form-team").submit();
 });

 function createTeam(data) {
     let request = callAjax("createTeam", 'POST', data, true);

     request.done(function(result) {

         if (result.success) {
             toastr.success("Equipo " + result.model.name + " creado");
             $("#modal-team").modal("hide");
             $('#form-team').trigger("reset");
             listViewTeams.dataSource.read();
         }

     }).fail(function(jqXHR, status) {
         $.unblockUI();
         toastr.error("Error");
     });
 }

 $("#btn-update-team").click(function() {
     window.teamAction = "update";
     $("#form-team").submit();
 });

 function updateTeam(data) {
     let request = callAjax("updateTeam", 'POST', data, true);

     request.done(function(result) {

         if (result.success) {
             toastr.success("Equipo " + result.model.name + " editado");
             $("#modal-team").modal("hide");
             $('#form-team').trigger("reset");
             listViewTeams.dataSource.read();
         }

     }).fail(function(jqXHR, status) {
         $.unblockUI();
         toastr.error("Error");
     });
 }

 function deleteTeam() {
     let request = callAjax("deleteTeam", 'POST', { "id": window.teamSelected.id }, true);

     request.done(function(result) {

         if (result.success) {
             toastr.success("Equipo " + result.model.name + " eliminado <button type='button' class='btn btn-light btn-sm' onclick='restoreTeam()'>DESHACER</button>");
             listViewTeams.dataSource.read();
         } else {
             toastr.warning("El equipo tiene relaciones activas");
         }

     }).fail(function(jqXHR, status) {
         $.unblockUI();
         toastr.error("Error");
     });
 }

 function restoreTeam() {
     let request = callAjax("restoreTeam", 'POST', { "id": window.teamSelected.id }, true);

     request.done(function(result) {

         if (result.success) {
             toastr.success("Equipo " + result.model.name + " recuperado");
             listViewTeams.dataSource.read();
         }
     }).fail(function(jqXHR, status) {
         $.unblockUI();
         toastr.error("Error");
     });
 }