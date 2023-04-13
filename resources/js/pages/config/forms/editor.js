window.itemSelected = null;
window.columSelected = null;
window.optiontypesAllowedTable = [1, 2, 3, 4, 5, 8, 9, 10, 13];
var optionTypeSection = 6;

$(function() {

    initDropDownChecklist();
    initFormListView();
    initWindowProperties();

    $(".ui-draggable").kendoDraggable({
        hint: function(e) {
            return $(e).clone();
        },
    });

    $(document).on("click", ".btn-edit-column", function(event) {
        window.itemSelected = window.columSelected;
        refreshWindowProperties();
    });

    $(document).on("click", ".btn-delete-column", function(event) {
        window.itemSelected = window.columSelected;

        if (confirm("¿Eliminar ítem?")) {
            deleteFormItem({ id: window.itemSelected.id, idchecklist: window.itemSelected.idchecklist });
        }
    });

    $(document).on("dblclick", ".newItem2", function() {
        window.itemSelected = window.columSelected;
        refreshWindowProperties();
    });

    $(document).on("click", ".btn-edit", function(event) {
        refreshWindowProperties();
    });

    $(document).on("dblclick", ".newItem", function() {
        refreshWindowProperties();
    });

    $(document).on("click", ".btn-delete", function(event) {
        if (confirm("¿Eliminar ítem?")) {
            deleteFormItem({ id: window.itemSelected.id, idchecklist: window.itemSelected.idchecklist });
        }
    });

    $(document).on("click", "#btn-update-form", function(event) {

        var validator = $("#form-item").kendoValidator().data("kendoValidator");

        if (validator.validate()) {
            let data = $("#form-item").serializeFormJSON();

            if ($("#form-item").find(":checkbox[name='required']").length > 0) {
                data["required"] = $("#item-required").is(':checked') ? true : false;
            }

            if ($("#form-item").find(":checkbox[name='value']").length > 0) {
                data["value"] = $("#item-value").is(':checked') ? true : false;
            }

            if ($("#form-item").find(":checkbox[name='showinreport']").length > 0) {
                data["showinreport"] = $("#item-showinreport").is(':checked') ? 1 : 0;
            }

            data['id'] = window.itemSelected.id;

            updateFormItem(data);
        }
    });

});

function initTableListView(table, dataSource)
{
    $("#table-" + table).kendoListView({
        dataSource: dataSource,
        template: kendo.template($("#field-template-column").html()),
        scrollable: "endless",
        selectable: "single",
        height: "200px",
        change: function(e) {
            let table = e.sender.select().parent().attr('id');

            window.columSelected = $("#" + table).data("kendoListView").dataSource.getByUid(e.sender.select().data("uid"));
        },
        dataBound: function(e) {

            if (this.dataSource.data().length == 0) {
                $("#table-" + table).append("<div class='text-center alert alert-danger mt-1 alert-validation-msg' role='alert'><span>Arrastre columnas aquí</span> <i class='fal fa-hand-paper mr-1 align-middle'></i></div>");
            }

        }
    }).data("kendoListView");

    $("#table-" + table).kendoDropTargetArea({
        filter: ".newItem2",
        dragleave: resetStyling,
        drop: function(e) {

            let idparent = $(e.dropTarget).parent().data('id');

            let dropTarget = $("#table-" + idparent).data("kendoListView").dataSource.getByUid($(e.dropTarget).data('uid'));
    
            // Apply the changes to the data after an item is dropped.
            if (!e.draggable.currentTarget.hasClass("ui-draggable")) return false;

            var draggableElement = e.draggable.currentTarget;

            var dataItem = dataElements.getByUid(draggableElement.data("uid"));

            if(!optiontypesAllowedTable.includes(dataItem.optiontype))
            {
                toastr.warning("Tipo de columna no permitido");
                return false;
            }

             // Find the corresponding dataItem by uid.
            addFormItem({ idchecklist: dropDownChecklist.value(), optiontype: dataItem.optiontype, name: dataItem.name, icon: dataItem.icon, position: dropTarget.position, idparent: idparent});
            resetStyling.call(this); // Reset the visual dropTarget indication that was added on dragenter.

        }
    });

    $("#table-" + table).kendoDropTarget({
        dragleave: resetStyling,
        drop: function(e) {

            let idparent = $(e.dropTarget).data("id");

            // Apply the changes to the data after an item is dropped.
            if (!e.draggable.currentTarget.hasClass("ui-draggable")) return false;

            var draggableElement = e.draggable.currentTarget;

            var dataItem = dataElements.getByUid(draggableElement.data("uid")); // Find the corresponding dataItem by uid.

            if(!optiontypesAllowedTable.includes(dataItem.optiontype))
            {
                toastr.warning("Tipo de columna no permitido");
                return false;
            }

            addFormItem({ idchecklist: dropDownChecklist.value(), optiontype: dataItem.optiontype, name: dataItem.name, icon: dataItem.icon, position: 0, idparent: idparent});
            resetStyling.call(this); // Reset the visual dropTarget indication that was added on dragenter.
            
        }
    });

    
    $("#table-" + table).kendoSortable({
        autoScroll: true,
        filter: "> div.newItem2",
        cursor: "move",
        ignore: "div.newItem",
        placeholder: function(element) {
            return element.clone().css("opacity", 0.1);
        },
        hint: function(element) {
            return element.clone().removeClass("k-state-selected");
        },
        start: function(e) {
            console.log("column", e, e.draggableEvent.currentTarget);
        },
        change: function(e) {

            let idparent = e.item.data("idparent");

            let dataTable = $("#table-" + idparent).data("kendoListView").dataSource;
            
            var skip = 0,
                oldIndex = e.oldIndex + 0,
                newIndex = e.newIndex + 0;
            let data = dataTable.data();
            let dataItem = dataTable.getByUid(e.item.data("uid"));
            dataTable.remove(dataItem);
            dataTable.insert(newIndex, dataItem);
            sortFormItems(dataTable.data(), true);
        }



    });
}

function refreshWindowProperties() {
    windowProperties.refresh({
        url: "getFormProperties",
        data: { id: window.itemSelected.id },
    });
    windowProperties.center().open();
}

function getCurrentValue() {
    if (window.itemSelected.properties == null) return "";
    let obj = JSON.parse(window.itemSelected.properties);
    return (obj.hasOwnProperty('value') && obj.value != null) ? obj.value : "";
}

function getCurrentProperties() {
    if (window.itemSelected.properties == null) return "";
    return JSON.parse(window.itemSelected.properties);
}

function getIcon(optiontype) {
    let result = listA.dataSource.data().find(o => o.optiontype === optiontype);
    return result.icon;
}

function hasHeaderClass(optiontype) {
    return optiontype == 6 ? "header" : "";
}

function initFormListView() {
    dataForm = new kendo.data.DataSource({
        transport: {
            read: {
                url: "getFormDetails",
                type: "GET",
                dataType: "JSON",
                data: function() {
                    return {
                        idchecklist: dropDownChecklist.value()
                    };
                }
            },
        },
        schema: {
            model: {
                id: "id",
                fields: {
                    id: { type: "number" },
                    optiontype: { type: "number" },
                    name: { type: "string" },
                    icon: { type: "string" }
                }
            }
        },
        requestEnd: function(e) {

            setTimeout(() => {

                let tables = e.response.filter(item => item.optiontype == 16);

                tables.forEach(function (table) {

                    let data = table.children;

                    initTableListView(table.id, data);

                });
                
            }, 200);

         },
    });

    listB = $("#listB").kendoListView({
        dataSource: dataForm,
        template: kendo.template($("#field-template").html()),
        scrollable: "endless",
        selectable: "single",
        change: function(e) {
            window.itemSelected = dataForm.getByUid(e.sender.select().data("uid"));
        },
        dataBound: function(e) {
            if (this.dataSource.data().length == 0) {
                $("#listB").append("<div class='text-center alert alert-danger mt-1 alert-validation-msg' role='alert'><span>Arrastre elementos</span> <i class='fal fa-hand-paper mr-1 align-middle'></i></div>");
            }

            $("#total-items").text("Total ítems " + this.dataSource.data().length);
        }
    }).data("kendoListView");
}

var dataElements = new kendo.data.DataSource({
    data: [
        { optiontype: 1, name: "Checkbox", icon: "fa fa-check-square" },
        { optiontype: 2, name: "Radio Button", icon: "fad fa-circle" },
        { optiontype: 3, name: "Campo de texto", icon: "fa fa-align-left" },
        { optiontype: 4, name: "Campo numérico", icon: "fa fa-list-ol" },
        { optiontype: 5, name: "Lista", icon: "fa fa-bars" },
        { optiontype: 6, name: "Sección", icon: "fa fa-heading" },
        //{ optiontype: 7,  name: "Campo de entrada", icon: "fa fa-font" },
        { optiontype: 8, name: "Texto fijo", icon: "fa fa-paragraph" },
        { optiontype: 9, name: "Fecha", icon: "fa fa-calendar-day" },
        { optiontype: 10, name: "Hora", icon: "fa fa-clock" },
        { optiontype: 11, name: "Imagen fija", icon: "fa fa-image" },
        { optiontype: 12, name: "Firma", icon: "fa fa-signature" },
        { optiontype: 13, name: "Foto", icon: "fa fa-camera" },
        { optiontype: 14, name: "Control de tiempo", icon: "fa fa-play" },
        //{ optiontype: 15, name: "Arqueo de caja", icon: "fa fa-dollar" },
        { optiontype: 16, name: "Tabla", icon: "fa fa-table" },

    ],
    schema: {
        model: {
            id: "id",
            fields: {
                id: { type: "number" },
                optiontype: { type: "number" },
                name: { type: "string" }
            }
        }
    }
});

listA = $("#listA").kendoListView({
    dataSource: dataElements,
    template: kendo.template($("#template").html())
}).data("kendoListView");

$("#listA").kendoDraggable({
    filter: ".item",
    hint: function(element) {
        return element.clone().css({
            "opacity": 0.6,
            "background-color": "#0cf"
        });
    }
});

$("#listB").kendoDropTargetArea({
    filter: ".newItem",
    dragleave: resetStyling,
    drop: function(e) {

        let dropTarget = dataForm.getByUid($(e.dropTarget).data('uid'));

        // Apply the changes to the data after an item is dropped.
        if (!e.draggable.currentTarget.hasClass("ui-draggable")) return false;
        var draggableElement = e.draggable.currentTarget;
        var dataItem = dataElements.getByUid(draggableElement.data("uid")); // Find the corresponding dataItem by uid.
        addFormItem({ idchecklist: dropDownChecklist.value(), optiontype: dataItem.optiontype, name: dataItem.name, icon: dataItem.icon, position: dropTarget.position, idparent: null});
        resetStyling.call(this); // Reset the visual dropTarget indication that was added on dragenter.
    }
});

$("#listB").kendoDropTarget({
    dragleave: resetStyling,
    drop: function(e) {
        // Apply the changes to the data after an item is dropped.
        if (!e.draggable.currentTarget.hasClass("ui-draggable")) return false;
        var draggableElement = e.draggable.currentTarget;
        var dataItem = dataElements.getByUid(draggableElement.data("uid")); // Find the corresponding dataItem by uid.
        addFormItem({ idchecklist: dropDownChecklist.value(), optiontype: dataItem.optiontype, name: dataItem.name, icon: dataItem.icon, position: 0, idparent: null });
        resetStyling.call(this); // Reset the visual dropTarget indication that was added on dragenter.
    }
});


$("#listB").kendoSortable({
    autoScroll: true,
    filter: "> div.newItem",
    cursor: "move",
    ignore: "div.newItem2, div.column-name, div.column-name > .column-name, div.column-icon > .fa-stack, .column-icon",
    start: function(e) {
        console.log("listB", e, e.draggableEvent.currentTarget);
    },
    placeholder: function(element) {
        return element.clone().css("opacity", 0.1);
    },
    hint: function(element) {
        return element.clone();
        
    },
    change: function(e) {
        var skip = 0,
            oldIndex = e.oldIndex + 0,
            newIndex = e.newIndex + 0;
        let data = dataForm.data();
        let dataItem = dataForm.getByUid(e.item.data("uid"));
        dataForm.remove(dataItem);
        dataForm.insert(newIndex, dataItem);
        sortFormItems(listB.dataSource.data());
    }
});

function addFormItem(obj) {

    $.blockUI({ message: '<h1>Guardando...</h1>' });

    obj.isgroup = (obj.optiontype == optionTypeSection) ? true : false;

    let request = callAjax('addFormItem', 'POST', obj, false);

    request.done(function(result) {

        listB.dataSource.read();
        window.itemSelected = result.model;
        setTimeout(() => { refreshWindowProperties(); }, 200);

        $.unblockUI();

    }).fail(function(jqXHR, status) {
        $.unblockUI();
    });
}

function updateFormItem(obj) {
    windowProperties.close();
    $.blockUI({ message: '<h1>Guardando...</h1>' });

    let request = callAjax('updateFormItem', 'POST', obj, false);

    request.done(function(result) {

        $("#modal-properties").modal("hide");
        listB.dataSource.read();
        $.unblockUI();

    }).fail(function(jqXHR, status) {
        $.unblockUI();
    });
}

function sortFormItems(data, children = false) {
    $.blockUI({ message: '<h1>Ordenando...</h1>' });

    let request = callAjax('sortFormItems', 'POST', { "data": JSON.stringify(data), "children" : children}, false);

    request.done(function(result) {
        listB.dataSource.read();
        $.unblockUI();

    }).fail(function(jqXHR, status) {
        $.unblockUI();
    });
}

function deleteFormItem(obj) {
    $.blockUI({ message: '<h1>Borrando...</h1>' });

    let request = callAjax('deleteFormItem', 'POST', obj, false);

    request.done(function(result) {

        listB.dataSource.read();
        $.unblockUI();

    }).fail(function(jqXHR, status) {
        $.unblockUI();
    });
}

function addStyling(e) {
    this.element.css({
        "border-color": "#06c",
        "background-color": "#e0e0e0",
        "opacity": 0.6
    });
}

function resetStyling(e) {
    this.element.css({
        "border-color": "black",
        "background-color": "transparent",
        "opacity": 1
    });
}

function initWindowProperties() {
    windowProperties = $("#window-properties").kendoWindow({
        width: "450px",
        height: "500px",
        modal: true,
        title: "Configuración avanzada",
        resizable: true,
        scrollable: false,
        visible: false,
        refresh: function() {

            switch (parseInt(window.itemSelected.optiontype)) {
                case 2:
                    initDropDownListData();
                    initDropDownListValue();
                    dropDownListData.value(window.itemSelected.iddata);
                    dropDownListValue.value(getCurrentValue());
                    break;

                case 5:
                    initDropDownListData();
                    initDropDownListValue();
                    dropDownListData.value(window.itemSelected.iddata);
                    dropDownListValue.value(getCurrentValue());
                    break;

                case 11:
                    let data = renameKey(getCurrentProperties(), 'value', 'name');
                    let initialFiles = getCurrentValue() != "" ? [data] : [];
                    initKendoUpload(initialFiles);

                case 15:

                default:
                    break;
            }
        }
    }).data("kendoWindow");
}

function initDropDownChecklist() {
    dropDownChecklist = $("#dropDownChecklist").kendoDropDownList({
        dataTextField: "text",
        dataValueField: "value",
        dataSource: window.global_forms,
        filter: "contains",
        height: 400,
        change: function() {
            listB.dataSource.read();
        }
    }).data("kendoDropDownList");

    if (window.formSelected != "null") {
        dropDownChecklist.value(window.formSelected.id);
    }
}

function initDropDownListData() {
    dropDownListData = $("#dropDownListData").kendoDropDownList({
        dataTextField: "text",
        dataValueField: "value",
        dataSource: window.global_checklist_data,
        change: changeData,
    }).data("kendoDropDownList");
}

function initDropDownListValue() {
    dropDownListValue = $("#dropDownListValue").kendoDropDownList({
        dataTextField: "text",
        dataValueField: "value",
        dataSource: getDataItems(),
        height: 400
    }).data("kendoDropDownList");
}

function changeData() {
    window.itemSelected.iddata = parseInt(dropDownListData.value());
    dropDownListValue.setDataSource(getDataItems());
}

function getDataItems() {
    if (window.itemSelected.iddata == null) return [];

    let data = window.global_checklist_data.find(o => o.value === window.itemSelected.iddata);

    let jsonData = JSON.parse(data.items);

    jsonData.unshift({ "text": "", "value": 'null' });

    return jsonData;
}

function initKendoUpload(initialFiles) {
    var template = kendo.template($("#templateImage").html());

    if (initialFiles.length > 0) {
        $("#products").html(kendo.render(template, initialFiles));
    }

    $("#files").kendoUpload({
        async: {
            saveUrl: "api/saveFormImage",
            removeUrl: "api/removeFormImage",
            autoUpload: true
        },
        upload: function(e) {
            e.data = { id: itemSelected.id };
        },
        remove: function(e) {
            e.data = { id: itemSelected.id };
        },
        validation: {
            allowedExtensions: [".jpg", ".jpeg", ".png", ".bmp", ".gif"]
        },
        showFileList: true,
        files: initialFiles,
        dropZone: ".dropZoneElement",
        success: onSuccess,
        select: onSelect
    });

    function onSelect(e) {
        if (e.files.length > 1) {
            alert("Please select max 1 files.");
            e.preventDefault();
        }
    }

    function onSuccess(e) {

        if (e.operation == "upload") {
            $("#products").empty();

            for (var i = 0; i < e.files.length; i++) {
                var file = e.files[i].rawFile;

                if (file) {
                    var reader = new FileReader();

                    reader.onloadend = function() {
                        $("<div class='product'><img src=" + this.result + " /></div>").appendTo($("#products"));
                    };

                    reader.readAsDataURL(file);
                }
            }
        }

        if (e.operation == "remove") {
            $("#products").empty();
        }

        listB.dataSource.read();
    }
}

const renameKey = (object, key, newKey) => {

    const clonedObj = clone(object);

    const targetKey = clonedObj[key];



    delete clonedObj[key];

    clonedObj[newKey] = targetKey;

    return clonedObj;

};

const clone = (obj) => Object.assign({}, obj);