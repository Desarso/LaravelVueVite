$(document).ready(function() {

    initDropDownChecklist();

    var c = new ChecklistOption();
    c.initGrid();
    fixKendoGridHeight();

    $('.k-grid-data').before($('#dropDownParent'));
    $('#dropDownParent').show();

    $("#export").click(function(e) {
        var grid = $("#gridChecklistOptions").data("kendoGrid");
        grid.saveAsExcel();
    });

    $(".k-grid-checklists").click(function(e) { // note that the class is k-grid-new since the name of the command is new
        e.preventDefault();
        window.location = 'config-checklists';
    });

    $(".k-grid-metrics").click(function(e) { // note that the class is k-grid-new since the name of the command is new
        e.preventDefault();
        window.location = 'config-metrics';
    });

});


var ChecklistOption = /** @class */ (function() {
    function ChecklistOption() {}
    ChecklistOption.prototype.initGrid = function() {
        var grid = $("#gridChecklistOptions").kendoGrid({
            excel: {
                fileName: "Whagons Checklists.xlsx",
            },
            dataSource: {
                transport: {

                    read: {
                        url: "getChecklistOptions",
                        type: "get",
                        dataType: "json",
                        data: function() {
                            return {
                                idchecklist: dropDownChecklist.value()
                            };
                        },
                    },
                    create: {
                        url: "api/createChecklistOption",
                        type: "post",
                        dataType: "json",
                        data: function() {
                            return {
                                reorder: window.reorder
                            }
                        }
                    },
                    update: {
                        url: "api/updateChecklistOption",
                        type: "post",
                        dataType: "json"
                    },
                    destroy: {
                        url: "api/deleteChecklistOption",
                        type: "delete",
                    }
                },
                pageSize: 20,
                schema: {
                    model: {
                        id: "id",
                        fields: {
                            id: { type: "number", editable: false, nullable: true },
                            name: { editable: true, field: "name", type: "string", validation: { required: { message: "Nombre es requerido" } } },
                            idchecklist: { editable: true, field: "idchecklist", type: "number" },
                            optiontype: { editable: true, field: "optiontype", type: "number", defaultValue: 1 },
                            position: { editable: true, field: "position", type: "number" },
                            group: { editable: true, field: "group", type: "number", nullable: true },
                            isgroup: { editable: true, field: "isgroup", type: "boolean" },
                            iddata: { editable: true, field: "iddata", type: "number", nullable: true },
                            iditem: { editable: true, field: "iditem", type: "number", nullable: true },
                            idspot: { editable: true, field: "idspot", type: "number", nullable: true },
                            idasset: { editable: true, field: "idasset", type: "number", nullable: true },
                            departments: { field: "departments" },
                            startdate: { editable: true, field: "startdate", type: "time", nullable: true },
                            idmetric: { editable: true, field: "idmetric", type: "number", nullable: true },
                            enabled: { editable: true, field: "enabled", type: "boolean", defaultValue: true },
                        }
                    }
                },

                requestEnd: function(e) {
                    if (e.type != 'read')
                        $("#gridChecklistOptions").data("kendoGrid").dataSource.read();

                }

            },

            dataBinding: function(e) {
                if (window.sorting) return;

                let rows = e.sender.select();
                let selected = e.sender.dataItem(rows[0]);
                if (e.action == 'rebind' || e.action == 'sync') {
                    // Set current Group and Position
                    let length = e.sender.dataSource.total();
                    if (length > 0) window.group = e.sender.dataSource.data()[length - 1].group;
                    window.position = length + 1;

                } else if (e.action == 'add') {
                    if (selected != null) { // There is a row selected
                        e.items[0].group = selected.group;
                        e.items[0].position = selected.position + 1;
                        e.items[0].idchecklist = dropDownChecklist.value();
                        window.reorder = true;
                    } else {
                        window.e = e;
                        e.items[0].group = window.group;
                        e.items[0].position = window.position;
                        e.items[0].idchecklist = dropDownChecklist.value();
                        window.reorder = false;
                    }
                }

            },

            dataBound: function(e) {
                var data = e.sender.dataSource.data();
                $.each(data, function(i, row) {
                    let tr = $('tr[data-uid="' + row.uid + '"]');
                    if (row.optiontype == 6) { // Header
                        tr.addClass('is-chechklist-group blog-shadow-dreamy');
                    }
                })
            },

            edit: function(e) {
                $('div[data-container-for="position"]').hide();
                $('label[for="position"]').hide();
                $('div[data-container-for="group"]').hide();
                $('label[for="group"]').hide();
            },
            save: function(e) {
                window.e = e;
                let optiontype = window.optionTypes.find((obj) => { return obj.value == e.model.optiontype; });
                if(optiontype.hasdata == true && e.model.iddata == null)
                {
                    e.preventDefault();
                    toastr.error('Seleccione la data a utilizar', 'Información incompleta');
                }

                if (e.model.isNew() && e.model.isgroup == 1) { // Nuevo Grupo
                    e.model.group = window.group + 1;
                }
            },
            editable: {
                mode: "popup"
            },
            toolbar: [{ template: kendo.template($("#toolbartemplateAcceptChanges").html()) }, { template: kendo.template($("#toolbartemplate").html()) }, { name: "data", text: " &nbsp;" + locale("Data"), iconClass: "fad fa-book-open commandIconOpacity" }, { name: "metrics", text: " &nbsp;" + locale("Metrics"), iconClass: "fad fa-ruler-combined commandIconOpacity" }],
            height: 700,
            sortable: true,
            selectable: true,
            reorderable: true,
            resizable: true,
            navigatable: true,
            pageable: {
                refresh: true,
                pageSizes: true,
                buttonCount: 5,
            },
            noRecords: true,
            messages: {
                noRecords: "No Data!"
            },
            filterable: true,
            columns: [{
                    selectable: true,
                    width: "50px",
                    hidden: true
                },
                { command: { name: "edit", text: { edit: " " }, iconClass: "fad fa-pen commandIconOpacity" }, title: " ", width: "80px", media: "(min-width: 850px)" },
                {
                    field: "name",
                    title: locale("Name"),
                    width: "400px",
                    groupable: false,
                    template: "#=formatName(name, optiontype, isgroup)#",
                    media: "(min-width: 350px)",
                    filterable: {
                        multi: true,
                        search: true
                    }
                },
                {
                    field: "optiontype",
                    title: locale("Type"),
                    template: "#=formatType(optiontype, isgroup)#",
                    values: window.optionTypes,
                    hidden: true,
                    filterable: {
                        multi: true,
                        search: true
                    }
                },
                {
                    field: "isgroup",
                    title: locale("Is Group"),
                    hidden: true
                },
                {
                    field: "idmetric",
                    title: locale("Metric"),
                    filterable: {
                        multi: true,
                        search: true
                    }
                },
                {
                    field: "idchecklist",
                    title: locale("Checklist"),
                    values: window.global_checklist,
                    hidden: true
                },
                {
                    field: "iddata",
                    title: locale("Data"),
                    values: window.checklistData,
                    editor: dropDownListEditor
                },
                {
                    field: "iditem",
                    editor: dropDownListEditor,
                    title: locale("Item"),
                    values: window.items,
                    hidden: true,
                    filterable: {
                        multi: true,
                        search: true
                    }
                },
                {
                    field: "idspot",
                    title: locale("Spot"),
                    editor: dropDownListEditor,
                    values: window.global_spots,
                    hidden: true,
                    filterable: {
                        multi: true,
                        search: true
                    }
                },
                {
                    field: "departments",
                    title: "Equipos",
                    values: global_teams,
                    editor: editorMultiSelectTeam,
                    hidden: true,
                    filterable: {
                        multi: true,
                        search: true
                    }
                },
                {
                    field: "position",
                    title: locale("Position"),
                    hidden: true
                },
                {
                    field: "group",
                    title: locale("Group"),
                    hidden: true
                },
                { command: { name: "notify", text: "", iconClass: "fad fa-envelope commandIconOpacity  " }, title: " ", width: "50px" },
                { command: { name: "destroy", text: " ", iconClass: "fad fa-trash commandIconDelete" }, title: " ", width: "80px", media: "(min-width: 850px)" }
            ],
        }).data("kendoGrid");
        sortableGrid(grid);

    };

    return ChecklistOption;
}());


function sortableGrid(grid) {
    grid.table.kendoSortable({
        autoScroll: true,
        filter: ">tbody >tr",
        hint: function(element) { //customize the hint
            var table = $('<table style="width: 500px;" class="k-grid k-widget"></table>');

            window.e = element;
            table.append(element.clone()); //append the dragged element
            table.css("opacity", 0.8);
            table.css("font-size", '1.2rem');

            return table; //return the hint element
        },
        cursor: "move",
        placeholder: function(element) {
            return $('<tr colspan="4" class="placeholder"></tr>');
        },
        change: function(e) {
            $("button[data-type='changes']").removeClass('hidden');
            console.log(e);
            console.log(grid.dataSource.skip());
            var skip = grid.dataSource.skip(),
                oldIndex = e.oldIndex + skip,
                newIndex = e.newIndex + skip,
                data = grid.dataSource.data(),
                dataItem = grid.dataSource.getByUid(e.item.data("uid"));

            window.sorting = true;
            grid.dataSource.remove(dataItem);
            grid.dataSource.insert(newIndex, dataItem);
            window.sorting = false;
            // orderGrid();

        }

    });
}


function cancelChanges() {
    $("#gridChecklistOptions").data("kendoGrid").dataSource.read();
    $("button[data-type='changes']").addClass('hidden');
}

function reorderOptions() {

    $("button[data-type='changes']").addClass('hidden');
    let data = $("#gridChecklistOptions").data("kendoGrid").dataSource.data();
    console.log('reorderOptions');
    window.d = data;
    let request = callAjax('reorderOptions', 'POST', { "data": JSON.stringify(data) }, false);
    request.done(function(result) {
        $("#gridChecklistOptions").data("kendoGrid").dataSource.read();

    }).fail(function(jqXHR, status) {
        console.log('fail reorderOptions');
        console.log(jqXHR);
        console.log(status);
        alert('Algo salió mal!');
    });
}

/// Helpers

function formatType(optiontype, isgroup) {

    return optiontype;
}

function formatName(name, optiontype, isgroup) {
    if (optiontype == 6 && isgroup == 0)
        return '<span style="font-size: 1rem; font-weigth: bold">' + name;
    else if (optiontype == 6 && isgroup == 1)
        return '<i class="fal fa-link" style="color: yellow"></i> <span style="font-size: 1.2rem; font-weigth: bold">' + name;
    else if (optiontype == 1)
        return '<i class="fas fa-check-square" style="opacity: 0.7"></i> ' + name;
    else if (optiontype == 2)
        return '<i class="fas fa-circle" style="opacity: 0.7"></i> ' + name;
    else if (optiontype == 3)
        return '<i class="fas fa-text" style="opacity: 0.7"></i> ' + name;
    else if (optiontype == 4)
        return '<i class="fas fa-ruler" style="opacity: 0.7"></i> ' + name;
    else if (optiontype == 7)
        return '<i class="fas fa-list-ul" style="opacity: 0.7"></i> ' + name;
    return name;
}

function formatGroupHeaderTemplate(group) {

    var grid = $("#gridChecklistOptions").data("kendoGrid"),
        ds = grid.dataSource;

    var groups = ds.view();
    var value = groups[group].items[0].name;

    return value;


}


function initDropDownChecklist()
{
    dropDownChecklist = $("#dropDownChecklist").kendoDropDownList({
        dataTextField: "text",
        dataValueField: "value",
        dataSource: window.global_checklist,
        filter: "contains",
        change: function() {
            $("#gridChecklistOptions").data("kendoGrid").dataSource.read();
        }
    }).data("kendoDropDownList");

    if (window.checklistSelected != "null") {
        dropDownChecklist.value(window.checklistSelected);
    }
}