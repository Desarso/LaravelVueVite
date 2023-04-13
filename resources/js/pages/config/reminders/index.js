window.reminderAction = "create";

$(document).ready(function() {

    initGridReminders();
    initContextMenu();
    initMultiSelectUser();
    initMultiSelectTeam();
    initTimePicker();

    $(document).on("click", "#btn-new-config", function(event) {
        $("#div-reminder-create-buttons").show();
        $("#div-reminder-update-buttons").hide();
        $('#form-reminder').trigger("reset");
        $("#title-modal-reminder").text("Nuevo recordatorio");
        $("#modal-reminder").modal("show");
    });

    var validator = $("#form-reminder").kendoValidator().data("kendoValidator");

    $("#form-reminder").submit(function(event) {
        event.preventDefault();

        if (validator.validate()) {

            let data = $("#form-reminder").serializeFormJSON();

            if (!validateReminder(data)) return;

            window.reminderAction == "create" ? createReminder(data) : updateReminder(data);
        }
    });

    $(document).on("change", ".switch-enabled", function(event) {
        let data = { 'id': $(this).data('id'), 'enabled': $(this).is(':checked') };
        changeReminder(data)
    });

});

function initMultiSelectTeam()
{
    multiSelectTeam = $("#multiSelectTeam").kendoMultiSelect({
        dataTextField: "text",
        dataValueField: "value",
        filter: "contains",
        height: 400,
        dataSource: global_teams,
    }).data("kendoMultiSelect");
}

function initMultiSelectUser()
{
    multiSelectUser = $("#multiSelectUser").kendoMultiSelect({
        dataTextField: "text",
        dataValueField: "value",
        filter: "contains",
        height: 400,
        dataSource: global_users.filter(user => (user.enabled == true && user.deleted_at == null)),
    }).data("kendoMultiSelect");
}

function initTimePicker()
{
    timePicker = $("#time").kendoTimePicker({
    }).data("kendoTimePicker");
}

function initGridReminders()
{
    gridReminders = $("#gridReminders").kendoGrid({
        dataSource: {
            transport: {
                read: {
                    url: "getReminders",
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
                        name: { editable: true, field: "name", type: "string", validation: { required: { message: locale("Name is required") } } },
                        isbranch: { type: "boolean" },
                        cleanable: { type: "boolean" }
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
           { command: { text: "", name: "actions", iconClass: "fas fa-ellipsis-v" }, title: " ", width: "60px" },
           {
                field: "message",
                title: "Mensaje",
                width: "300px",
                filterable: false
            },
            {
               field: "time",
               title: "Hora",
               width: "100px",
               filterable: false,
               template: function(dataItem) {
                    return moment(dataItem.time).format('hh:mm A');
               },
            },
            {
                field: "dow",
                title: "Días",
                width: "200px",
                filterable: false,
                template: function(dataItem) {

                    if(dataItem.dow == null) return "";

                    let result = "";

                    $.each(JSON.parse(dataItem.dow), function(i, day) {
                        result += "<span class='badge badge-secondary mb-1'>" + day + "</span> ";
                    });

                    return result;
                },
            },
            {
                field: "teams",
                title: "Equipos",
                width: "300px",
                filterable: false,
                template: function(dataItem) {

                    if(dataItem.teams == null) return "";

                    let teams = "";

                    let arrayTeams = JSON.parse(dataItem.teams);

                    $.each(arrayTeams, function(i, team) {

                        let teamObject = global_teams.find(o => o.value === team);

                        teams += "<span style='background-color:" + teamObject.color + "' class='badge mb-1'>" + teamObject.text + "</span> ";

                        if (i == 2) return false;
                    });

                    if (arrayTeams.length > 3) teams += "<span style='color:black;' class='badge badge-light teams'>" + (arrayTeams.length - 3) + "+</span>";

                    return teams;
                },
            },
            {
                field: "enabled",
                title: "Estado",
                width: "115px",
                template: function(dataItem) {
                    return "<div class='custom-control custom-switch custom-switch-success switch-lg'>" +
                                "<input id='enabled" + dataItem.id + "' type='checkbox' data-id='" + dataItem.id + "' class='switch-enabled custom-control-input' " + (dataItem.enabled ? "checked" : "") + ">" +
                                "<label class='custom-control-label' for='enabled" + dataItem.id + "'>" +
                                "<span class='switch-text-left'>Activo</span>" +
                                "<span class='switch-text-right'>Inactivo</span>" +
                                "</label>" +
                            "</div>";
                },
                filterable: {
                    ui: function(element) {
                      element.kendoDropDownList({
                        dataTextField: 'text',
                        dataValueField: 'value',
                        dataSource: [{ text: 'Activo', value: true }, { text: 'Inactivo', value: false }]
                      })
                    }
                }
             }
        ],
    }).data("kendoGrid");

    setTimeout(() => { $("#btn-new-config").text("Nuevo recordatorio") }, 300);
}

function initContextMenu()
{
    $("#context-menu").kendoContextMenu({
        target: "#gridReminders",
        filter: "td .k-grid-actions",
        showOn: "click",
        select: function(e) {
            var td = $(e.target).parent()[0];
            window.reminderSelected = gridReminders.dataItem($(td).parent()[0]);

            switch (e.item.id) {
                case "editReminder":
                    setReminder();
                    break;
                case "deleteReminder":
                    confirmDeleteReminder();
                    break;
            };
        }
    });
}

function setReminder()
{
    $("#div-reminder-update-buttons").show();
    $("#div-reminder-create-buttons").hide();

    $('#form-reminder').trigger("reset");

    $("#title-modal-reminder").text("Editar recordatorio");
    $("#modal-reminder").modal("show");

    $("#txt-reminder-id").val(window.reminderSelected.id);
    $("#txt-reminder-message").val(window.reminderSelected.message);

    timePicker.value(moment(new Date(window.reminderSelected.time)).format("hh:mm A"));

    var dow = JSON.parse(window.reminderSelected.dow);

    $.each($("input[name='dow[]']"), function(){
        $(this).prop("checked", ($.inArray($(this).val(), dow) != -1));
    });

    setTimeout(() => {
        multiSelectTeam.value(JSON.parse(window.reminderSelected.teams));
        multiSelectUser.value(JSON.parse(window.reminderSelected.users_exception));
    }, 100);

}

$("#btn-create-reminder").click(function() {
    window.reminderAction = "create";
    $("#form-reminder").submit();
});

function createReminder(data)
{
    let request = callAjax("createReminder", 'POST', data, true);

    request.done(function(result) {

        if (result.success) {
            toastr.success("Recordatorio creado");
            $("#modal-reminder").modal("hide");
            $('#form-reminder').trigger("reset");
            gridReminders.dataSource.read();
        }

    }).fail(function(jqXHR, status) {
        $.unblockUI();
        toastr.error("Error");
    });
}

$("#btn-update-reminder").click(function() {
    window.reminderAction = "update";
    $("#form-reminder").submit();
});

function updateReminder(data)
{
    let request = callAjax("updateReminder", 'POST', data, true);

    request.done(function(result) {

        if (result.success) {
            toastr.success("Recordatorio editado");
            $("#modal-reminder").modal("hide");
            $('#form-reminder').trigger("reset");
            gridReminders.dataSource.read();
        }

    }).fail(function(jqXHR, status) {
        $.unblockUI();
        toastr.error("Error");
    });
}

function changeReminder(data)
{
   let request = callAjax("changeReminder", 'POST', data, true);

   request.done(function(result) {

       if(result.success)
       {
           toastr.success("Reminder editado");
           gridReminders.dataSource.read();
       }

   }).fail(function(jqXHR, status) {
       $.unblockUI();
       toastr.error("Error");
   });
}

function confirmDeleteReminder()
{
    Swal.fire({
        title: 'Eliminar',
        text: "¿Eliminar recordatorio?",
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
    }).then(function(result) {
        if (result.value) deleteReminder();
    });
}

function deleteReminder()
{
    let request = callAjax("deleteReminder", 'POST', { "id": window.reminderSelected.id }, true);

    request.done(function(result) {

        if(result.success)
        {
            toastr.success("Recordatorio eliminado");
            gridReminders.dataSource.read();
        }

    }).fail(function(jqXHR, status) {
        $.unblockUI();
        toastr.error("Error");
    });
}

function validateReminder(data)
{
    if ($("#message").val() == "" || multiSelectTeam.value().length == 0 || timePicker.value() == null || data['dow[]'] == undefined)
    {
        PNotify.closeAll();
        PNotify.error({ title: 'Datos incompletos', text: 'Complete los datos del formulario' });
        return false;
    } 

    return true;
}