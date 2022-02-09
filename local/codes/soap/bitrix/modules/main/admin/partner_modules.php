<?
##############################################
# Bitrix Site Manager                        #
# Copyright (c) 2002-2007 Bitrix             #
# http://www.bitrixsoft.com                  #
# admin@bitrixsoft.com                       #
##############################################
require_once(dirname(__FILE__)."/../include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/update_client_partner.php");
define("HELP_FILE", "settings/module_admin.php");

if(!$USER->CanDoOperation('edit_other_settings') && !$USER->CanDoOperation('view_other_settings'))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$isAdmin = $USER->CanDoOperation('edit_other_settings');

IncludeModuleLangFile(__FILE__);

$id = $_REQUEST["id"];
$mod = $_REQUEST["mod"];
$resultMod = $_REQUEST["result"];

$arModules = array();
function OnModuleInstalledEvent($id)
{
	$db_events = GetModuleEvents("main", "OnModuleInstalled");
	while ($arEvent = $db_events->Fetch())
		ExecuteModuleEventEx($arEvent, array($id));
}

$handle=@opendir($DOCUMENT_ROOT.BX_ROOT."/modules");
if($handle)
{
	while (false !== ($dir = readdir($handle)))
	{
		if(is_dir($DOCUMENT_ROOT.BX_ROOT."/modules/".$dir) && $dir!="." && $dir!=".." && strpos($dir, ".") !== false)
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
				$arModules[$dir]["MODULE_PARTNER"] = $info->PARTNER_NAME;
				$arModules[$dir]["MODULE_PARTNER_URI"] = $info->PARTNER_URI;
				$arModules[$dir]["IsInstalled"] = $info->IsInstalled();
				if(defined(str_replace(".", "_", $info->MODULE_ID)."_DEMO"))
				{
					$arModules[$dir]["DEMO"] = "Y";
					if($info->IsInstalled())
					{
						if(CModule::IncludeModuleEx($info->MODULE_ID) != MODULE_DEMO_EXPIRED)
						{
							$arModules[$dir]["DEMO_DATE"] = ConvertTimeStamp($GLOBALS["SiteExpireDate_".str_replace(".", "_", $info->MODULE_ID)], "SHORT");
						}
						else
							$arModules[$dir]["DEMO_END"] = "Y";
					}
				}
			}
		}
	}
	closedir($handle);
}
uasort($arModules, create_function('$a, $b', 'if($a["MODULE_SORT"] == $b["MODULE_SORT"]) return strcasecmp($a["MODULE_NAME"], $b["MODULE_NAME"]); return ($a["MODULE_SORT"] < $b["MODULE_SORT"])? -1 : 1;'));

$stableVersionsOnly = COption::GetOptionString("main", "stable_versions_only", "Y");
$arRequestedModules = CUpdateClientPartner::GetRequestedModules("");

$arUpdateList = CUpdateClientPartner::GetUpdatesList($errorMessage, LANG, $stableVersionsOnly, $arRequestedModules, Array("fullmoduleinfo" => "Y"));
$strError_tmp = "";
$arClientModules = CUpdateClientPartner::GetCurrentModules($strError_tmp);


$linkToBuy = false;
$linkToBuyUpdate = false;
if(LANGUAGE_ID == "ru")
{
	$linkToBuy = "http://marketplace.1c-bitrix.ru"."/tobasket.php?ID=#CODE#";
	$linkToBuyUpdate = "http://marketplace.1c-bitrix.ru"."/tobasket.php?ID=#CODE#&lckey=".md5("BITRIX".CUpdateClientPartner::GetLicenseKey()."LICENCE");
}
	
$bHaveNew = false;
$modules = Array();
$modulesNew = Array();
if(!empty($arUpdateList["MODULE"]))
{
	foreach($arUpdateList["MODULE"] as $k => $v)
	{
		if(!array_key_exists($v["@"]["ID"], $arClientModules))
		{
			$bHaveNew = true;
			$modulesNew[] = Array(
					"NAME" => $v["@"]["NAME"],
					"ID" => $v["@"]["ID"],
					"DESCRIPTION" => $v["@"]["DESCRIPTION"],
					"PARTNER" => $v["@"]["PARTNER_NAME"],
					"FREE_MODULE" => $v["@"]["FREE_MODULE"],
					"DATE_FROM" => $v["@"]["DATE_FROM"],
					"DATE_TO" => $v["@"]["DATE_TO"],
					"UPDATE_END" => $v["@"]["UPDATE_END"],
				);
		}
		else
		{
			$modules[$v["@"]["ID"]] = Array(
					"VERSION" => (isset($v["#"]["VERSION"]) ? $v["#"]["VERSION"][count($v["#"]["VERSION"]) - 1]["@"]["ID"] : ""),
					"FREE_MODULE" => $v["@"]["FREE_MODULE"],
					"DATE_FROM" => $v["@"]["DATE_FROM"],
					"DATE_TO" => $v["@"]["DATE_TO"],
					"UPDATE_END" => $v["@"]["UPDATE_END"],
				);
		}
	}
}

$errorMessage = "";
$errorMessageFull = "";
$fb = ($id == 'fileman' && !$USER->CanDoOperation('fileman_install_control'));
if((strlen($uninstall)>0 || strlen($install)>0 || strlen($clear)>0) && $isAdmin && !$fb && check_bitrix_sessid())
{
	$id = str_replace("\\", "", str_replace("/", "", $id));
	if($Module = CModule::CreateModuleObject($id))
	{
		if($Module->IsInstalled() && strlen($uninstall)>0)
		{
			OnModuleInstalledEvent($id);
			if($Module->DoUninstall() !== false)
			{
				LocalRedirect($APPLICATION->GetCurPage()."?lang=".LANGUAGE_ID."&mod=".$id."&result=DELOK");
			}
			else
			{
				$errorMessage = GetMessage("MOD_UNINSTALL_ERROR", Array("#CODE#" => $id));
				if($e = $APPLICATION->GetException())
					$errorMessageFull = $e->GetString();
			}
		}
		elseif(!$Module->IsInstalled() && strlen($install) > 0)
		{
			if (strtolower($DB->type)=="mysql" && defined("MYSQL_TABLE_TYPE") && strlen(MYSQL_TABLE_TYPE)>0)
			{
				$DB->Query("SET storage_engine = '".MYSQL_TABLE_TYPE."'", true);
			}

			OnModuleInstalledEvent($id);
			if($Module->DoInstall() !== false)
			{
				LocalRedirect($APPLICATION->GetCurPage()."?lang=".LANGUAGE_ID."&mod=".$id."&result=OK");
			}
			else
			{
				$errorMessage = GetMessage("MOD_INSTALL_ERROR", Array("#CODE#" => $id));
				if($e = $APPLICATION->GetException())
					$errorMessageFull = $e->GetString();
			}

		}
		elseif(!$Module->IsInstalled() && strlen($clear) > 0)
		{
			if(strlen($Module->MODULE_ID) > 0 && is_dir($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$Module->MODULE_ID."/"))
			{
				DeleteDirFilesEx("/bitrix/modules/".$Module->MODULE_ID."/");
				LocalRedirect($APPLICATION->GetCurPage()."?lang=".LANGUAGE_ID."&mod=".$id."&result=CLEAROK");
			}
		}
	}
}

$sTableID = "upd_partner_modules_all";
$lAdmin = new CAdminList($sTableID);

$sTableID1 = "upd_partner_modules_new";
$lAdmin1 = new CAdminList($sTableID1);

$lAdmin->BeginPrologContent();
echo "<h2>".GetMessage("MOD_SMP_AV_MOD")."</h2><p>".GetMessage("MOD_SMP_AV_MOD_TEXT1")."<br />".GetMessage("MOD_SMP_AV_MOD_TEXT2")."</p>";
$lAdmin->EndPrologContent();

$arHeaders = Array(
	array(
		"id" => "NAME",
		"content" => GetMessage("MOD_NAME"),
		"default" => true,
	),
	array(
		"id" => "PARTNER",
		"content" => GetMessage("MOD_PARTNER"),
		"default" => true,
	),
	array(
		"id" => "VERSION",
		"content" => GetMessage("MOD_VERSION"),
		"default" => true,
	),
	array(
		"id" => "DATE_UPDATE",
		"content" => GetMessage("MOD_DATE_UPDATE"),
		"default" => true,
	),
	array(
		"id" => "DATE_TO",
		"content" => GetMessage("MOD_DATE_TO"),
		"default" => true,
	),
	array(
		"id" => "STATUS",
		"content" => GetMessage("MOD_SETUP"),
		"default" => true,
	),
);

$lAdmin->AddHeaders($arHeaders);

$rsData = new CDBResult;
$rsData->InitFromArray($arModules);
$rsData = new CAdminResult($rsData, $sTableID);
while($info = $rsData->Fetch())
{
	$row =& $lAdmin->AddRow($info["MODULE_ID"], $info);
	
	$name = "<b>".htmlspecialcharsbx($info["MODULE_NAME"])."</b> (".htmlspecialcharsbx($info["MODULE_ID"]).")";
	if($info["DEMO"] == "Y")
		$name .= " <span style=\"color:red;\">".GetMessage("MOD_DEMO")."</span>";
	$name .= "<br />".htmlspecialcharsbx($info["MODULE_DESCRIPTION"]);
	$row->AddViewField("NAME", $name);
	$row->AddViewField("PARTNER", ((strlen($info["MODULE_PARTNER"]) > 0) ? " ".str_replace(array("#NAME#", "#URI#"), array($info["MODULE_PARTNER"], $info["MODULE_PARTNER_URI"]), GetMessage("MOD_PARTNER_NAME"))."" : "&nbsp;"));
	$row->AddViewField("VERSION", $info["MODULE_VERSION"]);
	$row->AddViewField("DATE_UPDATE", CDatabase::FormatDate($info["MODULE_VERSION_DATE"], "YYYY-MM-DD HH:MI:SS", CLang::GetDateFormat("SHORT")));
	if($modules[$info["MODULE_ID"]]["FREE_MODULE"] != "Y")
	{
		if($info["DEMO"] == "Y")
		{
			if($linkToBuy)
			{
				if($info["DEMO_END"] == "Y")
					$row->AddViewField("DATE_TO", "<span class=\"required\">".GetMessage("MOD_DEMO_END")."</span><br /><a href=\"".str_replace("#CODE#", $info["MODULE_ID"], $linkToBuy)."\" target=\"_blank\">".GetMessage("MOD_UPDATE_BUY_DEMO")."</a>");
				else
					$row->AddViewField("DATE_TO", $info["DEMO_DATE"]."<br /><a href=\"".str_replace("#CODE#", $info["MODULE_ID"], $linkToBuy)."\" target=\"_blank\">".GetMessage("MOD_UPDATE_BUY_DEMO")."</a>");
			}
			else
			{
				if($info["DEMO_END"] == "Y")
					$row->AddViewField("DATE_TO", "<span class=\"required\">".GetMessage("MOD_DEMO_END")."</span>");
				else
					$row->AddViewField("DATE_TO", $info["DEMO_DATE"]);
			}
		}
		else
		{
			if($modules[$info["MODULE_ID"]]["UPDATE_END"] == "Y")
			{
				if($linkToBuy && !empty($modules[$info["MODULE_ID"]]["VERSION"]))
					$row->AddViewField("DATE_TO", "<span style=\"color:red;\">".$modules[$info["MODULE_ID"]]["DATE_TO"]."</span><br /><a href=\"".str_replace("#CODE#", $info["MODULE_ID"], $linkToBuyUpdate)."\" target=\"_blank\">".GetMessage("MOD_UPDATE_BUY")."</a>");
				else
					$row->AddViewField("DATE_TO", "<span style=\"color:red;\">".$modules[$info["MODULE_ID"]]["DATE_TO"]."</span>");
			}
			else
				$row->AddViewField("DATE_TO", $modules[$info["MODULE_ID"]]["DATE_TO"]);
		}
	}
	$status = "";
	if($info["IsInstalled"])
		$status = GetMessage("MOD_INSTALLED");
	else
		$status = "<span class=\"required\">".GetMessage("MOD_NOT_INSTALLED")."</span>";

	if(!empty($modules[$info["MODULE_ID"]]["VERSION"])) 
		$status .= "<br /><a href=\"/bitrix/admin/update_system_partner.php?tabControl_active_tab=tab2&addmodule=".$info["MODULE_ID"]."\" style=\"color:green;\">".GetMessage("MOD_SMP_NEW_UPDATES")."</a>";
	$row->AddViewField("STATUS", $status);
	
	$arActions = Array();
	if(!empty($modules[$info["MODULE_ID"]]) && !empty($modules[$info["MODULE_ID"]]["VERSION"])) 
	{
		$arActions[] = array(
			"ICON" => "",
			"DEFAULT" => true,
			"TEXT" => GetMessage("MOD_SMP_UPDATE"),
			"ACTION" => $lAdmin->ActionRedirect("/bitrix/admin/update_system_partner.php?tabControl_active_tab=tab2&addmodule=".$info["MODULE_ID"]),
		);
	}

	if($info["IsInstalled"])
	{
		$arActions[] = array(
			"ICON" => "delete",
			"DEFAULT" => false,
			"TEXT" => GetMessage("MOD_DELETE"),
			"ACTION" => $lAdmin->ActionRedirect($APPLICATION->GetCurPage()."?id=".htmlspecialcharsbx($info["MODULE_ID"])."&lang=".LANG."&uninstall=Y&".bitrix_sessid_get()),
		);
	}
	else
	{
		$arActions[] = array(
			"ICON" => "add",
			"DEFAULT" => false,
			"TEXT" => GetMessage("MOD_INSTALL_BUTTON"),
			"ACTION" => $lAdmin->ActionRedirect($APPLICATION->GetCurPage()."?id=".htmlspecialcharsbx($info["MODULE_ID"])."&lang=".LANG."&install=Y&".bitrix_sessid_get()),
		);
		$arActions[] = array(
			"ICON" => "delete",
			"DEFAULT" => false,
			"TEXT" => GetMessage("MOD_SMP_DELETE"),
			"ACTION" => "if(confirm('".GetMessageJS('MOD_CLEAR_CONFIRM', Array("#NAME#" => htmlspecialcharsbx($info["MODULE_NAME"])))."')) ".$lAdmin->ActionRedirect($APPLICATION->GetCurPage()."?id=".htmlspecialcharsbx($info["MODULE_ID"])."&lang=".LANG."&clear=Y&".bitrix_sessid_get()),
		);
	}
	$row->AddActions($arActions);
}

$lAdmin->AddFooter(
	array(
		array("title"=>GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value"=>$rsData->SelectedRowsCount()),
	)
);
$lAdmin->CheckListMode();


$lAdmin1->BeginPrologContent();
echo "<h2>".GetMessage("MOD_SMP_BUY_MOD")."</h2><p>".GetMessage("MOD_SMP_BUY_MOD_TEXT1")."<br />".GetMessage("MOD_SMP_BUY_MOD_TEXT2")."</p>";
$lAdmin1->EndPrologContent();

$arHeaders1 = Array(
	array(
		"id" => "NAME",
		"content" => GetMessage("MOD_NAME"),
		"default" => true,
	),
	array(
		"id" => "PARTNER",
		"content" => GetMessage("MOD_PARTNER"),
		"default" => true,
	),
	array(
		"id" => "DATE_TO",
		"content" => GetMessage("MOD_DATE_TO"),
		"default" => true,
	),
);
$lAdmin1->AddHeaders($arHeaders1);
$rsData = new CDBResult;
$rsData->InitFromArray($modulesNew);
$rsData = new CAdminResult($rsData, $sTableID1);

while($info = $rsData->Fetch())
{

	$row =& $lAdmin1->AddRow($info["ID"], $info);
	
	$row->AddViewField("NAME", "<b>".htmlspecialcharsbx($info["NAME"])."</b> (".htmlspecialcharsbx($info["ID"]).")<br />".htmlspecialcharsbx($info["DESCRIPTION"]));
	$row->AddViewField("PARTNER", $info["PARTNER"]);
	
	if($info["UPDATE_END"] == "Y")
	{
		if($linkToBuy)
		{
			if(strlen($info["DATE_TO"]) > 0)
				$row->AddViewField("DATE_TO", "<span style=\"color:red;\">".$info["DATE_TO"]."</span><br /><a href=\"".str_replace("#CODE#", $info["ID"], $linkToBuyUpdate)."\" target=\"_blank\">".GetMessage("MOD_UPDATE_BUY")."</a>");
			else
				$row->AddViewField("DATE_TO", "<a href=\"".str_replace("#CODE#", $info["ID"], $linkToBuyUpdate)."\" target=\"_blank\">".GetMessage("MOD_UPDATE_BUY")."</a>");
		}
		else
			$row->AddViewField("DATE_TO", "<span style=\"color:red;\">".$info["DATE_TO"]."</span>");
	}


	$arActions = Array();
	if($info["UPDATE_END"] != "Y")
	{
		$arActions[] = array(
			"ICON" => "",
			"DEFAULT" => true,
			"TEXT" => GetMessage("MOD_SMP_DOWNLOAD"),
			"ACTION" => $lAdmin1->ActionRedirect("/bitrix/admin/update_system_partner.php?tabControl_active_tab=tab2&addmodule=".$info["MODULE_ID"]),
		);
	}

	$row->AddActions($arActions);
}

$lAdmin1->AddFooter(
	array(
		array("title"=>GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value"=>$rsData->SelectedRowsCount()),
	)
);

$lAdmin1->CheckListMode();


$APPLICATION->SetTitle(GetMessage("TITLE"));
require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_admin_after.php");


if(strlen($mod) > 0 && $resultMod == "OK")
{
	CAdminMessage::ShowNote(GetMessage("MOD_SMP_INSTALLED", Array("#MODULE_NAME#" => $arModules[$mod]["MODULE_NAME"])));
}
elseif(strlen($mod) > 0 && $resultMod == "DELOK")
{
	CAdminMessage::ShowNote(GetMessage("MOD_SMP_UNINSTALLED", Array("#MODULE_NAME#" => $arModules[$mod]["MODULE_NAME"])));
}
elseif(strlen($mod) > 0 && $resultMod == "CLEAROK")
{
	CAdminMessage::ShowNote(GetMessage("MOD_SMP_DELETED", Array("#MODULE_NAME#" => $mod)));
}

if(strlen($errorMessage) > 0)
{
	CAdminMessage::ShowMessage(Array("DETAILS" => $errorMessageFull, "TYPE" => "ERROR", "MESSAGE" => $errorMessage, "HTML" => true));
}

if($bHaveNew)
{
	$lAdmin1->DisplayList();
}
	
$lAdmin->DisplayList();

require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");
?>
