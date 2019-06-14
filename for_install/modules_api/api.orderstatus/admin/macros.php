<?
/**
 * Bitrix vars
 *
 * @var CUser $USER
 * @var CMain $APPLICATION
 *
 */
use Bitrix\Main\Loader;
use Bitrix\Main\Application;
//use Bitrix\Main\SiteTable;
use Bitrix\Main\Localization\Loc;
use Api\OrderStatus\MacrosTable;

define('ADMIN_MODULE_NAME', 'api.orderstatus');
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');
Loc::loadMessages(__FILE__);

global $USER, $APPLICATION, $USER_FIELD_MANAGER;

$MODULE_SALE_RIGHT = $APPLICATION->GetGroupRight('sale');
if($MODULE_SALE_RIGHT <= 'D')
{
	$APPLICATION->AuthForm(Loc::getMessage('ACCESS_DENIED'));
}

if(!Loader::includeModule(ADMIN_MODULE_NAME))
	$APPLICATION->AuthForm(Loc::getMessage('ACCESS_DENIED'));

if(!Loader::includeModule('sale'))
	$APPLICATION->AuthForm(Loc::getMessage('ACCESS_DENIED'));



//Лэнги полей
$arFieldTitle = array();
foreach(MacrosTable::getMap() as $key => $value)
{
	$arFieldTitle[$key] = $value['title'];
}


$conn    = Application::getConnection();
$context = Application::getInstance()->getContext();
$request = $context->getRequest();
$lang    = $context->getLanguage();

$errorMsgs = null;


$ufEntityId = 'AOS_MACROS';
$sTableID   = MacrosTable::getTableName();
$oSort      = new CAdminSorting($sTableID, 'ID', 'asc');
$lAdmin     = new CAdminList($sTableID, $oSort);

$filterFields = array(
	"filter_name",
);
$USER_FIELD_MANAGER->AdminListAddFilterFields($ufEntityId, $filterFields);

$lAdmin->InitFilter($filterFields);

$filter = array();
if($filter_name)
	$filter['?NAME'] = $filter_name;

$USER_FIELD_MANAGER->AdminListAddFilter($ufEntityId, $filter);

if($lAdmin->EditAction())
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
				$lAdmin->AddUpdateError('#' . $id . ' : ' . Loc::getMessage('AOS_MACROS_FIELD_ERROR',array('#FIELD#' => $arFieldTitle[$reqField])), $id);
			}
		}

		if(!$error)
		{
			$arFields['NAME']        = trim($arFields['NAME']);
			$arFields['DATE_MODIFY'] = new \Bitrix\Main\Type\DateTime();
			$arFields['MODIFIED_BY'] = $USER->GetID();

			$conn->startTransaction();
			$res = MacrosTable::update($id, $arFields);
			if(!$res->isSuccess())
			{
				$conn->rollbackTransaction();
				$lAdmin->AddUpdateError(join("\n", $res->getErrorMessages()), $id);
				continue;
			}
			$conn->commitTransaction();
		}
	}
}

if($ids = $lAdmin->GroupAction())
{
	if($_REQUEST['action_target'] == 'selected')
	{
		$ids          = array();
		$params       = array(
			'select' => array('ID'),
			'filter' => $filter,
		);
		$dbResultList = MacrosTable::getList($params);

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

				$result = MacrosTable::delete($id);
				if(!$result->isSuccess())
				{
					if($error = $result->getErrorMessages())
						$lAdmin->AddGroupError(join("\n", $error), $id);
					else
						$lAdmin->AddGroupError(Loc::getMessage('AOS_MACROS_ERROR_DELETE'), $id);
				}
				break;

			case 'activate':
			case 'deactivate':

				//$arFields['ACTIVE'] = ($_REQUEST['action'] == 'activate' ? 'Y' : 'N');
				$arFields['DATE_MODIFY'] = new \Bitrix\Main\Type\DateTime();
				$arFields['MODIFIED_BY'] = $USER->GetID();

				$result = MacrosTable::update($id, $arFields);
				if(!$result->isSuccess())
				{
					if($error = $result->getErrorMessages())
						$lAdmin->AddGroupError(join("\n", $error), $id);
					else
						$lAdmin->AddGroupError(Loc::getMessage('AOS_MACROS_ERROR_SAVE'), $id);
				}
				break;
		}
	}
}


$userFields = $USER_FIELD_MANAGER->GetUserFields($ufEntityId);
$select = array('*');
foreach($userFields as $field)
	$select[] = $field['FIELD_NAME'];

$params = array(
	'select' => $select,
	'filter' => $filter,
	'order'  => array($by => $order),
);

$arMacros      = MacrosTable::getList($params);
$dbResultList = new CAdminResult($arMacros, $sTableID);
$dbResultList->NavStart();

$lAdmin->NavText($dbResultList->GetNavPrint(Loc::getMessage('AOS_MACROS_NAV_TITLE')));

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
		'id'      => 'VALUE',
		'content' => $arFieldTitle['VALUE'],
		'sort'    => '',
		'default' => true,
	),
	array(
		'id'      => 'DATE_MODIFY',
		'content' => $arFieldTitle['DATE_MODIFY'],
		'sort'    => 'DATE_MODIFY',
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

while($arMacros = $dbResultList->NavNext(true, 'f_'))
{
	//$row = &$lAdmin->AddRow($f_ID, $arMacros, "api_orderstatus_macros_edit.php?ID=".$f_ID."&lang=".$lang, Loc::getMessage('SALE_COMPANY_EDIT_DESCR'));
	$row = &$lAdmin->AddRow($f_ID, $arMacros);

	$row->AddCheckField('ACTIVE');

	if($row->bEditMode)
		$row->AddInputField('NAME', array('size' => 20));
	else
		$row->AddField('NAME', "<a href=\"api_orderstatus_macros_edit.php?ID=" . $f_ID . "&lang=" . $lang . GetFilterParams("filter_") . "\">" . $f_NAME . "</a>");

	if($row->bEditMode && $arMacros['VALUE_TYPE'] == 'text')
		$row->AddInputField('VALUE', array('size' => 80));
	else
		$row->AddField('VALUE', $arMacros['VALUE']);

	$row->AddField('MODIFIED_BY', GetFormatedUserName($f_MODIFIED_BY, false, true));
	$row->AddField('DATE_MODIFY', $f_DATE_MODIFY);

	$USER_FIELD_MANAGER->AddUserFields($ufEntityId, $arMacros, $row);

	$arActions = array(
		array(
			'ICON'    => 'edit',
			'TEXT'    => Loc::getMessage('MAIN_ADMIN_MENU_EDIT'),
			'ACTION'  => $lAdmin->ActionRedirect('api_orderstatus_macros_edit.php?ID=' . $f_ID . '&lang=' . $lang),
			'DEFAULT' => true,
		),
		array(
			'ICON'   => 'copy',
			'TEXT'   => Loc::getMessage('MAIN_ADMIN_MENU_COPY'),
			'ACTION' => $lAdmin->ActionRedirect('api_orderstatus_macros_edit.php?ID=' . $f_ID . '&action=copy&lang=' . $lang),
		),
		array("SEPARATOR" => true),
		array(
			'ICON'   => 'delete',
			'TEXT'   => Loc::getMessage('MAIN_ADMIN_MENU_DELETE'),
			'ACTION' => "if(confirm('" . Loc::getMessage('AOS_MACROS_DELETE_CONFIRM') . "')) " . $lAdmin->ActionDoGroup($f_ID, 'delete'),
		),
	);

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
$lAdmin->AddGroupActionTable(Array(
	'delete'     => Loc::getMessage('MAIN_ADMIN_LIST_DELETE'),
	//'activate'   => Loc::getMessage('MAIN_ADMIN_LIST_ACTIVATE'),
	//'deactivate' => Loc::getMessage('MAIN_ADMIN_LIST_DEACTIVATE'),
));


//Кнопка Добавить
$lAdmin->AddAdminContextMenu(array(
	array(
		'TEXT' => Loc::getMessage('MAIN_ADD'),
		'LINK' => 'api_orderstatus_macros_edit.php?lang=' . $lang,
		'ICON' => 'btn_new',
	),
));


$lAdmin->CheckListMode();

$APPLICATION->SetTitle(Loc::getMessage('AOS_MACROS_PAGE_TITLE'));
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php');
?>

	<form name="find_form" method="GET" action="<?=$APPLICATION->GetCurPage() ?>?">
		<?
		$arFindFields = array(
			$arFieldTitle['NAME'],
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
				<input type="text" name="filter_name" value="<?=htmlspecialcharsbx($filter_name) ?>"/>
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