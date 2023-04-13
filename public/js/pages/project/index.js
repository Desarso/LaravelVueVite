$(function() {
    var tt = new Project();
    tt.initGrid();
    fixKendoGridHeight();

    $("#export").on('click', function(e) {
        var grid = $("#gridProjects").data("kendoGrid");
        grid.saveAsExcel();
    });



});


var Project = /** @class */ (function() {
    function Project() {}

    Project.prototype.initGrid = function() {
        window.grid = $("#gridProjects").kendoGrid({
            excel: {
                fileName: "Whagons Projects.xlsx",

            },
            dataSource: {
                transport: {
                    read: {
                        url: "getAllProjects",
                        type: "get",
                        dataType: "json"
                    },
                    create: {
                        url: "api/createProject",
                        type: "post",
                        dataType: "json",
                    },
                    update: {
                        url: "api/updateProject",
                        type: "post",
                        dataType: "json"
                    },
                    destroy: {
                        url: "api/deleteProject",
                        type: "delete",
                    }
                },
                pageSize: 20,
                schema: {
                    model: {
                        id: "id",
                        fields: {
                            id: { type: "number", editable: false, nullable: true },
                            name: { editable: true, field: "name", type: "string", validation: { required: true } },
                            description: { editable: true, field: "description", type: "string" },
                            idstatus: { editable: true, field: "idstatus", type: "number", defaultValue: 1, validation: { required: { message: "Estado es requerido" } } },
                            progress: { type: "number", field: "progress", editable: false, nullable: false, defaultValue: 0 },
                            start: { editable: true, field: "start", type: "date", format: "{dd/MM/yyyy}", defaultValue: null },
                            end: { editable: true, field: "end", type: "date", format: "{dd/MM/yyyy}", defaultValue: null },
                            archived: { editable: true, field: "archived", type: "boolean" },
                            users: { field: "users" }

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
                { command: { name: "edit", text: { edit: " " }, iconClass: "fad fa-pen commandIconOpacity" }, title: " ", width: "55px", media: "(min-width: 850px)" },
                // { command: { name: "chkoptions", text: "", click: openGantt, iconClass: "fad fa-folder-open" }, title: " ", width: "55px" },


                {
                    field: "idstatus",
                    title: locale("Status"),
                    width: "100px",
                    values: window.statuses,
                    template: "#=formatStatus(idstatus)#",
                    media: "(min-width: 450px)",
                    hidden: true,
                    filterable: {
                        multi: true,
                        search: true
                    }
                },

                {
                    field: "name",
                    title: locale("Project Name"),
                    width: "300px",
                    template: "#=formatProjectName(name, description, idstatus, id)#",
                    media: "(min-width: 350px)",
                    filterable: {
                        multi: true,
                        search: true
                    }
                },
                {
                    field: "users",
                    title: locale("Owners"),
                    width: "200px",
                    values: global_users, // para que aparezca en el filtro
                    editor: editorMultiSelectUser,
                    filterable: false,
                    template: "#=formatUsers(users)#",
                    filterable: {
                        multi: true,
                        search: true
                    }

                },
                {
                    field: "description",
                    title: locale("Description"),
                    editor: textAreaEditor,
                    width: "300px",
                    hidden: true,
                    media: "(min-width: 450px)",
                    filterable: false
                },
                {
                    field: "start",
                    title: locale("Deadline"),
                    width: "150px",
                    //hidden: true,
                    media: "(min-width: 450px)"
                },
                {
                    field: "progress",
                    title: locale("Progress"),
                    width: "200px",
                    template: "#=formatProgress(progress, idstatus)#",
                    media: "(min-width: 450px)"
                },


                {
                    field: "arvhived",
                    title: "Archivar",
                    hidden: true,
                    editor: checkBoxEditor,
                    media: "(min-width: 450px)"
                },

                { command: { name: "destroy", text: " ", iconClass: "fad fa-trash commandIconDelete" }, title: " ", width: "80px", media: "(min-width: 850px)" },
            ],
        }).data("kendoGrid");
    };

    return Project;
}());


function openGantt(id) {

    let url = "gantt?id=" + id;
    document.location.href = url;
}


function formatStatus(idstatus) {
    let result = window.statuses.find(s => s.value === idstatus);
    if (typeof result == 'undefined') return "";
    if (idstatus == 2)
        return "<i class='fas fa-cog fa-spin' style='opacity: 1;  color: " + result.color + "' /></i> " +
            "<span style='color:grey'>" + result.text + "</span>";
    else if (idstatus == 3)
        return "<i class='fa fa-pause-circle' style='opacity: 1; color: " + result.color + "' /></i> " +
            "<span style='color:grey'>" + result.text + "</span>";
    else
        return "<i class='fa fa-circle' style='opacity: 1;  color: " + result.color + "' /></i> " +
            "<span style='color:grey'>" + result.text + "</span>";
}

function formatProgress(progress, idstatus) {

    let result = "";
    progress = progress * 100;

    result += '<div title="' + progress + '%" class="progress progress-sm mt-1 mb-0 box-shadow-2">';
    if (progress == 0)
        result += '<div class="progress-bar bg-gradient-x-' + idstatus + '" role="progressbar" style="width:' + progress + '%" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100"></div>';
    else
        result += '<div class="progress-bar bg-gradient-x-' + idstatus + '" role="progressbar" style="width:' + progress + '%" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100">' + progress + '%</div>';
    result += '</div>';
    return result;
}




function formatTeam(idteam) {
    let team = global_teams.find(o => o.value === idteam);
    if (typeof team == "undefined") return "N/A";
    return "<i class='fas fa-hard-hat' style='color:" + team.color + "'></i> " + "<span>" + team.text + "</span>";
}


function formatProjectName(name, description, idstatus, id) {
    let result = "";
    description = description == null ? '' : description;
    _status = window.statuses.find(s => s.value === idstatus);


    if (idstatus == 2)
        result += "<i class='fas fa-cog fa-spin' style='opacity: 1;  color: " + _status.color + "' /></i> ";
    else if (idstatus == 3)
        result += "<i class='fa fa-pause-circle' style='opacity: 1; color: " + _status.color + "' /></i> ";
    else
        result += "<i class='fa fa-circle' style='opacity: 1;  color: " + _status.color + "' /></i> ";



    result += "<div style='display:inline-block; vertical-align: middle; margin-right: 5px;'>"

    result += "</div>";
    result += "<div style='display:inline-block; vertical-align: middle'>";
    result += '<a href="' + 'gantt?id=' + id + '' + '" class="project" style="color: #0068F7;">' + name + '</a>';
    result += "<br><small style='opacity: 0.6'>" + description + "</small>";
    result += "</div>";
    return result;
}

function formatUsers(users) {
    if (users == null) return "";

    let result = "<ul  class='list-unstyled users-list m-0  d-flex align-items-center'>";
    console.log(users);
    console.log(users.length);

    for (var i = 0; i < users.length; i++) {
        let user = getUser(users[i].value);
        console.log(user);

        if (user != undefined) {
            if (i == 4) break;

            result += "<li data-toggle='tooltip' data-popup='tooltip-custom' data-placement='bottom' data-original-title='" + user.text + " ' class='avatar pull-up'>" +
                "<img title='" + user.text + "'  class='media-object rounded-circle' src='" + user.urlpicture + "' alt='Avatar' height='30' width='30'>" +
                "</li>";
        }
    }

    let span_more = users.length > 4 ? "<li class='d-inline-block pl-50'><span>+" + (users.length - 4) + " más</span></li>" : "";
    return result + span_more;
}