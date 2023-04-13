$(document).ready(function() {
    initDateRangePicker();
    initDropDownListItem();
    initDropDownListTicketType();
    initDropDownListSpot();
    initTicketTypeChart();
    initItemColumnChart();
    initGridCleaningRequest();
    getDataCleaningTicketType();
});

function initDropDownListItem()
{
    dropDownListItem = $("#dropDownListItem").kendoDropDownList({
        optionLabel: "-- Seleccione --",
        dataTextField: "text",
        dataValueField: "value",
        filter: "contains",
        dataSource: {
          transport: { read: "getCleaningRequestItems" }
        },
        height: 400,
        change: changeFilter,
    }).data("kendoDropDownList");
}

function initDropDownListTicketType()
{
    dropDownListTicketType = $("#dropDownListTicketType").kendoDropDownList({
        optionLabel: "-- Seleccione --",
        dataTextField: "text",
        dataValueField: "value",
        filter: "contains",
        dataSource: {
          transport: { read: "getCleaningRequestTicketTypes" }
        },
        height: 400,
        change: changeFilter,
    }).data("kendoDropDownList");
}

function changeFilter()
{
  gridCleaningRequest.dataSource.read();
  getDataCleaningTicketType();
}

function initItemColumnChart()
{
    var options = {
      series: [],
      chart: {
        height: 300,
        type: 'bar',
      },
      plotOptions: {
        bar: {
          columnWidth: '50%',
          dataLabels: {
            position: 'top', // top, center, bottom
          },
        }
      },
      dataLabels: {
        enabled: true
      },
      stroke: {
        width: 2
      },
      grid: {
        row: {
          colors: ['#fff', '#f2f2f2']
        }
      },
      xaxis: {
        labels: {
          rotate: -45
        },
        categories: [],
        position: 'center',
        tickPlacement: 'on'
      },
      yaxis: {
        title: {
          text: 'Servings',
        },
      },
      fill: {
        type: 'gradient',
        gradient: {
          shade: 'light',
          type: "horizontal",
          shadeIntensity: 0.25,
          gradientToColors: undefined,
          inverseColors: true,
          opacityFrom: 0.85,
          opacityTo: 0.85,
          stops: [50, 0, 100]
        },
      }
      };

      itemChart = new ApexCharts(document.querySelector("#item-column-chart"), options);
      itemChart.render();
}

function initTicketTypeChart()
{
  var options = {
            series: [],
            chart: {
            width: 500,
            height: 315,
            type: 'pie',
        },
        labels: [],
        responsive: [{
            breakpoint: 480,
            options: {
            chart: {
                width: 200
            },
            legend: {
                position: 'bottom'
            }
            }
        }]
    };

  ticketTypeChart = new ApexCharts( document.querySelector("#ticket-type-chart"), options);
  ticketTypeChart.render();
}

function initGridCleaningRequest()
{
    gridCleaningRequest = $("#gridCleaningRequest").kendoGrid({
        dataSource: {
            transport: {
                read: {
                    url: "getDataCleaningRequest",
                    type: "get",
                    dataType: "json",
                    data: function() {
                        return {
                            start  : $('#dateRangePicker').data('daterangepicker').startDate.format('YYYY-MM-DD'),
                            end    : $('#dateRangePicker').data('daterangepicker').endDate.format('YYYY-MM-DD'),
                            iditem : dropDownListItem.value(),
                            idspot : dropDownListSpot.value(),
                            idtype : dropDownListTicketType.value()
                        };
                    },
                },
            },
            pageSize: 20,
            schema: {
                model: {
                    id: "id",
                    fields: {
                        id: { type: "number", editable: false, nullable: true },
                        total: { type: "number", editable: false, nullable: true },
                    }
                }
            },
            aggregate: [
                { field: "total_tickets", aggregate: "sum" }
            ]
        },
        editable: {
            mode: "popup"
        },
        height:"500px",
        reorderable: true,
        resizable: true,
        sortable: true,
        pageable: {
          refresh: true,
          pageSizes: true,
          buttonCount: 5,
        },
        noRecords: {
            template: "<div class='alert alert-danger mt-1 alert-validation-msg' role='alert'><i class='feather icon-info mr-1 align-middle'></i><span>No hay datos</span></div>"
        },
        filterable: true,
        dataBound: function(e) {

            let data = this.dataSource.data();

            let labels = data.map(function(obj) { return obj.name; });
            let series = data.map(function(obj) { return obj.total_quantity; });

            itemChart.updateOptions({ xaxis: { categories: labels } });
            itemChart.updateSeries([{ name: 'Cantidad', data: series }]);
        },
        columns: [
            {
                field: "name",
                title:  "Item",
                width: "150px",
                media: "(min-width: 450px)",
                filterable: false
            },
            {
              field: "total_tickets",
              title: "Solicitudes",
              width: "60px",
              aggregates: ["sum"],
              footerTemplate: "<strong> Total: #=sum# </strong>",
              filterable: false
            },
            {
                field: "total_quantity",
                title: "Cantidad",
                width: "60px",
                media: "(min-width: 450px)",
                //template: "<p class='font-large-1 text-bold-500'>#=total_quantity#</p>",
                filterable: false
            },
            {
                field: "average",
                title: "% Porcentaje",
                width: "200px",
                template: "#=formatAverage(average)#",
                media: "(min-width: 450px)",
                filterable: false
            } 
        ],
    }).data("kendoGrid");
}

function getDataCleaningTicketType()
{
  let data = {
    start  : $('#dateRangePicker').data('daterangepicker').startDate.format('YYYY-MM-DD'),
    end    : $('#dateRangePicker').data('daterangepicker').endDate.format('YYYY-MM-DD'),
    iditem : dropDownListItem.value(),
    idspot : dropDownListSpot.value(),
    idtype : dropDownListTicketType.value()
  };

    let request = callAjax('getDataCleaningTicketType', 'POST', data, true);

    request.done(function(result) {

      let labels = result.map(function(obj) { return obj.name; });
      let series = result.map(function(obj) { return obj.percent; });
      ticketTypeChart.updateOptions({ labels: labels, series: series });

    }).fail(function(jqXHR, status) {
        console.log('getDataCleaningTicketType failed!');
    });
}

function formatAverage(average)
{
    return "<div class='progress progress-bar-success progress-xl'>" +
                "<div class='progress-bar progress-bar-striped' role='progressbar' aria-valuenow='20' aria-valuemin='20' aria-valuemax='100' style='width:" + average + "%;'>" + average + "%</div>" +
            "</div>";
}