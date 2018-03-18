<?php
define("ADMIN_MODULE_NAME", "karudo.vcs");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
CModule::IncludeModule('karudo.vcs');

$DOWNLOAD_URL = '/bitrix/admin/karudo.download_item.php';

IncludeModuleLangFile(__FILE__);

if(!$USER->IsAdmin()) {
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

CVCSMain::InitJS();

$message = false;

$Item = CVCSItem::GetByID(intval($_GET['ID']));
if (empty($Item)) {
	LocalRedirect('karudo.items_list.php?lang='.LANG);
}
$arDriver = CVCSMain::GetDriverByCode($Item->GetDriverCode());

$sTableID = 't_item_revisions_list';
$oSort = new CAdminSorting($sTableID, 'REVISION_ID', 'desc');
$lAdmin = new CAdminList($sTableID, $oSort);

$rsData = $Item->GetRevisionsList(array($by => $order), array('>=USER_ID' => 0), array(), array('ID', 'REVISION_ID', 'TIMESTAMP_X', 'REVISION_DESC', 'USER_ID', 'IS_NEW', 'DELETED', 'REVISION_ID'));
$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart(50);

$lAdmin->NavText($rsData->GetNavPrint(GetMessage("VCS_REVISIONS")));

$arHeaders = Array();

$arHeaders[] = Array('id'=>'ID', 'content'=>'ID', 'default'=>false, 'sort' => 'ID');
$arHeaders[] = Array('id'=>'REVISION_ID', 'content'=>GetMessage("VCS_HEADER_REVISION_ID"), 'default'=>true, 'sort' => 'REVISION_ID');
$arHeaders[] = Array('id'=>'REVISION_DESC', 'content'=>GetMessage("VCS_HEADER_REVISION_DESC"), 'default'=>true);
$arHeaders[] = Array('id'=>'TIMESTAMP_X', 'content'=>GetMessage("VCS_HEADER_TIMESTAMP_X"), 'default'=>true, 'sort' => 'TIMESTAMP_X');
$arHeaders[] = Array('id'=>'USER_LOGIN', 'content'=>GetMessage("VCS_HEADER_USER_LOGIN"), 'default'=>true);

$lAdmin->AddHeaders($arHeaders);

while ($arRevision = $rsData->GetNext()) {
	if (!empty($arRevision['USER_ID'])) {
		$arUser = CUser::GetByID($arRevision['USER_ID'])->Fetch();
		$arRevision['USER_LOGIN'] = $arUser['LOGIN'];
	}
	$row =& $lAdmin->AddRow($arRevision['ID'], $arRevision);

	$arActions = Array();

	$arActions[] = array(
		//'ICON' => 'delete',
		'TEXT' => GetMessage("VCS_SHOW_SOURCE"),
		'ACTION' => 'Karudo.page.showSource('.$arRevision['REVISION_ID'].')',
	);

	if (empty($arRevision['IS_NEW'])) {
		$arActions[] = array(
			'TEXT' => GetMessage("VCS_SHOW_CHANGES"),
			'ACTION' => 'Karudo.page.showChanges('.$arRevision['REVISION_ID'].')',
		);
	}

	$arActions[] = array(
		//'ICON' => 'delete',
		'TEXT' => GetMessage("VCS_RESET_FROM_THIS_REV"),
		'ACTION' => 'Karudo.page.reset('.$arRevision['REVISION_ID'].')',
	);

	$arActions[] = array(
		'TEXT'=>GetMessage("VCS_DOWNLOAD"),
		'ACTION'=>$lAdmin->ActionRedirect($DOWNLOAD_URL . '?item_id=' . $Item->GetID() . '&revision_id=' . $arRevision['REVISION_ID'])
	);

	$row->AddActions($arActions);
}

$lAdmin->CheckListMode();

$APPLICATION->SetTitle(GetMessage("VCS_PAGE_HEADER"));
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
if ($message) {
	echo $message->Show();
}
?>

<?=BeginNote()?>
<?=GetMessage("VCS_SHOW_REVISIONS_NOTE")?>
<?=$arDriver['name']?> - <?=$Item->GetOrigID()?>
<?=EndNote()?>

<?php
$lAdmin->DisplayList();
?>
<script type="text/javascript">
(function(K) {
	var item_id = <?=intval($Item->GetID())?>;
	K.page = {
		showSource: function(revision_id) {
			K.ServiceCall({service: 'item', cmd: 'showSource'}, {id: item_id, revision_id: revision_id}).done(function(r) {
				K.ui.window(r.source, <?=CUtil::PhpToJSObject($Item->GetOrigID())?>);
			});

		},
		showChanges: function(revision_id) {
			K.ServiceCall({service: 'item', cmd: 'showChanges'}, {id: item_id, revision_id: revision_id}).done(function(r) {
				K.ui.window(r.source, <?=CUtil::PhpToJSObject($Item->GetOrigID())?>);
			});
		},

		reset: function(revision_id) {
			K.ui.confirm("<?=GetMessage("VCS_RESET_CONFIRM")?>", function() {
				K.vcs.reset({
					revision_id: revision_id,
					item_id: <?=$Item->GetID()?>,
					driver: '<?=$Item->GetDriverCode()?>'
				});
			});
		}
	}
})(window.Karudo);
</script>
<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");