$(document).ready(function(){
    initGridWarehouseItem();
})

function initGridWarehouseItem() {
    var dataSourceWarehouse = new kendo.data.DataSource({
        transport: {
            read: {
                url: "getWarehouseItems",
                type: "get",
                dataType: "json",
            },
            create: {
                url: "api/createWarehouseItem",
                type: "post",
                dataType: "json",
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
                }
            },
            update: {
                url: "api/updateWarehouseItem",
                type: "post",
                dataType: "json",
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
                }
            },
            destroy: {
                url: "api/deleteWarehouseItem",
                type: "delete",
            },
        },
        schema: {
            model: {
                id: "id",
                fields: {
                    id: { type: "number", editable: false, nullable: true },
                    name: { editable: true, field: "name", type: "text", validation: { required: { message: "Nombre es requerido" } } },
                    description: { editable: true, field: "description", type: "text" },
                    code: { editable: true, field: "code", type: "text" , validation: { required: { message: "Codigo es requerido" } } },
                    idcategory: { editable: true, field: "idcategory", type: "number", validation: { required: { message: "Categoría es requerida" } } },
                    enabled: { editable: true, field: "enabled", type: "boolean", defaultValue: true }
                }
            },
            total: "total",
            data: "data"
        },
        pageSize: 100,
        serverPaging: true,
        serverFiltering: true,
        serverSorting: true,
        filter: { logic: "and", filters: [{ field: "name", operator: "startswith" }] }
    });
      
    $("#gridWarehouseItem").kendoGrid({
        dataSource: dataSourceWarehouse,
        sortable: true,
        selectable: true,
        editable: "popup",
        pageable: {
            refresh: true,
            pageSizes: true,
            buttonCount: 5
        },
        resizable: true,
        reorderable: true,
        filterable: true,
        noRecords: {
            template: "<div style='width:100%' class='alert alert-danger mt-1 alert-validation-msg' role='alert'><i class='feather icon-info mr-1 align-middle'></i><span>No hay ítems</span></div>"
        },
        toolbar: [{ template: kendo.template($("#toolbartemplate").html()) }],
        columns: [
            { command: { name: "edit", text: { edit: " " }, iconClass: "fad fa-pen commandIconOpacity" }, title: " ", width: "80px", media: "(min-width: 850px)" },
            {
                field: "name",
                title: locale("Name"),
                width: "200px",
                template: "#=formatItemName(name, enabled)#",
                media: "(min-width: 350px)",
                filterable: {
                    multi: true,
                    search: true
                }
            },
            {
                field: "description",
                title: locale("Description"),
                width: "300px",
                editor: textAreaEditor,
                media: "(min-width: 450px)",
                filterable: {
                    multi: true,
                    search: true
                }
            },
            {
                field: "code",
                title: locale("Code"),
                width: "130px",
            },
            {
                field: "idcategory",
                title: "Categoría",
                width: "200px",
                values: window.categories,
                filterable: {
                    multi: true,
                    search: true
                }
            },
            {
                field: "enabled",
                title: locale("Enabled"),
                hidden: true,
                template: "#=formatYesNo(enabled)#",
                media: "(min-width: 450px)",
                editor: checkBoxEditor,
            },
            { command: { name: "destroy", text: " ", iconClass: "fad fa-trash commandIconDelete" }, title: " ", width: "80px", media: "(min-width: 850px)" }
        ],
    });
}

function formatItemName(name, enabled) {
    let result = "";

    result += "<div style='display:inline-block; vertical-align: middle'>";
    if (enabled == 0) {
        result += "<strong style='opacity: 0.3; text-decoration: line-through'>" + name + "</strong>";
        result += "</div>";
    } else {
        result +=  name;
        result += "</div>";
    }
    return result;
}