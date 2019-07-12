<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if(!CModule::IncludeModule("iblock"))
	return;

$arTypes = Array(
	Array(
		"ID" => "el_news",
		"SECTIONS" => "Y",
		"IN_RSS" => "Y",
		"SORT" => 50,
		"LANG" => Array(),
	),
	Array(
		"ID" => "el_products",
		"SECTIONS" => "Y",
		"IN_RSS" => "N",
		"SORT" => 150,
		"LANG" => Array(),
	)
    
);



$arLanguages = Array();
$rsLanguage = CLanguage::GetList($by, $order, array());
while($arLanguage = $rsLanguage->Fetch())
	$arLanguages[] = $arLanguage["LID"];
$i=0;
$iblockType = new CIBlockType;
foreach($arTypes as $arType)
{
    $i++;
	$dbType = CIBlockType::GetList(Array(),Array("=ID" => $arType["ID"]));
	if($dbType->Fetch())
		continue;

	foreach($arLanguages as $languageID)
	{
		WizardServices::IncludeServiceLang("type.php", $languageID);

		$code = $arType["ID"];
		$arType["LANG"][$languageID]["NAME"] = GetMessage($code."_TYPE_NAME");
		$arType["LANG"][$languageID]["ELEMENT_NAME"] = GetMessage($code."_ELEMENT_NAME");

		if ($arType["SECTIONS"] == "Y")
			$arType["LANG"][$languageID]["SECTION_NAME"] = GetMessage($code."_SECTION_NAME");
	}

	$ID = $iblockType->Add($arType);

}
?>