<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>
<div class="subscribe_wrap">
	<?$APPLICATION->IncludeComponent(
		"bitrix:subscribe.form", 
		"main", 
		array(
			"AJAX_MODE" => "N",
			"SHOW_HIDDEN" => "N",
			"ALLOW_ANONYMOUS" => "Y",
			"SHOW_AUTH_LINKS" => "N",
			"CACHE_TYPE" => "A",
			"CACHE_TIME" => "3600000",
			"CACHE_NOTES" => "",
			"SET_TITLE" => "N",
			"AJAX_OPTION_JUMP" => "N",
			"AJAX_OPTION_STYLE" => "Y",
			"AJAX_OPTION_HISTORY" => "N",
			"AJAX_OPTION_ADDITIONAL" => "",
			"LK" => "Y",
			"COMPONENT_TEMPLATE" => "main",
			"USE_PERSONALIZATION" => "Y",
			"PAGE" => SITE_DIR."personal/subscribe/",
			"URL_SUBSCRIBE" => SITE_DIR."subscribe/"
		),
		false
	);?>
</div>