<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
if(!CModule::IncludeModule("v1rt.personal")) die();
$match = array();
preg_match("/#(.*)#/", $arParams["DETAIL_URL"], $match);

if($arParams["CHILDREN"] == "Y" && is_numeric($arParams["FOLDERS"][0]) && $arParams["FOLDERS"][0] > 0)
{
    $f = CMediaComponents::getChildren($arParams["FOLDERS"][0]);
    unset($arParams["FOLDERS"]);
    $arParams["FOLDERS"] = $f;
}

if($this->StartResultCache(false, array($arParams["FOLDERS"], $arParams["DETAIL_URL"], $match)))
{
    if(($count = count($arParams["FOLDERS"])) > 0)
    {
        $arResult["LIB"] = CMediaComponents::getList($arParams["FOLDERS"], $match, $arParams["DETAIL_URL"]);
        $this->IncludeComponentTemplate();
    }
}
?>