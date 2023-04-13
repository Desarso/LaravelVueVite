var startDate = null;
var endDate = null;
var pageSelected = null;
var weekYear = null;
var yearNumber = null;

$(document).ready(function() {
    initDropDownListUser();
    initDropDownListTeam();
    initDropDownListSchedule();
    initMonthPicker();
    setDateRange();
    initGridUserSchedule();
    initPagination(); 
    updateColumnTitle();

    startDatePicker = $("#start").kendoDatePicker({
        value: new Date(),
        change: startChange
    }).data("kendoDatePicker");

    endDatePicker = $("#end").kendoDatePicker({
        value: new Date(),
        change: endChange
    }).data("kendoDatePicker");

    startDatePicker.max(endDatePicker.value());
    endDatePicker.min(startDatePicker.value());

    changeFilter();
});

$("#btn-excel").click(function() {
    $("#modal-export").modal('show');
});

$("#btn-export-excel").click(function() {

    $.blockUI({ message: '<h1>Procesando...</h1>' });

    let request = callAjax(
        "exportUserSchedule", 
        'GET', 
        { 
            "start": moment(startDatePicker.value()).format("M/DD/YYYY"), 
            "end": moment(endDatePicker.value()).format("M/DD/YYYY"), 
            idteam     : dropDownListTeam.value(),
        }
    );

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

function startChange()
{
    var startDate = startDatePicker.value(),
    endDate = endDatePicker.value();

    if (startDate) {
        startDate = new Date(startDate);
        startDate.setDate(startDate.getDate());
        endDatePicker.min(startDate);
    } else if (endDate) {
        startDatePicker.max(new Date(endDate));
    } else {
        endDate = new Date();
        startDatePicker.max(endDate);
        endDatePicker.min(endDate);
    }
}

function endChange()
{
    var endDate = endDatePicker.value(),
    startDate = startDatePicker.value();

    if (endDate) {
        endDate = new Date(endDate);
        endDate.setDate(endDate.getDate());
        startDatePicker.max(endDate);
    } else if (startDate) {
        endDatePicker.min(new Date(startDate));
    } else {
        endDate = new Date();
        startDatePicker.max(endDate);
        endDatePicker.min(endDate);
    }
}

function changeFilter()
{
    setDateRange();
    gridUserSchedule.dataSource.read();
    updateColumnTitle();
}

function initDropDownListSchedule()
{
    dropDownListSchedule = $("#dropDownListSchedule").kendoDropDownList({
        optionLabel: "-- Seleccione --",
        dataTextField: "text",
        dataValueField: "value",
        filter: "contains",
        dataSource: window.schedules,
        height: 400,
        change: changeFilter,
    }).data("kendoDropDownList");
}

function initGridUserSchedule()
{
    gridUserSchedule = $("#gridUserSchedule").kendoGrid({
        excel: {
            fileName: "Whagons Planner.xlsx",
        },
        dataSource: {
            transport: {
                read: {
                    url: "getUserSchedule",
                    type: "get",
                    dataType: "json",
                    data: function() {
                        return {
                            idteam     : dropDownListTeam.value(),
                            iduser     : dropDownListUser.value(),
                            idschedule : dropDownListSchedule.value(),
                            start : startDate,
                            end : endDate
                        };
                    },
                },
                update: {
                    url: "updateUserSchedule",
                    type: "post",
                    dataType: "json",
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
                    }
                },
                parameterMap: function(options, operation) {
                    if (operation == "read") {
                        return options;
                    }
                    if (operation !== "read" && options.models) {
                        return {models: kendo.stringify(options.models), iduser: options.iduser, start: startDate, end: endDate};
                    }
                }
            },
            batch: true,
            pageSize: 20,
            schema: {
                model: {
                    id: "id",
                    fields: {
                        id: { type: "number", editable: false, nullable: true }
                    }
                }
            },
            requestEnd: function(e) {
                if (e.type == 'create' || e.type == 'update') {
                    gridUserSchedule.dataSource.read();
                }
            }
        },
        editable: true,
        toolbar: ["save", "cancel"],
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
        height: "650px",
        noRecords: {
            template: "<div class='alert alert-danger mt-1 alert-validation-msg' role='alert'><i class='feather icon-info mr-1 align-middle'></i><span>No hay datos</span></div>"
        },
        dataBound: function () {
            setColumnColor();
        },
        columns: [
            {
                field: "id",
                title: "Usuario",
                values: window.global_users,
                template: "#=formatUser(id)#",
                width: 110,
                filterable: false
            },
            {
                field: "SUN",
                title: "Domingo",
                values: window.schedules,
                editor: dropDownListEditorSchedule,
                width: 80,
                filterable: false
            },
            {
                field: "MON",
                title: "Lunes",
                values: window.schedules,
                editor: dropDownListEditorSchedule,
                width: 80,
                filterable: false
            },
            {
                field: "TUES",
                title: "Martes",
                values: window.schedules,
                editor: dropDownListEditorSchedule,
                width: 80,
                filterable: false
            },
            {
                field: "WED",
                title: "Miércoles",
                values: window.schedules,
                editor: dropDownListEditorSchedule,
                width: 80,
                filterable: false
            },
            {
                field: "THURS",
                title: "Jueves",
                values: window.schedules,
                editor: dropDownListEditorSchedule,
                width: 80,
                filterable: false
            },
            {
                field: "FRI",
                title: "Viernes",
                values: window.schedules,
                editor: dropDownListEditorSchedule,
                width: 80,
                filterable: false
            },
            {
                field: "SAT",
                title: "Sábado",
                values: window.schedules,
                editor: dropDownListEditorSchedule,
                width: 80,
                filterable: false
            }
        ],
    }).data("kendoGrid");   
}

function getSchedule(id)
{
    let text = (window.schedules.filter(function(c) { return c.value === id; })[0] || {}).text;
    return "<div style=' background-color: red;'>" + text + "</div>";
}

function dropDownListEditorSchedule(container, options)
{
    $('<input data-text-field="text" data-value-field="value" data-bind="value:' + options.field + '"/>')
        .appendTo(container)
        .kendoDropDownList({
            autoBind: false,
            //optionLabel: " ",
            valuePrimitive: true,
            filter: "contains",
            dataSource: window.schedules, //filterSchedules(options.model.teams),
            height: 400
        });
}

function filterSchedules(teams)
{
    if(teams.length == 0) return window.schedules;
    return window.schedules.filter(schedule => $.inArray(teams[0].id, JSON.parse(schedule.teams)) != -1);
}

function formatUser(iduser)
{
    if (iduser == 0) return "";

    let user = getUser(iduser);

    return "<li data-toggle='tooltip' data-popup='tooltip-custom' data-placement='top' data-original-title='" + user.text + "' class='avatar pull-up'>" +
        "<img class='media-object rounded-circle' src='" + user.urlpicture + "' alt='Avatar' height='30' width='30'> " +
        "</li>" + user.text;
}

function formatCoreTeam(teams)
{
    if(teams.length == 0) return "-----------";

    return teams[0].name;
}

function initDropDownListMonth()
{
    dropDownListMonth = $("#dropDownListMonth").kendoDropDownList({
        optionLabel: "-- Seleccione --",
        dataTextField: "text",
        dataValueField: "value",
        dataSource: {
            data: [{"value":"1","text":"Enero"},{"value":"2","text":"Febrero"},{"value":"3","text":"Marzo"},{"value":"4","text":"Abril"},{"value":"5","text":"Mayo"},{"value":"6","text":"Junio"},{"value":"7","text":"Julio"},{"value":"8","text":"Agosto"},{"value":"9","text":"Septiembre"},{"value":"10","text":"Octubre"},{"value":"11","text":"Noviembre"},{"value":"12","text":"Diciembre"}]
        },
        height: 400,
        change: changeFilter
    }).data("kendoDropDownList");

    
}

function initMonthPicker()
{
    monthPicker = $("#monthpicker").kendoDatePicker({
        culture: "es-ES",
        start: "year",
        depth: "year",
        format: "MMMM yyyy",
        dateInput: true,
        change: changeFilter,
        value: new Date()
    }).data("kendoDatePicker");

    $("#monthpicker").attr("readonly","readonly");
}


function initPagination() {

    pageSelected = moment().week() - moment().startOf('month').week() + 1;
    if (pageSelected < 1) pageSelected = moment().week();
    if (pageSelected > 5) pageSelected = 5;


    $('.page1-links').twbsPagination({
        totalPages: 5,
        visiblePages: 5,
        prev: ' ',
        next: ' ',
        first: false,
        last: false,
        loop: true,
        startPage: pageSelected,
    });

    $(document).on("click", ".page-item:not(.prev,.next)", function(event) {
        pageSelected = $('.page-item.active').text();
        validateGridHasChanges();
    });


    $(document).on("click", ".next", function(event) {
        pageSelected = $('.page-item.active').text();

        if (pageSelected == 1) {
            var newDate = moment(endDate).add(1, 'W').format();
            monthPicker.value(newDate);
        }

        validateGridHasChanges();
    });

    $(document).on("click", ".prev", function(event) {
        pageSelected = $('.page-item.active').text();
        if (pageSelected == 5) {
            var newDate = moment(startDate).subtract(1, 'W').format();
            monthPicker.value(newDate);
        }
        validateGridHasChanges();
    });
}

function setDateRange() {

    let monthNumber = monthPicker.value().getMonth();
    yearNumber = monthPicker.value().getFullYear()

    if (monthNumber == 0) {
        weekYear = parseInt(pageSelected - 1);
    } else {
        var weekNumber =  moment().month(monthNumber).startOf('month').week();
        weekYear = parseInt(pageSelected - 1) + weekNumber;
    }

    start = moment().year(yearNumber).week(weekYear).day(0);
    startDate = start.format('YYYY-MM-DD');
    end = moment().year(yearNumber).week(weekYear).day(6);
    endDate = end.format('YYYY-MM-DD');
    $("#dateRange").html(`${start.format('MMMM D, YYYY')} - ${end.format('MMMM D, YYYY')}`);
}

function updateColumnTitle() {

    let daysOfWeek = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado']
    let grid = $("#gridUserSchedule").data("kendoGrid");
    
    daysOfWeek.forEach((day, i) => {
        let dateNumber = moment().year(yearNumber).week(weekYear).day(i).dates();
        grid.thead.find(`[data-title~='${day}']`).html(`${day} ${dateNumber}`);
    });
}

function setColumnColor() {
    $('td').each(function(){

        switch ($(this).text()) {

            case 'DAY OFF':
                $(this).css("background-color", '#8EDBF9');
                $(this).addClass('day-off');
                break;

            case 'PSG':
                $(this).css("background-color", '#4DCB58');
                $(this).addClass('day-off');
                break;

            case 'PCG':
                $(this).css("background-color", '#DCDCDC');
                $(this).addClass('day-off');
                break;

            case 'ANJ':
                $(this).css("background-color", '#F14848');
                $(this).addClass('day-off');
                break;

            case 'INC':
                $(this).css("background-color", '#F1EC48');
                $(this).addClass('day-off');
                break;

            case 'VAC':
                $(this).css("background-color", '#CB7EFB');
                $(this).addClass('day-off');
                break;

            case 'SUSP':
                $(this).css("background-color", '#EB7D30');
                $(this).addClass('day-off');
                break;

            case 'Otros':
                $(this).css("background-color", '#30EB5C');
                $(this).addClass('day-off');
                break;
        }
    })
}


function validateGridHasChanges() {

   var hasChanges = gridUserSchedule.dataSource.hasChanges();

   if (hasChanges) {
        kendo.confirm("Desea guardar los cambios?").then(function () {
            gridUserSchedule.saveChanges();
            if(gridUserSchedule) changeFilter();
        }, function () {
            if(gridUserSchedule) changeFilter();
        });
   } else {
    if(gridUserSchedule) changeFilter();
   }
}