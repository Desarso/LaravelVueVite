 window.selectedTicket = null;
 window.startDate = null;
 window.endDate = null;
 window.isUpdating = false;
 window.lastUpdated = null;
 window.hasRangeDate = false;
 window.start = false;

 $(document).ready(function() {

    $("#btn-show-to-save-filter").kendoButton();

    switch(window.lenguague)
    {
        case 'es':
            initDateRangePickerEs();
            break;

        case 'en':
            initDateRangePickerEn();
            break;
    
        default:
            initDateRangePickerEs();
            break;
    }


     initGridTicket();
     initGridLog();

     $("#btnAddTask").removeClass('hidden');

    $(document).on("click", ".btn-overdue-task", function(event) {
        setFilter({ logic: "and", filters: [{ field: "duedate", value: 1, operator: "eq" }] });
        $("#filter-selected").html("<i class='fad fa-filter mr-50 font-medium-1'></i>"+"<i class='font-medium-1 mr-50 btn-remove-filter fad fa-times'></i>" + " Tareas Vencidas");
        changefilterText();
        $("#filter-selected").show();
        filterItemTotal();
    });

     $(document).on("click", ".ticket-reference", function(event) {
         let idticket = $(this).data("idticket");
         setFilter({ logic: "and", filters: [{ field: "id", value: idticket, operator: "eq" }] });
         $("#modalLog").modal("hide");
     });

     $(document).on("click", ".btn-status", function(event) {
         changeStatusTicket($(this).data("idstatus"));
     });

     $(document).on("click", ".ticket-stat", function(event) {
         setFilter({ logic: "and", filters: [{ field: "idstatus", value: $(this).data("idstatus"), operator: "eq" }] });
     });

     $(document).on("click", ".badge-status", function(event) {
         getStatusTicket();
     });

     $(document).on("click", ".clip-file", function(event) {
         showModalFile();
     });

     $('#modalStatus').on('show.bs.modal', function(e) {
         $('#title-modal-status').text((window.selectedTicket.id + " - " + window.selectedTicket.name));
     })

     //Get last ticket changes
     setInterval(() => {
         getLastTicket();
     }, 50000);

     //Refresh grid every minute
     setInterval(() => {
         gridTicket.dataSource.read();
     }, 50000);

     $(document).click(function(e) {
         $('[data-toggle="tooltip"]').tooltip();
     });

     if (window.ticketsFilter.length != []) {
         let options = filterTicket.getOptions();
         options.expression = window.ticketsFilter;
         filterTicket.setOptions(options);
         filterTicket.applyFilter();

         $("body").find("[aria-label='Add Group']").remove();
     }
 });

 var timer;

 $("#search").keyup(function() {
     clearTimeout(timer);
     var ms = 1000; // milliseconds
     timer = setTimeout(function() {
         $("#gridTicket").data("kendoGrid").dataSource.read();
     }, ms);
 });

 /*
 function setFilter(filter) {
     $("#search").val("");
     let filterTicket = $("#filterTicket").data("kendoFilter");
     let options = filterTicket.getOptions();

     
     if (options.expression.hasOwnProperty("filters")) {
         options.expression.filters.push(filter.filters[0]);
     } else {
         options.expression = filter;
     }
     

     options.expression = filter;

     filterTicket.setOptions(options);
     
     //$("#filterTicket").data("kendoFilter").applyFilter();

     $("body").find("[aria-label='Add Group']").remove();
 }
 */

 $("#btn-excel").click(function() {

     $.blockUI({ message: '<h1>Procesando...</h1>' });

     let newURL = window.urlExport.replace("getAllTicket", "exportTickets");
     let request = callAjax(newURL, 'GET', null);

     request.done(function(response, textStatus, request) {
         var a = document.createElement("a");
         a.href = response.file;
         a.download = response.name;
         document.body.appendChild(a);
         a.click();
         a.remove();
         $.unblockUI();
     }).fail(function(jqXHR, status) {
         $.unblockUI();
     });
 });

 function checkVisibility(column) {
     let settings = JSON.parse(organization.settings);

     if (!settings.hasOwnProperty("hide_columns")) return false;

     return ($.inArray(column, settings.hide_columns) != -1 ? true : false);
 }

 function initDateRangePickerEs() {
     var start = moment().subtract(29, 'days');
     var end = moment();

     function cb(start, end) {
         let label = moment(start, ["MMMM D, YYYY"]).isValid() ? (start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY')) : 'Todos';
         $('#dateRangePicker span').html(label);
     }

     // 

     dateRangePicker = $('#dateRangePicker').daterangepicker({
         startDate: null,
         endDate: null,
         ranges: {
             'Todos': [null, null],
             'Hoy': [moment(), moment()],
             'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
             'Últimos 7 días': [moment().subtract(6, 'days'), moment()],
             'Últimos 30 días': [moment().subtract(29, 'days'), moment()],
             'Este mes': [moment().startOf('month'), moment().endOf('month')],
             'Mes pasado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
         },
         locale: {
            "customRangeLabel": "Rango",
            "applyLabel": "Aplicar",
            "cancelLabel": "Cancelar",
         }
     }, cb);

     cb(null, null);

     $('#dateRangePicker').on('apply.daterangepicker', function(ev, picker) {
         let startDate = $('#dateRangePicker').data('daterangepicker').startDate;
         window.hasRangeDate = moment(startDate, ["MMMM D, YYYY"]).isValid() ? true : false;
         $("#gridTicket").data("kendoGrid").dataSource.read();

     });

     $('#dateRangePicker').on('showCalendar.daterangepicker', function(ev, picker) {

         console.log($("#dateRangePicker").find('span').text());

         if ($("#dateRangePicker").find('span').text() == 'Todos') {
             $('#dateRangePicker').data('daterangepicker').setStartDate(moment().subtract(29, 'days'));
             $('#dateRangePicker').data('daterangepicker').setEndDate(moment());
             $("#dateRangePicker").data().daterangepicker.updateCalendars();
             cb(moment().subtract(29, 'days'), moment());
         }
     });
 }

 function initDateRangePickerEn()
 {
    var start = moment().subtract(29, 'days');
    var end = moment();

    function cb(start, end) {
        let label = moment(start, ["MMMM D, YYYY"]).isValid() ? (start.lang("en").format('MMMM D, YYYY') + ' - ' + end.lang("en").format('MMMM D, YYYY')) : 'All';
        console.log(label);
        $('#dateRangePicker span').html(label);
    }

    dateRangePicker = $('#dateRangePicker').daterangepicker({
        startDate: null,
        endDate: null,
        ranges: {
            'All': [null, null],
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 days': [moment().subtract(6, 'days'), moment()],
            'Last 30 days': [moment().subtract(29, 'days'), moment()],
            'This month': [moment().startOf('month'), moment().endOf('month')],
            'Last month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        locale: {
           "separator": " - ",
           "applyLabel": "Apply",
           "cancelLabel": "Cancel",
           "fromLabel": "From",
           "toLabel": "To",
           "customRangeLabel": "Custom",
           "weekLabel": "W",
           "daysOfWeek": [
               "Su",
               "Mo",
               "Tu",
               "We",
               "Th",
               "Fr",
               "Sa"
           ],
           "monthNames": [
               "January",
               "February",
               "March",
               "April",
               "May",
               "June",
               "July",
               "August",
               "September",
               "October",
               "November",
               "December"
           ],
           "firstDay": 1
       }
    }, cb);

    cb(null, null);

    $('#dateRangePicker').on('apply.daterangepicker', function(ev, picker) {
        let startDate = $('#dateRangePicker').data('daterangepicker').startDate;
        window.hasRangeDate = moment(startDate, ["MMMM D, YYYY"]).isValid() ? true : false;
        $("#gridTicket").data("kendoGrid").dataSource.read();

    });

    $('#dateRangePicker').on('showCalendar.daterangepicker', function(ev, picker) {

        console.log($("#dateRangePicker").find('span').text());

        if ($("#dateRangePicker").find('span').text() == 'All') {
            $('#dateRangePicker').data('daterangepicker').setStartDate(moment().subtract(29, 'days'));
            $('#dateRangePicker').data('daterangepicker').setEndDate(moment());
            $("#dateRangePicker").data().daterangepicker.updateCalendars();
            cb(moment().subtract(29, 'days'), moment());
        }
    });
}

 function initGridTicket() {
     var dataSource = new kendo.data.DataSource({
         transport: {
             read: {
                 url: "getAllTicket",
                 type: "get",
                 dataType: "json",
                 data: function() {
                     return {
                         start: $('#dateRangePicker').data('daterangepicker').startDate.format('YYYY-MM-DD'),
                         end: $('#dateRangePicker').data('daterangepicker').endDate.format('YYYY-MM-DD'),
                         hasRangeDate: window.hasRangeDate,
                         search: $('#search').val()
                     };
                 },
                 beforeSend: function(e, request) {
                     window.urlExport = request.url;
                     getStatsTicket(request.url);
                     getMyStatsTicket(request.url);
                 },
             },
         },
         requestEnd: function(e) {

            if(e.type == "read" && e.response.overdueTasks > 0 && window.start == false)
            {
                toastr.error('<button type="button" class="btn-overdue-task btn btn-light clear btn-sm mt-1">Ver</button>', 'Hay '+ e.response.overdueTasks +' tareas vencidas', { positionClass: 'toast-top-center', containerId: 'toast-top-center' });
            }

            window.start = true;
         },


         error: function(e) {
             if (e.xhr.status == 401) {
                 location.href = "/login";
             }
         },
         schema: {
             model: {
                 id: "id",
                 fields: {
                     id: { editable: false, nullable: true, type: "number" },
                     code: { editable: false, nullable: true, type: "string" },
                     idstatus: { editable: false, field: "idstatus", type: "number" },
                     idpriority: { editable: false, field: "idpriority", type: "number" },
                     idspot: { editable: false, field: "idspot", type: "number" },
                     name: { editable: true, field: "name", type: "string" },
                     users: { editable: false },
                     description: { editable: false, field: "description", type: "string" },
                     approved: { editable: false, field: "approved", type: "number" },
                     created_at: { editable: false, field: "created_at", type: "string" },
                     created_by: { editable: false, field: "created_by", type: "number" },
                     idasset: { editable: false, field: "idasset", type: "number" },
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

     filterTicket = $("#filterTicket").kendoFilter({
         dataSource: dataSource,
         expressionPreview: false,
         applyButton: false,
         fields: [
             { name: "id", type: "number", label: "ID", editorTemplate: idNumericEditor },
             { name: "code", type: "string", label: "Código" },
             { name: "iditem", type: "number", label: "Tarea", defaultValue: 1, editorTemplate: itemDropDownEditor },
             { name: "idtype", type: "number", label: "Tipo de Tarea", defaultValue: 1, editorTemplate: ticketTypeDropDownEditor },
             { name: "idstatus", type: "number", label: "Estado", defaultValue: 1, editorTemplate: statusDropDownEditor },
             { name: "idspot", type: "number", label: "Spot", defaultValue: 1, editorTemplate: spotDropDownEditor },
             { name: "idbranch", type: "number", label: "Sede", defaultValue: 1, editorTemplate: branchDropDownEditor },
             { name: "idteam", type: "number", label: "Equipo", defaultValue: 1, editorTemplate: teamDropDownEditor },
             { name: "iduser", type: "number", label: "Responsable", defaultValue: 1, editorTemplate: userDropDownEditor },
             { name: "created_by", type: "number", label: "Reportado por", defaultValue: 1, editorTemplate: userDropDownEditor },
             { name: "description", type: "string", label: "Descripción" },
             { name: "idpriority", type: "number", label: "Prioridad", defaultValue: 1, editorTemplate: priorityDropDownEditor },
             { name: "idcopy", type: "number", label: "Copia", defaultValue: 1, editorTemplate: userCopyDropDownEditor },
             { name: "approved", type: "number", label: "Aprobado", defaultValue: 1, editorTemplate: approvedDropDownEditor },
             { name: "idtag", type: "number", label: "Etiqueta", defaultValue: 1, editorTemplate: tagDropDownEditor },
             { name: "code", type: "string", label: "Código" },
             { name: "note", type: "string", label: "Nota" },
             { name: "assign", type: "number", label: "Asignación", defaultValue: 1, editorTemplate: assignDropDownEditor },
             { name: "duedate", type: "number", label: "Tareas", defaultValue: 1, editorTemplate: overdueDropDownEditor }
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
             apply: 'Aplicar'
         },
         change: function(e) {

            if(e.expression.hasOwnProperty("filters") && e.expression.filters.length > 0)
            {
                $("#btn-show-to-save-filter").show();
                $("#div-save-filter-section").hide();
            }
            else
            {
                $("#btn-show-to-save-filter").hide();
                $("#div-save-filter-section").hide();
            }

            console.log(e);

            $("#search").val("");
         }
     }).data("kendoFilter");
     

     //remove add group filter option
     $(".k-filter-toolbar-item")[2].remove();

     $(".modal-filter .k-toolbar .k-filter-toolbar-item:nth-child(2) button").html('Agregar filtro <span class="k-icon k-i-filter-add-expression"></span>');

     $(".k-filter-preview").addClass("alert alert-primary");

     gridTicket = $("#gridTicket").kendoGrid({
        dataSource: dataSource,
        height: 550,
        sortable: true,
        selectable: true,
        columnMenu: true,
        reorderable: true,
        editable: 'incell',
        resizable: false,
        pageable: {
            refresh: true,
            pageSizes: true,
            buttonCount: 5
        },
        toolbar: false,
        noRecords: {
            template: "<div class='alert alert-danger mt-1 alert-validation-msg' role='alert'><i class='feather icon-info mr-1 align-middle'></i><span>No hay datos</span></div>"
        },
        columns: [{
            field: "code",
            title: locale("Code"),
            width: "10%",

        },
        {
            field: "idstatus",
            title: locale("Status"),
            values: global_statuses,
            template: "#=formatStatus(id, idstatus, justification)#",
            width: "12%"
        },
        {
            field: "idpriority",
            title: locale('Priority'),
            values: global_priorities,
            template: "#=formatPriority(data)#",
            width: "10%"
        },
        {
            template: '#=formatFiles(files)#',
            title: "",
            width: "3%",
            sortable: false
        },
        {
            field: "name",
            title: locale('Task'),
            template: "#=formatTicketName(name, item, tags, description, approved, duedate, signature, quantity)#",
            width: "35%"
        },
        {
            title: "",
            template: "#=formatActions(data)#",
            width: "8%",
            sortable: false
        },
        {
            field: "idspot",
            title: locale("Spot"),
            //values: global_spots,
            template: "#=formatSpot(idspot)#",
            width: "15%"
        },
        {
            field: "users",
            title: locale("Responsible"),
            template: "#=formatUsers(users, users_copy, duration, idstatus)#",
            sortable: false,
            width: "15%"
        },
        {
            field: "created_at",
            title: locale("Date"),
            template: "#=formatCreatedAt(created_at)#",
            width: "12%"
        },
        {
            field: "created_by",
            title: locale("Created by"),
            values: window.global_users,
            hidden: checkVisibility("created_by"),
            width: "15%"
        }
        ],
        dataBound: function () {
            $('[data-toggle="tooltip"]').tooltip();

            var data = gridTicket.dataSource.data();

            $.each(data, function(i, row) {

                let tr = $('tr[data-uid="' + row.uid + '"] ');

                if (row.idstatus != 4 && row.duedate != null)
                {
                   if(!moment(row.duedate).isAfter(moment())) tr.find("td:eq(4)").children(".duedate").addClass("blink");
                }
                
            });
        },
        change: function (e) {
            window.selectedTicket = this.dataItem(this.select());
        },
        edit: function (e) {

            $('[data-toggle="tooltip"], .tooltip').tooltip("hide");

            let field = this.columns[e.container.index()].field;
            if (field == "name") {
                this.closeCell();
                window.isUpdating = true;
                multiSelectItem.setDataSource(window.global_items);
                //showSidebarTicket(true);
                $("#btnCreateTicket").hide();
                $("#btnUpdateTicket").show();
                showTaskSidebar();
                getTicket();
            }
        },
    }).data("kendoGrid");

     $("#contextMenuTicket").kendoContextMenu({
         target: "#gridTicket",
         filter: "tr[role='row']",
         dataSource: [],
         open: function(e) {
             var grid = $("#gridTicket").data("kendoGrid");
             var model = grid.dataItem(e.target);
             this.setOptions({ dataSource: checkPermissions(model.idteam) });
         },
         select: function(e) {

             let option = this.dataSource.getByUid($(e.item).data('uid'));
             window.selectedTicket = $("#gridTicket").data("kendoGrid").dataItem(e.target);

             switch (option.action) {
                 case "delete":
                     deleteTicket();
                     break;

                 case "verify":
                     showModalVerify();
                     break;

                 case "log":
                     showModalLog();
                     break;

                 case "protocol":
                     showModalProtocol();
                     break;

                 case "signature":
                     showModalSignature();
                     break;

                 case "setduration":
                     $("#modalDuration").modal("show");
                     break;

                 case "escalate":
                     $("#modalEscalate").modal("show");
                     break;

                 case "locked":
                     break;

                 default:
                     break;
             }
         }
     });
 }

 function getLastTicket() {
     var request = callAjax("getLastTicket", 'POST', {});

     request.done(function(data) {

         if (window.lastUpdated == null) {
             window.lastUpdated = data;
         } else if (data !== window.lastUpdated) {
             window.lastUpdated = data;
             $("#gridTicket").data("kendoGrid").dataSource.read();
         }
     });
 }

 function checkPermissions(idteam) {
     var ticketActions = [{
             text: "<i class='fas fa-trash' aria-hidden='true'> <b>Eliminar</b></i>",
             encoded: false,
             cssClass: 'context-menu',
             action: 'delete'
         },
         {
             text: "<i class='fas fa-thumbs-up' aria-hidden='true'> <b>Verificar</b></i>",
             encoded: false,
             cssClass: 'context-menu',
             action: 'verify'
         },
         {
             text: "<i class='fas fa-file-check' aria-hidden='true'> <b>Protocolo</b></i>",
             encoded: false,
             cssClass: 'context-menu',
             action: 'protocol'
         },
         {
             text: "<i class='fas fa-signature' aria-hidden='true'> <b>Firma</b></i>",
             encoded: false,
             cssClass: 'context-menu',
             action: 'signature'
         },
         {
             text: "<i class='fas fa-people-arrows' aria-hidden='true'> <b>Escalar</b></i>",
             encoded: false,
             cssClass: 'context-menu',
             action: 'escalate'
         },
         {
             text: "<i class='fas fa-clock' aria-hidden='true'> <b>Duración</b></i>",
             encoded: false,
             cssClass: 'context-menu',
             action: 'setduration'
         },
         {
             text: "<i class='fas fa-align-justify' aria-hidden='true'> <b>Bitácora</b></i>",
             encoded: false,
             cssClass: 'context-menu',
             action: 'log'
         }
     ];

     var json_permissions = global_permissions.find((item) => { return item.idteam == idteam; });

     if (json_permissions == undefined) {
         return [{
             text: "<i class='fas fa-ban' aria-hidden='true'> <b>No hay permisos</b></i>",
             encoded: false,
             cssClass: 'context-menu',
             action: 'locked'
         }];
     }

     permissions = JSON.parse(json_permissions.permissions);

     $.each(permissions, function(key, value) {
         if (!value) {
             ticketActions = ticketActions.filter(function(obj) { return obj.action !== key; });
         }
     });

     return ticketActions;
 }

 function statusDropDownEditor(container, options)
 {
    let filterStatuses = global_statuses;

    filterStatuses.push({value: 5, text: 'Sin finalizar'});

     $('<input style="width:250px;" data-bind="value: value" name="' + options.field + '"/>')
         .appendTo(container)
         .kendoDropDownList({
             dataTextField: "text",
             dataValueField: "value",
             dataSource: filterStatuses
         });
 }

 function itemDropDownEditor(container, options) {
     $('<input style="width:250px;" data-bind="value: value" name="' + options.field + '"/>')
         .appendTo(container)
         .kendoDropDownList({
             filter: "contains",
             dataTextField: "name",
             dataValueField: "id",
             dataSource: global_items,
             height: 350,
             popup: { appendTo: $("#modal-filter") }
         });
 }

 function spotDropDownEditor(container, options) {
     $('<input style="width:250px;" data-bind="value: value" name="' + options.field + '"/>')
         .appendTo(container)
         .kendoDropDownList({
             filter: "contains",
             dataTextField: "text",
             dataValueField: "value",
             dataSource: getUserSpots(),
             template: "<div> <h5>#:data.text#</h5> <small style='color:gray;'>#:data.spotparent#</small> </div>",
             height: 350,
             popup: { appendTo: $("#modal-filter") }
         });
 }

 function branchDropDownEditor(container, options) {
     $('<input style="width:250px;" data-bind="value: value" name="' + options.field + '"/>')
         .appendTo(container)
         .kendoDropDownList({
             filter: "contains",
             dataTextField: "text",
             dataValueField: "value",
             template: "<div> <h5>#:data.text#</h5> <small style='color:gray;'>#:data.spotparent#</small> </div>",
             dataSource: getUserBranches(),
             popup: { appendTo: $("#modal-filter") }
         });
 }

 function userDropDownEditor(container, options) {
     $('<input style="width:250px;" data-bind="value: value" name="' + options.field + '"/>')
         .appendTo(container)
         .kendoDropDownList({
             filter: "contains",
             dataTextField: "text",
             dataValueField: "value",
             dataSource: global_users.filter(user => (user.enabled == true && user.deleted_at == null)),
             height: 350,
             popup: { appendTo: $("#modal-filter") }
         });
 }

 function teamDropDownEditor(container, options) {
     $('<input style="width:250px;" data-bind="value: value" name="' + options.field + '"/>')
         .appendTo(container)
         .kendoDropDownList({
             filter: "contains",
             dataTextField: "text",
             dataValueField: "value",
             dataSource: global_teams,
             popup: { appendTo: $("#modal-filter") }
         });
 }

 function userCopyDropDownEditor(container, options) {
     $('<input style="width:250px;" data-bind="value: value" name="' + options.field + '"/>')
         .appendTo(container)
         .kendoDropDownList({
             filter: "contains",
             dataTextField: "text",
             dataValueField: "value",
             dataSource: global_users.filter(user => (user.enabled == true && user.deleted_at == null)),
             height: 350,
             popup: { appendTo: $("#modal-filter") }
         });
 }

 function tagDropDownEditor(container, options) {
     $('<input style="width:250px;" data-bind="value: value" name="' + options.field + '"/>')
         .appendTo(container)
         .kendoDropDownList({
             filter: "contains",
             dataTextField: "text",
             dataValueField: "value",
             dataSource: global_tags,
             popup: { appendTo: $("#modal-filter") }
         });
 }

 function priorityDropDownEditor(container, options) {
     $('<input style="width:250px;" data-bind="value: value" name="' + options.field + '"/>')
         .appendTo(container)
         .kendoDropDownList({
             //filter: "contains",
             dataTextField: "text",
             dataValueField: "value",
             dataSource: global_priorities
         });
 }

 function ticketTypeDropDownEditor(container, options) {
     $('<input style="width:250px;" data-bind="value: value" name="' + options.field + '"/>')
         .appendTo(container)
         .kendoDropDownList({
             filter: "contains",
             dataTextField: "text",
             dataValueField: "value",
             dataSource: global_ticket_types,
             popup: { appendTo: $("#modal-filter") }
         });
 }

 function approvedDropDownEditor(container, options) {
     $('<input style="width:250px;" data-bind="value: value" name="' + options.field + '"/>')
         .appendTo(container)
         .kendoDropDownList({
             filter: "contains",
             dataTextField: "text",
             dataValueField: "value",
             dataSource: [{ "value": 1, "text": "Aprobado" }, { "value": 0, "text": "Reprobado" }],
             popup: { appendTo: $("#modal-filter") }
         });
 }

 function assignDropDownEditor(container, options) {
     $('<input style="width:250px;" data-bind="value: value" name="' + options.field + '"/>')
         .appendTo(container)
         .kendoDropDownList({
             filter: "contains",
             dataTextField: "text",
             dataValueField: "value",
             dataSource: [{ "value": 1, "text": "Sin asignar" }, { "value": 0, "text": "Asignadas" }, ],
             popup: { appendTo: $("#modal-filter") }
         });
 }

 function overdueDropDownEditor(container, options) {
    $('<input style="width:250px;" data-bind="value: value" name="' + options.field + '"/>')
        .appendTo(container)
        .kendoDropDownList({
            filter: "contains",
            dataTextField: "text",
            dataValueField: "value",
            dataSource: [{ "value": 1, "text": "Vencidas" }]
        });
}

 idNumericEditor = function idNumericEditor(container, options) {
     $('<input style="width:250px;" data-bind="value: value" name="' + options.field + '"/>')
         .appendTo(container)
         .kendoNumericTextBox({
             format: '{0:0}'
         });
 }

 function getButtonNote(row) {
     return "<button type='button' data-idticket='" + row.id + "' class='btn-note btn btn-outline-info btn-sm mr-2'><i class='far fa-comment-alt'></i> <span class='badge badge-light text-muted'>" + (row.notes_count > 0 ? row.notes_count : "") + "</span><span class='sr-only'></span></button>";
 }

 function getButtonChecklist(row) {
     if (row.checklists.length == 0) return "";

     let options = JSON.parse(row.checklists[0].results);
     let total = options.total;
     let completed = options.si;

     let label = (total == 0) ? "" : completed + " / " + total;
     let color = (completed == total) ? "success" : "danger";

     return "<button type='button' data-idticket='" + row.id + "' class='btn-checklist btn btn-outline-" + color + " btn-sm'><i class='far fa-check-square'></i> <span class='badge badge-light text-muted'>" + label + "</span></button>";
 }

 function getStatsTicket(url) {
     let newURL = url.replace("getAllTicket", "getStatsTicket");
     let request = callAjax(newURL, 'GET', null);

     request.done(function(result) {
         setStats(result);
     }).fail(function(jqXHR, status) {

     });
 }

 function getMyStatsTicket(url) {
     let newURL = url.replace("getAllTicket", "getMyStatsTicket");
     let request = callAjax(newURL, 'GET', null);

     request.done(function(result) {
         setMyStats(result);
     }).fail(function(jqXHR, status) {

     });
 }

 function getStatusTicket() {
     let request = callAjax('getStatusTicket', 'POST', { 'idticket': window.selectedTicket.id }, true);

     request.done(function(result) {
         //PNotify.closeAll();
         $("#modal-content-status").html(result);
         $("#modalStatus").modal("show");

     }).fail(function(jqXHR, status) {

     });
 }

 function changeStatusTicket(idstatus) {
     $(".btn-status").prop("disabled", true);

     let data = { 'action': "changestatus", 'idticket': window.selectedTicket.id, 'idstatus': idstatus };

     let request = callAjax('changeStatusTicket', 'POST', data, true);

     request.done(function(result) {

         $(".btn-status").prop("disabled", false);

         if (result.success) {
             PNotify.success({ title: 'Cambio de estado', text: 'Acción completada con éxito' });
             $("#gridTicket").data("kendoGrid").dataSource.read();
             $("#modalStatus").modal("hide");
         } else {
             if (result.confirm) {
                 $("#modalStatus").modal("hide");
                 let confirm = showConfirmModal('Pausar tarea actual', '¿Estás seguro?');

                 confirm.on('pnotify.confirm', function() {

                     data['confirmPause'] = true;

                     let request = callAjax('changeStatusTicket', 'POST', data, true);

                     request.done(function(result) {

                         PNotify.closeAll();
                         PNotify.success({ title: 'Cambio de estado', text: 'Acción completada con éxito' });
                         $("#gridTicket").data("kendoGrid").dataSource.read();

                     }).fail(function(jqXHR, status) {
                         PNotify.closeAll();
                         PNotify.error({ title: 'Problemas', text: 'La acción no se puedo completar' });
                     });

                 });
             } else {
                 toastr.error(result.message, 'Permisos');
             }
         }

     }).fail(function(jqXHR, status) {
         $(".btn-status").prop("disabled", false);
         PNotify.closeAll();
         PNotify.error({ title: 'Problemas', text: 'Intente nuevamente' });
     });
 }

 function setStats(data) {
     "1" in data.status ? "" : data.status['1'] = 0;
     "2" in data.status ? "" : data.status['2'] = 0;
     "3" in data.status ? "" : data.status['3'] = 0;
     "4" in data.status ? "" : data.status['4'] = 0;

     let total = data.total;

     var pendint = 0,
         progress = 0,
         paused = 0,
         finished = 0;

     if (total != 0) {
         pendint = Math.round((data.status['1'] / total) * 100);
         progress = Math.round((data.status['2'] / total) * 100);
         paused = Math.round((data.status['3'] / total) * 100);
         finished = Math.round((data.status['4'] / total) * 100);
     }

     $("#bar-pendint").width(pendint + "%");
     $("#count-pendint").text(data.status['1']);

     $("#bar-progress").width(progress + "%");
     $("#count-progress").text(data.status['2']);

     $("#bar-paused").width(paused + "%");
     $("#count-paused").text(data.status['3']);

     $("#bar-finished").width(finished + "%");
     $("#count-finished").text(data.status['4']);

     $("#total-task").text(total);

     efficacyChart.updateSeries([data.efficacy]);
     $("#average-duration").text(formatDuration(data.average_duration));
 }

 function formatDuration(duration) {
     if (duration <= 60) return duration + " min";

     let time = (duration / 60);

     return (Math.round(time * 100) / 100) + " hrs";
 }

 function setMyStats(data) {
     $("#my-count-pendint").text(data.pending);
     $("#my-count-finished").text(data.finished);
     $("#my-count-reproved").text(data.reproved);
     $("#my-count-expired").text(data.expired);

     myEfficiencyChart.updateSeries([data.efficiency]);
     myEfficacyChart.updateSeries([data.efficacy]);
 }

 formatTicketName = function formatTicketName(name, item, tags, description, approved, duedate, signature, quantity) {
     let html_quantity = formatQuantity(quantity);
     let html_signature = formatSigned(signature);
     let html_duedate = formatDuedate(duedate);
     let html_tags = getTags(tags);
     let html_approved = formatApproved(approved);
     let ticketType = global_ticket_types.find((obj) => { return obj.value == item.idtype; });

     let result = "<div class='todo-title-wrapper d-flex justify-content-between'>" +
         "<div class='todo-title-area d-flex align-items-center'>" +
         "<div class='title-wrapper d-flex'>" +
         "<h6 class='todo-title mt-50 mx-50'> " + html_signature + html_approved + "<i style='color:" + ticketType.color + "' class='font-medium-2 " + ticketType.icon + "'></i> " + name + "</h6>" +
         "</div>" +
         html_quantity + html_tags +
         "</div>" +
         "</div>" +
         "<p class='" + (description == null ? "hidden" : "") + " task-description truncate mb-50'>" + description + "</p>" + html_duedate;

     return result;
 }

 formatSpot = function formatSpot(idspot) {
     var spot = global_spots.find(function(e) { return e.value === idspot; });

     return spot.text + " <br><small style='color:lightgray'>" + spot.spotparent + "</small>";
 }

 formatQuantity = function formatQuantity(quantity) {
     return quantity == null ? "" : "<span data-toggle='tooltip' data-popup='tooltip-custom' data-placement='bottom' data-original-title='Cantidad: " + quantity + "' class='badge badge-warning badge-pill mr-1'>" + quantity + "</span> ";
 }

 formatDuedate = function formatDuedate(duedate) {
     return duedate == null ? "" : "<p class='duedate task-description truncate mb-0 text-muted'><i class='fad fa-calendar font-medium-2 mr-50'></i>" + moment(duedate).format('YY-MM-DD hh:mm A') + "</p>";
 }

 formatApproved = function formatApproved(approved) {
     if (approved == null) return "";
     let hand = approved == 1 ? "up" : "down";
     let title = approved == 1 ? "Aprobado" : "Reprobado";
     return "<i data-toggle='tooltip' data-popup='tooltip-custom' data-placement='bottom' data-original-title='" + title + "' class='font-medium-2 fad fa-thumbs-" + hand + "'></i> ";
 }

 formatSigned = function formatSigned(signature) {
     return signature == null ? "" : "<i data-toggle='tooltip' data-popup='tooltip-custom' data-placement='bottom' data-original-title='Firmado' class='fas fa-signature'></i> ";
 }

 formatFiles = function formatFiles(files) {
     return files == null ? "" : "<i class='clip-file fad fa-paperclip'></i>";
 }

 formatActions = function formatActions(row) {
     return "<div class='task-actions float-right2 todo-item-action d-flex2'>" +
         "<div class='position-relative d-inline-block mr-2'><a data-idticket='" + row.id + "' class='btn-note todo-item-info success'><i class='feather icon-message-square'></i><span style='" + (row.notes_count == 0 ? 'display:none;' : '') + "' class='badge badge-pill badge-success badge-up'>" + row.notes_count + "</span></a> </div>" +
         "<a style='" + (row.checklists.length == 0 ? 'display:none;' : '') + "' data-idticket='" + row.id + "' class='btn-checklist todo-item-favorite warning'><i class='feather icon-check-square'></i></a>" +
         "</div>";
 }

 function getTags(tags) {
     let result = "<div class='chip-wrapper'>";

     for (tag of tags) {
         result += "<div class='chip mb-0'>" +
             "<div class='chip-body'>" +
             "<span class='chip-text' data-value=''>" +
             "<span style='background-color:" + tag.color + "' class='bullet bullet-primary bullet-xs'></span> " + tag.name + "</span>" +
             "</div>" +
             "</div> ";
     }

     return result + "</div>";
 }

 formatStatus = function formatStatus(id, idstatus, justification) {

     let status = global_statuses.find((item) => { return item.value == idstatus; });

     let title = (justification == null ? "" : justification);

     let icon = (justification == null ? "" : " <i class='fal fa-comment-lines' style='font-size:15px;'></i>");

     return '<div data-idticket="' + id + '" data-toggle="tooltip" data-placement="top" title="' + title + '" class="badge-status chip chip-danger" style="cursor: pointer; background-color:' + status.color + ' !important;"><div class="chip-body"><div class="chip-text"><div class="spinner-border spinner-border-sm ' + (idstatus == 2 ? "" : "hidden") + '" role="status"><span class="sr-only"></span></div> ' + status.text.toUpperCase() + icon + '</div></div></div>';
 }

 formatPriority = function formatPriority(row) {

     let priority = global_priorities.find((item) => { return item.value == row.idpriority; });
     let item = global_items.find((item) => { return item.id == row.iditem; });

     if (item == undefined || item.hassla == 0 || row.idstatus == 4) {
         return '<i class="fa fa-circle font-small-3 mr-50" style="color:' + priority.color + '"></i>' + priority.text;
     } else {
         let expected = moment(row.created_at).add(priority.sla, 'minutes');
         let difference = moment(expected).diff(moment(), 'minutes');
         let options = JSON.parse(priority.options);
         options.sort((a, b) => (a.time > b.time) ? 1 : -1);

         for (let i = 0; i < options.length; i++) {

             if (difference < options[i].time) {
                 return '<i class="' + (options[i].blink ? "priority-high" : "") + ' fa fa-circle font-small-3 mr-50" style="color:' + options[i].color + '"></i>' + priority.text;
             }
         }
     }
 }

 formatUsers = function formatUsers(users, users_copy, duration, idstatus) {

     //console.log(users, users_copy);

     let copies = users_copy.map(function(obj) { return obj.iduser; });

     Array.prototype.push.apply(users, users_copy);

     let result = "<ul class='list-unstyled users-list m-0 d-flex align-items-center'>";

     for (var i = 0; i < users.length; i++) {
         let user = getUser(users[i].iduser);

         if (user != undefined) {
             if (i == 4) break;

             result += "<li data-toggle='tooltip' data-popup='tooltip-custom' data-placement='bottom' data-original-title='" + user.text + "' class='avatar pull-up'>" +
                 "<span title='Copiado' class='notify-badge' " + ($.inArray(users[i].iduser, copies) != -1 ? '' : 'hidden') + ">cc</span> <img class='media-object rounded-circle' src='" + user.urlpicture + "' alt='Avatar' height='30' width='30'>" +
                 "</li>";
         }
     }

     let time = moment().startOf('day').seconds(duration).format('H:mm:ss');

     let span_more = users.length > 4 ? "<li class='d-inline-block pl-50'><span>+" + (users.length - 4) + " más</span></li>" : "";
     return result + span_more + "</ul><small class='text-muted' " + (idstatus == 4 ? '' : 'hidden') + ">" + time + "</small>";
 }

 formatCreatedAt = function formatCreatedAt(value) {
     let time = moment(value);
     return "<span title='" + time.format('YY-MM-DD HH:mm') + "'>" + time.fromNow() + "</span>"
 }

 function templateNoRecords() {
     return '<br><span style="width:150px" class="m-badge m-badge--danger" >No hay tareas disponibles</span>'
 }