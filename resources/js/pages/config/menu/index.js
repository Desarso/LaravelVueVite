window.itemAction = "create";

 $(document).ready(function() {
    initGridMenu();

    $(document).on("change", ".menu-switch", function(event) {
        console.log($(this).data('id'), $(this).is(':checked'));
        enableMenu({ 'id': $(this).data('id'), 'enable': $(this).is(':checked') });
    });

 });

 function initGridMenu()
 {
    gridMenu = $("#gridMenu").kendoGrid({
         dataSource: {
             transport: {
                 read: {
                     url: "getAllMenu",
                     type: "get",
                     dataType: "json"
                 }
             },
             pageSize: 20,
             schema: {
                 model: {
                     id: "id",
                     fields: {
                         id: { type: "number", editable: false, nullable: true },
                         name: { editable: true, field: "name", type: "string", validation: { required: { message: "Nombre es requerido" } } },
                     }
                 }
             },
         },
         editable: false,
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
         dataBound: function(e) {
             $('[data-toggle="tooltip"]').tooltip();
         },
         columns: [
             {
                 field: "name",
                 title: locale("Name"),
                 width: "300px",
                 filterable: false
             },
             {
                field: "type",
                title: "Tipo",
                width: "300px",
                template: function(dataItem) {

                    let badge = "";

                    switch(dataItem.type)
                    {
                        case 'HEADER':
                            badge = "<div class='badge badge-danger'>" + dataItem.type + " </div>";
                            break;
                    
                        case 'PARENT':
                            badge = "<div class='badge badge-success'>" + dataItem.type + " </div>";
                            break;

                        case 'NAV':
                            badge = "<div class='badge badge-info'>" + dataItem.type + " </div>";
                            break;

                        case 'CHILD':
                            badge = "<div class='badge badge-warning'>" + dataItem.type + " </div>";
                            break;
                    }

                    return badge;
                },
                filterable: false
            },
            {
                field: "enable",
                title: "Estado",
                template: function(dataItem) {
                    return "<div class='custom-control custom-switch custom-switch-success switch-md mr-2 mb-1'>" +
                                    "<input type='checkbox' class='menu-switch custom-control-input' data-id='"+ dataItem.id + "' id='switch-menu" + dataItem.id + "' "+ (dataItem.enable ? "checked" : "") + ">" +
                                    "<label class='custom-control-label' for='switch-menu" + dataItem.id + "'>" +
                                    "<span class='switch-text-left'>True</span>" +
                                    "<span class='switch-text-right'>False</span>" +
                                    "</label>" +
                                "</div>";
                },
                width: "300px",
                filterable: false
            },
            { command: { text: "", name: "actions", iconClass: "fas fa-ellipsis-v" }, title: " ", width: "70px" }
         ],
     }).data("kendoGrid");

     setTimeout(() => { $("#btn-new-config").text("Crear Menú") }, 300);
 }

 function initContextMenu()
 {
     $("#context-menu").kendoContextMenu({
         target: "#gridMenu",
         filter: "td .k-grid-actions",
         showOn: "click",
         select: function(e) {
             var td = $(e.target).parent()[0];
             window.itemSelected = gridItems.dataItem($(td).parent()[0]);

             switch (e.item.id) {
                 case "editItem":
                     setItem();
                     break;

                 case "deleteItem":
                     confirmDeleteItem();
                     break;

                 case "selectSpots":
                     selectSpots();
                     break;
             };
         }
     });
 }

 function enableMenu(data)
 {
    let request = callAjax("enableMenu", 'POST', data, true);

    request.done(function(result) {

        if(result.success)
        {
            toastr.success("Acción realizada con éxito");
            gridMenu.dataSource.read();
        }

    }).fail(function(jqXHR, status) {
        $.unblockUI();
        toastr.error("Error");
    });
}