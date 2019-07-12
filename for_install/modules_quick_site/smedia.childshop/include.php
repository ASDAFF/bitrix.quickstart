<?
IncludeModuleLangFile(__FILE__);
class SMChildShopEvents
{
	function ShowPanel()
	{
		if ($GLOBALS["USER"]->IsAdmin() && COption::GetOptionString("main", "wizard_solution", "", SITE_ID) == "smedia.childshop")
		{
//			$GLOBALS["APPLICATION"]->SetAdditionalCSS("/bitrix/wizards/bitrix/smedia.childshop/css/panel.css"); 
			$GLOBALS["APPLICATION"]->AddPanelButton(array(
				"HREF" => "/bitrix/admin/wizard_install.php?lang=ru&wizardName=smedia:childshop&wizardSiteID=".SITE_ID."&".bitrix_sessid_get(),
				"ID" => "smedia_childshop_wizard",
				"ICON" => "bx-panel-site-wizard-icon",
				"ALT" => GetMessage("SMCS_BUTTON_ALT"),
				"TEXT" => GetMessage("SMCS_BUTTON_TEXT"),
				"TYPE" => "BIG",
				"MAIN_SORT" => 1335,
				"SORT" => 30,
				"MENU" => array(),
			));
		}
	}
}
?>