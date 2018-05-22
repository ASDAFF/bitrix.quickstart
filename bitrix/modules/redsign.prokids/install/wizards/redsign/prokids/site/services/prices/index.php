<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();
	
if(!CModule::IncludeModule('catalog'))
	return;

// set base price
$arCatalogGr = CCatalogGroup::GetBaseGroup();
if($arCatalogGr['ID'] < 1)
{
	$dbPriceType = CCatalogGroup::GetList(array('SORT' => 'ASC'),array('NAME' => 'BASE'));
	while($arPriceType = $dbPriceType->Fetch())
	{
		$ID = $arPriceType['ID'];
	}
	if($ID > 0)
	{
		$arFields = array('BASE' => 'Y');
		CCatalogGroup::Update($ID, $arFields);
	}
}

// rename prise types
$dbPriceType = CCatalogGroup::GetList(array('SORT' => 'ASC'),array());
while($arPriceType = $dbPriceType->Fetch())
{
	if($arPriceType['NAME_LANG']=='' || $arPriceType['NAME_LANG']==$arPriceType['NAME'])
	{
		$arNewFields = $arPriceType;
		$arLanguages = array('en','ru');
		foreach($arLanguages as $languageID)
		{
			WizardServices::IncludeServiceLang('index.php', $languageID);
			$arNewFields['USER_LANG'][$languageID] = GetMessage('PRICE_TYPE_NAME_'.$arPriceType['NAME']);
		}
		if(!CCatalogGroup::Update($arPriceType['ID'], $arNewFields))
		{
			// error
		}
	}
}