var logActions = [
{
    text: "PLAN CREADO",
    value: "CREATE_PLAN"
},
{
    text: "PLAN EDITADO",
    value: "EDIT_PLAN"
},
{
    text: "PLAN ELIMINADO",
    value: "DELETE_PLAN"
}];

$(document).ready(function() {
    initGridLog();
});

$("#btnLog").click(function(){
    $("#modalLog").modal("show");
    $("#gridCleaningLog").data("kendoGrid").dataSource.read();
});

initGridLog = function initGridLog()
{
    var dataSourceLog = new kendo.data.DataSource({
        transport: {
            read: {
                url: "getAllCleaningLog",
                type: "get",
                dataType: "json",
                data: function() {
                    return {
                        timezone: moment.tz.guess()
                    };
                },
            },
        },
        schema: {
            model: {
                id: "id",
                fields: {
                    id: { editable: false, nullable: true, type: "number" },
                    idspot: { editable: false, field: "idspot", type: "number" },
                    action: { editable: false, field: "action", type: "string" },
                    description: { editable: true, field: "description", type: "string" },
                    created_at: { editable: false, field: "created_at", type: "string" },
                }
            },
            total: "total",
            data: "data"
        },
        pageSize: 20,
        serverPaging: true,
        serverFiltering: true,
        serverSorting: true
    });


    $("#gridCleaningLog").kendoGrid({
        dataSource: dataSourceLog,
        height: 600,
        sortable: true,
        selectable: true,
        filterable: true,
        pageable: {
            refresh: true,
            pageSizes: true,
            buttonCount: 5
        },
        toolbar: false,
        columns: [
        {
            template: "#=formatAction(action)#",
            field: "action",
            title: "Acción",
            values: logActions,
            filterable: {
                multi: true,
                search: true
            },
            width: 100
        },
        {
            field: "idspot",
            template: "#=formatSpotLog(idspot)#",
            title: "Lugar",
            values: window.cleaningSpots,
            filterable: {
                multi: true,
                search: true
            },
            width: 100
        },
        {
            field: "description",
            template: "#=formatDescription(description)#",
            title: "Mensaje",
            filterable: false,
            width: 350
        },
        {
            field: "iduser",
            title: "Usuario",
            template: "#=formatUser(iduser)#",
            values: window.cleaningStaff,
            filterable: {
                multi: true,
                search: true
            },
            width: 180
        },
        {
            field: "created_at",
            title: "Fecha",
            template: "#=formatCreatedAt(created_at)#",
            filterable: false,
            width: 130
        }]
    });
}

function formatSpotLog(idspot)
{
    let spot = getSpot(idspot);

    return "<strong>" + spot.text + "</strong>";
}

formatDescription = function formatDescription(description)
{
    return description.replace(/&lt;/g, "<").replace(/&gt;/g, ">");
}

formatAction = function formatAction(action)
{
    let template = "";

    let log = logActions.find((item) => {return item.value == action;});

    switch(true)
    {
        case (action == "CREATE_PLAN" || action == "CREATE_NOTE"):
            template = "<span class='badge badge-pill badge-success'>" + log.text + "</span>";
            break;    

        case (action == "EDIT_PLAN"):
            template = "<span class='badge badge-pill badge-primary'>" + log.text + "</span>";
            break;  

        case (action == "DELETE_PLAN" || action == "DELETE_NOTE"):
            template = "<span class='badge badge-pill badge-danger'>" + log.text + "</span>";
            break;  
    }
    return template;
}

formatUser = function formatUser(iduser) {

    let user = getUser(iduser);

    return  "<div class='user-photo'" +
                "style='background-image: url(" + user.urlpicture + ")'></div>" +
            "<div class='user-name'>" + user.text + "</div>";
}

formatCreatedAt = function formatCreatedAt(value) {
    let time = moment(value);
    return "<span title='" + time.format('YY-MM-DD HH:mm') + "'>" + time.fromNow() + "</span>"
}


