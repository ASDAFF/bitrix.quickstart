<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if(CModule::IncludeModule("catalog")){
	CCatalogGroup::Update("1", Array("BASE"=>"Y"));

	$dbResultList = CCatalogGroup::GetList(Array(), Array("CODE" => "BASE"));
	if(!($dbResultList->Fetch()))
	{
	    $arFields = Array();
	    $rsLanguage = CLanguage::GetList($by, $order, array());
	    while($arLanguage = $rsLanguage->Fetch())
	    {
	        WizardServices::IncludeServiceLang("catalog.php", $arLanguage["ID"]);
	        $arFields["USER_LANG"][$arLanguage["ID"]] = GetMessage("WIZ_PRICE_NAME");
	    }
	    $arFields["BASE"] = "Y";
	    $arFields["SORT"] = 100;
	    $arFields["NAME"] = "BASE";
	    $arFields["USER_GROUP"] = Array(1, 2);
	    $arFields["USER_GROUP_BUY"] = Array(1, 2);
	    CCatalogGroup::Add($arFields);
	}
}
if(!CModule::IncludeModule("iblock"))
	return;


	
$arTypes = Array(
	Array(
		"ID" => "news",
		"SECTIONS" => "N",
		"IN_RSS" => "Y",
		"SORT" => 200,
		"LANG" => Array(),
	),
	Array(
		"ID" => "catalog",
		"SECTIONS" => "Y",
		"IN_RSS" => "N",
		"SORT" => 100,
		"LANG" => Array(),
	),
	Array(
		"ID" => "services",
		"SECTIONS" => "Y",
		"IN_RSS" => "N",
		"SORT" => 300,
		"LANG" => Array(),
	),
);

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

		$code = strtoupper($arType["ID"]);
		if(GetMessage($code."_TYPE_NAME") && strlen(GetMessage($code."_TYPE_NAME"))>0)
			$arType["LANG"][$languageID]["NAME"] = GetMessage($code."_TYPE_NAME");			
		else
			$arType["LANG"][$languageID]["NAME"] = $code;			

		$arType["LANG"][$languageID]["ELEMENT_NAME"] = GetMessage($code."_ELEMENT_NAME");

		if ($arType["SECTIONS"] == "Y")
			$arType["LANG"][$languageID]["SECTION_NAME"] = GetMessage($code."_SECTION_NAME");
	}

	$iblockType->Add($arType);
}

COption::SetOptionString('iblock','combined_list_mode','Y');
?>