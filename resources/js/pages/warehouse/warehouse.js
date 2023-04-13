window.lastWarehouse = null;
window.selectedWarehouse = null;
shouldPreventClose = false;

$(document).ready(function() {

    //Datepicker
    initDateRangePicker();

    //DropDownList
    initDropDownListSpotFilter();
    initDropDownListStatusFilter();
    initDropDownListItemFilter();
    initDropDownListUserFilter();
    initDropDownListSupplieFilter();
    initDropDownListSpotWarehouse();
    initDropDownListPriorityWarehouse();
    initDropDownListItem();
    initDropDownListSupplier();

    //Windows
    initWindowStatusWarehouse();
    initWindowNotesWarehouse();

    //Grids
    initGridWarehouse();
    initGridRequestWarehouse();

    //Get last warehouse changes
    setInterval(() => { getLastWarehouse() }, 10000);

    //Events

    $(document).on("click", ".btn-next-status", function(event) {
        windowStatusWarehouse.close();
        changeStatusWarehouse($(this).data("idstatus"));
    });

    $(document).on("click", ".btn-status", function(event) {

        let idwarehouse = $(this).data("id");

        window.selectedWarehouse = gridWarehouse.dataSource.get(idwarehouse);

        windowStatusWarehouse.center().open();
        windowStatusWarehouse.title(window.selectedWarehouse.item.name);
        windowStatusWarehouse.refresh({
            url: "nextWarehouseStatus",
            data: { id: idwarehouse },
        });
    });

    $(document).on("click", ".btn-note", function(event){

        let idwarehouse = $(this).data("id");

        window.selectedWarehouse = gridWarehouse.dataSource.get(idwarehouse);

        windowNotesWarehouse.title(window.selectedWarehouse.item.name);
        windowNotesWarehouse.refresh({
            url: "getWarehouseNotes",
            data: { id: idwarehouse },
        });
        windowNotesWarehouse.center().open();
    });

    $(document).on("click", "#btn-add-note", function(event){

        if($("#note").val() == "")
        {
            toastr.error("Escriba una nota", 'Advertencia');
            return false;
        }

        $(this).prop("disabled", true);
        createNoteWarehouse();
    });

    $("#cost").kendoNumericTextBox({
        restrictDecimals: true
    });

    $("#amount").kendoNumericTextBox({
        min: 1
    });
});

$("#btn-new-warehouse").click(function () {
    
    $("#modal-warehouse").modal("show");

    setTimeout(() => {
        dropDownListItem.value(null);
        dropDownListPriority.value(1);
        dropDownListSpot.select(0);
        dropDownListSpot.trigger("change");
    }, 300);

});

$("#btn-add").click(function () {
    addRow();
});

function checkVisibility(column)
{
    return ($.inArray(column, warehouse_settings.hide_columns) != -1 ? true : false);
}

function getLastWarehouse()
{
    var request = callAjax("getLastWarehouse", 'POST', {});

    request.done(function(data) {

        if (window.lastWarehouse == null) {
            window.lastWarehouse = data;
        } else if (data !== window.lastWarehouse) {
            window.lastWarehouse = data;
            gridWarehouse.dataSource.read();
        }
    });
}

function changeStatusWarehouse(idstatus)
{
    $.blockUI({ message: '<h1>Procesando...</h1>' });

    var request = callAjax("changeStatusWarehouse", 'POST', { "id": window.selectedWarehouse.id, "idstatus": idstatus });

    request.done(function(data) {
        $.unblockUI();
        gridWarehouse.dataSource.read();
    });
}

function createNoteWarehouse()
{
    $.blockUI({ message: '<h1>Procesando...</h1>' });

    var request = callAjax("createNoteWarehouse", 'POST', { "idwarehouse": window.selectedWarehouse.id, "note": $("#note").val() });

    request.done(function(data) {

        $.unblockUI();

        $("#btn-add-note").prop("disabled", false);

        $("#note").val("");

        gridWarehouse.dataSource.read();

        windowNotesWarehouse.refresh({
            url: "getWarehouseNotes",
            data: { id: window.selectedWarehouse.id },
        });
    });
}

function initDropDownListItem()
{
    dropDownListItem = $("#dropDownListItem").kendoDropDownList({
        optionLabel: {
            name: "-- Seleccione --",
            id: null
        },
        dataTextField: "name",
        dataValueField: "id",
        filter: "contains",
        popup: { appendTo: $("#modal-warehouse") },
        height: 520,
        virtual: {
            itemHeight: 26,
            valueMapper: function(options) {
                $.ajax({
                    url: "getValueMapper",
                    type: "GET",
                    dataType: "json",
                    data: convertValues(options.value),
                    success: function (data) {
                        options.success(data);
                    }
                });
            }
        },
        dataSource: {
            transport: {
                read: "getAllWarehouseItems",
            },
            schema: {
                    total: "total",
                    data: "data",
                    model: {
                        fields: {
                            id: { type: "number" },
                            name: { type: "string" },
                        }
                    }
            },
            pageSize: 80,
            serverPaging: true,
            serverFiltering: true
        }
    }).data("kendoDropDownList");
}

function convertValues(value) {
    var data = {};

    value = $.isArray(value) ? value : [value];

    for (var idx = 0; idx < value.length; idx++) {
        data["values[" + idx + "]"] = value[idx];
    }

    return data;
}

function initDropDownListSpotWarehouse()
{
    dropDownListSpot = $("#dropDownListSpot").kendoDropDownList({
        dataTextField: "text",
        dataValueField: "value",
        dataSource: getUserBranches(),
        filter: "contains",
        index: 0,
        popup: { appendTo: $("#modal-warehouse") },
        close: onClose
    }).data("kendoDropDownList");

    dropDownListSpot.wrapper.on("focus", function(e) {
        shouldPreventClose = true;
        setTimeout(function(){ shouldPreventClose = false }, 200)
        dropDownListSpot.open();
    });
}

function initDropDownListSpotFilter()
{
    dropDownListSpotFilter = $("#dropDownListSpotFilter").kendoDropDownList({
        optionLabel: "-- Seleccione --",
        dataTextField: "text",
        dataValueField: "value",
        dataSource: getUserBranches(),
        filter: "contains",
        change: changeFilter
    }).data("kendoDropDownList");
}

function initDropDownListStatusFilter()
{
    dropDownListStatusFilter = $("#dropDownListStatusFilter").kendoDropDownList({
        optionLabel: "-- Seleccione --",
        dataTextField: "text",
        dataValueField: "value",
        dataSource: warehouse_statuses,
        change: changeFilter
    }).data("kendoDropDownList");  
}

function initDropDownListItemFilter()
{
    dropDownListItemFilter = $("#dropDownListItemFilter").kendoDropDownList({
        optionLabel: {
            name: "-- Seleccione --",
            id: null
        },
        dataTextField: "name",
        dataValueField: "id",
        filter: "contains",
        change: changeFilter,
        height: 520,
        virtual: {
            itemHeight: 26,
            valueMapper: function(options) {
                $.ajax({
                    url: "getValueMapper",
                    type: "GET",
                    dataType: "json",
                    data: convertValues(options.value),
                    success: function (data) {
                        options.success(data);
                    }
                });
            }
        },
        dataSource: {
            transport: {
                read: "getAllWarehouseItems",
            },
            schema: {
                    total: "total",
                    data: "data",
                    model: {
                        fields: {
                            id: { type: "number" },
                            name: { type: "string" },
                        }
                    }
            },
            pageSize: 80,
            serverPaging: true,
            serverFiltering: true
        }
    }).data("kendoDropDownList");

    dropDownListItemFilter.value(null);
}

function initDropDownListUserFilter()
{
    dropDownListUserFilter = $("#dropDownListUserFilter").kendoDropDownList({
        optionLabel: "-- Seleccione --",
        dataTextField: "text",
        dataValueField: "value",
        dataSource: global_users,
        filter: "contains",
        change: changeFilter
    }).data("kendoDropDownList");   
}

function initDropDownListSupplieFilter()
{
    dropDownListSupplierFilter = $("#dropDownListSupplierFilter").kendoDropDownList({
        optionLabel: "-- Seleccione --",
        dataTextField: "text",
        dataValueField: "value",
        dataSource: warehouse_suppliers,
        filter: "contains",
        change: changeFilter
    }).data("kendoDropDownList");   
}

function initDropDownListSupplier()
{
    dropDownListSupplier = $("#dropDownListSupplier").kendoDropDownList({
        optionLabel: "-- Seleccione --",
        dataTextField: "text",
        dataValueField: "value",
        dataSource: warehouse_suppliers,
        popup: { appendTo: $("#modal-warehouse") },
        filter: "contains",
        close: onClose
    }).data("kendoDropDownList");   

    dropDownListSupplier.wrapper.on("focus", function(e) {
      shouldPreventClose = true;
      setTimeout(function(){ shouldPreventClose = false }, 200)
      dropDownListSupplier.open();
    });
}

function initDropDownListPriorityWarehouse()
{
    dropDownListPriority = $("#dropDownListPriority").kendoDropDownList({
        dataTextField: "text",
        dataValueField: "value",
        dataSource: window.global_priorities,
        height: 400,
        index: 1,
        close: onClose
    }).data("kendoDropDownList");

    dropDownListPriority.wrapper.on("focus", function(e) {
        shouldPreventClose = true;
        setTimeout(function(){ shouldPreventClose = false }, 200)
        dropDownListPriority.open();
    });
}

function changeFilter()
{
    gridWarehouse.dataSource.read();
}

function onClose(e)
{
    if (shouldPreventClose) {
      shouldPreventClose = false;
      e.preventDefault();
    }
}

function initWindowStatusWarehouse()
{
    windowStatusWarehouse = $("#window-warehouse-status").kendoWindow({
        width: "30%",
        height: "20%",
        modal: true,
        title: "Cambio de estado",
        resizable: true,
        visible: false
    }).data("kendoWindow"); 
}

function initWindowNotesWarehouse()
{
    windowNotesWarehouse = $("#window-warehouse-notes").kendoWindow({
        width: "450px",
        height: "500px",
        modal: true,
        title: "Notas",
        resizable: true,
        scrollable: false,
        visible: false
    }).data("kendoWindow"); 
}

function initGridWarehouse()
{
    gridWarehouse = $("#gridWarehouse").kendoGrid({
        dataSource: {
            transport: {
                read: {
                    url: "getWarehouses",
                    type: "get",
                    dataType: "json",
                    data: function() {
                        return {
                            start      : $('#dateRangePicker').data('daterangepicker').startDate.format('YYYY-MM-DD'),
                            end        : $('#dateRangePicker').data('daterangepicker').endDate.format('YYYY-MM-DD'),
                            idspot     : dropDownListSpotFilter.value(),
                            iduser     : dropDownListUserFilter.value(),
                            idstatus   : dropDownListStatusFilter.value(),
                            iditem     : dropDownListItemFilter.value(),
                            idsupplier : dropDownListSupplierFilter.value()
                        };
                    }
                },
                update: {
                    url: "updateWarehouse",
                    type: "post",
                    dataType: "json",
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
                    }
                },
                destroy: {
                    url: "deleteWarehouse",
                    type: "post",
                    dataType: "json",
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
                    }
                }
            },
            schema: {
                model: {
                    id: "id",
                    fields: {
                        id: { type: "number", editable: false, nullable: true },
                        ot: { editable: true, field: "ot", type: "text" },
                        idstatus: { editable: true, field: "idstatus", type: "number" },
                        iditem: { editable: true, field: "iditem", type: "number" },
                        amount: { editable: true, field: "amount", type: "number" },
                        idspot: { editable: true, field: "idspot", type: "number" },
                        idsupplier: { editable: true, field: "idsupplier", type: "number" },
                        description: { editable: true, field: "description", type: "text" },
                        idpriority: {editable: true, type: "number", field: "idpriority"},
                        iduser: { editable: false, field: "iduser", type: "number" },
                        created_at: { editable: false }
                    }
                },
                total: "total",
                data: "data",
            },
            requestEnd: function(e) {},
            pageSize: 100,
            serverPaging: true,
            serverFiltering: true,
            serverSorting: true
        },
        sortable: true,
        height: 600,
        selectable: true,
        editable: "popup",
        pageable: {
            refresh: true,
            pageSizes: true,
            buttonCount: 5
        },
        resizable: true,
        reorderable: true,
        filterable: true,
        noRecords: {
            template: "<div style='width:100%' class='alert alert-danger mt-1 alert-validation-msg' role='alert'><i class='feather icon-info mr-1 align-middle'></i><span>No hay solicitudes a bodega</span></div>"
        },
        toolbar: false,
        change: function(e) {},
        columns: [
            {
                field: "idstatus",
                template: "#= formatStatus(idstatus, id)#",
                values: warehouse_statuses,
                title: locale("Status"),
                width: "120px",
                filterable: false
            },
            {
                field: "oc",
                title: "Orden",
                width: "70px",
                filterable: false,
                hidden: checkVisibility("oc"),
            },
            {
                field: "idpriority",
                title: locale("Priority"),
                values: global_priorities,
                width: "70px",
                filterable: false
            },
            {
                field: "idspot",
                title: "Spot",
                values: global_spots,
                width: "150px",
                filterable: false
            },
            {
                field: "idsupplier",
                values: warehouse_suppliers,
                title: "Proveedor",
                width: "120px",
                filterable: false,
                hidden: checkVisibility("idsupplier")
            },
            {
                field: "iditem",
                title: locale("Item"),
                template: "#= formatItem(item.name, amount, description)#",
                values: warehouse_items,
                width: "250px",
                filterable: false
            },
            {
                field: "amount",
                title: locale("Amount"),
                width: "95px",
                filterable: false,
                hidden: checkVisibility("amount"),
            },
            {
                field: "cost",
                title: "Costo",
                width: "150px",
                template: function(dataItem) {
                    return dataItem.coin + dataItem.cost;
                },
                filterable: false,
                hidden: checkVisibility("cost")
            },
            {
                field: "iduser",
                title: locale("User"),
                width: "150px",
                values: global_users, 
                filterable: false,
                hidden: checkVisibility("iduser")
            },
            {
                field: "created_at",
                title: locale("Date"),
                width: "130px",
                template: "#=formatCreatedAt(created_at)#",
                filterable: false
            },
            {
                field: "iduser",
                title: "Notas",
                width: "50px",
                template: function(dataItem) {
                    return "<div class='task-actions float-right2 todo-item-action d-flex2'><div class='position-relative d-inline-block mr-2'><a data-id='" + dataItem.id + "' class='btn-note todo-item-info success'><i class='feather icon-message-square'></i><span style='" + (dataItem.notes_count == 0 ? 'display:none;' : '') + "' class='badge badge-pill badge-success badge-up'>" + dataItem.notes_count + "</span></a></div></div>";
                },
                filterable: false
            },
            {
                command: [
                    { iconClass: "fad fa-pen commandIconOpacity", name: "edit", text: "" },
                    { iconClass: "fad fa-trash commandIconDelete", name: "destroy", text: ""},
                ],
                title: "Acciones",
                width: "80px"
            }
        ],
    }).data("kendoGrid");  
}
  
function initGridRequestWarehouse()
{
    var dataSourceGrid = new kendo.data.DataSource({
        batch: true,
        transport: {
            read: {
                url: "",
                type: "get",
                dataType: "json",
            },
            create: {
                url: "createWarehouse",
                type: "post",
                dataType: "json",
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
                }
            },
            destroy: {
                url: "api/deleteWarehouse",
                type: "delete",
                dataType: "json",
            },
            parameterMap: function(options, operation) {
                if (operation !== "read" && options.models) {
                    return {models: options.models};
                }
            },
            pageSize: 20,
        },
        schema: {
            model: {
                id: "id",
                fields: {
                    id: { type: "number", editable: false, nullable: true },
                    iditem: { editable: true, validation: { required: { message: locale("Required field") } }, field: "iditem", type: "number" },
                    amount: { editable: true, validation: { required: { message: locale("Required field") }, min: 1 }, field: "amount", type: "number", defaultValue: 1 },
                    cost: { editable: false, field: "cost", type: "number" },
                    idspot: { editable: true, validation: { required: { message: locale("Required field") } }, field: "idspot", type: "number" },
                    description: { editable: true, field: "description", type: "text" },
                    idpriority: { editable: true, validation: { required: { message: locale("Required field") } }, field: "idpriority", type: "number", defaultValue: 1 },
                }
            },
        },  
        requestEnd: function(e) {
            if (e.type == 'create') {
                $.unblockUI();
                $("#modal-warehouse").modal("hide");
                gridRequestWarehouse.dataSource.data([]);
                gridWarehouse.dataSource.read();
            }
        },
        filter: { logic: "and", filters: [{ field: "name", operator: "startswith" }] }
    });
    
    gridRequestWarehouse = $("#gridRequestWarehouse").kendoGrid({
        dataSource: dataSourceGrid,
        editable: true,
        pageable: false,
        height: 500,
        resizable: true,
        reorderable: false,
        filterable: false,
        toolbar: ["save"], // "create", "cancel", "refresh"
        messages: {
            commands: {
              cancel: locale("Cancel changes"),
              create: locale("Add new item"),
              save: locale("Save changes")
            }
        },
        saveChanges: function(e) {
            if (gridRequestWarehouse.dataSource.data().length == 0)
            {
                toastr.error("Complete los datos", 'Advertencia');
                e.preventDefault();
            }
            else
            {
                $.blockUI({ message: '<h1>Procesando...</h1>' });
            }
        },
        columns: [
            {
                field: "oc",
                title: "OC",
                width: "60px",
                hidden: checkVisibility("oc")
            },
            {
                field: "idpriority",
                title: locale("Priority"),
                values: global_priorities,
                width: "60px",
                hidden: checkVisibility("idpriority")
            },
            {
                field: "idspot",
                title: "Lugar",
                values: global_spots,
                width: "130px",
            },
            {
                field: "idsupplier",
                title: "Proveedor",
                values: warehouse_suppliers,
                width: "130px",
                hidden: checkVisibility("idsupplier")
            },
            {
                field: "iditem",
                title: locale("Item"),
                values: warehouse_items,
                width: "200px",
            },
            {
                field: "amount",
                title: locale("Amount"),
                width: "60px",
                hidden: checkVisibility("amount")
            },
            {
                field: "coin",
                title: "Moneda",
                values: [{"value" : "₡", "text":"₡"}, {"value" : "$", "text":"$"}],
                width: "50px",
                hidden: checkVisibility("coin")
            },
            {
                field: "cost",
                title: "Costo",
                width: "50px",
                hidden: checkVisibility("cost")
            },
            {
                field: "description",
                title: locale("Description"),
                editor: textAreaEditor,
                width: "150px",
            },
            { command: { name: "destroy", text: " ", iconClass: "fad fa-trash commandIconDelete" }, title: " ", width: "80px", media: "(min-width: 850px)" }
        ],
    }).data("kendoGrid"); 
}

function addRow()
{
    var row = $('#form-warehouse').serializeArray().reduce(function(obj, item) {
        obj[item.name] = item.value;
        return obj;
    }, {});

    if(checkRow(row))
    {
        gridRequestWarehouse.dataSource.add(row);

        $("#form-warehouse").trigger("reset");

        setTimeout(() => {
            isHidden("oc") == true ? $('#oc').focus() : $('#amount').focus();
            dropDownListSpot.select(0);
            dropDownListSpot.trigger("change");
            dropDownListPriority.value(1);
            dropDownListItem.value(null);
        }, 300);
    }
    else
    {
        toastr.error("Complete los datos", 'Advertencia');
    }
}

function checkRow(row)
{
    let result = true;
    let requiredFields = ["oc", "idspot", "iditem", "idsupplier", "amount", "coin", "cost"];
    requiredFields = requiredFields.filter(val => !warehouse_settings.hide_columns.includes(val));
    console.log(requiredFields);

    $.each(row, function(key, value) {
        if($.inArray(key, requiredFields) != -1 && value == "")
        {       
            result = false;
            return false;
        }
    });

    return result;
}

function isHidden(field)
{
    if($.inArray(field, warehouse_settings.hide_columns) != -1)
    {       
        return true;
    }
    return false;
}

formatStatus = function formatStatus(idstatus, id)
{
    let status = warehouse_statuses.find((item) => { return item.value == idstatus; });

    return '<button data-id=' + id + ' style="cursor: pointer; background-color:' + status.color + ' !important;" type="button" class="btn-status btn btn-relief-primary waves-effect waves-light">' + status.text + '</button>';
}

function formatItem(itemName, amount, description)
{
    var html = "<span span style='width:100%' class='badge badge-light text-dark'>" + description + "</span>" ;
    description != null ? description = html    : description = "";

    return "<span class=''><b>" + itemName + "</b>&nbsp;<span class='amount badge badge-success'>" + amount + "</span> <br>"+ description +"</span>"
}

formatCreatedAt = function formatCreatedAt(value) {
    let time = moment(value);
    return "<span title='" + time.format('YY-MM-DD HH:mm') + "'>" + time.fromNow() + "</span>"
}
