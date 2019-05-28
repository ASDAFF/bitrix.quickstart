<?
define("STOP_STATISTICS", true);
define("PUBLIC_AJAX_MODE", true);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if (isset($_REQUEST["reloadcaptha"]) && $_REQUEST["reloadcaptha"] == "Y")
{
	echo CMain::CaptchaGetCode();

	die();
}

global $USER;

$arResult = array("STATUS" => "N", "NOTIFY_URL" => "", "ERRORS" => "NOTIFY_ERR_REG");

if (CModule::IncludeModule('sale'))
{
	//AJAX
	if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['ajax'] == "Y" && check_bitrix_sessid() && !$USER->IsAuthorized())
	{
		$arResult["ERRORS"] = "";
		$arErrors = array();
		$user_mail = trim($_POST['user_mail']);
		$id = IntVal($_POST['id']);
		$user_login = trim($_POST["user_login"]);
		$user_password = trim($_POST["user_password"]);
		$url = trim($_POST["notifyurl"]);

		if (strlen($user_login) <= 0 && strlen($user_password) <= 0 && strlen($user_mail) <= 0)
			$arResult["ERRORS"] = 'NOTIFY_ERR_NULL';

		if (COption::GetOptionString("main", "captcha_registration", "N") == "Y" || (isset($_SESSION["NOTIFY_PRODUCT"]["CAPTHA"]) && $_SESSION["NOTIFY_PRODUCT"]["CAPTHA"] == "Y"))
		{
			if (!$APPLICATION->CaptchaCheckCode($_REQUEST["captcha_word"], $_REQUEST["captcha_sid"]))
			{
				$arResult["ERRORS"] = 'NOTIFY_ERR_CAPTHA';
			}
		}

		if (strlen($user_mail) > 0 && strlen($arResult["ERRORS"]) <= 0)
		{
			$res = CUser::GetList($b, $o, array("=EMAIL" => $user_mail));
			if($res->Fetch())
				$arResult["ERRORS"] = 'NOTIFY_ERR_MAIL_EXIST';
		}

		if (strlen($arResult["ERRORS"]) <= 0)
		{
			if (strlen($user_mail) > 0 && COption::GetOptionString("main", "new_user_registration", "N") == "Y")
			{
				$user_id = CSaleUser::DoAutoRegisterUser($user_mail, array(), SITE_ID, $arErrors);
				if ($user_id > 0)
				{
					$USER->Authorize($user_id);
					if (count($arErrors) > 0)
					{
						$arResult["ERRORS"] = $arErrors[0]["TEXT"];
					}
				}
				else
				{
					$arResult["ERRORS"] = 'NOTIFY_ERR_REG';
				}
			}
			else
			{
				$arAuthResult = $USER->Login($user_login, $user_password, "Y");
				$rs = $APPLICATION->arAuthResult = $arAuthResult;
				if (count($rs) > 0 && $rs["TYPE"] == "ERROR")
					$arResult["ERRORS"] = $rs["MESSAGE"];
			}

			if (strlen($arResult["ERRORS"]) <= 0)
			{
				$arResult["STATUS"] = "Y";
			}
		}
	}
}

echo CUtil::PhpToJSObject($arResult);

require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_after.php");
?>