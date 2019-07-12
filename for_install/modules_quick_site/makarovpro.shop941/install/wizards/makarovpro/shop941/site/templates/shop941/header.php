<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
IncludeTemplateLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/templates/".SITE_TEMPLATE_ID."/header.php");

?>
<!doctype html>
<html>
<head>
<?$APPLICATION->ShowHead();?>
<title><? $APPLICATION->ShowTitle() ?></title>
<link rel="stylesheet" type="text/css" href="<?= SITE_TEMPLATE_PATH ?>/css/jquery.mCustomScrollbar.css">
<link rel="stylesheet" type="text/css" href="<?= SITE_TEMPLATE_PATH ?>/promo.css">
<script type="text/javascript" src="<?= SITE_TEMPLATE_PATH ?>/js/jquery-1.9.1.min.js"></script>
</head>
<body>
<?//$APPLICATION->IncludeFile(SITE_DIR . "include/promo.php", Array(), Array("MODE" => "html"));?>
<?$APPLICATION->ShowPanel(); ?>
<header class="header group">
  <div class="left">
      <div class="logo">
          
          <?$APPLICATION->IncludeFile(
                            SITE_DIR . "include/logo.php", Array(), Array("MODE" => "html")
                    );
                    ?>
      <span><?$APPLICATION->IncludeFile(
                SITE_DIR."include/slogan.php",
                Array(),
                Array("MODE"=>"html")
            );?></span>
    </div>
  </div>
  <div class="center">
    <nav class="horizontal-menu group">
    <?$APPLICATION->IncludeComponent("bitrix:menu", "horizontal_multilevel", array(
	"ROOT_MENU_TYPE" => "top",
	"MAX_LEVEL" => "1",
	"CHILD_MENU_TYPE" => "left",
	"USE_EXT" => "N",
	"MENU_CACHE_TYPE" => "A",
	"MENU_CACHE_TIME" => "36000000",
	"MENU_CACHE_USE_GROUPS" => "Y",
	"MENU_CACHE_GET_VARS" => ""
	),
	false,
	array(
	"ACTIVE_COMPONENT" => "Y"
	)
    );?>
    </nav>
    <div class="header-form">
        
        <?$APPLICATION->IncludeComponent("bitrix:search.title", "eshop", array(
            "NUM_CATEGORIES" => "1",
            "TOP_COUNT" => "5",
            "CHECK_DATES" => "N",
            "SHOW_OTHERS" => "N",
            "PAGE" => SITE_DIR."catalog/",
            "CATEGORY_0_TITLE" => GetMessage("SEARCH_GOODS") ,
            "CATEGORY_0" => array(
                    0 => "iblock_catalog",
            ),
            "CATEGORY_0_iblock_catalog" => array(
                    0 => "all",
            ),
            "CATEGORY_OTHERS_TITLE" => GetMessage("SEARCH_OTHER"),
            "SHOW_INPUT" => "Y",
            "INPUT_ID" => "title-search-input",
            "CONTAINER_ID" => "search",
            "PRICE_CODE" => array(
                    0 => "BASE",
            ),
            "SHOW_PREVIEW" => "Y",
            "PREVIEW_WIDTH" => "75",
            "PREVIEW_HEIGHT" => "75",
            "CONVERT_CURRENCY" => "Y"
    ),
    false
);?>

        
       
    </div>
  </div>
  <div class="right">
 
        
        		<?$APPLICATION->IncludeComponent("bitrix:system.auth.form", "", array(
					"REGISTER_URL" => SITE_DIR."login/",
					"PROFILE_URL" => SITE_DIR."personal/",
					"SHOW_ERRORS" => "N"
					),
					false,
					Array()
				);?>
  
    <div class="cart">
        <?$APPLICATION->IncludeComponent(
	"bitrix:sale.basket.basket.line",
	"",
	Array(
		"PATH_TO_BASKET" => SITE_DIR."personal/cart/",
		"PATH_TO_PERSONAL" => SITE_DIR."personal/",
		"SHOW_PERSONAL_LINK" => "N"
	),
false
);?> 
    </div>
  </div>
</header>
<section class="section group">
  <div class="left">
    <div id="tabs1">
      <div id="tabs-1" class="tabs-1">
          <?$APPLICATION->IncludeFile(
                SITE_DIR."include/tabs-2.php",
                Array(),
                Array("MODE"=>"html")
            );?>
      </div>
    </div>
      
      

    <nav class="left-menu">
        
        
    <?$APPLICATION->IncludeComponent("bitrix:menu", "vertical_multilevel", array(
	"ROOT_MENU_TYPE" => "left",
	"MAX_LEVEL" => "1",
	"CHILD_MENU_TYPE" => "left",
	"USE_EXT" => "Y",
	"MENU_CACHE_TYPE" => "A",
	"MENU_CACHE_TIME" => "36000000",
	"MENU_CACHE_USE_GROUPS" => "Y",
	"MENU_CACHE_GET_VARS" => ""
	),
	false,
	array(
	"ACTIVE_COMPONENT" => "Y"
	)
    );?>
    

    </nav>

  </div>
    
    
    	<?if($APPLICATION->GetCurPage(false)==SITE_DIR):?> 
<div class="center">
    <div class="wrapper">
        
        <h1 style="position: absolute; left: -9999px;">
            
            <?$APPLICATION->IncludeFile(
                SITE_DIR."include/slogan.php",
                Array(),
                Array("MODE"=>"html")
            );?>
            
        </h1>  

		<div class="sliders group">
		  <?$APPLICATION->IncludeFile(
                SITE_DIR."include/sliders.php",
                Array(),
                Array("MODE"=>"html")
            );?>
		</div>
	<?else:?>
			
<div class="center-inner">
    <div class="wrapper-inner">
        
        <div class="breadcrumbs">
            <?$APPLICATION->IncludeComponent(
	"bitrix:breadcrumb",
	"",
	Array(
		"START_FROM" => "0",
		"PATH" => "",
		"SITE_ID" => "-"
	),
	false
	);?>
        </div>
        <h1><?$APPLICATION->ShowTitle(false);?></h1>
  <?endif?>
    






