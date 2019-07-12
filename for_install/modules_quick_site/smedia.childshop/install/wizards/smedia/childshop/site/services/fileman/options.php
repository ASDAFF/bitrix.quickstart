<?
 if (WIZARD_IS_RERUN)
	return;
COption::SetOptionString("fileman", "menutypes", GetMessage("fileman_menutypes"), WIZARD_SITE_ID);
COption::SetOptionString("fileman", "num_menu_param", '2', WIZARD_SITE_ID);
COption::SetOptionString("fileman", "propstypes", GetMessage("fileman_propstypes"), WIZARD_SITE_ID);
?>