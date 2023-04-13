$(document).ready(function() {
    initDateRangePicker();
    initDropDownListSpot();
    initDropDownListStatus();
    initDropDownListItem();
    initDropDownListUser();
    initGridCleaning();
});

function initDropDownListItem()
{
    dropDownListItem = $("#dropDownListItem").kendoDropDownList({
        optionLabel: "-- Seleccione --",
        dataTextField: "text",
        dataValueField: "value",
        dataSource: window.cleaningItems,
        height: 400,
        change: changeFilter
    }).data("kendoDropDownList");
}

function initDropDownListStatus()
{
    dropDownListStatus = $("#dropDownListStatus").kendoDropDownList({
        optionLabel: "-- Seleccione --",
        dataTextField: "text",
        dataValueField: "value",
        dataSource: window.cleaningStatuses,
        height: 400,
        change: changeFilter
    }).data("kendoDropDownList");
}

function changeFilter()
{
    gridCleaning.dataSource.read();
}

function initGridCleaning()
{
    gridCleaning = $("#gridCleaning").kendoGrid({
        dataSource: {
            transport: {
                read: {
                    url: "getDataCleaning",
                    type: "get",
                    dataType: "json",
                    data: function() {
                        return {
                            start    : $('#dateRangePicker').data('daterangepicker').startDate.format('YYYY-MM-DD'),
                            end      : $('#dateRangePicker').data('daterangepicker').endDate.format('YYYY-MM-DD'),
                            iditem   : dropDownListItem.value(),
                            idstatus : dropDownListStatus.value(),
                            idspot   : dropDownListSpot.value(),
                            iduser   : dropDownListUser.value()
                        };
                    },
                },
            },
            pageSize: 20,
            group: {
                field: "idspot", aggregates: [ { field: "iditem", aggregate: "count" }, { field: "duration", aggregate: "sum" }] 
            },
            schema: {
                model: {
                    id: "id",
                    fields: {
                        id: { type: "number", editable: false, nullable: true },
                    }
                }
            },
        },
        toolbar: ["pdf"],
        pdf: {
            allPages: true,
            avoidLinks: true,
            paperSize: "A4",
            margin: { top: "2cm", left: "1cm", right: "1cm", bottom: "1cm" },
            landscape: true,
            repeatHeaders: true,
            scale: 0.8
        },
        editable: {
            mode: "popup"
        },
        height: "600px",
        groupable: false,
        reorderable: true,
        resizable: true,
        sortable: true,
        pageable: {
            refresh: true,
            pageSizes: true,
            buttonCount: 5,
        },
        filterable: true,
        noRecords: {
            template: "<div class='alert alert-danger mt-1 alert-validation-msg' role='alert'><i class='feather icon-info mr-1 align-middle'></i><span>No hay datos</span></div>"
        },
        columns: [
            {
                field: "id",
                title: "ID",
                width: "25px",
                filterable: false,
                hidden: true,
            },
            {
                field: "idspot",
                title: "Lugar",
                width: "0px",
                values: window.global_spots,
                groupHeaderTemplate: function(dataItem) {
                    return "<b>" + global_spots.find(o => o.value === parseInt(dataItem.value)).text + "</b>";
                },
                filterable: false,
                hidden: true
            },
            {
                field: "iditem",
                title: "Tarea",
                width: "100px",
                values: window.global_items,
                template: function(dataItem) {
                    return global_items.find(o => o.id === parseInt(dataItem.iditem)).name;
                },
                groupFooterTemplate: "<b>Limpiezas: #=count#</b>",
                filterable: false
            },
            {
                field: "startdate",
                title: "Inicio",
                width: "100px",
                template: "#=formatDate(startdate)#",
                filterable: false
            },
            {
                field: "finishdate",
                title: "Fin",
                width: "100px",
                template: "#=formatDate(finishdate)#",
                filterable: false
            },
            {
                field: "duration",
                title: "Duración",
                width: "100px",
                template: "#=formatDuration(duration)#",
                groupFooterTemplate: "#=formatTotalDuration(sum)#",
                filterable: false
            },
            {
                field: "iduser",
                title: "Responsable",
                width: "100px",
                values: window.global_users,
                filterable: false
            }
        ],
    }).data("kendoGrid");
}

function formatDuration(duration)
{
    if(duration == 0) return "-------------";
    let time = moment().startOf('day').seconds(duration).format('HH:mm:ss');
    return time;
}

function formatTotalDuration(duration)
{
    if(duration == 0) return "-------------";
    let time = moment().startOf('day').seconds(duration).format('HH:mm:ss');
    return "<b>" + time + "</b>";
}

function formatDate(date)
{
    return (date == null ? "------------------" : moment(date).format('YY-MM-DD hh:mm A'));
}