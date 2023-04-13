showModalProtocol= function showModalProtocol()
{
    showProtocol();
    $("#modalProtocol").modal("show");
}

showProtocol = function showProtocol() {

    let request = callAjax('showProtocol', 'POST', { "iditem" : window.selectedTicket.iditem }, false);

    request.done(function(result) {
        
        $("#content-protocol").html(result);

    }).fail(function(jqXHR, status) {

    });

}