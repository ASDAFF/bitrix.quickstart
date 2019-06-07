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
use Bitrix\Main\SiteTable;
use Bitrix\Main\Localization\Loc;
use Bitrix\Sale\Internals\StatusTable;
use Bitrix\Sale\Internals\StatusLangTable;
use Api\OrderStatus\SmsStatusTable;

define('ADMIN_MODULE_NAME', 'api.orderstatus');
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');
Loc::loadMessages(__FILE__);

global $USER, $APPLICATION;

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
foreach(SmsStatusTable::getMap() as $key => $value)
{
	$arFieldTitle[ $key ] = $value['title'];
}


$conn    = Application::getConnection();
$context = Application::getInstance()->getContext();
$request = $context->getRequest();
$lang    = $context->getLanguage();

$errorMsgs = null;


$ufEntityId = 'AOS_SMS_STATUS';
$sTableID   = SmsStatusTable::getTableName();
$oSort      = new CAdminSorting($sTableID, 'ID', 'asc');
$lAdmin     = new CAdminList($sTableID, $oSort);

$filterFields = array(
	"filter_status_id",
	"filter_site_id",
	"filter_active",
);

$lAdmin->InitFilter($filterFields);

$filter = array();
if($filter_status_id)
	$filter['=STATUS_ID'] = $filter_status_id;

if($filter_site_id && $filter_site_id != 'NOT_REF')
	$filter['?SITE_ID'] = $filter_site_id;

if($filter_active && $filter_active != 'NOT_REF')
	$filter['=ACTIVE'] = $filter_active;


if($lAdmin->EditAction())
{
	foreach($request->getPost('FIELDS') as $id => $arFields)
	{
		$error = false;
		$id    = intval($id);

		if($id <= 0 || !$lAdmin->IsUpdated($id))
			continue;

		$reqFields = array();
		if($reqFields)
		{
			foreach($reqFields as $reqField)
			{
				if(empty($arFields[ $reqField ]))
				{
					$error = true;
					$lAdmin->AddUpdateError('#' . $id . ' : ' . Loc::getMessage('AOS_SMS_STATUS_FIELD_ERROR', array('#FIELD#' => $arFieldTitle[ $reqField ])), $id);
				}
			}
		}

		if(!$error)
		{
			$arFields['DATE_MODIFY'] = new \Bitrix\Main\Type\DateTime();
			$arFields['MODIFIED_BY'] = $USER->GetID();

			$conn->startTransaction();
			$res = SmsStatusTable::update($id, $arFields);
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
		$dbResultList = SmsStatusTable::getList($params);

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

				$result = SmsStatusTable::delete($id);
				if(!$result->isSuccess())
				{
					if($error = $result->getErrorMessages())
						$lAdmin->AddGroupError(join("\n", $error), $id);
					else
						$lAdmin->AddGroupError(Loc::getMessage('AOS_SMS_STATUS_ERROR_DELETE'), $id);
				}
				break;

			case 'activate':
			case 'deactivate':

				$arFields['ACTIVE']      = ($_REQUEST['action'] == 'activate' ? 'Y' : 'N');
				$arFields['DATE_MODIFY'] = new \Bitrix\Main\Type\DateTime();
				$arFields['MODIFIED_BY'] = $USER->GetID();

				$result = SmsStatusTable::update($id, $arFields);
				if(!$result->isSuccess())
				{
					if($error = $result->getErrorMessages())
						$lAdmin->AddGroupError(join("\n", $error), $id);
					else
						$lAdmin->AddGroupError(Loc::getMessage('AOS_SMS_STATUS_ERROR_SAVE'), $id);
				}
				break;
		}
	}
}


$params = array(
	'select' => array('*'),
	'filter' => $filter,
	'order'  => array($by => $order),
);

$arTemplate   = SmsStatusTable::getList($params);
$dbResultList = new CAdminResult($arTemplate, $sTableID);
$dbResultList->NavStart();

$lAdmin->NavText($dbResultList->GetNavPrint(Loc::getMessage('AOS_SMS_STATUS_NAV_TITLE')));

$arHeaders = array(
	array(
		'id'      => 'ID',
		'content' => $arFieldTitle['ID'],
		'sort'    => 'ID',
		'default' => true,
	),
	array(
		'id'      => 'STATUS_ID',
		'content' => $arFieldTitle['STATUS_ID'],
		'sort'    => 'STATUS_ID',
		'default' => true,
	),
	array(
		'id'      => 'ACTIVE',
		'content' => $arFieldTitle['ACTIVE'],
		'sort'    => 'ACTIVE',
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
$lAdmin->AddHeaders($arHeaders);


//Все сайты
$arSiteMenu = array();
/*$rsSites = SiteTable::getList(array(
	'select' => array('LID', 'SITE_NAME'),
	'filter' => array('ACTIVE' => 'Y'),
));
while($arSite = $rsSites->fetch())
{
	$arSiteMenu[] = array(
		'ID'   => $arSite['LID'],
		'NAME' => $arSite['SITE_NAME'],
		'TEXT' => $arSite['SITE_NAME']." (". $arSite['LID'] .")",
		'ACTION' => "window.location = 'sale_order_create.php?lang=".$lang."&SITE_ID=".$arSite['LID']."';"
	);
}*/

$arStatusId = array();
$result = StatusTable::getList(array(
	'select' => array('ID'),
	'filter' => array('=TYPE' => 'O'),
));
while($status = $result->fetch())
	$arStatusId[] = $status['ID'];

$arStatus = array();
if($arStatusId)
{
	$result   = StatusLangTable::getList(array(
		'order'  => array('NAME' => 'ASC'),
		'filter' => array('=LID' => LANG, '=STATUS_ID' => $arStatusId),
	));
	while($row = $result->fetch())
		$arStatus[ $row['STATUS_ID'] ] = '[' . $row['STATUS_ID'] . '] ' . $row['NAME'];
}


while($arItem = $dbResultList->NavNext(true, 'f_'))
{
	//$row = &$lAdmin->AddRow($f_ID, $arItem, "api_orderstatus_sms_status_edit.php?ID=".$f_ID."&lang=".$lang, Loc::getMessage('SALE_COMPANY_EDIT_DESCR'));
	$row = &$lAdmin->AddRow($f_ID, $arItem);

	//$row->AddField("ID", "<a href=\"api_orderstatus_sms_status_edit.php?ID=".$f_ID."&lang=".$lang.GetFilterParams("filter_")."\">".$f_ID."</a>");

	$row->AddCheckField('ACTIVE');
	$row->AddInputField('SORT', array('size' => 4));

	/*if($row->bEditMode)
		$row->AddInputField('NAME', array('size' => 20));
	else
		$row->AddField('NAME', "<a href=\"api_orderstatus_sms_status_edit.php?ID=" . $f_ID . "&lang=" . $lang . GetFilterParams("filter_") . "\">" . $f_NAME . "</a>");
	*/

	$statusName = ($arItem['STATUS_ID'] && $arStatus[ $arItem['STATUS_ID'] ] ? $arStatus[ $arItem['STATUS_ID'] ] : $arItem['STATUS_ID']) ;
	$row->AddField('STATUS_ID', "<a href=\"api_orderstatus_sms_status_edit.php?ID=" . $f_ID . "&lang=" . $lang . GetFilterParams("filter_") . "\">" . $statusName . "</a>");

	$row->AddField('MODIFIED_BY', GetFormatedUserName($f_MODIFIED_BY, false, true));
	$row->AddField('DATE_MODIFY', $f_DATE_MODIFY);



	$arActions = array(
		array(
			'ICON'    => 'edit',
			'TEXT'    => Loc::getMessage('MAIN_ADMIN_MENU_EDIT'),
			'ACTION'  => $lAdmin->ActionRedirect('api_orderstatus_sms_status_edit.php?ID=' . $f_ID . '&lang=' . $lang),
			'DEFAULT' => true,
		),
		array(
			'ICON'   => 'copy',
			'TEXT'   => Loc::getMessage('MAIN_ADMIN_MENU_COPY'),
			'ACTION' => $lAdmin->ActionRedirect('api_orderstatus_sms_status_edit.php?ID=' . $f_ID . '&action=copy&lang=' . $lang),
		),
		array("SEPARATOR" => true),
		array(
			'ICON'   => 'delete',
			'TEXT'   => Loc::getMessage('MAIN_ADMIN_MENU_DELETE'),
			'ACTION' => "if(confirm('" . Loc::getMessage('CONFIRM_DELETE') . "')) " . $lAdmin->ActionDoGroup($f_ID, 'delete'),
		),
	);

	$row->AddActions($arActions);
}


$lAdmin->AddFooter(array(
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
	'activate'   => Loc::getMessage('MAIN_ADMIN_LIST_ACTIVATE'),
	'deactivate' => Loc::getMessage('MAIN_ADMIN_LIST_DEACTIVATE'),
));


//Кнопка Добавить
$lAdmin->AddAdminContextMenu(array(
	array(
		'TEXT'  => Loc::getMessage('MAIN_ADD'),
		'TITLE' => Loc::getMessage('MAIN_ADD'),
		'LINK'  => 'api_orderstatus_sms_status_edit.php?lang=' . $lang,
		'ICON'  => 'btn_new',
		"MENU"  => $arSiteMenu,
	),
));


$lAdmin->CheckListMode();

$APPLICATION->SetTitle(Loc::getMessage('AOS_SMS_STATUS_PAGE_TITLE'));
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php');
?>

	<form name="find_form" method="GET" action="<?=$APPLICATION->GetCurPage()?>?">
		<?
		$arFindFields = array(
			$arFieldTitle['STATUS_ID'],
			$arFieldTitle['SITE_ID'],
			$arFieldTitle['ACTIVE'],
		);
		$oFilter = new CAdminFilter(
			$sTableID . "_filter",
			$arFindFields
		);

		$oFilter->Begin();
		?>
		<tr>
			<td><?=$arFieldTitle['STATUS_ID'];?>:</td>
			<td>
				<select name="filter_status_id">
					<option value=""><?=GetMessage('MAIN_ALL')?></option>
					<?if($arStatus):?>
						<?foreach($arStatus as $key=>$status):?>
							<option value="<?=$key?>"><?=$status?></option>
						<?endforeach;?>
					<?endif?>
				</select>
				<?//=SelectBoxMFromArray('STATUS_ID',$arStatus,$filter_status_id);?>
			</td>
		</tr>
		<tr>
			<td><?=$arFieldTitle['SITE_ID'];?>:</td>
			<td><?=CLang::SelectBox('filter_site_id', htmlspecialcharsbx($filter_site_id), GetMessage('MAIN_ALL')); ?></td>
		</tr>
		<tr>
			<td><?=$arFieldTitle['ACTIVE']?>:</td>
			<td>
				<select name="filter_active">
					<option value="NOT_REF">(<?=Loc::getMessage('AOS_SMS_STATUS_OPTION_ALL');?>)</option>
					<option value="Y"<? if($filter_active == 'Y')
						echo " selected" ?>><?=Loc::getMessage('AOS_SMS_STATUS_OPTION_YES');?></option>
					<option value="N"<? if($filter_active == 'N')
						echo " selected" ?>><?=Loc::getMessage('AOS_SMS_STATUS_OPTION_NO');?></option>
				</select>
			</td>
		</tr>
		<?
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