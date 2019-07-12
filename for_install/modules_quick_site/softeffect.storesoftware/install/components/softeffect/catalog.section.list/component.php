<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if (!$this->InitComponentTemplate())
	return;

$template = &$this->GetTemplate();
//var_dump($template->GetFolder());

$arParams["CATALOG_SECTION_CODE"] = trim($arParams["CATALOG_SECTION_CODE"]);
if (strlen($arParams["CATALOG_SECTION_CODE"])<=0) {
	ShowError(GetMessage('SE_CATALOGSECTION_NOSECTION'));
	return;
}

$arParams["CATALOG_SECTION_L2_CODE"] = trim($arParams["CATALOG_SECTION_L2_CODE"]);
if (strlen($arParams["CATALOG_SECTION_L2_CODE"])<=0) {
	ShowError(GetMessage('SE_CATALOGSECTIONLIST_L2_NOSECTION'));
	return;
}

if(!CModule::IncludeModule("iblock")) {
	$this->AbortResultCache();
	ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
	return;
}

if(!CModule::IncludeModule("catalog")) {
	$this->AbortResultCache();
	ShowError(GetMessage("CATALOG_MODULE_NOT_INSTALLED"));
	return;
}

$dbSec = CIBlockSection::GetList(array(), array('IBLOCK_ID'=>$IB_CATALOG, 'CODE'=>$arParams["CATALOG_SECTION_CODE"]), FALSE, array('UF_*'));
if ($arSec = $dbSec->GetNext()) {
	$APPLICATION->AddChainItem($arSec['NAME'], SITE_DIR.'catalog/'.$arParams["CATALOG_SECTION_CODE"].'/');
	$dbSecL2 = CIBlockSection::GetList(array(), array('IBLOCK_ID'=>$IB_CATALOG, 'CODE'=>$arParams["CATALOG_SECTION_L2_CODE"], 'SECTION_ID'=>$arSec['ID']), FALSE, array('UF_*'));
	if ($arSecL2 = $dbSecL2->GetNext()) {
	
		// добавляем в цепочку элемент и ставим тайтл
		$APPLICATION->AddChainItem($arSecL2['NAME']);

		$name_rus = (strlen($arSecL2['UF_NAME_RUS'])>0) ? '('.$arSecL2['UF_NAME_RUS'].')'    : FALSE;
		
		$h1 = (strlen($arSecL2['UF_H1'])>0) ? $arSecL2['UF_H1'] : $arSec['NAME'].' - '.$arSecL2['NAME'].' '.$name_rus;
	
		$title = (strlen($arSecL2['UF_TITLE'])>0) ? $arSecL2['UF_TITLE'] : $h1;
		$desrc = (strlen($arSecL2['UF_DESCR'])>0) ? $arSecL2['UF_DESCR'] : $h1;
		$keyw = (strlen($arSecL2['UF_KEYW'])>0) ? $arSec['NAME'].' , '.$arSecL2['NAME'].$arSecL2['UF_KEYW'] : $arSec['NAME'].' , '.$arSecL2['NAME'];

		$APPLICATION->SetTitle($title);
		if ($desrc) {
			$APPLICATION->SetPageProperty("description", $desrc);
		}
		if ($keyw) {
			$APPLICATION->SetPageProperty("keywords", $keyw);
		}
	}
} else {
	CHTTP::SetStatus("404 Not Found");
	LocalRedirect(SITE_DIR.'404.php', FALSE, '404 Not Found');
}

$arResult = array(
	'ACTIONS'=>array(),
	'AJAX_PATH' => array(
		'COMPONENT' => $this->__path.'/include/ajax.php',
		'COMPONENT_FOLDER' => $this->__path,
		'TEMPLATE' => $template->GetFolder().'/ajax.php',
		'TEMPLATE_FOLDER' => $template->GetFolder()
	),
	'CATALOG_IBLOCK_ID' => $arParams['IBLOCK_CATALOG_ID'],
	'CATALOG_SECTION_CODE' => $arParams['CATALOG_SECTION_CODE'],
	'CATALOG_SECTION_L2_CODE' => $arParams['CATALOG_SECTION_L2_CODE'],
	'META' => array(
		'H1' => $h1
	)
);

// Акции по разделу
$dbEl = CIBlockElement::GetList(array("SORT"=>"ASC"), array('IBLOCK_ID'=>$IB_ACTIONS, 'ACTIVE'=>'Y',">ACTIVE_DATE" => date('d.m.Y'),'PROPERTY_SECTION'=>$arSec['ID']), FALSE, FALSE, array('DETAIL_PAGE_URL','NAME','IBLOCK_ID', 'ID','PROPERTY_CML2_ARTICLE','PROPERTY_PICTURE_BREND'));
while ($arEl = $dbEl->GetNext()) {
	$renderImage= CFile::ResizeImageGet($arEl['PROPERTY_PICTURE_BREND_VALUE'], Array("width" => 300,"height" => 91), BX_RESIZE_IMAGE_PROPORTIONAL_ALT, TRUE);
	$tmp = array(
		'NAME' => $arEl['NAME'],
		'URL' => $arEl['DETAIL_PAGE_URL'],
		'PICTURE_BREND'=>$renderImage['src'],
	);
	
	$arResult['ACTIONS'][] = $tmp;
}

$_SESSION['arSec'] = $arSec;
$_SESSION['arSecL2'] = $arSecL2;
$_SESSION['arResultComponent'] = $arResult;

$this->IncludeComponentTemplate();
?>
