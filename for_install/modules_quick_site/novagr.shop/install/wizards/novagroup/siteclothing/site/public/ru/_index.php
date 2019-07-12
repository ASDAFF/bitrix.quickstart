<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Интернет магазин одежды");
$APPLICATION->SetPageProperty("NOT_SHOW_NAV_CHAIN", "Y");
?>
<?$APPLICATION->IncludeComponent("novagroup:main.banners", ".default", array(
	"BANNER_IBLOCK_TYPE" => "banners",
	"BANNER_IBLOCK_ID" => "#BANNERS_ID#",
	"ELEMENT_ID" => "",
	"ELEMENT_CODE" => "banners-on-main",
	"SORT_FIELD" => "SORT",
	"SORT_BY" => "DESC",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "3600"
	),
	false
);?>
<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>