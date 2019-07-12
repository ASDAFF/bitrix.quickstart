var pSubscribe = {
    messages: [],
    init: function (messages) {
        var self = this;
        self.messages = messages;

// handlers for product subcribe
        var options = {
            beforeSubmit: self.checkNotifyForm,
            success: function (responseText) {
                var rs = eval('(' + responseText + ')');
                var error = '';
                if (rs["STATUS"] == "N") {

                    if (rs['ERRORS'] == 'NOTIFY_ALREADY_SUBSCRIBED') error = self.messages.NOTIFY_YOU_ARE_SUBSCRIBE;
                    else if (rs['ERRORS'] == 'NOTIFY_ERR_LOGIN') error = self.messages.NOTIFY_ERR_LOGIN;
                    else if (rs['ERRORS'] == 'NOTIFY_ERR_MAIL') error = self.messages.NOTIFY_ERR_MAIL;
                    else if (rs['ERRORS'] == 'NOTIFY_ERR_CAPTHA') error = self.messages.NOTIFY_ERR_CAPTHA;
                    else if (rs['ERRORS'] == 'NOTIFY_ERR_MAIL_EXIST') error = self.messages.NOTIFY_ERR_MAIL_EXIST;
                    else if (rs['ERRORS'] == 'NOTIFY_ERR_REG') error = self.messages.NOTIFY_ERR_REG;
//alert(error);

                    if (error != '') {
                        $('#notifyme-response').html(error);
                        $('#signed').show();
                        $('#notifyme-response').show();
                        $('#box').hide();
                        $('#notifyme-form').show();
                        $('#unsubsc').hide();
                    } else {
                        $('#notifyme-response').html(self.messages.NOTIFY_YOU_ARE_SUBSCRIBE);
                        $('#signed').show();
                        $('#notifyme-response').show();
                        $('#box').hide();
                        $('#notifyme-form').hide();
                        $('#notifyme-form').hide();
                        $('#unsubsc').show();
                    }


                } else if (rs["STATUS"] == "Y") {

                    $('#notifyme-response').html(self.messages.NOTIFY_YOU_ARE_SUBSCRIBE);
                    $('#signed').show();
                    $('#notifyme-response').show();
                    $('#box').hide();
                    $('#notifyme-form').hide();
                    $('#notifyme-form').hide();
                    $('#unsubsc').show();
// для списка
                    $("#btn_" + rs["elemId"]).html(self.messages.NOTIFY_SUBSCRIBED).removeClass("authNotify").removeClass("notify");
// для попап окна
//$("#btn2_"+rs["elemId"]).html("Подписан").removeClass("authNotify").removeClass("authNotifySize");

                    $("#myModal-notify").modal("hide");
                    /*
                     if (jQuery.inArray(rs["elemId"], cp.obj.offersSubsribed) == -1) {
                     //var mess = 'Уведомить о появлении';
                     cp.obj.offersSubsribed.push(rs["elemId"]);
                     } else {
                     cp.obj.offersSubsribed.push(rs["elemId"]);
                     }*/
//makeUserProductSubscribe(rs['elemId']);
                }
            }
        }
        $('#notifyForm').ajaxForm(options);

        $(".notify").live('click', function () {

            var elemId = $(this).data("elem-id");
            $("#notify_elem_id").val(elemId);
            $("#myModal-notify").modal("show");
            return false;
        });
        $(".authNotify").live('click', function () {
            var elemId = $(this).data("elem-id");
            self.makeUserProductSubscribe(elemId);
            return false;
        });
// subscription on click on sizes
        $(".authNotifySize").live('click', function () {

            var elemId = $(this).data("elem-id");
            $("#notify_elem_id").val(elemId);
            $("#notify_user_mail").val(product.userEmail);
            $("#notifyEmail").hide();

            $("#myModal-notify").modal("show");
            return false;
        });
    },
    makeUserProductSubscribe: function (elemId) {
        $.ajax({
            type: "POST",
            url: "/local/components/novagr.shop/catalog.list/templates/.default/ajax.php",
            data: {    'elemId': elemId, 'action': 'productSubsribe' },
            dataType: "JSON",
            success: function (responseText) {

                if (responseText.result == 'OK') {

                    $("#btn_" + responseText.elemId).html(pSubscribe.messages.NOTIFY_SUBSCRIBED).removeClass("authNotify").removeClass("authNotifySize");
                }
            }
        });
    },
    CheckEmail: function (email) {
        var reg_e = /^[0-9\.a-z_\-]+@[0-9a-z_\-^\.]+\.[a-z]{2,6}$/i;
        if (email != '') {
            if (!reg_e.test(email)) return false;
        }
        return true;
    },
    checkNotifyForm: function () {

        var error_str = '';
        var error = Array();
        var user_mail = document.getElementById('notify_user_mail');
        if (user_mail.value == '') {
            error.push(pSubscribe.messages.NOTIFY_EMAIL_WRING1);
        } else if (!pSubscribe.CheckEmail(user_mail.value)) {
            error.push(pSubscribe.messages.NOTIFY_EMAIL_WRING2);
        }

        var str_error = error.join("\n");
        if (str_error != '') {
            alert(str_error);
            return false;
        }
    }
};
