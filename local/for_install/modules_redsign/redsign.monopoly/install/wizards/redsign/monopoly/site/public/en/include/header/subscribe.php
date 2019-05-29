<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?if(IsModuleInstalled('subscribe')):?>

<?$APPLICATION->IncludeComponent(
	"bitrix:subscribe.form", 
	"footer", 
	array(
		"COMPONENT_TEMPLATE" => "footer",
		"USE_PERSONALIZATION" => "Y",
		"SHOW_HIDDEN" => "N",
		"PAGE" => SITE_DIR."about/press_center/subscribe/",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600"
	),
	false
);?>

<?endif;?>
