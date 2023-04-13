// Goal Overview  Chart
// -----------------------------

var $primary = '#7367F0';
var $success = '#28C76F';
var $danger = '#EA5455';
var $warning = '#FF9F43';
var $info = '#00cfe8';
var $primary_light = '#A9A2F6';
var $danger_light = '#f29292';
var $success_light = '#55DD92';
var $warning_light = '#ffc085';
var $info_light = '#1fcadb';
var $strok_color = '#b9c3cd';
var $label_color = '#e7e7e7';
var $white = '#fff';

var themeColors = [$primary, $success, $danger, $warning, $info];
// RTL Support
var yaxis_opposite = false;
if ($('html').data('textdirection') == 'rtl') {
    yaxis_opposite = true;
}


function initCharts() {

}


/******************************************* EQUIPMENT GOAL OVERVIEW ***************************************************** */
var goalChartoptions = {
    chart: {
        animations: {
            enabled: true,
            easing: 'easeinout',
            speed: 1500,
            animateGradually: {
                enabled: true,
                delay: 1500
            },
            dynamicAnimation: {
                enabled: true,
                speed: 2000
            }
        },
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
    colors: [$success],
    plotOptions: {
        radialBar: {
            size: 110,
            startAngle: -150,
            endAngle: 150,
            hollow: {
                size: '77%',
            },
            track: {
                background: $strok_color,
                strokeWidth: '50%',
            },
            dataLabels: {
                name: {
                    show: false
                },
                value: {
                    offsetY: 18,
                    color: '#99a2ac',
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


var goalChart = new ApexCharts(
    document.querySelector("#goal-overview-chart"),
    goalChartoptions
);

function drawGoalOverviewChart() {
    goalChart.render();
}

function updateGoalOverviewChart(newValue) { // progress
    goalChart.updateSeries([newValue]);
}


/**************************************GLOBAL GOAL************************************************************************ */

var globalGoalChartoptions = {
    chart: {
        animations: {
            enabled: true,
            easing: 'easeinout',
            speed: 1500,
            animateGradually: {
                enabled: true,
                delay: 1500
            },
            dynamicAnimation: {
                enabled: true,
                speed: 2000
            }
        },
        height: 350,
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
    colors: [$success],
    plotOptions: {
        radialBar: {
            size: 110,
            startAngle: -150,
            endAngle: 150,
            dataLabels: {
                name: {
                    show: false
                },
                value: {
                    offsetY: 18,
                    color: '#99a2ac',
                    fontSize: '3rem'
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


var globalGoalChart = new ApexCharts(
    document.querySelector("#global-goal-overview-chart"),
    goalChartoptions
);

function drawGlobalGoalOverviewChart() {
    globalGoalChart.render();
}

function updateGlobalGoalOverviewChart(newValue) { // progres
    globalGoalChart.updateSeries([newValue]);
}



// Bar Chart Production
// ----------------------------------
var barChartProductionOptions = {
    chart: {
        animations: {
            enabled: true,
            easing: 'easeinout',
            speed: 800,
            animateGradually: {
                enabled: true,
                delay: 150
            },
            dynamicAnimation: {
                enabled: true,
                speed: 350
            }
        },
        height: 280,
        type: 'bar',

    },

    colors: themeColors,
    plotOptions: {
        bar: {
            horizontal: true,
        }
    },
    dataLabels: {
        enabled: false
    },
    series: [{
        // name: "Producción",
        data: [0],

    }],
    xaxis: {
        categories: ['Maisa-1'],
        tickAmount: 5
    },
    yaxis: {
        opposite: yaxis_opposite
    }
}

var barChartProduction = new ApexCharts(
    document.querySelector("#bar-chart-production"),
    barChartProductionOptions
);



function updateBarChartProduction(series, categories) {
    barChartProduction.updateSeries([{
        data: series // las cantidades
    }]);

    barChartProduction.updateOptions({ xaxis: { categories: categories } }, true, true);

}



function drawBarChartProduction() {
    barChartProduction.render();
}