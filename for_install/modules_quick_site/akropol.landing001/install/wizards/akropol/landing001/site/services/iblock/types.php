<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if(!CModule::IncludeModule("iblock"))
	return;

$arTypes = Array();


$arTypeTemplate = Array(
		"ID" => "other",
		"SECTIONS" => "Y",
		"IN_RSS" => "N",
		"SORT" => 50,
		"LANG" => Array(), 
);
array_push($arTypes, $arTypeTemplate);



$arLanguages = Array();
$rsLanguage = CLanguage::GetList($by, $order, array());
while($arLanguage = $rsLanguage->Fetch())
	$arLanguages[] = $arLanguage["LID"];

$iblockType = new CIBlockType;
foreach($arTypes as $arType)
{
	$dbType = CIBlockType::GetList(Array(),Array("=ID" => $arType["ID"]));
	if($dbType->Fetch())
		continue;

	foreach($arLanguages as $languageID)
	{
		WizardServices::IncludeServiceLang("type.php", $languageID);

		$arType["LANG"][$languageID]["NAME"] = GetMessage("TEMPLATES_TYPE_NAME");
		$arType["LANG"][$languageID]["ELEMENT_NAME"] = GetMessage("TEMPLATES_ELEMENT_NAME");

		if ($arType["SECTIONS"] == "Y")
			$arType["LANG"][$languageID]["SECTION_NAME"] = GetMessage("TEMPLATES_SECTION_NAME");
	}

	$iblockType->Add($arType);
}
?>