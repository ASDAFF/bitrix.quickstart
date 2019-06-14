<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!CModule::IncludeModule('redsign.monopoly'))
	return;

$headType = RSMonopoly::getSettings('headType', 'type1');
$headStyle = RSMonopoly::getSettings('headStyle', 'style1');

$arParams['HEADER_TYPE'] = '';
$arParams['PATH_TO_HEADER_TYPES'] = SITE_DIR.'include/header/';
$arParams['HEAD_ADD_CSS_NAME'] = 'border';
if( $headStyle=='style1' ) {
	$arParams['HEAD_ADD_CSS_NAME'] = 'color';
} elseif( $headStyle=='style2' ) {
	$arParams['HEAD_ADD_CSS_NAME'] = 'no-border';
}

if( $arParams['AREA_FILE_SHOW']=='file' && ($headType!='type1' || $headStyle!='style1') ) {
	$arParams['HEADER_TYPE'] = 'head_'.$headType.'_menu.php';
	$filePath = $_SERVER['DOCUMENT_ROOT'].$arParams['PATH_TO_HEADER_TYPES'].$arParams['HEADER_TYPE'];
	$io = CBXVirtualIo::GetInstance();
	$bFileFound = $io->FileExists($filePath);
	if( $bFileFound ) {
		$arResult["FILE"] = $io->GetPhysicalName( $filePath );
	}
}
