<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

IncludeModuleLangFile(__FILE__);

$MODULE_ID='slobel.canonical';

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$MODULE_ID."/classes/main.php");

$POST_RIGHT = $APPLICATION->GetGroupRight($MODULE_ID);

if ($POST_RIGHT == "D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$sTableID = "tbl_canonical";
$oSort = new CAdminSorting($sTableID, "ID", "desc");
$lAdmin = new CAdminList($sTableID, $oSort);

if($lAdmin->EditAction() && $POST_RIGHT=="W")
{
  foreach($FIELDS as $ID=>$arFields)
  {
    if(!$lAdmin->IsUpdated($ID))
      continue;
    
    $DB->StartTransaction();
    $ID = IntVal($ID);
    $cData = new SlobelCanonical;
    if(($rsData = $cData->GetByID($ID)) && ($arData = $rsData->Fetch()))
    {
      foreach($arFields as $key=>$value)
        $arData[$key]=$value;
      if(!$cData->Update($ID, $arData))
      {
        $lAdmin->AddGroupError(GetMessage("slobel_save_error"), $ID);
        $DB->Rollback();
      }
    }
    else
    {
      $lAdmin->AddGroupError(GetMessage("slobel_save_error")." ".GetMessage("slobel_no_canonical"), $ID);
      $DB->Rollback();
    }
    $DB->Commit();
  }
}

if(($arID = $lAdmin->GroupAction()) && $POST_RIGHT=="W")
{
  if($_REQUEST['action_target']=='selected')
  {
    $cData = new SlobelCanonical;
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
      if(!SlobelCanonical::Delete($ID))
      {
        $DB->Rollback();
        $lAdmin->AddGroupError(GetMessage("slobel_del_err"), $ID);
      }
      $DB->Commit();
      break;
    
    case "activate":
    case "deactivate":
      $cData = new SlobelCanonical;
      if(($rsData = $cData->GetByID($ID)) && ($arFields = $rsData->Fetch()))
      {
        $arFields["ACTIVE"]=($_REQUEST['action']=="activate"?"Y":"N");
        if(!$cData->Update($ID, $arFields))
          $lAdmin->AddGroupError(GetMessage("slobel_save_error"), $ID);
      }
      else
        $lAdmin->AddGroupError(GetMessage("slobel_save_error")." ".GetMessage("slobel_no_canonical"), $ID);
      break;
    }
  }
}

$cData = new SlobelCanonical;
$rsData = $cData->GetList(array($by=>$order), $arFilter);

$rsData = new CAdminResult($rsData, $sTableID);

$rsData->NavStart();

$lAdmin->NavText($rsData->GetNavPrint(GetMessage("slobel_nav")));

$lAdmin->AddHeaders(array(
  array(  "id"    =>"ID",
    "content"  =>"ID",
    "sort"    =>"id",
    "align"    =>"right",
    "default"  =>true,
  ),
  array(  "id"    =>"ACTIVE",
		  "content"  =>GetMessage("slobel_act"),
		  "sort"    =>"active",
		  "default"  =>true,
  ),
  array(  "id"    =>"RULE",
    "content"  =>GetMessage("slobel_rule"),
    "sort"    =>"rule",
    "default"  =>true,
  ),
  array(  "id"    =>"BASE",
    "content"  =>GetMessage("slobel_base"),
    "sort"    =>"base",
    "default"  =>true,
  ),
  array(  "id"    =>"FILE",
    "content"  =>GetMessage("slobel_file"),
    "sort"    =>"file",
    "default"  =>true,
  ),
));

while($arRes = $rsData->NavNext(true, "f_")):
  
  $row =& $lAdmin->AddRow($f_ID, $arRes); 
  
  
  $row->AddViewField("ID", '<a href="slobel_canonical_edit.php?ID='.$f_ID.'&lang='.LANG.'">'.$f_ID.'</a>');
  
  $row->AddCheckField("ACTIVE");
  
  $row->AddInputField("RULE", array("size"=>20));
  
  $row->AddViewField("FILE", $f_FILE==""?GetMessage("FILE_ALL"):$f_FILE);
  $row->AddInputField("FILE", array("size"=>20));

  
  $row->AddViewField("BASE", $f_BASE=="/"?GetMessage("BASE_MAIN"):empty($f_BASE)?GetMessage("BASE_THIS"):$f_BASE);
  $row->AddInputField("BASE", array("size"=>20));


  $arActions = Array();


  $arActions[] = array(
    "ICON"=>"edit",
    "DEFAULT"=>true,
    "TEXT"=>GetMessage("slobel_edit"),
    "ACTION"=>$lAdmin->ActionRedirect("slobel_canonical_edit.php?ID=".$f_ID)
  );
  

  if ($POST_RIGHT>="W")
    $arActions[] = array(
      "ICON"=>"delete",
      "TEXT"=>GetMessage("slobel_del"),
      "ACTION"=>"if(confirm('".GetMessage('slobel_del_conf')."')) ".$lAdmin->ActionDoGroup($f_ID, "delete")
    );

  $arActions[] = array("SEPARATOR"=>true);

  if(is_set($arActions[count($arActions)-1], "SEPARATOR"))
    unset($arActions[count($arActions)-1]);
  
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
    "TEXT"=>GetMessage("BTN_ADD"),
    "LINK"=>"slobel_canonical_edit.php?lang=".LANG,
    "TITLE"=>GetMessage("BTN_ADD"),
    "ICON"=>"btn_new",
  ),
);

$lAdmin->AddAdminContextMenu($aContext);

$lAdmin->CheckListMode();

$APPLICATION->SetTitle(GetMessage("MODULE_STEP1"));


require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
$lAdmin->DisplayList();
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>