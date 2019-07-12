<?
/**
 * компонент выводит наиболее часто встречающиеся поисковые запросы
 * 
 */
if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 3600;

if(!isset($arParams["LIMIT"]))
	$arParams["LIMIT"] = 4;

$arFilter["!PHRASE"] = '';
$arFilter["SITE_ID"] = SITE_ID;
$arFields = array('COUNT','RESULT_COUNT','PHRASE','SITE_ID');

if ($this->StartResultCache(false, array($USER->GetGroups())))
{
	if(!CModule::IncludeModule("search"))
	{
		$this->AbortResultCache();
		ShowError('SEARCH MODULE NOT INSTALLED');
		return;
	}

	$rsData = CSearchStatistic::GetList(array('COUNT' => 'DESC','RESULT_COUNT'=>'DESC'), $arFilter, $arFields, true);
	$arResult["SEARCH"] = array();

	while ($data = $rsData -> Fetch())
	{
        $url = CHTTP::urlAddParams(
            str_replace("#SITE_DIR#", SITE_DIR, $arParams['PAGE'])
            ,array("q" => $data['PHRASE'])
            ,array("encode"=>true)
        );

        $arResult["SEARCH"][$data['PHRASE']] = array(
            "NAME" => $data['PHRASE'],
            "URL" => htmlspecialcharsex($url),
            "DATA" => $data,
        );

        if (count($arResult["SEARCH"])>=$arParams["LIMIT"]) break;
    }
	
	$this->IncludeComponentTemplate();
}
?>