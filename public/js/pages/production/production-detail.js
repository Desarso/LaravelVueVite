 window.producionDetailGrid = null;

 $(document).ready(function() {
     window.producionDetailGrid = new ProductionDetail();
     window.producionDetailGrid.initGrid();

     $(".k-grid-add").first().on("click", function(e) {
         // Prevenir agregamos detalles de producción si la máquina está apagada,
         // o si no hay producción y operador asignados. 
         // Para encender máquina se ocupan operador y producto, entonces se podría
         // solamente revisar que la máquina esté encendida...

         if ($('#powerOnEquipment').prop('checked') == false) {
             e.preventDefault();
             e.stopPropagation();
             Swal.fire({
                 type: "warning",
                 title: 'Máquina está apagada',
                 text: 'La Máquina debe estar encendida.',
                 confirmButtonClass: 'btn btn-danger',
             });
         }
     });

 });



 var ProductionDetail = /** @class */ (function() {
     function ProductionDetail() {}

     ProductionDetail.prototype.initGrid = function() {
         $("#gridProductionDetails").kendoGrid({
             excel: {
                 fileName: "Whagons ProductionDetails.xlsx",
             },
             dataSource: {
                 transport: {
                     read: {
                         url: "getProductionDetails",
                         type: "get",
                         dataType: "json",
                         /* data: function() {
                              return {
                                  idproduction: idproduction
                              };
                          }, */
                     },
                     create: {
                         url: "api/createProductionDetail",
                         type: "post",
                         dataType: "json",
                     },
                     update: {
                         url: "api/updateProductionDetail",
                         type: "post",
                         dataType: "json"
                     },
                     destroy: {
                         url: "api/deleteProductionDetail",
                         type: "delete",
                     }
                 },
                 pageSize: 20,
                 schema: {
                     model: {
                         id: "id",
                         fields: {
                             id: { type: "number", editable: false, nullable: true },
                             idproduction: { type: "number", field: "idproduction", editable: false, nullable: false },
                             idoperator: { type: "number", field: "idoperator", editable: false, nullable: false },
                             time: { type: "time", field: "time", editable: false, nullable: true },
                             quantity: { editable: true, field: "quantity", type: "number", validation: { min: 1, required: { message: "La cantidad es requerida" } } },
                         }
                     }
                 },

             },
             edit: function(e) {
                 if (e.model.isNew()) {
                     var today = new Date();
                 } //else {

                 //}
             },
             dataBinding: function(e) {
                 if (e.action == 'add') {
                     e.items[0].idproduction = getSelectedProduction();
                     e.items[0].idoperator = getSelectedOperator();
                 }

             },
             dataBound: function(e) {
                 //  $("#listView").data("kendoListView").dataSource.read();
             },

             editable: {
                 mode: "popup"
             },
             toolbar: [{ template: kendo.template($("#toolbartemplate").html()) }],
             height: "550px",
             sortable: true,
             reorderable: true,
             resizable: true,
             navigatable: true,
             pageable: {
                 refresh: true,
                 pageSizes: true,
                 buttonCount: 5,
             },
             noRecords: true,
             messages: {
                 noRecords: locale("No Data!")
             },

             columns: [
                 { command: { name: "edit", text: { edit: " " }, iconClass: "fad fa-pen commandIconOpacity" }, title: " ", width: "70px", media: "(min-width: 850px)" },
                 {
                     field: "quantity",
                     title: locale('Boxes'),
                     width: "100px",
                     media: "(min-width: 850px)"
                 },
                 {
                     field: "time",
                     title: locale("Time"),
                     template: "#=formatTime(time)#",
                     width: "150px"
                 },

                 {
                     field: "idoperator",
                     title: locale("Operator"),
                     values: global_users,
                     width: "200px",
                     media: "(min-width: 850px)"
                 },

                 { command: { name: "destroy", text: " ", iconClass: "fad fa-trash commandIconDelete" }, title: " ", width: "80px", media: "(min-width: 850px)" }

             ],
         }).data("kendoGrid");

     };




     // Updated idproduction data in transport read
     ProductionDetail.prototype.setProductionID = function(idproduction) {
         $("#gridProductionDetails").data("kendoGrid").dataSource.transport.options.read.data = function() { return { idproduction: idproduction } };
         $("#gridProductionDetails").data("kendoGrid").dataSource.read();
     };

     return ProductionDetail;
 }());

 function formatTime(time) {
     if (time == null) return new Date().toLocaleTimeString();
     d = new Date(time);
     return d.toLocaleTimeString();


 }