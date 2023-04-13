$(document).ready(function() {
    initDateRangePicker();
    initDropDownListBranch();
    initDropDownListUser();
    initGridChecklistInvoice();
    initGridChecklistInvoiceUser();
});

$("#btnRefresh").click(function() {
    changeFilter();
});

function initDropDownListBranch()
{
    dropDownListBranch = $("#dropDownListBranch").kendoDropDownList({
      dataValueField: "id",
      dataTextField: "text",
      dataValueField: "value",
      filter: "contains",
      optionLabel: "-- Sucursal --",
      height: 400,
      dataSource: global_spots,
      change: changeFilter,
    }).data("kendoDropDownList"); 
}

function changeFilter()
{
    gridChecklistInvoice.dataSource.read();
    gridChecklistInvoiceUser.dataSource.read();
}

function initGridChecklistInvoice()
{
    gridChecklistInvoice = $("#gridChecklistInvoice").kendoGrid({
        dataSource: {
            transport: {
                read: {
                    url: "getDataChecklistInvoice",
                    type: "get",
                    dataType: "json",
                    data: function() {
                        return {
                            start  : $('#dateRangePicker').data('daterangepicker').startDate.format('YYYY-MM-DD'),
                            end    : $('#dateRangePicker').data('daterangepicker').endDate.format('YYYY-MM-DD'),
                            idspot : dropDownListBranch.value(),
                            iduser : dropDownListUser.value()
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
        toolbar: ["pdf", "excel"],
        excelExport: function(e) {

            var sheet = e.workbook.sheets[0];

            sheet.columns.forEach(function(column){
                delete column.width;
                column.autoWidth = true;
            });
        
            for(var i = 1; i < sheet.rows.length; i++)
            {
                var row = sheet.rows[i];

                switch(row.type)
                {
                    case "footer":

                            break;

                    case "data":
                            row.cells[4].value = moment(row.cells[4].value).format('YYYY-MM-DD');
                            break;
                
                    default:
                        break;
                }
            }
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
        height: "600px",
        groupable: false,
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
        dataBound: function() {},
        columns: [
            {
                field: "idspot",
                title: "Sucursal",
                values: global_spots,
                width: "200px",
                filterable: false
            },
            {
                field: "iduser",
                title: "Usuario",
                values: global_users,
                width: "200px",
                filterable: false
            },
            {
                field: "code",
                title: "# Factura",
                width: "80px",
                filterable: false
            },
            {
                field: "issue_date",
                title: "Emisión",
                width: "80px",
                filterable: false
            },
            {
                field: "date",
                title: "Ingreso",
                width: "80px",
                template: function(dataItem) {
                    return moment(dataItem.date).format('YYYY-MM-DD');
                },
                filterable: false
            },
            {
                field: "days",
                title: "Días sin ingresar",
                width: "100px",
                filterable: false
            },
            {
                field: "supplier",
                title: "Proveedor",
                width: "180px",
                filterable: false
            },
            {
                field: "items",
                title: "# Ítems",
                width: "60px",
                filterable: false
            },
            {
                field: "amount",
                title: "Monto",
                format:"{0:n2}",
                width: "100px",
                filterable: false
            }
        ],
    }).data("kendoGrid");
}

function initGridChecklistInvoiceUser()
{
    gridChecklistInvoiceUser = $("#gridChecklistInvoiceUser").kendoGrid({
        dataSource: {
            transport: {
                read: {
                    url: "getDataChecklistInvoice",
                    type: "get",
                    dataType: "json",
                    data: function() {
                        return {
                            start  : $('#dateRangePicker').data('daterangepicker').startDate.format('YYYY-MM-DD'),
                            end    : $('#dateRangePicker').data('daterangepicker').endDate.format('YYYY-MM-DD'),
                            idspot : dropDownListBranch.value(),
                            iduser : dropDownListUser.value()
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
            group: {
                field: "iduser", aggregates: [
                { field: "idspot", aggregate: "count"},
                ]
            },

            aggregate: [
                { flied: "idspot", aggregate: "count" }
            ]
        },
        toolbar: ["pdf", "excel"],
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
                            break;

                    case "group-footer":
                            row.cells[1].value = row.cells[1].value.replace(/(<([^>]+)>)/gi, "");
                            break;

                    case "data":
                            row.cells[4].value = moment(row.cells[4].value).format('YYYY-MM-DD');
                            break;
                
                    default:
                        break;
                }
            }
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
        height: "600px",
        groupable: false,
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
        dataBound: function() {},
        columns: [
            {
                field: "iduser",
                title: "Usuario",
                groupHeaderTemplate: function(dataItem) {
                    return "<b>" + global_users.find(o => o.value === dataItem.value).text + "</b>";
                },
                hidden: true,
                width: "200px",
                filterable: false
            },
            {
                field: "idspot",
                title: "Sucursal",
                values: global_spots,
                width: "200px",
                aggregates: ["count"],
                groupFooterTemplate: "<b>Total: #=count#</b>",
                filterable: false
            },
            {
                field: "code",
                title: "# Factura",
                width: "80px",
                filterable: false
            },
            {
                field: "issue_date",
                title: "Emisión",
                width: "80px",
                filterable: false
            },
            {
                field: "date",
                title: "Ingreso",
                width: "80px",
                template: function(dataItem) {
                    return moment(dataItem.date).format('YYYY-MM-DD');
                },
                filterable: false
            },
            {
                field: "days",
                title: "Días sin ingresar",
                width: "100px",
                filterable: false
            },
            {
                field: "supplier",
                title: "Proveedor",
                width: "200px",
                filterable: false
            },
            {
                field: "items",
                title: "# Ítems",
                width: "60px",
                filterable: false
            },
            {
                field: "amount",
                title: "Monto",
                format:"{0:n2}",
                width: "80px",
                filterable: false
            }
        ],
    }).data("kendoGrid");
}

function formatDate(date)
{
    if (date == null) {
        return "------------------";
    } else {
        let date_format = date.replace(".000000Z", "");
        return moment(date_format).format('DD MMM YYYY hh:mm A');
    }
}