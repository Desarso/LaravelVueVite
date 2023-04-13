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

            if (loader == true) {
                $.blockUI({ message: '<h1>' + locale('Wait') + '...</h1>' });
            }


        },
        success: function() {
            $.unblockUI();
        },
        error: function(e) {
            if (e.status == 419) {
                location.href = "/login";
            }
        }
    });

    return request;
};

$.fn.serializeFormJSON = function() {
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        if (o[this.name]) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};

showConfirmModal = function showConfirmModal(title, text) {
    var notice = PNotify.notice({
        title: title,
        text: text,
        icon: 'fas fa-question-circle',
        hide: false,
        stack: {
            'dir1': 'down',
            'modal': true,
            'firstpos1': 25
        },
        modules: {
            Confirm: {
                confirm: true
            },
            Buttons: {
                closer: false,
                sticker: false
            },
            History: {
                history: false
            },
        }
    });

    return notice;
}

function showConfirmDialog(title, text) {
    var notice = PNotify.notice({
        title: title,
        text: text,
        icon: 'fas fa-question-circle',
        hide: false,
        modules: {
            Confirm: {
                confirm: true
            },
            Buttons: {
                closer: false,
                sticker: false
            },
            History: {
                history: false
            }
        }
    });

    return notice;
}