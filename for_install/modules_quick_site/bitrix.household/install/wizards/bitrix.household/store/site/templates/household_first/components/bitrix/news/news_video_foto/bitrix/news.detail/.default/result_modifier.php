<?
$arProperty = &$arResult['DISPLAY_PROPERTIES'][$arParams["~PROPERTY_VIDEO"]];

if ($arProperty['DISPLAY_VALUE']) 
{
   global $APPLICATION;
   ob_start(); 
   ?>
   
	<?
	if (is_array($arProperty['DISPLAY_VALUE'])):
	foreach ($arProperty['DISPLAY_VALUE'] as $path) :?>
   <br/><br/>
	<?
   $APPLICATION->IncludeComponent(
      "bitrix:player",
      "",
      Array(
         "PLAYER_TYPE" => "auto", 
         "USE_PLAYLIST" => "N", 
         "PATH" => CFile::GetPath($path),
         "WIDTH" => "300", 
         "HEIGHT" => "225", 
         "FULLSCREEN" => "Y", 
         "SKIN_PATH" => "/bitrix/components/bitrix/player/mediaplayer/skins", 
         "SKIN" => "bitrix.swf", 
         "CONTROLBAR" => "bottom", 
         "WMODE" => "transparent", 
         "HIDE_MENU" => "N", 
         "SHOW_CONTROLS" => "Y", 
         "SHOW_STOP" => "N", 
         "SHOW_DIGITS" => "Y", 
         "CONTROLS_BGCOLOR" => "FFFFFF", 
         "CONTROLS_COLOR" => "000000", 
         "CONTROLS_OVER_COLOR" => "000000", 
         "SCREEN_COLOR" => "000000", 
         "AUTOSTART" => "N", 
         "REPEAT" => "N", 
         "VOLUME" => "90", 
         "DISPLAY_CLICK" => "play", 
         "MUTE" => "N", 
         "HIGH_QUALITY" => "Y", 
         "ADVANCED_MODE_SETTINGS" => "N", 
         "BUFFER_LENGTH" => "10", 
         "DOWNLOAD_LINK_TARGET" => "_self" 
      )
   ); ?>
   
   <?endforeach;
     else:?>
	 <br/>
		<?
	   $APPLICATION->IncludeComponent(
		  "bitrix:player",
		  "",
		  Array(
			 "PLAYER_TYPE" => "auto", 
			 "USE_PLAYLIST" => "N", 
			 "PATH" => CFile::GetPath($arProperty['DISPLAY_VALUE']),
			 "WIDTH" => "300", 
			 "HEIGHT" => "225", 
			 "FULLSCREEN" => "Y", 
			 "SKIN_PATH" => "/bitrix/components/bitrix/player/mediaplayer/skins", 
			 "SKIN" => "bitrix.swf", 
			 "CONTROLBAR" => "bottom", 
			 "WMODE" => "transparent", 
			 "HIDE_MENU" => "N", 
			 "SHOW_CONTROLS" => "Y", 
			 "SHOW_STOP" => "N", 
			 "SHOW_DIGITS" => "Y", 
			 "CONTROLS_BGCOLOR" => "FFFFFF", 
			 "CONTROLS_COLOR" => "000000", 
			 "CONTROLS_OVER_COLOR" => "000000", 
			 "SCREEN_COLOR" => "000000", 
			 "AUTOSTART" => "N", 
			 "REPEAT" => "N", 
			 "VOLUME" => "90", 
			 "DISPLAY_CLICK" => "play", 
			 "MUTE" => "N", 
			 "HIGH_QUALITY" => "Y", 
			 "ADVANCED_MODE_SETTINGS" => "N", 
			 "BUFFER_LENGTH" => "10", 
			 "DOWNLOAD_LINK_TARGET" => "_self" 
		  )
	   ); ?>
     <?endif;?>
   
   <?
   $arProperty['DISPLAY_VALUE'] = ob_get_contents(); 
   ob_clean(); 
   ob_end_clean(); 
}
?>


<?
$arProperty = &$arResult['DISPLAY_PROPERTIES'][$arParams["~PROPERTY_FOTO"]];
if ($arProperty['DISPLAY_VALUE']) 
{
   global $APPLICATION;
   ob_start(); 
   ?>
   
	 <br/>
		<?$APPLICATION->IncludeComponent("bitrix:photo.section", ".default", array(
	"IBLOCK_TYPE" => "",
	"IBLOCK_ID" => $arProperty["LINK_IBLOCK_ID"],
	"SECTION_ID" => $arProperty["VALUE"],
	"SECTION_CODE" => "",
	"SECTION_USER_FIELDS" => array(
		0 => "",
		1 => "",
	),
	"ELEMENT_SORT_FIELD" => "sort",
	"ELEMENT_SORT_ORDER" => "asc",
	"FILTER_NAME" => "arrFilter",
	"FIELD_CODE" => array(
		0 => "",
		1 => "",
	),
	"PROPERTY_CODE" => array(
		0 => "",
		1 => "",
	),
	"PAGE_ELEMENT_COUNT" => "20",
	"LINE_ELEMENT_COUNT" => "3",
	"SECTION_URL" => "",
	"DETAIL_URL" => "",
	"AJAX_MODE" => "N",
	"AJAX_OPTION_SHADOW" => "Y",
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
	"PAGER_TITLE" => "Фотографии",
	"PAGER_SHOW_ALWAYS" => "Y",
	"PAGER_TEMPLATE" => "",
	"PAGER_DESC_NUMBERING" => "N",
	"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
	"PAGER_SHOW_ALL" => "Y",
	"AJAX_OPTION_ADDITIONAL" => ""
	),
	false
);?>
   
   <?
   $arProperty['DISPLAY_VALUE'] = ob_get_contents(); 
   ob_clean(); 
   ob_end_clean(); 
}
?>
