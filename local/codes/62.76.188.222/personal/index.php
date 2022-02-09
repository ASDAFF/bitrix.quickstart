<?php

define("NEED_AUTH", true); 
 
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Персональный раздел");
?>
<section class="b-detail">
        <div class="b-detail-content">
                <div class="b-checkout">В личном кабинете Вы можете проверить текущее состояние корзины, ход выполнения Ваших заказов, просмотреть или изменить личную информацию, а также подписаться на новости и другие информационные рассылки.</div>
                <div class="b-checkout m-checkout__last clearfix">
                        <div class="b-checkout__left">
                                <div class="b-checkout">
                                        <h3 class="b-h3 m-checkout__h3">Личная информация</h3>
                                        <p><a href="profile/">Изменить регистрационные данные</a></p>
                                        <p><a href="bonus/">Бонусные карты</a></p> 
                                </div>
                                <div class="b-checkout m-checkout__last">
                                        <h3 class="b-h3 m-checkout__h3">Подписка</h3>
                                        <p><a href="subscribe/">Изменить подписку</a></p>
                                </div>
                        </div>
                        <div class="b-checkout__right">
                                <div class="b-checkout">
                                        <h3 class="b-h3 m-checkout__h3">Заказы</h3>
                                        <p><a href="order/">Ознакомиться с состоянием заказов</a></p>
                                        <p><a href="cart/">Посмотреть содержимое корзины</a></p>
                                        <p><a href="order/?filter_history=Y">Посмотреть историю заказов</a></p>
                                </div>
                        </div> 
                </div>
        </div>
</section>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
