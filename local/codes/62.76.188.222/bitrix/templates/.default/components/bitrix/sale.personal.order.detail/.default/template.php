<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
 
function getPropVal_($id, $allProps){
  
    foreach($allProps as $val)
        if($val['ORDER_PROPS_ID'] == $id){
 
                return $val["VALUE"];    
      }
    
      return false;
}




?>
<?if(strlen($arResult["ERROR_MESSAGE"])<=0):?>
<h2 class="b-h2">Заказ №<?=$arResult["ID"]?>&nbsp;от <?=$arResult["DATE_INSERT"] ?></h2>
<div class="b-checkout">
    <table class="b-my_order-table m-width_100">
        <tbody><tr>
                <td class="b-my_order-table__title m-width_30">Текущий статус заказа:</td>
                <td><?=$arResult["STATUS"]["NAME"]?> (от <?=$arResult["DATE_STATUS"]?>)</td>
            </tr>
            <tr>
                <td class="b-my_order-table__title m-width_30">Сумма заказа:</td>
                <td><b><?
				echo "<b>".$arResult["PRICE_FORMATED"]."</b>";
				if (DoubleVal($arResult["SUM_PAID"]) > 0)
					echo " (".GetMessage("SPOD_ALREADY_PAID")."&nbsp;<b>".$arResult["SUM_PAID_FORMATED"]."</b>)";
				?> руб.</b></td>
            </tr>
            <tr>
                <td class="b-my_order-table__title m-width_30">Отменен:</td>
                <td><?
			echo (($arResult["CANCELED"] == "Y") ? GetMessage("SALE_YES") : GetMessage("SALE_NO"));
			if ($arResult["CANCELED"] == "Y")
			{
				echo GetMessage("SPOD_ORDER_FROM").$arResult["DATE_CANCELED"].")";
				if (strlen($arResult["REASON_CANCELED"]) > 0)
					echo "<br />".$arResult["REASON_CANCELED"];
			}
			elseif ($arResult["CAN_CANCEL"]=="Y")
			{
				?>&nbsp;<a href="<?=$arResult["URL_TO_CANCEL"]?>"><?=GetMessage("SALE_CANCEL_ORDER")?></a><?
			}
			?></td>
            </tr>
        </tbody></table>
</div>
	<?if (IntVal($arResult["USER_ID"])>0):?>
<div class="b-checkout">
    <h3 class="b-h3">Данные вашей учетной записи</h3>
    <table class="b-my_order-table m-width_100">
        <tbody><tr>
                <td class="b-my_order-table__title m-width_30">Учетная запись</td>
                <td><?=$arResult["USER_NAME"]?></td>
            </tr>
            <tr>
                <td class="b-my_order-table__title m-width_30">Логин:</td>
                <td><?=$arResult["USER"]["LOGIN"]?></td>
            </tr>
            <tr>
                <td class="b-my_order-table__title m-width_30">E-Mail адрес:</td>
                <td><a href="mailto:<?=$arResult["USER"]["EMAIL"]?>"><?=$arResult["USER"]["EMAIL"]?></a></td>
            </tr>
        </tbody></table>
</div><?endif;?>
 
<?if($arResult["PERSON_TYPE_ID"] == 1) {?> 
<div class="b-checkout">
    <h3 class="b-h3">Параметры заказа</h3>
    <div><b>Личные данные</b></div>
    <table class="b-my_order-table m-width_100">
        <tbody><tr>
                <td class="b-my_order-table__title m-width_30">Ф.И.О.:</td>
                <td><?=getPropVal_(1, $arResult["ORDER_PROPS"]);?></td>
            </tr>
            <tr>
                <td class="b-my_order-table__title m-width_30">E-Mail адрес:</td>
                <td><a href="<?=getPropVal_(2, $arResult["ORDER_PROPS"]);?>"><?=getPropVal_(2, $arResult["ORDER_PROPS"]);?></a></td>
            </tr>
            <tr>
                <td class="b-my_order-table__title m-width_30">Телефон:</td>
                <td><?=getPropVal_(3, $arResult["ORDER_PROPS"]);?></td>
            </tr>
        </tbody></table>
    <br>
    <div><b>Данные для доставки</b></div>
    <table class="b-my_order-table m-width_100">
        <tbody><tr>
                <td class="b-my_order-table__title m-width_30">Индекс:</td>
                <td><?=getPropVal_(4, $arResult["ORDER_PROPS"]);?></td>
            </tr>
            <tr>
                <td class="b-my_order-table__title m-width_30">Адрес доставки:</td>
                <td><?=getPropVal_(5, $arResult["ORDER_PROPS"]);?></td>
            </tr>
        </tbody></table>
</div>
<?} else {?>

<div class="b-checkout">
    <h3 class="b-h3">Параметры заказа</h3>
    <div><b>Личные данные</b></div>
    <table class="b-my_order-table m-width_100">
        <tbody><tr>
                <td class="b-my_order-table__title m-width_30">Ф.И.О.:</td>
                <td><?=getPropVal_(7, $arResult["ORDER_PROPS"]);?></td>
            </tr>
            <tr>
                <td class="b-my_order-table__title m-width_30">E-Mail адрес:</td>
                <td><a href="<?=getPropVal_(8, $arResult["ORDER_PROPS"]);?>"><?=getPropVal_(8, $arResult["ORDER_PROPS"]);?></a></td>
            </tr>
            <tr>
                <td class="b-my_order-table__title m-width_30">Телефон:</td>
                <td><?=getPropVal_(9, $arResult["ORDER_PROPS"]);?></td>
            </tr>
        </tbody></table>
    <br>
    <div><b>Данные для доставки</b></div>
    <table class="b-my_order-table m-width_100">
        <tbody><tr>
                <td class="b-my_order-table__title m-width_30">Индекс:</td>
                <td><?=getPropVal_(10, $arResult["ORDER_PROPS"]);?></td>
            </tr>
            <tr>
                <td class="b-my_order-table__title m-width_30">Адрес доставки:</td>
                <td><?=getPropVal_(11, $arResult["ORDER_PROPS"]);?></td>
            </tr>
        </tbody></table>
</div>
<?}?>
<div class="b-checkout">
    <h3 class="b-h3">Оплата и доставка</h3>
    <table class="b-my_order-table m-width_100">
        <tbody><tr>
                <td class="b-my_order-table__title m-width_30">Платежная система:</td>
                <td><?
			if (IntVal($arResult["PAY_SYSTEM_ID"]) > 0)
				echo $arResult["PAY_SYSTEM"]["NAME"];
			else
				echo GetMessage("SPOD_NONE");
			?></td>
            </tr>
            <tr>
                <td class="b-my_order-table__title m-width_30">Оплачен:</td>
                <td><?
			echo (($arResult["PAYED"] == "Y") ? GetMessage("SALE_YES") : GetMessage("SALE_NO"));
			if ($arResult["PAYED"] == "Y")
				echo GetMessage("SPOD_ORDER_FROM").$arResult["DATE_PAYED"].")";
			?></td>
            </tr>
            <tr>
                <td class="b-my_order-table__title m-width_30">Служба доставки:</td>
                <td><?
			if (strpos($arResult["DELIVERY_ID"], ":") !== false || IntVal($arResult["DELIVERY_ID"]) > 0)
			{
				echo $arResult["DELIVERY"]["NAME"];
			}
			else
			{
				echo GetMessage("SPOD_NONE");
			}
			?></td>
            </tr>
        </tbody></table>
</div>
<div class="b-composition_of_order">
    <h3 class="b-h3 m-checkout__h3">Состав заказа</h3>
    <table class="b-basket-table">
        <thead>
            <tr>
                <td>Название</td>
                <td>Свойства</td>
                <td>Скидка</td>
                <td>Вес</td>
                <td>Количество</td>
                <td>Цена</td>
            </tr>
        </thead>
        <tbody>
            
            <?
            foreach($arResult["BASKET"] as $val){
                ?>
            <tr>
                <td><?
                    if (strlen($val["DETAIL_PAGE_URL"])>0)
                            echo "<a href=\"".$val["DETAIL_PAGE_URL"]."\">";
                    echo htmlspecialcharsEx($val["NAME"]);
                    if (strlen($val["DETAIL_PAGE_URL"])>0)
                            echo "</a>";
                    ?></td>
                <td><?
							if(!empty($val["PROPS"])):?>
								<table cellspacing="0">
								<?
								foreach($val["PROPS"] as $vv) 
								{
										?>
										<tr>
											<td style="border:0px; padding:1px;"><?=$vv["NAME"]?>:</td>
											<td style="border:0px; padding:1px;"><?=$vv["VALUE"]?></td>
										</tr>
										<?
								}
								?>
								</table>
							<?endif;?>
                </td>
                <td><?=$val["DISCOUNT_PRICE_PERCENT_FORMATED"]?></td>
                <td><?=$val["WEIGHT_FORMATED"];?></td>
                <td><?=$val["QUANTITY"]?></td>
                <td><span class="b-price m-no_margin"><?=$val["PRICE_FORMATED"]?></span></td>
            </tr>
 <?}?>
        </tbody>
    </table>
    <div class="b-total-price clearfix">
        <div class="b-total-price-text">
            <table class="b-total-price__table">
                <tbody>
                    
                    
<?if(strlen($arResult["DISCOUNT_VALUE_FORMATED"]) > 0):?>
<tr><td class="b-total-price__title"><?=GetMessage("SPOD_DISCOUNT")?>:</td>
   <td class="b-total-price__value"><?=$arResult["DISCOUNT_VALUE_FORMATED"]?></td>
</tr>
<?endif;?> 

<tr><td class="b-total-price__title">Общий вес:</td>
   <td class="b-total-price__value"><?=$arResult["ORDER_WEIGHT_FORMATED"]?></td>
</tr>
 

<?
foreach($arResult["TAX_LIST"] as $val)
{
    ?>
    <tr>
     <td class="b-total-price__title"><?
                    echo $val["TAX_NAME"];
                    echo $val["VALUE_FORMATED"];
                    ?>:</td>
       <td class="b-total-price__value"><?=$val["VALUE_MONEY_FORMATED"]?></td>
    </tr>
    <?
}
?>
<?if(strlen($arResult["TAX_VALUE_FORMATED"]) > 0):?>
<tr>
    <td class="b-total-price__title"><?=GetMessage("SPOD_TAX")?>:</td>
    <td class="b-total-price__value"><?=$arResult["TAX_VALUE_FORMATED"]?></td>
</tr>
<?endif;?>
<?if(strlen($arResult["PRICE_DELIVERY_FORMATED"]) > 0):?>
<tr>
   <td class="b-total-price__title"><?=GetMessage("SPOD_DELIVERY")?>:</td>
   <td class="b-total-price__value"><?=$arResult["PRICE_DELIVERY_FORMATED"]?></td>
</tr>
<?endif;?>
<tr>
    <td class="b-total-price__title"><?=GetMessage("SPOD_ITOG")?>:</td>
   <td class="b-total-price__value"><span class="b-price m-no_margin"><?=$arResult["PRICE_FORMATED"]?></span></td>
</tr>                    
                    
                    
                    
                </tbody></table>
        </div>
    </div>
</div>
<?else:?>
	<?=ShowError($arResult["ERROR_MESSAGE"]);?>
<?endif;?>