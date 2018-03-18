<?
#################################################
#   Company developer: ALTASIB                  #
#   Developer: Serge                            #
#   Site: http://www.altasib.ru                 #
#   E-mail: dev@altasib.ru                      #
#   Copyright (c) 2006-2011 ALTASIB             #
#################################################
?>
<?
//global $DBType;
IncludeModuleLangFile(__FILE__);

$arClassesList = array(
        // main classes
        // API classes
);

Class UP_alx
{
    Function UpOnBeforeEndBufferContent()
    {
        global $APPLICATION;
        if (IsModuleInstalled("altasib.up"))
        {
            if(!defined(ADMIN_SECTION) && ADMIN_SECTION!==true)
            {
                                $altasib_up_en = COption::GetOptionString("altasib_up", "altasib_up_enable", "Y");
                                $altasib_up_en_site = COption::GetOptionString("altasib_up", "altasib_up_enable_site".'_'.SITE_ID, "Y");
                                if($altasib_up_en == 'Y' || $altasib_up_en_site == 'Y')
                                {
                                        if($altasib_up_en == 'Y')
                                                $altasib_up_link = trim(COption::GetOptionString("altasib_up", "altasib_up_link", ""));
                                        else
                                                $altasib_up_link = trim(COption::GetOptionString("altasib_up", "altasib_up_link"."_".SITE_ID, ""));

                                        $show = 'Y';

                                        //if current page is in the fiter list

                                        if(!empty($altasib_up_link))
                                        {
                                                if (COption::GetOptionString("altasib_up", "altasib_up_inverse_link".'_'.SITE_ID, "N") == "Y"):
	                                                $exception_links = explode("\n", $altasib_up_link);

	                                                foreach ($exception_links as $key => $value)
	                                                {
	                                                    if(stripos(trim($APPLICATION->GetCurPage(true)), trim(up_RelativeURL($exception_links[$key])))===0)
	                                                        {
	                                                            $show = 'N';
	                                                                break;
	                                                        }
	                                                }
                                                else:
	                                                $show = 'N';
	                                                $exception_links = explode("\n", $altasib_up_link);
	                                                foreach ($exception_links as $key => $value)
	                                                {
	                                                    if(stripos(trim($APPLICATION->GetCurPage(true)), trim(up_RelativeURL($exception_links[$key])))===0)
	                                                        {
	                                                            $show = 'Y';
	                                                                break;
	                                                        }
	                                                }
						endif;
                                        }
                                        if($show == 'Y')
                                        {
                                                UP_alx::addScriptOnSite();
                                                return true;
                                        }
                                }
            }
        }
    }
        function addScriptOnSite()
        {
                global $APPLICATION;
                $altasib_up_en = COption::GetOptionString("altasib_up", "altasib_up_enable", "Y");
                $altasib_up_en_site = COption::GetOptionString("altasib_up", "altasib_up_enable_site".'_'.SITE_ID, "Y");
                if($altasib_up_en == 'Y')
                {
                        $altasib_up_button = COption::GetOptionString("altasib_up", "altasib_up_button","/bitrix/images/altasib.up/button/1.png");
                        $altasib_up_pos = COption::GetOptionString("altasib_up", "altasib_up_pos", "3");
                        $altasib_up_pos_xy = COption::GetOptionString("altasib_up", "altasib_up_pos_xy", "10");
                        $jq = COption::GetOptionString("altasib_up", "enable_jquery", "Y");
                }
                else
                {
                        $altasib_up_button = COption::GetOptionString("altasib_up", "altasib_up_button".'_'.SITE_ID, "/bitrix/images/altasib.up/button/1.png");
                        $altasib_up_pos = COption::GetOptionString("altasib_up", "altasib_up_pos".'_'.SITE_ID, "3");
                        $altasib_up_pos_xy = COption::GetOptionString("altasib_up", "altasib_up_pos_xy".'_'.SITE_ID, "10");
                        $jq = COption::GetOptionString("altasib_up", "enable_jquery".'_'.SITE_ID, "Y");
                }
                $APPLICATION->AddHeadString("<script>altasib_up_button='".$altasib_up_button."'; altasib_up_pos ='".$altasib_up_pos."';altasib_up_pos_xy = '".$altasib_up_pos_xy."'</script>",true);

                if($jq == 1)
                {
                //if jquery=true already
                        $APPLICATION->AddHeadScript("/bitrix/js/altasib.up/script_jq.js");
                }
                elseif($jq == 2)
                {
                        $APPLICATION->AddHeadScript("/bitrix/js/altasib.up/script.js");
                }
                else
                {
                        CUtil::InitJSCore(Array("jquery"));
                        $APPLICATION->AddHeadScript("/bitrix/js/altasib.up/script_jq.js");

                }
                $APPLICATION->AddHeadString("<link href='/bitrix/js/altasib.up/style.css' type='text/css' rel='stylesheet' />",true);
        }
}
        //function converts url to relative link
if(!function_exists("up_RelativeURL"))
{
    function up_RelativeURL($url_change)
    {
        if(substr($url_change,0,7)=='http://')
            $url_change = substr($url_change,7);
        if(substr($url_change,0,4)=='www.')
            $url_change = substr($url_change,4);
        if(substr($url_change,0,strlen($_SERVER[HTTP_HOST])) == $_SERVER[HTTP_HOST])
            $url_change = substr($url_change,strlen($_SERVER[HTTP_HOST]));
        return $url_change;
    }
}
?>
