<?
/**
 * Copyright (c) 6/12/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

IncludeModuleLangFile(__FILE__);
class CQuickbusinesscard
{
	function ShowPanel()
	{
		if ($GLOBALS["USER"]->IsAdmin() && COption::GetOptionString("main", "wizard_solution", "", SITE_ID) == "businesscard")
		{
			$GLOBALS["APPLICATION"]->SetAdditionalCSS("/bitrix/wizards/quick/businesscard/css/panel.css");

			$arMenu = Array();

			$GLOBALS["APPLICATION"]->AddPanelButton(array(
				"HREF" => "/bitrix/admin/wizard_install.php?lang=".LANGUAGE_ID."&wizardName=quick:businesscard&run_wizard_design=Y&wizardSiteID=".SITE_ID."&".bitrix_sessid_get(),
				"ID" => "businesscard_wizard_quick",
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