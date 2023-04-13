$(document).ready(function () {

    $('#modalDuration').on('show.bs.modal', function () {
        $('#title-modal-duration').text((window.selectedTicket.id + " - " + window.selectedTicket.name));
    })

});

$('#btn-set-duration').on('click', function(){
    setTicketDuration();
});

function setTicketDuration()
{
    let data = {"idticket": window.selectedTicket.id, "duration": $('#duration').val(), "action": "setduration"};

    $.blockUI({ message: '<h1>Procesando...</h1>' });

    let request = callAjax('setTicketDuration', 'POST', data, false);
    
    request.done(function(result) {

        $.unblockUI();
        
        if(result.success)
        {
            $("#gridTicket").data("kendoGrid").dataSource.read();
            $("#modalDuration").modal("hide");
            $('#duration').val(1);
        }
        else
        {
            toastr.error(result.message, 'Permisos');
        }

    }).fail(function(jqXHR, status) {
        $.unblockUI();
        console.log('error cleaning status change');
    });
}
