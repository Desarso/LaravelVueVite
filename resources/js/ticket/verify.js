window.isapproved = null;

$('#modalVerify').on('show.bs.modal', function () {
    $('#title-modal-verify').text((window.selectedTicket.id + " - " + window.selectedTicket.name));
})

showModalVerify = function showModalVerify()
{
    clearVerify();
    $("#modalVerify").modal("show");
}

function clearVerify()
{
    $('#verifyNote').val("");
    $('#approved').prop('checked', false);
    $('#reprobate').prop('checked', false);
}

$('#approved').change(function() {
    window.isapproved = $(this).is(":checked") ? 1 : null;
    $('#reprobate').prop('checked', false);
});

$('#reprobate').change(function() {
    window.isapproved = $(this).is(":checked") ? 0 : null;
    $('#approved').prop('checked', false);
});

$('#btnVerifyTask').click(function() {
    verifyTicket();
});

verifyTicket = function verifyTicket()
{
    let request = callAjax('verifyTicket', 'POST', { 'action': 'verify', 'idticket': window.selectedTicket.id, "approved": window.isapproved, "note": $('#verifyNote').val() }, true);

    request.done(function(result) {

        if(result.success)
        {
            PNotify.success({ title: 'Tarea verificada', text: 'Acción completada con éxito' });
            $("#modalVerify").modal("hide");
            $("#gridTicket").data("kendoGrid").dataSource.read();
        }
        else
        {
            PNotify.error({ title: 'Permisos', text: result.message });
        }

    }).fail(function(jqXHR, status) {
        toastr.error('La acción no se puedo completar', 'Problemas');
    });
}



