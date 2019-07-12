<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if (!defined("WIZARD_SITE_ID"))
	return;

if (!defined("WIZARD_SITE_DIR"))
	return;

if (COption::GetOptionString("main", "upload_dir") == "")
	COption::SetOptionString("main", "upload_dir", "upload");
	
if(COption::GetOptionString("eshop", "wizard_installed", "N", WIZARD_SITE_ID) == "Y" && !WIZARD_INSTALL_DEMO_DATA)
{
	$wizard =& $this->GetWizard();
	

	if($wizard->GetVar('siteCodeCity', true)){	
		$code_city = preg_replace('~\D~', '', $wizard->GetVar("siteCodeCity"));
		___writeToAreasFile(WIZARD_SITE_PATH."include/code_tel.php", '('.$code_city.')');
	}	
	if($wizard->GetVar('siteNameCity', true))	
		___writeToAreasFile(WIZARD_SITE_PATH."include/city.php", $wizard->GetVar("siteNameCity"));	
	if($wizard->GetVar('siteTelephone', true))
		___writeToAreasFile(WIZARD_SITE_PATH."include/tel.php", $wizard->GetVar("siteTelephone"));
	
	if($wizard->GetVar('siteNameTaxi', true))
		___writeToAreasFile(WIZARD_SITE_PATH."include/name.php", $wizard->GetVar("siteNameTaxi"));

	if($wizard->GetVar('siteDescription', true)){
		$content = '<h2>'.GetMessage("WIZ_FILES_1").'</h2><article><p>#DESCRIPTION#</p></article><a class="see_all" href="'.SITE_DIR.'about/"><span>'.GetMessage("WIZ_FILES_2").'</span></a>';	
		$content = str_replace('#DESCRIPTION#', $wizard->GetVar("siteDescription"), $content);
		___writeToAreasFile(WIZARD_SITE_PATH."include/about.php", $content);
	}
	
	if($wizard->GetVar('siteMap', true)){	
		if ($wizard->GetVar("siteMap") == 'select') $map = 'yandex';
		elseif ($wizard->GetVar("siteMap") == 'list') $map = 'google';
		___writeToAreasFile(WIZARD_SITE_PATH."include/geoservice.php", $map);
	}

	//die;
	return;
}

$path = str_replace("//", "/", WIZARD_ABSOLUTE_PATH."/site/public/".LANGUAGE_ID."/"); 

$handle = @opendir($path);
if ($handle)
{
	while ($file = readdir($handle))
	{
		if (in_array($file, array(".", "..")))
			continue; 

		CopyDirFiles(
			$path.$file,
			WIZARD_SITE_PATH."/".$file,
			$rewrite = true, 
			$recursive = true,
			$delete_after_copy = false,
			$exclude = "bitrix"
		);
		if($wizard->GetVar('siteLogoSet', true)){
			CopyDirFiles(
				WIZARD_SITE_PATH."/_index_.php",
				WIZARD_SITE_PATH."/_index.php",
				$rewrite = true,
				$recursive = true,
				$delete_after_copy = true
			);
		}
		else
		{
			DeleteDirFilesEx(WIZARD_SITE_DIR."/_index_.php");
		}
	}
	

}

WizardServices::PatchHtaccess(WIZARD_SITE_PATH);

WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."about/", Array("SITE_DIR" => WIZARD_SITE_DIR));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."catalog/", Array("SITE_DIR" => WIZARD_SITE_DIR));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."include/", Array("SITE_DIR" => WIZARD_SITE_DIR));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."login/", Array("SITE_DIR" => WIZARD_SITE_DIR));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."news/", Array("SITE_DIR" => WIZARD_SITE_DIR));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."personal/", Array("SITE_DIR" => WIZARD_SITE_DIR));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."search/", Array("SITE_DIR" => WIZARD_SITE_DIR));
WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."store/", Array("SITE_DIR" => WIZARD_SITE_DIR));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."_index.php", Array("SITE_DIR" => WIZARD_SITE_DIR));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH.".top.menu.php", Array("SITE_DIR" => WIZARD_SITE_DIR));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."sect_search.php", Array("SITE_DIR" => WIZARD_SITE_DIR));

//WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."about/", Array("SALE_EMAIL" => $wizard->GetVar("siteTelephone")));
//WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."about/delivery/", Array("SALE_PHONE" => $wizard->GetVar("siteTelephone")));

copy(WIZARD_THEME_ABSOLUTE_PATH."/favicon.ico", WIZARD_SITE_PATH."favicon.ico");


$arNewUrlRewrite = array(
		array(
				"CONDITION"	=>	"#^".WIZARD_SITE_DIR."news/#",
				"RULE"	=>	"",
				"ID"	=>	"bitrix:news",
				"PATH"	=>	 WIZARD_SITE_DIR."news/index.php",
		),

);
$arNewUrlRewrite[] =
array(
		"CONDITION"	=>	"#^".WIZARD_SITE_DIR."services/([\\w\\d-]+)(\\\\?(.*))?#",
		"RULE"	=>	"code=$1",
		"ID"	=>	"bitrix:news.list",
		"PATH"	=>	WIZARD_SITE_DIR."services/detail.php",
);
$arNewUrlRewrite[] =
array(
		"CONDITION"	=>	"#^".WIZARD_SITE_DIR."prices/([\\w\\d-]+)(\\\\?(.*))?#",
		"RULE"	=>	"code=$1",
		"ID"	=>	"bitrix:news.list",
		"PATH"	=>	WIZARD_SITE_DIR."prices/detail.php",
);




foreach ($arNewUrlRewrite as $arUrl)
{
	if (!in_array($arUrl, $arUrlRewrite))
	{
		CUrlRewriter::Add($arUrl);
	}
}


function ___writeToAreasFile($fn, $text)
{
	if(file_exists($fn) && !is_writable($abs_path) && defined("BX_FILE_PERMISSIONS"))
		@chmod($abs_path, BX_FILE_PERMISSIONS);

	$fd = @fopen($fn, "wb");
	if(!$fd)
		return false;

	if(false === fwrite($fd, $text))
	{
		fclose($fd);
		return false;
	}

	fclose($fd);

	if(defined("BX_FILE_PERMISSIONS"))
		@chmod($fn, BX_FILE_PERMISSIONS);
}

CheckDirPath(WIZARD_SITE_PATH."include/");

$wizard =& $this->GetWizard();

$path_template = $_SERVER['DOCUMENT_ROOT'].'/bitrix/templates/'.WIZARD_TEMPLATE_ID.'_'.WIZARD_THEME_ID;


if($wizard->GetVar('siteCodeCity', true)){	
	$code_city = preg_replace('~\D~', '', $wizard->GetVar("siteCodeCity"));
	___writeToAreasFile(WIZARD_SITE_PATH."include/code_tel.php", '('.$code_city.')');
}	
if($wizard->GetVar('siteNameCity', true))	
	___writeToAreasFile(WIZARD_SITE_PATH."include/city.php", $wizard->GetVar("siteNameCity"));	
if($wizard->GetVar('siteTelephone', true))
	___writeToAreasFile(WIZARD_SITE_PATH."include/tel.php", $wizard->GetVar("siteTelephone"));

if($wizard->GetVar('siteNameTaxi', true))
	___writeToAreasFile(WIZARD_SITE_PATH."include/name.php", $wizard->GetVar("siteNameTaxi"));

if($wizard->GetVar('siteDescription', true)){
	$content = '<h2>'.GetMessage("WIZ_FILES_1").'</h2><article><p>#DESCRIPTION#</p></article><a class="see_all" href="'.SITE_DIR.'about/"><span>'.GetMessage("WIZ_FILES_2").'</span></a>';	
	$content = str_replace('#DESCRIPTION#', $wizard->GetVar("siteDescription"), $content);
	___writeToAreasFile(WIZARD_SITE_PATH."include/about.php", $content);
}

if($wizard->GetVar('siteMap', true)){	
	if ($wizard->GetVar("siteMap") == 'select'){
		copy(WIZARD_SITE_PATH."include/map/map.php", WIZARD_SITE_PATH."include/map.php");
		$map = 'yandex';
	}	
	elseif ($wizard->GetVar("siteMap") == 'list'){
		copy(WIZARD_SITE_PATH."include/map/map_g.php", WIZARD_SITE_PATH."include/map.php");
		$map = 'google';
	}
	___writeToAreasFile(WIZARD_SITE_PATH."include/geoservice.php", $map);
}



CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/index.php", Array("SITE_DIR" => WIZARD_SITE_DIR));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/.section.php", array("SITE_DESCRIPTION" => htmlspecialcharsbx($wizard->GetVar("siteMetaDescription"))));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/.section.php", array("SITE_KEYWORDS" => htmlspecialcharsbx($wizard->GetVar("siteMetaKeywords"))));


$array_month = array(GetMessage("MONTH_1"), GetMessage("MONTH_2"), GetMessage("MONTH_3"), GetMessage("MONTH_4"), GetMessage("MONTH_5"), GetMessage("MONTH_6"), GetMessage("MONTH_7"), GetMessage("MONTH_8"), GetMessage("MONTH_9"), GetMessage("MONTH_10"), GetMessage("MONTH_11"), GetMessage("MONTH_12"));

$date = date("d").' '.$array_month[date("n")-1].' '.date("Y").''.GetMessage("YEAR");

$info_table ='<table class="bx-gadgets-info-site-table" cellspacing="0"><tr><td class="bx-gadget-gray">'.GetMessage("MAIN_DESKTOP_CREATEDBY_KEY").'</td><td>'.GetMessage("MAIN_DESKTOP_CREATEDBY_VALUE").'</td><td class="bx-gadgets-info-site-logo" rowspan="5"><img src="/bitrix/components/bitrix/desktop/templates/admin/images/site_logo.png"></td></tr><tr><td class="bx-gadget-gray">'.GetMessage("MAIN_DESKTOP_ADRESS").'</td><td><a href="http://www.3colors.ru">www.3colors.ru</a></td></tr><tr><td class="bx-gadget-gray">'.GetMessage("MAIN_DESKTOP_DATE").'</td><td>'.$date.'</td></tr><tr><td class="bx-gadget-gray">'.GetMessage("MAIN_DESKTOP_RESPONSIBLE_KEY").'</td><td>'.GetMessage("MAIN_DESKTOP_RESPONSIBLE_VALUE").'</td></tr><tr><td class="bx-gadget-gray">E-mail:</td><td><a href="mailto:3@3colors.ru">3@3colors.ru</a></td></tr><tr><td class="bx-gadget-gray">'.GetMessage("MAIN_DESKTOP_PHONE_KEY").'</td><td>8 (3412) 676-555</td></tr></table>';

$arOptions = array(
		array(
				"GADGETS" => array(
						"HTML_AREA@444444444" => array(
								"COLUMN" => 1,
								"ROW" => 0,
								"HIDE" => "N",
								"USERDATA" => array(
										"content" => $info_table
								),
								"SETTINGS" => array(
										"TITLE_STD" => GetMessage("MAIN_DESKTOP_INFO_TITLE")
								)
						),						
				)
		)
);
CUserOptions::SetOption('intranet', "~gadgets_admin_index", $arOptions, true);

?>