$(document).ready(function () {
    initDateRangePicker();
    initDropDownListTeam();
    initDropDownListUser();
    initDropDownListItem();
    initDropDownListSpotBranch();

    initchartBarsTeams();
    initChartPieTeams();
    getTeamSummaryByStatus();
    getTeamSummary();

    initGridTeamsSummary();
    initGridTeamUserSummary();
});

function initDropDownListUser()
{
    dropDownListUser = $("#dropDownListUser").kendoDropDownList({
        optionLabel: "-- Seleccione --",
        dataTextField: "name",
        dataValueField: "id",
        dataSource: window.global_users,
        height: 400,
        change: changeFilter
    }).data("kendoDropDownList");
}

function initDropDownListItem()
{
    dropDownListItem = $("#dropDownListItem").kendoDropDownList({
        optionLabel: "-- Seleccione --",
        dataTextField: "name",
        dataValueField: "id",
        dataSource: window.global_items,
        filter: "contains",
        height: 400,
        change: changeFilter
    }).data("kendoDropDownList");
}

function initDropDownListSpotBranch()
{
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

function changeFilter()
{
    gridTeamsSummary.dataSource.read();
    gridTeamUserSummary.dataSource.read();

    getTeamSummaryByStatus();
    getTeamSummary();
}

function initchartBarsTeams()
{
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
        colors: ['rgb(255, 69, 96)', '#898b8e'],
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
            categories: [],
        },
        legend: {
            position: 'right',
            offsetY: 40
        },
        fill: {
            opacity: 1
        }
    };

    chartBarsTeams = new ApexCharts(document.querySelector("#chart-bars-teams-summary"), options);
    chartBarsTeams.render();
}

function initChartPieTeams()
{
    var options = {
        series: [],
        chart: {
            toolbar: {
                show: true,
            },
            events: {
                legendClick: function(chartContext, seriesIndex, config) {
                    console.log('pie clicked');
                    // console.log(config.w.config.series[config.dataPointIndex]);
                    console.log(seriesIndex); 
                }
            },
            type: 'pie',
            height: '340px',
        },
        legend: {
            position: 'right',
            offsetY: 40
        },
        labels: [],
        colors: [],
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    width: 190
                },

                legend: {
                    position: 'bottom'
                }
            },
        }],
    };

    chartPieTeams = new ApexCharts(document.querySelector("#chart-pie-teams-summary"), options);
    chartPieTeams.render();
}

function getTeamSummaryByStatus()
{
    let request = callAjax('getTeamSummaryByStatus', 'GET', getFilters() , true);

    request.done(function(result) {

        chartBarsTeams.updateOptions({ xaxis: { categories: result.labels } });
        chartBarsTeams.updateSeries(result.series);

    }).fail(function(jqXHR, status) {
        console.log('getTeamSummaryByStatus failed!');
    });
}

function getTeamSummary()
{
    let request = callAjax('getTeamSummary', 'GET', getFilters() , true);

    request.done(function(result) {

        chartPieTeams.updateSeries(result.series);
        chartPieTeams.updateOptions({ 
            labels: result.labels, 
            colors: result.colors 
        });

    }).fail(function(jqXHR, status) {
        console.log('getTeamSummary failed!');
    });
}

function initGridTeamsSummary()
{
    gridTeamsSummary = $("#gridTeamsSummary").kendoGrid({
        dataSource: {
            transport: {
                read: {
                    type: 'get',
                    url: "getDataTeamsSummary",
                    dataType: 'json',
                    data: function () {
                        return getFilters();
                    }
                }
            },
            pageSize: 20,
        },
        height: 500,
        sortable: true,
        filterable: false,
        groupable: false,
        resizable: true,
        reorderable: true,
        selectable: true,
        pageable: {
            refresh: true,
            pageSizes: true,
            buttonCount: 5,
        },
        noRecords: {
            template: "No hay datos disponibles"
        },
        change: function(e) {
            let selectedRow = this.dataItem(this.select());
            dropDownListTeam.value(selectedRow.idteam);
            dropDownListTeam.trigger("change");
        },
        columns:
            [
                {
                    field: "idteam",
                    title: "Equipos",
                    values: window.global_teams,
                    width: "200px",
                    filterable: false,
                },
                {
                    field: "quantity",
                    title: "Tareas",
                    width: "60px",
                    filterable: false,
                },
                {
                    field: "noAssign",
                    title: "Sin asignar",
                    width: "60px",
                    filterable: false,
                },
                {
                    field: "summary",
                    title: "Cumplimiento",
                    template: "#=formatPercent(summary)#",
                    width: "100px",
                    filterable: false,
                }
            ]
    }).data("kendoGrid");
}

function initGridTeamUserSummary()
{
    gridTeamUserSummary = $("#gridTeamUserSummary").kendoGrid({
        dataSource: {
            transport: {
                read: {
                    type: 'get',
                    url: "getDataTeamUserSummary",
                    dataType: 'json',
                    data: function () {
                        return getFilters();
                    }
                }
            },
            pageSize: 20,
        },
        height: 500, 
        sortable: true,
        filterable: false,
        groupable: false,
        resizable: true,
        reorderable: true,
        selectable: false,
        pageable: {
            refresh: true,
            pageSizes: true,
            buttonCount: 5,
        },
        noRecords: {
            template: "No hay datos disponibles"
        },
        columns:
            [
                {
                    field: "iduser",
                    title: "Usuario",
                    values: window.global_users,
                    width: "200px",
                    filterable: false,
                },
                {
                    field: "quantity",
                    title: "Tareas",
                    width: "60px",
                    filterable: false,
                },
                {
                    field: "summary",
                    title: "Cumplimiento",
                    width: "100px",
                    template: "#=formatPercent(summary)#",
                    filterable: false,
                }
            ]
    }).data("kendoGrid");
}

function getFilters()
{
    return {
        start  : $('#dateRangePicker').data('daterangepicker').startDate.format('YYYY-MM-DD'),
        end    : $('#dateRangePicker').data('daterangepicker').endDate.format('YYYY-MM-DD'),
        iduser : dropDownListUser.value(),
        idteam : dropDownListTeam.value(),
        iditem : dropDownListItem.value(),
        idspot : dropDownListSpot.value()
    };
}

function formatPercent(percent)
{
    return "<div class='progress progress-bar-" + getColor(percent) + " progress-xl'>" +
                "<div class='progress-bar progress-bar-striped' role='progressbar' aria-valuenow='20' aria-valuemin='20' aria-valuemax='100' style='width:" + percent + "%;'>" + percent + "%</div>" +
           "</div>";
}

function getColor(average)
{
    if(average >= 85)
    {
        return "success"
    }
    else if(average < 85 && average >= 70)
    {
        return "warning";
    }
    else
    {
        return "danger";
    }
}