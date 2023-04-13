$(document).ready(function() {

    let preferences = JSON.parse(window.user.preferences);

    console.log(preferences);

    if(preferences != null)
    {
        $("input[name='theme'][value='" + preferences.theme + "']").prop('checked', true);
        let collapsed = preferences.sidebarCollapsed ? "true" : "false";
        $("input[name='sidebarCollapsed'][value='" + collapsed + "']").prop('checked', true);
    }
    else
    {
        $("input[name='theme'][value='light']").prop('checked', true);
        $("input[name='sidebarCollapsed'][value='false']").prop('checked', true);
    }

    $('#form-profile input').jqBootstrapValidation({
        preventSubmit: true,
        submitSuccess: function($form, event){     
            event.preventDefault();
            var data = $("#form-profile").serializeArray();
            saveProfile(data);
        },
    });

    $('#form-security input').jqBootstrapValidation({
        preventSubmit: true,
        submitSuccess: function($form, event){    
           event.preventDefault();
           $("#modal-confirm-password").modal("show");
        },
    });

    $("#btnChangePassword").click(function() {
        var data = $("#form-security").serializeArray();
        data.push({'name': 'currentPassword', 'value' : $("#confirm-password").val() });
        changePassword(data);
    });

    $("#user-photo").fileinput({
        initialPreview: [user.urlpicture],
        initialPreviewAsData: true,
        initialPreviewConfig: [
            {caption: "Perfil.jpg", downloadUrl: user.urlpicture, width: "120px", key: 1}
        ],
        theme: 'fa',
        language: 'es',
        uploadUrl: '#',
        actionUpload: false,
        showRemove: false,
        showUpload: false,
        showUploadedThumbs: false,
        dropZoneEnabled: false,
        overwriteInitial: true,
        mainClass: "input-group-md",
        allowedFileExtensions: ['jpg', 'jpeg', 'png', 'gif'],
        fileActionSettings: { 
            showUpload: false,
            showRemove: false, 
        }
    });

    
    $("#form-user-photo" ).on('submit', function (e){

        e.preventDefault();
        var token = $("input[name=_token]").val();
        
        $.ajax({
                url:"changePhoto",
                headers:{'X-CSRF-TOKEN':token},
                type:'POST',
                datatype: 'json',
                data: new FormData(this),
                contentType: false,
                processData: false,
                success:function(data)
                {
                    //getPNotify('Información', 'Perfil actualizado', 'success');

                    setTimeout(function(){
                        location.reload();
                    }, 3000); 
                    
                },
                error:function(data)
                {
                }  
            })
    });
    
});


function saveProfile(data)
{
    let request = callAjax('saveProfile', 'POST', data, true);

    request.done(function(result) {

        if(result.success)
        {
            PNotify.success({ title: 'Acción completada con éxito', text: 'Perfil Editado' });
            location.reload();
        }
        else
        {
            toastr.error(result.message, 'Permisos');
        }

    }).fail(function(jqXHR, status) {
        toastr.error('La acción no se puedo completar', 'Hubo un problema!');
    });
}

function changePassword(data)
{
    let request = callAjax('changePassword', 'POST', data, true);

    request.done(function(result) {

        $("#confirm-password").val("");

        if(result.success)
        {
            $("#modal-confirm-password").modal("hide");
            PNotify.success({ title: 'Acción completada con éxito', text: 'Contraseña modificada' });
            $("#form-security").trigger("reset");
        }
        else
        {
            PNotify.error({ title: 'Cambio de contraseña', text: '¡La contraseña actual es incorrecta!' });
        }

    }).fail(function(jqXHR, status) {
        toastr.error('La acción no se puedo completar', 'Hubo un problema!');
    });
}

//////////////////////////////////////////////////////////////////
//FUNCIONES PARA CAMBIAR EL THEMA EN LA BASE DE DATOS

//CONSTANTE CON EL ICONO
const iconTheme = document.querySelector('#icon-mode');
const darkSwitchChange = document.getElementById("darkSwitch");
let preferences = JSON.parse(window.user.preferences);
const formData = new FormData();

$(document).ready(function () {
    if (preferences.theme == "dark") {
        iconTheme.classList.remove("fa-moon-o");
        iconTheme.classList.add("fa-sun-o");
        formData.append('theme', 'light');
    } else {
        iconTheme.classList.remove("fa-sun-o");
        iconTheme.classList.add("fa-moon-o");
        formData.append('theme', 'dark');
    }
});


darkSwitchChange.addEventListener("click", function () {
    var token = $("input[name=_token]").val();
    $.ajax({
        url: "changeDarkMode",
        headers: { 'X-CSRF-TOKEN': token },
        type: 'POST',
        datatype: 'json',
        data: formData,
        contentType: false,
        processData: false,
        success: function (data) {
            PNotify.success({ title: 'Tema Cambiado', text: 'Perfil Editado' });
            setTimeout(function () {
                location.reload();
            }, 1500);

        },
        error: function (data) {
        }
    })
});
