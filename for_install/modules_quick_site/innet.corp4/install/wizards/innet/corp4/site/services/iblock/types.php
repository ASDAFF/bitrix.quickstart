<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if (!CModule::IncludeModule("iblock"))
    return;

$arTypes = array(
    array(
        "ID" => "innet_catalog_" . WIZARD_SITE_ID,
        "SECTIONS" => "Y",
        "IN_RSS" => "Y",
        "SORT" => 10,
        "LANG" => array(),
    ),
	array(
        "ID" => "innet_objects_" . WIZARD_SITE_ID,
        "SECTIONS" => "Y",
        "IN_RSS" => "Y",
        "SORT" => 20,
        "LANG" => array(),
    ),
    array(
        "ID" => "innet_forms_" . WIZARD_SITE_ID,
        "SECTIONS" => "N",
        "IN_RSS" => "N",
        "SORT" => 30,
        "LANG" => array(),
    ),
);

$arLanguages = array();
$rsLanguage = CLanguage::GetList($by, $order, array());
while ($arLanguage = $rsLanguage->Fetch())
    $arLanguages[] = $arLanguage["LID"];

$iblockType = new CIBlockType;
foreach ($arTypes as $key => $arType) {
	
    $dbType = CIBlockType::GetList(array(), array("=ID" => $arType["ID"]));
    if ($dbType->Fetch())
        continue;

    foreach ($arLanguages as $languageID) {
        WizardServices::IncludeServiceLang("type.php", $languageID);

        $code = strtoupper($arType["ID"]);
        $arType["LANG"][$languageID]["NAME"] = GetMessage($code . "_TYPE_NAME");
        $arType["LANG"][$languageID]["ELEMENT_NAME"] = GetMessage($code . "_ELEMENT_NAME");

        if ($arType["SECTIONS"] == "Y")
            $arType["LANG"][$languageID]["SECTION_NAME"] = GetMessage($code . "_SECTION_NAME");
    }

    $iblockType->Add($arType);
}
?>