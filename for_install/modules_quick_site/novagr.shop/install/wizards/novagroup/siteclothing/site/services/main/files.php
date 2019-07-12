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
		if (in_array($file, array(".", "..","bitrix_messages")))
			continue;

        if ($file == 'upload')
            continue;
        elseif ($file == 'bitrix_admin')
            continue;
        elseif ($file == 'bitrix_php_interface_init')
            $to = $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/'.WIZARD_SITE_ID;
        elseif ($file == 'bitrix_php_interface')
            continue;
        elseif ($file == 'bitrix_js')
            continue;
        elseif ($file == 'bitrix_images')
            continue;
        elseif ($file == 'bitrix_themes')
            continue;
        else
            $to = WIZARD_SITE_PATH."/".$file;
		
		CopyDirFiles(
			$path.$file,
			$to,
			$rewrite = true, 
			$recursive = true,
			$delete_after_copy = false
		);
	}
	
	//CModule::IncludeModule("search");
	//CSearch::ReIndexAll(Array(WIZARD_SITE_ID, WIZARD_SITE_DIR));
}
copy(WIZARD_THEME_ABSOLUTE_PATH."/favicon.ico", WIZARD_SITE_PATH."favicon.ico");

$str = stripslashes(COption::GetOptionString ('fileman', 'menutypes', '', WIZARD_SITE_ID));
$arTypes = unserialize($str);
if (!array_key_exists('bottom', $arTypes)){
	$arTypes['bottom'] = 'bottom menu';
	COption::SetOptionString('fileman', 'menutypes', serialize($arTypes), '', WIZARD_SITE_ID);
}

COption::SetOptionString("main", "error_reporting", "0");

//обновить файлы компонентов
CopyDirFiles(
    WIZARD_ABSOLUTE_PATH."/site/components/",
    getenv("DOCUMENT_ROOT")."/local/components/",
    $rewrite = true,
    $recursive = true,
    $delete_after_copy = false
);

//обновить файлы админки
CopyDirFiles(
    WIZARD_ABSOLUTE_PATH."/site/module/",
    getenv("DOCUMENT_ROOT")."/bitrix/modules/novagr.shop/",
    $rewrite = true,
    $recursive = true,
    $delete_after_copy = false
);

if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/novagr.shop/admin/'))
{
    if ($dir = opendir($p))
    {
        while (false !== $item = readdir($dir))
        {
            if ($item == '..' || $item == '.' || $item == 'menu.php')
                continue;
            file_put_contents($file = $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/novagr.shop_'.$item,
                '<'.'? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/novagr.shop/admin/'.$item.'");?'.'>');
        }
        closedir($dir);
    }
}
?>