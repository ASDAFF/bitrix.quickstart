<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arParams['CACHE_GROUPS'] = $arParams['CACHE_GROUPS'] == 'N' ? 'N' : 'Y';


$this->SetResultCacheKeys(array('IDS'));

$arResult['CAT'] = array();
$arResult['IDS'] = array();

if ($this->StartResultCache(false, $arParams['CACHE_GROUPS'] == 'Y' ? $USER->GetGroups() : false))
{
	if (!CModule::IncludeModule('catalog'))
	{
		$this->AbortResultCache();
		return;
	}

	$dbRes = CIBlock::GetList(
		array('SORT' => 'ASC', 'ID' => 'DESC', 'NAME' => 'ASC'),
		array('TYPE' => 'catalog', 'LID' => SITE_ID)
	);
	$dbRes = new CIBlockResult($dbRes);
	while ($arRes = $dbRes->GetNext())
	{
		if(defined("BX_COMP_MANAGED_CACHE"))
			$GLOBALS["CACHE_MANAGER"]->RegisterTag("iblock_id_".$arRes["ID"]);

		if($arRes["ACTIVE"] == "Y")
		{
			if ($arRes['PICTURE'])
				$arRes['PICTURE'] = CFile::GetFileArray($arRes['PICTURE']);

			$arCatalog[$arRes['ID']] =  array(
				'ID' => $arRes['ID'],
				'LIST_PAGE_URL' => $arRes['LIST_PAGE_URL'],
				'PICTURE' => $arRes['PICTURE'],
				'DESCRIPTION' => $arRes['DESCRIPTION_TYPE'] == 'text' ? $arRes['DESCRIPTION'] : $arRes['~DESCRIPTION'],
				'NAME' => $arRes['NAME'],
			);
			$arResult['CAT'][$arRes['ID']] = $arCatalog[$arRes['ID']];
			$arResult['IDS'][] = $arRes['ID']; 
		}
	}

	if(defined("BX_COMP_MANAGED_CACHE"))
		$GLOBALS["CACHE_MANAGER"]->RegisterTag("iblock_id_new");

	foreach ($arResult['CAT'] as $arCat)
	{
		$dbRes = CIBlockSection::GetList(
			array('SORT' => 'ASC', 'NAME' => 'ASC'),
			array('ACTIVE' => 'Y', 'DEPTH_LEVEL' => 1, 'IBLOCK_ID' => $arCat['ID'])
		);

		$arResult['CAT'][$arCat['ID']]['CHILDREN'] = array();
		while ($arRes = $dbRes->GetNext())
		{
			$arResult['CAT'][$arCat['ID']]['CHILDREN'][$arRes['ID']] = array(
				'ID' => $arRes['ID'],
				'NAME' => $arRes['NAME'],
				'SECTION_PAGE_URL' => $arRes['SECTION_PAGE_URL'],
			);
		}
	}

	$this->IncludeComponentTemplate();
}

if (count($arResult['CAT']) == 1)
{
	$arCat = array_shift($arResult);
	LocalRedirect($arCat['LIST_PAGE_URL']);
}

?>