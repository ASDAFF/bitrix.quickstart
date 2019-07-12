<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
IncludeTemplateLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/templates/".SITE_TEMPLATE_ID."/header.php");
$wizTemplateId = COption::GetOptionString("main", "wizard_template_id", "bejetstore_purple_white", SITE_ID);
CUtil::InitJSCore();
CJSCore::Init(array("fx"));
$curPage = $APPLICATION->GetCurPage(true);
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, width=device-width">
	<link rel="shortcut icon" type="image/x-icon" href="<?=SITE_DIR?>favicon.ico" />
	<?echo '<meta http-equiv="Content-Type" content="text/html; charset='.LANG_CHARSET.'"'.(true ? ' /':'').'>'."\n";
	$APPLICATION->ShowMeta("robots", false, true);
	$APPLICATION->ShowMeta("keywords", false, true);
	$APPLICATION->ShowMeta("description", false, true);
	?>
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,700italic,400,300,700&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Roboto+Slab:400&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
	<?
	$APPLICATION->ShowCSS(true, true);
	$APPLICATION->ShowHeadStrings();
	$APPLICATION->ShowHeadScripts();
	//jquery
	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/jquery-1.11.1.min.js");
	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/materialize.min.js");
	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/jquery-ui.min.js");
	//materialize
	$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/materialize.min.css");
	//$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/materialize.min.js");
	//bootstrap
	if($_COOKIE["mobile"] != "mobile"){
		$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/bootstrap.min.css");
		$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/bootstrap.min.js");
	}else{
		$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/bootstrap-mobile.css");
		$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/bootstrap-mobile.js");
	}
	//$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/bootstrap.min.css");
	$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/bootstrap-theme.min.css");
	$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/jquery-ui.min.css");
	$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/jquery-ui.structure.min.css");
	$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/jquery-ui.theme.min.css");
	$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/plugins.css");?>
	<!--link rel="stylesheet" type="text/css" href="<?=CUtil::GetAdditionalFileURL(SITE_TEMPLATE_PATH."/colors.css")?>" /-->
	<link rel="stylesheet" type="text/css" href="<?=CUtil::GetAdditionalFileURL("/colors.css")?>" />
	<link rel="stylesheet" type="text/css" href="<?=CUtil::GetAdditionalFileURL(SITE_TEMPLATE_PATH."/custom.css")?>" />
	<?
	
	//$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/html5shiv.js");
	//$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/bootstrap.min.js");
	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/plugins.js");
	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/script.js");
	?>
	<title><?$APPLICATION->ShowTitle()?></title>
</head>
<body>
<div id="panel"><?$APPLICATION->ShowPanel();?></div>



    <ul id="sideNavPanel" class="side-nav fixed" style="left: -310px; visibility: hidden">
      <li class="logo">
         <?if($curPage == SITE_DIR."index.php"):?>
			<span class="bj-logo"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/company_logo.php"), false);?></span>
		<?else:?>
			<a href="<?=SITE_DIR?>" class="bj-logo"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/company_logo.php"), false);?></a>
		<?endif;?>
      </li>
	  

      <li class="no-padding">
        <div class="media">
          <div class="media-body">
			<?if($USER->IsAuthorized()):?>
			<?$rsUser = CUser::GetByID($USER->getId());
				$arUser = $rsUser->Fetch();?>
			<?
			$print_name=$arUser['NAME'].' '.$arUser['LAST_NAME'];
			if(!trim($print_name))$print_name=$arUser['LOGIN'];
			?>
            <ul>
              <li><a href="/personal/"><?=$print_name?></a></li>
              <li><a href="/?logout=yes"><?=GetMessage("OUT")?></a></li>
            </ul>
			<?else:?>
			<ul>
              <li><a href="/auth/"><?=GetMessage("IN")?></a></li>
              <li><a href="/auth/?register=yes"><?=GetMessage("REG")?></a></li>
            </ul>
			<?endif?>
          </div>
		  <?if($USER->IsAuthorized()):?>
			  <div class="media-right">
			  <?if($arUser["PERSONAL_PHOTO"]):?>
				<?$file = CFile::ResizeImageGet($arUser["PERSONAL_PHOTO"], array('width'=>64, 'height'=>64), BX_RESIZE_IMAGE_PROPORTIONAL, true);                
				$img = '<img src="'.$file['src'].'" width="'.$file['width'].'" height="'.$file['height'].'" />';
				?>
				<a href="/personal/"><?=$img?></a>
			 <?else:?>
				<a href="/personal/"><img src="/upload/userpic.png" alt=""></a>
			 <?endif?>
			  </div>
		  <?endif?>
        </div>        
      </li>
	  
	  <?if($USER->IsAuthorized()):?>
      <li class="no-padding">
        <ul>
          <li><a href="/personal/cart/"><?=GetMessage("CART")?> <span class="badge"><?
						$APPLICATION->IncludeComponent("bitrix:sale.basket.basket.line", "top-left", array(
						"PATH_TO_BASKET" => SITE_DIR."personal/cart/",
						"PATH_TO_PERSONAL" => SITE_DIR."personal/",
						"SHOW_PERSONAL_LINK" => "N"
						),
						false,
						Array('')
						);
					?></span></a></li>
          <li><a href="/personal/order/"><?=GetMessage("ORDERS")?></a></li>
          <li><a href="/personal/subscribe/"><?=GetMessage("SUBSCRIBE")?></a></li>
        </ul>
      </li>
	  <?endif?>
	  
      <li class="no-padding">
	  
	  <?$APPLICATION->IncludeComponent("bitrix:menu", "top_left", array(
		"ROOT_MENU_TYPE" => "sidenav_shopmenu",
		"MENU_CACHE_TYPE" => "N",
		"MENU_CACHE_TIME" => "36000000",
		"MENU_CACHE_USE_GROUPS" => "Y",
		"MENU_CACHE_GET_VARS" => array(
		),
		"MAX_LEVEL" => "1",
		"CHILD_MENU_TYPE" => "sidenav_shopmenu",
		"USE_EXT" => "Y",
		"DELAY" => "N",
		"ALLOW_MULTI_SELECT" => "N"
		),
		false
	);?>
					

      </li>
    </ul>


<header class="bj-page-header">
	<section class="bj-page-header__top">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-10 col-sm-9 col-xs-12 bj-logo-space">
					 <?if($curPage == SITE_DIR."index.php"):?>
						<span class="bj-logo"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/company_logo.php"), false);?></span>
					<?else:?>
						<a href="<?=SITE_DIR?>" class="bj-logo"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/company_logo.php"), false);?></a>
					<?endif;?>
					<!--nclude virtual="/blocks/page/header/user.html"-->
					<?$APPLICATION->IncludeComponent("bitrix:menu", "personal_menu", array(
						"ROOT_MENU_TYPE" => ($USER->IsAuthorized() ? "personal_menu_auth" : "personal_menu_not_auth"),
						"MENU_CACHE_TYPE" => "A",
						"MENU_CACHE_TIME" => "36000000",
						"MENU_CACHE_USE_GROUPS" => "Y",
						"MENU_CACHE_GET_VARS" => array(
						),
						"MAX_LEVEL" => "2",
						"CHILD_MENU_TYPE" => ($USER->IsAuthorized() ? "personal_menu_auth" : "personal_menu_not_auth"),
						"USE_EXT" => "Y",
						"DELAY" => "N",
						"ALLOW_MULTI_SELECT" => "N"
						),
						false
					);?>

					<?
						$APPLICATION->IncludeComponent("bitrix:sale.basket.basket.line", "top", array(
						"PATH_TO_BASKET" => SITE_DIR."personal/cart/",
						"PATH_TO_PERSONAL" => SITE_DIR."personal/",
						"SHOW_PERSONAL_LINK" => "N"
						),
						false,
						Array('')
						);
					?>
					
					<?$APPLICATION->IncludeComponent(
						"bitrix:search.title", 
						"simple", 
						array(
							"NUM_CATEGORIES" => "1",
							"TOP_COUNT" => "5",
							"CHECK_DATES" => "N",
							"SHOW_OTHERS" => "Y",
							"PAGE" => SITE_DIR."catalog/",
							"CATEGORY_0_TITLE" => GetMessage("SEARCH_GOODS"),
							"CATEGORY_0" => array(
								0 => "iblock_catalog",
							),
							"CATEGORY_0_iblock_catalog" => array(
								0 => "2",
							),
							"CATEGORY_OTHERS_TITLE" => GetMessage("SEARCH_OTHER"),
							"SHOW_INPUT" => "Y",
							"INPUT_ID" => "title-search-input",
							"CONTAINER_ID" => "search",
							"ORDER" => "date",
							"USE_LANGUAGE_GUESS" => "Y",
							"PRICE_CODE" => array(
							),
							"PRICE_VAT_INCLUDE" => "Y",
							"PREVIEW_TRUNCATE_LEN" => "",
							"SHOW_PREVIEW" => "Y",
							"CONVERT_CURRENCY" => "N"
						),
						false
					);?>	
          
				  <a href="#" data-activates="sideNavPanel" id="nav-button" class="bj-nav-button">
					<span class="glyphicon glyphicon-align-justify" data-toggle="tooltip" data-placement="bottom" title="Меню"></span>
				  </a>
					
					<?$APPLICATION->IncludeComponent("bitrix:menu", "top_menu", array(
						"ROOT_MENU_TYPE" => "top",
						"MENU_CACHE_TYPE" => "A",
						"MENU_CACHE_TIME" => "36000000",
						"MENU_CACHE_USE_GROUPS" => "Y",
						"MENU_CACHE_GET_VARS" => array(
						),
						"MAX_LEVEL" => "2",
						"CHILD_MENU_TYPE" => "top",
						"USE_EXT" => "Y",
						"DELAY" => "N",
						"ALLOW_MULTI_SELECT" => "N"
						),
						false
					);?>
				
				
				</div>
				<div class="col-md-2 col-sm-3 bj-phone">
					<span class="bj-phone__num hidden-xs">
						 <?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/telephone.php"), false);?>
						 <!--<span class="bj-phone__time">c 8:00 до 23:00</span>-->
					</span>
					<a href="#" class="glyphicon glyphicon-earphone hidden-xs" data-toggle="modal" data-target="#myModal"></a>
					<a href="tel:<?include($_SERVER["DOCUMENT_ROOT"].SITE_DIR."include/telephone.php");?>" class="glyphicon glyphicon-earphone visible-xs-block"></a>
          
          <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  <h2 class="modal-title text-center" id="myModalLabel"><?=GetMessage("OUR_TEL")?></h2>
                </div>
                <div class="modal-body text-center">
                  <div class="bj-text-x-large"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/telephone.php"), false);?></div>
                  <div class="small"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/schedule.php"), false);?></div>
                </div>
                <div class="modal-footer text-center" style="display:none">
                  <button type="button" class="btn btn-default"><?=GetMessage("PHONE_ME")?></button>
                </div>
              </div>
            </div>
          </div>

				</div>
			</div>
		</div>
	</section>
	<section class="bj-page-header__dropdown">
		<div class="i-relative container-fluid">
			<article>
	<?$APPLICATION->IncludeComponent("bitrix:menu", "catalog_menu", array(
		"ROOT_MENU_TYPE" => "catalog",
		"MENU_CACHE_TYPE" => "A",
		"MENU_CACHE_TIME" => "36000000",
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
	
	
	<?$APPLICATION->IncludeComponent(
	"bitrix:news.list", 
	"brand_in_menu", 
	array(
		"IBLOCK_TYPE" => "brands",
		"IBLOCK_ID" => BEJET_SELLER_BRANDS,//#BRANDS_IBLOCK_ID#
		"NEWS_COUNT" => "12",
		"SORT_BY1" => "ACTIVE_FROM",
		"SORT_ORDER1" => "DESC",
		"SORT_BY2" => "SORT",
		"SORT_ORDER2" => "ASC",
		"FILTER_NAME" => "",
		"FIELD_CODE" => array(
			0 => "",
			1 => "",
		),
		"PROPERTY_CODE" => array(
			0 => "MENU_PIC",
			1 => "",
		),
		"CHECK_DATES" => "Y",
		"DETAIL_URL" => "",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "Y",
		"PREVIEW_TRUNCATE_LEN" => "",
		"ACTIVE_DATE_FORMAT" => "d.m.Y",
		"SET_TITLE" => "N",
		"SET_BROWSER_TITLE" => "N",
		"SET_META_KEYWORDS" => "N",
		"SET_META_DESCRIPTION" => "N",
		"SET_STATUS_404" => "N",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
		"ADD_SECTIONS_CHAIN" => "N",
		"HIDE_LINK_WHEN_NO_DETAIL" => "N",
		"PARENT_SECTION" => "",
		"PARENT_SECTION_CODE" => "",
		"INCLUDE_SUBSECTIONS" => "Y",
		"PAGER_TEMPLATE" => "bejetstore",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"PAGER_TITLE" => "Новости",
		"PAGER_SHOW_ALWAYS" => "Y",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "Y",
		"DISPLAY_DATE" => "Y",
		"DISPLAY_NAME" => "Y",
		"DISPLAY_PICTURE" => "Y",
		"DISPLAY_PREVIEW_TEXT" => "Y",
		"AJAX_OPTION_ADDITIONAL" => ""
	),
	false
);?>

   
        <hr class="i-size-L">
        
				<button class="up"></button>
			</article>
		</div>
	</section>
	<section class="bj-page-header__submenu">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-2 col-xs-4">
					<a href="<?=SITE_DIR?>catalog/" class="bj-page-header__menu-link bj-icon-link">
						<span class="bj-icon i-menu bj-icon-link__icon"></span>
						<span class="bj-icon-link__link"><?=GetMessage("CATALOG")?></span>
					</a>
				</div>
				<?$APPLICATION->IncludeComponent("bitrix:menu", "lookbook", array(
					"ROOT_MENU_TYPE" => "lookbook",
					"MENU_CACHE_TYPE" => "A",
					"MENU_CACHE_TIME" => "36000000",
					"MENU_CACHE_USE_GROUPS" => "Y",
					"MENU_CACHE_GET_VARS" => array(
					),
					"MAX_LEVEL" => "2",
					"CHILD_MENU_TYPE" => "",
					"USE_EXT" => "Y",
					"DELAY" => "N",
					"ALLOW_MULTI_SELECT" => "N"
					),
					false
				);?>
<?global $addFilter;?>
<?include_once($_SERVER["DOCUMENT_ROOT"].SITE_DIR."catalog/filter_array.php");?>
<?$APPLICATION->IncludeComponent(
	"bitrix:search.tags.cloud",
	"top_tags",
	Array(
		"FONT_MAX" => "50",
		"FONT_MIN" => "10",
		"COLOR_NEW" => "3E74E6",
		"COLOR_OLD" => "C0C0C0",
		"PERIOD_NEW_TAGS" => "",
		"SHOW_CHAIN" => "Y",
		"COLOR_TYPE" => "Y",
		"WIDTH" => "100%",
		"SORT" => "NAME",
		"PAGE_ELEMENTS" => "5",
		"PERIOD" => "",
		"URL_SEARCH" => SITE_DIR."catalog/",
		"TAGS_INHERIT" => "Y",
		"CHECK_DATES" => "N",
		"FILTER_NAME" => "addFilter",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600",
		"arrFILTER" => array()
	),
false
);?>
<?$APPLICATION->IncludeComponent("bitrix:menu", "campaign", array(
	"ROOT_MENU_TYPE" => "campaign",
	"MENU_CACHE_TYPE" => "A",
	"MENU_CACHE_TIME" => "36000000",
	"MENU_CACHE_USE_GROUPS" => "Y",
	"MENU_CACHE_GET_VARS" => array(
	),
	"MAX_LEVEL" => "2",
	"CHILD_MENU_TYPE" => "",
	"USE_EXT" => "Y",
	"DELAY" => "N",
	"ALLOW_MULTI_SELECT" => "N"
	),
	false
);?>
			</div>
		</div>
	</section>
</header>








<div class="bj-page-content container-fluid">
	<div class="bj-top-decoration"></div>
<?if($APPLICATION->GetCurDir() != SITE_DIR):?>
<?$APPLICATION->IncludeComponent("bitrix:breadcrumb", "", array(
		"START_FROM" => "0",
		"PATH" => "",
		"SITE_ID" => "-"
	),
	false,
	Array('HIDE_ICONS' => 'Y')
);?>
<?endif;?>
<?if ($curPage != SITE_DIR."index.php"):?>
<?if(strpos($APPLICATION->GetCurDir(),"/catalog/") !== false):?>
<div class="row">
	<div class="col-sm-6">
	<h1><?=$APPLICATION->ShowTitle(false);?></h1>
	</div>
	<div class="col-sm-6">
	<?$APPLICATION->ShowViewContent("section_tags_position");?>
	</div>
</div>
<?else:?>
<h1><?=$APPLICATION->ShowTitle(false);?></h1>
<?endif;?>
<?endif?>