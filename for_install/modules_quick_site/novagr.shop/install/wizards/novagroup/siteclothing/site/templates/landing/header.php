<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$PATH_INCLUDE = SITE_DIR . "include";

if (strpos($_SERVER["HTTP_USER_AGENT"], "MSIE 6.0") === false
    && strpos($_SERVER["HTTP_USER_AGENT"], "MSIE 7.0") === false
) {
    $ie6 = false;
} else {
    $ie6 = true;
}

$currentUri = $APPLICATION->GetCurPage();

$VERSION_MODULE = NovaGroupGetVersionModule();

IncludeTemplateLangFile(__FILE__);
?>
<!DOCTYPE HTML>
<html>
<head>
    <title><? $APPLICATION->ShowTitle(); ?></title>
    <? $APPLICATION->ShowHead(); ?>

    <link href="<?= SITE_TEMPLATE_PATH ?>/template_styles.css" type="text/css" rel="stylesheet" />


    <?php
    CJSCore::Init(array("jquery"));
    //CAjax::Init();
    ?>
    <script src="/include/bootstrap/js/bootstrap.js"></script>
<script>
	var arMessage = 
		{
			required	: "<?=GetMessage('required');?>",
			remote		: "<?=GetMessage('remote');?>",
			email		: "<?=GetMessage('email');?>",
			url			: "<?=GetMessage('url');?>",
			date		: "<?=GetMessage('date');?>",
			dateISO		: "<?=GetMessage('dateISO');?>",
			number		: "<?=GetMessage('number');?>",
			digits		: "<?=GetMessage('digits');?>",
			creditcard	: "<?=GetMessage('creditcard');?>",
			equalTo		: "<?=GetMessage('equalTo');?>",
			accept		: "<?=GetMessage('accept');?>",
			maxlength	: "<?=GetMessage('maxlength');?>",
			minlength	: "<?=GetMessage('minlength');?>",
			angelength	: "<?=GetMessage('angelength');?>",
			range		: "<?=GetMessage('range');?>",
			max			: "<?=GetMessage('max');?>",
			min			: "<?=GetMessage('min');?>"
		};
</script>
	<script type="text/javascript" src="<?= SITE_TEMPLATE_PATH ?>/js/valid/jquery.validate.min.js"></script>
    <script type="text/javascript" src="<?= SITE_TEMPLATE_PATH ?>/js/valid/jquery.maskedinput.js"></script>
    <script type="text/javascript" src="<?= SITE_TEMPLATE_PATH ?>/js/valid/bootstrap-formhelpers.min.js"></script>
    <script src="<?= SITE_DIR ?>include/js/jquery.form.js"></script>


    <link href="<?= SITE_TEMPLATE_PATH ?>/css/images.css" type="text/css" rel="stylesheet" />


    <script src="<?= SITE_TEMPLATE_PATH ?>/js/bootstrap-modalmanager.js"></script>

    <link href="<?= SITE_TEMPLATE_PATH ?>/css/bootstrap/bootstrap.min.css" type="text/css" rel="stylesheet" />



    <link href="<?= SITE_TEMPLATE_PATH ?>/css/bootstrap/bootstrap-modal.css" rel="stylesheet" />
    <link href="<?= SITE_TEMPLATE_PATH ?>/js/valid/bootstrap-formhelpers.min.css" rel="stylesheet" />
    <link href="<?= SITE_TEMPLATE_PATH ?>/css/bootstrap/trend.css" type="text/css" rel="stylesheet" />

    <link href="<?= SITE_TEMPLATE_PATH ?>/js/valid/style.css" type="text/css" rel="stylesheet" />

    <link rel="shortcut icon" href="<?= SITE_TEMPLATE_PATH ?>/images/favicon.ico">

</head>
<body>
<?php



if ($ie6 == true) {

    $APPLICATION->SetTitle(GetMessage("T_OLD_BROWSER"));

    $APPLICATION->IncludeComponent("bitrix:main.include", "",
        array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "/include/ie6.php"),
        false
    );
    die('</body></html>');
}

?>
<div id="panel"><? $APPLICATION->ShowPanel(); ?></div>
<div class="page-container">
<div id="canvas">
<div class="container-land">
<header>
    <div class="row head-land">
        <div class="logo  span3">
            <a href="<?= SITE_DIR ?>"><img width="209" height="97" src="<?= SITE_TEMPLATE_PATH ?>/images/logo.gif" title="<?= GetMessage("T_LOGO_ALT") ?>" alt="<?= GetMessage("T_LOGO_ALT") ?>"></a>
        </div>
        <div class="reck span9">

            <?$APPLICATION->IncludeComponent(
            "bitrix:news.detail",
            "top_contacts_land",
            Array(
            "AJAX_MODE" => "N",
            "IBLOCK_TYPE" => "system",
            "IBLOCK_ID" => "14",
            "ELEMENT_ID" => '',
            "ELEMENT_CODE" => "block-contacts",
            "CHECK_DATES" => "Y",
            "FIELD_CODE" => array(),
            "PROPERTY_CODE" => array(),
            "IBLOCK_URL" => "",
            "META_KEYWORDS" => "-",
            "META_DESCRIPTION" => "-",
            "BROWSER_TITLE" => "-",
            "SET_TITLE" => "N",
            "SET_STATUS_404" => "N",
            "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
            "ADD_SECTIONS_CHAIN" => "N",
            "ACTIVE_DATE_FORMAT" => "d.m.Y",
            "USE_PERMISSIONS" => "N",
            "CACHE_TYPE" => "A",
            "CACHE_TIME" => "36000000",
            "CACHE_GROUPS" => "Y",
            "DISPLAY_TOP_PAGER" => "N",
            "DISPLAY_BOTTOM_PAGER" => "Y",
            "PAGER_TITLE" => GetMessage("T_PAGER_TITLE"),
            "PAGER_TEMPLATE" => "",
            "PAGER_SHOW_ALL" => "N",
            "AJAX_OPTION_JUMP" => "N",
            "AJAX_OPTION_STYLE" => "N",
            "AJAX_OPTION_HISTORY" => "N",
            "IS_MOBILE" => isMobile()
            ),
            false
            );?>

        </div>
    </div>
</header>

