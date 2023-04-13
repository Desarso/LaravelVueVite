$(document).ready(function() {
    initGridAssetLoanNotes();
});

$('#modal-asset-loan-notes').on('show.bs.modal', function(e) {
    $("#modal-title-asset-loan-notes").html(window.loanSelected.id + ' - ' + global_assets.find(o => o.value === parseInt(window.loanSelected.idasset)).text);
    $("#note").val("");
});

function initGridAssetLoanNotes()
{
    gridAssetLoanNotes = $("#gridAssetLoanNotes").kendoGrid({
        dataSource: {
            transport: {
                read: {
                    url: "getAssetLoanNotes",
                    type: "get",
                    dataType: "json",
                    data: function() {
                        return {
                            idassetloan : (window.loanSelected == null) ? null : window.loanSelected.id
                        };
                    },
                }
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
            aggregate: [ { field: "created_by",    aggregate: "count" } ]
        },
        editable: false,
        toolbar: false,
        reorderable: false,
        resizable: false,
        sortable: false,
        pageable: false,
        filterable: false,
        //scrollable: true,
        height:'400px',
        noRecords: {
            //template: kendo.template($("#template-no-data").html()),
        },
        columns: [
            {
                field: "created_by",
                title: "Usuario",
                values: window.global_users,
                width: "170px",
                aggregates: ["count"],
                footerTemplate: "<b>Notas: #: count # </b>",
                filterable: false
            },
            {
                field: "note",
                title: "Nota",
                width: "250px",
                template: function(dataItem) {

                    if(dataItem.type == 'TEXT') return dataItem.note;

                    return "<img src=" + dataItem.note + " alt='avatar' height='250' width='310'></img>";
                },
                filterable: false
            },
            {
                field: "created_at",
                title: "Fecha",
                width: "120px",
                template: function(dataItem) {
                    return moment(dataItem.created_at).format('YY-MM-DD hh:mm A');
                },
                filterable: false
            },
        ],
    }).data("kendoGrid"); 

    $("#context-menu-note").kendoContextMenu({
        target: "#gridAssetLoanNotes",
        filter: "tr[role='row']",
        dataSource: [],
        open: function(e) {

            var model = gridAssetLoanNotes.dataItem(e.target);
            
            var actions = [{
                text: "<i class='fas fa-trash' aria-hidden='true'> <b>Eliminar</b></i>",
                encoded: false,
                cssClass: 'context-menu',
                action: 'delete'
            }]

            if(user.id != model.created_by) actions = [];

            this.setOptions({ dataSource: actions });
        },
        select: function(e) {

            let option = this.dataSource.getByUid($(e.item).data('uid'));
            window.noteSelected = gridAssetLoanNotes.dataItem(e.target);

            switch(option.action)
            {
                case "delete":
                    deleteAssetLoanNote();
                    break;
            }
        }
    });
}

$("#btn-create-note").click(function(e) {
    createAssetLoanNote();
});

function createAssetLoanNote()
{
    if($("#note").val() == "")
    {
        toastr.warning("Escriba una nota");
        return false;
    }
    
    let data = {"idassetloan" : window.loanSelected.id, "note" : $("#note").val()};

    let request = callAjax('createAssetLoanNote', 'POST', data, true);

    request.done(function(result) {

        $("#modal-asset-loan-notes").modal("hide");
        gridAssetLoanNotes.dataSource.read();
        gridAssetLoan.dataSource.read();
        $.unblockUI();

    }).fail(function(jqXHR, status) {
        $.unblockUI();
        console.log('error!');
    });
}

function deleteAssetLoanNote()
{
    let request = callAjax('deleteAssetLoanNote', 'POST', {"id" : window.noteSelected.id}, true);

    request.done(function(result) {

        //$("#modal-asset-loan-notes").modal("hide");
        gridAssetLoanNotes.dataSource.read();
        gridAssetLoan.dataSource.read();
        $.unblockUI();

    }).fail(function(jqXHR, status) {
        $.unblockUI();
        console.log('error!');
    });
}
