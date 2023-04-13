$(function() {
    initDateRangePicker();
    initDropDownListStatus();
    initDropDownListTeam();
    initDropDownListUser();
    initDropDownListSpotBranch();
    initGridTickets();
    initTeamChart();
    getDataByTeam();
});

function initTeamChart() {
    var options = {
        series: [],
        chart: {
            type: 'bar',
            height: 350
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '55%',
                //endingShape: 'rounded'
            },
        },
        //colors: ['rgb(255, 69, 96)', 'rgba(84, 110, 122, 0.85)'],
        colors: ['rgb(255, 69, 96)', '#28c76f', '#FFA54F', '#898b8e'],
        dataLabels: {
            enabled: false
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        xaxis: {
            categories: [],
        },
        yaxis: {
            title: {
                text: 'Tareas'
            }
        },
        fill: {
            opacity: 1
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return val + " tareas"
                }
            }
        }
    };

    teamChart = new ApexCharts(document.querySelector("#team-column-chart"), options);
    teamChart.render();
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
        toolbar: ["pdf"],
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
                template: function(dataItem) {
                    var spot = global_spots.find(function(e) { return e.value === dataItem.idspot; });

                    return spot.text + " <br><small style='color:lightgray'>" + spot.spotparent + "</small>";
                },
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
        iduser: dropDownListUser.value()
    };
}

function getDataByTeam() {
    let request = callAjax('getDataByTeam', 'POST', getFilters(), true);

    request.done(function(result) {

        teamChart.updateOptions({ xaxis: { categories: result.labels } });
        teamChart.updateSeries(result.series);

    }).fail(function(jqXHR, status) {
        console.log('getDataByTeam failed!');
    });
}