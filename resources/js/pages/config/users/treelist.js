$(document).ready(function () {

    treeList = $("#treelist-spots").kendoTreeList({
        dataSource: {
            transport: {
                read: {
                    url: "getAllSpotsTreeList",
                    dataType: "json"
                }
            },
            schema: {
                model: {
                    id: "id",
                    parentId: "parentId",
                    expanded: true,
                    fields: {
                        parentId: { nullable: true },
                        id: { type: "number" },
                        //HireDate: { field: "HireDate", type: "date" }
                    }
                }
            }
        },
        toolbar: [ "search" ],
        height: 540,
        filterable: true,
        sortable: true,
        columns: [
            { field: "id", width: "100px", template : function(dataItem){ return "<input id='tttt-" + dataItem.id + "' class='k-checkbox k-checkbox-md k-rounded-md' type='checkbox' checked>" } },
            {
                field: "name", title: "Name",
                //template: "#: FirstName # #: LastName #"
            }
        ],
        
        pageable: {
            pageSize: 15,
            pageSizes: true
        },
        page: function(e) {
            /* The result can be observed in the DevTools(F12) console of the browser. */

            setTimeout(() => {
                var view = treeList.dataSource.view();
                console.log(view);
            }, 300);


        }
    
    }).data("kendoTreeList");;

});

    /*
    var dataSource = new kendo.data.TreeListDataSource({
        data: window.dataSpots,
        schema: {
            model: {
                id: "id",
                expanded: true
            }
        }
    });

    $("#treelist-spots").kendoTreeList({
        dataSource: dataSource,
        height: 540,
        columns: [
            { selectable: true, width: "100px" },
            { field: "name" },
            //{ field: "parentId" }
        ],
        pageable: {
            pageSize: 15,
            pageSizes: true
        }
    });
    
});
*/


/*

$(document).ready(function () {

    dataSourceSpots = new kendo.data.HierarchicalDataSource({
        data: window.spots
    });

    treeViewSpots = $("#treeview-spots").kendoTreeView({
        checkboxes: {
            checkChildren: true
        },
        dataSource: dataSourceSpots,
        //check: onCheck,  
    }).data("kendoTreeView");
});

function onExpand(e)
{
    $(e.node).find("li").show();
}

function onCheck(e)
{
    var checkedNodes = [];

    getCheckedNodes(treeViewSpots.dataSource.view(), checkedNodes);
    //WHTreeView.setMessage(checkedNodes.length);
}

function getCheckedNodes(nodes, checkedNodes)
{
    var node;

    for (var i = 0; i < nodes.length; i++) {
        node = nodes[i];

        if (node.checked) {
            checkedNodes.push({ text: node.text, id: node.id });
        }

        if (node.hasChildren) {
            getCheckedNodes(node.children.view(), checkedNodes);
        }
    }
}


function getSpotsChecked()
{
    var checkedNodes = [];

    getCheckedNodes(treeViewSpots.dataSource.view(), checkedNodes);
    let spots = checkedNodes.map(function(obj) { return obj.id; });
    console.log(spots);
    
    //window._save({ "spots": spots });
}



*/





















































































/*
$(document).ready(function () {

    dataSourceSpots = new kendo.data.HierarchicalDataSource({
        data: window.spots
    });

    treeViewSpots = $("#treeview-spots").kendoTreeView({
        checkboxes: {
            checkChildren: true
        },
        dataSource: dataSourceSpots,
    }).data("kendoTreeView");
});

function onExpand(e)
{
    $(e.node).find("li").show();
}

function onCheck(e)
{
    var checkedNodes = [];

    getCheckedNodes(treeViewSpots.dataSource.view(), checkedNodes);
    //WHTreeView.setMessage(checkedNodes.length);
}

function getCheckedNodes(nodes, checkedNodes)
{
    var node;

    for (var i = 0; i < nodes.length; i++) {
        node = nodes[i];

        if (node.checked) {
            checkedNodes.push({ text: node.text, id: node.id });
        }

        if (node.hasChildren) {
            getCheckedNodes(node.children.view(), checkedNodes);
        }
    }
}


function getSpotsChecked()
{
    var checkedNodes = [];

    getCheckedNodes(treeViewSpots.dataSource.view(), checkedNodes);
    let spots = checkedNodes.map(function(obj) { return obj.id; });
    console.log(spots);
    
    //window._save({ "spots": spots });
}

*/
