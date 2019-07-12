<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><!--start_html-->
<?
	// вызовем шаблон для элемента или списка каталога
	if( count($arResult['ELEMENT']) == 0 )
	{
?>
<div class="not-round link-mas">
	<h3><?=GetMessage("NOTHING_FOUND")?></h3>
<?
$APPLICATION->IncludeComponent(
	"bitrix:menu",
	"notfound_catalog",
	Array(
		"ROOT_MENU_TYPE" => "top",
		"MAX_LEVEL" => "1",
		"CHILD_MENU_TYPE" => "left",
		"USE_EXT" => "N",
		"DELAY" => "N",
		"ALLOW_MULTI_SELECT" => "N",
		"MENU_CACHE_TYPE" => "Y",
		"MENU_CACHE_TIME" => "3600",
		"MENU_CACHE_USE_GROUPS" => "Y",
		"MENU_CACHE_GET_VARS" => array()
	)
);
?>
</div>
<?
	}else{
		if(!empty($arResult['CUR_ELEMENT_CODE']))
			require_once("inc.element.php");
		else
			require_once("inc.elements.php");
	}
?>
<!--end_html-->