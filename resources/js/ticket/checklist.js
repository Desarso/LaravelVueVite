const moment = require("moment");

$(document).ready(function () {

    $('#modalChecklist').on('show.bs.modal', function () {
        $('#title-modal-checklist').text((window.selectedTicket.id + " - " + window.selectedTicket.name));
    })

    $(document).on("click", ".btn-checklist", function(event){
        window.selectedTicket.id = $(this).data("idticket");
        getChecklist();
    });

    $(document).on("click", ".btnAddNoteOption", function(event){
        createChecklistNote(this);
    });

    $(document).on("change", "#switchEvaluator", function(event){
        var confirm = showConfirmModal('¿Evaluar checklist?', '¿Estás seguro?');

        confirm.on('pnotify.confirm', function() {
            assignEvaluator();
        })
    
        confirm.on('pnotify.cancel', function() {
            $("#switchEvaluator").prop("checked", false);
        });
    });

});

$("#btnSaveChecklist").click(function () {
    $("#formChecklist").data('approve') ? approveChecklist() : saveChecklist();
});

$("#btnPdfChecklist").click(function () {
    generatePdf();
});

function getObjectOption(input)
{
    return { "idchecklistoption": input.attr('name'), "value": input.val() };
}

function getChecklist()
{
    let request = callAjax('getChecklist', 'POST', {'idticket' : window.selectedTicket.id}, true);

    request.done(function(result) {
        //PNotify.closeAll();
        $("#checklist-container").html(result);
        $("#modalChecklist").modal("show");
    }).fail(function(jqXHR, status) {
        console.log('ERROR');
    });
}

function getApproveData()
{
    var elements = [];

    $('#formChecklist input').each(function (index){

        let input = $(this);

        if(!input.hasClass('approve')) return; //Solo los inputs de aprobaciones

        if(input.is(":checked"))
        {
            let obj = { "idchecklistoption": input.data('option'), "value": input.val() };
            elements.push(obj);
        }
    });

    console.log(elements);

    return elements;
}

function getChecklistFormData()
{
    var elements = [];

    $('#formChecklist input, #formChecklist select').each(function (index){

        let input = $(this);

        if(input.data('isnote') || input.hasClass('approve')) return; //Ignoramos los inputs de notas

        switch(true)
        {
            case input.is('select'):
                let select = getObjectOption(input);
                elements.push(select);

                break;

            case input.is('input'):

                switch(input.attr('type'))
                {
                    case 'checkbox':
                        input.is(":checked") ? input.val("1") : input.val("0");
                        let checkbox = getObjectOption(input);
                        elements.push(checkbox);
                        break;
        
                    case 'radio':
                        if(input.is(":checked"))
                        {
                            let radio = getObjectOption(input);
                            elements.push(radio);
                        }
                        break;
        
                    case 'text':
                        let text = getObjectOption(input);
                        elements.push(text);
                        break;
        
                    case 'number':
                        let number = getObjectOption(input);
                        elements.push(number);
                        break;

                    default:
                        let data = getObjectOption(input);
                        elements.push(data);
                        break;
                }

                break;
        }
        //alert('Opción: ' + input.data("option") + ' \n Type: ' + input.attr('type') + ' \n Name: ' + input.attr('name') + '\n Value: ' + input.val());
    });

    console.log(elements);

    return elements;
}

function saveChecklist()
{
    let request = callAjax('saveChecklist', 'POST', {'idticket' : window.selectedTicket.id, 'options': getChecklistFormData(), 'action': 'editchecklist'}, true);

    request.done(function(result) {

        if(result.success)
        {
            $("#modalChecklist").modal("hide");
            $("#gridTicket").data("kendoGrid").dataSource.read();
        }
        else
        {
            toastr.error(result.message, 'Permisos');
        }

    }).fail(function(jqXHR, status) {
        console.log('ERROR');
    });
}


function approveChecklist()
{
    let request = callAjax('saveChecklist', 'POST', {'idticket' : window.selectedTicket.id, 'options': getApproveData(), 'action': 'editchecklist', 'approve': true}, true);

    request.done(function(result) {

        if(result.success)
        {
            $("#modalChecklist").modal("hide");
            $("#gridTicket").data("kendoGrid").dataSource.read();
        }
        else
        {
            toastr.error(result.message, 'Permisos');
        }

    }).fail(function(jqXHR, status) {
        console.log('ERROR');
    });
}

function createChecklistNote(btn)
{
    $.blockUI({ message: '<h1>Procesando...</h1>' });

    $(btn).prop("disabled", true);
    let idchecklistoption = $(btn).data("idchecklistoption");
    let note              = $('#option-note-' + idchecklistoption).val();

    let data = {'idticket' : window.selectedTicket.id, 'idchecklistoption': idchecklistoption, 'note' : note};

    let request = callAjax('createNote', 'POST', data, false);

    request.done(function(result) {
        $.unblockUI();
        toastr.success('Acción completada con éxito', 'Nueva nota');
        $(btn).prop("disabled", false);
        $("#btn-collapse-" + idchecklistoption).css("color", "");
        $('#option-note-' + idchecklistoption).val("");
        $("#ul-note-" + idchecklistoption).append(getTempalteNote(result.data.note, result.data.created_by));

    }).fail(function(jqXHR, status) {
        toastr.error('La acción no se puedo completar', '¡Hubo un problema!');
        $(btn).prop("disabled", false);
        console.log('ERROR');
        $.unblockUI();
    });
}

function getTempalteNote(note, created_by)
{
    let user = getUser(created_by);

    return  "<a href='#' class='list-group-item list-group-item-action list-group-item-success'>" +
                "<div class='d-flex w-100 justify-content-between'>" +
                    "<h5 class='mb-1'>" + user.text + "</h5>" +
                    "<small>" + moment().format('YYYY-MM-DD HH:mm:ss') + "</small>" +
                "</div>" +
                "<p>" + note + "</p>" +
            "</a>";
}

function assignEvaluator()
{
    let request = callAjax('assignEvaluator', 'POST', { 'idticket': window.selectedTicket.id, 'action': 'verify' }, true);

    request.done(function(result) {

        PNotify.closeAll();

        if(result.success)
        {
            PNotify.success({ title: 'Ahora eres el evaluador', text: 'Acción completada con éxito' });
            getChecklist();
        }
        else
        {
            $("#switchEvaluator").prop("checked", false);
            PNotify.error({ title: 'Permisos', text: result.message });
        }

    }).fail(function(jqXHR, status) {
        PNotify.closeAll();
        PNotify.error({ title: 'Problemas', text: 'La acción no se puedo completar' });
    });
}

function generatePdf()
{
    $.blockUI({ message: '<h1>Procesando...</h1>' });

    $.ajax({
        type: 'POST',
        url: 'generatePdfChecklist',
        data: {"idticket" : window.selectedTicket.id},
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        xhrFields: {
            responseType: 'blob'
        },
        success: function(response){
            console.log(response);
            $.unblockUI();
            var blob = new Blob([response]);
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = window.selectedTicket.name + ".pdf";
            link.click();
        },
        error: function(blob){
            $.unblockUI();
            toastr.error('La acción no se puedo completar', '¡Hubo un problema!');
            console.log(blob);
        }
    });
}