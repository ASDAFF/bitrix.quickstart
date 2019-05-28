<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\IO\Path;
use Bitrix\Main\Application;

require_once(Path::combine(__DIR__, "functions.php"));
Loc::loadLanguageFile(Path::combine(__DIR__, "statuses.php"));

$success =
	isset($_POST['bill_id']) &&
	isset($_POST['amount']) &&
	isset($_POST['ccy']) &&
	isset($_POST['status']) &&
	isset($_POST['error']) &&
	isset($_POST['user']) &&
	isset($_POST['comment']) &&
	isset($_POST['prv_name']) &&
	isset($_POST['command']);


if(!$success)
	qiwiWalletXmlResponse(QIWI_WALLET_ERROR_CODE_NONE);

$authType = CSalePaySystemAction::GetParamValue("AUTHORIZATION");;
if($authType == "OPEN")
{
	$login 		= CSalePaySystemAction::GetParamValue("SHOP_ID");
	$password	= CSalePaySystemAction::GetParamValue("NOTICE_PASSWORD");


	if(!qiwiWalletCheckAuth($login, $password))
		qiwiWalletXmlResponse(QIWI_WALLET_ERROR_CODE_AUTH);
}
else
{
	if(isset($_SERVER['HTTP_X_API_SIGNATURE']))
	{
		$key	= CSalePaySystemAction::GetParamValue("API_PASSWORD");
		$params = $_POST;
		ksort($params);
		$check = base64_encode(sha1($key, implode("|", array_values($params))));
		if($check != $_SERVER['HTTP_X_API_SIGNATURE'])
			qiwiWalletXmlResponse(QIWI_WALLET_ERROR_CODE_AUTH);
	}
	else
		qiwiWalletXmlResponse(QIWI_WALLET_ERROR_CODE_AUTH);
}

if($order = CSaleOrder::getById($_POST['bill_id']))
{
	$paidInfo = array(
		"PS_STATUS" 		=> $_POST['status'] == "paid" ? "Y" : "N",
		"PS_STATUS_CODE"	=> substr($_POST['status'], 0, 5),
		"PS_STATUS_MESSAGE" => Loc::getMessage("SALE_QWH_STATUS_MESSAGE_" . strtoupper($_POST['status'])),
		"PS_RESPONSE_DATE"	=> \Bitrix\Main\Type\DateTime::createFromTimestamp(time()),
		"PS_SUM"			=> (double)$_POST['amount'],
		"PS_CURRENCY"		=> $_POST['ccy'],
		"PS_STATUS_DESCRIPTION" => ""
	);

	if((int)$_POST['error'])
	{
		$paidInfo['PS_STATUS_DESCRIPTION'] = "Error: " . Loc::getMessage("SALE_QWH_ERROR_CODE_" . $_POST['error']);
		CSaleOrder::Update($order['ID'], $paidInfo);
		qiwiWalletXmlResponse(QIWI_WALLET_ERROR_CODE_OTHER);
	}

	foreach($_POST as $key => $value)
		$paidInfo['PS_STATUS_DESCRIPTION'] .= "{$key}:{$value}, ";
	CSaleOrder::Update($order['ID'], $paidInfo);

	$changeStatusPay = CSalePaySystemAction::GetParamValue("CHANGE_STATUS_PAY") == "Y";
	$difference = (double)$_POST['amount'] - (double)$order['PRICE'];

	if($difference < 0)
		qiwiWalletXmlResponse(QIWI_WALLET_ERROR_CODE_TOO_LOW);
	elseif($difference > 0)
		qiwiWalletXmlResponse(QIWI_WALLET_ERROR_CODE_TOO_HIGH);
	else
	{
		if($_POST['status'] == "paid" && $changeStatusPay)
			CSaleOrder::PayOrder($order['ID'], "Y", true, true);

		qiwiWalletXmlResponse(QIWI_WALLET_ERROR_CODE_NONE);
	}
}
else
{
	qiwiWalletXmlResponse(QIWI_WALLET_ERROR_CODE_NOT_FOUND);
}
?>