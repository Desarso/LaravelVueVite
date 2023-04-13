$(document).ready(function() {
    initDateRangePicker();
    initDropDownListChecklist();
    initDropDownListSpot();
    initDropDownListUser();
    initGridChecklist();
});

function initDropDownListChecklist()
{
    dropDownListChecklist = $("#dropDownListChecklist").kendoDropDownList({
        dataTextField: "text",
        dataValueField: "value",
        filter: "contains",
        dataSource: window.global_checklist,
        height: 400,
        change: changeFilter,
    }).data("kendoDropDownList");
}

function changeFilter()
{
    gridChecklist.dataSource.read();
}

function initGridChecklist()
{
    gridChecklist = $("#gridChecklist").kendoGrid({
        dataSource: {
            transport: {
                read: {
                    url: "getDataChecklistAudit",
                    type: "get",
                    dataType: "json",
                    data: function() {
                        return {
                            start       : $('#dateRangePicker').data('daterangepicker').startDate.format('YYYY-MM-DD'),
                            end         : $('#dateRangePicker').data('daterangepicker').endDate.format('YYYY-MM-DD'),
                            idchecklist : dropDownListChecklist.value(),
                            idspot      : dropDownListSpot.value(),
                            iduser      : dropDownListUser.value()
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
        },
        excelExport: function(e) {
            
            var sheet = e.workbook.sheets[0];
        
            for (var i = 1; i < sheet.rows.length; i++) {
                var row = sheet.rows[i];

                if(row.cells[2].value == 6)
                {
                    sheet.rows[i].height = 30;
                    for (var ci = 0; ci < sheet.rows[i].cells.length; ci++) {
                      sheet.rows[i].cells[ci].fontSize = 25;
                    }
                }

            }
        },
        toolbar: ["pdf"],
        excel: {
            fileName: "Datos de checklist.xlsx",
            filterable: false,
            allPages: true
        },
        pdf: {
            allPages: true,
            avoidLinks: true,
            paperSize: "A4",
            margin: { top: "2cm", left: "1cm", right: "1cm", bottom: "1cm" },
            landscape: true,
            repeatHeaders: true,
            scale: 0.8
        },
        editable: {
            mode: "popup"
        },
        height:"600px",
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
        dataBound: function(e) {},
        detailInit: detailInitChecklist,
        detailExpand: function(e) {
            var grid = e.sender;
            var rows = grid.element.find(".k-master-row").not(e.masterRow);

            rows.each(function(e) {
                grid.collapseRow(this);
            });
        },
        columns: [
            {
                field: "ticket.code",
                title:  "Cod",
                width: "60px",
                filterable: false
            },
            {
                field: "ticket",
                title: "Estado",
                template: function(dataItem) {
                    let status = global_statuses.find((item) => { return item.value == dataItem.ticket.idstatus; });
                    return '<i class="fa fa-circle font-small-3 mr-50" style="color:' + status.color + '"></i>' + status.text;
                },
                filterable: false

            },
            {
                field: "ticket.idspot",
                values: window.global_spots,
                title: "Lugar",
                width: "300px",
                filterable: false
            },
            {
                field: "idchecklist",
                values: window.global_checklist,
                title: "Checklist",
                width: "300px",
                filterable: false
            },
            {
                field: "ticket.created_by",
                values: window.global_users,
                title: "Creado por",
                filterable: false
            },
            {
                field: "email_sent",
                title: "# de envíos",
                filterable: false
            },
            {
                field: "created_at",
                title: "Fecha",
                template: function(dataItem) {
                    return moment(dataItem.created_at).format('YY-MM-DD hh:mm A');
                },
                filterable: false
            },
            { command: { text: "Enviar", click: sendEmail }, title: " ", width: "100px" }
        ]
    }).data("kendoGrid");
}

function detailInitChecklist(e)
{
    $("<div id='gridChecklist'></div>").appendTo(e.detailCell).kendoGrid({
        dataSource: {
            transport: {
                read: {
                    url: "getChecklistDetail",
                    type: "get",
                    dataType: "json",
                    data: function() {
                        return {
                            id : e.data.id
                        }
                    }
                }
            },
            pageSize: 20,
            schema: {
                model: {
                    id: "id",
                    fields: {
                        id: { editable: false, nullable: false, type: "number" },
                        name: { editable: true, field: "name", type: "string", nullable: false },
                        value: { editable: true, field: "value", type: "string", nullable: false },
                    }
                }
            },
        },
        editable: false,
        toolbar: [],
        scrollable: false,
        sortable: true,
        filterable: true,
        noRecords: {
            template: "<div class='alert alert-danger mt-1 alert-validation-msg' role='alert'><i class='feather icon-info mr-1 align-middle'></i><span>No hay datos</span></div>"
        },
        pageable: {
            refresh: true,
            pageSizes: true,
            buttonCount: 5
        },
        columns: [
            {
                field: "name",
                title: "Ítem",
                template: function(dataItem) {
                    if(dataItem.optiontype != 6) return dataItem.name;
                    return "<strong>" + dataItem.name + "</strong>";
                },
                filterable: false
            },
            {
                field: "value",
                title: "Valor",
                filterable: false
            }
        ],
    });
}

function sendEmail(e)
{
    $.blockUI({ message: '<h1>Procesando...</h1>' });

    e.preventDefault();
    var dataItem = this.dataItem($(e.currentTarget).closest("tr"));
    console.log(dataItem);
    
    let request = callAjax('sendEmailAudit', 'POST', { 'id': dataItem.id }, false);

    request.done(function(result) {
        gridChecklist.dataSource.read();
        toastr.success('Los emails fueron enviados', 'Email enviado');
        $.unblockUI();

    }).fail(function(jqXHR, status) {
        $.unblockUI();
        toastr.error('La acción no se puedo completar', 'Problemas');
    });
}

