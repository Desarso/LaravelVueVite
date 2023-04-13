 var logActions = [{
         text: locale("Login").toUpperCase(),
         value: "LOGIN"
     },
     {
         text: locale("Task Created").toUpperCase(),
         value: "CREATE_TICKET"
     },
     {
         text: locale("Task deleted").toUpperCase(),
         value: "DELETE_TICKET"
     },
     {
         text: locale('Task Edited').toUpperCase(),
         value: "EDIT_TICKET"
     },
     {
         text: locale("User").toUpperCase(),
         value: "USER"
     },
     {
         text: locale("Label").toUpperCase(),
         value: "TAG"
     },
     {
         text: locale("COPY").toUpperCase(),
         value: "COPY"
     },
     {
         text: locale("Note").toUpperCase(),
         value: "CREATE_NOTE"
     },
     {
         text: locale("Note Deleted").toUpperCase(),
         value: "DELETE_NOTE"
     }
 ];

 $("#btnLog").click(function() {
     $("#modalLog").modal("show");
     $("body").find("[aria-label='Add Group']").remove();
     $("#gridLog").data("kendoGrid").dataSource.read();
 });

 showModalLog = function showModalLog() {
     let filter = { logic: "and", filters: [{ field: "idticket", value: window.selectedTicket.id, operator: "eq" }] };
     setFilerLog(filter);
     $("#modalLog").modal("show");
     $("body").find("[aria-label='Add Group']").remove();
 }

 initGridLog = function initGridLog() {
     var dataSourceLog = new kendo.data.DataSource({
         transport: {
             read: {
                 url: "getAllLog",
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
                     idticket: { editable: false, field: "idticket", type: "number" },
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

     $("#filterLog").kendoFilter({
         dataSource: dataSourceLog,
         applyButton: false,
         fields: [
             { name: "action", type: "number", label: "Acción", defaultValue: "CREATE_TICKET", editorTemplate: logActionDropDownEditor },
             { name: "iduser", type: "number", label: "Usuario", defaultValue: 1, editorTemplate: userLogDropDownEditor },
             { name: "idticket", type: "number", label: "ID", editorTemplate: idNumericEditor },
         ],
         operators: {
             string: {
                 contains: 'Contiene'
             },
             number: {
                 eq: 'Igual a'
             }
         },
         messages: {
             apply: 'Buscar'
         },
         change: function(e) {
             $("#filterLog").data("kendoFilter").applyFilter();
         }
     });

     $("#gridLog").kendoGrid({
         dataSource: dataSourceLog,
         height: 600,
         groupable: false,
         sortable: false,
         pageable: {
             refresh: true,
             pageSizes: true,
             buttonCount: 5
         },
         columns: [{
                 field: "idticket",
                 title: locale("Code"),
                 template: "#=formatReference(ticket)#",
                 width: 50
             },
             {
                 template: "#=formatAction(action)#",
                 field: "action",
                 title: locale("Action"),
                 width: 100
             },
             {
                 field: "description",
                 template: "#=formatDescription(description)#",
                 title: locale('Message'),
                 width: 350
             },
             {
                 field: "user",
                 title: locale('User'),
                 template: "#=formatUser(user)#",
                 width: 180
             },
             {
                 field: "created_at",
                 title: locale('Date'),
                 template: "#=formatCreatedAt(created_at)#",
                 width: 130
             }
         ]
     });
 }

 function logActionDropDownEditor(container, options) {
     $('<input data-bind="value: value" name="' + options.field + '"/>')
         .appendTo(container)
         .kendoDropDownList({
             dataTextField: "text",
             dataValueField: "value",
             dataSource: logActions
         });
 }

 function userLogDropDownEditor(container, options) {
     $('<input data-bind="value: value" name="' + options.field + '"/>')
         .appendTo(container)
         .kendoDropDownList({
             filter: "contains",
             dataTextField: "text",
             dataValueField: "value",
             dataSource: global_users,
             popup: {
                 appendTo: $("#modalLog")
             }
         });
 }

 setFilerLog = function setFilerLog(filter) {
     let filterTicket = $("#filterLog").data("kendoFilter");
     let options = filterTicket.getOptions();
     options.expression = filter;
     filterTicket.setOptions(options);
     filterTicket.applyFilter();
 }

 formatDescription = function formatDescription(description) {
     return description.replace(/&lt;/g, "<").replace(/&gt;/g, ">");
 }

 formatAction = function formatAction(action) {
     let template = "";

     let log = logActions.find((item) => { return item.value == action; });

     switch (true) {
         case (action == "LOGIN"):
             template = "<span class='badge badge-pill badge-info'>" + log.text + "</span>";
             break;

         case (action == "CREATE_TICKET" || action == "CREATE_NOTE"):
             template = "<span class='badge badge-pill badge-success'>" + log.text + "</span>";
             break;

         case (action == "EDIT_TICKET"):
             template = "<span class='badge badge-pill badge-primary'>" + log.text + "</span>";
             break;

         case (action == "DELETE_TICKET" || action == "DELETE_NOTE"):
             template = "<span class='badge badge-pill badge-danger'>" + log.text + "</span>";
             break;

         default:
             template = "<span class='badge badge-pill badge-info'>" + log.text + "</span>";
             break;
     }
     return template;
 }

 formatUser = function formatUser(user) {

     return "<div class='user-photo'" +
         "style='background-image: url(" + user.urlpicture + ")'></div>" +
         "<div class='user-name'>" + user.firstname + " " + user.lastname + "</div>";
 }

 formatReference = function formatReference(ticket) {
     return ticket == null ? "" : "<strong>" + ticket.code + "</strong>";
 }