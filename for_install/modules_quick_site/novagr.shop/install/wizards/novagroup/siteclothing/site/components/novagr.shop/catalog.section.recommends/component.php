<?
/**
 * @var CBitrixComponent $this
 */
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

// ��� js-������
$APPLICATION->AddHeadScript('/local/templates/demoshop/js/jquery.bxslider.min.js');
// ��� css-������
$APPLICATION->SetAdditionalCSS("/local/templates/demoshop/css/jquery.bxslider.css");

$this->includeComponentTemplate();
?>