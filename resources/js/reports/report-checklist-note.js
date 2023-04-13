$(document).ready(function() {
    initDateRangePicker();
    initDropDownListBranch();
    initDropDownListChecklist();
    initGridChecklistNote();

    $(document).on("click", ".image", function(event) {
	    window.open($(this)[0].src, '_blank');
    });
});

$("#btnRefresh").click(function() {
    changeFilter();
});

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

function initDropDownListChecklist()
{
    dropDownListChecklist = $("#dropDownListChecklist").kendoDropDownList({
        filter: "contains",
        optionLabel: "-- Seleccione --",
        dataTextField: "text",
        dataValueField: "value",
        dataSource: window.global_checklist,
        height: 400,
        change: changeFilter
    }).data("kendoDropDownList");
}

function changeFilter()
{
    gridChecklistNote.dataSource.read();
}

function initGridChecklistNote()
{
    gridChecklistNote = $("#gridChecklistNote").kendoGrid({
        dataSource: {
            transport: {
                read: {
                    url: "getDataChecklistNote",
                    type: "get",
                    dataType: "json",
                    data: function() {
                        return getParams();
                    },
                },
            },
            pageSize: 20,
            schema: {
                model: {
                    id: "id",
                    fields: {
                        id: { type: "number", editable: false, nullable: true },
                    }
                }
            },
        },
        toolbar: [],
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
        dataBound: function() {},
        columns: [
            {
                field: "idspot",
                title: "Sucursal",
                values: window.global_spots,
                width: "120px",
                filterable: false
            },
            {
                field: "option",
                title: "Ítem",
                width: "300px",
                filterable: false
            },
            {
                field: "note",
                title: "Comentario",
                template: "#=formatNote(note, type)#",
                width: "300px",
                filterable: false
            },
            {
                field: "created_at",
                title: "Fecha",
                template: "#=formatDate(created_at)#",
                width: "80px",
                filterable: false
            }
        ],
    }).data("kendoGrid");
}

function getParams()
{
    return {
        start       : $('#dateRangePicker').data('daterangepicker').startDate.format('YYYY-MM-DD'),
        end         : $('#dateRangePicker').data('daterangepicker').endDate.format('YYYY-MM-DD'),
        idspot      : dropDownListBranch.value(),
        idchecklist : dropDownListChecklist.value()
      };
}

function formatDate(date)
{
    return (date == null ? "-----------------------" : moment(date).format('YY-MM-DD hh:mm A'));
}

function formatNote(note, type)
{
    if(type == 1)
    {
        return note;
    }

    return "<img src='" + note + "' class='img-fluid image' alt='' style='height: 120px; width: 200px;'></img>";
}

$("#btn-pdf").click(function () {
    generatePdfChecklistNote();
});

function generatePdfChecklistNote()
{
    $.blockUI({ message: '<h1>Procesando...</h1>' });

    $.ajax({
        type: 'POST',
        url: 'generatePdfChecklistNote',
        data: getParams(),
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        xhrFields: {
            responseType: 'blob'
        },
        success: function(response){
            console.log(response);
            $.unblockUI();
            var blob = new Blob([response]);
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = "Test.pdf";
            link.click();
        },
        error: function(blob){
            $.unblockUI();
            toastr.error('La acción no se puedo completar', '¡Hubo un problema!');
            console.log(blob);
        }
    });
}

