<?
/**
 * @var CBitrixComponent $this
 */
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

// הכÿ js-פאיכמג
$APPLICATION->AddHeadScript('/local/templates/demoshop/js/jquery.bxslider.min.js');
// הכÿ css-פאיכמג
$APPLICATION->SetAdditionalCSS("/local/templates/demoshop/css/jquery.bxslider.css");

$this->includeComponentTemplate();
?>