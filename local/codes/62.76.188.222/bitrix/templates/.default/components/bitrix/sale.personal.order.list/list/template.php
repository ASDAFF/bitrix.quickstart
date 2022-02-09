<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if($_REQUEST["filter_canceled"] == "Y" && $_REQUEST["filter_history"] == "Y")
	$page = "canceled";
elseif($_REQUEST["filter_status"] == "F" && $_REQUEST["filter_history"] == "Y")
	$page = "completed";
elseif($_REQUEST["filter_history"] == "Y")
	$page = "all";
else
	$page = "active";
?> 
<div class="b-catalog-sort clearfix">
        <div class="b-catalog-sort-name">
                <span class="b-catalog-sort__text">Показать заказы:</span>
                <a class="b-catalog-sort__link<?if($page == "active") echo " b-catalog-sort__active"?>" href="?filter_history=N"><span>активные</span></a>
                <a class="b-catalog-sort__link<?if($page == "all") echo " b-catalog-sort__active"?>" href="?filter_history=Y"><span>все</span></a>
                <a class="b-catalog-sort__link<?if($page == "completed") echo " b-catalog-sort__active"?>" href="?filter_status=F&filter_history=Y"><span>выполненные</span></a>
                <a class="b-catalog-sort__link<?if($page == "canceled") echo " b-catalog-sort__active"?>" href="?filter_canceled=Y&filter_history=Y"><span>отмененные</span></a>
        </div>
</div>
<?
if($arResult["ORDER_BY_STATUS"]){
foreach ($arResult["ORDER_BY_STATUS"] as $key => $orderArr) {
    foreach ($orderArr as $id => $order) { 
        ?>  
        <section class="b-detail">
            <div class="b-detail-content clearfix">
                <div class="b-my_order__info">
                    <div class="b-my_order__link"><a href="<?=$order["ORDER"]["URL_TO_DETAIL"]?>">Заказ №<?= $order["ORDER"]['ID'] ?>&nbsp;от&nbsp;<?= $order["ORDER"]["DATE_INSERT"] ?></a></div>
                    <div class="b-my_order-table-wrapper clearfix">
                        <table class="b-my_order-table m-table-left">
                            <tbody><tr>
                                    <td class="b-my_order-table__title">Сумма к оплате:</td>
                                    <td><?= $order["ORDER"]["FORMATED_PRICE"] ?> руб</td>
                                </tr>
                                <tr>
                                    <td class="b-my_order-table__title">Оплачен:</td>
                                    <td>
                                        <? if ($order["ORDER"]["PAYED"] == "Y") { ?>
                                            Да
                                        <? } else { ?>
                                            Нет (<a href="/personal/order/make/?ORDER_ID=<?=$order["ORDER"]['ID'];?>">Перейти к оплате</a>)
                                        <? } ?>
                                    </td>
                                </tr>
                            </tbody></table>
                        <table class="b-my_order-table m-table-left">
                            <tbody><tr>
                                    <td class="b-my_order-table__title">Способ оплаты:</td>
                                    <td><?= $arResult["INFO"]["PAY_SYSTEM"][$order["ORDER"]["PAY_SYSTEM_ID"]]["NAME"]; ?></td>
                                </tr>
                            </tbody></table>
                    </div>
                    <table class="b-my_order-table">
                        <tbody><tr>
                                <td class="b-my_order-table__title">Состав заказа:</td>
                                <td> <? foreach ($order["BASKET_ITEMS"] as $item) { ?>
                                        <a href="<?= $item["DETAIL_PAGE_URL"] ?>"><?= $item['NAME']; ?></a><br>
                                     <? } ?>
                                </td>

                            </tr>
                        </tbody></table>
                </div>
                <div class="b-my_order__button">
                    <?
                    if($order["ORDER"]["CANCELED"] == 'Y'){
                    ?>  
                        <div class="b-my_order-status">Заказ отменён</div>
                        <div class="b-my_order-button"><button onclick="location.href='<?=$order["ORDER"]["URL_TO_COPY"]?>'" class="b-button m-grey m-width_100">повторить заказ</button></div>
                    <? 
                    } else 
                    switch ($key) {
                        case "N":
                            ?>
                            <div class="b-my_order-status m-wait">Принят, ожидается оплата</div> 
                            <?if ($order["ORDER"]["CAN_CANCEL"] == "Y"):?>
                                <div class="b-my_order-button"><button onclick="location.href='<?=$order["ORDER"]["URL_TO_CANCEL"]?>'" class="b-button m-red m-width_100">Отменить заказ</button></div>
                            <?endif;?>
                            <div class="b-my_order-button"><button onclick="location.href='<?=$order["ORDER"]["URL_TO_COPY"]?>'" class="b-button m-grey m-width_100">повторить заказ</button></div>
                            <?
                            break;
                        case 'F':                    
                            ?>  
                            <div class="b-my_order-status">Оплачен, формируется к отправке</div>
                            <div class="b-my_order-button"><button onclick="location.href='<?=$order["ORDER"]["URL_TO_COPY"]?>'" class="b-button m-grey m-width_100">повторить заказ</button></div>
                            <? 
                            break;
                        default:
                            break;
                    }
                    ?>
                </div>

            </div>
        </section>
        <?
    }
}
} else {
    ?>
    <p>Заказов не найдено</p>
    <?
} 