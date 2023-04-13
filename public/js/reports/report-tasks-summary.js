$(document).ready(function () {
    initDateRangePicker();
    initDropDownListSpotBranch();
    initDropDownListTeam();
    initDropDownListTicketType();
    initDropDownListItem();

    initChartBarsTickets();
    initChartPieTickets();
    getTicketSummaryByMonth();
    getTicketSummary();

    initGridSpotSummary();
    initGridItemSummary();
    initGridTicketTypeSummary();
});

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

function initDropDownListTicketType()
{
    dropDownListTicketType = $("#dropDownListTicketType").kendoDropDownList({
        optionLabel: "-- Seleccione --",
        dataTextField: "text",
        dataValueField: "value",
        dataSource: window.global_ticket_types,
        filter: "contains",
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

function changeFilter()
{
    gridItemSummary.dataSource.read();
    gridSpotSummary.dataSource.read();
    gridTicketTypeSummary.dataSource.read();

    getTicketSummaryByMonth();
    getTicketSummary();
}

function initChartBarsTickets()
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

    chartBarsTickets = new ApexCharts(document.querySelector("#chart-bars-tickets-summary"), options);
    chartBarsTickets.render();
}

function initChartPieTickets()
{
    var options = {
        series: [],
        chart: {
            toolbar: {
                show: true,
            },
            events: {
                click: function(event, chartContext, config) {
                    console.log('pie clicked');
                    // The last parameter config contains additional information like `seriesIndex` and `dataPointIndex` for cartesian charts
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
        labels: ['Pendientes', 'Finalizadas'],
        colors: ['rgb(255, 69, 96)', '#898b8e'],
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

    chartPieTickets = new ApexCharts(document.querySelector("#chart-pie-tickets-summary"), options);
    chartPieTickets.render();
}

function getTicketSummaryByMonth()
{
    let request = callAjax('getTicketSummaryByMonth', 'GET', getFilters() , true);

    request.done(function(result) {

        chartBarsTickets.updateOptions({ xaxis: { categories: result.labels } });
        chartBarsTickets.updateSeries(result.series);

    }).fail(function(jqXHR, status) {
        console.log('getTicketSummaryByMonth failed!');
    });
}

function getTicketSummary()
{
    let request = callAjax('getTicketSummary', 'GET', getFilters() , true);

    request.done(function(result) {

        chartPieTickets.updateSeries(result.series);

    }).fail(function(jqXHR, status) {
        console.log('getTicketSummary failed!');
    });
}

function initGridSpotSummary()
{
    gridSpotSummary = $("#gridSpotSummary").kendoGrid({
        dataSource: {
            transport: {
                read: {
                    type: 'get',
                    url: "getDataSpotSummary",
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
            dropDownListSpot.value(selectedRow.idspot);
            dropDownListSpot.trigger("change");
        },
        columns:
            [
                {
                    field: "idspot",
                    title: "Sede",
                    values: window.global_spots,
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
                    template: "#=formatPercent(summary)#",
                    width: "100px",
                    filterable: false,
                }
            ]
    }).data("kendoGrid");
}

function initGridItemSummary()
{
    gridItemSummary = $("#gridItemSummary").kendoGrid({
        dataSource: {
            transport: {
                read: {
                    type: 'get',
                    url: "getDataItemSummary",
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
                    field: "iditem",
                    title: "Ítem",
                    template: function(dataItem) {
                        let item = global_items.find((item) => { return item.id == dataItem.iditem; });
                        return item.name;
                    },
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
                    template: "#=formatPercent(summary)#",
                    width: "100px",
                    filterable: false,
                }
            ]
    }).data("kendoGrid");
}

function initGridTicketTypeSummary()
{
    gridTicketTypeSummary = $("#gridTicketTypeSummary").kendoGrid({
        dataSource: {
            transport: {
                read: {
                    type: 'get',
                    url: "getDataTicketTypeSummary",
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
            dropDownListTicketType.value(selectedRow.idtype);
            dropDownListTicketType.trigger("change");
        },
        columns:
            [
                {
                    field: "idtype",
                    title: "Tipo Tarea",
                    values: window.global_ticket_types,
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
        idspot : dropDownListSpot.value(),
        idteam : dropDownListTeam.value(),
        idtype : dropDownListTicketType.value(),
        iditem : dropDownListItem.value()
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