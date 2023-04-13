 $(document).ready(function() {
     var s = new Product();
     s.initGrid();
     fixKendoGridHeight();

     $("#export").click(function(e) {
         var grid = $("#gridProducts").data("kendoGrid");
         grid.saveAsExcel();
     });

 });


 var Product = /** @class */ (function() {
     function Product() {}
     Product.prototype.initGrid = function() {
         $("#gridProducts").kendoGrid({
             excel: {
                 fileName: "Whagons Products.xlsx",
             },
             dataSource: {
                 transport: {
                     read: {
                         url: "getProducts",
                         type: "get",
                         dataType: "json"
                     },
                     create: {
                         url: "api/createProduct",
                         type: "post",
                         dataType: "json",
                     },
                     update: {
                         url: "api/updateProduct",
                         type: "post",
                         dataType: "json"
                     },
                     destroy: {
                         url: "api/deleteProduct",
                         type: "delete",
                     }
                 },
                 pageSize: 100,
                 schema: {
                     model: {
                         id: "id",
                         fields: {
                             id: { type: "number", editable: false, nullable: true },
                             name: { editable: true, field: "name", type: "string", validation: { required: { message: "Nombre es requerido" } } },
                             description: { editable: true, field: "description", type: "string" },
                             idproductcategory: { editable: true, field: "idproductcategory", type: "number", nullable: true },
                             iddestination: { editable: true, field: "iddestination", type: "number", nullable: true },
                             idpresentation: { editable: true, field: "idpresentation", type: "number", nullable: true },
                             idformula: { editable: true, field: "idformula", type: "number", nullable: true },
                             enabled: { editable: true, field: "enabled", type: "boolean", defaultValue: true }
                         }
                     }
                 },
             },

             editable: {
                 mode: "popup"
             },
             toolbar: [{ template: kendo.template($("#toolbartemplate").html()) }],
             sortable: true,
             reorderable: true,
             resizable: true,
             navigatable: true,
             pageable: {
                 refresh: true,
                 pageSizes: true,
                 buttonCount: 5,
             },
             filterable: true,

             columns: [
                 { command: { name: "edit", text: { edit: " " }, iconClass: "fad fa-pen commandIconOpacity" }, title: " ", width: "80px", media: "(min-width: 850px)" },
                 {
                     field: "name",
                     title: locale("Name"),
                     filterable: {
                         multi: true,
                         search: true
                     }
                 },
                 {
                     field: "description",
                     title: locale("Description"),
                     editor: textAreaEditor,
                     hidden: true,
                     width: "200px",
                     media: "(min-width: 850px)"
                 },

                 {
                     field: "idproductcategory",
                     title: locale("Category"),
                     width: "300px",
                     editor: dropDownListEditor,
                     values: window.productcategories,
                     filterable: {
                         multi: true,
                         search: true
                     },

                     media: "(min-width: 450px)",
                 },

                 {
                     field: "idpresentation",
                     title: locale("Presentación"),
                     width: "150",
                     editor: dropDownListEditor,
                     values: window.presentations,
                     filterable: {
                         multi: true,
                         search: true
                     },
                     media: "(min-width: 450px)",
                 },
                 {
                     field: "iddestination",
                     title: locale("Destination"),
                     width: "300",
                     editor: dropDownListEditor,
                     values: window.productdestinations,
                     filterable: {
                         multi: true,
                         search: true
                     },
                     media: "(min-width: 450px)",
                 },
                 {
                     field: "idformula",
                     title: locale("Formula"),
                     width: "100",
                     editor: dropDownListEditor,
                     values: window.formulas,
                     filterable: {
                         multi: true,
                         search: true
                     },
                     media: "(min-width: 450px)",
                 },
                 {
                     field: "enabled",
                     title: locale("Enabled"),
                     template: "#=formatYesNo(enabled)#",
                     hidden: true,
                     media: "(min-width: 850px)"

                 },
                 { command: { name: "destroy", text: " ", iconClass: "fad fa-trash commandIconDelete" }, title: " ", width: "80px", media: "(min-width: 850px)" }

             ],
         }).data("kendoGrid");

     };

     return Product;
 }());

 function formatDuration(duration) {
     return formatSLA(duration);
 }

 /*
 function teamDropDownEditor(container, options) {
     $('<input data-text-field="text" data-value-field="value" data-bind="value:' + options.field + '"/>')
         .appendTo(container)
         .kendoDropDownList({
             autoBind: false,
             optionLabel: " ",
             valuePrimitive: true,
             dataSource: {
                 data: global_teams
             }
         });
 }
 */



 function formatProjectStatus(idstatus) {
     let s = global_statuses.find(o => o.value === idstatus);
     if (typeof s == "undefined") return "?";
     return "<span style='background-color:" + s.color + " ' class='badge badge-pill badge-success'>" + s.text + "</span>";

 }


 function formatProjectName(name, description, code, isprivate) {
     let result = "";
     description = description == null ? '' : description;

     result += "<div style='display:inline-block; vertical-align: middle'>";
     if (isprivate == 1)
         result += "<i class='fa fa-lock text-primary'></i> ";
     result += "<strong>" + name + "</strong>";
     result += "<br><small style='opacity: 0.6'>" + description + "</small>";

     result += "</div>";
     return result;
 }