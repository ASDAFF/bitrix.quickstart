<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if(!CModule::IncludeModule("iblock"))
	return;

$iblockCode = "igrushka_".WIZARD_SITE_ID; 
$iblockType = "catalog"; 
$iblockID = false;
$iblockXMLFile =WIZARD_SERVICE_RELATIVE_PATH.'/xml/'.LANGUAGE_ID."/igrushka_data_1.xml"; 

$rsIBlock = CIBlock::GetList(array(), array("CODE" => $iblockCode, "TYPE" => $iblockType,'SITE_ID'=>WIZARD_SITE_ID));
if ($rsIBlock && $arIBlock = $rsIBlock->Fetch())
{
	$iblockID=$arIBlock['ID'];
	$arProperties = Array("NEWPRODUCT","SPECIALOFFER","SALELEADER");
	$arrPropID=array();
	foreach ($arProperties as $propertyName)
	{
		$arrPropID[$propertyName] = 0;
		$properties = CIBlockProperty::GetList(Array(), Array("ACTIVE"=>"Y", "IBLOCK_ID" => $iblockID, "CODE" => $propertyName));
		if ($arProperty = $properties->Fetch())
		{			
			$property_enums = CIBlockPropertyEnum::GetList(Array(), Array("IBLOCK_ID"=>$iblockID, "PROPERTY_ID"=>$arProperty["ID"],"XML_ID"=>"Y"));
			if($enum_fields = $property_enums->GetNext())
			{
			  	$arrPropID[$propertyName."_enum_Y"]=$enum_fields['ID'];			
			}
			else
			{
				$property_enums = CIBlockPropertyEnum::GetList(Array(), Array("IBLOCK_ID"=>$iblockID, "PROPERTY_ID"=>$arProperty["ID"],"XML_ID"=>"YYY"));
				if($enum_fields = $property_enums->GetNext())
				{
				  	$arrPropID[$propertyName."_enum_Y"]=$enum_fields['ID'];			
				}
				else
				{
					$property_enums = CIBlockPropertyEnum::GetList(Array(), Array("IBLOCK_ID"=>$iblockID, "PROPERTY_ID"=>$arProperty["ID"],"XML_ID"=>"YES"));
					if($enum_fields = $property_enums->GetNext())
					{
					  	$arrPropID[$propertyName."_enum_Y"]=$enum_fields['ID'];			
					}
				}
			}
		}
	}
	$arReplace=Array("igrushka_IBLOCK_TYPE" => $iblockType, "igrushka_IBLOCK_ID" => $iblockID);
	$arReplace=array_merge($arReplace,$arrPropID);
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH.WIZARD_SITE_DIR."catalog/index.php", Array("igrushka_IBLOCK_ID" => $iblockID,"igrushka_IBLOCK_TYPE" => $iblockType));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH.WIZARD_SITE_DIR."include/addToCartAjax.php", Array("igrushka_IBLOCK_ID" => $iblockID));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH.WIZARD_SITE_DIR."include/youHaveSeen.php", Array("igrushka_IBLOCK_ID" => $iblockID));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH.WIZARD_SITE_DIR."_index.php", $arReplace);
	COption::SetOptionString('smedia.childshop', 'catalog_installed', 'Y','', WIZARD_SITE_ID);

	return;
}

if ($iblockID == false)
{
	$findGr=array("sale_administrator","content_editor",);
	$arrGrId=array();
	foreach($findGr as $grCode)
	{
		$filter = Array
		(		   
		    "STRING_ID"  => $grCode // 嬠10 𐯫询᳥즩
		);
		$rsGroups = CGroup::GetList(($by="c_sort"), ($order="desc"), $filter); // 㼡鱠檠䱳𐰻
		if($arGroup=$rsGroups->GetNext())
			$arrGrId[$arGroup['STRING_ID']]=$arGroup['ID'];
	}
	$iblockID = WizardServices::ImportIBlockFromXML(
		$iblockXMLFile,
		'sm_igrushka_tmp',
		$iblockType,
		WIZARD_SITE_ID,
		$permissions = Array(
			"1" => "X",
		"2" => "R",
			// $arrGrId["sale_administrator"] => "W",
			// $arrGrId["content_editor"] => "W",

		)
	);
	if ($iblockID < 1)
		return;
		
	$arProperties = Array();
	$arrPropID=array();
	foreach ($arProperties as $propertyName)
	{
		$arrPropID[$propertyName] = 0;
		$properties = CIBlockProperty::GetList(Array(), Array("ACTIVE"=>"Y", "IBLOCK_ID" => $iblockID, "CODE" => $propertyName));
		if ($arProperty = $properties->Fetch())
			$arrPropID[$propertyName] = $arProperty["ID"];
	}

}

   $arProperties = Array("NEWPRODUCT","SPECIALOFFER","SALELEADER");
	$arrPropID=array();
	foreach ($arProperties as $propertyName)
	{
		$arrPropID[$propertyName] = 0;
		$properties = CIBlockProperty::GetList(Array(), Array("ACTIVE"=>"Y", "IBLOCK_ID" => $iblockID, "CODE" => $propertyName));
		if ($arProperty = $properties->Fetch())
		{			
			$property_enums = CIBlockPropertyEnum::GetList(Array(), Array("IBLOCK_ID"=>$iblockID, "PROPERTY_ID"=>$arProperty["ID"],"XML_ID"=>"Y"));
			if($enum_fields = $property_enums->GetNext())
			{
			  	$arrPropID[$propertyName."_enum_Y"]=$enum_fields['ID'];			
			}
		}
	}
	$arReplace=Array("igrushka_IBLOCK_TYPE" => $iblockType, "igrushka_IBLOCK_ID" => $iblockID);
	$arReplace=array_merge($arReplace,$arrPropID);
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH.WIZARD_SITE_DIR."include/addToCartAjax.php", Array("igrushka_IBLOCK_ID" => $iblockID));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH.WIZARD_SITE_DIR."include/youHaveSeen.php", Array("igrushka_IBLOCK_ID" => $iblockID));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH.WIZARD_SITE_DIR."catalog/index.php", Array("igrushka_IBLOCK_TYPE" => $iblockType, "igrushka_IBLOCK_ID" => $iblockID));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH.WIZARD_SITE_DIR."_index.php", $arReplace);
?>