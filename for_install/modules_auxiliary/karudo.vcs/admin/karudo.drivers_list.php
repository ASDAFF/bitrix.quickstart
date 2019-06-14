<?php
define("ADMIN_MODULE_NAME", "karudo.vcs");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
CModule::IncludeModule('karudo.vcs');

IncludeModuleLangFile(__FILE__);

if(!$USER->IsAdmin()) {
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

$LIST_URL = $APPLICATION->GetCurPage() . '?lang=' . LANG;
$EDIT_URL = '/bitrix/admin/karudo.driver_edit.php?lang=' . LANG;

CVCSMain::InitJS();

$message = false;

$sTableID = 't_drivers_list';
$oSort = new CAdminSorting($sTableID, 'SORT', 'asc');
$lAdmin = new CAdminList($sTableID, $oSort);

if ($lAdmin->EditAction()) {
	foreach($FIELDS as $ID => $arFields) {
		if (!empty($arFields['ACTIVE'])) {
			$arFields['ACTIVE'] = (empty($arFields['ACTIVE']) || $arFields['ACTIVE'] === 'N') ? 0 : 1;
			if (!CVCSDriversFactory::Update($ID, $arFields)) {
				$ex = CVCSMain::GetAPPLICATION()->GetException();
				$lAdmin->AddGroupError($ex->GetString());
			}
		}
	}
}

if ($arID = $lAdmin->GroupAction()) {
	if ($_REQUEST['action_target']=='selected') {
		$rsData = CVCSDriversFactory::GetList();
		while($arRes = $rsData->Fetch()) {
			$arID[] = $arRes['ID'];
		}
	}

	foreach ($arID as $ID) {
		CVCSDriversFactory::Update($ID, array('ACTIVE' => $_REQUEST['action'] == 'activate' ? 1 : 0));
	}
}

$rsData = CVCSDriversFactory::GetList(array($by => $order));
$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart(50);

$lAdmin->NavText($rsData->GetNavPrint(GetMessage('VCS_DRIVERS')));

$arHeaders = Array();

$arHeaders[] = Array('id'=>'ID', 'content'=>'ID', 'default'=>true, 'sort' => 'ID');
$arHeaders[] = Array('id'=>'DRIVER_CODE', 'content'=>GetMessage('VCS_HEADER_DRIVER_CODE'), 'default'=>true, 'sort' => 'DRIVER_CODE');
$arHeaders[] = Array('id'=>'NAME', 'content'=>GetMessage('VCS_HEADER_NAME'), 'default'=>true, 'sort'=>'NAME');
$arHeaders[] = Array('id'=>'ACTIVE', 'content'=>GetMessage('VCS_HEADER_ACTIVE'), 'default'=>true, 'sort' => 'ACTIVE');
$arHeaders[] = Array('id'=>'TIMESTAMP_X', 'content'=>GetMessage('VCS_HEADER_TIMESTAMP_X'), 'default'=>true, 'sort' => 'TIMESTAMP_X');
$arHeaders[] = Array('id'=>'LAST_CHECK', 'content'=>GetMessage('VCS_HEADER_LAST_CHECK'), 'default'=>true, 'sort' => 'LAST_CHECK');

$lAdmin->AddHeaders($arHeaders);

while ($arDriver = $rsData->GetNext()) {
	$arDriver['ACTIVE'] = empty($arDriver['ACTIVE']) ? 'N' : 'Y';
	$row =& $lAdmin->AddRow($arDriver['ID'], $arDriver);
	//$row->AddViewField('ACTIVE', empty($arDriver['ACTIVE']) ? GetMessage('MAIN_NO') : GetMessage('MAIN_YES'));
	$row->AddCheckField("ACTIVE");
	$row->AddInputField('NAME');

	$arActions = array();
	$arActions[] = array(
		"ICON"=>"edit",
		"TEXT"=>GetMessage("MAIN_ADMIN_MENU_EDIT"),
		"ACTION"=>$lAdmin->ActionRedirect($EDIT_URL . '&ID='.$arDriver['ID']),
		"DEFAULT"=>true,
	);
	$arActions[] = array(
		//"ICON"=>"edit",
		"TEXT"=>GetMessage("VCS_ACTIONS_EXPORT"),
		"ACTION"=>'Karudo.page.export('.$arDriver['ID'].', '.CUtil::PhpToJSObject($arDriver['DRIVER_CODE']).')',
		//"DEFAULT"=>true,
	);
	$row->AddActions($arActions);
}

$lAdmin->AddFooter(
	array(
		array("title"=>GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value"=>$rsData->SelectedRowsCount()),
		array("counter"=>true, "title"=>GetMessage("MAIN_ADMIN_LIST_CHECKED"), "value"=>"0"),
	)
);

$aContext = array(
	array(
		"ICON" => "btn_new",
		"TEXT" => GetMessage("VCS_ADD_NEW_DRIVER"),
		"LINK" => $EDIT_URL,
		"TITLE" => GetMessage("VCS_ADD_NEW_DRIVER_TITLE")
	),
);

$lAdmin->AddAdminContextMenu($aContext, false);

$lAdmin->AddGroupActionTable(Array(
	'activate'=>GetMessage('MAIN_ADMIN_LIST_ACTIVATE'),
	'deactivate'=>GetMessage('MAIN_ADMIN_LIST_DEACTIVATE'),
));

$lAdmin->CheckListMode();
$APPLICATION->SetTitle(GetMessage('VCS_DRIVERS_TITLE'));
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
if ($message) {
	echo $message->Show();
}

$lAdmin->DisplayList();
?>
<div id="karudo" class="bootstrap">
	<div id="karudo-export" class="modal hide fade " style="max-height: none;">
		<div class="modal-header">
			<a class="close" data-dismiss="modal">x</a>
			<h3><?=GetMessage("VCS_MODAL_EXPORT_TITLE")?></h3>
		</div>
		<div class="modal-body">

			<form class="form-horizontal">
				<fieldset>
					<div class="control-group">
						<label for="fromRevision" class="control-label"><?=GetMessage("VCS_EXP_DIALOG_REVISION_FROM")?></label>
						<div class="controls">
							<input type="text" id="fromRevision" value="" class="span1" style="height: 24px;" title="<?=GetMessage("VCS_EXP_DIALOG_INPUT_TITLE")?>">
						</div>
					</div>
					<div class="control-group">
						<label for="toRevision" class="control-label"><?=GetMessage("VCS_EXP_DIALOG_REVISION_TO")?></label>
						<div class="controls">
							<input type="text" id="toRevision" value="" class="span1" style="height: 24px;" title="<?=GetMessage("VCS_EXP_DIALOG_INPUT_TITLE")?>">
						</div>
					</div>
					<div class="control-group">
						<label for="exportDir" class="control-label"><?=GetMessage("VCS_EXP_DIALOG_DIR")?></label>
						<div class="controls">
							<input type="text" id="exportDir" value="" class="span4" style="height: 24px;" title="<?=GetMessage("VCS_EXP_DIALOG_INPUT_TITLE_DIR")?>">
						</div>
					</div>
				</fieldset>
			</form>


		</div>
		<div class="modal-footer">
			<a href="#" class="btn btn-success"><?=GetMessage("VCS_EXP_DIALOG_OK")?></a>
			<a href="#" class="btn" data-dismiss="modal"><?=GetMessage("VCS_EXP_DIALOG_CANCEL")?></a>
		</div>
	</div>
</div>
<script type="text/javascript">
(function(K) {
	var $ = K.$
	$(function() {
		$('#karudo-export input').tooltip();
		$('#karudo-export .btn-success').click(function() {
			var arr = {
				id: $('#karudo-export').data('exp-id'),
				revision_from: $('#fromRevision').val(),
				revision_to: $('#toRevision').val() ,
				dir: $('#exportDir').val()
			};
			K.ServiceCall({service: 'drivers', cmd: 'export'}, arr).always(function() {
				$('#karudo-export').modal('hide');
			}).done(function(r) {
				K.ui.alert(r.message);
			});
			return false;
		});
	});
	K.page = {
		export: function(id, code) {
			$('#exportDir').val('/bitrix/tmp/<?=CVCSConfig::MODULE_ID?>/'  + code);

			$('#karudo-export').data('exp-id', id).modal();
		}
	}

})(window.Karudo);
</script>
<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");