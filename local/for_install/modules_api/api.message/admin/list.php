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
use \Bitrix\Main\SiteTable;
use \Bitrix\Main\Localization\Loc;


define("ADMIN_MODULE_NAME", "api.message");
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');
Loc::loadMessages(__FILE__);

global $USER, $APPLICATION, $USER_FIELD_MANAGER;

$ASM_RIGHT = $APPLICATION->GetGroupRight(ADMIN_MODULE_NAME);
if($ASM_RIGHT == 'D')
	$APPLICATION->AuthForm(Loc::getMessage('ACCESS_DENIED'));

if(!Loader::includeModule(ADMIN_MODULE_NAME))
	$APPLICATION->AuthForm(Loc::getMessage('ACCESS_DENIED'));


use \Api\Message\MessageTable;
use \Api\Message\ConfigTable;

//Лэнги полей
$arFieldTitle = array();
foreach(MessageTable::getMap() as $key => $value)
{
	$arFieldTitle[ $key ] = $value['title'];
}


$conn    = Application::getConnection();
$context = Application::getInstance()->getContext();
$request = $context->getRequest();
$lang    = $context->getLanguage();

$errorMsgs = null;


$ufEntityId = 'ASM_MESSAGE';
$sTableID   = MessageTable::getTableName();
$oSort      = new CAdminSorting($sTableID, 'ID', 'asc');
$lAdmin     = new CAdminList($sTableID, $oSort);

$filterFields = array(
	"filter_name",
	"filter_active",
	"filter_site_id",
	"filter_type",
);
$USER_FIELD_MANAGER->AdminListAddFilterFields($ufEntityId, $filterFields);

$lAdmin->InitFilter($filterFields);

$filter = array();
if($filter_name)
	$filter['?NAME'] = $filter_name;

if($filter_active && $filter_active != 'NOT_REF')
	$filter['=ACTIVE'] = $filter_active;

if($filter_site_id && $filter_site_id != 'NOT_REF')
	$filter['?SITE_ID'] = $filter_site_id;

if($filter_type && $filter_type[0] != 'NOT_REF')
	$filter['=TYPE'] = $filter_type;


$USER_FIELD_MANAGER->AdminListAddFilter($ufEntityId, $filter);

if($ASM_RIGHT=='W' && $lAdmin->EditAction())
{
	foreach($request->getPost('FIELDS') as $id => $arFields)
	{
		$error = false;
		$id    = intval($id);

		if($id <= 0 || !$lAdmin->IsUpdated($id))
			continue;

		$reqFields = array('NAME');
		foreach($reqFields as $reqField)
		{
			if(empty($arFields[ $reqField ]))
			{
				$error = true;
				$lAdmin->AddUpdateError('#' . $id . ' : ' . Loc::getMessage('ASM_LIST_FIELD_ERROR', array('#FIELD#' => $arFieldTitle[ $reqField ])), $id);
			}
		}

		if(!$error)
		{
			$arFields['NAME'] = trim($arFields['NAME']);
			$arFields['ACTIVE'] = ($request->get('action') == 'activate' ? 'Y' : 'N');

			if($arFields['ACTIVE_FROM'])
				$arFields['ACTIVE_FROM'] = new \Bitrix\Main\Type\DateTime($arFields['ACTIVE_FROM']);

			if($arFields['ACTIVE_TO'])
				$arFields['ACTIVE_TO'] = new \Bitrix\Main\Type\DateTime($arFields['ACTIVE_TO']);

			$arFields['TIMESTAMP_X'] = new \Bitrix\Main\Type\DateTime();
			$arFields['MODIFIED_BY'] = $USER->GetID();

			$conn->startTransaction();
			$res = MessageTable::update($id, $arFields);
			if(!$res->isSuccess())
			{
				$conn->rollbackTransaction();
				$lAdmin->AddUpdateError(join("\n", $res->getErrorMessages()), $id);
				continue;
			}
			$conn->commitTransaction();
		}
	}

	//Clear cache
	ConfigTable::clearCache();
}

if($ASM_RIGHT=='W' && $ids = $lAdmin->GroupAction())
{
	if($_REQUEST['action_target'] == 'selected')
	{
		$ids          = array();
		$params       = array(
			'select' => array('ID'),
			'filter' => $filter,
		);
		$dbResultList = MessageTable::getList($params);

		while($result = $dbResultList->fetch())
			$ids[] = $result['ID'];
	}

	foreach($ids as $id)
	{
		if(empty($id))
			continue;

		switch($_REQUEST['action'])
		{
			case "delete":
				@set_time_limit(0);

				$result = MessageTable::delete($id);
				if(!$result->isSuccess())
				{
					if($error = $result->getErrorMessages())
						$lAdmin->AddGroupError(join("\n", $error), $id);
					else
						$lAdmin->AddGroupError(Loc::getMessage('ASM_LIST_ERROR_DELETE'), $id);
				}
				break;

			case 'activate':
			case 'deactivate':

				$arFields['ACTIVE'] = ($request->get('action') == 'activate' ? 'Y' : 'N');
				$arFields['TIMESTAMP_X'] = new \Bitrix\Main\Type\DateTime();
				$arFields['MODIFIED_BY'] = $USER->GetID();

				$result = MessageTable::update($id, $arFields);
				if(!$result->isSuccess())
				{
					if($error = $result->getErrorMessages())
						$lAdmin->AddGroupError(join("\n", $error), $id);
					else
						$lAdmin->AddGroupError(Loc::getMessage('ASM_LIST_ERROR_SAVE'), $id);
				}
				break;
		}
	}

	//Clear cache
	ConfigTable::clearCache();
}



$userFields = $USER_FIELD_MANAGER->GetUserFields($ufEntityId);
$select     = array('*');
foreach($userFields as $field)
	$select[] = $field['FIELD_NAME'];

$params = array(
	'select' => $select,
	'filter' => $filter,
	'order'  => array($by => $order),
);


$arMesages    = MessageTable::getList($params);
$dbResultList = new CAdminResult($arMesages, $sTableID);
$dbResultList->NavStart();

$lAdmin->NavText($dbResultList->GetNavPrint(Loc::getMessage('ASM_LIST_NAV_TITLE')));

$arHeaders = array(
	array(
		'id'      => 'ID',
		'content' => $arFieldTitle['ID'],
		'sort'    => 'ID',
		'default' => true,
	),
	array(
		 'id'      => 'NAME',
		 'content' => $arFieldTitle['NAME'],
		 'sort'    => 'NAME',
		 'default' => true,
	),
	array(
		'id'      => 'ACTIVE',
		'content' => $arFieldTitle['ACTIVE'],
		'sort'    => 'ACTIVE',
		'default' => true,
	),
	array(
		'id'      => 'ACTIVE_FROM',
		'content' => $arFieldTitle['ACTIVE_FROM'],
		'sort'    => 'ACTIVE_FROM',
		'default' => true,
	),
	array(
		'id'      => 'ACTIVE_TO',
		'content' => $arFieldTitle['ACTIVE_TO'],
		'sort'    => 'ACTIVE_TO',
		'default' => true,
	),
	array(
		'id'      => 'SORT',
		'content' => $arFieldTitle['SORT'],
		'sort'    => 'SORT',
		'default' => true,
	),
	array(
		'id'      => 'SITE_ID',
		'content' => $arFieldTitle['SITE_ID'],
		'sort'    => 'SITE_ID',
		'default' => true,
	),
	array(
		'id'      => 'TYPE',
		'content' => $arFieldTitle['TYPE'],
		'sort'    => 'TYPE',
		'default' => true,
	),
	array(
		'id'      => 'MESSAGE',
		'content' => $arFieldTitle['MESSAGE'],
		'sort'    => '',
		'default' => true,
	),
	array(
		'id'      => 'TIMESTAMP_X',
		'content' => $arFieldTitle['TIMESTAMP_X'],
		'sort'    => 'TIMESTAMP_X',
		'default' => true,
	),
	array(
		'id'      => 'MODIFIED_BY',
		'content' => $arFieldTitle['MODIFIED_BY'],
		'sort'    => 'MODIFIED_BY',
		'default' => true,
	),
);
$USER_FIELD_MANAGER->AdminListAddHeaders($ufEntityId, $headers);
$lAdmin->AddHeaders($arHeaders);

$arTypes = Loc::getMessage('ASM_LIST_TYPES');
while($arRecord = $dbResultList->NavNext(true, 'f_'))
{
	//$row = &$lAdmin->AddRow($f_ID, $arRecord, "api_message_edit.php?ID=".$f_ID."&lang=".$lang, Loc::getMessage('SALE_COMPANY_EDIT_DESCR'));
	$row = &$lAdmin->AddRow($f_ID, $arRecord);

	$row->AddCheckField('ACTIVE');
	$row->AddCalendarField('ACTIVE_FROM', $F_ACTIVE_FROM, true);
	$row->AddCalendarField('ACTIVE_TO', $F_ACTIVE_TO, true);
	$row->AddInputField('SORT', array('size' => 4));

	if($row->bEditMode)
		$row->AddInputField('NAME', array('size' => 20));
	else
		$row->AddField('NAME', "<a href=\"api_message_edit.php?ID=" . $f_ID . "&lang=" . $lang . GetFilterParams("filter_") . "\">" . $f_NAME . "</a>");


	$row->AddSelectField('TYPE', $arTypes, array());

	if($row->bEditMode && $f_MESSAGE_TYPE == 'text')
		$row->AddInputField('MESSAGE', array('size' => 80));
	else
		$row->AddField('MESSAGE', $f_MESSAGE);

	$row->AddField('TIMESTAMP_X', $f_TIMESTAMP_X);
	$row->AddField('MODIFIED_BY', CApiMessage::getFormatedUserName($f_MODIFIED_BY, false));

	$USER_FIELD_MANAGER->AddUserFields($ufEntityId, $arRecord, $row);


	$arActions = array();
	$arActions[] = array(
		'ICON'    => 'edit',
		'TEXT'    => Loc::getMessage('MAIN_ADMIN_MENU_EDIT'),
		'ACTION'  => $lAdmin->ActionRedirect('api_message_edit.php?ID=' . $f_ID . '&lang=' . $lang),
		'DEFAULT' => true,
	);

	if($ASM_RIGHT == 'W')
	{
		$arActions[] = array(
			'ICON'   => 'copy',
			'TEXT'   => Loc::getMessage('MAIN_ADMIN_MENU_COPY'),
			'ACTION' => $lAdmin->ActionRedirect('api_message_edit.php?ID=' . $f_ID . '&action=copy&lang=' . $lang),
		);
		$arActions[] = array("SEPARATOR" => true);
		$arActions[] = array(
			'ICON'   => 'delete',
			'TEXT'   => Loc::getMessage('MAIN_ADMIN_MENU_DELETE'),
			'ACTION' => "if(confirm('" . Loc::getMessage('ASM_LIST_DELETE_CONFIRM') . "')) " . $lAdmin->ActionDoGroup($f_ID, 'delete'),
		);
	}

	$row->AddActions($arActions);
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
if($ASM_RIGHT == 'W')
{
	$lAdmin->AddGroupActionTable(Array(
		'delete' => Loc::getMessage('MAIN_ADMIN_LIST_DELETE'),
		'activate'   => Loc::getMessage('MAIN_ADMIN_LIST_ACTIVATE'),
		'deactivate' => Loc::getMessage('MAIN_ADMIN_LIST_DEACTIVATE'),
	));
}


//Кнопка Добавить
$lAdmin->AddAdminContextMenu(array(
	array(
		'TEXT' => Loc::getMessage('MAIN_ADD'),
		'LINK' => 'api_message_edit.php?lang=' . $lang,
		'ICON' => 'btn_new',
	),
));


$lAdmin->CheckListMode();

$APPLICATION->SetTitle(Loc::getMessage('ASM_LIST_PAGE_TITLE'));
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php');
?>

	<form name="find_form" method="GET" action="<?=$APPLICATION->GetCurPage()?>?">
		<?
		$arFindFields = array(
			$arFieldTitle['NAME'],
			$arFieldTitle['ACTIVE'],
			$arFieldTitle['SITE_ID'],
			$arFieldTitle['TYPE'],
		);
		$USER_FIELD_MANAGER->AddFindFields($ufEntityId, $arFindFields);
		$oFilter = new CAdminFilter(
			$sTableID . "_filter",
			$arFindFields
		);

		$oFilter->Begin();
		?>
		<tr>
			<td><?=$arFieldTitle['NAME'];?>:</td>
			<td>
				<input type="text" name="filter_name" value="<?=htmlspecialcharsbx($filter_name)?>"/>
			</td>
		</tr>
		<tr>
			<td><?=$arFieldTitle['ACTIVE']?>:</td>
			<td>
				<select name="filter_active">
					<option value="NOT_REF">(<?=Loc::getMessage('ASM_LIST_OPTION_ALL');?>)</option>
					<option value="Y"<? if($filter_active == 'Y')
						echo " selected" ?>><?=Loc::getMessage('ASM_LIST_OPTION_YES');?></option>
					<option value="N"<? if($filter_active == 'N')
						echo " selected" ?>><?=Loc::getMessage('ASM_LIST_OPTION_NO');?></option>
				</select>
			</td>
		</tr>

		<tr>
			<td><?=$arFieldTitle['SITE_ID'];?>:</td>
			<td><?=CLang::SelectBox('filter_site_id', htmlspecialcharsbx($filter_site_id), Loc::getMessage('MAIN_ALL')); ?></td>
		</tr>
		<tr>
			<td><?=$arFieldTitle['TYPE'];?>:</td>
			<td>
				<?
				$arType = Loc::getMessage('ASM_LIST_TYPES');
				echo SelectBoxMFromArray(
					'filter_type[]',
					array(
						'reference'    => array_values($arType),
						'reference_id' => array_keys($arType),
					),
					$filter_type,
					Loc::getMessage('MAIN_ALL'),
					false,
					(count($arType) <= 5 ? count($arType) : 5)
				);
				?>
			</td>
		</tr>
		<?
		$USER_FIELD_MANAGER->AdminListShowFilter($ufEntityId);
		$oFilter->Buttons(
			array(
				"table_id" => $sTableID,
				"url"      => $APPLICATION->GetCurPage(),
				"form"     => "find_form",
			)
		);
		$oFilter->End();
		?>
	</form>
<?

$lAdmin->DisplayList();

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php');

?>