var timer;
window.loanSelected  = null;
window.assetSelected = null;
window.lastAssetLoanChange = null;

$(document).ready(function() {
    initDropDownListStatusFilter();
    initDropDownListAssetFilter();
    initDropDownListUserFilter();

    initDropDownListAsset();
    initDropDownListUser();
    initDuedateDatePicker();
    
    initGridAssetLoan();
    initContextMenu();

    initDropDownListUserReturned();

    $('#modal-asset-loan').on('hide.bs.modal', function(e) {
        $("#form-loan").trigger("reset");
    });

    $(document).on("click", ".switch-status", function(event) {
        
        window.loanSelected = $(this).data('loan');
        window.assetSelected = global_assets.find(o => o.value === parseInt(window.loanSelected.idasset));

        $("#loan-id").val(window.loanSelected.id);
        $("#asset-code").text(window.assetSelected.code);
        $("#asset-name").text(window.assetSelected.text);
        $("#asset-photo").attr('src', window.assetSelected.photo);

        dropDownListUserReturned.value(window.loanSelected.iduser);

        $("#modal-asset-loan-status").modal("show");

        event.preventDefault();
        return false;
    });

    $(document).on("click", ".btn-note", function(event) {
        window.loanSelected = gridAssetLoan.dataSource.get($(this).data('id'));
        $("#modal-asset-loan-notes").modal("show");
        gridAssetLoanNotes.dataSource.read();
    });

    setInterval(() => { getLastAssetLoanChange(); }, 10000);

    setInterval(() => { dropDownListAsset.dataSource.read() }, 10000);

    $("#search").keyup(function() {

        clearTimeout(timer);
    
        var ms = 300; // milliseconds
    
        timer = setTimeout(function() {
            gridAssetLoan.dataSource.read();
        }, ms);
    });

    $("#switch-duedate-filter").change(function() {
        gridAssetLoan.dataSource.read();
    });

});

$("#btn-asset-loan").click(function(e) {
    $("#modal-asset-loan").modal("show");
    $("#div-loan-create-buttons").show();
    $("#div-loan-update-buttons").hide();
});

function initDropDownListStatusFilter()
{
    dropDownListStatusFilter = $("#dropDownListStatusFilter").kendoDropDownList({
        optionLabel: "-- Seleccione --",
        dataTextField: "text",
        dataValueField: "value",
        dataSource: [{"value": "OPEN", "text": "OPEN"}, {"value": "CLOSE", "text": "CLOSE"}],
        height: 400,
        change: changeFilter
    }).data("kendoDropDownList");
}

function initDropDownListAssetFilter()
{
    dropDownListAssetFilter = $("#dropDownListAssetFilter").kendoDropDownList({
        optionLabel: "-- Seleccione --",
        dataTextField: "text",
        dataValueField: "value",
        filter: "contains",
        height: 400,
        template: $("#script-template-asset-filter").html(),
        height: 500,
        dataSource: window.global_assets,
        change: changeFilter,
        filtering: function(ev) {
            var filterValue = ev.filter != undefined ? ev.filter.value : "";
            ev.preventDefault();

          this.dataSource.filter({
            logic: "or",
            filters: [
              {
                field: "text",
                operator: "contains",
                value: filterValue
              },
              {
                field: "code",
                operator: "contains",
                value: filterValue
              }
            ]
          });
        }
    }).data("kendoDropDownList");
}

function initDropDownListUserFilter()
{
    dropDownListUserFilter = $("#dropDownListUserFilter").kendoDropDownList({
        optionLabel: "-- Seleccione --",
        dataTextField: "text",
        dataValueField: "value",
        filter: "contains",
        dataSource: window.global_users,
        height: 400,
        change: changeFilter
    }).data("kendoDropDownList");
}

function changeFilter()
{
    gridAssetLoan.dataSource.read();
}

function initDropDownListAsset()
{
    dropDownListAsset = $("#dropDownListAsset").kendoDropDownList({
        optionLabel: "-- Seleccione --",
        dataTextField: "text",
        dataValueField: "value",
        filter: "contains",
        template: $("#script-template-asset").html(),
        popup: { appendTo: $("#modal-asset-loan") },
        height: 500,
        dataSource: {
            transport: { read: "getListAssets" }
        },
        select: function(e) {
            toastr.clear();

            if(e.dataItem.isloaned)
            {
                e.preventDefault();
                toastr.warning("Activo no disponible");
            }
        },
        filtering: function(ev) {
            var filterValue = ev.filter != undefined ? ev.filter.value : "";
            ev.preventDefault();

          this.dataSource.filter({
            logic: "or",
            filters: [
              {
                field: "text",
                operator: "contains",
                value: filterValue
              },
              {
                field: "code",
                operator: "contains",
                value: filterValue
              }
            ]
          });
        }
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
        popup: { appendTo: $("#modal-asset-loan") },
        height: 400,
    }).data("kendoDropDownList");
}

function initDropDownListUserReturned()
{
    dropDownListUserReturned = $("#dropDownListUserReturned").kendoDropDownList({
        optionLabel: "-- Seleccione --",
        dataTextField: "text",
        dataValueField: "value",
        filter: "contains",
        dataSource: window.global_users,
        popup: { appendTo: $("#modal-asset-loan-status") },
        height: 400,
    }).data("kendoDropDownList");
}

function initDuedateDatePicker()
{
    duedateDatePicker = $("#duedateDatePicker").kendoDatePicker().data("kendoDatePicker");
}

function initGridAssetLoan()
{
    gridAssetLoan = $("#gridAssetLoan").kendoGrid({
        dataSource: {
            transport: {
                read: {
                    url: "getDataAssetLoan",
                    type: "get",
                    dataType: "json",
                    data: function() {
                        return {
                            search  : $('#search').val(),
                            status  : dropDownListStatusFilter.value(),
                            idasset : dropDownListAssetFilter.value(),
                            iduser  : dropDownListUserFilter.value(),
                            overdue : ($("#switch-duedate-filter").is(":checked") ? true : false)
                        };
                    },
                }
            },
            pageSize: 20,
            schema: {
                model: {
                    id: "id",
                    fields: {
                        id: { type: "number", editable: false, nullable: true },
                    }
                },
                total: "total",
                data: "data"
            },
            pageSize: 20,
            serverPaging: true,
            serverFiltering: true,
            serverSorting: true
        },
        editable: false,
        toolbar: [],
        reorderable: true,
        resizable: true,
        sortable: true,
        pageable: {
            refresh: true,
            pageSizes: true,
            buttonCount: 5
        },
        noRecords: {
            template: "<div class='alert alert-danger mt-1 alert-validation-msg' role='alert'><i class='feather icon-info mr-1 align-middle'></i><span>No hay préstamos registrados</span></div>"
        },
        height: '700px',
        filterable: true,
        dataBound: function(e) {
            var countLoansDue = 0;
            var data = this._data;
            var tableRows = $(this.table).find("tr");
            tableRows.each(function(index) {
                var row = $(this);
                var dataRow = data[index];
                if(dataRow.status == "CLOSE")
                {
                    row.css("background-color", "rgba(33,37,41,.065)");
                    row.css("color", "lightslategray");
                }
                else if(moment().isAfter(moment(dataRow.duedate)))
                {
                    row.find("td:eq(4)").css("color", "#ea5455 ");
                    countLoansDue++;
                }
            });

            if(countLoansDue > 0)
            {
                $("#count-loans-due").text(countLoansDue + (countLoansDue == 1 ? " préstamo se ecuentran vencido" : " préstamos se ecuentran vencidos"));
                $('#alert-danger-count-loans').show();
            }
            else
            {
                $('#alert-danger-count-loans').hide();
            }
        },
        columns: [
            {
                field: "status",
                title: "Estado",
                template: function(dataItem) {

                    let disabled = dataItem.status == "CLOSE" ? "disabled" : "";

                    return "<div class='custom-control switch-lg custom-switch custom-switch-success'>" +
                                "<input id='status" + dataItem.id + "' data-loan='"+ JSON.stringify(dataItem) +"' type='checkbox' data-id='" + dataItem.id + "' class='switch-status custom-control-input' " + (dataItem.status == "OPEN" ? "checked" : "") + " " + disabled + ">" +
                                "<label class='custom-control-label' for='status" + dataItem.id + "'>" +
                                "<span class='switch-text-left'>Abierto</span>" +
                                "<span class='switch-text-right'>Cerrado</span>" +
                                "</label>" +
                            "</div>";
                },
                width: "60px",
                filterable: false
            },
            {
                field: "id",
                title: "CÓDIGO",
                width: "60px",
                filterable: false
            },
            {
                field: "idasset",
                title: "Activo",
                template: function(dataItem){

                    let asset = global_assets.find(o => o.value === parseInt(dataItem.idasset));

                    let photo = (asset.photo == null ? "https://dingdonecdn.nyc3.digitaloceanspaces.com/demov2/tickets/2Xa1MQA0ROssaLcfCkksHlTALSkDKEpdlDtx6rYP.png" : asset.photo);

                    return "<img class='asset-image' src='"+ photo + "' alt='Imagen de activo'> <div class='pt-1' style='display: inline-block;'><spam style='font-weight: 500;'>" + asset.text + "</spam> <br> <spam class='asset-code'>" + asset.code + "</spam></div>";
                },
                width: "280px",
                filterable: false
            },
            {
                field: "iduser",
                title: "Usuario",
                values: window.global_users,
                width: "250px",
                filterable: false
            },
            {
                field: "duedate",
                title: "Fecha Vencimiento",
                template: function(dataItem) {
                    if(dataItem.duedate == null) return '';
                    return moment(dataItem.duedate).format('YY-MM-DD hh:mm A');
                },
                width: "120px",
                filterable: false
            },
            {
                field: "created_at",
                title: "Fecha Registro",
                template: function(dataItem) {
                    return moment(dataItem.created_at).format('YY-MM-DD hh:mm A');
                },
                width: "120px",
                filterable: false
            },
            {
               field: "notes_count",
               title: "Notas",
               template: function(dataItem) {

                   let notes_count = dataItem.notes_count == 0 ? "" : dataItem.notes_count;
                   let color = dataItem.status == "CLOSE" ? "secondary" : "success";

                   return "<div class='position-relative d-inline-block'>" +
                            "<i data-id='" + dataItem.id + "' class='btn-note feather icon-message-square font-medium-5 text-" + color + "'></i>" +
                            "<span class='badge badge-pill badge-" + color + " badge-glow badge-up'>" + notes_count + "</span>" +
                          "</div>";
               },
               width: "60px",
               filterable: false
            },
            { command: { text: "", name: "actions", iconClass: "fas fa-ellipsis-v" }, title: " ", width: "70px" }
        ],
    }).data("kendoGrid");
}

function initContextMenu()
{
    $("#context-menu").kendoContextMenu({
        target: "#gridAssetLoan",
        filter: "td .k-grid-actions",
        showOn: "click",
        select: function(e) {

            var td = $(e.target).parent()[0];
            window.loanSelected = gridAssetLoan.dataItem($(td).parent()[0]);
            
            switch(e.item.id)
            {
                case "editLoan":
                    setLoan();
                    break;

                case "deleteLoan":
                    confirmDeleteLoan();
                    break;

                case "detailLoan":
                    getAssetLoanDetail();
                    break;
            };
        }
    });
}

function setLoan()
{
    $("#div-loan-update-buttons").show();
    $("#div-loan-create-buttons").hide();

    $('#form-loan').trigger("reset");

    $("#modal-asset-loan").modal("show");

    $("#txt-loan-id").val(window.loanSelected.id);
    $("#comment").val(window.loanSelected.comment);

    duedateDatePicker.value((window.loanSelected.duedate == null ? null : new Date(window.loanSelected.duedate)));

    setTimeout(() => {
        dropDownListAsset.value(window.loanSelected.idasset);
        dropDownListUser.value(window.loanSelected.iduser);
    }, 50);
}

$("#btn-create-loan").click(function(e) {

    var data = $("#form-loan").serializeArray();

    if (!validateLoan(data)) return;

    createAssetLoan(data);
});

$("#btn-update-loan").click(function(e) {

    var data = $("#form-loan").serializeArray();

    if (!validateLoan(data)) return;

    updateAssetLoan(data);
});

function validateLoan(data)
{
    if (dropDownListAsset.value() == "" || dropDownListUser.value() == "" )
    {
        toastr.clear();
        toastr.warning("Datos incompletos");
        return false;
    }

    return true;
}

function createAssetLoan(data)
{
    let request = callAjax('createAssetLoan', 'POST', data, true);

    request.done(function(result) {

        if(result.success)
        {
            $("#modal-asset-loan").modal("hide");
            gridAssetLoan.dataSource.read();
            $.unblockUI();
        }
        else
        {
            toastr.warning("El activo no está disponible");
        }

    }).fail(function(jqXHR, status) {
        $.unblockUI();
        console.log('error!');
    });
}

function updateAssetLoan(data)
{
    let request = callAjax('updateAssetLoan', 'POST', data, true);

    request.done(function(result) {

        $("#modal-asset-loan").modal("hide");
        gridAssetLoan.dataSource.read();
        $.unblockUI();

    }).fail(function(jqXHR, status) {
        $.unblockUI();
        console.log('error!');
    });
}

function confirmDeleteLoan()
{
    Swal.fire({
        title: 'Eliminar',
        text: "¿Eliminar préstamo " + global_assets.find(o => o.value === parseInt(window.loanSelected.idasset)).text + "?",
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
        if (result.value) deleteAssetLoan();
    });
}

function deleteAssetLoan()
{
    let request = callAjax("deleteAssetLoan", 'POST', { "id": window.loanSelected.id }, true);

    request.done(function(result) {

        if(result.success)
        {
            toastr.success("Préstamo eliminado <button type='button' class='btn btn-light btn-sm' onclick='restoreAssetLoan()'>DESHACER</button>");
            gridAssetLoan.dataSource.read();
        }

    }).fail(function(jqXHR, status) {
        $.unblockUI();
        toastr.error("Error");
    });
}

$("#btn-change-loan-status").click(function(e) {
    changeAssetLoanStatus();
});

function changeAssetLoanStatus()
{
    let data = {'id' : window.loanSelected.id, 'iduser_returned' : dropDownListUserReturned.value()};

    let request = callAjax('changeAssetLoanStatus', 'POST', data, true);

    request.done(function(result) {

        $("#modal-asset-loan-status").modal("hide");
        gridAssetLoan.dataSource.read();
        $.unblockUI();

    }).fail(function(jqXHR, status) {
        $.unblockUI();
        console.log('error!');
    });
}

function getAssetLoanDetail()
{
    let request = callAjax('getAssetLoanDetail', 'POST', {'id' : window.loanSelected.id}, true);

    request.done(function(result) {
        
        $("#modal-asset-loan-details-title").html("Préstamo #" + window.loanSelected.id);
        $("#aseet-loan-details").html(result);
        $("#modal-asset-loan-details").modal("show");
        $.unblockUI();

    }).fail(function(jqXHR, status) {
        $.unblockUI();
        console.log('error!');
    });
}

function getLastAssetLoanChange()
{
    let request = callAjax('getLastAssetLoanChange', 'POST', null, false);

    request.done(function(result) {

        if(window.lastAssetLoanChange == null)
        {
            window.lastAssetLoanChange = result;
        }
        else if(result !== window.lastAssetLoanChange)
        {
            window.lastAssetLoanChange = result;
            gridAssetLoan.dataSource.read();
        }
        
    }).fail(function(jqXHR, status) {

    });
}