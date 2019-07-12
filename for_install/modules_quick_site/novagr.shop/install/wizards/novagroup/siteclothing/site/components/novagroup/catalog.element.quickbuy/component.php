<?
if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

if( CModule::IncludeModule("iblock") ) {
} else {
	die(GetMessage("MODULES_NOT_INSTALLED"));
}

global $USER;
$arParams['USER_EMAIL'] = $USER->GetEmail();

if(isset($_POST) and count($_POST)>0)
{
    $arParams['REQUEST'] = $requests = Novagroup_Classes_General_Main::getRequest();

    $orders = new Novagroup_Classes_General_QuickOrder((int)$arParams['ORDER_LIST_IBLOCK_ID'],(int)$arParams['ORDER_PRODUCT_IBLOCK_ID']);
    $p = $orders->addOrderProduct($requests);
    $o =$orders->addOrder($requests);
    if($orders->hasErrors())
    {
        $templateName = "error";
        $arResult['ERROR'] = $orders->getErrors();
    } else {
        $templateName = "ok";
    }
} else {
    $templateName = null;
}
$this->IncludeComponentTemplate($templateName);