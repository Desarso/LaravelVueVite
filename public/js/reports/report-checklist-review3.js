window.idticketchecklist = null;

$(document).ready(function () {
    initDateRangePicker();
    initDropDownListChecklist();
    initDropDownListBranch();

    initGridChecklistReview();
    initGridChecklistReviewDetail();
    initGridChecklistReviewNotes();
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
    window.selectedTicket = null;
    gridChecklistReview.dataSource.read();
    gridChecklistReviewDetail.dataSource.read();
    gridChecklistReviewNotes.dataSource.read();
}

function selectTicketChecklist(e) {
    var grid = $("#gridChecklistReview").data("kendoGrid");
    var row = $(e.target).closest("tr");

    if(row.hasClass("k-selected")){
        setTimeout(function(e) {
            var grid = $("#gridChecklistReview").data("kendoGrid");
            grid.clearSelection();
        })
    } else {
        grid.clearSelection();
    };

    var dataItem = grid.dataItem(row[0]);
    window.selectedTicket = dataItem.idticket;
    gridChecklistReviewDetail.dataSource.read();
    gridChecklistReviewNotes.dataSource.read();
};

function initGridChecklistReview()
{
    gridChecklistReview = $("#gridChecklistReview").kendoGrid({
        dataSource: {
            transport: { 
                read: {
                    type:'get',
                    url: "getDataChecklistReview3",
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
            aggregate: [{ field: "percentage", aggregate: "average" }],
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

            
        },
        columns: [
            { selectable: true, width: "30px", headerTemplate: ' ' },
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
            },
            { 
                field: "iduser",
                title: "Usuario",
                values: global_users,
                width: "200px",
                filterable: false,
            },
        ]
    }).data("kendoGrid");

    gridChecklistReview.tbody.on("click", ".k-checkbox", selectTicketChecklist);
}

function initGridChecklistReviewDetail()
{
    gridChecklistReviewDetail = $("#gridChecklistReviewDetail").kendoGrid({
        dataSource: {
            transport: {
                read: {
                    type: 'get',
                    url: "getDataChecklistReview3Detail",
                    dataType: 'json',
                    data: function () {
                        return getFilters();
                    }
                }
            },
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
                    field: "name",
                    title: "Ítem",
                    width: "200px",
                    filterable: false,
                },
                {
                    field: "value",
                    title: "Cumplimiento",
                    template: "#=formatValue(value)#",
                    width: "100px",
                    filterable: false,
                }
            ]
    }).data("kendoGrid");
}

function initGridChecklistReviewNotes()
{
    gridChecklistReviewNotes = $("#gridChecklistReviewNotes").kendoGrid({
        dataSource: {
            transport: {
                read: {
                    type: 'get',
                    url: "getDataChecklistReview3Notes",
                    dataType: 'json',
                    data: function () {
                        return getFilters();
                    }
                }
            },
            group: { field: "line" },
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
                    field: "line",
                    title: "Item",
                    width: "200px",
                    hidden: true,
                    filterable: false,
                    groupHeaderTemplate: function(data) {
                        return "<b>" + data.items[0].line + "</b>";
                    },
                },
                {
                    field: "note",
                    title: "Notas",
                    width: "200px",
                    template: "#=formatValue(note)#",
                    filterable: false,
                },
            ]
    }).data("kendoGrid");
}

function getFilters()
{
    return {
        start       : $('#dateRangePicker').data('daterangepicker').startDate.format('YYYY-MM-DD'),
        end         : $('#dateRangePicker').data('daterangepicker').endDate.format('YYYY-MM-DD'),
        idchecklist : dropDownListChecklist.value(),
        id          : window.idticketchecklist,
        selectedTicket: window.selectedTicket
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

function formatValue(value)
{
    if (value == null) {
        return '';
    }

    return value.replace(/&lt;/g, "<").replace(/&gt;/g, ">");;
}

function getBarPercentage(data)
{
    return "<div class='completed-bar progress progress-bar-" + getColor(data.percentage) + "' style='height:18px; ' title=" + data.percentage + ">"+
                "<div class='progress-bar progress-bar-striped' role='progressbar' style='width: " + data.percentage + "%'>" + data.percentage + "%</div>"+
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