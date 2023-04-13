
var isDateField = [];

$(document).ready(function () {
    initDateRangePicker();
    initDropDownListSpotBranch();
    initDropDownListChecklist();
    getDataChecklistSummary();
});

function initDropDownListSpotBranch()
{
    dropDownListSpot = $("#dropDownListSpot").kendoDropDownList({
        optionLabel: "-- Seleccione --",
        dataTextField: "text",
        dataValueField: "value",
        dataSource: getUserBranches(),
        filter: "contains",
        height: 400,
        change: changeFilter,
        dataBound: function() {

            /*
            if(getUserBranches().length > 0)
            {
                setTimeout(() => { 
                    dropDownListSpot.value(getUserBranches()[0].value);
                }, 5);
            }
            */
        },
    }).data("kendoDropDownList");
}

function initDropDownListChecklist()
{
    dropDownListChecklist = $("#dropDownListChecklist").kendoDropDownList({
        dataTextField: "text",
        dataValueField: "value",
        dataSource: window.global_checklist.filter(checklist => ($.inArray(checklist.value, [5, 6, 13, 16, 17, 18, 19, 22, 23]) != -1)),
        filter: "contains",
        height: 400,
        change: changeFilter
    }).data("kendoDropDownList");
}

function generateGrid(response)
{
    console.log(response);

    var model = generateModel(response);
    var columns = generateColumns(response);

    grid = $("#grid-checklist-summary").kendoGrid({
        dataSource: {
            transport: {
                read: function (options) {
                    options.success(response.data);
                }
            },
            pageSize: 20,
            schema: {
                model: model
            }
        },
        dataBound: function() {
            
            for (var i = 0; i < this.columns.length; i++) {
              this.autoFitColumn(i);
            }
            
          },
        noRecords: {
            template: "<div class='alert alert-danger mt-1 alert-validation-msg' role='alert'><i class='feather icon-info mr-1 align-middle'></i><span>No hay datos</span></div>"
        },
        scrollable: true,
        columns: columns,
        pageable: true,
        //resizable: true,
        editable: false,
        toolbar: ["excel", "pdf", "search"],
        height: 600,
        width: 1765,
        excel: {
            fileName: "Reporte de checklist.xlsx",
            filterable: false,
            allPages: true
        },
    }).data("kendoGrid");
}

function generateColumns(response) {
    var columnNames = response["columns"];
    return columnNames.map(function (name) {

        let object = { title: name.replaceAll('_', ' '), field: name, format: (isDateField[name] ? "{0:D}" : "") };

        (name == "Spot") ? object.locked = true : object.locked = false;

        return object;
    })
}

function generateModel(response) {

    var sampleDataItem = response["data"][0];

    var model = {};
    var fields = {};
    for (var property in sampleDataItem) {
        if (property.indexOf("ID") !== -1) {
            model["id"] = property;
        }
        var propType = typeof sampleDataItem[property];

        if (propType === "number") {
            fields[property] = {
                type: "number",
                validation: {
                    required: true
                }
            };
            if (model.id === property) {
                fields[property].editable = false;
                fields[property].validation.required = false;
            }
        } else if (propType === "boolean") {
            fields[property] = {
                type: "boolean"
            };
        } else if (propType === "string") {
            var parsedDate = kendo.parseDate(sampleDataItem[property]);
            if (parsedDate) {
                fields[property] = {
                    type: "date",
                    validation: {
                        required: true
                    }
                };
                isDateField[property] = true;
            } else {
                fields[property] = {
                    validation: {
                        required: true
                    }
                };
            }
        } else {
            fields[property] = {
                validation: {
                    required: true
                }
            };
        }
    }

    model.fields = fields;

    return model;
}

function changeFilter()
{
    $('#grid-checklist-summary').kendoGrid('destroy').empty();
    getDataChecklistSummary();
}

function getDataChecklistSummary()
{
    let request = callAjax('getDataChecklistSummary', 'GET', getFilters(), true);

    request.done(function (result) {

        generateGrid(result);

    }).fail(function (jqXHR, status) {
        console.log(' failed!');
    });
}

function getFilters()
{
    return {
        start       : $('#dateRangePicker').data('daterangepicker').startDate.format('YYYY-MM-DD'),
        end         : $('#dateRangePicker').data('daterangepicker').endDate.format('YYYY-MM-DD'),
        idspot      : dropDownListSpot.value(),
        idchecklist : dropDownListChecklist.value()
    };
}