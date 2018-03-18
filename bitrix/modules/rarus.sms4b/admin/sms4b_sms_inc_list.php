<?
//initialize all needed files
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php"); //first prolog
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/rarus.sms4b/include.php"); // module initialize
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/rarus.sms4b/prolog.php"); // module prolog
//initialize lang file
IncludeModuleLangFile(__FILE__);

if ($SMS4B->LastError != '')
{
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
	$APPLICATION->SetTitle(GetMessage("sms4b_title")); 
	echo '<tr><td colspan="2">'.CAdminMessage::ShowMessage(GetMessage('CHECK_MODULE_OPT')).'</td></tr>';
	return;
}

$module_id = "rarus.sms4b";
$SMS_RIGHT = $APPLICATION->GetGroupRight($module_id);
if($SMS_RIGHT < "R") 
{
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

$sTableID = "tbl_sms_list_inc";// table ID
$oSort = new CAdminSorting($sTableID, "Moment", "desc"); //sort object
$lAdmin = new CAdminList($sTableID, $oSort); //main list object

//checking filter values
function CheckFilter()
{
	global $FilterArr, $lAdmin;
	foreach ($FilterArr as $f) global $$f;
	
	return count($lAdmin->arFilterErrors) == 0; //if some errors took place, return false;
}

//description of filter elements
$FilterArr = Array(
	"find_GUID",
	"find_Moment_from",
	"find_Moment_to",
	"find_TimeOff_from",
	"find_TimeOff_to",
	"find_Source",
	"find_Destination",
	"find_Body",
	"find_Total",
);

//init filter
$lAdmin->InitFilter($FilterArr);

//if filter values are correct
if (CheckFilter())
{
	//array for GetList function
	$arFilter = Array(
		"GUID"			=> $find_GUID,
		"Moment_from"	=> $find_Moment_from,
		"Moment_to"		=> $find_Moment_to,
		"TimeOff_from"	=> $find_LastModified_from,
		"TimeOff_to"	=> $find_LastModified_to,
		"Source"		=> $find_Source,
		"Destination"	=> $find_Destination,
		"Body"			=> $find_Body,
		"Total"			=> $find_Total,
	);
}

?>
<?
$lAdmin->AddHeaders(array(
		array("id" => "GUID","content" => GetMessage("sms_GUID"),"sort" => "GUID","default" => false,),
		array("id" => "MOMENT","content" => GetMessage("sms_MOMENT"),"sort" => "Moment","default" => true,),
		array("id" => "TIMEOFF","content" => GetMessage("sms_TIMEOFF"),"sort" => "TimeOff","align" =>"right","default" => false,),
		array("id" => "SOURCE","content" => GetMessage("sms_SOURCE"),"sort" => "Source","default" => true,),
		array("id" => "DESTINATION","content" => GetMessage("sms_DESTINATION"),"sort" => "Destination","default" => true,),
		array("id" => "BODY","content" => GetMessage("sms_BODY"),"sort" => "Body","default" => true,),
		array("id" => "TOTAL","content" => GetMessage("sms_TOTAL"),"sort" => "Total","default" => true,
	),
));

//refresh incoming messages from gateway
$SMS4B->LoadIncoming();

//get from database incoming messages
$rsData = $SMS4B->GetListInc(array($by=>$order), $arFilter);

//transform to object of class CAdminResult
$rsData = new CAdminResult($rsData, $sTableID);

//page navigation ...
$rsData->NavStart();
//typical operation
$lAdmin->NavText($rsData->GetNavPrint(GetMessage("sms_nav")));

while($arRes = $rsData->NavNext(true, "f_")):
	
	$row =& $lAdmin->AddRow($arRes[GetColumnName('GUID')], $arRes); 
	$row->AddViewField("GUID", $arRes[GetColumnName('GUID')]);
	$row->AddViewField("MOMENT", $arRes[GetColumnName('Moment')]);
	$row->AddViewField("TIMEOFF", $arRes[GetColumnName('TimeOff')]);
	$row->AddViewField("SOURCE", $arRes[GetColumnName('Source')]);
	$row->AddViewField("DESTINATION", $arRes[GetColumnName('Destination')]);
	
	if (intval($arRes[GetColumnName('Coding')] == 0))
	{
		$row->AddViewField("BODY", htmlspecialchars(str_replace("
",'<br>',$SMS4B->decode($arRes[GetColumnName('Body')], $arRes[GetColumnName('Coding')]))));
	}
	else
	{
		$row->AddViewField("BODY", str_replace("
",'<br>',$SMS4B->decode($arRes[GetColumnName('Body')], $arRes[GetColumnName('Coding')])));	
	}
	$row->AddViewField("TOTAL", $arRes[GetColumnName('Total')]);
endwhile;

$lAdmin->AddFooter(
	array(
		array("title"=>GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value"=>$rsData->SelectedRowsCount()), //elements count
		array("counter"=>true, "title"=>GetMessage("MAIN_ADMIN_LIST_CHECKED"), "value"=>"0"), //selected elements
	)
);

$lAdmin->CheckListMode();

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php"); //second prolog

$APPLICATION->SetTitle(GetMessage("sms4b_title"));

//create filter object
$oFilter = new CAdminFilter(
	$sTableID."_filter",
	array(
		GetMessage("sms_f_delivery"),
		GetMessage("sms_f_active"),
		GetMessage("sms_f_Source"),
		GetMessage("sms_f_Destination"),
		GetMessage("sms_f_Total"),
	)
);
?>

<form name="find_form" method="get" action="<?=$APPLICATION->GetCurPage();?>">
<?$oFilter->Begin();?>
<tr>
	<td>GUID</td>
	<td>
		<input type="text" name="find_GUID" size="47" value="<?echo htmlspecialchars($find_GUID)?>" />
	</td>
</tr>
<tr>
	<td><?echo GetMessage("sms_f_Moment")." (".CLang::GetDateFormat("FULL")."):"?></td>
	<td><?echo CalendarPeriod("find_Moment_from", htmlspecialcharsex($find_Moment_from), "find_Moment_to", htmlspecialcharsex($find_StartSend_to), "find_form")?></td>
</tr>
<tr>
	<td><?echo GetMessage("sms_f_TimeOff")." (".CLang::GetDateFormat("FULL")."):"?></td>
	<td><?echo CalendarPeriod("find_TimeOff_from", htmlspecialcharsex($find_TimeOff_from), "find_TimeOff_to", htmlspecialcharsex($find_TimeOff_to), "find_form")?></td>
</tr>
<tr>
	<td><?=GetMessage("sms_f_Source").":"?></td>
	<td><input type="text" name="find_Source" size="47" value="<?echo htmlspecialchars($find_Source)?>"></td>
</tr>
<tr>
	<td><?=GetMessage("sms_f_Destination").":"?></td>
	<td><input type="text" name="find_Destination" size="47" value="<?echo htmlspecialchars($find_Destination)?>"></td>
</tr>
<tr>
	<td><?=GetMessage("sms_f_Total").":"?></td>
	<td><input type="text" name="find_Total" size="47" value="<?echo htmlspecialchars($find_Total)?>"></td>
</tr>


<?
$oFilter->Buttons(array("table_id"=>$sTableID, "url"=>$APPLICATION->GetCurPage(), "form"=>"find_form"));
$oFilter->End();
?>
</form>

<?$lAdmin->DisplayList();?>

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
