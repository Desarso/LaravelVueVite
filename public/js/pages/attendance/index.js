var markers = [];
var map     = null;
window.selectedUser = null;
window.lastUpdated = null;

$(document).ready(function() {
    initGridAttendance();
    initGridHistory();
    initMap();
    initChart();

    setInterval(() => {
        getLastAttendance();
    }, 10000);

    $('#modal-history').on('show.bs.modal', function(e) {
        gridHistory.dataSource.read();
    })

});

function initChart()
{
    var options = {
        series: [44, 55, 13, 43, 22],
        chart: {
        width: 380,
        type: 'pie',
      },
      labels: ['Team A', 'Team B', 'Team C', 'Team D', 'Team E'],
      responsive: [{
        breakpoint: 480,
        options: {
          chart: {
            width: 120
          },
          legend: {
            position: 'bottom'
          }
        }
      }]
      };

      var chart = new ApexCharts(document.querySelector("#chart"), options);
      chart.render();
}

function initMap()
{
  var center = { lat: 9.9356284, lng: -84.1483645 };

  map = new google.maps.Map( document.getElementById('map'), { zoom: 9, center: center });
}

function initGridAttendance()
{
    gridAttendance = $("#gridAttendance").kendoGrid({
        dataSource: {
            transport: {
                read: {
                    url: "getAllAttendances",
                    type: "get",
                    dataType: "json"
                },
            },
            pageSize: 20,
            schema: {
                model: {
                    id: "id",
                    fields: {
                        id: { type: "number", editable: false, nullable: true },
                    }
                }
            },
            aggregate: [
                { field: "duration", aggregate: "sum" },
            ]
        },
        editable: {
            mode: "popup"
        },
        height:"600px",
        dataBound: function(e) {},
        persistSelection: true,
        change: onChange,
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
            {
                selectable: true,
                width: "20px"
            },
            /*
            {
                field: "status",
                title: " ",
                width: "17px",
                template: "#=formatStatus(status)#",
                filterable: false
            },
            */
            {
                field: "id",
                title:  "Responsable",
                values: window.global_users,
                template: "#=formatUser(id)#",
                width: "120px",
                filterable: false
            },
            {
                field: "start_location",
                title: "Entrada",
                width: "190px",
                template: "#=formatLocation(start_location, punch_in, status)#",
                filterable: false
            },
            {
                field: "end_location",
                title: "Salida",
                width: "190px",
                template: "#=formatLocation(end_location, punch_out, status)#",
                filterable: false
            },
            {
                field: "duration",
                title: "Duración",
                width: "60px",
                template: "#=formatDuration(duration)#",
                aggregates: ["sum"],
                footerTemplate: "#=formatDuration(sum)#",
                filterable: false
            },
            {
                command: [
                    { name: "custom1", text: " ", iconClass: "fad fa-history", click: showHistory},
                    { name: "custom2", text: " ", iconClass: "fad fa-map-marked-alt", click: setMarkers}
                ],
                title: " ",
                width: "45px"
            }
        ],
    }).data("kendoGrid");
}

function showHistory(e)
{
    window.selectedUser = this.dataItem($(e.currentTarget).closest("tr"));

    $("#user-fullname").text(window.selectedUser.fullname);
    $("#user-job").text(window.selectedUser.job);
    $("#user-image").attr("src", window.selectedUser.urlpicture);

    $("#modal-history").modal("show");
}

function initGridHistory()
{
    gridHistory = $("#gridHistory").kendoGrid({
        dataSource: {
            transport: {
                read: {
                    url: "getAllAttendancesByUser",
                    type: "get",
                    dataType: "json",
                    data: function() {
                        return {
                            iduser: (window.selectedUser == null ? null : window.selectedUser.id)
                        };
                    },
                },
            },
            pageSize: 20,
            schema: {
                model: {
                    id: "id",
                    fields: {
                        id: { type: "number", editable: false, nullable: true },
                    }
                }
            },
            aggregate: [
                { field: "duration", aggregate: "sum" },
            ]
        },
        editable: {
            mode: "popup"
        },
        height:"443px",
        dataBound: function(e) {
        },
        reorderable: true,
        resizable: true,
        sortable: true,
        pageable: {
            refresh: true,
            pageSizes: true,
            buttonCount: 5,
        },
        filterable: true,
        noRecords: {
            template: "<div class='alert alert-danger mt-1 alert-validation-msg' role='alert'><i class='feather icon-info mr-1 align-middle'></i><span>No hay registros</span></div>"
        },
        columns: [
            {
                field: "start_location",
                title: "Entrada",
                width: "130px",
                template: "#=formatLocation(start_location, punch_in, status)#",
                filterable: false
            },
            {
                field: "end_location",
                title: "Salida",
                width: "130px",
                template: "#=formatLocation(end_location, punch_out, status)#",
                filterable: false
            },
            {
                field: "duration",
                title: "Duración",
                width: "60px",
                template: "#=formatDuration(duration)#",
                aggregates: ["sum"],
                footerTemplate: "#=formatDuration(sum)#",
                filterable: false
            }
        ],
    }).data("kendoGrid");
}

function setMarkers(e)
{
    e.preventDefault();

    $("#map-tab-justified").click();
    
    deleteMarkers();

    window.selectedUser = this.dataItem($(e.currentTarget).closest("tr"));

    if(window.selectedUser.latest_attendance != null)
    {
        let start_location = JSON.parse(window.selectedUser.latest_attendance.start_location);
        let dataStart = { lat : start_location.lat, lng : start_location.long };
        addMarker(dataStart, "Entrada");

        if(window.selectedUser.latest_attendance.end_location != null)
        {
            let end_location   = JSON.parse(window.selectedUser.latest_attendance.end_location);
            let dataEnd   = { lat : end_location.lat, lng : end_location.long };
            addMarker(dataEnd, "Salida");
        }
    }
}

function addMarker(location, title = "")
{
    const marker = new google.maps.Marker({
      position: location,
      map: map,
      title: title,
    });
    markers.push(marker);
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

function onChange(arg)
{
    console.log("The selected product ids are: [" + this.selectedKeyNames().join(", ") + "]");
}

function getLastAttendance()
{
    var request = callAjax("getLastAttendance", 'POST', {});

    request.done(function(data) {

        if (window.lastUpdated == null) {
            window.lastUpdated = data;
        } else if (data !== window.lastUpdated) {
            window.lastUpdated = data;
            gridAttendance.dataSource.read();
        }
    });
}

function formatLocation(location, date, status)
{
    let color = (status == "OUT" ? "black" : "black");

    if(location == null) return "------------------";
    return "<div class='title-wrapper d-flex'><h6 class='todo-title mt-50 mx-50' style='color:" + color + "; font-size: initial;'> <i style='color:#fd774d' class='font-medium-2 fas fa-map-marker-alt'></i> " + location + "</h6></div>" +
           "<p style='font-weight: 900;' class='truncate mb-0 ml-50'><i class='fad fa-clock font-medium-2 mr-50'></i>" + formatDate(date) + "</p>";
}

function formatDuration(duration)
{
    if(duration == 0) return "-------------";
    let time = moment().startOf('day').seconds(duration).format('HH:mm:ss');
    return "<p style='font-size: 1.4rem !important; margin-top: 9px; font-weight: 900;'>" + time + "</p>"
}

function formatUser(iduser)
{
    let user = getUser(iduser);
    if (user == null) return '';

    return "<li data-toggle='tooltip' data-popup='tooltip-custom' data-placement='top' data-original-title='" + user.text + "' class='avatar pull-up'>" +
            "<img class='media-object rounded-circle' src='" + user.urlpicture + "' alt='Avatar' height='30' width='30'>" +
        "</li><strong>" + user.text + "</strong>";
}

function formatDate(date)
{
    return (date == null ? "------------------" : moment(date).format('YY-MM-DD hh:mm A'));
}

function formatStatus(status)
{
    if(status == "OUT" || status == null) return "";

    return "<div class='spinner-border' role='status'>" +
                "<span class='sr-only'>Loading...</span>" +
            "</div>";
}