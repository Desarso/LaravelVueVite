var markers = [];
var flightPaths = [];
window.lastClockinChange = null;
var currentZoom = 9;
const iconClockin = "http://maps.google.com/mapfiles/ms/icons/green-dot.png";
const iconClockout = "http://maps.google.com/mapfiles/ms/icons/red-dot.png";

$(document).ready(function() {
    initDateRangePicker();
    initDropDownListUser();
    initDropDownListTeam();
    initGridClockinLog();

    //Get new changes in clockin
    setInterval(() => { getLastClockinChange(); }, 10000);
});

function initGridClockinLog()
{
    gridClockinLog = $("#gridClockinLog").kendoGrid({
        dataSource: {
            transport: {
                read: {
                    url: "getClockinData",
                    type: "get",
                    dataType: "json",
                    data: function() {
                        return {
                            start  : $('#dateRangePicker').data('daterangepicker').startDate.format('YYYY-MM-DD'),
                            end    : $('#dateRangePicker').data('daterangepicker').endDate.format('YYYY-MM-DD'),
                            iduser : dropDownListUser.value(),
                            idteam   : dropDownListTeam.value(),
                        };
                    },
                    beforeSend: function(e, request) {
                        window.urlExport = request.url;
                    },
                }
            },
            pageSize: 20,
            schema: {
                model: {
                    id: "id",
                    fields: {
                        id: { type: "number", editable: false, nullable: true },
                        name: { editable: true, field: "name", type: "string", validation: { required: { message: "Nombre es requerido" } } },
                    }
                }
            },
        },
        editable: false,
        toolbar: [],
        reorderable: true,
        resizable: true,
        sortable: true,
        pageable: {
            refresh: true,
            pageSizes: true,
            buttonCount: 5
        },
        height: '700px',
        filterable: true,
        selectable: true,
        noRecords: {
            template: "<div class='alert alert-danger mt-1 alert-validation-msg' role='alert'><i class='feather icon-info mr-1 align-middle'></i><span>No hay datos</span></div>"
        },
        dataBound: function(e) {
            gridClockinLog.select(e.sender.tbody.find("tr:first"));
        },
        change:function (e) {
            currentZoom = map.getZoom();
            var dataItem = gridClockinLog.dataItem(gridClockinLog.select()); 
            setMarkers([dataItem]);
        },
        columns: [
            {
                field: "iduser",
                title: "Usuario",
                width: "80px",
                template: function(dataItem) {
                    let user = global_users.find(o => o.value === dataItem.iduser)
                    return "<div class='user-photo' title='" + user.text + "' style='background-image: url(" + user.urlpicture + ");'></div> <b>" + user.firstname + "</b>";
                },
                filterable: false
            },
            {
                field: "start_location",
                title: "Entrada",
                template: function(dataItem) {

                    let location = JSON.parse(dataItem.start_location).location;

                    return location + "<br><b>" + moment(dataItem.clockin).format('DD MMMM YYYY hh:mm A') + "</b>";
                },
                width: "220px",
                filterable: false
            },
            {
                field: "end_location",
                title: "Salida",
                template: function(dataItem) {
                    if(dataItem.end_location == null) return "---------------------------------------------";
                    let location = JSON.parse(dataItem.end_location).location;

                    return location + "<br><b>" + moment(dataItem.clockout).format('DD MMMM YYYY hh:mm A') + "</b>";
                },
                width: "220px",
                filterable: false
            }
        ],
    }).data("kendoGrid");

    $("table th").eq(0).css({"background-color": "#978f8f", "color": "white"});
    $("table th").eq(1).css({"background-color": "#12c684", "color": "white"});
    $("table th").eq(2).css({"background-color": "#F4516C", "color": "white"});

}

function changeFilter()
{
    gridClockinLog.dataSource.read();
}

function initMap()
{
  var center = { lat: 9.9356284, lng: -84.1483645 };

  map = new google.maps.Map( document.getElementById('map'), { zoom: currentZoom, center: center });
}

function setMarkers(data)
{
    deletePaths();
    deleteMarkers();

    map.setZoom(currentZoom);

    if(data.length != 0)
    {
        data.forEach(clockin => {

            var dataStart;
            var dataEnd;
            
            if (clockin.start_location != null)
            {
                let coordinates = JSON.parse(clockin.start_location);
                clockin.location = coordinates.location;
                dataStart = { lat : coordinates.lat, lng : coordinates.long };
                addMarker(dataStart, "Entrada", iconClockin, clockin, clockin.clockin);
                map.setCenter(new google.maps.LatLng(coordinates.lat, coordinates.long));
                map.setZoom(currentZoom);
            }

            if (clockin.end_location != null)
            {
                let coordinates = JSON.parse(clockin.end_location);
                clockin.location = coordinates.location;
                dataEnd = { lat : coordinates.lat, lng : coordinates.long };
                addMarker(dataEnd, "Salida", iconClockout, clockin, clockin.clockout);
            }

            if (clockin.start_location != null && clockin.end_location != null)
            {
                flightPath = new google.maps.Polyline({
                    path: [ dataStart, dataEnd ],
                    geodesic: true,
                    strokeColor: "#7C7C7C",
                    strokeOpacity: 1.0,
                    strokeWeight: 2,
                });
                
                flightPath.setMap(map);
                flightPaths.push(flightPath);
            }
        });
    }
}

function addMarker(location, title = "", icon, clockin, date)
{
    const contentString =
    '<div id="content">' +
    '<div id="siteNotice">' +
    "</div>" +
    "<h1 id='firstHeading' class='firstHeading'><img src=" + icon + ">" + clockin.activity.name + "</h1>" +
    '<div id="bodyContent">' +
    "<p><b>" + clockin.location + "</b><br>" +
    "(" + moment(date).format('DD MMM YYYY hh:mm A') + ")" +
    "</div>" +
    "</div>";

    const marker = new google.maps.Marker({
      position: location,
      map: map,
      title: title,
    });

    marker.setIcon(icon);
    markers.push(marker);

    const infowindow = new google.maps.InfoWindow({
        content: contentString,
      });

      marker.addListener("click", () => {
        infowindow.open({
          anchor: marker,
          map,
          shouldFocus: false,
        });
      });
}

function setMapOnAll(map)
{
    for (let i = 0; i < markers.length; i++)
    {
        markers[i].setMap(map);
    }
}

function clearMarkers()
{
    setMapOnAll(null);
}

function deleteMarkers()
{
    clearMarkers();
    markers = [];
}


function setPathOnAll(path)
{
    for (let i = 0; i < flightPaths.length; i++)
    {
        flightPaths[i].setMap(path);
    }
}

function clearPaths()
{
    setPathOnAll(null);
}

function deletePaths()
{
    clearPaths();
    flightPaths = [];
}

function getLastClockinChange()
{
    var request = callAjax("getLastClockinChange", 'POST', {});

    request.done(function(data) {

        if(window.lastClockinChange == null)
        {
            window.lastClockinChange = data;
        }
        else if(data !== window.lastClockinChange)
        {
            window.lastClockinChange = data;
            gridClockinLog.dataSource.read();
        }
    });
}

$("#btn-excel").click(function() {

    $.blockUI({ message: '<h1>Procesando...</h1>' });

    let newURL = window.urlExport.replace("getClockinData", "exportClockinMap");
    let request = callAjax(newURL, 'GET', null);

    request.done(function(response, textStatus, request) {
        var a = document.createElement("a");
        a.href = response.file;
        a.download = response.name;
        document.body.appendChild(a);
        a.click();
        a.remove();
        $.unblockUI();
    }).fail(function(jqXHR, status) {
        $.unblockUI();
    });
});