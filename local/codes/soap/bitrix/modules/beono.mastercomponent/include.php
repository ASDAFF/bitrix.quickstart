<?
IncludeModuleLangFile(__FILE__);
class BeonoMasterComponent
{
	/**
	 * Displays start master button in public section
	 */
	function ShowCreateButton ()
	{
		// if user is admin
		if ($GLOBALS["USER"]->IsAdmin())
		{
			$GLOBALS["APPLICATION"]->AddHeadString('<style type="text/css">
#wizard_install_dialog { background-color: #F8F9FC; border: 1px solid #ABB7D8; }
#wizard_install_dialog div.title {background-color:#23468A; background-image:url(/bitrix/themes/.default/images/calendar/title_bg.gif); background-repeat:repeat-x; background-position:left top;}
#wizard_install_dialog div.title td.title-text {font-size:11px; font-family: Verdana,Arial,helvetica,sans-serif; font-weight:bold; color:#EEF1F7; padding:3px; cursor:move; vertical-align:top;}
</style>');

			$GLOBALS["APPLICATION"]->AddPanelButton(array(
				"HREF" => "javascript:WizardWindow.Open('beono:component','".bitrix_sessid()."')",   
            	"ID" => "beono.mastercomponent",
				"SRC" => "/bitrix/wizards/beono/component/panel-icon.png", 
				"MAIN_SORT" => 400,
				"SORT" => 100,
				"ALT" => GetMessage("BEONO_MODULE_MASTERCOMP_BUTTON_DESCRIPTION"),
				"TEXT" => GetMessage("BEONO_MODULE_MASTERCOMP_BUTTON_NAME"),
				"MENU" => array(),
			));
		}
	}
}
?>