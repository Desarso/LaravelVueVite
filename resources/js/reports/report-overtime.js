window.dataItem = null;
var exportFlag = false;

$(document).ready(function() {
    initDateRangePicker();
    initDropDownListUser();
    initDropDownListStatus();
    initGridOvertime();

    $(document).on("click", ".btn-status", function(event) {
        var status = $(this).data("status");
        kendo.confirm("¿Está seguro de realizar la acción?").then(function () {
            approveOvertime(status);
        }, function () {

        });
    });

    $('#modal-approval').on('show.bs.modal', function (e) {
        $("#note").val("");
    })


    gridOvertime.bind("excelExport", function (e) {
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
    });
});

function initDropDownListStatus()
{
    dropDownListStatus = $("#dropDownListStatus").kendoDropDownList({
        optionLabel: "-- Seleccione --",
        dataTextField: "text",
        dataValueField: "value",
        dataSource: [{value : "PENDING", text : "PEDIENTE"}, {value : "APPROVED", text : "APROBADO"}, {value : "REPROBATE", text : "REPROBADO"}],
        change: changeFilter
    }).data("kendoDropDownList");   
}

function changeFilter()
{
    gridOvertime.dataSource.read();
}

function initGridOvertime()
{
    gridOvertime = $("#gridOvertime").kendoGrid({
        dataSource: {
            transport: {
                read: {
                    url: "getDataAttendanceOvertime",
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
                field: "iduser", aggregates: [ { field: "normal_time", aggregate: "sum" }, { field: "half_time", aggregate: "sum" }, { field: "double_time", aggregate: "sum" }] 
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
                            row.cells[3].value = row.cells[3].value.replace(/(<([^>]+)>)/gi, "");
                            row.cells[4].value = row.cells[4].value.replace(/(<([^>]+)>)/gi, "");
                            row.cells[5].value = row.cells[5].value.replace(/(<([^>]+)>)/gi, "");
                            break;

                        case "data":

                            console.log(row);
                        
                            row.cells[2].value = moment(row.cells[2].value).format('YYYY-MM-DD');
                            row.cells[3].value = hhmmss(row.cells[3].value);
                            row.cells[4].value = hhmmss(row.cells[4].value);
                            row.cells[5].value = hhmmss(row.cells[5].value);
                            break;
                
                    default:
                        break;
                }
            }
        },
        toolbar: ["excel", "pdf"],
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

                    console.log(dataItem.note);

                    let template = "";

                    let tooltip = (dataItem.note == null) ? "" : "data-toggle='tooltip' data-placement='top' title='" + dataItem.note + "'";

                    switch(dataItem.status)
                    {
                        case "PENDING":
                            template = "<div " + tooltip + " class='badge badge-pill badge-warning'>PENDIENTE</div>";
                            break;

                        case "APPROVED":
                            template = "<div " + tooltip + " class='badge badge-pill badge-success'>APROBADO</div>";
                            break;

                        case "REPROBATE":
                            template = "<div " + tooltip + " class='badge badge-pill badge-danger'>REPROBADO</div>";
                            break;                    
                    }

                    return template;
                },
                filterable: false,
            },
            {
                field: "created_at",
                title: "Fecha",
                width: "10%",
                filterable: false,
                template:function(dataItem) {
                    return moment(dataItem.created_at).format('YYYY-MM-DD');
                }
            },
            {
                field: "normal_time",
                title: "Sencilla",
                template: "#=formatDurationApproved(normal_time, normal_time_approved)#",
                groupFooterTemplate: "#=formatDuration(sum)#",
                width: "20%",
                filterable: false,
            },
            {
                field: "half_time",
                title: "Tiempo y medio",
                template: "#=formatDurationApproved(half_time, half_time_approved)#",
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
                field: "normal_time_approved",
                title: "Sencilla Aprobada",
                hidden: true,
                width: "1%"
            },
            {
                field: "half_time_approved",
                title: "Tiempo y medio Aprobada",
                hidden: true,
                width: "1%"
            },
            {
                field: "double_time_approved",
                title: "Doble Aprobada",
                hidden: true,
                width: "1%"
            },
            {
                field: "note",
                title: "Nota",
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

    $("#double_time").val(hhmmss(window.dataItem.double_time));
    $("#half_time").val(hhmmss(window.dataItem.half_time));
    $("#normal_time").val(hhmmss(window.dataItem.normal_time));

    let double_time_approved = (window.dataItem.double_time_approved == null) ? hhmm(window.dataItem.double_time) : window.dataItem.double_time_approved;
    let half_time_approved   = (window.dataItem.half_time_approved == null) ? hhmm(window.dataItem.half_time) : window.dataItem.half_time_approved;
    let normal_time_approved = (window.dataItem.normal_time_approved == null) ? hhmm(window.dataItem.normal_time) : window.dataItem.normal_time_approved;

    $("#double_time_approve").val(double_time_approved);
    $("#half_time_approve").val(half_time_approved);
    $("#normal_time_approve").val(normal_time_approved);
}

function showDetails(e)
{
    window.dataItem = this.dataItem($(e.currentTarget).closest("tr"));
    
    let personName = global_users.find(o => o.value === window.dataItem.iduser).text;
    let dateOvertime = moment(window.dataItem.created_at).format('YYYY-MM-DD');
    
    $("#label-person-name").html(personName);
    $("#label-date-overtime").html(dateOvertime);

    initGridOvertimeDetails();
    $("#modal-overtime").modal("show");
}

function formatDate(date)
{
    return (date == null ? "------------------" : moment(date).format('YY-MM-DD hh:mm A'));
}

function formatDurationApproved(secs, approved)
{
    if(approved == null) approved = "---------";

    return "<h4 class='text-bold-400'><span>" + hhmmss(secs) + "</span></h4><h4 class='text-bold-400'><span data-toggle='tooltip' data-placement='bottom' title='Aprobado' class='text-success'>" + approved + "</span></h4>";
}

function formatDuration(secs)
{
    return "<h5 style='font-weight:700; color:#2c2c2c;'>" + hhmmss(secs) + "</h5>";
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

function approveOvertime(status)
{
    $.blockUI({ message: '<h1>Procesando...</h1>' });

    let note = $("#note").val();

    let data = {
        "id"                   : window.dataItem.id,
        "status"               : status,
        "note"                 : note,
        "double_time_approved" : $("#double_time_approve").val(),
        "half_time_approved"   : $("#half_time_approve").val(),
        "normal_time_approved" : $("#normal_time_approve").val(),
        "action"               : "approveovertime"
    };

    let request = callAjax('approveOvertime', 'POST', data, false);

    request.done(function(result) {

        if(result.success)
        {
            $("#modal-approval").modal("hide");
            gridOvertime.dataSource.read();
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


function initGridOvertimeDetails()
{
    gridOvertimeDetails = $("#gridOvertimeDetails").kendoGrid({
        dataSource: {
            transport: {
                read: {
                    url: "getOvertimeDetails",
                    type: "get",
                    dataType: "json",
                    data: function() {
                        return {
                            date     : moment(dataItem.created_at).format('YYYY-MM-DD'), 
                            iduser   : window.dataItem.iduser,
                        };
                    },
                },
            },
            pageSize: 100,
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
                field: "iduser",
                title: "Usuario",
                width: "0px",
                filterable: false,
                hidden: true
            },
            {
                field: "ticket",
                title: "OT",
                width: "100px",
                filterable: false,
                template: "#=ticket.code#",
            },
            {
                field: "ticket",
                title: "Tarea",
                width: "150px",
                filterable: false,
                template: "#=ticket.name#",
            },
            {
                field: "time",
                title: "Tiempo",
                template: "#=formatDuration(time)#",
                width: "120px",
                filterable: false,
            },
            {
                field: "start",
                title: "Inicio",
                width: "120px",
                filterable: false,
            },
            {
                field: "end",
                title: "Fin",
                width: "120px",
                filterable: false,
            }
        ],
    }).data("kendoGrid");
}