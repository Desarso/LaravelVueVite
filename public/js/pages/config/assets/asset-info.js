$(document).ready(function() {
    initDropDownListItem();
    initDropDownListSpot();
    initMultiSelectUser();
});

$("#btn-new-ticket").click(function(e) {
    $("#ticket-tab-fill").click();
});

function initDropDownListItem()
{
    dropDownListItem = $("#dropDownListItem").kendoDropDownList({
        optionLabel: "-- Seleccione --",
        dataTextField: "name",
        dataValueField: "id",
        dataSource: window.global_items,
        filter: "contains",
        height: 300
    }).data("kendoDropDownList");
}

function initDropDownListSpot()
{
    dropDownListSpot = $("#dropDownListSpot").kendoDropDownList({
        optionLabel: "-- Seleccione --",
        dataTextField: "text",
        dataValueField: "value",
        dataSource: window.global_spots,
        filter: "contains",
        height: 300,
        dataBound: function(e) {
            if(window.asset.idspot != null){
                this.value(window.asset.idspot);
            }
        }
    }).data("kendoDropDownList");
}

function initMultiSelectUser()
{
    multiSelectUser = $("#multiSelectUser").kendoMultiSelect({
        placeholder: "Responsables",
        dataTextField: "text",
        dataValueField: "value",
        filter: "contains",
        height: 300,
        tagTemplate: "<span class='selected-value' style='background-image: url(#:urlpicture#)'></span><span>#: text #</span>",
        itemTemplate: "<span class='selected-value' style='background-image: url(#:urlpicture#)'></span><span>#: text #</span>",
        dataSource: window.global_users,
        dataBound: function(e) {
            if(window.asset.idresponsible != null){
                this.value(window.asset.idresponsible);
            }
        }
    }).data("kendoMultiSelect");
}

$("#btn-create-ticket").click(function(e) {
    e.preventDefault();
    var data = $("#form-asset-ticket").serializeArray();
    if(validateData()) createTicket(data);
});

function createTicket(data)
{
    $.blockUI({ message: '<h1>Procesando...</h1>' });

    let request = callAjax('createAssetTicket', 'POST', data, false);

    request.done(function(result) {
        $.unblockUI();
        $("#form-asset-ticket").trigger("reset");
        PNotify.success({ title: 'Tarea creada', text: 'Acción completada con éxito' });


    }).fail(function(jqXHR, status) {
        $.unblockUI();
    });
}

function validateData()
{
    if(multiSelectUser.dataItems().length == 0 || dropDownListItem.value() == "" || dropDownListSpot.value() == "")
    {
        PNotify.closeAll();
        PNotify.error({ title: 'Datos incompletos', text: 'Complete los datos' });
        return false;
    }

    return true;
}