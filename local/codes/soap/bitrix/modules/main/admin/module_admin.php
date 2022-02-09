<?
##############################################
# Bitrix Site Manager                        #
# Copyright (c) 2002-2007 Bitrix             #
# http://www.bitrixsoft.com                  #
# admin@bitrixsoft.com                       #
##############################################
require_once(dirname(__FILE__)."/../include/prolog_admin_before.php");
define("HELP_FILE", "settings/module_admin.php");

if(!$USER->CanDoOperation('edit_other_settings') && !$USER->CanDoOperation('view_other_settings'))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$isAdmin = $USER->CanDoOperation('edit_other_settings');

IncludeModuleLangFile(__FILE__);

$id = $_REQUEST["id"];

$arModules = array();
function OnModuleInstalledEvent($id)
{
	$db_events = GetModuleEvents("main", "OnModuleInstalled");
	while ($arEvent = $db_events->Fetch())
		ExecuteModuleEventEx($arEvent, array($id));
}

//Получаем список подпапок в папке modules
$handle=@opendir($DOCUMENT_ROOT.BX_ROOT."/modules");
if($handle)
{
	while (false !== ($dir = readdir($handle)))
	{
		if(is_dir($DOCUMENT_ROOT.BX_ROOT."/modules/".$dir) && $dir!="." && $dir!=".." && $dir!="main" && strpos($dir, ".") === false)
		{
			$module_dir = $DOCUMENT_ROOT.BX_ROOT."/modules/".$dir;
			if($info = CModule::CreateModuleObject($dir))
			{
				$arModules[$dir]["MODULE_ID"] = $info->MODULE_ID;
				$arModules[$dir]["MODULE_NAME"] = $info->MODULE_NAME;
				$arModules[$dir]["MODULE_DESCRIPTION"] = $info->MODULE_DESCRIPTION;
				$arModules[$dir]["MODULE_VERSION"] = $info->MODULE_VERSION;
				$arModules[$dir]["MODULE_VERSION_DATE"] = $info->MODULE_VERSION_DATE;
				$arModules[$dir]["MODULE_SORT"] = $info->MODULE_SORT;
				$arModules[$dir]["MODULE_PARTNER"] = (strpos($dir, ".") !== false) ? $info->PARTNER_NAME : "";
				$arModules[$dir]["MODULE_PARTNER_URI"] = (strpos($dir, ".") !== false) ? $info->PARTNER_URI : "";
				$arModules[$dir]["IsInstalled"] = $info->IsInstalled();
			}
		}
	}
	closedir($handle);
}

uasort($arModules, create_function('$a, $b', 'if($a["MODULE_SORT"] == $b["MODULE_SORT"]) return strcasecmp($a["MODULE_NAME"], $b["MODULE_NAME"]); return ($a["MODULE_SORT"] < $b["MODULE_SORT"])? -1 : 1;'));

$fb = ($id == 'fileman' && !$USER->CanDoOperation('fileman_install_control'));
if($isAdmin && !$fb && check_bitrix_sessid())
{
	if(strlen($uninstall)>0 || strlen($install)>0)
	{
		$id = str_replace("\\", "", str_replace("/", "", $id));
		if($Module = CModule::CreateModuleObject($id))
		{
			if($Module->IsInstalled() && strlen($uninstall)>0)
			{
				OnModuleInstalledEvent($id);
				$Module->DoUninstall();
				LocalRedirect($APPLICATION->GetCurPage()."?lang=".LANGUAGE_ID);
			}
			elseif(!$Module->IsInstalled() && strlen($install) > 0)
			{
				if (strtolower($DB->type)=="mysql" && defined("MYSQL_TABLE_TYPE") && strlen(MYSQL_TABLE_TYPE)>0)
				{
					$DB->Query("SET storage_engine = '".MYSQL_TABLE_TYPE."'", true);
				}

				OnModuleInstalledEvent($id);
				$Module->DoInstall();
				LocalRedirect($APPLICATION->GetCurPage()."?lang=".LANG);
			}
		}
	}
	elseif(isset($_REQUEST["action"]) && $_REQUEST["action"] == "version_down")
	{
		require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_js.php");

		if($_REQUEST["id"] == "main")
			$fn = $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/version.php";
		else
			$fn = $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".preg_replace("/[^a-z0-9.]/", "", $_REQUEST["id"])."/install/version.php";

		if(file_exists($fn) && is_file($fn))
		{
			$fc = file_get_contents($fn);
			if(preg_match("/(\\d+)\\.(\\d+)\\.(\\d+)/", $fc, $match))
			{
				if($match[3] > 20)
					$match[3] -= 10;
				elseif($match[3] > 0)
					$match[3] -= 1;
				else
				{
					$match[3] = 99;
					if($match[2] == 5)
						$match[2] = 0;
					else
					{
						$match[2] = 5;
						$match[1] -= 1;
					}
				}

				if($match[1] > 0 && $match[2] >= 0 && $match[3] >= 0)
				{
					$fc = str_replace($match[0], $match[1].".".$match[2].".".$match[3], $fc);
					file_put_contents($fn, $fc);
					bx_accelerator_reset();
				}
				echo $match[1].".".$match[2].".".$match[3];
			}
		}

		require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin_js.php");
	}
}

$APPLICATION->SetTitle(GetMessage("TITLE"));
require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_admin_after.php");
?>
<style>
.no-select {-moz-user-select: none; -khtml-user-select: none; user-select: none;}
</style>
<script>
function DoAction(oEvent, action, module_id)
{
	if (oEvent.ctrlKey || BX.browser.IsMac() && oEvent.altKey)
	{
		BX('version_for_' + module_id).className = 'no-select';
		if(action == 'version_down')
		{
			ShowWaitWindow();
			BX.ajax.post(
				'module_admin.php?lang=<?echo LANGUAGE_ID?>&id='+module_id+'&<?echo bitrix_sessid_get()?>&action='+action,
				null,
				function(result){
					CloseWaitWindow();
					BX('version_for_' + module_id).className = '';
					if(result.length > 0)
						BX('version_for_' + module_id).innerHTML = result;
				}
			);
		}
	}
}
</script>
<table border="0" cellspacing="0" cellpadding="0" width="100%" class="list-table">
	<tr class="heading">
		<td width="60%"><b><?echo GetMessage("MOD_NAME")?></b></td>
		<td><b><?echo GetMessage("MOD_VERSION")?></b></td>
		<td><b><?echo GetMessage("MOD_DATE_UPDATE")?></b></td>
		<td><b><?echo GetMessage("MOD_SETUP")?></b></td>
		<td><b><?echo GetMessage("MOD_ACTION")?></b></td>
	</tr>
	<tr>
		<td><b><?=GetMessage("MOD_MAIN_MODULE")?></b><br><?
		$str = str_replace("#A1#","<a  href='sysupdate.php?lang=".LANG."'>",GetMessage("MOD_MAIN_DESCRIPTION"));
		$str = str_replace("#A2#","</a>",$str);
		echo $str;?></td>
		<td ondblclick="<?echo htmlspecialcharsbx("DoAction(event, 'version_down', 'main')")?>" id="version_for_main"><?echo SM_VERSION;?></td>
		<td nowrap><?echo CDatabase::FormatDate(SM_VERSION_DATE, "YYYY-MM-DD HH:MI:SS", CLang::GetDateFormat("SHORT"));?></td>
		<td><?=GetMessage("MOD_INSTALLED")?></td>
		<td>&nbsp;</td>
	</tr>
<?
foreach($arModules as $info) :
?>
	<tr>
		<td><b><?echo htmlspecialcharsex($info["MODULE_NAME"])?></b> <?echo htmlspecialcharsex(strlen($info["MODULE_PARTNER"]) > 0? " <b><i>(".str_replace(array("#NAME#", "#URI#"), array($info["MODULE_PARTNER"], $info["MODULE_PARTNER_URI"]), GetMessage("MOD_PARTNER_NAME")).")</i></b>" : "(".$info["MODULE_ID"].")") ?><br><?echo $info["MODULE_DESCRIPTION"]?></td>
		<td ondblclick="<?echo htmlspecialcharsbx("DoAction(event, 'version_down', '".CUtil::AddSlashes($info["MODULE_ID"])."')")?>" id="version_for_<?echo htmlspecialcharsbx($info["MODULE_ID"])?>"><?echo $info["MODULE_VERSION"]?></td>
		<td nowrap><?echo CDatabase::FormatDate($info["MODULE_VERSION_DATE"], "YYYY-MM-DD HH:MI:SS", CLang::GetDateFormat("SHORT"));?></td>
		<td nowrap><?if($info["IsInstalled"]):?><?echo GetMessage("MOD_INSTALLED")?><?else:?><span class="required"><?echo GetMessage("MOD_NOT_INSTALLED")?></span><?endif?></td>
		<td>
			<form action="<?echo $APPLICATION->GetCurPage()?>" method="GET" id="form_for_<?echo htmlspecialcharsbx($info["MODULE_ID"])?>">
				<input type="hidden" name="action" value="" id="action_for_<?echo htmlspecialcharsbx($info["MODULE_ID"])?>">
				<input type="hidden" name="lang" value="<?echo LANG?>">
				<input type="hidden" name="id" value="<?echo htmlspecialcharsbx($info["MODULE_ID"])?>">
				<?=bitrix_sessid_post()?>
				<?if($info["IsInstalled"]):?>
					<input <?if (!$isAdmin || $info["MODULE_ID"] == 'fileman' || $info["MODULE_ID"] == 'intranet') echo "disabled" ?> type="submit" name="uninstall" value="<?echo GetMessage("MOD_DELETE")?>">
				<?else:?>
					<input <?if (!$isAdmin) echo "disabled" ?> type="submit" class="adm-btn-green" name="install" value="<?echo GetMessage("MOD_INSTALL_BUTTON")?>">
				<?endif?>
			</form>
		</td>
	</tr>
	<tr style="display: none;"><td colspan="5"></td></tr>
<?
endforeach;
?>
</table>
<?
require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");
?>
