<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();?>
<?$APPLICATION->IncludeComponent(
	"bitrix:advertising.banner", 
	"owl", 
	array(
		"COMPONENT_TEMPLATE" => "owl",
		"TYPE" => "ADV_HOME_TOP",
		"NOINDEX" => "N",
		"QUANTITY" => "3",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "0",
		"SLIDER_AUTOPLAY" => "Y",
		"SLIDER_ITEMS" => "3",
		"SLIDER_CENTER" => "N",
		"SLIDER_LAZYLOAD" => "N",
		"SLIDER_LOOP" => "Y",
		"SLIDER_SMARTSPEED" => "2000",
		"SLIDER_ANIMATEIN" => "fadeIn",
		"SLIDER_ANIMATEOUT" => "fadeOut",
		"SLIDER_AUTOPLAY_SPEED" => "2000",
		"SLIDER_AUTOPLAY_TIMEOUT" => "5000"
	),
	false
);?>