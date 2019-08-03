<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/prolog.php");

if(!$USER->CanDoOperation('view_event_log'))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

IncludeModuleLangFile(__FILE__);
$MODULE_ID = 'esol.importxml';
CModule::IncludeModule($MODULE_ID);
CModule::IncludeModule('iblock');
$logger = new \Bitrix\EsolImportxml\Logger(false);

$oProfile = new \Bitrix\EsolImportxml\Profile();
$arProfiles = $oProfile->GetList();

$sTableID = "tbl_esol_importxml_event_log";
$oSort = new CAdminSorting($sTableID, "ID", "DESC");
$lAdmin = new CAdminList($sTableID, $oSort);

$arFilterFields = array(
	"find",
	"find_type",
	"find_id",
	"find_timestamp_x_1",
	"find_timestamp_x_2",
	"find_audit_type_id",
	"find_item_id",
	"find_site_id",
	"find_user_id",
	"find_guest_id",
	"find_remote_addr",
	"find_user_agent",
	"find_request_uri",
);
function CheckFilter()
{
	$str = "";
	if(strlen($_REQUEST["find_timestamp_x_1"])>0)
	{
		if(!CheckDateTime($_REQUEST["find_timestamp_x_1"], CSite::GetDateFormat("FULL")))
			$str.= GetMessage("ESOL_IX_EVENTLOG_WRONG_TIMESTAMP_X_FROM")."<br>";
	}
	if(strlen($_REQUEST["find_timestamp_x_2"])>0)
	{
		if(!CheckDateTime($_REQUEST["find_timestamp_x_2"], CSite::GetDateFormat("FULL")))
			$str.= GetMessage("ESOL_IX_EVENTLOG_WRONG_TIMESTAMP_X_TO")."<br>";
	}

	if(strlen($str) > 0)
	{
		global $lAdmin;
		$lAdmin->AddFilterError($str);
		return false;
	}

	return true;
}

$arFilter = array();
$lAdmin->InitFilter($arFilterFields);
InitSorting();

$find = $_REQUEST["find"];
$find_id = $_REQUEST["find_id"];
$find_audit_type = $_REQUEST["find_audit_type"];
$find_type = $_REQUEST["find_type"];
$find_audit_type_id = $_REQUEST["find_audit_type_id"];
$find_timestamp_x_1 = $_REQUEST["find_timestamp_x_1"];
$find_timestamp_x_2 = $_REQUEST["find_timestamp_x_2"];
$find_item_id = $_REQUEST["find_item_id"];
$find_site_id = $_REQUEST["find_site_id"];
$find_guest_id = $_REQUEST["find_guest_id"];
$find_remote_addr = $_REQUEST["find_remote_addr"];
$find_request_uri = $_REQUEST["find_request_uri"];
$find_user_agent = $_REQUEST["find_user_agent"];

if(CheckFilter())
{
	if(is_array($find_audit_type) && $find_audit_type[0] == "NOT_REF")
	{
		$audit_type_id_op = "=";
		$audit_type_id_filter = false;
	}
	elseif($find_type == "audit_type_id" && $find != '')
	{
		$audit_type_id_op = "";
		$audit_type_id_filter = $find;
	}
	elseif(is_array($find_audit_type))
	{
		$audit_type_id_op = "=";
		$audit_type_id_filter = $find_audit_type;
	}
	else
	{
		$audit_type_id_op = "";
		$audit_type_id_filter = $find_audit_type;
	}

	if(!is_array($audit_type_id_filter) && strlen($find_audit_type_id))
	{
		$audit_type_id_op = "";
		$audit_type_id_filter = "(".$audit_type_id_filter.")|(".$find_audit_type_id.")";
	}

	$arFilter = array(
		"ID" => $find_id,
		"TIMESTAMP_X_1" => $find_timestamp_x_1,
		"TIMESTAMP_X_2" => $find_timestamp_x_2,
		$audit_type_id_op."AUDIT_TYPE_ID" => $audit_type_id_filter,
		"MODULE_ID" => $MODULE_ID,
		"ITEM_ID" => $find_item_id,
		"SITE_ID" => $find_site_id,
		"USER_ID" => ($find != '' && $find_type == "user_id" ? $find : $find_user_id),
		"GUEST_ID" => $find_guest_id,
		"REMOTE_ADDR" => ($find != '' && $find_type == "remote_addr" ? $find : $find_remote_addr),
		"REQUEST_URI" => $find_request_uri,
		"USER_AGENT" => ($find != '' && $find_type == "user_agent" ? $find : $find_user_agent),
	);
}

if(isset($_REQUEST["mode"]) && $_REQUEST["mode"] == "excel")
	$arNavParams = false;
else
	$arNavParams = array("nPageSize"=>CAdminResult::GetNavSize($sTableID));

/** @global string $by  */
/** @global string $order  */
$rsData = CEventLog::GetList(array($by => $order), $arFilter, $arNavParams);
$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();
$lAdmin->NavText($rsData->GetNavPrint(GetMessage("ESOL_IX_EVENTLOG_LIST_PAGE")));

$arHeaders = array(
	array(
		"id" => "ID",
		"content" => GetMessage("ESOL_IX_EVENTLOG_ID"),
		"sort" => "ID",
		"default" => true,
		"align" => "right",
	),
	array(
		"id" => "TIMESTAMP_X",
		"content" => GetMessage("ESOL_IX_EVENTLOG_TIMESTAMP_X"),
		"sort" => "TIMESTAMP_X",
		"default" => true,
		"align" => "right",
	),
	array(
		"id" => "AUDIT_TYPE_ID",
		"content" => GetMessage("ESOL_IX_EVENTLOG_PROFILE_ID"),
		"default" => true,
	),
	array(
		"id" => "ITEM_ID",
		"content" => GetMessage("ESOL_IX_EVENTLOG_ITEM_ID"),
		"default" => true,
	),
	array(
		"id" => "REMOTE_ADDR",
		"content" => GetMessage("ESOL_IX_EVENTLOG_REMOTE_ADDR"),
		"default" => true,
	),
	array(
		"id" => "USER_AGENT",
		"content" => GetMessage("ESOL_IX_EVENTLOG_USER_AGENT"),
	),
	array(
		"id" => "REQUEST_URI",
		"content" => GetMessage("ESOL_IX_EVENTLOG_REQUEST_URI"),
		"default" => true,
	),
	array(
		"id" => "SITE_ID",
		"content" => GetMessage("ESOL_IX_EVENTLOG_SITE_ID"),
	),
	array(
		"id" => "USER_ID",
		"content" => GetMessage("ESOL_IX_EVENTLOG_USER_ID"),
		"default" => true,
	),
	array(
		"id" => "DESCRIPTION",
		"content" => GetMessage("ESOL_IX_EVENTLOG_DESCRIPTION"),
		"default" => true,
	),
);

$lAdmin->AddHeaders($arHeaders);

$arUsersCache = array();
$arGroupsCache = array();
$arForumCache = array("FORUM" => array(), "TOPIC" => array(), "MESSAGE" => array());
$a_ID = $a_AUDIT_TYPE_ID = $a_GUEST_ID = $a_USER_ID = $a_ITEM_ID = $a_REQUEST_URI = $a_DESCRIPTION = $a_REMOTE_ADDR = '';
while($db_res = $rsData->NavNext(true, "a_"))
{
	$row =& $lAdmin->AddRow($a_ID, $db_res);
	
	$profileName = $arProfiles[(int)preg_replace('/^.*_(\d+)$/', '$1', $a_AUDIT_TYPE_ID)];
	$row->AddViewField("AUDIT_TYPE_ID", $profileName);
	if($a_USER_ID)
	{
		if(!array_key_exists($a_USER_ID, $arUsersCache))
		{
			$rsUser = CUser::GetByID($a_USER_ID);
			if($arUser = $rsUser->GetNext())
			{
				$arUser["FULL_NAME"] = $arUser["NAME"].(strlen($arUser["NAME"])<=0 || strlen($arUser["LAST_NAME"])<=0?"":" ").$arUser["LAST_NAME"];
			}
			$arUsersCache[$a_USER_ID] = $arUser;
		}
		if($arUsersCache[$a_USER_ID])
			$row->AddViewField("USER_ID", '[<a href="user_edit.php?lang='.LANG.'&ID='.$a_USER_ID.'">'.$a_USER_ID.'</a>] '.$arUsersCache[$a_USER_ID]["FULL_NAME"]);
	}
	if($a_ITEM_ID)
	{
		if($a_ITEM_ID=='ELEMENT_NOT_FOUND')
		{
			$row->AddViewField("ITEM_ID", '['.$a_ITEM_ID.'] '.GetMessage("ESOL_IX_EVENTLOG_IBLOCK_ELEMENT_NOT_FOUND"));
			$a_DESCRIPTION = '<b>'.GetMessage("ESOL_IX_EVENTLOG_FILTER_FIELDS").'</b>'.$logger->GetElementDescriptionArray($a_DESCRIPTION);
		}
		elseif(strpos($a_ITEM_ID, 'ELEMENT_')===0)
		{
			list($eobject, $eaction, $eid) = explode('_', $a_ITEM_ID);
			if($eid > 0)
			{
				$dbRes = CIblockElement::GetList(array(), array('ID'=>$eid), false, array('nTopCount'=>1), array('ID', 'IBLOCK_ID', 'IBLOCK_TYPE_ID'));
				if($arElement = $dbRes->Fetch())
				{
					$a_ITEM_ID = '<a href="/bitrix/admin/iblock_element_edit.php?IBLOCK_ID='.$arElement['IBLOCK_ID'].'&type='.$arElement['IBLOCK_TYPE_ID'].'&ID='.$arElement['ID'].'&lang='.LANGUAGE_ID.'">'.$a_ITEM_ID.'</a>';
				}
				$row->AddViewField("ITEM_ID", '['.$a_ITEM_ID.'] '.GetMessage("ESOL_IX_EVENTLOG_IBLOCK_ELEMENT_".$eaction));
				
				if(strlen($a_DESCRIPTION))
				{
					$a_DESCRIPTION = $logger->GetElementDescription($a_DESCRIPTION);
				}
			}
		}
	}
	if(strlen($a_REQUEST_URI))
	{
		$row->AddViewField("REQUEST_URI", htmlspecialcharsbx($a_REQUEST_URI));
	}
	if(strlen($a_DESCRIPTION))
	{
		if(strncmp("==", $a_DESCRIPTION, 2)===0)
			$DESCRIPTION = htmlspecialcharsbx(base64_decode(substr($a_DESCRIPTION, 2)));
		else
			$DESCRIPTION = $a_DESCRIPTION;
		//htmlspecialcharsback for <br> <BR> <br/>
		$DESCRIPTION = preg_replace("#(&lt;)(\\s*br\\s*/{0,1})(&gt;)#is", "<\\2>", $DESCRIPTION);
		$row->AddViewField("DESCRIPTION", $DESCRIPTION);
	}
	else
	{
		$row->AddViewField("DESCRIPTION", '');
	}
}

$aContext = array();
$lAdmin->AddAdminContextMenu($aContext);

$APPLICATION->SetTitle(GetMessage("ESOL_IX_EVENTLOG_PAGE_TITLE"));
$lAdmin->CheckListMode();

require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_admin_after.php");
?>
<form name="find_form" method="GET" action="<?echo $APPLICATION->GetCurPage()?>?">
<input type="hidden" name="lang" value="<?echo LANG?>">
<?
$arFilterNames = array(
	"find_id" => GetMessage("ESOL_IX_EVENTLOG_ID"),
	"find_timestamp_x" => GetMessage("ESOL_IX_EVENTLOG_TIMESTAMP_X"),
	"find_item_id" => GetMessage("ESOL_IX_EVENTLOG_ITEM_ID"),
	"find_site_id" => GetMessage("ESOL_IX_EVENTLOG_SITE_ID"),
	"find_user_id" => GetMessage("ESOL_IX_EVENTLOG_USER_ID"),
	"find_remote_addr" => GetMessage("ESOL_IX_EVENTLOG_REMOTE_ADDR"),
	"find_user_agent" => GetMessage("ESOL_IX_EVENTLOG_USER_AGENT"),
	"find_request_uri" => GetMessage("ESOL_IX_EVENTLOG_REQUEST_URI"),
);

$oFilter = new CAdminFilter($sTableID."_filter", $arFilterNames);
$oFilter->Begin();
?>
<tr>
	<td><?echo GetMessage("ESOL_IX_EVENTLOG_PROFILE_ID")?>:</td>
	<td>
		<select name="find_audit_type_id" >
			<option value=""><?echo GetMessage("ESOL_IX_ALL"); ?></option>
			<?
			foreach($arProfiles as $k=>$profile)
			{
				$key = 'ESOL_IX_PROFILE_'.$k;
				?><option value="<?echo $key;?>" <?if($find_audit_type_id==$key){echo 'selected';}?>><?echo $profile; ?></option><?
			}
			?>
		</select>
	</td>
</tr>
<tr>
	<td><?echo GetMessage("ESOL_IX_EVENTLOG_ID")?>:</td>
	<td><input type="text" name="find_id" size="47" value="<?echo htmlspecialcharsbx($find_id)?>"></td>
</tr>
<tr>
	<td><?echo GetMessage("ESOL_IX_EVENTLOG_TIMESTAMP_X")?>:</td>
	<td><?echo CAdminCalendar::CalendarPeriod("find_timestamp_x_1", "find_timestamp_x_2", $find_timestamp_x_1, $find_timestamp_x_2, false, 15, true)?></td>
</tr>
<tr>
	<td><?echo GetMessage("ESOL_IX_EVENTLOG_ITEM_ID")?>:</td>
	<td><input type="text" name="find_item_id" size="47" value="<?echo htmlspecialcharsbx($find_item_id)?>">&nbsp;<?=ShowFilterLogicHelp()?></td>
</tr>
<?
$arSiteDropdown = array("reference" => array(), "reference_id" => array());
$v1 = "sort";
$v2 = "asc";
$rs = CSite::GetList($v1, $v2);
while ($ar = $rs->Fetch())
{
	$arSiteDropdown["reference_id"][] = $ar["ID"];
	$arSiteDropdown["reference"][]    = "[".$ar["ID"]."] ".$ar["NAME"];
}
?>
<tr>
	<td><?echo GetMessage("ESOL_IX_EVENTLOG_SITE_ID")?>:</td>
	<td><?echo SelectBoxFromArray("find_site_id", $arSiteDropdown, $find_site_id, GetMessage("ESOL_IX_ALL"), "");?></td>
</tr>
<tr>
	<td><?echo GetMessage("ESOL_IX_EVENTLOG_USER_ID")?>:</td>
	<td><input type="text" name="find_user_id" size="47" value="<?echo htmlspecialcharsbx($find_user_id)?>"></td>
</tr>
<tr>
	<td><?echo GetMessage("ESOL_IX_EVENTLOG_REMOTE_ADDR")?>:</td>
	<td><input type="text" name="find_remote_addr" size="47" value="<?echo htmlspecialcharsbx($find_remote_addr)?>">&nbsp;<?=ShowFilterLogicHelp()?></td>
</tr>
<tr>
	<td><?echo GetMessage("ESOL_IX_EVENTLOG_USER_AGENT")?>:</td>
	<td><input type="text" name="find_user_agent" size="47" value="<?echo htmlspecialcharsbx($find_user_agent)?>">&nbsp;<?=ShowFilterLogicHelp()?></td>
</tr>
<tr>
	<td><?echo GetMessage("ESOL_IX_EVENTLOG_REQUEST_URI")?>:</td>
	<td><input type="text" name="find_request_uri" size="47" value="<?echo htmlspecialcharsbx($find_request_uri)?>">&nbsp;<?=ShowFilterLogicHelp()?></td>
</tr>
<?
$oFilter->Buttons(array("table_id"=>$sTableID, "url"=>$APPLICATION->GetCurPage(), "form"=>"find_form"));
$oFilter->End();
?>
</form>
<?

$lAdmin->DisplayList();

require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");
?>
