<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$APPLICATION->RestartBuffer();

$formID = $_REQUEST['FORM_ID'];
$requestType = $_REQUEST['REQUEST_TYPE'];

switch ($requestType)
{
	case 'SEND':
		if (!empty($formID))
		{
			$APPLICATION->IncludeComponent('nsandrey:mailform', 'ajax', $_SESSION['UNIF'][$formID], false, array('HIDE_ICONS' => 'Y'));
		}
		break;

	case 'NEW_CAPTCHA':
		echo json_encode(array('TYPE' => 'NEW_CAPTCHA', 'NEW_CAPTCHA' => htmlspecialchars($APPLICATION->CaptchaGetCode())));
		break;

	default:
		break;
}