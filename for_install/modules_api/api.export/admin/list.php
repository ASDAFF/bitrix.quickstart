<?
/**
 * Bitrix vars
 *
 * @var CUser $USER
 * @var CMain $APPLICATION
 *
 */

use \Bitrix\Main\Loader;
use \Bitrix\Main\Application;
use \Bitrix\Main\Localization\Loc;

define("ADMIN_MODULE_NAME", "api.export");
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php'); // первый общий пролог
Loc::loadMessages(__FILE__);

$rights = $APPLICATION->GetGroupRight(ADMIN_MODULE_NAME);
if($rights < 'W')
	$APPLICATION->AuthForm(Loc::getMessage('ACCESS_DENIED'));

if(!Loader::includeModule(ADMIN_MODULE_NAME))
	$APPLICATION->AuthForm(Loc::getMessage("ACCESS_DENIED"));

if(!Loader::includeModule('iblock')) {
	$APPLICATION->AuthForm(Loc::getMessage("ACCESS_DENIED"));
}

use Api\Export\ProfileTable;
use Api\Export\Tools;

//Лэнги полей
$enableHeaders = array(
	 'ID', 'NAME', 'ACTIVE', 'STEP_LIMIT', 'SORT', 'SITE_ID', 'MODIFIED_BY', 'DATE_CREATE', 'TIMESTAMP_X', 'LAST_START', 'LAST_END', 'TOTAL_ITEMS', 'TOTAL_ELEMENTS', 'TOTAL_OFFERS', 'TOTAL_SECTIONS', 'TOTAL_RUN_TIME', 'TOTAL_MEMORY'
);
$arHeaders     = array();

$enableFields = array('ID', 'ACTIVE', 'SITE_ID');
$filterFields = array();

$arFieldTitle = array();
$columns      = ProfileTable::getEntity()->getFields();

/** @var \Bitrix\Main\Entity\Field $field */
foreach($columns as $code => $column) {

	$arFieldTitle[ $column->getName() ] = $column->getTitle();

	if(in_array($code, $enableHeaders)) {
		$arHeaders[] = array('id' => $code, 'content' => $column->getTitle(), 'sort' => $code, 'default' => true);
	}

	if(in_array($code, $enableFields)) {
		if($column->getDataType() == 'integer' || $column->getDataType() == 'datetime') {
			$filterFields[] = 'find_' . $code . '_1';
			$filterFields[] = 'find_' . $code . '_2';
		}
		else {
			$filterFields[] = 'find_' . $code;
		}

		$arFilterTitles[] = $column->getTitle();
	}
}

//echo "<pre>"; print_r($arHeaders);echo "</pre>";

$conn      = Application::getConnection();
$context   = Application::getInstance()->getContext();
$request   = $context->getRequest();
$lang      = $context->getLanguage();
$errorMsgs = null;

$sTableID = ProfileTable::getTableName();
$oSort    = new CAdminSorting($sTableID, 'ID', 'asc');
$lAdmin   = new CAdminList($sTableID, $oSort);

$lAdmin->InitFilter($filterFields);

$filter = array();
if($request->get('set_filter') == 'Y') {
	if($find_ID_1)
		$filter['>=ID'] = $find_ID_1;
	if($find_ID_2)
		$filter['<=ID'] = $find_ID_2;
	if($find_ACTIVE && $find_ACTIVE != 'NOT_REF')
		$filter['=ACTIVE'] = $find_ACTIVE;
	if($find_SITE_ID && $find_SITE_ID != 'NOT_REF')
		$filter['=SITE_ID'] = $find_SITE_ID;
}

if($rights == 'W' && $lAdmin->EditAction()) {
	foreach($request->getPost('FIELDS') as $id => $arFields) {
		$error = false;
		$id    = intval($id);

		if($id <= 0 || !$lAdmin->IsUpdated($id))
			continue;

		/*$reqFields = array('RATING');
		foreach($reqFields as $reqField) {
			if(empty($arFields[ $reqField ])) {
				$error = true;
				$lAdmin->AddUpdateError('#' . $id . ' : ' . Loc::getMessage('AQAAL_FIELD_ERROR', array('#FIELD#' => $arFieldTitle[ $reqField ])), $id);
			}
		}*/

		if(!$error) {

			$conn->startTransaction();
			$res = ProfileTable::update($id, $arFields);
			if(!$res->isSuccess()) {
				$conn->rollbackTransaction();
				$lAdmin->AddUpdateError(join("\n", $res->getErrorMessages()), $id);
				continue;
			}
			$conn->commitTransaction();
		}
	}
}

if($rights == 'W' && $ids = $lAdmin->GroupAction()) {

	$rsDelete = ProfileTable::getList(array(
		 'select' => array('ID'),
		 'filter' => array('ID' => $ids),
	));

	$ids = array();
	while($result = $rsDelete->fetch())
		$ids[] = $result['ID'];

	foreach($ids as $id) {
		if(empty($id))
			continue;

		//Обязательно смотрим $_REQUEST
		$action = $_REQUEST['action'];

		switch($action) {
			case "delete":
				@set_time_limit(0);

				$result = ProfileTable::delete($id);
				if(!$result->isSuccess()) {
					if($error = $result->getErrorMessages())
						$lAdmin->AddGroupError(join("\n", $error), $id);
					else
						$lAdmin->AddGroupError(Loc::getMessage('AQAAL_ERROR_DELETE'), $id);
				}
				break;

			case 'activate':
			case 'deactivate':
				$arFields['ACTIVE'] = ($action == 'activate' ? 'Y' : 'N');
				//$arFields['ACTIVE_FROM'] = new \Bitrix\Main\Type\DateTime($arFields['ACTIVE_FROM']);
				$arFields['TIMESTAMP_X'] = new \Bitrix\Main\Type\DateTime();
				$arFields['MODIFIED_BY'] = $USER->GetID();

				$result = ProfileTable::update($id, $arFields);
				if(!$result->isSuccess()) {
					if($error = $result->getErrorMessages())
						$lAdmin->AddGroupError(join("\n", $error), $id);
					else
						$lAdmin->AddGroupError(Loc::getMessage('ARAL_ERROR_SAVE'), $id);
				}
				break;
		}
	}
}


$params    = array(
	 'select' => array('*'),
	 'filter' => $filter,
	 'order'  => array($by => $order),
);
$rsProfile = ProfileTable::getList($params);
$rsProfile = new CAdminResult($rsProfile, $sTableID);
$rsProfile->NavStart();

$lAdmin->NavText($rsProfile->GetNavPrint(Loc::getMessage("post_nav")));
$lAdmin->AddHeaders($arHeaders);
//$lAdmin->AddVisibleHeaderColumn('ID');


while($ar = $rsProfile->NavNext(true, "f_")) {
	$row = &$lAdmin->AddRow($f_ID, $ar);

	//FIELDS FOR EDIT ACTION IN LIST
	$row->AddCheckField('ACTIVE');
	$row->AddInputField('NAME', array('size' => 20));
	$row->AddInputField('SORT', array('size' => 5));
	$row->AddInputField('STEP_LIMIT', array('size' => 5));
	$row->AddField('MODIFIED_BY', Tools::getFormatedUserName($f_MODIFIED_BY, false));


	$arActions = array();
	if($rights == 'W') {
		$arActions[] = array(
			 "ICON"    => "edit",
			 "DEFAULT" => true,
			 "TEXT"    => Loc::getMessage("MAIN_ADMIN_MENU_EDIT"),
			 "ACTION"  => $lAdmin->ActionRedirect("api_export_edit.php?ID=" . $f_ID),
		);
		$arActions[] = array(
			 "ICON"   => "copy",
			 "TEXT"   => Loc::getMessage("MAIN_ADMIN_MENU_COPY"),
			 "ACTION" => $lAdmin->ActionRedirect("api_export_edit.php?ID=" . $f_ID . "&amp;action=copy"),
		);
		$arActions[] = array(
			 "ICON"   => "delete",
			 "TEXT"   => Loc::getMessage("MAIN_ADMIN_MENU_DELETE"),
			 "ACTION" => "if(confirm('" . Loc::getMessage("CONFIRM_DELETE") . "')) " . $lAdmin->ActionDoGroup($f_ID, "delete"),
		);
	}

	$row->AddActions($arActions);
}



//ADMIN LIST: FOOTER
$lAdmin->AddFooter(
	 array(
			array(
				 "title" => Loc::getMessage("MAIN_ADMIN_LIST_SELECTED"),
				 "value" => $rsProfile->SelectedRowsCount(),
			),
			array(
				 "title"   => Loc::getMessage("MAIN_ADMIN_LIST_CHECKED"),
				 "value"   => "0",
				 "counter" => true,
			),
	 )
);
//Кнопка Добавить
$lAdmin->AddAdminContextMenu(array(
	 array(
			"TEXT" => Loc::getMessage("MAIN_ADD"),
			"LINK" => "api_export_edit.php?lang=" . LANG,
			"ICON" => "btn_new",
	 ),
));

if($rights == 'W') {

	//Массовые операции
	$lAdmin->AddGroupActionTable(Array(
		 "delete"     => Loc::getMessage("MAIN_ADMIN_MENU_DELETE"),
		 "activate"   => Loc::getMessage("MAIN_ADMIN_LIST_ACTIVATE"),
		 "deactivate" => Loc::getMessage("MAIN_ADMIN_LIST_DEACTIVATE"),
	));
}


$lAdmin->CheckListMode();

$APPLICATION->SetTitle(Loc::getMessage('AEAL_PAGE_TITLE'));
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php');
?>
	<form name="find_form" method="get" action="<?=$APPLICATION->GetCurPage();?>">
		<?
		$oFilter = new CAdminFilter($sTableID . "_filter", $arFilterTitles);
		?>
		<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
		<?
		$oFilter->Begin();
		?>
		<? foreach($columns as $code => $fld): ?>
			<? if(in_array($code, $enableFields)): ?>
				<tr>
					<td><?=htmlspecialcharsbx($fld->getTitle())?><? if($fld->getDataType() == 'integer'): ?> (<?=Loc::getMessage('AEAL_FROM_AND_TO')?>)<? endif ?>:</td>
					<td>
						<? if($fld->getDataType() == 'integer'): ?>
							<input type="text" name="find_<?=$code?>_1" value="<?=htmlspecialcharsbx($GLOBALS[ 'find_' . $code . '_1' ])?>">
							...
							<input type="text" name="find_<?=$code?>_2" value="<?=htmlspecialcharsbx($GLOBALS[ 'find_' . $code . '_2' ])?>">
						<? elseif($fld->getDataType() == 'datetime'): ?>
							<?=CalendarPeriod("find_{$code}_1", htmlspecialcharsbx($GLOBALS[ 'find_' . $code . '_1' ]), "find_{$code}_2", htmlspecialcharsbx($GLOBALS[ 'find_' . $code . '_2' ]), "find_form", "Y")?>
						<? elseif($code == 'ACTIVE'): ?>
							<select name="find_<?=$code?>">
								<option value="NOT_REF"><?=Loc::getMessage('AEAL_ACTIVE_ANY');?></option>
								<option value="Y"<? if($find_active == 'Y')
									echo " selected" ?>><?=Loc::getMessage('AEAL_ACTIVE_Y');?></option>
								<option value="N"<? if($find_active == 'N')
									echo " selected" ?>><?=Loc::getMessage('AEAL_ACTIVE_N');?></option>
							</select>
						<? elseif($code == 'SITE_ID'): ?>
							<?=CLang::SelectBox('find_' . $code, htmlspecialcharsbx($GLOBALS[ 'find_' . $code ]), Loc::getMessage('AEAL_ALL'));?>
						<? else: ?>
							<input type="text" name="find_<?=$code?>" value="<?=htmlspecialcharsbx($GLOBALS[ 'find_' . $code ])?>">
						<? endif ?>
					</td>
				</tr>
			<? endif ?>
		<? endforeach ?>
		<?
		$oFilter->Buttons(array(
			 "table_id" => $sTableID,
			 "url"      => $APPLICATION->GetCurPage(),
			 "form"     => "find_form",
		));
		$oFilter->End();
		?>
	</form>
<? $lAdmin->DisplayList(); ?>
<?
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php');
?>