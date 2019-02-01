<?
define("NO_KEEP_STATISTIC", true); 
define("NOT_CHECK_PERMISSIONS", true);
define("PUBLIC_AJAX_MODE", true); 
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
$APPLICATION->RestartBuffer();

if (!CModule::IncludeModule('webdebug.reviews')) {
	print 'Save error. Module not installed';
}

$InterfaceID = IntVal($_GET['interface']);
$Target = $_GET['target'];

$WD_Reviews2_Action = $_GET['action'];
switch($WD_Reviews2_Action) {
	case 'save':
		CWD_Reviews2_Tools::SaveReview($InterfaceID, $Target);
		break;
	case 'captcha':
		CWD_Reviews2_Tools::ShowCaptcha($InterfaceID);
		break;
	case 'vote':
		header('Content-Type: application/json');
		$ReviewID = IntVal($_GET['review']);
		$Amount = IntVal($_GET['amount']);
		$arResult = CWD_Reviews2_Tools::VoteReview($InterfaceID, $Target, $ReviewID, $Amount);
		print CWD_Reviews2::JsonEncode($arResult);
		break;
	case 'delete':
		$ReviewID = IntVal($_GET['review']);
		CWD_Reviews2_Tools::DeleteReview($InterfaceID, $Target, $ReviewID);
		LocalRedirect(isset($_GET['wd_back_url'])?$_GET['wd_back_url']:'/');
		break;
	case 'list':
		CWD_Reviews2_Tools::ShowReviewsList();
		break;
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
?>