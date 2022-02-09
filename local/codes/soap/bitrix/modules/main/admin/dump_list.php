<?php
##############################################
# Bitrix Site Manager                        #
# Copyright (c) 2002-2010 Bitrix             #
# http://www.bitrixsoft.com                  #
# mailto:admin@bitrixsoft.com                #
##############################################
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
define("HELP_FILE", "utilities/dump_list.php");

if(!$USER->CanDoOperation('edit_php'))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

IncludeModuleLangFile(dirname(__FILE__).'/dump.php');

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/backup.php");
$strBXError = '';
$bMcrypt = function_exists('mcrypt_encrypt');
$bBitrixCloud = $bMcrypt && CModule::IncludeModule('bitrixcloud') && CModule::IncludeModule('clouds');

if (function_exists('mb_internal_encoding'))
	mb_internal_encoding('ISO-8859-1');

define('DOCUMENT_ROOT', rtrim(str_replace('\\','/',$_SERVER['DOCUMENT_ROOT']),'/'));

$sTableID = "tbl_dump";
$oSort = new CAdminSorting($sTableID, "timestamp", "desc");
$lAdmin = new CAdminList($sTableID, $oSort);

$path = BX_ROOT."/backup";

// define('DUMP_DEBUG_MODE', true);
// xdebug_start_trace();
$arAllBucket = CBackup::GetBucketList();
if ($_REQUEST['action'])
{
	if (!check_bitrix_sessid())
		die(GetMessage("DUMP_MAIN_SESISON_ERROR"));

	if ($_REQUEST['action'] == 'download')
	{
		$name = $path.'/'.$_REQUEST['f_id'];
		echo '<script>';

		if ($BUCKET_ID = intval($_REQUEST['BUCKET_ID']))
		{
			if (CModule::IncludeModule('clouds'))
			{
				$obBucket = new CCloudStorageBucket($BUCKET_ID);
				if ($obBucket->Init())
				{
					while($obBucket->FileExists($name))
					{
						echo 'window.open("'.htmlspecialcharsbx($obBucket->GetFileSRC(array("URN" => $name))).'");'."\n";
						$name = CTar::getNextName($name);
					}
				}
			}
		}
		else
		{
			while(file_exists(DOCUMENT_ROOT.$name))
			{
				echo 'window.open("'.htmlspecialcharsbx($name).'");'."\n";
				$name = CTar::getNextName($name);
			}
		}
		echo '</script>';
		die();
	}
	elseif ($_REQUEST['action'] == 'link')
	{
		$name = $path.'/'.$_REQUEST['f_id'];
		echo '
		<script>
		';

		$url = '';
		if ($BUCKET_ID = intval($_REQUEST['BUCKET_ID']))
		{
			if (CModule::IncludeModule('clouds'))
			{
				$obBucket = new CCloudStorageBucket($BUCKET_ID);
				if ($obBucket->Init())
					$url = htmlspecialcharsbx($obBucket->GetFileSRC(array("URN" => $name)));
			}
		}
		else
		{
			$host = COption::GetOptionString('main', 'server_name', $_SERVER['HTTP_HOST']);
			$url = 'http://'.htmlspecialcharsbx($host.$name);
		}
		if ($url)
			echo 'window.prompt("'.GetMessage("MAIN_DUMP_USE_THIS_LINK").' restore.php", "'.htmlspecialcharsbx($url).'");'."\n";
		echo '</script>';
		die();
	}
	elseif ($_REQUEST['action'] == 'restore')
	{
		if (!copy($f = DOCUMENT_ROOT.BX_ROOT.'/modules/main/admin/restore.php', DOCUMENT_ROOT.'/restore.php'))
		{
			CAdminMessage::ShowMessage(array(
				"MESSAGE" => GetMessage("MAIN_DUMP_ERROR"),
				"DETAILS" =>  GetMessage("MAIN_DUMP_ERR_COPY_FILE").htmlspecialcharsbx($f),
				"TYPE" => "ERROR",
				"HTML" => true));
		}

		$url = '';
		$name = $path.'/'.$_REQUEST['f_id'];
		$BUCKET_ID = intval($_REQUEST['BUCKET_ID']);
		if ($BUCKET_ID == -1)
				$url = 'bitrixcloud_backup='.htmlspecialcharsbx(basename($name));
		elseif ($BUCKET_ID > 0)
		{
			if (CModule::IncludeModule('clouds'))
			{
				$obBucket = new CCloudStorageBucket($BUCKET_ID);
				if ($obBucket->Init())
					$url = 'arc_down_url='.htmlspecialcharsbx($obBucket->GetFileSRC(array("URN" => $name)));
			}
		}
		else
			$url = 'local_arc_name='.htmlspecialcharsbx($name);
		if ($url)
			echo '<script>document.location = "/restore.php?Step=1&'.$url.'";</script>';
		die();
	}
	elseif ($_REQUEST['action'] == 'restore.php')
	{
		if(CModule::IncludeModule("compression"))
			Ccompress::DisableCompression();

		if ($contents = file_get_contents($f = DOCUMENT_ROOT.BX_ROOT.'/modules/main/admin/restore.php'))
		{
			header("Content-Type: application/octet-stream");
			header("Content-Length: ".CTar::strlen($contents));
			header("Content-Disposition: attachment; filename=\"restore.php\"");
			header("Expires: 0");
			header("Cache-Control: no-cache, must-revalidate");
			header("Pragma: no-cache");

			echo $contents;
		}
		else
			CAdminMessage::ShowMessage(array(
				"MESSAGE" => GetMessage("MAIN_DUMP_ERROR"),
				"DETAILS" => GetMessage("MAIN_DUMP_ERR_COPY_FILE").htmlspecialcharsbx($f),
				"TYPE" => "ERROR",
				"HTML" => true));
		die();
	}
}
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/prolog.php");
######### Admin list #######
#$arFilterFields = array();
#$lAdmin->InitFilter($arFilterFields);
$lAdmin->BeginPrologContent();

if ($arID = $lAdmin->GroupAction())
{
	if($_REQUEST['action_target'] == 'selected')
	{
		$arID = array();
		if (is_dir($p = DOCUMENT_ROOT.BX_ROOT.'/backup'))
		{
			if ($dir = opendir($p))
			{
				while(($item = readdir($dir)) !== false)
				{
					$f = $p.'/'.$item;
					if (!is_file($f))
						continue;
					$arID[] = '0_'.basename($f);
				}
				closedir($dir);
			}
		}

		if ($arAllBucket)
		{
			foreach($arAllBucket as $arBucket)
			{
				if ($arCloudFiles = CBackup::GetBucketFileList($arBucket['ID'], BX_ROOT.'/backup/'))
				{
					foreach($arCloudFiles['file'] as $k=>$v)
					{
						$arID[] = $arBucket['ID'].'_'.$v;
					}
				}
			}
		}

		if ($bBitrixCloud)
			$lAdmin->AddGroupError(GetMessage("MAIN_DUMP_ERR_DELETE"), '');
	}

	$bBitrixCloudDelete = false;
	foreach ($arID as $ID)
	{

		if (strlen($ID) <= 0)
			continue;

		switch ($_REQUEST['action'])
		{
			case "delete":
				if (preg_match('#^(-?[0-9]+)_(.+)$#', $ID, $regs))
				{
					$BUCKET_ID = $regs[1];
					$item = $regs[2];

					if ($BUCKET_ID == -1)
					{
						if (!$bBitrixCloudDelete)
							$lAdmin->AddGroupError(GetMessage("MAIN_DUMP_ERR_DELETE"), $ID);
						$bBitrixCloudDelete = true;
					}
					elseif ($BUCKET_ID > 0)
					{
						if (CModule::IncludeModule('clouds'))
						{
							$obBucket = new CCloudStorageBucket($BUCKET_ID);
							if ($obBucket->Init())
							{
								$name = $path.'/'.$item;
								while($obBucket->FileExists($name))
								{
									$file_size = $obBucket->GetFileSize($name);
									if ($obBucket->DeleteFile($name))
										$obBucket->DecFileCounter($file_size);
									$name = CTar::getNextName($name);
								}

								$e = $APPLICATION->GetException();
								if(is_object($e))
									$lAdmin->AddGroupError($e->GetString(), $ID);
							}
							else
								$lAdmin->AddGroupError(GetMessage("MAIN_DUMP_ERR_INIT_CLOUD"), $ID);
						}
					}
					else
					{
						while(file_exists($f = DOCUMENT_ROOT.$path.'/'.$item))
						{
							if (!unlink($f))
								$lAdmin->AddGroupError(GetMessage('DUMP_DELETE_ERROR',array('#FILE#' => $f)), $ID);

							$item = CTar::getNextName($item);
						}
					}
				}
			break;
			case "rename":
				if (preg_match('#^[a-z0-9\-\._]+$#i',$_REQUEST['name']))
				{
					$arName = ParseFileName($_REQUEST['ID']);
					$new_name = $_REQUEST['name'].'.'.$arName['ext'];

					if ($BUCKET_ID = intval($_REQUEST['BUCKET_ID']))
					{
						// Not realized 'cos no cloud API
					}
					else
					{
						while(file_exists(DOCUMENT_ROOT.$path.'/'.$ID))
						{
							if (!rename(DOCUMENT_ROOT.$path.'/'.$ID, DOCUMENT_ROOT.$path.'/'.$new_name))
							{
								$lAdmin->AddGroupError(GetMessage("MAIN_DUMP_ERR_FILE_RENAME").htmlspecialcharsbx($ID), $ID);
								break;
							}

							$ID = CTar::getNextName($ID);
							$new_name = CTar::getNextName($new_name);
						}
					}
				}
				else
					$lAdmin->AddGroupError(GetMessage("MAIN_DUMP_ERR_NAME"), $ID);
			break;
		}
	}
}

$arDirs = array();
$arFiles = array();
$arTmpFiles = array();
$arFilter = array();
if (is_dir($p = DOCUMENT_ROOT.BX_ROOT.'/backup'))
{
	if ($dir = opendir($p))
	{
		while(($item = readdir($dir)) !== false)
		{
			$f = $p.'/'.$item;
			if (!is_file($f))
				continue;
			$arTmpFiles[] = array(
				'NAME' => $item,
				'SIZE' => filesize($f),
				'DATE' => filemtime($f),
				'BUCKET_ID' => 0,
				'PLACE' => GetMessage("MAIN_DUMP_LOCAL")
			);
		}
		closedir($dir);
	}
}

if ($bBitrixCloud)
{
	$backup = CBitrixCloudBackup::getInstance();
	try {
		foreach($backup->listFiles() as $ar)
		{
			$arTmpFiles[] = array(
				'NAME' => $ar['FILE_NAME'],
				'SIZE' => $ar['FILE_SIZE'],
				'DATE' => preg_match('#^([0-9]{4})([0-9]{2})([0-9]{2})_([0-9]{2})([0-9]{2})([0-9]{2})#', $ar['FILE_NAME'], $r) ? strtotime("{$r[1]}-{$r[2]}-{$r[3]} {$r[4]}:{$r[5]}:{$r[6]}") : 0,
				'BUCKET_ID' => -1,
				'PLACE' => GetMessage('DUMP_MAIN_BITRIX_CLOUD')
			);
		}
	} catch (Exception $e) {
		$bBitrixCloud = false;
		$strBXError = $e->getMessage();
	}
}

if ($arAllBucket)
{
	foreach($arAllBucket as $arBucket)
	{
		if ($arCloudFiles = CBackup::GetBucketFileList($arBucket['ID'], BX_ROOT.'/backup/'))
		{
			foreach($arCloudFiles['file'] as $k=>$v)
			{
				$arTmpFiles[] = array(
					'NAME' => $v,
					'SIZE' => $arCloudFiles['file_size'][$k],
					'DATE' => preg_match('#^([0-9]{4})([0-9]{2})([0-9]{2})_([0-9]{2})([0-9]{2})([0-9]{2})#', $v, $r) ? strtotime("{$r[1]}-{$r[2]}-{$r[3]} {$r[4]}:{$r[5]}:{$r[6]}") : 0,
					'BUCKET_ID' => $arBucket['ID'],
					'PLACE' => htmlspecialcharsbx($arBucket['BUCKET'].' ('.$arBucket['SERVICE_ID'].')')
				);
			}
		}
	}
}

$arParts = array();
$arSize = array();
$i=0;
foreach($arTmpFiles as $k=>$ar)
{
	if (preg_match('#^(.*\.(enc|tar|gz|sql))(\.[0-9]+)?$#',$ar['NAME'],$regs))
	{
		$i++;
		$BUCKET_ID = intval($ar['BUCKET_ID']);
		$arParts[$BUCKET_ID.$regs[1]]++;
		$arSize[$BUCKET_ID.$regs[1]] += $ar['SIZE'];
		if (!$regs[3])
		{
			if ($by == 'size')
				$key = $arSize[$BUCKET_ID.$regs[1]];
			elseif ($by == 'timestamp')
				$key = $ar['DATE'];
			elseif ($by == 'location')
				$key = $ar['PLACE'];
			else // name
				$key = $regs[1];
			$key .= '_'.$i;
			$arFiles[$key] = $ar;
		}
	}
}

if ($order == 'desc')
	krsort($arFiles);
else
	ksort($arFiles);

$rsDirContent = new CDBResult;
$rsDirContent->InitFromArray($arFiles);
$rsDirContent->NavStart(20);

$lAdmin->NavText($rsDirContent->GetNavPrint(GetMessage("MAIN_DUMP_FILE_PAGES")));
$lAdmin->AddHeaders(array(
		array("id"=>"NAME", "content"=>GetMessage("MAIN_DUMP_FILE_NAME"), "sort"=>"name", "default"=>true),
		array("id"=>"SIZE","content"=>GetMessage("MAIN_DUMP_FILE_SIZE1"), "sort"=>"size", "default"=>true),
		array("id"=>"PLACE","content"=>GetMessage("MAIN_DUMP_LOCATION"), "sort"=>"location", "default"=>true),
		array("id"=>"DATE", "content"=>GetMessage('MAIN_DUMP_FILE_TIMESTAMP'), "sort"=>"timestamp", "default"=>true)
));

$arWriteBucket = CBackup::GetBucketList($arFilter = array('READ_ONLY' => 'N'));
while($f = $rsDirContent->NavNext(true, "f_"))
{
	$BUCKET_ID = intval($f['BUCKET_ID']);
	$row =& $lAdmin->AddRow($BUCKET_ID.'_'.$f['NAME'], $f);

	$c = $arParts[$BUCKET_ID.$f['NAME']];
	if ($c > 1)
	{
		$parts = ' ('.GetMessage("MAIN_DUMP_PARTS").$c.')';
		$size = $arSize[$BUCKET_ID.$f['NAME']];
	}
	else
	{
		$parts = '';
		$size = $f['SIZE'];
	}

	$row->AddField("NAME", $f['NAME'].$parts);
	$row->AddField("SIZE", CFile::FormatSize($size));
	$row->AddField("PLACE", $f['PLACE']);
	$row->AddField("DATE", ($t = time() - $f['DATE']) < 86400 && $t > 0 ? HumanTime($t).' '.GetMessage('DUMP_BACK') : ConvertTimeStamp($f['DATE'], 'FULL'));

	$arActions = Array();

	if (defined('DUMP_DEBUG_MODE'))
	{
		$arActions[] = array(
			"ICON" => "clouds",
			"TEXT" => 'DEBUG - '.GetMessage("MAIN_DUMP_SEND_CLOUD").' Bitrix',
			"ACTION" => "if(confirm('".CUtil::JSEscape(GetMessage("MAIN_DUMP_SEND_FILE_CLOUD"))."?')) ".$lAdmin->ActionRedirect("/bitrix/admin/dump.php?f_id=".urlencode($f['NAME'])."&action=cloud_send&dump_bucket_id=-1&".bitrix_sessid_get())
		);
		$arActions[] = array(
			"ICON" => "archive",
			"TEXT" => 'DEBUG - '.GetMessage("INTEGRITY_CHECK"),
			"ACTION" => $lAdmin->ActionRedirect("/bitrix/admin/dump.php?f_id=".urlencode($f['NAME'])."&process=Y&action=check_archive&".bitrix_sessid_get())
		);
	}

	if (!preg_match('#\.sql$#i',$f['NAME']))
	{
		if ($BUCKET_ID != -1)
		{
			$arActions[] = array(
				"ICON" => "download",
				"DEFAULT" => true,
				"TEXT" => GetMessage("MAIN_DUMP_ACTION_DOWNLOAD"),
				"ACTION" => "AjaxSend('/bitrix/admin/dump_list.php?action=download&f_id=".$f['NAME']."&BUCKET_ID=".$BUCKET_ID."&".bitrix_sessid_get()."')"
			);
			$arActions[] = array(
				"ICON" => "link",
				"TEXT" => GetMessage("MAIN_DUMP_GET_LINK"),
				"ACTION" => "AjaxSend('/bitrix/admin/dump_list.php?action=link&f_id=".$f['NAME']."&BUCKET_ID=".$BUCKET_ID."&".bitrix_sessid_get()."')"
			);
		}

		$arActions[] = array(
			"ICON" => "restore",
			"TEXT" => GetMessage("MAIN_DUMP_RESTORE"),
			"ACTION" => "if(confirm('".CUtil::JSEscape(GetMessage("MAIN_RIGHT_CONFIRM_EXECUTE"))."')) AjaxSend('/bitrix/admin/dump_list.php?action=restore&f_id=".$f['NAME']."&BUCKET_ID=".$BUCKET_ID."&".bitrix_sessid_get()."')"
		);

		if ($BUCKET_ID == 0)
		{
			if ($arWriteBucket)
			{
				$arActions[] = array("SEPARATOR" => true);
				foreach($arWriteBucket as $arBucket)
					$arActions[] = array(
						"ICON" => "clouds",
						"TEXT" => GetMessage("MAIN_DUMP_SEND_CLOUD").htmlspecialcharsbx('"'.$arBucket['BUCKET'].'"'),
						"ACTION" => "if(confirm('".CUtil::JSEscape(GetMessage("MAIN_DUMP_SEND_FILE_CLOUD"))."?')) ".$lAdmin->ActionRedirect("/bitrix/admin/dump.php?f_id=".urlencode($f['NAME'])."&action=cloud_send&dump_bucket_id=".$arBucket['ID']."&".bitrix_sessid_get())
					);
			}

			$arActions[] = array("SEPARATOR" => true);
			$arName = ParseFileName($f['NAME']);
			$arActions[] = array(
				"ICON" => "rename",
				"TEXT" => GetMessage("MAIN_DUMP_RENAME"),
				"ACTION" => "if(name=prompt('".CUtil::JSEscape(GetMessage("MAIN_DUMP_ARC_NAME_W_O_EXT"))."','".htmlspecialcharsbx($arName['name'])."')) tbl_dump.GetAdminList('/bitrix/admin/dump_list.php?ID=".urlencode($f['NAME'])."&action=rename&lang=".LANGUAGE_ID."&".bitrix_sessid_get()."&BUCKET_ID=".$BUCKET_ID."&name='+name);"
			);
		}
	}

	if ($BUCKET_ID > -1)
	{
		$arActions[] = array(
			"ICON" => "delete",
			"TEXT" => GetMessage("MAIN_DUMP_DELETE"),
			"ACTION" => "if(confirm('".CUtil::JSEscape(GetMessage('MAIN_DUMP_ALERT_DELETE'))."')) ".$lAdmin->ActionDoGroup($BUCKET_ID.'_'.$f['NAME'], "delete")
		);
	}
	$row->AddActions($arActions);
}

$lAdmin->AddFooter(
	array(
/*		array(
			"title" => GetMessage("MAIN_ADMIN_LIST_SELECTED"),
			"value" => $rsDirContent->SelectedRowsCount()
		),
		array(
			"counter" => true,
			"title" => GetMessage("MAIN_ADMIN_LIST_CHECKED"),
			"value" => "0"
		),*/
	)
);

$lAdmin->AddGroupActionTable(
	array(
		"delete" => GetMessage("MAIN_ADMIN_LIST_DELETE")
	)
);

$aContext = array(
	array(
		"TEXT"	=> GetMessage("MAIN_DUMP_FILE_DUMP_BUTTON"),
		"LINK"	=> "dump.php?lang=".LANGUAGE_ID,
		"TITLE"	=> GetMessage("MAIN_DUMP_FILE_DUMP_BUTTON"),
		"ICON"	=> "btn_new"
	),
	array(
		"TEXT"  => GetMessage("MAIN_DUMP_AUTO_BUTTON"),
		"LINK"  => "dump_auto.php?lang=".LANGUAGE_ID,
		"TITLE" => GetMessage("MAIN_DUMP_AUTO_BUTTON"),
		// "ICON"  => "btn_new"
	),
);
$lAdmin->AddAdminContextMenu($aContext);
$lAdmin->CheckListMode();


$APPLICATION->SetTitle(GetMessage("MAIN_DUMP_LIST_PAGE_TITLE"));
require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_admin_after.php");

?><script>
	function AjaxSend(url, data)
	{
		CHttpRequest.Action = function(result)
		{
			BX('dump_result_div').innerHTML = result;
		}
		if (data)
			CHttpRequest.Post(url, data);
		else
			CHttpRequest.Send(url);
	}

	function EndDump()
	{
	}
</script>
<div id="dump_result_div"></div>
<?
$lAdmin->DisplayList();

echo BeginNote();
echo GetMessage("MAIN_DUMP_HEADER_MSG", array('#EXPORT#' => '/bitrix/admin/dump_list.php?action=restore.php&'.bitrix_sessid_get()));
echo EndNote();

require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");
#################################################
################## FUNCTIONS
function HumanTime($t)
{
	$ar = array(GetMessage('TIME_S'),GetMessage('TIME_M'),GetMessage('TIME_H'));
	if ($t < 60)
		return sprintf('%d '.$ar[0], $t);
	if ($t < 3600)
		return sprintf('%d '.$ar[1], floor($t/60));
//		return sprintf('%d '.$ar[1].' %d '.$ar[0], floor($t/60), $t%60);
	return sprintf('%d '.$ar[2].' %d '.$ar[1], floor($t/3600), floor($t%3600/60));
//	return sprintf('%d '.$ar[2].' %d '.$ar[1].' %d '.$ar[0], floor($t/3600), floor($t%3600/60), $t%60);
}

function ParseFileName($name)
{
	if (preg_match('#^(.+)\.(tar.*)$#', $name, $regs))
		return array('name' => $regs[1], 'ext' => $regs[2]);
	elseif (preg_match('#^(.+)\.([^\.]+)$#', $name, $regs))
		return array('name' => $regs[1], 'ext' => $regs[2]);
	return array('name' => $name, 'ext' => '');
}
?>
