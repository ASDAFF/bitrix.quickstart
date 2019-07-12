<?php
if(empty($_POST['parameters'])) {
	echo 'no parameters found';
	return;
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$signer = new \Bitrix\Main\Security\Sign\Signer;

$parameters = $signer->unsign($_POST['parameters'], 'bx.bd.products.recommendation');
$template = $signer->unsign($_POST['template'], 'bx.bd.products.recommendation');

$trueParameters = unserialize(base64_decode($parameters));
$trueParameters['VIEW'] = ( isset($_REQUEST['view']) ? $_REQUEST['view'] : '' );

$_SESSION['ALFASORTER']['ALFASORTER_CTEMPLATE'] = $trueParameters['VIEW'];

$APPLICATION->IncludeComponent(
	"bitrix:catalog.bigdata.products",
	$template,
	$trueParameters,
	false
);