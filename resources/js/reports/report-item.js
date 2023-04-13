$(document).ready(function() {
    initDateRangePicker();
    initDropDownListTeam();
    initDropDownListSpotBranch();
    
    initChartFrequentItems();
    initChartFrequentByclient();
    initChartTaskBySpot();
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


function changeFilter()
{
    chartItems.dataSource.read();
    chartByclient.dataSource.read();
    chartTaskBySpot.dataSource.read();
}

function initChartFrequentItems()
{
    chartItems = $("#chart-frequent-items").kendoChart({
        dataSource: {
            transport: {
                read: {
                    url: "getFrequenteItemsReport",
                    dataType: "json",
                    data: function() {
                        return {
                            start    : $('#dateRangePicker').data('daterangepicker').startDate.format('YYYY-MM-DD'),
                            end      : $('#dateRangePicker').data('daterangepicker').endDate.format('YYYY-MM-DD'),
                            idspot : dropDownListSpot.value(),
                            idteam   : dropDownListTeam.value(),
                            byclient : 0
                        };
                    }
                }
            },
        },
        title: {
            text: "Tareas más frecuentes"
        },
        legend: {
            position: "top"
        },
        seriesDefaults: {
            type: "column"
        },
        series:[{
            labels: {
                visible: true,
                position: "center",
                background: "transparent",
                color: "white",
                font: "13px sans-serif",
                //format: "{0}%"
            },
            field: "total",
            categoryField: "name",
            name: "Item",
            color: "#5867dd"
        }],
        categoryAxis: {
            labels: {
                //rotation: -90
            },
            majorGridLines: {
                visible: false
            }
        },
        valueAxis: {
            labels: {
                //format: "N0"
            },
            line: {
                visible: false
            }
        },
        tooltip: {
            visible: true,
            format: "N0"
        }
    }).data("kendoChart");
}


function initChartFrequentByclient()
{
    chartByclient = $("#chart-frequent-byclient").kendoChart({
        dataSource: {
            transport: {
                read: {
                    url: "getFrequenteItemsReport",
                    dataType: "json",
                    data: function() {
                        return {
                            start    : $('#dateRangePicker').data('daterangepicker').startDate.format('YYYY-MM-DD'),
                            end      : $('#dateRangePicker').data('daterangepicker').endDate.format('YYYY-MM-DD'),
                            idspot : dropDownListSpot.value(),
                            idteam   : dropDownListTeam.value(),
                            byclient : 1
                        };
                    }
                }
            },
        },
        title: {
            text: "Tareas más frecuentes"
        },
        legend: {
            position: "top"
        },
        seriesDefaults: {
            type: "column"
        },
        series:[{
            labels: {
                visible: true,
                position: "center",
                background: "transparent",
                color: "white",
                font: "13px sans-serif",
                //format: "{0}%"
            },
            field: "total",
            categoryField: "name",
            name: "Item",
            color: "#5867dd"
        }],
        categoryAxis: {
            labels: {
                //rotation: -90
            },
            majorGridLines: {
                visible: false
            }
        },
        valueAxis: {
            labels: {
                //format: "N0"
            },
            line: {
                visible: false
            }
        },
        tooltip: {
            visible: true,
            format: "N0"
        }
    }).data("kendoChart");
}

function initChartTaskBySpot()
{
    chartTaskBySpot = $("#chart-task-spot").kendoChart({
        dataSource: {
            transport: {
                read: {
                    url: "getDataTaskBySporReport",
                    dataType: "json",
                    data: function() {
                        return {
                            start    : $('#dateRangePicker').data('daterangepicker').startDate.format('YYYY-MM-DD'),
                            end      : $('#dateRangePicker').data('daterangepicker').endDate.format('YYYY-MM-DD'),
                            idspot : dropDownListSpot.value(),
                            idteam   : dropDownListTeam.value(),
                        };
                    }
                }
            },
        },
        title: {
            text: "Tareas más frecuentes"
        },
        legend: {
            position: "top"
        },
        seriesDefaults: {
            type: "column"
        },
        series:[{
            labels: {
                visible: true,
                position: "center",
                background: "transparent",
                color: "white",
                font: "13px sans-serif",
                //format: "{0}%"
            },
            field: "total",
            categoryField: "name",
            name: "Item",
            color: "#5867dd"
        }],
        categoryAxis: {
            labels: {
                //rotation: -90
            },
            majorGridLines: {
                visible: false
            }
        },
        valueAxis: {
            labels: {
                //format: "N0"
            },
            line: {
                visible: false
            }
        },
        tooltip: {
            visible: true,
            format: "N0"
        }
    }).data("kendoChart");
}