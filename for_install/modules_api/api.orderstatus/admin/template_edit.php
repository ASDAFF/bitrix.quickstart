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
use Bitrix\Main\Localization\Loc;
use Api\OrderStatus\TemplateTable;

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
foreach(TemplateTable::getMap() as $key => $value)
{
	$arFieldTitle[ $key ] = $value['title'];
}

/*
 * @var \Bitrix\Main\Entity\Field $field
foreach(TemplateTable::getMap() as $key=>$field)
{
	$data = array($field->getName(),$field->getDataType());
}*/


$context      = Application::getInstance()->getContext();
$documentRoot = Application::getDocumentRoot();
$lang         = $context->getLanguage();
$request      = $context->getRequest();
$id           = intval($request->get('ID'));
$ufEntityId   = 'AOS_TPL';
$bCopy        = ($action == "copy");
$company      = array();

$errorMessage = '';

if($request->isPost() && $request->getPost('update') && check_bitrix_sessid())
{
	$name             = $request->getPost('NAME');
	$status_id        = $request->getPost('STATUS_ID');
	$description      = $request->getPost('DESCRIPTION');
	$description_type = $request->getPost('DESCRIPTION_TYPE');

	if(empty($name))
		$errorMessage .= Loc::getMessage('AOS_TPL_EDIT_FIELD_ERROR', array('#FIELD#' => $arFieldTitle['NAME'])) . "\n";

	if(empty($status_id))
		$errorMessage .= Loc::getMessage('AOS_TPL_EDIT_FIELD_ERROR', array('#FIELD#' => $arFieldTitle['STATUS_ID'])) . "\n";

	if(empty($description))
		$errorMessage .= Loc::getMessage('AOS_TPL_EDIT_FIELD_ERROR', array('#FIELD#' => $arFieldTitle['DESCRIPTION'])) . "\n";

	if(empty($errorMessage))
	{
		$uFields = array();
		$USER_FIELD_MANAGER->EditFormAddFields($ufEntityId, $uFields);

		$fields = array(
			'ACTIVE'           => ($request->getPost('ACTIVE') !== null) ? 'Y' : 'N',
			'NAME'             => $name,
			'STATUS_ID'        => $status_id,
			'DESCRIPTION'      => $description,
			'DESCRIPTION_TYPE' => $description_type,
			'DATE_MODIFY'      => new \Bitrix\Main\Type\DateTime(),
			'MODIFIED_BY'      => $USER->GetID(),
		);

		$fields = array_merge($fields, $uFields);

		$result = null;
		if($id && !$bCopy)
		{
			$result = TemplateTable::update($id, $fields);
		}
		else
		{
			//$fields['DATE_CREATE'] = new \Bitrix\Main\Type\DateTime();
			//$fields['CREATED_BY'] = $USER->GetID();
			$result = TemplateTable::add($fields);
		}

		if($result && $result->isSuccess())
		{
			$id = $result->getId();
			if(strlen($request->getPost("apply")) == 0)
				LocalRedirect("/bitrix/admin/api_orderstatus_template.php?lang=" . $lang . "&" . GetFilterParams("filter_", false));
			else
				LocalRedirect("/bitrix/admin/api_orderstatus_template_edit.php?lang=" . $lang . "&ID=" . $id . "&" . GetFilterParams("filter_", false));
		}
		else
		{
			$errorMessage .= join("\n", $result->getErrorMessages());
		}
	}

	unset($fields);
}


$APPLICATION->SetTitle($id ? '[' . $id . '] ' . $company['NAME'] : GetMessage('AOS_TPL_EDIT_PAGE_TITLE'));
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php');
//START VIEW

if($errorMessage)
	CAdminMessage::ShowMessage($errorMessage);


if($id > 0)
{
	$select     = array('*');
	$userFields = $USER_FIELD_MANAGER->GetUserFields($ufEntityId);
	foreach($userFields as $field)
		$select[] = $field['FIELD_NAME'];

	$params  = array(
		'select' => $select,
		'filter' => array('=ID' => $id),
	);
	$company = TemplateTable::getList($params)->fetch();

	$APPLICATION->SetTitle($company['NAME']);
}
else
{
	$APPLICATION->SetTitle(Loc::getMessage("AOS_TPL_EDIT_TITLE"));
}


$arStatus = array();
$res      = CSaleStatus::GetList(array(), array('LID' => LANGUAGE_ID), false, false, array('ID', 'NAME'));
while($ar_status = $res->Fetch())
	$arStatus[ $ar_status['ID'] ] = '[' . $ar_status['ID'] . '] ' . $ar_status['NAME'];



//Кнопки = Добавить/Копировать/Удалить
$aMenu = array(
	array(
		"TEXT" => Loc::getMessage('MAIN_ADMIN_MENU_LIST'),
		"LINK" => "api_orderstatus_template.php?lang=" . $lang,
		"ICON" => "btn_list",
	),
);
if($id && !$bCopy)
{
	$aMenu[] = array("SEPARATOR" => "Y");
	$aMenu[] = array(
		"TEXT" => Loc::getMessage('MAIN_ADMIN_MENU_ADD'),
		"LINK" => "api_orderstatus_template_edit.php?lang=" . $lang,
		"ICON" => "btn_new",
	);
	$aMenu[] = array(
		"TEXT" => Loc::getMessage('MAIN_ADMIN_MENU_COPY'),
		"LINK" => "api_orderstatus_template_edit.php?ID=" . $id . "&amp;action=copy&amp;lang=" . $lang,
		"ICON" => "btn_copy",
	);
	$aMenu[] = array(
		"TEXT" => Loc::getMessage('MAIN_ADMIN_MENU_DELETE'),
		"LINK" => "javascript:if(confirm('" . Loc::getMessage('AOS_TPL_EDIT_ADMIN_MENU_DELETE_CONFIRM') . "'))window.location='api_orderstatus_template.php?ID=" . $id . "&action=delete&lang=" . $lang . "&" . bitrix_sessid_get() . "';",
		"ICON" => "btn_delete",
	);
}
$context = new CAdminContextMenu($aMenu);
$context->Show();


$aTabs      = array(
	array(
		"DIV"   => "edit1",
		"TAB"   => Loc::getMessage('AOS_TPL_EDIT_TAB_NAME'),
		"ICON"  => "",
		"TITLE" => Loc::getMessage('AOS_TPL_EDIT_TAB_TITLE'),
	),
);
$tabControl = new CAdminForm("template_edit", $aTabs);


$tabControl->BeginPrologContent();
//echo $USER_FIELD_MANAGER->ShowScript();

echo BeginNote();
echo Loc::getMessage('AOS_TPL_EDIT_NOTE_DESC_RESTRICTION');
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

$tabControl->AddEditField('NAME', $arFieldTitle['NAME'], true, array('size' => 120), htmlspecialcharsbx($fields['NAME']));
$tabControl->AddDropDownField('STATUS_ID', $arFieldTitle['STATUS_ID'], true, $arStatus, $fields['STATUS_ID']);

$tabControl->BeginCustomField('DESCRIPTION', $arFieldTitle['DESCRIPTION'], true);
?>
	<tr>
		<td><?=$tabControl->GetCustomLabelHTML()?></td>
		<td>
			<?
			CFileMan::AddHTMLEditorFrame(
				"DESCRIPTION",
				$fields["DESCRIPTION"],
				"DESCRIPTION_TYPE",
				$fields["DESCRIPTION_TYPE"],
				array(
					'height' => 200,
					'width'  => '100%',
				)
			); ?>
		</td>
	</tr>
<?
$tabControl->EndCustomField('DESCRIPTION');


// bitrix/modules/main/admin/user_edit.php
// bitrix/modules/sale/admin/company_edit.php
// bitrix/modules/main/admin/rating_edit.php

//$tabControl->AddFileField("PERSONAL_PHOTO", GetMessage("USER_PHOTO"), $str_PERSONAL_PHOTO, array("iMaxW"=>150, "iMaxH"=>150));
//$tabControl->AddCalendarField("PERSONAL_BIRTHDAY", GetMessage("USER_BIRTHDAY_DT").":", $str_PERSONAL_BIRTHDAY);
//$tabControl->HideField('ACTIVE');
//$tabControl->EndCustomField("ACTIVE", '<input type="hidden" name="ACTIVE" value="'.$str_ACTIVE.'">');
//$tabControl->AddTextField('DESCRIPTION', Loc::getMessage('AOS_LT_DESCRIPTION'), $fields['DESCRIPTION'], array('cols' => 60, 'rows' => 5), true);
//$tabControl->AddEditField("NAME", GetMessage("COMPANY_NAME"), true, array('size' => 120), htmlspecialcharsbx($fields['NAME']));
//$tabControl->AddEditField("SORT", GetMessage("COMPANY_SORT"), false, array('size' => 30), $fields['SORT']);
//$tabControl->AddEditField("CODE", GetMessage("COMPANY_CODE"), false, array('size' => 30), htmlspecialcharsbx($fields['CODE']));

$tabControl->Buttons(array(
	//"disabled" => ($saleModulePermissions < 'W'),
	"back_url" => "api_orderstatus_template.php?lang=" . $lang,
));

$tabControl->Show();

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php');
?>