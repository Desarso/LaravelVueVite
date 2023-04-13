window.assetAction = "create";
window.assetSelected = null;

$(document).ready(function() {

    initDropDownListStatus();
    initDropDownListCategory()
    initGridAssets();
    initContextMenu();
    initInputImage();

    $(document).on("click", "#btn-new-config", function(event) {
        $("#div-asset-create-buttons").show();
        $("#div-asset-update-buttons").hide();
        $('#form-asset').trigger("reset");
        $("#title-modal-asset").text("Nuevo Activo");
        $("#modal-asset").modal("show");
    });

    $(document).on("click", ".btn-asset-qr", function(event) {
        window.assetSelected = gridAssets.dataSource.get($(this).data('idasset'));
        getAssetQR();
    });

    datePickerPurchase = $("#asset-purchase-date").kendoDatePicker().data("kendoDatePicker");

    textBoxCost = $("#asset-cost").kendoNumericTextBox({
        value: 0,
        format: "c",
        decimals: 3,
        min: 0
    }).data("kendoNumericTextBox");

    var validator = $("#form-asset").kendoValidator().data("kendoValidator");

    $("#form-asset").submit(function(event) {
        event.preventDefault();

        if(validator.validate())
        {
            let data = $("#form-asset").serializeFormJSON();

            updateOrCreateAsset();
        }
    });

});

function initInputImage(url = null, config = null)
{
    let initialPreview = (url == null ? [] : [url]);
    let initialPreviewConfig = (url == null ? [] : [config]);

    inputFile = $("#image").fileinput('destroy').fileinput({
        initialPreview: initialPreview,
        initialPreviewAsData: true,
        initialPreviewConfig: initialPreviewConfig,
        theme: 'fa',
        language: 'es',
        uploadUrl: '#',
        actionUpload: false,
        showRemove: false,
        showUpload: false,
        showUploadedThumbs: false,
        dropZoneEnabled: true,
        overwriteInitial: true,
        mainClass: "input-group-md",
        allowedFileExtensions: ['jpg', 'jpeg', 'png', 'gif'],
        fileActionSettings: { 
            showUpload: false,
            showRemove: false, 
        }
    });
}

function updateOrCreateAsset()
{
    var formData = new FormData();
    var files = $('#image')[0].files;

    $.each( $("#form-asset").serializeFormJSON(), function( key, value ) {
        formData.append(key, value);
    });

    formData.append("hasPreview", (inputFile.fileinput('getPreview', true).config.length != 0 ? true : false));

    if(files.length > 0) formData.append('file', files[0]);

    $.ajax({
        url: "updateOrCreateAsset",
        headers: { 'X-CSRF-TOKEN': $("input[name=_token]").val() },
        type: 'POST',
        datatype: 'json',
        contentType: false,
        processData: false,
        data: formData,
        beforeSend: function (){
            $.blockUI({ message: '<h1>Procesando...</h1>' });
        },
        success: function (result) {

            $.unblockUI();

            if(result.success)
            {
                toastr.success("Activo " + result.model.name + " editado");
                $("#modal-asset").modal("hide");
                $('#form-asset').trigger("reset");
                gridAssets.dataSource.read();
            }
            else
            {
                toastr.warning(result.errors[0]);
            }

        },
        error: function (data) {
            $.unblockUI();
            alert("Problema al crear activo");
        }
    });
}

function initDropDownListStatus()
{
    dropDownListStatus= $("#dropDownListStatus").kendoDropDownList({
        //optionLabel: locale('Select'),
        dataTextField: "text",
        dataValueField: "value",
        filter: "contains",
        dataSource: statuses,
        popup: { appendTo: $("#modal-asset") },
        height: 400
    }).data("kendoDropDownList");
}

function initDropDownListCategory()
{
    dropDownListCategory= $("#dropDownListCategory").kendoDropDownList({
        //optionLabel: locale('Select'),
        dataTextField: "text",
        dataValueField: "value",
        filter: "contains",
        dataSource: categories,
        popup: { appendTo: $("#modal-asset") },
        height: 400
    }).data("kendoDropDownList");
}

function initGridAssets()
{
    gridAssets = $("#gridAssets").kendoGrid({
        dataSource: {
            transport: {
                read: {
                    url: "getAssets",
                    type: "get",
                    dataType: "json"
                }
            },
            pageSize: 20,
            schema: {
                model: {
                    id: "id",
                    fields: {
                        id: { type: "number", editable: false, nullable: true },
                        isloaned: { type: "boolean" }
                    }
                }
            },
        },
        editable: false,
        toolbar: [{ template: kendo.template($("#template-search-panel").html()) }],
        reorderable: true,
        resizable: true,
        sortable: true,
        pageable: {
            refresh: true,
            pageSizes: true,
            buttonCount: 5
        },
        height: '700px',
        filterable: true,
        dataBound: function(e) {

        },
        columns: [
            {
                field: "idstatus",
                title: "Estado",
                template: function(dataItem) {
                    let status = statuses.find(o => o.value === parseInt(dataItem.idstatus));

                    return "<div class='badge badge-primary badge-md' style='background-color:" + status.color + " !important'>" + status.text + "</div>";
                },
                width: "110px",
                values: statuses,
                filterable: {
                   multi: true,
                   search: true
               }
            },
            /*
            {
                field: "code",
                title: "Código",
                width: "100px",
                filterable: false
            },
            */
            {
                field: "name",
                title: locale("Name"),
                template: function(dataItem){
                    let photo = (dataItem.photo == null ? "https://dingdonecdn.nyc3.digitaloceanspaces.com/demov2/tickets/2Xa1MQA0ROssaLcfCkksHlTALSkDKEpdlDtx6rYP.png" : dataItem.photo);

                    return "<img class='asset-image' src='" + photo + "' style='max-height: 60px;' alt='Img placeholder'> <div class='pt-1' style='display: inline-block;'><spam style='font-weight: 500;'>" + dataItem.name + "</spam> <br> <spam class='asset-code'>" + dataItem.code + "</spam></div>";
                },
                width: "250px",
                filterable: false
            },
            {
                field: "model",
                title: "Modelo",
                width: "100px",
                filterable: false
            },
            {
                field: "idcategory",
                title: "Categoría",
                values: categories,
                width: "200px",
                filterable: {
                   multi: true,
                   search: true
               }
            },
            {
                field: "isloaned",
                title: "Disponibilidad",
                width: "100px",
                template: function(dataItem) {
                    return (dataItem.isloaned == true ? "<i class='fa fa-circle font-small-3 text-danger mr-50'></i> Prestado" : "<i class='fa fa-circle font-small-3 text-success mr-50'></i> Disponible");
                },
                filterable: {
                    ui: function(element) {
                      element.kendoDropDownList({
                        dataTextField: 'text',
                        dataValueField: 'value',
                        dataSource: [{ text: 'Disponible', value: false }, { text: 'Prestado', value: true }]
                      })
                    }
                }
            },
            {
                field: "model",
                title: " ",
                template: function(dataItem) {
                    return "<button type='button' data-idasset='" + dataItem.id + "' class='btn bg-gradient-success waves-effect waves-light btn-asset-qr'><i class='fas fa-qrcode'></i> Generar QR</button>";
                },
                width: "115px",
                filterable: false
            },
            { command: { text: "", name: "actions", iconClass: "fas fa-ellipsis-v" }, title: " ", width: "40px" }
        ],
    }).data("kendoGrid");

    setTimeout(() => { $("#btn-new-config").text("Nuevo Activo") }, 100);
}

function initContextMenu()
{
    $("#context-menu").kendoContextMenu({
        target: "#gridAssets",
        filter: "td .k-grid-actions",
        showOn: "click",
        select: function(e) {
            var td = $(e.target).parent()[0];
            window.assetSelected = gridAssets.dataItem($(td).parent()[0]);

            switch (e.item.id) {
                case "editAsset":
                    setAsset();
                    break;
                case "deleteAsset":
                    confirmDeleteAsset();
                    break;
            };
        }
    });
}

function setAsset()
{
    $("#div-asset-update-buttons").show();
    $("#div-asset-create-buttons").hide();

    $('#form-asset').trigger("reset");

    $("#title-modal-asset").text("Editar Activo");
    $("#modal-asset").modal("show");

    $("#txt-asset-id").val(window.assetSelected.id);
    $("#txt-asset-name").val(window.assetSelected.name);
    $("#txt-asset-code").val(window.assetSelected.code);
    $("#txt-asset-model").val(window.assetSelected.model);

    textBoxCost.value(window.assetSelected.cost);
    datePickerPurchase.value((window.assetSelected.purchase_date == null ? null : new Date(window.assetSelected.purchase_date)));

    setTimeout(() => {
        dropDownListCategory.value(window.assetSelected.idcategory);
        dropDownListStatus.value(window.assetSelected.idstatus);
    }, 100);

    initInputImage(window.assetSelected.photo, {caption: "Activo.jpg", downloadUrl: window.assetSelected.photo, width: "120px", key: 1});
}

$("#btn-create-asset").click(function() {
    window.assetAction = "create";
    $("#form-asset").submit();
});

$("#btn-update-asset").click(function() {
    window.assetAction = "update";
    $("#form-asset").submit();
});

function confirmDeleteAsset()
{
    Swal.fire({
        title: 'Eliminar',
        text: "¿Eliminar activo " + window.assetSelected.name + "?",
        type: 'warning',
        buttonsStyling: true,
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Eliminar',
        confirmButtonClass: 'btn bg-gradient-primary',
        cancelButtonClass: 'btn bg-gradient-danger ml-1',
        cancelButtonText: 'Cancelar',
        buttonsStyling: false
    }).then(function(result) {
        if (result.value) deleteAsset();
    });
}

function deleteAsset()
{
    let request = callAjax("deleteAsset", 'POST', { "id": window.assetSelected.id }, true);

    request.done(function(result) {

        if(result.success)
        {
            toastr.success("Activo " + result.model.name + " eliminado <button type='button' class='btn btn-light btn-sm' onclick='restoreAsset()'>DESHACER</button>");
            gridAssets.dataSource.read();
        }
        else
        {
            toastr.warning("Activo " + result.model.name + " tiene préstamos activas");
        }

    }).fail(function(jqXHR, status) {
        $.unblockUI();
        toastr.error("Error");
    });
}

function getAssetQR()
{
    let request = callAjax("getAssetQR", 'POST', {'id' : window.assetSelected.id}, true);

    request.done(function(result) {

        $("#qr-content").html(result);
        $("#modal-asset-qr").modal("show");

    }).fail(function(jqXHR, status) {
        $.unblockUI();
        toastr.error("Error");
    });
}

$("#btn-download-qr").click(function() {
    generatePdf();
});

function generatePdf()
{
    $.blockUI({ message: '<h1>Descargando...</h1>' });

    $.ajax({
        type: 'POST',
        url: 'downloadAssetQR',
        data: {"id" : window.assetSelected.id},
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        xhrFields: {
            responseType: 'blob'
        },
        success: function(response){
            $.unblockUI();
            var blob = new Blob([response]);
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = window.assetSelected.name + ".pdf";
            link.click();
        },
        error: function(blob){
            $.unblockUI();
            toastr.error('La acción no se puedo completar', '¡Hubo un problema!');
            console.log(blob);
        }
    });
}