<?
##############################################
# Bitrix Site Manager                        #
# Copyright (c) 2002-2007 Bitrix             #
# http://www.bitrixsoft.com                  #
# mailto:admin@bitrixsoft.com                #
##############################################
require_once(dirname(__FILE__)."/../include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/prolog.php");
define("HELP_FILE", "settings/mail_events/messagetype_admin.php");
IncludeModuleLangFile(__FILE__);
$err_mess = "File: ".__FILE__."<br>Line: ";
$arResult = array();
$arFilter = array();
$error = false;

if(!$USER->CanDoOperation('edit_other_settings') && !$USER->CanDoOperation('view_other_settings'))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$isAdmin = $USER->CanDoOperation('edit_other_settings');

$sTableID = "tbl_event_type";
$oSort = new CAdminSorting($sTableID, "event_name", "desc");
$lAdmin = new CAdminList($sTableID, $oSort);

$arFilterFields = Array(
	"find",
	"find_type",
	"find_type_id",
	"find_tmpl_id",
	);
$lAdmin->InitFilter($arFilterFields);
if (!empty($find))$arFilter["~".strToUpper($find_type)] = $find;
if (!empty($find_type_id))
	$arFilter["ID"] = $find_type_id;
if (!empty($find_tmpl_id))
	$arFilter["MESSAGE_ID"] = $find_tmpl_id;
	
if(($arID = $lAdmin->GroupAction()) && $isAdmin && check_bitrix_sessid())
{
	if($_REQUEST['action_target']=='selected')
	{
		$rsData = CEventType::GetListEx(array($by => $order), $arFilter, array("type" => "none"));
		while($arRes = $rsData->Fetch())
			$arID[] = $arRes['EVENT_NAME'];
	}

	foreach($arID as $ID)
	{
		if(strLen($ID) <= 0)
			continue;
		switch($_REQUEST['action'])
		{
			case "delete":
			case "clean":
				$DB->StartTransaction();
				$ID = array("EVENT_NAME" => $ID);
				$db_res = CEventMessage::GetList($by, $order, $ID);
				if ($db_res && ($res = $db_res->Fetch()))
				{
					do 
					{
						if (!CEventMessage::Delete($res["ID"]))
						{
							$error = true;
							break;
						}
					} while ($res = $db_res-> Fetch());
				}
				
				if ($error || !CEventType::Delete($ID))
				{
					$DB->Rollback();
					$lAdmin->AddGroupError(GetMessage("DELETE_ERROR"), $ID);
				}
				else
					$DB->Commit();
			break;
		}
	}
}
$arLID = array();
$db_res = CLanguage::GetList(($by_="sort"), ($order_="asc"));
if ($db_res && $res = $db_res->GetNext())
{
	do 
	{
		$arLID[$res["LID"]] = $res["LID"];
	} while ($res = $db_res->GetNext());
}


$lAdmin->AddHeaders(array(
	array("id"=>"ID", "content"=>"ID", "default"=>true),
	array("id"=>"LID", "content"=>GetMessage("LANG"), "default"=>true),
	array("id"=>"EVENT_NAME", "content"=>GetMessage("EVENT_TYPE"), "sort"=>"event_name", "default"=>true),
	array("id"=>"NAME", "content"=>GetMessage("EVENT_NAME"), "default"=>true),
	array("id"=>"DESCRIPTION", "content"=>GetMessage("EVENT_DESCRIPTION"), "default"=>false),
	array("id"=>"TEMPLATES", "content"=>GetMessage("EVENT_TEMPLATES"), "default"=>false)));

$db_res = CEventType::GetListEx(array($by=>$order), $arFilter, array("type" => "full"));
if ($db_res && $res = $db_res->Fetch())
{
	do 
	{
		$arResult[] = $res;
	}while ($res = $db_res->Fetch());
}
$rsData = new CDBResult;
$rsData->InitFromArray($arResult);
$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();
$lAdmin->NavText($rsData->GetNavPrint(GetMessage("PAGES")));
while($arRes = $rsData->NavNext(true, "f_"))
{
	$arr = $f_ID;
	$f_ID = $f_EVENT_NAME;
	$row =& $lAdmin->AddRow($f_EVENT_NAME, $arRes, "type_edit.php?EVENT_NAME=".$f_EVENT_NAME, GetMessage("type_admin_edit_title"));
	$row->AddViewField("ID", implode("<br />", $arr));
	$row->AddViewField("LID", implode("<br />", array_intersect($arLID, $f_LID)));
	$row->AddViewField("EVENT_NAME", "<a href=\"type_edit.php?EVENT_NAME=".$f_EVENT_NAME."\">".$f_EVENT_NAME."</a>");
	$row->AddViewField("NAME", $f_NAME);
	$row->AddViewField("DESCRIPTION", $f_DESCRIPTION);
	$templates = array();
	if (is_array($f_TEMPLATES) && !empty($f_TEMPLATES))
	{
		$templates = array();
		foreach ($f_TEMPLATES as $k => $v)
			$templates[$k] = "<a href=\"".BX_ROOT."/admin/message_edit.php?ID=".intVal($k)."&lang=".LANGUAGE_ID."\">".intVal($k)."</a>";
	}
	$row->AddViewField("TEMPLATES", implode("<br />", $templates));

	$arActions = Array();
	$arActions[] = array("ICON"=>"edit", "TEXT"=>GetMessage("MAIN_ADMIN_MENU_EDIT"), "ACTION"=>$lAdmin->ActionRedirect("type_edit.php?EVENT_NAME=".$f_EVENT_NAME));
	if($isAdmin)
	{
		$arActions[] = array("ICON"=>"delete", "TEXT"=>GetMessage("MAIN_ADMIN_MENU_DELETE"), "ACTION"=>"if(confirm('".GetMessage('CONFIRM_DEL_ALL_MESSAGE')."')) ".$lAdmin->ActionDoGroup($f_ID, "delete"));
	}
	$row->AddActions($arActions);
}

$lAdmin->AddFooter(
	array(
		array("title"=>GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value"=>$rsData->SelectedRowsCount()),
		array("counter"=>true, "title"=>GetMessage("MAIN_ADMIN_LIST_CHECKED"), "value"=>"0"),
	)
);

$lAdmin->AddGroupActionTable(Array(
	"delete"=>GetMessage("MAIN_ADMIN_LIST_DELETE"),
	));

$aContext = array(
	array(
		"TEXT" => GetMessage("ADD_TYPE"),
		"LINK" => "type_edit.php?lang=".LANGUAGE_ID,
		"TITLE" => GetMessage("ADD_TYPE_TITLE"),
		"ICON" => "btn_new"
	),
);
$lAdmin->AddAdminContextMenu($aContext);
$lAdmin->CheckListMode();
$APPLICATION->SetTitle(GetMessage("TITLE"));
require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_admin_after.php");
?><form name="find_form" method="GET" action="<?=$APPLICATION->GetCurPage()?>?"><?
$oFilter = new CAdminFilter($sTableID."_filter", array(
	GetMessage('F_ID')." ".GetMessage('F_TYPE'), 
	GetMessage('F_ID')." ".GetMessage('F_TMPL')));
$oFilter->Begin();
?><tr>
	<td><b><?=GetMessage("F_SEARCH")?>:</b></td>
	<td nowrap>
		<input type="text" size="25" name="find" value="<?=htmlspecialcharsbx($find)?>" title="<?=GetMessage("F_SEARCH_TITLE")?>">
		<select name="find_type">
			<option value="event_name"<?if($find_type=="event_name") echo " selected"?>><?=GetMessage('F_EVENT_NAME')?></option>
			<option value="name"<?if($find_type=="subject") echo " selected"?>><?=GetMessage('F_NAME')?></option>
			<option value="description"<?if($find_type=="from") echo " selected"?>><?=GetMessage('F_DESCRIPTION')?></option>
		</select>
	</td>
</tr>
<tr>
	<td>ID <?=GetMessage('F_TYPE')?>:</td>
	<td><input type="text" name="find_type_id" size="47" value="<?=htmlspecialcharsbx($find_type_id)?>"></td>
</tr>
<tr>
	<td>ID <?=GetMessage('F_TMPL')?>:</td>
	<td><input type="text" name="find_tmpl_id" size="47" value="<?=htmlspecialcharsbx($tmpl)?>"></td>
</tr>
<?
$oFilter->Buttons(array("table_id"=>$sTableID, "url"=>$APPLICATION->GetCurPage(), "form"=>"find_form"));
$oFilter->End();
?>
</form>
<?
$lAdmin->DisplayList();

require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");?>