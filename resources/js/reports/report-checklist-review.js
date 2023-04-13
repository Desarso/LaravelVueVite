window.idticketchecklist = null;

$(document).ready(function () {
    initDateRangePicker();
    initDropDownListChecklist();

    initChartBarsChecklistReview();
    getDataChecklistReviewBySection();

    initGridChecklistReview();
    initGridChecklistReviewByOption();
});

function initDropDownListChecklist()
{
    dropDownListChecklist = $("#dropDownListChecklist").kendoDropDownList({
        dataTextField: "text",
        dataValueField: "value",
        dataSource: window.global_checklist.filter(checklist => (checklist.type == 1)),
        filter: "contains",
        height: 400,
        change: changeFilter
    }).data("kendoDropDownList");
}

function changeFilter()
{
    gridChecklistReview.dataSource.read();
    gridChecklistReviewByOption.dataSource.read();

    getDataChecklistReviewBySection();
}

function initChartBarsChecklistReview()
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
        colors: ['rgba(0, 143, 251, 0.85)'],
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
        dataLabels: {
            enabled: true,
            formatter: function (val) {
              return val + "%";
            },
            offsetY: -20,
            style: {
              fontSize: '13px',
            }
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

    chartBarsChecklistReview = new ApexCharts(document.querySelector("#chart-bars-checklist-review"), options);
    chartBarsChecklistReview.render();
}


function getDataChecklistReviewBySection()
{
    let request = callAjax('getDataChecklistReviewBySection', 'GET', getFilters() , true);

    request.done(function(result) {

        chartBarsChecklistReview.updateOptions({ xaxis: { categories: result.labels } });
        chartBarsChecklistReview.updateSeries(result.series);

    }).fail(function(jqXHR, status) {
        console.log('getDataChecklistReviewBySection failed!');
    });
}

function initGridChecklistReview()
{
    gridChecklistReview = $("#gridChecklistReview").kendoGrid({
        dataSource: {
            transport: {
                read: {
                    type: 'get',
                    url: "getDataChecklistReview",
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
            template: "<div class='alert alert-danger mt-1 alert-validation-msg' role='alert'><i class='feather icon-info mr-1 align-middle'></i><span>No hay datos</span></div>"
        },
        change: function(e) {

            let selectedRow = this.dataItem(this.select());

            let idselected = (selectedRow == null ? null : selectedRow.id);

            let result = (window.idticketchecklist != idselected)

            window.idticketchecklist = idselected;

            if(result) changeFilter();
        },
        dataBound: function(e) {
            var grid = this;
            var rows = grid.items();

            if(window.idticketchecklist != null && rows.length == 1) grid.select(rows[0]);

        },
        columns:
            [
                { selectable: true, width: "20px" },
                {
                    field: "created_by",
                    title: "Usuario",
                    values: window.global_users,
                    width: "150px",
                    filterable: false,
                },
                {
                    field: "created_at",
                    title: "Fecha",
                    width: "80px",
                    template:function(dataItem) {
                        return moment(dataItem.created_at).format('YY-MM-DD HH:mm');
                    },
                    filterable: false
                },
                {
                    field: "percentage",
                    title: "Cumplimiento",
                    template: "#=formatPercent(percentage)#",
                    width: "100px",
                    filterable: false,
                },
                { command: { text: "Tarea", click: showTicket }, title: " ", width: "40px" }
            ]
    }).data("kendoGrid");
}

function initGridChecklistReviewByOption()
{
    gridChecklistReviewByOption = $("#gridChecklistReviewByOption").kendoGrid({
        dataSource: {
            transport: {
                read: {
                    type: 'get',
                    url: "getDataChecklistReviewByOption",
                    dataType: 'json',
                    data: function () {
                        return getFilters();
                    }
                }
            },
            group: { field: "section", aggregates: [{ field: "percentage", aggregate: "average" }] },
        },
        height: 500,
        sortable: true,
        filterable: false,
        groupable: false,
        resizable: true,
        reorderable: true,
        selectable: false,
        pageable: false,
        noRecords: {
            template: "<div class='alert alert-danger mt-1 alert-validation-msg' role='alert'><i class='feather icon-info mr-1 align-middle'></i><span>No hay datos</span></div>"
        },
        columns:
            [
                {
                    field: "section",
                    title: "Sección",
                    width: "200px",
                    hidden: true,
                    filterable: false,
                    groupHeaderTemplate: function(data) {
                        return "<b>" + data.items[0].section + "</b>";
                    },
                },
                {
                    field: "name",
                    title: "Ítem",
                    width: "200px",
                    filterable: false,
                },
                {
                    field: "percentage",
                    title: "Cumplimiento",
                    template: "#=formatPercent(percentage)#",
                    width: "100px",
                    groupFooterTemplate: "<b>#=Math.round(average)#%</b>",
                    filterable: false,
                }
            ]
    }).data("kendoGrid");
}

function getFilters()
{
    return {
        start       : $('#dateRangePicker').data('daterangepicker').startDate.format('YYYY-MM-DD'),
        end         : $('#dateRangePicker').data('daterangepicker').endDate.format('YYYY-MM-DD'),
        idchecklist : dropDownListChecklist.value(),
        id          : window.idticketchecklist
    };
}

function showTicket(e)
{
    e.preventDefault();

    var dataItem = this.dataItem($(e.currentTarget).closest("tr"));

    kendo.confirm("¿Ver tarea en el dashboard?")
    .done(function(){
        let url = "dashboard-tasks?tickets=" + dataItem.idticket;
        window.open(url, '_blank');
    })
    .fail(function(){

    });
}

function formatPercent(percent)
{
    let color = getColor(percent);

    return "<div class='progress progress-bar-primary progress-xl'>" +
                "<div class='progress-bar progress-bar-striped' role='progressbar' aria-valuenow='20' aria-valuemin='20' aria-valuemax='100' style='background-color:"+ color +"; width:" + percent + "%;'>" + percent + "%</div>" +
           "</div>";
}

function getColor(percent)
{
    let color = "";

    switch (true)
    {
        case percent <= 25:
            color = "#ff6464";
            break;

        case (percent > 25 && percent <= 50):
            color = "#f59f00";
            break;
            
        case (percent > 50 && percent <= 75):
            color = "#e1d40b";
            break;

        case percent > 75:
            color = "#28c76f";
            break;
    }

    return color;
}