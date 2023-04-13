$("#btn-close-filter-applied").click(function (e) {

    $(".k-button-flat").click();

    $(".modal-filter .k-toolbar .k-filter-toolbar-item:nth-child(2) button").html('Agregar filtro <span class="k-icon k-i-filter-add-expression"></span>');

    $(".k-filter-preview").addClass("alert alert-primary");

    filterTicket.applyFilter();

    getFilterMessage();
});

$(".div-filters-applied").click(function (e) {
    $('#modal-filter').modal('show');
});

$("#btn-show-to-save-filter").click(function() {
    $(this).hide();
    $("#div-save-filter-section").show();
    $("#txt-filter-name").focus();
});

$("#btn-save-filter").click(function() {
    saveFilter();
});


$('#modal-filter').on('hidden.bs.modal', function (e) {
    filterTicket.applyFilter();
    getFilterMessage();
});

$("#btn-apply-filter").click(function (e) {

    $('#modal-filter').modal('hide');

});

$("#btn-clear-filter").click(function (e) {

    $("#div-save-filter-section").hide();

    $(".k-button-flat").click();

    $(".modal-filter .k-toolbar .k-filter-toolbar-item:nth-child(2) button").html('Agregar filtro <span class="k-icon k-i-filter-add-expression"></span>');

    $(".k-filter-preview").addClass("alert alert-primary");

    filterTicket.applyFilter();

});

$(document).on("click", ".item-filter", function (event) {
    
    console.log($(this).data("filter"));

    setFilter($(this).data("filter"));
});

$(document).on("click", ".btn-delete-filter", function(event) {
    deleteFilter($(this).data("idfilter"), $(this).data("name"));
});

setFilter = function setFilter(filter)
{
    let options = filterTicket.getOptions();
    options.expression = filter;
    filterTicket.setOptions(options);

    filterTicket.applyFilter();

    $("body").find("[aria-label='Add Group']").remove();

    $(".modal-filter .k-toolbar .k-filter-toolbar-item:nth-child(2) button").html('Agregar filtro <span class="k-icon k-i-filter-add-expression"></span>');

    $(".k-filter-preview").addClass("alert alert-primary");

    getFilterMessage();
}

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

function saveFilter()
{
    let filterName = $("#txt-filter-name").val();

    if (filterName == "")
    {
        PNotify.error({ title: 'Datos incompletos', text: 'Ingrese el nombre del filtro' });
        return false;
    }

    let options = filterTicket.getOptions();
    let data = { "name": filterName, "data": JSON.stringify(options.expression) };

    let request = callAjax('createFilter', 'POST', data, true);

    request.done(function (result) {

        console.log(result);

        $("#txt-filter-name").val("");
        $("#div-save-filter-section").hide();

        let tr = "<tr class='dropdown-item' id='row-filter-" + result.id + "' >" +
                    "<td><i data-idfilter='" + result.id + "' data-name='" + result.name + "' class='btn-delete-filter fad fa-trash-alt'></i></td>" +
                    "<td data-filter='" + result.data + "' data-name='" + result.name + "' class='item-filter'>" + result.name + "</td>" +
                 "</tr>";

        $("#table-filter").append(tr);

        PNotify.success({ title: 'Nuevo filtro', text: 'Acción completada con éxito' });


    }).fail(function (jqXHR, status) {
        //toastr.error('La acción no se puedo completar', 'Hubo un problema!');
    });
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