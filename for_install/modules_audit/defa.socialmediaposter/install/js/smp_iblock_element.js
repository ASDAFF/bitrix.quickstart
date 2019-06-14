/**
 * DEFA SocialMediaPoster
 */

if (typeof DEFA === 'undefined') {
    var DEFA = {};
}

DEFA.SMP = {};

DEFA.SMP.AJAX = {

    get_socnet_button: function (name) {
        return this.get_socnet_row(name).querySelector('.defa-smp-but');
    },

    get_socnet_status_info: function (name) {
        return this.get_socnet_row(name).querySelector('.defa-status-info');
    },

    get_socnet_row: function (name) {
        return document.querySelector('.defa__smp__iblock_element_edit [data-socnet-row=' + name + ']');
    },

    success_callback: function (data) {

    	BX.adminPanel.closeWait(DEFA.SMP.AJAX.get_socnet_button(data.socnet));

        if (data.status === 'error' || data.socnet.length === 0) {
            return DEFA.SMP.AJAX.custom_error_callback(data);
        }

        var info = DEFA.SMP.AJAX.get_socnet_status_info(data.socnet);

        info.innerHTML = BX.message('SOCIALMEDIAPOSTER_AJAX_JS_PUBLISHED');
        BX.addClass(info, 'defa-success');

        return true;
    },

    custom_error_callback: function (data) {

    	BX.adminPanel.closeWait(DEFA.SMP.AJAX.get_socnet_button(data.socnet));

    	var info = DEFA.SMP.AJAX.get_socnet_status_info(data.socnet),
            a = document.createElement("a");

        BX.addClass(info, 'defa-error');

        a.setAttribute("href", "/bitrix/admin/event_log.php?find_audit_type%5B0%5D=SOCIALMEDIAPOSTER_" + data.socnet.toUpperCase());
        a.setAttribute("target", "_blank");
        a.appendChild(document.createTextNode(data.message));

        BX.cleanNode(info);
        info.appendChild(a);

        return false;
    },

    element_click_handler: function () {

        var socnet = BX.findParent(this, {tag: 'tr'}).getAttribute('data-socnet-row'),
            info = DEFA.SMP.AJAX.get_socnet_status_info(socnet),
            button,
            els;

        if (socnet === 'all') {
            els = document.querySelectorAll('.defa__smp__iblock_element_edit.adm-detail-toolbar input[type="checkbox"]');

            for (var i = 0; i < els.length; i++) {
            	if (els[i].checked !== true) continue;

                button = DEFA.SMP.AJAX.get_socnet_button(els[i].value);
                BX.fireEvent(button, 'click');
            }
            return true;
        }

        info.innerHTML = BX.message('SOCIALMEDIAPOSTER_AJAX_JS_SENDING');
        BX.adminPanel.showWait(this);
        BX.removeClass(info, "defa-success defa-error");

        BX.ajax({
            'method': 'POST',
            'dataType': 'json',
            'url': __defa_ajax_post_link,
            'data': {
                _defa_action: 'smp',
                socnet: socnet
            },
            'onsuccess': DEFA.SMP.AJAX.success_callback
        });

        //BX.unbind(this, 'click', DEFA.SMP.AJAX.element_click_handler);
        return true;
    }

};

BX.ready(function () {
    var buttons = document.querySelectorAll('button.adm-btn.defa-smp-but, a.adm-btn.defa-smp-but-all'),
        socnet,
        i;

    for (i = 0; i < buttons.length; i++) {
        socnet = BX.findParent(buttons[i], {tag: 'tr'}).getAttribute('data-socnet');
        BX.bind(buttons[i], 'click', DEFA.SMP.AJAX.element_click_handler);
    }

});
