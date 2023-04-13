$(document).ready(function() {

    initTagKendoColorPalette();
    initDropDownListTicketType();
    initDropDownListSpotParent();
    initDropDownListSpotType();
    initDropDownListTeam();
    initkendoColorPicker();
});

addNewConfig = function addNewConfig(id, value)
{
    switch(id)
    {
        case "multiSelectItem":

            $("#modalNewItem").modal("show");
            $("#item-name").val(value);
            break;

        case "multiSelectSpot":

            $("#modalNewSpot").modal("show");
            $("#spot-name").val(value);
            break;
            
        case "multiSelectTag":

            $("#modalNewTag").modal("show");
            $("#tag-name").val(value);
            break;
    }
}

$("#card-new-ticket-type").click(function() {
    $("#modalNewTicketType").modal("show");
});

$("#btnNewItem").click(function() {
    $(this).hide();
    $("#btnSpinnerItem").show();
    createItemOnFly();
});

$("#btnNewSpot").click(function() {
    $(this).hide();
    $("#btnSpinnerSpot").show();
    createSpotOnFly();
});

$("#btnNewTag").click(function() {
    $(this).hide();
    $("#btnSpinnerTag").show();
    createTagOnFly();
});

$("#btnNewTicketType").click(function() {
    $(this).hide();
    $("#btnSpinnerTicketType").show();
    createTicketTypeOnFly();
});

function initDropDownListTicketType()
{
    dropDownListTicketType = $("#dropDownListTicketType").kendoDropDownList({
        dataTextField: "text",
        dataValueField: "value",
        dataSource: window.global_ticket_types,
        template: '<span class="k-state-default"><i class="#:data.icon#" style="color:#:data.color#"></i></span> <span> #:data.text#</span>',
    }).data("kendoDropDownList")
}

function initDropDownListSpotParent()
{
    dropDownListSpotParent = $("#dropDownListSpotParent").kendoDropDownList({
        filter: "contains",
        dataTextField: "text",
        dataValueField: "value",
        dataSource: window.global_spots,
        popup: { appendTo: $("#modalNewSpot") }
    }).data("kendoDropDownList")
}

function initDropDownListSpotType()
{
    dropDownListSpotType = $("#dropDownListSpotType").kendoDropDownList({
        dataTextField: "text",
        dataValueField: "value",
        dataSource: window.global_spot_types
    }).data("kendoDropDownList")
}

function initTagKendoColorPalette()
{
    tagColorPalette = $("#tagColorPalette").kendoColorPalette({
        palette: ["#007bff", "#17a2b8", "#746153", "#3a4c8b", "#ffcc33", "#fb455f", "#ac120f"],
        tileSize: 30,
        value: "#007bff",
        change: function() {
            //previewTag();
        }
    }).data("kendoColorPalette");
}

function initDropDownListTeam()
{
    dropDownListTeam = $("#dropDownListTeam").kendoDropDownList({
        filter: "contains",
        dataTextField: "text",
        dataValueField: "value",
        dataSource: window.global_teams,
        popup: { appendTo: $("#modalNewTicketType") }
    }).data("kendoDropDownList")
}

function initkendoColorPicker()
{
    $("#picker").kendoColorPicker({
        value: "#ffffff",
        buttons: false,
        //select: preview
    });
}

function previewTag()
{
    let color  = tagColorPalette.value();
    let tagname = $("#tagname").val()
    $("#tag-result").text(tagname);
    $("#tag-result").css("background-color", color);
}

function createItemOnFly()
{
    let data = $("#formNewItem").serializeFormJSON();
    data['action'] = 'createconfig';

    let request = callAjax('createItemOnFly', 'POST', data, false);

    request.done(function(result) {

        if(result.success)
        {
            let ticketType = global_ticket_types.find((obj) => { return obj.value == result.model.idtype; });
            
            result.model["color"]  = ticketType.color;
            result.model["icon"]   = ticketType.icon;
            result.model["hassla"] = ticketType.hassla;

            toastr.success('Acción completada con éxito', 'Nuevo Item');
            $("#modalNewItem").modal("hide");
            window.global_items.push(result.model);
            let data_source = multiSelectItem.dataSource.data();
            data_source.push(result.model);
            multiSelectItem.dataSource.data(data_source);
            multiSelectItem.dataSource.filter([]);
            multiSelectItem.value(result.model.id);
            multiSelectItem.trigger('select',  {dataItem: result.model} );
            multiSelectSpot.focus();  
        }
        else
        {
            toastr.error(result.message, 'Permisos');
        }

        $("#btnNewItem").show();
        $("#btnSpinnerItem").hide();

    }).fail(function(jqXHR, status) {
        toastr.error('La acción no se puedo completar', 'Problemas');
    });
}

function createSpotOnFly()
{
    let data = $("#formNewSpot").serializeFormJSON();
    data['action'] = 'createconfig';

    let request = callAjax('createSpotOnFly', 'POST', data, false);

    request.done(function(result) {
        
        if(result.success)
        {
            toastr.success('Acción completada con éxito', 'Nuevo Spot');
            $("#modalNewSpot").modal("hide");
            let obj = {"value": result.model.id, "text": result.model.name, "isbranch": result.model.isbranch};
            window.global_spots.push(obj);
            let user_spots = JSON.parse(window.user.spots);
            user_spots.push(obj.value);
            window.user.spots = JSON.stringify(user_spots);
            let data_source = multiSelectSpot.dataSource.data();
            data_source.push(obj);
            multiSelectSpot.dataSource.data(data_source);
            multiSelectSpot.value(obj.value);
            multiSelectSpot.trigger('select');
            multiSelectSpot.focus();
        }
        else
        {
            toastr.error(result.message, 'Permisos');
        }

        $("#btnNewSpot").show();
        $("#btnSpinnerSpot").hide();

    }).fail(function(jqXHR, status) {
        $("#btnNewSpot").show();
        $("#btnSpinnerSpot").hide();
        toastr.error('La acción no se puedo completar', 'Problemas');
    });
}

function createTicketTypeOnFly()
{
    let data = $("#formNewTicketType").serializeFormJSON();
    data['action'] = 'createconfig';

    let request = callAjax('createTicketTypeOnFly', 'POST', data, false);

    request.done(function(result) {
        
        if(result.success)
        {
            toastr.success('Acción completada con éxito', 'Nuevo Tipo de Tarea');
            let template = templateTicketType(result.model);
            $("#swiper-ticket-type").append(template);
            $("#modalNewTicketType").modal("hide");
        }
        else
        {
            toastr.error(result.message, 'Permisos');
        }

        $("#btnNewTicketType").show();
        $("#btnSpinnerTicketType").hide();

    }).fail(function(jqXHR, status) {
        $("#btnNewTicketType").show();
        $("#btnSpinnerTicketType").hide();
        toastr.error('La acción no se puedo completar', 'Problemas');
    });
}

function createTagOnFly()
{
    let data = $("#formNewTag").serializeFormJSON();
    data['color'] = tagColorPalette.value();
    data['action'] = 'createconfig';

    let request = callAjax('createTagOnFly', 'POST', data, false);

    request.done(function(result) {
        
        PNotify.closeAll();

        if(result.success)
        {
            toastr.success('Acción completada con éxito', 'Nueva Etiqueta');
            $("#modalNewTag").modal("hide");
            let data_source = multiSelectTag.dataSource.data();
            let obj = {"value": result.model.id, "text": result.model.name, "color": result.model.color}
            data_source.push(obj);
            window.global_tags.push(obj);
            let tags = multiSelectTag.value();
            multiSelectTag.dataSource.data(data_source);
            tags.push(obj.value);
            multiSelectTag.value(tags);
            multiSelectTag.trigger('select')
            multiSelectTag.focus();
        }
        else
        {
            toastr.error(result.message, 'Permisos');
        }

        $("#btnNewTag").show();
        $("#btnSpinnerTag").hide();

    }).fail(function(jqXHR, status) {
        $("#btnNewTag").show();
        $("#btnSpinnerTag").hide();
        toastr.error('La acción no se puedo completar', 'Problemas');
    });
}

function templateTicketType(model)
{
    return "<div data-idtype='" + model.id + "' class='card-ticket-type swiper-slide rounded swiper-shadow py-1 px-3 d-flex'>" +
                "<i class='" + model.icon + " mr-50 font-medium-3'></i>" +
                "<div class='swiper-text'>" + model.name + "</div>" +
            "</div>";
}