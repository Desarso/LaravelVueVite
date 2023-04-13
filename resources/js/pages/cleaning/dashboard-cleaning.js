var timer;
window.menuData = [];
window.spotSelected = [];
window.planSelected = null;
window.lastCleaningChange = null;

$(document).ready(function() {

    formatCleaningStatusDataMenu();

    initDropDownListCleaningBranchFilter()
    initDropDownListCleaningStatusFilter();
    initDropDownListCleaningSpotFilter();

    initListViewSpots();

    initDropDownListCleaningSpot();
    initDropDownListCleaningItem();
    initDropDownListCleaningStaff();
    initTimePickerCleanat();

    initGridCleaningPlans();
    initGridCleaningChecklist();
    initGridCleaningNotes();

    initPlanContextMenu();

    $(document).on("click", ".spot-cleaning", function(e) {

        window.spotSelected = cleaningSpots.find(o => o.value === $(e.currentTarget).data("idspot")); 

        $("#modal-title-cleaning-details").text(window.spotSelected.text);

        gridCleaningPlans.dataSource.read();
        gridCleaningChecklist.dataSource.read();
        gridCleaningNotes.dataSource.read();

        $("#nav-task-tab").click();

        $("#modal-cleaning-details").modal("show");

    });

    //Refresca cuando se cambia directo el estado de limpieza del spot
    setInterval(() => { getLastCleaningChange(); }, 10000);

});

$('#modal-cleaning-plan').on('hidden.bs.modal', function(e) {
    $("#form-clening-plan").trigger("reset");
});

$('#modal-cleaning-details').on('hidden.bs.modal', function(e) {
    window.planSelected = null;
});

$("#search").keyup(function() {

    clearTimeout(timer);

    var ms = 300; // milliseconds

    timer = setTimeout(function() {
        listViewSpots.dataSource.read();
    }, ms);
});

$("#btn-new-plan").click(function(e) {
    $("#modal-cleaning-plan").modal("show");
});

$("#btn-create-plan").click(function(e) {
    var data = $("#form-clening-plan").serializeArray();
    createCleaningPlan(data);
});

function initPlanContextMenu()
{
    $("#plan-context-menu").kendoContextMenu({
        target: "#gridCleaningPlans",
        filter: "td .k-grid-actions",
        showOn: "click",
        select: function(e) {
            var td = $(e.target).parent()[0];
            window.planSelected = gridCleaningPlans.dataItem($(td).parent()[0]);

            switch(e.item.id)
            {
                case "deletePlan":
                    confirmDeleteCleaningPlan();
                    break;
            };
        }
    });
}

function confirmDeleteCleaningPlan()
{
    Swal.fire({
        title: 'Eliminar',
        text: "¿Eliminar limpieza?",
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
        if (result.value) deleteCleaningPlan();
      });
}

function deleteCleaningPlan()
{
    let request = callAjax("deleteCleaningPlan", 'POST', {"id" : window.planSelected.id}, true);

    request.done(function(result) {

        if(result.success)
        {
            toastr.success("Plan de limpieza eliminado");
            gridCleaningPlans.dataSource.read();
            listViewSpots.dataSource.read();
        }

    }).fail(function(jqXHR, status) {
        $.unblockUI();
        toastr.error("Error");
    });
}

function confirmInitializeCleaningDashboard()
{
    Swal.fire({
      title: '¿Inicializar Limpieza?',
      text: 'Todas los spots serán marcados como sucios',
      type: 'warning',
      buttonsStyling: true,
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Aceptar',
      confirmButtonClass: 'btn btn-success',
      cancelButtonClass: 'btn btn-danger ml-1',
      cancelButtonText: 'Cancelar',
      buttonsStyling: false
    }).then(function (result) {
      if (result.value) initializeCleaningDashboard();
    });
}

function initializeCleaningDashboard()
{
    let request = callAjax("initializeCleaningDashboard", 'POST', {}, true);

    request.done(function(result) {

        if(result.success)
        {
            toastr.success("Dashboard de limpieza inicializado");
            listViewSpots.dataSource.read();
        }

    }).fail(function(jqXHR, status) {
        $.unblockUI();
        toastr.error("Error");
    });
}

function initDropDownListCleaningStatusFilter()
{
    dropDownListCleaningStatusFilter = $("#dropDownListCleaningStatusFilter").kendoDropDownList({
        optionLabel: "-- Seleccione --",
        valueTemplate: $("#filter-cleaning-status-template").html(),
        template: $("#filter-cleaning-status-template").html(),
        dataTextField: "text",
        dataValueField: "value",
        dataSource: window.cleaningStatus,
        height: 400,
        change: changeFilter,
    }).data("kendoDropDownList");
}

function initDropDownListCleaningBranchFilter()
{
    dropDownListCleaningBranchFilter = $("#dropDownListCleaningBranchFilter").kendoDropDownList({
        optionLabel: "-- Seleccione --",
      dataTextField: "text",
      dataValueField: "value",
      filter: "contains",
      height: 400,
      dataSource: getUserBranches(),
      change: changeFilter,
    }).data("kendoDropDownList"); 
}

function initDropDownListCleaningSpotFilter()
{
    dropDownListCleaningSpotFilter = $("#dropDownListCleaningSpotFilter").kendoDropDownList({
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
    listViewSpots.dataSource.read();
}

function initListViewSpots()
{
    var dataSource = new kendo.data.DataSource({
        transport: {
            read: {
                url: "getCleaningSpots",
                type: "GET",
                dataType: "JSON",
                data: function() {
                    return {
                        search           : $('#search').val(),
                        idbranch         : dropDownListCleaningBranchFilter.value(),
                        idcleaningstatus : dropDownListCleaningStatusFilter.value(),
                        idspot           : dropDownListCleaningSpotFilter.value()
                    };
                },
            },
        },
        group: {
            field: 'parent_name',
            dir: 'desc',
            compare: function (a, b) {
                if (a.items.length === b.items.length) {
                    return 0;
                } else if (a.items.length > b.items.length) {
                    return 1;
                } else {
                    return -1;
                }
            }
        },
        requestEnd: function(e) {}
    });

    listViewSpots = $("#listViewSpots").kendoListView({
        dataSource: dataSource,
        dataBound: function(e) {

            if(this.dataSource.data().length == 0)
            {
                $("#listViewSpots").append("<div class='text-center alert alert-danger mt-1 alert-validation-msg' role='alert'><i class='feather icon-info mr-1 align-middle'></i><span>No se encontraron resultados</span></div>");
            }
        },
        template:  kendo.template($("#template-spot").html()),
    }).data("kendoListView");

    $("#contextCleaningMenu").kendoContextMenu({
        target: "#listViewSpots",
        filter: ".card",
        dataSource: window.menuData,
        open: function(e) {

            window.spotSelected = cleaningSpots.find(o => o.value === $(e.target).data("idspot")); 
            let idstatus = $(e.target).data("idstatus")

            let filteMenu = window.menuData.filter(function(obj) { return obj.idstatus !== idstatus; });
            this.setOptions({ dataSource: filteMenu });
            
        },
        select: function(e) {
            let option = this.dataSource.getByUid($(e.item).data('uid'));
            let data = {'action': "changecleaningstatus", "id": window.spotSelected.value, "idcleaningstatus": option.idstatus};

            if(option.action == "change-status")
            {
                confirmChangeCleaningStatus(data);
            }
            else
            {
                dropDownListCleaningSpot.value(window.spotSelected.value);
                $("#modal-cleaning-plan").modal("show");
            }
        }
    });
}

function confirmChangeCleaningStatus(data)
{
    let status = cleaningStatus.find(o => o.value === data.idcleaningstatus); 

    Swal.fire({
      title: window.spotSelected.text,
      text: '¿Cambiar de estado a ' + status.text + '?',
      type: 'warning',
      buttonsStyling: true,
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Aceptar',
      confirmButtonClass: 'btn btn-success',
      cancelButtonClass: 'btn btn-danger ml-1',
      cancelButtonText: 'Cancelar',
      buttonsStyling: false
    }).then(function (result) {
      if (result.value) changeCleaningStatus(data);
    });
}

function changeCleaningStatus(data)
{
    let request = callAjax('changeCleaningStatus', 'POST', data, true);
    
    request.done(function(result) {

        if(result.success)
        {
            listViewSpots.dataSource.read();
        }
        else
        {

        }

    }).fail(function(jqXHR, status) {
        $.unblockUI();
        console.log('error cleaning status change');
    });
}

function formatCleaningStatusDataMenu()
{
    let newItem =
    {
        text: "<i style='color: gray !important;' class='fa fa-plus-circle font-small-3' aria-hidden='true'></i><span class='ml-1 text-bold-600'>Nueva limpieza</span>",
        encoded: false,
        cssClass: 'context-menu',
        idstatus: 0,
        action: "new-task"
    };

    window.menuData.push(newItem);

    $.each(cleaningStatus, function(index, item) {

        let menuItem =
        {
            text: "<i style='color:" + item.background + " !important;' class='fa fa-circle font-small-3' aria-hidden='true'></i><span class='ml-1 text-bold-600'>" + item.text + "</span>",
            encoded: false,
            cssClass: 'context-menu',
            idstatus: item.value,
            action: "change-status"
        };

        window.menuData.push(menuItem);
    });
}

function initDropDownListCleaningSpot()
{
    dropDownListCleaningSpot = $("#dropDownListCleaningSpot").kendoDropDownList({
        optionLabel: "-- Seleccione --",
        dataTextField: "text",
        dataValueField: "value",
        height: 400,
        dataSource: window.cleaningSpots,
    }).data("kendoDropDownList");
}

function initDropDownListCleaningStaff()
{
    dropDownListCleaningStaff = $("#dropDownListCleaningStaff").kendoDropDownList({
        optionLabel: "-- Seleccione --",
        dataTextField: "text",
        dataValueField: "value",
        height: 400,
        dataSource: window.cleaningStaff,
    }).data("kendoDropDownList");
}

function initDropDownListCleaningItem()
{
    dropDownListCleaningItem = $("#dropDownListCleaningItem").kendoDropDownList({
        optionLabel: "-- Seleccione --",
        dataTextField: "text",
        dataValueField: "value",
        height: 400,
        dataSource: cleaningItems
    }).data("kendoDropDownList");
}

function initTimePickerCleanat()
{
    timePickerCleanat = $("#timePickerCleanat").kendoTimePicker().data("kendoTimePicker");
}

function createCleaningPlan(data)
{
    if(data[0].value == '' || data[1].value == '')
    {
        toastr.warning("Debe seleciconar el lugar y el tipo de limpieza");
        return false;
    }

    let request = callAjax('createCleaningPlan', 'POST', data, true);
    
    request.done(function(result) {

        if(result.success)
        {
            $("#modal-cleaning-plan").modal("hide");
            listViewSpots.dataSource.read();
        }
        else
        {

        }

    }).fail(function(jqXHR, status) {
        $.unblockUI();
        console.log('error');
    });
}

function initGridCleaningPlans()
{
    gridCleaningPlans = $("#gridCleaningPlans").kendoGrid({
        dataSource: {
            transport: {
                read: {
                    url: "getCleaningPlans",
                    type: "get",
                    dataType: "json",
                    data: function() {
                        return {
                            idspot : window.spotSelected.value
                        };
                    },
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
            aggregate: [ { field: "idcleaningstatus",    aggregate: "count" } ]
        },
        editable: false,
        toolbar: false,
        reorderable: false,
        resizable: false,
        sortable: false,
        pageable: false,
        filterable: false,
        selectable: "single",
        height:'400px',
        noRecords: {
            template: kendo.template($("#template-no-data").html()),
        },
        change:function (e) {
            var dataItem = gridCleaningPlans.dataItem(gridCleaningPlans.select()); 
            window.planSelected = dataItem.id;
            gridCleaningChecklist.dataSource.read();
            gridCleaningNotes.dataSource.read();
        },
        columns: [
            {
                field: "idcleaningstatus",
                title: "Estado",
                width: "150px",
                values: window.cleaningStatus,
                template: function(dataItem) {
                    if(dataItem.idcleaningstatus == 2) return "<div class='spinner-border text-success' role='status'><span class='sr-only'>Loading...</span></div>";

                    let status = cleaningStatus.find(o => o.value === dataItem.idcleaningstatus); 
                    return "<div class='badge badge-pill' style='background:" + status.background + "'>" + status.text + "</div>";
                },
                aggregates: ["count"],
                footerTemplate: "<b>Tareas: #: count # </b>",
                filterable: false
            },
            {
                field: "iditem",
                title: "Tarea",
                values: window.cleaningItems,
                width: "200px",
                filterable: false
            },
            {
                field: "iduser",
                title: "Usuario",
                values: window.global_users,
                width: "200px",
                filterable: false
            },
            {
                field: "cleanat",
                title: "Hora",
                width: "100px",
                filterable: false
            },
            { command: { text: "", name: "actions", iconClass: "fas fa-ellipsis-v"}, title: " ", width: "55px" }
        ],
    }).data("kendoGrid"); 
}

function initGridCleaningChecklist()
{
    gridCleaningChecklist = $("#gridCleaningChecklist").kendoGrid({
        dataSource: {
            transport: {
                read: {
                    url: "getCleaningChecklist",
                    type: "get",
                    dataType: "json",
                    data: function() {
                        return {
                            idplan : window.planSelected
                        };
                    },
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
            aggregate: [ { field: "name",    aggregate: "count" } ]
        },
        editable: false,
        toolbar: false,
        reorderable: false,
        resizable: false,
        sortable: false,
        pageable: false,
        filterable: false,
        scrollable: true,
        height:'400px',
        noRecords: {
            template: kendo.template($("#template-no-data").html()),
        },
        columns: [
            {
                field: "name",
                title: "Ítem",
                width: "300px",
                aggregates: ["count"],
                footerTemplate: "<b>Ítems: #: count # </b>",
                filterable: false
            },
            {
                field: "value",
                title: "Valor",
                width: "100px",
                filterable: false
            },
        ],
    }).data("kendoGrid"); 
}

function initGridCleaningNotes()
{
    gridCleaningNotes = $("#gridCleaningNotes").kendoGrid({
        dataSource: {
            transport: {
                read: {
                    url: "getCleaningNotes",
                    type: "get",
                    dataType: "json",
                    data: function() {
                        return {
                            idplan : window.planSelected
                        };
                    },
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
            aggregate: [ { field: "created_by",    aggregate: "count" } ]
        },
        editable: false,
        toolbar: false,
        reorderable: false,
        resizable: false,
        sortable: false,
        pageable: false,
        filterable: false,
        //scrollable: true,
        height:'400px',
        noRecords: {
            template: kendo.template($("#template-no-data").html()),
        },
        columns: [
            {
                field: "created_by",
                title: "Usuario",
                values: window.global_users,
                width: "170px",
                aggregates: ["count"],
                footerTemplate: "<b>Notas: #: count # </b>",
                filterable: false
            },
            {
                field: "note",
                title: "Nota",
                width: "250px",
                template: function(dataItem) {

                    if(dataItem.type == 'TEXT') return dataItem.note;

                    return "<img src=" + dataItem.note + " alt='avatar' height='250' width='310'></img>";
                },
                filterable: false
            },
            {
                field: "created_at",
                title: "Fecha",
                width: "120px",
                template: function(dataItem) {
                    return moment(dataItem.created_at).format('YY-MM-DD hh:mm A');
                },
                filterable: false
            },
        ],
    }).data("kendoGrid"); 
}

function getLastCleaningChange()
{
    var request = callAjax("getLastCleaningChange", 'POST', {});

    request.done(function(data) {

        if(window.lastCleaningChange == null)
        {
            window.lastCleaningChange = data;
        }
        else if(data !== window.lastCleaningChange)
        {
            window.lastCleaningChange = data;
            listViewSpots.dataSource.read();
        }
    });
}
