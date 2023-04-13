    window.global_user_branch = [];
    window.global_user_workplan = [];

    kendo.ui.TimelineMonthView = kendo.ui.TimelineMonthView.extend({

        _createColumnsLayout: function(resources, inner) {
          return customCreateLayoutConfiguration("columns", resources, inner);
        },

        _createRowsLayout: function(resources, inner) {
          return customCreateLayoutConfiguration("rows", resources, inner);
        },

    });

    function customCreateLayoutConfiguration(name, resources, inner) {

        var resource = resources[0];
        if (resource) {
          var configuration = [];

          var data = resource.dataSource.view();

          for (var dataIndex = 0; dataIndex < data.length; dataIndex++) {

            let spot = global_spots.find((item) => { return item.value == data[dataIndex].idspot; });

            let usersString = formatUsers(data[dataIndex].users_text);

            var defaultText = kendo.htmlEncode(kendo.getter(resource.dataTextField)(data[dataIndex]));

            var template = kendo.template("<span data-planner='"+ JSON.stringify(data[dataIndex]) +"' class='title-task'>#=data#</span> " + usersString + "<br> <small style='color:darkgray'>" + spot.text + "</small>");

            var obj = {
              text: template(defaultText),
              className: "k-slot-cell"
            };

            obj[name] = customCreateLayoutConfiguration(name, resources.slice(1), inner);

            configuration.push(obj);
          }
          return configuration;
        }
        return inner;
    }   

$(document).ready(function() {

    initDropDownListWorkPlanFilter();
    initDropDownListUser();

    setTimeout(() => {
        initWorkPlanScheduler();
    }, 5);

    setInterval(() => {
        schedulerWorkPlan.dataSource.read();
    }, 60000);

});

$("#btn-excel").click(function () {
    exportWorkPlanToExcel();
});

function initDropDownListUser()
{
    dropDownListUser = $("#dropDownListUser").kendoDropDownList({
        optionLabel: "-- Seleccione --",
        dataTextField: "text",
        dataValueField: "value",
        filter: "contains",
        dataSource: window.global_users,
        height: 400,
        change: changeFilter,
    }).data("kendoDropDownList");
}

function initDropDownListWorkPlanFilter()
{
    dropDownListWorkPlanFilter = $("#dropDownListWorkPlanFilter").kendoDropDownList({
        optionLabel: "-- Seleccione --",
        dataTextField: "text",
        dataValueField: "value",
        filter: "contains",
        template: $("#script-template-workplan").html(),
        dataSource: window.workPlans,
        height: 400,
        dataBound: function() {

            if(window.workPlans.length > 0)
            {
                setTimeout(() => { 
                    let value = (workPlanSelected != null ? workPlanSelected : window.workPlans[0].value);
                    dropDownListWorkPlanFilter.value(value);
                    changeSpotList(); 
                }, 5);
            }
        },
        change: changeFilter,
    }).data("kendoDropDownList");
}

function changeFilter()
{
    changeSpotList(); 
    $("#scheduler").html("");
    schedulerWorkPlan.destroy();
    getPlannerList();
}

function getPlannerList()
{
    let request = callAjax('getPlannerList', 'POST', { 'idworkplan': dropDownListWorkPlanFilter.value(), 'iduser': dropDownListUser.value() }, true);

    request.done(function(result) {
        window.planners = result;
        initWorkPlanScheduler();

    }).fail(function(jqXHR, status) {
        $.unblockUI();
        console.log('error during creating planner!');
    });
}

function initWorkPlanScheduler()
{
    schedulerWorkPlan = $("#scheduler").kendoScheduler({
        date: new Date(),
        startTime: todayFirstHour(),
        dateHeaderTemplate: kendo.template("<strong>#=kendo.toString(date, 'MMM d')#</strong>"),
        height: "650px",
        toolbar: [],
        pdf: {
            fileName: "Kendo UI Scheduler Export.pdf",
            proxyURL: "https://demos.telerik.com/kendo-ui/service/export"
        },
        views: [{
            type: "timelineMonth",
            startTime: new Date("2013/6/13 00:00 AM"),
            majorTick: 1440
        }],
        //allDayTemplate: $("#allday-event-template").html(),
        eventTemplate: $("#event-template").html(),
        timezone: moment.tz.guess(),
        dataSource: {
            sync: function() {
                this.read();
            },
            batch: true,
            serverPaging: true,
            transport: {
                read: {
                    url: "getDataWorkPlan",
                    type: "get",
                    dataType: "json",
                    data : getParams
                },
                parameterMap: function (options, operation) {
                    if (operation !== "read" && options.models) {
                        return { models: kendo.stringify(options.models) };
                    }

                    if(operation === 'read') return options;
                }
            },
            requestEnd: function(e) {

                if(e.type == "read")
                {
                    if(e.response.length > 0) scrollToCurrentDate();
                }
    
            },
            schema: {
                model: {
                    id: "id",
                    fields: {
                        id: { from: "id", type: "number" },
                        idplanner: { from: "idplanner", type: "number" },
                        title: { from: "title", defaultValue: "No title", validation: { required: true } },
                        start: { type: "date", from: "start" },
                        end: { type: "date", from: "end" },
                        description: { from: "description" },
                        users: { from: "users" },
                        recurrenceRule: { from: "recurrenceRule" },
                        isAllDay: { type: "boolean", from: "all_day" }
                    }
                }
            }
        },
        group: {
            resources: ["Rooms"],
            orientation: "vertical"
        },
        resources: [
            {
                field: "idplanner",
                name: "Rooms",
                dataSource: window.planners,
                title: "Room"
            },
        ],
        editable: {
            move: false,
            destroy: false,
            resize: false
        },
        dataBinding: function(e) {},
        dataBound: function(e) {

            /* The result can be observed in the DevTools(F12) console of the browser. */
            var view = this.view();
            var events = this.dataSource.view();

            dropDownListWorkPlanFilter.dataItem().type == "STANDARD" ? setProgressBar(events) : setProgressBarEvaluation(events);

            var view = this.view();
            view.datesHeader.find("tr:last").prev().hide();
            view.timesHeader.find("tr:last").prev().hide();
        },
        add: function (e)
        {
            e.preventDefault();

            $("#btn-new-planner").click();

            datePicker.value(e.event.start);
        },
        edit: function (e)
        {
            e.preventDefault();

            if(e.event.ticket != null)
            {
                kendo.confirm("¿Ver tarea en el dashboard?")
                .done(function(){
                    let url = "dashboard-tasks?tickets=" + e.event.ticket.id;
                    window.open(url, '_blank');
                })
                .fail(function(){
        
                });
            }
        }
    }).data("kendoScheduler");
}

function setProgressBar(events)
{
    let finished = 0;

    $("#section-progress-bar-evaluation").hide();
    $("#section-progress-bar").show();

    $.each(events, function(key, event) {
        if(event.idstatus == 4) finished++;

        if(event.note == 3) excellent++;
        if(event.note == 2) regular++;
        if(event.note == 1) bad++;
        if(event.note == 0) notdone++;
    });

    let progress = (events.length == 0) ? 0 : (Math.round((finished  / events.length) * 100));

    $("#work-plan-progress-bar").width(progress + "%");
    $("#work-plan-progress-bar").text(progress + "%");
}

function setProgressBarEvaluation(events)
{
    var excellent = 0, regular = 0, bad = 0, notdone = 0;

    $("#section-progress-bar-evaluation").show();
    $("#section-progress-bar").hide();

    $.each(events, function(key, event) {

        if(event.note == 3) excellent++;
        if(event.note == 2) regular++;
        if(event.note == 1) bad++;
        if(event.note == 0) notdone++;

    });

    let progress_excellent = (events.length == 0) ? 0 : (Math.round((excellent / events.length) * 100));
    let progress_regular   = (events.length == 0) ? 0 : (Math.round((regular / events.length) * 100));
    let progress_bad       = (events.length == 0) ? 0 : (Math.round((bad / events.length) * 100));
    let progress_notdone   = (events.length == 0) ? 0 : (Math.round((notdone / events.length) * 100));

    $("#progress-bar-excellent").width(progress_excellent + "%");
    $("#progress-bar-excellent").text(progress_excellent == 0 ? "" : (progress_excellent + "%"));

    $("#progress-bar-regular").width(progress_regular + "%");
    $("#progress-bar-regular").text(progress_regular == 0 ? "" : (progress_regular + "%"));

    $("#progress-bar-bad").width(progress_bad + "%");
    $("#progress-bar-bad").text(progress_bad == 0 ? "" : (progress_bad + "%"));

    $("#progress-bar-notdone").width(progress_notdone + "%");
    $("#progress-bar-notdone").text(progress_notdone == 0 ? "" : (progress_notdone + "%"));
}

function getParams()
{
    var view      = schedulerWorkPlan.view();
    var endDate   = new Date(view.endDate().getTime());
    var startDate = new Date(view.startDate().getTime());

    endDate.setHours(23);
    endDate.setMinutes(59);
    endDate.setSeconds(59);

    return {
        endDate    : kendo.toString(endDate  , 'yyyy-MM-dd HH:mm:ss'),
        startDate  : kendo.toString(startDate, 'yyyy-MM-dd HH:mm:ss'),
        idworkplan : dropDownListWorkPlanFilter.value(),
        iduser     : dropDownListUser.value()
    };
}

function todayFirstHour()
{
    var date = new Date();

    return new Date(date.getYear(), date.getMonth(), date.getDay(), 00, 00, 00, 00);
}

function formatUsers(users)
{
    let usersString = "";

    $.each(users.split(","), function( index, value ) {
        usersString += "<div class='badge badge-secondary'>" + value + "</div> ";
    });

    return usersString;
} 

function changeSpotList() {
    let branch = dropDownListWorkPlanFilter.dataItem();
    window.global_user_branch = window.global_spots.filter(spot => spot.value == branch.idspot);
    getSpotChildren(branch.idspot, window.global_user_branch);
    initDropDownListSpot();
}

function getSpotChildren(idspot, result = []) {

    var nodes = window.global_spots.filter(spot => spot.idparent == idspot && spot.value != idspot);

    for (var i = 0; i < nodes.length; i++) {
        node = nodes[i];
        result.push(node);
        getSpotChildren(node.value, result);
    }
}


function scrollToCurrentDate()
{
    var scheduler = $("#scheduler").data("kendoScheduler");
    var contentDiv = scheduler.element.find("div.k-scheduler-content");
    var pointer = scheduler.element.find(".k-current-time-arrow-down");

    if(pointer.length > 0) contentDiv.scrollLeft(pointer.offset().left - contentDiv.offset().left - 100);
}

function exportWorkPlanToExcel()
{
    var startDate = moment(schedulerWorkPlan.view().startDate()).startOf('day').format('YYYY/MM/DD HH:mm:ss');
    var endDate   = moment(schedulerWorkPlan.view().endDate()).endOf('day').format('YYYY/MM/DD HH:mm:ss');

    let request = callAjax("exportWorkPlanToExcel", 'GET', { "idworkplan" : dropDownListWorkPlanFilter.value(), "startDate" : startDate, "endDate" : endDate }, true);

    request.done(function(response, textStatus, request) {
        var a = document.createElement("a");
        a.href = response.file;
        a.download = response.name;
        document.body.appendChild(a);
        a.click();
        a.remove();
        $.unblockUI();
    }).fail(function(jqXHR, status) {
        $.unblockUI();
    });
}