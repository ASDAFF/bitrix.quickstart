<?
IncludeModuleLangFile(__FILE__);
class CSiteOrange
{
	function ShowPanel()
	{
		
			$GLOBALS["APPLICATION"]->AddPanelButton(array(
				"HREF" => "/bitrix/admin/wizard_install.php?lang=".LANGUAGE_ID."&wizardName=erdesign:orange&wizardSiteID=".SITE_ID."&".bitrix_sessid_get(),
				"ID" => "orange_wizard",
				"ICON" => "bx-panel-site-wizard-icon",
				"MAIN_SORT" => 2500,
				"TYPE" => "BIG",
				"SORT" => 10,	
				"ALT" => GetMessage("SCOM_BUTTON_DESCRIPTION"),
				"TEXT" => GetMessage("SCOM_BUTTON_NAME"),
				"MENU" => array(),
			));
			

	}
}
?>