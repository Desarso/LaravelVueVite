/**
 * Created by leonardo on 30/12/16.
 */

/**
 * @TODO: Hay que limitar las tareas que no son templates y no mostrar la opction de repetir en el formulario.
 * @TODO: También hay que evitar que se pueda cambiar el tipo de recurrencia y si se editar que solo aplique
 * para la fecha actual en adelante.
 */

var SchedulerModule = (function(window, $, undefined){

    var globlas = {
        INITILIZED : false
    };

    /**
     * Use getScheduler to access this var.
     *
     * @type {kendoScheduler}
     */
    var scheduler = null;


    var $Scheduler_idmodule = null;

    var requiredFields = {
        idfacility: {
            message: 'Valor requerido'
        },
        idtype: {
            message: 'Valor requerido'
        }
    };

    function init(){
        console.log('INIT SCHEDULER');

        if(globlas.INITILIZED) return;

        window.ShedulerMod = { onAdd : false, noName : false };
        globlas.INITILIZED = true;

        $("#scheduler").kendoScheduler({
            date: new Date(),
            editable: {
                editRecurringMode: "series"
            },
            startTime: todayFirstHour(),
            height: 1100,
            // eventTemplate: $("#event-template").html(),
            views: [
                "day",
                // "workWeek",
                "week",
                { type: "month", selected: true, eventHeight: 38 },
                "agenda",
                // { type: "timeline", eventHeight: 50 }
            ],
            //timezone: "Etc/UTC",
            timezone: window.timezone,
            dataSource: {
                isAllDay: true,
                sync: function() {
                    this.read();
                },
                batch: true,
                serverPaging: true,
                transport: {
                    read: {
                        url: "api/getTicketScheduler",
                        type : "POST",
                        dataType: "json",
                        data : viewRangeDates
                    },
                    update: {
                        url: "api/updateSchedulerSlotMoved",
                        type : "POST",
                        dataType: "json"
                    },
                    create: {
                        url: window.HOSTURL + "/task/create",
                        dataType: "json"
                    },
                    destroy:{
                        url: "api/removeTicket",
                        type : "POST",
                        dataType: "json"
                    },
                    parameterMap: function(options, operation) {
                        if (operation !== "read" && options.models) {

                            console.log('MODELS', options.models);

                            return {models: kendo.stringify(options.models)};
                        }

                        if(operation === 'read') return options;
                    }
                },
                schema: {
                    model: {
                        id: "id",
                        fields: {
                            id       : { from: "id", type: "number" },
                            title    : { from: "name", defaultValue: "Sin nombre", validation: { required: true } },
                            start    : { type: "date", from: "started_date" },
                            end      : { type: "date", from: "finished_date" },
                            isAllDay : { type: "boolean", from: "isAllDay" },
                            recurrence: { from: "recurrence", type: "object" }
                        }
                    },
                    parse: function (response) {
                        return response;
                    }
                },
                filter: {}
            },
            resources: [
                {
                    field: "roomId",
                    dataSource: [
                        { text: "Vencidos", value: 1, color: "#efc829" },
                        { text: "Al dia",   value: 2, color: "#2EB149" }
                    ],
                    title: "Room"
                }
            ],
            add: function (e)
            {
                console.log(e);
                e.preventDefault();
                showWindow();
                let datetimepickerstart = $('#startdate').data('kendoDateTimePicker');
                let datetimepickerfinish = $('#finishdate').data('kendoDateTimePicker');
                datetimepickerstart.value(new Date(e.event.start.setMinutes(e.event.start.getMinutes() + 480)));//se pasa el valor de la fecha y hora seleccionada en el calendario al modal de crear tiquete.  
                datetimepickerfinish.value(new Date(e.event.start.setMinutes(e.event.start.getMinutes() + 60)));
            },
            edit: function (e)
            {
                e.preventDefault();
                isupdating = true;
                ticketId = e.event.id;
                window.idticket = e.event.id;
                showWindow(true);
            },
            remove: function (e)
            {
                
            },
        });

        /*
        $.extend(true, kendo.ui.validator, {
            rules: {
                // Add custom validation rule to validate
                //description field
                
                description: function (input, params) {

                    return applyValidations(input);
                }
                
            },
            messages: {}
        });
*/
    }

    function applyValidations(input)
    {
        if(typeof input.data('bind') === 'undefined') return true;
        var key = input.data('bind').replace('value:', '');
        var validator = requiredFields[key];

        if(typeof validator === 'undefined') return true;

        console.log(validator);

        $parent = input.closest('div');
        $parent.find('.error-messages').remove();

        if (input.val() !== 'undefined' && input.val().length > 0) return true;

        $parent.append(buildErrors(validator.message));

        return false;
    }

    function buildErrors(messages)
    {
        var template = '<ul class="error-messages">' + ('<li>' +messages  + '</li>') + '</ul>';

        return template;
    }

    function onChkAllDayChange()
    {
        var $chk = $(this);

        if($chk.is(":checked")) $('input[name="start"]').unbind('change');
        else{
            $('input[name="start"]').on('change', updateDatePickerEndDate);
            updateDatePickerEndDate();
        }
    }

    function updateDatePickerEndDate()
    {
        var startDatePk = $('input[name="start"]').data('kendoDateTimePicker');
        var endDatePk   = $('input[name="end"]')  .data('kendoDateTimePicker');
        var strDate     = startDatePk.value();

        if (strDate)
        {
            var date = new Date(strDate);

            date.setMinutes(date.getMinutes() + 30);
            endDatePk.value(date);
            endDatePk.trigger('change');
        }
    }

    function showSpinnerOnSave()
    {
        var $elements = $('.k-scheduler-update');

        $elements.each(function (i, ele) {

            var $element = $(ele);
            var $div     = $element.parent('div');
            var $spinner = $('<a class="k-button k-primary" href="javascript:;">Por favor espere...</a>');

            $div.prepend($spinner);
            $element.remove();
        });
    }

    /**
     * returns the KendoScheduler object
     *
     * @returns {kendoScheduler}
     */
    function getScheduler()
    {
        if(scheduler === null) scheduler = $("#scheduler").data("kendoScheduler");

        return scheduler;
    }


    function viewRangeDates()
    {
        var view      = getScheduler().view();
        var endDate   = new Date(view.endDate().getTime());
        var startDate = new Date(view.startDate().getTime());

        endDate.setHours(23);
        endDate.setMinutes(59);
        endDate.setSeconds(59);

        return {
            endDate   : kendo.toString(endDate  , 'yyyy-MM-dd HH:mm:ss'),
            startDate : kendo.toString(startDate, 'yyyy-MM-dd HH:mm:ss')
        };
    }

    function todayFirstHour()
    {
        var tmpDate = new Date();

        return new Date(tmpDate.getYear(), tmpDate.getMonth(), tmpDate.getDay(), 00, 00, 00, 00);
    }

    function translateSchedulerLabels()
    {
        $("label[for='title']")         .text('Titulo');
        $("label[for='start']")         .text('Fecha inicio');
        $("label[for='end']")           .text('Fecha fin');
        $("label[for='isAllDay']")      .text('Evento de todo el día');
        $("label[for='recurrenceRule']").text('Repetir');
        $("label[for='description']")   .text('Descripción');
        $("label[for='end']")           .text('Fecha fin');
        $("label[for='end']")           .text('Fecha fin');
        $("li:contains('Never')")       .text('Nunca');
        $("li:contains('Daily')")       .text('Diario');
        $("li:contains('Weekly')")      .text('Semanal');
        $("li:contains('Monthly')")     .text('Mensual');
        $("li:contains('Yearly')")      .text('Anual').hide();
        $("li:contains('day')")         .hide();
        $("li:contains('weekday')")     .hide();
        $("li:contains('weekend day')") .hide();
        $('div[name="recurrenceRule"] input').on('change', function () {
            setTimeout(function () {
                $("li:contains('day')")        .hide();
                $("li:contains('weekday')")    .hide();
                $("li:contains('weekend day')").hide();

                $("label:contains('Repeat every: ')").text('Repetir cada: ');
                $("label:contains('End:')")          .text('Fin:');
                $("label:contains('Repeat on: ')")   .text('Repetir los: ');
            }, 200);
        });

    }

    function reloadTaskType(idTypeSelected)
    {
        var idItem        = $("select[data-bind='value:iditem']").val();
        var $taskTypesSlc = $("select[data-bind='value:idtype']").getKendoDropDownList();
        idItem            = idItem == '' ? 0 : idItem;

        if(typeof idTypeSelected !== 'undefined' && typeof idTypeSelected !== 'object')
        {
            var idItem = findItemByType(idTypeSelected);
            if(idItem !== false)
            {
                var $itemSlc = $("select[data-bind='value:iditem']").getKendoDropDownList();
                $itemSlc.value(idItem);
            }

            reloadTaskTypeSelect(idItem);
            $taskTypesSlc.value(idTypeSelected);
            return;
        }

        reloadTaskTypeSelect(idItem);
        reloadLocationSelect();
    }

    function reloadTaskTypeSelect(idItem)
    {
        var newDataSource = null;

        if(window.taskTypes.hasOwnProperty(idItem))
        {
            newDataSource = new kendo.data.DataSource({
                data: window.taskTypes[idItem]
            });
        }else
        {
            newDataSource = new kendo.data.DataSource({
                data: []
            });
        }

        var $taskTypesSlc = $("select[data-bind='value:idtype']").getKendoDropDownList();
        $taskTypesSlc.setDataSource(newDataSource);
    }

    function setDefaultName()
    {
        if(!window.ShedulerMod.onAdd) return;

        var $input       = $('input[name="title"]');
        var $slcType     = $("select[data-bind='value:idtype']").getKendoDropDownList();
        var $slcFacility = $("select[data-bind='value:idfacility']").getKendoDropDownList();
        var name         = $input.val();

        if (name == 'Sin nombre') window.ShedulerMod.noName = true;

        if (window.ShedulerMod.noName)
        {
            var facility = $slcFacility.selectedIndex == 0 ? false : $slcFacility.text();
            var type     = $slcType.selectedIndex == 0     ? false : $slcType.text();
            type         = (type !== false && type === 'Otro') ? false : type;
            var name     = (type === false ? '' : type.trim()) + (facility !== false && type !== false ? ' - ' : '') + (facility === false ? '' : facility.trim());

            if(name != '')
            {
                $input.val(name);
                $input.trigger('change');
            }
        }

    }

    function reloadLocationSelect(idTypeSelected)
    {
        var idType        = $("select[data-bind='value:idtype']").val();
        var newDataSource = null;

        if(window._locations.hasOwnProperty(idType))
        {
            newDataSource = new kendo.data.DataSource({
                data: window._locations[idType]
            });

        }else
        {
            newDataSource = new kendo.data.DataSource({
                data: []
            });
        }

        var $locationSlc = $("select[data-bind='value:idlocation']").getKendoDropDownList();
        $locationSlc.setDataSource(newDataSource);

        if(typeof idTypeSelected !== 'number' && newDataSource._data.length === 1)
        {
            idTypeSelected = newDataSource._data[0].value;
        }

        if(!isNaN(idTypeSelected))
        {
            $locationSlc.value(idTypeSelected);
            $locationSlc.trigger('change');
        }

        setDefaultName();
    }

    function findItemByType(idType)
    {
        var idItem = false;

        $.each(window.taskTypes, function (i, types) {
            $.each(types, function (j, type) {
                if(idType == type.value) {
                    idItem = i;
                    return false;
                }
            });

            if(idItem !== false) return false;
        });

        return idItem;
    }

    function getHTMLClassColorByAlertStatus(idstatus)
    {
        var cssClass = 'scheduler-color-default';

        if(!idstatus) return cssClass;

        switch (parseInt(idstatus)){
            case CONST.TASK_STATUS.NOT_STARTED:
                cssClass = 'scheduler-color-pending';
                break;
            case CONST.TASK_STATUS.IN_PROGRESS:
                cssClass = 'scheduler-color-progress';
                break;
            case CONST.TASK_STATUS.PAUSED:
                cssClass = 'scheduler-color-paused';
                break;
            case CONST.TASK_STATUS.FINISHED:
                cssClass = 'scheduler-color-resolved';
                break;
            default:
                cssClass = 'scheduler-color-default';
                break;
        }

        return cssClass;
    }

    function setIdMudele($idmodule) {

        $Scheduler_idmodule = $idmodule;
    }

    return {
        init                           : init,
        setIdMudele                    : setIdMudele,
        getHTMLClassColorByAlertStatus : getHTMLClassColorByAlertStatus

    }
})(window, jQuery);


$(function(){
    SchedulerModule.init();
});