<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

if (count($arParams) > 2)
{
	$cache_id = md5(serialize($arParams));
	$cache_dir = "/nsandrey.sitemap/";
	$obCache = new CPHPCache();

	if($obCache->InitCache(3600, $cache_id, $cache_dir))
	{
		$arResult = $obCache->GetVars();
	}
	else if(CModule::IncludeModule('nsandrey.sitemap') && $obCache->StartDataCache())
	{
		$sitemap = new CSitemapStructure($_SERVER['DOCUMENT_ROOT']);

		$arResult['MAP_CONTENT'] = $sitemap->getSitemap($arParams);

		$obCache->EndDataCache($arResult);
	}
}
else
{
	ShowError(GetMessage('NSANDREY_SITEMAP_NO_SETTINGS'));
	return;
}

$this->IncludeComponentTemplate();