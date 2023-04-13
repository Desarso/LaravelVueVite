$('#modalSignature').on('show.bs.modal', function () {
    $('#title-modal-signature').text((window.selectedTicket.id + " - " + window.selectedTicket.name));
    let signature = window.selectedTicket.signature;

    if (signature != null) {
        $("#unsigned").hide();
        $("#image-signature").show();
        $("#image-signature").attr("src", window.selectedTicket.signature);
    } else {
        $("#unsigned").show();
        $("#image-signature").hide();
    }
    
})

showModalSignature = function showModalSignature()
{
    $('#modalSignature').modal("show");
}