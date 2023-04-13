masterRow = null;

$(document).ready(function() {
    initDateRangePicker();
    initDropDownListChecklist()
    initGridChecklistDuration();
});

$("#btnRefresh").click(function() {
    changeFilter();
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
    gridChecklistDuration.dataSource.read();
}

function initGridChecklistDuration()
{
    gridChecklistDuration = $("#gridChecklistDuration").kendoGrid({
        dataSource: {
            transport: {
                read: {
                    url: "getDataChecklistDuration",
                    type: "get",
                    dataType: "json",
                    data: function() {
                        return {
                            start       : $('#dateRangePicker').data('daterangepicker').startDate.format('YYYY-MM-DD'),
                            end         : $('#dateRangePicker').data('daterangepicker').endDate.format('YYYY-MM-DD'),
                            idchecklist : dropDownListChecklist.value(),
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
        detailInit: detailInitChecklistDuration,
        detailExpand: function(e) {

            masterRow = this.dataItem(e.masterRow);

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
                width: "120px",
                filterable: false
            },
            {
                field: "ticket",
                title: "Estado",
                template: function(dataItem) {
                    let status = global_statuses.find((item) => { return item.value == dataItem.ticket.idstatus; });
                    return '<i class="fa fa-circle font-small-3 mr-50" style="color:' + status.color + '"></i>' + status.text;
                },
                width: "170px",
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
                width: "200px",
                filterable: false
            },
            {
                field: "ticket.startdate",
                title: "Fecha de inicio",
                template: function(dataItem) {
                    if(dataItem.ticket.startdate == null) return "";
                    return moment(dataItem.startdate).format('YY-MM-DD hh:mm A');
                },
                filterable: false
            },
            {
                field: "ticket.finishdate",
                title: "Fecha de fin",
                template: function(dataItem) {
                    if(dataItem.ticket.finishdate == null) return "";
                    return moment(dataItem.finishdate).format('YY-MM-DD hh:mm A');
                },
                filterable: false
            },
            {
                field: "created_at",
                title: "Fecha",
                template: function(dataItem) {
                    return moment(dataItem.created_at).format('YY-MM-DD hh:mm A');
                },
                width: "160px",
                filterable: false
            },
        ]
    }).data("kendoGrid");
}

function detailInitChecklistDuration(e)
{
    $("<div id='gridChecklistDurationDetails'></div>").appendTo(e.detailCell).kendoGrid({
        dataSource: {
            transport: {
                read: {
                    url: "getDataChecklistDurationDetail",
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
                    }
                }
            },
            aggregate: [
                { field: "duration", aggregate: "sum" },
                { field: "name", aggregate: "count" },
            ]
        },
        excelExport: function(e) {

            e.workbook.fileName = getFileName() + ".xlsx";

            var sheet = e.workbook.sheets[0];

            sheet.columns.forEach(function(column){
                delete column.width;
                column.autoWidth = true;
            });
        
            mergedCells = [];

            for (var i = 1; i < sheet.rows.length; i++)
            {
                var row = sheet.rows[i];
                row.height = 20;

                switch (row.type)
                {
                    case "footer":
                        row.cells[0].value = row.cells[0].value.replace(/(<([^>]+)>)/gi, "");
                        row.cells[4].value = row.cells[4].value.replace(/(<([^>]+)>)/gi, "");
                        break;
                    
                    case "data":
                        if(row.cells[4].value == null && i > 0)
                        {
                            let mergedCell = ("A" + (i + 1) + ":" + "E" + (i + 1));
                            mergedCells.push(mergedCell);
        
                            for(var ci = 0; ci < row.cells.length; ci++)
                            {
                                row.cells[ci].fontSize = 16;
                                row.cells[ci].textAlign = "center";
                                row.cells[ci].bold = true;
                            }
                        }

                        row.cells[1].value = (row.cells[1].value == null) ? "" : global_statuses.find((item) => { return item.value == row.cells[1].value; }).text;
                        row.cells[4].value = (row.cells[4].value == null) ? "" : hhmmss(row.cells[4].value);
                        break;
                }
            }
            sheet.mergedCells = mergedCells;
        },
        edit: function(e){

        },
        editable: false,
        toolbar: ["excel"],
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
                    if(dataItem.optiontype == 6) return "<div style='text-align: center'><strong class='text-center'>" + dataItem.name + "</strong></div>";
                    return dataItem.name;
                },
                footerTemplate: function(dataItem) {
                    return "<b>Ítems: " + dataItem.name.count + "</b>";
                },
                filterable: false
            },
            {
                field: "value",
                title: "Estado",
                template: function(dataItem) {
                    if(dataItem.value == null) return "";
                    let status = global_statuses.find((item) => { return item.value == dataItem.value; });
                    return '<i class="fa fa-circle font-small-3 mr-50" style="color:' + status.color + '"></i>' + status.text;
                },
                width: "170px",
                filterable: false
            },
            {
                field: "startdate",
                title: "Fecha de inicio",
                template: function(dataItem) {
                    if(dataItem.startdate == null) return "";
                    return moment(dataItem.startdate).format('DD MMM YYYY hh:mm A');
                },
                filterable: false
            },
            {
                field: "finishdate",
                title: "Fecha de fin",
                template: function(dataItem) {
                    if(dataItem.finishdate == null) return "";
                    return moment(dataItem.finishdate).format('DD MMM YYYY hh:mm A');
                },
                filterable: false
            },
            {
                field: "duration",
                title: "Duración",
                template: function(dataItem) {
                    if(dataItem.optiontype == 6) return "";
                    return hhmmss(dataItem.duration);
                },
                footerTemplate: function(dataItem) {
                    return "<b>" + hhmmss(dataItem.duration.sum) + "</b>";
                },
                filterable: false
            }
        ],
    });
}

function hhmmss(secs)
{
    var minutes = Math.floor(secs / 60);
    secs = secs%60;
    var hours = Math.floor(minutes/60)
    minutes = minutes%60;
    return pad(hours) + ":" + pad(minutes) + ":" + pad(secs);
}

function pad(num)
{
    return ("0" + num).slice(-2);
}

function getFileName()
{
    let spot      = global_spots.find((item) => { return item.value == masterRow.ticket.idspot; });
    let checklist = global_checklist.find((item) => { return item.value == masterRow.idchecklist; });

    return spot.text + " - " + checklist.text + " - " + moment(masterRow.created_at).format('YYYY-MM-DD');
}
