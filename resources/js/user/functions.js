//Filtramos los spots en base a los spots del usuario en sesión
getUserSpots = function getUserSpots()
{
    return window.global_spots.filter(spot => ($.inArray(spot.value, JSON.parse(window.user.spots)) != -1));
}

//Filtramos los spots en base a los spots del usuario en sesión y que sean sede.
getUserBranches = function getUserBranches()
{
    let spots = getUserSpots();
    return spots.filter(spot => spot.isbranch == true);
}

getBranches = function getBranches()
{
    return window.global_spots.filter(spot => spot.isbranch == true);
}

saveShortcut = function saveShortcut(shortcut)
{
    $.blockUI({ message: '<h1>Configurando favoritos...</h1>' });

    let request = callAjax('saveShortcut', 'POST', {'shortcut' : shortcut}, false);

    request.done(function(result) {
        $.unblockUI();
    }).fail(function(jqXHR, status) {
        $.unblockUI();
        console.log('ERROR');
    });
}

$("#btn-collapse").click(function() {
    let value = $(this).hasClass('rotate') ? "true" : "false";
    savePreferences("dashboardCollapse", value);
});

savePreferences = function savePreferences(preference, value)
{
    $.blockUI({ message: '<h1>Configurando...</h1>' });

    let request = callAjax('savePreferences', 'POST', {'preference' : preference, 'value' : value}, false);

    request.done(function(result) {
        $.unblockUI();
    }).fail(function(jqXHR, status) {
        $.unblockUI();
        console.log('ERROR');
    });
}
