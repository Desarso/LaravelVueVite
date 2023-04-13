window.ticketTypeAction = "create";

$(document).ready(function () {

    initListViewTicketTypes();
    initDropDownListTeamTicketType();
    initDropDownListIcon();

    $("#btn-new-ticket-type").click(function(e) { 
        $('#form-ticket-type').trigger("reset");
        $("#div-ticket-type-update-buttons").hide();
        $("#div-ticket-type-create-buttons").show();
        $("#modal-list-ticket-type").modal("hide");
        $("#modal-ticket-type").modal("show");
        $("#title-modal-ticket-type").html("<i id='icon-back-ticket-type' class='fas fa-arrow-left'></i>  Crear categoría");
    });

    $(document).on("click", "#icon-back-ticket-type", function(event) {
        $("#modal-ticket-type").modal("hide");
        $("#modal-list-ticket-type").modal("show");
    });

    $(document).on("click", ".btn-edit-ticket-type", function(event) {
        $("#div-ticket-type-update-buttons").show();
        $("#div-ticket-type-create-buttons").hide();
        $("#title-modal-ticket-type").html("<i id='icon-back-ticket-type' class='fas fa-arrow-left'></i>  Editar categoría");
        setTicketType();
    });

    $(document).on("click", ".btn-delete-ticket-type", function(event) {
        
        Swal.fire({
            title: 'Eliminar',
            text: "¿Eliminar categoría " + window.ticketTypeSelected.name + "?",
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
          }).then(function (result) {
            if (result.value) deleteTicketType();
          });
    });

    var validator = $("#form-ticket-type").kendoValidator().data("kendoValidator");

    $("#form-ticket-type").submit(function(event) {
        event.preventDefault();

        if(validator.validate())
        {
            let data = $("#form-ticket-type").serializeFormJSON();

            data["showingrid"] = $("#showingrid").is(":checked") ? true : false;

            window.ticketTypeAction == "create" ? createTicketType(data) : updateTicketType(data);
        } 
    });

});

function initListViewTicketTypes()
{
    dataTicketTypes = new kendo.data.DataSource({
        transport: {
            read: {
                url: "getTicketTypes",
                type: "GET",
                dataType: "JSON"
            },
        },
        schema: {
            model: {
                id: "id",
                fields: {
                    id: { type: "number" },
                }
            }
        },
    });

    listViewTicketTypes = $("#listViewTicketTypes").kendoListView({
        dataSource: dataTicketTypes,
        template: kendo.template($("#ticket-type-template").html()),
        selectable: "single",
        height: 600,
        scrollable: "endless",
        change: function(e){
            window.ticketTypeSelected = dataTicketTypes.getByUid(e.sender.select().data("uid")); 
            console.log(window.ticketTypeSelected);
        },
        dataBound: function(e) {
            if(this.dataSource.data().length == 0)
            {
                $("#listViewTicketTypes").append("<div class='text-center alert alert-danger mt-1 alert-validation-msg' role='alert'><span>Arrastre elementos</span> <i class='fal fa-hand-paper mr-1 align-middle'></i></div>");
            }
        }
    }).data("kendoListView");
}

function setTicketType()
{
    $("#modal-list-ticket-type").modal("hide");
    $("#modal-ticket-type").modal("show");
    $("#txt-ticket-type-id").val(window.ticketTypeSelected.id);
    $("#txt-ticket-type-name").val(window.ticketTypeSelected.name);
    $("#txt-ticket-type-description").val(window.ticketTypeSelected.description);

    $('#showingrid').prop('checked', window.ticketTypeSelected.showingrid);

    dropDownListTeamTicketType.value(window.ticketTypeSelected.idteam);
    dropDownListIcon.value(window.ticketTypeSelected.icon);
}


function initDropDownListTeamTicketType()
{
    dropDownListTeamTicketType = $("#dropDownListTeamTicketType").kendoDropDownList({
      optionLabel: "Seleccione Equipo",
      dataTextField: "text",
      dataValueField: "value",
      filter: "contains",
      dataSource: window.global_teams,
      popup: { appendTo: $("#modal-ticket-type") },
      height: 400
    }).data("kendoDropDownList");
}

function initDropDownListIcon()
{
    dropDownListIcon = $("#dropDownListIcon").kendoDropDownList({
      optionLabel: "Seleccione ícono",
      dataTextField: "text",
      dataValueField: "value",
      filter: "contains",
      dataSource: window.dataIcons,
      popup: { appendTo: $("#modal-ticket-type") },
      template: "<span><i class='#: value #'></i> #: text #</span>",
      valueTemplate: "<span><i class='#: value #'></i> #: text #</span>",
      height: 400
    }).data("kendoDropDownList");
}

$("#btn-create-ticket-type").click(function () {
    window.ticketTypeAction = "create";
    $("#form-ticket-type").submit();
});

function createTicketType(data)
{
    let request = callAjax("createTicketType", 'POST', data, true);

    request.done(function(result) {

        if(result.success)
        {
            toastr.success("Categoría " + result.model.name + " creada");
            $("#modal-ticket-type").modal("hide");
            $('#form-ticket-type').trigger("reset");
            listViewTicketTypes.dataSource.read();
        }

    }).fail(function(jqXHR, status) {
        $.unblockUI();
        toastr.error("Error");
    });
}

$("#btn-update-ticket-type").click(function () {
    window.ticketTypeAction = "update";
    $("#form-ticket-type").submit();
});

function updateTicketType(data)
{
    let request = callAjax("updateTicketType", 'POST', data, true);

    request.done(function(result) {

        if(result.success)
        {
            toastr.success("Categoría " + result.model.name + " editada");
            $("#modal-ticket-type").modal("hide");
            $('#form-ticket-type').trigger("reset");
            listViewTicketTypes.dataSource.read();
        }

    }).fail(function(jqXHR, status) {
        $.unblockUI();
        toastr.error("Error");
    });
}

function deleteTicketType()
{
    let request = callAjax("deleteTicketType", 'POST', {"id" : window.ticketTypeSelected.id}, true);

    request.done(function(result) {

        if(result.success)
        {
            toastr.success("Categoría " + result.model.name + " eliminada <button type='button' class='btn btn-light btn-sm' onclick='restoreTicketType()'>DESHACER</button>");
            listViewTicketTypes.dataSource.read();
        }
        else
        {
            toastr.info("Categoría " + result.model.name + " tiene relaciones activas");
        }

    }).fail(function(jqXHR, status) {
        $.unblockUI();
        toastr.error("Error");
    });
}

function restoreTicketType()
{
    let request = callAjax("restoreTicketType", 'POST', {"id" : window.ticketTypeSelected.id}, true);

    request.done(function(result) {

        if(result.success)
        {
            toastr.success("Categoría " + result.model.name + " recuperada");
            listViewTicketTypes.dataSource.read();
        }
        
    }).fail(function(jqXHR, status) {
        $.unblockUI();
        toastr.error("Error");
    });
}

