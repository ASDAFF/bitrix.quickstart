<?
require_once(dirname(__FILE__)."/../include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/prolog.php");
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/classes/general/wizard.php");
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/classes/general/tar_gz.php");

if(!$USER->CanDoOperation('edit_php') || !check_bitrix_sessid())
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

IncludeModuleLangFile(__FILE__);

$ID = $_REQUEST["ID"];
$ID = str_replace("\\", "", $ID);
$ID = str_replace("/", "", $ID);

$bUseCompression = true;
if(!extension_loaded('zlib') || !function_exists("gzcompress"))
	$bUseCompression = false;

$HTTP_ACCEPT_ENCODING = "";

CheckDirPath($_SERVER['DOCUMENT_ROOT'].BX_PERSONAL_ROOT."/tmp/wizards/");
$tempFile = $_SERVER['DOCUMENT_ROOT'].BX_PERSONAL_ROOT."/tmp/wizards/".md5(uniqid(rand(), true).".tar.gz");
$wizardPath = $_SERVER["DOCUMENT_ROOT"].CWizardUtil::GetRepositoryPath().CWizardUtil::MakeWizardPath($ID);

$strError = "";

if(is_dir($wizardPath))
{
	$oArchiver = new CArchiver($tempFile, $bUseCompression);
	$success = $oArchiver->add("\"".$wizardPath."\"", false, $_SERVER["DOCUMENT_ROOT"].CWizardUtil::GetRepositoryPath());

	if ($success)
	{
		header('Pragma: public');
		header('Cache-control: private');
		header('Accept-Ranges: bytes');
		header("Content-Length: ".filesize($tempFile));
		header("Content-Type: application/x-force-download; filename=".str_replace(":", "-", $ID).".tar.gz");
		header("Content-Disposition: attachment; filename=\"".str_replace(":", "-", $ID).".tar.gz\"");
		header("Content-Transfer-Encoding: binary");

		readfile($tempFile);
		unlink($tempFile);
	}
	else
	{
		$strError .= GetMessage("MAIN_WIZARD_EXPORT_ERROR");
		$arErrors = &$oArchiver->GetErrors();
		if(count($arErrors)>0)
		{
			$strError .= ":<br>";
			foreach ($arErrors as $value)
				$strError .= "[".$value[0]."] ".$value[1]."<br>";
		}
		else
			$strError .= ".<br>";
	}
}
else
	$strError .= GetMessage("MAIN_WIZARD_EXPORT_ERROR");

if (strlen($strError) > 0)
{
	$APPLICATION->SetTitle(GetMessage("MAIN_WIZARD_EXPORT_ERROR"));
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

	CAdminMessage::ShowMessage($strError);

	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin_before.php");
}
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin_after.php");
?>