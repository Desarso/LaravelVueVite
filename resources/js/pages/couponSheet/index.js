var keypressTime;
var code = '';
var idcoupon = null;
var reading = true;

$(document).ready(function() {
    initGridCouponSheet();
    initEventInputCodeCoupon();
    initContextMenu();
    initUploadFile();
    initCouponChart();
    getDataScannedCoupons();
});

$('#modal-import-excel').on('show.bs.modal', function(e) {
    getNextCouponSheet();
    $("#files").data("kendoUpload").clearAllFiles();
});

$('#modal-sheet').on('show.bs.modal', function(e) {
    $("#sheetcode").val('');
});

$("#btn-new-sheet").click(function() {
    $("#modal-sheet").modal("show");
});

$("#btn-import-excel").click(function() {
    $("#modal-import-excel").modal("show");
});

$("#btn-show-scan").click(function() {
    $("#id-msg").hide();
    $("#modal-show-result").modal("show");
    code = ''; 
    idcoupon = null;
    setTimeout(function() { 
        $('#couponCode').trigger('focus');
    }, 800);
});

$("#btn-create-sheet").click(function() {

    if($("#sheetcode").val() == "")
    {
        toastr.warning("Digite el código de la hoja");
        return false;
    }
    
    if($("#sheetcode").val().length != 15)
    {
        toastr.warning("El código debe de tener una longitud de 15 dígitos");
        return false;
    }

    createSheet();
});

$("#btn-update-sheet").click(function() {

    if($("#new-sheetcode").val() == "")
    {
        toastr.warning("Digite el código de la hoja");
        return false;
    }
    
    if($("#new-sheetcode").val().length != 15)
    {
        toastr.warning("El código debe de tener una longitud de 15 dígitos");
        return false;
    }

    updateSheet();
});

$("#btn-upload-excel").click(function() {

    if($("#next-sheet-code").val() == "")
    {
        toastr.warning("Digite el código de la hoja");
        return false;
    }
    
    if($("#next-sheet-code").val().length < 15)
    {
        toastr.warning("El código debe de tener una longitud minima 15 dígitos");
        return false;
    }

    if(uploadFiles.getFiles().length == 0)
    {
        toastr.warning("Seleccione el archivo de excel");
        return false;
    }

    $("#files").data("kendoUpload").upload();
});

$("#btn-close-sheet").click(function(e) {
    confirmCloseSheet();
});

$("#btn-mark-coupon").click(function(e) {
    markCoupon();
});

$("#btn-show-report").click(function() {
    initGridReport();
    getDataScannedCoupons();
    $("#modal-report").modal("show");
});

$('#btn-scan-coupon').on('click', function(e) {
    code = $("#couponCode").val();
    if (code != '') {
        getCouponDetail();
    }
});

function changeFilter()
{
    gridCouponSheet.dataSource.read();
}

function initUploadFile()
{
    uploadFiles = $("#files").kendoUpload({
        async: {
            saveUrl: "sendExcelFiles",
            autoUpload: false,
            concurrent: true,
            batch: true
        },
        upload: function(e) {

            var xhr = e.XMLHttpRequest; 

            xhr.addEventListener("readystatechange", function(e) {
                if (xhr.readyState == 1)
                {
                    xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
                }
            });

            e.data = { barcode: $("#next-sheet-code").val() };
        },
        success: function(e) {
            getNextCouponSheet();
            toastr.success("Archivos cargados con éxito");
            gridCouponSheet.dataSource.read();
        },
        error: function(e) {
            let response = JSON.parse(e.XMLHttpRequest.response);
            toastr.error(response.message);
        },
        select: function(e) {
            if(uploadFiles.getFiles().length > 0) uploadFiles.clearAllFiles();
        },
        validation: {
            allowedExtensions: [".xlsx"],
            maxFileSize: 900000,
        }
    }).data("kendoUpload");
}

function initGridCouponSheet()
{
    gridCouponSheet = $("#gridCouponSheet").kendoGrid({
        dataSource: {
            transport: {
                read: {
                    url: "getDataCouponSheet",
                    type: "get",
                    dataType: "json",
                    data: function() {
                        return {

                        };
                    },
                },
            },
            requestEnd: function(e) {

                if(e.type == "read" && e.response != undefined)
                {
                    $("#sheet-count").text("Total hojas: " + e.response.length);

                    let totalCoupons   = 0;
                    let scannedCoupons = 0;
                    
                    $.each(e.response, function(key, value) {
                        totalCoupons    += value.coupons_count;
                        scannedCoupons  += value.escanned_coupons_count;
                    });

                    let percent = totalCoupons  == 0 ? 0 : Math.round((scannedCoupons / totalCoupons) * 100);

                    $("#coupons-bar").width(percent + "%");
                    $("#coupons-bar").text(percent + "%");
                    $("#txt-coupons-bar").text(scannedCoupons + "/" + totalCoupons);
                }
            },
            pageSize: 20,
            schema: {
                model: {
                    id: "id",
                    fields: {
                        id: { type: "number", editable: false, nullable: true },
                    }
                }
            },
        },
        //toolbar: [{ template: kendo.template($("#template-search-panel").html()) }],
        dataBound: function(e) {

            console.log(e);



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
            { command: { text: "", name: "actions", iconClass: "fas fa-ellipsis-v" }, title: " ", width: "30px" },
            {
                field: "barcode",
                title: "Código",
                width: "100px",
                filterable: false,
            },
            {
                field: "created_by",
                title: "Usuario",
                values: window.global_users,
                width: "180px",
                filterable: false,
            },
            {
                field: "coupons_count",
                title: "N° Cupones",
                width: "100px",
                filterable: false,
                template: function(dataItem) {

                    let color = dataItem.coupons_count == dataItem.escanned_coupons_count ? "success" : "danger";

                    return "<span class='font-weight-bold text-" + color + "'>" + dataItem.escanned_coupons_count + " / " + dataItem.coupons_count + "</span>"
                },
            },
            
            {
                field: "created_at",
                title: "Fecha registro",
                width: "100px",
                filterable: false,
                template: function(dataItem) {
                    return moment(dataItem.created_at).format('YYYY-MM-DD hh:mm A');
                },
            },
            { command: { text: "Detalles", click: showDetail }, title: " ", width: "55px" },
        ],
    }).data("kendoGrid");
}

function initContextMenu()
{
    $("#context-menu").kendoContextMenu({
        target: "#gridCouponSheet",
        filter: "td .k-grid-actions",
        showOn: "click",
        select: function(e) {
            var td = $(e.target).parent()[0];
            window.sheetSelected = gridCouponSheet.dataItem($(td).parent()[0]);

            switch (e.item.id)
            {
                case "edit":
                    $("#modal-update-sheet").modal("show");
                    $("#new-sheetcode").val(window.sheetSelected.barcode);
                    break;

                case "delete":
                    confirmDeleteSheet();
                    break;
            };
        }
    });
}

function deleteSheet()
{
    let request = callAjax("deleteSheet", 'POST', { "id": window.sheetSelected.id }, true);

    request.done(function(result) {

        if(result.success)
        {
            toastr.success("Acción completada");
            gridCouponSheet.dataSource.read();
        }

    }).fail(function(jqXHR, status) {
        $.unblockUI();
        toastr.error("Error");
    });
}

function confirmDeleteSheet()
{
    Swal.fire({
        title: 'Eliminar',
        text: "¿Eliminar hoja " + window.sheetSelected.barcode + "?",
        type: 'warning',
        buttonsStyling: true,
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Eliminar',
        confirmButtonClass: 'btn btn-primary',
        cancelButtonClass: 'btn btn-danger ml-1',
        cancelButtonText: 'Cancelar',
        buttonsStyling: false
    }).then(function(result) {
        if (result.value) deleteSheet();
    });
}

function createSheet()
{
    let request = callAjax('createSheet', 'POST', {"initial_code": $("#sheetcode").val()}, true);

    request.done(function(result) {

        if(result.success)
        {
            toastr.success("Acción completada");
            $("#modal-sheet").modal("hide");
            location.reload();
        }
        else
        {
            toastr.error(result.errors.initial_code.join());
        }

    }).fail(function(jqXHR, status) {
        $.unblockUI();
    });
}

function updateSheet()
{
    let request = callAjax("updateSheet", 'POST', { "id": window.sheetSelected.id, "barcode": $("#new-sheetcode").val() }, true);

    request.done(function(result) {

        if(result.success)
        {
            $("#modal-update-sheet").modal("hide");
            toastr.success("Acción completada");
            gridCouponSheet.dataSource.read();
        }
        else
        {
            toastr.error("Código duplicado");
        }

    }).fail(function(jqXHR, status) {
        $.unblockUI();
        toastr.error("Error");
    });
}

function closeSheet()
{
    let request = callAjax('closeSheet', 'POST', {}, true);

    request.done(function(result) {

        if(result.success)
        {
            toastr.success("Acción completada");
            location.reload();
        }
        else
        {
            toastr.warning("Problemas al finalizar el mes");
        }

    }).fail(function(jqXHR, status) {
        $.unblockUI();
    });
}

function showDetail(e)
{
    e.preventDefault();

    var dataItem = this.dataItem($(e.currentTarget).closest("tr"));

    getSheetDetail(dataItem.id);
}

function getSheetDetail(id)
{
    let request = callAjax('getSheetDetail', 'POST', {"id": id}, true);

    request.done(function(result) {
        $("#sheet-container").html(result);
        $("#modal-sheet-detail").modal("show");

    }).fail(function(jqXHR, status) {
        $.unblockUI();
    });
}

function getNextCouponSheet()
{
    let request = callAjax('getNextCouponSheet', 'POST', { }, true);

    request.done(function(result) {
        $("#next-sheet-code").val(result);

    }).fail(function(jqXHR, status) {
        $.unblockUI();
    });
}

function confirmCloseSheet()
{
    Swal.fire({
        title: 'Finalizar proceso',
        text: "¿Está seguro de finalizar el mes?",
        type: 'warning',
        buttonsStyling: true,
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Aceptar',
        confirmButtonClass: 'btn btn-primary',
        cancelButtonClass: 'btn btn-danger ml-1',
        cancelButtonText: 'Cancelar',
        buttonsStyling: false
    }).then(function(result) {
        if (result.value) closeSheet();
    });
}

function markCoupon() {

    if(idcoupon == null) return false;

    let data = { "idcoupon": idcoupon };
    let request = callAjax('markCouponToReady', 'POST', data, true);

    request.done(function(result) {

        code = "";

        $("#id-msg").removeClass('badge-success').removeClass('badge-danger');
        idcoupon = null;
        $("#id-msg").addClass(`badge badge-info`);
        $("#id-msg").html("Cupón marcado como listo");
        $("#btn-mark-coupon").blur();
        $('#couponCode').trigger('focus');

        $.unblockUI();

    }).fail(function(jqXHR, status) {
        $.unblockUI();
    });
}


function initEventInputCodeCoupon() {

    $('#couponCode').on('keypress', function(e) {

        if (e.keyCode == 13) {
            code = $("#couponCode").val();
            getCouponDetail();
        }

        clearTimeout(keypressTime);
        keypressTime = setTimeout(() => {

            let barCode = $("#couponCode").val();

            if (barCode.length >= 13) {
                code = $("#couponCode").val();
                getCouponDetail();
            } 
        }, 800);
    });
}

function getCouponDetail() {
    
    let data = { "barcode": code };
    let request = callAjax('scanCoupon', 'POST', data, true);

    request.done(function(result) {

        code = "";
        $("#id-msg").show();
        $("#modal-show-result").modal("show");
        $("#id-msg").html(result['msg']);
        idcoupon = result['idcoupon'];

        $("#id-msg").removeClass('badge-success').removeClass('badge-danger').removeClass('badge-info');
        let alertClass = result['success'] ? 'badge-success' : 'badge-danger'; 
        $("#id-msg").addClass(`badge ${alertClass}`);
        $("#couponCode").val('');

        $.unblockUI();

    }).fail(function(jqXHR, status) {
        code = "";
        $.unblockUI();
    });
}


// function initScanBarcode() {
    
//     $(document).on('keypress', function(e) {
//         if (e.keyCode == 13) {
//             if(code.length > 10) {

//                 reading = false;
//             }
//         } else {
//             code += e.key; 
//         }
//     });
// }