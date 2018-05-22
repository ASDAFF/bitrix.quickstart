<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

if(!CModule::IncludeModule('iblock'))
	return;
if(!CModule::IncludeModule('catalog'))
	return;

// ____________________________________________________________________________________________________________________________ //

// take ID iblocks

$arrFilter1 = array(
	array(
		'IBLOCK_TYPE' => 'catalog',
		'IBLOCK_CODE' => 'catalog',
		'IBLOCK_XML_ID' => 'catalog_'.WIZARD_SITE_ID,
	),
	array(
		'IBLOCK_TYPE' => 'catalog',
		'IBLOCK_CODE' => 'offers',
		'IBLOCK_XML_ID' => 'offers_'.WIZARD_SITE_ID,
	),
);

foreach($arrFilter1 as $filter1)
{
	$rsIBlock = CIBlock::GetList(array(), array( 'TYPE' => $filter1['IBLOCK_TYPE'], 'CODE' => $filter1['IBLOCK_CODE'], 'XML_ID' => $filter1['IBLOCK_XML_ID'] ));
	if ($arIBlock = $rsIBlock->Fetch())
	{
		$code1 = $filter1['IBLOCK_CODE'];
		$arrIBlockIDs[$code1] = $arIBlock['ID'];
	}
}

// ____________________________________________________________________________________________________________________________ //

$resProp = CIBlockProperty::GetList(array('SORT'=>'ASC'),array('ACTIVE'=>'Y', 'IBLOCK_ID'=> $arrIBlockIDs['offers'],'CODE'=>'CML2_LINK'));
if($arProp = $resProp->GetNext())
{
	$PROP_LINK_ID = $arProp['ID'];

	$arFields = array(
		'IBLOCK_ID' => $arrIBlockIDs['offers'],
		'LINK_IBLOCK_ID' => $arrIBlockIDs['catalog'],
		'USER_TYPE' => 'SKU',
		'XML_ID' => 'CML2_LINK',
		'MULTIPLE' => 'N',
		'PROPERTY_TYPE' => 'E',
		'FILTRABLE' => 'Y',
		'SEARCHABLE' => 'N',
	);
	$ibp = new CIBlockProperty;
	if($ibp->Update($PROP_LINK_ID, $arFields))
	{
		$arCatalog = CCatalog::GetByID($arrIBlockIDs['offers']);
		if ($arCatalog)
		{
			CCatalog::Update($arrIBlockIDs['offers'],array('PRODUCT_IBLOCK_ID' => $arrIBlockIDs['catalog'],'SKU_PROPERTY_ID' => $PROP_LINK_ID));
		} else {
			CCatalog::Add(array('IBLOCK_ID' => $arrIBlockIDs['offers'], 'PRODUCT_IBLOCK_ID' => $arrIBlockIDs['catalog'], 'SKU_PROPERTY_ID' => $PROP_LINK_ID));
		}
	}
}