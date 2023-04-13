window.frequencies = [{"value": "DAILY", "text": "Días"}, {"value": "WEEKLY", "text": "Semanas"}, {"value": "MONTHLY", "text": "Meses"}];

window.plannerSelected = null;

$(document).ready(function() {

    initDropDownListWorkPlanEvaluate();
    initDropDownListItem();
    initDropDownListSpot();
    initDropDownListFrecuency();

    initMultiSelectUser();

    initDatePicker();
    initStartTimePicker();
    initEndTimePicker();

    initUntilDatePicker();

    initIntervalNumericTextBox();

    $(document).on("click", ".title-task", function(event) {

        window.plannerSelected = $(this).data('planner');
        $("#modal-title-planner").text("Editar tarea");
        setPlanner();

        $("#div-update-buttons").show();
        $("#div-create-buttons").hide();
        $("#modal-planner").modal("show");
    });

    resetPlannerForm();
});

$('#modal-planner').on('hide.bs.modal', function(e) {
    resetPlannerForm();

    let today = new Date();
    var date = new Date(today.getFullYear(), today.getMonth(), today.getDate(), today.getHours(), 0, 0);
    momentDate = moment(date);

    datePicker.value(date);

    window.plannerSelected = null;
});

$('#modal-planner').on('show.bs.modal', function(e) {

    $("#idworkplan").val(dropDownListWorkPlanFilter.value());

    if(window.plannerSelected == null)
    {
        let today = new Date();
        var date = new Date(today.getFullYear(), today.getMonth(), today.getDate(), today.getHours(), 0, 0);
        momentDate = moment(date);

        startTimePicker.value(momentDate.format("hh:mm A"));
        endTimePicker.value(momentDate.add(60, 'minutes').format("hh:mm A"));
    }

    dropDownListWorkPlanFilter.dataItem().type == "EVALUATIVE" ? $("#section-dropdownList-workplan").show() : $("#section-dropdownList-workplan").hide();

});

$("#switch-repeat").change(function(event) {
    $(this).is(':checked') == true ? $("#div-repeat-options").css('visibility', 'visible') : $("#div-repeat-options").css('visibility', 'hidden');
});

$("#btn-new-planner").click(function(e) {
    $("#modal-title-planner").text("Nueva tarea");
    $("#div-create-buttons").show();
    $("#div-update-buttons").hide();
    $("#modal-planner").modal("show");
});

$("#btn-create-planner").click(function(e) {

    var data = $("#form-planner").serializeArray();

    if (!validatePlanner(data)) return;

    let repeat = {"name" : "repeat", "value" : ($("#switch-repeat").is(":checked") ? true : false)};

    data.push(repeat); 

    createPlanner(data);
});

$("#btn-update-planner").click(function(e) {

    var data = $("#form-planner").serializeArray();

    if (!validatePlanner(data)) return;

    let repeat = {"name" : "repeat", "value" : ($("#switch-repeat").is(":checked") ? true : false)};

    data.push(repeat); 

    updatePlanner(data);
});

$("#btn-delete-planner").click(function(e) {
    confirmDeletePlanner();
});

function initUntilDatePicker()
{
    untilDatePicker = $("#until").kendoDatePicker().data("kendoDatePicker");
}

function initDatePicker()
{
    let today = new Date();
    var date = new Date(today.getFullYear(), today.getMonth(), today.getDate(), today.getHours(), 0, 0);

    datePicker = $("#date").kendoDatePicker({
        value: date,
    }).data("kendoDatePicker");
}

function initStartTimePicker()
{
    startTimePicker = $("#start").kendoTimePicker({
        change: changeHour,
    }).data("kendoTimePicker");
}

function initEndTimePicker()
{
    endTimePicker = $("#end").kendoTimePicker({
        change: changeHour,
    }).data("kendoTimePicker");
}

function changeHour()
{
    let startDate = new Date(startTimePicker.value());
    let endDate   = new Date(endTimePicker.value());
    startDate = moment(startDate);
    endDate   = moment(endDate);

    if(startDate.isAfter(endDate) || startDate.isSame(endDate) || endTimePicker.value() == null)
    {
        endTimePicker.value(startDate.add(60, 'minutes').format("hh:mm A"));
    }
}

function initIntervalNumericTextBox()
{
    intervalTextBox = $("#interval").kendoNumericTextBox({
        format: '{0:0}',
        min: 1
    }).data("kendoNumericTextBox");
}

function initDropDownListWorkPlanEvaluate()
{
    dropDownListWorkPlanEvaluate = $("#dropDownListWorkPlanEvaluate").kendoDropDownList({
        optionLabel: "-- Seleccione --",
        dataTextField: "text",
        dataValueField: "value",
        filter: "contains",
        dataSource: window.workPlans,
        popup: { appendTo: $("#modal-planner") },
        height: 400,
    }).data("kendoDropDownList");
}

function initDropDownListItem()
{
    dropDownListItem = $("#dropDownListItem").kendoDropDownList({
        optionLabel: "-- Seleccione --",
        dataTextField: "name",
        dataValueField: "id",
        filter: "contains",
        dataSource: window.global_items,
        popup: { appendTo: $("#modal-planner") },
        height: 400,
    }).data("kendoDropDownList");
}

function initDropDownListSpot()
{
    dropDownListSpot = $("#dropDownListSpot").kendoDropDownList({
        optionLabel: "-- Seleccione --",
        dataTextField: "text",
        dataValueField: "value",
        template: "<div> <h5>#:data.text#</h5> <small style='color:gray;'>#:data.spotparent#</small> </div>",
        filter: "contains",
        dataSource: window.global_user_branch,
        popup: { appendTo: $("#modal-planner") },
        height: 400,
    }).data("kendoDropDownList");
}

function initMultiSelectUser()
{
    multiSelectUser = $("#multiSelectUser").kendoMultiSelect({
        placeholder: "-- Seleccione --",
        dataTextField: "text",
        dataValueField: "value",
        filter: "contains",
        height: 400,
        dataSource: window.global_users,
    }).data("kendoMultiSelect");
}

function initDropDownListFrecuency()
{
    dropDownListFrecuency = $("#dropDownListFrecuency").kendoDropDownList({
        dataTextField: "text",
        dataValueField: "value",
        dataSource: window.frequencies,
        popup: { appendTo: $("#modal-planner") },
        height: 400,
        change: changeFrequency
    }).data("kendoDropDownList");
}

function changeFrequency(e)
{
    $('[name="days[]"]').prop("checked", false);
    $("#section-week-list").hide();

    switch(e.sender.value())
    {
        case "DAILY":
            break;
        
        case "WEEKLY":
            $("#section-week-list").show();
            break;

        case "MONTHLY":
            break;
    }
}

function createPlanner(data)
{
    let request = callAjax('createPlannerTask', 'POST', data, true);

    request.done(function(result) {

        $("#modal-planner").modal("hide");
        dropDownListWorkPlanFilter.trigger("change");
        $.unblockUI();

    }).fail(function(jqXHR, status) {
        $.unblockUI();
    });
}

function updatePlanner(data)
{
    let request = callAjax('updatePlannerTask', 'POST', data, true);

    request.done(function(result) {

        if(result.success)
        {
            toastr.success("La tarea fue editada");
            $("#modal-planner").modal("hide");
            dropDownListWorkPlanFilter.trigger("change");
        }
        else
        {
            toastr.warning("La tarea tiene relaciones activas");
        }

    }).fail(function(jqXHR, status) {
        $.unblockUI();
    });
}

function validatePlanner(data)
{
    if(dropDownListWorkPlanFilter.value() == "")
    {
        PNotify.closeAll();
        PNotify.error({ title: 'Datos incompletos', text: 'Seleccione el plan de trabajo' });
        return false;
    }

    if (dropDownListItem.value() == "" || dropDownListSpot.value() == "" || startTimePicker.value() == null || endTimePicker.value() == null)
    {
        PNotify.closeAll();
        PNotify.error({ title: 'Datos incompletos', text: 'Complete los datos' });
        return false;
    }

    if (dropDownListFrecuency.value() == "WEEKLY")
    {
        let result = data.find(o => o.name === "days[]");

        if(typeof result == "undefined")
        {
            PNotify.closeAll();
            PNotify.error({ title: 'Datos incompletos', text: 'Seleccione los días de la semana' });
            return false;
        }   
    }

    return true;
}

function resetPlannerForm()
{
    $("#form-planner").trigger("reset");
    
    setTimeout(() => { 
        dropDownListFrecuency.value('DAILY');
        dropDownListFrecuency.trigger('change');
    }, 300); 

    $("#div-repeat-options").css('visibility', 'hidden');
}

function confirmDeletePlanner()
{
    Swal.fire({
        title: 'Eliminar',
        text: "¿Eliminar tarea " + window.plannerSelected.text + "?",
        type: 'warning',
        buttonsStyling: true,
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Eliminar',
        confirmButtonClass: 'btn btn-primary',
        cancelButtonClass: 'btn btn-danger ml-1',
        cancelButtonText: 'Cancelar',
        buttonsStyling: false
    }).then(function(result) {
        if (result.value) deletePlanner();
    });
}

function deletePlanner()
{
    let request = callAjax("deletePlanner", 'POST', { "id": window.plannerSelected.id }, true);

    request.done(function(result) {

        if(result.success)
        {
            toastr.success("La tarea fue eliminada");
            $("#modal-planner").modal("hide");
            dropDownListWorkPlanFilter.trigger("change");
        }
        else
        {
            toastr.warning("La tarea tiene relaciones activas");
        }

    }).fail(function(jqXHR, status) {
        $.unblockUI();
        toastr.error("Error");
    });
}

function setPlanner()
{
    $("#idplanner").val(window.plannerSelected.id);
    $("#idworkplan").val(window.plannerSelected.idworkplan);
    $("#description").val(window.plannerSelected.description);

    dropDownListWorkPlanEvaluate.value(window.plannerSelected.idworkplan_evaluate);
    dropDownListItem.value(window.plannerSelected.iditem);
    dropDownListSpot.value(window.plannerSelected.idspot);

    multiSelectUser.value(JSON.parse(window.plannerSelected.users));
    datePicker.value(new Date(window.plannerSelected.start));
    startTimePicker.value(moment(new Date(window.plannerSelected.start)).format("hh:mm A"));
    endTimePicker.value(moment(new Date(window.plannerSelected.end)).format("hh:mm A"));
    intervalTextBox.value(window.plannerSelected.interval);
    dropDownListFrecuency.value(window.plannerSelected.frequency);

    switch(window.plannerSelected.frequency)
    {
        case "NEVER":

            dropDownListFrecuency.value("DAILY");
            break;

        case "DAILY":

            $('#switch-repeat').click();
            break;
        
        case "WEEKLY":

            $('#switch-repeat').click();
            $("#section-week-list").show();

            var days = window.plannerSelected.by_day.split(',');

            $.each($("input[name='days[]']"), function(){
                $(this).prop("checked", ($.inArray($(this).val(), days) != -1));
            });

            break;

        case "MONTHLY":

            $('#switch-repeat').click();
            break;
    }

    let until = (window.plannerSelected.until == null ? null : new Date(window.plannerSelected.until));
    untilDatePicker.value(until);
}
