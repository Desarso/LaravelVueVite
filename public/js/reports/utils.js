function initDateRangePicker(days = 29)
{
    switch(window.lenguague)
    {
        case 'es':
            initDateRangePickerEs(days);
            break;

        case 'en':
            initDateRangePickerEn(days);
            break;
    
        default:
            initDateRangePickerEs(days);
            break;
    }
}

function initDateRangePickerEs(days)
{
    var start = moment().subtract(days, 'days');
    var end = moment();

    function cb(start, end) {
        $('#dateRangePicker span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
    }

    $('#dateRangePicker').daterangepicker({
        startDate: start,
        endDate: end,
        ranges: {
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

    cb(start, end);

    $('#dateRangePicker').on('apply.daterangepicker', function(ev, picker) {
        changeFilter();
    });
}

function initDateRangePickerEn(days)
{
    var start = moment().subtract(days, 'days');
    var end = moment();

    function cb(start, end) {
        $('#dateRangePicker span').html(start.lang("en").format('MMMM D, YYYY') + ' - ' + end.lang("en").format('MMMM D, YYYY'));
    }

    $('#dateRangePicker').daterangepicker({
        startDate: start,
        endDate: end,
        ranges: {
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

    cb(start, end);

    $('#dateRangePicker').on('apply.daterangepicker', function(ev, picker) {
        changeFilter();
    });
}

//Lista de filtro por spot
function initDropDownListSpot() {

    let newSpots = window.global_spots.filter(spot => (spot.enabled == true) && (spot.deleted_at == null) && ($.inArray(spot.value, JSON.parse(window.user.spots)) != -1));

    dropDownListSpot = $("#dropDownListSpot").kendoDropDownList({
        optionLabel: "-- Seleccione --",
        dataTextField: "text",
        dataValueField: "value",
        template: "<div> <h5>#:data.text#</h5> <small style='color:gray;'>#:data.spotparent#</small> </div>",
        filter: "contains",
        dataSource: newSpots,
        height: 400,
        change: changeFilter,
        filtering: function(ev){
            var filterValue = ev.filter != undefined ? ev.filter.value : "";
            ev.preventDefault();

          this.dataSource.filter({
            logic: "or",
            filters: [
              {
                field: "text",
                operator: "contains",
                value: filterValue
              },
              {
                field: "spotparent",
                operator: "contains",
                value: filterValue
              }
            ]
          });
        }
    }).data("kendoDropDownList");
}

//Lista de filtro por usuario
function initDropDownListUser() {
    dropDownListUser = $("#dropDownListUser").kendoDropDownList({
        optionLabel: "-- Seleccione --",
        dataTextField: "text",
        dataValueField: "value",
        filter: "contains",
        dataSource: window.global_users,
        height: 400,
        change: changeFilter,
    }).data("kendoDropDownList");
}

//Lista de filtro por prioridad
function initDropDownListPriority() {

    dropDownListPriority = $("#dropDownListPriority").kendoDropDownList({
        optionLabel: "-- Seleccione --",
        dataTextField: "text",
        dataValueField: "value",
        dataSource: window.global_priorities,
        height: 400,
        change: changeFilter,
    }).data("kendoDropDownList");
}

//Lista de filtro por equipo
function initDropDownListTeam() {
    dropDownListTeam = $("#dropDownListTeam").kendoDropDownList({
        optionLabel: "-- Seleccione --",
        dataTextField: "text",
        dataValueField: "value",
        dataSource: window.global_teams,
        filter: "contains",
        height: 400,
        change: changeFilter,
    }).data("kendoDropDownList");
}