<?
 if (WIZARD_IS_RERUN)
	return;
COption::SetOptionString("store", "shopAdr", GetMessage("store_shopAdr"), WIZARD_SITE_ID);
COption::SetOptionString("store", "shopBANK", GetMessage("store_shopBANK"), WIZARD_SITE_ID);
COption::SetOptionString("store", "shopBANKREKV", GetMessage("store_shopBANKREKV"), WIZARD_SITE_ID);
COption::SetOptionString("store", "shopEmail", 'sale@igrushka.3.dev.smedia.ru', WIZARD_SITE_ID);
COption::SetOptionString("store", "shopINN", '1234567890', WIZARD_SITE_ID);
COption::SetOptionString("store", "shopKPP", '123456789', WIZARD_SITE_ID);
COption::SetOptionString("store", "shopKS", '30101 810 4 0000 0000225', WIZARD_SITE_ID);
COption::SetOptionString("store", "shopLocation", GetMessage("store_shopLocation"), WIZARD_SITE_ID);
COption::SetOptionString("store", "shopNS", '0000 0000 0000 0000 0000', WIZARD_SITE_ID);
COption::SetOptionString("store", "shopOfName", GetMessage("store_shopOfName"), WIZARD_SITE_ID);
COption::SetOptionString("store", "siteName", GetMessage("store_siteName"), WIZARD_SITE_ID);
COption::SetOptionString("store", "siteStamp", '', WIZARD_SITE_ID);
COption::SetOptionString("store", "siteTelephone", '8 (495) 212 85 06', WIZARD_SITE_ID);
COption::SetOptionString("store", "wizard_installed", 'Y', WIZARD_SITE_ID);
?>