<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if (!defined("WIZARD_TEMPLATE_ID"))
	return;

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

$wizard =& $this->GetWizard();
$bitrixTemplateDir = $_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".WIZARD_TEMPLATE_ID."";

$phone = '<span class="code">' . $wizard->GetVar("phoneCode").'</span> <b>'.$wizard->GetVar("phoneNumber").'</b>';
___writeToAreasFile($bitrixTemplateDir."/include/phone.php", $phone);

___writeToAreasFile($bitrixTemplateDir."/include/copyright.php", $wizard->GetVar("siteCopy"));

$siteLogo = $wizard->GetVar("siteLogo");
$sWizardTemplatePath = WizardServices::GetTemplatesPath(WIZARD_RELATIVE_PATH."/site")."/".WIZARD_TEMPLATE_ID."/";
$sTemplatePath = BX_PERSONAL_ROOT."/templates/".WIZARD_TEMPLATE_ID."/";

// ставим логотип
if($siteLogo>0)
{
	$ff = CFile::GetByID($siteLogo);
	if($zr = $ff->Fetch())
	{
		$strOldFile = str_replace("//", "/", WIZARD_SITE_ROOT_PATH."/".(COption::GetOptionString("main", "upload_dir", "upload"))."/".$zr["SUBDIR"]."/".$zr["FILE_NAME"]);
        @copy($strOldFile, $_SERVER['DOCUMENT_ROOT'].$sTemplatePath."images/logo.png");
        ___writeToAreasFile($_SERVER['DOCUMENT_ROOT'].$sTemplatePath."include/logo.php", '<img src="'.$sTemplatePath.'images/logo.png"  />');
		CFile::Delete($siteLogo);
	}
}
elseif(!file_exists($sTemplatePath."include/logo.php"))
{
    ___writeToAreasFile($_SERVER['DOCUMENT_ROOT'].$sTemplatePath."include/logo.php", '<img src="'.$sTemplatePath.'images/logo.png"  />');
}

$siteImage = $wizard->GetVar("siteImage");
$sWizardTemplatePath = WizardServices::GetTemplatesPath(WIZARD_RELATIVE_PATH."/site")."/".WIZARD_TEMPLATE_ID."/";
$sTemplatePath = BX_PERSONAL_ROOT."/templates/".WIZARD_TEMPLATE_ID."/";

if($siteImage>0)
{
	$ff = CFile::GetByID($siteImage);
	if($zr = $ff->Fetch())
	{
		$strOldFile = str_replace("//", "/", WIZARD_SITE_ROOT_PATH."/".(COption::GetOptionString("main", "upload_dir", "upload"))."/".$zr["SUBDIR"]."/".$zr["FILE_NAME"]);
        @copy($strOldFile, $_SERVER['DOCUMENT_ROOT'].$sTemplatePath."images/im1.png");
        ___writeToAreasFile($_SERVER['DOCUMENT_ROOT'].$sTemplatePath."include/site_image.php", '<img src="'.$sTemplatePath.'images/im1.png"  />');
		CFile::Delete($siteImage);
	}
}
elseif(!file_exists($sTemplatePath."include/site_image.php"))
{
    ___writeToAreasFile($_SERVER['DOCUMENT_ROOT'].$sTemplatePath."include/site_image.php", '<img src="'.$sTemplatePath.'images/im1.png"  />');
}
?>