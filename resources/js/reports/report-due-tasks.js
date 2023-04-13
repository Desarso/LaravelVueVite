let spotSelected = [];
let itemSelected = [];
let teamSelected = [];

$(document).ready(function () {

    initDateRangePicker();
    initDropDownListBranch();
    initDropDownListTeam();
    initDropDownListUser();
    initDropDownListItem();
    initGridSpot();
    initGridTeam();
    initGridItem();
    initGridTicket();
});

$(document).on("click", "#gridSpot .k-checkbox", function(event) {
    spotSelected.length > 0 ? dropDownListBranch.value(spotSelected[0]) : dropDownListBranch.value("");
    dropDownListBranch.trigger("change");
});

$(document).on("click", "#gridTeam .k-checkbox", function(event) {
    teamSelected.length > 0 ? dropDownListTeam.value(teamSelected[0]) : dropDownListTeam.value("");
    dropDownListTeam.trigger("change");
});

$(document).on("click", "#gridItem .k-checkbox", function(event) {
    itemSelected.length > 0 ? dropDownListItem.value(itemSelected[0]) : dropDownListItem.value("");
    dropDownListItem.trigger("change");
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
      dataSource: getUserBranches(),
      change: changeFilter,
    }).data("kendoDropDownList"); 
}

function initDropDownListUser()
{
    dropDownListUser = $("#dropDownListUser").kendoDropDownList({
        optionLabel: "-- Seleccione --",
        dataTextField: "text",
        dataValueField: "value",
        filter: "contains",
        dataSource: window.global_users,
        height: 400,
        change: changeFilter
    }).data("kendoDropDownList");
}

function initDropDownListItem()
{
    dropDownListItem = $("#dropDownListItem").kendoDropDownList({
        optionLabel: "-- Seleccione --",
        dataTextField: "name",
        dataValueField: "id",
        dataSource: window.global_items,
        filter: "contains",
        height: 400,
        change: changeFilter
    }).data("kendoDropDownList");
}

function changeFilter()
{
    gridSpot.dataSource.read();
    gridItem.dataSource.read();
    gridTeam.dataSource.read();
    gridTicket.dataSource.read();
}

function initGridSpot()
{
    gridSpot = $("#gridSpot").kendoGrid({
        dataSource: {
            transport: { 
                read: {
                    type:'get',
                    url: "getDueTasksBySpot",
                    dataType: 'json',
                    data: function() {
                        return getFilters();
                    }
                }
            },
            schema: {
                model: {
                    id: "id"
                }
            },
            aggregate: [{ field: "total", aggregate: "sum" }, { field: "unfinished", aggregate: "sum" }, { field: "overdue", aggregate: "sum" },
            { field: "average", aggregate: "average" }],
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
                            row.cells[1].value = row.cells[1].value.replace(/(<([^>]+)>)/gi, "");
                            row.cells[2].value = row.cells[2].value.replace(/(<([^>]+)>)/gi, "");
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
            fileName: "Tareas con fecha de vencimiento por sucursal.xlsx",
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
            spotSelected = this.selectedKeyNames();
        },
        columns:
        [
            { selectable: true, width: "30px" },
            { 
                field: "id",
                title: "Lugar",
                values: global_spots,
                width: "200px",
                filterable: false,
            },
            { 
                field: "total",
                title: "Total",
                width: "200px",
                filterable: false,
                aggregates: ["sum"],
                footerTemplate: "<h4 style='text-align: center; font-weight:700;'> #: sum # </h4>",
            },
            { 
                field: "unfinished",
                title: "Sin finalizar",
                width: "200px",
                filterable: false,
                aggregates: ["sum"],
                footerTemplate: "<h4 style='text-align: center; font-weight:700;'> #: sum # </h4>",
            },
            { 
                field: "overdue",
                title: "Retrasos",
                width: "200px",
                filterable: false,
                aggregates: ["sum"],
                footerTemplate: "<h4 style='text-align: center; font-weight:700;'> #: sum # </h4>",
            },
            { 
                field: "average",
                title: "Cumplimiento",
                width:"200px",
                filterable: false,
                template: '#=getBarCompliance(data)#',
                aggregates: ["average"],
                footerTemplate: "<h4 style='text-align: center; font-weight:700;'> #: round(average, 2) # %</h4>",
            }
        ]
    }).data("kendoGrid");
}

function initGridTeam()
{
    gridTeam = $("#gridTeam").kendoGrid({
        dataSource: {
            transport: { 
                read: {
                    type:'get',
                    url: "getDueTasksByTeam",
                    dataType: 'json',
                    data: function() {
                        return getFilters();
                    }
                }
            },
            schema: {
                model: {
                    id: "id"
                }
            },
            pageSize: 20,
        },
        toolbar: ["excel"],
        excel: {
            fileName: "Tareas con fecha de vencimiento por equipo.xlsx",
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
            teamSelected = this.selectedKeyNames();
        },
        columns:
        [
            { selectable: true, width: "40px" },
            { 
                field: "id",
                title: "Equipo",
                values: global_teams,
                width: "200px",
                filterable: false,
            },
            { 
                field: "total",
                title: "Total",
                width: "100px",
                filterable: false,
            },
            { 
                field: "unfinished",
                title: "Sin finalizar",
                width: "100px",
                filterable: false,
            },
            { 
                field: "overdue",
                title: "Retrasos",
                width: "100px",
                filterable: false,
            },
            { 
                field: "average",
                title: "Cumplimiento",
                width:"200px",
                filterable: false,
                template: '#=getBarCompliance(data)#'
            }
        ]
    }).data("kendoGrid");
}

function initGridItem()
{
    gridItem = $("#gridItem").kendoGrid({
        dataSource: {
            transport: { 
                read: {
                    type:'get',
                    url: "getDueTasksByItem",
                    dataType: 'json',
                    data: function() {
                        return getFilters();
                    }
                }
            },
            schema: {
                model: {
                    id: "id"
                }
            },
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
                        console.log(row.cells);
                            row.cells[0].value = global_items.find((item) => { return item.id == row.cells[0].value; }).name;
                            break;
                }
            }
        },
        toolbar: ["excel"],
        excel: {
            fileName: "Tareas con fecha de vencimiento.xlsx",
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
            itemSelected = this.selectedKeyNames();
        },
        columns:
        [
            { selectable: true, width: "40px" },
            { 
                field: "id",
                title: "Ítem",
                template: function(dataItem) {
                    let item = global_items.find((item) => { return item.id == dataItem.id; });
                    return item.name;
                },
                width: "200px",
                filterable: false,
            },
            { 
                field: "total",
                title: "Total",
                width: "100px",
                filterable: false,
            },
            { 
                field: "unfinished",
                title: "Sin finalizar",
                width: "100px",
                filterable: false,
            },
            { 
                field: "overdue",
                title: "Retrasos",
                width: "100px",
                filterable: false,
            },
            { 
                field: "average",
                title: "Cumplimiento",
                width:"200px",
                filterable: false,
                template: '#=getBarCompliance(data)#'
            }
        ]
    }).data("kendoGrid");
}

function initGridTicket()
{
    gridTicket = $("#gridTicket").kendoGrid({
        dataSource: {
            transport: { 
                read: {
                    type:'get',
                    url: "getDueTasks",
                    dataType: 'json',
                    data: function() {
                        return getFilters();
                    }
                }
            },
            schema: {
                model: {
                    id: "id"
                }
            },
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
                            row.cells[0].value = global_statuses.find((status) => { return status.value == row.cells[0].value; }).text;
                            row.cells[4].value = moment(row.cells[4].value).format('YY-MM-DD hh:mm A');
                            row.cells[5].value = moment(row.cells[5].value).format('YY-MM-DD hh:mm A');
                            row.cells[6].value = row.cells[6].value == null ? "" : moment(row.cells[6].value).format('YY-MM-DD hh:mm A');
                            break;
                }

            }
        },
        toolbar: ["excel"],
        excel: {
            fileName: "Tareas con fecha de vencimiento.xlsx",
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
            spotSelected = this.selectedKeyNames();
        },
        columns:
        [
            {
                field: "idstatus",
                title: "Estado",
                template: function(dataItem) {
                    let status = global_statuses.find((item) => { return item.value == dataItem.idstatus; });
                    return '<i class="fa fa-circle font-small-3 mr-50" style="color:' + status.color + '"></i>' + status.text;
                },
                filterable: false,
                width: "160px",
            },
            {
                field: "name",
                title: "Tarea",
                template: function(dataItem) {
                    return '<b>' + dataItem.name + '</b>';
                },
                filterable: false
            },
            {
                field: "idteam",
                title: "Equipo",
                values: window.global_teams,
                filterable: false
            },
            {
                field: "created_by",
                values: window.global_users,
                title: "Reportado por",
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
            {
                field: "duedate",
                title: "Fecha Vencimiento",
                template: function(dataItem) {

                    return dataItem.isOverdue == true ? "<p class='blink'>" + moment(dataItem.duedate).format('YY-MM-DD hh:mm A') + "</p>" : moment(dataItem.duedate).format('YY-MM-DD hh:mm A');

                },
                filterable: false
            },
            {
                field: "finishdate",
                title: "Fecha Fin",
                template: function(dataItem) {
                    return dataItem.finishdate == null ? "" : moment(dataItem.finishdate).format('YY-MM-DD hh:mm A');
                },
                filterable: false
            }
        ]
    }).data("kendoGrid");
}

function getFilters()
{
    return {
        start  : $('#dateRangePicker').data('daterangepicker').startDate.format('YYYY-MM-DD'),
        end    : $('#dateRangePicker').data('daterangepicker').endDate.format('YYYY-MM-DD'),
        iduser : dropDownListUser.value(),
        idteam : dropDownListTeam.value(),
        iditem : dropDownListItem.value(),
        idspot : dropDownListBranch.value()
    };
}

function getBarCompliance(data)
{
    return "<div class='completed-bar progress progress-bar-" + getColor(data.average) + "' style='height:18px; ' title=" + data.average + ">"+
                "<div class='progress-bar progress-bar-striped' role='progressbar' style='width: " + data.average + "%'>" + data.average + "%</div>"+
           "</div>";
}

function getColor(average)
{
    if(average >= 85) return "success"

    if(average >= 70) return "warning";

    return "danger";
}

function round(value, decimals)
{
    return Number(Math.round(value+'e'+decimals)+'e-'+decimals);
}

