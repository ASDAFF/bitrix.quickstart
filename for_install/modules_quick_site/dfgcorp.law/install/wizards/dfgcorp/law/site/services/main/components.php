<?


if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();
if (!defined("WIZARD_SITE_ID"))
	return;
if (!defined("WIZARD_SITE_DIR"))
	return;

	$path = str_replace("//", "/", WIZARD_ABSOLUTE_PATH."/site/components/");
	$handle = @opendir($path);
	if ($handle){
		while ($file = readdir($handle)){
			if (in_array($file, array(".", "..")))
				continue; 
			CopyDirFiles(
				$path.$file,
				WIZARD_SITE_PATH."/bitrix/components/".$file,
				$rewrite = true, 
				$recursive = true,
				$delete_after_copy = false
			);
		}
	}
	//die();
?>