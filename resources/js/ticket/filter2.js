
var idFilter = "";
var nameFilter = "";
$(document).ready(function () {
    $("#filter-selected").hide();
});

$(document).on("click", ".btn-remove-filter", function (event) {
    $(".k-filter-lines").remove();
    changefilterText();
    $("#filter-selected").hide();
    $("#filter-selected").html("<p class='mt-1 mb-0' title='Filtro seleccionado'></p>");
    $("#filterList").text("Filtros");
   $("#filterTicket").data("kendoFilter").dataSource.filter({});
   let options = filterTicket.getOptions();
   options.expression = window.ticketsFilter;
   filterTicket.setOptions(options);
   filterTicket.applyFilter();
   $("body").find("[aria-label='Add Group']").remove();
});

$("#btn-close-filter-applied").click(function (e) {
    $("#clearFilter").click();
});

$(".div-filters-applied").click(function (e) {
    $('#modalFilterEdit').modal('show');
});

$(document).on("click", "#addFilter", function (event) {
    /*
    setFilter($("#filterTicket").data("kendoFilter").dataSource.filter());
    $("#filter-selected").html("<i class='fad fa-filter mr-50 font-medium-1'></i>"+"<i class='font-medium-1 mr-50 btn-remove-filter fad fa-times'></i>" + " ");
    changefilterText();
    $("#filter-selected").show();
    $("#modalFilterEdit").modal('toggle');
    console.log($("#gridTicket").data("kendoGrid").dataSource.filter());
    console.log($("#filterTicket").data("kendoFilter").dataSource.filter());
    */

    $("#filterTicket").data("kendoFilter").applyFilter();

    $('#modalFilterEdit').modal('hide');

    getFilterMessage();
});

$('#modalFilterEdit').on('hidden.bs.modal', function (e) {
    $("#addFilter").click();
});

$(document).on("click", "#clearFilter", function (event) {

    setFilter();

    $(".modalFilterEdit .k-toolbar .k-filter-toolbar-item:nth-child(2) button").html('Agregar filtro <span class="k-icon k-i-filter-add-expression"></span>');

    $(".k-filter-preview").addClass("alert alert-primary");

    getFilterMessage();

});

$(document).on("click", ".item-filter", function (event) {
    
    console.log($(this).data("filter"));

    setFilter($(this).data("filter"), true);
    //$("#filter-selected").html("<i class='fad fa-filter mr-50 font-medium-1'></i>"+"<i class='font-medium-1 mr-50 btn-remove-filter fad fa-times'></i>" + $(this).data("name"));
    //changefilterText();
    //$("#filter-selected").show();
});

$(document).on("click", ".item-edit-filter", function (event) {
    setFilter($(this).data("filter"));
});

function getFilterMessage()
{
    let filterTicket = $("#filterTicket").data("kendoFilter").getOptions();

    if(filterTicket.hasOwnProperty("expression") && filterTicket.expression.hasOwnProperty("filters"))
    {
        $("#div-filters-applied").show()
        $("#message-filters-applied").text("Tienes " + filterTicket.expression.filters.length + " filtros aplicados");
    }
    else
    {
        $("#div-filters-applied").hide();
    }
}


function FilterTextES() {
    $("#btnSavenNewFilter").text("Guardar");
    $(".btn-update-filter").text("Actualizar");
    $(".btn-cancel-filter").text("Cancelar");
    $("#filterNewName").attr("placeholder", "Nombre del filtro");
    $(".k-button-group span:nth-child(1)").text("Y");
    $(".k-button-group span:nth-child(2)").text("O");
}

function FilterTextENG() {
    $("#btnSavenNewFilter").text("Save");
    $(".btn-update-filter").text("Update");
    $(".btn-cancel-filter").text("Cancel");
    $("#filterNewName").attr("placeholder", "Filter name");
    $(".k-button-group span:nth-child(1)").text("And");
    $(".k-button-group span:nth-child(2)").text("Or");
}

function changefilterText() {
    if (window.lenguague == 'es') {
        FilterTextES();
    } else if (window.lenguague == 'en') {
        FilterTextENG();
    } else {
        FilterTextES();
    }
}

$("#newFilter").click(function (e) {
    e.preventDefault();
    $(".btn-update-filter").hide();
    $("#showNewFilterSave").show();

});

$(document).on("click", ".btn-delete-filter", function(event) {
    console.log($(this).data("idfilter"), $(this).data("name"));
    deleteFilter($(this).data("idfilter"), $(this).data("name"));
    //filterItemTotal();
});

$(document).on("click", ".btn-open-modal-filter", function (event) {
    idFilter = $(this).data("idfilter");
    nameFilter = $(this).data("name");
    $(".btn-update-filter").show();
    $("#showNewFilterSave").hide();
});

$(".btn-update-filter").click(function (e) {
    e.preventDefault();
    let filterTicket = $("#filterTicket").data("kendoFilter");
    let options = filterTicket.getOptions();
    let data = { "id": idFilter, "name": nameFilter, "data": JSON.stringify(options.expression) };
    updateFilter(data);

});

$("#btnSaveFilter").click(function(e) {
    e.preventDefault();
    let filterName = $("#filterName").val();

    if(filterName == "")
    {
        PNotify.error({ title: 'Datos incompletos', text: 'Ingrese el nombre del filtro' });
        return false;
    }

    let filterTicket = $("#filterTicket").data("kendoFilter");
    let options = filterTicket.getOptions();
    let data = {"name" : filterName, "data" : JSON.stringify(options.expression)};
    saveFilter(data);
    
});


$("#btnSavenNewFilter").click(function (e) {
    e.preventDefault();
    let filterName = $("#filterNewName").val();

    if (filterName == "") {
        PNotify.error({ title: 'Datos incompletos', text: 'Ingrese el nombre del filtro' });
        return false;
    }

    let filterTicket = $("#filterTicket").data("kendoFilter");
    let options = filterTicket.getOptions();
    let data = { "name": filterName, "data": JSON.stringify(options.expression) };
    saveFilter(data);
    let filter = $("#filterTicket").data("kendoFilter").dataSource.filter();
    setFilter(filter);
    $("#filter-selected").html("<i class='fad fa-filter mr-50 font-medium-1'></i>"+"<i class='font-medium-1 mr-50 btn-remove-filter fad fa-times'></i>" + filterName);
    changefilterText();
    $("#filter-selected").show();
});

function saveFilter(data) {
    let request = callAjax('createFilter', 'POST', data, true);

    request.done(function (result) {

        $("#filterName").val("");
        $("#filterNewName").val("");

        let tr = "<tr class='dropdown-item'>" +
            "<td><i data-idfilter='" + data.id + "' data-name='" + data.name + "' class='btn-delete-filter fad fa-trash-alt'></i></td>" +
            "<td data-filter='" + data.data + "' data-name='" + data.name + "' class='item-filter'>" + data.name + "</td>" +
            "<td><i data-idfilter='" + data.id + "' data-name='" + data.name + "'data-toggle='modal'" + "' data-target='#modalFilterEdit'" + "' class='btn-open-modal-filter item-edit-filter fas fa-pen'></i></td>" +
            "</tr>";
        $("#table-filter").append(tr);

        PNotify.success({ title: 'Nuevo filtro', text: 'Acción completada con éxito' });
        $("#modalFilterEdit").modal('toggle');
    }).fail(function (jqXHR, status) {
        //toastr.error('La acción no se puedo completar', 'Hubo un problema!');
    });
}

setFilter = function setFilter(filter = {logic: 'and'}, apply = false) {

    alert("hola");

    let filterTicket = $("#filterTicket").data("kendoFilter");
    let options = filterTicket.getOptions();
    options.expression = filter;
    filterTicket.setOptions(options);

    if(apply == true) filterTicket.applyFilter();

    $("body").find("[aria-label='Add Group']").remove();
    //filterItemTotal();
}

function deleteFilter(id, name)
{
    let confirm = showConfirmModal('Eliminar ' + name, '¿Estás seguro?');

    confirm.on('pnotify.confirm', function() {

        let request = callAjax('deleteFilter', 'POST', { 'id': id}, true);

        request.done(function(result) {

            $("#row-filter-" + result.id).remove();
            PNotify.closeAll();
            PNotify.success({ title: 'Filtro eliminado', text: 'Acción completada con éxito' });

        }).fail(function(jqXHR, status) {
            PNotify.closeAll();
            PNotify.error({ title: 'Problemas', text: 'La acción no se puedo completar' });
        });
        
    });
}

function updateFilter(data) {
    let request = callAjax('updateFilter', 'POST', data, true);

    request.done(function (result) {
        $("#row-filter-" + data.id).remove();
        let tr = "<tr id='" + 'row-filter-' + data.id + "' class='dropdown-item'>" +
        "<td><i data-idfilter='" + data.id + "' data-name='" + data.name + "' class='btn-delete-filter fad fa-trash-alt'></i></td>" +
        "<td data-filter='" + result.data + "' data-name='" + data.name + "' class='item-filter'>" + data.name + "</td>" +
        "<td><i data-filter='" + result.data + "' data-idfilter='" + data.id + "' data-name='" + data.name + "'data-toggle='modal'" + "' data-target='#modalFilterEdit'" + "' class='btn-open-modal-filter item-edit-filter fas fa-pen'></i></td>" +
        "</tr>";
    $("#table-filter").append(tr);

        PNotify.success({ title: 'Filtro Actualizado', text: 'Acción completada con éxito' });
        $("#modalFilterEdit").modal('toggle');
    }).fail(function (jqXHR, status) {

    });
}

/////////////////////
$(document).on("click", "#closeModal", function (event) {
    //filterItemTotal();
});

$(document).on("click", ".k-filter-toolbar-item .k-button-flat", function(event) {
    //filterItemTotal();
});

$(document).on("click", ".k-filter-toolbar-item:nth-child(2) .k-button", function(event) {
    //filterItemTotal();
});

function filterItemTotal(){
    var total = $(".k-filter-lines .k-filter-item").length;
    if(total > 0){
        $("#filterList").text("Filtros " + "("+total+")");
    }else{
        $("#filterList").text("Filtros");
    }
    
}
