 $(document).ready(function() {

     initListViewSpotTypes();

     $("#btn-new-spot-type").click(function(e) {
         $('#form-spot-type').trigger("reset");
         $("#div-spot-type-update-buttons").hide();
         $("#div-spot-type-create-buttons").show();
         $("#modal-list-spot-type").modal("hide");
         $("#modal-spot-type").modal("show");
         $("#title-modal-spot-type").html("<i id='icon-back-spot-type' class='fas fa-arrow-left'></i>" + locale('New Spot Type'));
     });

     $(document).on("click", "#icon-back-spot-type", function(event) {
         $("#modal-spot-type").modal("hide");
         $("#modal-list-spot-type").modal("show");
     });

     $(document).on("click", ".btn-edit-spot-type", function(event) {
         $("#div-spot-type-update-buttons").show();
         $("#div-spot-type-create-buttons").hide();
         $("#title-modal-spot-type").html("<i id='icon-back-spot-type' class='fas fa-arrow-left'></i>" + locale('Edit Spot Type'));
         setSpotType();
     });

     $(document).on("click", ".btn-delete-spot-type", function(event) {

         Swal.fire({
             title: locale('Delete'),
             text: locale("Delete") + ' ' + window.spotTypeSelected.name + "?",
             type: 'warning',
             buttonsStyling: true,
             showCancelButton: true,
             confirmButtonColor: '#3085d6',
             cancelButtonColor: '#d33',
             confirmButtonText: locale('Delete'),
             confirmButtonClass: 'btn btn-danger ',
             cancelButtonClass: 'btn btn-primary ml-1',
             cancelButtonText: locale('Cancel'),
             buttonsStyling: false
         }).then(function(result) {
             if (result.value) deleteSpotType();
         });
     });

     var validator = $("#form-spot-type").kendoValidator().data("kendoValidator");

     $("#form-spot-type").submit(function(event) {
         event.preventDefault();

         if (validator.validate()) {
             let data = $("#form-spot-type").serializeFormJSON();

             data['islodging'] = $("#islodging").is(":checked") ? true : false;

             window.spotTypeAction == "create" ? createSpotType(data) : updateSpotType(data);
         }
     });

 });

 function initListViewSpotTypes() {
     dataSpotTypes = new kendo.data.DataSource({
         transport: {
             read: {
                 url: "getSpotTypes",
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

     listViewSpotTypes = $("#listViewSpotTypes").kendoListView({
         dataSource: dataSpotTypes,
         template: kendo.template($("#spot-type-template").html()),
         selectable: "single",
         height: 600,
         scrollable: "endless",
         change: function(e) {
             window.spotTypeSelected = dataSpotTypes.getByUid(e.sender.select().data("uid"));
             console.log(window.spotTypeSelected);
         },
         dataBound: function(e) {
             if (this.dataSource.data().length == 0) {
                 $("#listViewSpotTypes").append("<div class='text-center alert alert-danger mt-1 alert-validation-msg' role='alert'><span>Arrastre elementos</span> <i class='fal fa-hand-paper mr-1 align-middle'></i></div>");
             }
         }
     }).data("kendoListView");
 }

 function setSpotType() {
     $("#modal-list-spot-type").modal("hide");
     $("#modal-spot-type").modal("show");
     $("#txt-spot-type-id").val(window.spotTypeSelected.id);
     $("#txt-spot-type-name").val(window.spotTypeSelected.name);
     $("#txt-spot-type-description").val(window.spotTypeSelected.description);

     $('#islodging').prop('checked', window.spotTypeSelected.islodging);
 }

 $("#btn-create-spot-type").click(function() {
     window.spotTypeAction = "create";
     $("#form-spot-type").submit();
 });

 function createSpotType(data) {
     let request = callAjax("createSpotType", 'POST', data, true);

     request.done(function(result) {

         if (result.success) {
             toastr.success("Tipo de spot " + result.model.name + " creado");
             $("#modal-spot-type").modal("hide");
             $('#form-spot-type').trigger("reset");
             listViewSpotTypes.dataSource.read();
         }

     }).fail(function(jqXHR, status) {
         $.unblockUI();
         toastr.error("Error");
     });
 }

 $("#btn-update-spot-type").click(function() {
     window.spotTypeAction = "update";
     $("#form-spot-type").submit();
 });

 function updateSpotType(data) {
     let request = callAjax("updateSpotType", 'POST', data, true);

     request.done(function(result) {

         if (result.success) {
             toastr.success("Tipo de spot " + result.model.name + " editado");
             $("#modal-spot-type").modal("hide");
             $('#form-spot-type').trigger("reset");
             listViewSpotTypes.dataSource.read();
         }

     }).fail(function(jqXHR, status) {
         $.unblockUI();
         toastr.error("Error");
     });
 }

 function deleteSpotType() {
     let request = callAjax("deleteSpotType", 'POST', { "id": window.spotTypeSelected.id }, true);

     request.done(function(result) {

         if (result.success) {
             toastr.success("Tipo de spot " + result.model.name + " eliminado <button type='button' class='btn btn-light btn-sm' onclick='restoreSpotType()'>DESHACER</button>");
             listViewSpotTypes.dataSource.read();
         } else {
             toastr.info("Tipo de spot " + result.model.name + " tiene relaciones activas");
         }

     }).fail(function(jqXHR, status) {
         $.unblockUI();
         toastr.error("Error");
     });
 }

 function restoreSpotType() {
     let request = callAjax("restoreSpotType", 'POST', { "id": window.spotTypeSelected.id }, true);

     request.done(function(result) {

         if (result.success) {
             toastr.success("Tipo de spot " + result.model.name + " recuperado");
             listViewSpotTypes.dataSource.read();
         }

     }).fail(function(jqXHR, status) {
         $.unblockUI();
         toastr.error("Error");
     });
 }