$(document).ready(function () {
    getNotifications();

    $(document).on("click", ".item-notification", function(event) {

        let notification = $(this).data("item");

        switch(notification.type)
        {
            case "Ticket":

                let filter = {logic: "and", filters: [{field: "code", value: notification.idreference, operator: "eq"}]};
                setFilter(filter);
                
                break;

            case "Cleaning":
                
                break;        
        }
    });

});

$("#dropdown-notification").click(function () {
    readNotifications();
});

getNotifications = function getNotifications()
{
    let request = callAjax('getNotifications', 'POST', {}, false);

    request.done(function(result) {

        console.log(result);

        $("#list-notification").html("");

        for(item of result.notifications)
        {
            $("#list-notification").append(formatNotification(item));
        }

        result.notifications_count == 0 ? $("#badge-unread-notification").hide() : $("#badge-unread-notification").show();

        $("#badge-unread-notification").html(result.notifications_count);

    }).fail(function(jqXHR, status) {
        console.log('ERROR');
    });
}

function readNotifications()
{
    let request = callAjax('readNotifications', 'POST', {}, false);

    request.done(function(result) {

        getNotifications();

    }).fail(function(jqXHR, status) {
        console.log('ERROR');
    });
}

function formatNotification(item)
{
    let color = (item.type == "Ticket" ? "success" : "info");
    let icon  = (item.type == "Ticket" ? "fa-clipboard-check " : "fa-broom");

    return  "<a data-item='" +  JSON.stringify(item) + "' class='d-flex justify-content-between item-notification' href='javascript:void(0)'>" +
                "<div class='media d-flex align-items-start'>" +
                        "<div class='media-left'><i class='fad " + icon + " font-medium-5 " + color + "'></i></div>" +
                        "<div class='media-body'>" +
                            "<h6 class='" + color + " media-heading'>" + item.title + "</h6><small class='notification-text'>" + item.message + "</small>" +
                        "</div>" +
                        "<small><time class='media-meta' datetime='2015-06-11T18:29:20+08:00'>" + moment(item.created_at).fromNow()  + "</time></small>" +
                "</div>" +
            "</a>";
}