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
use Api\OrderStatus\SmsGatewayTable;

define("ADMIN_MODULE_NAME", "api.orderstatus");
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');
Loc::loadMessages(__FILE__);

global $USER, $APPLICATION, $USER_FIELD_MANAGER;

$MODULE_SALE_RIGHT = $APPLICATION->GetGroupRight('sale');
if($MODULE_SALE_RIGHT <= 'D') {
	$APPLICATION->AuthForm(Loc::getMessage('ACCESS_DENIED'));
}

if(!Loader::includeModule(ADMIN_MODULE_NAME))
	$APPLICATION->AuthForm(Loc::getMessage('ACCESS_DENIED'));

if(!Loader::includeModule('sale'))
	$APPLICATION->AuthForm(Loc::getMessage('ACCESS_DENIED'));


//Лэнги полей
$arFields     = array();
$arFieldTitle = array();
foreach(SmsGatewayTable::getMap() as $key => $value) {
	$arFields[ $key ]     = $value;
	$arFieldTitle[ $key ] = $value['title'];
}

//Все сайты
$arSites = array();
$rsSites = SiteTable::getList(array(
	 'select' => array('LID', 'NAME', 'SITE_NAME', 'SERVER_NAME', 'EMAIL'),
	 'filter' => array('ACTIVE' => 'Y'),
));
while($site = $rsSites->fetch()) {

	if(empty($site['SERVER_NAME']))
		$site['SERVER_NAME'] = $serverName;

	if(empty($site['SITE_NAME']))
		$site['SITE_NAME'] = $site['NAME'] ? $site['NAME'] : $siteName;

	$arSites[ $site['LID'] ] = $site;
}

/*
 * @var \Bitrix\Main\Entity\Field $field
foreach(SmsGatewayTable::getMap() as $key=>$field)
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

if($request->isPost() && $request->getPost('update') && check_bitrix_sessid()) {
	$postParams = $request->getPost('PARAMS');

	/*
	if(isset($postParams['LOGIN']) && empty($postParams['LOGIN']))
		$errorMessage .= Loc::getMessage('AOS_SMS_GW_EDIT_FIELD_ERROR', array('#FIELD#' => Loc::getMessage('AOS_SMS_GW_EDIT_FIELD_LOGIN'))) . "\n";
	if(empty($postParams['PASSWORD']))
		$errorMessage .= Loc::getMessage('AOS_SMS_GW_EDIT_FIELD_ERROR', array('#FIELD#' => Loc::getMessage('AOS_SMS_GW_EDIT_FIELD_PASSWORD'))) . "\n";
	if(empty($postParams['SENDER']))
		$errorMessage .= Loc::getMessage('AOS_SMS_GW_EDIT_FIELD_ERROR', array('#FIELD#' => Loc::getMessage('AOS_SMS_GW_EDIT_FIELD_SENDER'))) . "\n";
	*/


	if(empty($errorMessage)) {
		$uFields = array();
		$USER_FIELD_MANAGER->EditFormAddFields($ufEntityId, $uFields);

		$fields = array(
			 'ACTIVE'      => ($request->getPost('ACTIVE') !== null) ? 'Y' : 'N',
			 'SORT'        => $request->getPost('SORT'),
			 'PARAMS'      => serialize($postParams),
			 'DATE_MODIFY' => new \Bitrix\Main\Type\DateTime(),
			 'MODIFIED_BY' => $USER->GetID(),
		);

		$fields = array_merge($fields, $uFields);

		$result = null;
		if($id && !$bCopy) {
			$result = SmsGatewayTable::update($id, $fields);
		}
		else {
			$result = SmsGatewayTable::add($fields);
		}

		if($result && $result->isSuccess()) {
			$id = $result->getId();
			if(strlen($request->getPost("apply")) == 0)
				LocalRedirect("/bitrix/admin/api_orderstatus_sms_gateway.php?lang=" . $lang . "&" . GetFilterParams("filter_", false));
			else
				LocalRedirect("/bitrix/admin/api_orderstatus_sms_gateway_edit.php?lang=" . $lang . "&ID=" . $id . "&" . GetFilterParams("filter_", false));
		}
		else {
			$errorMessage .= join("\n", $result->getErrorMessages());
		}
	}

	unset($fields);
}


$APPLICATION->SetTitle($id ? '[' . $id . '] ' . $company['NAME'] : GetMessage('AOS_SMS_GW_EDIT_PAGE_TITLE'));
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php');
//START VIEW

if($errorMessage)
	CAdminMessage::ShowMessage($errorMessage);


if($id > 0) {
	$select     = array('*');
	$userFields = $USER_FIELD_MANAGER->GetUserFields($ufEntityId);
	foreach($userFields as $field)
		$select[] = $field['FIELD_NAME'];

	$params  = array(
		 'select' => $select,
		 'filter' => array('=ID' => $id),
	);
	$company = SmsGatewayTable::getList($params)->fetch();

	$APPLICATION->SetTitle($company['NAME']);
}
else {
	$APPLICATION->SetTitle(Loc::getMessage("AOS_SMS_GW_EDIT_TITLE"));
}

$fields = ($request->isPost()) ? $_POST : $company;

//Кнопки = Добавить/Копировать/Удалить
$aMenu = array(
	 array(
			"TEXT" => Loc::getMessage('MAIN_ADMIN_MENU_LIST'),
			"LINK" => "api_orderstatus_sms_gateway.php?lang=" . $lang,
			"ICON" => "btn_list",
	 ),
);
/*
if($id && !$bCopy)
{
	$aMenu[] = array("SEPARATOR" => "Y");
	$aMenu[] = array(
		"TEXT" => Loc::getMessage('MAIN_ADMIN_MENU_ADD'),
		"LINK" => "api_orderstatus_sms_gateway_edit.php?lang=" . $lang,
		"ICON" => "btn_new",
	);
	$aMenu[] = array(
		"TEXT" => Loc::getMessage('MAIN_ADMIN_MENU_COPY'),
		"LINK" => "api_orderstatus_sms_gateway_edit.php?ID=" . $id . "&amp;action=copy&amp;lang=" . $lang,
		"ICON" => "btn_copy",
	);
	$aMenu[] = array(
		"TEXT" => Loc::getMessage('MAIN_ADMIN_MENU_DELETE'),
		"LINK" => "javascript:if(confirm('" . Loc::getMessage('AOS_SMS_GW_EDIT_ADMIN_MENU_DELETE_CONFIRM') . "'))window.location='api_orderstatus_sms_gateway_edit.php?ID=" . $id . "&action=delete&lang=" . $lang . "&" . bitrix_sessid_get() . "';",
		"ICON" => "btn_delete",
	);
}
*/
$context = new CAdminContextMenu($aMenu);
$context->Show();


$aTabs      = array(
	 array(
			"DIV"   => "edit1",
			"TAB"   => Loc::getMessage('AOS_SMS_GW_EDIT_TAB_NAME'),
			"ICON"  => "",
			"TITLE" => Loc::getMessage('AOS_SMS_GW_EDIT_TAB_NAME'),
	 ),
);
$tabControl = new CAdminForm("template_edit", $aTabs);


$gatewayNote = Loc::getMessage('AOS_SMS_GW_EDIT_NOTE_' . $fields['NAME']);
$tabControl->BeginPrologContent();
//echo $USER_FIELD_MANAGER->ShowScript();

if($gatewayNote) {
	echo BeginNote();
	echo $gatewayNote;
	echo EndNote();
}

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

$tabControl->AddViewField('ID', $arFieldTitle['ID'] . ':', $company['ID']);
$tabControl->AddCheckBoxField('ACTIVE', $arFieldTitle['ACTIVE'], false, 'Y', $fields['ACTIVE'] != 'N');

if($company['MODIFIED_BY'])
	$tabControl->AddViewField('MODIFIED_BY', $arFieldTitle['MODIFIED_BY'] . ':', GetFormatedUserName($company['MODIFIED_BY'], false, true));

if($company['DATE_MODIFY'])
	$tabControl->AddViewField('DATE_MODIFY', $arFieldTitle['DATE_MODIFY'] . ':', $company['DATE_MODIFY']);


$tabControl->AddEditField(
	 'SORT',
	 $arFieldTitle['SORT'],
	 false,
	 array('size' => 5),
	 (isset($fields['SORT']) ? $fields['SORT'] : 500)
);

$tabControl->BeginCustomField('PARAMS', $arFieldTitle['PARAMS'], true);

$params = (is_string($fields['PARAMS']) ? unserialize($fields['PARAMS']) : $fields['PARAMS']);

$textSize = 42;
$arSender = $params['SENDER'];
?>
	<tr class="heading">
		<td colspan="2">
			<?=Loc::getMessage('AOS_SMS_GW_EDIT_HEADING_' . $fields['NAME'])?>
			<input type="hidden" name="PARAMS[GATEWAY]" value="<?=$fields['NAME']?>">
		</td>
	</tr>

	<? if($fields['NAME'] == 'Smsru'): ?>
		<tr>
			<td><b><?=Loc::getMessage('AOS_SMS_GW_EDIT_FIELD_API_ID')?></b></td>
			<td>
				<input type="text" name="PARAMS[API_ID]" value="<?=$params['API_ID']?>" size="<?=$textSize?>">
			</td>
		</tr>
	<? else: ?>

		<tr>
			<td><b><?=Loc::getMessage('AOS_SMS_GW_EDIT_FIELD_LOGIN')?></b></td>
			<td>
				<input type="text" name="PARAMS[LOGIN]" value="<?=$params['LOGIN']?>" size="<?=$textSize?>">
			</td>
		</tr>

		<? if($fields['NAME'] == 'Redsms' || $fields['NAME'] == 'Redsms3'): ?>
			<tr>
				<td><b><?=Loc::getMessage('AOS_SMS_GW_EDIT_FIELD_API_KEY')?></b></td>
				<td>
					<input type="text" name="PARAMS[API_KEY]" value="<?=$params['API_KEY']?>" size="<?=$textSize?>">
				</td>
			</tr>
		<? elseif($fields['NAME'] == 'Smsclub'): ?>
			<tr>
				<td><b><?=Loc::getMessage('AOS_SMS_GW_EDIT_FIELD_TOKEN')?></b></td>
				<td>
					<input type="text" name="PARAMS[TOKEN]" value="<?=$params['TOKEN']?>" size="<?=$textSize?>">
				</td>
			</tr>
		<? else: ?>
			<tr>
				<td><b><?=Loc::getMessage('AOS_SMS_GW_EDIT_FIELD_PASSWORD')?></b></td>
				<td>
					<input type="text" name="PARAMS[PASSWORD]" value="<?=$params['PASSWORD']?>" size="<?=$textSize?>">
				</td>
			</tr>
		<? endif ?>
	<? endif ?>

	<tr>
		<td valign="top"><b><?=Loc::getMessage('AOS_SMS_GW_EDIT_FIELD_SENDER')?></b></td>
		<td>
			<? foreach($arSites as $site): ?>
				<div style="margin-bottom:5px">
					<input type="text" size="<?=$textSize?>" name="PARAMS[SENDER][<?=$site['LID']?>]" value="<?=$arSender[ $site['LID'] ]?>"> [<?=$site['LID']?>] <?=$site['SITE_NAME']?>
				</div>
			<? endforeach; ?>
		</td>
	</tr>
<?
$tabControl->EndCustomField('SENDER');

$tabControl->Buttons(array(
	 "back_url" => "api_orderstatus_sms_gateway.php?lang=" . $lang,
));

$tabControl->Show();

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php');
?>