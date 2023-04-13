$(document).ready(function() {
    // alert("hola");
    initDateRangePicker();
    initGridAverageReport2();
});

function changeFilter()
{
    gridAverageReport.dataSource.read();
}

function initGridAverageReport2()
{
    gridAverageReport = $("#gridAverageReport").kendoGrid({
        dataSource: {
            transport: {
                read: {
                    url: "getDataAverageReport",
                    type: "get",
                    dataType: "json",
                    data: function() {
                        return {
                            start: $('#dateRangePicker').data('daterangepicker').startDate.format('YYYY-MM-DD'),
                            end: $('#dateRangePicker').data('daterangepicker').endDate.format('YYYY-MM-DD'),
                        };
                    },
                },
            },
            pageSize: 20,
            aggregate: [
                { field: "total",      aggregate: "sum" },
                { field: "pendint",  aggregate: "sum" },
                { field: "finish", aggregate: "sum" },
                { field: "ReIni",   aggregate: "sum" },
                { field: "IniFin", aggregate: "sum" },
                { field: "ReFin", aggregate: "sum" }
            ],
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
        height:"450px",
        reorderable: true,
        resizable: true,
        sortable: true,
        pageable: false,
        noRecords: {
            template: "<div class='alert alert-danger mt-1 alert-validation-msg' role='alert'><i class='feather icon-info mr-1 align-middle'></i><span>No hay datos</span></div>"
        },
        filterable: false,
        columns: [
            {
                field: "name",
                title:  "Spot",
                template: function(dataItem) {
                    return "<strong>" + dataItem.name + "</strong>";
                },
                width: "150px",
                filterable: false,
                locked: true,
            },
            {
                field: "total",
                title: "Total",
                width: "100px",
                locked: true,
                filterable: false,
                aggregates: ["sum"],
                footerTemplate: "<h3 style='text-align: center; font-weight:500; '> #: sum # </h3>",
            },
            {
                field: "pendint",
                title: "Pendientes",
                width: "150px",
                locked: true,
                filterable: false,
                aggregates: ["sum"],
                footerTemplate: "<h3 style='text-align: center; font-weight:500; '> #: sum # </h3>",
            },
            {
                field: "finish",
                title: "Finalizados",
                width: "150px",
                locked: true,
                filterable: false,
                footerTemplate: "<h3 style='text-align: center; font-weight:500; '> #: sum # </h3>",
            },
            {
                field: "ReIni",
                title: "Reportadas - Iniciadas",
                template: "#= formatDuration(ReIni)#",
                width: "200px",
                filterable: false,
                aggregates: ["sum"],
                footerTemplate: "<p style='text-align: center; font-weight:200; '> #: formatDuration(sum) # </p>",
            },
            {
                field: "IniFin",
                title: "Iniciadas - Finalizadas",
                template: "#= formatDuration(IniFin)#",
                width: "200px",
                filterable: false ,
                aggregates: ["sum"], 
                footerTemplate: "<p style='text-align: center; font-weight:200; '> #: formatDuration(sum) # </p>",
            },
            {
                field: "ReFin",
                title: "Reportadas - Finalizadas",
                template: "#= formatDuration(ReFin)#",
                width: "200px",
                filterable: false,
                aggregates: ["sum"],
                footerTemplate: "<p style='text-align: center; font-weight:200; '> #: formatDuration(sum) # </p>",
            },
        ]
    }).data("kendoGrid");
}

function pad(num) {
    return ("0"+num).slice(-2);
}




function formatDuration(seconds) {
    var days = Math.floor(seconds / (3600*24));
    seconds  -= days*3600*24;
    var hrs   = Math.floor(seconds / 3600);
    seconds  -= hrs*3600;
    var minutes = Math.floor(seconds / 60);
    seconds  -= minutes*60;


    return  days+" días "+pad(hrs)+" hrs "+pad(minutes)+ " min";
}


