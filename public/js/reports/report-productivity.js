$(document).ready(function() {
    initDateRangePicker();
    initDropDownListTeam();
    initDropDownListUser();

    initGridTeam();
    initGridUsers();
    getIndicatorStats();
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

function changeFilter()
{
    gridTeam.dataSource.read();
    gridUsers.dataSource.read();
    getIndicatorStats();
}

function initGridTeam()
{
    gridTeam = $("#gridTeam").kendoGrid({
        dataSource: {
            transport: {
                read: {
                    url: "getProductivityByTeam",
                    type: "get",
                    dataType: "json",
                    data: () => getFilters(),
                },
            },
            pageSize: 20,
            schema: {
                model: {
                    id: "id",
                    fields: {
                        idteam: { type: "number", editable: false, nullable: true },
                        total: { type: "number", editable: false, nullable: true },
                        efectivity: { type: "number", editable: false, nullable: true },
                        productivity: { type: "number", editable: false, nullable: true }
                    }
                }
            },
        },
        excelExport: function(e) {
            
            var sheet = e.workbook.sheets[0];
            sheet.columns[0].autoWidth = true;
        
            for (var i = 1; i < sheet.rows.length; i++) {
                sheet.rows[i].height = 30;
                sheet.rows[i].cells[0].fontSize = 25;
            }
        },
        toolbar: ["excel", "pdf"],
        excel: {
            fileName: "Reporte Productividad general.xlsx",
            filterable: false,
            allPages: true
        },
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
        height:"600px",
        reorderable: true,
        resizable: true,
        sortable: true,
        pageable: {
          refresh: true,
          pageSizes: true,
          buttonCount: 5,
        },
        noRecords: {
            template: "<div class='alert alert-danger mt-1 alert-validation-msg' role='alert'><i class='feather icon-info mr-1 align-middle'></i><span>No hay datos</span></div>"
        },
        filterable: true,
        dataBound: function(e) {},
        columns: [
            {
                field: "idteam",
                title:  "Equipo",
                values: window.global_teams,
                template: function(dataItem) {
                    let team = window.global_teams.find(o => o.value === parseInt(dataItem.idteam)).text;
                    return `<strong>${team}</strong>`;
                },
                width: "60px",
                filterable: false
            },
            {
                field: "total",
                title: "Tareas",
                width: "50px",
                filterable: false
            },
            {
                field: "productivity",
                title: "Productividad",
                template: function(dataItem) {
                    return `${dataItem.productivity}%`;
                },
                width: "50px",
                filterable: false
            },
            {
                field: "efectivity",
                title: "Efectividad",
                template: function(dataItem) {
                    return `${dataItem.efectivity}%`;
                },
                width: "50px",
                filterable: false
            }
        ]
    }).data("kendoGrid");
}

function initGridUsers()
{
    gridUsers = $("#gridUsers").kendoGrid({
        dataSource: {
            transport: {
                read: {
                    url: "getProductivityByUser",
                    type: "get",
                    dataType: "json",
                    data:() => getFilters(),
                },
            },
            pageSize: 20,
            schema: {
                model: {
                    id: "id",
                    fields: {
                        id: { type: "number", editable: false, nullable: true },
                        name: { type: "text", editable: false, nullable: true },
                        urlpicture: { type: "text", editable: false, nullable: true },
                        total: { type: "number", editable: false, nullable: true },
                        efectivity: { type: "number", editable: false, nullable: true },
                        productivity: { type: "number", editable: false, nullable: true }
                    }
                }
            },
        },
        excelExport: function(e) {
            
            var sheet = e.workbook.sheets[0];
            sheet.columns[0].autoWidth = true;
        
            for (var i = 1; i < sheet.rows.length; i++) {
                sheet.rows[i].height = 30;
                sheet.rows[i].cells[0].fontSize = 25;
            }
        },
        toolbar: ["excel", "pdf"],
        excel: {
            fileName: "Reporte productividad por usuario.xlsx",
            filterable: false,
            allPages: true
        },
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
        height:"600px",
        reorderable: true,
        resizable: true,
        sortable: true,
        pageable: {
          refresh: true,
          pageSizes: true,
          buttonCount: 5,
        },
        noRecords: {
            template: "<div class='alert alert-danger mt-1 alert-validation-msg' role='alert'><i class='feather icon-info mr-1 align-middle'></i><span>No hay datos</span></div>"
        },
        filterable: true,
        dataBound: function(e) {},
        columns: [
            {
                field: "name",
                title:  "Nombre",
                template: "#=formatUserInfo(name, urlpicture)#",
                width: "60px",
                filterable: false
            },
            {
                field: "total",
                title: "Tareas",
                width: "50px",
                filterable: false
            },
            {
                field: "productivity",
                title: "Productividad",
                template: function(dataItem) {
                    return `${dataItem.productivity}%`;
                },
                width: "50px",
                filterable: false
            },
            {
                field: "efectivity",
                title: "Efectividad",
                template: function(dataItem) {
                    return `${dataItem.efectivity}%`;
                },
                width: "50px",
                filterable: false
            }
        ]
    }).data("kendoGrid");
}

function formatUserInfo(name, urlpicture) {
    let result = "";

    (urlpicture == null || urlpicture == "") ? (urlpicture = 'https://dingdonecdn.nyc3.digitaloceanspaces.com/general/dummy.png') : urlpicture = urlpicture;
    result += '<div class="image_outer_container"><img  class="round" width="30" height="30" src="' + urlpicture + '"+"></div>';
    result += "<div style='color: #212529; display: inline-block; vertical-align: middle'><strong>" + name +"</strong></div>";

    return result;
}

function getFilters() {

    return {
        start       : $('#dateRangePicker').data('daterangepicker').startDate.format('YYYY-MM-DD'),
        end         : $('#dateRangePicker').data('daterangepicker').endDate.format('YYYY-MM-DD'),
        idteam      : dropDownListTeam.value(),
        iduser      : dropDownListUser.value()
    };
}

function getIndicatorStats() {

    let data = getFilters();

    let request = callAjax('getProductivityGeneral', 'GET', data, true);

    request.done(function(result) {
        $("#count-pruductivity").text(`${result['productivity']}%`);
        $("#count-efectivity").text(`${result['efectivity']}%`);
        $("#count-approved").text(result['approved']);
        $("#count-reproved").text(result['reprobate']);
        // $("#count-reopen").text(result['reopen']);
    });
}