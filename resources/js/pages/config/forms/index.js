window.formAction = "create";

$(document).ready(function() {

    initDropDownListTicketType();
    initGridForms();
    initContextMenu();

    $(document).on("click", "#btn-new-config", function(event) {
        
        $("#div-form-create-buttons").show();
        $("#div-form-update-buttons").hide();
        $('#form-form').trigger("reset");
        setTimeout(() => { dropDownListTicketType.value(1) }, 200);
        $("#title-modal-form").text("Nuevo Formulario");
        
        $("#modal-form").modal("show");
    });

    var validator = $("#form-form").kendoValidator().data("kendoValidator");

    $("#form-form").submit(function(event) {
        event.preventDefault();

        if(validator.validate())
        {
            let data = $("#form-form").serializeFormJSON();
            data["collapse"] = $("#collapse").is(":checked") ? true : false;

            window.formAction == "create" ? createForm(data) : updateForm(data);
        }
    });

    $(document).on("change", ".switch-enabled", function(event) {
        confirmDisableForm(this);
    });

    $(document).on("click", ".form-name", function(event) {

        let id = $(this).data("id")

        kendo.confirm("¿Ir a detalles del formulario?")
        .done(function(){
    
            let url = "form-editor?id=" + id;
            document.location.href = url;
    
        })
        .fail(function(){
    
        });
    });

});

function initDropDownListTicketType()
{
    dropDownListTicketType = $("#dropDownListTicketType").kendoDropDownList({
        optionLabel: "Seleccione Tipo",
        dataTextField: "text",
        dataValueField: "value",
        filter: "contains",
        dataSource: window.global_ticket_types,
        popup: { appendTo: $("#modal-form") },
        height: 400
    }).data("kendoDropDownList");
}

function initGridForms()
{
    gridForms = $("#gridForms").kendoGrid({
        excel: {
            fileName: "Whagons Forms.xlsx",
        },
        dataSource: {
            transport: {
                read: {
                    url: "getForms",
                    type: "get",
                    dataType: "json"
                },
            },
            pageSize: 20,
            schema: {
                model: {
                    id: "id",
                    fields: {
                        id: { type: "number", editable: false, nullable: true },
                        name: { editable: true, field: "name", type: "string", validation: { required: { message: "Nombre es requerido" } } },
                        idtype: { editable: true, field: "idtype", type: "number", validation: { required: { message: "Tipo es requerido" } } },
                        description: { editable: true, field: "description", type: "string" },
                        enabled: { editable: true, field: "enabled", type: "boolean", defaultValue: true },
                    }
                }
            },
        },
        editable: {
            mode: "popup"
        },
        toolbar: [{ template: kendo.template($("#template-search-panel").html()) }],
        sortable: true,
        reorderable: true,
        resizable: true,
        navigatable: true,
        pageable: {
            refresh: true,
            pageSizes: true,
            buttonCount: 5,
        },
        noRecords: true,
        messages: {
            noRecords: "Create your first Form!"
        },
        height: '700px',
        filterable: true,
        columns: [
            { command: { text: "", name: "actions", iconClass: "fas fa-ellipsis-v" }, title: " ", width: "40px" },
            {
                field: "name",
                title: locale("Name"),
                template: function(dataItem) {
                    return "<a href='#' data-id='"+ dataItem.id +"' class='form-name'>" + dataItem.name + "</a>";
                },
                width: "400px",
                media: "(min-width: 350px)",
                filterable: {
                    multi: true,
                    search: true
                }
            },
            {
                field: "idtype",
                title: locale("Type"),
                width: "250px",
                values: global_ticket_types,
                filterable: {
                    multi: true,
                    search: true
                }
            },
            { command: { text: "Detalles", click: goToFormEditor }, title: " ", width: "55px" },
            {
                field: "enabled",
                title: "Estado",
                width: "60px",
                template: function(dataItem) {
                    return "<div class='custom-control custom-switch custom-switch-success switch-lg'>" +
                        "<input id='enabled" + dataItem.id + "' type='checkbox' data-id='" + dataItem.id + "' data-form='" + dataItem.name + "' class='switch-enabled custom-control-input' " + (dataItem.enabled ? "checked" : "") + ">" +
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
            },
        ],
    }).data("kendoGrid");

    setTimeout(() => { $("#btn-new-config").text("Crear Formulario") }, 200);
}

function initContextMenu()
{
    $("#context-menu").kendoContextMenu({
        target: "#gridForms",
        filter: "td .k-grid-actions",
        showOn: "click",
        select: function(e) {
            var td = $(e.target).parent()[0];
            window.formSelected = gridForms.dataItem($(td).parent()[0]);

            switch (e.item.id)
            {
                case "editForm":
                    setForm();
                    break;

                case "deleteForm":
                    confirmDeleteForm();
                    break;
            };
        }
    });
}

function setForm()
{
    $("#div-form-update-buttons").show();
    $("#div-form-create-buttons").hide();

    $('#form-form').trigger("reset");

    $("#title-modal-form").text("Editar Formulario");
    
    $("#modal-form").modal("show");

    $("#txt-form-id").val(window.formSelected.id);
    $("#txt-form-name").val(window.formSelected.name);
    $("#collapse").prop('checked', window.formSelected.collapse);

    setTimeout(() => {
        dropDownListTicketType.value(window.formSelected.idtype);
    }, 200);
}

$("#btn-create-form").click(function() {
    window.formAction = "create";
    $("#form-form").submit();
});

function createForm(data)
{
    let request = callAjax("createForm", 'POST', data, true);

    request.done(function(result) {

        if(result.success)
        {
            toastr.success("Formulario " + result.model.name + " creado");
            $("#modal-form").modal("hide");
            $('#form-form').trigger("reset");
            gridForms.dataSource.read();
        }

    }).fail(function(jqXHR, status) {
        $.unblockUI();
        toastr.error("Error");
    });
}

$("#btn-update-form").click(function() {
    window.formAction = "update";
    $("#form-form").submit();
});

function updateForm(data)
{
    let request = callAjax("updateForm", 'POST', data, true);

    request.done(function(result) {

        if(result.success)
        {
            toastr.success("Formulario " + result.model.name + " editado");
            $("#modal-form").modal("hide");
            $('#form-form').trigger("reset");
            gridForms.dataSource.read();
        }

    }).fail(function(jqXHR, status) {
        $.unblockUI();
        toastr.error("Error");
    });
}

function confirmDeleteForm()
{
    Swal.fire({
        title: 'Eliminar',
        text: "¿Eliminar formulario " + window.formSelected.name + "?",
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
        if (result.value) deleteForm();
    });
}

function deleteForm()
{
    let request = callAjax("deleteForm", 'POST', { "id": window.formSelected.id }, true);

    request.done(function(result) {

        if (result.success) {
            toastr.success("Formulario " + result.model.name + " eliminado");
            gridForms.dataSource.read();
        } else {
            toastr.warning("El formulario tiene relaciones activas");
        }

    }).fail(function(jqXHR, status) {
        $.unblockUI();
        toastr.error("Error");
    });
}

function goToFormEditor(e)
{
    e.preventDefault();

    var dataItem = this.dataItem($(e.currentTarget).closest("tr"));

    kendo.confirm("¿Ir a detalles del formulario?")
    .done(function(){

        let url = "form-editor?id=" + dataItem.id;
        document.location.href = url;

    })
    .fail(function(){

    });
}

function confirmDisableForm(element)
{
    let form = $(element).data('form');

    console.log(form);

    let data = { 'id': $(element).data('id'), 'enabled': $(element).is(':checked') };

    let action = $(element).is(':checked') ? "Activar" : "Inactivar";

    Swal.fire({
        title: action,
        text: "¿" + action + " formulario " + form + "?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar',
    }).then((result) => {

        if (result.value)
        {
            disableForm(data);
        }
        else
        {
            let property = $(element).is(':checked');
            $(element).prop("checked", !property); 
        }

    });
}

function disableForm(data)
{
    let request = callAjax("disableForm", 'POST', data, true);

    request.done(function(result) {

        if(result.success)
        {
            let action = result.model.enabled == true ? " habilitado" : " deshabilitado";

            toastr.success("Usuario " + result.model.name + action);

            gridForms.dataSource.read();
        }

    }).fail(function(jqXHR, status) {
        $.unblockUI();
        toastr.error("Error");
    });
}