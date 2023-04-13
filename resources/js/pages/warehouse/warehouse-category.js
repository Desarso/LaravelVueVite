$(document).ready(function(){
    initGridWarehouseCategory();
})

function initGridWarehouseCategory()
{
    var dataSourceWarehouse = new kendo.data.DataSource({
        transport: {
            read: {
                url: "getWarehouseCategories",
                type: "get",
                dataType: "json",
            },
            create: {
                url: "createWarehouseCategory",
                type: "post",
                dataType: "json",
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
                }
            },
            update: {
                url: "updateWarehouseCategory",
                type: "post",
                dataType: "json",
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
                }
            },
            destroy: {
                url: "deleteWarehouseCategory",
                type: "delete",
            }
        },
        schema: {
            model: {
                id: "id",
                fields: {
                    id: { type: "number", editable: false, nullable: true },
                    name: { editable: true, field: "name", type: "text", validation: { required: { message: "Nombre es requerido" } } },
                    description: { editable: true, field: "description", type: "text" },
                }
            }
        },
        pageSize: 100
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
            { command: { name: "edit", text: { edit: " " }, iconClass: "fad fa-pen commandIconOpacity" }, title: " ", width: "80px" },
            {
                field: "name",
                title: locale("Name"),
                width: "200px",
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
                filterable: {
                    multi: true,
                    search: true
                }
            },
            { command: { name: "destroy", text: " ", iconClass: "fad fa-trash commandIconDelete" }, title: " ", width: "80px", media: "(min-width: 850px)" }
        ],
    });
}