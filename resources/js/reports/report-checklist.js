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
                    url: "getDataChecklist",
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
        toolbar: ["excel", "pdf"],
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
        columns: [
            {
                field: "name",
                title:  "Item",
                template: function(dataItem) {
                    return (dataItem.optiontype == 6 ? "<strong>" + dataItem.name + "</strong>" : dataItem.name);
                },
                width: "300px",
                filterable: false
            },
            {
                field: "value",
                title: "Valor",
                width: "60px",
                filterable: false
            },
            {
                field: "optiontype",
                title: " ",
                width: "1px",
                filterable: false
            }
        ]
    }).data("kendoGrid");
}

