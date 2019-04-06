<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Open Boom App XML File");?>
<?$APPLICATION->IncludeComponent(
	'ithive:oxml', 
	'.default',
	array(),
	false
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
