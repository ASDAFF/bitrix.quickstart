<?
@set_time_limit(10000);
ini_set("track_errors", "1");
ignore_user_abort(true);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
IncludeModuleLangFile(__FILE__);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/prolog.php");
define("HELP_FILE", "utilities/file_checker.php");

if(!$USER->CanDoOperation('view_other_settings'))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$isAdmin = $USER->CanDoOperation('edit_other_settings');

@set_time_limit(10000);

$serverFileLogName = "/serverfilelog.dat";
$serverFileLogPath = $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules";
$serverFileLog = $serverFileLogPath.$serverFileLogName;

$serverFileLogNameError = "/serverfilerr.dat";
$serverFileLogError = $serverFileLogPath.$serverFileLogNameError;

$errorMessage = "";
$okMessage = "";
$message = null;

$INTEGRITY_VALUE = "";
if (defined("INTEGRITY_VALUE"))
	$INTEGRITY_VALUE = INTEGRITY_VALUE;

if (!function_exists("file_get_contents"))
{
	function file_get_contents($filename)
	{
		$fd = fopen("$filename", "rb");
		$content = fread($fd, filesize($filename));
		fclose($fd);
		return $content;
	}
}

function fileCRC($path)
{
	$fileString = file_get_contents($path);
	$crc = crc32($fileString);
	return sprintf("%u", $crc);
}

function CRCCryptData($data, $pwdString, $type)
{
	$type = strtoupper($type);
	if ($type != "D")
		$type = "E";

	$res_data = "";

	if ($type == 'D')
		$data = urldecode($data);

	$key[] = "";
	$box[] = "";
	$temp_swap = "";
	$pwdLength = CUtil::BinStrlen($pwdString);

	for ($i = 0; $i <= 255; $i++)
	{
		$key[$i] = ord(CUtil::BinSubstr($pwdString, ($i % $pwdLength), 1));
		$box[$i] = $i;
	}
	$x = 0;

	for ($i = 0; $i <= 255; $i++)
	{
		$x = ($x + $box[$i] + $key[$i]) % 256;
		$temp_swap = $box[$i];
		$box[$i] = $box[$x];
		$box[$x] = $temp_swap;
	}
	$temp = "";
	$k = "";
	$cipherby = "";
	$cipher = "";
	$a = 0;
	$j = 0;
	for ($i = 0, $n = CUtil::BinStrlen($data); $i < $n; $i++)
	{
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$temp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $temp;
		$k = $box[(($box[$a] + $box[$j]) % 256)];
		$cipherby = ord(CUtil::BinSubstr($data, $i, 1)) ^ $k;
		$cipher .= chr($cipherby);
	}

	if ($type == 'D')
		$res_data = urldecode(urlencode($cipher));
	else
		$res_data = urlencode($cipher);

	return $res_data;
}

function CRCPrepareFileInfo($path)
{
	$path = str_replace("\\", "/", $path);

	$fileSize = filesize($_SERVER["DOCUMENT_ROOT"].$path);
	$fileCRC = fileCRC($_SERVER["DOCUMENT_ROOT"].$path);

	return "*".$path."*".$fileSize."*".$fileCRC;
}

function CRCGetDirRec($path, $arExcept, &$hFile)
{
	$path = str_replace("\\", "/", $path);

	if ($handle = @opendir($_SERVER["DOCUMENT_ROOT"].$path))
	{
		while (($file = readdir($handle)) !== false)
		{
			if ($file == "." || $file == "..")
				continue;

			$bExcept = False;
			for ($i = 0; $i < count($arExcept); $i++)
			{
				if (
					strlen($path."/".$file) >= strlen($arExcept[$i])
					&& substr($path."/".$file, 0, strlen($arExcept[$i])) == $arExcept[$i])
				{
					$bExcept = True;
					break;
				}
			}

			if ($bExcept)
				continue;

			if (is_dir($_SERVER["DOCUMENT_ROOT"].$path."/".$file))
			{
				CRCGetDirRec($path."/".$file, $arExcept, $hFile);
			}
			else
			{
				fwrite($hFile, CRCPrepareFileInfo($path."/".$file)."\n");
			}
		}

		closedir($handle);
	}

	return;
}

function CRCVerifyDirRec($path, $arExcept, &$hFileError, &$logData)
{
	$path = str_replace("\\", "/", $path);

	if ($handle = @opendir($_SERVER["DOCUMENT_ROOT"].$path))
	{
		while (($file = readdir($handle)) !== false)
		{
			if ($file == "." || $file == "..")
				continue;

			$bExcept = False;
			for ($i = 0; $i < count($arExcept); $i++)
			{
				if (
					strlen($path."/".$file) >= strlen($arExcept[$i])
					&& substr($path."/".$file, 0, strlen($arExcept[$i])) == $arExcept[$i])
				{
					$bExcept = True;
					break;
				}
			}

			if ($bExcept)
				continue;

			if (is_dir($_SERVER["DOCUMENT_ROOT"].$path."/".$file))
			{
				CRCVerifyDirRec($path."/".$file, $arExcept, $hFileError, $logData);
			}
			else
			{
				if (strpos($logData, "*".$path."/".$file."*") === false)
					fwrite($hFileError, str_replace("#FILE#", $path."/".$file, GetMessage("MFCW_C_FILE_NEW"))."\n");
			}
		}

		closedir($handle);
	}

	return;
}

function CRCVerify($path, $arExcept, $logName, &$hFileError)
{
	$hFile = fopen($logName, "r");

	while (!feof($hFile))
	{
		$buffer = fgets($hFile, 4096);
		if (strlen($buffer) > 0)
		{
			$arBuffer = explode("*", $buffer);

			if (count($arBuffer) < 4)
			{
				$errorCnt++;
				fwrite($hFileError, str_replace("#BUF#", $buffer, GetMessage("MFCW_C_STR_BAD"))."\n");
			}
			else
			{
				$bShouldCheck = False;

				if (strlen($path) <= 0
					|| substr($arBuffer[1], 0, strlen($path)) == $path)
				{
					$bShouldCheck = True;

					for ($i = 0; $i < count($arExcept); $i++)
					{
						if (
							strlen($arBuffer[1]) >= strlen($arExcept[$i])
							&& substr($arBuffer[1], 0, strlen($arExcept[$i])) == $arExcept[$i])
						{
							$bShouldCheck = False;
							break;
						}
					}
				}

				if ($bShouldCheck)
				{
					if (!file_exists($_SERVER["DOCUMENT_ROOT"].$arBuffer[1]))
					{
						$errorCnt++;
						fwrite($hFileError, str_replace("#FILE#", $_SERVER["DOCUMENT_ROOT"].$arBuffer[1], GetMessage("MFCW_C_NO_FILE"))."\n");
					}
					else
					{
						$fs = filesize($_SERVER["DOCUMENT_ROOT"].$arBuffer[1]);
						if ($fs != $arBuffer[2])
						{
							$errorCnt++;
							fwrite($hFileError, str_replace("#NS#", $fs, str_replace("#OS#", $arBuffer[2], str_replace("#FILE#", $_SERVER["DOCUMENT_ROOT"].$arBuffer[1], GetMessage("MFCW_C_FILE_SIZE"))))."\n");
						}
						else
						{
							$crc = fileCRC($_SERVER["DOCUMENT_ROOT"].$arBuffer[1]);
							if ($crc != Trim($arBuffer[3]))
							{
								$errorCnt++;
								fwrite($hFileError, str_replace("#NS#", $crc, str_replace("#OS#", Trim($arBuffer[3]), str_replace("#FILE#", $_SERVER["DOCUMENT_ROOT"].$arBuffer[1], GetMessage("MFCW_C_FILE_CRC"))))."\n");
							}
							else
							{
								$goodCnt++;
							}
						}
					}
				}
			}
		}
	}

	fclose($hFile);

	$logData = file_get_contents($logName);
	CRCVerifyDirRec($path, $arExcept, $hFileError, $logData);
}

function GetIntegrityParams($data, $password)
{
	if (strlen($data) <= 0)
		return False;

	$dataNew = CRCCryptData($data, $password, "D");
	$arDataNew = explode("*", $dataNew);

	if (count($arDataNew) != 2)
		return False;

	return array("CRC" => $arDataNew[0], "KEY" => $arDataNew[1]);
}

function SetIntegrityParams($arData, $password)
{
	if (!is_array($arData) || !isset($arData["CRC"]) || !isset($arData["KEY"]))
		return False;

	$data = $arData["CRC"]."*".$arData["KEY"];
	$dataNew = CRCCryptData($data, $password, "E");

	return $dataNew;
}

if ($REQUEST_METHOD == "GET" && $get_data_file == "Y" && $isAdmin && check_bitrix_sessid())
{
	$bUseCompression = (function_exists("gzcompress") ? True : False);

	$streamFileName = $serverFileLog;
	if ($bUseCompression)
	{
		$streamFileName = $serverFileLog."2";
		$zp_file = gzopen($serverFileLog."2", "wb9f");

		$hFile = fopen($serverFileLog, "rb");
		while (!feof($hFile))
		{
			$buffer = fgets($hFile, 4096);
			if (strlen($buffer) > 0)
				gzwrite($zp_file, $buffer);
		}
		gzclose($zp_file);
		fclose($hFile);
	}

	$filesize = filesize($streamFileName);
	header($_SERVER["SERVER_PROTOCOL"]." 200 OK");
	header("Content-Type: application/force-download; name=\"".substr($serverFileLogName, 1)."\"");
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: ".$filesize);
	header("Content-Disposition: attachment; filename=\"".substr($serverFileLogName, 1)."\"");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Expires: 0");
	header("Pragma: public");

	$p = file_get_contents($streamFileName);
	echo $p;
	flush();

	if ($bUseCompression)
	{
		if (!@unlink($serverFileLog."2"))
			$errorMessage .= str_replace("#FILE#", $serverFileLog."2", GetMessage("MFCW_CANT_DEL_FILE"))."<br>";
	}
}

if ($REQUEST_METHOD == "GET" && $delete_errlog == "Y" && $isAdmin && check_bitrix_sessid())
{
	if (!@unlink($serverFileLogError))
		$errorMessage .= str_replace("#FILE#", $serverFileLogError, GetMessage("MFCW_CANT_DEL_LOG"))."<br>";
}

if ($REQUEST_METHOD == "POST" && $action == "load" && $isAdmin && check_bitrix_sessid())
{
	if (!isset($_FILES["crc_file"]) || !is_uploaded_file($_FILES["crc_file"]["tmp_name"]))
		$errorMessage .= GetMessage("MFCW_FILE_NOT_LOAD")."<br>";

	if (strlen($errorMessage) <= 0)
	{
		$bUseCompression = (function_exists("gzcompress") ? True : False);

		if ($bUseCompression && $crc_unpack_file == "Y")
		{
			$hFile = fopen($serverFileLog, "wb");
			$zp_file = gzopen($_FILES["crc_file"]["tmp_name"], "rb9f");
			while (!gzeof($zp_file))
			{
				$buffer = gzread($zp_file, 4096);
				if (strlen($buffer) > 0)
					fwrite($hFile, $buffer);
			}
			gzclose($zp_file);
			fclose($hFile);

			$okMessage .= GetMessage("MFCW_FILE_LOAD_SUCCESS")."<br>";
		}
		else
		{
			if (copy($_FILES["crc_file"]["tmp_name"], $serverFileLog))
				$okMessage .= GetMessage("MFCW_FILE_LOAD_SUCCESS")."<br>";
			else
				$errorMessage .= GetMessage("MFCW_FILE_CANT_COPY")."<br>";
		}
	}
}

if ($REQUEST_METHOD == "POST" && $action == "integrity" && $isAdmin && check_bitrix_sessid())
{
	if (strlen($crc_password) <= 0)
	{
		$errorMessage .= GetMessage("MFCW_NO_PASSWORD")."<br>";
		$aMsg[] = array("id"=>"crc_password", "text"=>GetMessage("MFCW_NO_PASSWORD"));
	}
	elseif(strlen($errorMessage) <= 0 && $crc_password_check != $crc_password)
	{
		$errorMessage .= GetMessage("MFCW_NO_PASSWORD_CONF")."<br>";
		$aMsg[] = array("id"=>"crc_password_check", "text"=>GetMessage("MFCW_NO_PASSWORD_CONF"));
		$aMsg[] = array("id"=>"crc_password", "text"=>"");
	}

	if (strlen($errorMessage) <= 0)
	{
		$bVerify = True;
		if (strlen($crc_collect) > 0)
			$bVerify = False;

		if (!$bVerify)
		{
			if (strlen($crc_key) <= 0)
			{
				$errorMessage .= GetMessage("MFCW_NO_NEW_KEY")."<br>";
				$aMsg[] = array("id"=>"crc_key", "text"=>GetMessage("MFCW_NO_NEW_KEY"));
			}
		}
	}

	if (strlen($errorMessage) <= 0)
	{
		if (!$bVerify)
		{
			if ($crc_key == $crc_password)
			{
				$errorMessage .= GetMessage("MFCW_KEY_PASS")."<br>";
				$aMsg[] = array("id"=>"crc_key", "text"=>GetMessage("MFCW_KEY_PASS"));
				$aMsg[] = array("id"=>"crc_password", "text"=>"");
			}
		}
	}

	if (strlen($errorMessage) <= 0)
	{
		if ($bVerify)
		{
			$fileString = file_get_contents(__FILE__);
			$fileString = preg_replace("#<"."\?[\s]*define\(\"INTEGRITY_VALUE\",[\s]*\"[^\"]*?\"\);?[\s]*\?".">#i", "", $fileString);
			$currentCRC = sprintf("%u", crc32($fileString));

			if (strlen($INTEGRITY_VALUE) > 0)
			{
				if ($arIntegrityParams = GetIntegrityParams($INTEGRITY_VALUE, $crc_password))
				{
					if ($arIntegrityParams["CRC"] != $currentCRC)
						$errorMessage .= GetMessage("MFCW_CRC_CHANGED")."<br>";
					else
						$okMessage .= str_replace("#KEY#", $arIntegrityParams["KEY"], GetMessage("MFCW_INT_CHECK"))."<br>";
				}
				else
				{
					$errorMessage .= GetMessage("MFCW_INT_BAD")."<br>";
				}
			}
			else
			{
				$errorMessage .= GetMessage("MFCW_INT_NO")."<br>";
			}
		}
		else
		{
			$fileString = file_get_contents(__FILE__);
			$fileString = preg_replace("#<"."\?[\s]*define\(\"INTEGRITY_VALUE\",[\s]*\"[^\"]*?\"\);?[\s]*\?".">#i", "", $fileString);
			$currentCRC = sprintf("%u", crc32($fileString));

			$data = SetIntegrityParams(
					array("CRC" => $currentCRC, "KEY" => $crc_key),
					$crc_password
				);

			$fileString = "<"."?define(\"INTEGRITY_VALUE\",\"".$data."\");?".">".$fileString;

			$hFile = fopen(__FILE__, "wb");
			fwrite($hFile, $fileString);
			fclose($hFile);

			$INTEGRITY_VALUE = $data;
			$okMessage .= GetMessage("MFCW_KEY_SET")."<br>";
		}
	}
}

if ($REQUEST_METHOD == "POST" && $action == "check" && $isAdmin && check_bitrix_sessid())
{
	if (strlen($crc_password) <= 0)
	{
		$errorMessage .= GetMessage("MFCW_NO_PASSWORD")."<br>";
		$aMsg[] = array("id"=>"crc_password", "text"=>GetMessage("MFCW_NO_PASSWORD"));
	}
	elseif(strlen($errorMessage) <= 0 && $crc_password_check != $crc_password)
	{
		$errorMessage .= GetMessage("MFCW_NO_PASSWORD_CONF")."<br>";
		$aMsg[] = array("id"=>"crc_password_check", "text"=>GetMessage("MFCW_NO_PASSWORD_CONF"));
		$aMsg[] = array("id"=>"crc_password", "text"=>"");
	}

	if (strlen($errorMessage) <= 0)
	{
		$crc_kernel = (($crc_kernel == "Y") ? "Y" : "N");
		$crc_special = (($crc_special == "Y") ? "Y" : "N");
		$crc_public = (($crc_public == "Y") ? "Y" : "N");

		if ($crc_kernel != "Y" && $crc_special != "Y" && $crc_public != "Y")
		{
			$errorMessage = GetMessage("MFCW_NO_AREA")."<br>";
			$aMsg[] = array("id"=>"crc_kernel", "text"=>GetMessage("MFCW_NO_AREA"));
			$aMsg[] = array("id"=>"crc_public", "text"=>"");
			$aMsg[] = array("id"=>"crc_special", "text"=>"");
		}
	}

	if (strlen($errorMessage) <= 0)
	{
		$bVerify = True;
		if (strlen($crc_collect) > 0)
			$bVerify = False;

		if ($bVerify)
		{
			if (!file_exists($serverFileLog))
				$errorMessage = GetMessage("MFCW_NO_VERIFILE")."<br>";
		}
	}

	if (strlen($errorMessage) <= 0)
	{
		if ($bVerify)
		{
			$fileString = file_get_contents($serverFileLog);
			$fileStringNew = CRCCryptData($fileString, $crc_password, "D");
			if (substr($fileStringNew, 0, 1) == "*")
			{
				$hFile = fopen($serverFileLog."1", "wb");
				fwrite($hFile, $fileStringNew);
				fclose($hFile);
			}
			else
			{
				$errorMessage .= GetMessage("MFCW_ERR_DECRYPT");
			}
		}
	}

	if (strlen($errorMessage) <= 0)
	{
		$aExceptSpec = array(
			BX_ROOT."/modules",
			BX_PERSONAL_ROOT."/cache",
			BX_PERSONAL_ROOT."/cache_image",
			BX_PERSONAL_ROOT."/managed_cache",
			BX_PERSONAL_ROOT."/stack_cache",
		);
		if ($bVerify)
		{
			$hFileError = fopen($serverFileLogError, "wb");

			if ($crc_kernel == "Y")
				CRCVerify(BX_ROOT."/modules", array(BX_ROOT."/modules".$serverFileLogName, BX_ROOT."/modules".$serverFileLogName."1", BX_ROOT."/modules".$serverFileLogNameError), $serverFileLog."1", $hFileError);

			if ($crc_special == "Y")
			{
				CRCVerify(BX_ROOT, $aExceptSpec, $serverFileLog."1", $hFileError);
				if(BX_PERSONAL_ROOT <> BX_ROOT)
					CRCVerify(BX_PERSONAL_ROOT, $aExceptSpec, $serverFileLog."1", $hFileError);
			}

			if ($crc_public == "Y")
				CRCVerify("", array(BX_ROOT, "/upload"), $serverFileLog."1", $hFileError);

			fclose($hFileError);

			if (!@unlink($serverFileLog."1"))
				$errorMessage .= str_replace("#FILE#", $serverFileLog."1", GetMessage("MFCW_CANT_DEL_FILE"))."<br>";

			$okMessage .= GetMessage("MFCW_CHECK_FINISH")."<br>";
		}
		else
		{
			$hFile = fopen($serverFileLog."1", "w");

			if ($crc_kernel == "Y")
				CRCGetDirRec(BX_ROOT."/modules", array(BX_ROOT."/modules".$serverFileLogName, BX_ROOT."/modules".$serverFileLogName."1", BX_ROOT."/modules".$serverFileLogNameError), $hFile);

			if ($crc_special == "Y")
			{
				CRCGetDirRec(BX_ROOT, $aExceptSpec, $hFile);
				if(BX_PERSONAL_ROOT <> BX_ROOT)
					CRCGetDirRec(BX_PERSONAL_ROOT, $aExceptSpec, $hFile);
			}

			if ($crc_public == "Y")
				CRCGetDirRec("", array(BX_ROOT, "/upload"), $hFile);

			fclose($hFile);

			sleep(3);

			$fileString = file_get_contents($serverFileLog."1");
			$fileStringNew = CRCCryptData($fileString, $crc_password, "E");
			$hFile = fopen($serverFileLog, "wb");
			fwrite($hFile, $fileStringNew);
			fclose($hFile);

			if (!@unlink($serverFileLog."1"))
				$errorMessage .= str_replace("#FILE#", $serverFileLog."1", GetMessage("MFCW_CANT_DEL_FILE"))."<br>";

			$okMessage .= GetMessage("MFCW_INFO_CHECK")."<br>";
		}
	}
}

$APPLICATION->SetTitle(GetMessage("MFCW_FILE_TITLE"));

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("MFCW_TAB_1"), "ICON" => "file_check", "TITLE" => GetMessage("MFCW_FILE_CHECK")),
	array("DIV" => "edit2", "TAB" => GetMessage("MFCW_TAB_2"), "ICON" => "file_check", "TITLE" => GetMessage("MFCW_LOAD_VERIFILE_T")),
	array("DIV" => "edit3", "TAB" => GetMessage("MFCW_TAB_3"), "ICON" => "file_check", "TITLE" => GetMessage("MFCW_INT_TITLE")),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs, true, true);
if(!empty($aMsg))
{
	$e = new CAdminException($aMsg);
	$APPLICATION->ThrowException($e);
	if($e = $APPLICATION->GetException())
	{
		$message = new CAdminMessage(GetMessage("MFCW_ERROR"), $e);
		if($message)
			echo $message->Show();
	}
}

if(strlen($okMessage)>0)
	CAdminMessage::ShowMessage(Array("MESSAGE" =>$okMessage, "TYPE"=>"OK"));
if(strlen($errorMessage)>0 && !$message)
	CAdminMessage::ShowMessage($errorMessage);

$tabControl->Begin();
?>
<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>" name="ffile_checker">
<?echo bitrix_sessid_post()?>
<input type="hidden" name="action" value="check">
<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
<input type="hidden" name="tabControl_active_tab" value="edit1">
<?
$tabControl->BeginNextTab();
?>
	<tr>
		<td valign="top" width="30%"><span class="required">*</span><?echo GetMessage("MFCW_PASSWORD")?></td>
		<td valign="top" width="70%">
			<input type="password" name="crc_password" style="width:80%;"> <?echo BeginNote(); echo GetMessage("MFCW_PASSWORD_HINT");?><br><?echo GetMessage("MFCW_PASSWORD_HINT1"); echo EndNote();?>
		</td>
	</tr>
	<tr>
		<td valign="top"><span class="required">*</span><?echo GetMessage("MFCW_PASSWORD_RET")?></td>
		<td valign="top">
			<input type="password" name="crc_password_check" style="width:80%;">
		</td>
	</tr>
	<tr>
		<td valign="top"><span class="required">*</span><?echo GetMessage("MFCW_AREAS")?></td>
		<td valign="top"><input type="checkbox" name="crc_kernel" id="id_crc_kernel" value="Y"<?if ($REQUEST_METHOD != "POST" || $crc_kernel == "Y") echo " checked";?>> <label for="id_crc_kernel"><?echo GetMessage("MFCW_KERNEL")?></label><br>
				<input type="checkbox" name="crc_special" id="id_crc_special" value="Y"<?if ($crc_special == "Y") echo " checked";?>> <label for="id_crc_special"><?echo GetMessage("MFCW_SLUG")?></label><br>
				<input type="checkbox" name="crc_public" id="id_crc_public" value="Y"<?if ($crc_public == "Y") echo " checked";?>> <label for="id_crc_public"><?echo GetMessage("MFCW_PUBLIC")?></label><br>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<?if (file_exists($serverFileLog))
			{
				?><input type="submit"<?if(!$isAdmin) echo " disabled"?> name="crc_verify" value="<?echo GetMessage("MFCW_CHECK_FILES")?>">&nbsp;<?
			}?>
			<input type="submit"<?if(!$isAdmin) echo " disabled"?> name="crc_collect" value="<?echo GetMessage("MFCW_INFO_FILES")?>">
		</td>
	</tr>
<?
$tabControl->EndTab();
?>
</form>
<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>" name="ffile_load" enctype="multipart/form-data">
<?echo bitrix_sessid_post()?>
<input type="hidden" name="action" value="load">
<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
<input type="hidden" name="tabControl_active_tab" value="edit2">
<?
$tabControl->BeginNextTab();
if ($REQUEST_METHOD == "POST" && $action == "check" && $bVerify):
	?>
	<tr><td colspan="2"><textarea name="ta" rows="20" cols="100"><?
			$fileString = file_get_contents($serverFileLogError);
			if (strlen($fileString) <= 0)
				echo GetMessage("MFCW_NO_CHANGES");
			else
				echo $fileString;
			?></textarea><br>
		<a href="file_checker.php?lang=<?=LANG?>&amp;delete_errlog=Y&amp;<?echo bitrix_sessid_get()?>" title="<?=GetMessage("MFCW_DEL_LOG")?>"><?echo GetMessage("MFCW_DEL_LOG")?> (<?= $serverFileLogError ?>)</a></td>
	</tr>
<?
endif;
?>
	<?
	if (file_exists($serverFileLog) && $isAdmin)
	{
		?>
		<tr>
			<td valign="top" align="left" colspan="2">
				<?
				echo str_replace("#URL#", "file_checker.php?lang=".LANG."&amp;get_data_file=Y&amp;".bitrix_sessid_get(), str_replace("#FILE#", $serverFileLog, GetMessage("MFCW_CUR_VERIFILE")));
				echo GetMessage("MFCW_CUR_VERIFILE_H");
				?>
			</td>
		</tr>
		<?
	}
	?>
	<tr>
		<td valign="top" width="30%"><span class="required">*</span><?echo GetMessage("MFCW_VERIFILE")?></td>
		<td valign="top" width="70%"><input type="hidden" name="MAX_FILE_SIZE" value="1000000000">
			<input type="file" name="crc_file" size="40"></td>
	</tr>
	<?if (function_exists("gzcompress"))
	{
		?>
		<tr>
			<td valign="top"><label for="crc_unpack_file"><?echo GetMessage("MFCW_VERIPACK")?></label></td>
			<td valign="top"><input type="checkbox" name="crc_unpack_file" value="Y" id="crc_unpack_file"></td>
		</tr>
		<?
	}?>
	<tr>
		<td colspan="2"><input type="submit"<?if(!$isAdmin) echo " disabled"?> name="crc_load" value="<?echo GetMessage("MFCW_VERILOAD")?>"></td>
	</tr>
<?
$tabControl->EndTab();
?>
</form>
<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>" name="ffile_integrity">
<?echo bitrix_sessid_post()?>
<input type="hidden" name="action" value="integrity">
<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
<input type="hidden" name="tabControl_active_tab" value="edit3">
<?
$tabControl->BeginNextTab();

if(strlen($INTEGRITY_VALUE) <= 0):
?>
	<tr>
		<td colspan="2">
		<?
		CAdminMessage::ShowMessage(GetMessage("MFCW_PROMT_KEY"));
		?>
		</td>
	</tr>
<?
endif;
?>
	<tr>
		<td valign="top" width="30%"><span class="required">*</span><?echo GetMessage("MFCW_PASSWORD")?><br></td>
		<td valign="top" width="70%"><input type="password" name="crc_password" style="width:80%;"><?echo BeginNote().GetMessage("MFCW_INT_PASS")."<br>".GetMessage("MFCW_INT_PASS1").EndNote();?></td>
	</tr>
	<tr>
		<td valign="top"><span class="required">*</span><?echo GetMessage("MFCW_PASSWORD_RET")?></td>
		<td valign="top"><input type="password" name="crc_password_check" style="width:80%;"></td>
	</tr>
	<tr>
		<td valign="top"><?if (strlen($INTEGRITY_VALUE) <= 0) echo "<span class=\"required\">*</span>";?><?echo GetMessage("MFCW_INT_KEY")?><br><small><?
			if (strlen($INTEGRITY_VALUE) <= 0)
				echo "<span class=\"required\">(".GetMessage("MFCW_INT_NO_KEY").")</span>";
			else
				echo "(".GetMessage("MFCW_INT_HAVE_KEY").")";
			?></small></td>
		<td valign="top"><input type="text" name="crc_key" style="width:80%;" value=""><?echo BeginNote().GetMessage("MFCW_INT_KEY_HINT").EndNote();?></td>
	</tr>
	<tr>
		<td colspan="2">
			<?
			if (strlen($INTEGRITY_VALUE) > 0)
			{
				?><input type="submit"<?if(!$isAdmin) echo " disabled"?> name="crc_verify" value="<?echo GetMessage("MFCW_INT_DO_CHECK")?>">&nbsp;<?
			}
			?>
			<input type="submit"<?if(!$isAdmin) echo " disabled"?> name="crc_collect" value="<?echo GetMessage("MFCW_INT_DO_SET")?>">
		</td>
	</tr>
<?
$tabControl->EndTab();
?>
</form>

<?
$tabControl->End();

$tabControl->ShowWarnings("ffile_checker", $message);
$tabControl->ShowWarnings("ffile_integrity", $message);

$legend = GetMessage("MFCW_LEGEND");
if (strlen($legend) > 0)
{
	echo BeginNote().$legend.EndNote();
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>
