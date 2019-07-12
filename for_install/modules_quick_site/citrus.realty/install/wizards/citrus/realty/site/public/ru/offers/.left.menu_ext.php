<?
if (!CModule::IncludeModule("iblock"))
	return;

if (!\Bitrix\Main\Loader::includeModule("citrus.realty"))
	return;

// nook make component with proper caching

/*
	$obCache = new CPHPCache;
	$life_time = 30*60;
	$cache_id = "root_left_menu_ext_cache";

	if($obCache->InitCache($life_time, $cache_id, "/")) :
	    $vars = $obCache->GetVars();
	    $aNewMenuLinks = $vars["menu_links"];

	else:
*/
$sections = CIBlockSection::GetList(Array("SORT"=>"ASC"), Array("SITE_ID" => SITE_ID, "IBLOCK_ID" => \Citrus\Realty\Helper::getIblock("offers"), "ACTIVE" => "Y", "DEPTH_LEVEL" => 1));
$sections->SetUrlTemplates();
$aNewMenuLinks = Array();
$prev_level = 1;

while ($section = $sections->GetNext())
{
	/*if ($prev_level < $section['DEPTH_LEVEL'])
		$aNewMenuLinks[count($aNewMenuLinks)-1][3]['IS_PARENT'] = true;*/
	$prev_level = $section['DEPTH_LEVEL'];
	//print_r($section);
	$aNewMenuLinks[] = Array(
		$section["NAME"],
		$section["SECTION_PAGE_URL"],
		Array(),
		Array(
			"FROM_IBLOCK" => true,
			"IS_PARENT" => false,
			"DEPTH_LEVEL" => $section['DEPTH_LEVEL'],
			"LEVEL" => $section['DEPTH_LEVEL']
		)
	);
}

/*
	endif;
*/
$aMenuLinks = array_merge($aMenuLinks, $aNewMenuLinks);
/*
	if($obCache->StartDataCache()):
	    $obCache->EndDataCache(array(
	        "menu_links"    => $aNewMenuLinks
	        ));
	endif;
*/

?>