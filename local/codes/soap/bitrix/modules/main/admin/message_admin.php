<?
##############################################
# Bitrix Site Manager                        #
# Copyright (c) 2002-2007 Bitrix             #
# http://www.bitrixsoft.com                  #
# mailto:admin@bitrixsoft.com                #
##############################################

require_once(dirname(__FILE__)."/../include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/prolog.php");
define("HELP_FILE", "settings/mail_events/message_admin.php");

if(!$USER->CanDoOperation('edit_other_settings') && !$USER->CanDoOperation('view_other_settings'))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$isAdmin = $USER->CanDoOperation('edit_other_settings');

IncludeModuleLangFile(__FILE__);
$err_mess = "File: ".__FILE__."<br>Line: ";

// variable with ID of table
$sTableID = "tbl_main_message";
// sorting
$oSort = new CAdminSorting($sTableID, "TIMESTAMP_X", "desc");
// list
$lAdmin = new CAdminList($sTableID, $oSort);

$arFilterFields = Array(
	"find",
	"find_id",
	"find_type",
	"find_type_id",
	"find_timestamp_1",
	"find_timestamp_2",
	"find_lid",
	"find_active",
	"find_from",
	"find_to",
	"find_bcc",
	"find_subject",
	"find_body_type",
	"find_body"
	);

$lAdmin->InitFilter($arFilterFields);


/***************************************************************************
Functions
***************************************************************************/
function CheckFilter($arFilterFields) // checking input fields
{
	$FilterArr = $arFilterFields;
	reset($FilterArr); foreach ($FilterArr as $f) global $$f;
	$str = "";
	if (strlen(trim($find_timestamp_1))>0 || strlen(trim($find_timestamp_2))>0)
	{
		$date_1_ok = false;
		$date1_stm = MkDateTime(FmtDate($find_timestamp_1,"D.M.Y"),"d.m.Y");
		$date2_stm = MkDateTime(FmtDate($find_timestamp_2,"D.M.Y")." 23:59","d.m.Y H:i");
		if (!$date1_stm && strlen(trim($find_timestamp_1))>0)
			$str.= GetMessage("MAIN_WRONG_TIMESTAMP_FROM")."<br>";
		else $date_1_ok = true;
		if (!$date2_stm && strlen(trim($find_timestamp_2))>0)
			$str.= GetMessage("MAIN_WRONG_TIMESTAMP_TILL")."<br>";
		elseif ($date_1_ok && $date2_stm <= $date1_stm && strlen($date2_stm)>0)
			$str.= GetMessage("MAIN_FROM_TILL_TIMESTAMP")."<br>";
	}
	$GLOBALS["lAdmin"]->AddFilterError($str);
	if (strlen($str)>0) return false; else return true;
}

if(CheckFilter($arFilterFields))
{
	$arFilter = Array(
		"ID"			=> $find_id,
		"TYPE"			=> $find_event_type,
		"TYPE_ID"		=> $find_type_id,
		"TIMESTAMP_1"	=> $find_timestamp_1,
		"TIMESTAMP_2"	=> $find_timestamp_2,
		"LANG"			=> $find_lid,
		"ACTIVE"		=> $find_active,
		"FROM"			=> ($find!='' && $find_type == "from"? $find: $find_from),
		"TO"			=> ($find!='' && $find_type == "to"? $find: $find_to),
		"BCC"			=> $find_bcc,
		"SUBJECT"		=> ($find!='' && $find_type == "subject"? $find: $find_subject),
		"BODY_TYPE"		=> $find_body_type,
		"BODY"			=> ($find!='' && $find_type == "body"? $find: $find_body)
		);
}


// edit (Check rights before saving!)
if($lAdmin->EditAction() && $isAdmin) // if saving from list
{
	foreach($FIELDS as $ID=>$arFields)
	{
		if(!$lAdmin->IsUpdated($ID))
			continue;

		$DB->StartTransaction();
		$ID = IntVal($ID);

		$em = new CEventMessage;
		if(!$em->Update($ID, $arFields))
		{
			$lAdmin->AddUpdateError(GetMessage("SAVE_ERROR").$id.": ".$em->LAST_ERROR, $ID);
			$DB->Rollback();
		}
		$DB->Commit();
	}
}

// Actions
if(($arID = $lAdmin->GroupAction()) && $isAdmin)
{
	if($_REQUEST['action_target']=='selected')
	{
		$rsData = CEventMessage::GetList($by, $order, $arFilter);
		while($arRes = $rsData->Fetch())
			$arID[] = $arRes['ID'];
	}

	foreach($arID as $ID)
	{
		if(strlen($ID)<=0)
			continue;

		$ID = IntVal($ID);

		$emessage = new CEventMessage;
		switch($_REQUEST['action'])
		{
		case "delete":
			$DB->StartTransaction();
			if(!$emessage->Delete($ID))
			{
				$DB->Rollback();
				$lAdmin->AddGroupError(GetMessage("DELETE_ERROR"), $ID);
			}
			else
				$DB->Commit();
			break;
		case "activate":
		case "deactivate":
			$arFields = Array("ACTIVE"=>($_REQUEST['action']=="activate"?"Y":"N"));
			if(!$emessage->Update($ID, $arFields))
				$lAdmin->AddGroupError(GetMessage("MAIN_EDIT_ERROR").':'.$emessage->LAST_ERROR, $ID);
			break;
		}
	}
}

$rsData = CEventMessage::GetList($by, $order, $arFilter);
$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();

// LIST
$lAdmin->NavText($rsData->GetNavPrint(GetMessage("PAGES")));



// Header
$lAdmin->AddHeaders(array(
	array("id"=>"ID", "content"=>"ID", 	"sort"=>"id", "default"=>true, "align"=>"right"),
	array("id"=>"TIMESTAMP_X", "content"=>GetMessage('TIMESTAMP'), "default"=>true, "align"=>"center"),
	array("id"=>"ACTIVE", "content"=>GetMessage('ACTIVE'), "sort"=>"active", "default"=>true, "align"=>"center"),
	array("id"=>"LID", "content"=>GetMessage('LANG'), "default"=>true, "align"=>"center"),
	array("id"=>"EVENT_NAME", "content"=>GetMessage("EVENT_TYPE"), "sort"=>"event_name", "default"=>true),
	array("id"=>"EVENT_TYPE", "content"=>GetMessage("EVENT_TYPE")),
	array("id"=>"SUBJECT", "content"=>GetMessage('SUBJECT'), "sort"=>"subject", "default"=>true),
	array("id"=>"EMAIL_FROM", "content"=>GetMessage("F_FROM"), "sort"=>"from"),
	array("id"=>"EMAIL_TO", "content"=>GetMessage("F_TO"), "sort"=>"to"),
	array("id"=>"BCC", "content"=>GetMessage("F_BCC"), "sort"=>"bcc"),
	array("id"=>"BODY_TYPE","content"=>GetMessage("F_BODY_TYPE"), "sort"=>"body_type"),
));


$arText_HTML = Array("text"=>GetMessage("MAIN_TEXT"), "html"=>GetMessage("MAIN_HTML"));
$arEventTypes = Array();
$rsType = CEventType::GetListEx(array(), array(), array("LID"=>LANG, "type" => "type"));
while($arType = $rsType->Fetch())
	$arEventTypes[$arType["EVENT_NAME"]] = $arType["NAME"];

// Body
while($arRes = $rsData->NavNext(true, "f_"))
{
	$row =& $lAdmin->AddRow($f_ID, $arRes, "message_edit.php?lang=".LANGUAGE_ID."&ID=".$f_ID, GetMessage("MAIN_ADMIN_MENU_EDIT_TITLE"));
	$row->AddViewField("ID", '<a href="message_edit.php?lang='.LANGUAGE_ID.'&ID='.$f_ID.'" title="'.GetMessage("MAIN_ADMIN_MENU_EDIT_TITLE").'">'.$f_ID.'</a>');

	$strSITE_ID = '';
	$db_LID = CEventMessage::GetLang($f_ID);
	while($ar_LID = $db_LID->Fetch())
		$strSITE_ID .= htmlspecialcharsbx($ar_LID["LID"])."<br>";

	$row->AddViewField("LID", $strSITE_ID);
	$row->AddCheckField("ACTIVE");
	$row->AddInputField("SUBJECT", Array("size"=>30));
	$row->AddSelectField("BODY_TYPE", $arText_HTML);
	$row->AddViewField("EMAIL_FROM", TxtToHtml($arRes["EMAIL_FROM"])); $row->AddInputField("EMAIL_FROM");
	$row->AddViewField("EMAIL_TO", TxtToHtml($arRes["EMAIL_TO"])); $row->AddInputField("EMAIL_TO");
	$row->AddViewField("BCC", TxtToHtml($arRes["BCC"])); $row->AddInputField("BCC");
	$row->AddSelectField("EVENT_NAME", $arEventTypes);

	$arActions = Array();
	$arActions[] = array("ICON"=>"edit", "TEXT"=>GetMessage("MAIN_ADMIN_MENU_EDIT"), "ACTION"=>$lAdmin->ActionRedirect("message_edit.php?ID=".$f_ID));
	$arActions[] = array("ICON"=>"copy", "TEXT"=>GetMessage("MAIN_ADMIN_ADD_COPY"), "ACTION"=>$lAdmin->ActionRedirect("message_edit.php?COPY_ID=".$f_ID));
	if($isAdmin)
	{
		$arActions[] = array("SEPARATOR"=>true);
		$arActions[] = array("ICON"=>"delete", "TEXT"=>GetMessage("MAIN_ADMIN_MENU_DELETE"), "ACTION"=>"if(confirm('".GetMessage('CONFIRM_DEL_MESSAGE')."')) ".$lAdmin->ActionDoGroup($f_ID, "delete"));
	}

	$row->AddActions($arActions);
}

// Footer
$lAdmin->AddFooter(
	array(
		array("title"=>GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value"=>$rsData->SelectedRowsCount()),
		array("counter"=>true, "title"=>GetMessage("MAIN_ADMIN_LIST_CHECKED"), "value"=>"0"),
	)
);

// Form with buttons
$lAdmin->AddGroupActionTable(Array(
	"delete"=>GetMessage("MAIN_ADMIN_LIST_DELETE"),
	"activate"=>GetMessage("MAIN_ADMIN_LIST_ACTIVATE"),
	"deactivate"=>GetMessage("MAIN_ADMIN_LIST_DEACTIVATE"),
));


//			$arr = array("REFERENCE" => $event_type_ref, "REFERENCE_ID" => $event_type_ref_id);
//			echo SelectBoxFromArray("EVENT_NAME", $arr, htmlspecialcharsbx($find_type_id));


// contextual menu (add, go_to_list)
$aContext = array(
	array(
		"TEXT" => GetMessage("ADD_TEMPL"),
		"LINK" => "message_edit.php?lang=".LANG."&type=".urlencode($type).'&'.GetFilterParams("find_".$type."_"),
		"TITLE" => GetMessage("ADD_TEMPL_TITLE"),
		"ICON" => "btn_new"
	),
);
$lAdmin->AddAdminContextMenu($aContext);

// Check information before outing
$lAdmin->CheckListMode();

$APPLICATION->SetTitle(GetMessage("TITLE"));

require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_admin_after.php");
?>
<form name="find_form" method="GET" action="<?=$APPLICATION->GetCurPage()?>?">
<?
$oFilter = new CAdminFilter(
	$sTableID."_filter",
	array(
		GetMessage('F_ID'),
		GetMessage('F_TYPE'),
		GetMessage('F_D_MODIF'),
		GetMessage('F_SITE'),
		GetMessage('F_ACTIVE'),
		GetMessage('F_FROM'),
		GetMessage('F_TO'),
		GetMessage('F_BCC'),
		GetMessage('F_THEME'),
		GetMessage('F_BODY_TYPE'),
		GetMessage('F_CONTENT'))
);

$oFilter->Begin();
?>
<tr>
	<td><b><?=GetMessage("F_SEARCH")?></b></td>
	<td nowrap>
		<input type="text" size="25" name="find" value="<?echo htmlspecialcharsbx($find)?>" title="<?=GetMessage("F_SEARCH_TITLE")?>">
		<select name="find_type">
			<option value="subject"<?if($find_type=="subject") echo " selected"?>><?=GetMessage('F_THEME')?></option>
			<option value="from"<?if($find_type=="from") echo " selected"?>><?=GetMessage('F_FROM')?></option>
			<option value="to"<?if($find_type=="to") echo " selected"?>><?=GetMessage('F_TO')?></option>
			<option value="body"<?if($find_type=="body") echo " selected"?>><?=GetMessage('F_CONTENT')?></option>
		</select>
	</td>
</tr>
<tr>
	<td><?echo GetMessage("MAIN_F_ID")?></td>
	<td><input type="text" name="find_id" size="47" value="<?echo htmlspecialcharsbx($find_id)?>"><?=ShowFilterLogicHelp()?></td>
</tr>
<tr>
	<td><?echo GetMessage("MAIN_F_EVENT_TYPE")?></td>
	<td><input type="text" name="find_event_type" size="47" value="<?echo htmlspecialcharsbx($find_event_type)?>"><?=ShowFilterLogicHelp()?><br><?
		$event_type_ref = array();
		$event_type_ref_id = array();
		$ref_en = array();
		$rsType = CEventType::GetList(array("LID"=>LANGUAGE_ID), array("sort"=>"asc", "name"=>"asc"));
		while($arType = $rsType->Fetch())
		{
			$event_type_ref[] = $arType["NAME"].($arType["NAME"] == ''? '' : ' ')."[".$arType["EVENT_NAME"]."]";
			$event_type_ref_id[] = $arType["EVENT_NAME"];
		}

		$arr = array("REFERENCE" => $event_type_ref, "REFERENCE_ID" => $event_type_ref_id);
		echo SelectBoxFromArray("find_type_id", $arr, htmlspecialcharsbx($find_type_id), GetMessage("MAIN_ALL"));
	?></td>
</tr>
<tr>
	<td><?echo GetMessage("MAIN_F_TIMESTAMP").":"?></td>
	<td><?echo CalendarPeriod("find_timestamp_1", htmlspecialcharsbx($find_timestamp_1), "find_timestamp_2", htmlspecialcharsbx($find_timestamp_2), "find_form","Y")?></td>
</tr>
<tr>
	<td><?=GetMessage("MAIN_F_LID")?></td>
	<td><?echo CLang::SelectBox("find_lid", htmlspecialcharsbx($find_lid), GetMessage("MAIN_ALL")); ?></td>
</tr>
<tr>
	<td><?=GetMessage("F_ACTIVE")?></td>
	<td><?
		$arr = array("reference"=>array(GetMessage("MAIN_YES"), GetMessage("MAIN_NO")), "reference_id"=>array("Y","N"));
		echo SelectBoxFromArray("find_active", $arr, htmlspecialcharsbx($find_active), GetMessage("MAIN_ALL"));
		?></td>
</tr>
<tr>
	<td><?echo GetMessage("MAIN_F_FROM")?></td>
	<td><input type="text" name="find_from" size="47" value="<?echo htmlspecialcharsbx($find_from)?>"><?=ShowFilterLogicHelp()?></td>
</tr>
<tr>
	<td><?echo GetMessage("MAIN_F_TO")?></td>
	<td><input type="text" name="find_to" size="47" value="<?echo htmlspecialcharsbx($find_to)?>"><?=ShowFilterLogicHelp()?></td>
</tr>
<tr>
	<td><?echo GetMessage("MAIN_F_BCC")?></td>
	<td><input type="text" name="find_bcc" size="47" value="<?echo htmlspecialcharsbx($find_bcc)?>"><?=ShowFilterLogicHelp()?></td>
</tr>
<tr>
	<td><?echo GetMessage("F_SUBJECT")?></td>
	<td><input type="text" name="find_subject" size="47" value="<?echo htmlspecialcharsbx($find_subject)?>"><?=ShowFilterLogicHelp()?></td>
</tr>
<tr>
	<td><?=GetMessage("MAIN_F_BODY_TYPE")?></td>
	<td><?
		$arr = array("reference"=>array(GetMessage("MAIN_TEXT"), GetMessage("MAIN_HTML")), "reference_id"=>array("text","html"));
		echo SelectBoxFromArray("find_body_type", $arr, htmlspecialcharsbx($find_body_type), GetMessage("MAIN_ALL"));
		?></td>
</tr>
<tr>
	<td><?echo GetMessage("MAIN_F_MESSAGE_BODY")?></td>
	<td><input type="text" name="find_body" size="47" value="<?echo htmlspecialcharsbx($find_body)?>"><?=ShowFilterLogicHelp()?></td>
</tr>
<?
$oFilter->Buttons(array("table_id"=>$sTableID, "url"=>$APPLICATION->GetCurPage(), "form"=>"find_form"));
$oFilter->End();
?>
</form>
<?
// Display list
$lAdmin->DisplayList();

require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");?>
