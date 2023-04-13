 window.itemAction = "create";

 $(document).ready(function() {
     initGridItems();
     initDropDownListTicketType();
     initDropDownListTeam();
     initDropDownListForm();
     initDropDownListPriority();
     initMultiSelectUsers();
     initContextMenu();

     if(window.open)
     {
        $("#modal-list-ticket-type").modal("show");
     }

     window.treeView = new WHTreeView(window.spots, saveItemSpots);

     $(document).on("click", "#btn-new-config", function(event) {
         $("#div-item-create-buttons").show();
         $("#div-item-update-buttons").hide();
         $('#form-item').trigger("reset");
         setTimeout(() => { dropDownListPriority.value(1) }, 300);
         $("#title-modal-item").text(locale("New Item Type"));
         $("#modal-item").modal("show");
     });

     $("#btn-config").click(function(e) {
         $("#modal-list-ticket-type").modal("show");
     });

     var validator = $("#form-item").kendoValidator().data("kendoValidator");

     $("#form-item").submit(function(event) {
         event.preventDefault();

         if (validator.validate()) {
             let data = $("#form-item").serializeFormJSON();

             data['isprivate'] = $("#isprivate").is(":checked") ? true : false;

             window.itemAction == "create" ? createItem(data) : updateItem(data);
         }
     });
 });

 function initGridItems() {
     gridItems = $("#gridItems").kendoGrid({
         dataSource: {
             transport: {
                 read: {
                     url: "getItems",
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
                 field: "name",
                 title: locale("Name"),
                 width: "300px",
                 filterable: false
             },
             {
                 field: "idtype",
                 title: locale("Type"),
                 values: global_ticket_types,
                 template: function(dataItem) {
                     let ticketType = global_ticket_types.find(o => o.value === dataItem.idtype);
                     return "<span class='badge badge-primary badge-md'><i class='fas " + ticketType.icon + "'></i> " + ticketType.text + "</span>";
                 },
                 width: "300px",
                 filterable: {
                    multi: true,
                    search: true
                }
             },
             {
                 field: "idteam",
                 title: locale("Team"),
                 values: global_teams,
                 template: function(dataItem) {
                     let team = global_teams.find(o => o.value === dataItem.idteam);
                     return "<span style='background:" + team.color + ";' class='badge badge-light badge-md'>" + team.text + "</span>";
                 },
                 width: "300px",
                 filterable: {
                    multi: true,
                    search: true
                }
             },
             {
                 field: "idchecklist",
                 title: locale("Checklist"),
                 values: window.global_checklist,
                 width: "300px",
                 filterable: {
                    multi: true,
                    search: true
                }
             }
         ],
     }).data("kendoGrid");

     setTimeout(() => { $("#btn-new-config").text("Crear Ítem") }, 300);
 }

 function initDropDownListTicketType() {
     dropDownListTicketType = $("#dropDownListTicketType").kendoDropDownList({
         optionLabel: "Seleccione Categoría",
         dataTextField: "text",
         dataValueField: "value",
         filter: "contains",
         dataSource: window.global_ticket_types,
         popup: { appendTo: $("#modal-item") },
         height: 400
     }).data("kendoDropDownList");
 }

 function initDropDownListTeam() {
     dropDownListTeam = $("#dropDownListTeam").kendoDropDownList({
         optionLabel: "Seleccione Equipo",
         dataTextField: "text",
         dataValueField: "value",
         filter: "contains",
         dataSource: window.global_teams,
         popup: { appendTo: $("#modal-item") },
         height: 400
     }).data("kendoDropDownList");
 }

 function initDropDownListForm() {
     dropDownListForm = $("#dropDownListForm").kendoDropDownList({
         optionLabel: "Seleccione Formulario",
         dataTextField: "text",
         dataValueField: "value",
         filter: "contains",
         dataSource: window.global_checklist,
         popup: { appendTo: $("#modal-item") },
         height: 400
     }).data("kendoDropDownList");
 }

 function initDropDownListPriority() {
     dropDownListPriority = $("#dropDownListPriority").kendoDropDownList({
         dataTextField: "text",
         dataValueField: "value",
         dataSource: window.global_priorities,
         popup: { appendTo: $("#modal-item") },
         height: 400
     }).data("kendoDropDownList");
 }

 function initMultiSelectUsers() {
     multiSelectUsers = $("#multiSelectUsers").kendoMultiSelect({
         placeholder: "Elije responsables",
         dataTextField: "text",
         dataValueField: "value",
         filter: "contains",
         height: 400,
         dataSource: window.global_users
     }).data("kendoMultiSelect");
 }

 function initContextMenu() {
     $("#context-menu").kendoContextMenu({
         target: "#gridItems",
         filter: "td .k-grid-actions",
         showOn: "click",
         select: function(e) {
             var td = $(e.target).parent()[0];
             window.itemSelected = gridItems.dataItem($(td).parent()[0]);

             switch (e.item.id) {
                 case "editItem":
                     setItem();
                     break;

                 case "deleteItem":
                     confirmDeleteItem();
                     break;

                 case "selectSpots":
                     selectSpots();
                     break;
             };
         }
     });
 }

 function selectSpots() {
     window.treeView.open(window.itemSelected.name, window.itemSelected.spots);
 }

 function setItem() {
     $("#div-item-update-buttons").show();
     $("#div-item-create-buttons").hide();

     $('#form-item').trigger("reset");

     $("#title-modal-item").text("Editar plantilla de tarea");
     $("#modal-item").modal("show");

     $("#txt-item-id").val(window.itemSelected.id);
     $("#txt-item-name").val(window.itemSelected.name);
     $("#txt-item-description").val(window.itemSelected.description);

     $('#isprivate').prop('checked', window.itemSelected.isprivate);

     setTimeout(() => {
         dropDownListForm.value(window.itemSelected.idchecklist);
         dropDownListPriority.value(window.itemSelected.idpriority);
         dropDownListTeam.value(window.itemSelected.idteam);
         dropDownListTicketType.value(window.itemSelected.idtype);

         let users = window.itemSelected.users.map(function(obj) { return obj.value; });
         multiSelectUsers.value(users);
     }, 200);
 }

 $("#btn-create-item").click(function() {
     window.itemAction = "create";
     $("#form-item").submit();
 });

 function createItem(data) {
     let request = callAjax("createItem", 'POST', data, true);

     request.done(function(result) {

         if (result.success) {
             toastr.success("Plantilla de tarea " + result.model.name + " creado");
             $("#modal-item").modal("hide");
             $('#form-item').trigger("reset");
             gridItems.dataSource.read();
         }

     }).fail(function(jqXHR, status) {
         $.unblockUI();
         toastr.error("Error");
     });
 }

 $("#btn-update-item").click(function() {
     window.itemAction = "update";
     $("#form-item").submit();
 });

 function updateItem(data) {
     let request = callAjax("updateItem", 'POST', data, true);

     request.done(function(result) {

         if (result.success) {
             toastr.success("Plantilla de tarea " + result.model.name + " editada");
             $("#modal-item").modal("hide");
             $('#form-item').trigger("reset");
             gridItems.dataSource.read();
         }

     }).fail(function(jqXHR, status) {
         $.unblockUI();
         toastr.error("Error");
     });
 }

 function confirmDeleteItem() {
     Swal.fire({
         title: 'Eliminar',
         text: "¿Eliminar plantilla de tarea " + window.itemSelected.name + "?",
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
         if (result.value) deleteItem();
     });
 }

 function deleteItem() {
     let request = callAjax("deleteItem", 'POST', { "id": window.itemSelected.id }, true);

     request.done(function(result) {

         if (result.success) {
             toastr.success("Plantilla de tarea " + result.model.name + " eliminada <button type='button' class='btn btn-light btn-sm' onclick='restoreItem()'>DESHACER</button>");
             gridItems.dataSource.read();
         } else {
             toastr.warning("El ítem tiene relaciones activas");
         }

     }).fail(function(jqXHR, status) {
         $.unblockUI();
         toastr.error("Error");
     });
 }

 function restoreItem() {
     let request = callAjax("restoreItem", 'POST', { "id": window.itemSelected.id }, true);

     request.done(function(result) {

         if (result.success) {
             toastr.success("Plantilla de tarea " + result.model.name + " recuperada");
             gridItems.dataSource.read();
         }

     }).fail(function(jqXHR, status) {
         $.unblockUI();
         toastr.error("Error");
     });
 }

 function saveItemSpots(data) {
     data["iditem"] = window.itemSelected.id;
     let request = callAjax('saveItemSpots', 'POST', data, true);

     request.done(function(result) {

         gridItems.dataSource.read();

     }).fail(function(jqXHR, status) {

     });
 }