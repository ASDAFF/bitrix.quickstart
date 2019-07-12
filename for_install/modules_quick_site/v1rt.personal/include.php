<?
IncludeModuleLangFile(__FILE__);

class CSiteV1rtPersonal
{
	function ShowPanel()
    {		
		if ($GLOBALS["USER"]->IsAdmin() && COption::GetOptionString("main", "wizard_solution", "", SITE_ID) == "personal")
        {
			$GLOBALS["APPLICATION"]->AddPanelButton(array(
				"HREF"      => "/bitrix/admin/wizard_install.php?lang=ru&wizardName=v1rt:personal&wizardSiteID=".SITE_ID."&".bitrix_sessid_get(),
				"ID"        => "personal",
				"ICON"      => "bx-panel-site-wizard-icon",
				"MAIN_SORT" => 2500,
				"TYPE"      => "BIG",
				"SORT"      => 10,					
				"ALT"       => GetMessage("V1RT_MODULE_BUTTON_DESCRIPTION"),
				"TEXT"      => GetMessage("V1RT_MODULE_BUTTON_NAME"),
				"MENU"      => array(),
			));
		}
	}
}

CModule::AddAutoloadClasses(
	"v1rt.personal",
	array(
        'CMediaComponents' => 'classes/mysql/CMediaComponents.php',
        'CWebMaster' => 'classes/general/CWebMaster.php',
	)
);
?>