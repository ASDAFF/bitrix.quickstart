<?php
define("ADMIN_MODULE_NAME", "karudo.vcs");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
CModule::IncludeModule('karudo.vcs');

IncludeModuleLangFile(__FILE__);

if(!$USER->IsAdmin()) {
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

//$LIST_URL = $APPLICATION->GetCurPage() . '?lang=' . LANGUAGE_ID;
$LIST_URL = $APPLICATION->GetCurPageParam();

CVCSMain::InitJS();


$message = false;

$sTableID = 't_chitems_list';
$oSort = new CAdminSorting($sTableID, 'SORT', 'asc');
$lAdmin = new CAdminList($sTableID, $oSort);

$filter = new CAdminFilter(
	$sTableID.'_filter_id',
	array(
		'DRIVER_CODE',
		'ORIG_ID',
		'STATUS',
	)
);

$arFilterFields = Array(
	'FIND_DRIVER_CODE',
	'FIND_ORIG_ID',
	'FIND_STATUS'
);

$lAdmin->InitFilter($arFilterFields);
$arFilter = array();
foreach($arFilterFields as $key) {
	if (array_key_exists($key, $GLOBALS) && strlen($GLOBALS[$key]) > 0) {
		$s_key = substr($key, 5);
		if ($s_key == 'ORIG_ID') {
			$arFilter['%' . $s_key] = $GLOBALS[$key];
		} else {
			$arFilter['=' . $s_key] = $GLOBALS[$key];
		}

	}
}

if($arID = $lAdmin->GroupAction()) {
	if($_REQUEST['action_target']=='selected') {
		$rsData = CVCSChangedItemFactory::GetList(array($by => $order), $arFilter);
		while($arRes = $rsData->Fetch())
			$arID[] = $arRes['ID'];
	}

	foreach($arID as $ID) {
		if(strlen($ID)<=0)
			continue;
		$ID = intval($ID);

		switch($_REQUEST['action']) {
			case 'delete':
				@set_time_limit(0);
				CVCSChangedItemFactory::Delete($ID);
				break;
		}
	}
}


$rsData = CVCSChangedItemFactory::GetList(array($by => $order), $arFilter);
$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart(50);

$lAdmin->NavText($rsData->GetNavPrint(GetMessage('VCS_CHENGED_ITEMS')));

$arHeaders = Array();

$arHeaders[] = Array('id'=>'ID', 'content'=>'ID', 'default'=>false, 'sort' => 'ID');
$arHeaders[] = Array('id'=>'DRIVER_CODE', 'content'=>GetMessage('VCS_HEADER_DRIVER_CODE'), 'default'=>true, 'sort' => 'DRIVER_CODE');
$arHeaders[] = Array('id'=>'ORIG_ID', 'content'=>GetMessage('VCS_HEADER_ORIG_ID'), 'default'=>true, 'sort' => 'ORIG_ID');
//$arHeaders[] = Array('id'=>'IS_NEW', 'content'=>GetMessage('VCS_HEADER_IS_NEW'), 'default'=>true, 'sort' => 'IS_NEW');
$arHeaders[] = Array('id'=>'STATUS', 'content'=>GetMessage('VCS_HEADER_STATUS'), 'default'=>true, 'sort' => 'STATUS');
$arHeaders[] = Array('id'=>'TIMESTAMP_X', 'content'=>GetMessage('VCS_HEADER_TIMESTAMP_X'), 'default'=>true, 'sort' => 'TIMESTAMP_X');

$lAdmin->AddHeaders($arHeaders);

$arStatuses = array(
	CVCSConfig::CIST_NEW => '<span class="vcs-status-new">'.GetMessage('VCS_STATUS_NEW').'</span>',
	CVCSConfig::CIST_UPD => '<span class="vcs-status-upd">'.GetMessage('VCS_STATUS_UPD').'</span>',
	CVCSConfig::CIST_DEL => '<span class="vcs-status-del">'.GetMessage('VCS_STATUS_DEL').'</span>',
);

$arDrivers = CVCSMain::GetDriversArray(array('all' => true));
while ($arChangedItem = $rsData->GetNext()) {
	//$isNew = !empty($arChangedItem['IS_NEW']);
	$row =& $lAdmin->AddRow($arChangedItem['ID'], $arChangedItem);
	$row->AddViewField("STATUS", $arStatuses[$arChangedItem['STATUS']]);
	$row->AddViewField("DRIVER_CODE", $arDrivers[$arChangedItem['DRIVER_CODE']]['name']);
	//$row->AddViewField("TIMESTAMP_X", ConvertDateTime($arChangedItem['TIMESTAMP_X'],));

	$arActions = Array();

	if (CVCSConfig::CIST_NEW === $arChangedItem['STATUS']) {
		$arActions[] = array(
			//'ICON' => 'delete',
			'TEXT' => GetMessage('VCS_SHOW_SOURCE'),
			'ACTION' => 'Karudo.page.showSource('.$arChangedItem['ID'].', '.CUtil::PhpToJSObject($arChangedItem['ORIG_ID']).')'
		);
		$arActions[] = array(
			'ICON' => 'delete',
			'TEXT' => GetMessage('VCS_DELETE_FROM_DISK'),
			'ACTION' => 'Karudo.page.deleteNewFile('.$arChangedItem['ID'].')'
		);
	} elseif (CVCSConfig::CIST_UPD === $arChangedItem['STATUS']) {
		$arActions[] = array(
			//'ICON' => 'delete',
			'TEXT' => GetMessage('VCS_SHOW_CHANGES'),
			'ACTION' => 'Karudo.page.showChanges('.$arChangedItem['ID'].', '.CUtil::PhpToJSObject($arChangedItem['ORIG_ID']).')'
		);
		$arActions[] = array(
			//'ICON' => 'delete',
			'TEXT' => GetMessage('VCS_RESTORE_ITEM'),
			'ACTION' => 'Karudo.page.restore('.$arChangedItem['ID'].')'
		);
		$arActions[] = array(
			'ICON' => 'delete',
			'TEXT' => GetMessage("VCS_SET_DELETED"),
			'ACTION' => 'Karudo.page.setDeleted('.$arChangedItem['ID'].')'
		);
	} elseif (CVCSConfig::CIST_DEL === $arChangedItem['STATUS']) {
		$arActions[] = array(
			'TEXT' => GetMessage('VCS_SHOW_LAST_SOURCE'),
			'ACTION' => 'Karudo.page.showLastSource('.$arChangedItem['ID'].', '.CUtil::PhpToJSObject($arChangedItem['ORIG_ID']).')'
		);
		$arActions[] = array(
			'TEXT' => GetMessage('VCS_RESTORE_LAST_ITEM'),
			'ACTION' => 'Karudo.page.restore('.$arChangedItem['ID'].')'
		);
	}
	$arActions[] = array(
		'ICON' => 'delete',
		'TEXT' => GetMessage('VCS_DELETE'),
		'ACTION' => 'Karudo.page.questionForDelete(function() {'.$lAdmin->ActionDoGroup($arChangedItem['ID'], 'delete').'})'
	);

	$row->AddActions($arActions);
}

$lAdmin->AddFooter(
	array(
		array('title'=>GetMessage('MAIN_ADMIN_LIST_SELECTED'), 'value'=>$rsData->SelectedRowsCount()),
		array('counter'=>true, 'title'=>GetMessage('MAIN_ADMIN_LIST_CHECKED'), 'value'=>'0'),
	)
);

$lAdmin->AddGroupActionTable(Array(
		'delete'=>GetMessage('MAIN_ADMIN_LIST_DELETE'),
	)
);

$aContext = array(
	array(
		//'ICON'=> 'btn_new',
		'TEXT'=> GetMessage('VCS_CHECK_FOR_NEW'),
		'LINK'=>'javascript:Karudo.page.check();',
		'TITLE'=>GetMessage('VCS_CHECK_FOR_NEW')
	),
	array(
		'ICON'=> 'btn_new',
		'TEXT'=> GetMessage('VCS_COMMIT'),
		'LINK'=>'javascript:Karudo.page.commit();',
		'TITLE'=>GetMessage('VCS_COMMIT')
	),
);


$lAdmin->AddAdminContextMenu($aContext, false, false);

$lAdmin->CheckListMode();
$APPLICATION->SetTitle(GetMessage('VCS_CHITEMS_TITLE'));
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
if ($message) {
	echo $message->Show();
}
?>
<form name="form1" method="GET" action="<?=$APPLICATION->GetCurPage()?>?">
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
		<td>
			<input type="text" name="FIND_ORIG_ID" size="47" value="<?=htmlspecialchars($FIND_ORIG_ID)?>">&nbsp;<?=ShowFilterLogicHelp()?>
		</td>
	</tr>
	<tr>
		<td><?=GetMessage('VCS_FILTER_STATUS')?></td>
		<td><select name="FIND_STATUS">
			<option value=""><?=GetMessage('MAIN_ALL')?></option>
			<? foreach ($arStatuses as $status_code => $status_name) { ?>
			<option value="<?=$status_code?>"<? if($status_code==$FIND_STATUS) { ?> selected="selected"<? } ?>><?=$status_name?></option>
			<? } ?>

		</select></td>
	</tr>
	<?php
	$filter->Buttons(array("table_id"=>$sTableID, "url"=>$APPLICATION->GetCurPage(), "form"=>"form1"));
	$filter->End();
	?>
</form>
<?php

echo BeginNote('id="last_check_note"');
echo CVCSMain::GetLastCheckTimeText();
echo EndNote();

$lAdmin->DisplayList();

?>
<script type="text/javascript">
(function(K) {
	K.page = {
		questionForDelete: function(f) {
			K.ui.confirm(<?=CUtil::PhpToJSObject(GetMessage('VCS_DELETE_CONFIRMATION'))?>, f);
		},

		check: function() {
			K.vcs.checkForNew().done(function(r) {
				K.$('#last_check_note .content').text(r.last_check_text);
				K.ui.alert('<?=GetMessage("VCS_NEW_FILES_FOUNDED")?>' + r.count, false, function() {
					<?=$lAdmin->ActionAjaxReload($LIST_URL)?>;
				});
			});
		},

		commit: function() {
			K.vcs.commit().done(function() {
				<?=$lAdmin->ActionAjaxReload($LIST_URL)?>;
			});
		},

		showSource: function(id, title) {
			K.ServiceCall({service: 'vcs', cmd: 'showSource'}, {id: id}).done(function(r) {
				K.ui.window(r.source, title);
			});
		},

		showLastSource: function(id, title) {
			K.ServiceCall({service: 'vcs', cmd: 'showLastSource'}, {id: id}).done(function(r) {
				K.ui.window(r.source, title);
			});
		},

		showChanges: function(id, title) {
			K.ServiceCall({service: 'vcs', cmd: 'showDiff'}, {id: id}).done(function(r) {
				K.ui.window(r.source, title);
			});
		},

		deleteNewFile: function(id) {
			K.ui.confirm(<?=CUtil::PhpToJSObject(GetMessage('VCS_DELETE_CONFIRMATION'))?>, function() {
				K.ServiceCall({service: 'vcs', cmd: 'deleteNewFile'}, {id: id}).done(function(r) {
					<?=$lAdmin->ActionAjaxReload($LIST_URL)?>;
				});
			});
		},

		restore: function(id) {
			K.ui.confirm(<?=CUtil::PhpToJSObject(GetMessage('VCS_RESTORE_CONFIRMATION'))?>, function() {
				K.ServiceCall({service: 'vcs', cmd: 'restoreItem'}, {id: id}).done(function(r) {
					<?=$lAdmin->ActionAjaxReload($LIST_URL)?>;
				});
			});
		},

		setDeleted: function(id) {
			K.ui.confirm("<?=GetMessage("VCS_SET_DELETED_CONFIRMATION")?>", function() {
				K.ServiceCall({service: 'vcs', cmd: 'setDeleted'}, {id: id}).done(function(r) {
					<?=$lAdmin->ActionAjaxReload($LIST_URL)?>;
				});
			});
		}
	};
})(window.Karudo)


</script>
<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>