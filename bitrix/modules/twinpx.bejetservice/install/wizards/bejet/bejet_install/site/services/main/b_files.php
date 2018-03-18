<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if (!defined("WIZARD_SITE_ID"))
	return;

if (!defined("WIZARD_SITE_DIR"))
	return;

$path = str_replace("//", "/", WIZARD_ABSOLUTE_PATH."/site/private/".LANGUAGE_ID."/");

$handle = @opendir($path);
if ($handle)
{
	while ($file = readdir($handle))
	{
		if (in_array($file, array(".", "..")))
			continue;
			
		CopyDirFiles(
			$path.$file,
			$_SERVER['DOCUMENT_ROOT']."/".$file,
			$rewrite = true,
			$recursive = true,
			$delete_after_copy = false
		);
	}
}

WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH, Array("SITE_DIR" => WIZARD_SITE_DIR));

?>

