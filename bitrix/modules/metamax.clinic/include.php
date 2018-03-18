<?
IncludeModuleLangFile(__FILE__);
class CSiteClinic
	function ShowPanel(){
		
		if ($GLOBALS["USER"]->IsAdmin() && COption::GetOptionString("main", "wizard_solution", "", SITE_ID) == "clinic"){
			$GLOBALS["APPLICATION"]->AddPanelButton(array(
				"HREF" => "/bitrix/admin/wizard_install.php?lang=ru&wizardName=metamax:clinic&wizardSiteID=".SITE_ID."&".bitrix_sessid_get(),
				"ID" => "clinic",
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