<?
#################################################
#	Company developer: ITENA
#	Site: http://itena.ru
#	E-mail: info@itena.ru
#	Copyright (c) 2012 ITENA
#################################################
?>
<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/itena.panel/prolog.php");
IncludeModuleLangFile(__FILE__);

$PANEL_RIGHT = $APPLICATION->GetGroupRight("itena.panel");
if($PANEL_RIGHT=="D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

if(!CModule::IncludeModule('forum') || !CModule::IncludeModule('iblock')) 
{
  echo GetMessage("PANEL_NOMODULE");
  return; 
}
  
$forum_id = unserialize(COption::GetOptionString("itena.panel", "forum_id"));
if(!$forum_id)
{
  echo GetMessage("PANEL_NOFID");
  return;
}

$sTableID = "tbl_panel";
$oSort = new CAdminSorting($sTableID, "ID", "desc");
$lAdmin = new CAdminList($sTableID, $oSort);

$arFilterFields = Array(
	"find_fid",
	"find_date1",
  "find_date2",
	"find_news",
	"find_author",
);

$arFilter = Array();
$lAdmin->InitFilter($arFilterFields);

function CheckFilter()
{
	global $arFilterFields, $lAdmin;
	foreach ($arFilterFields as $s) global $$s;
	$bGotErr = false;
  
	$find_date1 = trim($find_date1);
	$find_date2 = trim($find_date2);

	if(strlen($find_date1) > 0 || strlen($find_date2) > 0)
	{
		$find_date1_stm = MkDateTime(ConvertDateTime($find_date1,"D.M.Y"),"d.m.Y");
		$find_date2_stm = MkDateTime(ConvertDateTime($find_date2,"D.M.Y")." 23:59:59","d.m.Y H:i:s");
		if (!$find_date1_stm && strlen(trim($find_date1)) > 0)
		{
			$bGotErr = true; 
			$lAdmin->AddUpdateError(GetMessage("PANEL_F_WRONG_START_DATE_FROM"));
		}

		if (!$find_date2_stm && strlen(trim($find_date2)) > 0)
		{
			$bGotErr = true; 
			$lAdmin->AddUpdateError(GetMessage("PANEL_F_WRONG_START_DATE_TILL"));
		}
		if (!$str && $find_date2_stm <= $find_date1_stm && strlen($find_date2_stm) > 0)
		{
			$bGotErr = true; 
			$lAdmin->AddUpdateError(GetMessage("PANEL_F_WRONG_START_FROM_TILL"));
		}
	}

	return ($bGotErr ? false : true);
}

if (CheckFilter())
{
  if($find_fid)
  {
    $arFilter["FORUM_ID"] = $find_fid;
  }
  else
  {
    $arFilter["@FORUM_ID"] = $forum_id;
  }
  if(!$find_date1 && !$find_date2)
  {
    $period = COption::GetOptionString("itena.panel", "def_period");
    if(!$period) $period = 30;
    $stmp = AddToTimeStamp(array("DD"	=> -$period), getmicrotime());
    $arFilter[">=POST_DATE"] = ConvertTimeStamp($stmp, "SHORT")." 00:00:00";
    $arFilter["<POST_DATE"] = date("d.m.Y")." 23:59:59";
  }
  else
  {
    if(strlen($find_date1) > 0)
      $arFilter[">=POST_DATE"] = Trim($find_date1);
      
    if(strlen($find_date2) > 0)
      $arFilter["<POST_DATE"] = Trim($find_date2);
  }
  if($find_news)
  {
    $arFilter["PARAM2"] = $find_news;
  }
  if($find_author)
  {
    $arFilter["AUTHOR_NAME"] = $find_author;
  }
}

if ($lAdmin->EditAction() && $PANEL_RIGHT >= "W" && check_bitrix_sessid())
{
	foreach($FIELDS as $ID => $arFields)
	{
		if(!$lAdmin->IsUpdated($ID))
			continue;
		$ID = intVal($ID);
		$arFieldsStore = array(
			"POST_MESSAGE" => $arFields['POST_MESSAGE']);
		if(!CForumMessage::Update($ID, $arFieldsStore)):
			$lAdmin->AddUpdateError($ID.": ".GetMessage("PANEL_SAVE_ERROR"), $ID);
		endif;
	}
}

if(($arID = $lAdmin->GroupAction()) && $PANEL_RIGHT=="W")
{
	if($_REQUEST['action_target']=='selected')
	{
		$cData = new CForumMessage;
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
			if(!CForumMessage::Delete($ID))
			{
				$DB->Rollback();
				$lAdmin->AddGroupError(GetMessage("PANEL_DEL_ERROR"), $ID);
			}
			$DB->Commit();
			break;
		case "activate":
		case "deactivate":
			$cData = new CForumMessage;
			if(($rsData = $cData->GetByID($ID)))
			{
				$arFields["APPROVED"]=($_REQUEST['action']=="activate"?"Y":"N");
				if(!$cData->Update($ID, $arFields))
					$lAdmin->AddGroupError(GetMessage("PANEL_SAVE_ERROR").$cData->LAST_ERROR, $ID);
			}
			else
				$lAdmin->AddGroupError(GetMessage("PANEL_SAVE_ERROR")." ".GetMessage("PANEL_NO_COMM"), $ID);
			break;
		}
	}
}
$cData = new CForumMessage;
$rsData = $cData->GetListEx(array($by=>$order), $arFilter);
$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();
$lAdmin->NavText($rsData->GetNavPrint(GetMessage("PANEL_NAV")));

$arHeaders = array(
	array(	
    "id"		  =>"LAMP",
		"content"	=>GetMessage("PANEL_T_LAMP"),
		"sort"		=>"APPROVED",
		"align"		=>"center",
		"default"	=>true,
	),
	array(	
    "id"		  =>"POST_MESSAGE",
		"content"	=>GetMessage("PANEL_T_COMMENT"),
		"sort"		=>"POST_MESSAGE",
		"align"		=>"left",
		"default"	=>true,
	),
  array(	
    "id"		  =>"DATE",
		"content"	=>GetMessage("PANEL_T_DATE"),
		"sort"		=>"POST_DATE",
		"align"		=>"left",
		"default"	=>true,
	),
	array(	
    "id"		  =>"AUTHOR",
		"content"	=>GetMessage("PANEL_T_AUTHOR"),
		"sort"		=>"AUTHOR_NAME",
		"align"		=>"left",
		"default"	=>true,
	),
	array(	
    "id"		  =>"AUTHOR_EMAIL",
		"content"	=>GetMessage("PANEL_T_AUTHOR_EMAIL"),
		"sort"		=>"AUTHOR_EMAIL",
		"align"		=>"left",
		"default"	=>true,
	),
	array(	
    "id"		  =>"IP",
		"content"	=>GetMessage("PANEL_T_IP"),
		"sort"		=>"AUTHOR_IP",
		"align"		=>"left",
		"default"	=>true,
	),
	array(	
    "id"		  =>"ELEMENT_NAME",
		"content"	=>GetMessage("PANEL_T_NAME"),
		"sort"		=>"ELEMENT_NAME",
		"align"		=>"left",
		"default"	=>true,
	),
	array(	
    "id"		  =>"ELEMENT_PREVIEW",
		"content"	=>GetMessage("PANEL_T_PREVIEW"),
		"sort"		=>"ELEMENT_PREVIEW",
		"align"		=>"left",
		"default"	=>false,
	),
);

$lAdmin->AddHeaders($arHeaders);
$arVisibleColumns = $lAdmin->GetVisibleHeaderColumns();
  
while($arRes = $rsData->NavNext(true, "f_")){
	$row =& $lAdmin->AddRow($f_ID, $arRes);
  
  $lamp = $f_APPROVED == "Y" ? "green" : "red";
	$lamphtml = '<div class="lamp-'.$lamp.'"></div>';
  
  $parser = new forumTextParser(LANGUAGE_ID, "/bitrix/images/forum/smile/");
  $arAllow = array("HTML" => "Y", "ANCHOR" => "Y", "BIU" => "Y", "IMG" => "Y", "VIDEO" => "Y", "LIST" => "Y", "QUOTE" => "Y", "CODE" => "Y", "FONT" => "Y", "SMILES" => "Y", "UPLOAD" => "Y", "NL2BR" => "Y", "TABLE" => "Y");
    $f_POST_MESSAGE = $parser->convert($f_POST_MESSAGE, $arAllow);
  
  if($f_AUTHOR_IP != $f_AUTHOR_REAL_IP && $f_AUTHOR_REAL_IP):
    $ip = "<a href=\"http://whois.domaintools.com/".$f_AUTHOR_IP."\">".$f_AUTHOR_IP."</a> (<a href=\"http://whois.domaintools.com/".$f_AUTHOR_REAL_IP."\">".$f_AUTHOR_REAL_IP."</a>)";
  else:
    $ip = "<a href=\"http://whois.domaintools.com/".$f_AUTHOR_IP."\">".$f_AUTHOR_IP."</a>";
  endif;
  if(CModule::IncludeModule("statistic"))
  {
    $arr = explode(".", $f_AUTHOR_IP);
    if(count($arr)==4)
    {
      $ip .= '<br><a href="stoplist_edit.php?lang='.LANGUAGE_ID.'&amp;net1='.intval($arr[0]).'&amp;net2='.intval($arr[1]).'&amp;net3='.intval($arr[2]).'&amp;net4='.intval($arr[3]).'">['.GetMessage("PANEL_T_STOP_LIST").']<a>';
    }
  }

  $element_name = "";
  $element_url = "";
  $element_preview = "";
  if(in_array("ELEMENT_NAME", $arVisibleColumns) || in_array("ELEMENT_PREVIEW", $arVisibleColumns))
  {
    $obIBlockElement = CIBlockElement::GetList(Array(), Array("ID" => intval($f_PARAM2)), false, false, Array("NAME", "DETAIL_PAGE_URL", "PREVIEW_TEXT"));
    if($arIBlockElement = $obIBlockElement->GetNext())
    {
      $element_name = $arIBlockElement["NAME"];
      $element_url = $arIBlockElement["DETAIL_PAGE_URL"];
      $element_preview = $arIBlockElement["PREVIEW_TEXT"];
    }
  }
    
	$row->AddViewField("LAMP", $lamphtml);
	//$row->AddInputField("COMMENT", array("size"=>20));
	$row->AddViewField("POST_MESSAGE", $f_POST_MESSAGE);
	$row->AddViewField("DATE", $f_POST_DATE);
	$row->AddViewField("AUTHOR", $f_AUTHOR_NAME);
	$row->AddViewField("AUTHOR_EMAIL", $f_AUTHOR_EMAIL);
	$row->AddViewField("IP", $ip);
  $row->AddViewField("ELEMENT_NAME", "<a href=\"".$element_url."\" target=\"_blank\">".$element_name."</a>");  
  $row->AddViewField("ELEMENT_PREVIEW", substr($element_preview, 0, 50));
  
	$arActions = Array();

	$arActions[] = array(
		"ICON"=>"edit",
		"DEFAULT"=>true,
		"TEXT"=>GetMessage("PANEL_EDIT"),
		"ACTION"=>$lAdmin->ActionRedirect("panel_comments_edit.php?ID=".$f_ID)
	);
  
	if ($PANEL_RIGHT>="W")
		$arActions[] = array(
			"ICON"=>"activate",
			"TEXT"=>GetMessage("PANEL_ACT"),
			"ACTION"=>$lAdmin->ActionDoGroup($f_ID, "activate")
		);
	if ($PANEL_RIGHT>="W")
		$arActions[] = array(
			"ICON"=>"deactivate",
			"TEXT"=>GetMessage("PANEL_DEACT"),
			"ACTION"=>$lAdmin->ActionDoGroup($f_ID, "deactivate")
		);
	if ($PANEL_RIGHT>="W")
		$arActions[] = array(
			"ICON"=>"delete",
			"TEXT"=>GetMessage("PANEL_DEL"),
			"ACTION"=>"if(confirm('".GetMessage('PANEL_DEL_CONF')."')) ".$lAdmin->ActionDoGroup($f_ID, "delete")
		);

	if(is_set($arActions[count($arActions)-1], "SEPARATOR"))
		unset($arActions[count($arActions)-1]);
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
	"activate"=>GetMessage("MAIN_ADMIN_LIST_ACTIVATE"),
	"deactivate"=>GetMessage("MAIN_ADMIN_LIST_DEACTIVATE"),
	));
  
$lAdmin->AddAdminContextMenu(Array());
$lAdmin->CheckListMode();

$APPLICATION->SetTitle(GetMessage('PANEL_TITLE'));
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$oFilter = new CAdminFilter(
	$sTableID."_filter",
	array(
		GetMessage("PANEL_F_FID"),
		GetMessage("PANEL_F_NEWS"),
		GetMessage("PANEL_F_AUTHOR"),
	)
);
?>
<form name="find_form" method="get" action="<?echo $APPLICATION->GetCurPage();?>">
<?$oFilter->Begin();?>
<tr>
	<td><?echo GetMessage("PANEL_F_DATE")." (".CLang::GetDateFormat("SHORT")."):"?></td>
	<td><?echo CalendarPeriod("find_date1", $find_date1, "find_date2", $find_date2, "find_form","Y")?></td>
</tr>
<tr>
	<td><?echo GetMessage("PANEL_F_FID").":"?></td>
	<td>
  <?
	$ref = array();
	$ref_id = array();
  $arFilter = array("ACTIVE" => "Y");
  $arOrder = array("SORT"=>"ASC", "NAME"=>"ASC");
  $rs = CForumNew::GetList($arOrder, $arFilter);
  while ($ar = $rs->Fetch())
  { 
    if(count($forum_id) > 0 && !in_array($ar["ID"], $forum_id)) continue;
		$ref[] = $ar["NAME"];
		$ref_id[] = $ar["ID"];
  }
  echo SelectBoxFromArray("find_fid", array("reference" => $ref, "reference_id" => $ref_id), $find_fid, GetMessage("PANEL_F_FID_ALL"));
	?>
  </td>
</tr>
<tr>
	<td><?echo GetMessage("PANEL_F_NEWS").":"?></td>
	<td><input type="text" name="find_news" size="10" value="<?echo htmlspecialchars($find_news)?>"><?=ShowFilterLogicHelp()?></td>
</tr>
<tr>
	<td><?echo GetMessage("PANEL_F_AUTHOR").":"?></td>
	<td><input type="text" name="find_author" size="20" value="<?echo htmlspecialchars($find_author)?>"><?=ShowFilterLogicHelp()?></td>
</tr>
<?
$oFilter->Buttons(array("table_id"=>$sTableID,"url"=>$APPLICATION->GetCurPage(),"form"=>"find_form"));
$oFilter->End();
?>
</form>
<?
$lAdmin->DisplayList();
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>