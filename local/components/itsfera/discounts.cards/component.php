<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

CModule::IncludeModule("iblock");

global $USER;
if ($USER->IsAuthorized())
{

    $IBLOCK_ID = getIBlockIdByCode("discount_cards");
    $user_id = cuser::getid();

    $arSelect = Array("ID", "NAME", "PROPERTY_PERCENT", "PROPERTY_TOTAL", "PROPERTY_CARDTYPE");
    $arFilter = Array("IBLOCK_ID"=>$IBLOCK_ID, "ACTIVE"=>"Y", "PROPERTY_USER_ID"=>$user_id);
    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
    if($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $arFields["PROPERTIES"] = $ob->GetProperties();
        $arResult['CARD'] = $arFields;
    }
    $arResult['AUTH'] = "Y";
}
else
{
    $arResult['AUTH'] = "N";
}
$this->IncludeComponentTemplate();
