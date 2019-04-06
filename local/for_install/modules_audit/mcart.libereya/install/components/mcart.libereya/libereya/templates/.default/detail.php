<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$APPLICATION->AddHeadString("<link href='http://fonts.googleapis.com/css?family=PT+Sans:700,400&subset=cyrillic,latin' rel='stylesheet' type='text/css'>
	<link rel='stylesheet' href='/bitrix/components/mcart.libereya/libereya/styles/libereya.css'>");?>
<?
CJSCore::RegisterExt('lib', array(	
'js' => '/bitrix/components/mcart.libereya/libereya/js/lib.js',	
//'css' => '/bitrix/js/your_module/css/functions.css',	
'lang' => '/bitrix/modules/mcart.libereya/lang/'.LANGUAGE_ID.'/lib_js.php',	
'rel' => array('jquery') 
));
CJSCore::RegisterExt('tinyscrollbar', array(	
'js' => "/bitrix/components/mcart.libereya/libereya/js/tinyscrollbar.min.js",		
'rel' => array('jquery') 
));
CJSCore::RegisterExt('selectbox', array(	
'js' => "/bitrix/components/mcart.libereya/libereya/js/selectbox.min.js",		
'rel' => array('jquery') 
));
?>
<?CJSCore::Init(array("ajax", "window", 'lib', 'tinyscrollbar', 'selectbox'));?>

<div class="contentbox">
	<?$back_url = (!empty($arResult['FOLDER']))
			? $arResult['FOLDER']
			: $arResult['URL_TEMPLATES']['list'];?>
	<div class="content content-library">
		<p class="step_back"><a href="<?=$back_url;?>"><?=GetMessage("MCART_LIBEREYA_DETAIL_BACK");?></a></p>
		<?$ElementID = $APPLICATION->IncludeComponent(
			"mcart.libereya:libereya.detail",
			"",
			Array(
				"DISPLAY_DATE" => $arParams["DISPLAY_DATE"],
				"DISPLAY_NAME" => $arParams["DISPLAY_NAME"],
				"DISPLAY_PICTURE" => $arParams["DISPLAY_PICTURE"],
				"DISPLAY_PREVIEW_TEXT" => $arParams["DISPLAY_PREVIEW_TEXT"],
				"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
				"IBLOCK_ID" => $arParams["IBLOCK_ID"],
				"FIELD_CODE" => $arParams["DETAIL_FIELD_CODE"],
				"PROPERTY_CODE" => $arParams["DETAIL_PROPERTY_CODE"],
				"DETAIL_URL"	=>	$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["detail"],
				"SECTION_URL"	=>	$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
				"META_KEYWORDS" => $arParams["META_KEYWORDS"],
				"META_DESCRIPTION" => $arParams["META_DESCRIPTION"],
				"BROWSER_TITLE" => $arParams["BROWSER_TITLE"],
				"DISPLAY_PANEL" => $arParams["DISPLAY_PANEL"],
				"SET_TITLE" => $arParams["SET_TITLE"],
				"SET_STATUS_404" => $arParams["SET_STATUS_404"],
				"INCLUDE_IBLOCK_INTO_CHAIN" => $arParams["INCLUDE_IBLOCK_INTO_CHAIN"],
				"ADD_SECTIONS_CHAIN" => $arParams["ADD_SECTIONS_CHAIN"],
				"ACTIVE_DATE_FORMAT" => $arParams["DETAIL_ACTIVE_DATE_FORMAT"],
				"CACHE_TYPE" => $arParams["CACHE_TYPE"],
				"CACHE_TIME" => $arParams["CACHE_TIME"],
				"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
				"USE_PERMISSIONS" => $arParams["USE_PERMISSIONS"],
				"GROUP_PERMISSIONS" => $arParams["GROUP_PERMISSIONS"],
				"DISPLAY_TOP_PAGER" => $arParams["DETAIL_DISPLAY_TOP_PAGER"],
				"DISPLAY_BOTTOM_PAGER" => $arParams["DETAIL_DISPLAY_BOTTOM_PAGER"],
				"PAGER_TITLE" => $arParams["DETAIL_PAGER_TITLE"],
				"PAGER_SHOW_ALWAYS" => "N",
				"PAGER_TEMPLATE" => $arParams["DETAIL_PAGER_TEMPLATE"],
				"PAGER_SHOW_ALL" => $arParams["DETAIL_PAGER_SHOW_ALL"],
				"CHECK_DATES" => $arParams["CHECK_DATES"],
				"ELEMENT_ID" => $arResult["VARIABLES"]["ELEMENT_ID"],
				"ELEMENT_CODE" => $arResult["VARIABLES"]["ELEMENT_CODE"],
				"IBLOCK_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["news"],
				"USE_SHARE" 			=> $arParams["USE_SHARE"],
				"SHARE_HIDE" 			=> $arParams["SHARE_HIDE"],
				"SHARE_TEMPLATE" 		=> $arParams["SHARE_TEMPLATE"],
				"SHARE_HANDLERS" 		=> $arParams["SHARE_HANDLERS"],
				"SHARE_SHORTEN_URL_LOGIN"	=> $arParams["SHARE_SHORTEN_URL_LOGIN"],
				"SHARE_SHORTEN_URL_KEY" => $arParams["SHARE_SHORTEN_URL_KEY"],
			),
			$component
		);?>

		<p><a href="<?=$back_url;?>"><?=GetMessage("MCART_LIBEREYA_DETAIL_BACK");?></a></p>

		
		<?if($arParams["USE_REVIEW"]=="Y" && IsModuleInstalled("forum") && $ElementID):?>
		<hr id="reviews_block" />
		<?$APPLICATION->IncludeComponent(
			"bitrix:forum.topic.reviews",
			"",
			Array(
				"CACHE_TYPE" => $arParams["CACHE_TYPE"],
				"CACHE_TIME" => $arParams["CACHE_TIME"],
				"MESSAGES_PER_PAGE" => $arParams["MESSAGES_PER_PAGE"],
				"USE_CAPTCHA" => $arParams["USE_CAPTCHA"],
				"PATH_TO_SMILE" => $arParams["PATH_TO_SMILE"],
				"FORUM_ID" => $arParams["FORUM_ID"],
				"URL_TEMPLATES_READ" => $arParams["URL_TEMPLATES_READ"],
				"SHOW_LINK_TO_FORUM" => 'N',
				"ELEMENT_ID" => $ElementID,
				"AJAX_POST" => $arParams["REVIEW_AJAX_POST"],
				"IBLOCK_ID" => $arParams["IBLOCK_ID"],
				"POST_FIRST_MESSAGE" => $arParams["POST_FIRST_MESSAGE"],
				"URL_TEMPLATES_DETAIL" => $arParams["POST_FIRST_MESSAGE"]==="Y"? $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["detail"] :"",
			),
			$component
		);?>
		<?endif?>
	</div>
</div>

