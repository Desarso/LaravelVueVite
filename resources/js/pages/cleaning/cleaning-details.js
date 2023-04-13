$(document).ready(function() {

    initGridCleaningPlans();

    $("#modal-cleaning-details").on('show.bs.modal', function (event) {
        $("#modal-cleaning-title").text(window.selectedSpot.name);
        $("#modal-cleaning-header").css("background-color", window.selectedSpot.cleaning_status.background);
        gridCleaningPlans.dataSource.read();
    });
});

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
                            idspot: (window.selectedSpot == null ? null : window.selectedSpot.id)
                        };
                    }
                },
                create: {
                    url: "createOrUpdateCleaningPlan",
                    type: "post",
                    dataType: "json",
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
                    },
                    data: function() {
                        return {
                            idspot: (window.selectedSpot == null ? null : window.selectedSpot.id)
                        };
                    }
                },
                update: {
                    url: "createOrUpdateCleaningPlan",
                    type: "post",
                    dataType: "json",
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
                    }
                },
                destroy: {
                    url: "deleteCleaningPlan",
                    type: "post",
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
                    }
                }
            },
            pageSize: 20,
            schema: {
                model: {
                    id: "id",
                    fields: {
                        id: { type: "number", editable: false, nullable: true },
                        iditem: { editable: true, field: "iditem", type: "number", validation: { required: { message: "Tipo de limpieza es requerido" } }},
                        idcleaningstatus: { editable: false, field: "idcleaningstatus", type: "number" },
                        iduser: { editable: true, field: "iduser", type: "number" },
                    }
                }
            },
            requestEnd: function(e) {
                if (e.type == 'destroy' || e.type == 'create' || e.type == 'update') {
                    refreshCleaningChart();
                    $("#listView").data("kendoListView").dataSource.read();
                }
            }
        },
        //toolbar: ["create"],
        toolbar: [{ template: kendo.template($("#toolbar-details-template").html()) }],
        editable: "inline",
        height: "400px",
        sortable: true,
        reorderable: true,
        resizable: true,
        navigatable: true,
        pageable: true,
        noRecords: {
            template: "<div class='alert alert-danger mt-1 alert-validation-msg' role='alert'><i class='feather icon-info mr-1 align-middle'></i><span>No hay limpiezas programadas</span></div>"
        },
        columns: [
            {
                field: "idcleaningstatus",
                title: "Estado",
                width: "120px",
                groupable: false,
                values: window.cleaningstatuses,
                template: "#=formatCleaningStatus(idcleaningstatus)#",
                media: "(min-width: 350px)",
                editable: false
            },
            {
                field: "iditem",
                title: "Tipo de limpieza",
                width: "180px",
                groupable: false,
                values: window.cleaningItems,
                template: "#=formatCleaningItem(iditem)#",
                media: "(min-width: 350px)"
            },
            {
                field: "iduser",
                title: "Responsable",
                values: window.cleaningStaff,
                width: "180px",
                groupable: false,
                template: "#=formatUser(iduser)#",
                media: "(min-width: 350px)"
            },
            {
                command: [
                    { iconClass: "fad fa-pen commandIconOpacity", name: "edit", text: "" },
                    { iconClass: "fad fa-trash commandIconDelete", name: "destroy", text: ""},
                ],
                title: "Acciones",
                width: "100px"
            }
        ],
    }).data("kendoGrid");    
}

function formatCleaningItem(iditem)
{
    if(iditem == 0) return "";

    let item = global_items.find((obj) => { return obj.id == iditem; });
    let ticketType = global_ticket_types.find((obj) => { return obj.value == item.idtype; });

    return "<p class='font-weight-bold mb-0' style='font-size: 15;'><i style='color:" + ticketType.color + "' class='" + ticketType.icon + " font-medium-2 align-middle'></i>  " + item.name;
}

function formatCleaningStatus(idstatus)
{
    if(idstatus == 0) return "";

    let status = cleaningstatuses.find((item) => { return item.value == idstatus; });

    return '<i class="fa fa-circle font-small-3 mr-50" style="color:' + status.background + '"></i>' + status.text;
}

function formatUser(iduser)
{
    if(iduser == 0 || iduser == null) return "";

    let user = getUser(iduser);

    return "<img class='media-object rounded-circle' src='" + user.urlpicture + "' alt='Avatar' height='25' width='25'> <small class='font-weight-bold' style='font-size:14;'>" + user.text + "</small>";
}