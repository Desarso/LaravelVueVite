$(function() {


    var $primary = '#7367F0',
        $success = '#28C76F',
        $danger = '#EA5455',
        $warning = '#FF9F43',
        $info = '#00cfe8',
        $label_color_light = '#dae1e7';

    themeColors = [$primary, $success, $danger, $warning, $info];
    // RTL Support
    yaxis_opposite = false;
    if ($('html').data('textdirection') == 'rtl') {
        yaxis_opposite = true;
    }

    initDateRangePicker(7);
    initDropDownListStatus();
    initDropDownListTeam();
    initDropDownListSpotBranch();


    initTeamTasksChart();
    initTeamTaskStatusesChart();
    initTeamActivityChart();

    initGridTickets();

    getDataByTeam();


});


function initTeamActivityChart() {
    var chartOptions = {
        chart: {
            height: 350,
            type: 'area',
        },
        // colors: themeColors,
        colors: ['rgb(255, 69, 96)', '#28c76f', '#FFA54F', '#898b8e'],
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth'
        },
        series: [],
        /*series: [{
            name: 'series1',
            data: [31, 40, 28, 51, 42, 109, 100]
        }, {
            name: 'series2',
            data: [11, 32, 45, 32, 34, 52, 41]
        }], */
        legend: {
            offsetY: -10
        },
        xaxis: {
            type: 'datetime',
            categories: ["2019-09-18T00:00:00", "2019-09-18T01:00:00", "2019-09-18T02:00:00",
                "2019-09-18T03:00:00", "2019-09-18T04:00:00", "2019-09-18T05:00:00",
                "2019-09-18T06:00:00"
            ],
        },
        yaxis: {
            opposite: yaxis_opposite
        },
        tooltip: {
            x: {
                format: 'dd/MM/yy HH:mm'
            },
        }
    }
    teamActivityChart = new ApexCharts(
        document.querySelector("#team-activity-chart"),
        chartOptions
    );
    teamActivityChart.render();

}


function initTeamTaskStatusesChart() {
    var options = {
        series: [],
        chart: {
            type: 'bar',
            height: 300,
            stacked: true,
            toolbar: {
                show: true
            },
            zoom: {
                enabled: true
            }
        },
        colors: ['rgb(255, 69, 96)', '#28c76f', '#FFA54F', '#898b8e'],
        responsive: [{
            breakpoint: 480,
            options: {
                legend: {
                    position: 'bottom',
                    offsetX: -10,
                    offsetY: 0
                }
            }
        }],
        plotOptions: {
            bar: {
                horizontal: false,
                borderRadius: 10,
                columnWidth: '50%',
            },
        },
        xaxis: {
            //type: 'datetime',
            categories: [],
            /* categories: ['01/01/2011 GMT', '01/02/2011 GMT', '01/03/2011 GMT', '01/04/2011 GMT',
                 '01/05/2011 GMT', '01/06/2011 GMT'
             ],*/
        },
        legend: {
            position: 'right',
            offsetY: 40
        },
        fill: {
            opacity: 1
        }
    };

    teamChart = new ApexCharts(document.querySelector("#team-task-statuses-chart"), options);
    teamChart.render();


}

// colores de los equipos
function initTeamTasksChart() {
    var options = {
        // series: [44, 55, 13, 43, 22],
        series: [],
        chart: {
            toolbar: {
                show: true,


            },
            events: {
                click: function(event, chartContext, config) {
                    console.log('hola');
                    // The last parameter config contains additional information like `seriesIndex` and `dataPointIndex` for cartesian charts
                }
            },
            //  width: 380,
            type: 'pie',
            height: '340px',

        },
        legend: {
            show: true,
            position: 'left',
            fontSize: '16px',
            fontFamily: 'Montserrat, Arial',
            //horizontalAlign: 'center',
            floating: false,
        },


        labels: [],
        //labels: ['Team A', 'Team B', 'Team C', 'Team D', 'Team E'],
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {

                    width: 200
                },

                legend: {
                    position: 'bottom'
                }
            },

        }],

    };

    totalsChart = new ApexCharts(document.querySelector("#team-tasks-chart"), options);
    totalsChart.render();


}





function initDropDownListSpotBranch() {
    dropDownListSpot = $("#dropDownListSpot").kendoDropDownList({
        optionLabel: "-- Seleccione --",
        dataTextField: "text",
        dataValueField: "value",
        dataSource: getUserBranches(),
        filter: "contains",
        height: 400,
        change: changeFilter
    }).data("kendoDropDownList");
}

function initDropDownListStatus() {
    dropDownListStatus = $("#dropDownListStatus").kendoDropDownList({
        optionLabel: "-- Seleccione --",
        dataTextField: "text",
        dataValueField: "value",
        dataSource: window.global_statuses,
        filter: "contains",
        height: 400,
        change: changeFilter,
    }).data("kendoDropDownList");
}

function changeFilter() {
    gridTickets.dataSource.read();
    getDataByTeam();
}

function initGridTickets() {
    gridTickets = $("#gridTickets").kendoGrid({
        dataSource: {
            transport: {
                read: {
                    url: "getDataTeam",
                    type: "get",
                    dataType: "json",
                    data: function() {
                        return getFilters()
                    },
                },
            },
            pageSize: 20,
            schema: {
                model: {
                    id: "id",
                    fields: {
                        id: { type: "number", editable: false, nullable: true },
                    }
                }
            },
        },
        toolbar: ["excel"],
        pdf: {
            allPages: true,
            avoidLinks: true,
            paperSize: "A4",
            margin: { top: "2cm", left: "1cm", right: "1cm", bottom: "1cm" },
            landscape: true,
            repeatHeaders: true,
            scale: 0.8
        },
        editable: {
            mode: "popup"
        },
        height: "450px",
        groupable: false,
        reorderable: true,
        resizable: true,
        sortable: true,
        pageable: {
            refresh: true,
            pageSizes: true,
            buttonCount: 5,
        },
        filterable: true,
        noRecords: {
            template: "<div class='alert alert-danger mt-1 alert-validation-msg' role='alert'><i class='feather icon-info mr-1 align-middle'></i><span>No hay datos</span></div>"
        },
        dataBound: function() {
            var data = gridTickets.dataSource.data();
            $("#count-pendint").text(data.filter((item) => { return item.idstatus == 1; }).length);
            $("#count-progress").text(data.filter((item) => { return item.idstatus == 2; }).length);
            $("#count-paused").text(data.filter((item) => { return item.idstatus == 3; }).length);
            $("#count-finished").text(data.filter((item) => { return item.idstatus == 4; }).length);
        },
        columns: [{
                field: "code",
                title: "Cod",
                width: "25px",
                filterable: false
            },
            {
                field: "idstatus",
                title: "Estado",
                width: "60px",
                values: global_statuses,
                filterable: false
            },
            {
                field: "idspot",
                title: "Lugar",
                width: "80px",
                values: global_spots,
                filterable: false
            },
            {
                field: "iditem",
                title: "Tarea",
                width: "160px",
                template: function(dataItem) {
                    return global_items.find(o => o.id === parseInt(dataItem.iditem)).name;
                },
                filterable: false
            },
            {
                field: "idteam",
                title: "Equipo",
                width: "100px",
                values: global_teams,
                filterable: false
            },
            {
                field: "created_at",
                title: "Fecha",
                width: "100px",
                template: function(dataItem) {
                    return moment(dataItem.created_at).format('YYYY-MM-DD hh:mm A');
                },
                filterable: false
            },
        ],
    }).data("kendoGrid");
}

function getFilters() {
    return {
        start: $('#dateRangePicker').data('daterangepicker').startDate.format('YYYY-MM-DD'),
        end: $('#dateRangePicker').data('daterangepicker').endDate.format('YYYY-MM-DD'),
        idteam: dropDownListTeam.value(),
        idstatus: dropDownListStatus.value(),
        idspot: dropDownListSpot.value(),
        // iduser: dropDownListUser.value()
    };
}

function getDataByTeam() {
    let request = callAjax('getDataByTeam', 'POST', getFilters(), true);

    request.done(function(result) {

        teamChart.updateOptions({ xaxis: { categories: result.labels } });
        teamChart.updateSeries(result.series);


        totalsChart.updateOptions({ labels: result.labels });
        totalsChart.updateSeries(result.series_total);

        teamActivityChart.updateSeries(result.series);


    }).fail(function(jqXHR, status) {
        console.log('getDataByTeam failed!');
    });
}