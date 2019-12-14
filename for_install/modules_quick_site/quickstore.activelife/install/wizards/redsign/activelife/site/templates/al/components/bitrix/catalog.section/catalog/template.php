<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
/** @global CDatabase $DB */

//echo"<textarea>";print_r($this);echo"</textarea>";
//echo"<textarea>";print_r($arResult['NAV_RESULT']);echo"</textarea>";

$sTemplateExtPath = $_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/template_ext/catalog.section/al/template.php';
if (file_exists($sTemplateExtPath)) {
    include($sTemplateExtPath);    
}