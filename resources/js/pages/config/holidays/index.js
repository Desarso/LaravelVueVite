$(document).ready(function() {
    initGridHolidays();

    $("#export").click(function(e) {
        gridHoliday.saveAsExcel();
    });
});

function initGridHolidays()
{
    gridHoliday = $("#gridHoliday").kendoGrid({
        excel: {
            fileName: "Whagons Spot Types.xlsx",

        },
        dataSource: {
            transport: {
                read: {
                    url: "getHolidays",
                    type: "get",
                    dataType: "json",
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
                    },
                },
                create: {
                    url: "createHoliday",
                    type: "post",
                    dataType: "json",
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
                    },
                },
                update: {
                    url: "updateHoliday",
                    type: "post",
                    dataType: "json",
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
                    },
                },
                destroy: {
                    url: "deleteHoliday",
                    type: "post",
                    dataType: "json",
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
                    },
                },
                parameterMap: function(options, type) {
                    if (type == "create" || type == "update") {
                        var d = new Date(options.date);
                        options.date = kendo.toString(new Date(d), "MM/dd/yyyy");
                        return options;
                    }
                    return options;
                }
            },
            pageSize: 20,
            schema: {
                model: {
                    id: "id",
                    fields: {
                        id: { type: "number", editable: false, nullable: true },
                        name: { editable: true, field: "name", type: "string", validation: { required: { message: "Nombre es requerido" } } },
                        date: { editable: true, field: "date", type: "date", format: "{dd/MM/yyyy}", validation: { required: true } },
                    }
                }
            },
            requestEnd: function(e) {
                if (e.type == 'destroy' || e.type == 'create' || e.type == 'update') {
                    gridHoliday.dataSource.read();
                }
            }
        },
        editable: {
            mode: "popup"
        },
        toolbar: ["create"],
        reorderable: true,
        resizable: true,
        sortable: true,
        pageable: {
            refresh: true,
            pageSizes: true,
            buttonCount: 5
        },
        filterable: true,

        columns: [
            { command: { name: "edit", text: { edit: " " }, iconClass: "fad fa-pen commandIconOpacity" }, title: " ", width: "80px", media: "(min-width: 850px)" },
            {
                field: "name",
                title: locale('Name'),
                width: "300px",
                media: "(min-width: 350px)",
                filterable: {
                    multi: true,
                    search: true
                }
            },
            {
                field: "date",
                title: locale("Date"),
                width: "100px",
                media: "(min-width: 450px)",
                template: function(dataItem) {
                    let date = moment(dataItem.date);
                    return "<span title='" + date.format('YY-MM-DD') + "'>" + date.format('YYYY-MM-DD') + "</span>";
                },
            },
            { command: { name: "destroy", text: " ", iconClass: "fad fa-trash commandIconDelete" }, title: " ", width: "80px", media: "(min-width: 850px)" }
        ],
    }).data("kendoGrid");
}

