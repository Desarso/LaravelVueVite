$(document).ready(function() {

    window.treeView = new WHTreeView(window.hierarchySpots, checkSpots);

    window.sidebar_clone = $('#sidebarfields').clone(true);
    window.lastTicketType = null;
    FileInputModule.init({ id: "1" }, $("#imagesContainer"));
    initMultiSelectItem();

    $(".hide-data-sidebar, .cancel-data-btn, .overlay-bg").on("click", function() {
        hideTaskSidebar();
        setTimeout(() => { multiSelectItem.close() }, 300);

    });

    $(document).on("click", ".card-ticket-type", function(event) {
        $("#btnAddTask").click();
        let idtype = $(this).data("idtype")
        let filters = [{ field: "idtype", operator: "eq", value: idtype }];
        multiSelectItem.dataSource.filter({ logic: "or", filters: filters });
        multiSelectItem.listView.setDSFilter(multiSelectItem.dataSource.filter());
        window.filterItems = filters;
        setTimeout(() => { multiSelectItem.open() }, 300);
    });

    $(document).on("click", "#btn-tree-spots", function(event) {
        window.treeView.open();
    });

    $(document).on("change", "#byclient", function(event) {
        $(this).is(":checked") ? multiSelectPriority.value(3) : multiSelectPriority.value(1);
    });

    $('#modal-task').on('hidden.bs.modal', function () {
        console.log('Close modal');
    });
    
});

function checkSpots(data)
{
    multiSelectSpot.value(data.spots);
}

function initKendoControls() {

    initMultiSelectSpot();
    initMultiSelectUser();
    initMultiSelectPriority();
    initMultiSelectCopy();
    initMultiSelectTag();
    initMultiSelectAsset();
    initMultiSelectApprovers();
    initDateTimePickerDuedate();
}

function initDateTimePickerDuedate()
{
    dateTimePickerDuedate = $("#duedate").kendoDateTimePicker({
        //value: new Date()
        culture: "es-ES",
        format: "MM/dd/yyyy hh:mm tt"
    }).data("kendoDateTimePicker");
}

function initMultiSelectItem() {
    multiSelectItem = $("#multiSelectItem").kendoMultiSelect({
        placeholder: locale("Task"),
        dataTextField: "name",
        dataValueField: "id",
        filter: "contains",
        filtering: function(ev) {

            var filterValue = ev.filter != undefined ? ev.filter.value : "";
            ev.preventDefault();

            let filter = [{
                logic: "or",
                filters: [
                    {
                        field: "name",
                        operator: "contains",
                        value: filterValue
                    },
                    {
                        field: "code",
                        operator: "contains",
                        value: filterValue
                    }
                ]
            }];

            if (window.filterItems) {
                filter.push(window.filterItems[0]);
            }

            this.dataSource.filter(filter);
        },
        itemTemplate: '<span class="k-state-default"><i class="#:data.icon#" style="color:#:data.color#"></i>  </span> <span>#:data.name#</span>',
        tagTemplate: '<span class="selected-value"><i class="#:data.icon#" style="color:#:data.color#"></i>  </span> <span>#:data.name#</span>',
        noDataTemplate: $("#noDataTemplate").html(),
        height: 400,
        dataSource: getItems(),
        maxSelectedItems: 1,
        select: selectItem,
        deselect: deselectItem,
        change: changeItem
    }).data("kendoMultiSelect");
}

function getItems()
{
    let teams = global_permissions.map(function(obj) { return obj.idteam; });

    return window.global_items.filter(item => ((item.isprivate == 0 || $.inArray(item.idteam, teams) != -1) && item.showingrid == 1 && item.enabled == 1));
}

function initMultiSelectAsset() {
    multiSelectAsset = $("#multiSelectAsset").kendoMultiSelect({
        placeholder: locale("Asset"),
        dataTextField: "name",
        dataValueField: "id",
        filter: "contains",
        itemTemplate: '<span class="k-state-default"><i class="#:data.icon#" style="color:#:data.color#"></i>  </span> <span>#:data.name#</span>',
        tagTemplate: '<span class="selected-value"><i class="#:data.icon#" style="color:#:data.color#"></i></span> <span>#:data.name#</span>',
        height: 400,
        dataSource: window.global_assets,
        maxSelectedItems: 1
    }).data("kendoMultiSelect");
}

function changeItem(e)
{

}

function selectItem(e)
{
    let item = e.dataItem;

    if(checkPermission(item.idteam, "assigntask"))
    {
        setTimeout(() => {
            $("#divUsers").css("display", "block");
            $("#divCopies").css("display", "block");
        }, 300);
    }

    // Apply Template
    applyTemplate(item);

    // Default users for item 
    let users = item.users.map(function(obj) { return obj.value; });
    multiSelectUser.value(users);

    if (item.spots != null) {
        filterMultiSelectSpot(item.spots);
    }

    multiSelectPriority.value(item.idpriority);
}

// Apply Ticket Type Template
function applyTemplate(item) {
    console.log('Apply Template');
    // Ocupo clonar sidebarfields...por si se selecciona otro item de otro tipo
    // y ocupamos redibujar el template.


    // Obtenemos el template basado en el tipo de ticket del item.
    let type = global_ticket_types.find(o => o.value === item.idtype);
    if (typeof type != 'undefined') {
        $('#createTaskIcon').removeClass().addClass(type.icon).css('color', type.color);
        $('#_createTaskTitle').html(type.text);
        if (window.lastTicketType == item.idtype) { console.log('Mismo Ticket Type..no cambio template'); return; }
        window.lastTicketType = item.idtype;
        let template = JSON.parse(type.template);
        if (template != null) {
            typeof template.width != 'undefined' ? $('.add-new-data').width(template.width) : $('.add-new-data').width('30.57rem');

            $('#sidebarfields').html(window.sidebar_clone.html()); // Restauramos sidebar con elementos en posición original
            $('#createTaskShortDescription').html(template.shortdescription);
            if (template.template != null) {
                template.template.forEach(e => {
                    // get DOM element
                    let el = $('div[data-template=' + e.field + ']');
                    e.attributes.forEach(a => {
                        switch (a.name) {
                            // Show or hide element
                            case 'hidden':
                                a.value == false ? el.removeClass('hidden') : el.addClass('hidden');
                                break;
                                // Position of element in the form
                            case 'position':
                                $('#sidebarfields div:nth-child(' + a.value + ')').after(el);
                                break;
                                // change default label
                            case 'label':
                                $('div[data-template=' + e.field + '] label').text(locale(a.value));
                                break;
                                // bolden element    
                            case 'highlight':
                                $('div[data-template=' + e.field + '] label').css('font-weight', 'bold');
                                break;
                                // add tooltip to element       
                            case 'tooltip':
                                $("#" + e.field + ' label').attr('title', a.value);
                            default:
                                // code block
                        }
                    });
                });
            } else {
                console.log('No tiene Template.template');

            }
            initKendoControls();

        } else { // Template is null
            console.log('No tiene Template');
            $('#createTaskShortDescription').html('');
            $('#sidebarfields').html(window.sidebar_clone.html());
            $('.add-new-data').width('30.57rem');
            initKendoControls();
        }
    }

    if(window.selectedTicket != null)
    {
        setTimeout(() => {
        
            $("#code").val(window.selectedTicket.code);
            $("#description").val(window.selectedTicket.description);
            $("#justification").val(window.selectedTicket.justification);
            multiSelectSpot.value(window.selectedTicket.idspot);
    
            let users  = window.selectedTicket.users.map(function(obj) { return obj.iduser; });
            let copies = window.selectedTicket.users_copy.map(function(obj) { return obj.iduser; });
    
            users = (Array.isArray(users) ? users : [users]); 
            copies = (Array.isArray(copies) ? copies : [copies]); 
    
            multiSelectUser.value(users);
            multiSelectCopy.value(copies);
    
        }, 300);
    }

    $("#code").val((window.selectedTicket != null ? window.selectedTicket.code : null));
    // Mostrar el formulario
    $("#sidebarfields").removeClass('hidden');
}

function deselectItem(e) {
    multiSelectUser.value([]);
    clearFilterMultiSelectSpot();
}

initMultiSelectSpot = function initMultiSelectSpot() {
    let dataSpots = getUserSpots();

    multiSelectSpot = $("#multiSelectSpot").kendoMultiSelect({
        placeholder: locale("Spot"),
        dataTextField: "text",
        dataValueField: "value",
        itemTemplate: '<span class="k-state-default"><span style="font-size:1em;">#: data.text #</span><span style="font-size:0.9em; color:lightgray;">&nbsp;#: data.spotparent #</span></span>',
        tagTemplate:  '<span class="k-state-default"><span style="font-size:1em;">#: data.text #</span><span style="font-size:0.9em; color:gray;">&nbsp;&nbsp;#: data.spotparent #</span></span>',
        filter: "contains",
        height: 400,
        dataSource: dataSpots.filter(spot => (spot.enabled == 1)),
        noDataTemplate: $("#noDataTemplate").html(),
        change: changeSpot
    }).data("kendoMultiSelect");
}

function changeSpot()
{
    if(multiSelectSpot.value().length == 1)
    {
        console.log(multiSelectSpot.value());

        let filteredUsers = global_users.filter(user => (user.enabled == true && user.deleted_at == null) && ($.inArray(multiSelectSpot.value()[0], JSON.parse(user.spots)) != -1));

        multiSelectUser.setDataSource(filteredUsers);
    }
    else
    {
        let filteredUsers = global_users.filter(user => (user.enabled == true && user.deleted_at == null));

        multiSelectUser.setDataSource(filteredUsers);
    }
}

function filterMultiSelectSpot(spots) {
    let filters = [{ field: "value", operator: "eq", value: 0 }];

    $.each(JSON.parse(spots), function(index, item) {
        filters.push({ field: "value", operator: "eq", value: item });
    });

    multiSelectSpot.dataSource.filter({ logic: "or", filters: filters });
    multiSelectSpot.listView.setDSFilter(multiSelectSpot.dataSource.filter());
}

function clearFilterMultiSelectSpot()
{
    multiSelectSpot.dataSource.filter([]);
    multiSelectSpot.listView.setDSFilter(multiSelectSpot.dataSource.filter());
}

function clearFilterMultiSelectItem() {
    multiSelectItem.dataSource.filter([]);
    multiSelectItem.listView.setDSFilter(multiSelectItem.dataSource.filter());
    window.filterItems = null;
}

function initMultiSelectPriority() {
    multiSelectPriority = $("#multiSelectPriority").kendoMultiSelect({
        placeholder: locale("Priority"),
        dataTextField: "text",
        dataValueField: "value",
        filter: "contains",
        height: 400,
        dataSource: window.global_priorities,
        maxSelectedItems: 1,
        value: [1]
    }).data("kendoMultiSelect");
}

function initMultiSelectWarehouseStatus() {
    multiSelectWarehouseStatus = $("#multiSelectWarehouseStatus").kendoMultiSelect({
        placeholder: locale("WarehouseStatus"),
        dataTextField: "text",
        dataValueField: "value",
        filter: "contains",
        height: 400,
        dataSource: window.global_WarehouseStatus,
        maxSelectedItems: 1,
        value: [1]
    }).data("kendoMultiSelect");
}

function initMultiSelectUser() {

    multiSelectUser = $("#multiSelectUser").kendoMultiSelect({
        placeholder: locale("Responsible"),
        dataTextField: "text",
        dataValueField: "value",
        filter: "contains",
        height: 400,
        itemTemplate: "<span class='k-state-default' style='background-image: url(#:urlpicture#)'></span><span class='k-state-default'><h4>#: text #</h4><p>#: text #</p></span>",
        tagTemplate: "<span class='selected-value' style='background-image: url(#:urlpicture#)'></span><span>#: text #</span>",
        dataSource: global_users.filter(user => (user.enabled == true && user.deleted_at == null)),
    }).data("kendoMultiSelect");
}

function initMultiSelectApprovers() {
    multiSelectApprovers = $("#multiSelectApprovers").kendoMultiSelect({
        placeholder: locale("If approval needed"),
        dataTextField: "text",
        dataValueField: "value",
        filter: "contains",
        height: 400,
        itemTemplate: "<span class='k-state-default' style='background-image: url(#:urlpicture#)'></span><span class='k-state-default'><h3>#: text #</h3><p>#: text #</p></span>",
        tagTemplate: "<span class='selected-value' style='background-image: url(#:urlpicture#)'></span><span>#: text #</span>",
        dataSource: global_users,
    }).data("kendoMultiSelect");
}

function initMultiSelectCopy() {
    multiSelectCopy = $("#multiSelectCopy").kendoMultiSelect({
        placeholder: locale("Copy to"),
        dataTextField: "text",
        dataValueField: "value",
        filter: "contains",
        height: 400,
        itemTemplate: "<span class='k-state-default' style='background-image: url(#:urlpicture#)'></span><span class='k-state-default'><h3>#: text #</h3><p>#: text #</p></span>",
        tagTemplate: "<span class='selected-value' style='background-image: url(#:urlpicture#)'></span><span>#: text #</span>",
        dataSource: global_users.filter(user => (user.enabled == true && user.deleted_at == null)),
    }).data("kendoMultiSelect");
}

function initMultiSelectTag() {
    multiSelectTag = $("#multiSelectTag").kendoMultiSelect({
        placeholder: locale("Tags"),
        dataTextField: "text",
        dataValueField: "value",
        filter: "contains",
        height: 400,
        dataSource: global_tags,
        noDataTemplate: $("#noDataTemplate").html(),
        select: function(e) {
            setTimeout(function() {
                e.sender.tagList.children().each(function(idx, item) {
                    $(item).css('background-color', e.sender.dataItems()[idx].color);
                    $(item).css('color', 'white');
                })
            });
        },
    }).data("kendoMultiSelect");
}

$("#btnCreateTicket").click(function() {
    let data = $("#formTicket").serializeFormJSON();
    data['byclient'] = $("#byclient").is(":checked") ? 1 : 0;
    data['byresource'] = $("#byresource").is(":checked") ? 1 : 0;
    data['action'] = 'create';
    createTicket(data);
});

$("#btnAddTask").click(function() {
    multiSelectItem.setDataSource(getItems());
    clearFormTicket();
    window.selectedTicket = null;
    $("#btnCreateTicket").show();
    $("#btnUpdateTicket").hide();
    $("#label-team").hide();
    showTaskSidebar();
});

showTaskSidebar = function showTaskSidebar() {

    if(!window.isUpdating) window.selectedTicket = null;

    $(".add-new-data").addClass("show");
    $(".overlay-bg").addClass("show");
}

function hideTaskSidebar() {
    window.isUpdating = false;
    $(".add-new-data").removeClass("show");
    $(".overlay-bg").removeClass("show");
}

function createTicket(data) {
    if (!validateTicket()) return;

    let request = callAjax('createTicket', 'POST', data, true);

    request.done(function(result) {

        if (result.success) {
            clearFormTicket();
            hideTaskSidebar();
            PNotify.success({ title: 'Acción completada con éxito', text: 'Nueva tarea' });
            //toastr.success('Acción completada con éxito', 'Nueva tarea');
            $("#gridTicket").data("kendoGrid").dataSource.read();
            $("#modal-task").modal("hide");
            FileInputModule.uploadFiles(result.tickets, [{ id: 1 }], false);
        } else {
            toastr.error(result.message, 'Permisos');
        }

    }).fail(function(jqXHR, status) {
        toastr.error('La acción no se puedo completar', 'Hubo un problema!');
    });
}

function validateTicket() {
    if (multiSelectSpot.dataItems().length == 0 || multiSelectItem.dataItems().length == 0) {
        //PNotify.closeAll();
        PNotify.error({ title: 'Datos incompletos', text: 'Complete los datos' });
        return false;
    }

    return true;
}

getTicket = function getTicket() {
    $("#sidebarfields").removeClass('hidden');

    let request = callAjax('getTicket', 'POST', { 'idticket': window.selectedTicket.id }, true);

    request.done(function(result) {
        //PNotify.closeAll();
        window.selectedTicket = result;
        setTicket(result);

    }).fail(function(jqXHR, status) {
        toastr.error('La acción no se puedo completar', 'Problemas');
    });
}

function setTicket(ticket)
{
    if(checkPermission(ticket.idteam, "assigntask"))
    {
        setTimeout(() => {
            $("#divUsers").css("display", "block");
            $("#divCopies").css("display", "block");
        }, 300);
    }

    let team = window.global_teams.find(o => o.value === ticket.idteam);

    $("#label-team").show();
    $("#label-team").html("<i class='fad fa-users'></i> " + team.text);

    let users = ticket.users.map(function(obj) { return obj.iduser; });
    let copies = ticket.users_copy.map(function(obj) { return obj.iduser; });
    let tags = ticket.tags.map(function(obj) { return obj.idtag; });
    let approvers = ticket.approvers.map(function(obj) { return obj.iduser; });

    multiSelectItem.value(ticket.iditem);
    applyTemplate(multiSelectItem.dataItems()[0]);
    multiSelectSpot.options.maxSelectedItems = 1;
    multiSelectSpot.setDataSource(getUserSpots());
    multiSelectUser.setDataSource(global_users);
    multiSelectCopy.setDataSource(global_users);

    multiSelectSpot.value(ticket.idspot);
    multiSelectUser.value(users);
    multiSelectCopy.value(copies);
    multiSelectTag.value(tags);
    multiSelectApprovers.value(approvers);
    multiSelectTag.trigger('select')
    multiSelectPriority.value(ticket.idpriority);
    multiSelectAsset.value(ticket.idasset);

    let duedate = (ticket.duedate == null ? null : new Date(ticket.duedate));
    dateTimePickerDuedate.value(duedate);

    $("#description").val(ticket.description);
    $("#justification").val(ticket.justification);
    $("#code").val(ticket.code);
    ticket.byclient == 1 ? $('#byclient').prop('checked', true) : $('#byclient').prop('checked', false);

    FileInputModule.init({ id: 1 }, $("#imagesContainer"), ticket.files, ticket.id);

    let user = getUser(ticket.created_by);
    $("#lbl-created-by").text("Creado por: " + user.text).show();
}

$("#btnUpdateTicket").click(function() {
    let data = $("#formTicket").serializeFormJSON();
    data['idticket'] = window.selectedTicket.id;
    data['action'] = 'edit';
    data['byclient'] = $("#byclient").is(":checked") ? 1 : 0;
    updateTicket(data);
});

function updateTicket(data) {
    if (!validateTicket()) return;

    data['idspot'] = data['spots'];
    let request = callAjax('updateTicket', 'POST', data, true);
    request.done(function(result) {
        if (result.success) {
            clearFormTicket();
            hideTaskSidebar();
            toastr.success('Acción completada con éxito', 'Tarea actualizada');
            $("#gridTicket").data("kendoGrid").dataSource.read();
            $("#modal-task").modal("hide");
            FileInputModule.uploadFiles(window.selectedTicket.id, [{ id: 1 }], true);
        } else {
            toastr.error(result.message, 'Permisos');
        }

    }).fail(function(jqXHR, status) {
        toastr.error('La acción no se puedo completar', 'Problemas');
    });
}

deleteTicket = function deleteTicket() {

    let confirm = showConfirmModal('Eliminar ' + window.selectedTicket.id + ' - ' + window.selectedTicket.name, '¿Estás seguro?');

    confirm.on('pnotify.confirm', function() {

        let request = callAjax('deleteTicket', 'POST', { 'idticket': window.selectedTicket.id, 'action': 'delete' }, true);

        request.done(function(result) {

            PNotify.closeAll();

            if(result.success) {
                PNotify.success({ title: 'Tarea eliminada', text: 'Acción completada con éxito' });
                $("#gridTicket").data("kendoGrid").dataSource.read();
            }
            else
            {
                PNotify.error({ title: 'Permisos', text: result.message });
            }

        }).fail(function(jqXHR, status) {
            PNotify.closeAll();
            PNotify.error({ title: 'Problemas', text: 'La acción no se puedo completar' });
        });

    });
}

clearFormTicket = function clearFormTicket() {
    window.lastTicketType = null;
    $('#formTicket').trigger("reset");
    $('#sidebarfields').addClass('hidden');
    $('#createTaskIcon').removeClass().addClass('fa fa-list-alt').css('color', '#15AABF');
    $('#_createTaskTitle').html(locale('Add Task'));
    $('#createTaskShortDescription').html('');
    $('.add-new-data').width('30.57rem');
    clearFilterMultiSelectItem();
    FileInputModule.init({ id: "1" }, $("#imagesContainer"));
    $('#lbl-created-by').hide();
}

function checkPermission(idteam, action)
{
    let data = window.global_permissions.find(o => o.idteam === idteam);

    if(typeof data == 'undefined') return false;

    let permissions = JSON.parse(data.permissions);

    if(permissions.hasOwnProperty(action))
    {
        return permissions[action];
    }
    else
    {
        return false;
    }
}