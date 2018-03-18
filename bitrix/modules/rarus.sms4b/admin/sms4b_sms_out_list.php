<?
//connect all necessary files
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/rarus.sms4b/include.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/rarus.sms4b/prolog.php");
IncludeModuleLangFile(__FILE__);

if ($SMS4B->LastError != '')
{
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
	$APPLICATION->SetTitle(GetMessage("sms4b_title"));
	echo '<tr><td colspan="2">'.CAdminMessage::ShowMessage(GetMessage('CHECK_MODULE_OPT')).'</td></tr>';
	return;
}

//like in sms4b_sms_inc_list.php, if need comments, u can find them there
$module_id = "rarus.sms4b";
$SMS_RIGHT = $APPLICATION->GetGroupRight($module_id);
if($SMS_RIGHT < "R")
{
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

$sTableID = "tbl_sms_list_outgoing";
$oSort = new CAdminSorting($sTableID, "StartSend", "desc");
$lAdmin = new CAdminList($sTableID, $oSort);

function CheckFilter()
{
	global $FilterArr, $lAdmin;
	foreach ($FilterArr as $f) global $$f;

	return count($lAdmin->arFilterErrors) == 0;
}

$FilterArr = Array(
	"find_ID",
	"find_GUID",
	"find_SenderName",
	"find_Destination",
	"find_StartSend_from",
	"find_StartSend_to",
	"find_LastModified_from",
	"find_LastModified_to",
	"find_CountPart",
	"find_SendPart",
	"find_CodeType",
	"find_TextMessage",
	"find_Sale_Order",
	"find_Posting",
	"find_Events"
	);

$lAdmin->InitFilter($FilterArr);

if (CheckFilter())
{
	//filter for
	$arFilter = Array(
		"ID"				=> $find_id,
		"GUID"				=> $find_GUID,
		"SenderName"		=> $find_SenderName,
		"Destination"		=> $find_Destination,
		"StartSend_from"	=> $find_StartSend_from,
		"StartSend_to"		=> $find_StartSend_to,
		"LastModified_from"	=> $find_LastModified_from,
		"LastModified_to"	=> $find_LastModified_to,
		"CountPart"			=> $find_CountPart,
		"SendPart"			=> $find_SendPart,
		"CodeType"			=> $find_CodeType,
		"TextMessage"		=> $find_TextMessage,
		"Sale_Order"		=> $find_Sale_Order,
		"Posting"			=> $find_Posting,
		"Events"			=> $find_Events,
	);
}
?>
<?
$lAdmin->AddHeaders(array(
							array("id" => "ID","content" =>"ID","sort" =>"id","default" =>true,),
							array("id" => "GUID","content" =>GetMessage("sms_GUID"),"sort" =>"GUID","default" =>false,),
							array("id" => "SENDERNAME","content" =>GetMessage("sms_SENDERNAME"),"sort" =>"SenderName","default" =>true,),
							array("id" => "DESTINATION","content" =>GetMessage("sms_DESTINATION"),"sort" =>"Destination","align" =>"right","default" =>true,),
							array("id" => "STARTSEND","content" =>GetMessage("sms_STARTSEND"),"sort" =>"StartSend","default" =>true,),
							array("id" => "LASTMODIFIED","content" =>GetMessage("sms_LASTMODIFIED"),"sort" =>"LastModified","default" =>true,),
							/*array("id" => "COUNTPART","content" =>GetMessage("sms_COUNTPART"),"sort" =>"CountPart","default" =>true,),
							array("id" => "SENDPART","content" =>GetMessage("sms_SENDPART"),"sort" =>"SendPart","default" =>true,),*/
							array("id" => "CODETYPE","content" =>GetMessage("sms_CODETYPE"),"sort" =>"CodeType","default" =>true,),
							array("id" => "TEXTMESSAGE","content" =>GetMessage("sms_TEXTMESSAGE"),"sort" =>"TextMessage","default" =>true,),
							array("id" => "SALE_ORDER","content" =>GetMessage("sms_SALE_ORDER"),"sort" =>"Sale_Order","default" =>true,),
							array("id" => "POSTING","content" =>GetMessage("sms_POSTING"),"sort" =>"Posting","default" =>true,),
							array("id" => "EVENTS","content" =>GetMessage("sms_EVENTS"),"sort" =>"Events","default" =>true,),
							)
);
//    
/*  
if(($arID = $lAdmin->GroupAction()) && $SMS_RIGHT=="W")
{

	//for each element
	if($_REQUEST['action_target']=='selected')
	{
		$arFilter_upd = $arFilter;
		$arFilter_upd["OnlyUpdate"] = 'Y';
		$rsData = $SMS4B->GetList(array($by=>$order), $arFilter_upd);
		while($arRes = $rsData->Fetch())
			$arID[] = $arRes['id'];
	}

	foreach($arID as $ID)
	{
		if(strlen($ID)<=0)
			continue;
		$ID = IntVal($ID);

		//do action for each elemnt
		switch($_REQUEST['action'])
		{

			case "update":
				$SMS4B->UpdateStatusSms($ID);
			break;
		}
	}
}
*/
//get list of outcoming sms
$rsData = $SMS4B->GetList(array($by=>$order), $arFilter); 
$rsData = new CAdminResult($rsData, $sTableID);    
$rsData->NavStart();
$lAdmin->NavText($rsData->GetNavPrint(GetMessage("sms_nav")));

while($arRes = $rsData->NavNext(true, "f_")): 

	$row =& $lAdmin->AddRow($arRes[GetColumnName('id')], $arRes);
	$row->AddViewField("ID", $arRes[GetColumnName('id')]);
	$row->AddViewField("GUID", $arRes[GetColumnName('GUID')]);
	$row->AddViewField("SENDERNAME", $arRes[GetColumnName('SenderName')]);
	$row->AddViewField("DESTINATION", $arRes[GetColumnName('Destination')]);
	$row->AddViewField("STARTSEND", $arRes[GetColumnName('StartSend')]);
	$row->AddViewField("LASTMODIFIED", $arRes[GetColumnName('LastModified')]);
	$row->AddViewField("COUNTPART", $arRes[GetColumnName('CountPart')]);
	$row->AddViewField("SENDPART", $arRes[GetColumnName('SendPart')]);
	$row->AddViewField("CODETYPE", $arRes[GetColumnName('CodeType')]);
	$row->AddViewField("TEXTMESSAGE", str_replace("
	",'<br>',$arRes[GetColumnName('TextMessage')]));
	$row->AddViewField("SALE_ORDER", $arRes[GetColumnName('Sale_Order')]);
	$row->AddViewField("POSTING", $arRes[GetColumnName('Posting')]);
	$row->AddViewField("EVENTS", $arRes[GetColumnName('Events')]);
endwhile;

$lAdmin->AddFooter(
	array(
		array("title"=>GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value"=>$rsData->SelectedRowsCount()),
		array("counter"=>true, "title"=>GetMessage("MAIN_ADMIN_LIST_CHECKED"), "value"=>"0"),
	)
);

/*$lAdmin->AddGroupActionTable(Array(
		"update"=>GetMessage("SMS_LIST_UPDATE"),
	));
*/

$lAdmin->AddAdminContextMenu();

$lAdmin->CheckListMode();

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$APPLICATION->SetTitle(GetMessage("sms4b_title"));

$oFilter = new CAdminFilter(
	$sTableID."_filter",
	array(
		GetMessage("sms_f_GUID"),
		GetMessage("sms_f_SenderName"),
		GetMessage("sms_f_Destination"),
		GetMessage("sms_f_StartSend_from"),
		GetMessage("sms_f_LastModified_from"),
		GetMessage("sms_f_CountPart"),
		GetMessage("sms_f_SendPart"),
		GetMessage("sms_f_CodeType"),
		GetMessage("sms_f_Sale_Order"),
		GetMessage("sms_f_Posting"),
		GetMessage("sms_f_Events"),
	)
);
?>

<form name="find_form" method="get" action="<?echo $APPLICATION->GetCurPage();?>">
<?$oFilter->Begin();?>
<tr>
	<td>ID</td>
	<td>
		<input type="text" name="find_id" size="47" value="<?echo htmlspecialchars($find_id)?>">
	</td>
</tr>
<tr>
	<td>GUID</td>
	<td>
		<input type="text" name="find_GUID" size="47" value="<?echo htmlspecialchars($find_GUID)?>">
	</td>
</tr>
<tr>
	<td><?=GetMessage("sms_f_SenderName").":"?></td>
	<td><input type="text" name="find_SenderName" size="47" value="<?echo htmlspecialchars($find_SenderName)?>"></td>
</tr>
<tr>
	<td><?=GetMessage("sms_f_Destination").":"?></td>
	<td><input type="text" name="find_Destination" size="47" value="<?echo htmlspecialchars($find_Destination)?>"></td>
</tr>
<tr>
	<td><?echo GetMessage("sms_f_StartSend")." (".CLang::GetDateFormat("FULL")."):"?></td>
	<td><?echo CalendarPeriod("find_StartSend_from", htmlspecialcharsex($find_StartSend_from), "find_StartSend_to", htmlspecialcharsex($find_StartSend_to), "find_form")?></td>
</tr>
<tr>
	<td><?echo GetMessage("sms_f_LastModified")." (".CLang::GetDateFormat("FULL")."):"?></td>
	<td><?echo CalendarPeriod("find_LastModified_from", htmlspecialcharsex($find_LastModified_from), "find_LastModified_to", htmlspecialcharsex($find_LastModified_to), "find_form")?></td>
</tr>

<tr>
	<td><?=GetMessage("sms_f_CountPart").":"?></td>
	<td><input type="text" name="find_CountPart" size="47" value="<?echo htmlspecialchars($find_CountPart)?>"></td>
</tr>
<tr>
	<td><?=GetMessage("sms_f_SendPart").":"?></td>
	<td><input type="text" name="find_SendPart" size="47" value="<?echo htmlspecialchars($find_SendPart)?>"></td>
</tr>
<tr>
	<td><?=GetMessage("sms_f_CodeType").":"?></td>
	<td><input type="text" name="find_CodeType" size="47" value="<?echo htmlspecialchars($find_CodeType)?>"></td>
</tr>
<tr>
	<td><?=GetMessage("sms_f_Sale_Order").":"?></td>
	<td><input type="text" name="find_Sale_Order" size="47" value="<?echo htmlspecialchars($find_Sale_Order)?>"></td>
</tr>
<tr>
	<td><?=GetMessage("sms_f_Posting").":"?></td>
	<td><input type="text" name="find_Posting" size="47" value="<?echo htmlspecialchars($find_Posting)?>"></td>
</tr>
<tr>
	<td><?=GetMessage("sms_f_Events").":"?></td>
	<td><input type="text" name="find_Events" size="47" value="<?echo htmlspecialchars($find_Events)?>"></td>
</tr>


<?
$oFilter->Buttons(array("table_id"=>$sTableID,"url"=>$APPLICATION->GetCurPage(),"form"=>"find_form"));
$oFilter->End();
?>
</form>

<?
$lAdmin->DisplayList();
?>

<?
/* Oracle      .  */
function GetColumnName($column)
{
	global $DB;
	if ($DB->type == 'ORACLE')
		return strtoupper($column);
	else
		return $column;
}
?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>
