<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$PATH_INCLUDE = SITE_DIR . "include";

$currentUri = $APPLICATION->GetCurPage();

// single-column template
global $oneColumnFlag;


$oneColumnFlag = true;

if (
    empty($_REQUEST['elmid'])
    &&
    (
        strpos($APPLICATION->GetCurPage(), SITE_DIR . 'catalog') === 0 ||
    strpos($APPLICATION->GetCurPage(), SITE_DIR.'imageries') === 0
    )
    &&
    (!defined('ERROR_404') || constant('ERROR_404')!=="Y")

) $oneColumnFlag = false;

if ($currentUri == SITE_DIR . "catalog/" && empty($_REQUEST["q"]) ) {
    $oneColumnFlag = true;
}

if(Novagroup_Classes_General_Catalog::showSectionsCatalog(false)===true){
  //  $oneColumnFlag = true;
    $showSectionsCatalog = true;
} else {
    $showSectionsCatalog = false;
}

$VERSION_MODULE = NovaGroupGetVersionModule();

IncludeTemplateLangFile(__FILE__);
?>
<!DOCTYPE HTML>
<html>
<head>
    <title><? $APPLICATION->ShowTitle(); ?></title>
    <? $APPLICATION->ShowHead();

    CJSCore::Init(array("jquery"));
    CAjax::Init();


/*
	<script src="<?= SITE_DIR ?>include/bootstrap/js/bootstrap.js?<?= $VERSION_MODULE ?>"></script>
    <script src="<?= SITE_TEMPLATE_PATH ?>/js/bootstrap-modalmanager.js?<?= $VERSION_MODULE ?>"></script>
    <script src="<?=SITE_TEMPLATE_PATH?>/js/bootstrap-affix.js?<?=$VERSION_MODULE?>"></script>
*/


	// unite CSS
	$APPLICATION -> SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/images.css?".$VERSION_MODULE);
	$APPLICATION -> SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/logout.css?".$VERSION_MODULE);
	
	if (!isMobile())
		$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/default_style.css?".$VERSION_MODULE);

	$APPLICATION -> SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/bootstrap/bootstrap.min.css?".$VERSION_MODULE);
	$APPLICATION -> SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/bootstrap/bootstrap-responsive.min.css?".$VERSION_MODULE);
	$APPLICATION -> SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/bootstrap/bootstrap-modal.css?".$VERSION_MODULE);
	$APPLICATION -> SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/template_style.css?".$VERSION_MODULE);
	//$APPLICATION -> SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/bootstrap/trend.css?".$VERSION_MODULE);
	$APPLICATION -> SetAdditionalCSS(SITE_DIR."include/css/quickbuy.css?".$VERSION_MODULE);
	$APPLICATION -> SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/bootstrap-slider.css?".$VERSION_MODULE);

	// unite JS
	$APPLICATION->AddHeadScript(SITE_DIR."include/bootstrap/js/bootstrap.js?".$VERSION_MODULE);
	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/bootstrap-modalmanager.js?".$VERSION_MODULE);
	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/bootstrap-affix.js?".$VERSION_MODULE);
	
	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/jquery.tinycarousel.min.js?".$VERSION_MODULE);
	$APPLICATION->AddHeadScript(SITE_DIR."include/js/jquery.form.js?".$VERSION_MODULE);
	$APPLICATION->AddHeadScript(SITE_DIR."include/js/general.js?".$VERSION_MODULE);
	$APPLICATION->AddHeadScript(SITE_DIR."include/js/jquery.cookie.js?".$VERSION_MODULE);
	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/script.js");
	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/history.js");


/*
if (!isMobile())
{
	<link href="<?=SITE_TEMPLATE_PATH?>/css/default_style.css" rel="stylesheet">
}
*/


/*	
	<link href="<?= SITE_TEMPLATE_PATH ?>/css/images.css?<?= $VERSION_MODULE ?>" type="text/css" rel="stylesheet"/>
	<link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/css/logout.css?<?= $VERSION_MODULE ?>"/>
	<link href="<?= SITE_TEMPLATE_PATH ?>/css/bootstrap/bootstrap.min.css" type="text/css" rel="stylesheet" />
    <link href="<?= SITE_TEMPLATE_PATH ?>/css/bootstrap/bootstrap-responsive.min.css?<?= $VERSION_MODULE ?>" type="text/css" rel="stylesheet"/>
    <link href="<?= SITE_TEMPLATE_PATH ?>/css/bootstrap/bootstrap-modal.css?<?= $VERSION_MODULE ?>" rel="stylesheet"/>
    <link href="<?= SITE_TEMPLATE_PATH ?>/css/template_style.css" type="text/css" rel="stylesheet"/>
*/
$APPLICATION -> SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/bootstrap/trend.css?".$VERSION_MODULE);

/*
    <link href="<?= SITE_DIR ?>include/css/quickbuy.css?<?=$VERSION_MODULE?>" type="text/css" rel="stylesheet" />
    <link href="<?=SITE_TEMPLATE_PATH?>/css/bootstrap-slider.css?<?=$VERSION_MODULE?>" rel="stylesheet" type="text/css">
*/


	if(defined('NOVAGROUP_MODULE_ID') and NOVAGROUP_MODULE_ID=='novagr.liteshop')
	{
/*
?>
    <link href="<?= SITE_DIR ?>include/css/trend-lite.css?<?=$VERSION_MODULE?>" type="text/css" rel="stylesheet" />
<?
/*/
		$APPLICATION->SetAdditionalCSS(SITE_DIR."include/css/trend-lite.css?".$VERSION_MODULE);
	}
?>
<!--[if lt IE 9]>
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<?
/*
    <script type="text/javascript" src="<?= SITE_TEMPLATE_PATH ?>/js/jquery.tinycarousel.min.js"></script>
    <script src="<?= SITE_DIR ?>include/js/jquery.form.js?<?= $VERSION_MODULE ?>"></script>
    <script src="<?= SITE_DIR ?>include/js/general.js?<?= $VERSION_MODULE ?>"></script>
    <script src="<?= SITE_DIR ?>include/js/jquery.cookie.js?<?= $VERSION_MODULE ?>"></script>
<script src="<?=SITE_TEMPLATE_PATH?>/js/script.js?<?=$VERSION_MODULE?>"></script>
*/
?>
    <script type="text/javascript">
    // forgot pass link
        $(document).ready(function () {
            $("#forgot_link").bind("click", function () {
                var data = {'only_form': 1, 'form_id': 'forgot'};
                $.post('<?=SITE_DIR?>auth/ajax/forms.php', data, function (res) {

                    $("#popupForm").html(res);
                    ForgotPasswdDialogPrepare('popupForm', 1, 'authForm');
                    $("#forgotPass").modal('show');
                }, 'html');
                return false;
            });
        });
        JAVASCRIPT_SITE_DIR = "<?=SITE_DIR?>";
        JW_USER_EMAIL = "<? global $USER; print $USER->GetEmail(); ?>";
    </script>
<?php
$detailCardView = COption::GetOptionString("main", "detail_card", "1");
if ($detailCardView == 2) {
	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jquery.zoom-min.js');

} else if ($detailCardView == 3) {

	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/fancybox/jquery.fancybox.js');
	$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/fancybox/jquery.fancybox.css');

} else if ($detailCardView == 4) {

    $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jquery.elevateZoom-3.0.8.min.js');
    $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jquery.zoom-min.js');
    $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/fancybox/jquery.fancybox.js');
    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/fancybox/jquery.fancybox-castom.css');
    
}

/*
    <script type="text/javascript" src="<?= SITE_TEMPLATE_PATH ?>/js/history.js?<?= $VERSION_MODULE ?>"></script>
*/
?>
    <link rel="shortcut icon" href="<?= SITE_TEMPLATE_PATH ?>/images/favicon.ico">
    <!--[if IE 8]>
    <style type="text/css">
        .header .searchspan[class*="span"] {
            min-height: 22px !important;
        }
    </style>
    <![endif]-->
    <!--[if IE]>
  <link rel="stylesheet" type="text/css" href="<?= SITE_TEMPLATE_PATH ?>/css/ie/ie.css?<?=$VERSION_MODULE?>" />
    <![endif]-->
    <!--[if IE 8]>
  <link rel="stylesheet" type="text/css" href="<?= SITE_DIR ?>include/css/ie8.css?<?=$VERSION_MODULE?>" />
    <![endif]-->
    <script type="text/javascript">
        $(document).ready(function(){
            $('#slider5').tinycarousel({ axis: 'y'});
        });
    </script>
    <script type="text/javascript">
        function hideBasketItem(obj) {
            $.get(JAVASCRIPT_SITE_DIR+'cabinet/cart/?action=delete&id='+obj, function(){
                $.get(JAVASCRIPT_SITE_DIR + "include/ajax/basket.php", function (data) {
                    $('#cart_line_1').html($(data).html());
                    $('.hide-1').click(function () {
                        $(this).siblings(".list-basket").slideToggle("slow");
                        $('#slider5').tinycarousel({ axis: 'y' });
                        return false;
                    });
                });
            });
        }
    </script>
    <script type="text/javascript" src="http://s7.addthis.com/js/300/addthis_widget.js"></script>
    <script src="/local/templates/demoshop/js/bootstrap-slider.js?<?=$VERSION_MODULE?>"></script>
    <script src="/local/templates/demoshop/js/tools.js?<?=$VERSION_MODULE?>"></script>

</head>
<body>
<div id="not-old-browser">
<div id="panel"><? $APPLICATION->ShowPanel(); ?></div>
<div class="page-container">
    <div id="canvas">
        <div class="wrap-header">
            <div class="header ui">
                <div class="span3">
                    <div class="logo">
                        <a href="<?= SITE_DIR ?>" class="logo-link"></a>
                        <div class="logo-l"></div>
                    </div>
                </div>
                <div class="span9">
                    <div class="fix-header">
                        <div class="span first">
                            <?php
                            $APPLICATION->IncludeFile(SITE_DIR . "include/news/top_contacts.php");
                            ?>
                        </div>
                        <div class="span">
                            <div class="search-box demo">
                                <?php
                                $APPLICATION->IncludeFile(SITE_DIR . "include/search/title.php");
                                ?>
                            </div>
                        </div>
                        <div class="span last-ie new">
                            <div class="bx-system-auth-form">
                                <div class="auth-menu before_auth tooltip-demo reg-nenu">
                                    <?
                                    $APPLICATION->IncludeComponent("bitrix:system.auth.form", "demoshop", Array(
                                            "REGISTER_URL" => SITE_DIR . "auth/?register=yes",
                                            "FORGOT_PASSWORD_URL" => SITE_DIR . "auth/?forgot_password=yes",
                                            "PROFILE_URL" => USER_PROFILE_URL,
                                            "SHOW_ERRORS" => "Y	",
                                        ),
                                        false
                                    );
                                    ?>

                                    <div id="popupForm">
                                    </div>
                                <?
                                if (!CUser::IsAuthorized()) {
                                ?>
                                    <div aria-hidden="true" aria-labelledby="myModalLabel9" role="dialog" tabindex="-1"
                                         class="modal hide fade registarton" id="agreeForm">
                                        <div class="modal-header">
                                            <button aria-hidden="true" data-dismiss="modal" class="close"
                                                    type="button">&times;</button>
                                            <h3 id="myModalLabel9"><?= GetMessage("T_ACCEPT_RIGHTS") ?></h3>
                                        </div>
                                        <div class="modal-body">
                                            <?php
                                            $APPLICATION->IncludeFile(SITE_DIR . "include/news/pure_detail.php");
                                            ?>
                                            <input type="submit" id="agreeBtn" class="btn btn-rl"
                                                   value="<?= GetMessage("T_AGGREE_LABEL") ?>">
                                        </div>
                                    </div>
                                <?php

                                    $APPLICATION->IncludeComponent("novagroup:main.register", "ajax_template", Array(
                                            "SHOW_FIELDS" => "",
                                            "REQUIRED_FIELDS" => "",
                                            "AUTH" => "Y",
                                            "USE_BACKURL" => "",
                                            "SUCCESS_PAGE" => "",
                                            "SET_TITLE" => "N",
                                            "USER_PROPERTY" => "",
                                            "USER_PROPERTY_NAME" => "",
                                        ),
                                        false
                                    );
                                }
                                    ?>
                                    <?php
                                    $APPLICATION->IncludeFile(SITE_DIR . "include/form/feedback.php");
                                    ?>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div data-offset-top="140" data-spy="affix" style="z-index: 1030" class="top_nav new clearfix-menu affix-top new-top-nav" id="top_nav">
                <?php
                $APPLICATION->IncludeFile(SITE_DIR . "include/ajax/basket.php");
                ?>
                <div class="nav-bg"></div>
                <?$_GET['HTTP_HOST'] = getenv("HTTP_HOST");?>
                <?$APPLICATION->IncludeComponent("bitrix:menu", "tree_horizontal_novagr", array(
                    "ROOT_MENU_TYPE" => "left",
                    "MENU_CACHE_TYPE" => "Y",
                    "MENU_CACHE_TIME" => "14400",
                    "MENU_CACHE_USE_GROUPS" => "N",
                    "MENU_CACHE_GET_VARS" => array(
                        0 => "HTTP_HOST",
                        1 => "",
                    ),
                    "MAX_LEVEL" => "3",
                    "CHILD_MENU_TYPE" => "left",
                    "USE_EXT" => "Y",
                    "DELAY" => "Y",
                    "IS_MOBILE" => ( isMobile() == true ? "Y" : "N"),
                    "ALLOW_MULTI_SELECT" => "N"
                    ),
                    false
                );?>
            </div>
            <div class="content proba">
                <?php
                if ($oneColumnFlag == true) {
                ?>
                <div id="filter-hint" class="main-demo">

                    <?
                    } else {
                    ?>
                    <div class="menu-clear-left"></div>
                    <div id="filter-hint" class="main">
                        <?php
                        }
                        ?>
                        <?if($showSectionsCatalog==false){?>
                        <div id="chain-hint"><?php
                        $APPLICATION->IncludeComponent("bitrix:breadcrumb", "", array(
                                "START_FROM" => "0",
                                "PATH" => "",
		                        "SITE_ID" => SITE_ID
                            ),
                            false
                        );?></div>
                        <div>
                            <?}?>
                            <!--  content  -->
                            <div id="workarea">
                                