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

define("ADMIN_MODULE_NAME", "api.message");
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');
Loc::loadMessages(__FILE__);

global $USER, $APPLICATION, $USER_FIELD_MANAGER;

$ASM_RIGHT = $APPLICATION->GetGroupRight(ADMIN_MODULE_NAME);
if($ASM_RIGHT < 'W')
	$APPLICATION->AuthForm(Loc::getMessage('ACCESS_DENIED'));

if(!Loader::includeModule(ADMIN_MODULE_NAME))
	$APPLICATION->AuthForm(Loc::getMessage('ACCESS_DENIED'));

CJSCore::Init(array('jquery2'));

use \Api\Message\MessageTable;
use \Api\Message\ConfigTable;
use \Api\Message\Tools;


//Лэнги полей
$arFieldTitle = array();
foreach(MessageTable::getMap() as $key => $value) {
	$arFieldTitle[ $key ] = $value['title'];
}

/*
 * @var \Bitrix\Main\Entity\Field $field
foreach(MessageTable::getMap() as $key=>$field)
{
	$data = array($field->getName(),$field->getDataType());
}*/

$admInputStyle = 'padding: 5px 10px 5px 5px; overflow-y: auto; height: auto;';

$context      = Application::getInstance()->getContext();
$documentRoot = Application::getDocumentRoot();
$lang         = $context->getLanguage();
$request      = $context->getRequest();
$id           = intval($request->get('ID'));
$ufEntityId   = 'ASM_MESSAGE';
$bCopy        = ($action == "copy");
$company      = array();

$errorMessage = '';

if($request->isPost() && $request->getPost('update') && check_bitrix_sessid()) {
	$postFields = $request->getPost('FIELDS');

	//Prepare post fields
	$postFields['ACTIVE'] = ($postFields['ACTIVE'] == 'Y' ? 'Y' : 'N');
	$postFields['BLOCK']  = ($postFields['BLOCK'] == 'Y' ? 'Y' : 'N');

	if($postFields['ACTIVE_FROM'])
		$postFields['ACTIVE_FROM'] = new \Bitrix\Main\Type\DateTime($postFields['ACTIVE_FROM']);

	if($postFields['ACTIVE_TO'])
		$postFields['ACTIVE_TO'] = new \Bitrix\Main\Type\DateTime($postFields['ACTIVE_TO']);

	if($postFields['SITE_ID'])
		$postFields['SITE_ID'] = join(',', $postFields['SITE_ID']);

	if($postFields['GROUP_ID']) {
		foreach($postFields['GROUP_ID'] as $key => $groupId) {
			if($groupId == '')
				unset($postFields['GROUP_ID'][ $key ]);
		}
		$postFields['GROUP_ID'] = join(',', $postFields['GROUP_ID']);
		unset($key,$groupId);
	}


	if($postFields['USER_ID']) {
		foreach($postFields['USER_ID'] as $key => $userId) {
			if($userId == '')
				unset($postFields['USER_ID'][ $key ]);
		}
		$postFields['USER_ID'] = join(',', $postFields['USER_ID']);
		unset($key,$userId);
	}


	$postFields['MESSAGE']      = $request->getPost('MESSAGE');
	$postFields['MESSAGE_TYPE'] = $request->getPost('MESSAGE_TYPE');

	$postFields['TIMESTAMP_X'] = new \Bitrix\Main\Type\DateTime();
	$postFields['MODIFIED_BY'] = $USER->GetID();


	//Required fields validate
	if(empty($postFields['NAME']))
		$errorMessage .= Loc::getMessage('ASM_MESSAGE_EDIT_FIELD_ERROR', array('#FIELD#' => $arFieldTitle['NAME'])) . "\n";

	if(empty($postFields['SITE_ID']))
		$errorMessage .= Loc::getMessage('ASM_MESSAGE_EDIT_FIELD_ERROR', array('#FIELD#' => $arFieldTitle['SITE_ID'])) . "\n";

	if(empty($postFields['MESSAGE']))
		$errorMessage .= Loc::getMessage('ASM_MESSAGE_EDIT_FIELD_ERROR', array('#FIELD#' => $arFieldTitle['MESSAGE'])) . "\n";


	//Write data to db
	if(empty($errorMessage)) {
		//Clear cache
		ConfigTable::clearCache();

		$uFields = array();
		$USER_FIELD_MANAGER->EditFormAddFields($ufEntityId, $uFields);

		$fields = array_merge($postFields, $uFields);

		$result = null;
		if($id && !$bCopy) {
			$result = MessageTable::update($id, $fields);
		}
		else {
			$result = MessageTable::add($fields);
		}

		if($result && $result->isSuccess()) {
			$id = $result->getId();
			if(strlen($request->getPost("apply")) == 0)
				LocalRedirect("/bitrix/admin/api_message_list.php?lang=" . $lang . "&" . GetFilterParams("filter_", false));
			else
				LocalRedirect("/bitrix/admin/api_message_edit.php?lang=" . $lang . "&ID=" . $id . "&" . GetFilterParams("filter_", false));
		}
		else {
			$errorMessage .= join("\n", $result->getErrorMessages());
		}
	}

	unset($fields);
}


$APPLICATION->SetTitle($id ? '[' . $id . '] ' . $company['NAME'] : GetMessage('ASM_MESSAGE_EDIT_PAGE_TITLE'));
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
	$company = MessageTable::getList($params)->fetch();

	$APPLICATION->SetTitle($company['NAME']);
}
else {
	$APPLICATION->SetTitle(Loc::getMessage("ASM_MESSAGE_EDIT_TITLE"));
}


$arGroups = array();
$db       = CGroup::GetList($by = "c_sort", $order = "asc", array("ACTIVE" => "Y"));
while($ar = $db->Fetch())
	$arGroups[ $ar["ID"] ] = $ar["NAME"];



//Кнопки = Добавить/Копировать/Удалить
$aContext = array(
	 array(
			"TEXT" => Loc::getMessage('MAIN_ADMIN_MENU_LIST'),
			"LINK" => "api_message_list.php?lang=" . $lang,
			"ICON" => "btn_list",
	 ),
);
if($id && !$bCopy && $ASM_RIGHT == 'W') {
	$aContext[] = array("SEPARATOR" => "Y");
	$aContext[] = array(
		 "TEXT" => Loc::getMessage('MAIN_ADMIN_MENU_ADD'),
		 "LINK" => "api_message_edit.php?lang=" . $lang,
		 "ICON" => "btn_new",
	);
	$aContext[] = array(
		 "TEXT" => Loc::getMessage('MAIN_ADMIN_MENU_COPY'),
		 "LINK" => "api_message_edit.php?ID=" . $id . "&amp;action=copy&amp;lang=" . $lang,
		 "ICON" => "btn_copy",
	);
	$aContext[] = array(
		 "TEXT" => Loc::getMessage('MAIN_ADMIN_MENU_DELETE'),
		 "LINK" => "javascript:if(confirm('" . Loc::getMessage('ASM_MESSAGE_EDIT_DELETE_CONFIRM') . "'))window.location='api_message_list.php?ID=" . $id . "&action=delete&lang=" . $lang . "&" . bitrix_sessid_get() . "';",
		 "ICON" => "btn_delete",
	);
}
$context = new CAdminContextMenu($aContext);
$context->Show();


$aTabs      = array(
	 array(
			"DIV"   => "edit1",
			"TAB"   => Loc::getMessage('ASM_MESSAGE_EDIT_TAB_NAME'),
			"ICON"  => "",
			"TITLE" => Loc::getMessage('ASM_MESSAGE_EDIT_TAB_NAME'),
	 ),
);
$tabControl = new CAdminForm("api_message", $aTabs);

$tabControl->BeginPrologContent();
?>
	<style>
		.popup-window-titlebar{
			height: 49px;
			line-height: 49px;
			font-weight: bold;
		}
		#api_message_form textarea{ width: 100%; height: 100px; }
	</style>
	<script>

		jQuery(function ($) {
			$('select[name*=TYPE]').on('change', function () {
				if ($(this).val() == 'custom') {
					$('#api_message_field_color').show();
				}
				else {
					$('#api_message_field_color').hide().find('input[name*=COLOR]').val('');
				}
			});
		});

		function API_SetColor(color) {
			$('input[name*=COLOR]').val(color);
		}

		function API_ShowColorModal() {
			var popup_id = Math.random();
			//var data = {ID:'new_' + popup_id};
			var wnd = new BX.PopupWindow('popup_' + popup_id, window, {
				titleBar: {
					content: BX.create('SPAN', {
						text: '<?=CUtil::JSEscape(Loc::getMessage('ASM_MESSAGE_EDIT_POPUP_COLOR_TITLE'))?>'
					})
				},
				draggable: true,
				autoHide: false,
				closeIcon: true,
				closeByEsc: true,
				content: '<?=CUtil::JSEscape(Loc::getMessage('ASM_MESSAGE_EDIT_POPUP_COLOR_LIST'))?>',
				/*buttons: [
				 new BX.PopupWindowButton({
				 text : BX.message('JS_CORE_WINDOW_SAVE'),
				 className : "popup-window-button-accept",
				 events : {
				 click : function(){CRMSave(wnd, data, document.forms['form_'+popup_id])}
				 }
				 }),
				 new BX.PopupWindowButtonLink({
				 text : BX.message('JS_CORE_WINDOW_CANCEL'),
				 className : "popup-window-button-link-cancel",
				 events : {
				 click : function() {wnd.close()}
				 }
				 })
				 ]*/
			});
			wnd.show();
		}

		function API_ShowButtonModal() {
			var popup_id = Math.random();
			//var data = {ID:'new_' + popup_id};
			var wnd = new BX.PopupWindow('popup_' + popup_id, window, {
				titleBar: {
					content: BX.create('SPAN', {
						text: '<?=CUtil::JSEscape(Loc::getMessage('ASM_MESSAGE_EDIT_POPUP_BUTTON_TITLE'))?>'
					})
				},
				draggable: true,
				autoHide: false,
				closeIcon: true,
				closeByEsc: true,
				content: '<?=CUtil::JSEscape(Loc::getMessage('ASM_MESSAGE_EDIT_POPUP_BUTTON_LIST'))?>',
				/*buttons: [
				 new BX.PopupWindowButton({
				 text : BX.message('JS_CORE_WINDOW_SAVE'),
				 className : "popup-window-button-accept",
				 events : {
				 click : function(){CRMSave(wnd, data, document.forms['form_'+popup_id])}
				 }
				 }),
				 new BX.PopupWindowButtonLink({
				 text : BX.message('JS_CORE_WINDOW_CANCEL'),
				 className : "popup-window-button-link-cancel",
				 events : {
				 click : function() {wnd.close()}
				 }
				 })
				 ]*/
			});
			wnd.show();
		}
	</script>
<?
//echo BeginNote();
//echo Loc::getMessage('ASM_MESSAGE_EDIT_NOTE_1');
//echo EndNote();
echo $USER_FIELD_MANAGER->ShowScript();
$tabControl->EndPrologContent();


$tabControl->BeginEpilogContent();
?>
<?=bitrix_sessid_post()?>
	<input type="hidden" name="update" value="Y">
	<input type="hidden" name="lang" value="<?=$lang;?>">
<? if(!$bCopy): ?>
	<input type="hidden" name="ID" value="<?=$id;?>">
<? endif ?>
<?
$tabControl->EndEpilogContent();

//заголовки закладок
$tabControl->Begin(array('FORM_ACTION' => $APPLICATION->GetCurPage() . "?lang=" . $lang));


//*********************************************************
//                   первая закладка
//*********************************************************
$tabControl->BeginNextFormTab();

$fields = ($request->isPost()) ? $request->getPost('FIELDS') : $company;
if($request->isPost()) {
	$fields['MESSAGE']      = $request->getPost('MESSAGE');
	$fields['MESSAGE_TYPE'] = $request->getPost('MESSAGE_TYPE');
}


$tabControl->AddViewField('ID', $arFieldTitle['ID'] . ':', $fields['ID']);
$tabControl->AddCheckBoxField('FIELDS[ACTIVE]', $arFieldTitle['ACTIVE'], false, 'Y', $fields['ACTIVE'] != 'N');

if($fields['TIMESTAMP_X'])
	$tabControl->AddViewField('FIELDS[TIMESTAMP_X]', $arFieldTitle['TIMESTAMP_X'] . ':', $fields['TIMESTAMP_X']);

if($fields['MODIFIED_BY'])
	$tabControl->AddViewField('FIELDS[MODIFIED_BY]', $arFieldTitle['MODIFIED_BY'] . ':', CApiMessage::getFormatedUserName($fields['MODIFIED_BY'], false, true));

$tabControl->AddEditField('FIELDS[SORT]', $arFieldTitle['SORT'], false, array('size' => 5), (isset($fields['SORT']) ? $fields['SORT'] : 500));


//$tabControl->AddCalendarField('FIELDS[ACTIVE_FROM]', $arFieldTitle['ACTIVE_FROM'] . ':', $fields['ACTIVE_FROM']);
$tabControl->BeginCustomField('ACTIVE_FROM', $arFieldTitle['ACTIVE_FROM']);
?>
	<tr id="tr_ACTIVE_FROM">
		<td><?=$tabControl->GetCustomLabelHTML()?>:</td>
		<td><?=CAdminCalendar::CalendarDate('FIELDS[ACTIVE_FROM]', $fields['ACTIVE_FROM'], 19, true)?></td>
	</tr>
<?
$tabControl->EndCustomField("ACTIVE_FROM", '<input type="hidden" id="ACTIVE_FROM" name="ACTIVE_FROM" value="' . $fields['ACTIVE_FROM'] . '">');

//$tabControl->AddCalendarField('FIELDS[ACTIVE_TO]', $arFieldTitle['ACTIVE_TO'] . ':', $fields['ACTIVE_TO']);
$tabControl->BeginCustomField('ACTIVE_TO', $arFieldTitle['ACTIVE_TO']);
?>
	<tr id="tr_ACTIVE_TO">
		<td><?=$tabControl->GetCustomLabelHTML()?>:</td>
		<td><?=CAdminCalendar::CalendarDate('FIELDS[ACTIVE_TO]', $fields['ACTIVE_TO'], 19, true)?></td>
	</tr>
<?
$tabControl->EndCustomField('ACTIVE_TO', '<input type="hidden" id="ACTIVE_TO" name="ACTIVE_TO" value="' . $fields['ACTIVE_TO'] . '">');

$tabControl->BeginCustomField('SITE_ID', $arFieldTitle['SITE_ID'], true);
?>
	<tr>
		<td><?=$tabControl->GetCustomLabelHTML()?></td>
		<td>
			<div class="adm-input" style="<?=$admInputStyle?>">
				<?=CSite::SelectBoxMulti('FIELDS[SITE_ID]', (is_array($fields['SITE_ID']) ? $fields['SITE_ID'] : explode(',', $fields['SITE_ID'])));?>
			</div>
		</td>
	</tr>
<?
$tabControl->EndCustomField('SITE_ID');

$tabControl->AddEditField('FIELDS[NAME]', $arFieldTitle['NAME'], true, array('size' => 56), $fields['NAME']);

$tabControl->BeginCustomField('TYPE', $arFieldTitle['TYPE']);
$arTypeOptions = Loc::getMessage('ASM_MESSAGE_EDIT_TYPE_VALUES');
?>
	<tr>
		<td><?=$tabControl->GetCustomLabelHTML()?></td>
		<td>
			<select name="FIELDS[TYPE]">
				<? foreach($arTypeOptions as $key => $val): ?>
					<option value="<?=$key?>"<?=($fields['TYPE'] == $key ? ' selected' : '')?>><?=$val?></option>
				<? endforeach; ?>
			</select>
		</td>
	</tr>
<?
$tabControl->EndCustomField('TYPE');

$tabControl->BeginCustomField('COLOR', $arFieldTitle['COLOR']);
?>
	<tr id="api_message_field_color" style="<?=($fields['TYPE'] == 'custom' ? '' : 'display:none')?>">
		<td><?=$tabControl->GetCustomLabelHTML()?></td>
		<td>
			<input type="text" size="56" name="FIELDS[COLOR]" value="<?=$fields['COLOR']?>" placeholder="#006DCA">&nbsp;<input type="button" value="..." onclick="BX.findChildren(this.parentNode, {'tag': 'IMG'}, true)[0].onclick();">
			<span style="float:left;width:1px;height:1px;visibility:hidden;position:absolute;">
				<? $APPLICATION->IncludeComponent("bitrix:main.colorpicker", "", array("SHOW_BUTTON" => "Y", "ONSELECT" => "API_SetColor")); ?>
			</span>
			&nbsp;<input type="button" value="?" onclick="API_ShowColorModal()">
		</td>
	</tr>
<?
$tabControl->EndCustomField('COLOR');


//CSite::InGroup()

$tabControl->BeginCustomField('GROUP_ID', $arFieldTitle['GROUP_ID']);
$arDBGroups = ($fields['GROUP_ID'] ? explode(',', $fields['GROUP_ID']) : array());
$groupSize  = (count($arGroups) > 5 ? 5 : count($arGroups) + 1);
?>
	<tr>
		<td><?=$tabControl->GetCustomLabelHTML()?></td>
		<td>
			<select name="FIELDS[GROUP_ID][]" size="<?=$groupSize?>" multiple>
				<option value=""><?=Loc::getMessage('ASM_MESSAGE_EDIT_OPTION')?></option>
				<? foreach($arGroups as $key => $val): ?>
					<option value="<?=$key?>"<?=($arDBGroups && in_array($key, $arDBGroups) ? ' selected' : '')?>><?=$val?></option>
				<? endforeach; ?>
			</select>
		</td>
	</tr>
<?
$tabControl->EndCustomField('GROUP_ID');

$tabControl->BeginCustomField('USER_ID', $arFieldTitle['USER_ID']);
$arUserId = $fields['USER_ID'] ? $fields['USER_ID'] : array();
if($arUserId && !is_array($arUserId))
	$arUserId = explode(',', $arUserId);
?>
	<tr>
		<td><?=$tabControl->GetCustomLabelHTML()?></td>
		<td>
			<?=Tools::findUserId('FIELDS[USER_ID]', $arUserId, $tabControl->GetFormName());?>
		</td>
	</tr>
<?
$tabControl->EndCustomField('USER_ID');


$tabControl->AddEditField('FIELDS[EXPIRES]', $arFieldTitle['EXPIRES'], false, array('size' => 56), (int)$fields['EXPIRES']);

$tabControl->AddEditField('FIELDS[CLOSE_TEXT]', $arFieldTitle['CLOSE_TEXT'], false, array('size' => 56), $fields['CLOSE_TEXT']);
//$tabControl->AddEditField('FIELDS[CLOSE_CLASS]', $arFieldTitle['CLOSE_CLASS'], false, array('size' => 56), $fields['CLOSE_CLASS']);
$tabControl->BeginCustomField('CLOSE_CLASS', $arFieldTitle['CLOSE_CLASS']);
?>
	<tr>
		<td><?=$tabControl->GetCustomLabelHTML()?></td>
		<td>
			<input type="text" size="56" name="FIELDS[CLOSE_CLASS]" value="<?=$fields['CLOSE_CLASS']?>" placeholder="api_button api_button_yellow">
			<input type="button" value="?" onclick="API_ShowButtonModal()">
		</td>
	</tr>
<?
$tabControl->EndCustomField('CLOSE_CLASS');

$tabControl->AddCheckBoxField('FIELDS[BLOCK]', $arFieldTitle['BLOCK'], false, 'Y', $fields['BLOCK'] != 'N');

$tabControl->AddTextField('FIELDS[PAGE_URL]', $arFieldTitle['PAGE_URL'], $fields['PAGE_URL']);
$tabControl->BeginCustomField('MESSAGE', $arFieldTitle['MESSAGE'], true);
?>
	<tr>
		<td><?=$tabControl->GetCustomLabelHTML()?></td>
		<td>
			<?
			CFileMan::AddHTMLEditorFrame(
				 "MESSAGE",
				 $fields["MESSAGE"],
				 "MESSAGE_TYPE",
				 $fields["MESSAGE_TYPE"],
				 array(
						'height' => 200,
						'width'  => '100%',
				 )
			); ?>
		</td>
	</tr>
<?
$tabControl->EndCustomField('MESSAGE');


// bitrix/modules/main/admin/user_edit.php
// bitrix/modules/sale/admin/company_edit.php
// bitrix/modules/main/admin/rating_edit.php

//$tabControl->AddFileField("PERSONAL_PHOTO", GetMessage("USER_PHOTO"), $str_PERSONAL_PHOTO, array("iMaxW"=>150, "iMaxH"=>150));
//$tabControl->AddCalendarField("PERSONAL_BIRTHDAY", GetMessage("USER_BIRTHDAY_DT").":", $str_PERSONAL_BIRTHDAY);
//CalendarDate("PERSONAL_BIRTHDAY", $str_PERSONAL_BIRTHDAY, "post_form", "20")
//$tabControl->AddCheckBoxField('ACTIVE', $arFieldTitle['ACTIVE'], false, 'Y', $fields['ACTIVE'] != 'N');
/*
$tabControl->BeginCustomField("ACTIVE", GetMessage('RATING_EDIT_FRM_ACTIVE'), false);
?>
	<tr>
		<td><?=GetMessage("RATING_EDIT_FRM_ACTIVE")?></td>
		<td><?=InputType("checkbox", "ACTIVE", "Y", $str_ACTIVE)?></td>
	</tr>
<?
$tabControl->EndCustomField("ACTIVE");
*/
//$tabControl->HideField('ACTIVE');
//$tabControl->EndCustomField("ACTIVE", '<input type="hidden" name="ACTIVE" value="'.$str_ACTIVE.'">');
//$tabControl->AddTextField('DESCRIPTION', Loc::getMessage('AOS_LT_DESCRIPTION'), $fields['DESCRIPTION'], array('cols' => 60, 'rows' => 5), true);
//$tabControl->AddEditField("NAME", GetMessage("COMPANY_NAME"), true, array('size' => 120), htmlspecialcharsbx($fields['NAME']));
//$tabControl->AddEditField("SORT", GetMessage("COMPANY_SORT"), false, array('size' => 30), $fields['SORT']);
//$tabControl->AddEditField("CODE", GetMessage("COMPANY_CODE"), false, array('size' => 30), htmlspecialcharsbx($fields['CODE']));

$tabControl->Buttons(array(
	 "disabled" => ($ASM_RIGHT < "W"),
	 "back_url" => "api_message_list.php?lang=" . $lang,
));

$tabControl->Show();

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php');
?>