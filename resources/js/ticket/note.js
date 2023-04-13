
$(document).ready(function () {

    $('#modalNote').on('show.bs.modal', function () {
        $('#title-modal-note').text((window.selectedTicket.id + " - " + window.selectedTicket.name));
        $('#note').trigger('focus');
    })

    $(document).on("click", ".btn-note", function(event){
        window.selectedTicket.id = $(this).data("idticket");
        getNotes();
    });

    $(document).on("click", ".btnDeleteNote", function(event){
        deleteNote($(this).data("idnote"));
    });

    $('#btnCreateNote').on('click', function(){
        createNote();
    });
    
    $("#note").on("keyup", function(e) {
        e.preventDefault();
        if (e.keyCode === 13) $('#btnCreateNote').click();
    });

});

function initNoteMenu()
{
    $("#context-menu").kendoContextMenu({
        target: "#modalNote",
        filter: ".my-note",
        select: function(e) {
            var idnote = $(e.target).data('idnote');
            var action = e.item.id;
            switch(action)
            {
                case "deleteNote":
                    deleteNote(idnote);
                    break;
            };
        }
    });
}

function getNotes()
{
    let request = callAjax('getNotes', 'POST', {'idticket' : window.selectedTicket.id}, true);

    request.done(function(result) {
        //PNotify.closeAll();
        $(".chats").html(result);
        $("#modalNote").modal("show");
        initNoteMenu();

    }).fail(function(jqXHR, status) {
        console.log('ERROR');
    });
}

function createNote()
{
    if(!validateNote()) return;
    $("#btnCreateNote").prop("disabled", true);

    let request = callAjax('createNote', 'POST', {'idticket' : window.selectedTicket.id, "note" : $('#note').val()}, true);

    request.done(function(result) {
        $("#btnCreateNote").prop("disabled", false);
        //PNotify.closeAll();
        $('#note').val("");
        $("#gridTicket").data("kendoGrid").dataSource.read();
        getNotes();
        
    }).fail(function(jqXHR, status) {
        $("#btnCreateNote").prop("disabled", false);
        console.log('ERROR');
    });
}

function deleteNote(idnote)
{
    let request = callAjax('deleteNote', 'POST', {'id' : idnote}, true);

    request.done(function(result) {
        //PNotify.closeAll();
        $("#gridTicket").data("kendoGrid").dataSource.read();
        getNotes();
        
    }).fail(function(jqXHR, status) {
        console.log('ERROR');
    });
}

function validateNote()
{
    if($("#note").val().length == 0)
    {
        PNotify.closeAll();
        PNotify.error({ title: 'Datos incompletos', text: 'Escriba una nota'});
        return false;
    }

    return true;
}

