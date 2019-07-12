<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

if (!defined('WIZARD_THEME_ID') || WIZARD_THEME_ID == "default")
	return;

$moduleId = 'redsign.flyaway';
$content = WIZARD_THEME_ID;

$gzFileName = $content.'.tar.gz';
$folderPath = '/upload/rs_download_data/'.$moduleId.'/';
$gzFilePath = $folderPath.$gzFileName;
$copyDirPath = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$moduleId.'/';

if (!file_exists($_SERVER['DOCUMENT_ROOT'].$gzFilePath)) {
	return true;
}

// unpack
$arUnpackOptions = array(
	'REMOVE_PATH' => $_SERVER['DOCUMENT_ROOT'],
	'UNPACK_REPLACE' => true,
);
$resArchiver = CBXArchive::GetArchive($_SERVER['DOCUMENT_ROOT'].$gzFilePath);
$resArchiver->SetOptions($arUnpackOptions);
$uRes = $resArchiver->Unpack($copyDirPath);

// copy
CopyDirFiles($copyDirPath.'install/wizards', $_SERVER["DOCUMENT_ROOT"]."/bitrix/wizards", true, true);
