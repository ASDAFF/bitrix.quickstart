<?php
$intID = intval($_REQUEST['ID']);
if (0 < $intID) {
	include_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/license_key.php';
	$strPath = $_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/asd.iblock/';
	$strName = 'asd_props_export_'.$intID.'_'.md5($LICENSE_KEY).'.xml';
	if (file_exists($strPath.$strName) && is_file($strPath.$strName)) {
		header('Content-type: text/xml');
		header('Content-Disposition: attachment; filename="'.$strName.'"');
		readfile($strPath.$strName);
		unlink($strPath.$strName);
	}
}