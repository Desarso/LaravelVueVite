window.dataItem = null;
var exportFlag = false;

$(document).ready(function() {

    initDateRangePicker();
    initDropDownListUser();
    initDropDownListStatus();
    initGridClockin();
    initGridClockinDetails();

    $('#modal-approval').on('show.bs.modal', function (e) {
        $("#note").val("");
    });
});

$("#btn-verified").click(function() {
    kendo.confirm("¿Está seguro de realizar la acción?").then(function () {
        approveClockinTime();
    }, function () {

    });
});

function initDropDownListStatus()
{
    dropDownListStatus = $("#dropDownListStatus").kendoDropDownList({
        optionLabel: "-- Seleccione --",
        dataTextField: "text",
        dataValueField: "value",
        dataSource: [{value : "PENDING", text : "PEDIENTE"}, {value : "VERIFIED", text : "VEFICADO"}],
        change: changeFilter
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
                    url: "getDataClockin",
                    type: "get",
                    dataType: "json",
                    data: function() {
                        return {
                            start    : $('#dateRangePicker').data('daterangepicker').startDate.format('YYYY-MM-DD'),
                            end      : $('#dateRangePicker').data('daterangepicker').endDate.format('YYYY-MM-DD'),
                            iduser   : dropDownListUser.value(),
                            idstatus : dropDownListStatus.value()
                        };
                    },
                },
            },
            pageSize: 20,
            group: {
                field: "iduser", aggregates: [ { field: "regular_time", aggregate: "sum" }, { field: "overtime", aggregate: "sum" }, { field: "double_time", aggregate: "sum" }, { field: "regular_time_approved", aggregate: "sum" }, { field: "overtime_approved", aggregate: "sum" }, { field: "double_time_approved", aggregate: "sum" }] 
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

                            row.cells[3].value = row.cells[3].value.replace(/(<([^>]+)>)/gi, "");
                            row.cells[4].value = row.cells[4].value.replace(/(<([^>]+)>)/gi, "");
                            row.cells[5].value = row.cells[5].value.replace(/(<([^>]+)>)/gi, "");

                            row.cells[6].value = row.cells[6].value.replace(/(<([^>]+)>)/gi, "");
                            row.cells[7].value = row.cells[7].value.replace(/(<([^>]+)>)/gi, "");
                            row.cells[8].value = row.cells[8].value.replace(/(<([^>]+)>)/gi, "");
        

                            break;

                        case "data":
                        
                            row.cells[2].value = moment(row.cells[2].value).format('YYYY-MM-DD');
                            row.cells[3].value = hhmmss(row.cells[3].value);
                            row.cells[4].value = hhmmss(row.cells[4].value);
                            row.cells[5].value = hhmmss(row.cells[5].value);
                            
                            row.cells[6].value = hhmmss(row.cells[6].value);
                            row.cells[7].value = hhmmss(row.cells[7].value);
                            row.cells[8].value = hhmmss(row.cells[8].value);

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
                field: "status",
                title: "Estado",
                width: "15%",
                template: function(dataItem) {

                    let template = "";

                    let tooltip = (dataItem.note == null) ? "" : "data-toggle='tooltip' data-placement='top' title='" + dataItem.note + "'";

                    switch(dataItem.status)
                    {
                        case "PENDING":
                            template = "<div " + tooltip + " class='badge badge-pill badge-warning'>PENDIENTE</div>";
                            break;

                        case "VERIFIED":
                            template = "<div " + tooltip + " class='badge badge-pill badge-success'>VERIFICADO</div>";
                            break;                  
                    }

                    return template;
                },
                filterable: false,
            },
            {
                field: "date",
                title: "Fecha",
                width: "10%",
                filterable: false,
                template:function(dataItem) {
                    let icon = dataItem.fully_approved == 1 ? "" : "<i style='color:#fd774d' class='fa-lg fa fa-exclamation-circle' title='Existe diferencia entre las horas registradas y las aprobadas'></i> ";
                    return icon + moment(dataItem.date).format('YYYY-MM-DD');
                }
            },
            {
                field: "regular_time",
                title: "Regular",
                template: "#=formatDurationApproved(regular_time, regular_time_approved)#",
                groupFooterTemplate: "#=formatDuration(sum)#",
                width: "20%",
                filterable: false,
            },
            {
                field: "overtime",
                title: "Extra",
                template: "#=formatDurationApproved(overtime, overtime_approved)#",
                groupFooterTemplate: "#=formatDuration(sum)#",
                width: "20%",
                filterable: false,
            },
            {
                field: "double_time",
                title: "Doble",
                template: "#=formatDurationApproved(double_time, double_time_approved)#",
                groupFooterTemplate: "#=formatDuration(sum)#",
                width: "20%",
                filterable: false,
            },
            {
                field: "regular_time_approved",
                title: "Regular Aprobado",
                groupFooterTemplate: "#=formatDuration(sum)#",
                hidden: true,
                width: "0%"
            },
            {
                field: "overtime_approved",
                title: "Tiempo y medio Aprobado",
                groupFooterTemplate: "#=formatDuration(sum)#",
                hidden: true,
                width: "0%"
            },
            {
                field: "double_time_approved",
                title: "Doble Aprobado",
                groupFooterTemplate: "#=formatDuration(sum)#",
                hidden: true,
                width: "0%"
            },
            {
                field: "note_approved",
                title: "Comentario",
                hidden: true,
                width: "0%"
            },
            { command: { text: "Acción", click: showApprove }, title: " ", width: "10%" },
            { command: { text: "Detalles", click: showDetails }, title: " ", width: "10%" }
        ],
    }).data("kendoGrid");
}

function showApprove(e)
{
    $("#modal-approval").modal("show");

    window.dataItem = this.dataItem($(e.currentTarget).closest("tr"));

    $("#double_time").val(hhmm(window.dataItem.double_time));
    $("#overtime").val(hhmm(window.dataItem.overtime));
    $("#regular_time").val(hhmm(window.dataItem.regular_time));

    let double_time_approved  = (window.dataItem.double_time_approved == null)  ? hhmm(window.dataItem.double_time)  : hhmm(window.dataItem.double_time_approved);
    let overtime_approved     = (window.dataItem.overtime_approved == null)     ? hhmm(window.dataItem.overtime)     : hhmm(window.dataItem.overtime_approved);
    let regular_time_approved = (window.dataItem.regular_time_approved == null) ? hhmm(window.dataItem.regular_time) : hhmm(window.dataItem.regular_time_approved);

    $("#double_time_approve").val(double_time_approved);
    $("#overtime_approve").val(overtime_approved);
    $("#regular_time_approve").val(regular_time_approved);

    $("#note").val(window.dataItem.note_approved); 
}

function approveClockinTime()
{
    $.blockUI({ message: '<h1>Procesando...</h1>' });

    let note = $("#note").val();

    let regular_time_approved = getTimeWithSeconds($("#regular_time").val(), $("#regular_time_approve").val());
    let overtime_approved     = getTimeWithSeconds($("#overtime").val(), $("#overtime_approve").val());
    let double_time_approved  = getTimeWithSeconds($("#double_time").val(), $("#double_time_approve").val());

    let data = {
        "id"                    : window.dataItem.id,
        "note_approved"         : note,
        "double_time_approved"  : convertTimeToSeconds(double_time_approved),
        "overtime_approved"     : convertTimeToSeconds(overtime_approved),
        "regular_time_approved" : convertTimeToSeconds(regular_time_approved),
        "action"                : "approveovertime"
    };

    console.log(data);

    let request = callAjax('approveClockinTime', 'POST', data, false);

    request.done(function(result) {

        if(result.success)
        {
            $("#modal-approval").modal("hide");
            gridClockin.dataSource.read();
        }
        else
        {
            toastr.error(result.message, 'Permisos');
        }
        
        $.unblockUI();

    }).fail(function(jqXHR, status) {
        $.unblockUI();
    });
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
    let dateOvertime = moment(window.dataItem.date).format('YYYY-MM-DD');
    
    $("#label-person-name").html(personName);
    $("#label-date-overtime").html(dateOvertime);

    gridClockinDetails.dataSource.read();
    $("#modal-overtime").modal("show");
}

function formatDate(date)
{
    return (date == null ? "------------------" : moment(date).format('YY-MM-DD hh:mm A'));
}

function formatDurationApproved(secs, approved)
{
    if(approved == null) approved = 0;

    return "<h4 class='text-bold-400'><span>" + hhmm(secs) + "</span></h4><h4 class='text-bold-400'><span data-toggle='tooltip' data-placement='bottom' title='Aprobado' class='text-success'>" + hhmm(approved) + "</span></h4>";
}

function formatDuration(secs)
{
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
    return ("0" + num).slice(-2);
}

function initGridClockinDetails()
{
    gridClockinDetails = $("#gridClockinDetails").kendoGrid({
        dataSource: {
            transport: {
                read: {
                    url: "getClockinDetails",
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