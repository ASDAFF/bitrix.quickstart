<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if ('section' == $this->GetPageName())
{
	CModule::IncludeModule('catalog');
	$dbRes = CCatalogGroup::GetList(
		array(), array('NAME' => $arParams['PRICE_CODE'][0])
	);
	
	if ($arRes = $dbRes->Fetch())
		$arResult['_PRICE_ID'] = $arRes['ID'];
}
?>
