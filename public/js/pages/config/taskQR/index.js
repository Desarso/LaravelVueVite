$(document).ready(function() {

    initDropDownListSpot();
    initDropDownListItem();
    initQR();
});

function initDropDownListItem()
{
    dropDownListItem = $("#dropDownListItem").kendoDropDownList({
        optionLabel: "-- Seleccione --",
        dataTextField: "name",
        dataValueField: "id",
        filter: "contains",
        dataSource: window.global_items,
        height: 400,
        change: changeFilter
    }).data("kendoDropDownList");
}

function changeFilter()
{
    if(dropDownListItem.value() != "" || dropDownListSpot.value() != "")
    {
        $('#btn-qr').prop('disabled', false);
    }
    else
    {
        $('#btn-qr').prop('disabled', true);
    }
}

function initQR() {
    (function() {
        qr = new QRious({
            element: document.getElementById('qr-code'),
            size: 200,
            value: 'https://whagons.com'
        });
    })();
}

function generateQRCode()
{
    let iditem = dropDownListItem.value();
    let idspot = dropDownListSpot.value();

    iditem = (iditem == "" ? null : iditem);
    idspot = (idspot == "" ? null : idspot);

    if(iditem == "" && idspot == "")
    {
        toastr.warning("Para generar un QR debes seleccionar un ítem o un spot");
        return false;
    }

    var qrtext = `{ "iditem": "${iditem}", "idspot": "${idspot}" }`;

    qr.set({
        foreground: 'black',
        size: 200,
        value: qrtext
    });

    $("#msg-qr").show();

    $("#msg-item").text(dropDownListItem.text());
    $("#msg-spot").text(dropDownListSpot.text());

    dropDownListItem.value("");
    dropDownListSpot.value("");

    dropDownListSpot.trigger("change");
}

function initGridQr()
{
    gridQR = $("#gridQr").kendoGrid({
        dataSource: {
            transport: {
                read: {
                    url: "getDataQR",
                    type: "get",
                    dataType: "json",
                },
                destroy: {
                    url: "api/deleteQR",
                    type: "delete",
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
        editable: {
            mode: "popup"
        },
        height: "450px",
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
        dataBound: function(e) {
            var data = e.sender.dataSource.data();
            console.log("dataBound dataBound dataBound");
            $.each(data, function(i, row) {
                let qrtext = `{ "iditem": "${row.iditem}", "idspot": "${row.idspot}" }`;
                new QRious({
                    element: document.getElementById(`qr-${row.id}`),
                    size: 120,
                    value: qrtext,
                });
            })
        },
        columns: [
            {
                field: "id",
                title: "Cod",
                width: "25px",
                filterable: false
            },
            {
                field: "idspot",
                title: "Lugar",
                width: "80px",
                values: global_spots,
                filterable: false
            },
            {
                field: "iditem",
                title: "Tarea",
                width: "160px",
                template: function(dataItem) {
                    return global_items.find(o => o.id === parseInt(dataItem.iditem)).name;
                },
                filterable: false
            },
            {
                title: "QR",
                width: "150px",
                template: function(dataItem) {
                    return `<div><canvas id='qr-${dataItem.id}'></canvas></div>`;
                },
                filterable: false
            },
            { command: { name: "print", text: "", iconClass: "fad fa-print commandIconOpacity  ", click: ShowPrintModal, }, title: " ", width: "50px" },
            { command: { name: "destroy", text: " ", iconClass: "fad fa-trash commandIconDelete" }, title: " ", width: "50px" }
        ],
    }).data("kendoGrid");
}

function printOut() {
    printJS('qr-code', 'html');
}


function saveQR()
{
    let data = {
        'iditem': dropDownListItem.value(),
        'idspot': dropDownListSpot.value()
    };

    let request = callAjax('api/createQR', 'POST', data, true);

    request.done(function(result) {
        $("#gridQr").data("kendoGrid").dataSource.read();
    }).fail(function(jqXHR, status) {
        console.log('getDataByTeam failed!'); 
    });
}

function ShowPrintModal(e) {

    e.preventDefault();
    window.rowSeleted = this.dataItem($(e.currentTarget).closest("tr"));
    let iditem = rowSeleted.iditem;
    let idspot = rowSeleted.idspot;

    var qrtext = `{ "iditem": "${iditem}", "idspot": "${idspot}" }`;

    qr.set({
        foreground: 'black',
        size: 200,
        value: qrtext
    });

    printOut();
}