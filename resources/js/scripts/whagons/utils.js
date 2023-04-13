callAjax = function callAjax(url, type = 'POST', data, loader = false) {
    var request = $.ajax({
        url: url,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: type,
        datatype: 'JSON',
        data: data,
        beforeSend: function() {
            /*
            if(loader == true)
            {
              PNotify.info({
                title: 'Procesando....',
                text: 'Espereme un momento',
                icon: 'fas fa-cog fa-spin'
              });
            }
            */
        },
    });

    return request;
};


locale = function locale(key, file = 'locale') {
    if (typeof window.translations[file][key] == 'undefined') return '_' + key;
    return window.translations[file][key];
}


function formatDows(dow) {

    if (dow == null) return "";
    let result = "";
    for (var i = 0; i < dow.length; i++) {
        result += '<span class="badge badge-primary">' + getDow(dow[i].value) + '</span>  ';
    }
    return result;
}

function formatInputs(inputs) {
    if (inputs == null) return "";
    let result = "";
    for (var i = 0; i < inputs.length; i++) {
        result += '<span class="badge badge-primary">' + getInput(inputs[i].value) + '</span>  ';
    }
    return result;

}

function getInput(idinput) {
    let result = window.inputs.find(o => o.value === idinput);
    return (typeof result == "undefined" ? '' : result.text);
}


function formatBreaks(br) {

    if (br == null) return "";
    let result = "";
    for (var i = 0; i < br.length; i++) {
        result += '<span class="badge badge-primary">' + getBreak(br[i].value) + '</span>  ';
    }
    return result;

}


function getBreak(idbreak) {
    let result = window.breaks.find(o => o.value === idbreak);
    return (typeof result == "undefined" ? '' : result.text);
}

function getDow(iddow) {
    let result = window.dows.find(o => o.value === iddow);
    return (typeof result == "undefined" ? '' : result.text);
}




function editorMultiSelectDOW(container, options) {
    editorMultiSelectTeam = $('<select style="width: 250px"  name="' + options.field + '"/>')
        .appendTo(container)
        .kendoMultiSelect({
            dataTextField: "text",
            dataValueField: "value",
            dataSource: window.dows,
            autoClose: true,
            //footerTemplate: 'Total #: instance.dataSource.total() #',
        }).data("kendoMultiSelect");
}

function editorMultiSelectBreak(container, options) {
    editorMultiSelectTeam = $('<select style="width: 250px"  name="' + options.field + '"/>')
        .appendTo(container)
        .kendoMultiSelect({
            dataTextField: "text",
            dataValueField: "value",
            dataSource: window.breaks,
            autoClose: true,
            //footerTemplate: 'Total #: instance.dataSource.total() #',
        }).data("kendoMultiSelect");
}

function editorMultiSelectInput(container, options) {
    editor = $('<select style="width: 250px"  name="' + options.field + '"/>')
        .appendTo(container)
        .kendoMultiSelect({
            dataTextField: "text",
            dataValueField: "value",
            dataSource: window.inputs,
            autoClose: true,
            //footerTemplate: 'Total #: instance.dataSource.total() #',
        }).data("kendoMultiSelect");
}



function getSafeHeight() {
    let zoomLevel = Math.round(window.devicePixelRatio * 100);
    let gridheight = 500;

    if (zoomLevel == 100)
        gridheight = $(window).height() * 0.62;
    else if (zoomLevel < 100) {
        if (zoomLevel < 100 && zoomLevel >= 80)
            gridheight = $(window).height() * 0.67;
        else if (zoomLevel < 80 && zoomLevel >= 67)
            gridheight = $(window).height() * 0.7;
        else if (zoomLevel < 67 && zoomLevel >= 50)
            gridheight = $(window).height() * 0.75;
        else if (zoomLevel < 50 && zoomLevel >= 33)
            gridheight = $(window).height() * 0.8;
        else if (zoomLevel < 33 && zoomLevel >= 25)
            gridheight = $(window).height() * 0.85;
        else if (zoomLevel < 25)
            gridheight = $(window).height() * 0.9;
    } else { // > 100
        if (zoomLevel <= 110)
            gridheight = $(window).height() * 0.60;
        else if (zoomLevel <= 125)
            gridheight = $(window).height() * 0.55;
        else if (zoomLevel <= 150)
            gridheight = $(window).height() * 0.52;
        else if (zoomLevel <= 175)
            gridheight = $(window).height() * 0.48;
    }

    return gridheight;

}

// Set the height of the grid according to the space in the screen
function fixKendoGridHeight() {
    let zoomLevel = Math.round(window.devicePixelRatio * 100);
    let gridheight = 500;

    if (zoomLevel == 100)
        gridheight = $(window).height() * 0.62;
    else if (zoomLevel < 100) {
        if (zoomLevel < 100 && zoomLevel >= 80)
            gridheight = $(window).height() * 0.67;
        else if (zoomLevel < 80 && zoomLevel >= 67)
            gridheight = $(window).height() * 0.7;
        else if (zoomLevel < 67 && zoomLevel >= 50)
            gridheight = $(window).height() * 0.75;
        else if (zoomLevel < 50 && zoomLevel >= 33)
            gridheight = $(window).height() * 0.8;
        else if (zoomLevel < 33 && zoomLevel >= 25)
            gridheight = $(window).height() * 0.85;
        else if (zoomLevel < 25)
            gridheight = $(window).height() * 0.9;
    } else { // > 100
        if (zoomLevel <= 110)
            gridheight = $(window).height() * 0.60;
        else if (zoomLevel <= 125)
            gridheight = $(window).height() * 0.55;
        else if (zoomLevel <= 150)
            gridheight = $(window).height() * 0.52;
        else if (zoomLevel <= 175)
            gridheight = $(window).height() * 0.48;
    }

    $(".k-grid-content").height(gridheight);

}


// Finders
function getTicketType(idtype) {
    let result = global_ticket_types.find(o => o.value === idtype);
    return (typeof result == "undefined" ? null : result);
}


function isCleaningTicketType(idtype) {
    let result = global_ticket_types.find(o => o.value === idtype);
    return (typeof result == "undefined" ? null :
        result.iscleaningtask == 1);
}

function getUser(iduser) {
    let result = global_users.find(o => o.value === parseInt(iduser));
    return (typeof result == "undefined" ? null : result);
}

function getSpot(idspot) {
    let result = global_spots.find(o => o.value === idspot);
    return (typeof result == "undefined" ? null : result);
}

function timeEditor(container, options) {
    $('<input data-text-field="' + options.field + '" data-value-field="' + options.field + '" data-bind="value:' + options.field + '" data-format="' + options.format + '"/>')
        .appendTo(container)
        .kendoTimePicker({});
}

function editorMultiSelectUser(container, options) {

    editorMultiSelectUser = $('<select style="width: 250px"  name="' + options.field + '"/>')
        .appendTo(container)
        .kendoMultiSelect({
            dataTextField: "text",
            dataValueField: "value",
            dataSource: global_users.filter(user => (user.enabled == true && user.deleted_at == null)),
            autoClose: true,
            footerTemplate: 'Total #: instance.dataSource.total() #',
        }).data("kendoMultiSelect");
}

function editorMultiSelectTeam(container, options) {

    editorMultiSelectTeam = $('<select style="width: 250px"  name="' + options.field + '"/>')
        .appendTo(container)
        .kendoMultiSelect({
            dataTextField: "text",
            dataValueField: "value",
            dataSource: global_teams,
            autoClose: true,
            footerTemplate: 'Total #: instance.dataSource.total() #',
        }).data("kendoMultiSelect");
}


function editorMultiSelectSpot(container, options) {

    editorMultiSelectSpot = $('<select style="width: 250px"  name="' + options.field + '"/>')
        .appendTo(container)
        .kendoMultiSelect({
            dataTextField: "text",
            dataValueField: "value",
            dataSource: global_spots,
            autoClose: true,
            footerTemplate: 'Total #: instance.dataSource.total() #',
        }).data("kendoMultiSelect");
}

// Editors
function dropDownListEditor(container, options)
{
    $('<input data-text-field="text" data-value-field="value" data-bind="value:' + options.field + '"/>')
        .appendTo(container)
        .kendoDropDownList({
            autoBind: false,
            optionLabel: " ",
            valuePrimitive: true,
            filter: "contains",
            dataSource: options.values,
            height: 400
        });
}

function dropDownListEditor2(container, options) {
    $('<input data-text-field="text" data-value-field="value" data-bind="value:' + options.field + '"/>')
        .appendTo(container)
        .kendoDropDownList({
            //autoBind: false,
            //optionLabel: " ",
            //valuePrimitive: true,
            dataSource: options.values
        });
}

function formatSLA(sla) {
    if (sla < 60) return sla + " min";
    return Math.trunc(sla / 60) + " hr " + sla % 60 + " min";
}


// Kendo Grid Helpers
function formatYesNo(value) {
    if (value == 0) return "<span class='badge badge-secondary'>" + locale('NO') + "</span>";
    return "<span class='badge badge-primary'>" + locale('YES') + "</span>";
}


function textAreaEditor(container, options) {
    $('<textarea rows="4" class="k-textbox" name="' + options.field + '" style="width:100%;height:100%;" />').appendTo(container);
}


function checkBoxEditor(container, options) {

    $('<label class="container"><input type="checkbox" name="' + options.field + '">\
    <span class="checkmark"></span></label>').appendTo(container);
}

function deleteMessage(relations) {
    Swal.fire({
        type: 'error',
        title: 'Relaciones activas',
        text: relations.join(", "),
        footer: '<a href>Registro utilizado</a>',
        confirmButtonClass: 'btn btn-primary',
        buttonsStyling: false,
    })
}



//</label>$('<input type="checkbox" name="' + options.field + '"/>').appendTo(container);