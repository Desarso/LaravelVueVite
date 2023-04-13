window.spotAction = "create";

$(document).ready(function () {
    initGridTicketPriorities();
});

function initGridTicketPriorities()
{
    gridTicketPriorities = $("#gridTicketPriorities").kendoGrid({
        dataSource: {
            transport: {
                read: {
                    url: "getTicketPriorities",
                    type: "get",
                    dataType: "json"
                },
                update: {
                    url: "updateTicketPriority",
                    type: "post",
                    dataType: "json",
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
                    }
                },
            },
            pageSize: 20,
            schema: {
                model: {
                    id: "id",
                    fields: {
                        id: { type: "number", editable: false, nullable: true },
                        name: { editable: false, field: "name", type: "string", validation: { required: { message: locale("Name is required") } } },
                        sla: { type: "number" },
                        color: { editable: false, type: "string" },
                    }
                }
            },
        },
        editable: "inline",
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
        dataBound: function (e) {

        },
        columns: [
            {
                field: "name",
                title: locale("Name"),
                width: "300px",
                filterable: false,
                editable: false
            },
            {
                field: "sla",
                title: "SLA",
                width: "300px",
                filterable: false
            },
            {
                field: "color",
                title: locale("Color"),
                width: "300px",
                filterable: false
            },
            { command: ["edit"], title: "&nbsp;", width: "250px" },
        ],
    }).data("kendoGrid");
}

