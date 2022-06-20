<?
/**
 * Copyright (c) 25/7/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
if ($_GET["mode"] == 'captcha_sid')
  echo $APPLICATION->CaptchaGetCode();
elseif ($_GET["mode"] == 'captcha_code')
{
    if (!$GLOBALS["USER"]->IsAuthorized())
    {
        include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/captcha.php");
        $cpt = new CCaptcha();
        $captchaPass = COption::GetOptionString("main", "captcha_password", "");
        if (strLen($captchaPass) <= 0)
        {
            $captchaPass = randString(10);
            COption::SetOptionString("main", "captcha_password", $captchaPass);
        }
        $cpt->SetCodeCrypt($captchaPass);
        echo htmlspecialchars($cpt->GetCodeCrypt());
    }
}
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
?>