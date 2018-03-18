<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/ghj2k2.mailinfo/prolog.php");
IncludeModuleLangFile(__FILE__);
$MOD_RIGHT = $APPLICATION->GetGroupRight("ghj2k2.mailinfo");
if($MOD_RIGHT<"R") $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$APPLICATION->SetTitle(GetMessage("MAILINFO_INDEX_TITLE"));
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/ghj2k2.mailinfo/include.php");

if(!CModule::IncludeModule('ghj2k2.mailinfo'))
  return;

$CMailinfo = new CMailinfo();
$all=$CMailinfo->getMailTemplates();
ksort($all);

$sTableID = "mailinfo_success";
$oSort = new CAdminSorting($sTableID, "id", "desc");// инициализация сортировки
$lAdmin = new CAdminList($sTableID, $oSort);// инициализация списка

//названия всех input'ов фильтра
$arFilterFields = Array(
  "filter_period_from",
  "filter_period_to",
  "filter_event_type"
);

$lAdmin->InitFilter($arFilterFields);//инициализация фильтра
$filter = new CAdminFilter(
  $sTableID."_filter", 
  array(
    GetMessage("MAILINFO_DATE_FILTER_2"),
  )
);


$arFilter=array('SUCCESS_EXEC'=>'Y');
if(strlen($filter_period_from))
  $arFilter['DATE_EXEC:>=']=date('Y-m-d H:i:s', strtotime($filter_period_from));

if(strlen($filter_period_to))
  $arFilter['DATE_EXEC:<=']=date('Y-m-d H:i:s', strtotime($filter_period_to));

if(strlen($filter_event_type))
  $arFilter['EVENT_NAME']=$filter_event_type;

if(strlen($_REQUEST['order']) && strlen($_REQUEST['by']))
  $rsData = $CMailinfo->getEvents($arFilter, $_REQUEST['by'], $_REQUEST['order']);
else
  $rsData = $CMailinfo->getEvents($arFilter);

$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();
$lAdmin->NavText($rsData->GetNavPrint(GetMessage("MAIL_LOG_NAVIGATION")));

$arHeaders = Array();
$arHeaders[] = Array("id"=>"ID", "content"=>GetMessage("MAILINFO_LOG_ID"), "default"=>false, "sort" => "id");
$arHeaders[] = Array("id"=>"EVENT_NAME", "content"=>GetMessage("MAILINFO_LOG_EVENT_NAME"), "default"=>true, "sort" => "event_name");
$arHeaders[] = Array("id"=>"NAME", "content"=>GetMessage("MAILINFO_LOG_NAME"), "default"=>true, "sort" => false);
$arHeaders[] = Array("id"=>"DATE_INSERT", "content"=>GetMessage("MAILINFO_LOG_DATE_INSERT"), "default"=>false, "sort" => "date_insert");
$arHeaders[] = Array("id"=>"DATE_EXEC", "content"=>GetMessage("MAILINFO_LOG_DATE_EXEC"), "default"=>true, "sort" => "date_exec");
$arHeaders[] = Array("id"=>"SUCCESS_EXEC", "content"=>GetMessage("MAILINFO_LOG_SUCCESS_EXEC"), "default"=>true, "sort" => false);
$arHeaders[] = Array("id"=>"DUPLICATE", "content"=>GetMessage("MAILINFO_LOG_DUPLICATE"), "default"=>false, "sort" => false);


$lAdmin->AddHeaders($arHeaders);

while($arRes = $rsData->GetNext(true, false)) {
  
  $row =& $lAdmin->AddRow($f_ID, $arRes);
  
  $row->AddViewField("ID", $arRes["ID"]);
  $codeTemplates=$CMailinfo->getMailTemplates($arRes['EVENT_NAME']);
  if(is_array($codeTemplates) && count($codeTemplates)) {
    $row->AddViewField("EVENT_NAME", '<a href="mailinfo_view.php?ID='.$arRes['ID'].'&EVENT='.$arRes['EVENT_NAME'].'&lang='.LANG.GetFilterParams("filter_").'">'.$arRes['EVENT_NAME'].'</a>');
    $row->AddViewField("NAME", $codeTemplates["NAME"]);
  }
  
  $row->AddViewField("SUCCESS_EXEC", getStatus($arRes['SUCCESS_EXEC']));
  $row->AddViewField("DUPLICATE", getStatus($arRes['DUPLICATE']));
    
  $arActions = Array();
  $arActions[] = array(
    "ICON"=>"edit",
    "TEXT"=>GetMessage("MAILINFO_VIEW"),    
    "DEFAULT" => "Y",
    "ACTION"=>$lAdmin->ActionRedirect("mailinfo_view.php?ID=".$arRes['ID']."&EVENT=".$arRes['EVENT_NAME']."&lang=".LANG.GetFilterParams("filter_"))
    
  );
  $arActions[] = array("SEPARATOR"=>true);
  $arActions[] = array(
    "ICON"=>"edit",
    "TEXT"=>GetMessage("MAILINFO_TEMPLATE_VIEW"),    
    "ACTION"=>$lAdmin->ActionRedirect('/bitrix/admin/type_edit.php?EVENT_NAME='.$arRes['EVENT_NAME'])
  );
  
  $row->AddActions($arActions);    
}

$lAdmin->AddFooter(
  array(
    array("title"=>GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value"=>$rsData->SelectedRowsCount()),
  )
);

$lAdmin->AddAdminContextMenu();
$lAdmin->CheckListMode();

require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_admin_after.php");?>

<form method="get" action="<?=$APPLICATION->GetCurPage()?>" name="find_form">
<?$filter->Begin();?>
  <tr>
    <td><?echo GetMessage("MAILINFO_DATE_FILTER_1")?>:</td>
    <td>
      <?echo CalendarDate("filter_period_from", $filter_period_from)?> ... <?echo CalendarDate("filter_period_to", $filter_period_to)?>
    </td>
  </tr>
  <tr>
    <td><?echo GetMessage("MAILINFO_DATE_FILTER_2")?>:</td>
    <td>
    	<select name="filter_event_type">
		<option value=""></option>
		<?foreach($all as $k=>$v):?>
			<option value="<?=$k?>" <?=($k==$filter_event_type)?'selected="selected"':''?>><?=$k?></option>
      		<?endforeach;?>
    	</select>
    </td>
  </tr>

<?$filter->Buttons(array("table_id"=>$sTableID, "url"=>$APPLICATION->GetCurPage(), "form"=>"find_form"));$filter->End();?>
</form>

<?$lAdmin->DisplayList();

echo BeginNote();?>

<img src="/bitrix/themes/.default/icons/ghj2k2.mailinfo/task-done.png"> - <?=GetMessage('MAILINFO_TASK_DONE')?><br/>
<img src="/bitrix/themes/.default/icons/ghj2k2.mailinfo/task-error.png"> - <?=GetMessage('MAILINFO_TASK_ERROR')?><br/>
<img src="/bitrix/themes/.default/icons/ghj2k2.mailinfo/task-alert.png"> - <?=GetMessage('MAILINFO_TASK_ALERT')?><br/>    
<img src="/bitrix/themes/.default/icons/ghj2k2.mailinfo/task-none.png"> - <?=GetMessage('MAILINFO_TASK_NONE')?><br/>
<img src="/bitrix/themes/.default/icons/ghj2k2.mailinfo/task-wait.png"> - <?=GetMessage('MAILINFO_TASK_WAIT')?><br/>    
<p><?=GetMessage('BITRIX_API_INFO')?></p>
<?echo EndNote();
require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");
?>