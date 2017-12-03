<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/asdaff.mass/prolog.php");
CModule::IncludeModule('asdaff.mass');
IncludeModuleLangFile(__FILE__);
$arModuleActions = CWDA::GetActionsList();
$arIBlocks = array();
if(CModule::IncludeModule('iblock')) {
	$resIBlocks = CIBlock::GetList();
	while($arIBlock = $resIBlocks->GetNext(false,false)) {
		$arIBlocks[$arIBlock['ID']] = $arIBlock;
	}
}

$sTableID = "WdaProfiles";
$oSort = new CAdminSorting($sTableID, "SORT", "ASC");
$lAdmin = new CAdminList($sTableID, $oSort);

// Filter
function CheckFilter() {
	global $FilterArr, $lAdmin;
	foreach ($FilterArr as $f) global $f;
	return count($lAdmin->arFilterErrors)==0;
}
$FilterArr = Array(
	"find_id",
	"find_name",
	"find_description",
	"find_send_email",
	"find_iblock_id",
	"find_action",
);
$lAdmin->InitFilter($FilterArr);
if (CheckFilter()) {
	$arFilter = Array(
		"ID" => $find_id,
		"%NAME" => $find_name,
		"%DESCRIPTION" => $find_description,
		"SEND_EMAIL" => $find_send_email,
		"IBLOCK_ID" => $find_iblock_id,
		"=ACTION" => $find_action,
	);
}

// Processing with actions
if($lAdmin->EditAction()) {
	foreach($FIELDS as $ID=>$arFields) {
		if(!$lAdmin->IsUpdated($ID)) continue;
		$DB->StartTransaction();
		$ID = IntVal($ID);
		if(($rsData = CWDA_Profile::GetByID($ID)) && ($arData = $rsData->Fetch())) {
			foreach($arFields as $key=>$value) $arData[$key]=$value;
			if(!CWDA_Profile::Update($ID, $arData)) {
				$lAdmin->AddGroupError(GetMessage("rub_save_error"), $ID);
				$DB->Rollback();
			}
		} else {
			$lAdmin->AddGroupError(GetMessage("rub_save_error")." ".GetMessage("rub_no_rubric"), $ID);
			$DB->Rollback();
		}
		$DB->Commit();
	}
}
if(($arID = $lAdmin->GroupAction())) {
  if($_REQUEST['action_target']=='selected') {
    $rsData = CWDA_Profile::GetList(array($by=>$order), $arFilter);
    while($arRes = $rsData->Fetch()) $arID[] = $arRes['ID'];
  }
  foreach($arID as $ID) {
    if(strlen($ID)<=0) continue;
    $ID = IntVal($ID);
    switch($_REQUEST['action']) {
			case "delete":
				@set_time_limit(0);
				$DB->StartTransaction();
				if(!CWDA_Profile::Delete($ID)) {
					$DB->Rollback();
					$lAdmin->AddGroupError(GetMessage("rub_del_err"), $ID);
				}
				$DB->Commit();
				break;
    }
  }
}

// Get items list
$rsData = CWDA_Profile::GetList(array($by => $order), $arFilter);
$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();
$lAdmin->NavText($rsData->GetNavPrint(GetMessage("rub_nav")));

// Add headers
$lAdmin->AddHeaders(array(
  array(
	  "id" => "ID",
    "content" => GetMessage("WDA_PROFILES_HEADER_ID"),
    "sort" => "ID",
    "align" => "right",
    "default" => true,
  ),
  array(
	  "id" => "NAME",
    "content" => GetMessage("WDA_PROFILES_HEADER_NAME"),
    "sort" => "NAME",
		"align" => "left",
    "default" => true,
  ),
  array(
	  "id" => "DESCRIPTION",
    "content" => GetMessage("WDA_PROFILES_HEADER_DESCRIPTION"),
    "sort" => "DESCRIPTION",
		"align" => "left",
    "default" => true,
  ),
  array(
	  "id" => "SORT",
    "content" => GetMessage("WDA_PROFILES_HEADER_SORT"),
    "sort" => "SORT",
    "align" => "right",
    "default" => true,
  ),
  array(
	  "id" => "SEND_EMAIL",
    "content" => GetMessage("WDA_PROFILES_HEADER_SEND_EMAIL"),
    "sort" => "SEND_EMAIL",
    "align" => "center",
    "default" => true,
  ),
  array(
	  "id" => "IBLOCK_ID",
    "content" => GetMessage("WDA_PROFILES_HEADER_IBLOCK_ID"),
    "sort" => "IBLOCK_ID",
		"align" => "left",
    "default" => true,
  ),
  array(
	  "id" => "SECTIONS_ID",
    "content" => GetMessage("WDA_PROFILES_HEADER_SECTIONS_ID"),
    "sort" => "SECTIONS_ID",
		"align" => "left",
    "default" => true,
  ),
  array(
	  "id" => "WITH_SUBSECTIONS",
    "content" => GetMessage("WDA_PROFILES_HEADER_WITH_SUBSECTIONS"),
    "sort" => "WITH_SUBSECTIONS",
		"align" => "left",
    "default" => true,
  ),
  array(
	  "id" => "FILTER",
    "content" => GetMessage("WDA_PROFILES_HEADER_FILTER"),
    "sort" => "FILTER",
		"align" => "left",
    "default" => true,
  ),
  array(
	  "id" => "ACTION",
    "content" => GetMessage("WDA_PROFILES_HEADER_ACTION"),
    "sort" => "ACTION",
    "align" => "left",
    "default" => true,
  ),
  array(
	  "id" => "DATE_CREATED",
    "content" => GetMessage("WDA_PROFILES_HEADER_DATE_CREATED"),
    "sort" => "DATE_CREATED",
		"align" => "left",
    "default" => true,
  ),
  array(
	  "id" => "DATE_SUCCESS",
    "content" => GetMessage("WDA_PROFILES_HEADER_DATE_SUCCESS"),
    "sort" => "DATE_SUCCESS",
		"align" => "left",
    "default" => true,
  ),
));

// Build items list
while ($arRes = $rsData->NavNext(true, "f_")) {
  $row = &$lAdmin->AddRow($f_ID, $arRes); 
	// ID
	$row->AddViewField("ID", $f_ID);
  // NAME
  $row->AddInputField("NAME", array("SIZE" => "30"));
	$row->AddViewField("NAME", '<div style="min-width:150px;">'.$f_NAME.'</div>');
	// DESCRIPTION
	$sHTML = '<textarea rows="2" cols="30" name="FIELDS['.$f_ID.'][DESCRIPTION]">'.htmlspecialcharsbx($f_DESCRIPTION).'</textarea>';
	$row->AddEditField("DESCRIPTION", $sHTML);
	$row->AddViewField("DESCRIPTION", '<div style="min-width:150px;">'.$f_DESCRIPTION.'</div>');
  // SORT
  $row->AddInputField("SORT", array("SIZE"=>5));
	// SEND_EMAIL
	$row->AddCheckField("SEND_EMAIL", $f_SEND_EMAIL);
	// IBLOCK_ID
	$strIBlock = $f_IBLOCK_ID;
	if($f_IBLOCK_ID>0 && is_array($arIBlocks[$f_IBLOCK_ID])) {
		$strIBlock = '<a href="/bitrix/admin/iblock_list_admin.php?IBLOCK_ID='.$f_IBLOCK_ID.'&type='.$arIBlocks[$f_IBLOCK_ID]['IBLOCK_TYPE_ID'].'&lang='.LANGUAGE_ID.'&find_section_section=0">['.$f_IBLOCK_ID.'] '.$arIBlocks[$f_IBLOCK_ID]['NAME'];
	}
	$row->AddViewField("IBLOCK_ID", $strIBlock);
	// SECTIONS_ID
	if(empty($f_SECTIONS_ID)) {
		$f_SECTIONS_ID = GetMessage('WDA_PROFILES_HEADER_SECTIONS_ID_ALL');
	} else {
		$f_SECTIONS_ID = str_replace(',',', ',$f_SECTIONS_ID);
	}
	if(strlen($f_SECTIONS_ID)>100) {
		$f_SECTIONS_ID = substr($f_SECTIONS_ID,0,100).'...';
	}
	$row->AddViewField("SECTIONS_ID", $f_SECTIONS_ID);
  // WITH_SUBSECTIONS
  $row->AddViewField("WITH_SUBSECTIONS", $f_WITH_SUBSECTIONS=='Y'?GetMessage('WDA_PROFILES_HEADER_WITH_SUBSECTIONS_Y'):GetMessage('WDA_PROFILES_HEADER_WITH_SUBSECTIONS_N'));
	// FILTER
	parse_str($f_FILTER,$arValues);
	$arProfileFilter = array();
	if(is_array($arValues['f_p1'])) {
		foreach($arValues['f_p1'] as $Index => $Value){
			$arProfileFilter[] = '<li style="margin-bottom:4px;"><b>'.$Value.'</b> '.$arValues['f_e1'][$Index].' <b>'.$arValues['f_v1'][$Index].'</b></li>';
		}
		$f_FILTER = '<ul style="margin:0;min-width:200px;padding:0 0 0 20px;">'.implode('',$arProfileFilter).'</ul>';
	}
	$row->AddViewField("FILTER", $f_FILTER);
	// ACTION
	$strAction = $f_ACTION;
	foreach($arModuleActions as $arModuleAction){
		if($arModuleAction['CODE']==$strAction) {
			$strAction = $arModuleAction['NAME'];
			break;
		}
	}
	$row->AddViewField("ACTION", '<div style="min-width:150px;">'.$strAction.'</div>');
	// DATE_CREATED
	$row->AddViewField("DATE_CREATED", $f_DATE_CREATED);
	// DATE_SUCCESS
	$row->AddViewField("DATE_SUCCESS", $f_DATE_SUCCESS);
	// Build context menu
  $arActions = Array();
	$arActions[] = array(
		"ICON" => "delete",
		"DEFAULT" => false,
		"TEXT" => GetMessage("WDA_PROFILES_CONTEXT_DELETE"),
		"ACTION" => "if(confirm('".sprintf(GetMessage('WDA_PROFILES_CONTEXT_DELETE_CONFIRM'), $f_NAME)."')) ".$lAdmin->ActionDoGroup($f_ID, "delete")
	);
	$Command = CWDA::GetCronCommand($f_ID);
	if($Command!==false) {
		$arActions[] = array(
			"SEPARATOR" => true
		);
		$arActions[] = array(
			"ICON" => "edit",
			"DEFAULT" => false,
			"TEXT" => GetMessage("WDA_PROFILES_CONTEXT_GET_COMMAND"),
			"ACTION" => 'prompt("'.GetMessage('WDA_PROFILES_CONTEXT_GET_COMMAND_TITLE').'","'.$Command.'");'
		);
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
  "delete" => GetMessage("MAIN_ADMIN_LIST_DELETE"),
  "activate" => GetMessage("MAIN_ADMIN_LIST_ACTIVATE"),
  "deactivate" => GetMessage("MAIN_ADMIN_LIST_DEACTIVATE"),
));

// Start output
$lAdmin->CheckListMode();
$APPLICATION->SetTitle(GetMessage("WDA_PROFILES_PAGE_TITLE"));
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

// Output filter
$oFilter = new CAdminFilter(
  $sTableID."_filter",
  array(
		GetMessage("WDA_PROFILES_FILTER_NAME"),
		GetMessage("WDA_PROFILES_FILTER_DESCRIPTION"),
		GetMessage("WDA_PROFILES_FILTER_SEND_EMAIL"),
		GetMessage("WDA_PROFILES_FILTER_IBLOCK_ID"),
		GetMessage("WDA_PROFILES_FILTER_ACTION"),
  )
);
?>

<form name="find_form" method="get" action="<?=$APPLICATION->GetCurPage();?>">
	<?$oFilter->Begin();?>
	<tr>
		<td><b><?=GetMessage("WDA_PROFILES_FILTER_ID")?>:</b></td>
		<td>
			<input type="text" size="25" name="find_id" value="<?=htmlspecialchars($find_id)?>" title="<?=GetMessage("WEBDEBUG_SMS_FILTER_ID_DESCR");?>"/>
		</td>
	</tr>
	<tr>
		<td><?=GetMessage("WDA_PROFILES_FILTER_NAME")?>:</td>
		<td><input type="text" size="50" maxlength="255" name="find_name" value="<?=htmlspecialchars($find_name)?>" title="<?=GetMessage("WEBDEBUG_SMS_FILTER_NAME_DESCR");?>"/></td>
	</tr>
	<tr>
		<td><?=GetMessage("WDA_PROFILES_FILTER_DESCRIPTION")?>:</td>
		<td><input type="text" size="50" maxlength="255" name="find_description" value="<?=htmlspecialchars($find_description)?>" title="<?=GetMessage("WEBDEBUG_SMS_FILTER_DESCRIPTION_DESCR");?>"/></td>
	</tr>
	<tr>
		<td><?=GetMessage("WDA_PROFILES_FILTER_SEND_EMAIL")?>:</td>
		<td>
			<select name="find_send_email">
				<option value=""><?=GetMessage('MAIN_ALL');?></option>
				<option value="Y"><?=GetMessage('MAIN_YES');?></option>
				<option value="N"><?=GetMessage('MAIN_NO');?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td><?=GetMessage("WDA_PROFILES_FILTER_IBLOCK_ID")?>:</td>
		<td>
			<?$arIBlocks = CWDA::GetIBlockList(true, false);?>
			<select name="find_iblock_id">
				<option value=""><?=GetMessage('MAIN_ALL');?></option>
				<?foreach($arIBlocks as $IBlockTypeCode => $arIBlockType):?>
					<?if(is_array($arIBlockType['ITEMS'])&&!empty($arIBlockType['ITEMS'])):?>
						<optgroup label="<?=$arIBlockType['NAME'];?>">
							<?foreach($arIBlockType['ITEMS'] as $arItem):?>
								<option value="<?=$arItem['ID'];?>">[<?=$arItem['ID'];?>] [<?=$arItem['CODE'];?>] <?=$arItem['NAME'];?></option>
							<?endforeach?>
						</optgroup>
					<?endif?>
				<?endforeach?>
			</select>
		</td>
	</tr>
	<tr>
		<td><?=GetMessage("WDA_PROFILES_FILTER_ACTION")?>:</td>
		<td>
			<select name="find_action">
				<option value=""><?=GetMessage('MAIN_ALL');?></option>
				<?foreach ($arModuleActions as $arModuleAction):?>
					<option value="<?=$arModuleAction['CODE'];?>"><?=$arModuleAction['NAME']?></option>
				<?endforeach?>
			</select>
		</td>
	</tr>
	<?$oFilter->Buttons(array("table_id"=>$sTableID,"url"=>$APPLICATION->GetCurPage(),"form" => "find_form"));?>
	<?$oFilter->End();?>
</form>

<?// Output ?>
<?$lAdmin->DisplayList();?>

<?
if(!CWDA::WdaCheckCli()) {
	CAdminMessage::ShowMessage(array(
		"MESSAGE" => GetMessage("WDA_CLI_CHECK_TITLE"),
		"DETAILS" => GetMessage("WDA_CLI_CHECK_CONTENT"),
		"HTML" => true,
	)); 
}
?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>