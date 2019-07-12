
    <div id="notifyme" class="accordion" style="display:none;">
        <div class="not-available">
            <div class="extremum-slide" id="box" style="display: none;">
                <div class="wrap-extremum-slide">
                    <button class="close" type="button">&times;</button>
                    <h3>На данный момент товара нет на складе</h3>
                    Вы можете подписаться на уведомление о поступлении товара.
                </div>
                <div id="triangle-down"></div>
            </div>

            <div id="signed" class="accordion-heading" style="display:none;">
                <div id="notifyme-response" class="wr">Вы подписаны на уведомление о появлении товара на складе.</div>
                <span id="unsubsc" class="btnclick btn not-avi">Не уведомлять о появлении</span>
            </div>
            <div id="notifyme-form" style="display:none;">
                <?
                if(CUser::IsAuthorized())
                {
                    ?>
                    <form id="notifyForm" method="post" name="notifyForm" action="/local/components/novagr.shop/catalog.list/templates/.default/ajax.php">
                        <input type="hidden" value="" id="notify_elem_id" name="elemId" >
                        <input type="hidden" value="Y" name="ajax" >
                        <input type="hidden" value="productSubsribe" name="action" >
                        <?=bitrix_sessid_post()?>
                        <input type="hidden" id="notify_user_mail" autocomplete="on" value="<?=CUser::GetEmail();?>" name="user_mail">
                        <input type="submit" class="btn bt3 not-extremum" value="Уведомить о появлении">
                        <!--a href="#collapseTwo" data-parent="#accordion2" data-toggle="collapse" class="btn bt3 not-extremum">Уведомить о появлении</a-->
                    </form>
                <?
                }else{
                    ?>
                    <div class="accordion-heading">
                        <a href="#collapseTwo" data-parent="#accordion2" data-toggle="collapse" class="btn bt3 not-extremum">Уведомить о появлении</a>
                    </div>

                    <div class="accordion-body collapse" id="collapseTwo">
                        <div class="accordion-inner">
                            <form id="notifyForm" method="post" name="notifyForm" action="/local/components/novagr.shop/catalog.list/templates/.default/ajax.php">
                                <input type="hidden" value="" id="notify_elem_id" name="elemId" >
                                <input type="hidden" value="Y" name="ajax" >
                                <input type="hidden" value="productSubsribe" name="action" >
                                <?=bitrix_sessid_post()?>
                                <div class="notice">
                                    <div class="login" id="notifyEmail">
                                        <div class="">Введите e-mail для уведомления:<span class="starrequired">*</span></div>
                                        <div class="value">
                                            <input type="text" id="notify_user_mail" autocomplete="on" value="" maxlength="50" name="user_mail">
                                            <input type="submit" class="btn" value="Отправить">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                <?
                }
                ?>
            </div>

        </div>

    </div>

