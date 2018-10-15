<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arParams["TYPE"] = (isset($arParams["TYPE"]) ? trim($arParams["TYPE"]) : "");

if($arParams["NOINDEX"] <> "Y") {
	$arParams["NOINDEX"] = "N";
}

if(!isset($arParams['LIMIT'])) {
	$arParams['LIMIT'] = 5;
}

if (is_numeric($arParams['WIDTH'])) {
	$arParams['WIDTH'] = intval($arParams['WIDTH']).'px';
}

if (is_numeric($arParams['HEIGHT'])) {
	$arParams['HEIGHT'] = intval($arParams['HEIGHT']).'px';
}

$arResult = array();

$obCache = new CPHPCache;
$cache_id = SITE_ID."|banner_rotation|".serialize($arParams)."|".$USER->GetGroups();
$cache_path = "/".SITE_ID.$this->GetRelativePath();

if ($obCache->StartDataCache($arParams["CACHE_TIME"], $cache_id, $cache_path))
{
	if ($arParams['SOURCE'] == 'medialib') {
		
		for ($i=1; $i <= $arParams['LIMIT']; $i++) {
			
			$banner_html = '';

			if (strpos($arParams['BANNER_IMAGE_'.$i], '.swf') !== false) {				
				
				if ($arParams['BANNER_HREF_'.$i]) {
					$banner_html = '<div style="position:absolute;"><a href="'.$arParams['BANNER_HREF_'.$i].'"><img src="/bitrix/images/1.gif" width="'.str_replace('px', '', $arParams['WIDTH']).'" height="'.str_replace('px', '', $arParams['HEIGHT']).'" border="0" /></a></div>';
				}
					
				$banner_html .= '
<OBJECT
	classid="clsid:D27CDB6E-AE6D-11CF-96B8-444553540000"
	codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0"
	WIDTH="'.str_replace('px', '', $arParams['WIDTH']).'"
	HEIGHT="'.str_replace('px', '', $arParams['HEIGHT']).'">
		<PARAM NAME="movie" VALUE="'.$arParams['BANNER_IMAGE_'.$i].'" />
		<PARAM NAME="quality" VALUE="high" />
		<PARAM NAME="bgcolor" VALUE="#FFFFFF" />
		<PARAM NAME="wmode" VALUE="opaque" />
		<EMBED
			src="'.$arParams['BANNER_IMAGE_'.$i].'"
			quality="high"
			bgcolor="#FFFFFF"
			wmode="opaque"
			WIDTH="'.str_replace('px', '', $arParams['WIDTH']).'"
			HEIGHT="'.str_replace('px', '', $arParams['HEIGHT']).'"
			NAME="banner"
			TYPE="application/x-shockwave-flash"
			PLUGINSPAGE="http://www.macromedia.com/go/getflashplayer">
		</EMBED>
</OBJECT>';
				
			} else {		
				if ($arParams['BANNER_HREF_'.$i]) {
					$banner_html = '<a href="'.$arParams['BANNER_HREF_'.$i].'"><img alt="'.$arParams['BANNER_NAME_'.$i].'" src="'.$arParams['BANNER_IMAGE_'.$i].'"/></a>';
					if ($arParams["NOINDEX"] == "Y") {
						$banner_html = '<noindex>'.$banner_html.'</noindex>';
					}
				} else {
					$banner_html = '<img alt="'.$arParams['BANNER_NAME_'.$i].'" src="'.$arParams['BANNER_IMAGE_'.$i].'"/>';
				}		
			}
			
			$arResult["BANNERS"][] = array (
				'HTML' => $banner_html,
				'FIELDS' => array(
					'NAME' => $arParams['BANNER_NAME_'.$i],			
					'IMAGE_ALT' => $arParams['BANNER_NAME_'.$i],
					'IMAGE_ID' => $_SERVER['DOCUMENT_ROOT'].$arParams['BANNER_IMAGE_'.$i]
				)
			);
		
		}
	} else {
		if(!CModule::IncludeModule("advertising"))
			return;
	
		$arFilter['TYPE_SID'] = $arParams["TYPE"];
		$arFilter['TYPE_SID_EXACT_MATCH'] = 'Y';	
		$arFilter['LAMP'] = 'green';
		$arFilter['SITE'] = SITE_ID;
		$rsBanners = CAdvBanner::GetList(($by="s_weight"), ($order="desc"), $arFilter, $is_filtered, "N");
		$rsBanners->NavStart($arParams['LIMIT']);
		while($arBanner = $rsBanners->GetNext())
		{		
			if($banner_html = CAdvBanner::GetHTML($arBanner, ($arParams["NOINDEX"] == "Y"))) {
				$arResult["BANNERS"][] = array (
					'HTML' => $banner_html,
					'FIELDS' => $arBanner
				);	
				CAdvBanner::FixShow($arBanner);
			}
		}
	}

	if (!empty($arResult["BANNERS"])) {
		$this->IncludeComponentTemplate();
	}

	$templateCachedData = $this->GetTemplateCachedData();

	$obCache->EndDataCache(
		Array(
			"arResult" => $arResult,
			"templateCachedData" => $templateCachedData
		)
	);
}
else
{
	$arVars = $obCache->GetVars();
	$arResult = $arVars["arResult"];
	$this->SetTemplateCachedData($arVars["templateCachedData"]);
}
?>