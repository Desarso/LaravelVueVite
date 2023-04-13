window.spotDragged    = null;
window.planDragged    = null;
window.userZone       = null;
window.elementDragged = null;
window.lastCleaningUpdated = null;

$(document).ready(function() {

    initGridCleaningAssign();
    initGridAvailableSpots();
    initMultiSelectCleaningStaff();
    initMultiSelectCleaningItem();
    initMultiSelectCleaningSpot();
    initTimePickerCleanat();

    fixKendoGridHeight();

    $(document).on("click", ".badge-spot", function(event) {
        findCleaningPlan($(this).data("idplan"));
        $("#div-delete-plan").show();
        $("#modal-cleaning-plan").modal("show");
    });

    $('#modal-cleaning-plan').on('show.bs.modal', function(e) {
        $("#form-clening-plan").trigger("reset");

        setTimeout(() => {
            multiSelectCleaningItem.value(cleaningSettings.default_cleaning_item);   
        }, 200);
    })

    //Refresca la grilla al encontrar un cambio
    setInterval(() => { getLastCleaningChange(); }, 10000);
});

$("#btnNewPlan").click(function(e) {
    $("#div-delete-plan").hide();
    $("#modal-cleaning-plan").modal("show");
});

$("#btnEditCleaningPlan").click(function(e) {
    var data = $("#form-clening-plan").serializeArray();
    if(validateData()) editCleaningPlan(data);
});

$("#btnGeneratePlan").click(function(e) {

    let confirm = showConfirmModal('Generar limpiezas', '¿Estás seguro?');

    confirm.on('pnotify.confirm', function() {
        generateCleaningPlan();
    });

});

$("#switch-delete-plan").change(function(e) {
    var confirm = showConfirmModal('¿Eliminar limpieza?', '¿Estás seguro?');

    confirm.on('pnotify.confirm', function() {
        deleteCleaningPlan();
    })
    
    confirm.on('pnotify.cancel', function() {
        $("#switch-delete-plan").prop("checked", false);
    });
});

function initMultiSelectCleaningStaff()
{
    multiSelectCleaningStaff = $("#multiSelectCleaningStaff").kendoMultiSelect({
        placeholder: locale("Responsible"),
        dataTextField: "text",
        dataValueField: "value",
        filter: "contains",
        height: 400,
        tagTemplate: "<span class='selected-value' style='background-image: url(#:urlpicture#)'></span><span>#: text #</span>",
        itemTemplate: "<span class='selected-value' style='background-image: url(#:urlpicture#)'></span><span>#: text #</span>",
        dataSource: window.cleaningStaff,
        maxSelectedItems: 1
    }).data("kendoMultiSelect");
}

function initMultiSelectCleaningSpot()
{
    multiSelectCleaningSpot = $("#multiSelectCleaningSpot").kendoMultiSelect({
        placeholder: "Lugar",
        dataTextField: "text",
        dataValueField: "value",
        filter: "contains",
        height: 400,
        dataSource: window.cleaningSpots,
        maxSelectedItems: 1
    }).data("kendoMultiSelect");
}

function initMultiSelectCleaningItem()
{
    multiSelectCleaningItem = $("#multiSelectCleaningItem").kendoMultiSelect({
        placeholder: "Tipo de limpienza",
        dataTextField: "text",
        dataValueField: "value",
        filter: "contains",
        height: 400,
        dataSource: {
            transport: { read: "getCleaningItems" }
        },
        maxSelectedItems: 1
    }).data("kendoMultiSelect");
}

function initTimePickerCleanat()
{
    timePickerCleanat = $("#timePickerCleanat").kendoTimePicker({
        //dateInput: true
    }).data("kendoTimePicker");
}

function generateCleaningPlan()
{
    let request = callAjax('generateCleaningPlan', 'POST', null, true);
    request.done(function(result) {

        gridCleaningAssign.dataSource.read();
        gridAvailableSpots.dataSource.read();

    }).fail(function(jqXHR, status) {
        console.log('error during generate cleaning plane!');
    });
}

function initGridCleaningAssign()
{
    gridCleaningAssign = $("#gridCleaningAssign").kendoGrid({
        excel: {
            fileName: "Whagons Cleaning Schedules.xlsx",
        },
        dataSource: {
            transport: {
                read: {
                    url: "getCleaningStaffWithPlans",
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
                    }
                }
            },
        },

        editable: {
            mode: "popup"
        },
        rowTemplate: "#=rowTemplate(data)#", 
        dataBound: function(e) {
            initDragAndDrop();
        },
        reorderable: true,
        resizable: true,
        sortable: true,
        pageable: {
            refresh: true,
            pageSizes: true,
            buttonCount: 5,
        },
        filterable: true,
        columns: [
            {
                field: "id",
                title:  "Responsable",
                values: window.cleaningStaff,
                width: "300px",
                media: "(min-width: 450px)",
                filterable: {
                    multi: true,
                    search: true
                }
            },
            {
                field: "total",
                title: "Asignadas",
                width: "100px",
                media: "(min-width: 450px)",
                filterable: false
            },
            {
                field: "total",
                title: "Limpio",
                width: "100px",
                media: "(min-width: 450px)",
                filterable: false
            },
            {
                field: "total",
                title: "Sucio",
                width: "100px",
                media: "(min-width: 450px)",
                filterable: false
            },
            {
                field: "total",
                title:  "Progreso",
                width: "300px",
                media: "(min-width: 450px)",
                filterable: false
            },
        ],
    }).data("kendoGrid");
}

function initGridAvailableSpots()
{
    gridAvailableSpots = $("#gridAvailableSpots").kendoGrid({
        excel: {
            fileName: "Whagons Cleaning Schedules.xlsx",

        },
        dataSource: {
            transport: {
                read: {
                    url: "getAvailableSpots",
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
                        name: { editable: true, field: "name", type: "string" }
                    }
                }
            },
        },
        editable: {
            mode: "popup"
        },
        toolbar: ["search"],
        reorderable: true,
        resizable: true,
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
                title: "Lugar",
                template: "#=templateSpot(data)#", 
                width: "200px",
                media: "(min-width: 300px)",
                filterable: false
            }
        ],
    }).data("kendoGrid");
}

function templateSpot(data)
{
    let badge = data.cleaning_plans_count > 0 ? "<span class='badge badge-pill badge-danger badge-glow'>" + data.cleaning_plans_count + "</span>" : "";
    return badge + "  <strong class='tr-spot'>" + data.name + "</strong>";
}

function initDragAndDrop()
{
    $("#gridAvailableSpots").kendoDraggable({
        filter: "tr[role='row']",
        hint: function(e) {
            var item = $('<div class="k-grid k-widget" style="background-color:#ea5455; color:#ffffff; border-radius: 5px;"><table><tbody><tr>' + e.html() + '</tr></tbody></table></div>');
            return item;
        },
        dragstart: spotDragStart,
    });

    $("#gridCleaningAssign").kendoDraggable({
        filter: ".badge-spot",
        hint: function(e) {
            return '<div class="badge-spot badge badge-danger badge-lg ml-1 mb-1">' + e.html() + '</div>';
        },
        dragstart: planDragStart,
    });

    $("#gridCleaningAssign").kendoDropTargetArea({
        filter: ".drop-zone",
        dragenter: droptargetOnDragEnter,
        drop: droptargetOnDrop
    });
}

function spotDragStart(e)
{
    window.elementDragged = "spot";
    window.spotDragged    = gridAvailableSpots.dataItem($(e.currentTarget).closest("tr"));
}

function planDragStart(e)
{
    window.elementDragged = "plan";
    window.planDragged    = {"id" : $(e.currentTarget).data('idplan')};
}

function droptargetOnDragEnter(e)
{
    let uid = $(e.dropTarget).data('uid');
    window.userZone = gridCleaningAssign.dataSource.getByUid(uid);
}

function droptargetOnDrop(e)
{
    window.elementDragged == "spot" ? assignCleaning() : moveCleaningPlan();
}

function assignCleaning()
{
    let request = callAjax('assignCleaning', 'POST', {"iduser" : window.userZone.id, "idspot" : window.spotDragged.id}, true);

    request.done(function(result) {

        if(result.success)
        {
            gridCleaningAssign.dataSource.read();
            gridAvailableSpots.dataSource.read();
        }

    }).fail(function(jqXHR, status) {

    });
}

function moveCleaningPlan()
{
    let request = callAjax('moveCleaningPlan', 'POST', {"iduser" : window.userZone.id, "id" : window.planDragged.id}, true);

    request.done(function(result) {

        if(result.success)
        {
            gridCleaningAssign.dataSource.read();
        }

    }).fail(function(jqXHR, status) {

    });
}

function deleteCleaningPlan()
{
    let request = callAjax('deleteCleaningPlan', 'POST', {"id" : $("#idCleaningPlan").val() }, true);

    request.done(function(result) {

        if(result.success)
        {
            $("#modal-cleaning-plan").modal("hide");
            gridCleaningAssign.dataSource.read();
            gridAvailableSpots.dataSource.read();
        }

    }).fail(function(jqXHR, status) {

    });
}

function findCleaningPlan(idplan)
{
    let request = callAjax('findCleaningPlan', 'POST', { "id" : idplan }, true);

    request.done(function(result) {

        multiSelectCleaningStaff.value(result.iduser);
        multiSelectCleaningItem.value(result.iditem);
        multiSelectCleaningSpot.value(result.idspot);
        $('#idCleaningPlan').val(result.id);

        timePickerCleanat.value(result.cleanat);

    }).fail(function(jqXHR, status) {

    });
}

function editCleaningPlan(data)
{
    let request = callAjax('editCleaningPlan', 'POST', data, true);

    request.done(function(result) {

        PNotify.success({ title: 'Plan de limpieza', text: 'Acción completada con éxito' });
        gridCleaningAssign.dataSource.read();
        gridAvailableSpots.dataSource.read();
        $("#modal-cleaning-plan").modal("hide");

    }).fail(function(jqXHR, status) {

    });
}

function rowTemplate(data)
{
    let user = formatStaff(data.id);

    let spots = "";

    for(plan of data.cleaning_plans)
    {
        spots += formatSpot(plan);
    }

    return "<tr style='box-shadow: 1px 2px 5px 0px rgba(0,0,0,0.75);'>" +
                    "<td>" + user + "</td>"+
                    "<td>" + data.total + "</td>" +
                    "<td>" + data.clean + "</td>" +
                    "<td>" + data.dirty_plan + "</td>" +
                    "<td><div id='cleaning-progress-bar' class='progress progress-bar-success progress-lg'><div class='progress-bar progress-bar-striped progress-bar-animated' role='progressbar' aria-valuenow='80' aria-valuemin='80' aria-valuemax='100' style='width:" + data.average + "%'>" + data.average + "%</div></div></td>" +
                    "</tr>" +
                    "<tr data-uid='" + data.uid + "' class='drop-zone' style='background-color: rgba(33,38,41,0.15); height:64px'>" +
                        "<td colspan='5' style=''>" + spots + "</td>"+
                    "</tr>";
}

function formatStaff(iduser)
{
    let user = getUser(iduser);
    if (user == null) return '';

    return "<li data-toggle='tooltip' data-popup='tooltip-custom' data-placement='top' data-original-title='" + user.text + "' class='avatar pull-up'>" +
            "<img class='media-object rounded-circle' src='" + user.urlpicture + "' alt='Avatar' height='30' width='30'>" +
        "</li><strong>" + user.text + "</strong>";
}

function formatSpot(plan)
{
    let spot = getSpot(plan.idspot);
    let status = getCleaningStatus(plan.idcleaningstatus);

    let style = plan.done ? "background-color:#00cfe8; text-decoration:line-through;" : "background-color:" + status.background;

    return "<div data-idplan='" + plan.id + "' class='badge-spot badge badge-danger badge-lg' style='" + style + "'>" + (spot.shortname == null ? spot.text : spot.shortname) + "</div>";
}

function getCleaningStatus(id)
{
    let result = cleaningStatus.find(o => o.id === id);
    return (typeof result == "undefined" ? null : result);
}

function validateData()
{
    if(multiSelectCleaningItem.dataItems().length == 0 || multiSelectCleaningSpot.dataItems().length == 0 || multiSelectCleaningStaff.dataItems().length == 0)
    {
        PNotify.closeAll();
        PNotify.error({ title: 'Datos incompletos', text: 'Complete los datos' });
        return false;
    }

    return true;
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
            gridCleaningAssign.dataSource.read();
            gridAvailableSpots.dataSource.read();
        }
    });
}