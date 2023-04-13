$(document).ready(function() {
    initGridProduction();
    fixKendoGridHeight();
});

function initGridProduction() {
    var dataSourceProduction = new kendo.data.DataSource({
        transport: {
            read: {
                url: "getProductions",
                type: "get",
                dataType: "json",
            },
            create: {
                url: "createProduction",
                type: "post",
                dataType: "json",
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
                }
            },
            update: {
                url: "updateProduction",
                type: "post",
                dataType: "json",
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
                }
            },
            destroy: {
                url: "api/deleteProduction",
                type: "delete",
            },
            parameterMap: function(options, type) {
                if (type == "create" || type == "update") {
                    var d = new Date(options.productiondate);
                    options.productiondate = kendo.toString(new Date(d), "MM/dd/yyyy");
                    return options;
                }
                return options;
            }
        },
        schema: {
            model: {
                id: "id",
                fields: {
                    id: { type: "number", editable: false, type: "number", nullable: true },
                    idstatus: { editable: true, field: "idstatus", type: "number", defaultValue: 1, nullable: false },
                    idequipment: { editable: true, field: "idequipment", type: "number", validation: { required: true } },
                    idschedule: { type: "number", field: "idschedule", editable: true, nullable: true },
                    idproduct: { editable: true, field: "idproduct", type: "number", validation: { required: true } },
                    productiongoal: { editable: false, field: "productiongoal", type: "number" },
                    productionorder: { editable: true, field: "productionorder", type: "string", validation: { required: true } },
                    lot: { editable: true, field: "lot", type: "string", validation: { required: true } },
                    productiondate: { editable: true, field: "productiondate", type: "date", format: "{dd/MM/yyyy}", validation: { required: true } },
                    idoperator: { editable: true, field: "idoperator", type: "number" }
                }
            },
            total: "total",
            data: "data",
        },

        requestEnd: function(e) {
            if (e.type == 'create') {
                $("#gridProduction").data("kendoGrid").dataSource.read();

            }
        },
        pageSize: 100,
        serverPaging: true,
        serverFiltering: true,
        serverSorting: true,
        filter: { logic: "and", filters: [{ field: "name", operator: "startswith" }] }

    });

    //$(".k-filter-toolbar-item")[2].remove();

    $("#gridProduction").kendoGrid({
        dataSource: dataSourceProduction,
        sortable: true,
        selectable: true,

        editable: "popup", //"inline",
        pageable: {
            refresh: true,
            pageSizes: true,
            buttonCount: 5
        },
        resizable: true,
        reorderable: true,
        filterable: true,
        toolbar: [{ template: kendo.template($("#toolbartemplate").html()) }],
        columns: [
            { command: { name: "edit", text: { edit: " " }, iconClass: "fad fa-pen commandIconOpacity" }, title: " ", width: "80px", media: "(min-width: 850px)" },
            {
                field: "productiondate",
                title: locale("Date"),
                width: "100px",
                media: "(min-width: 450px)",
                template: "#=formatProductionDate(productiondate)#"
            },
            {
                field: "idstatus",
                title: locale("Status"),
                template: '#=formatProductionStatus(idstatus)#',
                width: "130px",
                values: window.productionstatuses,
                filterable: {
                    multi: true,
                    search: true
                }

            },
            {
                field: "idequipment",
                title: locale("Equipment"),
                values: window.equipments,
                editor: dropDownListEditor2,
                width: "100px",
                filterable: {
                    multi: true,
                    search: true
                },
            },
            {
                field: "idschedule",
                title: locale("Schedule"),
                width: "120px",
                editor: dropDownListEditor,
                values: window.schedules,
                media: "(min-width: 450px)",
                filterable: {
                    multi: true,
                    search: true
                }
            },
            {
                field: "idproduct",
                title: locale("Product"),
                width: "200px",
                editor: dropDownListEditor2,
                values: window.products,
                filterable: {
                    multi: true,
                    search: true
                }

            },
            {
                field: "productionorder",
                title: locale("PO"),
                width: "100px",
                filterable: false,

            },
            {
                field: "lot",
                title: locale("Lot"),
                width: "100px",
                sortable: true,
                filterable: false,
            },


            {
                field: "idoperator",
                title: locale("Operator"),
                width: "100px",
                editor: dropDownListEditor2,
                values: global_users,
                filterable: {
                    multi: true,
                    search: true
                }
            },
            {
                field: "productiongoal",
                title: locale("Projection"),
                template: '#=formatProjection(productiongoal)#',
                width: "100px",
                filterable: false,
            },
            { command: { name: "destroy", text: " ", iconClass: "fad fa-trash commandIconDelete" }, title: " ", width: "80px", media: "(min-width: 850px)" }
        ],
    });
}




function formatProjection(projection) {

    //TODO: Click en Proyección...mostrar Popup con datos del cálculo

    return '<a class="projection" href = "#">' + projection + ' </span>';

}

function formatDuration(duration) {
    return formatSLA(duration);
}

function formatProductionStatus(idstatus) {
    let s = window.productionstatuses.find(o => o.value === idstatus);
    if (typeof s == "undefined") return "?";
    return "<span style='width: 100px;padding:8px 10px;background-color:" + s.color + " ' class='badge badge-pill badge-success'>" + s.text + "</span>";

}

function formatProductionDate(value) {
    let time = moment(value);
    return "<span title='" + time.format('YY-MM-DD') + "'>" + time.format('YY-MM-DD') + "</span>"
}

formatCreatedAt = function formatCreatedAt(value) {
    let time = moment(value);
    return "<span title='" + time.format('YY-MM-DD HH:mm') + "'>" + time.fromNow() + "</span>"
}