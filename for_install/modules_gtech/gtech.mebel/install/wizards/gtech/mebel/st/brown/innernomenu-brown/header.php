<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
IncludeTemplateLangFile(__FILE__);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=LANGUAGE_ID?>" lang="<?=LANGUAGE_ID?>">
<head>
<link rel="shortcut icon" type="image/x-icon" href="<?=SITE_TEMPLATE_PATH?>/favicon.ico" />
<script type="text/javascript" src="http://code.jquery.com/jquery-latest.js"></script>
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/jquery/fancybox/jquery.fancybox-1.3.1.pack.js"></script>
<link rel="stylesheet" type="text/css" href="<?=SITE_TEMPLATE_PATH?>/jquery/fancybox/jquery.fancybox-1.3.1.css" />
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/jquery/jquery-ui/js/jquery-ui-1.8.23.custom.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?=SITE_TEMPLATE_PATH?>/jquery/jquery-ui/css/blitzer/jquery-ui-1.8.23.custom.css" />
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/jquery/jquery.cookie.js"></script>
<?$APPLICATION->ShowHead();?>
<title><?$APPLICATION->ShowTitle()?></title>
</head>

<body>
<script type="text/javascript">
$(document).ready(function() {
    $('a#link').fancybox({
        'transitionIn': 'elastic',
        'transitionOut': 'elastic',
        'speedIn': 600,
        'speedOut': 400,
        'overlayShow': false,
        'cyclic' : true,
        'padding': 20,
        'titlePosition': 'over',
		'showTitle': 'none',
        'onComplete': function() {
            $("#fancybox-title").css({ 'top': '100%', 'bottom': 'auto' });
        }
    });
});
</script>
<?$APPLICATION->ShowPanel();?>

<?CModule::IncludeModule("iblock");
$rsCatalogID = CIBlock::GetList(array(),array("CODE"=>"catalog"))->Fetch();
$CatalogID = $rsCatalogID["ID"];
$rsNewsID = CIBlock::GetList(array(),array("CODE"=>"news"))->Fetch();
$NewsID = $rsNewsID["ID"];
$rsColorschemeID = CIBlock::GetList(array(),array("CODE"=>"colorscheme"))->Fetch();
$ColorschemeID = $rsColorschemeID["ID"];
$rsOffersID = CIBlock::GetList(array(),array("CODE"=>"offers"))->Fetch();
$OffersID = $rsOffersID["ID"];
?>

<div class="header">
	<div class="header-inner">
		<?$APPLICATION->IncludeComponent("bitrix:sale.basket.basket.small", "aero-basket", array(
	"PATH_TO_BASKET" => "/personal/cart/",
	"PATH_TO_ORDER" => "/personal/order/make/"
	),
	false
);?>
		<div class="logo"><a href="/"><img src="<?=SITE_TEMPLATE_PATH?>/images/logo.png" border="0"></a></div>
		<div class="header-address">
			<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array(
	"AREA_FILE_SHOW" => "file",
	"PATH" => "/includes/header-address.php",
	"EDIT_TEMPLATE" => ""
	),
	false
);?>
		</div>
		<div class="header-feedback"><?$APPLICATION->IncludeComponent("bitrix:main.feedback", "popup1", array(
	"USE_CAPTCHA" => "Y",
	"OK_TEXT" => "Спасибо, ваше сообщение принято.",
	"EMAIL_TO" => "sales@mebel.g-tech.su",
	"REQUIRED_FIELDS" => array(
	),
	"EVENT_MESSAGE_ID" => array(
	)
	),
	false
);?>
		</div>
		<div class="header-authform">
			<?$APPLICATION->IncludeComponent("bitrix:main.feedback", "auth_form", array(),	false);?>
		</div>
	</div>
</div>
<div class="mainmenu"><div class="mainmenu-inner">
	<?$APPLICATION->IncludeComponent("bitrix:menu", "mainmenu", Array(
	"ROOT_MENU_TYPE" => "top",	// Тип меню для первого уровня
	"MENU_CACHE_TYPE" => "A",	// Тип кеширования
	"MENU_CACHE_TIME" => "3600",	// Время кеширования (сек.)
	"MENU_CACHE_USE_GROUPS" => "Y",	// Учитывать права доступа
	"MENU_CACHE_GET_VARS" => "",	// Значимые переменные запроса
	"MAX_LEVEL" => "1",	// Уровень вложенности меню
	"CHILD_MENU_TYPE" => "left",	// Тип меню для остальных уровней
	"USE_EXT" => "N",	// Подключать файлы с именами вида .тип_меню.menu_ext.php
	"DELAY" => "N",	// Откладывать выполнение шаблона меню
	"ALLOW_MULTI_SELECT" => "N",	// Разрешить несколько активных пунктов одновременно
	),
	false
);?>
</div></div>
<div class="breadcrumb"><?$APPLICATION->IncludeComponent("bitrix:breadcrumb", ".default", array(
	"START_FROM" => "1",
	"PATH" => "",
	"SITE_ID" => "-"
	),
	false
);?></div>
<div class="main">
	<h1><?$APPLICATION->ShowTitle();?></h1>