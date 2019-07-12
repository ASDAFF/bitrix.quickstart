<?
IncludeTemplateLangFile(__FILE__);
if($APPLICATION->GetCurPage(true)==SITE_DIR.'index.php')define('INDEX', 'Y');
if(strpos($APPLICATION->GetCurPage(), SITE_DIR.'personal/')!==false 
    && strpos($APPLICATION->GetCurPage(), SITE_DIR.'personal/cart/')===false
    && strpos($APPLICATION->GetCurPage(), SITE_DIR.'personal/order/make/')===false
    && strpos($APPLICATION->GetCurPage(), SITE_DIR.'personal/order/detail/')===false
    
    )define('PERSONAL', 'Y');
$theme=COption::GetOptionString("main", "wizard_".SITE_TEMPLATE_ID."_theme_id", "", SITE_ID);

?>
<!DOCTYPE html>
<html>
<head>
 
  <?$APPLICATION->ShowHead()?>
  <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1, maximum-scale=1">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <link rel="shortcut icon" href="<?=SITE_TEMPLATE_PATH?>/favicon.ico" type="image/x-icon">

  <title><?$APPLICATION->ShowTitle(false)?></title>
  <link rel="stylesheet" type="text/css" href="<?=SITE_TEMPLATE_PATH?>/css/chosen.css">
  <link rel="stylesheet" type="text/css" href="<?=SITE_TEMPLATE_PATH?>/css/jquery-ui-1.10.3.custom.min.css">
  <link rel="stylesheet" type="text/css" href="<?=SITE_TEMPLATE_PATH?>/css/prettyPhoto.css">
  <link rel="stylesheet" type="text/css" href="<?=SITE_TEMPLATE_PATH?>/css/flexslider.css">
  <link rel="stylesheet" type="text/css" href="<?=SITE_TEMPLATE_PATH?>/css/font-awesome.css">

  <link rel="stylesheet" type="text/css" href="<?=SITE_TEMPLATE_PATH?>/css/bootstrap.css">
  <link rel="stylesheet" type="text/css" href="<?=SITE_TEMPLATE_PATH?>/css/style.css">
  <link rel="stylesheet" type="text/css" href="<?=SITE_TEMPLATE_PATH?>/css/style_click.css">
  <link rel="stylesheet" type="text/css" href="<?=SITE_TEMPLATE_PATH?>/ajax/style-popup.css">

  <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!--[if lt IE 9]>
  <script src="<?=SITE_TEMPLATE_PATH?>/bootstrap/js/html5shiv.js"></script>
  <script src="<?=SITE_TEMPLATE_PATH?>/bootstrap/js/respond.min.js"></script>
  <![endif]-->
<script>
var template_path='<?=SITE_TEMPLATE_PATH?>';
var sitedir='<?=SITE_DIR?>';
var siteid='<?=SITE_ID?>';
var ajaxactions=sitedir+'include/ajax/actions.php';
</script>
</head>
<body class="<?=$theme?>">
<div id="panel"><?$APPLICATION->ShowPanel()?></div>
<div class="wrapper">

<header id="MainNav">
  <div class="container">
    <div class="row">
      <section class="col-md-12" id="TopBar">
        <?$APPLICATION->IncludeComponent("bitrix:system.auth.form", "auth", array(
            "REGISTER_URL" => "",
            "FORGOT_PASSWORD_URL" => "",
            "PROFILE_URL" => SITE_DIR."personal/",
            "SHOW_ERRORS" => "N"
            ),
            false
        );?>

        <!-- SHOPPING CART -->
        <div class="shopping-cart-widget pull-right">
        <?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array(
    "AREA_FILE_SHOW" => "file",
    "PATH" => SITE_DIR."include/ajax/basket.php",
    "EDIT_TEMPLATE" => ""
    ),
    false
);?>
         </div>
        <!-- !SHOPPING CART -->
      </section>
      <nav class="navbar navbar-default">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle btn btn-primary">
            <span class="sr-only">menu</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="<?=SITE_DIR?>"><?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array(
    "AREA_FILE_SHOW" => "file",
    "PATH" => SITE_DIR."include/logo.php",
    "EDIT_TEMPLATE" => ""
    ),
    false
);?></a>
        </div>

        <div class="navbar-collapse navbar-main-collapse" role="navigation">
        <?$APPLICATION->IncludeComponent("bitrix:menu", "top", Array(
            "ROOT_MENU_TYPE" => "top",    // Тип меню для первого уровня
            "MENU_CACHE_TYPE" => "A",    // Тип кеширования
            "MENU_CACHE_TIME" => "3600",    // Время кеширования (сек.)
            "MENU_CACHE_USE_GROUPS" => "Y",    // Учитывать права доступа
            "MENU_CACHE_GET_VARS" => "",    // Значимые переменные запроса
            "MAX_LEVEL" => "3",    // Уровень вложенности меню
            "CHILD_MENU_TYPE" => "left",    // Тип меню для остальных уровней
            "USE_EXT" => "Y",    // Подключать файлы с именами вида .тип_меню.menu_ext.php
            "DELAY" => "N",    // Откладывать выполнение шаблона меню
            "ALLOW_MULTI_SELECT" => "N",    // Разрешить несколько активных пунктов одновременно
            ),
            false
        );?>
        
          <?$APPLICATION->IncludeComponent("bitrix:search.title", "search", array(
	"NUM_CATEGORIES" => "1",
	"TOP_COUNT" => "5",
	"ORDER" => "date",
	"USE_LANGUAGE_GUESS" => "Y",
	"CHECK_DATES" => "N",
	"SHOW_OTHERS" => "N",
	"PAGE" => "#SITE_DIR#catalog/",
	"CATEGORY_0_TITLE" => "",
	"CATEGORY_0" => array(
		0 => "iblock_catalog",
	),
	"CATEGORY_0_iblock_catalog" => array(
		0 => "all",
	),
	"SHOW_INPUT" => "Y",
	"INPUT_ID" => "navbar-search",
	"CONTAINER_ID" => "title-search"
	),
	false
);?>
        </div>
        <!-- /.navbar-collapse -->
      </nav>
    </div>
  </div>
</header>

<section id="Content" role="main">
<?if(defined('INDEX')):?>
<?$APPLICATION->IncludeComponent(
    "bitrix:main.include",
    "",
    Array(
        "AREA_FILE_SHOW" => "page",
        "AREA_FILE_SUFFIX" => "slider",
        "EDIT_TEMPLATE" => ""
    )
);?>
<?elseif(!defined('ERROR_404')):?>
 <div id="titlehead" class="full-width section-emphasis-1 page-header page-header-short">
  <div class="container">
    <header class="row">
      <div class="col-md-12">
        <h1 id="titleheadh1" class="strong-header pull-left"><?$APPLICATION->ShowTitle(false)?></h1>
        <?$APPLICATION->IncludeComponent("bitrix:breadcrumb", "breadcrumbs", array(
	"START_FROM" => "0",
	"PATH" => "",
	"SITE_ID" => "-"
	),
	false
);?>
      </div>
    </header>
  </div>
</div>
<?endif?>
<?if(!defined('ERROR_404')):?>
<?$APPLICATION->IncludeComponent(
    "bitrix:main.include",
    "",
    Array(
        "AREA_FILE_SHOW" => "page",
        "AREA_FILE_SUFFIX" => "addinfo",
        "EDIT_TEMPLATE" => ""
    )
);?>
<div class="container">
<?if(defined('PERSONAL')):?>
<section class="row">
  <?$APPLICATION->IncludeComponent("bitrix:menu", "left", array(
	"ROOT_MENU_TYPE" => "personal",
	"MENU_CACHE_TYPE" => "A",
	"MENU_CACHE_TIME" => "3600",
	"MENU_CACHE_USE_GROUPS" => "Y",
	"MENU_CACHE_GET_VARS" => array(
		0 => "filter_history",
	),
	"MAX_LEVEL" => "1",
	"CHILD_MENU_TYPE" => "",
	"USE_EXT" => "Y",
	"DELAY" => "N",
	"ALLOW_MULTI_SELECT" => "N"
	),
	false
);?>
  <div class="clearfix visible-xs space-30"></div>
  <div class="col-sm-9 space-left-30">
<?endif?>
<?endif?>
