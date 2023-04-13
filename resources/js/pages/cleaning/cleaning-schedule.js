$(document).ready(function() {
    var cs = new CleaningSchedule();
    cs.initGrid();
    fixKendoGridHeight();

    $("#export").click(function(e) {
        var grid = $("#gridCleaningSchedule").data("kendoGrid");
        grid.saveAsExcel();
    });

});


var CleaningSchedule = /** @class */ (function() {
    function CleaningSchedule() {}

    CleaningSchedule.prototype.initGrid = function() {
        window.grid = $("#gridCleaningSchedule").kendoGrid({
            excel: {
                fileName: "Whagons Cleaning Schedules.xlsx",

            },
            dataSource: {
                transport: {
                    read: {
                        url: "getCleaningSchedules",
                        type: "get",
                        dataType: "json"
                    },
                    create: {
                        url: "createCleaningSchedule",
                        type: "post",
                        dataType: "json",
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
                        }
                    },
                    update: {
                        url: "updateCleaningSchedule",
                        type: "post",
                        dataType: "json",
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
                        }
                    },
                    destroy: {
                        url: "api/deleteCleaningSchedule",
                        type: "delete",
                    },
                    parameterMap: function(options, type) {
                        if (type == "create" || type == "update") {
                            console.log(options.time);
                            if(options.time != null)
                            {
                                var d = new Date(options.time);
                                options.time = kendo.toString(new Date(d), "MM/dd/yyyy HH:mm");
                            }
                            else
                            {
                                options.time = null;
                            }
                        }
                        return options;
                    }
                },
                pageSize: 20,
                schema: {
                    model: {
                        id: "id",
                        fields: {
                            id: { type: "number", editable: false, nullable: true },
                            idspot: { editable: true, field: "idspot", type: "number", validation: { required: { message: "Lugar es requerido" } } },
                            dow: { field: "dow", validation: {
                                    required: true,
                                    dowvalidation: function (input) {
                                        
                                        if (input.is("[name='dow']") && input.val().length == 0) {
                                            input.attr("data-dow-msg", "Días es requerido");
                                            return /^[A-Z]/.test(input.val());
                                        }
                                    
                                        return true;
                                    }
                                }
                            },
                            iditem: { editable: true, field: "iditem", type: "number", nullable: false, validation: { required: { message: "Tipo de limpieza es requerido" } } },
                            time: { editable: true, field: "time", type: "date", format: "{HH:mm}", nullable: true, defaultValue: null },
                            iduser: { editable: true, field: 'iduser', type: "number", nullable: true },
                            //sequence: { editable: true, field: "sequence", type: "number", nullable: true },
                            enabled: { editable: true, field: "enabled", type: "boolean", defaultValue: true },
                        }
                    }
                },
            },

            editable: {
                mode: "popup"
            },
            toolbar: [{ template: kendo.template($("#toolbartemplate").html()) }],
            reorderable: true,
            resizable: true,
            sortable: true,
            pageable: {
                refresh: true,
                pageSizes: true,
                buttonCount: 5,
            },
            filterable: true,

            columns: [
                { command: { name: "edit", text: { edit: " " }, iconClass: "fad fa-pen commandIconOpacity" }, title: " ", width: "80px", media: "(min-width: 850px)" },
                {
                    field: "idspot",
                    title: locale("Spot"),
                    //template: "#=formatAssetName(name,description, icon,color,enabled)#",                    
                    width: "200px",
                    values: window.spots,
                    filterable: {
                        multi: true,
                        search: true
                    }
                },
                {
                    field: "dow",
                    title: locale("Day"),
                    width: "200px",
                    template: "#=formatDows(dow)#",
                    editor: editorMultiSelectDOW,
                    filterable: {
                        multi: true,
                        search: true
                    }
                },
                {
                    field: "time",
                    title: locale("Time"),
                    width: "100px",
                    filterable: false,
                    template: "#=formatTime(time)#",
                    format: "{0:HH:mm}",
                    editor: timeEditor


                },
                {
                    field: "iduser",
                    title: locale("User"),
                    values: global_users,
                    template: "#=formatUser(iduser)#",
                    editor: dropDownListEditor,
                    width: "300px",
                    media: "(min-width: 450px)"
                },
                /*
                {
                    field: "sequence",
                    title: locale("Sequence"),
                    width: "150px",
                    media: "(min-width: 450px)"
                },
                */
                {
                    field: "iditem",
                    title: locale("Task"),
                    width: "150px",
                    values: window.items,
                    media: "(min-width: 450px)"
                },

                { command: { name: "destroy", text: " ", iconClass: "fad fa-trash commandIconDelete" }, title: " ", width: "80px", media: "(min-width: 850px)" }
            ],
        }).data("kendoGrid");
    };

    return CleaningSchedule;
}());





formatUser = function formatUser(iduser) {

    let user = getUser(iduser);
    if (user == null) return '';
    let result = "<ul class='list-unstyled users-list m-0  d-flex align-items-center'>";


    result += "<li data-toggle='tooltip' data-popup='tooltip-custom' data-placement='top' data-original-title='" + user.text + "' class='avatar pull-up'>" +
        "<img class='media-object rounded-circle' src='" + user.urlpicture + "' alt='Avatar' height='30' width='30'>" +
        "</li>" + user.text;

    return result;
}

function formatTime(time) {

    if (time == null) return '';
    return moment(time).format('HH:mm');
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

function formatAssetName(name, description, icon, color, enabled) {
    let result = "";

    enabled == false ? result += '<div  style="display:inline-block; vertical-align: middle; margin-right: 5px; opacity: 0.5; text-decoration: line-through;" >' :
        result += "<div style='display:inline-block; vertical-align: middle; margin-right: 5px;'>"

    result += "<i class='" + icon + "' style='font-size: 1em; color:" + color + "'></i>   ";

    result += "<strong>" + name + "</strong>";
    result += "<br><small style='opacity: 0.6'>" + description + "</small>";
    result += "</div>";
    return result;
}