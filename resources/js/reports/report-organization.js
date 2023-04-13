var startDate;
var endDate;

$(document).ready(function () {

    initDateRangePicker();
    initDropDownListBranch();
    initDropDownListUser();
    initDropDownListChecklist();

    dropDownListChecklist.value(2);
    startDate = $('#dateRangePicker').data('daterangepicker').startDate.format('YYYY-MM-DD');
    endDate = $('#dateRangePicker').data('daterangepicker').endDate.format('YYYY-MM-DD');

    initChecklistDashboard();
    initGridBranch();
    initGridOptions();

    $(document).on("click", ".completed-bar", function(event) {
        let tickets = $(this).data("tickets");
        console.log(tickets);

        if(tickets.length == 0 || tickets == "undefined")
        {
            toastr.warning('No hay tareas', 'Información');
            return;
        }

        kendo.confirm("¿Ver tareas en el dashboard?")
        .done(function(){
            let url = "dashboard-tasks?tickets=" + tickets;
            window.open(url, '_blank');
        })
        .fail(function(){

        });
    });

});

$(document).on("change", "#showinreport", function(event) {
    changeFilter();
});

function initDropDownListChecklist()
{
    dropDownListChecklist = $("#dropDownListChecklist").kendoDropDownList({
      dataTextField: "text",
      dataValueField: "value",
      filter: "contains",
      dataSource: window.global_checklist,
      height: 400,
      change: changeFilter,
    }).data("kendoDropDownList");
}


function initDropDownListBranch()
{
    dropDownListBranch = $("#dropDownListBranch").kendoDropDownList({
      dataValueField: "id",
      dataTextField: "text",
      dataValueField: "value",
      filter: "contains",
      optionLabel: "-- Sucursal --",
      height: 400,
      dataSource: getUserBranches(),
      change: changeFilter,
    }).data("kendoDropDownList"); 
}

function changeFilter() {

    changeDates();

    chartChecklist.dataSource.read();
    gridBranch.dataSource.read();
    gridOptions.dataSource.read();
}

function changeDates() {

    startDate = $('#dateRangePicker').data('daterangepicker').startDate.format('YYYY-MM-DD');
    endDate = $('#dateRangePicker').data('daterangepicker').endDate.format('YYYY-MM-DD');

    if(getDifferenceInMonths() > 3) {
        end = moment(startDate).add(3, 'months').subtract(1, 'days');
        endDate = end.format('YYYY-MM-DD');
        $('#dateRangePicker').data('daterangepicker').setEndDate(moment(end));
        $('#dateRangePicker').data('daterangepicker').refresh();
    }
}

function getDifferenceInMonths()
{
    let start = moment(startDate);
    let end   = moment(endDate);

    return end.diff(start, 'months');
}



function initChecklistDashboard()
{
    chartChecklist = $("#chart-checklist-section").kendoChart({
        dataSource: {
            transport: {
                read: {
                    type:'get',
                    url: "getChecklistBranchReport",
                    dataType: 'json',
                    data: function() {
                        return {
                            idspot      : dropDownListBranch.value(),
                            idchecklist : dropDownListChecklist.value(),
                            iduser      : dropDownListUser.value(),
                            start       : startDate,
                            end         : endDate,
                            group       : $("#group").val(),
                            showinreport: $("#showinreport").is(":checked") ? 1 : 0
                        };
                    }
                }
            },
            requestEnd: function(e) {
                if(e.type == "read")
                {
                    let total_checked  = 0;
                    let total_approved = 0;
                    let checked_true   = 0;
                    let approved_true  = 0;
                    let approved_false = 0;  

                    $.each(e.response, function(key, value) {
                        total_checked  += value.total_checked;
                        total_approved += value.total_approved;
                        checked_true   += value.checked_true;
                        approved_true  += value.approved_true;
                         approved_false += value.approved_false;
                    });

                    let completed  = total_checked  == 0 ? 0 : Math.round((checked_true / total_checked) * 100);
                    let evaluation = total_approved == 0 ? 0 : Math.round((approved_true / total_approved) * 100);
            
                    $("#lbl-completed").text(completed + "%");
                    $("#lbl-evaluation").text((approved_false == 0 && approved_true == 0) ? '---' : (evaluation + "%"));
                }
            }
        },
        legend: {
            position: "top"
        },
        seriesDefaults: {
            type: "column"
        },
        series:
        [{
            labels: {
                visible: true,
                position: "center",
                background: "transparent",
                color: "white",
                font: "13px sans-serif",
                format: "{0}%"
            },
            group_id: "group_id",
            field: "average_completed",
            categoryField: "group_name",
            name: "Cumplimiento",
            color: '#5867dd'
        },
        {
            labels: {
                visible: true,
                position: "center",
                background: "transparent",
                color: "white",
                font: "13px sans-serif",
                format: "{0}%"
            },
            group_id: "group_id",
            field: "average_verified",
            categoryField: "group_name",
            name: "Evaluación",
            color: "#34bfa3"
        }],
        pannable: {
            lock: "y"
        },
        zoomable: {
            mousewheel: {
                lock: "y"
            },
            selection: {
                lock: "y"
            }
        },
        categoryAxis: {
            min: 2,
            max: 10,
            majorGridLines: {
                visible: false
            }
        },
        valueAxis: {
            max: 100,
            min: 0,
            labels: {
                format: "N0"
            },
            line: {
                visible: false
            }
        },
        tooltip: {
            visible: true,
            template: "#= series.name #: #= value #%"
        },
        seriesClick: function(e) {
            $("#group").val().length > 0 ? $("#group").val("") : $("#group").val(e.dataItem.group_id);
            changeFilter();
        }
    }).data("kendoChart");
}

function initGridBranch()
{
  gridBranch = $("#gridBranch").kendoGrid({
        dataSource: {
            transport: { 
                read: {
                    type:'get',
                    url: "getChecklistGroupBySpot",
                    dataType: 'json',
                    data: function() {
                        return {
                          idchecklist : dropDownListChecklist.value(),
                          idspot      : dropDownListBranch.value(),
                          iduser      : dropDownListUser.value(),
                          start       : startDate,
                          end         : endDate,
                          group       : $("#group").val(),
                          showinreport: $("#showinreport").is(":checked") ? 1 : 0
                        };
                    }
                }
            },
            requestEnd: function(e) {
                if(e.type == "read")
                {
                    let total_evaluations = 0; 
                    let evaluations_expected = e.response.length * 2;

                    $.each(e.response, function(key, value) {
                        total_evaluations += value.applied_evaluation;
                    });

                    let evaluation = total_evaluations == 0 ? 0 : Math.round((total_evaluations / evaluations_expected) * 100);
            
                    $("#lbl-completed-evaluation").text(evaluation > 100 ? "100%" : (evaluation + "%"));
                }
            }
        },
        change: function(e) {
            let row = this.dataItem(this.select());
            dropDownListBranch.value(row.idspot);
            dropDownListBranch.trigger("change");
        },
        height: "500px",
        height: 500,
        sortable: true,
        filterable: {
            mode: "row",
        },
        resizable: true,
        reorderable: true,
        selectable: true,
        noRecords: {
            template: "No hay datos disponibles"
        },
        pageable: false,
        columns:
        [
            { field: "name", title: "Sucursal", width:"200px", filterable: false},
            {
                field: "average_completed ",
                title: "Cumplimiento",
                width: "120px",
                responsive: true,
                filterable: false, 
                template: '#=formatBarCompleted(data)#',
            },
            {
                field: "average_verified ",
                title: "Evaluación",
                width: "120px",
                responsive: true,
                filterable: false,
                template: '#=formatBarEvaluation(data)#',
            },
            {
                field: "applied_checklist ",
                title: "Realizados",
                width: "120px",
                responsive: true,
                filterable: false,
                template: '#=formatApplied(data)#',
            },
            {
                field: "applied_eveluation ",
                title: "Evaluaciones",
                width: "120px",
                responsive: true,
                filterable: false,
                template: '#=formatEvaluationApplied(data)#',
            }
        ]
    }).data("kendoGrid");
}


function initGridOptions()
{
    gridOptions = $("#gridOptions").kendoGrid({
        dataSource: {
            transport: { 
                read: {
                    type:'get',
                    url: "getChecklistBranchReportBySection",
                    dataType: 'json',
                    data: function() {
                        return {
                            idspot      : dropDownListBranch.value(),
                            idchecklist : dropDownListChecklist.value(),
                            idspot      : dropDownListBranch.value(),
                            iduser      : dropDownListUser.value(),
                            start       : startDate,
                            end         : endDate,
                            group       : $("#group").val(),
                            showinreport: $("#showinreport").is(":checked") ? 1 : 0
                        };
                    }
                }
            },
            group: { field: "group" } 
        },
        height: "500px",
        height: 500,
        sortable: true,
        filterable: {
            mode: "row",
        },
        groupable: false,
        resizable: true,
        reorderable: true,
        selectable: true,
        noRecords: {
            template: "No hay datos disponibles"
        },
        pageable: false,
        columns:
        [
            { 
                field: "group",
                title: "Group",
                width: "200px",
                filterable: false,
                hidden: true,
                groupHeaderTemplate: "#= getHeader(data)#"
            },
            { 
                field: "name",
                title: "Sección",
                width:"200px",
                filterable: false,
            },
            {
                field: "average_completed ",
                title: "Cumplimiento",
                width: "120px",
                responsive: true,
                filterable: false,
                template: '#=formatBarCompleted(data)#',
            },
            {
                field: "average_verified ",
                title: "Evaluación",
                width: "120px",
                responsive: true,
                filterable: false,
                template: '#=formatBarEvaluation(data)#',
            }
        ]
    }).data("kendoGrid");
}


/*****************  Templates  ***********************/

function getHeader(data)
{
    return data.items[0].header;
}

function formatBarCompleted(data)
{


console.log(data.ticket_not_checked);

    return "<div class='completed-bar progress' data-tickets=" + JSON.stringify(data.ticket_not_checked) + " style='height:18px; margin-top:20px;' title=" + data.average_completed + ">"+
                "<div class='progress-bar bg-primary' role='progressbar' style='background-color:" + getColor(data.average_completed) + "!important; width: " + data.average_completed + "%' aria-valuenow='30' aria-valuemin='0' aria-valuemax='100'>" + data.average_completed + "%</div>"+
           "</div>";
}

function formatBarEvaluation(data)
{
    return "<div class='progress' style='height:18px; margin-top:20px;' title=" + data.average_verified + ">"+
                "<div class='progress-bar bg-success' role='progressbar' style='background-color:" + getColor(data.average_verified) + "!important; width: " + data.average_verified + "%' aria-valuenow='30' aria-valuemin='0' aria-valuemax='100'>" + data.average_verified + "%</div>"+
           "</div>";
}

function formatApplied(data)
{   
    let color = "danger";
    let icon  = "";

    if(data.applied_checklist[0] >= data.applied_checklist[1])
    {
        color = "success";
        icon  = "<i class='fad fa-check-circle'></i>";
    }

    return "<span class='badge badge-" + color + "' style='font-weight:500;font-size: 14px;'>" + icon + " " + data.applied_checklist[0] + ' de ' + data.applied_checklist[1] +"</span>";
}

function formatEvaluationApplied(data)
{   
    let color = "danger";
    let icon  = "";

    if(data.applied_evaluation >= 2)
    {
        color = "success";
        icon  = "<i class='fad fa-check-circle'></i>";
    }

    return "<span title='Códigos: " + data.evaluation_codes + "' class='badge badge-" + color + "' style='font-weight:500;font-size: 14px;'>" + icon + " " + data.applied_evaluation + " de 2</span>";
}

function getColor(average)
{
    if(average >= 96)
    {
        return "#308a3f"
    }
    else if(average < 96 && average > 90)
    {
        return "#2daf43";
    }
    else if(average <= 90 && average > 70)
    {
        return "#ef7b29";
    }
    else
    {
        return "#e0361b";
    }
}
