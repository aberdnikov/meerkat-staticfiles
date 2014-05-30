MEERKAT.Modal = {
    id:0,
    fill: function (data) {
        $('#modal .modal-header h4').html(data.header);
        $('#modal .modal-body p').html(data.body);
        $('#modal .modal-footer').html(data.footer);
    },
    show_small: function (url, attrs) {
        this.show(url, attrs, 'modal-dialog-sm');

    },
    show_large: function (url, attrs) {
        this.show(url, attrs, 'modal-dialog-lg');

    },
    show: function (url, selector, id, modal_mode="") {
        MEERKAT.removeClassWithPrefix('#modal', 'js_');
        $('#modal').addClass(selector);
        if (modal_mode) {
            $('#modal .modal-dialog').removeClass('modal-dialog-lg modal-dialog-sm').addClass(modal_mode);
        }
        $.getJSON(url, function (data) {
            MEERKAT.Modal.fill(data);
            $('#modal').modal('show');
        });
    },
    hide: function () {
        $('#modal').modal('hide');
    }
};



var MeerkatPopup = {};
//заполнить попап
MeerkatPopup.fill = function (header, body, footer) {
}

//поднять попап
MeerkatPopup.show = function (url, callback) {
    $.getJSON(url, function (data) {
        MeerkatPopup.fill(data.header, data.body, data.footer);
        $('#modal').modal('show');
        if (callback) {
            callback.call();
        }
    });
}

//закрыть попап
MeerkatPopup.hide = function () {
    $('#modal').modal('hide');
}
