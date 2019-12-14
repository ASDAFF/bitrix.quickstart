<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

?><div class="orderlist"><?
if(!empty($arResult['ERRORS']['FATAL']))
{
	foreach($arResult['ERRORS']['FATAL'] as $error)
	{
		?><?=ShowError($error)?><?
	}
} else {
	if(!empty($arResult['ERRORS']['NONFATAL']))
	{
		foreach($arResult['ERRORS']['NONFATAL'] as $error)
		{
			?><?=ShowError($error)?><?
		}
	}
	?><div class="switch clearfix"><?
		$nothing = empty($_REQUEST["show_all"]) && empty($_REQUEST["filter_history"]);
		?><a<?if($_REQUEST['show_all']=='Y'):?> class="selected"<?endif;?> href="<?=$arResult['CURRENT_PAGE']?>?show_all=Y"><?=GetMessage('SPOL_CUSTOM_ORDERS_ALL')?></a><?
		?><div class="separator"></div><?
		?><a<?if($_REQUEST['filter_history']=='N' || $nothing):?> class="selected"<?endif;?> href="<?=$arResult['CURRENT_PAGE']?>?filter_history=N"><?=GetMessage('SPOL_CUSTOM_CUR_ORDERS')?></a><?
		?><div class="separator"></div><?
		?><a<?if($_REQUEST['filter_history']=='Y'):?> class="selected"<?endif;?> href="<?=$arResult['CURRENT_PAGE']?>?filter_history=Y"><?=GetMessage('SPOL_CUSTOM_ORDERS_HISTORY')?></a><?
	?></div><?
	
	if(!empty($arResult['ORDERS']))
	{
		foreach($arResult['ORDER_BY_STATUS'] as $key => $group)
		{
			foreach($group as $k => $order)
			{
				?><a class="item" href="<?=$order['ORDER']['URL_TO_DETAIL']?>"><?
					?><div class="clearfix"><?
						?><div class="namedate"><?
							?><span class="name"><?=GetMessage('SPOL_ORDER')?> <?=GetMessage('SPOL_NUM_SIGN')?><?=$order['ORDER']['ID']?></span> <?
							?><span class="date"><?=GetMessage('SPOL_FROM')?> <?=$order["ORDER"]["DATE_INSERT_FORMATED"];?></span><?
						?></div><?
						?><div class="float"><?
							?><span class="status"><?=$arResult['INFO']['STATUS'][$key]['NAME']?></span><?
							?><span class="price"><?=$order['ORDER']['FORMATED_PRICE']?></span><?
						?></div><?
					?></div><?
					?><div class="products"><?
						foreach($order['BASKET_ITEMS'] as $item)
						{
							?><?=$item['NAME']?> - <?=$item['QUANTITY']?> <?=(isset($item["MEASURE_NAME"]) ? $item["MEASURE_NAME"] : GetMessage('SPOL_SHT'))?><br /><?
						}
					?></div><?
				?></a><?
			}
		}
		if(strlen($arResult['NAV_STRING']))
		{
			?><?=$arResult['NAV_STRING']?><?
		}
	} else {
		?><?=GetMessage('SPOL_NO_ORDERS')?><?
	}
}
?></div>