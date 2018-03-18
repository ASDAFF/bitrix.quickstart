<?php
define("ADMIN_MODULE_NAME", "karudo.vcs");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
CModule::IncludeModule('karudo.vcs');

IncludeModuleLangFile(__FILE__);

if(!$USER->IsAdmin()) {
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

$LIST_URL = $APPLICATION->GetCurPage() . '?lang=' . LANG;
$ITEMS_URL = '/bitrix/admin/karudo.items_list.php';

CVCSMain::InitJS();

$message = false;

$sTableID = 't_revision_list';
$oSort = new CAdminSorting($sTableID, 'ID', 'desc');
$lAdmin = new CAdminList($sTableID, $oSort);

$rsData = CVCSRevisionFactory::GetList(array($by => $order));
$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart(50);

$lAdmin->NavText($rsData->GetNavPrint(GetMessage('VCS_REVISIONS')));

$arHeaders = Array();

$arHeaders[] = Array('id'=>'ID', 'content'=>'ID', 'default'=>true, 'sort' => 'ID');
$arHeaders[] = Array('id'=>'DESCRIPTION', 'content'=>GetMessage('VCS_HEADER_DESCRIPTION'), 'default'=>true, 'sort' => 'DESCRIPTION');
$arHeaders[] = Array('id'=>'USER_LOGIN', 'content'=>GetMessage('VCS_HEADER_USER_LOGIN'), 'default'=>true);
$arHeaders[] = Array('id'=>'DATEADD', 'content'=>GetMessage('VCS_HEADER_DATEADD'), 'default'=>true, 'sort' => 'DATEADD');
$arHeaders[] = Array('id'=>'COUNT_ITEMS', 'content'=>GetMessage('VCS_HEADER_COUNT_ITEMS'), 'default'=>false);

$lAdmin->AddHeaders($arHeaders);

while ($arRevision = $rsData->GetNext()) {
	if (!empty($arRevision['USER_ID'])) {
		$arUser = CUser::GetByID($arRevision['USER_ID'])->Fetch();
		$arRevision['USER_LOGIN'] = $arUser['LOGIN'];
	}
	$row =& $lAdmin->AddRow($arRevision['ID'], $arRevision);
	$row->AddViewField('DESCRIPTION', $arRevision['DESCRIPTION']);

	$arActions = Array();

	$arActions[] = array(
		'ICON'=>'edit',
		'DEFAULT' => 'Y',
		'TEXT'=>GetMessage('VCS_REVISION_ITEMS'),
		'ACTION'=>$lAdmin->ActionRedirect($ITEMS_URL.'?lang='.LANGUAGE_ID.'&set_filter=Y&FIND_DELETED=all&FIND_SOURCE_REVISION_ID='.$arRevision['ID'])
	);

	$arActions[] = array(
		//'ICON' => 'delete',
		'TEXT' => GetMessage("VCS_RESET_REV"),
		'ACTION' => 'Karudo.page.reset('.$arRevision['ID'].')',
	);

	$row->AddActions($arActions);
}

$lAdmin->CheckListMode();

$APPLICATION->SetTitle(GetMessage('VCS_REVISIONS_TITLE'));
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
if ($message) {
	echo $message->Show();
}
?>


<?php
$lAdmin->DisplayList();
?>

<script type="text/javascript">
(function(K) {
	K.page = {
		reset: function(revision_id) {
			K.ui.confirm("<?=GetMessage("VCS_ACHTUNG")?>", function() {
				K.ServiceCall({service: 'drivers', cmd: 'GetList'}).done(function(r) {
					K.ui.selector(r, {title: "<?=GetMessage("VCS_DRIVERS_SEL")?>", text: "<?=GetMessage("VCS_DRIVERS_SEL_TEXT")?>"}, function(sel_drivers) {
						K.vcs.reset({revision_id: revision_id, driver: sel_drivers}).done(function() {
							K.ui.alert("<?=GetMessage("VCS_DONE")?>");
						})
					});
				});
			})
		}
	};
})(window.Karudo);
</script>

<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");