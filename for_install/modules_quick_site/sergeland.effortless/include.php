<?
IncludeModuleLangFile(__FILE__);
class CSergelandeffortless
{
	function ShowPanel()
	{
		if ($GLOBALS["USER"]->IsAdmin() && COption::GetOptionString("main", "wizard_solution", "", SITE_ID) == "effortless")
		{
			$GLOBALS["APPLICATION"]->SetAdditionalCSS("/bitrix/wizards/sergeland/effortless/css/panel.css"); 

			$arMenu = Array();

			$GLOBALS["APPLICATION"]->AddPanelButton(array(
				"HREF" => "/bitrix/admin/wizard_install.php?lang=".LANGUAGE_ID."&wizardName=sergeland:effortless&run_wizard_design=Y&wizardSiteID=".SITE_ID."&".bitrix_sessid_get(),
				"ID" => "effortless_wizard_sergeland",
				"ICON" => "bx-panel-site-wizard-icon",
				"MAIN_SORT" => 2500,
				"TYPE" => "BIG",
				"SORT" => 10,	
				"ALT" => GetMessage("SCOM_BUTTON_DESCRIPTION"), // Запустить мастер смены дизайна
				"TEXT" => GetMessage("SCOM_BUTTON_NAME"), 		// Мастер смены дизайна
				"MENU" => $arMenu,
			));
		}
	}
}
?>