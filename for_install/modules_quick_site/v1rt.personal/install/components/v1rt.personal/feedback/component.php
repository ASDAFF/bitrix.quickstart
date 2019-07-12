<?
$nl = "\n\r";
if(isset($_GET["ajax"]))
{
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
    @include(dirname(__FILE__)."/lang/ru/component.php");
    
    $arParams["TEMPLATE_MAIL"] = "FEEDBACK_FORM";
    
    if(isset($_GET["captcha"]))
    {
        include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/captcha.php");
        $captcha_code = $_POST["captcha_sid"];
        $captcha_word = $_POST["captcha_word"];
        $cpt = new CCaptcha();
        $captchaPass = COption::GetOptionString("main", "captcha_password", "");
        if(strlen($captcha_word) > 0 && strlen($captcha_code) > 0)
        {
            if(!$cpt->CheckCodeCrypt($captcha_word, $captcha_code, $captchaPass))
                $arResult["ERROR"][] = $MESS["CAPTCHA_ERROR"];
        }
        else
        {
            $arResult["ERROR"][] = $MESS["CAPTCHA_ERROR_NULL"];
        }
    }
    
    if($_POST["name"] != "" && $_POST["email"] != "" && $_POST["message"] != "")
    {
        if(CModule::IncludeModuleEx("v1rt.personal"))
        {
            $arSite = CSite::GetByID(SITE_ID)->Fetch();
            $email = COption::GetOptionString("v1rt.personal", "v1rt_personal_email");
            if($email != "")
                $strEmail = $email;
            elseif($arSite["EMAIL"] != "")
                $strEmail = $arSite["EMAIL"];
            else
                $arResult["ERROR"][] = $MESS["ADMIN_EMAIL_ERROR_NULL"];
        }
        
        if($arSite["CHARSET"] == "windows-1251")
        {
            $arFields = Array(
                "AUTHOR" => utf8win1251($_POST["name"]),
                "AUTHOR_EMAIL" => utf8win1251($_POST["email"]),
                "TEXT" => utf8win1251($_POST["message"]).$nl.$MESS["AUTHOR_PHONE"]." ".($_POST["phone"] != "" ? $_POST["phone"] : "-"),
    		);
        }
        else
        {
            $arFields = Array(
                "AUTHOR" => $_POST["name"],
                "AUTHOR_EMAIL" => $_POST["email"],
                "TEXT" => $_POST["message"].$nl.$MESS["AUTHOR_PHONE"]." ".($_POST["phone"] != "" ? $_POST["phone"] : "-"),
    		);
        }
    }
    
    if(count($arResult["ERROR"]) == 0)
    {
        CEvent::Send($arParams["TEMPLATE_MAIL"], SITE_ID, $arFields);
        echo 1;
        return;
    }
    else
    {
        echo -1;
        return;
    }
}

if($arParams["CAPTCHA"] == "Y")
{
    $arResult["capCode"] =  htmlspecialcharsbx($APPLICATION->CaptchaGetCode());
    $arResult["AJAX_PATH"] = $componentPath."/component.php?ajax=1&captcha=1";
}
else
{
    $arResult["AJAX_PATH"] = $componentPath."/component.php?ajax=1";
}

$this->IncludeComponentTemplate();
?>