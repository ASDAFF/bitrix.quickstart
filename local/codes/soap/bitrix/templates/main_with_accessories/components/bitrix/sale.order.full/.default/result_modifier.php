<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die(); 



if (!$USER->IsAuthorized()) {
    CModule::IncludeModule('sale');
    $arResult["PERSON_TYPE_INFO"] = Array();
    $dbPersonType = CSalePersonType::GetList(
                    array("SORT" => "ASC", "NAME" => "ASC"), array("LID" => SITE_ID, "ACTIVE" => "Y")
    );
    $bFirst = True;
    while ($arPersonType = $dbPersonType->GetNext()) {
        if (IntVal($arResult["POST"]["PERSON_TYPE"]) == IntVal($arPersonType["ID"]) || IntVal($arResult["POST"]["PERSON_TYPE"]) <= 0 && $bFirst)
            $arPersonType["CHECKED"] = "Y";
        $arResult["PERSON_TYPE_INFO"][] = $arPersonType;
        $bFirst = False;
    }
}