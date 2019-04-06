<?
IncludeModuleLangFile(__FILE__);
class COffices
{
	function ShowPanel()
	{
		if ($GLOBALS["USER"]->IsAdmin() && COption::GetOptionString("main", "wizard_solution", "", SITE_ID) == "offices")
		{
			$GLOBALS["APPLICATION"]->AddPanelButton(array(
				"HREF" => "/bitrix/admin/wizard_install.php?lang=".LANGUAGE_ID."&wizardName=ithive:offices&wizardSiteID=".SITE_ID."&".bitrix_sessid_get(),
				"ID" => "offices",
				"ICON" => "bx-panel-site-wizard-icon",
				"MAIN_SORT" => 2500,
				"TYPE" => "BIG",
				"SORT" => 10,	
				"ALT" => GetMessage("ITHIVE_BUTTON_DESCRIPTION"),
				"TEXT" => GetMessage("ITHIVE_BUTTON_NAME"),
				"MENU" => array(),
			));
		}
		

	}
}
?>