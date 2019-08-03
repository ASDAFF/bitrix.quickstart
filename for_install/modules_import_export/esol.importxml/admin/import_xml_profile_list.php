<?
require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
$moduleId = 'esol.importxml';
$moduleFilePrefix = 'esol_import_xml';
$moduleJsId = str_replace('.', '_', $moduleId);
$moduleDemoExpiredFunc = $moduleJsId.'_demo_expired';
$moduleShowDemoFunc = $moduleJsId.'_show_demo';
CModule::IncludeModule($moduleId);
CJSCore::Init(array('fileinput', $moduleJsId));
require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/prolog.php");
IncludeModuleLangFile(__FILE__);

include_once(dirname(__FILE__).'/../install/demo.php');
if ($moduleDemoExpiredFunc()) {
	require ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
	$moduleShowDemoFunc();
	require ($DOCUMENT_ROOT."/bitrix/modules/main/include/epilog_admin.php");
	die();
}

$MODULE_RIGHT = $APPLICATION->GetGroupRight($moduleId);
if($MODULE_RIGHT < "W") $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$oProfile = new \Bitrix\EsolImportxml\Profile();
$sTableID = "tbl_esolimportxml_profile";
$instance = \Bitrix\Main\Application::getInstance();
$context = $instance->getContext();
$request = $context->getRequest();

if(isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'export')
{
	$oProfile->OutputBackup();
}

$oSort = new CAdminSorting($sTableID, "ID", "asc");
$lAdmin = new CAdminList($sTableID, $oSort);

$arFilterFields = array(
	"filter_name"
);

$lAdmin->InitFilter($arFilterFields);

$filter = array();

if (strlen($filter_name) > 0)
	$filter["%NAME"] = trim($filter_name);

if($lAdmin->EditAction())
{
	foreach ($_POST['FIELDS'] as $ID => $arFields)
	{
		$ID = (int)$ID;

		if ($ID <= 0 || !$lAdmin->IsUpdated($ID))
			continue;

		$oProfile = new \Bitrix\EsolImportxml\Profile();
		
		$dbRes = \Bitrix\EsolImportxml\ProfileTable::update($ID, $arFields);
		if(!$dbRes->isSuccess())
		{
			$error = '';
			if($dbRes->getErrors())
			{
				foreach($dbRes->getErrors() as $errorObj)
				{
					$error .= $errorObj->getMessage().'. ';
				}
			}
			if($error)
				$lAdmin->AddUpdateError($error, $ID);
			else
				$lAdmin->AddUpdateError(GetMessage("ESOL_IX_ERROR_UPDATING_REC")." (".$arFields["ID"].", ".$arFields["NAME"].", ".$arFields["SORT"].")", $ID);
		}
	}
}

if(($arID = $lAdmin->GroupAction()))
{
	if($_REQUEST['action_target']=='selected')
	{
		$arID = Array();
		$dbResultList = \Bitrix\EsolImportxml\ProfileTable::getList(array('filter'=>$filter, 'select'=>array('ID')));
		while($arResult = $dbResultList->Fetch())
			$arID[] = $arResult['ID'];
	}

	foreach ($arID as $ID)
	{
		if(strlen($ID) <= 0)
			continue;

		switch ($_REQUEST['action'])
		{
			case "delete":
				$dbRes = \Bitrix\EsolImportxml\ProfileTable::delete($ID);
				if(!$dbRes->isSuccess())
				{
					$error = '';
					if($dbRes->getErrors())
					{
						foreach($dbRes->getErrors() as $errorObj)
						{
							$error .= $errorObj->getMessage().'. ';
						}
					}
					if($error)
						$lAdmin->AddGroupError($error, $ID);
					else
						$lAdmin->AddGroupError(GetMessage("ESOL_IX_ERROR_DELETING_TYPE"), $ID);
				}
				break;
		}
	}
}

$params = array(
	'select' => array('ID', 'ACTIVE', 'NAME', 'DATE_START', 'DATE_FINISH', 'SORT'),
	'filter' => $filter
);

$params['order'] = array(ToUpper($by) => ToUpper($order));

$dbRes = \Bitrix\EsolImportxml\ProfileTable::getList($params);

$result = array();

while($profile = $dbRes->fetch())
{
	$profile['ID']--;
	$result[] = $profile;
}

$dbRes = new CDBResult();
$dbRes->InitFromArray($result);

$dbRes = new CAdminResult($dbRes, $sTableID);
$dbRes->NavStart();

$lAdmin->NavText($dbRes->GetNavPrint(GetMessage("ESOL_IX_PROFILE_LIST")));

$lAdmin->AddHeaders(array(
	array("id"=>"ID", "content"=>"ID", 	"sort"=>"ID", "default"=>true),
	array("id"=>"ACTIVE", "content"=>GetMessage("ESOL_IX_PL_ACTIVE"), "sort"=>"ACTIVE", "default"=>true),
	array("id"=>"NAME", "content"=>GetMessage("ESOL_IX_PL_NAME"), "sort"=>"NAME", "default"=>true),
	array("id"=>"DATE_START", "content"=>GetMessage("ESOL_IX_PL_DATE_START"), "sort"=>"DATE_START", "default"=>true),
	array("id"=>"DATE_FINISH", "content"=>GetMessage("ESOL_IX_PL_DATE_FINISH"), "sort"=>"DATE_FINISH", "default"=>true),
	array("id"=>"SORT", "content"=>GetMessage("ESOL_IX_PL_SORT"), "sort"=>"SORT", "default"=>true),
	array("id"=>"STATUS", "content"=>GetMessage("ESOL_IX_PL_STATUS"), "default"=>true),
));

$oProfile = new \Bitrix\EsolImportxml\Profile();
$arVisibleColumns = $lAdmin->GetVisibleHeaderColumns();
while ($arProfile = $dbRes->NavNext(true, "f_"))
{
	$row =& $lAdmin->AddRow(($f_ID+1), $arProfile, $moduleFilePrefix.".php?PROFILE_ID=".$f_ID."&lang=".LANG, GetMessage("ESOL_IX_TO_PROFILE"));

	$row->AddField("ID", "<a href=\"".$moduleFilePrefix.".php?PROFILE_ID=".$f_ID."&lang=".LANG."\">".$f_ID."</a>");
	$row->AddCheckField("ACTIVE", $f_ACTIVE);
	$row->AddInputField("NAME", $f_NAME);
	$row->AddInputField("SORT", $f_SORT);
	$row->AddField("DATE_START", $f_DATE_START);
	$row->AddField("DATE_FINISH", $f_DATE_FINISH);
	$row->AddField("STATUS", $oProfile->GetStatus($f_ID));
	
	$arActions = array();
	$arActions[] = array("ICON"=>"edit", "TEXT"=>GetMessage("ESOL_IX_TO_PROFILE_ACT"), "ACTION"=>$lAdmin->ActionRedirect($moduleFilePrefix.".php?PROFILE_ID=".$f_ID."&lang=".LANG), "DEFAULT"=>true);

	$arActions[] = array("SEPARATOR" => true);
	$arActions[] = array("ICON"=>"delete", "TEXT"=>GetMessage("ESOL_IX_PROFILE_DELETE"), "ACTION"=>"if(confirm('".GetMessageJS("ESOL_IX_PROFILE_DELETE_CONFIRM")."')) ".$lAdmin->ActionDoGroup(($f_ID+1), "delete"));

	$row->AddActions($arActions);
}

$lAdmin->AddFooter(
	array(
		array(
			"title" => GetMessage("MAIN_ADMIN_LIST_SELECTED"),
			"value" => $dbRes->SelectedRowsCount()
		),
		array(
			"counter" => true,
			"title" => GetMessage("MAIN_ADMIN_LIST_CHECKED"),
			"value" => "0"
		),
	)
);

$lAdmin->AddGroupActionTable(
	array(
		"delete" => GetMessage("MAIN_ADMIN_LIST_DELETE"),
	)
);

$lAdmin->CheckListMode();

$APPLICATION->SetTitle(GetMessage("ESOL_IX_PROFILE_LIST_TITLE"));
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

if (!$moduleDemoExpiredFunc()) {
	$moduleShowDemoFunc();
}

$aMenu = array(
	array(
		"TEXT" => GetMessage("ESOL_IX_BACK_TO_IMPORT"),
		"ICON" => "btn_list",
		"LINK" => "/bitrix/admin/".$moduleFilePrefix.".php?lang=".LANG
	),
	array(
		"TEXT"=>GetMessage("ESOL_IX_MENU_EXPORT_IMPORT_PROFILES"),
		"TITLE"=>GetMessage("ESOL_IX_MENU_EXPORT_IMPORT_PROFILES"),
		"MENU" => array(
			array(
				"TEXT" => GetMessage("ESOL_IX_MENU_EXPORT_PROFILES"),
				"TITLE" => GetMessage("ESOL_IX_MENU_EXPORT_PROFILES"),
				"LINK" => "/bitrix/admin/".$moduleFilePrefix."_profile_list.php?mode=export"
			),
			array(
				"TEXT" => GetMessage("ESOL_IX_MENU_IMPORT_PROFILES"),
				"TITLE" => GetMessage("ESOL_IX_MENU_IMPORT_PROFILES"),
				"ONCLICK" => "EProfileList.ShowRestoreWindow();"
			)
		),
		"ICON" => "btn_green",
	)
);

$context = new CAdminContextMenu($aMenu);
$context->Show();
?>

<form name="find_form" method="GET" action="<?echo $APPLICATION->GetCurPage()?>?">
<?
$oFilter = new CAdminFilter(
	$sTableID."_filter",
	array(
		GetMessage("SALE_F_PERSON_TYPE"),
	)
);

$oFilter->Begin();
?>
	<tr>
		<td><?echo GetMessage("ESOL_IX_F_NAME")?>:</td>
		<td>
			<input type="text" name="filter_name" value="<?echo htmlspecialcharsex($filter_name)?>">
		</td>
	</tr>
<?
$oFilter->Buttons(
	array(
		"table_id" => $sTableID,
		"url" => $APPLICATION->GetCurPage(),
		"form" => "find_form"
	)
);
$oFilter->End();
?>
</form>

<?
$lAdmin->DisplayList();
require ($DOCUMENT_ROOT."/bitrix/modules/main/include/epilog_admin.php");
?>
