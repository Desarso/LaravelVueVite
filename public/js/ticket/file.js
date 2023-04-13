$('#modalFile').on('show.bs.modal', function () {
    $('#title-modal-file').text((window.selectedTicket.id + " - " + window.selectedTicket.name));
});

showModalFile = function showModalFile()
{
    FileInputModule.clearVariables();
    FileInputModule.setBrowse(false);
    FileInputModule.init({ id: 2 }, $("#previewFileContainer"), window.selectedTicket.files, window.selectedTicket.id);
    $('#modalFile').modal("show");
}