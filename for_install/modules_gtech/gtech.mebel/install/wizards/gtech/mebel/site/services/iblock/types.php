<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();
if(CModule::IncludeModule("catalog")){	CCatalogGroup::Update("1", Array("BASE"=>"Y"));}
if(!CModule::IncludeModule("iblock"))
	return;

$arTypes = Array(
	array(
		"ID" => "catalog",
		"SECTIONS" => "Y",
		"IN_RSS" => "N",
		"SORT" => 100,
		"NAME" => GetMessage("CATALOG_TYPE_NAME"),
		"LANG" => Array()
	),
	array(
		"ID" => "content",
		"SECTIONS" => "Y",
		"IN_RSS" => "N",
		"SORT" => 500,
		"NAME" => GetMessage("CONTENT_TYPE_NAME"),
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
			$arType["LANG"][$languageID]["NAME"] = $arType["NAME"];
		}
		$iblockType->Add($arType);
	}
endforeach;
?>