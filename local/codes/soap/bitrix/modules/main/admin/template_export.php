<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/prolog.php");

if((!$USER->CanDoOperation('edit_other_settings') && !$USER->CanDoOperation('view_other_settings')) || !check_bitrix_sessid())
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$ID = str_replace("\\", "", $ID);
$ID = str_replace("/", "", $ID);
$bUseCompression = True;
if(!extension_loaded('zlib') || !function_exists("gzcompress"))
	$bUseCompression = False;

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/tar_gz.php");

CheckDirPath($_SERVER['DOCUMENT_ROOT'].BX_PERSONAL_ROOT."/tmp/templates/");
$tmpfname = $_SERVER['DOCUMENT_ROOT'].BX_PERSONAL_ROOT."/tmp/templates/".md5(uniqid(rand(), true).".tar.gz");

$HTTP_ACCEPT_ENCODING = "";

$strError = "";
if(is_dir($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".$ID))
{
	$oArchiver = new CArchiver($tmpfname, $bUseCompression);
	$tres = $oArchiver->add("\"".$_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".$ID."\"", false, $_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/");
	if(!$tres)
	{
		$strError = "Archiver error";
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

	header('Pragma: public');
	header('Cache-control: private');
	header("Content-Type: application/force-download; name=\"".$ID.".tar.gz\"");
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: ".filesize($tmpfname));
	header("Content-Disposition: attachment; filename=\"".$ID.".tar.gz\"");
	header("Expires: 0");
	
	readfile($tmpfname);
	unlink($tmpfname);
	//	die();
}

if (strlen($strError) > 0)
{
	$APPLICATION->SetTitle("Archiver error");
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

	CAdminMessage::ShowMessage($strError);

	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin_before.php");
}
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin_after.php");
?>
