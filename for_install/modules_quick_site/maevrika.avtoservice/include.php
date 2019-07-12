<?
IncludeModuleLangFile(__FILE__);
class CSiteAvtoservice
{
	function ShowPanel(){
		
		if ($GLOBALS["USER"]->IsAdmin() && COption::GetOptionString("main", "wizard_solution", "", SITE_ID) == "avtoservice"){
			$GLOBALS["APPLICATION"]->AddPanelButton(array(
				"HREF" => "/bitrix/admin/wizard_install.php?lang=ru&wizardName=evrica:avtoservice&wizardSiteID=".SITE_ID."&".bitrix_sessid_get(),
				"ID" => "avtoservice",
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
}
?>