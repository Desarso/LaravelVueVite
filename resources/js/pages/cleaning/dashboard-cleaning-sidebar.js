function initSideBar() {


    cleaningTasksGrid = new CleaningTasksGrid();
    cleaningTasksGrid.init();

    $('#sidebarCleaningDetail').css('display', 'block');

    // Click event to hide Sidebar via close or cancel
    $('.hide-data-sidebar, .cancel-data-btn, .overlay-bg').on("click", function() {
        hideCleaningSidebar();
    });

    // Click event to handle Sidebar Accept button
    $('#btnAccept').on("click", function() {
        saveSidebarChanges();
        $("#listView").data("kendoListView").dataSource.read();
        hideCleaningSidebar();
    });

    $("#hasCleaningPlan").click(function() {
        console.log(this.checked);
    });

    initCleaningStatusesDropDown();
}


//////////////////////////
/// Tareas de Limpieza de un SPOT
/// 

var CleaningTasksGrid = /** @class */ (function() {
    function CleaningTasksGrid() {}
    CleaningTasksGrid.prototype.init = function() {
        $("#cleaningTasksGrid").kendoGrid({
            dataSource: {
                transport: {
                    read: {
                        url: "getSpotCleaningPlan",
                        type: "get",
                        dataType: "json",
                    },
                    create: {
                        url: "api/createProductionDetail",
                        type: "post",
                        dataType: "json",
                    },
                    update: {
                        url: "api/updateProductionDetail",
                        type: "post",
                        dataType: "json"
                    },
                    destroy: {
                        url: "api/deleteProductionDetail",
                        type: "delete",
                    }
                },
                pageSize: 20,
                schema: {
                    model: {
                        id: "id",
                        fields: {
                            id: { type: "number", editable: false, nullable: true },
                            iditem: { type: "number", field: "iditem", editable: true, nullable: false },
                            iduser: { type: "number", field: "iduser", editable: true, nullable: true },
                            cleanat: { type: "time", field: "cleanat", editable: true, nullable: true },

                        }
                    }
                },

            },
            edit: function(e) {
                if (e.model.isNew()) {
                    var today = new Date();
                } //else {

                //}
            },
            dataBinding: function(e) {
                if (e.action == 'add') {
                    /*  e.items[0].idproduction = getSelectedProduction();
                      e.items[0].idoperator = getSelectedOperator();
                      */
                }

            },


            editable: {
                mode: "popup"
            },
            toolbar: [{ template: kendo.template($("#toolbartemplate").html()) }],
            height: "200px",
            sortable: true,
            reorderable: true,
            resizable: true,
            navigatable: true,
            pageable: false,
            noRecords: true,
            messages: {
                noRecords: "Sin Limpiezas para hoy!"
            },

            columns: [
                { command: { name: "edit", text: { edit: " " }, iconClass: "fad fa-pen commandIconOpacity" }, title: " ", width: "30px", media: "(min-width: 850px)" },
                {
                    field: "iditem",
                    title: locale('Task'),
                    values: window.cleaningtasktypes,
                    //template: "#=formatCleaningPlan(iditem)#",
                    width: "100px",
                    media: "(min-width: 850px)"
                },
                {
                    field: "iduser",
                    title: locale("User"),
                    values: global_users,
                    width: "200px",
                    media: "(min-width: 850px)"
                },
                {
                    field: "time",
                    title: locale("Time"),
                    // template: "#=formatTime(time)#",
                    width: "150px"
                },



                { command: { name: "destroy", text: " ", iconClass: "fad fa-trash commandIconDelete" }, title: " ", width: "30px", media: "(min-width: 850px)" }

            ],
        }).data("kendoGrid");

    };

    CleaningTasksGrid.prototype.setSpotID = function(idspot) {
        $("#cleaningTasksGrid").data("kendoGrid").dataSource.transport.options.read.data = function() { return { idspot: idspot } };
        $("#cleaningTasksGrid").data("kendoGrid").dataSource.read();
    };


    return CleaningTasksGrid;

}());



///////////////////////////////////////////////////////////////////

function getCleaningPlanTicket(idticket) {
    if (idticket == null) return null;
    let data = $("#cleaningTasksGrid").data("kendoGrid").dataSource.data();
    for (var i = 0; i < data.length; i++) {
        if (data[0].id == idticket) return data[0].cleaningticket;
    }
    return null;
}


function formatCleaningPlan(iditem) {

    var result = "";

    return "<span style='font-size: 14px'>" + result + "</span>";

    /*let result = "";
    description = description == null ? '' : description;

    result += "<div style='display:inline-block; vertical-align: middle'>";
    if (isprivate == 1)
        result += "<i class='fa fa-lock text-primary'></i> ";
    result += "<strong>" + name + "</strong>";
    result += "<br><small style='opacity: 0.6'>" + description + "</small>";

    result += "</div>";
    return result;
    */
}


function showCleaningDetail() {
    let spot = window.selectedSpot;
    cleaningTasksGrid.setSpotID(spot.id);
    $('#spotName').html(spot.name);
    $('#sidebarTitle').removeClass('cleaningstatus1 cleaningstatus2 cleaningstatus3 cleaningstatus4 cleaningstatus5');
    $('#sidebarTitle').addClass('cleaningstatus' + spot.idcleaningstatus);

    $('#personInChargeFullName').html(getFullName(spot));
    $('#personInChargePicture').css('background-image', 'url("' + getNextCleaningAvatarURL(spot.cleaning_plans) + '")')
        .css('background-size', 'cover')
        .css('background-repeat', 'no-repeat');

    // Turn On/FF switch    

    $("#cleaningStatusDropDown").data("kendoDropDownList").value(spot.idcleaningstatus);
    $('#donotdisturb').prop('checked', spot.dnd == 1);
    $('#isrush').prop('checked', spot.isrush == 1);

    showCleaningSidebar();

}



function getFullName(spot) {
    if (spot.cleaning_pna)
        return (spot.firstname == null ? '' : spot.firstname) + ' ' + (spot.lastname == null ? '' : spot.lastname);
}




// Save Production/Operator of current Equipment
function saveSidebarChanges() {

    let data = {
        idspot: window.selectedSpot.id,
        idcleaningstatus: getSelectedCleaningStatus(),
        isrush: $('#isrush').prop('checked'),
        dnd: $('#donotdisturb').prop('checked')
    };

    let request = callAjax('api/updateSpotCleaningInfo', 'POST', data, true);
    request.done(function(result) {
        console.log('success updateProductionData');
    }).fail(function(jqXHR, status) {
        console.log('fail udateProductionData');
        //TODO: show error
    });

}





function showCleaningSidebar() {
    $(".add-new-data").addClass("show");
    $(".overlay-bg").addClass("show");
}

function hideCleaningSidebar() {
    $(".add-new-data").removeClass("show");
    $(".overlay-bg").removeClass("show");
}






function initCleaningStatusesDropDown() {
    cleaningStatusesDropDownList = $("#cleaningStatusDropDown").kendoDropDownList({
        optionLabel: "",
        dataTextField: "text",
        dataValueField: "value",
        dataSource: window.cleaningstatuses,
        autoClose: true,
        // change: operatorChanged,
        // height: 310,
        // template: '<span class="selected-value" style="background-image: url(\'#:data.urlpicture#\')"></span><span>#:data.text#</span>',
        // valueTemplate: '<span class="selected-value" style="background-image: url(\'#:data.urlpicture#\')"></span><span>#:data.text#</span>',
        //footerTemplate: 'Total number of <strong>#: instance.dataSource.total() #</strong> operators found',
    }).data("kendoDropDownList");
}


function getSelectedCleaningStatus() {
    let value = $("#cleaningStatusDropDown").data('kendoDropDownList').value();
    if (value == "") return null;
    return value;
}