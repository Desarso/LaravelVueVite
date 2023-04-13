// Matthias  - 28/04/2020

jQuery.expr[':'].icontains = function(a, i, m) {
    return jQuery(a).text().toUpperCase()
        .indexOf(m[3].toUpperCase()) >= 0;
  };

window._save = null;
class WHTreeView {

    constructor(data, _save) {
        this.constructHTML();
        console.log('Init TreeView');
        window._save = _save;
        this.dataSource = new kendo.data.HierarchicalDataSource({
            data: data
        });
        // TreeView
        this.treeView = $("#treeview").kendoTreeView({
            loadOnDemand: false,
            checkboxes: {
                checkChildren: true
            },
            dataSource: this.dataSource,
            check: WHTreeView.onCheck,
            expand: WHTreeView.onExpand
        }).data("kendoTreeView");



        // Kendo Dialog
        this.dialog = $("#dialog").kendoDialog({
            width: "400px",
            height: "600px",
            visible: false,
            animation: {
                open: {
                    effects: "zoom:in",
                },

            },
            actions: [{
                    text: 'Cancel',
                    primary: false,
                    action: this.dialogCancel
                },
                {
                    text: 'Aceptar',
                    primary: true,
                    action: this.dialogOK
                }
            ],
        }).data("kendoDialog");

    }

    constructHTML() {
        let html = '<div id="dialog" style="display: none">\
                        <div class="dialogContent">\
                        <input id="filterText" type="text" placeholder="Buscar Spots" />\
                        <div class="selectAll">\
                        <input type="checkbox" id="chbAll" class="k-checkbox" onchange="chbAllOnChange()" />\
                        <label class="k-checkbox-label" for="chbAll">Seleccionar todos</label>\
                        <span id="result">0 spots selecionados</span>\
                        </div>\
                        <div id="treeview" style="height:400px;"></div>\
                    </div>\
                    </div>';
        $("body").append(html);

        // filter logic
        $("#filterText").keyup(function(e) {
            var filterText = $(this).val();
            console.log('filtro');

            if (filterText !== "") {

                $(".selectAll").css("visibility", "hidden");

                $("#treeview .k-group .k-group .k-in").closest("li").hide();
                $("#treeview .k-group").closest("li").hide();
                $("#treeview .k-in:icontains(" + filterText + ")").each(function() {
                    $(this).parents("ul, li").each(function() {
                        var treeView = $("#treeview").data("kendoTreeView");
                        treeView.expand($(this).parents("li"));
                        $(this).show();
                    });
                });
                $("#treeview .k-group .k-in:icontains(" + filterText + ")").each(function() {
                    $(this).parents("ul, li").each(function() {
                        $(this).show();
                    });
                });
            } else {
                $("#treeview .k-group").find("li").show();
                var nodes = $("#treeview > .k-group > li");

                $.each(nodes, function(i, val) {
                    if (nodes[i].getAttribute("data-expanded") == null) {
                        $(nodes[i]).find("li").hide();
                    }
                });

                $(".selectAll").css("visibility", "visible");
            }
        });

    }

    // Open TreeView Dialog
    open(title, checkedNodes) {
        this.treeView.expand(".k-item");

        var checked = checkedNodes == null ? [] : JSON.parse(checkedNodes);
        var treeview = $("#treeview").data("kendoTreeView");
        // Clear treeview selection
        $("#treeview .k-checkbox-wrapper input").prop("checked", false).trigger("change");
        WHTreeView.setMessage(checked.length);

        for (var i = 0; i < checked.length; i++)
        {
            var barDataItem = this.dataSource.get(checked[i]);

            if (typeof barDataItem !== 'undefined')
            {
                var barElement = treeview.findByUid(barDataItem.uid);
                treeview.dataItem(barElement).set("checked", true);
            }
        }

        //$("#dialog").data("kendoDialog").title(title);
        $('.k-dialog-title').html(title);

        $('#dialog').show();
        var dialog = $("#dialog");
        dialog.data("kendoDialog").open();
    }




    dialogCancel(e) { e.sender.close() }


    dialogOK(e) {
        var checkedNodes = [];
        var treeView = $('#treeview').data("kendoTreeView");

        WHTreeView.getCheckedNodes(treeView.dataSource.view(), checkedNodes);
        let spots = checkedNodes.map(function(obj) { return obj.id; });
        // window._save({ "spots": spots, "iduser": window.userSelected.id });
        window._save({ "spots": spots });
        $("#dialog").data("kendoDialog").close();
        //TreeView.saveUserSpots({ "spots": spots, "iduser": window.userSelected.id });
    }

    static onCheck(e) {
        var checkedNodes = [];
        var treeView = $("#treeview").data("kendoTreeView");

        WHTreeView.getCheckedNodes(treeView.dataSource.view(), checkedNodes);
        WHTreeView.setMessage(checkedNodes.length);
    }



    static onExpand(e) {
        if ($("#filterText").val() == "") {
            $(e.node).find("li").show();
        }
    }


    static getCheckedNodes(nodes, checkedNodes) {
        var node;

        for (var i = 0; i < nodes.length; i++) {
            node = nodes[i];

            if (node.checked) {
                checkedNodes.push({ text: node.text, id: node.id });
            }

            if (node.hasChildren) {
                WHTreeView.getCheckedNodes(node.children.view(), checkedNodes);
            }
        }
    }



    static setMessage(checkedNodes) {

        var message;

        if (checkedNodes > 0) {
            message = checkedNodes + " spots seleccionados";
        } else {
            message = "0 spots seleccionados";
        }

        $("#result").html(message);
    }

}


////


function chbAllOnChange() {
    var checkedNodes = [];

    var treeView = $("#treeview").data("kendoTreeView");
    var isAllChecked = $('#chbAll').prop("checked");

    checkUncheckAllNodes(treeView.dataSource.view(), isAllChecked)

    if (isAllChecked) {
        WHTreeView.setMessage($('#treeview input[type="checkbox"]').length);
    } else {
        WHTreeView.setMessage(0);
    }
}

function checkUncheckAllNodes(nodes, checked) {

    for (var i = 0; i < nodes.length; i++) {
        nodes[i].set("checked", checked);

        if (nodes[i].hasChildren) {
            checkUncheckAllNodes(nodes[i].children.view(), checked);
        }
    }
}