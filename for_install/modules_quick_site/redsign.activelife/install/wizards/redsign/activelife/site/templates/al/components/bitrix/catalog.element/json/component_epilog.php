<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();
/** @var array $templateData */
/** @var @global CMain $APPLICATION */

$APPLICATION->RestartBuffer();
if ('utf-8' != strtolower(SITE_CHARSET)) {
	$data = $APPLICATION->ConvertCharsetArray($templateData['JSON_EXT'], SITE_CHARSET, 'utf-8');
	$json_str_utf = json_encode($data);
	$json_str = $APPLICATION->ConvertCharset($json_str_utf, 'utf-8', SITE_CHARSET);
	echo $json_str;
} else {
	echo json_encode($templateData['JSON_EXT']);
}
die();
