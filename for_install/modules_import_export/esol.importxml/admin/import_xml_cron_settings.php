<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
$moduleId = 'esol.importxml';
CModule::IncludeModule('iblock');
CModule::IncludeModule($moduleId);
IncludeModuleLangFile(__FILE__);

$suffix = '';
$cronFrame = 'cron_frame.php';
if($_GET['suffix']=='highload') 
{
	$suffix = 'highload';
	$cronFrame = 'cron_frame_highload.php';
}

define("ESOL_IX_PATH2EXPORTS", "/bitrix/php_interface/include/".$moduleId."/");

if ($_REQUEST["action"]=="save")
{
	$strErrorMessage = $strSuccessMessage = '';
	
	if (strlen($PROFILE_ID) < 1)
	{
		$strErrorMessage .= GetMessage("ESOL_IX_CRON_NOT_PROFILE")."\n";
	}

	if (strlen($strErrorMessage)<=0 && $_REQUEST["subaction"]=='add')
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
		if (strlen($agent_php_path)<=0) $agent_php_path = "/usr/bin/php";

		$cfg_data = "";
		if (file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/crontab/crontab.cfg"))
		{
			$cfg_file_size = filesize($_SERVER["DOCUMENT_ROOT"]."/bitrix/crontab/crontab.cfg");
			$fp = fopen($_SERVER["DOCUMENT_ROOT"]."/bitrix/crontab/crontab.cfg", "rb");
			$cfg_data = fread($fp, $cfg_file_size);
			fclose($fp);
		}

		CheckDirPath($_SERVER["DOCUMENT_ROOT"].ESOL_IX_PATH2EXPORTS."logs/");
		if (strlen($PROFILE_ID) > 0)
		{
			if ($agent_period>0)
			{
				$strTime = "0 */".$agent_period." * * * ";
			}
			else
			{
				$strTime = intval($agent_minute)." ".intval($agent_hour)." * * * ";
			}

			// add
			if (strlen($cfg_data)>0) $cfg_data .= "\n";
			$execFile = $_SERVER["DOCUMENT_ROOT"].ESOL_IX_PATH2EXPORTS.$cronFrame;
			$logFile = $_SERVER["DOCUMENT_ROOT"].ESOL_IX_PATH2EXPORTS."logs/".$PROFILE_ID.".txt";
			if(\Bitrix\EsolImportxml\Utils::getSiteEncoding()=='utf-8') $phpParams = '-d mbstring.func_overload=2 -d mbstring.internal_encoding=UTF-8';
			else $phpParams = '-d mbstring.func_overload=0 -d mbstring.internal_encoding=CP1251';
			$cfg_subdata = $strTime.$agent_php_path." ".$phpParams." -f ".$execFile." ".$PROFILE_ID." >".$logFile."\n";
			$cfg_data .= $cfg_subdata;
			$strSuccessMessage .= GetMessage("ESOL_IX_CRON_PANEL_CONFIG")."<br><br><i>".$cfg_subdata.'</i><br><br>';
			$strSuccessMessage .= GetMessage("ESOL_IX_CRON_WHERE_IS")."<br>";
			$strSuccessMessage .= '<i>'.$strTime.'</i> - '.GetMessage("ESOL_IX_CRON_TIME_EXECUTE_COMMENT")."<br>";
			$strSuccessMessage .= '<i>'.$agent_php_path.'</i> - '.GetMessage("ESOL_IX_CRON_PHP_PATH_COMMENT")."<br>";
			$strSuccessMessage .= '<i>'.$execFile.'</i> - '.GetMessage("ESOL_IX_CRON_EXEC_FILE_COMMENT")."<br>";
			$strSuccessMessage .= '<i>'.$PROFILE_ID.'</i> - '.GetMessage("ESOL_IX_CRON_PROFILE_ID_COMMENT")."<br>";
			$strSuccessMessage .= '<i>'.$logFile.'</i> - '.GetMessage("ESOL_IX_CRON_LOG_FILE_COMMENT")."<br>";
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
				/*if (intval($return_var)!=0)
				{
					$strErrorMessage .= GetMessage("CES_ERROR_ADD2CRON")." \n";
					if (is_array($arRetval) && !empty($arRetval))
					{
						$strErrorMessage .= implode("\n", $arRetval)."\n";
					}
					else
					{
						$strErrorMessage .= GetMessage("CES_ERROR_UNKNOWN")."\n";
					}
				}*/
			}
		}
	}
	
	if (strlen($strErrorMessage)<=0 && $_REQUEST["subaction"]=='delete')
	{
		$cfg_data = "";
		if (file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/crontab/crontab.cfg"))
		{
			$cfg_file_size = filesize($_SERVER["DOCUMENT_ROOT"]."/bitrix/crontab/crontab.cfg");
			$fp = fopen($_SERVER["DOCUMENT_ROOT"]."/bitrix/crontab/crontab.cfg", "rb");
			$cfg_data = fread($fp, $cfg_file_size);
			fclose($fp);

			$cfg_data = preg_replace("#^.*?".preg_quote(ESOL_IX_PATH2EXPORTS).$cronFrame." +".$PROFILE_ID." *>.*?$#im", "", $cfg_data);

			$cfg_data = preg_replace("#[\r\n]{2,}#im", "\n", $cfg_data);
			$fp = fopen($_SERVER["DOCUMENT_ROOT"]."/bitrix/crontab/crontab.cfg", "wb");
			fwrite($fp, $cfg_data);
			fclose($fp);

			$arRetval = array();
			@exec("crontab ".$_SERVER["DOCUMENT_ROOT"]."/bitrix/crontab/crontab.cfg", $arRetval, $return_var);
			/*if (intval($return_var)!=0)
			{
				$strErrorMessage .= GetMessage("CES_ERROR_ADD2CRON")." \n";
				if (is_array($arRetval) && !empty($arRetval))
				{
					$strErrorMessage .= implode("\n", $arRetval)."\n";
				}
				else
				{
					$strErrorMessage .= GetMessage("CES_ERROR_UNKNOWN")."\n";
				}
			}*/
		}
	}
	
	$APPLICATION->RestartBuffer();
	if(ob_get_contents()) ob_end_clean();
		
	if($strErrorMessage)
	{
		CAdminMessage::ShowMessage(array(
			'TYPE' => 'ERROR',
			'MESSAGE' => $strErrorMessage,
			'HTML' => true
		));
	}
	else 
	{
		CAdminMessage::ShowMessage(array(
			'TYPE' => 'OK',
			'MESSAGE' => GetMessage("ESOL_IX_CRON_SAVE_SUCCESS"),
			'DETAILS' => $strSuccessMessage,
			'HTML' => true
		));
	}	
	die();
}
/*$obJSPopup = new CJSPopup();
$obJSPopup->ShowTitlebar(GetMessage("ESOL_IX_CRON_TITLE"));*/

$oProfile = new \Bitrix\EsolImportxml\Profile($suffix);
$arProfiles = $oProfile->GetList();
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_popup_admin.php");
?>
<form action="<?=$_SERVER['REQUEST_URI']?>" method="post" enctype="multipart/form-data" name="field_settings">
	<input type="hidden" name="action" value="save">
	<div id="esol-ix-cron-result"></div>
	<table width="100%">
		<!--<tr class="heading">
			<td colspan="2"><?echo GetMessage("ESOL_IX_CRON_PROFILE_TITLE"); ?></td>
		</tr>-->
		<tr>
			<td class="adm-detail-content-cell-l"><?echo GetMessage("ESOL_IX_CRON_CHOOSE_PROFILE");?>:</td>
			<td class="adm-detail-content-cell-r">
				<select name="PROFILE_ID" onchange="/*EProfile.Choose(this)*/"><?
					?><option value=""><?echo GetMessage("ESOL_IX_CRON_NO_PROFILE"); ?></option><?
					foreach($arProfiles as $k=>$profile)
					{
						?><option value="<?echo $k;?>"><?echo $profile; ?></option><?
					}
				?></select>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<table class="edit-table" align="center">
				<tr>
					<td style="font-size: 12px;"><? echo GetMessage("ESOL_IX_CRON_RUN_INTERVAL"); ?></td>
					<td><input type="text" name="agent_period" value="" size="10"></td>
				</tr>
				<tr>
					<td colspan="2" style="text-align: center; font-weight: bold; font-size: 12px;"><? echo GetMessage("ESOL_IX_CRON_OR"); ?></td>
				</tr>
				<tr>
					<td style="font-size: 12px;"><? echo GetMessage("ESOL_IX_CRON_RUN_TIME"); ?></td>
					<td style="white-space: nowrap;"><input type="text" name="agent_hour" value="" size="2"> : <input type="text" name="agent_minute" value="" size="2"></td>
				</tr>
				<tr>
					<td style="font-size: 12px;"><? echo GetMessage("ESOL_IX_CRON_PHP_PATH"); ?> <span id="hint_CRON_PHP_PATH"></span><script>BX.hint_replace(BX('hint_CRON_PHP_PATH'), '<?echo GetMessage("ESOL_IX_CRON_PHP_PATH_HINT"); ?>');</script></td>
					<td><input type="text" name="agent_php_path" value="/usr/bin/php" size="25"></td>
				</tr>
				<tr>
					<td style="font-size: 12px;"><? echo GetMessage("ESOL_IX_CRON_AUTO_CRON"); ?> <span id="hint_CRON_AUTO_CRON"></span><script>BX.hint_replace(BX('hint_CRON_AUTO_CRON'), '<?echo GetMessage("ESOL_IX_CRON_AUTO_CRON_HINT"); ?>');</script></td>
					<td><input type="hidden" name="auto_cron_tasks" value="N"><input type="checkbox" name="auto_cron_tasks" value="Y"></td>
				</tr>
				<tr>
					<td colspan="2" style="text-align: center;">
						<input type="submit" name="delete" value="<? echo GetMessage("ESOL_IX_CRON_UNSET"); ?>" onclick="return EProfile.SaveCron(this);">
						<input type="submit" name="add" value="<? echo GetMessage("ESOL_IX_CRON_SET"); ?>" onclick="return EProfile.SaveCron(this);">
					</td>
				</tr>
				</table>
			</td>
		</tr>
	</table>
	
	<?echo BeginNote();?>
		<? echo GetMessage("ESOL_IX_CRON_DESCRIPTION"); ?>
	<?echo EndNote();?>
</form>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_popup_admin.php");?>