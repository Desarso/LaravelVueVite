// Last Modified: 21-11-2021
// By: Matthias Malek



var apiURL = '/api/ganttData?id=';
var gantt_root = 100001; // TODO: calcular cual es el root
var selected_task = 0;

$(function() {

    // Set URL    
    var urlParams = new URLSearchParams(window.location.search);
    projectId = urlParams.has('id') ? urlParams.get('id') : null;

    // SET locale
    gantt.i18n.setLocale("es");
    gantt.config.date_format = "%Y-%m-%d";
    var labels = gantt.locale.labels;
    labels.column_owner = labels.section_owner = locale("Owner");

    // *** CONFIGURATIONS ***
    autoScheduling = false;
    gantt.config.min_column_width = 80;

    gantt.config.order_branch = true;
    gantt.config.order_branch_free = true;

    gantt.config.scale_height = 30;
    gantt.config.grid_width = 600;
    gantt.config.row_height = 25;



    gantt.config.lightbox.sections = [
        { name: "description", height: 70, map_to: "text", type: "textarea", focus: true },
        { name: "type", type: "typeselect", map_to: "type" },
        { name: "owner", height: 22, map_to: "owner_id", type: "select", options: gantt.serverList("staff") },
        { name: "time", type: "duration", map_to: "auto" },
    ];

    // ZOOM Config

    var zoomConfig = {
        levels: [{
                name: "day",
                scale_height: 27,
                min_column_width: 80,
                scales: [
                    { unit: "day", step: 1, format: "%d %M" }
                ]
            },
            {
                name: "week",
                scale_height: 50,
                min_column_width: 50,
                scales: [{
                        unit: "week",
                        step: 1,
                        format: function(date) {
                            var dateToStr = gantt.date.date_to_str("%d %M");
                            var endDate = gantt.date.add(date, -6, "day");
                            var weekNum = gantt.date.date_to_str("%W")(date);
                            return "Semana " + weekNum + ", " + dateToStr(date) + " - " + dateToStr(endDate);
                        }
                    },
                    { unit: "day", step: 1, format: "%j %D" }
                ]
            },
            {
                name: "month",
                scale_height: 50,
                min_column_width: 120,
                scales: [
                    { unit: "month", format: "%F, %Y" },
                    // { unit: "week", format: "Semana #%W" }
                ]
            },
            {
                name: "quarter",
                height: 50,
                min_column_width: 90,
                scales: [
                    { unit: "month", step: 1, format: "%M" },
                    {
                        unit: "quarter",
                        step: 1,
                        format: function(date) {
                            var dateToStr = gantt.date.date_to_str("%M");
                            var endDate = gantt.date.add(gantt.date.add(date, 3, "month"), -1, "day");
                            return dateToStr(date) + " - " + dateToStr(endDate);
                        }
                    }
                ]
            },
            {
                name: "year",
                scale_height: 50,
                min_column_width: 30,

                scales: [
                    { unit: "year", step: 1, format: "%Y" }
                ]
            }
        ],
        useKey: "ctrlKey",
        trigger: "wheel",
        element: function() {
            return gantt.$root.querySelector(".gantt_task");
        }
    };

    gantt.ext.zoom.init(zoomConfig);
    gantt.ext.zoom.setLevel("quarter");

    // COLUMNS
    gantt.config.columns = [

        {
            name: "owner",
            width: 65,
            align: "center",
            resize: true,
            template: function(item) {
                return byId(gantt.serverList('staff'), item.owner_id)
            }
        },
        {
            name: "text",
            width: 350,
            tree: true,
            resize: true,
            template: function(task) {
                if (task.type == 'project') return task.text.toUpperCase();
                else
                if (task.progress == 1)
                    return "<span style='opacity: 0.5'>" + task.text.strike() + "</span>";
                else
                    return "<span>" + task.text + "</span>";

            }
        },
        { name: "start_date", align: "center", width: 200, resize: true },
        // { name: "duration", align: "center" },
        {
            name: "progress",
            label: "Progreso",
            align: "center",
            template: function(task) {
                return '<progress style=" width: 95%"   value="' + task.progress * 100 + '" max="100">' + task.progress * 100 + '%  </progress>';
            }
        },


        { name: "add", label: "", width: 44 }
    ];


    //  *** TEMPLATES ***

    // date format
    gantt.templates.tooltip_date_format = function(date) {
        var formatFunc = gantt.date.date_to_str(gantt.config.date_format);
        return formatFunc(date);
    };

    var hourToStr = gantt.date.date_to_str("%H:%i");
    var hourRangeFormat = function(step) {
        return function(date) {
            var intervalEnd = new Date(gantt.date.add(date, step, "hour") - 1)
            return hourToStr(date) + " - " + hourToStr(intervalEnd);
        };
    }

    // Tooltip
    gantt.templates.tooltip_text = function(start, end, task) {
        var progress = Math.floor(task.progress * 100);
        return "<b>Tarea:</b> " + task.text + '<br><br>' +
            '<div class="progress mt-0 mb-0" style="height: 30px; ">' +
            '<div class="progress-bar" role="progressbar" style="font-size:1.5rem; font-weight: bold;background-color:#F46120;  width: ' + progress + '%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">' + progress + '%</div> ' +
            '</div> ' +
            "<br/><b>Inicia:</b> " +
            gantt.templates.tooltip_date_format(start) +
            "<br/><b>Finaliza:</b> " + gantt.templates.tooltip_date_format(end);
    };

    // Icons
    // gantt.templates.grid_folder = function(item) { return "<div style='opacity: 0.3' class='gantt_tree_icon gantt_folder_" + (item.$open ? "open" : "open") + "'></div>"; };
    gantt.templates.grid_file = function(item) { return "<div style='opacity: 0.3' class='gantt_tree_icon gantt_file'></div>"; };

    // Task
    gantt.templates.task_class = function(start, end, task) {

        switch (task.priority) {
            case "1":
                return "high";
                break;
            case "2":
                return "medium";
                break;
            case "3":
                return "low";
                break;
                /* default:
                     return "low";
                     */

        }
    };



    // ***** PLUGINS *****
    gantt.plugins({
        undo: true,
        marker: true,
        auto_scheduling: true,
        critical_path: true,
        click_drag: true,
        keyboard_navigation: true,
        grouping: true,
        quick_info: false,
        tooltip: true
    });


    // LOAD Staff
    gantt.serverList("staff", [
        { key: 1, label: "Eric", backgroundColor: "#03A9F4", textColor: "#FFF" },
        { key: 2, label: "María", backgroundColor: "#f57730", textColor: "#FFF" },
        { key: 3, label: "Ana", backgroundColor: "#e157de", textColor: "#FFF" },
        { key: 4, label: "Antonio", backgroundColor: "#78909C", textColor: "#FFF" },
        { key: 7, label: "Jorge", backgroundColor: "#8D6E63", textColor: "#FFF" }
    ]);


    // *** EVENTS ***

    gantt.attachEvent("onAfterTaskUpdate", (id, item) => {
        var globalprogress = getGlobalProgress();
        updateGlobalGoalOverviewChart(globalprogress);

    });

    gantt.attachEvent("onLoadEnd", function() {
        gantt.showDate(new Date()); //
        var globalprogress = getGlobalProgress();
        updateGlobalGoalOverviewChart(globalprogress);

    });

    gantt.attachEvent("onAfterTaskAdd", function(id, item) {
        var globalprogress = getGlobalProgress();
        updateGlobalGoalOverviewChart(globalprogress);
    });

    gantt.attachEvent("onAfterTaskDelete", function(id, item) {
        var globalprogress = getGlobalProgress();
        updateGlobalGoalOverviewChart(globalprogress);
    });

    gantt.attachEvent("onTaskClick", function(id, e) {
        selected_task = id;
        return true;
    });


    // *** ACTION BUTTONS ***
    $('a[data-action="collapseAll"]').on('click', function() {
        gantt.close(gantt_root);
        gantt.batchUpdate(function() {
            gantt.eachTask(function(child) {
                gantt.close(child.id)
            }, gantt_root)
        });

    });

    $('a[data-action="expandAll"]').on('click', function() {
        gantt.open(gantt_root);
        gantt.batchUpdate(function() {
            gantt.eachTask(function(child) {
                gantt.open(child.id)
            }, gantt_root)
        });
    });

    $('a[data-action="undo"]').on('click', function() {
        gantt.undo();
    });

    $('a[data-action="redo"]').on('click', function() {
        gantt.redo();
    });

    $('a[data-action="redo"]').on('click', function() {
        gantt.redo();
    });

    $('a[data-action="zoomIn"]').on('click', function() {
        gantt.ext.zoom.zoomIn();
    });

    $('a[data-action="zoomOut"]').on('click', function() {
        gantt.ext.zoom.zoomOut();
    });

    $('a[data-action="toggleCriticalPath"]').on('click', function() {
        gantt.config.highlight_critical_path = !gantt.config.highlight_critical_path;
        if (gantt.config.highlight_critical_path)
            $('a[data-action="toggleCriticalPath"]').addClass('menu-item-active');
        else
            $('a[data-action="toggleCriticalPath"]').removeClass('menu-item-active');
        gantt.render();
    });

    $('a[data-action="toggleAutoScheduling"]').on('click', function() {
        gantt.config.auto_scheduling = !gantt.config.auto_scheduling;

        if (gantt.config.auto_scheduling)
            $('a[data-action="toggleAutoScheduling"]').addClass('menu-item-active');
        else
            $('a[data-action="toggleAutoScheduling"]').removeClass('menu-item-active');
    });



    // *** INITIALIZATION ***
    AddTodayMarker();
    // init
    gantt.init("whagonsGantt");
    gantt.load(apiURL + projectId);
    var dp = new gantt.dataProcessor("/api");
    dp.init(gantt);
    dp.setTransactionMode("REST");


    // *** CHARTS ***
    initializeCharts();

});


// ********************** FUNCTIONS ************************************ //

function initializeCharts() {
    drawGlobalGoalOverviewChart();

}


function AddTodayMarker() {
    var dateToStr = gantt.date.date_to_str(gantt.config.task_date);
    var today = new Date();
    gantt.addMarker({
        start_date: today,
        css: "today",
        text: "Hoy",
        title: "Hoy: " + dateToStr(today)
    });
}

function getGlobalProgress() {

    var tasks = gantt.getTaskByTime();
    var total_tasks = tasks.length;
    $('#totalactivities').html(total_tasks);
    if (total_tasks == 0) return 0;
    var pending = 0;
    var inprogress = 0;
    var completed = 0;

    var progress = 0;
    for (var i = 0; i < tasks.length; i++) {
        if (tasks[i].progress == 0) pending++;
        else if (tasks[i].progress == 1) ++completed;
        else ++inprogress;
        progress += tasks[i].progress;
    }
    $('#completedactivities').html(completed);
    $('#count-pending').html(pending);

    var pending_percent = Math.round((pending / total_tasks) * 100);
    $('#activities-pending').css('width', pending_percent.toString() + '%');
    $('#activities-pending').attr('title', pending_percent.toString() + '%');

    var inprogress_percent = Math.round((inprogress / total_tasks) * 100);
    $('#activities-inprogress').css('width', inprogress_percent.toString() + '%');
    $('#activities-inprogress').attr('title', inprogress_percent.toString() + '%');

    var completed_percent = Math.round((completed / total_tasks) * 100);
    $('#activities-completed').css('width', completed_percent.toString() + '%');
    $('#activities-completed').attr('title', completed_percent.toString() + '%');

    $('#count-inprogress').html(inprogress);
    $('#count-finished').html(completed);


    return Math.round((progress / total_tasks) * 100);

}

function byId(list, id) {
    for (var i = 0; i < list.length; i++) {
        if (list[i].key == id)
            return list[i].label || "";
    }
    return "";
}