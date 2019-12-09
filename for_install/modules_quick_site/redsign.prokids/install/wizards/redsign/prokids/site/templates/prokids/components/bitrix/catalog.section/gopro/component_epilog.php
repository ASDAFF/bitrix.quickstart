<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
//echo"<textarea>";print_r($templateData);echo"</textarea>";
if($arParams['IS_AJAXPAGES']=='Y') {
	global $JSON;
	$JSON['IDENTIFIER'] = $arParams['AJAXPAGESID'];
	$JSON['HTML'] = $templateData;
}

if( $arParams['IS_SORTERCHANGE']=='Y') {
	global $JSON;
	$JSON['HTMLBYID'] = $templateData;
}

if( $templateData['ADD_HIDER'] ) {
	?><script>RSGoPro_Hider();</script><?
}