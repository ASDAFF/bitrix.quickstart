<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arParams['COUNTDOWN_ID'] = (trim($arParams['COUNTDOWN_ID'])=='') ? 'countdown_dashboard'.$arParams['ID'] : $arParams['COUNTDOWN_ID'];

$arResult["ID"] = false;
$action = new Novagroup_Classes_General_TimeToBuy($arParams['ID'], $arParams['IBLOCK_ID']);
if ($action->checkAction()) {
    $arResult = $action->getAction();
    $this->IncludeComponentTemplate();
}
return $arResult["ID"];
?>