$(document).ready(function() {
    
    $(document).on("click", ".configcard", function(e) {

        e.preventDefault();

        let route = $(this).data("configlink");

        switch(route)
        {
            case 'config-tasktypes':
                window.location = "config-items?open=true";
                break;

            case 'config-spottypes':
                window.location = "config-spots?open=true";
                break;

            case 'config-teams':
                window.location = "config-users?open=true";
                break;

            case 'config-roles':
                window.location = "config-users?open=true";
                break;

            default:
                window.location = route;
                break;
        }
    });

});