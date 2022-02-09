<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

IncludeModuleLangFile(__FILE__);

if(!$USER->CanDoOperation('manage_short_uri') && !$USER->CanDoOperation('view_other_settings'))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$isAdmin = $USER->CanDoOperation('manage_short_uri');

$sTableID = "tbl_short_uri";
$oSort = new CAdminSorting($sTableID, "ID", "desc");
$lAdmin = new CAdminList($sTableID, $oSort);

function CheckFilter()
{
	global $FilterArr, $lAdmin;
	foreach ($FilterArr as $f) global $$f;
	if (strlen(trim($find_modified_1))>0 || strlen(trim($find_modified_2))>0)
	{
		$date_1_ok = false;
		$date1_stm = MkDateTime(FmtDate($find_modified_1,"D.M.Y"),"d.m.Y");
		$date2_stm = MkDateTime(FmtDate($find_modified_2,"D.M.Y")." 23:59","d.m.Y H:i");
		if (!$date1_stm && strlen(trim($find_modified_1))>0)
			$lAdmin->AddFilterError(GetMessage("SU_AF_WRONG_UPDATE_FROM"));
		else $date_1_ok = true;
		if (!$date2_stm && strlen(trim($find_modified_2))>0)
			$lAdmin->AddFilterError(GetMessage("SU_AF_WRONG_UPDATE_TILL"));
		elseif ($date_1_ok && $date2_stm <= $date1_stm && strlen($date2_stm)>0)
			$lAdmin->AddFilterError(GetMessage("SU_AF_FROM_TILL_UPDATE"));
	}
	if (strlen(trim($find_last_used_1))>0 || strlen(trim($find_last_used_2))>0)
	{
		$date_1_ok = false;
		$date1_stm = MkDateTime(FmtDate($find_last_used_1,"D.M.Y"),"d.m.Y");
		$date2_stm = MkDateTime(FmtDate($find_last_used_2,"D.M.Y")." 23:59","d.m.Y H:i");
		if (!$date1_stm && strlen(trim($find_last_used_1))>0)
			$lAdmin->AddFilterError(GetMessage("SU_AF_WRONG_INSERT_FROM"));
		else $date_1_ok = true;
		if (!$date2_stm && strlen(trim($find_last_used_2))>0)
			$lAdmin->AddFilterError(GetMessage("SU_AF_WRONG_INSERT_TILL"));
		elseif ($date_1_ok && $date2_stm <= $date1_stm && strlen($date2_stm)>0)
			$lAdmin->AddFilterError(GetMessage("SU_AF_FROM_TILL_INSERT"));
	}
	return count($lAdmin->arFilterErrors)==0;
}

$FilterArr = Array(
	"find_uri",
	"find_short_uri",
	"find_modified_1",
	"find_modified_2",
	"find_last_used_1",
	"find_last_used_2",
	);

$lAdmin->InitFilter($FilterArr);

if (CheckFilter())
{
	$arFilter = Array();
	if (strlen($find_modified_1) > 0)
		$arFilter["MODIFIED_1"]	= $find_modified_1;
	if (strlen($find_modified_2) > 0)
		$arFilter["MODIFIED_2"]	= $find_modified_2;
	if (strlen($find_last_used_1) > 0)
		$arFilter["LAST_USED_1"] = $find_last_used_1;
	if (strlen($find_last_used_2) > 0)
		$arFilter["LAST_USED_2"] = $find_last_used_2;
	if (strlen($find_uri) > 0)
		$arFilter["URI"] = $find_uri;
	if (strlen($find_short_uri) > 0)
		$arFilter["SHORT_URI"] = $find_short_uri;
}

if($lAdmin->EditAction() && $isAdmin)
{
	foreach($FIELDS as $ID=>$arFields)
	{
		if(!$lAdmin->IsUpdated($ID))
			continue;
		$DB->StartTransaction();
		$ID = IntVal($ID);

		if(!CBXShortUri::Update($ID, $arFields))
		{
			$lAdmin->AddUpdateError(GetMessage("SU_AF_SAVE_ERROR").$ID.": ".implode("\n ", CBXShortUri::GetErrors()), $ID);
			$DB->Rollback();
		}
		$DB->Commit();
	}
}

$strError = $strOk = "";

if(($arID = $lAdmin->GroupAction()) && $isAdmin)
{
	if($_REQUEST['action_target']=='selected')
	{
		$rsData = CBXShortUri::GetList(array($by=>$order), $arFilter);
		while($arRes = $rsData->Fetch())
			$arID[] = $arRes['ID'];
	}

	foreach($arID as $ID)
	{
		if(strlen($ID)<=0)
			continue;
		$ID = IntVal($ID);
		switch($_REQUEST['action'])
		{
		case "delete":
			@set_time_limit(0);
			$DB->StartTransaction();
			if(!CBXShortUri::Delete($ID))
			{
				$DB->Rollback();
				$lAdmin->AddGroupError(GetMessage("SU_AF_del_err"), $ID);
			}
			$DB->Commit();
			break;
		}

	}
}

$rsData = CBXShortUri::GetList(array($by=>$order), $arFilter, array("nPageSize"=>CAdminResult::GetNavSize($sTableID)));
$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();
$lAdmin->NavText($rsData->GetNavPrint(GetMessage("SU_AF_nav")));

$lAdmin->AddHeaders(array(
	array(	"id"		=>"ID",
		"content"	=>"ID",
		"sort"		=>"ID",
		"align"		=>"right",
		"default"	=>true,
	),
	array(	"id"		=>"MODIFIED",
		"content"	=>GetMessage("SU_FLD_MODIFIED"),
		"sort"		=>"MODIFIED",
		"default"	=>true,
	),
	array(	"id"		=>"URI",
		"content"	=>GetMessage("SU_FLD_URI"),
		"sort"		=>"URI",
		"default"	=>true,
	),
	array(	"id"		=>"SHORT_URI",
		"content"	=>GetMessage("SU_FLD_SHORT_URI"),
		"sort"		=>"SHORT_URI",
		"default"	=>true,
	),
	array(	"id"		=>"STATUS",
		"content"	=>GetMessage("SU_FLD_STATUS"),
		"sort"		=>"STATUS",
		"default"	=>true,
	),
	array(	"id"		=>"LAST_USED",
		"content"	=>GetMessage("SU_FLD_LAST_USED"),
		"sort"		=>"LAST_USED",
		"default"	=>true,
	),
	array(	"id"		=>"NUMBER_USED",
		"content"	=>GetMessage("SU_FLD_NUMBER_USED"),
		"sort"		=>"NUMBER_USED",
		"default"	=>true,
	),
));

while($arRes = $rsData->NavNext(true, "f_")):
	$row =& $lAdmin->AddRow($f_ID, $arRes);

/*	if($f_USER_ID > 0)
		$strUser = "[<a class='tablebodylink' href=\"/bitrix/admin/user_edit.php?ID=".$f_USER_ID."&amp;lang=".LANG."\" title=\"".GetMessage("subscr_user_edit_title")."\">".$f_USER_ID."</a>] (".$f_USER_LOGIN.") ".$f_USER_NAME." ".$f_USER_LAST_NAME;
	else
		$strUser = GetMessage("subscr_adm_anon");
	$row->AddViewField("USER_ID", $strUser);
	$row->AddCheckField("ACTIVE");
	$row->AddInputField("EMAIL", array("size"=>20));
	$row->AddViewField("EMAIL", '<a href="subscr_edit.php?ID='.$f_ID.'&amp;lang='.LANG.'" title="'.GetMessage("subscr_upd").'">'.$f_EMAIL.'</a>');
	$row->AddSelectField("FORMAT",array("text"=>GetMessage("SU_AF_TEXT"),"html"=>GetMessage("SU_AF_HTML")));
	$row->AddCheckField("CONFIRMED");*/

	$arActions = Array();

	$arActions[] = array(
		"ICON"=>"edit",
		"DEFAULT"=>true,
		"TEXT"=>GetMessage("SU_AF_upd"),
		"ACTION"=>$lAdmin->ActionRedirect("short_uri_edit.php?ID=".$f_ID)
	);
	if ($isAdmin)
		$arActions[] = array(
			"ICON"=>"delete",
			"TEXT"=>GetMessage("SU_AF_del"),
			"ACTION"=>"if(confirm('".GetMessage("SU_AF_del_conf")."')) ".$lAdmin->ActionDoGroup($f_ID, "delete")
		);
	$row->AddActions($arActions);

endwhile;

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
		"TEXT"=>GetMessage("MAIN_ADD"),
		"LINK"=>"short_uri_edit.php?lang=".LANG,
		"TITLE"=>GetMessage("SU_AF_add_title"),
		"ICON"=>"btn_new",
	),
);
$lAdmin->AddAdminContextMenu($aContext);
$lAdmin->CheckListMode();

$APPLICATION->SetTitle(GetMessage("SU_AF_title"));

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$oFilter = new CAdminFilter(
	$sTableID."_filter",
	array(
//		GetMessage("SU_AF_F_URI"),
		GetMessage("SU_AF_F_SHORT_URI"),
		GetMessage("SU_AF_F_MODIFIED"),
		GetMessage("SU_AF_F_LAST_USED"),
	)
);
?>

<form name="find_form" method="get" action="<?echo $APPLICATION->GetCurPage();?>">
<?$oFilter->Begin();?>
<tr>
	<td><b><?=GetMessage("SU_AF_F_URI")?>:</b></td>
	<td>
		<input type="text" size="47" name="find_uri" value="<?echo htmlspecialcharsbx($find_uri)?>">&nbsp;<?=ShowFilterLogicHelp()?>
	</td>
</tr>
<tr>
	<td><?echo GetMessage("SU_AF_F_SHORT_URI")?>:</td>
	<td><input type="text" name="find_short_uri" size="47" value="<?echo htmlspecialcharsbx($find_short_uri)?>"></td>
</tr>
<tr>
	<td><?echo GetMessage("SU_AF_F_MODIFIED")?>:</td>
	<td><?echo CalendarPeriod("find_modified_1", htmlspecialcharsbx($find_modified_1), "find_modified_2", htmlspecialcharsbx($find_modified_2), "find_form","Y")?></td>
</tr>
<tr>
	<td><?echo GetMessage("SU_AF_F_LAST_USED")?>:</td>
	<td><?echo CalendarPeriod("find_last_used_1", htmlspecialcharsbx($find_last_used_1), "find_last_used_2", htmlspecialcharsbx($find_last_used_2), "find_form","Y")?></td>
</tr>
<?
$oFilter->Buttons(array("table_id"=>$sTableID,"url"=>$APPLICATION->GetCurPage(),"form"=>"find_form"));
$oFilter->End();
?>
</form>

<?$lAdmin->DisplayList();?>

<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>