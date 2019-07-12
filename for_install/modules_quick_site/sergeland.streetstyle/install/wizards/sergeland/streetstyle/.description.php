<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!defined("WIZARD_DEFAULT_SITE_ID") && !empty($_REQUEST["wizardSiteID"])) 
	define("WIZARD_DEFAULT_SITE_ID", $_REQUEST["wizardSiteID"]); 

$arWizardDescription = Array(
	"NAME" 			=> GetMessage("PORTAL_WIZARD_NAME"),  // ��������-�������
	"DESCRIPTION" 	=> GetMessage("PORTAL_WIZARD_DESC"),  // ������ �������� ��������-��������
	"VERSION" 		=> "1.0.1",
	"START_TYPE" 	=> "WINDOW",
	"WIZARD_TYPE" 	=> "INSTALL",
	"IMAGE" 		=> "/images/".LANGUAGE_ID."/solution.png",
	"PARENT" 		=> "wizard_sol",
	"TEMPLATES" 	=> Array(Array('SCRIPT' => 'scripts/template.php', 'CLASS' => 'WizardTemplate')), // ���� ������ �� ����� scripts
	"STEPS" 		=> array(),
);

if(defined("WIZARD_DEFAULT_SITE_ID"))
{
	if(LANGUAGE_ID == "ru")
		 $arWizardDescription["STEPS"] = Array(
		 
				 "SiteSettingsStep", 	// ���������� � �����
				 "CatalogSettings", 	// ��������� ��������
				 "ShopSettings", 		// ���������� � ��������
				 "PersonType", 			// ���� ������������
				 "PaySystem", 			// ������ � ��������
				 "DataInstallStep", 	// ��������� �������
				 "FinishStep"			// ���������� ���������
			);
			
	else $arWizardDescription["STEPS"] = Array(
	 
				 "SiteSettingsStep", 
				 "CatalogSettings", 
				 "ShopSettings", 
				 "PersonType", 
				 "PaySystem", 
				 "DataInstallStep", 
				 "FinishStep"
			);
}
else
{
	if(LANGUAGE_ID == "ru")
		 $arWizardDescription["STEPS"] = Array(
		 
						 "SelectSiteStep",     // ����� ����� 
						 "SiteSettingsStep",   // ���������� � �����
						 "CatalogSettings",    // ��������� ��������
						 "ShopSettings",       // ���������� � ��������
						 "PersonType",         // ���� ������������
						 "PaySystem",          // ������ � ��������
						 "DataInstallStep" ,   // ��������� �������
						 "FinishStep"          // ���������� ���������
					);
					
	else $arWizardDescription["STEPS"] = Array(
	
						 "SelectSiteStep",     
						 "SiteSettingsStep",   
						 "CatalogSettings",    
						 "ShopSettings",       
						 "PersonType",         
						 "PaySystem",          
						 "DataInstallStep" ,   
						 "FinishStep"          
					);
}
?>