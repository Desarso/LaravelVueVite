$(document).ready(function () {

    $('#modalEscalate').on('show.bs.modal', function () {
        $('#title-modal-escalate').text((window.selectedTicket.id + " - " + window.selectedTicket.name));
        multiSelectEscalateUser.value([]);
        dropDownListTeam.value(window.selectedTicket.idteam);
    });

    initMultiSelectEscalateUser();
    initDropDownListTeam();

});

$("#btn-escalate").click(function () {
    escalateTicket();
});

function initMultiSelectEscalateUser()
{
    multiSelectEscalateUser = $("#multiSelectEscalateUser").kendoMultiSelect({
        placeholder: locale("Responsible"),
        dataTextField: "text",
        dataValueField: "value",
        filter: "contains",
        height: 400,
        itemTemplate: "<span class='k-state-default' style='background-image: url(#:urlpicture#)'></span><span class='k-state-default'><h4>#: text #</h4><p>#: text #</p></span>",
        tagTemplate: "<span class='selected-value' style='background-image: url(#:urlpicture#)'></span><span>#: text #</span>",
        dataSource: global_users.filter(user => (user.enabled == true && user.deleted_at == null)),
    }).data("kendoMultiSelect");
}

function initDropDownListTeam()
{
    dropDownListTeam = $("#dropDownListTeamEscalate").kendoDropDownList({
        filter: "contains",
        dataTextField: "text",
        dataValueField: "value",
        dataSource: window.global_teams,
        height: 400,
        popup: { appendTo: $("#modalEscalate") }
    }).data("kendoDropDownList");
}

function escalateTicket()
{
    $.blockUI({ message: '<h1>Procesando...</h1>' });

    let data = { "idticket" : window.selectedTicket.id, "users": multiSelectEscalateUser.value(), "idteam": dropDownListTeam.value(), "action" : "escalate"};

    let request = callAjax('escalateTicket', 'POST', data, false);
    
    request.done(function(result) {

        $.unblockUI();

        if (result.success)
        {
            $("#modalEscalate").modal("hide");
            multiSelectEscalateUser.value([]);
            $("#gridTicket").data("kendoGrid").dataSource.read();
        }
        else
        {
            toastr.error(result.message, 'Permisos');
        }

    }).fail(function(jqXHR, status) {
        $.unblockUI();
        //console.log('error cleaning status change');
    });
}