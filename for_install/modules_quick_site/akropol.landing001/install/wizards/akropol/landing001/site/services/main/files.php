<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

if (!defined("WIZARD_SITE_ID"))
    return;

if (!defined("WIZARD_SITE_DIR"))
    return;

$path = str_replace("//", "/", WIZARD_ABSOLUTE_PATH . "/site/public/" . LANGUAGE_ID . "/");

$handle = @opendir($path);
if ($handle) {
    while ($file = readdir($handle)) {
        if (in_array($file, array(
            ".",
            "..",
            "bitrix_messages",
            "bitrix_admin",
            "bitrix_php_interface",
            "bitrix_js",
            "bitrix_images",
            "bitrix_themes"
        ))
        ) continue;


        if ($file == 'bitrix_php_interface_init')
            $to = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/php_interface/' . WIZARD_SITE_ID;
        elseif ($file == 'upload')
            $to = $_SERVER['DOCUMENT_ROOT'] . '/upload/';
        else
            $to = WIZARD_SITE_PATH . "/" . $file;

        CopyDirFiles(
            $path . $file,
            $to,
            $rewrite = true,
            $recursive = true,
            $delete_after_copy = false
        );
    }

}
copy(WIZARD_THEME_ABSOLUTE_PATH . "/favicon.ico", WIZARD_SITE_PATH . "favicon.ico");

CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/_index.php", array(
"SITE_SEO_TITLE" => htmlspecialcharsbx($wizard->GetVar("siteSeoTitle")),
"PHONE" => htmlspecialcharsbx($wizard->GetVar("sitePhone")),
"SITE_SEO_DESCRIPTION" => htmlspecialcharsbx($wizard->GetVar("siteSeoDescription")),
"SITE_SEO_KEYWORDS" => htmlspecialcharsbx($wizard->GetVar("siteSeoKeywords")),
"SITE_TITLE" => htmlspecialcharsbx($wizard->GetVar("siteName")),
"SITE_SLOGAN" => htmlspecialcharsbx($wizard->GetVar("siteDescription")),
"SITE_TITLE" => htmlspecialcharsbx($wizard->GetVar("siteName")),
));

CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/local/templates/".WIZARD_TEMPLATE_ID."/include/logo.php", array("SITE_TITLE" => htmlspecialcharsbx($wizard->GetVar("siteName"))));

//START IMG LOGO
$siteLogoImg = $wizard->GetVar("siteLogoImg");
if($siteLogoImg>0)
{
	$ff = CFile::GetByID($siteLogoImg);
	if($zr = $ff->Fetch())
	{
		$strOldFile = str_replace("//", "/", WIZARD_SITE_ROOT_PATH."/".(COption::GetOptionString("main", "upload_dir", "upload"))."/".$zr["SUBDIR"]."/".$zr["FILE_NAME"]);
		$strNewFile = WIZARD_SITE_PATH."include/logo.png";
		@copy($strOldFile, $strNewFile);
		CFile::Delete($siteLogoImg);
	}
}
else
{
	$strNewFile = WIZARD_SITE_PATH."include/logo.png";
	@copy(WIZARD_SITE_ROOT_PATH."/bitrix/wizards/akropol/landing001/images/ru/logotip.png",$strNewFile);
}
//END IMG LOGO

CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/_index.php", array(
"LOGO_IMG_URL" => WIZARD_SITE_DIR."include/logo.png",
));

//START Add BG
$siteBgImg = $wizard->GetVar("siteBgImg");
if($siteBgImg>0)
{
	$ff = CFile::GetByID($siteBgImg);
	if($zr = $ff->Fetch())
	{
		$strOldFile = str_replace("//", "/", WIZARD_SITE_ROOT_PATH."/".(COption::GetOptionString("main", "upload_dir", "upload"))."/".$zr["SUBDIR"]."/".$zr["FILE_NAME"]);
		$strNewFile = WIZARD_SITE_PATH."include/bg03.jpg";
		@copy($strOldFile, $strNewFile);
		CFile::Delete($siteBgImg);
	}
}
else
{
	$strNewFile = WIZARD_SITE_PATH."include/bg03.jpg";
	@copy(WIZARD_SITE_ROOT_PATH."/bitrix/wizards/akropol/landing001/images/ru/bg03.jpg",$strNewFile);
}
//END Add BG

CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/sect_area_1.php", array(
"HEADER_IMG_URL" => WIZARD_SITE_DIR."include/bg03.jpg",
));

//START UP
$siteUp = $wizard->GetVar("upAction");
if($siteUp == "Y")
{
	$strNewFile = WIZARD_SITE_PATH."include/up.php";
	@copy(WIZARD_SITE_ROOT_PATH."/bitrix/wizards/akropol/landing001/include/ru/up.php",$strNewFile);
}
//END UP


//START FUNK COPY F
$bitrixTemplateDir = $_SERVER["DOCUMENT_ROOT"] . "/bitrix/templates/" . WIZARD_TEMPLATE_ID;

CopyDirFiles(
    $_SERVER["DOCUMENT_ROOT"] . WizardServices::GetTemplatesPath(WIZARD_RELATIVE_PATH . "/site") . "/" . WIZARD_TEMPLATE_ID,
    $bitrixTemplateDir,
    $rewrite = true,
    $recursive = true,
    $delete_after_copy = false
);
//END FUNK COPY F
?>