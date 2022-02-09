<?
define('NO_AGENT_CHECK', true);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

if (!($USER->CanDoOperation('catalog_read') || $USER->CanDoOperation('catalog_import_edit') || $USER->CanDoOperation('catalog_import_exec')))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$bCanEdit = $USER->CanDoOperation('catalog_import_edit');
$bCanExec = $USER->CanDoOperation('catalog_import_exec');

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/catalog/include.php");

if ($ex = $APPLICATION->GetException())
{
	require($DOCUMENT_ROOT."/bitrix/modules/main/include/prolog_admin_after.php");

	$strError = $ex->GetString();
	ShowError($strError);

	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
	die();
}

IncludeModuleLangFile(__FILE__);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/catalog/prolog.php");

set_time_limit(0);
$strErrorMessage = "";
$strOKMessage = "";

/////////////////////////////////////////////////////////////////////
function GetReportsList($strPath2Import)
{
	$arReports = array();

	CheckDirPath($_SERVER["DOCUMENT_ROOT"].$strPath2Import);
	if ($handle = opendir($_SERVER["DOCUMENT_ROOT"].$strPath2Import))
	{
		while (($file = readdir($handle)) !== false)
		{
			if ($file == "." || $file == "..")
				continue;

			if ($GLOBALS["DB"]->type != "MYSQL" && substr($file, 0, strlen("commerceml_g_")) == "commerceml_g_")
				continue;

			if (is_file($_SERVER["DOCUMENT_ROOT"].$strPath2Import.$file) && substr($file, strlen($file)-8)=="_run.php")
			{
				$import_name = substr($file, 0, strlen($file)-8);

				$rep_title = $import_name;
				$file_handle = fopen($_SERVER["DOCUMENT_ROOT"].$strPath2Import.$file, "rb");
				$file_contents = fread($file_handle, 1500);
				fclose($file_handle);

				$arMatches = array();
				if (preg_match("#<title[\s]*>([^<]*)</title[\s]*>#i", $file_contents, $arMatches))
				{
					$arMatches[1] = Trim($arMatches[1]);
					if (strlen($arMatches[1]) > 0)
						$rep_title = $arMatches[1];
				}

				$arReports[$import_name] = array(
						"PATH" => $strPath2Import,
						"FILE_RUN" => $strPath2Import.$file,
						"TITLE" => $rep_title
					);
				if (file_exists($_SERVER["DOCUMENT_ROOT"].$strPath2Import.$import_name."_setup.php"))
				{
					$arReports[$import_name]["FILE_SETUP"] = $strPath2Import.$import_name."_setup.php";
				}
			}
		}
	}
	closedir($handle);

	return $arReports;
}

$arReportsList = GetReportsList(CATALOG_PATH2IMPORTS);

/////////////////////////////////////////////////////////////////////
// In setup wizard
//	$FINITE = true  on the last step
//	$SETUP_FIELDS_LIST  the list of fields which are saved in pofile (coma-separated)
//	$STEP  current wizard step
//	$SETUP_PROFILE_NAME  profile name
//	$strImportErrorMessage  error messages
/////////////////////////////////////////////////////////////////////
if (($bCanEdit || $bCanExec) && check_bitrix_sessid())
{
	$strActFileName = trim(strval($_REQUEST["ACT_FILE"]));
	if (strlen($_REQUEST["ACTION"])>0 && strlen($strActFileName)<=0)
	{
		$strErrorMessage .= GetMessage("CES_ERROR_NO_FILE")."\n";
	}
	elseif (strlen($_REQUEST["ACTION"])<=0 && strlen($strActFileName)>0)
	{
		$strErrorMessage .= GetMessage("CES_ERROR_NO_ACTION")."\n";
	}
	elseif (preg_match(BX_CATALOG_FILENAME_REG, $strActFileName))
	{
		$strErrorMessage .= GetMessage("CES_ERROR_BAD_FILENAME2")."\n";
	}

	if (strlen($strErrorMessage)<=0 && strlen($strActFileName)>0)
	{
		if (!file_exists($_SERVER["DOCUMENT_ROOT"].$arReportsList[$strActFileName]["FILE_RUN"])
			|| !is_file($_SERVER["DOCUMENT_ROOT"].$arReportsList[$strActFileName]["FILE_RUN"])
			)
			$strErrorMessage .= GetMessage("CES_ERROR_FILE_NOT_EXIST")." (".$arReportsList[$strActFileName]["FILE_RUN"].").\n";

		if (strlen($strErrorMessage)<=0)
		{
			$PROFILE_ID = intval($_REQUEST["PROFILE_ID"]);

			//////////////////////////////////////////////
			// Import
			//////////////////////////////////////////////
			if ($bCanExec && $_REQUEST["ACTION"]=="IMPORT")
			{
				$CUR_FILE_POS = intval($CUR_FILE_POS);
				$CUR_LOAD_SESS_ID = trim($_REQUEST['CUR_LOAD_SESS_ID']);
				if (1 != preg_match('/^CL\d+$/',$CUR_LOAD_SESS_ID))
				{
					$CUR_LOAD_SESS_ID = '';
				}

				if ($CUR_FILE_POS > 0 && !empty($CUR_LOAD_SESS_ID) && is_set($_SESSION, $CUR_LOAD_SESS_ID))
				{
					$bFirstLoadStep = false;

					if (is_set($_SESSION[$CUR_LOAD_SESS_ID], "SETUP_VARS"))
					{
						parse_str($_SESSION[$CUR_LOAD_SESS_ID]["SETUP_VARS"], $output);

						// it's almost unreal to use it
						//$arSetupVarsList = explode(",", $SETUP_VARS_LIST);

						foreach ($output as $key => $value)
						{
							//if (in_array($key, $arSetupVarsList))
								${$key} = $value;
						}
					}

					//$arInternalVarsList = explode(",", $INTERNAL_VARS_LIST);
					if (is_set($_SESSION[$CUR_LOAD_SESS_ID], "INTERNAL_VARS"))
					{
						foreach ($_SESSION[$CUR_LOAD_SESS_ID]["INTERNAL_VARS"] as $key => $value)
						{
							//if (in_array($key, $arInternalVarsList))
								${$key} = $value;
						}
					}
				}
				else
				{
					// if default profile than
					//		if have setup file than
					//			setup and run
					//		else
					//			run
					// else
					//		init params and run
					$bFirstLoadStep = true;

					$bDefaultProfile = true;
					$boolNeedEdit = false;
					if ($PROFILE_ID > 0)
					{
						$ar_profile = CCatalogImport::GetByID($PROFILE_ID);
						if ($ar_profile)
						{
							if ($ar_profile["DEFAULT_PROFILE"] != "Y")
								$bDefaultProfile = false;
							if ('Y' == $ar_profile["NEED_EDIT"])
								$boolNeedEdit = true;
						}
						else
						{
							$PROFILE_ID = 0;
						}
					}

					// If there is no profile than lets search default profile
					if ($PROFILE_ID <= 0)
					{
						$db_profile = CCatalogImport::GetList(array(), array("DEFAULT_PROFILE"=>"Y", "FILE_NAME"=>$strActFileName));
						if ($ar_profile = $db_profile->Fetch())
						{
							$PROFILE_ID = intval($ar_profile["ID"]);
							if ('Y' == $ar_profile["NEED_EDIT"])
								$boolNeedEdit = true;
						}
					}

					if ($bDefaultProfile || $boolNeedEdit)
					{
						if (strlen($arReportsList[$strActFileName]["FILE_SETUP"]) > 0)
						{
							$STEP = intval($_REQUEST["STEP"]);
							if (isset($_POST['backButton'])) $STEP-=2;
							if ($STEP<=0) $STEP = 1;
							$FINITE = false;

							ob_start();
							// compatibility hack!
							$CATALOG_RIGHT = 'W';
							include($_SERVER["DOCUMENT_ROOT"].$arReportsList[$strActFileName]["FILE_SETUP"]);

							if ($FINITE !== true)
							{
								$ob = ob_get_contents();
								ob_end_clean();

								$APPLICATION->SetTitle($arReportsList[$strActFileName]["TITLE"]);
								include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

								echo $ob;

								include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
								die();
							}
							ob_end_clean();
						}
					}
					else
					{
						parse_str($ar_profile["SETUP_VARS"]);
					}

					if (isset($CUR_LOAD_SESS_ID) && strlen($CUR_LOAD_SESS_ID) > 0 && is_set($_SESSION, $CUR_LOAD_SESS_ID))
					{
						unset($_SESSION[$CUR_LOAD_SESS_ID]);
						unset($CUR_LOAD_SESS_ID);
					}
					$CUR_FILE_POS = 0;
				}

				$strImportErrorMessage = "";
				$strImportOKMessage = "";

				$bAllDataLoaded = true;
				CCatalogDiscountSave::Disable();

				include($_SERVER["DOCUMENT_ROOT"].$arReportsList[$strActFileName]["FILE_RUN"]);

				CCatalogDiscountSave::Enable();
				if (!$bAllDataLoaded)
				{
					if (strlen($CUR_LOAD_SESS_ID) <= 0)
						$CUR_LOAD_SESS_ID = "CL".time();

					$arInternalVarsList = explode(",", $INTERNAL_VARS_LIST);
					$arInternalVars = array();
					for ($i = 0; $i < count($arInternalVarsList); $i++)
					{
						$arInternalVarsList[$i] = Trim($arInternalVarsList[$i]);
						$arInternalVars[$arInternalVarsList[$i]] = ${$arInternalVarsList[$i]};
					}

					$arSetupVarsList = explode(",", $SETUP_VARS_LIST);
					$setupVars = "";
					for ($i = 0; $i < count($arSetupVarsList); $i++)
					{
						$arSetupVarsList[$i] = Trim($arSetupVarsList[$i]);

						$vValue = ${$arSetupVarsList[$i]};
						if (is_array($vValue))
						{
							foreach ($vValue as $key1 => $value1)
							{
								if (strlen($setupVars) > 0)
									$setupVars .= "&";
								$setupVars .= $arSetupVarsList[$i]."[".(is_numeric($key1)?"":"\"").$key1.(is_numeric($key1)?"":"\"")."]=".urlencode($value1);
							}
						}
						else
						{
							if (strlen($setupVars) > 0)
								$setupVars .= "&";
							$setupVars .= $arSetupVarsList[$i]."=".urlencode($vValue);
						}
					}

					$_SESSION[$CUR_LOAD_SESS_ID]["CUR_FILE_POS"] = intval($CUR_FILE_POS);
					$_SESSION[$CUR_LOAD_SESS_ID]["INTERNAL_VARS"] = $arInternalVars;
					$_SESSION[$CUR_LOAD_SESS_ID]["SETUP_VARS"] = $setupVars;
					$_SESSION[$CUR_LOAD_SESS_ID]["ERROR_MESSAGE"] .= $strImportErrorMessage;
					$_SESSION[$CUR_LOAD_SESS_ID]["OK_MESSAGE"] .= $strImportOKMessage;

					$urlParams = "CUR_FILE_POS=".intval($CUR_FILE_POS)."&CUR_LOAD_SESS_ID=".urlencode($CUR_LOAD_SESS_ID)."&ACT_FILE=".urlencode($strActFileName)."&ACTION=IMPORT&PROFILE_ID=".intval($PROFILE_ID);
					?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
					<html>
					<head>
					<title><?= GetMessage("CICML_STEP_AUTITLE") ?></title>
					</head>
					<body>
						<?echo GetMessage("CATI_AUTO_REFRESH");?>
						<a href="<?echo $APPLICATION->GetCurPage() ?>?lang=<?echo urlencode(LANGUAGE_ID); ?>&<?echo $urlParams ?>&<?=bitrix_sessid_get()?>"><?echo GetMessage("CATI_AUTO_REFRESH_STEP");?></a><br>
						<script type="text/javascript">
						function DoNext()
						{
							window.location="<?echo $APPLICATION->GetCurPage()?>?lang=<?echo urlencode(LANGUAGE_ID); ?>&<?echo CUtil::JSEscape($urlParams); ?>&<?=bitrix_sessid_get()?>";
						}
						setTimeout('DoNext()', 2000);
						</script>
					</body>
					</html><?

					include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin_after.php");
					die();
				}
				else
				{
					if (isset($CUR_LOAD_SESS_ID) && strlen($CUR_LOAD_SESS_ID) > 0 && is_set($_SESSION, $CUR_LOAD_SESS_ID))
					{
						$strImportErrorMessage = $_SESSION[$CUR_LOAD_SESS_ID]["ERROR_MESSAGE"].$strImportErrorMessage;
						$strImportOKMessage = $_SESSION[$CUR_LOAD_SESS_ID]["OK_MESSAGE"].$strImportOKMessage;

						unset($_SESSION[$CUR_LOAD_SESS_ID]);
						unset($CUR_LOAD_SESS_ID);
					}
				}

				if (isset($CUR_LOAD_SESS_ID) && strlen($CUR_LOAD_SESS_ID) > 0 && is_set($_SESSION, $CUR_LOAD_SESS_ID))
				{
					$strImportErrorMessage = $_SESSION[$CUR_LOAD_SESS_ID]["ERROR_MESSAGE"].$strImportErrorMessage;
					$strImportOKMessage = $_SESSION[$CUR_LOAD_SESS_ID]["OK_MESSAGE"].$strImportOKMessage;
				}

				if (strlen($strImportErrorMessage)>0)
					$strErrorMessage .= $strImportErrorMessage;
				if (strlen($strImportOKMessage)>0)
					$strOKMessage .= $strImportOKMessage;

				if (strlen($strErrorMessage)<=0)
				{
					if ($PROFILE_ID>0)
					{
						CCatalogImport::Update($PROFILE_ID, array(
							"=LAST_USE" => $DB->GetNowFunction(),
							'NEED_EDIT' => 'N',
						));
					}
					else
					{
						$PROFILE_ID = CCatalogImport::Add(array(
							"=LAST_USE"		=> $DB->GetNowFunction(),
							"FILE_NAME"		=> $strActFileName,
							"NAME"			=> $arReportsList[$strActFileName]["TITLE"],
							"DEFAULT_PROFILE" => "Y",
							"IN_MENU"		=> "N",
							"IN_AGENT"		=> "N",
							"IN_CRON"		=> "N",
							'NEED_EDIT' => 'N',
							"SETUP_VARS"	=> false
							));
					}

					$randVal = rand(1, 1000);
					$_SESSION["COMMERCEML_IMPORT_".$randVal] = $strOKMessage;
					LocalRedirect("/bitrix/admin/cat_import_setup.php?lang=".urlencode(LANGUAGE_ID)."&success_import=Y&message_sess_id=".$randVal);
				}
			}
			//////////////////////////////////////////////
			// MENU
			//////////////////////////////////////////////
			elseif ($bCanEdit && $_REQUEST["ACTION"]=="MENU")
			{
				if ($PROFILE_ID>0)
				{
					$ar_profile = CCatalogImport::GetByID($PROFILE_ID);
					if (!$ar_profile)
					{
						$PROFILE_ID = 0;
					}
				}

				// If profile is not set lets find default one
				if ($PROFILE_ID<=0)
				{
					$db_profile = CCatalogImport::GetList(array(), array("DEFAULT_PROFILE"=>"Y", "FILE_NAME"=>$strActFileName));
					if ($ar_profile = $db_profile->Fetch())
					{
						$PROFILE_ID = intval($ar_profile["ID"]);
					}
				}

				if ($PROFILE_ID>0)
				{
					CCatalogImport::Update($PROFILE_ID, array(
						"IN_MENU" => ($ar_profile["IN_MENU"]=="Y" ? "N" : "Y")
						));
				}
				else
				{
					$PROFILE_ID = CCatalogImport::Add(array(
						"LAST_USE"		=> false,
						"FILE_NAME"		=> $strActFileName,
						"NAME"			=> $arReportsList[$strActFileName]["TITLE"],
						"DEFAULT_PROFILE" => "Y",
						"IN_MENU"		=> "Y",
						"IN_AGENT"		=> "N",
						"IN_CRON"		=> "N",
						"NEED_EDIT"		=> "N",
						"SETUP_VARS"	=> false
						));
				}

				if (strlen($strErrorMessage)<=0)
				{
					LocalRedirect($APPLICATION->GetCurPage()."?lang=".urlencode(LANGUAGE_ID)."&success_import=Y");
				}
			}
			//////////////////////////////////////////////
			// AGENT
			//////////////////////////////////////////////
			elseif ($USER->CanDoOperation('edit_php') && $_REQUEST["ACTION"]=="AGENT")
			{
				$bDefaultProfile = true;
				$boolNeedEdit = false;
				if ($PROFILE_ID>0)
				{
					$ar_profile = CCatalogImport::GetByID($PROFILE_ID);
					if ($ar_profile)
					{
						if ($ar_profile["DEFAULT_PROFILE"]!="Y")
							$bDefaultProfile = false;
						if ('Y' == $ar_profile["NEED_EDIT"])
							$boolNeedEdit = true;
					}
					else
					{
						$PROFILE_ID = 0;
					}
				}

				// If profile is not set lets find default one
				if ($PROFILE_ID<=0)
				{
					$db_profile = CCatalogImport::GetList(array(), array("DEFAULT_PROFILE"=>"Y", "FILE_NAME"=>$strActFileName));
					if ($ar_profile = $db_profile->Fetch())
					{
						$PROFILE_ID = intval($ar_profile["ID"]);
						if ('Y' == $ar_profile["NEED_EDIT"])
							$boolNeedEdit = true;
					}
				}

				if (($bDefaultProfile && strlen($arReportsList[$strActFileName]["FILE_SETUP"])>0) || $boolNeedEdit)
				{
					$strErrorMessage .= GetMessage("CES_ERROR_NOT_AGENT")."\n";
				}

				if (strlen($strErrorMessage)<=0)
				{
					$agent_period = intval($_REQUEST["agent_period"]);
					if ($agent_period<=0) $agent_period = 24;

					if ($PROFILE_ID>0)
					{
						if ($ar_profile["IN_AGENT"]=="Y")
							CAgent::RemoveAgent("CCatalogImport::PreGenerateImport(".$PROFILE_ID.");", "catalog");
						else
							CAgent::AddAgent("CCatalogImport::PreGenerateImport(".$PROFILE_ID.");", "catalog", "N", $agent_period*60*60, "", "Y");

						CCatalogImport::Update($PROFILE_ID, array(
							"IN_AGENT" => ($ar_profile["IN_AGENT"]=="Y" ? "N" : "Y")
							));
					}
					else
					{
						$PROFILE_ID = CCatalogImport::Add(array(
							"LAST_USE"		=> false,
							"FILE_NAME"		=> $strActFileName,
							"NAME"			=> $arReportsList[$strActFileName]["TITLE"],
							"DEFAULT_PROFILE" => "Y",
							"IN_MENU"		=> "N",
							"IN_AGENT"		=> "Y",
							"IN_CRON"		=> "N",
							"NEED_EDIT"		=> "N",
							"SETUP_VARS"	=> false
							));
						if (intval($PROFILE_ID)>0)
						{
							CAgent::AddAgent("CCatalogImport::PreGenerateImport(".$PROFILE_ID.");", "catalog", "N", $agent_period*60*60, "", "Y");
						}
						else
						{
							$strErrorMessage .= GetMessage("CES_ERROR_ADD_PROFILE")."\n";
						}
					}
				}

				if (strlen($strErrorMessage)<=0)
				{
					LocalRedirect($APPLICATION->GetCurPage()."?lang=".urlencode(LANGUAGE_ID)."&success_import=Y");
				}
			}
			//////////////////////////////////////////////
			// CRON
			//////////////////////////////////////////////
			elseif ($USER->CanDoOperation('edit_php') && $_REQUEST["ACTION"]=="CRON")
			{
				$bDefaultProfile = true;
				$boolNeedEdit = false;
				if ($PROFILE_ID>0)
				{
					$ar_profile = CCatalogImport::GetByID($PROFILE_ID);
					if ($ar_profile)
					{
						if ($ar_profile["DEFAULT_PROFILE"]!="Y")
							$bDefaultProfile = false;
						if ('Y' == $ar_profile["NEED_EDIT"])
							$boolNeedEdit = true;
					}
					else
					{
						$PROFILE_ID = 0;
					}
				}

				// If profile is not set lets find default one
				if ($PROFILE_ID<=0)
				{
					$db_profile = CCatalogImport::GetList(array(), array("DEFAULT_PROFILE"=>"Y", "FILE_NAME"=>$strActFileName));
					if ($ar_profile = $db_profile->Fetch())
					{
						$PROFILE_ID = intval($ar_profile["ID"]);
						if ('Y' == $ar_profile["NEED_EDIT"])
							$boolNeedEdit = true;
					}
				}

				if (($bDefaultProfile && strlen($arReportsList[$strActFileName]["FILE_SETUP"])>0) || $boolNeedEdit)
				{
					$strErrorMessage .= GetMessage("CES_ERROR_NOT_CRON")."\n";
				}

				if (strlen($strErrorMessage)<=0)
				{
					$agent_period = intval($_REQUEST["agent_period"]);
					$agent_hour = Trim($_REQUEST["agent_hour"]);
					$agent_minute = Trim($_REQUEST["agent_minute"]);

					if ($agent_period<=0 && (strlen($agent_hour)<=0 || strlen($agent_minute)<=0))
					{
						$agent_period = 24;
						$agent_hour = "";
						$agent_minute = "";
					}
					elseif ($agent_period>0 && strlen($agent_hour)>0 && strlen($agent_minute)>0)
					{
						$agent_period = 0;
					}

					$agent_php_path = Trim($_REQUEST["agent_php_path"]);
					if (strlen($agent_php_path)<=0) $agent_php_path = "/usr/local/php/bin/php";

					if (!file_exists($_SERVER["DOCUMENT_ROOT"].CATALOG_PATH2IMPORTS."cron_frame.php"))
					{
						CheckDirPath($_SERVER["DOCUMENT_ROOT"].CATALOG_PATH2IMPORTS);
						$tmp_file_size = filesize($_SERVER["DOCUMENT_ROOT"].CATALOG_PATH2IMPORTS_DEF."cron_frame.php");
						$fp = fopen($_SERVER["DOCUMENT_ROOT"].CATALOG_PATH2IMPORTS_DEF."cron_frame.php", "rb");
						$tmp_data = fread($fp, $tmp_file_size);
						fclose($fp);

						$tmp_data = str_replace("#DOCUMENT_ROOT#", $_SERVER["DOCUMENT_ROOT"], $tmp_data);
						$tmp_data = str_replace("#PHP_PATH#", $agent_php_path, $tmp_data);

						$fp = fopen($_SERVER["DOCUMENT_ROOT"].CATALOG_PATH2IMPORTS."cron_frame.php", "ab");
						fwrite($fp, $tmp_data);
						fclose($fp);
					}

					$cfg_data = "";
					if (file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/crontab/crontab.cfg"))
					{
						$cfg_file_size = filesize($_SERVER["DOCUMENT_ROOT"]."/bitrix/crontab/crontab.cfg");
						$fp = fopen($_SERVER["DOCUMENT_ROOT"]."/bitrix/crontab/crontab.cfg", "rb");
						$cfg_data = fread($fp, $cfg_file_size);
						fclose($fp);
					}

					CheckDirPath($_SERVER["DOCUMENT_ROOT"].CATALOG_PATH2IMPORTS."logs/");
					if ($PROFILE_ID>0)
					{
						if ($ar_profile["IN_CRON"]=="Y")
						{
							// remove
							$cfg_data = preg_replace("#^.*?".preg_quote(CATALOG_PATH2IMPORTS)."cron_frame.php +".$PROFILE_ID." *>.*?$#im", "", $cfg_data);
						}
						else
						{
							if ($agent_period>0)
							{
								//$strTime = "* */".$agent_period." * * * ";
								$strTime = "0 */".$agent_period." * * * ";
							}
							else
							{
								$strTime = intval($agent_minute)." ".intval($agent_hour)." * * * ";
							}

							// add
							if (strlen($cfg_data)>0) $cfg_data .= "\n";
							$cfg_data .= $strTime.$agent_php_path." -f ".$_SERVER["DOCUMENT_ROOT"].CATALOG_PATH2IMPORTS."cron_frame.php ".$PROFILE_ID." >".$_SERVER["DOCUMENT_ROOT"].CATALOG_PATH2IMPORTS."logs/".$PROFILE_ID.".txt\n";
						}

						CCatalogImport::Update($PROFILE_ID, array(
							"IN_CRON" => ($ar_profile["IN_CRON"]=="Y" ? "N" : "Y")
							));
					}
					else
					{
						$PROFILE_ID = CCatalogImport::Add(array(
							"LAST_USE"		=> false,
							"FILE_NAME"		=> $strActFileName,
							"NAME"			=> $arReportsList[$strActFileName]["TITLE"],
							"DEFAULT_PROFILE" => "Y",
							"IN_MENU"		=> "N",
							"IN_AGENT"		=> "N",
							"IN_CRON"		=> "Y",
							'NEED_EDIT'		=> 'N',
							"SETUP_VARS"	=> false
							));
						if (intval($PROFILE_ID)>0)
						{
							// add
							if ($agent_period>0)
							{
								//$strTime = "* */".$agent_period." * * * ";
								$strTime = "0 */".$agent_period." * * * ";
							}
							else
							{
								$strTime = intval($agent_minute)." ".intval($agent_hour)." * * * ";
							}

							if (strlen($cfg_data)>0) $cfg_data .= "\n";
							$cfg_data .= $strTime.$agent_php_path." -f ".$_SERVER["DOCUMENT_ROOT"].CATALOG_PATH2IMPORTS."cron_frame.php ".$PROFILE_ID." >".$_SERVER["DOCUMENT_ROOT"].CATALOG_PATH2IMPORTS."logs/".$PROFILE_ID.".txt\n";
						}
						else
						{
							$strErrorMessage .= GetMessage("CES_ERROR_ADD_PROFILE")."\n";
						}
					}
					if (strlen($strErrorMessage)<=0)
					{
						CheckDirPath($_SERVER["DOCUMENT_ROOT"]."/bitrix/crontab/");
						$cfg_data = preg_replace("#[\r\n]{2,}#im", "\n", $cfg_data);
						$fp = fopen($_SERVER["DOCUMENT_ROOT"]."/bitrix/crontab/crontab.cfg", "wb");
						fwrite($fp, $cfg_data);
						fclose($fp);

						if ($_REQUEST["auto_cron_tasks"]=="Y")
						{
							$arRetval = array();
							@exec("crontab ".$_SERVER["DOCUMENT_ROOT"]."/bitrix/crontab/crontab.cfg", $arRetval, $return_var);
							if (intval($return_var)!=0)
							{
								$strErrorMessage .= GetMessage("CES_ERROR_ADD2CRON")." \n";
								if (is_array($arRetval) && count($arRetval)>0)
								{
									for ($ier = 0; $ier < count($arRetval); $ier++)
									{
										$strErrorMessage .= $arRetval[$i]." \n";
									}
								}
								else
								{
									$strErrorMessage .= GetMessage("CES_ERROR_UNKNOWN")."\n";
								}
							}
						}
					}
				}

				if (strlen($strErrorMessage)<=0)
				{
					LocalRedirect($APPLICATION->GetCurPage()."?lang=".urlencode(LANGUAGE_ID)."&success_import=Y");
				}
			}
			//////////////////////////////////////////////
			// DEL_PROFILE
			//////////////////////////////////////////////
			elseif ($bCanEdit && $_REQUEST["ACTION"]=="DEL_PROFILE")
			{
				$ar_profile = CCatalogImport::GetByID($PROFILE_ID);
				if (!$ar_profile)
					$strErrorMessage .= GetMessage("CES_ERROR_NO_PROFILE1").$PROFILE_ID." ".GetMessage("CES_ERROR_NO_PROFILE2")."\n";

				if (strlen($strErrorMessage)<=0)
				{
					if ($ar_profile["IN_AGENT"]=="Y")
					{
						CAgent::RemoveAgent("CCatalogImport::PreGenerateImport(".$PROFILE_ID.");", "catalog");
					}
					if ($ar_profile["IN_CRON"]=="Y")
					{
						$cfg_data = "";
						if (file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/crontab/crontab.cfg"))
						{
							$cfg_file_size = filesize($_SERVER["DOCUMENT_ROOT"]."/bitrix/crontab/crontab.cfg");
							$fp = fopen($_SERVER["DOCUMENT_ROOT"]."/bitrix/crontab/crontab.cfg", "rb");
							$cfg_data = fread($fp, $cfg_file_size);
							fclose($fp);

							$cfg_data = preg_replace("#^.*?".preg_quote(CATALOG_PATH2IMPORTS)."cron_frame.php +".$PROFILE_ID." *>.*?$#im", "", $cfg_data);

							$cfg_data = preg_replace("#[\r\n]{2,}#im", "\n", $cfg_data);
							$fp = fopen($_SERVER["DOCUMENT_ROOT"]."/bitrix/crontab/crontab.cfg", "wb");
							fwrite($fp, $cfg_data);
							fclose($fp);

							$arRetval = array();
							@exec("crontab ".$_SERVER["DOCUMENT_ROOT"]."/bitrix/crontab/crontab.cfg", $arRetval, $return_var);
							if (intval($return_var)!=0)
							{
								$strErrorMessage .= GetMessage("CES_ERROR_ADD2CRON")." \n";
								if (is_array($arRetval) && count($arRetval)>0)
								{
									for ($ier = 0; $ier < count($arRetval); $ier++)
									{
										$strErrorMessage .= $arRetval[$i]." \n";
									}
								}
								else
								{
									$strErrorMessage .= GetMessage("CES_ERROR_UNKNOWN")."\n";
								}
							}
						}
					}
					CCatalogImport::Delete($PROFILE_ID);
				}
			}
			//////////////////////////////////////////////
			// IMPORT_SETUP
			//////////////////////////////////////////////
			elseif ($bCanEdit && $_REQUEST["ACTION"]=="IMPORT_SETUP")
			{
				if (strlen($arReportsList[$strActFileName]["FILE_SETUP"])>0)
				{
					$STEP = intval($_REQUEST["STEP"]);
					if (isset($_POST['backButton'])) $STEP-=2;
					if ($STEP<=0) $STEP = 1;
					$FINITE = false;

					ob_start();
					$APPLICATION->SetTitle($arReportsList[$strActFileName]["TITLE"]);
					include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

					// compatibility hack!
					$CATALOG_RIGHT = 'W';
					include($_SERVER["DOCUMENT_ROOT"].$arReportsList[$strActFileName]["FILE_SETUP"]);

					if ($FINITE!==true)
					{
						ob_end_flush();
						include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
						die();
					}
					ob_end_clean();

					// Saving import profile
					if (strlen($SETUP_FIELDS_LIST)<=0) $SETUP_FIELDS_LIST = $_REQUEST["SETUP_FIELDS_LIST"];
					$arProfileFields = explode(",", $SETUP_FIELDS_LIST);
					$strSETUP_VARS = "";
					for ($i = 0; $i < count($arProfileFields); $i++)
					{
						$arProfileFields[$i] = Trim($arProfileFields[$i]);

						$vValue = ${$arProfileFields[$i]};
						if (!is_array($vValue) && strlen($vValue)<=0) $vValue = $_REQUEST[$arProfileFields[$i]];

						if (is_array($vValue))
						{
							foreach ($vValue as $key1 => $value1)
							{
								if (strlen($strSETUP_VARS)>0) $strSETUP_VARS .= "&";
								$strSETUP_VARS .= $arProfileFields[$i]."[".(is_numeric($key1)?"":"\"").$key1.(is_numeric($key1)?"":"\"")."]=".urlencode($value1);
							}
						}
						else
						{
							if (strlen($strSETUP_VARS)>0) $strSETUP_VARS .= "&";
							$strSETUP_VARS .= $arProfileFields[$i]."=".urlencode($vValue);
						}
					}

					if (strlen($SETUP_PROFILE_NAME)<=0) $SETUP_PROFILE_NAME = $_REQUEST["SETUP_PROFILE_NAME"];
					if (strlen($SETUP_PROFILE_NAME)<=0) $SETUP_PROFILE_NAME = $arReportsList[$strActFileName]["TITLE"];

					$PROFILE_ID = CCatalogImport::Add(array(
						"LAST_USE"		=> false,
						"FILE_NAME"		=> $strActFileName,
						"NAME"			=> $SETUP_PROFILE_NAME,
						"DEFAULT_PROFILE" => "N",
						"IN_MENU"		=> "N",
						"IN_AGENT"		=> "N",
						"IN_CRON"		=> "N",
						"NEED_EDIT"		=> "N",
						"SETUP_VARS"	=> $strSETUP_VARS
						));

					if (intval($PROFILE_ID)<=0)
					{
						$strErrorMessage .= GetMessage("CES_ERROR_SAVE_PROFILE")."\n";
					}
				}
				else
				{
					$strErrorMessage .= GetMessage("CES_ERROR_NO_SETUP_FILE")."\n";
				}
				if (strlen($strErrorMessage)<=0)
				{
					LocalRedirect($APPLICATION->GetCurPage()."?lang=".urlencode(LANGUAGE_ID)."&success_import=Y");
				}
			}
			elseif ($bCanEdit && ($_REQUEST["ACTION"]=="IMPORT_EDIT" || $_REQUEST["ACTION"]=="IMPORT_COPY"))
			{
				$boolFlag = true;
				if (!isset($_REQUEST['PROFILE_ID']))
				{
					$strErrorMessage .= GetMessage('CES_EDIT_PROFILE_ERR_ID_ABSENT')."\n";
					$boolFlag = false;
				}
				else
				{
					$PROFILE_ID = intval($_REQUEST['PROFILE_ID']);
					if ($PROFILE_ID <= 0)
					{
						$strErrorMessage .= GetMessage('CES_EDIT_PROFILE_ERR_ID_ABSENT')."\n";
						$boolFlag = false;
					}
				}
				if ($boolFlag)
				{
					$arProfile = CCatalogImport::GetByID($PROFILE_ID);
					if ($arProfile)
					{
						if ($arProfile["DEFAULT_PROFILE"] == "Y")
						{
							$strErrorMessage .= ($_REQUEST["ACTION"]=="IMPORT_EDIT" ? GetMessage('CES_EDIT_PROFILE_ERR_DEFAULT') : GetMessage('CES_COPY_PROFILE_ERR_DEFAULT'))."\n";
							$boolFlag = false;
						}
					}
					else
					{
						$strErrorMessage .= ($_REQUEST["ACTION"]=="IMPORT_EDIT" ? GetMessage('CES_EDIT_PROFILE_ERR_DEFAULT') : GetMessage('CES_COPY_PROFILE_ERR_DEFAULT'))."\n";
						$boolFlag = false;
					}
				}
				if ($boolFlag)
				{
					if (strlen($arReportsList[$arProfile['FILE_NAME']]["FILE_SETUP"])>0)
					{
						$STEP = intval($_REQUEST["STEP"]);
						if (isset($_POST['backButton'])) $STEP-=2;
						if ($STEP<=0) $STEP = 1;
						$FINITE = false;

						$arOldSetupVars = array();
						if ($arProfile['SETUP_VARS'])
							parse_str($arProfile['SETUP_VARS'],$arOldSetupVars);
						$arOldSetupVars['SETUP_PROFILE_NAME'] = $arProfile['NAME'];
						$_REQUEST['OLD_SETUP_VARS'] = $arOldSetupVars;

						ob_start();
						$APPLICATION->SetTitle($arReportsList[$strActFileName]["TITLE"]);
						include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

						// compatibility hack!
						$CATALOG_RIGHT = 'W';
						include($_SERVER["DOCUMENT_ROOT"].$arReportsList[$strActFileName]["FILE_SETUP"]);

						if ($FINITE!==true)
						{
							ob_end_flush();
							include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
							die();
						}
						ob_end_clean();

						// Saving import profile
						if (strlen($SETUP_FIELDS_LIST)<=0) $SETUP_FIELDS_LIST = $_REQUEST["SETUP_FIELDS_LIST"];
						$arProfileFields = explode(",", $SETUP_FIELDS_LIST);
						$strSETUP_VARS = "";
						for ($i = 0; $i < count($arProfileFields); $i++)
						{
							$arProfileFields[$i] = Trim($arProfileFields[$i]);

							$vValue = ${$arProfileFields[$i]};
							if (!is_array($vValue) && strlen($vValue)<=0) $vValue = $_REQUEST[$arProfileFields[$i]];

							if (is_array($vValue))
							{
								foreach ($vValue as $key1 => $value1)
								{
									if (strlen($strSETUP_VARS)>0) $strSETUP_VARS .= "&";
									$strSETUP_VARS .= $arProfileFields[$i]."[".(is_numeric($key1)?"":"\"").$key1.(is_numeric($key1)?"":"\"")."]=".urlencode($value1);
								}
							}
							else
							{
								if (strlen($strSETUP_VARS)>0) $strSETUP_VARS .= "&";
								$strSETUP_VARS .= $arProfileFields[$i]."=".urlencode($vValue);
							}
						}

						if (strlen($SETUP_PROFILE_NAME)<=0) $SETUP_PROFILE_NAME = $_REQUEST["SETUP_PROFILE_NAME"];
						if (strlen($SETUP_PROFILE_NAME)<=0) $SETUP_PROFILE_NAME = $arReportsList[$strActFileName]["TITLE"];

						if ($_REQUEST["ACTION"]=="IMPORT_EDIT")
						{
							$NEW_PROFILE_ID = CCatalogImport::Update($PROFILE_ID,array(
								"NAME"			=> $SETUP_PROFILE_NAME,
								"SETUP_VARS"	=> $strSETUP_VARS,
								'NEED_EDIT'		=> 'N',
							));
							if ($NEW_PROFILE_ID != $PROFILE_ID)
							{
								$strErrorMessage .= GetMessage("CES_ERROR_PROFILE_UPDATE")."\n";
							}
						}
						elseif ($_REQUEST["ACTION"]=="IMPORT_COPY")
						{
							$NEW_PROFILE_ID = CCatalogImport::Add(array(
								"LAST_USE"		=> false,
								"FILE_NAME"		=> $strActFileName,
								"NAME"			=> $SETUP_PROFILE_NAME,
								"DEFAULT_PROFILE" => "N",
								"IN_MENU"		=> "N",
								"IN_AGENT"		=> "N",
								"IN_CRON"		=> "N",
								'NEED_EDIT'		=> 'N',
								"SETUP_VARS"	=> $strSETUP_VARS
							));
							if (intval($PROFILE_ID)<=0)
							{
								$strErrorMessage .= GetMessage("CES_ERROR_COPY_PROFILE")."\n";
							}
						}
					}
					else
					{
						$strErrorMessage .= GetMessage("CES_ERROR_NO_SETUP_FILE")."\n";
					}
				}
				if (strlen($strErrorMessage)<=0)
				{
					LocalRedirect($APPLICATION->GetCurPage()."?lang=".urlencode(LANGUAGE_ID)."&success_import=Y");
				}
			}
		}
	}
}
/////////////////////////////////////////////////////////////////////

// Set NEW_OS in GET string to test other operational systems!!!
$bWindowsHosting = false;
if (isset($_GET["NEW_OS"]))
{
	if (strlen(trim($_GET["NEW_OS"]))<=0)
		unset($_SESSION["TMP_MY_NEW_OS"]);
	else
		$_SESSION["TMP_MY_NEW_OS"] = $_GET["NEW_OS"];
}
$strCurrentOS = PHP_OS;
if (isset($_SESSION["TMP_MY_NEW_OS"]) && strlen($_SESSION["TMP_MY_NEW_OS"])>0)
	$strCurrentOS = $_SESSION["TMP_MY_NEW_OS"];
if (strtoupper(substr($strCurrentOS, 0, 3)) === "WIN")
{
	$bWindowsHosting = true;
}

$sTableID = "import_setup";

$lAdmin = new CAdminList($sTableID);

$lAdmin->AddHeaders(array(
	array("id"=>"NAME", "content"=>GetMessage("import_setup_name"), "default"=>true),
	array("id"=>"FILE", "content"=>GetMessage("import_setup_file"), "default"=>true),
	array("id"=>"PROFILE", "content"=>GetMessage("CES_PROFILE"), "default"=>true),
	array("id"=>"IN_MENU", "content"=>GetMessage("CES_IN_MENU"), "default"=>true),
	array("id"=>"IN_AGENT", "content"=>GetMessage("CES_IN_AGENT"), "default"=>true),
	array("id"=>"IN_CRON", "content"=>GetMessage("CES_IN_CRON"), "default"=>true),
	array("id"=>"USED", "content"=>GetMessage("CES_USED"), "default"=>true),
	array('id' => 'CREATED_BY', 'content' => GetMessage('CES_CREATED_BY'), 'default' => false),
	array('id' => 'DATE_CREATE', 'content' => GetMessage('CES_DATE_CREATE'), 'default' => false),
	array('id' => 'MODIFIED_BY', 'content' => GetMessage('CES_MODIFIED_BY'), 'default' => true),
	array('id' => 'TIMESTAMP_X', 'content' => GetMessage('CES_TIMESTAMP_X'), 'default' => true),
));

$arUserList = array();
$strNameFormat = CSite::GetNameFormat(true);

$arContextMenu = array();

foreach($arReportsList as $strReportFile => $arReportParams)
{
	if ($bCanEdit && !empty($arReportParams["FILE_SETUP"]))
	{
		$arContextMenu[] = array(
			"TEXT" => htmlspecialcharsbx($arReportParams["TITLE"]),
			"TITLE" => GetMessage("import_setup_script")." &quot;".$strReportFile."&quot;",
			"ACTION"=>"window.location='".addslashes($APPLICATION->GetCurPage()."?lang=".LANGUAGE_ID."&ACT_FILE=".urlencode($strReportFile)."&ACTION=IMPORT_SETUP"."&".bitrix_sessid_get())."';"
		);
	}

	$boolExist = false;
	$rsProfiles = CCatalogImport::GetList(
		array("LAST_USE" => "DESC", "NAME" => "ASC"),
		array("FILE_NAME" => $strReportFile, 'DEFAULT_PROFILE' => 'Y')
	);

	while ($arProfile = $rsProfiles->Fetch())
	{

		$boolExist = true;
		$boolNeedEdit = (isset($arProfile['NEED_EDIT']) && 'Y' == $arProfile['NEED_EDIT']);

		$row = &$lAdmin->AddRow($arProfile['ID'], $arProfile);

		$row->AddViewField("NAME", htmlspecialcharsbx($arReportParams["TITLE"]));
		$row->AddViewField("FILE", $strReportFile);

		$strProfileLink = '';
		if ($bCanEdit)
		{
			if ($boolNeedEdit)
			{
				$strProfileLink = '<a href="'.$APPLICATION->GetCurPage()."?lang=".LANGUAGE_ID."&amp;ACT_FILE=".urlencode($strReportFile)."&amp;ACTION=IMPORT_EDIT&amp;PROFILE_ID=".$arProfile["ID"]."&amp;".bitrix_sessid_get().'" title="'.GetMessage("CES_EDIT_PROPFILE_DESCR").'"><i>'.GetMessage("CES_DEFAULT").'</i></a><br /><i>('.GetMessage('CES_NEED_EDIT').')</i>';
			}
			else
			{
				$strProfileLink = '<a href="'.('Y' == $arProfile["IN_MENU"] ? '/bitrix/admin/cat_exec_imp.php' : $APPLICATION->GetCurPage()).'?lang='.LANGUAGE_ID."&amp;ACT_FILE=".urlencode($strReportFile)."&amp;ACTION=IMPORT&amp;PROFILE_ID=".$arProfile["ID"]."&amp;".bitrix_sessid_get().'" title="'.GetMessage("import_setup_begin").'"><i>'.GetMessage("CES_DEFAULT").'</i></a>';
			}
		}
		else
		{
			$strProfileLink = '<i>'.GetMessage("CES_DEFAULT").'</i>';
		}
		$row->AddViewField('PROFILE', $strProfileLink);

		$row->AddCheckField("IN_MENU", false);
		$row->AddCheckField("IN_AGENT", false);
		$row->AddCheckField("IN_CRON", false);

		$row->AddCalendarField("USED", false);

		$strModifiedBy = '';
		$arProfile['MODIFIED_BY'] = intval($arProfile['MODIFIED_BY']);
		if (0 < $arProfile['MODIFIED_BY'])
		{
			if (!array_key_exists($arProfile['MODIFIED_BY'], $arUserList))
			{
				$rsUsers = CUser::GetList(($by = 'ID'),($order = 'ASC'),array('ID_EQUAL_EXACT' => $arProfile['MODIFIED_BY']),array('FIELDS' => array('ID', 'LOGIN', 'NAME', 'LAST_NAME', 'SECOND_NAME')));
				if ($arOneUser = $rsUsers->Fetch())
				{
					$arOneUser['ID'] = intval($arOneUser['ID']);
					$arUserList[$arOneUser['ID']] = CUser::FormatName($strNameFormat, $arOneUser);
				}
			}
			if (isset($arUserList[$arProfile['MODIFIED_BY']]))
				$strModifiedBy = '<a href="/bitrix/admin/user_edit.php?lang='.LANGUAGE_ID.'&ID='.$arProfile['MODIFIED_BY'].'">'.$arUserList[$arProfile['MODIFIED_BY']].'</a>';
		}

		$row->AddViewField("CREATED_BY", '');
		$row->AddViewField("DATE_CREATE", '');
		$row->AddViewField("MODIFIED_BY", $strModifiedBy);
		$row->AddCalendarField("TIMESTAMP_X", false);

		$arActions = array();

		if ($bCanExec)
		{
			$arActions[] = array(
				"DEFAULT"=>true,
				"TEXT"=>GetMessage("CES_RUN_IMPORT"),
				"TITLE"=>GetMessage("CES_RUN_IMPORT_DESCR"),
				"ACTION"=>$lAdmin->ActionRedirect($APPLICATION->GetCurPage()."?lang=".LANGUAGE_ID."&ACT_FILE=".urlencode($strReportFile)."&".bitrix_sessid_get()."&ACTION=IMPORT&PROFILE_ID=".$arProfile['ID']),
			);
			$arActions[] = array(
				"TEXT" => GetMessage('CES_ADD_PROFILE'),
				"TITLE" => GetMessage('CES_ADD_PROFILE_DESCR'),
				"ACTION" => $lAdmin->ActionRedirect($APPLICATION->GetCurPage()."?lang=".LANGUAGE_ID."&ACT_FILE=".urlencode($strReportFile)."&ACTION=IMPORT_SETUP"."&".bitrix_sessid_get()),
			);
		}

		if ($bCanEdit || $bCanExec)
			$arActions[] = array("SEPARATOR"=>true);

		if ($bCanEdit)
		{
			if ('Y' == $arProfile["IN_MENU"])
			{
				$arActions[] = array(
					"TEXT" => GetMessage("CES_TO_LEFT_MENU_DEL"),
					"TITLE" => GetMessage("CES_TO_LEFT_MENU_DESCR_DEL"),
					"ACTION" => $lAdmin->ActionRedirect($APPLICATION->GetCurPage()."?lang=".LANGUAGE_ID."&ACT_FILE=".urlencode($strReportFile)."&".bitrix_sessid_get()."&ACTION=MENU&PROFILE_ID=".$arProfile['ID']),
				);
			}
			else
			{
				$arActions[] = array(
					"TEXT" => GetMessage("CES_TO_LEFT_MENU"),
					"TITLE" => GetMessage("CES_TO_LEFT_MENU_DESCR"),
					"ACTION" => $lAdmin->ActionRedirect($APPLICATION->GetCurPage()."?lang=".LANGUAGE_ID."&ACT_FILE=".urlencode($strReportFile)."&".bitrix_sessid_get()."&ACTION=MENU&PROFILE_ID=".$arProfile['ID']),
				);
			}
		}

		if (empty($arReportParams["FILE_SETUP"]) && 'Y' != $arProfile["NEED_EDIT"] && $USER->CanDoOperation('edit_php'))
		{
			if ('Y' == $arProfile["IN_AGENT"])
			{
				$arActions[] = array(
					"TEXT" => GetMessage("CES_TO_AGENT_DEL"),
					"TITLE" => GetMessage("CES_TO_AGENT_DESCR_DEL"),
					"ACTION" => $lAdmin->ActionRedirect($APPLICATION->GetCurPage()."?lang=".LANGUAGE_ID."&ACT_FILE=".urlencode($ReportFile)."&".bitrix_sessid_get()."&ACTION=AGENT&PROFILE_ID=".$ar_prof_res["ID"]),
				);
			}
			else
			{
				$arActions[] = array(
					"TEXT" => GetMessage("CES_TO_AGENT"),
					"TITLE" => GetMessage("CES_TO_AGENT_DESCR"),
					"ACTION" => "ShowAgentForm('".$APPLICATION->GetCurPage()."?lang=".LANGUAGE_ID."&amp;ACT_FILE=".$strReportFile."&amp;".bitrix_sessid_get()."&amp;ACTION=AGENT&amp;PROFILE_ID=".$arProfile["ID"]."');",
				);
			}

			if ('Y' == $arProfile['IN_CRON'])
			{
				$arActions[] = array(
					"DISABLED" => $bWindowsHosting,
					"TEXT" => GetMessage("CES_TO_CRON_DEL"),
					"TITLE" => GetMessage("CES_TO_CRON_DESCR_DEL"),
					"ACTION" => ($bWindowsHosting ? '' : "ShowCronForm('".$APPLICATION->GetCurPage()."?lang=".LANGUAGE_ID."&amp;ACT_FILE=".$strReportFile."&amp;".bitrix_sessid_get()."&amp;ACTION=CRON&amp;PROFILE_ID=".$arProfile["ID"]."', false);"),
				);
			}
			else
			{
				$arActions[] = array(
					"DISABLED" => $bWindowsHosting,
					"TEXT" => GetMessage("CES_TO_CRON"),
					"TITLE" => GetMessage("CES_TO_CRON_DESCR"),
					"ACTION" => ($bWindowsHosting ? '' : "ShowCronForm('".$APPLICATION->GetCurPage()."?lang=".LANGUAGE_ID."&amp;ACT_FILE=".$strReportFile."&amp;".bitrix_sessid_get()."&amp;ACTION=CRON&amp;PROFILE_ID=".$arProfile["ID"]."', true);"),
				);
			}
		}

		$row->AddActions($arActions);
	}

	if (!$boolExist)
	{
		$row = &$lAdmin->AddRow(0, null);

		$row->AddViewField("NAME", htmlspecialcharsbx($arReportParams["TITLE"]));
		$row->AddViewField("FILE", $strReportFile);

		$strProfileLink = '<i>'.GetMessage("CES_DEFAULT").'</i>';
		if ($bCanEdit)
		{
			$strProfileLink = '<a href="'.$APPLICATION->GetCurPage()."?lang=".LANGUAGE_ID."&amp;ACT_FILE=".urlencode($strReportFile)."&amp;".bitrix_sessid_get()."&amp;ACTION=IMPORT&amp;PROFILE_ID=0".'" title="'.GetMessage("export_setup_begin").'"><i>'.GetMessage("CES_DEFAULT").'</i></a>';
		}
		$row->AddViewField('PROFILE', $strProfileLink);

		$row->AddViewField("IN_MENU", GetMessage("CES_NO"));
		$row->AddViewField("IN_AGENT", GetMessage("CES_NO"));
		$row->AddViewField("IN_CRON", GetMessage("CES_NO"));

		$row->AddViewField("USED", '');

		$row->AddViewField("CREATED_BY", '');
		$row->AddViewField("DATE_CREATE", '');
		$row->AddViewField("MODIFIED_BY", '');
		$row->AddViewField("TIMESTAMP_X", '');

		$arActions = array();

		if ($bCanExec)
		{
			$arActions[] = array(
				"DEFAULT" => true,
				"TEXT" => GetMessage("CES_RUN_IMPORT"),
				"TITLE" => GetMessage("CES_RUN_IMPORT_DESCR"),
				"ACTION" => $lAdmin->ActionRedirect($APPLICATION->GetCurPage()."?lang=".LANGUAGE_ID."&ACT_FILE=".urlencode($strReportFile)."&".bitrix_sessid_get()."&ACTION=IMPORT&PROFILE_ID=0"),
			);
			$arActions[] = array(
				"TEXT" => GetMessage('CES_ADD_PROFILE'),
				"TITLE" => GetMessage('CES_ADD_PROFILE_DESCR'),
				"ACTION" => $lAdmin->ActionRedirect($APPLICATION->GetCurPage()."?lang=".LANGUAGE_ID."&ACT_FILE=".urlencode($strReportFile)."&ACTION=IMPORT_SETUP"."&".bitrix_sessid_get()),
			);
		}
		if (!empty($arActions))
			$arActions[] = array("SEPARATOR"=>true);

		if ($bCanEdit)
		{
			$arActions[] = array(
				"TEXT" => GetMessage("CES_TO_LEFT_MENU"),
				"TITLE" => GetMessage("CES_TO_LEFT_MENU_DESCR"),
				"ACTION" => $lAdmin->ActionRedirect($APPLICATION->GetCurPage()."?lang=".LANGUAGE_ID."&ACT_FILE=".urlencode($strReportFile)."&".bitrix_sessid_get()."&ACTION=MENU&PROFILE_ID=0"),
			);
		}

		if (empty($arReportParams["FILE_SETUP"]) && $USER->CanDoOperation('edit_php'))
		{
			$arActions[] = array(
				"TEXT" => GetMessage("CES_TO_AGENT"),
				"TITLE" => GetMessage("CES_TO_AGENT_DESCR"),
				"ACTION" => "ShowAgentForm('".$APPLICATION->GetCurPage()."?lang=".LANGUAGE_ID."&amp;ACT_FILE=".$strReportFile."&amp;".bitrix_sessid_get()."&amp;ACTION=AGENT&amp;PROFILE_ID=0');",
			);
			$arActions[] = array(
				"DISABLED" => $bWindowsHosting,
				"TEXT" => GetMessage("CES_TO_CRON"),
				"TITLE" => GetMessage("CES_TO_CRON_DESCR"),
				"ACTION" => ($bWindowsHosting ? '' : "ShowCronForm('".$APPLICATION->GetCurPage()."?lang=".LANGUAGE_ID."&amp;ACT_FILE=".$strReportFile."&amp;".bitrix_sessid_get()."&amp;ACTION=CRON&amp;PROFILE_ID=0', true);"),
			);
		}

		$row->AddActions($arActions);
	}

	$rsProfiles = CCatalogImport::GetList(
		array("LAST_USE" => "DESC", "NAME" => "ASC"),
		array("FILE_NAME" => $strReportFile, '!DEFAULT_PROFILE' => 'Y')
	);

	while ($arProfile = $rsProfiles->Fetch())
	{
		$boolNeedEdit = (isset($arProfile['NEED_EDIT']) && 'Y' == $arProfile['NEED_EDIT']);

		$row = &$lAdmin->AddRow($arProfile['ID'], $arProfile);

		$row->AddViewField("NAME", htmlspecialcharsbx($arReportParams["TITLE"]));
		$row->AddViewField("FILE", $strReportFile);

		$strProfileLink = '';
		if ($bCanExec)
		{
			if ($boolNeedEdit)
			{
				$strProfileLink = '<a href="'.$APPLICATION->GetCurPage()."?lang=".LANGUAGE_ID."&amp;ACT_FILE=".urlencode($strReportFile)."&amp;ACTION=IMPORT_EDIT&amp;PROFILE_ID=".$arProfile["ID"]."&amp;".bitrix_sessid_get().'" title="'.GetMessage("CES_EDIT_PROPFILE_DESCR").'">'.htmlspecialcharsbx($arProfile["NAME"]).'</a>'.
					'<br /><i>('.GetMessage('CES_NEED_EDIT').')</i>';
			}
			else
			{
				$strProfileLink = '<a href="'.('Y' == $arProfile["IN_MENU"] ? "/bitrix/admin/cat_exec_exp.php" : $APPLICATION->GetCurPage())."?lang=".LANGUAGE_ID."&amp;ACT_FILE=".urlencode($strReportFile)."&amp;ACTION=IMPORT&amp;PROFILE_ID=".$arProfile["ID"]."&amp;".bitrix_sessid_get().'" title="'.GetMessage("export_setup_begin").'">'.htmlspecialcharsbx($arProfile["NAME"]).'</a>';
			}
		}
		else
		{
			$strProfileLink = htmlspecialcharsbx($arProfile["NAME"]);
			if ($boolNeedEdit)
			{
				$strProfileLink .= '<br><i>('.GetMessage('CES_NEED_EDIT').')</i>';
			}
		}
		$row->AddViewField('PROFILE', $strProfileLink);

		$row->AddCheckField("IN_MENU", false);
		$row->AddCheckField("IN_AGENT", false);
		$row->AddCheckField("IN_CRON", false);
		$row->AddCalendarField("USED", false);

		$strCreatedBy = '';
		$strModifiedBy = '';
		$arProfile['CREATED_BY'] = intval($arProfile['CREATED_BY']);
		if (0 < $arProfile['CREATED_BY'])
		{
			if (!array_key_exists($arProfile['CREATED_BY'], $arUserList))
			{
				$rsUsers = CUser::GetList(($by = 'ID'),($order = 'ASC'),array('ID_EQUAL_EXACT' => $arProfile['CREATED_BY']),array('FIELDS' => array('ID', 'LOGIN', 'NAME', 'LAST_NAME')));
				if ($arOneUser = $rsUsers->Fetch())
				{
					$arOneUser['ID'] = intval($arOneUser['ID']);
					$arUserList[$arOneUser['ID']] = CUser::FormatName($strNameFormat, $arOneUser);
				}
			}
			if (isset($arUserList[$arProfile['CREATED_BY']]))
				$strCreatedBy = '<a href="/bitrix/admin/user_edit.php?lang='.LANGUAGE_ID.'&ID='.$arProfile['CREATED_BY'].'">'.$arUserList[$arProfile['CREATED_BY']].'</a>';
		}
		$arProfile['MODIFIED_BY'] = intval($arProfile['MODIFIED_BY']);
		if (0 < $arProfile['MODIFIED_BY'])
		{
			if (!array_key_exists($arProfile['MODIFIED_BY'], $arUserList))
			{
				$rsUsers = CUser::GetList(($by = 'ID'),($order = 'ASC'),array('ID_EQUAL_EXACT' => $arProfile['MODIFIED_BY']),array('FIELDS' => array('ID', 'LOGIN', 'NAME', 'LAST_NAME')));
				if ($arOneUser = $rsUsers->Fetch())
				{
					$arOneUser['ID'] = intval($arOneUser['ID']);
					$arUserList[$arOneUser['ID']] = CUser::FormatName($strNameFormat, $arOneUser);
				}
			}
			if (isset($arUserList[$arProfile['MODIFIED_BY']]))
				$strModifiedBy = '<a href="/bitrix/admin/user_edit.php?lang='.LANGUAGE_ID.'&ID='.$arProfile['MODIFIED_BY'].'">'.$arUserList[$arProfile['MODIFIED_BY']].'</a>';
		}

		$row->AddViewField("CREATED_BY", $strCreatedBy);
		$row->AddCalendarField("DATE_CREATE", false);
		$row->AddViewField("MODIFIED_BY", $strModifiedBy);
		$row->AddCalendarField("TIMESTAMP_X", false);

		$arActions = array();
		if ($bCanExec)
			$arActions[] = array(
				"DEFAULT" => false,
				"TEXT" => GetMessage("CES_RUN_IMPORT"),
				"TITLE" => GetMessage("CES_RUN_IMPORT_DESCR"),
				"ACTION" => $lAdmin->ActionRedirect(('Y' == $arProfile["IN_MENU"] ? "/bitrix/admin/cat_exec_exp.php" : $APPLICATION->GetCurPage())."?lang=".LANGUAGE_ID."&ACT_FILE=".urlencode($strReportFile)."&".bitrix_sessid_get()."&ACTION=IMPORT&PROFILE_ID=".$arProfile["ID"]),
			);

		if ($bCanEdit)
		{
			$arVars = explode('&', preg_replace("/[\n\r]+/i", "", $arProfile["SETUP_VARS"]));
			foreach ($arVars as &$value)
			{
				$value = htmlspecialcharsbx(urldecode($value));
			}
			if (isset($value))
				unset($value);
			$arActions[] = array(
				"TEXT" => GetMessage("CES_SHOW_VARS_LIST"),
				"TITLE" => GetMessage("CES_SHOW_VARS_LIST_DESCR"),
				"ACTION" => "ShowVarsForm('".CUtil::JSEscape(implode('<br />', $arVars))."')",
			);
			$arActions[] = array(
				"DEFAULT" => true,
				"TEXT" => GetMessage("CES_EDIT_PROFILE"),
				"TITLE" => GetMessage("CES_EDIT_PROPFILE_DESCR"),
				"ACTION" => $lAdmin->ActionRedirect($APPLICATION->GetCurPage()."?lang=".urlencode(LANGUAGE_ID)."&ACT_FILE=".urlencode($strReportFile)."&ACTION=IMPORT_EDIT&PROFILE_ID=".$arProfile['ID']."&".bitrix_sessid_get()),
			);
			$arActions[] = array(
				"TEXT" => GetMessage("CES_COPY_PROFILE"),
				"TITLE" => GetMessage("CES_COPY_PROPFILE_DESCR"),
				"ACTION" => $lAdmin->ActionRedirect($APPLICATION->GetCurPage()."?lang=".urlencode(LANGUAGE_ID)."&ACT_FILE=".urlencode($strReportFile)."&ACTION=IMPORT_COPY&PROFILE_ID=".$arProfile['ID']."&".bitrix_sessid_get()),
			);
		}

		if (!empty($arActions))
			$arActions[] = array("SEPARATOR" => true);

		if ($bCanEdit)
		{
			if ('Y' == $arProfile["IN_MENU"])
			{
				$arActions[] = array(
					"TEXT" => GetMessage("CES_TO_LEFT_MENU_DEL"),
					"TITLE" => GetMessage("CES_TO_LEFT_MENU_DESCR_DEL"),
					"ACTION" => $lAdmin->ActionRedirect($APPLICATION->GetCurPage()."?lang=".LANGUAGE_ID."&ACT_FILE=".urlencode($strReportFile)."&".bitrix_sessid_get()."&ACTION=MENU&PROFILE_ID=".$arProfile["ID"]),
				);
			}
			else
			{
				$arActions[] = array(
					"TEXT" => GetMessage("CES_TO_LEFT_MENU"),
					"TITLE" => GetMessage("CES_TO_LEFT_MENU_DESCR"),
					"ACTION" => $lAdmin->ActionRedirect($APPLICATION->GetCurPage()."?lang=".LANGUAGE_ID."&ACT_FILE=".urlencode($strReportFile)."&".bitrix_sessid_get()."&ACTION=MENU&PROFILE_ID=".$arProfile["ID"]),
				);
			}
		}

		if ($USER->CanDoOperation('edit_php') && 'Y' != $arProfile["NEED_EDIT"])
		{
			if ('Y' == $arProfile["IN_AGENT"])
			{
				$arActions[] = array(
					"TEXT" => GetMessage("CES_TO_AGENT_DEL"),
					"TITLE" => GetMessage("CES_TO_AGENT_DESCR_DEL"),
					"ACTION" => $lAdmin->ActionRedirect($APPLICATION->GetCurPage()."?lang=".LANGUAGE_ID."&ACT_FILE=".urlencode($strReportFile)."&".bitrix_sessid_get()."&ACTION=AGENT&PROFILE_ID=".$arProfile["ID"]),
				);
			}
			else
			{
				$arActions[] = array(
					"TEXT" => GetMessage("CES_TO_AGENT"),
					"TITLE" => GetMessage("CES_TO_AGENT_DESCR"),
					"ACTION" => "ShowAgentForm('".$APPLICATION->GetCurPage()."?lang=".LANGUAGE_ID."&amp;ACT_FILE=".$strReportFile."&amp;".bitrix_sessid_get()."&amp;ACTION=AGENT&amp;PROFILE_ID=".$arProfile["ID"]."');",
				);
			}

			if ('Y' == $arProfile["IN_CRON"])
			{
				$arActions[] = array(
					"DISABLED" => $bWindowsHosting,
					"TEXT" => GetMessage("CES_TO_CRON_DEL"),
					"TITLE" => GetMessage("CES_TO_CRON_DESCR_DEL"),
					"ACTION" => ($bWindowsHosting ? '' : "ShowCronForm('".$APPLICATION->GetCurPage()."?lang=".LANGUAGE_ID."&amp;ACT_FILE=".$strReportFile."&amp;".bitrix_sessid_get()."&amp;ACTION=CRON&amp;PROFILE_ID=".$arProfile["ID"]."', false);"),
				);
			}
			else
			{
				$arActions[] = array(
					"DISABLED" => $bWindowsHosting,
					"TEXT" => GetMessage("CES_TO_CRON"),
					"TITLE" => GetMessage("CES_TO_CRON_DESCR"),
					"ACTION" => ($bWindowsHosting ? '' : "ShowCronForm('".$APPLICATION->GetCurPage()."?lang=".LANGUAGE_ID."&amp;ACT_FILE=".$strReportFile."&amp;".bitrix_sessid_get()."&amp;ACTION=CRON&amp;PROFILE_ID=".$arProfile["ID"]."', true);"),
				);
			}
		}

		if($bCanEdit)
		{
			$arActions[] = array("SEPARATOR" => true);
			$arActions[] = array(
				"TEXT" => GetMessage("CES_DELETE_PROFILE"),
				"TITLE" => GetMessage("CES_DELETE_PROFILE_DESCR"),
				"ACTION" => "if(confirm('".GetMessage("CES_DELETE_PROFILE_CONF")."')) window.location='".$APPLICATION->GetCurPage()."?lang=".LANGUAGE_ID."&ACT_FILE=".urlencode($strReportFile)."&".bitrix_sessid_get()."&ACTION=DEL_PROFILE&PROFILE_ID=".$arProfile["ID"]."';",
			);
		}
		$row->AddActions($arActions);
	}
}

$arContext = array();
if (!empty($arContextMenu))
{
	$arContext[] = array(
		"TEXT" => GetMessage("CES_ADD_PROFILE"),
		"TITLE" => GetMessage("CES_ADD_PROFILE_DESCR"),
		"ICON" => "btn_new",
		"MENU" => $arContextMenu,
	);
}
$lAdmin->AddAdminContextMenu($arContext, false);

$lAdmin->CheckListMode();

$APPLICATION->SetTitle(GetMessage("TITLE_IMPORT_PAGE"));
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
?>
<div id="form_shadow" style="display:none;" class="float-form-shadow">&nbsp;</div>
<div id="agent_form" style="display:none;" class="float-form">
<form name="agentform" id="agentform" action="" method="post">
	<table class="edit-table">
		<tbody>
	<tr>
		<td style="white-space: nowrap; font-size: 12px;"><? echo GetMessage("CES_RUN_INTERVAL"); ?></td>
		<td><input type="text" name="agent_period" value="" size="10"></td>
	</tr>
		</tbody>
		<tfoot>
	<tr>
		<td colspan="2" style="text-align: center;">
			<input type="submit" value="<? echo GetMessage("CES_SET"); ?>">&nbsp;&nbsp;<input type="button" value="<? echo GetMessage("CES_CLOSE"); ?>" onclick="HideAgentForm();">
		</td>
	</tr>
		</tfoot>
	</table>
</form>
</div>

<div id="cron_form_add" style="display:none;" class="float-form">
<form name="cronform_add" id="cronform_add" action="" method="post">
	<table class="edit-table">
	<tr>
		<td style="font-size: 12px;"><? echo GetMessage("CES_RUN_INTERVAL"); ?></td>
		<td><input type="text" name="agent_period" value="" size="10"></td>
	</tr>
	<tr>
		<td colspan="2" style="text-align: center; font-weight: bold; font-size: 12px;"><? echo GetMessage("CES_OR"); ?></td>
	</tr>
	<tr>
		<td style="font-size: 12px;"><? echo GetMessage("CES_RUN_TIME"); ?></td>
		<td style="white-space: nowrap;"><input type="text" name="agent_hour" value="" size="2"> : <input type="text" name="agent_minute" value="" size="2"></td>
	</tr>
	<tr>
		<td style="font-size: 12px;"><? echo GetMessage("CES_PHP_PATH"); ?></td>
		<td><input type="text" name="agent_php_path" value="/usr/local/php/bin/php" size="25"></td>
	</tr>
	<tr>
		<td style="font-size: 12px;"><? echo GetMessage("CES_AUTO_CRON"); ?></td>
		<td><input type="hidden" name="auto_cron_tasks" value="N"><input type="checkbox" name="auto_cron_tasks" value="Y"></td>
	</tr>
	<tr>
		<td colspan="2" style="text-align: center;">
			<input type="submit" value="<? echo GetMessage("CES_SET"); ?>">&nbsp;&nbsp;<input type="button" value="<? echo GetMessage("CES_CLOSE"); ?>" onclick="HideCronForm(true);">
		</td>
	</tr>
	</table>
</form>
</div>

<div id="cron_form_del" style="display:none;" class="float-form">
<form name="cronform_del" id="cronform_del" action="" method="post">
	<table class="edit-table">
	<tr>
		<td style="font-size: 12px;"><? echo GetMessage("CES_AUTO_CRON_DEL"); ?></td>
		<td><input type="hidden" name="auto_cron_tasks" value="N"><input type="checkbox" name="auto_cron_tasks" value="Y"></td>
	</tr>
	<tr>
		<td colspan="2" style="text-align: center;">
			<input type="submit" value="<? echo GetMessage("CES_DELETE"); ?>">&nbsp;&nbsp;<input type="button" value="<? echo GetMessage("CES_CLOSE"); ?>" onclick="HideCronForm(false);">
		</td>
	</tr>
	</table>
</form>
</div>

<div id="vars_div" style="display:none;" class="float-form">
<div id="vars_div_cont" class="data" style="font-size: 12px;">
</div>
<div style="text-align: center;">
	<input type="button" value="<? echo GetMessage("CES_CLOSE"); ?>" onclick="HideVarsForm();">
</div>
</div>
<?
if (strlen($strErrorMessage) > 0)
	CAdminMessage::ShowMessage(array("MESSAGE"=>GetMessage("CES_ERRORS"), "DETAILS"=>$strErrorMessage));

if ($_GET["success_import"]=="Y")
{
	CAdminMessage::ShowNote(GetMessage("CES_SUCCESS"));
	if (strlen($_SESSION["COMMERCEML_IMPORT_".intval($message_sess_id)]) > 0)
	{
		echo "<p>".$_SESSION["COMMERCEML_IMPORT_".intval($message_sess_id)]."</p>";
		unset($_SESSION["COMMERCEML_IMPORT_".intval($message_sess_id)]);
	}
}
?>
<script type="text/javascript">
function ShowDiv(div, shadow)
{
	var obDiv = BX(div);
	var obShadow = BX(shadow);
	if (!!obDiv && !!obShadow)
	{
		var obCoord = BX.GetWindowSize();
		BX.style(obDiv, 'display', 'block');
		BX.style(obShadow, 'display', 'block');

		var l = parseInt(obCoord.scrollLeft + obCoord.innerWidth/2 - obDiv.offsetWidth/2);
		var t = parseInt(obCoord.scrollTop + obCoord.innerHeight/2 - obDiv.offsetHeight/2);

		BX.adjust(obDiv, {style: {left: l + "px", top: t + "px"}});
		BX.adjust(obShadow, {style: {left: (l+4) + "px", top: (t+4) + "px", width: obDiv.offsetWidth + 'px', height: obDiv.offsetHeight + 'px'}});
	}
}

function HideDiv(div, shadow)
{
	var obDiv = BX(div);
	var obShadow = BX(shadow);
	if (!!obDiv && !!obShadow)
	{
		BX.style(obDiv, 'display', 'none');
		BX.style(obShadow, 'display', 'none');
	}
}

function SetForm(form, strAction)
{
	var obForm = BX(form);
	if (!!obForm)
	{
		obForm.action = strAction;
		var obTbl = BX.findChild(obForm, {tag: 'table', className: 'edit-table'}, false, false);
		if (!!obTbl)
		{
			var n = obTbl.tBodies[0].rows.length;
			for (var i=0; i<n; i++)
			{
				if (obTbl.tBodies[0].rows[i].cells.length > 1)
				{
					BX.addClass(obTbl.rows[i].cells[0], 'adm-detail-content-cell-l');
					BX.addClass(obTbl.rows[i].cells[1], 'adm-detail-content-cell-r');
				}
			}
		}
		BX.adminFormTools.modifyFormElements(obTbl);
		return true;
	}
	else
	{
		return false;
	}
}

function ShowAgentForm(strAction)
{
	if (SetForm('agentform', strAction))
	{
		ShowDiv('agent_form', 'form_shadow');
	}
}

function HideAgentForm()
{
	HideDiv('agent_form', 'form_shadow');
}

function ShowCronForm(strAction, boolAdd)
{
	if (boolAdd)
	{
		if (SetForm('cronform_add', strAction))
		{
			ShowDiv('cron_form_add', 'form_shadow');
		}
	}
	else
	{
		if (SetForm('cronform_del', strAction))
		{
			ShowDiv('cron_form_del', 'form_shadow');
		}
	}
}

function HideCronForm(boolAdd)
{
	if (boolAdd)
	{
		HideDiv('cron_form_add', 'form_shadow');
	}
	else
	{
		HideDiv('cron_form_del', 'form_shadow');
	}
}

function ShowVarsForm(strData)
{
	var obDivCont = BX('vars_div_cont');
	if (!!obDivCont)
	{
		BX.adjust(obDivCont, { html: (!!strData.length && 0 < strData.length ? strData : ' ')});
		ShowDiv('vars_div', 'form_shadow');
	}
}

function HideVarsForm()
{
	HideDiv('vars_div', 'form_shadow');
}
</script>
<?
$lAdmin->DisplayList();

echo BeginNote();
	echo GetMessage("import_setup_cat")?> <?echo CATALOG_PATH2IMPORTS;?><br><br>
	<?echo GetMessage("CES_NOTES1");?><br><br>
	<?if ($bWindowsHosting):?>
		<b><?echo GetMessage("CES_NOTES2");?></b>
	<?else:?>
		<?echo GetMessage("CES_NOTES3");?>
		<b><?echo $_SERVER["DOCUMENT_ROOT"];?>/bitrix/crontab/crontab.cfg</b>
		<?echo GetMessage("CES_NOTES4");?><br>
		<?echo GetMessage("CES_NOTES5");?><br>
		<b>crontab <?echo $_SERVER["DOCUMENT_ROOT"];?>/bitrix/crontab/crontab.cfg</b><br>
		<?echo GetMessage("CES_NOTES6");?><br>
		<b>crontab -l</b><br>
		<?echo GetMessage("CES_NOTES7");?><br>
		<b>crontab -r</b><br><br>
		<?
		$arRetval = array();
		@exec("crontab -l", $arRetval);
		if (is_array($arRetval) && count($arRetval)>0)
		{
			?>
			<?echo GetMessage("CES_NOTES8");?><br>
			<textarea name="crontasks" cols="70" rows="5" wrap="off" readonly>
			<?
			for ($i = 0; $i < count($arRetval); $i++)
			{
				echo $arRetval[$i]."\n";
			}
			?>
			</textarea><br>
			<?
		}
		echo GetMessage("CES_NOTES10");?><br><br>
		<?echo GetMessage("CES_NOTES11");?><br>
		<?echo $_SERVER["DOCUMENT_ROOT"];?>/bitrix/php_interface/include/catalog_import/cron_frame.php<br>
		<?echo GetMessage("CES_NOTES12");?>
	<?endif;

echo EndNote();

require($DOCUMENT_ROOT."/bitrix/modules/main/include/epilog_admin.php");
?>