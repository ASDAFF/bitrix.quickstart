<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
            <?
                if (!empty($arResult["ORDER"]))
                {
                ?>
                <div class="b-cart__list clearfix">
                    <div class="b-cart-field__info">
                        Вы заказали:<br /><b>4 товара на сумму 34 800.–</b>
                        <br /><br />
                        Информация о покупателе:<br />
                        <b>Физическое лицо</b><br />
                        <a href="mailto:email@mail.ru">email@mail.ru</a><br />
                        <b>Иванов Иван Иванович</b><br />
                        <b>+7 495 9873947</b>
                        <br /><br />
                        Адрес доставки:<br />
                        <b>Курьером по Москве</b><br />
                        <b>г. Москва, м. Белорусская, ул. </b><br />
                        <b>Вавилова, д.19 кв. 3</b><br />
                        <b>С 19:00 до 23:00</b><br />
                    </div>
                    <h2 class="b-thanks__h2">Ваш заказ принят,<br />в ближайшее время с Вами свяжется<br />оператор по указанному телефону</h2>
                </div>
                <?
                }
                else
                {
                ?>
                <b><?echo GetMessage("STOF_ERROR_ORDER_CREATE")?></b><br /><br />

                <table class="sale_order_full_table">
                    <tr>
                        <td>
                            <?=str_replace("#ORDER_ID#", $arResult["ORDER_ID"], GetMessage("STOF_NO_ORDER"))?>
                            <?=GetMessage("STOF_CONTACT_ADMIN")?>
                        </td>
                    </tr>
                </table>
                <?
                }
            ?>
