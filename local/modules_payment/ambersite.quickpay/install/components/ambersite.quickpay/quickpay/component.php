<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
if($arParams['USETITLE']=='Y') $APPLICATION->SetTitle(GetMessage("ZAKAZ_NA_OPLATY"));
if(CModule::IncludeModule('ambersite.quickpay') && $_REQUEST['sha1_hash'] && $_REQUEST['label'] && $arParams['SECRETKEY']) QuickPay::Confirm($arParams['SECRETKEY'], $_REQUEST);
require($_SERVER['DOCUMENT_ROOT'].$this->GetPath().'/errors.php'); if($error) return;

if(CModule::IncludeModuleEx('ambersite.quickpay')) {
	if(!$_REQUEST['qp_order']) $arResult = QuickPay::QuickpayResult($arParams);
	if($_REQUEST['qp_order']) $arResult = QuickPay::QuickpayResult($arParams, $_REQUEST['qp_order']);
} else {ShowError(GetMessage('MODUL_NE_NAIDEN')); return;}

if($arResult['ORDER'] && !$arResult['ORDERCORRECT']) {ShowError(GetMessage("NEKORREKTNUJ_KOD_ZAKAZA")); return;}

$arResult['SENDPATH'] = $this->GetPath().'/send.php';

$this->IncludeComponentTemplate();

if($arParams['JQUERY']=='Y') CJSCore::Init('jquery');
if($arParams['FONT_RC']=='Y') $APPLICATION->SetAdditionalCSS('https://fonts.googleapis.com/css?family=Roboto+Condensed:400,700,400italic&subset=latin,cyrillic');
$APPLICATION->SetAdditionalCSS($this->GetPath().'/lib/jquery.formstyler.css'); 
$APPLICATION->AddHeadScript($this->GetPath().'/lib/jquery.formstyler.min.js'); 
$APPLICATION->AddHeadScript($this->GetPath().'/lib/jquery.maskedinput.min.js');
$APPLICATION->AddHeadString('<meta name="robots" content="noindex">', true);

?>