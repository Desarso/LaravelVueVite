$(document).ready(function() {

    initDateRangePicker();
    initDropDownListTeam();
    initDropDownListUser();
    //initDropDownListShift();
    initGridClockinDevice();
});

function initDropDownListShift()
{
    dropDownListShift = $("#dropDownListShift").kendoDropDownList({
        optionLabel: "-- Seleccione --",
        dataTextField: "text",
        dataValueField: "value",
        dataSource: {
            transport: {
                read: {
                    dataType: "json",
                    url: "getListShifts",
                }
            }
        },
        height: 400,
        change: changeFilter
    }).data("kendoDropDownList");
}

function changeFilter()
{
    gridClockinDevice.dataSource.read();
}

function initGridClockinDevice()
{
    gridClockinDevice = $("#gridClockinDevice").kendoGrid({
        dataSource: {
            transport: {
                read: {
                    url: "getDataClockinDevice",
                    type: "get",
                    dataType: "json",
                    data: function() {
                        return {
                            start    : $('#dateRangePicker').data('daterangepicker').startDate.format('YYYY-MM-DD'),
                            end      : $('#dateRangePicker').data('daterangepicker').endDate.format('YYYY-MM-DD'),
                            idteam   : dropDownListTeam.value(),
                            iduser   : dropDownListUser.value(),
                            //idshift  : dropDownListShift.value()
                        };
                    },
                },
            },
            pageSize: 20,
            group: {
                field: "iduser", aggregates: [  
                    { field: "clockin", aggregate: "count" }, 
                    { field: "duration", aggregate: "sum" },
                    { field: "ovetime", aggregate: "sum" },
                    { field: "late_time", aggregate: "sum" },
                    { field: "regular_time", aggregate: "sum" },
                    { field: "overtime", aggregate: "sum" },
                    { field: "double_time", aggregate: "sum" },
                ] 
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
                            row.cells[3].value = row.cells[3].value.replace(/(<([^>]+)>)/gi, "");
                            row.cells[4].value = row.cells[4].value.replace(/(<([^>]+)>)/gi, "");
                            row.cells[5].value = row.cells[5].value.replace(/(<([^>]+)>)/gi, "");
                            row.cells[6].value = row.cells[6].value.replace(/(<([^>]+)>)/gi, "");
                            row.cells[7].value = row.cells[7].value.replace(/(<([^>]+)>)/gi, "");
                            break;

                        case "data":
                            row.cells[1].value = formatDate(row.cells[1].value);
                            row.cells[2].value = formatDate(row.cells[2].value);
                            row.cells[3].value = hhmmss(row.cells[3].value);
                            row.cells[4].value = hhmmss(row.cells[4].value);
                            row.cells[8].value = (row.cells[8].value == 1) ? "SI" : "NO";
                            row.cells[9].value = (row.cells[9].value == 1) ? "SI" : "NO";
                            break;
                
                    default:
                        break;
                }
            }
        },
        toolbar: ["excel", "pdf"],
        excel: {
            fileName: "Reporte de asistencia.xlsx",
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
        dataBound: function(e) {

            var data = gridClockinDevice.dataSource.data();

            $.each(data, function(i, row) {
                let tr = $('tr[data-uid="' + row.uid + '"] ');
                /*
                if (row.free_day) {
                    tr.css("background-color", "#f94438");
                    tr.find('td').each (function() {
                        $(this).find('h4').css("color", "#000000");
                    }); 
                }
                */
            });
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
        columns: [
            /*
            {
                field: "free_day",
                title: "",
                width: "25px",
                filterable: false,
                hidden: true,
            },
            */
            {
                field: "id",
                title: "ID",
                width: "25px",
                filterable: false,
                hidden: true,
            },
            {
                field: "iduser",
                title: "Usuario",
                width: "0px",
                groupHeaderTemplate: function(dataItem) {
                    let avatar = dataItem.items[0].avatar;
                    let name = dataItem.value;
                    let teamName = dataItem.items[0].teamName;

                    return  "<span class='avatar'><img src='" + avatar + "' height='32' width='32'></span>" + 
                            "<b>" + name + "</b>" +
                            " (" + teamName + ")";
                },
                filterable: false,
                hidden: true
            },
            {
                field: "clockin",
                title: "Entrada",
                width: "60px",
                template: "#=formatDate(clockin)#",
                filterable: false,
                groupFooterTemplate: "<b>Asistencias: #=count#</b>",
                attributes: {
                    "class": "ticket-progress"
                },
            },
            {
                field: "clockout",
                title: "Salida",
                width: "60px",
                template: "#=formatDate(clockout)#",
                filterable: false,
                attributes: {
                    "class": "ticket-pendint"
                },
            },
            {
                field: "late_time",
                title: "Tardia (hrs)",
                width: "60px",
                template: "#=formatTime(late_time)#",
                groupFooterTemplate: "#=formatTotalTime(sum)#",
                filterable: false,
                attributes: {
                    "class": "ticket-finished"
                },
            },
            {
                field: "duration",
                title: "Total Horas",
                width: "60px",
                template: "#=formatDuration(duration)#",
                groupFooterTemplate: "#=formatTotalDuration(sum)#",
                filterable: false,
                attributes: {
                    "class": "ticket-finished"
                },
            },
            {
                field: "regular_time",
                title: "Diurna",
                width: "35px", 
                template: "#=formatOverTime(regular_time)#",
                groupFooterTemplate: "#=formatTotalOverTime(sum)#",
                filterable: false,
                attributes: {
                    "class": "ticket-finished"
                },
            },
            {
                field: "overtime",
                title: "Mixta",
                width: "35px",
                template: "#=formatOverTime(overtime)#",
                groupFooterTemplate: "#=formatTotalOverTime(sum)#",
                filterable: false,
                attributes: {
                    "class": "ticket-finished"
                },
            },
            {
                field: "double_time",
                title: "Nocturna",
                width: "35px",
                template: "#=formatOverTime(double_time)#",
                groupFooterTemplate: "#=formatTotalOverTime(sum)#",
                filterable: false,
                attributes: {
                    "class": "ticket-finished"
                },
            },
            {
                field: "isholiday",
                title: "Doble",
                width: "35px",
                template: "#=formatIsdouble(isholiday)#",
                filterable: false,
                attributes: {
                    "class": "ticket-finished"
                },
            },
            {
                field: "out_of_time",
                title: "Fuera horario",
                width: "35px",
                template: "#=formatIsdouble(out_of_time)#",
                filterable: false,
                attributes: {
                    "class": "ticket-finished"
                },
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

function formatDuration(secs)
{
    return "<h5 style='font-weight:400; color:#2c2c2c;'>" + hhmmss(secs) + "</h5>";
}

function formatTotalDuration(secs)
{
    console.log("formatTotalDuration formatTotalDuration formatTotalDuration");
    console.log(secs);
    return "<h4 style='font-weight:700; color:#2c2c2c;'>" + hhmmss2(secs) + "</h4>";
}

function formatTime(secs)
{
    return "<h4 style='font-weight:400; color:#2c2c2c;'>" + hhmmss(secs) + "</h4>";
}

function formatTotalTime(secs)
{
    return "<h4 style='font-weight:700; color:#2c2c2c;'>" + hhmmss(secs) + "</h4>";
}

function formatOverTime(min)
{
    let over_time = min ?? 0;
    return "<h4 style='font-weight:400; color:#2c2c2c;'>" + over_time + "</h4>";
}


function formatTotalOverTime(over_time, color)
{
    return "<h4 style='font-weight:700; color:#" + color + ";'>"+ over_time + "</h4>";
}

function formatIsdouble(isdouble)
{
    let text = isdouble ? "Sí" : "No" ;
    return "<h4 style='font-weight:700; color:#4F4F4F;'>"+ text + "</h4>";
}


function hhmmss(secs)
{
    var minutes = Math.floor(secs / 60);
    secs = secs%60;
    var hours = Math.floor(minutes/60)
    minutes = minutes%60;
    return pad(hours) + ":" + pad(minutes) + ":" + pad(secs);
}

function hhmmss2(secs)
{
    var minutes = Math.floor(secs / 60);
    secs = secs%60;
    var hours = Math.floor(minutes/60)
    minutes = minutes%60;
    return hours + ":" + pad(minutes) + ":" + pad(secs);
}

function pad(num)
{
    return ("0" + num).slice(-2);
}