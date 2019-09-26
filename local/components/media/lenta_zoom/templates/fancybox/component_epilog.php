<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$APPLICATION->AddHeadScript($this->getTemplate()->GetFolder() . '/lib/jquery.fancybox.pack.js');
$APPLICATION->SetAdditionalCss($this->getTemplate()->GetFolder() . '/lib/jquery.fancybox.css');
?>