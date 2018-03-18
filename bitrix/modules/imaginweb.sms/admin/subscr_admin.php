<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/imaginweb.sms/include.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/imaginweb.sms/prolog.php");

IncludeModuleLangFile(__FILE__);

$POST_RIGHT = $APPLICATION->GetGroupRight("imaginweb.sms");
if($POST_RIGHT <= "D"){
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

$sTableID = "tbl_subscr";
$oSort = new CAdminSorting($sTableID, "ID", "desc");
$lAdmin = new CAdminList($sTableID, $oSort);

function CheckFilter(){
	
	global $FilterArr, $lAdmin;
	foreach ($FilterArr as $f) global $$f;
	if(strlen(trim($find_update_1)) > 0 || strlen(trim($find_update_2)) > 0){
		$date_1_ok = false;
		$date1_stm = MkDateTime(FmtDate($find_update_1,"D.M.Y"),"d.m.Y");
		$date2_stm = MkDateTime(FmtDate($find_update_2,"D.M.Y")." 23:59","d.m.Y H:i");
		if(!$date1_stm && strlen(trim($find_update_1)) > 0){
			$lAdmin->AddFilterError(GetMessage("POST_WRONG_UPDATE_FROM"));
		}
		else{
			$date_1_ok = true;
		}
		if(!$date2_stm && strlen(trim($find_update_2)) > 0){
			$lAdmin->AddFilterError(GetMessage("POST_WRONG_UPDATE_TILL"));
		}	
		elseif($date_1_ok && $date2_stm <= $date1_stm && strlen($date2_stm) > 0){
			$lAdmin->AddFilterError(GetMessage("POST_FROM_TILL_UPDATE"));
		}
			
	}
	if(strlen(trim($find_insert_1)) > 0 || strlen(trim($find_insert_2)) > 0){
		$date_1_ok = false;
		$date1_stm = MkDateTime(FmtDate($find_insert_1,"D.M.Y"),"d.m.Y");
		$date2_stm = MkDateTime(FmtDate($find_insert_2,"D.M.Y")." 23:59","d.m.Y H:i");
		if(!$date1_stm && strlen(trim($find_insert_1)) > 0){
			$lAdmin->AddFilterError(GetMessage("POST_WRONG_INSERT_FROM"));
		}
		else{
			$date_1_ok = true;
		}
		if(!$date2_stm && strlen(trim($find_insert_2)) > 0){
			$lAdmin->AddFilterError(GetMessage("POST_WRONG_INSERT_TILL"));
		}
		elseif($date_1_ok && $date2_stm <= $date1_stm && strlen($date2_stm) > 0){
			$lAdmin->AddFilterError(GetMessage("POST_FROM_TILL_INSERT"));
		}
	}
	return count($lAdmin->arFilterErrors) == 0;
}

$FilterArr = Array(
	"find",
	"find_type",
	"find_id",
	"find_update_1",
	"find_update_2",
	"find_insert_1",
	"find_insert_2",
	"find_user",
	"find_user_id",
	"find_anonymous",
	"find_active",
	"find_phone",
	"find_format",
	"find_confirmed",
	"find_distribution",
);

$lAdmin->InitFilter($FilterArr);

if(CheckFilter()){
	$arFilter = Array(
		"ID"		=> ($find!="" && $find_type == "id"? $find : $find_id),
		"PHONE"		=> ($find!="" && $find_type == "phone"? $find : $find_phone),
		"UPDATE_1"	=> $find_update_1,
		"UPDATE_2"	=> $find_update_2,
		"INSERT_1"	=> $find_insert_1,
		"INSERT_2"	=> $find_insert_2,
		"USER_ID"	=> $find_user_id,
		"USER"		=> ($find!="" && $find_type == "user"? $find : $find_user),
		"ANONYMOUS"	=> $find_anonymous,
		"CONFIRMED"	=> $find_confirmed,
		"ACTIVE"	=> $find_active,
		"FORMAT"	=> $find_format,
		"DISTRIBUTION"	=> $find_distribution,
	);
}

if($lAdmin->EditAction() && $POST_RIGHT == "W"){
	foreach($FIELDS as $ID=>$arFields){
		if(!$lAdmin->IsUpdated($ID)){
			continue;
		}
		$DB->StartTransaction();
		$ID = IntVal($ID);
		$ob = new SMSCSubscription;
		if(!$ob->Update($ID, $arFields))
		{
			$lAdmin->AddUpdateError(GetMessage("POST_SAVE_ERROR").$ID.": ".$ob->LAST_ERROR, $ID);
			$DB->Rollback();
		}
		$DB->Commit();
	}
}

$strError = $strOk = "";

if(($arID = $lAdmin->GroupAction()) && $POST_RIGHT=="W")
{
	if($_REQUEST['action_target']=='selected')
	{
		$cData = new SMSCSubscription;
		$rsData = $cData->GetList(array($by=>$order), $arFilter);
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
			if(!SMSCSubscription::Delete($ID))
			{
				$DB->Rollback();
				$lAdmin->AddGroupError(GetMessage("subscr_del_err"), $ID);
			}
			$DB->Commit();
			break;
		case "activate":
		case "deactivate":
			$ob = new SMSCSubscription;
			$arFields = Array("ACTIVE"=>($_REQUEST['action']=="activate"?"Y":"N"));
			if(!$ob->Update($ID, $arFields))
				$lAdmin->AddGroupError(GetMessage("subscr_save_error").$ob->LAST_ERROR, $ID);
			break;
		case "confirm":
			$ob = new SMSCSubscription;
			$arFields = Array("CONFIRMED"=>"Y");
			if(!$ob->Update($ID, $arFields))
				$lAdmin->AddGroupError(GetMessage("subscr_save_error").$ob->LAST_ERROR, $ID);
			break;
		}

	}
}

$cData = new SMSCSubscription;
$rsData = $cData->GetList(array($by=>$order), $arFilter, array("nPageSize"=>CAdminResult::GetNavSize($sTableID)));
$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();
$lAdmin->NavText($rsData->GetNavPrint(GetMessage("subscr_nav")));

$lAdmin->AddHeaders(array(
	array(	
		"id"		=> "ID",
		"content"	=> "ID",
		"sort"		=> "id",
		"align"		=> "right",
		"default"	=> true,
	),
	array(	
		"id"		=> "DATE_INSERT",
		"content"	=> GetMessage("POST_DATE_INSERT"),
		"sort"		=> "date_insert",
		"default"	=> true,
	),
	array(	
		"id"		=> "PHONE",
		"content"	=> GetMessage("subscr_addr"),
		"sort"		=> "phone",
		"default"	=> true,
	),
	array(	
		"id"		=> "USER_ID",
		"content"	=> GetMessage("subscr_user"),
		"sort"		=> "user",
		"default"	=> true,
	),
	/* array(
		"id"		=> "CONFIRMED",
		"content"	=> GetMessage("subscr_conf"),
		"sort"		=> "conf",
		"default"	=> true,
	), */
	array(
		"id"		=> "ACTIVE",
		"content"	=> GetMessage("subscr_act"),
		"sort"		=> "act",
		"default"	=> true,
	),
	array(
		"id"		=> "DATE_UPDATE",
		"content"	=> GetMessage("subscr_updated"),
		"sort"		=> "date_update",
		"default"	=> false,
	),
	/* array(
		"id"		=> "DATE_CONFIRM",
		"content"	=> GetMessage("subscr_conf_time"),
		"sort"		=> "date_confirm",
		"default"	=> false,
	),
	array(
		"id"		=> "CONFIRM_CODE",
		"content"	=> GetMessage("subscr_conf_code"),
		"sort"		=> "confirm_code",
		"default"	=> false,
	), */
));

while($arRes = $rsData->NavNext(true, "f_")):
	$row =& $lAdmin->AddRow($f_ID, $arRes);

	if($f_USER_ID > 0)
		$strUser = "[<a class='tablebodylink' href=\"/bitrix/admin/user_edit.php?ID=".$f_USER_ID."&amp;lang=".LANG."\" title=\"".GetMessage("subscr_user_edit_title")."\">".$f_USER_ID."</a>] (".$f_USER_LOGIN.") ".$f_USER_NAME." ".$f_USER_LAST_NAME;
	else
		$strUser = GetMessage("subscr_adm_anon");
	$row->AddViewField("USER_ID", $strUser);
	$row->AddCheckField("ACTIVE");
	$row->AddInputField("PHONE", array("size"=>20));
	$row->AddViewField("PHONE", '<a href="imaginweb.sms_subscr_edit.php?ID='.$f_ID.'&amp;lang='.LANG.'" title="'.GetMessage("subscr_upd").'">'.$f_PHONE.'</a>');
	$row->AddSelectField("FORMAT",array("text"=>GetMessage("POST_TEXT"),"html"=>GetMessage("POST_HTML")));
	$row->AddCheckField("CONFIRMED");

	$arActions = Array();

	$arActions[] = array(
		"ICON"=>"edit",
		"DEFAULT"=>true,
		"TEXT"=>GetMessage("subscr_upd"),
		"ACTION"=>$lAdmin->ActionRedirect("imaginweb.sms_subscr_edit.php?ID=".$f_ID)
	);
	if ($POST_RIGHT>="W")
		$arActions[] = array(
			"ICON"=>"delete",
			"TEXT"=>GetMessage("subscr_del"),
			"ACTION"=>"if(confirm('".GetMessage("subscr_del_conf")."')) ".$lAdmin->ActionDoGroup($f_ID, "delete")
		);
	$row->AddActions($arActions);

endwhile;

$lAdmin->AddFooter(
	array(
		array("title" => GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value" => $rsData->SelectedRowsCount()),
		array("counter" => true, "title" => GetMessage("MAIN_ADMIN_LIST_CHECKED"), "value" => "0"),
	)
);
$lAdmin->AddGroupActionTable(Array(
	"delete" => GetMessage("MAIN_ADMIN_LIST_DELETE"),
	"activate" => GetMessage("MAIN_ADMIN_LIST_ACTIVATE"),
	"deactivate" => GetMessage("MAIN_ADMIN_LIST_DEACTIVATE"),
	/* "confirm" => GetMessage("subscr_confirm"), */
	));

$aContext = array(
	array(
		"TEXT" => GetMessage("MAIN_ADD"),
		"LINK" => "imaginweb.sms_subscr_edit.php?lang=".LANG,
		"TITLE" => GetMessage("subscr_add_title"),
		"ICON" => "btn_new",
	),
);
$lAdmin->AddAdminContextMenu($aContext);
$lAdmin->CheckListMode();

$APPLICATION->SetTitle(GetMessage("subscr_title"));

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$oFilter = new CAdminFilter(
	$sTableID."_filter",
	array(
		GetMessage("POST_F_ID"),
		GetMessage("POST_F_INSERT"),
		GetMessage("POST_F_UPDATE"),
		GetMessage("POST_F_PHONE"),
		GetMessage("POST_F_ANONYMOUS"),
		GetMessage("POST_F_USER_ID"),
		GetMessage("POST_F_USER"),
		GetMessage("POST_F_CONFIRMED"),
		GetMessage("POST_F_ACTIVE"),
		/* GetMessage("POST_F_FORMAT"), */
		GetMessage("POST_F_DISTRIBUTION"),
	)
);
?>
<form name="find_form" method="get" action="<?=$APPLICATION->GetCurPage();?>">
<?$oFilter->Begin();?>
<tr>
	<td><b><?=GetMessage("POST_F_FIND")?>:</b></td>
	<td>
		<input type="text" size="25" name="find" value="<?=htmlspecialchars($find)?>" title="<?=GetMessage("POST_F_FIND_TITLE")?>">
		<?
		$arr = array(
			"reference" => array(
				GetMessage("POST_F_PHONE"),
				GetMessage("POST_F_ID"),
				GetMessage("POST_F_USER"),
			),
			"reference_id" => array(
				"phone",
				"id",
				"user",
			)
		);
		echo SelectBoxFromArray("find_type", $arr, $find_type, "", "");
		?>
	</td>
</tr>
<tr>
	<td><?=GetMessage("POST_F_ID")?>:</td>
	<td><input type="text" name="find_id" size="47" value="<?=htmlspecialchars($find_id)?>">&nbsp;<?=ShowFilterLogicHelp()?></td>
</tr>
<tr>
	<td><?=GetMessage("POST_F_INSERT")." (".FORMAT_DATE."):"?></td>
	<td><?=CalendarPeriod("find_insert_1", htmlspecialchars($find_insert_1), "find_insert_2", htmlspecialchars($find_insert_2), "find_form","Y")?></td>
</tr>
<tr>
	<td><?=GetMessage("POST_F_UPDATE")." (".FORMAT_DATE."):"?></td>
	<td><?=CalendarPeriod("find_update_1", htmlspecialchars($find_update_1), "find_update_2", htmlspecialchars($find_update_2), "find_form","Y")?></td>
</tr>
<tr>
	<td><?=GetMessage("POST_F_PHONE")?>:</td>
	<td><input type="text" name="find_phone" size="47" value="<?=htmlspecialchars($find_phone)?>">&nbsp;<?=ShowFilterLogicHelp()?></td>
</tr>
<tr>
	<td><?=GetMessage("POST_F_ANONYMOUS")?>:</td>
	<td><?
		$arr = array("reference"=>array(GetMessage("MAIN_YES"), GetMessage("MAIN_NO")), "reference_id"=>array("Y","N"));
		echo SelectBoxFromArray("find_anonymous", $arr, htmlspecialchars($find_anonymous), GetMessage("MAIN_ALL"));
	?></td>
</tr>
<tr>
	<td><?=GetMessage("POST_F_USER_ID")?>:</td>
	<td><input type="text" name="find_user_id" size="47" value="<?=htmlspecialchars($find_user_id)?>">&nbsp;<?=ShowFilterLogicHelp()?></td>
</tr>
<tr>
	<td><?=GetMessage("POST_F_USER")?>:</td>
	<td><input type="text" name="find_user" size="47" value="<?=htmlspecialchars($find_user)?>">&nbsp;<?=ShowFilterLogicHelp()?></td>
</tr>
<tr>
	<td><?=GetMessage("POST_F_CONFIRMED")?>:</td>
	<td><?
		$arr = array("reference"=>array(GetMessage("MAIN_YES"), GetMessage("MAIN_NO")), "reference_id"=>array("Y","N"));
		echo SelectBoxFromArray("find_confirmed", $arr, htmlspecialchars($find_confirmed), GetMessage("MAIN_ALL"));
	?></td>
</tr>
<tr>
	<td><?=GetMessage("POST_F_ACTIVE")?>:</td>
	<td><?
		$arr = array("reference"=>array(GetMessage("MAIN_YES"), GetMessage("MAIN_NO")), "reference_id"=>array("Y","N"));
		echo SelectBoxFromArray("find_active", $arr, htmlspecialchars($find_active), GetMessage("MAIN_ALL"));
	?></td>
</tr>
<tr valign="top">
	<td><?=GetMessage("POST_F_DISTRIBUTION")?>:</td>
	<td><?
		$ref = array();
		$ref_id = array();
		$rsRubric = SMSCRubric::GetList(array("LID"=>"ASC", "SORT"=>"ASC", "NAME"=>"ASC"), array("ACTIVE" => "Y"));
		while ($arRubric = $rsRubric->Fetch())
		{
			$ref[] = "[".$arRubric["ID"]."] (".$arRubric["LID"].") ".$arRubric["NAME"];
			$ref_id[] = $arRubric["ID"];
		}
		$arr = array(
			"reference" => $ref,
			"reference_id" => $ref_id);
		echo SelectBoxMFromArray("find_distribution[]", $arr, $find_distribution, "", false, 5);
	?></td>
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