window.selectedTickets = [];
window.selectedGroups  = [];

$(document).ready(function () {

    initDateRangePicker();
    initDropDownListBranch();
    initDropDownListChecklist();
    initGridChecklistManagement();
    initGridChecklistGroup();
    initGridChecklistOption();
});

$(document).on("click", "#gridChecklistManagement .k-checkbox", function(event) {
    gridChecklistGroup.dataSource.read();
    gridChecklistOption.dataSource.read();
});

$(document).on("click", "#gridChecklistGroup .k-checkbox", function(event) {
    gridChecklistOption.dataSource.read();
});

function initDropDownListChecklist()
{
    dropDownListChecklist = $("#dropDownListChecklist").kendoDropDownList({
      dataTextField: "text",
      dataValueField: "value",
      filter: "contains",
      //dataSource: window.global_checklist,
      dataSource: window.global_checklist.filter(checklist => ($.inArray(checklist.value, [25, 26]) != -1)),
      height: 400,
      change: changeFilter,
      index: 1
    }).data("kendoDropDownList");
}

function initDropDownListBranch()
{
    dropDownListBranch = $("#dropDownListBranch").kendoDropDownList({
      dataValueField: "id",
      dataTextField: "text",
      dataValueField: "value",
      filter: "contains",
      optionLabel: "-- Sucursal --",
      height: 400,
      dataSource: getUserBranches(),
      change: changeFilter,
    }).data("kendoDropDownList"); 
}

function changeFilter()
{
    gridChecklistManagement.dataSource.read();
    gridChecklistGroup.dataSource.read();
    gridChecklistOption.dataSource.read();
}

function initGridChecklistManagement()
{
    gridChecklistManagement = $("#gridChecklistManagement").kendoGrid({
        dataSource: {
            transport: { 
                read: {
                    type:'get',
                    url: "getDataChecklistManagement",
                    dataType: 'json',
                    data: function() {
                        return {
                            start       : $('#dateRangePicker').data('daterangepicker').startDate.format('YYYY-MM-DD'),
                            end         : $('#dateRangePicker').data('daterangepicker').endDate.format('YYYY-MM-DD'),
                            idspot      : dropDownListBranch.value(),
                            idchecklist : dropDownListChecklist.value()
                        };
                    }
                }
            },
            schema: {
                model: {
                    id: "idticket"
                }
            },
            aggregate: [{ field: "percentage", aggregate: "average" }, { field: "compliance", aggregate: "average" }],
            pageSize: 20,
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
                    case "data":
                            row.cells[2].value = moment(row.cells[2].value).format('YYYY-MM-DD');
                            break;

                    case "footer":
                            row.cells[3].value = row.cells[3].value.replace(/(<([^>]+)>)/gi, "");
                            row.cells[4].value = row.cells[4].value.replace(/(<([^>]+)>)/gi, "");
                            break;
                
                    default:
                        break;
                }
            }
        },
        toolbar: ["excel"],
        excel: {
            fileName: "Lista de checklist.xlsx",
            filterable: false,
            allPages: true
        },
        height: 450,
        sortable: true,
        filterable: {
            mode: "row",
        },
        groupable: false,
        resizable: true,
        reorderable: true,
        selectable: false,
        noRecords: {
            template: "No hay datos disponibles"
        },
        persistSelection: true,
        pageable: {
            refresh: true,
            pageSizes: true,
            buttonCount: 5,
        },
        change: function (e) {

            let selectedRows = this.select();
            let selectedDataItems = [];

            for (var i = 0; i < selectedRows.length; i++)
            {
                var dataItem = this.dataItem(selectedRows[i]);
                selectedDataItems.push(dataItem);
            }

            window.selectedTickets = selectedDataItems.map(function(obj) { return obj.idticket; });
        },
        columns:
        [
            { selectable: true, width: "30px" },
            { 
                field: "idspot",
                title: "Lugar",
                values: global_spots,
                width: "200px",
                filterable: false,
            },
            { 
                field: "iduser",
                title: "Usuario",
                values: global_users,
                width: "200px",
                filterable: false,
            },
            { 
                field: "created_at",
                title: "Fecha",
                width: "200px",
                filterable: false,
                template:function(dataItem) {
                    return moment(dataItem.created_at).format('LLLL');
                }
            },
            { 
                field: "percentage",
                title: "Porcentaje",
                width:"200px",
                filterable: false,
                template: '#=getBarPercentage(data)#',
                aggregates: ["average"],
                footerTemplate: "<h4 style='text-align: center; font-weight:700;'> #: round(average, 2) # </h4>",
            },
            { 
                field: "compliance",
                title: "Cumplimiento",
                width:"200px",
                filterable: false,
                template: '#=getBarCompliance(data)#',
                aggregates: ["average"],
                footerTemplate: "<h4 style='text-align: center; font-weight:700;'> #: round(average, 2) # </h4>",
            }
        ]
    }).data("kendoGrid");
}

function initGridChecklistGroup()
{
    gridChecklistGroup = $("#gridChecklistGroup").kendoGrid({
        dataSource: {
            transport: { 
                read: {
                    type:'get',
                    url: "getDataChecklistManagementGroup",
                    dataType: 'json',
                    data: function() {
                        return {
                            start       : $('#dateRangePicker').data('daterangepicker').startDate.format('YYYY-MM-DD'),
                            end         : $('#dateRangePicker').data('daterangepicker').endDate.format('YYYY-MM-DD'),
                            idspot      : dropDownListBranch.value(),
                            idchecklist : dropDownListChecklist.value(),
                            tickets     : window.selectedTickets
                        };
                    }
                }
            },
            schema: {
                model: {
                    id: "idgroup"
                }
            },
            aggregate: [{ field: "percentage", aggregate: "sum" }, { field: "compliance", aggregate: "average" },],
            pageSize: 20,
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
                    case "footer":
                            row.cells[2].value = row.cells[2].value.replace(/(<([^>]+)>)/gi, "");
                            row.cells[3].value = row.cells[3].value.replace(/(<([^>]+)>)/gi, "");
                            break;

                    default:
                        break;
                }
            }
        },
        toolbar: ["excel"],
        excel: {
            fileName: "Efectos cualitativos.xlsx",
            filterable: false,
            allPages: true
        },
        height: "700px",
        sortable: true,
        filterable: {
            mode: "row",
        },
        groupable: false,
        resizable: true,
        reorderable: true,
        selectable: false,
        noRecords: {
            template: "No hay datos disponibles"
        },
        pageable: false,
        persistSelection: true,
        change: function (e) {

            let selectedRows2 = this.select();
            let selectedDataItems2 = [];

            for (var i = 0; i < selectedRows2.length; i++)
            {
                var dataItem2 = this.dataItem(selectedRows2[i]);
                selectedDataItems2.push(dataItem2);
            }

            window.selectedGroups = selectedDataItems2.map(function(obj) { return obj.idgroup; });
        },
        columns:
        [
            { selectable: true, width: "30px" },
            { 
                field: "weight",
                title: "Importancia",
                width: "80px",
                filterable: false,
            },
            { 
                field: "group",
                title: "Efecto Cualitativo",
                width: "200px",
                filterable: false,
            },
            { 
                field: "percentage",
                title: "Porcentaje",
                width:"70px",
                filterable: false,
                template: function(dataItem) {
                    return "<b>" + dataItem.percentage + "<b/>";
                },
                aggregates: ["sum"],
                footerTemplate: "<h4 style='text-align: center; font-weight:700;'> #: round(sum, 2) # </h4>",
            },
            { 
                field: "compliance",
                title: "Cumplimiento",
                width: "180px",
                filterable: false,
                template: '#=getBarCompliance(data)#',
                aggregates: ["average"],
                footerTemplate: "<h4 style='text-align: center; font-weight:700;'> #: round(average, 2) # </h4>",
            }
        ]
    }).data("kendoGrid");
}

function initGridChecklistOption()
{
    gridChecklistOption = $("#gridChecklistOption").kendoGrid({
        dataSource: {
            transport: { 
                read: {
                    type:'get',
                    url: "getDataChecklistManagementOption",
                    dataType: 'json',
                    data: function() {
                        return {
                            start       : $('#dateRangePicker').data('daterangepicker').startDate.format('YYYY-MM-DD'),
                            end         : $('#dateRangePicker').data('daterangepicker').endDate.format('YYYY-MM-DD'),
                            idspot      : dropDownListBranch.value(),
                            idchecklist : dropDownListChecklist.value(),
                            tickets     : window.selectedTickets,
                            groups      : window.selectedGroups
                        };
                    }
                }
            },
            group: { field: "group" },
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
                            break;

                    default:
                        break;
                }
            }
        },
        toolbar: ["excel"],
        excel: {
            fileName: "Detalles de efectos cualitativos.xlsx",
            filterable: false,
            allPages: true
        },
        height: "700px",
        sortable: true,
        filterable: {
            mode: "row",
        },
        groupable: false,
        resizable: true,
        reorderable: true,
        selectable: true,
        noRecords: {
            template: "No hay datos disponibles"
        },
        pageable: false,
        columns:
        [
            { 
                field: "group",
                title: "Group",
                width: "200px",
                filterable: false,
                hidden: true,
                groupHeaderTemplate: function(dataItem) {
                    return "<b>" + dataItem.value + "<b/>";
                }
            },
            { 
                field: "name",
                title: "Ítem",
                width: "400px",
                filterable: false,
            },
            { 
                field: "compliance",
                title: "Cumplimiento",
                width:"100px",
                filterable: false,
                template: '#=getBarCompliance(data)#',
            }
        ]
    }).data("kendoGrid");
}

function round(value, decimals)
{
    return Number(Math.round(value+'e'+decimals)+'e-'+decimals);
}

function getBarCompliance(data)
{
    return "<div class='completed-bar progress progress-bar-" + getColor(data.compliance) + "' style='height:18px; ' title=" + data.compliance + ">"+
                "<div class='progress-bar progress-bar-striped' role='progressbar' style='width: " + data.compliance + "%'>" + data.compliance + "%</div>"+
           "</div>";
}

function getBarPercentage(data)
{
    return "<div class='completed-bar progress progress-bar-" + getColor(data.percentage) + "' style='height:18px; ' title=" + data.percentage + ">"+
                "<div class='progress-bar progress-bar-striped' role='progressbar' style='width: " + data.percentage + "%'>" + data.percentage + "%</div>"+
           "</div>";
}

function getColor(average)
{
    if(average >= 85)
    {
        return "success"
    }
    else if(average < 85 && average >= 70)
    {
        return "warning";
    }
    else
    {
        return "danger";
    }
}

