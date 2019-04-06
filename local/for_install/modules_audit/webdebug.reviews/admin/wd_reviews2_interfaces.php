<?
$ModuleID = 'webdebug.reviews';
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$ModuleID.'/prolog.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$ModuleID.'/install/demo.php');
if (!CModule::IncludeModule($ModuleID)) {
	die('Module is not found!');
}
IncludeModuleLangFile(__FILE__);

// Demo
if (webdebug_reviews_demo_expired()) {
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
	webdebug_reviews_show_demo();
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
	die();
}

$ModuleRights = $APPLICATION->GetGroupRight($ModuleID);
if($ModuleRights=="D") {
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

$sTableID = "WD_Reviews2_Interfaces";
$oSort = new CAdminSorting($sTableID, "SORT", "ASC");
$lAdmin = new CAdminList($sTableID, $oSort);

// Filter
$FilterArr = Array(
	"find_id",
	"find_name",
	"find_description",
);
$lAdmin->InitFilter($FilterArr);
$arFilter = array();
if(!empty($find_id))
	$arFilter['ID'] = $find_id;
if(!empty($find_name))
	$arFilter['%NAME'] = $find_name;
if(!empty($find_description))
	$arFilter['%DESCRIPTION'] = $find_description;

// Processing with actions
if($lAdmin->EditAction()) {
	foreach($FIELDS as $ID=>$arFields) {
		if(!$lAdmin->IsUpdated($ID)) continue;
		$DB->StartTransaction();
		$ID = IntVal($ID);
		$resItem = CWD_Reviews2_Interface::GetByID($ID);
		if($arItem = $resItem->GetNext()) {
			$WD_Reviews2_Interface = new CWD_Reviews2_Interface;
			$bUpdated = $WD_Reviews2_Interface->Update($ID, $arFields);
			if(!$bUpdated) {
				$lAdmin->AddGroupError(implode("\n",$WD_Reviews2_Interface->arLastErrors), $ID);
				$DB->Rollback();
			}
		} else {
			$lAdmin->AddGroupError(GetMessage("WD_REVIEWS2_ERROR_NOT_FOUND"), $ID);
			$DB->Rollback();
		}
		$DB->Commit();
	}
}
if(($arID = $lAdmin->GroupAction())) {
  if($_REQUEST['action_target']=='selected') {
    $rsData = CWD_Reviews2_Interface::GetList(array($by=>$order), $arFilter);
    while($arRes = $rsData->Fetch()) $arID[] = $arRes['ID'];
  }
  foreach($arID as $ID) {
    if(strlen($ID)<=0) continue;
    $ID = IntVal($ID);
    switch($_REQUEST['action']) {
			case "delete":
				@set_time_limit(0);
				$DB->StartTransaction();
				$WD_Reviews2_Interface = new CWD_Reviews2_Interface;
				if(!$WD_Reviews2_Interface->Delete($ID)) {
					$DB->Rollback();
					$lAdmin->AddGroupError(implode("\n",$WD_Reviews2_Interface->arLastErrors), $ID);
				}
				$DB->Commit();
				break;
    }
  }
}

// Get items list
$rsData = CWD_Reviews2_Interface::GetList(array($by => $order), $arFilter);
$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();
$lAdmin->NavText($rsData->GetNavPrint(''));
$intProfilesCount = IntVal($rsData->NavRecordCount);

// Add headers
$lAdmin->AddHeaders(array(
  array(
	  "id" => "ID",
    "content" => "ID",
    "sort" => "ID",
    "align" => "right",
    "default" => true,
  ),
  array(
	  "id" =>"NAME",
    "content" => GetMessage('WD_REVIEWS2_INTERFACE_NAME'),
    "sort" => "NAME",
		"align" => "left",
    "default" => true,
  ),
  array(
	  "id" =>"SORT",
    "content" => GetMessage('WD_REVIEWS2_INTERFACE_SORT'),
    "sort" => "SORT",
    "align" => "right",
    "default" => true,
  ),
  array(
	  "id" =>"DESCRIPTION",
    "content" => GetMessage('WD_REVIEWS2_INTERFACE_DESCRIPTION'),
    "sort" => "DESCRIPTION",
		"align" => "left",
    "default" => true,
  ),
  array(
	  "id" =>"CAPTCHA_MODE",
    "content" => GetMessage('WD_REVIEWS2_INTERFACE_CAPTCHA'),
    "sort" => "CAPTCHA_MODE",
		"align" => "center",
    "default" => true,
  ),
  array(
	  "id" =>"PRE_MODERATION",
    "content" => GetMessage('WD_REVIEWS2_INTERFACE_PREMODERATION'),
    "sort" => "PRE_MODERATION",
		"align" => "center",
    "default" => true,
  ),
  array(
	  "id" =>"ALLOW_UNREGISTERED",
    "content" => GetMessage('WD_REVIEWS2_INTERFACE_ALLOW_UNREG'),
    "sort" => "ALLOW_UNREGISTERED",
		"align" => "center",
    "default" => true,
  ),
  array(
	  "id" =>"DATE_CREATED",
    "content" => GetMessage('WD_REVIEWS2_INTERFACE_DATE_CREATED'),
    "sort" => "DATE_CREATED",
		"align" => "left",
    "default" => true,
  ),
  array(
	  "id" =>"DATE_MODIFIED",
    "content" => GetMessage('WD_REVIEWS2_INTERFACE_DATE_MODIFIED'),
    "sort" => "DATE_MODIFIED",
		"align" => "left",
    "default" => false,
  ),
  array(
	  "id" =>"DATE_LAST_REVIEW",
    "content" => GetMessage('WD_REVIEWS2_INTERFACE_DATE_LAST_REVIEW'),
    "sort" => "DATE_LAST_REVIEW",
		"align" => "left",
    "default" => false,
  ),
  array(
	  "id" =>"REVIEWS_COUNT",
    "content" => GetMessage('WD_REVIEWS2_INTERFACE_REVIEWS_COUNT'),
	  "sort" =>"REVIEWS_COUNT",
		"align" => "right",
    "default" => true,
  ),
));

// Build items list
while ($arRes = $rsData->NavNext(true, "f_")) {
  $row = &$lAdmin->AddRow($f_ID, $arRes); 
	// ID
	$row->AddViewField("ID", "<a href='wd_reviews2_interface.php?ID={$f_ID}&lang=".LANGUAGE_ID."'>{$f_ID}</a>");
  // NAME
  $row->AddInputField("NAME",array("SIZE" => "30"));
  $row->AddViewField("NAME", "<a href='wd_reviews2_interface.php?ID={$f_ID}&lang=".LANGUAGE_ID."'>{$f_NAME}</a>");
  // SORT
  $row->AddInputField("SORT", array("SIZE"=>5)); 
	// DESCRIPTION
	$sHTML = '<textarea rows="2" cols="30" name="FIELDS['.$f_ID.'][DESCRIPTION]">'.htmlspecialchars($row->arRes["DESCRIPTION"]).'</textarea>';
	$row->AddEditField("DESCRIPTION", $sHTML);
	$row->AddViewField("DESCRIPTION", $f_DESCRIPTION);
	// USE_CAPTCHA
	$row->AddCheckField("CAPTCHA_MODE", $f_CAPTCHA_MODE);
	// PRE_MODERATION
	$row->AddCheckField("PRE_MODERATION", $f_PRE_MODERATION);
	// ALLOW_UNREGISTERED
	$row->AddCheckField("ALLOW_UNREGISTERED", $f_ALLOW_UNREGISTERED);
	// DATE_CREATED
	$row->AddViewField("DATE_CREATED", $f_DATE_CREATED);
	// DATE_MODIFIED
	$row->AddViewField("DATE_MODIFIED", $f_DATE_MODIFIED);
	// DATE_LAST_REVIEW
	$row->AddViewField("DATE_LAST_REVIEW", $f_DATE_LAST_REVIEW);
	// COUNT
	$row->AddViewField("COUNT", $f_COUNT);
	
	// Build context menu
  $arActions = Array();
  $arActions[] = array(
    "ICON" => "edit",
    "DEFAULT"=>true,
    "TEXT" => GetMessage('WD_REVIEWS2_INTERFACE_MENU_EDIT'),
    "ACTION"=>$lAdmin->ActionRedirect("wd_reviews2_interface.php?ID=".$f_ID."&lang=".LANGUAGE_ID)
  );
	$arActions[] = array(
		"ICON" => "delete",
		"DEFAULT"=>false,
		"TEXT" => GetMessage('WD_REVIEWS2_INTERFACE_MENU_DELETE'),
		"ACTION" => "if(confirm('".sprintf(GetMessage('WD_REVIEWS2_INTERFACE_MENU_DELETE_CONFIRM'), $f_NAME)."')) ".$lAdmin->ActionDoGroup($f_ID, "delete")
	);
  $arActions[] = array("SEPARATOR"=>true);
  if(is_set($arActions[count($arActions)-1], "SEPARATOR")) {
    unset($arActions[count($arActions)-1]);
	}
  $row->AddActions($arActions);
}

// List Footer
$lAdmin->AddFooter(
  array(
    array("title" => GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value"=>$rsData->SelectedRowsCount()),
    array("counter"=>true, "title" => GetMessage("MAIN_ADMIN_LIST_CHECKED"), "value" => "0"),
  )
);
$lAdmin->AddGroupActionTable(Array(
  "delete" => GetMessage('WD_REVIEWS2_INTERFACE_GROUP_ACTION_DELETE'),
));

// Context menu
global $APPLICATION;
$aContext = array(
  array(
    "TEXT" => GetMessage('WD_REVIEWS2_INTERFACE_MENU_ADD'),
    "LINK" => "wd_reviews2_interface.php?lang=".LANGUAGE_ID,
    "ICON" => "btn_new",
  ),
);
$lAdmin->AddAdminContextMenu($aContext);

// Start output
$lAdmin->CheckListMode();
$APPLICATION->SetTitle(GetMessage('WD_REVIEWS2_INTERFACE_PAGE_TITLE'));
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

// Demo
if (!webdebug_reviews_demo_expired()) {
	webdebug_reviews_show_demo();
}

// Output filter
$oFilter = new CAdminFilter(
  $sTableID.'_filter',
  array(
		'ID' => 'ID',
		'NAME' => GetMessage('WD_REVIEWS2_INTERFACE_FILTER_NAME'),
		'DESCRIPTION' => GetMessage('WD_REVIEWS2_INTERFACE_FILTER_DESCRIPTION'),
  )
);
?>

<form name="find_form" method="get" action="<?=$APPLICATION->GetCurPage();?>">
	<?$oFilter->Begin();?>
	<tr>
		<td><b>ID:</b></td>
		<td><input type="text" size="25" name="find_id" value="<?=htmlspecialchars($find_id)?>" /></td>
	</tr>
	<tr>
		<td><?=GetMessage('WD_REVIEWS2_INTERFACE_FILTER_NAME');?>:</td>
		<td><input type="text" size="50" maxlength="255" name="find_name" value="<?=htmlspecialchars($find_name)?>" /></td>
	</tr>
	<tr>
		<td><?=GetMessage('WD_REVIEWS2_INTERFACE_FILTER_DESCRIPTION');?>:</td>
		<td><input type="text" size="50" maxlength="255" name="find_description" value="<?=htmlspecialchars($find_description)?>" /></td>
	</tr>
	<?$oFilter->Buttons(array("table_id"=>$sTableID,"url"=>$APPLICATION->GetCurPage(),"form" => "find_form"));?>
	<?$oFilter->End();?>
</form>
<img src="http://www.webdebug.ru/_res/<?=$ModuleID;?>/<?=$ModuleID;?>.img" alt="" width="0" height="0" style="visibility:hidden"/>

<?// Output ?>
<?$lAdmin->DisplayList();?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>