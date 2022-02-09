<?
/*
##############################################
# Bitrix Site Manager                        #
# Copyright (c) 2002-2007 Bitrix             #
# http://www.bitrixsoft.com                  #
# mailto:admin@bitrixsoft.com                #
##############################################
*/
require_once(dirname(__FILE__)."/../include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/prolog_user.php");
define("HELP_FILE", "users/task_admin.php");

if (!$USER->CanDoOperation('view_tasks'))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

IncludeModuleLangFile(__FILE__);
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/admin/task_description.php");

$modules = COperation::GetAllowedModules();
$arModuleRef = Array(GetMessage("TASK_FILTER_MAIN"));
$arModuleRefId = Array('main');
for($i = 0, $l = count($modules); $i < $l;$i++)
{
	$MID = $modules[$i];
	if($MID=="main" || !file_exists($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/".$MID."/install/index.php"))
		continue;
	IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/".$MID."/admin/task_description.php");
	if (!($m = CModule::CreateModuleObject($MID)))
		continue;
	$arModuleRef[] = htmlspecialcharsbx($m->MODULE_NAME);
	$arModuleRefId[] = $MID;
}
$arr = array("reference" => $arModuleRef, "reference_id" => $arModuleRefId);


// ������������� �������
$sTableID = "tbl_user_task";
// ������������� ����������
$oSort = new CAdminSorting($sTableID, "c_sort", "asc");
// ������������� ������
$lAdmin = new CAdminList($sTableID, $oSort);

// ������������� ���������� ������ - �������
$arFilterFields = Array(
	"find",
	"find_type",
	"find_id",
	"find_letter",
	"find_module_id",
	"find_sys",
	"find_binding"
);

$lAdmin->InitFilter($arFilterFields);

function CheckFilter($arFields) // �������� ��������� �����
{
	global $strError;
	$str = "";
	$strError .= $str;
	if(strlen($str)>0)
	{
		global $lAdmin;
		$lAdmin->AddFilterError($str);
		return false;
	}

	return true;
}
$arFilter = Array();
if(CheckFilter($arFilterFields))
{
	$arFilter = Array(
		"ID"			=> ($find!='' && $find_type == "id"? $find : $find_id),
		"LETTER"		=> $find_letter,
		"MODULE_ID"	=> $find_module_id,
		"SYS"			=> $find_sys,
		"BINDING"	=> $find_binding
	);
}

// ��������� �������������� (����� �������!)
if($lAdmin->EditAction() && $USER->CanDoOperation('edit_tasks'))
{
	foreach($FIELDS as $ID=>$arFields)
	{
		$ID = IntVal($ID);

		if(!$lAdmin->IsUpdated($ID))
			continue;
		CTask::Update($ID, $arFields);
	}
}

// ��������� �������� ��������� � ���������
if(($arID = $lAdmin->GroupAction()) && $USER->CanDoOperation('edit_tasks'))
{
	if($_REQUEST['action_target']=='selected')
	{
		$arID = Array();
		$rsData = CTask::GetList(Array($by=>$order), $arFilter);
		while($arRes = $rsData->Fetch())
			$arID[] = $arRes['ID'];
	}

	if ($_REQUEST['action'] == "delete")
	{
		foreach($arID as $ID)
		{
			if(strlen($ID)<=0)
				continue;
			CTask::Delete($ID);
		}
	}
}
// ������������� ������ - ������� ������.
$rsData = CTask::GetList(Array($by=>$order), $arFilter);
$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();

// ��������� ���������� ������
$lAdmin->NavText($rsData->GetNavPrint(GetMessage("PAGES")));

// ��������� ������
$lAdmin->AddHeaders(array(
	array("id"=>"ID", "content"=>"ID","sort"=>"id", "default"=>false, "align"=>"right"),
	array("id"=>"NAME", "content"=>GetMessage("NAME"), "sort"=>"", "default"=>true),
	array("id"=>"LETTER", "content"=>GetMessage("LETTER"), "sort"=>"letter", "default"=>true),
	array("id"=>"DESCRIPTION", "content"=>GetMessage("MAIN_DESCRIPTION"), "sort"=>"", "default"=>true),
	array("id"=>"MODULE_ID", "content"=>GetMessage("MAIN_MODULE_ID"),  "sort"=>"module_id", "default"=>true),
	array("id"=>"SYS", "content"=>GetMessage("SYS"), "sort"=>"sys", "default"=>true),
	array("id"=>"BINDING", "content"=>GetMessage("BINDING"), "sort"=>"binding", "default"=>true)
));

// ���������� ������
while($arRes = $rsData->NavNext(true, "f_"))
{
	$row =& $lAdmin->AddRow($f_ID, $arRes, "task_edit.php?lang=".LANGUAGE_ID."&ID=".$f_ID, GetMessage("MAIN_EDIT_TITLE"));
	$row->AddViewField("ID", "<a href='task_edit.php?lang=".LANGUAGE_ID."&ID=".$f_ID."' title='".GetMessage("MAIN_EDIT_TITLE")."'>".$f_ID."</a>");

	$sys = (strtoupper($f_SYS) == 'Y');
	//$row->AddViewField("NAME", $f_TITLE);
	$row->AddViewField("NAME", "<a href='task_edit.php?lang=".LANGUAGE_ID."&ID=".$f_ID."' title='".GetMessage("MAIN_EDIT_TITLE")."'>".$f_TITLE."</a>");
	$row->AddViewField("DESCRIPTION", $f_DESC);
	$module_id_ = $f_MODULE_ID;
	if (strlen($mid_loc = GetMessage("TASK_FILTER_".strtoupper($module_id_))) > 0)
		$module_id_ = $mid_loc;
	$row->AddViewField("MODULE_ID", $module_id_);
	$row->AddViewField("LETTER", $f_LETTER);

	$row->AddViewField("SYS", ($sys ? GetMessage("MAIN_YES") : GetMessage("MAIN_NO")));
	$row->AddViewField("BINDING", GetMessage("TASK_BINDING_".strtoupper($f_BINDING)));
	$arActions = Array();
	$arActions[] = array("ICON"=>"edit", "TEXT"=>(($sys) ? GetMessage("MENU_VIEW") : GetMessage("MAIN_ADMIN_MENU_EDIT")),"DEFAULT" => true, "ACTION"=>$lAdmin->ActionRedirect("task_edit.php?ID=".$f_ID));
	$arActions[] = array("ICON"=>"copy", "TEXT"=>GetMessage("MAIN_ADMIN_MENU_COPY"), "ACTION"=>$lAdmin->ActionRedirect("task_edit.php?COPY_ID=".$f_ID));

	if($USER->CanDoOperation('edit_tasks') && (!$sys))
	{
		$arActions[] = array("SEPARATOR"=>true);
		$arActions[] = array("ICON"=>"delete", "TEXT"=>GetMessage("MAIN_ADMIN_MENU_DELETE"), "ACTION"=>"if(confirm('".GetMessage('CONFIRM_DEL_TASK')."')) ".$lAdmin->ActionDoGroup($f_ID, "delete"));
	}
	$row->AddActions($arActions);
}

// "������" ������
$lAdmin->AddFooter(
	array(
		array("title"=>GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value"=>$rsData->SelectedRowsCount()),
		array("counter"=>true, "title"=>GetMessage("MAIN_ADMIN_LIST_CHECKED"), "value"=>"0"),
	)
);

// ����� ����� � �������� ����������, ...
$lAdmin->AddGroupActionTable(Array(
	"delete"=>GetMessage("MAIN_ADMIN_LIST_DELETE")
));

$aContext = array(
	array(
		"TEXT"	=> GetMessage("ADD_TASK"),
		"LINK"	=> "task_edit.php?lang=".LANGUAGE_ID,
		"TITLE"	=> GetMessage("ADD_TASK_TITLE"),
		"ICON"	=> "btn_new"
	)
);
$lAdmin->AddAdminContextMenu($aContext);

// �������� �� ����� ������ ������ (� ������ ������, ������ ������ ����������� �� �����)
$lAdmin->CheckListMode();
$APPLICATION->SetTitle(GetMessage("TITLE"));
require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_admin_after.php");
?>
<form name="find_form" method="GET" action="<?echo $APPLICATION->GetCurPage()?>?">
<?
$arFilter = Array(
		"ID"			=> $find_id,
		"LETTER"		=> $find_letter,
		"MODULE_ID"	=> $find_module_id,
		"SYS"			=> $find_sys,
		"BINDING"	=> $find_binding
	);

$oFilter = new CAdminFilter(
	$sTableID."_filter",
	array(
		GetMessage('TASK_FILTER_LETTER'),
		GetMessage('TASK_FILTER_MODULE_ID'),
		GetMessage('TASK_FILTER_SYS'),
		GetMessage('TASK_FILTER_BINDING')
	)
);
$oFilter->Begin();
?>
<tr>
	<td nowrap><?echo GetMessage("TASK_FILTER_ID")?>:</td>
	<td nowrap><input type="text" name="find_id" value="<?echo htmlspecialcharsbx($find_id)?>" size="35"></td>
</tr>
<tr>
	<td nowrap><?echo GetMessage("TASK_FILTER_LETTER")?>:</td>
	<td nowrap><input type="text" name="find_letter" value="<?echo htmlspecialcharsbx($find_letter)?>" size="10"></td>
</tr>
<tr>
	<td nowrap><?echo GetMessage("TASK_FILTER_MODULE_ID")?>:</td>
	<td nowrap>
	<?
	$arr = array("reference" => $arModuleRef, "reference_id" => $arModuleRefId);
	echo SelectBoxFromArray("find_module_id", $arr, htmlspecialcharsbx($find_module_id));
	?>
	</td>
</tr>
<tr>
	<td nowrap><?echo GetMessage("TASK_FILTER_SYS")?>:</td>
	<td nowrap>
		<?
		$arr = array("reference" => array(GetMessage("TASK_FILTER_ANY"), GetMessage("MAIN_YES"), GetMessage("MAIN_NO")), "reference_id" => array("", "Y", "N"));
		echo SelectBoxFromArray("find_sys", $arr, htmlspecialcharsbx($find_sys));
		?>
	</td>
</tr>
<tr>
	<td nowrap><?echo GetMessage("TASK_FILTER_BINDING")?>:</td>
	<td nowrap>
		<?
		$bindings = COperation::GetBindingList();
		$arRef = Array(GetMessage("TASK_FILTER_ANY"));
		$arRefId = Array('');
		
		for($i = 0,$l = count($bindings); $i < $l;$i++)
		{
			$b = $bindings[$i];
			$text = GetMessage('TASK_BINDING_'.strtoupper($b));
			if (!$text)
				$text = strtoupper($b);			
			$arRef[] = $text;
			$arRefId[] = $b;
		}
		$arr = array("reference" => $arRef, "reference_id" => $arRefId);
		echo SelectBoxFromArray("find_binding", array("reference" => $arRef, "reference_id" => $arRefId), htmlspecialcharsbx($find_binding));	
		?>
	</td>
</tr>
<?
$oFilter->Buttons(array("table_id"=>htmlspecialcharsbx($sTableID), "url"=>$APPLICATION->GetCurPage(), "form"=>"find_form"));
$oFilter->End();
?>
</form>
<?$lAdmin->DisplayList();?>

<?require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");?>