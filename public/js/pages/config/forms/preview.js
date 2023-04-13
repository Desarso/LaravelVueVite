$(document).ready(function() {
    initDropDownTicketType();
});

$("#btn-create-form-copy").click(function(e) {
    confirmCopyform();
});

$("#btn-copy").click(function(e) {
    $("#modal-form-copy").modal("show");
});

$('#modal-form-copy').on('show.bs.modal', function (e) {
    $('#title-modal-form-copy').text("Copiar formulario: " + dropDownChecklist.text());
    $("#name-form-copy").val("");
});

$("#btn-preview").click(function(e) {
    getFormPreview();
});

$('#modal-form-preview').on('show.bs.modal', function (e) {
    $('#title-modal-form-preview').text(dropDownChecklist.text());
});

function getFormPreview()
{
    let request = callAjax('getFormPreview', 'POST', {'idchecklist' : dropDownChecklist.value()}, true);

    request.done(function(result) {
        $("#form-container").html(result);
        $("#modal-form-preview").modal("show");
    }).fail(function(jqXHR, status) {
        console.log('ERROR');
    });
}

function initDropDownTicketType()
{
    dropDownTicketType = $("#dropDownTicketType").kendoDropDownList({
        dataTextField: "text",
        dataValueField: "value",
        dataSource: window.global_ticket_types,
        filter: "contains",
        height: 400,
        popup: { appendTo: $("#modal-form-copy") },
    }).data("kendoDropDownList");
}

function createFormCopy()
{
    let data = {
        'idchecklist' : dropDownChecklist.value(),
        'name'        : $("#name-form-copy").val(),
        'idtype'      : dropDownTicketType.value(),
    };

    let request = callAjax('createFormCopy', 'POST', data , true);

    request.done(function(result) {

        $("#modal-form-copy").modal("hide");

        result.model["value"]  = result.model.id;
        result.model["text"]   = result.model.name;

        //window.global_items.push(result.model);
        let dataSource = dropDownChecklist.dataSource.data();
        dataSource.push(result.model);
        dropDownChecklist.dataSource.data(dataSource);
        dropDownChecklist.value(result.model.id);
        dropDownChecklist.trigger('change',  {dataItem: result.model} );
        dropDownChecklist.focus();  


    }).fail(function(jqXHR, status) {
        console.log('ERROR');
    });
}

function confirmCopyform()
{
    Swal.fire({
        title: 'Copiar Formulario',
        text: "¿Está seguro de copiar formulario " + dropDownChecklist.text() + "?",
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
        if (result.value) createFormCopy();
    });
}