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

/**************************************GLOBAL GOAL************************************************************************ */

var cleanChartoptions = {
    chart: {
      type: 'pie',
      height: 330,
      dropShadow: {
        enabled: false,
        blur: 5,
        left: 1,
        top: 1,
        opacity: 0.2
      },
      toolbar: {
        show: false
      }
    },
    labels: [],
    series: [],
    dataLabels: {
      enabled: false
    },
    legend: { show: false },
    stroke: {
      width: 5
    },
    colors: [],
    /*
    fill: {
      type: 'gradient',
      gradient: {
        gradientToColors: [$primary_light, $warning_light, $danger_light]
      }
    }
    */
  }

  var cleanChart = new ApexCharts(
    document.querySelector("#clean-chart"),
    cleanChartoptions
  );

function initCleanChart()
{
    cleanChart.render();
}

function refreshCleanChart(data)
{ 
    cleanChart.updateOptions(data);
}