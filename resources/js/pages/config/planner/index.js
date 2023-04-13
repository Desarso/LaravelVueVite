window.frequencies = [{"value": "DAILY", "text": "Días"}, {"value": "WEEKLY", "text": "Semanas"}];

//kendo.culture("es-ES");

$(document).ready(function() {

    initGridPlanner();
    initPlannerScheduler();
    initDropDownListItem();
    //initDropDownListAsset();
    initDropDownListFrecuency();
    initMultiSelectSpot();
    initMultiSelectUser();
    initMultiSelectCopy();
    //initMultiSelectTag();
    initIntervalNumericTextBox();
    initBusinessDaysNumericTextBox();
    initUntilDatePicker();
    initStartDateTimePicker();
    initEndDateTimePicker();

    $(document).on("click", ".switch-enable", function(event) {

        let enabled = $(this).is(':checked') == true ? 1 : 0;
        let data = { "id" : $(this).data("id"), "enabled" : enabled };
        enabledPlanner(data);

    });

    resetPlannerForm();
});

$('#modal-planner').on('hide.bs.modal', function(e) {
    resetPlannerForm();
    let today = new Date();
    var startDate = new Date(today.getFullYear(), today.getMonth(), today.getDate(), today.getHours(), 0, 0);
    startDateTimePicker.value(startDate);
});

$("#btn-new-planner").click(function(e) {
    $("#modal-planner").modal("show");
});

$("#btn-generate").click(function(e) {
    generatePlanners();
});

$("#scheduler-tab-justified").click(function(e) {
    schedulerPlanner.dataSource.read();
    gridPlanner.dataSource.read();
});

$("#grid-tab-justified").click(function(e) {
    schedulerPlanner.dataSource.read();
    gridPlanner.dataSource.read();
});

$("#btn-create-planner").click(function(e) {

    var data = $("#form-planner").serializeArray();
    if (!validatePlanner(data)) return;
    data[0].value == "" ? createPlanner(data) : updatePlanner(data);
});

function initEndDateTimePicker()
{
    let today = new Date();
    let startDate = new Date(today.getFullYear(), today.getMonth(), today.getDate(), today.getHours(), 0, 0);
    startDate = moment(startDate);

    endDateTimePicker = $("#end").kendoDateTimePicker({
        value: startDate.add(60, 'minutes').format("M/DD/YYYY hh:mm A"),
        change: endChange,
        culture: "es-ES",
        format: "MM/dd/yyyy hh:mm tt"
    }).data("kendoDateTimePicker");
}

function endChange()
{
    checkRange();
}

function initStartDateTimePicker()
{
    let today = new Date();
    var startDate = new Date(today.getFullYear(), today.getMonth(), today.getDate(), today.getHours(), 0, 0);

    startDateTimePicker = $("#start").kendoDateTimePicker({
        value: startDate,
        change: startChange,
        culture: "es-ES",
        format: "MM/dd/yyyy hh:mm tt"
    }).data("kendoDateTimePicker");
}

function startChange()
{  
    checkRange();
}

function checkRange()
{
    let startDate = new Date(startDateTimePicker.value());
    let endDate   = new Date(endDateTimePicker.value());
    startDate = moment(startDate);
    endDate   = moment(endDate);

    if(startDate.isAfter(endDate))
    {
        //PNotify.error({ title: 'Rango de fechas', text: 'La fecha de inicio debe de ser mayor a la fecha de fin' });
        endDateTimePicker.value(startDate.add(60, 'minutes').format("M/DD/YYYY hh:mm A"));
    }
}

function initUntilDatePicker()
{
    untilDatePicker = $("#until").kendoDatePicker({
        culture: "es-ES",
        format: "MM/dd/yyyy hh:mm tt"
    }).data("kendoDatePicker");
}

function initIntervalNumericTextBox()
{
    intervalTextBox = $("#interval").kendoNumericTextBox({
        format: '{0:0}',
        min: 1
    }).data("kendoNumericTextBox");
}

function initBusinessDaysNumericTextBox()
{
    businessDaysTextBox = $("#businessDays").kendoNumericTextBox({
        format: '{0:0}',
        min: 0
    }).data("kendoNumericTextBox");
}

function initDropDownListItem()
{
    dropDownListItem = $("#dropDownListItem").kendoDropDownList({
        optionLabel: "-- Seleccione --",
        dataTextField: "text",
        dataValueField: "value",
        filter: "contains",
        dataSource: window.items,
        popup: { appendTo: $("#modal-planner") },
        height: 400,
    }).data("kendoDropDownList");
}

function initDropDownListAsset()
{
    dropDownListAsset = $("#dropDownListAsset").kendoDropDownList({
        optionLabel: "-- Seleccione --",
        dataTextField: "name",
        dataValueField: "id",
        filter: "contains",
        dataSource: window.global_assets,
        popup: { appendTo: $("#modal-planner") },
        height: 400,
    }).data("kendoDropDownList");
}

function initMultiSelectSpot()
{
    let newSpots = window.global_spots.filter(spot => spot.enabled == true && spot.deleted_at == null);

    multiSelectSpot = $("#multiSelectSpot").kendoMultiSelect({
        placeholder: "-- Seleccione --",
        dataTextField: "text",
        dataValueField: "value",
        filter: "contains",
        height: 400,
        dataSource: newSpots,
    }).data("kendoMultiSelect");
}

function initMultiSelectUser()
{
    multiSelectUser = $("#multiSelectUser").kendoMultiSelect({
        placeholder: "-- Seleccione --",
        dataTextField: "text",
        dataValueField: "value",
        filter: "contains",
        height: 400,
        dataSource: global_users.filter(user => (user.enabled == true && user.deleted_at == null))
    }).data("kendoMultiSelect");
}

function initMultiSelectCopy()
{
    multiSelectCopy = $("#multiSelectCopy").kendoMultiSelect({
        placeholder: "-- Seleccione --",
        dataTextField: "text",
        dataValueField: "value",
        filter: "contains",
        height: 400,
        dataSource: global_users.filter(user => (user.enabled == true && user.deleted_at == null))
    }).data("kendoMultiSelect");
}

function initMultiSelectTag()
{
    multiSelectTag = $("#multiSelectTag").kendoMultiSelect({
        placeholder: "-- Seleccione --",
        dataTextField: "text",
        dataValueField: "value",
        filter: "contains",
        height: 400,
        dataSource: window.global_tags
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
    }
}

function initGridPlanner()
{
    gridPlanner = $("#gridPlanner").kendoGrid({
        excel: {
            fileName: "Whagons Planner.xlsx",
        },
        dataSource: {
            transport: {
                read: {
                    url: "getPlanner",
                    type: "get",
                    dataType: "json"
                },
                destroy: {
                    url: "deletePlanner",
                    type: "post",
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
                    }
                }
            },
            pageSize: 20,
            schema: {
                model: {
                    id: "id",
                    fields: {
                        id: { type: "number", editable: false, nullable: true },
                        description: { editable: true, field: "description", type: "string" },
                        users: { field: "users" },
                        iditem: { editable: true, field: "iditem", type: "number", nullable: true, validation: { required: { message: "Ítem es requerido" } } },
                        enabled: { editable: true, field: "enabled", type: "boolean", defaultValue: true },
                    }
                }
            },
        },
        editable: {
            mode: "popup"
        },
        toolbar: [],
        sortable: true,
        reorderable: true,
        resizable: true,
        navigatable: true,
        pageable: {
            refresh: true,
            pageSizes: true,
            buttonCount: 5,
        },
        filterable: true,
        height: "650px",
        noRecords: {
            template: "<div class='alert alert-danger mt-1 alert-validation-msg' role='alert'><i class='feather icon-info mr-1 align-middle'></i><span>No hay tareas programadas</span></div>"
        },
        columns: [
            { command: { name: "showPlanner", text: "", click: showPlanner, iconClass: "fad fa-pen commandIconOpacity" },  title: " ", width: "40px" },
            {
                field: "iditem",
                title: 'Tarea',
                width: "150px",
                values: window.items,
                filterable: {
                    multi: true,
                    search: true
                }
            },
            {
                field: "idspot",
                title: "Lugar",
                width: "130px",
                values: window.global_spots,
                filterable: {
                    multi: true,
                    search: true
                }
            },
            {
                field: "users",
                title: "Responsables",
                width: "130px",
                values: window.global_users,
                filterable: false,
                template: "#=formatUsers(users)#",
                filterable: false
            },
            {
                field: "interval",
                title: "Intervalo",
                width: "50px",
                filterable: false
            },
            {
                field: "frequency",
                title: "Frecuencia",
                width: "60px",
                filterable: false
            },
            {
                field: "description",
                title: "Descripción",
                hidden: true,
                width: "200px",
                filterable: false,
                media: "(min-width: 850px)"
            },
            {
                field: "tickets_count",
                title: "Tareas",
                width: "60px",
                filterable: false
            },
            {
                field: "enabled",
                title: "Estado",
                template: "#=formatEnabled(id, enabled)#",
                width: "80px",
                filterable: false
            },
            { command: { name: "destroy", text: " ", iconClass: "fad fa-trash commandIconDelete" }, title: " ", width: "50px", media: "(min-width: 850px)" }
        ],
    }).data("kendoGrid");   
}

function showPlanner(e)
{
    e.preventDefault();
    var row = this.dataItem($(e.currentTarget).closest("tr"));
    $("#modal-planner").modal("show");

    $("#idplanner").val(row.id);
    $("#description").val(row.description);
    dropDownListItem.value(row.iditem);
    //dropDownListAsset.value(row.idasset);
    multiSelectUser.value(JSON.parse(row.users));
    multiSelectCopy.value(JSON.parse(row.copies));
    //multiSelectTag.value(JSON.parse(row.tags));
    multiSelectSpot.value(row.idspot);
    startDateTimePicker.value(new Date(row.start));
    endDateTimePicker.value(new Date(row.end));
    intervalTextBox.value(row.interval);
    businessDaysTextBox.value(row.business_days);
    dropDownListFrecuency.value(row.frequency);

    switch(row.frequency)
    {
        case "DAILY":
            break;
        
        case "WEEKLY":

            $("#section-week-list").show();

            var days = row.by_day.split(',');

            $.each($("input[name='days[]']"), function(){
                $(this).prop("checked", ($.inArray($(this).val(), days) != -1));
            });

            break;
    }

    let until = (row.until == null ? null : new Date(row.until));
    untilDatePicker.value(until);
}

function formatUsers(users)
{
    if(users == null) return "";

    users = JSON.parse(users);

    let result = "<ul class='list-unstyled users-list m-0  d-flex align-items-center'>";

    $.each(users, function( index, value ) {

        let user = getUser(value);

        result += "<li data-toggle='tooltip' data-popup='tooltip-custom' data-placement='top' data-original-title='" + user.text + "' class='avatar pull-up'>" +
                    "<img class='media-object rounded-circle' src='" + user.urlpicture + "' alt='Avatar' height='30' width='30'>" +
                  "</li>";
    });

    return result + "</ul>";
}

function formatEnabled(id, enabled)
{
    return "<div class='custom-control custom-switch switch-lg custom-switch-success'>" +
                "<input type='checkbox' class='switch-enable custom-control-input' id='switch-enable-" + id + "' " + (enabled == 1 ? "checked" : "") + " data-id=" + id + ">" +
                    "<label class='custom-control-label' for='switch-enable-" + id + "'>" +
                    "<span class='switch-text-left'>Activo</span>" +
                    "<span class='switch-text-right'>Inactivo</span>" +
                "</label>" +
            "</div>";
}

function initPlannerScheduler()
{
    schedulerPlanner = $("#scheduler").kendoScheduler({
        date: new Date(),
        startTime: todayFirstHour(),
        height: "650px",
        toolbar: ["pdf"],
        pdf: {
            fileName: "Kendo UI Scheduler Export.pdf",
            proxyURL: "https://demos.telerik.com/kendo-ui/service/export"
        },
        views: [
            "day",
            "week",
            "workWeek",
            {
                type: "month",
                eventsPerDay: 8,
                eventHeight: 55,
                eventSpacing: 5,
                adaptiveSlotHeight: true,
                selected: true
            },
            {
                type: "agenda",
                //eventTemplate: $("#agenda-template").html()
            }
        ],
        //allDayTemplate: $("#allday-event-template").html(),
        //eventTemplate: $("#allday-event-template").html(),
        timezone: moment.tz.guess(),
        dataSource: {
            batch: true,
            transport: {
                read: {
                    url: "getAllScheduler",
                    type: "get",
                    dataType: "json"
                },
                parameterMap: function (options, operation) {
                    if (operation !== "read" && options.models) {
                        return { models: kendo.stringify(options.models) };
                    }
                }
            },
            schema: {
                model: {
                    id: "id",
                    fields: {
                        id: { from: "id", type: "number" },
                        title: { from: "title", defaultValue: "No title", validation: { required: true } },
                        start: { type: "date", from: "start" },
                        end: { type: "date", from: "end" },
                        description: { from: "description" },
                        recurrenceRule: { from: "recurrenceRule" },
                        isAllDay: { type: "boolean", from: "all_day" }
                    }
                }
            }
        },
        dataBinding: function(e) {},
        add: function (e)
        {
            e.preventDefault();

            if(e.event.hasOwnProperty("id"))
            {
                PNotify.error({ title: 'Acción no permitida', text: 'Use la lista para cambiar la configuración de la tarea' });
                return;
            }

            $("#modal-planner").modal("show");

            startDateTimePicker.value(e.event.start);
            endDateTimePicker.value(e.event.end);
        },
        edit: function (e)
        {
            e.preventDefault();
            PNotify.error({ title: 'Acción no permitida', text: 'Use la lista para cambiar la configuración de la tarea' });
        }
    }).data("kendoScheduler");
}

function todayFirstHour()
{
    var date = new Date();

    return new Date(date.getYear(), date.getMonth(), date.getDay(), 00, 00, 00, 00);
}

function createPlanner(data)
{
    $.blockUI();

    let request = callAjax('createPlanner', 'POST', data, false);

    request.done(function(result) {

        $("#modal-planner").modal("hide");
        gridPlanner.dataSource.read();
        schedulerPlanner.dataSource.read();
        $.unblockUI();

    }).fail(function(jqXHR, status) {
        $.unblockUI();
        console.log('error during creating planner!');
    });
}

function updatePlanner(data)
{
    $.blockUI();

    let request = callAjax('updatePlanner', 'POST', data, false);

    request.done(function(result) {
        $("#modal-planner").modal("hide");
        gridPlanner.dataSource.read();
        $.unblockUI();

    }).fail(function(jqXHR, status) {
        $.unblockUI();
        console.log('error during creating planner!');
    });
}

function enabledPlanner(data)
{
    $.blockUI();

    let request = callAjax('enabledPlanner', 'POST', data, false);

    request.done(function(result) {
        gridPlanner.dataSource.read();
        $.unblockUI();

    }).fail(function(jqXHR, status) {
        $.unblockUI();
        console.log('error during creating planner!');
    });
}

function generatePlanners()
{
    $.blockUI();

    let request = callAjax('generateRecurringTickets', 'POST', null, false);

    request.done(function(result) {
        $.unblockUI();

    }).fail(function(jqXHR, status) {
        $.unblockUI();
        console.log('error during creating planner!');
    });
}

function resetPlannerForm()
{
    $("#form-planner").trigger("reset");
    
    setTimeout(() => { 
        dropDownListFrecuency.value('DAILY');
        dropDownListFrecuency.trigger('change');
    }, 300); 
}

function validatePlanner(data)
{
    if (dropDownListItem.value() == "" || multiSelectSpot.dataItems().length == 0 || startDateTimePicker.value() == null || endDateTimePicker.value() == null)
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




