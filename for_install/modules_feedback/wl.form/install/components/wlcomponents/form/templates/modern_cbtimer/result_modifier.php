<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();
global $APPLICATION;

$APPLICATION->SetAdditionalCSS($this->GetFolder() . '/font-awesome/css/font-awesome.min.css');
$APPLICATION->SetAdditionalCSS($this->GetFolder() . '/animate.css');
?>