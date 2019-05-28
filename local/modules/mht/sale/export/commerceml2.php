<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

ob_start();

CSaleExport::ExportOrders2Xml($arFilter);

$contents = ob_get_contents();
ob_end_clean();

$str = (function_exists("mb_strlen")? mb_strlen($contents, 'latin1'): strlen($contents));
if(toUpper(LANG_CHARSET) != "WINDOWS-1251")
	$contents = $APPLICATION->ConvertCharset($contents, LANG_CHARSET, "windows-1251");


header('Pragma: public');
header('Cache-control: private');
header('Accept-Ranges: bytes');
header("Content-Type: application/xml; charset=windows-1251");
header("Content-Length: ".$str);
header("Content-Disposition: attachment; filename=orders.xml");

echo $contents;
?>