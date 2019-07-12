<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
IncludeTemplateLangFile(__FILE__);?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<!-- <?=GetMessage("DVS_COP")?> -->
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=LANGUAGE_ID?>" lang="<?=LANGUAGE_ID?>">
<head>
    <title><?$APPLICATION->ShowTitle()?></title>
    <link rel="shortcut icon" type="image/x-icon" href="<?=SITE_TEMPLATE_PATH?>/favicon.ico" />
    <script src="<?=SITE_TEMPLATE_PATH?>/j/jquery-1.7.min.js"></script>
    <script src="<?=SITE_TEMPLATE_PATH?>/j/jquery-ui-1.8.16.custom.min.js"></script>
    <script src="<?=SITE_TEMPLATE_PATH?>/j/jquery.selectBox.js"></script>
    <script src="<?=SITE_TEMPLATE_PATH?>/j/products.js"></script>
	<script src="<?=SITE_TEMPLATE_PATH?>/j/encode_form_field_utf8_detect.js"></script>
	<script>
	if (encodeFormFieldIsPageOnUTF8()){
		document.write('<'+'script src="<?=SITE_TEMPLATE_PATH?>/j/encode_form_field_utf8_stub.js"></'+'script>');
	}else{
		document.write('<'+'script src="<?=SITE_TEMPLATE_PATH?>/j/encode_form_field.js"></'+'script>');
		window.encodeURIComponent = function(text) { return(encodeFormField(text)); }
	}
	</script>
	
    <script src="<?=SITE_TEMPLATE_PATH?>/j/jquery.qtip.js"></script>
	
    <!--[if lt IE 9]><script src="http://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js"></script><![endif]-->
    <?$APPLICATION->ShowHead();?>
    <link href="<?=SITE_TEMPLATE_PATH?>/color.css" type="text/css" rel="stylesheet" />
	<link href="<?=SITE_TEMPLATE_PATH?>/c/jquery.qtip.css" type="text/css" rel="stylesheet" />
</head>

<?if (CSite::inDir(SITE_DIR."catalog/")) {
    $isDetailPage = (count(explode("/", rtrim(str_replace(SITE_DIR . "catalog/", "", $APPLICATION->GetCurDir()), "/"))) > 1 ? 1 : 0);
}?>
<body<?=(CSite::inDir(SITE_DIR."catalog/") || (CSite::inDir(SITE_DIR."brands/")) ? ($isDetailPage ? ' class="item"' : ' class="catalog"') : '')?>>

<div id="panel"><?$APPLICATION->ShowPanel();?></div>
<div id="overlay"></div>
<? $APPLICATION->IncludeComponent(
	"fashion:callback",
	"dvs_callback",
	array(
		"REQUIRED_FIELDS" => array("NAME", "TEL", "TIME_FROM", "TIME_TILL"),
		"TIME_FROM" => "18:00",
		"TIME_TILL" => "20:00"
	)
);
?>
<?if($isDetailPage){?>
<div id="cart-confirm" class="dialog">
    <h3><?=GetMessage("ADDED2BASKET")?> <strong></strong></h3>
    <div class="info">
        <p class="image"><img id="cart-image" /></p>
        <ul class="opts">
            <li><?=GetMessage("COLOR")?>: <span class="color" id="cart-color"></span></li>
            <li><?=GetMessage("SIZE")?>: <strong id="cart-size"></strong></li>
            <li><?=GetMessage("COST")?>: <strong id="cart-price"></strong> <span class="rub"><?=GetMessage("RUB")?></span></li>
            <li><?=GetMessage("COUNT")?>: <strong id="cart-quantity"></strong></li>
            <li><?=GetMessage("TOTAL")?>: <strong id="cart-overall"></strong> <span class="rub"><?=GetMessage("RUB")?></span></li>
        </ul>
    </div>
    <p>
        <a href="#" class="button close"><?=GetMessage("GO2ITEMS")?></a>
    </p>
    <p>
        <input type="submit" value="<?=GetMessage("GO2BASKET")?>" id="go-to-basket" />
        <script>$('#go-to-basket').click(function (){document.location.href = "<?=SITE_DIR?>personal/cart/";});</script>
    </p>
</div>
<div id="cart-confirm-set" class="dialog">
    <h3><?=GetMessage("ADDED2BASKET")?> <br>
        <strong></strong>
    </h3>
    <div id="cart-confirm-insert-to"></div>
    <div class="clear-both"></div>
    <p id="total-set">

    </p>
    <p class="text-align-center padding-10">
        <a href="#" class="button close cart-confirm-set-button-close"><?=GetMessage("GO2ITEMS")?></a>
    </p>
    <p class="text-align-center">
        <input type="submit" value="<?=GetMessage("GO2BASKET")?>" id="go-to-basket2" />
        <script>$('#go-to-basket2').click(function (){document.location.href = "<?=SITE_DIR?>personal/cart/";});</script>
    </p>
</div>
<?}?>

<div class="back">
    <div class="back-2">

<div id="wrapper">
    <div id="header" itemscope itemtype = "http://schema.org/LocalBusiness">
        <div class="title vcard">
            <div class="wrapper">
            <a href="<?=SITE_DIR?>"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/company_logo.php"), false);?></a>
            <abbr itemprop = "name" class="category"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/company_name.php"), false);?></abbr>
            </div>
        </div><!-- .logo -->

        <div class="user-links">
            <div class="wrapper">
                <span id="top-cart"><?$APPLICATION->IncludeComponent(
                    "bitrix:sale.basket.basket.small",
                    "",
                    Array(
                        "PATH_TO_BASKET" => "#SITE_DIR#personal/cart/",
                        "PATH_TO_ORDER" => "#SITE_DIR#personal/order/"
                    ),
                false
                );?></span>
                <?$APPLICATION->IncludeComponent("bitrix:system.auth.form", "", array(
                    "REGISTER_URL" => "#SITE_DIR#auth/",
                    "PROFILE_URL" => "#SITE_DIR#personal/",
                    "SHOW_ERRORS" => "N"
                    ),
                    false,
                    Array()
                );?>
            </div>
        </div>

        <div class="adr">
            <div class="wrapper">
            <address itemprop = "address"><strong><?=GetMessage("ADDRESS")?></strong> <span class="locality"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/locality.php"), false);?></span>, <span class="street-address"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/street_address.php"), false);?></span></address>
            <p><a href="<?=SITE_DIR?>contacts/" class="sheme"><?=GetMessage("SCHEME")?></a></p>
            </div>
        </div>

        <div class="contacts vcard">
            <div class="wrapper">
            <abbr itemprop = "telephone" class="tel"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/telephone.php"), false);?></abbr>
            <span itemprop = "openingHours" class="workhours"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/schedule.php"), false);?></span>
            <br /><a id="open-dialog"><?=GetMessage("ORDER_CALLBACK");?></a>
			</div>
        </div>

        <?$APPLICATION->IncludeComponent("bitrix:menu", "top", array(
            "ROOT_MENU_TYPE" => "catalog",
            "MENU_CACHE_TYPE" => "A",
            "MENU_CACHE_TIME" => "3600",
            "MENU_CACHE_USE_GROUPS" => "Y",
            "MENU_CACHE_GET_VARS" => array(
            ),
            "MAX_LEVEL" => "2",
            "CHILD_MENU_TYPE" => "catalog",
            "USE_EXT" => "Y",
            "DELAY" => "N",
            "ALLOW_MULTI_SELECT" => "N"
            ),
            false
        );?>
    </div><!-- #header -->

    <?$APPLICATION->IncludeComponent("bitrix:menu", "all", array(
        "ROOT_MENU_TYPE" => "catalog",
        "MENU_CACHE_TYPE" => "A",
        "MENU_CACHE_TIME" => "3600",
        "MENU_CACHE_USE_GROUPS" => "Y",
        "MENU_CACHE_GET_VARS" => array(
        ),
        "MAX_LEVEL" => "2",
        "CHILD_MENU_TYPE" => "catalog",
        "USE_EXT" => "Y",
        "DELAY" => "N",
        "ALLOW_MULTI_SELECT" => "N"
        ),
        false
    );?>

    <div id="middle">
    <div id="container">
    <div id="content"<?=((CSite::inDir(SITE_DIR."catalog/") && !$isDetailPage) || (CSite::inDir(SITE_DIR."brands/")) ? '' : ' class="no-sidebar"')?>>

        <div class="title">
        <?$APPLICATION->IncludeComponent("bitrix:breadcrumb", ".default", array(
            "START_FROM" => "0",
            "PATH" => "",
            "SITE_ID" => "-"
            ),
            false,
            Array('HIDE_ICONS' => 'Y')
        );?>

        <h1><?$APPLICATION->ShowTitle(false);?><?$APPLICATION->ShowProperty("ADDITIONAL_TITLE", "");?></h1>
        </div>
