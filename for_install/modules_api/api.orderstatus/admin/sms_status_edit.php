<?
/**
 * Bitrix vars
 *
 * @var CUser $USER
 * @var CMain $APPLICATION
 *
 */
use Bitrix\Main\Loader;
use Bitrix\Main\SiteTable;
use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use Bitrix\Sale\Internals\StatusTable;
use Bitrix\Sale\Internals\StatusLangTable;
use Api\OrderStatus\SmsStatusTable;

define("ADMIN_MODULE_NAME", "api.orderstatus");
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
foreach(SmsStatusTable::getMap() as $key => $value)
{
	$arFieldTitle[ $key ] = $value['title'];
}

//$statusFields = StatusTable::getEntity()->getFields();
//$statusLangFields = StatusLangTable::getEntity()->getFields();
//$statusFields['ID']->getTitle()

/*
 * @var \Bitrix\Main\Entity\Field $field
foreach(SmsStatusTable::getMap() as $key=>$field)
{
	$data = array($field->getName(),$field->getDataType());
}*/


$context      = Application::getInstance()->getContext();
$documentRoot = Application::getDocumentRoot();
$lang         = $context->getLanguage();
$request      = $context->getRequest();
$id           = intval($request->get('ID'));
$bCopy        = ($action == "copy");
$company      = array();
$errorMessage = '';


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



$arSite = array();
$result = SiteTable::getList(array(
	'filter' => array('=ACTIVE' => 'Y'),
));
while($row = $result->fetch())
	$arSite[ $row['LID'] ] = '[' . $row['LID'] . '] ' . $row['NAME'];



if($request->isPost() && $request->getPost('update') && check_bitrix_sessid())
{
	$site_id   = $request->getPost('SITE_ID');
	$status_id = $request->getPost('STATUS_ID');

	if(empty($site_id))
		$errorMessage .= Loc::getMessage('AOS_SMS_STATUS_EDIT_FIELD_ERROR', array('#FIELD#' => $arFieldTitle['SITE_ID'])) . "\n";

	if(empty($status_id))
		$errorMessage .= Loc::getMessage('AOS_SMS_STATUS_EDIT_FIELD_ERROR', array('#FIELD#' => $arFieldTitle['STATUS_ID'])) . "\n";


	if(empty($errorMessage))
	{
		$fields = array(
			'ACTIVE'           => ($request->getPost('ACTIVE') == 'Y' ) ? 'Y' : 'N',
			'SORT'             => intval($request->getPost('SORT')),
			'SITE_ID'          => join(',',$site_id),
			'STATUS_ID'        => $status_id,
			'DESCRIPTION'      => $request->getPost('DESCRIPTION'),
			'DATE_MODIFY'      => new \Bitrix\Main\Type\DateTime(),
			'MODIFIED_BY'      => intval($USER->GetID()),
		);

		$result = null;
		if($id && !$bCopy)
			$result = SmsStatusTable::update($id, $fields);
		else
			$result = SmsStatusTable::add($fields);

		if($result && $result->isSuccess())
		{
			$id = $result->getId();
			if(strlen($request->getPost("apply")) == 0)
				LocalRedirect("/bitrix/admin/api_orderstatus_sms_status.php?lang=" . $lang . "&" . GetFilterParams("filter_", false));
			else
				LocalRedirect("/bitrix/admin/api_orderstatus_sms_status_edit.php?lang=" . $lang . "&ID=" . $id . "&" . GetFilterParams("filter_", false));
		}
		else
		{
			$errorMessage .= join("\n", $result->getErrorMessages());
		}
	}

	unset($fields);
}


require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php');

//START VIEW

if($errorMessage)
	CAdminMessage::ShowMessage($errorMessage);


if($id > 0)
{
	$params  = array(
		'select' => array('*'),
		'filter' => array('=ID' => $id),
	);
	$company = SmsStatusTable::getList($params)->fetch();

	$APPLICATION->SetTitle($arStatus[$company['STATUS_ID']]);
}
else
{
	$APPLICATION->SetTitle(Loc::getMessage("AOS_SMS_STATUS_EDIT_PAGE_TITLE"));
}



//Кнопки = Добавить/Копировать/Удалить
$aMenu = array(
	array(
		"TEXT" => Loc::getMessage('MAIN_ADMIN_MENU_LIST'),
		"LINK" => "api_orderstatus_sms_status.php?lang=" . $lang,
		"ICON" => "btn_list",
	),
);
if($id && !$bCopy)
{
	$aMenu[] = array("SEPARATOR" => "Y");
	$aMenu[] = array(
		"TEXT" => Loc::getMessage('MAIN_ADMIN_MENU_ADD'),
		"LINK" => "api_orderstatus_sms_status_edit.php?lang=" . $lang,
		"ICON" => "btn_new",
	);
	$aMenu[] = array(
		"TEXT" => Loc::getMessage('MAIN_ADMIN_MENU_COPY'),
		"LINK" => "api_orderstatus_sms_status_edit.php?ID=" . $id . "&amp;action=copy&amp;lang=" . $lang,
		"ICON" => "btn_copy",
	);
	$aMenu[] = array(
		"TEXT" => Loc::getMessage('MAIN_ADMIN_MENU_DELETE'),
		"LINK" => "javascript:if(confirm('" . Loc::getMessage('AOS_SMS_STATUS_EDIT_ADMIN_MENU_DELETE_CONFIRM') . "'))window.location='api_orderstatus_sms_status.php?ID=" . $id . "&action=delete&lang=" . $lang . "&" . bitrix_sessid_get() . "';",
		"ICON" => "btn_delete",
	);
}
$context = new CAdminContextMenu($aMenu);
$context->Show();


$aTabs      = array(
	array(
		"DIV"   => "edit1",
		"TAB"   => Loc::getMessage('AOS_SMS_STATUS_EDIT_TAB_NAME'),
		"ICON"  => "",
		"TITLE" => Loc::getMessage('AOS_SMS_STATUS_EDIT_TAB_TITLE'),
	),
);
$tabControl = new CAdminForm("template_edit", $aTabs);


$tabControl->BeginPrologContent();

echo BeginNote();
echo Loc::getMessage('AOS_SMS_STATUS_EDIT_NOTE_DESC_RESTRICTION');
echo EndNote();

$tabControl->EndPrologContent();


$tabControl->BeginEpilogContent();
echo bitrix_sessid_post();
?>
	<input type="hidden" name="update" value="Y">
	<input type="hidden" name="lang" value="<?=$lang;?>">
<? if(!$bCopy): ?>
	<input type="hidden" name="ID" value="<?=$id;?>">
<? endif ?>
<?
$tabControl->EndEpilogContent();

//заголовки закладок
$tabControl->Begin(array("FORM_ACTION" => $APPLICATION->GetCurPage() . "?lang=" . $lang));


//*********************************************************
//                   первая закладка
//*********************************************************
$tabControl->BeginNextFormTab();


$fields = ($request->isPost()) ? $_POST : $company;


$tabControl->AddViewField('ID', $arFieldTitle['ID'] . ':', $company['ID']);

$tabControl->AddCheckBoxField('ACTIVE', $arFieldTitle['ACTIVE'], false, 'Y', $fields['ACTIVE'] != 'N');

if($company['DATE_MODIFY'])
	$tabControl->AddViewField('DATE_MODIFY', $arFieldTitle['DATE_MODIFY'] . ':', $company['DATE_MODIFY']);

if($company['MODIFIED_BY'])
	$tabControl->AddViewField('MODIFIED_BY', $arFieldTitle['MODIFIED_BY'] . ':', GetFormatedUserName($company['MODIFIED_BY'], false, true));

$tabControl->AddEditField('SORT', $arFieldTitle['SORT'], false, array('size' => 5), (isset($fields['SORT']) ? $fields['SORT'] : 500));

$tabControl->BeginCustomField('SITE_ID', $arFieldTitle['SITE_ID'], true);
?>
	<tr>
		<td><?=$tabControl->GetCustomLabelHTML()?></td>
		<td>
			<?=CSite::SelectBoxMulti('SITE_ID', explode(',',$fields['SITE_ID']));?>
		</td>
	</tr>
<?
$tabControl->EndCustomField('SITE_ID');

/*
$tabControl->AddDropDownField(
	'SITE_ID[]',
	$arFieldTitle['SITE_ID'],
	true,
	$arSite,
	false,
	array('multiple="multiple" size="'. count($arSite) .'"')
);*/

$tabControl->AddDropDownField('STATUS_ID', $arFieldTitle['STATUS_ID'], true, $arStatus, $fields['STATUS_ID']);

$tabControl->AddTextField('DESCRIPTION', $arFieldTitle['DESCRIPTION'], $fields['DESCRIPTION'], array('cols' => '60', 'rows' => 4));

$tabControl->AddViewField('HINT', '', Loc::getMessage('AOS_SMS_STATUS_EDIT_DESC_LENGTH') .' '. strlen($fields['DESCRIPTION']));

$tabControl->Buttons(array(
	//"disabled" => ($saleModulePermissions < 'W'),
	"back_url" => "api_orderstatus_sms_status.php?lang=" . $lang,
));

$tabControl->Show();

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php');
?>