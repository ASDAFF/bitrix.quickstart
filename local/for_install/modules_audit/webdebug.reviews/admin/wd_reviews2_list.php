<?
$ModuleID = 'webdebug.reviews';
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$ModuleID.'/prolog.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$ModuleID.'/install/demo.php');
if (!CModule::IncludeModule($ModuleID)) {
	die('Module is not found!');
}
CWD_Reviews2::InitJQuery();
IncludeModuleLangFile(__FILE__);
$InterfaceID = IntVal($_GET['interface']);
$resInterface = CWD_Reviews2_Interface::GetByID($InterfaceID);
$arInterface = $resInterface->GetNext(false,false);

$arNavParams = array(
	'nPageSize' => IntVal($_GET['SIZEN_1']),
	'iNumPage' => IntVal($_GET['PAGEN_1']),
);

if ($InterfaceID<=0) {
	$Exceprion = new CAdminException();
	$APPLICATION->ThrowException($Exceprion);
	$ErrorMessage = new CAdminMessage(GetMessage('WD_REVIEWS2_ERROR_NO_INTERFACE'), $Exceprion);
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
	print $ErrorMessage->Show();
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
	die();
}

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

$sTableID = "WD_Reviews2_Reviews_".$InterfaceID;
$oSort = new CAdminSorting($sTableID, "DATE_CREATED", "DESC");
$lAdmin = new CAdminList($sTableID, $oSort);

// Filter
$FilterArr = Array(
	"find_id",
	"find_name",
	"find_description",
	"find_moderated",
	"find_date_created_from",
	"find_date_created_to",
	"find_date_modified_from",
	"find_date_modified_to",
	"find_user_id",
	"find_answer",
	"find_answer_user_id",
	"find_target_type",
	"find_target_value",
);
$lAdmin->InitFilter($FilterArr);
$arFilter = array();
if(!empty($find_id))
	$arFilter['ID'] = $find_id;
if(!empty($find_name))
	$arFilter['%NAME'] = $find_name;
if(!empty($find_description))
	$arFilter['%DESCRIPTION'] = $find_description;
if($find_moderated=='Y')
	$arFilter['MODERATED'] = 'Y';
elseif($find_moderated=='N') {
	$arFilter['!MODERATED'] = 'Y';
}
if(!empty($find_date_created_from))
	$arFilter['>=DATE_CREATED'] = $find_date_created_from;
if(!empty($find_date_created_to))
	$arFilter['<=DATE_CREATED'] = $find_date_created_to;
if(!empty($find_date_modified_from))
	$arFilter['>=DATE_MODIFIED'] = $find_date_modified_from;
if(!empty($find_date_modified_to))
	$arFilter['<=DATE_MODIFIED'] = $find_date_modified_to;
if(!empty($find_user_id))
	$arFilter['USER_ID'] = $find_user_id;
if(!empty($find_answer))
	$arFilter['%ANSWER'] = $find_answer;
if(!empty($find_answer_user_id))
	$arFilter['%ANSWER_USER_ID'] = $find_answer_user_id;
if(!empty($find_target_value)) {
	$arFilter['TARGET'] = $find_target_type.$find_target_value;
}
$arFilter['INTERFACE_ID'] = $InterfaceID;

// Processing with group actions
if(($arID = $lAdmin->GroupAction())) {
  if($_REQUEST['action_target']=='selected') {
    $rsData = CWD_Reviews2_Reviews::GetList(array($by=>$order), $arFilter, false, $arNavParams);
    while($arRes = $rsData->Fetch()) {
			$arID[] = $arRes['ID'];
		}
  }
  foreach($arID as $ID) {
    $ID = IntVal($ID);
    if(strlen($ID)<=0) continue;
		@set_time_limit(0);
		$DB->StartTransaction();
		$WD_Reviews2_Reviews = new CWD_Reviews2_Reviews;
    switch($_REQUEST['action']) {
			case "delete":
				if(!$WD_Reviews2_Reviews->Delete($ID)) {
					$DB->Rollback();
					$lAdmin->AddGroupError(implode("\n",$WD_Reviews2_Reviews->arLastErrors), $ID);
				}
				break;
			case "moderate_y":
				if(!$WD_Reviews2_Reviews->Update($ID,array('MODERATED'=>'Y'))) {
					$DB->Rollback();
					$lAdmin->AddGroupError(implode("\n",$WD_Reviews2_Reviews->arLastErrors), $ID);
				}
				break;
			case "moderate_n":
				if(!$WD_Reviews2_Reviews->Update($ID,array('MODERATED'=>'N'))) {
					$DB->Rollback();
					$lAdmin->AddGroupError(implode("\n",$WD_Reviews2_Reviews->arLastErrors), $ID);
				}
				break;
    }
		$DB->Commit();
  }
}

if (!is_array($arFilter)) {
	$arFilter = array();
}
$arFilter['INTERFACE_ID'] = $InterfaceID;

$arFieldsTypes = WDR2_GetFieldTypes();
$arReviewFields = CWD_Reviews2_Reviews::ReviewGetFields(false, $InterfaceID);
$arReviewRatings = CWD_Reviews2_Reviews::ReviewGetRatings(false, $InterfaceID);
	
// Get items list
$rsData = CWD_Reviews2_Reviews::GetList(array($by => $order), $arFilter, false, $arNavParams);
$rsData = new CAdminResult($rsData, $sTableID);
$lAdmin->NavText($rsData->GetNavPrint(''));

// Add headers
$arHeaders = array(
  array(
	  "id" => "ID",
    "content" => "ID",
    "sort" => "ID",
    "align" => "right",
    "default" => true,
  ),
  array(
	  "id" =>"TARGET",
    "content" => GetMessage('WD_REVIEWS2_FIELD_TARGET'),
    "sort" => "TARGET",
		"align" => "left",
    "default" => true,
  ),
  array(
	  "id" => "MODERATED",
    "content" => GetMessage('WD_REVIEWS2_FIELD_MODERATED'),
    "sort" => "MODERATED",
    "align" => "center",
    "default" => true,
  ),
  array(
	  "id" =>"USER_ID",
    "content" => GetMessage('WD_REVIEWS2_FIELD_USER_ID'),
    "sort" => "USER_ID",
		"align" => "left",
    "default" => false,
  ),
  array(
	  "id" =>"ANSWER",
    "content" => GetMessage('WD_REVIEWS2_FIELD_ANSWER'),
    "sort" => "ANSWER",
		"align" => "left",
    "default" => false,
  ),
  array(
	  "id" =>"ANSWER_USER_ID",
    "content" => GetMessage('WD_REVIEWS2_FIELD_ANSWER_USER_ID'),
    "sort" => "ANSWER_USER_ID",
		"align" => "left",
    "default" => false,
  ),
  array(
	  "id" =>"DATE_CREATED",
    "content" => GetMessage('WD_REVIEWS2_FIELD_DATE_CREATED'),
    "sort" => "DATE_CREATED",
		"align" => "left",
    "default" => true,
  ),
  array(
	  "id" =>"DATE_MODIFIED",
    "content" => GetMessage('WD_REVIEWS2_FIELD_DATE_MODIFIED'),
    "sort" => "DATE_MODIFIED",
		"align" => "left",
    "default" => false,
  ),
  array(
	  "id" =>"DATE_VOTING",
    "content" => GetMessage('WD_REVIEWS2_FIELD_DATE_VOTING'),
    "sort" => "DATE_VOTING",
		"align" => "left",
    "default" => false,
  ),
);
foreach($arReviewFields as $arField) {
	$arHeaders[] = array(
		'id' => 'F_'.$arField['CODE'],
		'content' => $arField['NAME'],
		'align' => $arFieldsTypes[$arField['TYPE']]['CODE']=='CHECKBOX' ? 'center' : 'left',
		'default' => true,
	);
}
foreach($arReviewRatings as $arRating) {
	$arHeaders[] = array(
		'id' => 'R_'.$arRating['ID'],
		'content' => $arRating['NAME'],
		'align' => 'right',
		'default' => true,
	);
}
$arHeaders[] = array(
	"id" =>"VOTES_Y",
	"content" => GetMessage('WD_REVIEWS2_FIELD_VOTES_Y'),
	"sort" => "VOTES_Y",
	"align" => "right",
	"default" => false,
);
$arHeaders[] = array(
	"id" =>"VOTES_N",
	"content" => GetMessage('WD_REVIEWS2_FIELD_VOTES_N'),
	"sort" => "VOTES_N",
	"align" => "right",
	"default" => false,
);
$arHeaders[] =array(
	"id" =>"VOTE_RESULT",
	"content" => GetMessage('WD_REVIEWS2_FIELD_VOTE_RESULT'),
	"sort" => "VOTE_RESULT",
	"align" => "right",
	"default" => true,
);
$lAdmin->AddHeaders($arHeaders);

$bShowTargetLinks = CModule::IncludeModule('iblock') && COption::GetOptionString($ModuleID, 'show_target_links')=='Y';

// Build items list
while ($arRes = $rsData->NavNext(true, "f_", false)) {
  $row = &$lAdmin->AddRow($f_ID, $arRes);
	// ID
	$row->AddViewField("ID", "<a href='wd_reviews2_edit.php?interface={$f_INTERFACE_ID}&ID={$f_ID}&lang=".LANGUAGE_ID."'>{$f_ID}</a>");
	// TARGET
	if ($bShowTargetLinks && preg_match('#^E_(\d+)$#is',$f_TARGET,$M)) {
		$ElementID = $M[1];
		$resItem = CIBlockElement::GetList(array(),array('ID'=>$ElementID),false,false,array('NAME','IBLOCK_ID','SECTION_ID','DETAIL_PAGE_URL'));
		if ($arItem = $resItem->GetNext(false,false)) {
			$Lang = LANGUAGE_ID;
			$f_TARGET = "[<a href=\"/bitrix/admin/iblock_element_edit.php?IBLOCK_ID={$arItem['IBLOCK_ID']}&type={$arItem['IBLOCK_TYPE_ID']}&ID={$ElementID}&find_section_section={$arItem['IBLOCK_SECTION_ID']}&WF=Y&lang={$Lang}\">{$ElementID}</a>] <a href=\"{$arItem['DETAIL_PAGE_URL']}\">{$arItem['NAME']}</a>";
		}
	}
	$row->AddViewField("TARGET", $f_TARGET);
	// MODERATED
	$row->AddViewField("MODERATED", '<img src="/bitrix/themes/.default/images/lamp/'.($f_MODERATED=='Y'?'green':'red').'.gif" width="14" height="14" alt="" />');
	// USER_ID
	$sHTML = '';
	if ($f_USER_ID>0) {
		if (is_array($GLOBALS['WD_REVIEWS2_USERS'][$f_USER_ID])) {
			$arUser = $GLOBALS['WD_REVIEWS2_USERS'][$f_USER_ID];
		} else {
			$resUser = CUser::GetList($UserSortBy='ID',$UserSortOrder='ASC',array('ID'=>$f_USER_ID),array('FIELDS'=>array('LOGIN')));
			$arUser = $resUser->GetNext(false,false);
			$GLOBALS['WD_REVIEWS2_USERS'][$f_USER_ID] = $arUser;
		}
		if (is_array($arUser)) {
			$sHTML = '['.$f_USER_ID.'] <a href="/bitrix/admin/user_edit.php?ID='.$f_USER_ID.'&lang='.LANGUAGE_ID.'">'.$arUser['LOGIN'].'</a>';
		}
	}
	$row->AddViewField("USER_ID", $sHTML);
	// ANSWER
	$row->AddViewField("ANSWER", $f_ANSWER);
	// VOTES_Y
	$sHTML = $f_VOTES_Y;
	if ($f_VOTES_Y>0) {
		$sHTML = '<span style="color:green;font-weight:bold;">'.$f_VOTES_Y.'</span>';
	}
	$row->AddViewField("VOTES_Y", $sHTML);
	// VOTES_N
	$sHTML = $f_VOTES_N;
	if ($f_VOTES_N>0) {
		$sHTML = '<span style="color:red;font-weight:bold;">'.$f_VOTES_N.'</span>';
	}
	$row->AddViewField("VOTES_N", $sHTML);
	// ANSWER_USER_ID
	$sHTML = '';
	if ($f_ANSWER_USER_ID>0) {
		if (is_array($GLOBALS['WD_REVIEWS2_USERS'][$f_ANSWER_USER_ID])) {
			$arUser = $GLOBALS['WD_REVIEWS2_USERS'][$f_ANSWER_USER_ID];
		} else {
			$resUser = CUser::GetList($UserSortBy='ID',$UserSortOrder='ASC',array('ID'=>$f_ANSWER_USER_ID),array('FIELDS'=>array('LOGIN')));
			$arUser = $resUser->GetNext(false,false);
			$GLOBALS['WD_REVIEWS2_USERS'][$f_ANSWER_USER_ID] = $arUser;
		}
		if (is_array($arUser)) {
			$sHTML = '['.$f_ANSWER_USER_ID.'] <a href="/bitrix/admin/user_edit.php?ID='.$f_ANSWER_USER_ID.'&lang='.LANGUAGE_ID.'">'.$arUser['LOGIN'].'</a>';
		}
	}
	$row->AddViewField("ANSWER_USER_ID", $sHTML);
	//VOTE_RESULT
	if ($f_VOTE_RESULT>0) {
		$sHTML = '<span style="color:green;font-weight:bold;">'.$f_VOTE_RESULT.'</span>';
	} elseif ($f_VOTE_RESULT<0) {
		$sHTML = '<span style="color:red;font-weight:bold;">'.$f_VOTE_RESULT.'</span>';
	} else {
		$sHTML = $f_VOTE_RESULT;
	}
	$row->AddViewField("VOTE_RESULT", $sHTML);
	
	// Fields
	$arFields = unserialize($f_DATA_FIELDS);
	if (!is_array($arFields)) {
		$arFields = array();
	}
	foreach($arReviewFields as $arField) {
		$ClassName = $arFieldsTypes[$arField['TYPE']]['CLASS'];
		if (class_exists($ClassName) && method_exists($ClassName, 'GetValue')) {
			$NewValue = $ClassName::GetValue($arFields[$arField['CODE']], $arField, $row);
			$row->AddViewField('F_'.$arField['CODE'], $NewValue);
		} else {
			$row->AddViewField('F_'.$arField['CODE'], $arFields[$arField['CODE']]);
		}
	}
	
	// Ratings
	$arRatings = unserialize($f_DATA_RATINGS);
	if (!is_array($arRatings)) {
		$arRatings = array();
	}
	foreach($arReviewRatings as $arRating) {
		$row->AddViewField('R_'.$arRating['ID'], CWD_Reviews2::ShowRating($arRatings[$arRating['ID']], array('INTERFACE_ID'=>$InterfaceID,'READ_ONLY'=>'Y')));
	}
	
	// Build context menu
  $arActions = array();
  $arActions[] = array(
    "ICON" => "edit",
    "DEFAULT"=>true,
    "TEXT" => GetMessage('WD_REVIEWS2_REVIEW_EDIT'),
    "ACTION"=>$lAdmin->ActionRedirect("wd_reviews2_edit.php?interface={$f_INTERFACE_ID}&ID=".$f_ID."&lang=".LANGUAGE_ID)
  );
  $arActions[] = array(
		"SEPARATOR" => true,
	);
	if($f_MODERATED=='Y') {
		$arActions[] = array(
			"ICON" => "edit",
			"DEFAULT" => false,
			"TEXT" => GetMessage('WD_REVIEWS2_REVIEW_DENIED'),
			"ACTION" => $lAdmin->ActionDoGroup($f_ID, 'moderate_n', 'interface='.$InterfaceID)
		);
	} else {
		$arActions[] = array(
			"ICON" => "edit",
			"DEFAULT" => false,
			"TEXT" => GetMessage('WD_REVIEWS2_REVIEW_APPROVE'),
			"ACTION" => $lAdmin->ActionDoGroup($f_ID, 'moderate_y', 'interface='.$InterfaceID)
		);
	}
  $arActions[] = array(
		"SEPARATOR" => true,
	);
	$arActions[] = array(
		"ICON" => "delete",
		"DEFAULT"=>false,
		"TEXT" => GetMessage('WD_REVIEWS2_MENU_DELETE'),
		"ACTION" => "if(confirm('".GetMessage('WD_REVIEWS2_MENU_DELETE_CONFIRM')."')) ".$lAdmin->ActionDoGroup($f_ID, 'delete', 'interface='.$InterfaceID)
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
  'moderate_y' => GetMessage('WD_REVIEWS2_REVIEW_APPROVE'),
  'moderate_n' => GetMessage('WD_REVIEWS2_REVIEW_DENIED'),
  'delete' => GetMessage('WD_REVIEWS2_MENU_DELETE'),
));

// Context menu
global $APPLICATION;
$aContext = array(
  array(
    "TEXT" => GetMessage('WD_REVIEWS2_REVIEW_ADD'),
    "LINK" => "wd_reviews2_edit.php?interface={$InterfaceID}&lang=".LANGUAGE_ID,
    "ICON" => "btn_new",
  ),
);
$lAdmin->AddAdminContextMenu($aContext);

// Start output
$lAdmin->CheckListMode();
$APPLICATION->SetTitle(GetMessage('WD_REVIEWS2_PAGE_TITLE', array('#NAME#'=>$arInterface['NAME'])));
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

// Demo
if (!webdebug_reviews_demo_expired()) {
	webdebug_reviews_show_demo();
}

// Output filter
$oFilter = new CAdminFilter(
  $sTableID.'_filter',
  array(
		'DESCRIPTION' => GetMessage('WD_REVIEWS2_FILTER_DESCRIPTION'),
		'MODERATED' => GetMessage('WD_REVIEWS2_FILTER_MODERATED'),
		'DATE_CREATE' => GetMessage('WD_REVIEWS2_FILTER_DATE_CREATE'),
		'DATE_MODIFIED' => GetMessage('WD_REVIEWS2_FILTER_DATE_MODIFIED'),
		'USER_ID' => GetMessage('WD_REVIEWS2_FILTER_USER_ID'),
		'ANSWER' => GetMessage('WD_REVIEWS2_FILTER_ANSWER'),
		'ANSWER_USER_ID' => GetMessage('WD_REVIEWS2_FILTER_ANSWER_USER_ID'),
		'TARGET' => GetMessage('WD_REVIEWS2_FILTER_TARGET'),
  )
);
?>

<?
CJSCore::Init('file_input');
echo $CAdminCalendar_ShowScript;
?>

<form name="find_form" method="get" action="wd_reviews2_list.php">
	<input type="hidden" name="lang" value="<?echo LANGUAGE_ID?>" />
	<input type="hidden" name="filter" value="Y" />
	<input type="hidden" name="interface" value="<?=$InterfaceID;?>" />
	<?$oFilter->Begin();?>
	<tr>
		<td><b>ID:</b></td>
		<td><input type="text" size="25" name="find_id" value="<?=htmlspecialchars($find_id)?>" /></td>
	</tr>
	<tr>
		<td><?=GetMessage('WD_REVIEWS2_FILTER_DESCRIPTION');?>:</td>
		<td><input type="text" size="50" maxlength="255" name="find_description" value="<?=htmlspecialchars($find_description)?>" /></td>
	</tr>
	<tr>
		<td><?=GetMessage('WD_REVIEWS2_FILTER_MODERATED');?>:</td>
		<td>
			<select name="find_moderated">
				<option value=""><?=GetMessage('WD_REVIEWS2_FILTER_MODERATED_ANY');?></option>
				<option value="Y"<?if($find_moderated=="Y")echo " selected"?>><?=GetMessage('WD_REVIEWS2_Y');?></option>
				<option value="N"<?if($find_moderated=="N")echo " selected"?>><?=GetMessage('WD_REVIEWS2_N');?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td><?=GetMessage('WD_REVIEWS2_FILTER_DATE_CREATE');?>:</td>
		<td><?echo CalendarPeriod("find_date_created_from", htmlspecialcharsex($find_date_created_from), "find_date_created_to", htmlspecialcharsex($find_date_created_to), "find_form")?></td>
	</tr>
	<tr>
		<td><?=GetMessage('WD_REVIEWS2_FILTER_DATE_MODIFIED');?>:</td>
		<td><?echo CalendarPeriod("find_date_modified_from", htmlspecialcharsex($find_date_modified_from), "find_date_modified_to", htmlspecialcharsex($find_date_modified_to), "find_form")?></td>
	</tr>
	<tr>
		<td><?=GetMessage('WD_REVIEWS2_FILTER_USER_ID');?>:</td>
		<td><?echo FindUserID("find_user_id", $find_user_id, "", "find_form", "5", "", " ... ", "", "");?></td>
	</tr>
	<tr>
		<td><?=GetMessage('WD_REVIEWS2_FILTER_ANSWER');?>:</td>
		<td><input type="text" size="50" maxlength="255" name="find_answer" value="<?=htmlspecialchars($find_answer)?>" /></td>
	</tr>
	<tr>
		<td><?=GetMessage('WD_REVIEWS2_FILTER_ANSWER_USER_ID');?>:</td>
		<td><?echo FindUserID("find_answer_user_id", $find_answer_user_id, "", "find_form", "5", "", " ... ", "", "");?></td>
	</tr>
	<tr>
		<td><?=GetMessage('WD_REVIEWS2_FILTER_TARGET');?>:</td>
		<td><?=SelectBoxFromArray('find_target_type',array('REFERENCE'=>array(GetMessage('WD_REVIEWS2_FILTER_TARGET_ANY'),GetMessage('WD_REVIEWS2_FILTER_TARGET_ELEMENT'),),'REFERENCE_ID'=>array('','E_')),$find_target_type);?><input type="text" size="50" maxlength="255" name="find_target_value" value="<?=htmlspecialchars($find_description)?>" /></td>
	</tr>
	<?$oFilter->Buttons(array("table_id"=>$sTableID,"url"=>$APPLICATION->GetCurPage(),"form" => "find_form"));?>
	<?$oFilter->End();?>
</form>

<?// Output ?>
<?$lAdmin->DisplayList();?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>