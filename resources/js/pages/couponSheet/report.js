function initGridReport()
{
    gridReport = $("#grid-report-coupon").kendoGrid({
        dataSource: {
            transport: {
                read: {
                    url: "getCouponDeficit",
                    type: "get",
                    dataType: "json",
                    data: function() {
                        return {

                        };
                    },
                },
            },
            pageSize: 100,
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
        excel: {
            fileName: "Reporte deficit de cupones.xlsx",
            filterable: false,
            allPages: true
        },
        dataBound: function(e) {

        },
        editable: {
            mode: "popup"
        },
        height: "380px",
        groupable: false,
        reorderable: false,
        resizable: true,
        sortable: true,
        pageable: false,
        filterable: false,
        noRecords: {
            template: "<div class='alert alert-danger mt-1 alert-validation-msg' role='alert'><i class='feather icon-info mr-1 align-middle'></i><span>No hay datos</span></div>"
        },
        columns: [
            {
                field: "barcode",
                title: "Código",
                width: "50px",
                filterable: false,
            },
            {
                field: "count",
                title: "Cantidad",
                width: "50px",
                filterable: false,
            },
            {
                field: "description",
                title: "Medicamento",
                width: "250px",
                filterable: false,
            },
        ],
    }).data("kendoGrid");
}

function initCouponChart()
{
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
                dataLabels: {
                    position: 'top', // top, center, bottom
                },
                //endingShape: 'rounded'
            },
        },
        //colors: ['rgb(255, 69, 96)', 'rgba(84, 110, 122, 0.85)'],
        colors: ['rgb(255, 69, 96)', '#28c76f', '#FFA54F', '#898b8e'],
        dataLabels: {
            enabled: true
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
                text: 'Cupones'
            }
        },
        fill: {
            opacity: 1
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return val + " cupones"
                }
            }
        }
    };

    couponChart = new ApexCharts(document.querySelector("#coupon-chart"), options);
    couponChart.render();
}

function getDataScannedCoupons()
{
    let request = callAjax('getDataScannedCoupons', 'GET', {}, false);

    request.done(function(result) {

        couponChart.updateOptions({ xaxis: { categories: result.labels } });
        couponChart.updateSeries(result.series);

    }).fail(function(jqXHR, status) {
        console.log('getDataScannedCoupons failed!');
    });
}

$("#btn-export-excel").click(function () {
    exportCouponsToExcel();
});

function exportCouponsToExcel()
{
    let request = callAjax("exportCouponsToExcel", 'GET', {}, true);

    request.done(function(response, textStatus, request) {
        var a = document.createElement("a");
        a.href = response.file;
        a.download = response.name;
        document.body.appendChild(a);
        a.click();
        a.remove();
        $.unblockUI();
    }).fail(function(jqXHR, status) {
        $.unblockUI();
    });
}