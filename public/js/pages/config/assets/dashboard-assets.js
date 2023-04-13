$(document).ready(function () {
    var asset = new Asset();
    asset.initGrid();
    fixKendoGridHeight();

    $("#export").click(function (e) {
        var grid = $("#gridAssets").data("kendoGrid");
        grid.saveAsExcel();
    });

    $("#printQRCode").click(function (e) {
        PrintQR();
    });

    // Charts
    initAssetsChart();
    refreshingAssetsChart();
});


function generateQRCode() {

    document.getElementById("qrcode").innerHTML = "";

    qrcode = new QRCode(document.getElementById("qrcode"), {
        text: window.location.hostname + '/viewAsset?id=' + window.currentAsset.id,
        width: 200,
        height: 200,
        title: window.currentAsset.name, // content 
        titleFont: "bold 14px Arial", //font. default is "bold 16px Arial"
        titleColor: "#004284", // color. default is "#000"
        titleBackgroundColor: "#fff", // background color. default is "#fff"
        titleHeight: 70, // height, including subTitle. default is 0
        titleTop: 25, // draws y coordinates. default is 30
        correctLevel: QRCode.CorrectLevel.H,
        subTitle: window.currentAsset.brand, // content
        subTitleFont: "10px Arial", // font. default is "14px Arial"
        subTitleColor: 'lightgrey', // color. default is "4F4F4F"
        subTitleTop: 40, // draws y coordinates. default is 0
        PO: '#0570A3', // Global Posotion Outer color. if not set, the defaut is `colorDark`
        PI: '#1FAA5D', // Global Posotion Inner color. if not set, the defaut is `colorDark`
        PO_TL: '', // Posotion Outer color - Top Left 
        PI_TL: '', // Posotion Inner color - Top Left 
        PO_TR: '', // Posotion Outer color - Top Right 
        PI_TR: '', // Posotion Inner color - Top Right 
        binary: true,
    });
}


function refreshingAssetsChart() {
    showLoader('#clean-card');

    let request = callAjax('getAssetsData', 'POST', {}, false);

    request.done(function (result) {

        let colors = result.map(function (obj) { return obj.color; });
        let labels = result.map(function (obj) { return obj.name; });
        let series = result.map(function (obj) { return obj.asset_count; });
        refreshAssetChart({ labels: labels, series: series, colors: colors });
        drawAssetStatusList(result);

        $("#clean-card").unblock();

    }).fail(function (jqXHR, status) {
        $("#clean-card").unblock();
        console.log('ERROR');
    });
}


function drawAssetStatusList(data) {
    $('#list-cleaning-status').empty();

    $.each(data, function (index, item) {

        let li = "<li class='list-group-item d-flex justify-content-between'>" +
            "<div class='series-info'>" +
            "<i class='fa fa-circle font-small-3' style='color:" + item.color + " !important;'></i>" +
            " <span class='text-bold-600'>" + item.name + "</span>" +
            "</div>" +
            "<div class='product-result'>" +
            "<span>" + item.asset_count + "</span>" +
            "</div>"
        "</li>";

        $("#list-cleaning-status").append(li);
    });
}




var Asset = /** @class */ (function () {
    function Asset() { }

    Asset.prototype.initGrid = function () {
        window.grid = $("#gridAssets").kendoGrid({
            excel: {
                fileName: "Whagons Assets.xlsx",
            },
            dataSource: {
                transport: {
                    read: {
                        url: "getEnabledAssets",
                        type: "get",
                        dataType: "json"
                    },
                },
                pageSize: 20,
                schema: {
                    model: {
                        id: "id",
                        fields: {
                            id: { type: "number", editable: false, nullable: true },
                            idcategory: { editable: true, field: "idcategory", type: "numeric" },
                            idstatus: { editable: true, field: "idstatus", type: "numeric" },
                            name: { editable: true, field: "name", type: "string", validation: { required: { message: "Nombre es requerido" } } },
                            description: { editable: true, field: "description", type: "string" },
                            idspot: { editable: true, field: "idspot", type: "numeric" },
                            icon: { editable: true, field: "icon", type: "string" },
                            color: { editable: true, field: "color", type: "string" },
                            enabled: { editable: true, field: "enabled", type: "boolean", defaultValue: true },
                        }
                    }
                },
            },
            change: function (arg) {
                getAssetData(this.dataItem(this.select()));
            },
            selectable: true,
            toolbar: ['search'],
            sortable: true,
            pageable: {
                refresh: true,
                pageSizes: true,
                buttonCount: 5,
            },
            filterable: true,
            columns: [
                {
                    field: "name",
                    title: locale("Name"),
                    template: "#=formatAssetName(name,description, icon,color,idstatus, enabled)#",
                    filterable: {
                        multi: true,
                        search: true
                    }
                },
                {
                    field: "idspot",
                    editor: dropDownListEditor,
                    title: locale("Spot"),
                    width: "250px",
                    values: window.global_spots,
                    filterable: {
                        multi: true,
                        search: true
                    }
                },
            ],
        }).data("kendoGrid");
    };

    return Asset;
}());


function getAssetData(asset) {

    $('#assetImage').hide();
    showLoader('#assetSummary');

    let request = callAjax('getAssetInfo', 'POST', { id: asset.id }, false);

    request.done(function (result) {
        window.currentAsset = result;
        showAssetData(result);
        $("#assetSummary").unblock();
        $('#tabsSection').show();
        generateQRCode();
        getAssetTasks(asset.id)

    }).fail(function (jqXHR, status) {
        $("#assetSummary").unblock();
    });
}

function showAssetData(asset) {

    $("#assetName").html(asset.name);
    $("#assetIcon").removeClass().addClass(asset.icon);
    $("#assetIcon").css('color', asset.color);
    $("#assetBrand").html(asset.brand);
    $("#assetModel").html(asset.model);
    $("#assetDescription").html(asset.description);
    $("#assetStatus").html(asset.status.name);
    $("#assetStatus").css('color', asset.status.color);
    $("#assetCategory").html(asset.category.name);
    $("#assetCode").html(asset.code);
    $("#assetSpot").html(asset.spot == null ? '' : asset.spot.name);
}

function formatAssetName(name, description, icon, color, idstatus, enabled) {
    let result = "";

    enabled == false ? result += '<div  style="display:inline-block; vertical-align: middle; margin-right: 5px; opacity: 0.5; text-decoration: line-through;" >' :
        result += "<div style='display:inline-block; vertical-align: middle; margin-right: 5px;'>"

    let status = window.statuses.find(s => s.value === idstatus);
    result += "<i class='fa fa-circle' style='opacity: 0.5; font-size: 10px;color: " + status.color + "' /></i>";
    result += "&nbsp;&nbsp;<i class='" + icon + "' style='opacity: 0.7;font-size: 1em; color:" + color + "'></i>   ";
    result += "<strong>" + name + "</strong>";
    result += "</div>";
    return result;
}


function showLoader(element) {
    $(element).block({ message: '<div class="feather icon-refresh-cw icon-spin font-medium-2 text-primary"></div>' });
}

function getAssetTasks(idAsset) {
    // alert("")
    console.log("getAssetTasks getAssetTasks");


    $("#gridAssetsTasks").kendoGrid({
        excel: {
            fileName: "Whagons Assets Task.xlsx",
        },
        height: 500,
        dataSource: {
            transport: {
                read: {
                    url: "getAssetTasks",
                    type: "get",
                    dataType: "json",
                    data: function() {
                        return {
                            idasset : idAsset
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
                        code: { editable: true, field: "code", type: "numeric" },
                        name: { editable: true, field: "name", type: "text" },
                    }
                }
            },
        },
        selectable: true,
        toolbar: ['search'],
        sortable: true,
        pageable: {
            refresh: true,
            pageSizes: true,
            buttonCount: 5,
        },
        filterable: true,
        columns: [
            {
                field: "code",
                title: "Código",
                width: "50px",
                filterable: false,
            },
            {
                field: "name",
                title: "Tarea", 
                template: "#=formatTicketName(name, icon, color)#",
                width: "250px",
                filterable: false,
            },
            {
                field: "created_at",
                title: "Fecha",
                template: "#=formatCreatedAt(created_at)#",
                width: "12%",
                filterable: false,
            },
        ],
    }).data("kendoGrid");
}

formatCreatedAt = function formatCreatedAt(value) {
    let time = moment(value);
    return "<span title='" + time.format('YY-MM-DD HH:mm') + "'>" + time.fromNow() + "</span>"
}

formatTicketName = function formatTicketName(name, icon, color) {
    
    return "<div class='title-wrapper d-flex'>" +
            "<h6 class='todo-title mt-50 mx-50'> " + "<i style='color:" + color + "' class='font-medium-2 " + icon + "'></i> " + name + "</h6>" +
            "</div>";
}

function PrintQR()
{
    var mywindow = window.open('', 'PRINT', 'height=400,width=600');
    mywindow.document.write(document.getElementById('qrcode').innerHTML);
    mywindow.document.close(); // necessary for IE >= 10
    mywindow.focus(); // necessary for IE >= 10*/
    mywindow.print();

    return true;
}