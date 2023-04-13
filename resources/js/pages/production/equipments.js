$(document).ready(function() {
    var s = new Equipment();
    s.initGrid();
    fixKendoGridHeight();

    $("#export").click(function(e) {
        var grid = $("#gridEquipments").data("kendoGrid");
        grid.saveAsExcel();
    });
});


var Equipment = /** @class */ (function() {
    function Equipment() {}
    Equipment.prototype.initGrid = function() {
        $("#gridEquipments").kendoGrid({
            excel: {
                fileName: "Whagons Equipments.xlsx",
            },
            dataSource: {
                transport: {
                    read: {
                        url: "getEquipments",
                        type: "get",
                        dataType: "json"
                    },
                    create: {
                        url: "api/createEquipment",
                        type: "post",
                        dataType: "json",
                    },
                    update: {
                        url: "api/updateEquipment",
                        type: "post",
                        dataType: "json"
                    },
                    destroy: {
                        url: "api/deleteEquipment",
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
                            description: { editable: true, field: "description", type: "string" },
                            idtype: { editable: true, field: "idtype", type: "number", defaultValue: 1, validation: { required: { message: "Tipo es requerido" } } },
                            idproductcategory: { editable: true, field: "idproductcategory", type: "number", nullable: true },
                            idstatus: { editable: false, field: "idstatus", type: "number", defaultValue: 3 },
                            warmup_duration: { type: "number", field: "warmup_duration", editable: true, nullable: false, defaultValue: 0 },
                            cleaning_duration: { type: "number", field: "cleaning_duration", editable: true, nullable: false, defaultValue: 0 },

                            enabled: { editable: true, field: "enabled", type: "boolean", defaultValue: true }
                        }
                    }
                },
            },

            editable: {
                mode: "popup"
            },
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
            toolbar: [{ template: kendo.template($("#toolbartemplate").html()) }],
            columns: [
                { command: { name: "edit", text: { edit: " " }, iconClass: "fad fa-pen commandIconOpacity" }, title: " ", width: "80px", media: "(min-width: 850px)" },
                {
                    field: "name",
                    title: locale("Name"),
                    template: '#=formatName(name, enabled)#',
                    width: "100px",
                    groupable: false,
                    media: "(min-width: 350px)",
                    filterable: {
                        multi: true,
                        search: true
                    }
                },
                {
                    field: "description",
                    title: locale("Description"),
                    hidden: true,
                    editor: textAreaEditor,
                    width: "200px",
                    media: "(min-width: 850px)",
                    filterable: false,
                },

                {
                    field: "idtype",
                    title: locale("Type"),
                    width: "100px",
                    values: window.types,
                    media: "(min-width: 450px)",
                    filterable: {
                        multi: true,
                        search: true
                    }
                },
                {
                    field: "idproductcategory",
                    title: locale("Product Category"),
                    width: "100px",
                    editor: dropDownListEditor,
                    values: window.productcategories,
                    media: "(min-width: 450px)",
                    filterable: {
                        multi: true,
                        search: true
                    }
                },

                {
                    field: "velocity",
                    title: locale("Velocity"),
                    template: "#=formatVelocity(velocity)#",
                    width: "80px",
                    media: "(min-width: 450px)",
                    filterable: false,
                },
                /*  {
                      field: "warmup_duration",
                      title: locale("Warmup"),
                      template: "#=formatDuration(warmup_duration)#",
                      width: "80px",
                      media: "(min-width: 450px)",
                      filterable: false,
                  },
                  {
                      field: "cleaning_duration",
                      title: locale("Cleanup"),
                      template: "#=formatDuration(cleaning_duration)#",
                      width: "80px",
                      media: "(min-width: 450px)",
                      filterable: false
                  },
                  */

                {
                    field: "enabled",
                    title: locale("Enabled"),
                    template: "#=formatYesNo(enabled)#",
                    hidden: true,
                    media: "(min-width: 850px)"

                },
                { command: { name: "destroy", text: " ", iconClass: "fad fa-trash commandIconDelete" }, title: " ", width: "80px", media: "(min-width: 850px)" }

            ],
        }).data("kendoGrid");

    };

    return Equipment;
}());


function formatName(name, enabled) {
    let result = "";
    if (enabled == 1)
        result += '<span>' + name + '</span>';
    else
        result += "<span style='opacity: 0.3'>" + name + '</span>';
    return result;
}

function formatDuration(duration) {
    let format = formatSLA(duration);
    return '<i class="fad fa-clock text-light"></i> ' + format;
}

function formatVelocity(duration) {
    //let format = formatSLA(duration);
    return '<i class="fad fa-tachometer-fast text-light"></i> ' + duration;
}

/*
function teamDropDownEditor(container, options) {
    $('<input data-text-field="text" data-value-field="value" data-bind="value:' + options.field + '"/>')
        .appendTo(container)
        .kendoDropDownList({
            autoBind: false,
            optionLabel: " ",
            valuePrimitive: true,
            dataSource: {
                data: global_teams
            }
        });
}
*/


function formatProjectStatus(idstatus) {
    let s = global_statuses.find(o => o.value === idstatus);
    if (typeof s == "undefined") return "?";
    return "<span style='background-color:" + s.color + " ' class='badge badge-pill badge-success'>" + s.text + "</span>";


}


function formatProjectName(name, description, code, isprivate) {
    let result = "";
    description = description == null ? '' : description;

    result += "<div style='display:inline-block; vertical-align: middle'>";
    if (isprivate == 1)
        result += "<i class='fa fa-lock text-primary'></i> ";
    result += "<strong>" + name + "</strong>";
    result += "<br><small style='opacity: 0.6'>" + description + "</small>";

    result += "</div>";
    return result;
}