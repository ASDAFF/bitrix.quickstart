<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if (!defined("WIZARD_SITE_ID"))
	return;

if (WIZARD_INSTALL_DEMO_DATA)
{
	$path = str_replace("//", "/", WIZARD_ABSOLUTE_PATH."/site/public/".LANGUAGE_ID."/");

	$handle = @opendir($path);
	if ($handle)
	{
		while ($file = readdir($handle))
		{
			if (in_array($file, array(".", "..")))
				continue;
/*			elseif (
				is_file($path.$file)
				&&
				(
					($file == "index.php"  && trim(WIZARD_SITE_PATH, " /") == trim(WIZARD_SITE_ROOT_PATH, " /"))
					||
					($file == "_index.php" && trim(WIZARD_SITE_PATH, " /") != trim(WIZARD_SITE_ROOT_PATH, " /"))
				)
			)
				continue;
*/
			CopyDirFiles(
				$path.$file,
				WIZARD_SITE_PATH."/".$file,
				$rewrite = true,
				$recursive = true,
				$delete_after_copy = false
			);
		}
		CModule::IncludeModule("search");
		CSearch::ReIndexAll(false, 0, Array(WIZARD_SITE_ID, WIZARD_SITE_DIR));
	}

	WizardServices::PatchHtaccess(WIZARD_SITE_PATH);

	WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."articles/", Array("SITE_DIR" => WIZARD_SITE_DIR));
	WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."services/", Array("SITE_DIR" => WIZARD_SITE_DIR));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."_index.php", Array("SITE_DIR" => WIZARD_SITE_DIR));

	$arUrlRewrite = array();
	if (file_exists(WIZARD_SITE_ROOT_PATH."/urlrewrite.php"))
	{
		include(WIZARD_SITE_ROOT_PATH."/urlrewrite.php");
	}
	$arNewUrlRewrite = array(
		array(
			"CONDITION" => "#^".WIZARD_SITE_DIR."articles/#",
			"RULE" => "",
			"ID" => "bitrix:news",
			"PATH" => WIZARD_SITE_DIR."articles/index.php"
		),
		array(
			"CONDITION" => "#^".WIZARD_SITE_DIR."services/#",
			"RULE" => "",
			"ID" => "bitrix:news",
			"PATH" => WIZARD_SITE_DIR."services/index.php"
		)
	);

	foreach ($arNewUrlRewrite as $arUrl)
	{
		if (!in_array($arUrl, $arUrlRewrite))
		{
			CUrlRewriter::Add($arUrl);
		}
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

___writeToAreasFile(WIZARD_SITE_PATH."include/company_slogan.php", $wizard->GetVar("siteSlogan"));
___writeToAreasFile(WIZARD_SITE_PATH."include/contacts_phone_footer.php", $wizard->GetVar("sitePhone"));
___writeToAreasFile(WIZARD_SITE_PATH."include/contacts_address_footer.php", $wizard->GetVar("siteAddress"));

$siteLogo = $wizard->GetVar("siteLogo");
if($siteLogo>0)
{
	$ff = CFile::GetByID($siteLogo);
	if($zr = $ff->Fetch())
	{
		$strOldFile = str_replace("//", "/", WIZARD_SITE_ROOT_PATH."/".(COption::GetOptionString("main", "upload_dir", "upload"))."/".$zr["SUBDIR"]."/".$zr["FILE_NAME"]);
		@copy($strOldFile, WIZARD_SITE_PATH."include/logo.png");
		___writeToAreasFile(WIZARD_SITE_PATH."include/company_name.php", '<img src="'.WIZARD_SITE_DIR.'include/logo.png"  />');
		CFile::Delete($siteLogo);
	}
}
elseif(!file_exists(WIZARD_SITE_PATH."include_areas/company_name.php"))
{
	copy(WIZARD_THEME_ABSOLUTE_PATH."/lang/".LANGUAGE_ID."/logo.gif", WIZARD_SITE_PATH."include/logo.png");
	___writeToAreasFile(WIZARD_SITE_PATH."include/company_name.php", '<img src="'.WIZARD_SITE_DIR.'include/logo.png"  />');
}

if (WIZARD_INSTALL_DEMO_DATA)
{
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/.section.php", array("SITE_DESCRIPTION" => htmlspecialcharsbx($wizard->GetVar("siteMetaDescription"))));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/.section.php", array("SITE_KEYWORDS" => htmlspecialcharsbx($wizard->GetVar("siteMetaKeywords"))));
}
?>