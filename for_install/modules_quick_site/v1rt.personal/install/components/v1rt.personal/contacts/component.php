<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if(!function_exists("v1rtPersonalEmail"))
{
    function v1rtPersonalEmail($email1, $email2) 
    {
        $mai  = '<script type="text/javascript">';
        $mai .=  "var login = '".$email1."'; ";
        $mai .=  "var server = '".$email2."';";
        $mai .=  "var email = login+'@'+server ;";
        $mai .=  "var url = 'mailto:'+email; ";
        $mai .=  "document.write('<a href='+url+'>'+email+'</a>');";
        $mai .=  '</script>';
        return $mai;
    }
}

if(CModule::IncludeModuleEx("v1rt.personal"))
{
    $arResult["TWITTER"]    = COption::GetOptionString("v1rt.personal", "v1rt_personal_twitter");
    $arResult["EMAIL"]      = COption::GetOptionString("v1rt.personal", "v1rt_personal_email");
    $arResult["PHONE"]      = COption::GetOptionString("v1rt.personal", "v1rt_personal_phone");
    $arResult["VK"]         = COption::GetOptionString("v1rt.personal", "v1rt_personal_vk");
    $arResult["FACEBOOK"]   = COption::GetOptionString("v1rt.personal", "v1rt_personal_fb");
    $this->IncludeComponentTemplate();
}
?>