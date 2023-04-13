window.lastCleaningUpdated = null;
window.lastCleaningTicketUpdated = null;
window.lastSpotUpdated = null;
window.selectedSpot = null;

window.menuData = [];

$(document).ready(function() {

    formatCleaningStatusMenu();
    initDropDownListCleaningStaff();
    initDropDownListCleaningStatus();
    initDropDownListCleaningSpot();
    initDropDownListCleaningBranch();

    initListViewSpots();
    initGridEssentialProducts();
    initCleanChart();
    refreshCleaningChart();

    //Refresca el listview de spots al encontrar un cambio
    setInterval(() => { getLastCleaningChange(); }, 10000);

    //Refresca la grilla de tickets de limpieza
    setInterval(() => { getLastCleaningTicket(); }, 10000);

    //Refresca cuando se cambia directo el estado de limpieza del spot
    setInterval(() => { getLastCleaningSpot(); }, 10000);

    $("#dropdown-item-generate").click(function() {
        let confirm = showConfirmModal('Generar limpiezas', '¿Estás seguro?');

        confirm.on('pnotify.confirm', function() {
            generateCleaningPlan();
        });
    });

    $("#dropdown-item-init").click(function() {
        let confirm = showConfirmModal('Inicializar limpiezas', '¿Estás seguro?');

        confirm.on('pnotify.confirm', function() {
            initializeCleaning();
        });
    });

    $("#reload-cleaning-status").click(function() {
        refreshCleaningChart();
    });

    $("#reload-grid-tickets").click(function() {
        $("#gridEssentialProducts").data("kendoGrid").dataSource.read();
    });

    $("#reload-list-spots").click(function() {
        $("#listView").data("kendoListView").dataSource.read();
    });

    $("#btn-clear").click(function() {
        dropDownListCleaningSpot.value("");
        dropDownListCleaningStaff.value("");
        dropDownListCleaningStatus.value("");
        $("#listView").data("kendoListView").dataSource.read();
    });

    $(document).on("click", ".cleaning-status", function(event) {
        dropDownListCleaningStatus.value($(this).data("cleaning-status"));
        dropDownListCleaningStatus.trigger("change");
    });

});


function formatCleaningStatusMenu()
{
    $.each(cleaningstatuses, function(index, item) {

        let menuItem =
        {
            text: "<i style='color:" + item.background + " !important;' class='fa fa-circle font-small-3' aria-hidden='true'></i><span class='ml-1 text-bold-600'>" + item.text + "</span>",
            encoded: false,
            cssClass: 'context-menu',
            idstatus: item.value
        };

        window.menuData.push(menuItem);
    });
}

function initDropDownListCleaningStaff()
{
    dropDownListCleaningStaff = $("#dropDownListCleaningStaff").kendoDropDownList({
        optionLabel: "-- Seleccione --",
        dataTextField: "text",
        dataValueField: "value",
        filter: "contains",
        dataSource: window.cleaningStaff,
        height: 400,
        change: changeFilter,
    }).data("kendoDropDownList");
}

function initDropDownListCleaningStatus()
{
    dropDownListCleaningStatus = $("#dropDownListCleaningStatus").kendoDropDownList({
        optionLabel: "-- Seleccione --",
        dataTextField: "text",
        dataValueField: "value",
        dataSource: window.cleaningstatuses,
        height: 400,
        change: changeFilter,
    }).data("kendoDropDownList");
}

function initDropDownListCleaningBranch()
{
    dropDownListCleaningBranch = $("#dropDownListCleaningBranch").kendoDropDownList({
      dataValueField: "id",
      dataTextField: "text",
      dataValueField: "value",
      filter: "contains",
      optionLabel: "-- Sede --",
      height: 400,
      dataSource: getBranches(),
      change: changeFilter,
    }).data("kendoDropDownList"); 
}

function initDropDownListCleaningSpot()
{
    dropDownListCleaningSpot = $("#dropDownListCleaningSpot").kendoDropDownList({
        optionLabel: "-- Seleccione --",
        dataTextField: "text",
        dataValueField: "value",
        filter: "contains",
        dataSource: window.cleaningSpots,
        change: changeFilter,
        height: 400,
    }).data("kendoDropDownList");
}

function changeFilter()
{
    $("#listView").data("kendoListView").dataSource.read();
}

function initGridEssentialProducts()
{
    $("#gridEssentialProducts").kendoGrid({
        dataSource: {
            transport: {
                read: {
                    url: "getEssentialProducts",
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
                        name: { editable: true, field: "name", type: "string", validation: { required: { message: "Nombre es requerido" } } },
                        iditem: { editable: true, field: "iditem", type: "number" },
                        idspot: { editable: true, field: "idspot", type: "number" },
                        idstatus: { editable: true, field: "idstatus", type: "number" },
                        quantity: { editable: true, field: "quantity", type: "number" },
                    }
                }
            },
        },
        editable: {
            confirmation: "¿Está seguro que desea eliminar este Registro?",
            mode: "popup"
        },
        rowTemplate: "#=rowTemplate(data)#", 
        height: 250,
        sortable: true,
        reorderable: true,
        resizable: true,
        navigatable: true,
        pageable: false,
        noRecords: {
            template: "<div class='alert alert-danger mt-1 alert-validation-msg' role='alert'><i class='feather icon-info mr-1 align-middle'></i><span>No hay solicitudes</span></div>"
        },
        columns: [
            {
                field: "idspot",
                title: "Lugar",
                width: "150px",
                groupable: false,
                media: "(min-width: 350px)"
            },
            {
                field: "name",
                title: "Productos solicitados",
                width: "300px",
                groupable: false,
                media: "(min-width: 350px)"
            },
            {
                field: "created_by",
                title: "Usuario",
                width: "200px",
                groupable: false,
                media: "(min-width: 350px)"
            }
        ],
    }).data("kendoGrid");    
}

function initListViewSpots()
{
    var dataSource = new kendo.data.DataSource({
        transport: {
            read: {
                url: "getCleaningPlan",
                type: "GET",
                dataType: "JSON",
                data: function() {
                    return {
                        idbranch         : dropDownListCleaningBranch.value(),
                        idspot           : dropDownListCleaningSpot.value(),
                        iduser           : dropDownListCleaningStaff.value(),
                        idcleaningstatus : dropDownListCleaningStatus.value()
                    };
                }
            },
        },
        requestEnd: function(e) {}
    });

    $("#listView").kendoListView({
        dataSource: dataSource,
        dataBound: function(e) {

            fixedCardSize(this.dataSource.data());

            if(this.dataSource.data().length == 0)
            {
                $("#listView").append("<div class='alert alert-danger mt-1 alert-validation-msg' role='alert'><i class='feather icon-info mr-1 align-middle'></i><span>No hay lugares para mostrar</span></div>");
            }
        },
        template: "#=templatePlan(data)#",
        selectable: "single",
        change: cardSelected,
    });    

    $("#contextCleaningMenu").kendoContextMenu({
        target: "#listView",
        filter: "section",
        dataSource: window.menuData,
        open: function(e) {

            let spot = $("#listView").data("kendoListView").dataItem(e.target);
            let filteMenu = window.menuData.filter(function(obj) { return obj.idstatus !== spot.idcleaningstatus; });
            this.setOptions({ dataSource: filteMenu });
        },
        select: function(e) {

            let option = this.dataSource.getByUid($(e.item).data('uid'));
            let spot = $("#listView").data("kendoListView").dataItem(e.target);
            let data = {'action': "changecleaningstatus", "id": spot.id, "idcleaningstatus": option.idstatus};
            changeCleaningStatus(data);
        }
    });

    $("#contextCleaningStatusMenu").kendoContextMenu({
        target: "#gridEssentialProducts",
        filter: "tr",
        dataSource:
        [
            {
                text: "<i style='color:#94999d !important;' class='fa fa-circle font-small-3' aria-hidden='true'></i><span class='ml-1 text-bold-600'>Finalizar</span>",
                encoded: false,
                cssClass: 'context-menu',
                idstatus: 4
            }
        ],
        select: function(e) {
            let option = this.dataSource.getByUid($(e.item).data('uid'));
            window.selectedTicket = $("#gridEssentialProducts").data("kendoGrid").dataItem(e.target);
            changeStatusTicket(option.idstatus);
        }
    });
}

function changeStatusTicket(idstatus)
{
    $.blockUI({ message: '<h1>Procesando...</h1>' });

    let data = { 'action': "changestatus", 'idticket': window.selectedTicket.id, 'idstatus': idstatus };

    let request = callAjax('changeStatusTicket', 'POST', data, false);

    request.done(function(result) {

        $.unblockUI();

        if(result.success)
        {
            PNotify.success({ title: 'Cambio de estado', text: 'Acción completada con éxito' });
            $("#gridEssentialProducts").data("kendoGrid").dataSource.read();
        }
        else
        {
            toastr.error(result.message, 'Permisos');
        }

    }).fail(function(jqXHR, status) {
        $.unblockUI();
        PNotify.closeAll();
        PNotify.error({ title: 'Problemas', text: 'Intente nuevamente' });
    });
}



function fixedCardSize(data)
{
    let result = data.map(function(obj) { return obj.cleaning_plans.length; });
    let max = Math.max.apply(Math, result);

    if(max == 6) return;

    let size = (max / 6);
    let increment = parseInt(size);
    if(Number.isInteger(size)) (increment--);
    let height = (increment * 15) + 80;

    $(".spot section").css("height", height + "px");
}

function templatePlan(data)
{
    let plans = getPlansTemplate(data.cleaning_plans);

    let user = getUserTemplate(data.current_cleaning);

    return "<section id='" + data.id + "' class='" + (data.idcleaningstatus == cleaning_statuses.rush ? "rush" : "") + "' style='background-color:" + data.cleaning_status.background + ";'>"+
                "<span>" + (data.shortname == null ? data.name : data.shortname) + "</span>"+
                "<span class='badge " + (data.tickets_count == 0 ? "hidden": "hasIssue") + "'>" + data.tickets_count + "</span>"+
                 user +
                 plans +
            "</section>";
}

function getPlansTemplate(plans)
{
    let cleans = "<ul class='unstyled-list list-inline ratings-list mt-1'>";

    $.each( plans, function( index, plan ){
        let ready = (plan.idcleaningstatus == cleaning_statuses.clean || plan.idcleaningstatus == cleaning_statuses.inspected) ? "fas" : "fal";
        cleans += "<li class='cleaning-list-item'><i class='" + ready + " fa-broom'></i></li>";
    });

    return cleans + "</ul>";
}

function getUserTemplate(current_cleaning)
{
    if(current_cleaning == null || current_cleaning.iduser == null) return "";

    let user = getUser(current_cleaning.iduser);

    return "<img title='" + user.text + "' class='avatar' src='" + user.urlpicture + "'>";
}

function rowTemplate(data)
{
    let ticketType = global_ticket_types.find((obj) => { return obj.value == data.item.idtype; });
    let user = getUser(data.created_by);

    return  "<tr>" +
                "<td><p class='font-weight-bold mb-0' style='font-size:14;'>" + (data.spot.shortname == null ? data.spot.name : data.spot.shortname) + "</p></td>" +
                "<td>" +
                    "<div>" +
                    "<div class='timeline-info'>"+
                        "<p class='font-weight-bold mb-0' style='font-size: 15;'><i style='color:" + ticketType.color + "' class='" + ticketType.icon + " font-medium-2 align-middle'></i>  " + data.name + " <span class='badge badge-warning badge-pill'>" + (data.quantity == null ? "" : data.quantity) + "</span></p>" +
                        "<span class='font-small-3 ml-1'>" + (data.description == null ? "" : data.description)  + "</span>" +
                    "</div>" +
                    "<small class='text-muted ml-1' style='font-size:10;'>"+ moment(data.created_at).fromNow() + "</small>"+
                    "</div>"+
                "</td>" +
                "<td><img class='media-object rounded-circle' src='" + user.urlpicture + "' alt='Avatar' height='25' width='25'> <small class='font-weight-bold' style='font-size:14;'>" + user.text + "</small></td>" +
            "</tr>";
}

function cardSelected(e)
{
    var data = this.dataSource.view();
    var card = $.map(this.select(), function(item) { return data[$(item).index()];});
    window.selectedSpot = card[0];
    $("#modal-cleaning-details").modal("show");
    //$.blockUI();
}

function createCleaningPlan()
{
    let request = callAjax('createCleaningPlan', 'POST', '[]', true);

    request.done(function(result) {

        $("#listView").data("kendoListView").dataSource.read();

    }).fail(function(jqXHR, status) {

        console.log('createCleaningPlan failed!');

    });
}

function refreshCleaningChart()
{
    showLoader('#clean-card');

    let request = callAjax('getCleaningData', 'POST', {}, false);

    request.done(function(result) {

        let colors = result.map(function(obj) { return obj.background; });
        let labels = result.map(function(obj) { return obj.name; });
        let series = result.map(function(obj) { return obj.cleaning_count; });
        refreshCleanChart({ labels: labels, series: series, colors: colors });
        drawCleaningStatusList(result);

        $("#clean-card").unblock();

    }).fail(function(jqXHR, status) {
        $("#clean-card").unblock();
        console.log('ERROR');
    });    
}

function drawCleaningStatusList(data)
{
    $('#list-cleaning-status').empty();

    $.each(data, function(index, item){

        let li = "<li data-cleaning-status="+ item.id +" class='cleaning-status list-group-item d-flex justify-content-between'>"+
                    "<div class='series-info'>"+
                        "<i class='fa fa-circle font-small-3' style='color:"+ item.background +" !important;'></i>"+
                        " <span class='text-bold-600'>"+ item.name +"</span>"+
                    "</div>"+
                    "<div class='product-result'>"+
                        "<span>"+ item.cleaning_count +"</span>"+
                    "</div>"
                 "</li>";

        $("#list-cleaning-status").append(li);
    });
}

function generateCleaningPlan()
{
    let request = callAjax('createCleaningPlan', 'POST', null, true);
    
    request.done(function(result) {

        refreshCleaningChart();
        $("#listView").data("kendoListView").dataSource.read();

    }).fail(function(jqXHR, status) {
        console.log('error during generate cleaning plane!');
    });
}

function changeCleaningStatus(data)
{
    $.blockUI({ message: '<h1>Procesando...</h1>' });

    let request = callAjax('changeCleaningStatus', 'POST', data, false);
    
    request.done(function(result) {
         
        console.log(result);

        if (result.success) {

            $.unblockUI();
            refreshCleaningChart();
            $("#listView").data("kendoListView").dataSource.read();
        } else {
            $.unblockUI();
            toastr.error(result.message, 'Permisos');
        }
    }).fail(function(jqXHR, status) {
        $.unblockUI();
        console.log('error cleaning status change');
    });
}

function initializeCleaning()
{
    let request = callAjax('initializeCleaning', 'POST', null, true);
    
    request.done(function(result) {

        refreshCleaningChart();
        $("#listView").data("kendoListView").dataSource.read();

    }).fail(function(jqXHR, status) {
        console.log('error during generate cleaning plane!');
    });
}

function getLastCleaningChange()
{
    var request = callAjax("getLastCleaningChange", 'POST', {});

    request.done(function(data) {

        if(window.lastCleaningUpdated == null)
        {
            window.lastCleaningUpdated = data;
        }
        else if(data !== window.lastCleaningUpdated)
        {
            window.lastCleaningUpdated = data;
            refreshCleaningChart();
            $("#listView").data("kendoListView").dataSource.read();
        }
    });
}

function getLastCleaningTicket()
{
    var request = callAjax("getLastCleaningTicket", 'POST', {});

    request.done(function(data) {

        if(window.lastCleaningTicketUpdated == null)
        {
            window.lastCleaningTicketUpdated = data;
        }
        else if(data !== window.lastCleaningTicketUpdated)
        {
            window.lastCleaningTicketUpdated = data;
            $("#gridEssentialProducts").data("kendoGrid").dataSource.read();
            $("#listView").data("kendoListView").dataSource.read();
        }
    });
}

function getLastCleaningSpot()
{
    var request = callAjax("getLastCleaningSpot", 'POST', {});

    request.done(function(data) {

        if(window.lastSpotUpdated == null)
        {
            window.lastSpotUpdated = data;
        }
        else if(data !== window.lastSpotUpdated)
        {
            window.lastSpotUpdated = data;
            $("#gridEssentialProducts").data("kendoGrid").dataSource.read();
            $("#listView").data("kendoListView").dataSource.read();
        }
    });
}

function showLoader(element)
{
    $(element).block({message: '<div class="feather icon-refresh-cw icon-spin font-medium-2 text-primary"></div>'}); 
}