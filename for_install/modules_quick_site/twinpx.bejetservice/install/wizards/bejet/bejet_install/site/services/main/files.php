<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if (!defined("WIZARD_SITE_ID"))
	return;

if (!defined("WIZARD_SITE_DIR"))
	return;


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
}

WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH, Array("SITE_DIR" => WIZARD_SITE_DIR));

/*$arUrlRewrite = array();
if (file_exists(WIZARD_SITE_ROOT_PATH."/urlrewrite.php"))
{
	include(WIZARD_SITE_ROOT_PATH."/urlrewrite.php");
}

$arNewUrlRewrite = array(
	array(
		"CONDITION"	=>	"#^".WIZARD_SITE_DIR."events/#",
		"RULE"	=>	"",
		"ID"	=>	"bitrix:news",
		"PATH"	=>	 WIZARD_SITE_DIR."events/index.php",
	),
	array(
		"CONDITION"	=>	"#^".WIZARD_SITE_DIR."services/#",
		"RULE"	=>	"",
		"ID"	=>	"bejet:catalog",
		"PATH"	=>	 WIZARD_SITE_DIR."services/index.php",
	),
);


foreach ($arNewUrlRewrite as $arUrl)
{
	if (!in_array($arUrl, $arUrlRewrite))
	{
		CUrlRewriter::Add($arUrl);
	}
}*/


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

//CheckDirPath(WIZARD_SITE_PATH."include_areas/");

$wizard =& $this->GetWizard();
___writeToAreasFile(WIZARD_SITE_PATH."bitrix/templates/bejet_themes_serv/inc/slogan.php", $wizard->GetVar("siteSlogan"));
//___writeToAreasFile(WIZARD_SITE_PATH."bitrix/templates/bejet_themes_serv/inc/logo.php", $wizard->GetVar("siteName"));
___writeToAreasFile(WIZARD_SITE_PATH."bitrix/templates/bejet_themes_serv/inc/tel.php", $wizard->GetVar("sitePhone"));
___writeToAreasFile(WIZARD_SITE_PATH."bitrix/templates/bejet_themes_serv/inc/address.php", $wizard->GetVar("siteAddress"));
___writeToAreasFile(WIZARD_SITE_PATH."include_areas/slogan.php", $wizard->GetVar("siteSlogan"));
//___writeToAreasFile($_SERVER['DOCUMENT_ROOT']."/include_areas/logo.php", $wizard->GetVar("siteName"));
___writeToAreasFile(WIZARD_SITE_PATH."include_areas/tel.php", $wizard->GetVar("sitePhone"));
___writeToAreasFile(WIZARD_SITE_PATH."include_areas/address.php", $wizard->GetVar("siteAddress"));
?>

