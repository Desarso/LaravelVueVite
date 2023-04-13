window.selectedTickets = [];
window.selectedSpots   = [];
window.selectedOptions = [];

$(document).ready(function () {

    initDateRangePicker();
    initDropDownListBranch();
    initDropDownListChecklist();
    initGridChecklistManagement();
    initGridChecklistSpot();
    initGridChecklistOption();
});

$(document).on("click", "#gridChecklistManagement .k-checkbox", function(event) {
    gridChecklistOption.dataSource.read();
    gridChecklistSpot.dataSource.read();
});

$(document).on("click", "#gridChecklistSpot .k-checkbox", function(event) {
    dropDownListBranch.value(window.selectedSpots);
    dropDownListBranch.trigger("change");
});

$(document).on("click", "#gridChecklistOption .k-checkbox", function(event) {
    gridChecklistOption.dataSource.read();
    gridChecklistSpot.dataSource.read();
});

function initDropDownListChecklist()
{
    dropDownListChecklist = $("#dropDownListChecklist").kendoDropDownList({
      dataTextField: "text",
      dataValueField: "value",
      filter: "contains",
      dataSource: window.global_checklist,
      height: 400,
      change: changeFilterChecklist,
      index: 20
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

function changeFilterChecklist()
{
    window.selectedTickets = [];
    window.selectedSpots   = [];
    window.selectedOptions = [];
    gridChecklistManagement.dataSource.read();
    gridChecklistSpot.dataSource.read();
    gridChecklistOption.dataSource.read();
}

function changeFilter()
{
    window.selectedTickets = [];
    window.selectedSpots   = [];
    window.selectedOptions = [];
    gridChecklistManagement.dataSource.read();
    gridChecklistSpot.dataSource.read();
    gridChecklistOption.dataSource.read();
}

function initGridChecklistManagement()
{
    gridChecklistManagement = $("#gridChecklistManagement").kendoGrid({
        dataSource: {
            transport: { 
                read: {
                    type:'get',
                    url: "getDataChecklistReview2",
                    dataType: 'json',
                    data: function() {
                        return {
                            start             : $('#dateRangePicker').data('daterangepicker').startDate.format('YYYY-MM-DD'),
                            end               : $('#dateRangePicker').data('daterangepicker').endDate.format('YYYY-MM-DD'),
                            idspot            : dropDownListBranch.value(),
                            idchecklist       : dropDownListChecklist.value()
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
                field: "idchecklist",
                title: "Checklist",
                width: "200px",
                values: window.global_checklist,
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

function initGridChecklistSpot()
{
    gridChecklistSpot = $("#gridChecklistSpot").kendoGrid({
        dataSource: {
            transport: { 
                read: {
                    type:'get',
                    url: "getDataChecklistReviewBySpot2",
                    dataType: 'json',
                    data: function() {
                        return {
                            start             : $('#dateRangePicker').data('daterangepicker').startDate.format('YYYY-MM-DD'),
                            end               : $('#dateRangePicker').data('daterangepicker').endDate.format('YYYY-MM-DD'),
                            idspot            : dropDownListBranch.value(),
                            idchecklist       : dropDownListChecklist.value(),
                            tickets           : window.selectedTickets,
                            idchecklistoption : window.selectedOptions
                        };
                    }
                }
            },
            schema: {
                model: { id: "idspot" }
            }
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
        toolbar: [],
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
        selectable: false,
        //selectable: "multiple",
        persistSelection: true,
        noRecords: {
            template: "No hay datos disponibles"
        },
        pageable: false,
        change: function (e) {

            let selectedRows = this.select();
            let selectedDataItems = [];

            for (var i = 0; i < selectedRows.length; i++)
            {
                var dataItem = this.dataItem(selectedRows[i]);
                selectedDataItems.push(dataItem);
            }

            window.selectedSpots = selectedDataItems.map(function(obj) { return obj.idspot; });
        },
        columns:
        [
            { selectable: true, width: "30px" },
            { 
                field: "idspot",
                title: "Lugar",
                width: "120px",
                values: window.global_spots,
                filterable: false,
            },
            { 
                field: "compliance",
                title: "Resultados",
                width:"120px",
                filterable: false,
                template: '#=getResults(data)#',
            },
            { 
                field: "compliance",
                title: "Porcentajes",
                width:"120px",
                filterable: false,
                template: '#=getBarPercentage(data)#',
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
                    url: "getDataChecklistReviewByOption2",
                    dataType: 'json',
                    data: function() {
                        return {
                            start             : $('#dateRangePicker').data('daterangepicker').startDate.format('YYYY-MM-DD'),
                            end               : $('#dateRangePicker').data('daterangepicker').endDate.format('YYYY-MM-DD'),
                            idspot            : dropDownListBranch.value(),
                            idchecklist       : dropDownListChecklist.value(),
                            tickets           : window.selectedTickets,
                            idchecklistoption : window.selectedOptions
                        };
                    }
                }
            },
            schema: {
                model: { id: "idchecklistoption" }
            }
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
        toolbar: [],
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
        selectable: false,
        //selectable: "multiple",
        persistSelection: true,
        noRecords: {
            template: "No hay datos disponibles"
        },
        pageable: false,
        change: function (e) {

            let selectedRows = this.select();
            let selectedDataItems = [];

            for (var i = 0; i < selectedRows.length; i++)
            {
                var dataItem = this.dataItem(selectedRows[i]);
                selectedDataItems.push(dataItem);
            }

            window.selectedOptions = selectedDataItems.map(function(obj) { return obj.idchecklistoption; });

            console.log(window.selectedOptions);
        },
        columns:
        [
            { selectable: true, width: "30px" },
            { 
                field: "name",
                title: "Ítem",
                width: "200px",
                filterable: false,
            },
            { 
                field: "compliance",
                title: "Resultados",
                width:"100px",
                filterable: false,
                template: '#=getResults(data)#',
            },
            { 
                field: "compliance",
                title: "Porcentajes",
                width:"100px",
                filterable: false,
                template: '#=getBarPercentage(data)#',
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
    return "<div class='completed-bar progress progress-bar-" + getColor(data.compliance) + "' style='height:18px;' title=" + data.compliance + ">"+
                "<div class='progress-bar progress-bar-striped' role='progressbar' style='width: " + data.compliance + "%'>" + data.compliance + "%</div>"+
           "</div>";
}

function getResults(data)
{
    let template = "<ul class='list-group list-group-flush customer-info'>"

    $.each(data.percentages, function(i, row) {

        template += "<li class='list-group-item d-flex justify-content-between'>" +
                        "<div class='series-info'>" +
                            "<i class='fa fa-circle font-small-3' style='color:" + row.color + "'></i>" +
                            "<span class='text-bold-600'>" + row.text + "</span>" +
                        "</div>" +
                        "<div class='product-result'>" +
                            "<span>" + row.count + "</span>" +
                        "</div>" +
                    "</li>";
    });

    return template + "</ul>";
}

function getBarPercentage(data)
{
    let template = "<ul class='list-group list-group-flush customer-info'>"

    $.each(data.percentages, function(i, row) {

        template += "<li class='list-group-item d-flex justify-content-between'>" +
                        "<div class='series-info'>" +
                            "<span class='text-bold-600'>" + row.text + "</span>" +
                        "</div>" +
                        "<div class='product-result' style='width: 100%'>" +
                     "<div class='ml-1 mb-0 progress progress-xl' style='width: 100%' title=" + row.percent + "%" + ">" +
                     "<div class='progress-bar progress-bar-striped' role='progressbar' style='width: " + row.percent + "%; background-color:" + row.color + "'>" + row.percent + "%</div>" +
                     "</div>" +
                        "</div>" +
                    "</li>";
    });

    return template + "</ul>";

    
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

