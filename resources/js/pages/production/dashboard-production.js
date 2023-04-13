window.lastUpdated = null;

$(document).ready(function() {

    setInterval(function() {
        getLastProduction();
    }, 10000);

    // Initialize Production Dashboard 
    var d = new ProductionDashboard();
    d.initListView();

    $('#sidebarProductionDetail').css('display', 'block');

    // Click event to hide Sidebar via close or cancel
    $('.hide-data-sidebar, .cancel-data-btn, .overlay-bg').on("click", function() {
        hideProductionSidebar();
    })

    // Click event to handle Sidebar Accept button
    $('#btnAccept').on("click", function() {
        saveSidebarChanges();
        $("#listView").data("kendoListView").dataSource.read();
        hideProductionSidebar();
    })

    // Click event handler for turning ON/OFF the Equipment
    $('#powerOnEquipment').on('click', function(e) {
        if ($(this).prop("checked") == true) {
            if (!hasProduction()) return false;
            if (!hasOperator()) return false;
            startProduction().then(function(result) {
                //exito
                //deshabilitar poder cambiar la producción mientras máquina encendida
                $("#equipmentProductions").data('kendoDropDownList').enable(false);
                //hideProductionSidebar();
            }, function(err) {
                // error o se canceló...volver checked a estado original
                $('#powerOnEquipment').prop("checked", false)
            });

        } else { // apagar
            finishProduction().then(function(result) {
                // exito
                $("#equipmentProductions").data('kendoDropDownList').enable(true);
                //hideProductionSidebar();
            }, function(err) {
                // error o se canceló...volver checked a estado original
                $('#powerOnEquipment').prop("checked", true)
            });
        }
    });

    // CHARTS
    initializeCharts();

    initOperatorDropDown();
    initSchedulesDropDown();
    initProductionsDropDown();


});

function hasProduction() {
    if (getSelectedProduction() == null) {
        Swal.fire({
            type: "warning",
            title: 'Falta Producción',
            text: 'La Máquina debe tener producción asignada.',
            confirmButtonClass: 'btn btn-danger',
        });
        return false;
    }
    return true;
}

function hasOperator() {
    if (getSelectedOperator() == null) {
        Swal.fire({
            type: "warning",
            title: 'Falta Operario',
            text: 'La Máquina debe tener un operario asignado.',
            confirmButtonClass: 'btn btn-danger',
        });
        return false;
    }
    return true;
}

function getLastProduction() {
    var request = callAjax("getLastProduction", 'POST', {});

    request.done(function(data) {

        if (window.lastUpdated == null) {
            window.lastUpdated = data;
        } else if (data !== window.lastUpdated) {
            window.lastUpdated = data;
            $("#listView").data("kendoListView").dataSource.read();
        }
    })
}


var ProductionDashboard = /** @class */ (function() {
    function ProductionDashboard() {}
    ProductionDashboard.prototype.initListView = function() {
        var dataSource = new kendo.data.DataSource({
            transport: {
                read: {
                    url: "getCurrentProduction",
                    type: "GET",
                    dataType: "JSON"
                },
            },
            requestEnd: function(e) {

            }
        });

        $("#listView").kendoListView({
            dataSource: dataSource,
            dataBound: function(e) {
                if (this.dataSource.data().length == 0) {
                    $("#listView").append("<h1>Sin Programación!</h1>");
                } else
                    updateCharts();

            },
            template: kendo.template($("#templateEquipment").html()),
            selectable: "single",
            change: onEquipmentSelected,

        });
    }
    return ProductionDashboard;
}());




function initializeProduction() {
    Swal.fire({
        title: 'Está Seguro que desea Inicializar la Producción ?',
        text: "Todas las máquinas quedarán apagadas y sin operario y las producciones quedarán finalizadas!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, Inicializar!'
    }).then((result) => {
        if (result.value) {

            let request = callAjax('initializeProduction', 'POST', {}, true);

            request.done(function(result) {
                $("#listView").data("kendoListView").dataSource.read()
                Swal.fire(
                    'Producción Inicializada!',
                    'Puede iniciar el proceso de Producción',
                    'success'
                );

            }).fail(function(jqXHR, status) {
                console.log('ERROR');
            });

        }
    })
}


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Helpers
function onEquipmentSelected(e) {
    var data = this.dataSource.view();
    var selectedItem = $.map(this.select(), function(item) {
        return data[$(item).index()];
    });
    window.equipment = selectedItem[0];
}


function showProductionData() {

    setSidebarData(window.equipment);
    showProductionSidebar();
    setTimeout(() => {
        updateGoalOverviewChart(productionprogress);
    }, 100);
}

function setSidebarData(equipment) {


    // HEADER DEL SIDEBAR
    $('.new-data-title').removeClass('idstatus1 idstatus2 idstatus3 idstatus4');
    $('.new-data-title').addClass('idstatus' + equipment.status.id);
    $('.new-data-title').css('background-color', equipment.status.color);

    $('#equipmentName').html(equipment.name).css('color', 'white');
    $('#statusIcon').html('<i class="' + equipment.status.icon + '"</i>').css('color', 'white');

    // SHOW/HIDE SECTION DEPENDING UPON THE MACHINE HAVING PRODUCTIONS OR NOT
    if (equipment.productions.length > 0) {
        $('#hasData').removeClass('hidden');
        $('#hasNoData').addClass('hidden');
    } else { // nothing to show
        $('#hasNoData').removeClass('hidden');
        $('#hasData').addClass('hidden');
        return;
    }
    //POPULATE Productions DropDown with Equipment productions    
    $("#equipmentProductions").data("kendoDropDownList").dataSource.data(makeProductionsList(equipment.productions));
    // Set Productions DropDown with idproduction    
    if (equipment.idproduction != null)
        $("#equipmentProductions").data("kendoDropDownList").value(equipment.idproduction);
    else {
        console.log('aca');
        $("#equipmentProductions").data("kendoDropDownList").value(equipment.productions[0].id);
        $("#productionSchedule").data("kendoDropDownList").value(equipment.productions[0].idschedule);
        $("#equipmentOperator").data("kendoDropDownList").value(equipment.productions[0].idoperator);
    }

    // Turn On/FF switch
    $('#powerOnEquipment').prop('checked', equipment.idstatus != equipmentStatus.OFF);
    $("#equipmentProductions").data('kendoDropDownList').enable(equipment.idstatus == equipmentStatus.OFF);


    refreshSidebarData(equipment.idproduction);


}



// Called when productions DropDown changes
function refreshSidebarData(idproduction) {

    // Set Production Operator
    //$("#equipmentOperator").data('kendoDropDownList').value(getEquipmentOperator(equipment));

    let prod = _getProduction(idproduction, window.equipment.productions);
    console.log(prod);

    if (prod != null) {
        $("#productName").html(prod.product.name);
        $("#equipmentOperator").data('kendoDropDownList').value(prod.idoperator);
        $("#productionSchedule").data('kendoDropDownList').value(prod.idschedule);
        $("#presentationName").html(prod.presentation.name);
        $('#destinationName').html(prod.destination.name);
        $('#productiongoal').html(prod.productiongoal);
        $('#totalproduced').html(prod.totalproduced);
        $('#initialcount').html(prod.initialcount);
        $('#finalcount').html(prod.finalcount);
        $('#productionorder').html(prod.productionorder);
        $('#lot').html(prod.lot);
        // Production Progress
        window.productionprogress = Math.round((prod.totalproduced / prod.productiongoal) * 100);
        // Production Detail Grid
        window.producionDetailGrid.setProductionID(prod.id);

    } else {
        $("#productName").html('');
        //$("#equipmentOperator").data('kendoDropDownList').value(-1);
        //$("#productionSchedule").data('kendoDropDownList').value(-1);
        $("#presentationName").html('');
        $('#destinationName').html('');
        $('#productiongoal').html('');
        $('#totalproduced').html('');
        $('#initialcount').html('');
        $('#finalcount').html('');
        $('#productionorder').html('');
        $('#lot').html('');
        window.productionprogress = 0;
    }


}




// COnvert productions list to value/text to be used by dropdown
function makeProductionsList(productions) {
    var list = [];
    productions.forEach(function(prod) {
        list.push({ 'value': prod.id, 'text': prod.product.name });
    });
    return list;
}


function initProductionsDropDown() {
    $("#equipmentProductions").kendoDropDownList({
        optionLabel: "Seleccione Producción",
        dataTextField: "text",
        dataValueField: "value",
        //dataSource: global_users,
        autoClose: true,
        change: productionChanged,
        // height: 310,
        // template: '<span class="selected-value" style="background-image: url(\'#:data.urlpicture#\')"></span><span>#:data.text#</span>',
        //TODO: MEJORAR EL TEMPLATE..TAL VEZ REDONDEAR BORDER..CAMBIAR COLOR UN POCO
        valueTemplate: '<span  style="border-radius: 20px; padding: 10px 20px; background-color: \\#252424; color: white">#:data.text#</span>',
        //footerTemplate: 'Total number of <strong>#: instance.dataSource.total() #</strong> operators found',

    }).data("kendoDropDownList");
}


function initOperatorDropDown() {
    $("#equipmentOperator").kendoDropDownList({
        optionLabel: "Selecione Operario",
        dataTextField: "text",
        dataValueField: "value",
        dataSource: global_users,
        autoClose: true,
        change: operatorChanged,
        // height: 310,
        template: '<span class="selected-value" style="background-image: url(\'#:data.urlpicture#\')"></span><span>#:data.text#</span>',
        valueTemplate: '<span class="selected-value" style="background-image: url(\'#:data.urlpicture#\')"></span><span>#:data.text#</span>',
        //footerTemplate: 'Total number of <strong>#: instance.dataSource.total() #</strong> operators found',

    }).data("kendoDropDownList");
}


function initSchedulesDropDown() {
    $("#productionSchedule").kendoDropDownList({
        optionLabel: "Selecione Horario",
        dataTextField: "text",
        dataValueField: "value",
        dataSource: window.schedules,
        autoClose: true,
        change: operatorChanged,
        // height: 310,       
        //footerTemplate: 'Total number of <strong>#: instance.dataSource.total() #</strong> operators found',

    }).data("kendoDropDownList");
}



function productionChanged(e) {
    refreshSidebarData(e.sender.value());
}
// Set this operator to the selected production if nay
function operatorChanged(e) {
    /* //e.sender.value();
     var prod = getEquipmentCurrentProduction(window.equipment);
     if (prod != null) {
         prod.idoperator = e.sender.value();
     }
     */
}

//
/*
function getProduction(idproduction) {
    let result = window.equipment.find(p => p.id === idproduction);
    return (typeof result == "undefined" ? null : result);
}
*/

// From DropDown
function getSelectedProduction() {

    let value = $("#equipmentProductions").data('kendoDropDownList').value();
    if (value == "") return null;
    return value;
}


function getSelectedOperator() {
    let value = $("#equipmentOperator").data('kendoDropDownList').value();
    if (value == "") return null;
    return value;
}

function getSelectedSchedule() {
    let value = $("#productionSchedule").data('kendoDropDownList').value();
    if (value == "") return null;
    return value;
}


// Save Production/Operator of current Equipment
function saveSidebarChanges() {

    let data = {
        idequipment: window.equipment.id,
        idproduction: getSelectedProduction(),
        idoperator: getSelectedOperator(),
        idschedule: getSelectedSchedule(),
    };
    let request = callAjax('api/updateEquipmentProduction', 'POST', data, true);
    request.done(function(result) {
        console.log('success updateProductionData');
    }).fail(function(jqXHR, status) {
        console.log('fail udateProductionData');
        //TODO: show error
    });
}






function startProduction() {
    var initialCount;
    return new Promise(function(resolve, reject) {
        Swal.mixin({
            input: 'number',
            allowOutsideClick: false,
            confirmButtonText: 'Siquiente &rarr;',
            showCancelButton: true,
            progressSteps: ['1', '2'],
            confirmButtonClass: 'btn btn-primary',
            buttonsStyling: false,
            cancelButtonClass: "btn btn-danger ml-1"
        }).queue([{
            title: 'Contador Inicial',
            text: 'Digite el contador de inicio'
        }]).then(function(result) {
            if (result.dismiss == "cancel") { reject('fail'); }
            if (result.value) {
                Swal.fire({
                    title: '¿Iniciar producción?',
                    html: 'Contador Inicial: ' + result.value[0],
                    showCancelButton: true,
                    confirmButtonText: 'Aceptar',
                    cancelButtonText: "Cancelar",
                    cancelButtonColor: '#d33',
                    preConfirm: function(e) {
                        initialCount = result.value[0];
                        let request = callAjax('api/startProduction', 'POST', {
                            'idequipment': window.equipment.id, // 
                            'idproduction': getSelectedProduction(),
                            'idoperator': getSelectedOperator(),
                            'initialcount': result.value[0]
                        }, true);
                        request.done(function(result) {

                            PNotify.success({ title: window.equipment.name, text: 'Máquina encendida con éxito' });
                            $("#listView").data("kendoListView").dataSource.read();
                            // Update initial count
                            $('#initialcount').html(initialCount);
                            resolve('done');
                        }).fail(function(jqXHR, status) {
                            Swal.fire({
                                type: "error",
                                title: 'Error!',
                                text: 'Algo salíó mal!',
                                confirmButtonClass: 'btn btn-success',
                            });
                            reject('fail');
                        });
                    }
                }).then(function(result) {
                    if (result.dismiss == "cancel") { reject('fail'); }
                })
            }
        })
    })
}

function finishProduction() {
    var finalCount;

    return new Promise(function(resolve, reject) {
        Swal.mixin({
            input: 'number',
            allowOutsideClick: false,
            confirmButtonText: 'Siquiente &rarr;',
            showCancelButton: true,
            progressSteps: ['1', '2'],
            confirmButtonClass: 'btn btn-primary',
            buttonsStyling: false,
            cancelButtonClass: "btn btn-danger ml-1"
        }).queue([{
            title: 'Contador Final',
            text: 'Digite el contador final'
        }]).then(function(result) {
            if (result.dismiss == "cancel") { reject('fail'); }
            if (result.value) {
                Swal.fire({
                    title: '¿Finalizar producción?',
                    html: 'Contador Final: ' + result.value[0],
                    showCancelButton: true,
                    confirmButtonText: 'Aceptar',
                    cancelButtonText: "Cancelar",
                    cancelButtonColor: '#d33',
                    preConfirm: function(e) {
                        finalCount = result.value[0];
                        let request = callAjax('api/finishProduction', 'POST', { 'idproduction': window.equipment.idproduction, 'finalcount': result.value[0] }, true);
                        request.done(function(result) {
                            $("#listView").data("kendoListView").dataSource.read();
                            // update final count
                            $('#finalcount').html(finalCount);
                            PNotify.success({ title: window.equipment.name, text: 'Máquina apagada con éxito' });
                            resolve('done');
                        }).fail(function(jqXHR, status) {
                            Swal.fire({
                                type: "error",
                                title: 'Error!',
                                text: 'Algo salíó mal!',
                                confirmButtonClass: 'btn btn-success',
                            });
                            reject('fail');
                        });
                    }
                }).then(function(result) {
                    if (result.dismiss == "cancel") { reject('fail'); }
                })
            }
        })
    })
}




function initializeCharts() {
    drawGlobalGoalOverviewChart();
    drawBarChartProduction();
    drawGoalOverviewChart(); // sidebar Chart
}

function updateCharts() {

    // Global Production/Goal

    var totals = getProductionTotals();
    updateGlobalGoalOverviewChart(totals.progress);
    $('#totalproducedglobal').html(totals.totalproduced);
    $('#productiongoalglobal').html(totals.productiongoal);
    // Equipments Stats
    $('#equipmentsWorking').html(totals.equipmentsWorking);
    $('#equipmentsStopped').html(totals.equipmentsStopped);
    $('#equipmentsOff').html(totals.equipmentsOff);

    //Production Chart
    if (totals.totalproduced > 0)
        updateBarChartProduction(totals.produced, totals.equipments);
}


function getProductionTotals() {
    var data = $("#listView").data("kendoListView").dataSource.data();
    var productiongoal = 0;
    var totalproduced = 0;
    var equipmentsWorking = 0;
    var equipmentsStopped = 0;
    var equipmentsOff = 0;
    var equipments = [];
    var produced = []; // by equipment



    data.map(function(equipment) {
        if (equipment.idstatus == 2) equipmentsWorking++;
        else if (equipment.idstatus == 3) equipmentsStopped++;
        else if (equipment.idstatus == 1) equipmentsOff++;
        equipment.productions.map(function(prod) {
            productiongoal += prod.productiongoal;
            totalproduced += prod.totalproduced;
            if (prod.totalproduced > 0) {
                equipments.push(equipment.name);
                produced.push(prod.totalproduced);
            }
        });
    })

    return {
        productiongoal: productiongoal,
        totalproduced: totalproduced,
        progress: productiongoal == 0 ? 0 : Math.round((totalproduced / productiongoal) * 100),
        equipmentsWorking: equipmentsWorking,
        equipmentsStopped: equipmentsStopped,
        equipmentsOff: equipmentsOff,
        equipments: equipments,
        produced: produced
    }
}


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Event Helpers
function showProductionSidebar() {
    $(".add-new-data").addClass("show");
    $(".overlay-bg").addClass("show");
}

function hideProductionSidebar() {
    $(".add-new-data").removeClass("show");
    $(".overlay-bg").removeClass("show");
}



////////////////////////////////////////////////////////////////
const equipmentStatus = {
    OFF: 1,
    WORKING: 2,
    STOPPED: 3
}