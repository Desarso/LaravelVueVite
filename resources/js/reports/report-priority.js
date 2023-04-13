$(document).ready(function() {
    initDateRangePicker();
    initDropDownListUser();
    initDropDownListPriority();
    initDropDownListStatus();
    initDropDownListTeam();
    initChartEfficacy();
    initGridPriority();
    initGridTicketPriority();
    initGridUserPriority();
    getEfficiencyPriority();
});

function initDropDownListStatus() {
    dropDownListStatus = $("#dropDownListStatus").kendoDropDownList({
        optionLabel: "-- Seleccione --",
        dataTextField: "text",
        dataValueField: "value",
        dataSource: window.global_statuses,
        height: 400,
        change: changeFilter,
    }).data("kendoDropDownList");
}

function changeFilter() {
    gridPriority.dataSource.read();
    gridTicketPriority.dataSource.read();
    gridUserPriority.dataSource.read();
    getEfficiencyPriority();
}

function initChartEfficacy() {
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

function initGridPriority() {
    gridPriority = $("#gridPriority").kendoGrid({
        dataSource: {
            transport: {
                read: {
                    url: "getDataPriority",
                    type: "get",
                    dataType: "json",
                    data: function() {
                        return {
                            start: $('#dateRangePicker').data('daterangepicker').startDate.format('YYYY-MM-DD'),
                            end: $('#dateRangePicker').data('daterangepicker').endDate.format('YYYY-MM-DD'),
                            idpriority: dropDownListPriority.value(),
                            idstatus: dropDownListStatus.value(),
                            idteam: dropDownListTeam.value(),
                            iduser: dropDownListUser.value()
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
                { field: "total", aggregate: "sum" },
                { field: "delayed", aggregate: "sum" },
                { field: "postponed", aggregate: "sum" },
                { field: "percent", aggregate: "average" },
            ]
        },
        editable: {
            mode: "popup"
        },
        height: "250px",
        reorderable: true,
        resizable: true,
        sortable: true,
        pageable: false,
        noRecords: {
            template: "<div class='alert alert-danger mt-1 alert-validation-msg' role='alert'><i class='feather icon-info mr-1 align-middle'></i><span>No hay datos</span></div>"
        },
        filterable: true,
        columns: [{
                field: "idpriority",
                title: "Prioridad",
                values: window.global_priorities,
                width: "150px",
                media: "(min-width: 450px)",
                filterable: false
            },
            {
                field: "total",
                title: "Total",
                width: "60px",
                media: "(min-width: 450px)",
                aggregates: ["sum"],
                footerTemplate: "<strong> #=sum# </strong>",
                filterable: false
            },
            {
                field: "delayed",
                title: "Retrasadas",
                width: "60px",
                media: "(min-width: 450px)",
                aggregates: ["sum"],
                footerTemplate: "<strong> #=sum# </strong>",
                filterable: false
            },
            {
                field: "postponed",
                title: "Pospuestas",
                width: "60px",
                aggregates: ["sum"],
                footerTemplate: "<strong> #=sum# </strong>",
                filterable: false
            },
            {
                field: "percent",
                title: "% de retraso",
                width: "200px",
                template: "#=formatPercent(percent)#",
                media: "(min-width: 450px)",
                //aggregates: ["average"],
                //footerTemplate: "<strong> #=average# %</strong>",
                filterable: false
            }
        ],
    }).data("kendoGrid");
}

function initGridTicketPriority() {
    gridTicketPriority = $("#gridTicketPriority").kendoGrid({
        dataSource: {
            transport: {
                read: {
                    url: "getDataTicketPriority",
                    type: "get",
                    dataType: "json",
                    data: function() {
                        return {
                            start: $('#dateRangePicker').data('daterangepicker').startDate.format('YYYY-MM-DD'),
                            end: $('#dateRangePicker').data('daterangepicker').endDate.format('YYYY-MM-DD'),
                            idpriority: dropDownListPriority.value(),
                            idstatus: dropDownListStatus.value(),
                            idteam: dropDownListTeam.value(),
                            iduser: dropDownListUser.value()
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
                    }
                }
            },
        },
        toolbar: ["excel", "pdf"],
        excel: {
            fileName: "Lista de tareas por prioridad.xlsx",
            filterable: false,
            allPages: true
        },
        excelExport: function(e) {

            var sheet = e.workbook.sheets[0];

            sheet.columns.forEach(function(column){
                delete column.width;
                column.autoWidth = true;
            });
        
            for (var i = 1; i < sheet.rows.length; i++)
            {
                var row = sheet.rows[i];

                switch (row.type)
                {
                    case "group-header":
                            row.cells[0].value = row.cells[0].value.replace(/(<([^>]+)>)/gi, "");
                            row.cells[0].fontSize = 25;
                            break;

                    case "group-footer":
                            row.cells[3].value = row.cells[3].value.replace(/(<([^>]+)>)/gi, "");
                            row.cells[4].value = row.cells[4].value.replace(/(<([^>]+)>)/gi, "");
                            row.cells[5].value = row.cells[5].value.replace(/(<([^>]+)>)/gi, "");
                            break;

                        case "data":

                            console.log(row);
                            if (row.cells[5].value != null) {
                                row.cells[5].value = moment(row.cells[5].value).format('DD-MM-YYYY');
                            }
                            if (row.cells[6].value != null) {
                                row.cells[6].value = moment(row.cells[6].value).format('DD-MM-YYYY');
                            }
                            if (row.cells[8].value != null) {
                                row.cells[8].value = moment(row.cells[8].value).format('DD-MM-YYYY');
                            }
                            row.cells[7].value = (row.cells[7].value == 0) ? 'NO' : 'Sí';
                
                    default:
                        break;
                }
            }
        },
        editable: {
            mode: "popup"
        },
        height: "400px",
        dataBound: function(e) {

            var data = gridTicketPriority.dataSource.data();

            $.each(data, function(i, row) {
                let tr = $('tr[data-uid="' + row.uid + '"] ');
                row.delayed == true ? tr.css("background-color", "rgb(245 119 120)") : tr.css("background-color", "#12c684");
            });

            $("#gridTicketPriority tbody tr").css("color", "white");
        },
        reorderable: true,
        resizable: true,
        sortable: true,
        pageable: {
            refresh: true,
            pageSizes: true,
            buttonCount: 5,
        },
        filterable: true,
        noRecords: {
            template: "<div class='alert alert-danger mt-1 alert-validation-msg' role='alert'><i class='feather icon-info mr-1 align-middle'></i><span>No hay datos</span></div>"
        },
        columns: [{
                field: "code",
                title: "Código",
                width: "30px",
                filterable: false
            },
            {
                field: "idstatus",
                title: "Estado",
                values: window.global_statuses,
                width: "60px",
                filterable: false
            },
            {
                field: "idpriority",
                title: "Prioridad",
                values: window.global_priorities,
                width: "60px",
                filterable: false
            },
            {
                field: "idspot",
                title: "Lugar",
                width: "100px",
                values: window.global_spots,
                filterable: false
            },
            {
                field: "name",
                title: "Tarea",
                width: "150px",
                template: "<strong>#: name # </strong>",
                filterable: false
            },
            {
                field: "duedate",
                title: "Vencimiento",
                width: "60px",
                filterable: false,
                template: "#=formatDate(duedate)#",
            },
            {
                field: "finishdate",
                title: "Final",
                width: "60px",
                filterable: false,
                template: "#=formatDate(finishdate)#",
            },
            {
                field: "postponed",
                title: "Pospuesta",
                width: "60px",
                template: "<strong> #= (postponed == 1 ? 'Si' : 'No') # </strong>",
                filterable: false
            },
            {
                field: "created_at",
                title: "Creado",
                width: "100px",
                filterable: false,
                template: "#=formatDate(created_at)#",
            }
        ],
    }).data("kendoGrid");
}

function initGridUserPriority() {
    gridUserPriority = $("#gridUserPriority").kendoGrid({
        dataSource: {
            transport: {
                read: {
                    url: "getUserPriority",
                    type: "get",
                    dataType: "json",
                    data: function() {
                        return {
                            start: $('#dateRangePicker').data('daterangepicker').startDate.format('YYYY-MM-DD'),
                            end: $('#dateRangePicker').data('daterangepicker').endDate.format('YYYY-MM-DD'),
                            idpriority: dropDownListPriority.value(),
                            idstatus: dropDownListStatus.value(),
                            idteam: dropDownListTeam.value(),
                            iduser: dropDownListUser.value()
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
                    }
                }
            },
        },
        toolbar: ["excel", "pdf"],
        excel: {
            fileName: "Reporte numero tareas con retraso por usuario.xlsx",
            filterable: false,
            allPages: true
        },
        excelExport: function(e) {

            var sheet = e.workbook.sheets[0];

            sheet.columns.forEach(function(column){
                delete column.width;
                column.autoWidth = true;
            });
        
            for (var i = 1; i < sheet.rows.length; i++)
            {
                var row = sheet.rows[i];

                switch (row.type)
                {
                    case "group-header":
                            row.cells[0].value = row.cells[0].value.replace(/(<([^>]+)>)/gi, "");
                            row.cells[0].fontSize = 25;
                            break;

                    case "group-footer":
                            break;

                        case "data":

                            row.cells[6].value = `${row.cells[6].value}%`;
                
                    default:
                        break;
                }
            }
        },
        editable: {
            mode: "popup"
        },
        height: "400px",
        reorderable: true,
        resizable: true,
        sortable: true,
        pageable: {
            refresh: true,
            pageSizes: true,
            buttonCount: 5,
        },
        filterable: true,
        noRecords: {
            template: "<div class='alert alert-danger mt-1 alert-validation-msg' role='alert'><i class='feather icon-info mr-1 align-middle'></i><span>No hay datos</span></div>"
        },
        columns: [{
                field: "id",
                title: "Responsable",
                width: "100px",
                template: "#=formatUser(id)#",
                filterable: false
            },
            {
                field: "total",
                title: "Tareas",
                width: "25px",
                filterable: false
            },
            {
                field: "pending",
                title: "Pendientes",
                width: "25px",
                filterable: false
            },
            {
                field: "finished",
                title: "Finalizadas",
                width: "25px",
                filterable: false
            },
            {
                field: "delayed",
                title: "Retraso",
                width: "25px",
                filterable: false
            },
            {
                field: "postponed",
                title: "Pospuestas",
                width: "25px",
                filterable: false
            },
            {
                field: "efficiency",
                title: "Eficiencia",
                width: "80px",
                template: "#=formatAverage(efficiency)#",
                filterable: false
            }
        ],
    }).data("kendoGrid");
}

function getEfficiencyPriority() {
    let data = {
        start: $('#dateRangePicker').data('daterangepicker').startDate.format('YYYY-MM-DD'),
        end: $('#dateRangePicker').data('daterangepicker').endDate.format('YYYY-MM-DD'),
        idpriority: dropDownListPriority.value(),
        idstatus: dropDownListStatus.value(),
        idteam: dropDownListTeam.value(),
        iduser: dropDownListUser.value()
    };

    let request = callAjax('getEfficiencyPriority', 'POST', data, false);
    request.done(function(result) {

        efficacyChart.updateSeries([result.average]);
        $("#total-tickets").text(result.total);
        $("#total-delayed-tickets").text(result.delayed);
        $("#total-postponed-tickets").text(result.postponed);

    }).fail(function(jqXHR, status) {
        console.log('error calculando la eficiencia');
    });
}

function formatDate(date) {
    return (date == null ? "------------------" : moment(date).format('YY-MM-DD HH:mm'));
}

function formatAverage(percent) {
    return "<div class='progress progress-bar-success progress-xl'>" +
        "<div class='progress-bar progress-bar-striped' role='progressbar' aria-valuenow='20' aria-valuemin='20' aria-valuemax='100' style='width:" + percent + "%;'>" + percent + "%</div>" +
        "</div>";
}

function formatPercent(percent) {
    return "<div class='progress progress-bar-danger progress-xl'>" +
        "<div class='progress-bar progress-bar-striped' role='progressbar' aria-valuenow='20' aria-valuemin='20' aria-valuemax='100' style='width:" + percent + "%;'>" + percent + "%</div>" +
        "</div>";
}

function formatUser(iduser) {
    let user = getUser(iduser);
    if (user == null) return '';

    return "<li data-toggle='tooltip' data-popup='tooltip-custom' data-placement='top' data-original-title='" + user.text + "' class='avatar pull-up'>" +
        "<img class='media-object rounded-circle' src='" + user.urlpicture + "' alt='Avatar' height='30' width='30'>" +
        "</li><strong>" + user.text + "</strong>";
}