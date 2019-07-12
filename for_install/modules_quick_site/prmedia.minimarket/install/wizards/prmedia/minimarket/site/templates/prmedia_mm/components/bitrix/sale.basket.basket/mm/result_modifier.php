<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>

<?php

foreach ($arResult['ITEMS']['AnDelCanBuy'] as $k => $item)
{
	$id = $item['ID'];

	// delete link
	$item['DELETE_LINK'] = $APPLICATION->GetCurPageParam("action=delete&id=$id");
	$arResult['ITEMS']['AnDelCanBuy'][$k] = $item;
}