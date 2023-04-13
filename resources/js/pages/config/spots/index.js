 window.spotAction = "create";

 $(document).ready(function() {
     initGridSpots();
     initContextMenu();
     initDropDownListSpotType();
     initDropDownListParent();

     if(window.open)
     {
        $("#modal-list-spot-type").modal("show");
     }

     $(document).on("click", "#btn-new-config", function(event) {
         $("#div-spot-create-buttons").show();
         $("#div-spot-update-buttons").hide();
         $('#form-spot').trigger("reset");
         $("#title-modal-spot").text(locale("New Spot"));
         $("#modal-spot").modal("show");
     });

     $("#btn-config").click(function(e) {
         $("#modal-list-spot-type").modal("show");
     });

     var validator = $("#form-spot").kendoValidator().data("kendoValidator");

     $("#form-spot").submit(function(event) {
         event.preventDefault();

         if (validator.validate()) {
             let data = $("#form-spot").serializeFormJSON();

             data['isbranch'] = $("#isbranch").is(":checked") ? true : false;
             data['cleanable'] = $("#cleanable").is(":checked") ? true : false;

             window.spotAction == "create" ? createSpot(data) : updateSpot(data);
         }
     });

     $(document).on("change", ".switch-isbranch", function(event) {
        let data = { 'id': $(this).data('id'), 'isbranch': $(this).is(':checked') };
        changeSpot(data)
     });

     $(document).on("change", ".switch-cleanable", function(event) {
        let data = { 'id': $(this).data('id'), 'cleanable': $(this).is(':checked') };
        changeSpot(data);
     });
 });

 function initGridSpots() {
     gridSpots = $("#gridSpots").kendoGrid({
         dataSource: {
             transport: {
                 read: {
                     url: "getSpots",
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
                         name: { editable: true, field: "name", type: "string", validation: { required: { message: locale("Name is required") } } },
                         isbranch: { type: "boolean" },
                         cleanable: { type: "boolean" }
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

         },
         columns: [
            { command: { text: "", name: "actions", iconClass: "fas fa-ellipsis-v" }, title: " ", width: "60px" },
            {
                 field: "name",
                 title: locale("Name"),
                 width: "300px",
                 filterable: false
             },
             {
                field: "idparent",
                title: "Pertenece a",
                values: window.spotsParents,
                width: "300px",
                filterable: {
                   multi: true,
                   search: true
               }
            },
             {
                 field: "idtype",
                 title: locale('Type'),
                 values: window.spot_types,
                 width: "300px",
                 filterable: {
                    multi: true,
                    search: true
                }
             },
             {
                field: "isbranch",
                title: "Sede",
                template: function(dataItem) {
                    return "<div class='custom-control custom-switch custom-switch-success'>" +
                                "<input id='isbranch" + dataItem.id + "' type='checkbox' data-id='" + dataItem.id + "' class='switch-isbranch custom-control-input' " + (dataItem.isbranch ? "checked" : "") + ">" +
                                "<label class='custom-control-label' for='isbranch" + dataItem.id + "'>" +
                                "<span class='switch-text-left'>Si</span>" +
                                "<span class='switch-text-right'>No</span>" +
                                "</label>" +
                            "</div>";
                },
                width: "80px",
                filterable: {
                   multi: true,
                   search: true
               }
            },
            {
                field: "cleanable",
                title: "Limpiable",
                template: function(dataItem) {
                    return "<div class='custom-control custom-switch custom-switch-info'>" +
                                "<input id='cleanable" + dataItem.id + "' type='checkbox' data-id='" + dataItem.id + "' class='switch-cleanable custom-control-input' " + (dataItem.cleanable ? "checked" : "") + ">" +
                                "<label class='custom-control-label' for='cleanable" + dataItem.id + "'>" +
                                "<span class='switch-text-left'>Si</span>" +
                                "<span class='switch-text-right'>No</span>" +
                                "</label>" +
                            "</div>";
                },
                width: "80px",
                filterable: {
                   multi: true,
                   search: true
               }
            }
         ],
     }).data("kendoGrid");

     setTimeout(() => { $("#btn-new-config").text(locale("New Spot")) }, 300);
 }

 function initContextMenu() {
     $("#context-menu").kendoContextMenu({
         target: "#gridSpots",
         filter: "td .k-grid-actions",
         showOn: "click",
         select: function(e) {
             var td = $(e.target).parent()[0];
             window.spotSelected = gridSpots.dataItem($(td).parent()[0]);

             switch (e.item.id) {
                 case "editSpot":
                     setSpot();
                     break;
                 case "deleteSpot":
                     confirmDeleteSpot();
                     break;
             };
         }
     });
 }

 function setSpot() {
     $("#div-spot-update-buttons").show();
     $("#div-spot-create-buttons").hide();

     $('#form-spot').trigger("reset");

     $("#title-modal-spot").text("Editar Spot");
     $("#modal-spot").modal("show");

     $("#txt-spot-id").val(window.spotSelected.id);
     $("#txt-spot-name").val(window.spotSelected.name);

     $('#isbranch').prop('checked', window.spotSelected.isbranch);
     $('#cleanable').prop('checked', window.spotSelected.cleanable);

     setTimeout(() => {
         dropDownListSpotType.value(window.spotSelected.idtype);
         dropDownListParent.value(window.spotSelected.idparent);
     }, 200);
 }

 function initDropDownListSpotType() {
     dropDownListSpotType = $("#dropDownListSpotType").kendoDropDownList({
         //optionLabel: locale('Select'),
         dataTextField: "text",
         dataValueField: "value",
         filter: "contains",
         dataSource: window.spot_types,
         popup: { appendTo: $("#modal-spot") },
         height: 400
     }).data("kendoDropDownList");
 }

 function initDropDownListParent()
 {
     dropDownListParent = $("#dropDownListParent").kendoDropDownList({
         //optionLabel: locale('Select'),
         dataTextField: "text",
         dataValueField: "value",
         filter: "contains",
         dataSource: window.spotsParents,
         popup: { appendTo: $("#modal-spot") },
         height: 400
     }).data("kendoDropDownList");
 }

 $("#btn-create-spot").click(function() {
     window.spotAction = "create";
     $("#form-spot").submit();
 });

 function createSpot(data) {
     let request = callAjax("createSpot", 'POST', data, true);

     request.done(function(result) {

         if (result.success) {
             toastr.success("Spot " + result.model.name + " creado");
             $("#modal-spot").modal("hide");
             $('#form-spot').trigger("reset");
             gridSpots.dataSource.read();
         }

     }).fail(function(jqXHR, status) {
         $.unblockUI();
         toastr.error("Error");
     });
 }

 $("#btn-update-spot").click(function() {
     window.spotAction = "update";
     $("#form-spot").submit();
 });

 function updateSpot(data) {
     let request = callAjax("updateSpot", 'POST', data, true);

     request.done(function(result) {

         if (result.success) {
             toastr.success("Spot " + result.model.name + " editado");
             $("#modal-spot").modal("hide");
             $('#form-spot').trigger("reset");
             gridSpots.dataSource.read();
         }

     }).fail(function(jqXHR, status) {
         $.unblockUI();
         toastr.error("Error");
     });
 }

 function confirmDeleteSpot() {
     Swal.fire({
         title: 'Eliminar',
         text: "¿Eliminar spot " + window.spotSelected.name + "?",
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
         if (result.value) deleteSpot();
     });
 }

 function deleteSpot() {
     let request = callAjax("deleteSpot", 'POST', { "id": window.spotSelected.id }, true);

     request.done(function(result) {

         if (result.success) {
             toastr.success("Spot " + result.model.name + " eliminado <button type='button' class='btn btn-light btn-sm' onclick='restoreSpot()'>DESHACER</button>");
             gridSpots.dataSource.read();
         } else {
             toastr.warning("Spot " + result.model.name + " tiene relaciones activas");
         }

     }).fail(function(jqXHR, status) {
         $.unblockUI();
         toastr.error("Error");
     });
 }

 function restoreSpot() {
     let request = callAjax("restoreSpot", 'POST', { "id": window.spotSelected.id }, true);

     request.done(function(result) {

         if (result.success) {
             toastr.success("Spot " + result.model.name + " recuperado");
             gridSpots.dataSource.read();
         }

     }).fail(function(jqXHR, status) {
         $.unblockUI();
         toastr.error("Error");
     });
 }

 function changeSpot(data)
 {
    let request = callAjax("updateSpot", 'POST', data, true);

    request.done(function(result) {

        if(result.success)
        {
            toastr.success("Spot " + result.model.name + " editado");
            gridSpots.dataSource.read();
        }

    }).fail(function(jqXHR, status) {
        $.unblockUI();
        toastr.error("Error");
    });
}