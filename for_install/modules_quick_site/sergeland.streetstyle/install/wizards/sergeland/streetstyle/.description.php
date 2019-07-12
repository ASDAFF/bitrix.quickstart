<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!defined("WIZARD_DEFAULT_SITE_ID") && !empty($_REQUEST["wizardSiteID"])) 
	define("WIZARD_DEFAULT_SITE_ID", $_REQUEST["wizardSiteID"]); 

$arWizardDescription = Array(
	"NAME" 			=> GetMessage("PORTAL_WIZARD_NAME"),  // Интернет-магазин
	"DESCRIPTION" 	=> GetMessage("PORTAL_WIZARD_DESC"),  // Мастер создания интернет-магазина
	"VERSION" 		=> "1.0.1",
	"START_TYPE" 	=> "WINDOW",
	"WIZARD_TYPE" 	=> "INSTALL",
	"IMAGE" 		=> "/images/".LANGUAGE_ID."/solution.png",
	"PARENT" 		=> "wizard_sol",
	"TEMPLATES" 	=> Array(Array('SCRIPT' => 'scripts/template.php', 'CLASS' => 'WizardTemplate')), // свой мастер из папки scripts
	"STEPS" 		=> array(),
);

if(defined("WIZARD_DEFAULT_SITE_ID"))
{
	if(LANGUAGE_ID == "ru")
		 $arWizardDescription["STEPS"] = Array(
		 
				 "SiteSettingsStep", 	// Информация о сайте
				 "CatalogSettings", 	// Настройка каталога
				 "ShopSettings", 		// Информация о магазине
				 "PersonType", 			// Типы плательщиков
				 "PaySystem", 			// Оплата и доставка
				 "DataInstallStep", 	// Установка решения
				 "FinishStep"			// Завершение настройки
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
		 
						 "SelectSiteStep",     // Выбор сайта 
						 "SiteSettingsStep",   // Информация о сайте
						 "CatalogSettings",    // Настройка каталога
						 "ShopSettings",       // Информация о магазине
						 "PersonType",         // Типы плательщиков
						 "PaySystem",          // Оплата и доставка
						 "DataInstallStep" ,   // Установка решения
						 "FinishStep"          // Завершение настройки
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