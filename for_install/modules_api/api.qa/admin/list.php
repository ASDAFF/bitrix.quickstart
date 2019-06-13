<?
/**
 * Bitrix vars
 *
 * @var CUser $USER
 * @var CMain $APPLICATION
 *
 */

use \Bitrix\Main\Loader,
	 \Bitrix\Main\Application,
	 \Bitrix\Main\Localization\Loc;

define("ADMIN_MODULE_NAME", "api.qa");
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');
Loc::loadMessages(__FILE__);

global $USER, $APPLICATION;

$rights = $APPLICATION->GetGroupRight(ADMIN_MODULE_NAME);
if($rights == 'D')
	$APPLICATION->AuthForm(Loc::getMessage('ACCESS_DENIED'));

if(!Loader::includeModule(ADMIN_MODULE_NAME))
	$APPLICATION->AuthForm(Loc::getMessage('ACCESS_DENIED'));

if(!Loader::includeModule('iblock'))
	$APPLICATION->AuthForm(Loc::getMessage('ACCESS_DENIED'));

$APPLICATION->SetAdditionalCSS('/bitrix/css/api.qa/admin.css');

use Api\QA\QuestionTable;
use Api\QA\Tools;


$conn    = Application::getConnection();
$context = Application::getInstance()->getContext();
$request = $context->getRequest();
$lang    = $context->getLanguage();


//Лэнги полей
$arHeaders      = array();
$arFieldTitle   = array();
$arFilterFields = array();
$arFilterTitles = array();

$arExcludeFilter = array('NOTIFY', 'VOTE_UP', 'VOTE_DO');

$columns = QuestionTable::getEntity()->getFields();

/** @var \Bitrix\Main\Entity\Field $field */
foreach($columns as $code => $column) {
	$arFieldTitle[ $column->getName() ] = $column->getTitle();

	$arHeaders[] = array(
		 'id'      => $code,
		 'content' => $column->getTitle(),
		 'sort'    => $code,
		 'default' => true,
	);

	if(!in_array($code, $arExcludeFilter)) {
		if($column->getDataType() == 'integer' || $column->getDataType() == 'datetime') {
			$arFilterFields[] = 'find_' . $code . '_1';
			$arFilterFields[] = 'find_' . $code . '_2';
		}
		else {
			$arFilterFields[] = 'find_' . $code;
		}

		$arFilterTitles[] = $column->getTitle();
	}
}

$errorMsgs = null;


$sTableID = QuestionTable::getTableName();
$oSort    = new CAdminSorting($sTableID, 'ID', 'desc');
$lAdmin   = new CAdminList($sTableID, $oSort);
$lAdmin->InitFilter($arFilterFields);


$arFilter = array();
if($find_ID_1)
	$arFilter['>=ID'] = $find_ID_1;
if($find_ID_2)
	$arFilter['<=ID'] = $find_ID_2;
if($find_ACTIVE && $find_ACTIVE != 'NOT_REF')
	$arFilter['=ACTIVE'] = $find_ACTIVE;
if($find_DATE_CREATE_1)
	$arFilter['>=DATE_CREATE'] = $find_DATE_CREATE_1;
if($find_DATE_CREATE_2)
	$arFilter['<=DATE_CREATE'] = $find_DATE_CREATE_2;
if($find_TYPE)
	$arFilter['=TYPE'] = $find_TYPE;
if($find_EMAIL)
	$arFilter['=EMAIL'] = $find_EMAIL;
if($find_PARENT_ID_1)
	$arFilter['>=PARENT_ID'] = $find_PARENT_ID_1;
if($find_PARENT_ID_2)
	$arFilter['<=PARENT_ID'] = $find_PARENT_ID_2;
if($find_LEVEL_1)
	$arFilter['>=LEVEL'] = $find_LEVEL_1;
if($find_LEVEL_2)
	$arFilter['<=LEVEL'] = $find_LEVEL_2;
if($find_USER_ID_1)
	$arFilter['>=USER_ID'] = $find_USER_ID_1;
if($find_USER_ID_2)
	$arFilter['<=USER_ID'] = $find_USER_ID_2;
if($find_GUEST_NAME)
	$arFilter['GUEST_NAME'] = $find_GUEST_NAME;
if($find_GUEST_EMAIL)
	$arFilter['=GUEST_EMAIL'] = $find_GUEST_EMAIL;
if($find_TEXT)
	$arFilter['TEXT'] = $find_TEXT;
if($find_LOCATION)
	$arFilter['=LOCATION'] = $find_LOCATION;
if($find_PAGE_URL)
	$arFilter['?PAGE_URL'] = $find_PAGE_URL;
if($find_PAGE_TITLE)
	$arFilter['?PAGE_TITLE'] = $find_PAGE_TITLE;
if($find_IBLOCK_ID_1)
	$arFilter['>=IBLOCK_ID'] = $find_IBLOCK_ID_1;
if($find_IBLOCK_ID_2)
	$arFilter['<=IBLOCK_ID'] = $find_IBLOCK_ID_2;
if($find_ELEMENT_ID_1)
	$arFilter['>=ELEMENT_ID'] = $find_ELEMENT_ID_1;
if($find_ELEMENT_ID_2)
	$arFilter['<=ELEMENT_ID'] = $find_ELEMENT_ID_2;
if($find_URL)
	$arFilter['?URL'] = $find_URL;
if($find_XML_ID)
	$arFilter['=XML_ID'] = $find_XML_ID;
if($find_CODE)
	$arFilter['?CODE'] = $find_CODE;
if($find_SITE_ID && $find_SITE_ID != 'NOT_REF')
	$arFilter['=SITE_ID'] = $find_SITE_ID;
if($find_IP)
	$arFilter['=IP'] = $find_IP;


if($rights >= 'W' && $lAdmin->EditAction()) {
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
			$res = QuestionTable::update($id, $arFields);
			if(!$res->isSuccess()) {
				$conn->rollbackTransaction();
				$lAdmin->AddUpdateError(join("\n", $res->getErrorMessages()), $id);
				continue;
			}
			$conn->commitTransaction();
		}
	}
}

if($rights >= 'W') {
	if($ids = $lAdmin->GroupAction()) {
		foreach($ids as $id) {
			if(empty($id))
				continue;

			//Обязательно смотрим $_REQUEST
			$action = $_REQUEST['action'];

			switch($action) {
				case "delete":
					Tools::deleteTree($id);
					break;

				case 'activate':
				case 'deactivate':
				case 'moderation':
					$arFields['ACTIVE'] = ($action == 'activate' ? 'Y' : 'N');
					//$arFields['ACTIVE_FROM'] = new \Bitrix\Main\Type\DateTime($arFields['ACTIVE_FROM']);
					//$arFields['TIMESTAMP_X'] = new \Bitrix\Main\Type\DateTime();
					//$arFields['MODIFIED_BY'] = $USER->GetID();

					if($action == 'moderation') {
						$arFields['ACTIVE'] = 'N';
						//$arFields['ACTIVE_FROM'] = null;
					}

					$result = QuestionTable::update($id, $arFields);
					if(!$result->isSuccess()) {
						if($error = $result->getErrorMessages())
							$lAdmin->AddGroupError(join("\n", $error), $id);
						else
							$lAdmin->AddGroupError(Loc::getMessage('AQAAL_ERROR_SAVE'), $id);
					}
					break;
			}
		}
	}
}


$arOrder = array($by => $order);

if(isset($_REQUEST['PID']) && $_REQUEST['PID'] >= 0) {
	$arFilter['=PARENT_ID'] = intval($_REQUEST['PID']);
}
if(isset($_REQUEST['TYPE'])) {
	if(in_array($_REQUEST['TYPE'], array('Q', 'A', 'C'))) {
		$arFilter['=TYPE'] = trim($_REQUEST['TYPE']);
	}
}

$params       = array(
	 'filter' => $arFilter,
	 'order'  => $arOrder,
);
$arSubscribe  = QuestionTable::getList($params);
$dbResultList = new CAdminResult($arSubscribe, $sTableID);
$dbResultList->NavStart();

$lAdmin->NavText($dbResultList->GetNavPrint(Loc::getMessage('AQAAL_NAV_TITLE')));

$lAdmin->AddHeaders($arHeaders);

$arTypes = Loc::getMessage('AQAAL_LIST_TYPES');
while($arRecord = $dbResultList->NavNext(true, 'f_')) {
	$row = &$lAdmin->AddRow($f_ID, $arRecord);

	$row->AddViewField('ID', '<a href="api_qa_edit.php?ID=' . $f_ID . '&lang=' . $lang . GetFilterParams("filter_") . '">' . $f_ID . '</a>');
	$row->AddViewField('PARENT_ID', '<a href="api_qa_list.php?PID=' . $f_PARENT_ID . '&lang=' . $lang . GetFilterParams("filter_") . '">' . $f_PARENT_ID . '</a>');

	if($row->bEditMode) {
		$row->AddCheckField('ACTIVE');
	}
	else {
		$sHtml = ($f_ACTIVE == 'Y' ? '<i class="qa-active qa-green"></i>' : '<i class="qa-active qa-red"></i>');
		$row->AddViewField('ACTIVE', $sHtml);
	}


	$row->AddCheckField('NOTIFY');
	$row->AddInputField('IBLOCK_ID', $f_IBLOCK_ID);
	$row->AddInputField('ELEMENT_ID', $f_ELEMENT_ID);
	$row->AddInputField('XML_ID', $f_XML_ID);
	$row->AddInputField('CODE', $f_CODE);
	$row->AddInputField('VOTE_UP', $f_VOTE_UP);
	$row->AddInputField('VOTE_DO', $f_VOTE_DO);
	$row->AddInputField('GUEST_NAME', $f_GUEST_NAME);
	$row->AddInputField('GUEST_EMAIL', $f_GUEST_EMAIL);
	$row->AddViewField('TEXT', $f_TEXT);

	if($f_USER_ID)
		$row->AddField('USER_ID', Tools::getFormatedUserName($f_USER_ID));
	else
		$row->AddInputField('USER_ID', $f_USER_ID);

	if($f_ELEMENT_ID || $f_ELEMENT_ID) {
		$arElement = CIBlockElement::GetList(false, array('=ID' => $f_ELEMENT_ID), false, false, array('ID', 'IBLOCK_ID', 'NAME', 'IBLOCK_SECTION_ID', 'DETAIL_PAGE_URL'))->GetNext(false, false);
		$row->AddViewField('ELEMENT_ID', '<a target="_blank" href="' . $arElement['DETAIL_PAGE_URL'] . '">' . $f_ELEMENT_ID . '</a>');
	}


	$row->AddSelectField('TYPE', $arTypes, array());

	$actions = array();
	if($rights >= 'W') {
		$actions[] = array(
			 'ICON'    => 'edit',
			 'TEXT'    => Loc::getMessage('MAIN_ADMIN_MENU_EDIT'),
			 //'ACTION'  => $lAdmin->ActionDoGroup($f_ID, 'edit'),
			 'ACTION'  => $lAdmin->ActionRedirect('api_qa_edit.php?ID=' . $f_ID . '&lang=' . $lang),
			 'DEFAULT' => true,
		);
		$actions[] = array("SEPARATOR" => true);
		$actions[] = array(
			 'TEXT'   => Loc::getMessage('MAIN_ADMIN_LIST_ACTIVATE'),
			 'ACTION' => $lAdmin->actionDoGroup($f_ID, 'activate'),
		);
		$actions[] = array(
			 'TEXT'   => Loc::getMessage('MAIN_ADMIN_LIST_DEACTIVATE'),
			 'ACTION' => $lAdmin->actionDoGroup($f_ID, 'deactivate'),
		);
		$actions[] = array(
			 'ICON'   => 'delete',
			 'TEXT'   => Loc::getMessage('MAIN_ADMIN_MENU_DELETE'),
			 'ACTION' => "if(confirm('" . Loc::getMessage('AQAAL_DELETE_CONFIRM') . "')) " . $lAdmin->ActionDoGroup($f_ID, 'delete'),
		);
	}

	$row->AddActions($actions);
}

$lAdmin->AddFooter(
	 array(
			array(
				 'title' => Loc::getMessage('MAIN_ADMIN_LIST_SELECTED'),
				 'value' => $dbResultList->SelectedRowsCount(),
			),
			array(
				 'counter' => true,
				 'title'   => Loc::getMessage('MAIN_ADMIN_LIST_CHECKED'),
				 'value'   => '0',
			),
	 )
);


//Массовые операции
if($rights >= 'W') {
	$lAdmin->AddGroupActionTable(Array(
		//'delete'     => Loc::getMessage('MAIN_ADMIN_LIST_DELETE'),
		'activate'   => Loc::getMessage('MAIN_ADMIN_LIST_ACTIVATE'),
		'deactivate' => Loc::getMessage('MAIN_ADMIN_LIST_DEACTIVATE'),
		//'moderation' => Loc::getMessage('AQAAL_ADMIN_LIST_MODERATION'),
	));
}

//Кнопка Добавить
$lAdmin->AddAdminContextMenu(array(
	 array(
			'TEXT' => Loc::getMessage('MAIN_ADD'),
			'LINK' => 'api_qa_edit.php?lang=' . $lang,
			'ICON' => 'btn_new',
	 ),
));


$lAdmin->CheckListMode();

$APPLICATION->SetTitle(Loc::getMessage('AQAAL_PAGE_TITLE'));
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php');
?>
	<form name="find_form" method="GET" action="<?=$APPLICATION->GetCurPage()?>">
		<?
		$filterObject = new CAdminFilter(
			 $sTableID . "_filter",
			 $arFilterTitles
		);
		?>
		<? $filterObject->SetDefaultRows(array('find_ID_1','find_ID_2','find_ACTIVE')); ?>
		<? $filterObject->Begin(); ?>
		<? foreach($columns as $code => $fld): ?>
			<? if(!in_array($code, $arExcludeFilter)): ?>
				<tr>
					<td><?=htmlspecialcharsbx($fld->getTitle())?><? if($fld->getDataType() == 'integer'): ?> (<?=Loc::getMessage('AQAAL_FROM_AND_TO')?>)<? endif ?>:</td>
					<td>
						<? if($fld->getDataType() == 'integer'): ?>
							<input type="text" name="find_<?=$code?>_1" value="<?=htmlspecialcharsbx($GLOBALS[ 'find_' . $code . '_1' ])?>">
							...
							<input type="text" name="find_<?=$code?>_2" value="<?=htmlspecialcharsbx($GLOBALS[ 'find_' . $code . '_2' ])?>">
						<? elseif($fld->getDataType() == 'datetime'): ?>
							<?=CalendarPeriod("find_{$code}_1", htmlspecialcharsbx($GLOBALS[ 'find_' . $code . '_1' ]), "find_{$code}_2", htmlspecialcharsbx($GLOBALS[ 'find_' . $code . '_2' ]), "find_form", "Y")?>
						<? elseif($code == 'SITE_ID'): ?>
							<?=CLang::SelectBox('find_' . $code, htmlspecialcharsbx($GLOBALS[ 'find_' . $code ]), Loc::getMessage('MAIN_ALL'));?>
						<? elseif($code == 'ACTIVE'): ?>
							<select name="find_<?=$code?>">
								<option value="NOT_REF">(<?=Loc::getMessage('AQAAL_OPTION_ALL');?>)</option>
								<option value="Y" <?=($find_ACTIVE == 'Y' ? 'selected' : '');?>><?=Loc::getMessage('AQAAL_OPTION_YES');?></option>
								<option value="N" <?=($find_ACTIVE == 'N' ? 'selected' : '');?>><?=Loc::getMessage('AQAAL_OPTION_NO');?></option>
							</select>
						<? elseif($code == 'TYPE'): ?>
							<select name="find_<?=$code?>">
								<option value="NOT_REF">(<?=Loc::getMessage('AQAAL_OPTION_ALL');?>)</option>
								<option value="Q" <?=($find_TYPE == 'Q' ? 'selected' : '');?>>[Q] <?=Loc::getMessage('AQAAL_ITEM_QUESTION');?></option>
								<option value="A" <?=($find_TYPE == 'A' ? 'selected' : '');?>>[A] <?=Loc::getMessage('AQAAL_ITEM_ANSWER');?></option>
								<option value="C" <?=($find_TYPE == 'C' ? 'selected' : '');?>>[C] <?=Loc::getMessage('AQAAL_ITEM_COMMENTS');?></option>
							</select>
						<? else: ?>
							<input type="text" name="find_<?=$code?>" value="<?=htmlspecialcharsbx($GLOBALS[ 'find_' . $code ])?>">
						<? endif ?>
					</td>
				</tr>
			<? endif ?>
		<? endforeach ?>
		<?
		$filterObject->Buttons(array(
			 "table_id" => $sTableID,
			 "url"      => $APPLICATION->GetCurPageParam(),
			 "form"     => "find_form",
		));
		$filterObject->End();
		?>
	</form>
<?
//echo BeginNote();
//echo Loc::getMessage('AQAAL_NOTE');
//echo EndNote();

$ALL      = Api\QA\QuestionTable::getCount();
$QUESTION = Api\QA\QuestionTable::getCount(array('=TYPE' => 'Q'));
$ANSWER   = Api\QA\QuestionTable::getCount(array('=TYPE' => 'A'));
$COMMENTS = Api\QA\QuestionTable::getCount(array('=TYPE' => 'C'));


$curPage = $APPLICATION->GetCurPage() . '?lang=' . $lang;
?>
	<div class="api-qa">
		<div class="topbar">
			<div>
				<a href="<?=$curPage?>"><?=Loc::getMessage('AQAAL_ITEM_ALL')?></a> (<?=$ALL?>) |
			</div>
			<div>
				<a href="<?=$curPage?>&TYPE=Q" class="<?=$TYPE == 'Q' ? 'active' : ''?>"><?=Loc::getMessage('AQAAL_ITEM_QUESTION')?></a> (<?=$QUESTION?>) |
			</div>
			<div>
				<a href="<?=$curPage?>&TYPE=A" class="<?=$TYPE == 'A' ? 'active' : ''?>"><?=Loc::getMessage('AQAAL_ITEM_ANSWER')?></a> (<?=$ANSWER?>) |
			</div>
			<div>
				<a href="<?=$curPage?>&TYPE=C" class="<?=$TYPE == 'C' ? 'active' : ''?>"><?=Loc::getMessage('AQAAL_ITEM_COMMENTS')?></a> (<?=$COMMENTS?>)
			</div>
		</div>
		<?
		$lAdmin->DisplayList();
		?>
	</div>
<?
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php');
?>