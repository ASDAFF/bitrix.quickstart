<?php
##############################################
# Bitrix Site Manager                        #
# Copyright (c) 2002-2010 Bitrix             #
# http://www.bitrixsoft.com                  #
# mailto:admin@bitrixsoft.com                #
##############################################
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
define("HELP_FILE", "utilities/dump.php");

if(!$USER->CanDoOperation('edit_php'))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

if(!defined("START_EXEC_TIME"))
	define("START_EXEC_TIME", microtime(true));

if (!defined("BX_DIR_PERMISSIONS"))
	define("BX_DIR_PERMISSIONS", 0777);

if (!defined("BX_FILE_PERMISSIONS"))
	define("BX_FILE_PERMISSIONS", 0666);

IncludeModuleLangFile(__FILE__);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/backup.php");
$strBXError = '';
$bGzip = function_exists('gzcompress');
$bMcrypt = function_exists('mcrypt_encrypt');
$bBitrixCloud = $bMcrypt;
if (!CModule::IncludeModule('bitrixcloud'))
{
	$bBitrixCloud = false;
	$strBXError = GetMessage('ERR_NO_BX_CLOUD');
}
elseif (!CModule::IncludeModule('clouds'))
{
	$bBitrixCloud = false;
	$strBXError = GetMessage('ERR_NO_CLOUDS');
}

if (function_exists('mb_internal_encoding'))
	mb_internal_encoding('ISO-8859-1');

define('DOCUMENT_ROOT', rtrim(str_replace('\\','/',$_SERVER['DOCUMENT_ROOT']),'/'));

// define('DUMP_DEBUG_MODE', true);
// xdebug_start_trace();

$arAllBucket = CBackup::GetBucketList();

if($_REQUEST['process'] == "Y")
{
	if (!check_bitrix_sessid())
		RaiseErrorAndDie(GetMessage("DUMP_MAIN_SESISON_ERROR"));

	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_js.php");
	$NS =& $_SESSION['BX_DUMP_STATE'];
	if($_REQUEST['action'] == 'start')
	{
		define('NO_TIME', true);

		$bFull = $_REQUEST['dump_all'] == 'Y';

		if(!file_exists(DOCUMENT_ROOT.BX_ROOT."/backup"))
			mkdir(DOCUMENT_ROOT.BX_ROOT."/backup", BX_DIR_PERMISSIONS);

		if(!file_exists(DOCUMENT_ROOT.BX_ROOT."/backup/index.php"))
		{
			$f = fopen(DOCUMENT_ROOT.BX_ROOT."/backup/index.php","w");
			fwrite($f,"<head><meta http-equiv=\"REFRESH\" content=\"0;URL=/bitrix/admin/index.php\"></head>");
			fclose($f);
		}

		if(!is_dir(DOCUMENT_ROOT.BX_ROOT."/backup") || !is_writable(DOCUMENT_ROOT.BX_ROOT."/backup"))
			RaiseErrorAndDie(GetMessage("MAIN_DUMP_FOLDER_ERR",array('#FOLDER#' => DOCUMENT_ROOT.BX_ROOT.'/backup')));

		DeleteDirFilesEx(BX_ROOT.'/backup/clouds');

		$NS = Array();
		$NS['finished_steps'] = 0;
		$NS['BUCKET_ID'] = intval($_REQUEST['dump_bucket_id']);
		COption::SetOptionInt("main", "dump_bucket_id", $NS['BUCKET_ID']);

		if ($bMcrypt && $_REQUEST['dump_encrypt_key'])
		{
			$NS['dump_encrypt_key'] =  $_REQUEST['dump_encrypt_key'];
//			if (!defined('BX_UTF') || !BX_UTF)
//				$NS['dump_encrypt_key'] = $APPLICATION->ConvertCharset('windows-1251','utf-8',$NS['dump_encrypt_key']);
			COption::SetOptionInt("main", "dump_encrypt", 1);
		}
		else
			COption::SetOptionInt("main", "dump_encrypt", 0);

		if ($NS['BUCKET_ID'] == -1 && !$NS['dump_encrypt_key'])
			RaiseErrorAndDie(GetMessage('MAIN_DUMP_BXCLOUD_ENC'));


		$bUseCompression = $bGzip && ($_REQUEST['dump_disable_gzip'] != 'Y' || $bFull);
		COption::SetOptionInt("main", "dump_use_compression", $bUseCompression);

		if ($bFull)
		{
			$NS['total_steps'] = 4; // dump, tar dump, tar files, integrity

			COption::SetOptionInt("main", "dump_max_exec_time", 20);
			COption::SetOptionInt("main", "dump_max_exec_time_sleep", 1);
			COption::SetOptionInt("main", "dump_archive_size_limit", 1024 * 1024 * 1024);
			COption::SetOptionInt("main", "dump_integrity_check", 1);
			COption::SetOptionInt("main", "dump_max_file_size", 0);

			COption::SetOptionInt("main", "dump_file_public", 1);
			COption::SetOptionInt("main", "dump_file_kernel", 1);
			COption::SetOptionInt("main", "dump_base", $DB->type == 'MYSQL' ? 1 : 0);
			COption::SetOptionInt("main", "dump_base_skip_stat", 0);
			COption::SetOptionInt("main", "dump_base_skip_search", 0);
			COption::SetOptionInt("main", "dump_base_skip_log", 0);
			COption::SetOptionInt("main", "skip_symlinks", 0);

			if ($arAllBucket)
			{
				$bDumpCloud = 1;
				$NS['total_steps']++;
				COption::SetOptionInt("main", "dump_do_clouds", 1);
				foreach($arAllBucket as $arBucket)
					COption::SetOptionInt('main', 'dump_cloud_'.$arBucket['ID'], 1);
			}

			COption::SetOptionInt("main", "skip_mask", 0);
		}
		else
		{
			COption::SetOptionInt("main", "skip_symlinks", $_REQUEST['skip_symlinks'] == 'Y');
			COption::SetOptionInt("main", "dump_max_exec_time", intval($_REQUEST['dump_max_exec_time']) < 5 ? 5 : $_REQUEST['dump_max_exec_time']);
			COption::SetOptionInt("main", "dump_max_exec_time_sleep", $_REQUEST['dump_max_exec_time_sleep']);
			$dump_archive_size_limit = intval($_REQUEST['dump_archive_size_limit']);
			if ($dump_archive_size_limit > 2047 || $dump_archive_size_limit <= 10)
				$dump_archive_size_limit = 1024;
			COption::SetOptionInt("main", "dump_archive_size_limit", $dump_archive_size_limit * 1024 * 1024);
			COption::SetOptionInt("main", "dump_max_file_size", $_REQUEST['max_file_size']);

			$NS['total_steps'] = 0;
			if ($r = $_REQUEST['dump_file_public'] == 'Y')
				$NS['total_steps'] = 1;
			COption::SetOptionInt("main", "dump_file_public", $r);

			if ($r = $_REQUEST['dump_file_kernel'] == 'Y')
				$NS['total_steps'] = 1;
			COption::SetOptionInt("main", "dump_file_kernel", $r);

			if ($r = $DB->type == 'MYSQL' ? ($_REQUEST['dump_base'] == 'Y') : 0)
				$NS['total_steps'] += 2;
			COption::SetOptionInt("main", "dump_base", $r);
			COption::SetOptionInt("main", "dump_base_skip_stat", $_REQUEST['dump_base_skip_stat'] == 'Y');
			COption::SetOptionInt("main", "dump_base_skip_search", $_REQUEST['dump_base_skip_search'] == 'Y');
			COption::SetOptionInt("main", "dump_base_skip_log", $_REQUEST['dump_base_skip_log'] == 'Y');

			if ($r = $_REQUEST['dump_integrity_check'] == 'Y')
				$NS['total_steps']++;
			COption::SetOptionInt("main", "dump_integrity_check", $r);

			$bDumpCloud = false;
			if ($arAllBucket)
			{
				foreach($arAllBucket as $arBucket)
				{
					if ($res = $_REQUEST['dump_cloud'][$arBucket['ID']] == 'Y')
						$bDumpCloud = true;
					COption::SetOptionInt('main', 'dump_cloud_'.$arBucket['ID'], $res);
				}
				if ($bDumpCloud)
					$NS['total_steps']++;
			}
			COption::SetOptionInt("main", "dump_do_clouds", $bDumpCloud);

			$skip_mask = $_REQUEST['skip_mask'] == 'Y';
			COption::SetOptionInt("main", "skip_mask", $skip_mask);

			$skip_mask_array = array();
			if ($skip_mask && is_array($_REQUEST['arMask']))
			{
				$arMask = array_unique($_REQUEST['arMask']);
				foreach($arMask as $mask)
					if (trim($mask))
					{
						$mask = rtrim(str_replace('\\','/',trim($mask)),'/');
						$skip_mask_array[] = $mask;
					}
				COption::SetOptionString("main", "skip_mask_array", serialize($skip_mask_array));
			}
		}

		$NS["step"] = 1;
		$NS['st_row'] = -1;

		if ($NS['BUCKET_ID']) // send to the [bitrix]cloud
			$NS['total_steps']++;

		if ($NS['BUCKET_ID'] == -1) // Bitrixcloud
		{
			$name = DOCUMENT_ROOT.BX_ROOT."/backup/".date('Ymd_His_').rand(11111111,99999999);
			$NS['arc_name'] = $name.'.enc'.($bUseCompression ? ".gz" : '');
			$NS['dump_name'] = $name.'.sql';
		}
		else
		{
			$prefix = '';
			if ($_REQUEST['dump_site_id'])
			{
				$NS['dump_site_id'] = $_REQUEST['dump_site_id'];
				$prefix .= '_'.$NS['dump_site_id'];
			}
			if ($bDumpCloud)
				$prefix .= '_cloud';

			$arc_name = CBackup::GetArcName($prefix);

			$NS['dump_name'] = $arc_name.".sql";
			$NS['arc_name'] = $arc_name.($NS['dump_encrypt_key'] ? ".enc" : ".tar").($bUseCompression ? ".gz" : '');
		}
	}
	elseif($_REQUEST['action'] == 'cloud_send')
	{
		define('NO_TIME', true);
		$NS = Array();
		$NS['finished_steps'] = 0;
		$NS['total_steps'] = 1;
		$NS['cloud_send'] = 1;
//		$NS['dump_encrypt_key'] = '111111';
		$NS['arc_name'] = $name = DOCUMENT_ROOT.BX_ROOT.'/backup/'.str_replace(array('..','/','\\'),'',$_REQUEST['f_id']);
		$NS['arc_size'] = filesize($NS['arc_name']);
		$NS['BUCKET_ID'] = intval($_REQUEST['dump_bucket_id']);
		while(file_exists($name = CTar::getNextName($name)))
			$NS['arc_size'] += filesize($name);
		$NS['step'] = 6;
	}
	elseif($_REQUEST['action'] == 'check_archive')
	{
		define('NO_TIME', true);
		$NS = Array();
		$NS['finished_steps'] = 0;
		$NS['total_steps'] = 1;
		$NS['arc_name'] = $name = DOCUMENT_ROOT.BX_ROOT.'/backup/'.str_replace(array('..','/','\\'),'',$_REQUEST['f_id']);
		$NS['step'] = 5;
		$NS['dump_encrypt_key'] = '111111';
		$NS['check_archive'] = true;
		$tar = new CTar;
		$NS['data_size'] = $tar->getDataSize($name);
	}
	else
	{
		$ar = unserialize(COption::GetOptionString("main","skip_mask_array"));
		$skip_mask_array = is_array($ar) ? $ar : array();
	}

	$after_file = str_replace('.sql','_after_connect.sql',$NS['dump_name']);

	// Step 1: Dump
	if($NS["step"] == 1)
	{
		$step_done = 0;
		if (IntOption('dump_base'))
		{
			$bres = CBackup::BaseDump($NS["dump_name"], intval($NS["num"]), intval($NS["st_row"]), $TotalTables);
			$NS["ptab"] = $bres["ptab"];
			$NS["num"] = $bres["num"];
			$NS["st_row"] = $bres["st_row"];

			$status_title = GetMessage('DUMP_DB_CREATE');
			$status_details = GetMessage("MAIN_DUMP_TABLE_FINISH")." <b>".(intval($NS["num"]))."</b> ".GetMessage('MAIN_DUMP_FROM').' <b>'.$TotalTables.'</b>';
			$step_done = $NS['num'] / $TotalTables;

			if ($bres['end'])
			{

				$rs = $DB->Query('SHOW VARIABLES LIKE "character_set_results"');
				if (($f = $rs->Fetch()) && array_key_exists ('Value', $f))
					file_put_contents($after_file, "SET NAMES '".$f['Value']."';\n");

				$rs = $DB->Query('SHOW VARIABLES LIKE "collation_database"');
				if (($f = $rs->Fetch()) && array_key_exists ('Value', $f))
					file_put_contents($after_file, "ALTER DATABASE `<DATABASE>` COLLATE ".$f['Value'].";\n",8);

				$NS["step"]++;
				$NS['finished_steps']++;
			}
		}
		else
			$NS["step"]++;
	}

	// Step 2: pack dump 
	if($NS["step"] == 2)
	{
		$step_done = 0;
		if (IntOption('dump_base'))
		{
			if (haveTime())
			{
				$tar = new CTar;
				$tar->EncryptKey = $NS['dump_encrypt_key'];
				$tar->ArchiveSizeLimit = IntOption('dump_archive_size_limit');
				$tar->gzip = IntOption('dump_use_compression');
				$tar->path = DOCUMENT_ROOT;
				$tar->ReadBlockCurrent = intval($NS['ReadBlockCurrent']);

				if (!$tar->openWrite($NS["arc_name"]))
					RaiseErrorAndDie(GetMessage('DUMP_NO_PERMS'));

				if (!$tar->ReadBlockCurrent && file_exists($f = DOCUMENT_ROOT.BX_ROOT.'/.config.php'))
					$tar->addFile($f);

				$Block = $tar->Block;
				while(haveTime() && ($r = $tar->addFile($NS['dump_name'])) && $tar->ReadBlockCurrent > 0);
				$NS["data_size"] += 512 * ($tar->Block - $Block);
				
				if ($r === false)
					RaiseErrorAndDie(implode('<br>',$tar->err));

				$NS["ReadBlockCurrent"] = $tar->ReadBlockCurrent;
				
				$status_title = GetMessage("MAIN_DUMP_DB_PROC");
				$status_details = GetMessage('CURRENT_POS').' <b>'.round(100 * $NS['data_size'] / filesize($NS['dump_name'])).'%</b>';
				$step_done = $NS['data_size'] / filesize($NS['dump_name']);

				if($tar->ReadBlockCurrent == 0)
				{
					$tar->addFile($after_file);
					unlink($NS["dump_name"]) && (!file_exists($after_file) || unlink($after_file));

					$NS['arc_size'] = 0;
					$name = $NS["arc_name"];
					while(file_exists($name))
					{
						$size = filesize($name);
						$NS['arc_size'] += $size;
						if (IntOption("disk_space") > 0)
							CDiskQuota::updateDiskQuota("file", $size, "add");
						$name = CTar::getNextName($name);
					}

					$NS["step"]++;
					$NS['finished_steps']++;
				}
				$tar->close();
			}
		}
		else
			$NS["step"]++;
	}

	// Step 3: Download Cloud Files 
	$arDumpClouds = false;
	if($NS["step"] == 3)
	{
		$step_done = 0;
		if ($arDumpClouds = CBackup::CheckDumpClouds())
		{
			if (haveTime())
			{
				foreach($arDumpClouds as $arBucket)
				{
					$id = $arBucket['ID'];
					if ($NS['bucket_finished_'.$id])
						continue;

					$obCloud = new CloudDownload($id);
					$obCloud->last_bucket_path = $NS['last_bucket_path'];
					if ($res = $obCloud->Scan(''))
						$NS['bucket_finished_'.$id] = true;
					else // partial
					{
						$NS['last_bucket_path'] = $obCloud->path;
						$NS['download_cnt'] += $obCloud->download_cnt;
						$NS['download_size'] += $obCloud->download_size;
						if ($c = count($NS->arSkipped))
							$NS['download_skipped'] += $c;
						break;
					}
				}

				$status_title = GetMessage("MAIN_DUMP_CLOUDS_DOWNLOAD");
				$status_details = GetMessage("MAIN_DUMP_FILES_DOWNLOADED").': <b>'.intval($NS["download_cnt"]).'</b>';
//				if ($NS['download_skipped'])
//					$status_title .= GetMessage("MAIN_DUMP_DOWN_ERR_CNT").': <b>'.$NS['download_skipped'].'</b><br>';

				if ($res) // finish
				{
					$NS['step']++;
					$NS['finished_steps']++;
				}
			}
		}
		else
			$NS["step"]++;
	}
	
	// Step 4: Tar Files
	if($NS["step"] == 4)
	{
		$step_done = 0;
		if (CBackup::CheckDumpFiles() || CBackup::CheckDumpClouds())
		{
			if (haveTime())
			{
				$DOCUMENT_ROOT_SITE = DOCUMENT_ROOT;
				if ($NS['dump_site_id'])
				{
					$rs = CSite::GetList($by='sort', $order='asc', array('ID' => $NS['dump_site_id']));
					if ($f = $rs->Fetch())
					{
						$DOCUMENT_ROOT_SITE = rtrim(str_replace('\\','/',$f['ABS_DOC_ROOT']),'/');
					//	$DIR = rtrim(str_replace('\\','/',$f['DIR']),'/');
					}
				}
				if (!defined('DOCUMENT_ROOT_SITE'))
					define('DOCUMENT_ROOT_SITE', $DOCUMENT_ROOT_SITE);

				$tar = new CTar;
				$tar->EncryptKey = $NS['dump_encrypt_key'];
				$tar->ArchiveSizeLimit = IntOption('dump_archive_size_limit');
				$tar->gzip = IntOption('dump_use_compression');
				$tar->path = DOCUMENT_ROOT_SITE;
				$tar->ReadBlockCurrent = intval($NS['ReadBlockCurrent']);

				if (!$tar->openWrite($NS["arc_name"]))
					RaiseErrorAndDie(GetMessage('DUMP_NO_PERMS'));

				$Block = $tar->Block;
				$DirScan = new CDirRealScan;

				if (!$NS['startPath'])
				{
					if (!IntOption('dump_base') && file_exists($f = DOCUMENT_ROOT.BX_ROOT.'/.config.php'))
						$tar->addFile($f);
				}
				else
					$DirScan->startPath = $NS['startPath'];

				$r = $DirScan->Scan(DOCUMENT_ROOT_SITE);//.$DIR);
				$NS["data_size"] += 512 * ($tar->Block - $Block);
				$tar->close();

				if ($r === false)
					RaiseErrorAndDie(implode('<br>',array_merge($tar->err,$DirScan->err)));

				$NS["ReadBlockCurrent"] = $tar->ReadBlockCurrent;
				$NS["startPath"] = $DirScan->nextPath;
				$NS["cnt"] += $DirScan->FileCount;

				$status_title = GetMessage("MAIN_DUMP_SITE_PROC");
				$status_details = GetMessage("MAIN_DUMP_FILE_CNT")." <b>".intval($NS["cnt"])."</b>";
				$last_files_count = IntOption('last_files_count');
				if (!$last_files_count)
					$last_files_count = 100000;
				$step_done = $NS['cnt'] / $last_files_count;
				if ($step_done > 1)
					$step_done = 1;

//				if (is_object($DirScan))
//					$status_title .= '<br>'.GetMessage('DUMP_CUR_PATH').' <b>'.substr($DirScan->nextPath,strlen(DOCUMENT_ROOT_SITE)).'</b>';
				if ($r !== 'BREAK') // finish
				{
					$NS['arc_size'] = 0;
					$name = $NS["arc_name"];
					while(file_exists($name))
					{
						$size = filesize($name);
						$NS['arc_size'] += $size;
						if (IntOption("disk_space") > 0)
							CDiskQuota::updateDiskQuota("file", $size, "add");
						$name = CTar::getNextName($name);
					}
					DeleteDirFilesEx(BX_ROOT.'/backup/clouds');
					$NS["step"]++;
					$NS['finished_steps']++;
				}
			}
		}
		else
			$NS["step"]++;
	}

	// Step 5: Integrity check
	if($NS["step"] == 5)
	{
		$step_done = 0;
		if (IntOption('dump_integrity_check') || $NS['check_archive'])
		{
			if (haveTime())
			{
				$tar = new CTarCheck;
				$tar->EncryptKey = $NS['dump_encrypt_key'];

				if (!$tar->openRead($NS["arc_name"]))
					RaiseErrorAndDie(GetMessage('DUMP_NO_PERMS_READ').'<br>'.implode('<br>',$tar->err));
				else
				{
					if(($Block = intval($NS['Block'])) && !$tar->SkipTo($Block))
						RaiseErrorAndDie(implode('<br>',$tar->err));
					while(($r = $tar->extractFile()) && haveTime());

					$NS["Block"] = $tar->Block;
					$status_title = GetMessage('INTEGRITY_CHECK');
					$status_details = GetMessage('CURRENT_POS').' <b>'.CFile::FormatSize($NS['Block'] * 512).'</b> '.GetMessage('MAIN_DUMP_FROM').' <b>'.CFile::FormatSize($NS['data_size']).'</b>';
					$step_done = $NS['Block'] * 512 / $NS['data_size'];

					if ($r === false)
						RaiseErrorAndDie(implode('<br>',$tar->err));
					if ($r === 0)
					{
						$NS["step"]++;
						$NS['finished_steps']++;
					}
				}
				$tar->close();
			}
		}
		else
			$NS["step"]++;
	}

	// Step 6: Send to the cloud 
	if($NS["step"] == 6)
	{
		$step_done = 0;
		if ($NS['BUCKET_ID'])
		{
			if (haveTime())
			{
				if (!CModule::IncludeModule('clouds'))
					RaiseErrorAndDie(GetMessage("MAIN_DUMP_NO_CLOUDS_MODULE"));

				$file_size = filesize($NS["arc_name"]);
				$file_name = $NS['BUCKET_ID'] == -1 ? basename($NS['arc_name']) : substr($NS['arc_name'],strlen(DOCUMENT_ROOT));
				$obUpload = new CCloudStorageUpload($file_name);

				if ($NS['BUCKET_ID'] == -1)
				{
					if (!$bBitrixCloud)
						RaiseErrorAndDie(getMessage('DUMP_BXCLOUD_NA'));


					if (!$NS['obBucket'])
					{
						try {
							$backup = CBitrixCloudBackup::getInstance();
							$q = $backup->getQuota();
							if ($NS['arc_size'] > $q)
								RaiseErrorAndDie(GetMessage('DUMP_ERR_BIG_BACKUP', array('#ARC_SIZE#' => $NS['arc_size'], '#QUOTA#' => $q)));

							$obBucket = $backup->getBucketToWriteFile(CTar::getCheckword($NS['dump_encrypt_key']), basename($NS['arc_name']));
							$NS['obBucket'] = serialize($obBucket);
						}
						catch (Exception $e) {
							unset($NS['obBucket']);
							RaiseErrorAndDie($e->getMessage(),true);
						}
					}
					else
						$obBucket = unserialize($NS['obBucket']);

					$obBucket->Init();
					$obBucket->GetService()->setPublic(false);

					$bucket_id = $obBucket;
				}
				else
				{
					$obBucket = null;
					$bucket_id = $NS['BUCKET_ID'];
				}

				if (!$obUpload->isStarted())
				{
					if (is_object($obBucket))
						$obBucket->setCheckWordHeader();

					if (!$obUpload->Start($bucket_id, $file_size))
					{
						if ($e = $APPLICATION->GetException())
							$strError = $e->GetString();
						else
							$strError = GetMessage('MAIN_DUMP_INT_CLOUD_ERR');
						unset($NS['obBucket']);
						RaiseErrorAndDie($strError,true);
					}

					if (is_object($obBucket))
						$obBucket->unsetCheckWordHeader();
				}

				if ($fp = fopen($NS['arc_name'],'rb'))
				{
					fseek($fp, $obUpload->getPos());
					$part = fread($fp, $obUpload->getPartSize());
					fclose($fp);
					$fails = 0;
					while($obUpload->hasRetries())
					{
						if($res = $obUpload->Next($part, $obBucket))
							break;
						elseif (++$fails >= 10)
							RaiseErrorAndDie('Internal Error: could not init upload for '.$fails.' times');
					}

					if ($res)
					{
						$pos = $obUpload->getPos();
						if ($pos >= $file_size) // file ended
						{
							if($obUpload->Finish($obBucket))
							{
								$NS['pos'] += $file_size;
								$pos = 0;

								if ($NS['BUCKET_ID'] != -1)
								{
									$oBucket = new CCloudStorageBucket($NS['BUCKET_ID']);
									$oBucket->IncFileCounter($file_size);
								}

								if (file_exists($arc_name = CTar::getNextName($NS['arc_name'])))
								{
									unset($NS['obBucket']);
									$NS['arc_name'] = $arc_name;
								}
								else
								{
									CBitrixCloudBackup::clearOptions();
									$name = CTar::getFirstName($NS['arc_name']);
									while(file_exists($name))
									{
										$size = filesize($name);
										if (unlink($name) && IntOption("disk_space") > 0)
											CDiskQuota::updateDiskQuota("file",$size , "del");
										$name = CTar::getNextName($name);
									}

									$NS["step"]++;
									$NS['finished_steps']++;
								}
							}
							else
							{
								$obUpload->Delete();
								unset($NS['obBucket']);
								RaiseErrorAndDie(GetMessage("MAIN_DUMP_ERR_FILE_SEND").basename($NS['arc_name']),true);
							}
						}

						$pos += $NS['pos'];

						$status_title = GetMessage("MAIN_DUMP_FILE_SENDING");
						$status_details = GetMessage('CURRENT_POS').' <b>'.CFile::FormatSize($pos).'</b>  '.GetMessage('MAIN_DUMP_FROM').' <b>'.CFile::FormatSize($NS["arc_size"])."</b>";
						$step_done = $pos / $NS['arc_size'];
					}
					else
					{
						$obUpload->Delete();
						unset($NS['obBucket']);
						RaiseErrorAndDie(GetMessage("MAIN_DUMP_ERR_FILE_SEND").basename($NS['arc_name']),true);
					}
				}
				else
				{
					unset($NS['obBucket']);
					RaiseErrorAndDie(GetMessage("MAIN_DUMP_ERR_OPEN_FILE").$NS['arc_name'],true);
				}
			}
		}
		else
			$NS["step"]++;
	}


	$NS["time"] += workTime();

	if ($NS["step"] <= 6) // partial
	{
		CAdminMessage::ShowMessage(array(
			"TYPE" => "PROGRESS",
			"MESSAGE" => $status_title,
			"DETAILS" => $status_details.'#PROGRESS_BAR#'.
//				GetMessage('TIME_SPENT').' '.HumanTime($NS["time"]),
				GetMessage('TIME_SPENT').' <span id="counter_field">'.sprintf('%02d',floor($NS["time"]/60)).':'.sprintf('%02d', $NS['time']%60).'</span><!--'.intval($NS['time']).'-->',
			"HTML" => true,
			"PROGRESS_TOTAL" => 100,
			"PROGRESS_VALUE" => ($NS['finished_steps'] + $step_done) * 100 / $NS['total_steps'],
		));
		?>
		<script>
			window.setTimeout("if(!stop)AjaxSend('?process=Y&<?=bitrix_sessid_get()?>')",<?=1000 * IntOption("dump_max_exec_time_sleep")?>);
		</script>
		<?
	}
	else // Finish
	{
		$title = ($NS['cloud_send'] ? GetMessage("MAIN_DUMP_SUCCESS_SENT") : GetMessage("MAIN_DUMP_FILE_FINISH")).'<br><br>';
		$status_msg = '';
		
		if ($NS["arc_size"])
		{
			$status_msg .= GetMessage("MAIN_DUMP_ARC_NAME").": <b>".basename(CTar::getFirstName($NS["arc_name"]))."</b><br>";
			$status_msg .= GetMessage("MAIN_DUMP_ARC_SIZE")." <b>".CFile::FormatSize($NS["arc_size"])."</b><br>";
			if ($NS['BUCKET_ID'] > 0)
				$l = ''; //htmlspecialcharsbx($arBucket['BUCKET'].' ('.$arBucket['SERVICE_ID'].')');
			elseif ($NS['BUCKET_ID'] == -1)
				$l = GetMessage('DUMP_MAIN_BITRIX_CLOUD');
			else
				$l = GetMessage("MAIN_DUMP_LOCAL");

			if ($l)
				$status_msg .= GetMessage("MAIN_DUMP_LOCATION").": <b>".$l."</b><br>";
		}

		if ($NS["num"])
			$status_msg .= GetMessage("MAIN_DUMP_TABLE_FINISH")." <b>".$NS["num"]."</b><br>";
		if ($NS["cnt"])
		{
			$status_msg .= GetMessage("MAIN_DUMP_FILE_CNT")." <b>".$NS["cnt"]."</b><br>";
			if (IntOption("dump_file_public") && IntOption("dump_file_kernel"))
				COption::SetOptionInt("main", "last_files_count", $NS['cnt']);
		}

		if ($NS["data_size"])
			$status_msg .= GetMessage("MAIN_DUMP_FILE_SIZE")." <b>".CFile::FormatSize($NS["data_size"])."</b><br>";

		$status_msg .= GetMessage('TIME_SPENT').' <b>'.HumanTime($NS["time"]).'</b>';

		CAdminMessage::ShowMessage(array(
			"MESSAGE" => $title,
			"DETAILS" => $status_msg,
			"TYPE" => "OK",
			"HTML" => true));

?>
		<?echo bitrix_sessid_post()?>
		<script>
			EndDump();
		</script>
<?
	}
	require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin_js.php");
	die();
}
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/prolog.php");

// in case of error
$DB->Query("UNLOCK TABLES",true);

$APPLICATION->SetTitle(GetMessage("MAIN_DUMP_PAGE_TITLE"));
require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_admin_after.php");

$aTabs = array();
$aTabs[] = array("DIV"=>"std", "TAB"=>GetMessage("DUMP_MAIN_MAKE_ARC"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("MAKE_DUMP_FULL"));
$aTabs[] = array("DIV"=>"expert", "TAB"=>GetMessage("DUMP_MAIN_PARAMETERS"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("DUMP_MAIN_EXPERT_SETTINGS"));

$editTab = new CAdminTabControl("editTab", $aTabs, true, true);

if ($DB->type != 'MYSQL')
	echo BeginNote().GetMessage('MAIN_DUMP_MYSQL_ONLY').EndNote();
if (!$bMcrypt)
{
	CAdminMessage::ShowMessage(array(
		"MESSAGE" => GetMessage("MAIN_DUMP_NOT_INSTALLED"),
		"DETAILS" => GetMessage("MAIN_DUMP_NO_ENC_FUNCTIONS"),
		"TYPE" => "ERROR",
		"HTML" => true));
}

if (defined('DUMP_DEBUG_MODE'))
	echo '<div style="color:red">DEBUG MODE</div><input type=button value=Next onclick="AjaxSend(\'?process=Y&'.bitrix_sessid_get().'\')">';

?><div id="dump_result_div"></div><?
$aMenu = array(
	array(
		"TEXT"	=> GetMessage("MAIN_DUMP_LIST_PAGE_TITLE"),
		"LINK"	=> "/bitrix/admin/dump_list.php?lang=".LANGUAGE_ID,
		"TITLE"	=> GetMessage("MAIN_DUMP_LIST_PAGE_TITLE"),
		"ICON"	=> "btn_list"
	)
);
$context = new CAdminContextMenu($aMenu);
$context->Show();

CAdminFileDialog::ShowScript(
	Array
	(
		"event" => "__bx_select_dir",
		"arResultDest" => Array("FUNCTION_NAME" => "mnu_SelectValue"),
		"arPath" => Array('PATH'=>"/"),
		"select" => 'D',
		"operation" => 'O',
		"showUploadTab" => false,
		"showAddToMenuTab" => false,
		"allowAllFiles" => true,
		"SaveConfig" => true 
	)
);		
?>
<script language="JavaScript">
var counter_started = false;
var counter_sec = 0;

function StartCounter()
{
	counter_started = true;
}

function StopCounter(result)
{
	counter_started = false;
	if (result)
	{
		if (regs = /<!--([0-9]+)-->/.exec(result))
			counter_sec = regs[1];
	}
}

function IncCounter()
{
	window.setTimeout(IncCounter, 1000);
	if (!counter_started)
		return;

	counter_sec ++;
	if (ob = BX('counter_field'))
	{
		min = Math.floor(counter_sec / 60);
		sec = counter_sec % 60;
		if (min < 10)
			min = '0' + min;
		if (sec < 10)
			sec = '0' + sec;
		ob.innerHTML = min + ':' + sec;
	}
}
window.setTimeout(IncCounter, 1000);

function GetLicenseInfo()
{
	CHttpRequest.Action = function(result)
	{
		BX('license_info').innerHTML = result;
	}
	CHttpRequest.Send('?action=key_info&lang=<?=LANGUAGE_ID?>&<?=bitrix_sessid_get()?>');
}

var numRows=0;
function AddTableRow()
{
	oTable = BX('skip_mask_table');
	numRows = oTable.rows.length;
	oRow = oTable.insertRow(-1);
	oCell = oRow.insertCell(0);
	oCell.innerHTML = '<input type="text" name="arMask[]" id="mnu_FILES_' + numRows  +'" size=30><input type="button" id="mnu_FILES_btn_' + numRows  + '" value="..." onclick="showMenu(this, '+ numRows  +')">';
}

var currentID;

function showMenu(div, id)
{
	currentID = id;
	__bx_select_dir();
}

function mnu_SelectValue(filename, path, site, title, menu)
{
	BX('mnu_FILES_' + currentID).value = path + (path == '/' ? '' : '/') + filename;
}

function CheckExpert()
{
	ob = document.fd1.dump_expert;

	table = BX('tr_dump_expert').parentNode.parentNode;
	found = false;
	for(i=0;i<table.rows.length;i++)
	{
		if (found)
			table.rows[i].style.display = ob.checked ? '' : 'none';
		if (table.rows[i].id == 'tr_dump_expert')
			found = true;
	}
	CheckActiveStart();
}

function CheckActiveStart()
{
	if (counter_started)
		return;

	start = true;
	if (document.fd1.dump_expert.checked)
	{
		start = document.fd1.dump_file_public.checked || document.fd1.dump_file_kernel.checked;

		document.fd1.max_file_size.disabled = !start;
		document.fd1.skip_symlinks.disabled = !start;
		document.fd1.skip_mask.disabled = !start;

		mask = start && document.fd1.skip_mask.checked;
		BX('more_button').disabled = !mask;

		oTable = BX('skip_mask_table');
		numRows = oTable.rows.length;
		for(i=0;i<numRows;i++)
		{
			BX('mnu_FILES_'+i).disabled = !mask;
			BX('mnu_FILES_btn_'+i).disabled = !mask;
		}

		<?
		if ($arAllBucket)
		{
			foreach($arAllBucket as $arBucket)
				echo 'start = start || BX("dump_cloud_'.$arBucket['ID'].'").checked;'."\n";
		}
		?>

		if (ob = document.fd1.dump_base)
		{
			document.fd1.dump_base_skip_stat.disabled = !ob.checked;
			document.fd1.dump_base_skip_search.disabled = !ob.checked;
			document.fd1.dump_base_skip_log.disabled = !ob.checked;

			start = start || ob.checked;
		}
	}

	BX('start_button').disabled = !start;
}

function CheckEncrypt(ob)
{
	if(enc = document.fd1.dump_encrypt)
	{
		enc.disabled = (ob.value == -1);
	}
}

BX.ready(
	function()
	{
		CheckExpert();
		<?
			if ($_REQUEST['from'] == 'bitrixcloud')
				echo 'StartDump();';
			elseif ($_REQUEST['action'] == 'cloud_send' && check_bitrix_sessid())
				echo "AjaxSend('?process=Y&action=cloud_send&f_id=".CUtil::JSEscape($_REQUEST['f_id'])."&dump_bucket_id=".CUtil::JSEscape($_REQUEST['dump_bucket_id'])."&".bitrix_sessid_get()."');";
		?>
	}
);

var stop;
var dump_encrypt_key;
var PasswordDialog;

function SavePassword()
{
	key = BX('dump_encrypt_key').value;
	l = key.length;

	strError = '';
	if (!l)
		strError = "<?=GetMessage("MAIN_DUMP_EMPTY_PASS")?>";
	else if (!/^[\040-\176]*$/.test(key))
		strError = "<?=GetMessage('DUMP_ERR_NON_ASCII')?>";
	else if (l < 6)
		strError = "<?=GetMessage("MAIN_DUMP_ENC_PASS_DESC")?>";
	else if (key != BX('dump_encrypt_key_confirm').value)
		strError = "<?=GetMessage("DUMP_MAIN_ERR_PASS_CONFIRM")?>";

	if (strError)
	{
		BX('password_error').innerHTML = strError;
		BX('dump_encrypt_key').focus();
	}
	else
	{
		BX('password_error').innerHTML = '';
		dump_encrypt_key = key;
		BX.WindowManager.Get().Close();
		DoDump();
	}
}

function StartDump()
{
	if (BX('bitrixcloud').checked || (document.fd1.dump_expert.checked && (ob = document.fd1.dump_encrypt) && ob.checked))
	{
		if (!PasswordDialog)
		{
			PasswordDialog = new BX.CDialog({
				title: '<?=GetMessage("DUMP_MAIN_ENC_ARC")?>',
				content: '<? 
					echo '<div style="color:red" id=password_error></div>';
					echo CUtil::JSEscape(BeginNote().GetMessage('MAIN_DUMP_SAVE_PASS').EndNote());
					echo '<table>';
					echo '<tr><td>'.GetMessage('MAIN_DUMP_ENC_PASS').'</td><td><input type="password" value="" id="dump_encrypt_key" onkeyup="if(event.keyCode==13) {BX(&quot;dump_encrypt_key_confirm&quot;).focus()}"/></td></tr>';
					echo '<tr><td>'.GetMessage('DUMP_MAIN_PASSWORD_CONFIRM').'</td><td><input type="password" value="" id="dump_encrypt_key_confirm"  onkeyup="if(event.keyCode==13) {SavePassword()}"/></td></tr>';
					echo '</table>';
				?>',
				height: 300,
				width: 600,
				resizable: false,
				buttons: [ {
					title: '<?=GetMessage("MAIN_DUMP_FILE_DUMP_BUTTON")?>',
	//				id: 'my_save',
	//				name: 'my_save',
					className: 'adm-btn-save',
					action: SavePassword

				}, BX.CAdminDialog.btnCancel ]
			})
		}
		PasswordDialog.Show()
		BX('dump_encrypt_key').focus();
	}
	else
	{
		dump_encrypt_key = '';
		DoDump();
	}
}

function DoDump()
{
	counter_sec = 0;
	queryString='lang=<?echo htmlspecialcharsbx(LANGUAGE_ID)?>&process=Y&action=start';

	ob = document.fd1.dump_bucket_id;
	for (i = 0; i<ob.length; i++)
		if (ob[i].checked)
			queryString += '&dump_bucket_id=' + ob[i].value;

	if (ob = document.fd1.dump_site_id)
		queryString += '&dump_site_id=' + ob.value;

	if (dump_encrypt_key)
		queryString += '&dump_encrypt_key=' + encodeURIComponent(dump_encrypt_key);

	if (document.fd1.dump_expert.checked)
	{
		queryString+='&dump_max_exec_time=' + encodeURIComponent(document.fd1.dump_max_exec_time.value);
		queryString+='&dump_max_exec_time_sleep=' + encodeURIComponent(document.fd1.dump_max_exec_time_sleep.value);
		queryString+='&dump_archive_size_limit=' + encodeURIComponent(document.fd1.dump_archive_size_limit.value);

		if (document.fd1.dump_disable_gzip.checked)
			queryString += '&dump_disable_gzip=Y';

		if (document.fd1.dump_integrity_check.checked)
			queryString += '&dump_integrity_check=Y';
		
		if(document.fd1.dump_file_public.checked)
			queryString +='&dump_file_public=Y';

		if(document.fd1.dump_file_kernel.checked)
			queryString+='&dump_file_kernel=Y';

		if(document.fd1.skip_symlinks.checked)
			queryString+='&skip_symlinks=Y';

		if(document.fd1.skip_mask.checked)
		{
			queryString+='&skip_mask=Y';

			oTable = BX('skip_mask_table');
			numRows = oTable.rows.length;

			for(i=0;i<numRows;i++)
				queryString+='&arMask[]=' + BX('mnu_FILES_'+i).value;
		}

		if(document.fd1.dump_file_public.checked || document.fd1.dump_file_kernel.checked)
			queryString+='&max_file_size=' + document.fd1.max_file_size.value;

		if((ob = document.fd1.dump_base) && ob.checked)
		{
			queryString +='&dump_base=Y';

			if(document.fd1.dump_base_skip_stat.checked)
				queryString +='&dump_base_skip_stat=Y';
			if(document.fd1.dump_base_skip_search.checked)
				queryString +='&dump_base_skip_search=Y';
			if(document.fd1.dump_base_skip_log.checked)
				queryString +='&dump_base_skip_log=Y';
		}

		<?
		if ($arAllBucket)
		{
			foreach($arAllBucket as $arBucket)
			{
				?>
					if (BX('dump_cloud_<?=$arBucket['ID']?>').checked)
						queryString += '&dump_cloud[<?=$arBucket['ID']?>]=Y';
				<?
			}
		}
		?>
	}
	else
		queryString += '&dump_all=Y';

	queryString += '&<?=bitrix_sessid_get()?>';

	BX('dump_result_div').innerHTML='';
	AjaxSend('dump.php', queryString);
	window.scrollTo(0, 0);
}

function EndDump(conditional)
{
	BX('stop_button').disabled = stop = true;

	if (!conditional || !counter_started)
		BX('start_button').disabled = false;
}

function AjaxSend(url, data)
{
	stop = false;
	BX('stop_button').disabled=false;
	BX('start_button').disabled=true;

	StartCounter();
	CHttpRequest.Action = function(result)
	{
		StopCounter(result);
		if (stop)
		{
			EndDump();
			BX('dump_result_div').innerHTML = '';
		}
		else 
			BX('dump_result_div').innerHTML = result;
	}
	if (data)
		CHttpRequest.Post(url, data);
	else
		CHttpRequest.Send(url);
}
</script>


	<form name="fd1" action="<?echo $APPLICATION->GetCurPage()?>?lang=<?=LANGUAGE_ID?>" method="GET">
	<?
	$editTab->Begin();
	$editTab->BeginNextTab();

	if ($bBitrixCloud)
	{
	?>
	<tr>
		<td class="adm-detail-valign-top" width="40%"><?=GetMessage('DUMP_MAIN_BITRIX_CLOUD_DESC')?><span class="required"><sup>1</sup></span>:</td>
		<td width="60%">
		<?
			$backup = CBitrixCloudBackup::getInstance();
			CAdminMessage::ShowMessage(array(
				"TYPE" => "PROGRESS",
				"DETAILS" => GetMessage("BCL_BACKUP_USAGE", array(
					"#QUOTA#" => CFile::FormatSize($quota = $backup->getQuota()),
					"#USAGE#" => CFile::FormatSize($usage = $backup->getUsage()),
				)).'#PROGRESS_BAR#',
				"HTML" => false,
				"PROGRESS_TOTAL" => $quota,
				"PROGRESS_VALUE" => $usage,
			));
		?>
		</td>
	</tr>
	<?
	}
	?>
	<tr>
		<td class="adm-detail-valign-top" width=40%><?=GetMessage('MAIN_DUMP_ARC_LOCATION')?></td>
		<td>
			<div><input type=radio name=dump_bucket_id value="-1" <?=$bBitrixCloud ? "checked" : ""?> id="bitrixcloud" <?=$bBitrixCloud ? '' : 'disabled'?> onclick="CheckEncrypt(this)"> <label for="bitrixcloud"><?=GetMessage('DUMP_MAIN_IN_THE_BXCLOUD')?></label><?=$strBXError ? ' <span style="color:red">('.$strBXError.')</span>' : ''?></div>
			<div><input type=radio name=dump_bucket_id value="0"  <?=!$bBitrixCloud ? "checked" : ""?> id="dump_bucket_id_0" onclick="CheckEncrypt(this)"> <label for="dump_bucket_id_0"><?=GetMessage('MAIN_DUMP_LOCAL_DISK')?></label></div>
			<? 
			$arWriteBucket = CBackup::GetBucketList($arFilter = array('READ_ONLY' => 'N'));
			if ($arWriteBucket) 
			{ 
				foreach($arWriteBucket as $f)
					echo '<div><input type=radio name=dump_bucket_id value="'.$f['ID'].'" id="dump_bucket_id_'.$f['ID'].'" onclick="CheckEncrypt(this)"> <label for="dump_bucket_id_'.$f['ID'].'">'.GetMessage('DUMP_MAIN_IN_THE_CLOUD').' '.htmlspecialcharsbx($f['BUCKET'].' ('.$f['SERVICE_ID'].')').'</label></div>';
			}
			?>
		</td>
	</tr>
<?
	$arSitePath = array();
	$res = CSite::GetList($by='sort', $order='asc', array('ACTIVE'=>'Y'));
	while($f = $res->Fetch())
	{
		$root = rtrim($f['ABS_DOC_ROOT'],'/');
		if (is_dir($root))
			$arSitePath[$root] = array($f['ID'] => '['.$f['ID'].'] '.$f['NAME']);
	}

	if (count($arSitePath) > 1)
	{
	?>
	<tr>
		<td><?=GetMessage("DUMP_MAIN_SITE")?><span class="required"><sup>2</sup></span></td>
		<td>
			<select name=dump_site_id>
			<?
				foreach($arSitePath as $path => $val)
				{
					$path = rtrim(str_replace('\\','/',$path),'/');
					list($k,$v) = each($val);
					echo '<option value="'.htmlspecialcharsbx($k).'"'.($path == DOCUMENT_ROOT ? ' selected' : '').'>'.htmlspecialcharsbx($v).'</option>';
				}
			?>
			</select>
		</td>
	</tr>
	<?
	}
	?>
	<?
	$editTab->BeginNextTab();
	?>
	<tr>
		<td width=40%><?=GetMessage("DUMP_MAIN_ENABLE_EXPERT")?>:</td>
		<td><input type="checkbox" name="dump_expert" onclick="CheckExpert()"></td>
	</tr>
	<tr id="tr_dump_expert">
		<td colspan=2><? 
		echo BeginNote();
		echo GetMessage("DUMP_MAIN_CHANGE_SETTINGS");
		echo EndNote();
		?></td>
	</tr>



	<tr class="heading">
		<td colspan="2"><?=GetMessage("DUMP_MAIN_ARC_CONTENTS")?></td>
	</tr>
<?
if ($arAllBucket)
{
?>
	<tr>
		<td class="adm-detail-valign-top"><?=GetMessage("DUMP_MAIN_DOWNLOAD_CLOUDS")?></td>
		<td>
			<?
			foreach($arAllBucket as $arBucket)
				echo '<div><input type="checkbox" id="dump_cloud_'.$arBucket['ID'].'" OnClick="CheckActiveStart()" '.(IntOption("dump_cloud_".$arBucket['ID']) ? "checked" : "").'> <label for="dump_cloud_'.$arBucket['ID'].'">'.htmlspecialcharsbx($arBucket['BUCKET'].' ('.$arBucket['SERVICE_ID'].')').'</label></div>';
			?>
		</td>
	</tr>
<?
}
?>
	<?
	if ($DB->type == 'MYSQL') 
	{
		?>
		<tr>
			<td><?=GetMessage("DUMP_MAIN_ARC_DATABASE")?> (<?=getTableSize("")?> <?=GetMessage("MAIN_DUMP_BASE_SIZE")?>):</td>
			<td><input type="checkbox" name="dump_base" OnClick="CheckActiveStart()" <?=IntOption("dump_base") ? "checked" : "" ?>></td>
		</tr>
		<tr>
			<td class="adm-detail-valign-top"><?=GetMessage("DUMP_MAIN_DB_EXCLUDE")?></td>
			<td>
				<div><input type="checkbox" name="dump_base_skip_stat" <?=IntOption("dump_base_skip_stat") ? "checked" : "" ?> id="dump_base_skip_stat"> <label for="dump_base_skip_stat"><? echo GetMessage("MAIN_DUMP_BASE_STAT")." (".getTableSize("b_stat")." ".GetMessage("MAIN_DUMP_BASE_SIZE").")" ?></label></div>
				<div><input type="checkbox" name="dump_base_skip_search" value="Y" <?=IntOption("dump_base_skip_search") ? "checked" : "" ?> id="dump_base_skip_search"> <label for="dump_base_skip_search"><? echo GetMessage("MAIN_DUMP_BASE_SINDEX")." (".getTableSize("b_search")." ".GetMessage("MAIN_DUMP_BASE_SIZE").")" ?></label></div>
				<div><input type="checkbox" name="dump_base_skip_log" value="Y"<?=IntOption("dump_base_skip_log") ? "checked" : "" ?> id="dump_base_skip_log"> <label for="dump_base_skip_log"><? echo GetMessage("MAIN_DUMP_EVENT_LOG")." (".getTableSize("b_event_log")." ".GetMessage("MAIN_DUMP_BASE_SIZE").")" ?></label></div>
			</td>
		</tr>
		<?
	}
	?>
	<tr>
		<td><?echo GetMessage("MAIN_DUMP_FILE_KERNEL")?></td>
		<td><input type="checkbox" name="dump_file_kernel" value="Y" OnClick="CheckActiveStart()" <?=IntOption("dump_file_kernel") ? "checked" : ''?>></td>
	</tr>
	<tr>
		<td><?echo GetMessage("MAIN_DUMP_FILE_PUBLIC")?></td>
		<td><input type="checkbox" name="dump_file_public" value="Y" OnClick="CheckActiveStart()" <?=IntOption("dump_file_public") ? "checked" : ''?>></td>
	</tr>
	<tr>
		<td class="adm-detail-valign-top"><?echo GetMessage("MAIN_DUMP_MASK")?><span class="required"><sup>3</sup></span></td>
		<td>
			<input type="checkbox" name="skip_mask" value="Y" <?=IntOption('skip_mask')?" checked":'';?> onclick="CheckActiveStart()">
			<table id="skip_mask_table" cellspacing=0 cellpadding=0>
			<?
			$i=-1;

			$res = unserialize(COption::GetOptionString("main","skip_mask_array"));
			$skip_mask_array = is_array($res)?$res:array();

			foreach($skip_mask_array as $mask)
			{
				$i++;
				echo
				'<tr><td>
					<input type="text" name="arMask[]" id="mnu_FILES_'.$i.'" value="'.htmlspecialcharsbx($mask).'" size=30>'.
					'<input type="button" id="mnu_FILES_btn_'.$i.'" value="..." onclick="showMenu(this, \''.$i.'\')">'.
				'</tr>';
			}
			$i++;
			?>
				<tr><td><input type="text" name="arMask[]" id="mnu_FILES_<?=$i?>" size=30><input type="button" id="mnu_FILES_btn_<?=$i?>" value="..." onclick="showMenu(this, '<?=$i?>')"></tr>
			</table>
			<input type=button id="more_button" value="<?=GetMessage('MAIN_DUMP_MORE')?>" onclick="AddTableRow()">
		</td>
	</tr>
	<tr>
		<td><?echo GetMessage("MAIN_DUMP_FILE_MAX_SIZE")?></td>
		<td><input type="text" name="max_file_size" size="10" value="<?=IntOption("dump_max_file_size")?>" <?=CBackup::CheckDumpFiles() ? '' : "disabled"?>>
		<?echo GetMessage("MAIN_DUMP_FILE_MAX_SIZE_kb")?></td>
	</tr>
	<tr>
		<td><?echo GetMessage("MAIN_DUMP_SKIP_SYMLINKS")?></td>
		<td><input type="checkbox" name="skip_symlinks" value="Y" <?=IntOption("skip_symlinks") ? "checked" : ''?>></td>
	</tr>




	<tr class="heading">
		<td colspan="2"><?=GetMessage("DUMP_MAIN_ARC_MODE")?></td>
	</tr>
	<tr>
		<td><?=GetMessage("MAIN_DUMP_ENABLE_ENC")?><span class="required"><sup>4</sup></td>
		<td><input type="checkbox" name="dump_encrypt" value="Y" <?=(IntOption("dump_encrypt") ? "checked" : "")?> <?=$bMcrypt && !$bBitrixCloud  ? '' : 'disabled'?>></td>
	</tr>
	<tr>
		<td width=40%><?=GetMessage('INTEGRITY_CHECK_OPTION')?></td>
		<td><input type="checkbox" name="dump_integrity_check" <?=IntOption('dump_integrity_check') ? 'checked' : '' ?>>
	</tr>
	<tr>
		<td><?=GetMessage('DISABLE_GZIP')?></td>
		<td><input type="checkbox" name="dump_disable_gzip" <?=IntOption('dump_use_compression') && $bGzip ? '' : 'checked' ?> <?=$bGzip ? '' : 'disabled'?>>
	</tr>
	<tr>
		<td width=40%><?=GetMessage('STEP_LIMIT')?></td>
		<td>
			<input type="text" name="dump_max_exec_time" value="<?=IntOption("dump_max_exec_time")?>" size=2>
			<?echo GetMessage("MAIN_DUMP_FILE_STEP_sec");?>,
			<?echo GetMessage("MAIN_DUMP_FILE_STEP_SLEEP")?>
			<input type="text" name="dump_max_exec_time_sleep" value="<?=IntOption("dump_max_exec_time_sleep")?>" size=2>
			<?echo GetMessage("MAIN_DUMP_FILE_STEP_sec");?>
		</td>
	</tr>

	<tr>
		<td><?=GetMessage("MAIN_DUMP_MAX_ARCHIVE_SIZE")?></td>
		<td><input type="text" name="dump_archive_size_limit" value="<?=intval(COption::GetOptionString('main', 'dump_archive_size_limit', 1024 * 1024 * 1024)) / 1024 / 1024?>" size=4></td>
	</tr>
	<?
	$editTab->Buttons();
	?>
	<input type="button" id="start_button" class="adm-btn-save" value="<?=GetMessage("MAIN_DUMP_FILE_DUMP_BUTTON")?>" OnClick="StartDump();">
	<input type="button" id="stop_button" value="<?=GetMessage("MAIN_DUMP_FILE_STOP_BUTTON")?>" OnClick="EndDump(true)" disabled>
	<?
	$editTab->End();
	?>
	</form>
	<br>

<?
echo BeginNote();
echo '<div><span class=required><sup>1</sup></span> '.GetMessage("DUMP_MAIN_BXCLOUD_INFO").'</div>';
echo '<div><span class=required><sup>2</sup></span> '.GetMessage("DUMP_MAIN_MULTISITE").'</div>';
echo '<div><span class=required><sup>3</sup></span> '.GetMessage("MAIN_DUMP_FOOTER_MASK").'</div>';
echo '<div><span class=required><sup>4</sup></span> '.GetMessage("MAIN_DUMP_BXCLOUD_ENC").'</div>';
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

function IntOption($name)
{
	static $CACHE;
	if (!$CACHE[$name])
		$CACHE[$name] = COption::GetOptionInt("main", $name, 0);
	return $CACHE[$name];
}

function getTableSize($prefix)
{
	global $DB;
	if ($DB->type != 'MYSQL')
		return 0;
	$size = 0;

	$sql = "SHOW TABLE STATUS LIKE '".$DB->ForSql($prefix)."%'";
	$res = $DB->Query($sql);

	while($row = $res->Fetch())
		$size += $row["Data_length"];

	return round($size/(1048576), 2);
}

function haveTime()
{
	if(defined('NO_TIME'))
		return microtime(true) - START_EXEC_TIME < 1;
	return microtime(true) - START_EXEC_TIME < IntOption("dump_max_exec_time");
}

function workTime()
{
	return microtime(true) - START_EXEC_TIME;
}


function RaiseErrorAndDie($strError, $bRepeat = false)
{
//	echo '<pre>';print_r(debug_print_backtrace());echo '</pre>';
	if ($bRepeat)
		$strError .= '<br><input type=button value="'.GetMessage('DUMP_RETRY').'" onclick="this.disabled=true;AjaxSend(\'?process=Y&'.bitrix_sessid_get().'\');">';

	CAdminMessage::ShowMessage(array(
		"MESSAGE" => GetMessage("MAIN_DUMP_ERROR"),
		"DETAILS" =>  $strError,
		"TYPE" => "ERROR",
		"HTML" => true));
	echo '<script>EndDump();</script>';
	die();
}
?>
