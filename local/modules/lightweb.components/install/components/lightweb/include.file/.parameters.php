<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arFiles=scandir();

foreach(glob($_SERVER['DOCUMENT_ROOT'].'/include/'.'*.php') as $file) {$arFiles['/include/'.basename($file)] = basename($file);}

$arComponentParameters = array(
	"PARAMETERS" => array(
		"FILE_CONNECTION" => Array(
			"NAME" => GetMessage('SWIF_FILE_CONNECTION'),
			"TYPE" => "LIST",
			"PARENT" => "BASE",
			"VALUES" => $arFiles,
		),
		"CONTENT_TYPE" => Array(
			"NAME" => GetMessage('SWIF_CONTENT_TYPE'),
			"TYPE" => "LIST",
			"PARENT" => "BASE",
			"VALUES" => array('text'=>'TEXT','html'=>'HTML')
		),
	)
);


?>