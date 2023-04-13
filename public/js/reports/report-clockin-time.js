window.dataItem = null;
var exportFlag = false;

$(document).ready(function() {
    initDateRangePicker();
    initDropDownListUser();
    initDropDownListBranch();
    initGridClockin();
    initGridClockinDetails();
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

function changeFilter()
{
    gridClockin.dataSource.read();
}

function initGridClockin()
{
    gridClockin = $("#gridClockin").kendoGrid({
        dataSource: {
            transport: {
                read: {
                    url: "getDataClockinTime",
                    type: "get",
                    dataType: "json",
                    data: function() {
                        return {
                            start    : $('#dateRangePicker').data('daterangepicker').startDate.format('YYYY-MM-DD'),
                            end      : $('#dateRangePicker').data('daterangepicker').endDate.format('YYYY-MM-DD'),
                            iduser   : dropDownListUser.value(),
                            idbranch : dropDownListBranch.value(),
                        };
                    },
                },
            },
            pageSize: 20,
            group: {
                field: "iduser", aggregates: [ { field: "regular_time", aggregate: "sum" } ] 
            },
            schema: {
                model: {
                    id: "id",
                    fields: {
                        id: { type: "number", editable: false, nullable: true },
                    }
                }
            },
        },
        excelExport: function(e) {

            if (!exportFlag) {
                e.sender.showColumn(6);
                e.sender.showColumn(7);
                e.sender.showColumn(8);
                e.sender.showColumn(9);
                e.preventDefault();
                exportFlag = true;
                setTimeout(function () {
                    e.sender.saveAsExcel();
                });
            } else {
                e.sender.hideColumn(6);
                e.sender.hideColumn(7);
                e.sender.hideColumn(8);
                e.sender.hideColumn(9);
                exportFlag = false;
            }

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

                            console.log(row.cells[2].value);

                            row.cells[2].value = row.cells[2].value.replace(/(<([^>]+)>)/gi, "");
        
                            break;

                        case "data":
                        
                            row.cells[1].value = moment(row.cells[1].value).format('YYYY-MM-DD');
                            row.cells[2].value = hhmmss(row.cells[2].value);

                            break;
                
                    default:
                        break;
                }
            }
        },
        toolbar: ["excel"],
        excel: {
            fileName: "Reporte de horas extra.xlsx",
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
        dataBound: function() {
            $('[data-toggle="tooltip"]').tooltip();
        },
        columns: [
            {
                field: "iduser",
                title: "Usuario",
                width: "0%",
                groupHeaderTemplate: function(dataItem) {
                    return "<b>" + global_users.find(o => o.value === dataItem.value).text + "</b>";
                },
                filterable: false,
                hidden: true
            },
            {
                field: "date",
                title: "Fecha",
                width: "50%",
                filterable: false,
                template:function(dataItem) {
                    return moment(dataItem.date).format('YYYY-MM-DD');
                }
            },
            {
                field: "regular_time",
                title: "Horas trabajadas",
                template: "#=formatDurationApproved(regular_time)#",
                groupFooterTemplate: "#=formatDuration(sum)#",
                width: "50%",
                filterable: false,
            },
            { command: { text: "Detalles", click: showDetails }, title: " ", width: "10%" }
        ],
    }).data("kendoGrid");
}

function getTimeWithSeconds(time, time_approve)
{
    return (time_approve + ':' + time.split(":")[2]);
}

function convertTimeToSeconds(time)
{
    return moment(time, 'HH:mm:ss').diff(moment().startOf('day'), 'seconds')
}

function showDetails(e)
{
    window.dataItem = this.dataItem($(e.currentTarget).closest("tr"));
    
    let personName = global_users.find(o => o.value === window.dataItem.iduser).text;
    let dateOvertime = moment(window.dataItem.created_at).format('YYYY-MM-DD');
    
    $("#label-person-name").html(personName);
    $("#label-date-overtime").html(dateOvertime);

    gridClockinDetails.dataSource.read();
    $("#modal-overtime").modal("show");
}

function formatDate(date)
{
    return (date == null ? "------------------" : moment(date).format('YY-MM-DD hh:mm A'));
}

function formatDurationApproved(secs)
{
    return "<h4 class='text-bold-400'><span>" + hhmm(secs) + "</span></h4>";
}

function formatDuration(secs)
{
    console.log(secs);
    
    return "<h5 style='font-weight:700; color:#2c2c2c;'>" + hhmm(secs) + "</h5>";
}

function formatTotalDuration(secs)
{
    return "<h4 style='font-weight:700; color:#2c2c2c;'>" + hhmmss(secs) + "</h4>";
}

function hhmmss(secs)
{
    var minutes = Math.floor(secs / 60);
    secs = secs%60;
    var hours = Math.floor(minutes/60)
    minutes = minutes%60;
    return pad(hours) + ":" + pad(minutes) + ":" + pad(secs);
}

function hhmm(secs)
{
    var minutes = Math.floor(secs / 60);
    secs = secs%60;
    var hours = Math.floor(minutes/60)
    minutes = minutes%60;
    return pad(hours) + ":" + pad(minutes);
}

function pad(num)
{
    let slice = num.toString().length <= 2 ? 2 : num.toString().length;

    return ("0" + num).slice(-slice);
}

function initGridClockinDetails()
{
    gridClockinDetails = $("#gridClockinDetails").kendoGrid({
        dataSource: {
            transport: {
                read: {
                    url: "getClockinTimeDetails",
                    type: "get",
                    dataType: "json",
                    data: function() {
                        return {
                            date     : window.dataItem == null ? null : moment(dataItem.date).format('YYYY-MM-DD'), 
                            iduser   : window.dataItem == null ? null : window.dataItem.iduser,
                        };
                    },
                },
            },
            pageSize: 100,
            aggregate: [
                { field: "duration",    aggregate: "sum" }
            ]
        },
        height: "400px",
        groupable: false,
        reorderable: true,
        resizable: true,
        sortable: true,
        pageable: false,
        filterable: true,
        noRecords: {
            template: "<div class='alert alert-danger mt-1 alert-validation-msg' role='alert'><i class='feather icon-info mr-1 align-middle'></i><span>No hay datos</span></div>"
        },
        columns: [
            {
                field: "idactivity",
                title: "Actividad",
                width: "200px",
                filterable: false,
                template:function(dataItem) {
                    return "<div class='badge badge-pill badge-danger' style='background-color:" + dataItem.activity.color + ";'>" + dataItem.activity.name + "</div>";
                }
            },
            {
                field: "clockin",
                title: "Inicio",
                width: "120px",
                filterable: false,
                template:function(dataItem) {
                    return moment(dataItem.clockin).format('hh:mm A');
                }
            },
            {
                field: "clockout",
                title: "Fin",
                width: "120px",
                filterable: false,
                template:function(dataItem) {
                    if(dataItem.clockout == null) return "-------";
                    return moment(dataItem.clockout).format('hh:mm A');
                }
            },
            {
                field: "duration",
                title: "Tiempo",
                template: "#=formatDuration(duration)#",
                width: "120px",
                aggregates: ["sum"],
                footerTemplate: "<h2 style='text-align: center; font-weight:700; color:rgba(120, 123, 128);'> #=formatDuration(sum) # </h2>",
                filterable: false,
            },
        ],
    }).data("kendoGrid");
}

