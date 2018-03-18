<?php
define("ADMIN_MODULE_NAME", "karudo.vcs");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
CModule::IncludeModule('karudo.vcs');

IncludeModuleLangFile(__FILE__);

if(!$USER->IsAdmin()) {
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

$LIST_URL = $APPLICATION->GetCurPage() . '?lang=' . LANG;
$REV_LIST_URL = '/bitrix/admin/karudo.item_revisions_list.php?lang=' . LANG;
$DOWNLOAD_URL = '/bitrix/admin/karudo.download_item.php';

CVCSMain::InitJS();

$message = false;

$sTableID = 't_items_list';
$oSort = new CAdminSorting($sTableID, 'SORT', 'asc');
$lAdmin = new CAdminList($sTableID, $oSort);

$filter = new CAdminFilter(
	$sTableID.'_filter_id',
	array(
		'DRIVER_CODE',
		'ORIG_ID',
		'REVISION_ID',
		'FIRST_REVISION_ID',
		'SOURCE_REVISION_ID',
		'DELETED',
		'DELETED_IN_REVISION'
	)
);

$arFilterFields = Array(
	'FIND_DRIVER_CODE',
	'FIND_ORIG_ID',
	'FIND_REVISION_ID',
	'FIND_FIRST_REVISION_ID',
	'FIND_SOURCE_REVISION_ID',
	'FIND_DELETED',
	'FIND_DELETED_IN_REVISION',

	'FIND_OP_REVISION_ID',
	'FIND_OP_FIRST_REVISION_ID',
	'FIND_OP_SOURCE_REVISION_ID'
);

$lAdmin->InitFilter($arFilterFields);
if (strlen($FIND_DELETED) < 1) {
	$FIND_DELETED = 0;
}
$arFilter = array(
	//'DELETED' => 0
);
foreach($arFilterFields as $key) {
	if (array_key_exists($key, $GLOBALS) && strlen($GLOBALS[$key]) > 0) {
		$s_key = substr($key, 5);
		$s_op = empty($GLOBALS['FIND_OP_' . $s_key]) ? '=' : $GLOBALS['FIND_OP_' . $s_key];
		if (!in_array($s_op, CVCSAdminHelpers::GetFilterOperationsArray())) {
			$s_op = '=';
		}
		/*if ($s_key == 'DRIVER_CODE') {
			$s_op = '=';
		}*/
		if ($s_key == 'ORIG_ID') {
			$arFilter['%' . $s_key] = $GLOBALS[$key];
		} elseif (($s_key == 'DELETED' && is_numeric($GLOBALS[$key])) || $s_key != 'DELETED'){
			$arFilter[$s_op . $s_key] = $GLOBALS[$key];
		}
	}
}

$rsData = CVCSItemFactory::GetList(array($by => $order), $arFilter, array('select_revs_count' => true,/* 'deleted_in_revision' => true*/), array('ID', 'DRIVER_CODE', 'ORIG_ID', 'FIRST_REVISION_ID', 'REVISION_ID', 'TIMESTAMP_X', 'DELETED', 'DELETED_IN_REVISION', 'REVISIONS_COUNT'));
$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart(50);

$lAdmin->NavText($rsData->GetNavPrint(GetMessage('VCS_ITEMS')));

$arHeaders = Array();

$arHeaders[] = Array('id'=>'ID', 'content'=>'ID', 'default'=>false, 'sort' => 'ID');
$arHeaders[] = Array('id'=>'DRIVER_CODE', 'content'=>GetMessage('VCS_HEADER_DRIVER_CODE'), 'default'=>true, 'sort' => 'DRIVER_CODE');
$arHeaders[] = Array('id'=>'ORIG_ID', 'content'=>GetMessage('VCS_HEADER_ORIG_ID'), 'default'=>true, 'sort' => 'ORIG_ID');
$arHeaders[] = Array('id'=>'FIRST_REVISION_ID', 'content'=>GetMessage('VCS_HEADER_FIRST_REVISION_ID'), 'default'=>true, 'sort' => 'FIRST_REVISION_ID');
$arHeaders[] = Array('id'=>'REVISION_ID', 'content'=>GetMessage('VCS_HEADER_REVISION_ID'), 'default'=>true, 'sort' => 'REVISION_ID');
$arHeaders[] = Array('id'=>'TIMESTAMP_X', 'content'=>GetMessage('VCS_HEADER_TIMESTAMP_X'), 'default'=>true, 'sort' => 'TIMESTAMP_X');
$arHeaders[] = Array('id'=>'REVISIONS_COUNT', 'content'=>GetMessage('VCS_HEADER_REVISIONS_COUNT'), 'default'=>true, 'sort' => 'REVISIONS_COUNT');
$arHeaders[] = Array('id'=>'DELETED', 'content'=>GetMessage('VCS_HEADER_DELETED'), 'default'=>true, 'sort' => 'DELETED');
//$arHeaders[] = Array('id'=>'DELETED_IN_REVISION', 'content'=>GetMessage('VCS_HEADER_DELETED_IN_REVISION'), 'default'=>false, 'sort' => 'DELETED_IN_REVISION');

$lAdmin->AddHeaders($arHeaders);

$arDrivers = CVCSMain::GetDriversArray(array('all' => true));
while ($arItem = $rsData->GetNext()) {
	$row =& $lAdmin->AddRow($arItem['ID'], $arItem);
	$row->AddViewField("DRIVER_CODE", $arDrivers[$arItem['DRIVER_CODE']]['name']);
	$row->AddViewField("DELETED", (empty($arItem['DELETED']) ? GetMessage('VCS_NO') : (GetMessage('VCS_YES') . ' ('.$arItem['DELETED_IN_REVISION'].')') ));
	//$row->AddViewField("TIMESTAMP_X", ConvertDateTime($arChangedItem['TIMESTAMP_X'],));

	$arActions = Array();

	$arActions[] = array(
		//'ICON' => 'delete',
		'TEXT' => GetMessage('VCS_SHOW_SOURCE'),
		'ACTION' => 'Karudo.page.showSource('.$arItem['ID'].', '.CUtil::PhpToJSObject($arItem['ORIG_ID']).')',
	);

	if ($arItem['FIRST_REVISION_ID'] != $arItem['REVISION_ID']) {
		$arActions[] = array(
			'TEXT' => GetMessage('VCS_SHOW_CHANGES'),
			'ACTION' => 'Karudo.page.showChanges('.$arItem['ID'].', '.CUtil::PhpToJSObject($arItem['ORIG_ID']).')',
		);
	}

	$arActions[] = array(
		//'ICON'=>'edit',
		'DEFAULT' => 'Y',
		'TEXT'=>GetMessage("VCS_REVISION_LIST"),
		'ACTION'=>$lAdmin->ActionRedirect($REV_LIST_URL.'&ID='.$arItem['ID'])
	);

	$arActions[] = array(
		'TEXT'=>GetMessage("VCS_DOWNLOAD"),
		'ACTION'=>$lAdmin->ActionRedirect($DOWNLOAD_URL.'?item_id='.$arItem['ID'])
	);
	$arActions[] = array(
		'ICON' => 'delete',
		'TEXT' => GetMessage('VCS_DELETE'),
		'ACTION' => 'Karudo.page.deleteItem('.$arItem['ID'].')',
	);

	$row->AddActions($arActions);
}

$lAdmin->AddFooter(
	array(
		array('title'=>GetMessage('MAIN_ADMIN_LIST_SELECTED'), 'value'=>$rsData->SelectedRowsCount()),
		//array('counter'=>true, 'title'=>GetMessage('MAIN_ADMIN_LIST_CHECKED'), 'value'=>'0'),
	)
);

/*$lAdmin->AddGroupActionTable(Array(
		'delete'=>GetMessage('MAIN_ADMIN_LIST_DELETE'),
	)
);*/



$lAdmin->AddAdminContextMenu(array(), false, true);

$lAdmin->CheckListMode();
$APPLICATION->SetTitle(GetMessage('VCS_ITEMS_TITLE'));
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
if ($message) {
	echo $message->Show();
}
?>
<form name="form1" method="GET" action="<?=$APPLICATION->GetCurPage()?>?" id="filter-form">
	<?php $filter->Begin(); ?>
	<tr>
		<td><?=GetMessage("VCS_FILTER_FRIVER_CODE")?>:</td>
		<td>
			<select name="FIND_DRIVER_CODE">
				<option value=""><?=GetMessage('VCS_FILTER_ALL')?></option>
				<? foreach ($arDrivers as $k => $v) { ?>
				<option value="<?=$k?>"<? if ($k == $FIND_DRIVER_CODE) { ?> selected="selected"<? } ?>><?=$v['name']?></option>
				<? } ?>
			</select>
		</td>
	</tr>
	<tr>
		<td><?=GetMessage("VCS_FILTER_FILE")?>:</td>
		<td><input type="text" name="FIND_ORIG_ID" size="47" value="<?=htmlspecialchars($FIND_ORIG_ID)?>">&nbsp;<?=ShowFilterLogicHelp()?></td>
	</tr>
	<tr>
		<td><?=GetMessage("VCS_FILTER_REVISION")?>:</td>
		<td>
			<?=CVCSAdminHelpers::GetFiltersOperationsSelect('FIND_OP_REVISION_ID', $FIND_OP_REVISION_ID)?>
			<input type="text" name="FIND_REVISION_ID" size="30" value="<?=htmlspecialchars($FIND_REVISION_ID)?>">
		</td>
	</tr>
	<tr>
		<td><?=GetMessage("VCS_FILTER_FIRST_REVISION")?>:</td>
		<td>
			<?=CVCSAdminHelpers::GetFiltersOperationsSelect('FIND_OP_FIRST_REVISION_ID', $FIND_OP_FIRST_REVISION_ID)?>
			<input type="text" name="FIND_FIRST_REVISION_ID" size="30" value="<?=htmlspecialchars($FIND_FIRST_REVISION_ID)?>">
		</td>
	</tr>
	<tr>
		<td><?=GetMessage("VCS_FILTER_SOURCE_REVISION_ID")?>:</td>
		<td>
			<?=CVCSAdminHelpers::GetFiltersOperationsSelect('FIND_OP_SOURCE_REVISION_ID', $FIND_OP_SOURCE_REVISION_ID)?>
			<input type="text" name="FIND_SOURCE_REVISION_ID" size="30" value="<?=htmlspecialchars($FIND_SOURCE_REVISION_ID)?>">
		</td>
	</tr>
	<tr>
		<td><?=GetMessage("VCS_FILTER_DELETED")?>:</td>
		<td>
			<select name="FIND_DELETED" id="select-find-deleted">
				<option value="0"<? if(strlen($FIND_DELETED) > 0 && is_numeric($FIND_DELETED) && $FIND_DELETED == 0) { ?> selected="selected"<? } ?>><?=GetMessage('MAIN_NO')?></option>
				<option value="1"<? if(strlen($FIND_DELETED) > 0 && is_numeric($FIND_DELETED) && $FIND_DELETED == 1) { ?> selected="selected"<? } ?>><?=GetMessage('MAIN_YES')?></option>
				<option value="all"<? if(strlen($FIND_DELETED) > 0 && $FIND_DELETED === 'all') { ?> selected="selected"<? } ?>><?=GetMessage('MAIN_ALL')?></option>
			</select>
		</td>
	</tr>
	<tr class="tr-deleted-in-revision">
		<td><?=GetMessage("VCS_FILTER_DELETED_IN_REVISION")?>:</td>
		<td>
			<?=CVCSAdminHelpers::GetFiltersOperationsSelect('FIND_OP_DELETED_IN_REVISION', $FIND_OP_DELETED_IN_REVISION)?>
			<input type="text" name="FIND_DELETED_IN_REVISION" size="30" value="<?=htmlspecialchars($FIND_DELETED_IN_REVISION)?>">
		</td>
	</tr>
	<?php
	$filter->Buttons(array("table_id"=>$sTableID, "url"=>$APPLICATION->GetCurPage(), "form"=>"form1"));
	$filter->End();
	?>
</form>
<?php
$lAdmin->DisplayList();
?>
<script type="text/javascript">
(function(K) {
	var $ = K.$;
	K.page = {
		showSource: function(id, title) {
			K.ServiceCall({service: 'item', cmd: 'showSource'}, {id: id}).done(function(r) {
				K.ui.window(r.source, title);
			});
		},

		showChanges: function(id, title) {
			K.ServiceCall({service: 'item', cmd: 'showChanges'}, {id: id}).done(function(r) {
				K.ui.window(r.source, title);
			});
		},

		deleteItem: function(id) {
			K.ui.confirm(<?=CUtil::PhpToJSObject(GetMessage('VCS_DELETE_CONFIRM'))?>, function() {
				K.ServiceCall({service: 'item', cmd: 'delete'}, {id: id}).done(function(r) {
					K.ui.alert(<?=CUtil::PhpToJSObject(GetMessage('VCS_DELETE_AFTER'))?>);
				});
			});
		}
	};

	$(function() {
		var f = function() {
			if ($('#select-find-deleted').val() === "0") {
				$('.tr-deleted-in-revision select,.tr-deleted-in-revision input').attr('disabled', 'disabled');
			} else {
				$('.tr-deleted-in-revision select,.tr-deleted-in-revision input').removeAttr('disabled');
			}
			return f;
		};

		$('#select-find-deleted').change(f());
	});
})(window.Karudo)

</script>
<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");