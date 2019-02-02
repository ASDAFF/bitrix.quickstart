<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
IncludeTemplateLangFile(__FILE__);
?> 
<!DOCTYPE html>
<!--[if IE 8]> 				 <html class="no-js lt-ie9" lang="en" > <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en" > <!--<![endif]-->

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width" />
  <title><?$APPLICATION->ShowTitle()?></title>


  <?CUtil::InitJSCore(array('jquery'));?>
  <?/*$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH);*/?>
  <?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/javascripts/vendor/custom.modernizr.js');?>  
  <?$APPLICATION->ShowHead();?>

</head>
<body>
  <?$APPLICATION->ShowPanel();?>


	<div class="row">
		<div class="large-4 columns">
			<a class="title" href="<?=SITE_DIR?>"><?
        $APPLICATION->IncludeFile(
	        SITE_DIR."include/company_name.php",
	        Array(),
	        Array("MODE"=>"html")
        );
        ?></a>
		</div>
		<div class="large-8 columns">
			<?$APPLICATION->IncludeFile(
	        SITE_DIR."include/company_contacts.php",
	        Array(),
	        Array("MODE"=>"html")
        );
        ?>
		</div>
	</div>

  <div class="row">
    <div class="large-12 columns">
      <?$APPLICATION->IncludeComponent(
	      "bitrix:menu",
	      "topbar",
	      Array(
		      "ROOT_MENU_TYPE" => "top",
		      "MENU_CACHE_TYPE" => "N",
		      "MENU_CACHE_TIME" => "3600",
		      "MENU_CACHE_USE_GROUPS" => "Y",
		      "MENU_CACHE_GET_VARS" => array(),
		      "MAX_LEVEL" => "3",
		      "CHILD_MENU_TYPE" => "left",
		      "USE_EXT" => "N",
		      "DELAY" => "N",
		      "ALLOW_MULTI_SELECT" => "N"
	      )
      );?>
    </div>
  </div>

  <div class="row">
    <div class="large-8 columns">
      <?$APPLICATION->IncludeComponent(
	      "bitrix:photo.section",
	      "orbit",
	      Array(
		      "IBLOCK_TYPE" => "products",
		      "IBLOCK_ID" => "2",
		      "SECTION_ID" => "1",
		      "SECTION_CODE" => "",
		      "SECTION_USER_FIELDS" => array(0=>"",1=>"",),
		      "ELEMENT_SORT_FIELD" => "sort",
		      "ELEMENT_SORT_ORDER" => "asc",
		      "FILTER_NAME" => "arrFilter",
		      "FIELD_CODE" => array(0=>"",1=>"",),
		      "PROPERTY_CODE" => array(0=>"",1=>"",),
		      "PAGE_ELEMENT_COUNT" => "20",
		      "LINE_ELEMENT_COUNT" => "3",
		      "SECTION_URL" => "",
		      "DETAIL_URL" => "",
		      "AJAX_MODE" => "N",
		      "AJAX_OPTION_JUMP" => "N",
		      "AJAX_OPTION_STYLE" => "Y",
		      "AJAX_OPTION_HISTORY" => "N",
		      "CACHE_TYPE" => "A",
		      "CACHE_TIME" => "36000000",
		      "CACHE_FILTER" => "N",
		      "CACHE_GROUPS" => "Y",
		      "META_KEYWORDS" => "-",
		      "META_DESCRIPTION" => "-",
		      "BROWSER_TITLE" => "-",
		      "SET_TITLE" => "Y",
		      "SET_STATUS_404" => "N",
		      "ADD_SECTIONS_CHAIN" => "Y",
		      "DISPLAY_TOP_PAGER" => "N",
		      "DISPLAY_BOTTOM_PAGER" => "Y",
		      "PAGER_TITLE" => "Images",
		      "PAGER_SHOW_ALWAYS" => "Y",
		      "PAGER_TEMPLATE" => "",
		      "PAGER_DESC_NUMBERING" => "N",
		      "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		      "PAGER_SHOW_ALL" => "Y",
		      "AJAX_OPTION_ADDITIONAL" => ""
	      )
      );?>
    </div>
    <div class="large-4 columns">
      <form>
        <input type="text" placeholder="Line 1">
        <input type="text" placeholder="Line 2">
        <a href="#" class="small button">Small Button</a>
      </form>
    </div>
  </div>







  <div class="row">
    <div class="large-12 columns">

