<?
IncludeModuleLangFile(__FILE__);
class COkshop
{
	function ShowPanel()
	{
		if ($GLOBALS["USER"]->IsAdmin() && 
			COption::GetOptionString("main", "wizard_solution", "", SITE_ID) == "okshop")
		{
			$GLOBALS["APPLICATION"]->SetAdditionalCSS("/bitrix/wizards/krayt/okshop/css/panel.css"); 

			$arMenu = Array(
				Array(		
					"ACTION" => "jsUtils.Redirect([], '".CUtil::JSEscape("/bitrix/admin/wizard_install.php?lang=".LANGUAGE_ID."&wizardSiteID=".SITE_ID."&wizardName=bitrix:okshop&".bitrix_sessid_get())."')",
					"ICON" => "bx-popup-item-wizard-icon",
					"TITLE" => GetMessage("STOM_BUTTON_TITLE_W1"),
					"TEXT" => GetMessage("STOM_BUTTON_NAME_W1"),
				)
			);

			$GLOBALS["APPLICATION"]->AddPanelButton(array(
				"HREF" => "/bitrix/admin/wizard_install.php?lang=".LANGUAGE_ID."&wizardName=bitrix:okshop&wizardSiteID=".SITE_ID."&".bitrix_sessid_get(),
				"ID" => "emarket_wizard",
				"ICON" => "bx-panel-site-wizard-icon",
				"MAIN_SORT" => 2500,
				"TYPE" => "BIG",
				"SORT" => 10,	
				"ALT" => GetMessage("SCOM_BUTTON_DESCRIPTION"),
				"TEXT" => GetMessage("SCOM_BUTTON_NAME"),
				"MENU" => $arMenu,
			));
		}
	}

    function OnBuildGlobalMenu(&$aGlobalMenu, &$aModuleMenu)
    {

        global $USER;
        if(!$USER->IsAdmin())
            return;


        $aGlobalMenu['global_menu_krayt'] = array(
            'menu_id' => "krayt",
            'text' => GetMessage('K_MENU_GLOBAL_TITLE'),
            'title' => GetMessage('K_MENU_GLOBAL_TEXT'),
            'sort'  => 1000,
            'items_id' => 'global_menu_krayt',
            'help_section' => 'krayt',
            'items' => array()

        );

    }

    function OnEndBufferContent(&$content)
    {
        if(SITE_TEMPLATE_ID == 'okshop')
        {
            $copyright = '<a href="http://marketplace.1c-bitrix.ru/partners/detail.php?ID=712923.php">'.GetMessage('K_COPY_TEXT').'</a>';

            if (preg_match('|<div class="copyright">|sei', $content))
            {
                $content = preg_replace("!<div class=\"copyright\">(.*?)</div>!si","<div class=\"copyright\">$copyright</div>",$content);
            }else{
                $copyright = '<div class="copyright" style="
                                                            position: absolute;
                                                            display: block !important;
                                                        "><a href="http://marketplace.1c-bitrix.ru/partners/detail.php?ID=712923.php" style="
                                                            position: relative !important;
                                                            top: -69px !important;
                                                            left: 60px !important;
                                                            color: #7aa4cf;
                                                            font-size: 14px;
                                                            display: block !important;
                                                        ">'.GetMessage('K_COPY_TEXT').'</a></div>';
                $content = preg_replace("!</body>!si","<div class='copyright'>$copyright</div></body>",$content);
            }
        }

    }
}

use Bitrix\Main\Loader;
use Bitrix\Main\EventManager;

$arClasses = array(
    "CkraytUtilit" => "classes/general/ckrayt_util.php",

);
CModule::AddAutoloadClasses("krayt.okshop", $arClasses);
?>