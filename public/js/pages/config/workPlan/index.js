 window.workPlanAction = "create";

 $(document).ready(function() {

    initGridWorkPlans();
    initContextMenu();
    initDropDownListType();
    initDropDownListSpot();
    initDropDownListBranch();

     $(document).on("click", "#btn-new-config", function(event) {
         $("#div-work-plan-create-buttons").show();
         $("#div-work-plan-update-buttons").hide();
         $('#form-work-plan').trigger("reset");
         $("#title-modal-work-plan").text("Nuevo Plan de Trabajo");
         $("#modal-work-plan").modal("show");
     });

     var validator = $("#form-work-plan").kendoValidator().data("kendoValidator");

     $("#form-work-plan").submit(function(event) {
         event.preventDefault();

         if (validator.validate())
         {
            let data = $("#form-work-plan").serializeFormJSON();

            window.workPlanAction == "create" ? createWorkPlan(data) : updateWorkPlan(data);
         }
     });

 });

 function initDropDownListType()
{
    dropDownListType = $("#dropDownListType").kendoDropDownList({
        optionLabel: "-- Seleccione --",
        dataTextField: "text",
        dataValueField: "value",
        dataSource: [{"value": "STANDARD", "text": "Estándar"}, {"value": "EVALUATIVE", "text": "Evaluativo"}],
        height: 400
    }).data("kendoDropDownList");
}

 function initGridWorkPlans()
 {
     gridWorkPlans = $("#gridWorkPlans").kendoGrid({
         dataSource: {
             transport: {
                 read: {
                     url: "getAllWorkPlans",
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
                         name: { editable: true, field: "name", type: "string", validation: { required: { message: locale("Name is required") } } },
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
            {
                 field: "name",
                 title: locale("Name"),
                 width: "300px",
                 filterable: false
            },
            {
                field: "type",
                title: "Tipo",
                width: "300px",
                filterable: false,
                //<div class="badge badge-primary">Primary</div>
                template: function(dataItem) {
                    return (dataItem.type == "STANDARD" ? "<div class='badge badge-primary'>ESTÁNDAR</div>" : "<div class='badge badge-danger'>EVALUATIVO</div>");
                },
           },
           {
                field: "idspot",
                title: "Sede",
                values: user_branches,
                width: "100px",
                filterable: {
                    multi: true,
                    search: true
                }
            },
            { command: { text: "Detalles", click: goToWorkPlan }, title: " ", width: "55px" },
            { command: { text: "", name: "actions", iconClass: "fas fa-ellipsis-v" }, title: " ", width: "55px" }
         ],
     }).data("kendoGrid");

     setTimeout(() => { $("#btn-new-config").text("Nuevo Plan de Trabajo") }, 300);
 }

function goToWorkPlan(e)
{
    e.preventDefault();

    var dataItem = this.dataItem($(e.currentTarget).closest("tr"));

    console.log(dataItem);

    kendo.confirm("¿Ir a detalles del plan?")
    .done(function(){

        let url = "work-plan?idworkplan=" + dataItem.id;
        document.location.href = url;

    })
    .fail(function(){

    });
}

 function initContextMenu()
 {
     $("#context-menu").kendoContextMenu({
         target: "#gridWorkPlans",
         filter: "td .k-grid-actions",
         showOn: "click",
         select: function(e) {
             var td = $(e.target).parent()[0];
             window.workPlanSelected = gridWorkPlans.dataItem($(td).parent()[0]);

             switch (e.item.id) {
                 case "editWorkPlan":
                     setWorkPlan();
                     break;
                 case "deleteWorkPlan":
                     confirmDeleteWorkPlan();
                     break;
                 case "copyWorkPlan":
                    showCopyModal();
                     break;
             };
         }
     });
 }

 function setWorkPlan()
 {
     $("#div-work-plan-update-buttons").show();
     $("#div-work-plan-create-buttons").hide();

     $('#form-work-plan').trigger("reset");

     $("#title-modal-work-plan").text("Editar Plan de Trabajo");
     $("#modal-work-plan").modal("show");

     $("#txt-work-plan-id").val(window.workPlanSelected.id);
     $("#txt-work-plan-name").val(window.workPlanSelected.name);
     
     setTimeout(() => {
        dropDownListSpot.value(window.workPlanSelected.idspot);
        dropDownListType.value(window.workPlanSelected.type);
    }, 100);

 }

 $("#btn-create-work-plan").click(function() {
     window.workPlanAction = "create";
     $("#form-work-plan").submit();
 });

 function createWorkPlan(data)
 {
     let request = callAjax("createWorkPlan", 'POST', data, true);

     request.done(function(result) {

         if(result.success)
         {
             toastr.success("Plan de trabajo " + result.model.name + " creado");
             $("#modal-work-plan").modal("hide");
             $('#form-work-plan').trigger("reset");
             gridWorkPlans.dataSource.read();
         }

     }).fail(function(jqXHR, status) {
         $.unblockUI();
         toastr.error("Error");
     });
 }

 $("#btn-update-work-plan").click(function() {
     window.workPlanAction = "update";
     $("#form-work-plan").submit();
 });

 function updateWorkPlan(data)
 {
     let request = callAjax("updateWorkPlan", 'POST', data, true);

     request.done(function(result) {

         if(result.success)
         {
             toastr.success("Plan de trabajo " + result.model.name + " editado");
             $("#modal-work-plan").modal("hide");
             $('#form-work-plan').trigger("reset");
             gridWorkPlans.dataSource.read();
         }

     }).fail(function(jqXHR, status) {
         $.unblockUI();
         toastr.error("Error");
     });
 }

 function confirmDeleteWorkPlan()
 {
     Swal.fire({
         title: 'Eliminar',
         text: "¿Eliminar plan de trabajo " + window.workPlanSelected.name + "?",
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
         if (result.value) deleteWorkPlan();
     });
 }

 function deleteWorkPlan()
 {
     let request = callAjax("deleteWorkPlan", 'POST', { "id": window.workPlanSelected.id }, true);

     request.done(function(result) {

         if(result.success)
         {
             toastr.success("Plan de trabajo " + result.model.name + " eliminado <button type='button' class='btn btn-light btn-sm' onclick='restoreWorkPlan()'>DESHACER</button>");
             gridWorkPlans.dataSource.read();
         }
         else
         {
             toastr.warning("Plan de trabajo " + result.model.name + " tiene relaciones activas");
         }

     }).fail(function(jqXHR, status) {
         $.unblockUI();
         toastr.error("Error");
     });
 }

 function restoreWorkPlan()
 {
     let request = callAjax("restoreWorkPlan", 'POST', { "id": window.workPlanSelected.id }, true);

     request.done(function(result) {

         if(result.success)
         {
             toastr.success("Plan de trabajo " + result.model.name + " recuperado");
             gridWorkPlans.dataSource.read();
         }

     }).fail(function(jqXHR, status) {
         $.unblockUI();
         toastr.error("Error");
     });
 }

 function initDropDownListSpot()
{
    dropDownListSpot = $("#dropDownListSpot").kendoDropDownList({
        optionLabel: "-- Seleccione --",
        dataTextField: "text",
        dataValueField: "value",
        dataSource: window.user_branches,
        height: 400
    }).data("kendoDropDownList");
}

 function initDropDownListBranch()
{
    dropDownBranch = $("#dropDownBranch").kendoDropDownList({
        optionLabel: "-- Seleccione --",
        dataTextField: "text",
        dataValueField: "value",
        dataSource: window.user_branches,
        height: 400
    }).data("kendoDropDownList");
}

function showCopyModal() {
    $('#title-modal-work-plan-copy').text("Copiar Plan de trabajo: " + window.workPlanSelected.name);
    $("#name-plan-copy").val("");
    $("#modal-work-plan-copy").modal("show");
}

$("#btn-create-work-plan-copy").click(function(e) {
    copyWorkPlan();
});

function copyWorkPlan() {

    let data = {
        'idworkplan' : window.workPlanSelected.id,
        'name'        : $("#name-plan-copy").val(),
        'type'        : window.workPlanSelected.type,
        'idspot'      : dropDownBranch.value(),
    };

    let request = callAjax("copyWorkPlan", 'POST', data, true);

    request.done(function(result) {

        if(result.success)
        {
            $("#modal-work-plan-copy").modal("hide");
            toastr.success("Plan de trabajo " + result.model.name + " Copiado con exito!");
            gridWorkPlans.dataSource.read();
        }
        else
        {
            toastr.warning("Plan de trabajo " + result.model.name + " tiene relaciones activas");
        }

    }).fail(function(jqXHR, status) {
        $.unblockUI();
        toastr.error("Error");
    });
}