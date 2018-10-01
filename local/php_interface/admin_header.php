<?php
/**
 * Created by PhpStorm.
 * User: ASDAFF
 * Date: 01.10.2018
 * Time: 19:55
 */
?>
<script>
    // опишем всплывающее окно (средствами Битрикса)
    var Dialog = new BX.CDialog({
        title: "Все заказы пользователя",
        content: '<div id="all_orders"></div>', // в этот div будем вставлять инфу, полученную функцией orders_ms()
        icon: 'head-block',
        resizable: true,
        draggable: true,
        height: '400',
        width: '600',
        buttons: [BX.CDialog.btnClose]
    });

    // теперь функция
    function orders_ms(id) {
// теперь в ajax-е обратимся к странице, на которой выполним нужный нам код и отправим письмо
        BX.ready(function () {
            BX.ajax({
                method: 'GET',
                dataType: 'html',
                url: '/bitrix/admin/all_orders.php?id='+id,
                data: { id: id },
                onsuccess: function(data){
                    BX('all_orders').innerHTML = data;
                    Dialog.Show(); // вызвали окно, которое описано выше
                },
                onfailure: function(){
                    alert('Возникла ошибка');
                }
            });
        })
    }
</script>

<script>
    // опишем всплывающее окно (средствами Битрикса)
    var Dialog = new BX.CDialog({
        title: "Отправить письмо",
        content: '<form method="POST" style="overflow:hidden;" action="button.php" id="mailform">\
 <textarea name="comment" id="comment" style="height: 78px; width: 374px;">Пишем текст сюда</textarea><br><br>\
 <input type="checkbox" name="sostav" id="sostav"><label for="sostav">Отправлять состав заказа</label>\
 </form>',
        icon: 'head-block',
        resizable: true,
        draggable: true,
        height: '200',
        width: '400',
        buttons: ['<input type="submit" class="btnSubmit" value="Отправить" />', BX.CDialog.btnCancel, BX.CDialog.btnClose]
    });

    // теперь функция
    function mail_ms(id) {
        Dialog.Show(); // вызвали окно

// теперь в ajax-е обратимся к странице, на которой выполним нужный нам код и отправим письмо
        $('.btnSubmit').on('click', function () {
            var comment = $('#comment').val();
            if($('#sostav').is(":checked")) {var sostav = 'on';} else {var sostav = 'off';}

            $.ajax({
                type: 'GET',
                url: '/bitrix/admin/button.php',
                data: { id: id, comment: comment, sostav: sostav },
                success: function(data) {
// в случае успеха закроем окно
                    Dialog.Close();
// и перезагрузим страницу
                    location='<?=$_SERVER["REQUEST_URI"]?>';
                },
                error: function(xhr, str){
                    alert('Возникла ошибка: ' + xhr.responseCode);
                }
            });
        })

    }
</script>