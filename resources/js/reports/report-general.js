$(document).ready(function() {
    initDateRangePicker(30);

    initDropDownListStatus();
    initDropDownListTeam();
    initDropDownListSpot();
    initDropDownListItem();

    initEfficacyChart();
    initActivityChart();
    initSpotChart();
    initItemChart();

    getDataEfficacy();
    getActivityData();
    getDataActivityBySpot();
    getDataActivityByItem();
});

function initDropDownListStatus()
{
    dropDownListStatus = $("#dropDownListStatus").kendoDropDownList({
        optionLabel: "-- Seleccione --",
        dataTextField: "text",
        dataValueField: "value",
        dataSource: window.global_statuses,
        height: 400,
        change: changeFilter,
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
        height: 400,
        change: changeFilter,
    }).data("kendoDropDownList");
}

function changeFilter()
{
    getDataEfficacy();
    getActivityData();
    getDataActivityBySpot();
    getDataActivityByItem();
}

function getFilters()
{
    return {
        start    : $('#dateRangePicker').data('daterangepicker').startDate.format('YYYY-MM-DD'),
        end      : $('#dateRangePicker').data('daterangepicker').endDate.format('YYYY-MM-DD'),
        idteam   : dropDownListTeam.value(),
        idstatus : dropDownListStatus.value(),
        idspot   : dropDownListSpot.value(),
        iditem   : dropDownListItem.value()
    };
}

function initEfficacyChart()
{
    var efficacyChartoptions = {
        chart: {
          height: 250,
          type: 'radialBar',
          sparkline: {
              enabled: true,
          },
          dropShadow: {
              enabled: true,
              blur: 3,
              left: 1,
              top: 1,
              opacity: 0.1
          },
        },
        colors: ['#00db89'],
        plotOptions: {
            radialBar: {
                size: 110,
                startAngle: -150,
                endAngle: 150,
                hollow: {
                    size: '77%',
                },
                track: {
                    background: '#b9c3cd',
                    strokeWidth: '50%',
                },
                dataLabels: {
                    name: {
                        show: false
                    },
                    value: {
                        offsetY: 18,
                        color: '#b9c3cd',
                        fontSize: '4rem'
                    }
                }
            }
        },
        fill: {
            type: 'gradient',
            gradient: {
                shade: 'dark',
                type: 'horizontal',
                shadeIntensity: 0.5,
                gradientToColors: ['#00b5b5'],
                inverseColors: true,
                opacityFrom: 1,
                opacityTo: 1,
                stops: [0, 100]
            },
        },
        series: [0],
        stroke: {
          lineCap: 'round'
        },
  
      }
  
      efficacyChart = new ApexCharts(
        document.querySelector("#efficacy-chart"),
        efficacyChartoptions
      );
  
      efficacyChart.render();   
}

function initActivityChart()
{
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
        legend: {
            offsetY: -10
        },
        xaxis: {
            type: 'datetime',
            categories: [],
        },
        yaxis: {
            opposite: true
        },
        tooltip: {
            x: {
                format: 'dd/MM/yy'
            },
        }
    }

    ActivityChart = new ApexCharts( document.querySelector("#activity-chart"), chartOptions );
    ActivityChart.render();
}

function initSpotChart()
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

    spotChart = new ApexCharts(document.querySelector("#spot-chart"), options);
    spotChart.render();
}

function initItemChart()
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

    itemChart = new ApexCharts(document.querySelector("#item-chart"), options);
    itemChart.render();
}

function getDataEfficacy()
{
    let request = callAjax('getDataEfficacy', 'POST', getFilters(), false);

    request.done(function(result) {

        efficacyChart.updateSeries([result.efficacy]);
        $("#task-total").text(result.total);
        $("#task-finished").text(result.finished);

    }).fail(function(jqXHR, status) {
        console.error('error');
    });
}

function getActivityData()
{
    let request = callAjax('getActivityByDates', 'POST', getFilters(), true);

    request.done(function(result) {

        ActivityChart.updateOptions({ xaxis: { categories: result.categories } });
        ActivityChart.updateSeries(result.series);


    }).fail(function(jqXHR, status) {
        console.log('getDataByTeam failed!');
    });
}

function getDataActivityBySpot()
{
    let request = callAjax('getDataActivityBySpot', 'POST', getFilters(), false);

    request.done(function(result) {

        spotChart.updateOptions({ xaxis: { categories: result.labels } });
        spotChart.updateSeries(result.series);

    }).fail(function(jqXHR, status) {
        console.error('error');
    });
}

function getDataActivityByItem()
{
    let request = callAjax('getDataActivityByItem', 'POST', getFilters(), false);

    request.done(function(result) {

        itemChart.updateOptions({ xaxis: { categories: result.labels } });
        itemChart.updateSeries(result.series);

    }).fail(function(jqXHR, status) {
        console.error('error');
    });
}