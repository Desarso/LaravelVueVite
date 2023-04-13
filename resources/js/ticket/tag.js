$(document).ready(function() {

    initTagKendoColorPalette();
    initDropDownListTicketType();

    $("#tagname").keypress(function() {
        previewTag();
    });

    $("#btnSaveTag").click(function() {
        createTag();
    });
});

addNewConfig = function addNewConfig(id, value)
{
    switch(id)
    {
        case "multiSelectItem":
            $("#modalNewItem").modal("show");
            break;

        case "multiSelectSpot":
            $("#modalNewSpot").modal("show");
            break;
            
        case "multiSelectTag":
            
            break;
    }
    alert(id + " " + value);
}

function showModalTag(tagname)
{
    $("#modalTag").modal("show");
    $("#tagname").val(tagname).focus();
    previewTag();
}

function initDropDownListTicketType()
{
    $("#dropDownListTicketType").kendoDropDownList({
        dataTextField: "text",
        dataValueField: "value",
        dataSource: [],
    });
}

function initTagKendoColorPalette()
{
    tagKendoColorPalette = $("#tag-color-chooser").kendoColorPalette({
        palette: ["#007bff", "#17a2b8", "#746153", "#3a4c8b", "#ffcc33", "#fb455f", "#ac120f"],
        tileSize: 30,
        value: "#007bff",
        change: function() {
            previewTag();
        }
    }).data("kendoColorPalette");
}

function previewTag()
{
    let color  = $("#tag-color-chooser").data("kendoColorPalette").value();
    let tagname = $("#tagname").val()
    $("#tag-result").text(tagname);
    $("#tag-result").css("background-color", color);
}

function createTag()
{
    let data = {"name": $("#tagname").val(), "color": $("#tag-color-chooser").data("kendoColorPalette").value()};

    let request = callAjax('createTag', 'POST', data, true);

    request.done(function(result) {
        
        PNotify.closeAll();

        if(result.success)
        {
            $("#modalTag").modal("hide");
            let data_source = multiSelectTag.dataSource.data();
            let obj = {"value": result.data.id, "text": result.data.name, "color": result.data.color}
            data_source.push(obj);
            window.global_tags.push(obj);
            let tags = multiSelectTag.value();
            multiSelectTag.dataSource.data(data_source);
            tags.push(obj.value);
            multiSelectTag.value(tags);
            multiSelectTag.trigger('select')
            multiSelectTag.focus();
            PNotify.success({ title: 'Etiqueta creada', text: 'Acción completada con éxito' });
        }

    }).fail(function(jqXHR, status) {
        PNotify.closeAll();
        PNotify.error({ title: 'Problemas', text: 'La acción no se puedo completar' });
    });
}
