<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) 
	die();

if(!CModule::IncludeModule("iblock"))
	return;

$arTypes = Array(
	array(
		"ID" => "catalogs",
		"SECTIONS" => "Y",
		"IN_RSS" => "N",
		"SORT" => 100,
		"LANG" => Array()
	),
);

$arLanguages = Array();
$rsLanguage = CLanguage::GetList($by, $order, array());
while($arLanguage = $rsLanguage->Fetch())
	$arLanguages[] = $arLanguage["LID"];

foreach($arTypes as $arType):
	$iblockType = new CIBlockType;
	$dbType = CIBlockType::GetList(Array(),Array("=ID" => $arType["ID"]));
	if(!$dbType->Fetch()){	
		foreach($arLanguages as $languageID)
		{
			WizardServices::IncludeServiceLang("types.php", $languageID);
	
			$code = $arType["ID"];
			$arType["LANG"][$languageID]["NAME"] = GetMessage("CATALOGS_TYPE_NAME");
		}
		$iblockType->Add($arType);
	}
endforeach;
?>