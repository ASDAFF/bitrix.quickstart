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

define("ADMIN_MODULE_NAME", "api.formdesigner");
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');
Loc::loadMessages(__FILE__);

$rights = $APPLICATION->GetGroupRight(ADMIN_MODULE_NAME);
if($rights == 'D')
	$APPLICATION->AuthForm(Loc::getMessage('ACCESS_DENIED'));

if(!Loader::includeModule(ADMIN_MODULE_NAME))
	$APPLICATION->AuthForm(Loc::getMessage("ACCESS_DENIED"));

if(!Loader::includeModule('iblock')) {
	$APPLICATION->AuthForm(Loc::getMessage("ACCESS_DENIED"));
}

use Api\FormDesigner\Crm;
use Api\FormDesigner\Crm\CrmTable;

if($_SERVER['REQUEST_METHOD'] == 'POST' && $_REQUEST['saveCrm'] && check_bitrix_sessid()) {
	if($_REQUEST['ajax'])
		CUtil::JSPostUnEscape();

	$arAdditionalAuthData = array();
	$lastUpdated          = '';
	if(is_array($_REQUEST['CRM'])) {
		foreach($_REQUEST['CRM'] as $ID => $arCrm) {
			if(is_array($arCrm)) {
				$arCrm['ID'] = intval($ID);
				if($arCrm['DELETED'] && $arCrm['ID'] > 0) {
					CrmTable::delete($arCrm['ID']);
				}
				else {
					$arCrmFields = array(
						 'NAME' => trim($arCrm['NAME']),
						 'URL'  => trim($arCrm['URL']),
					);

					if($arCrm['ID'] <= 0) {
						$result = CrmTable::add($arCrmFields);
						if($result->isSuccess())
							$arCrm['ID'] = $result->getId();
					}
					else {
						CrmTable::update($arCrm['ID'], $arCrmFields);
					}

					$lastUpdated = $arCrm['ID'];

					if(strlen($arCrm['LOGIN']) > 0 && strlen($arCrm['PASSWORD']) > 0) {
						$arAdditionalAuthData[ $arCrm['ID'] ] = array(
							 'LOGIN'    => $arCrm['LOGIN'],
							 'PASSWORD' => $arCrm['PASSWORD'],
						);
					}
				}
			}
		}
	}

	if($_REQUEST['ajax']) {
		$arCRMServers = array();

		$rsCrm = CrmTable::getList();
		while($arServer = $rsCrm->fetch()) {
			if(isset($arAdditionalAuthData[ $arServer['ID'] ]))
				$arServer = array_merge($arServer, $arAdditionalAuthData[ $arServer['ID'] ]);
			if($lastUpdated == $arServer['ID'])
				$arServer['NEW'] = 'Y';

			$arCRMServers[] = $arServer;
		}

		$APPLICATION->RestartBuffer();
		echo CUtil::PhpToJSObject($arCRMServers);
		require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin_after.php");
		die();
	}
}

if($_SERVER['REQUEST_METHOD'] == 'GET' && $_REQUEST['action']) {

	$action = $_REQUEST['action'];
	$result = '{"result":"error"}';

	if(check_bitrix_sessid()) {
		switch($action) {
			case 'get_fields':
			case 'check':
				$CRM_ID = intval($_REQUEST['ID']);

				if($CRM_ID > 0) {
					$arAuth = null;
					if($_REQUEST['LOGIN'] && $_REQUEST['PASSWORD']) {
						$arAuth = array('LOGIN' => $_REQUEST['LOGIN'], 'PASSWORD' => $_REQUEST['PASSWORD']);
					}

					$link   = new Crm\Lead($CRM_ID, $arAuth);
					$arFields = $link->getFields($_REQUEST['reload'] == 'Y');

					if(is_array($arAuth)) {
						$authHash = $link->getAuthHash();
					}

					if($arFields) {
						$result = '{"result":"ok","fields":' . CUtil::PhpToJsObject($arFields) . (is_array($arAuth) ? ',"auth_hash":"' . $authHash . '"' : '') . '}';
					}
					else {
						$result = '{"result":"error","error":"' . CUtil::JSEscape($link->error) . '"}';
					}
				}
				break;

			case 'add_lead':
				$FORM_ID   = intval($_REQUEST['FORM_ID']);
				$RESULT_ID = intval($_REQUEST['RESULT_ID']);

				if($FORM_ID > 0 && $RESULT_ID > 0) {
					$leadId = CFormCrm::AddLead($FORM_ID, $RESULT_ID);
					if($leadId > 0) {
						$result = '{"result":"ok",ID:' . intval($leadId) . '}';
					}
					else {
						if($ex = $APPLICATION->GetException()) {
							$result = '{"result":"error","error":"' . CUtil::JSEscape($ex->GetString()) . '"}';
						}
					}
				}
				break;
		}
	}
	else {
		$result = '{"result":"error","error":"session_expired"}';
	}

	$APPLICATION->RestartBuffer();
	echo $result;
	die();
}


$APPLICATION->SetTitle(Loc::getMessage('API_FDA_CRM_PAGE_TITLE'));
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php');


CJSCore::Init(array('popup', 'ajax'));


$crmList = array();
$rsCrm   = CrmTable::getList();
while($row = $rsCrm->Fetch()) {
	$crmList[] = $row;
}


$aTabs      = array(
	 array(
			"DIV"   => "edit1",
			"TAB"   => Loc::getMessage('API_FDA_CRM_TAB_NAME'),
			"ICON"  => "",
			"TITLE" => Loc::getMessage('API_FDA_CRM_TAB_TITLE'),
	 ),
);
$tabControl = new CAdminForm("api_formdesigner", $aTabs);

$tabControl->BeginPrologContent();
?>
	<style>
		.popup-window-titlebar{ height: 49px; line-height: 49px; font-weight: bold; }
		#crm_table{ width: 100% }
		#crm_table tr td:last-child{ white-space: nowrap }
		.form-crm-settings table{ width: 100%; }
		.form-crm-settings input{ width: 100%; }
		.form-crm-settings table td{ padding: 4px; }
		.form-crm-settings-hide-auth .form-crm-auth{ display: none; }
		.form-action-button{ display: inline-block; height: 20px; width: 20px; margin: 0 3px }
		.action-edit{ background: scroll transparent url('/bitrix/css/api.core/images/icons/light/file-edit.svg') no-repeat 0 0; }
		.action-delete{ background: scroll transparent url('/bitrix/css/api.core/images/icons/light/trash.svg') no-repeat 0 0; }
	</style>
	<script type="text/javascript">
		function _showPass(el) {
			el.parentNode.replaceChild(BX.create('INPUT', {
				props: {
					type: el.type == 'text' ? 'password' : 'text',
					name: el.name,
					value: el.value
				}
			}), el);
		}

		function CRM(data) {
			var popup_id = Math.random();

			data = data || {ID: 'new_' + popup_id}

			if (data && data.URL) {
				var r = /^(http|https):\/\/([^\/]+)(.*)$/i,
					 res = r.exec(data.URL);
				if (!res) {
					var proto = data.URL.match(/\.bitrix24\./) ? 'https' : 'http';

					data.URL = proto + '://' + data.URL;
					res = r.exec(data.URL);
				}

				if (res) {
					data.URL_SERVER = res[1] + '://' + res[2];
					data.URL_PATH = res[3];
				}
			}

			if (!data.HASH) {
				var content = '<div class="form-crm-settings"><form name="form_' + popup_id + '"><table cellpadding="0" cellspacing="2" border="0"><tr><td align="right"><?=CUtil::JSEscape(GetMessage('FORM_CRM_TITLEBAR_NAME'))?>:</td><td><input type="text" name="NAME" value="' + BX.util.htmlspecialchars(data.NAME || '') + '"></td></tr><tr><td align="right"><?=CUtil::JSEscape(GetMessage('FORM_TAB_CRM_FORM_URL_SERVER'))?>:</td><td><input type="text" name="URL_SERVER" value="' + BX.util.htmlspecialchars(data.URL_SERVER || '') + '"></td></tr><tr><td align="right"><?=CUtil::JSEscape(GetMessage('FORM_TAB_CRM_FORM_URL_PATH'))?>:</td><td><input type="text" name="URL_PATH" value="' + BX.util.htmlspecialchars(data.URL_PATH || '<?=GetMessage('FORM_CRM_DEFAULT_URL')?>') + '"></td></tr><tr><td colspan="2" align="center"><b><?=CUtil::JSEscape(GetMessage('FORM_TAB_CRM_ROW_AUTH'))?></b></td></tr><tr><td align="right"><?=CUtil::JSEscape(GetMessage('FORM_TAB_CRM_ROW_AUTH_LOGIN'))?>:</td><td><input type="text" name="LOGIN" value="' + BX.util.htmlspecialchars(data.LOGIN || '') + '"></td></tr><tr><td align="right"><?=CUtil::JSEscape(GetMessage('FORM_TAB_CRM_ROW_AUTH_PASSWORD'))?>:</td><td><input type="password" name="PASSWORD" value="' + BX.util.htmlspecialchars(data.PASSWORD || '') + '"></td></tr><tr><td></td><td><a href="javascript:void(0)" onclick="_showPass(document.forms[\'form_' + popup_id + '\'].PASSWORD); BX.hide(this.parentNode);"><?=CUtil::JSEscape(GetMessage('FORM_TAB_CRM_ROW_AUTH_PASSWORD_SHOW'))?></a></td></tr></table></form></div>';
			}
			else {
				var content = '<div class="form-crm-settings form-crm-settings-hide-auth" id="popup_cont_' + popup_id + '"><form name="form_' + popup_id + '"><table cellpadding="0" cellspacing="2" border="0"><tr><td align="right"><?=CUtil::JSEscape(GetMessage('FORM_TAB_CRM_ROW_TITLE'))?>:</td><td><input type="text" name="NAME" value="' + BX.util.htmlspecialchars(data.NAME || '') + '"></td></tr><tr><td align="right"><?=CUtil::JSEscape(GetMessage('FORM_TAB_CRM_FORM_URL_SERVER'))?>:</td><td><input type="text" name="URL_SERVER" value="' + BX.util.htmlspecialchars(data.URL_SERVER || '') + '"></td></tr><tr><td align="right"><?=CUtil::JSEscape(GetMessage('FORM_TAB_CRM_FORM_URL_PATH'))?>:</td><td><input type="text" name="URL_PATH" value="' + BX.util.htmlspecialchars(data.URL_PATH || '<?=GetMessage('FORM_CRM_DEFAULT_URL')?>') + '"></td></tr><tr class="form-crm-auth"><td colspan="2" align="center"><b><?=CUtil::JSEscape(GetMessage('FORM_TAB_CRM_ROW_AUTH'))?></b></td></tr><tr class="form-crm-auth"><td align="right"><?=CUtil::JSEscape(GetMessage('FORM_TAB_CRM_ROW_AUTH_LOGIN'))?>:</td><td><input type="text" name="LOGIN" value="' + BX.util.htmlspecialchars(data.LOGIN || '') + '"></td></tr><tr class="form-crm-auth"><td align="right"><?=CUtil::JSEscape(GetMessage('FORM_TAB_CRM_ROW_AUTH_PASSWORD'))?>:</td><td><input type="password" name="PASSWORD" value="' + BX.util.htmlspecialchars(data.PASSWORD || '') + '"></td></tr><tr><td align="right"></td><td><a href="javascript:void(0)" onclick="_showPass(document.forms[\'form_' + popup_id + '\'].PASSWORD);BX.hide(this);" class="form-crm-auth"><?=CUtil::JSEscape(GetMessage('FORM_TAB_CRM_ROW_AUTH_PASSWORD_SHOW'))?></a><a href="javascript:void(0)" onclick="BX.removeClass(BX(\'popup_cont_' + popup_id + '\'), \'form-crm-settings-hide-auth\'); BX.hide(this);"><?=CUtil::JSEscape(GetMessage('FORM_TAB_CRM_ROW_AUTH_SHOW'))?></a></td></tr></table></form></div>';
			}

			var wnd = new BX.PopupWindow('popup_' + popup_id, window, {
				titleBar: {content: BX.create('SPAN', {text: !isNaN(parseInt(data.ID)) ? '<?=CUtil::JSEscape(GetMessage('FORM_CRM_TITLEBAR_EDIT'))?>' : '<?=CUtil::JSEscape(GetMessage('FORM_CRM_TITLEBAR_NEW'))?>'})},
				draggable: true,
				autoHide: false,
				closeIcon: true,
				closeByEsc: true,
				width: 350,
				content: content,
				buttons: [
					new BX.PopupWindowButton({
						text: BX.message('JS_CORE_WINDOW_SAVE'),
						className: "popup-window-button-accept",
						events: {
							click: function () {CRMSave(wnd, data, document.forms['form_' + popup_id])}
						}
					}),
					new BX.PopupWindowButtonLink({
						text: BX.message('JS_CORE_WINDOW_CANCEL'),
						className: "popup-window-button-link-cancel",
						events: {
							click: function () {wnd.close()}
						}
					})
				]
			});

			wnd.show();
		}

		function CRMRedraw(data) {
			var table = BX('crm_table').tBodies[0];

			while (table.rows.length > 0)
				table.removeChild(table.rows[0]);

			for (var i = 0; i < data.length; i++) {
				var tr = table.insertRow(-1);
				tr.id = 'crm_row_' + data[i].ID;

				tr.insertCell(-1).appendChild(document.createTextNode(data[i].NAME || '<?=CUtil::JSEscape(GetMessage('FORM_TAB_CRM_UNTITLED'))?>'));
				tr.insertCell(-1).appendChild(document.createTextNode(data[i].URL));

				var authCell = tr.insertCell(-1);
				authCell.id = 'crm_auth_cell_' + data[i].ID;
				if (!!data[i].LOGIN && !!data[i].PASSWORD) {
					authCell.appendChild(document.createTextNode('<?=CUtil::JSEscape(GetMessage('FORM_TAB_CRM_CHECK_LOADING'))?>'));
					BX.ajax.loadJSON('<?=$APPLICATION->GetCurPage()?>?action=check&reload=Y&ID=' + BX.util.urlencode(data[i].ID) + '&LOGIN=' + BX.util.urlencode(data[i].LOGIN) + '&PASSWORD=' + BX.util.urlencode(data[i].PASSWORD) + '&<?=bitrix_sessid_get()?>', BX.delegate(function (data) {
						BX.cleanNode(this);
						this.innerHTML = (data && data.result == 'ok') ? 'OK' : ('<?=CUtil::JSEscape(GetMessage('FORM_TAB_CRM_CHECK_ERROR'))?>'.replace('#ERROR#', data.error || ''));
					}, authCell));
				}
				else if (data[i].HASH) {
					authCell.appendChild(BX.create('A', {
						props: {BXCRMID: data[i].ID},
						attrs: {href: 'javascript: void(0)'},
						events: {click: function () {CRMCheck(this.BXCRMID)}},
						text: '<?=CUtil::JSEscape(GetMessage('FORM_TAB_CRM_CHECK'))?>'
					}));
				}
				else {
					authCell.appendChild(document.createTextNode('<?=CUtil::JSEscape(GetMessage('FORM_TAB_CRM_CHECK_NO'))?>'));
				}

				BX.adjust(tr.insertCell(-1), {
					children: [
						BX.create('A', {
							props: {
								className: 'form-action-button action-edit',
								title: '<?=CUtil::JSEscape(GetMessage('FORM_TAB_CRM_EDIT'))?>'
							},

							attrs: {href: 'javascript: void(0)'},
							events: {click: BX.delegate(function () {CRM(this);}, data[i])}
						}),
						BX.create('A', {
							props: {
								BXCRMID: data[i].ID,
								className: 'form-action-button action-delete',
								title: '<?=CUtil::JSEscape(GetMessage('FORM_TAB_CRM_DELETE'))?>'
							},
							attrs: {href: 'javascript: void(0)'},
							events: {click: function () {CRMDelete(this.BXCRMID);}}
						})
					]
				});
			}
		}

		function CRMSave(wnd, data_old, form) {
			var URL = form.URL_SERVER.value;
			if (URL.substring(URL.length - 1, 1) != '/' && form.URL_PATH.value.substring(0, 1) != '/')
				URL += '/';
			URL += form.URL_PATH.value;

			var flds = ['ID', 'NAME', 'URL', 'LOGIN', 'PASSWORD'],
				 data = {
					 ID: data_old.ID,
					 NAME: form.NAME.value,
					 URL: URL,
					 LOGIN: !!form.LOGIN ? form.LOGIN.value : '',
					 PASSWORD: !!form.PASSWORD ? form.PASSWORD.value : ''
				 };

			var res = false, r = /^(http|https):\/\/([^\/]+)(.*)$/i;
			if (data.URL) {
				res = r.test(data.URL);
				if (!res) {
					var proto = data.URL.match(/\.bitrix24\./) ? 'https' : 'http';
					data.URL = proto + '://' + data.URL;
					res = r.test(data.URL);
				}
			}

			if (!res) {
				alert('<?=CUtil::JSEscape(GetMessage('FORM_TAB_CRM_WRONG_URL'))?>');
			}
			else {
				var query_str = '';

				for (var i = 0; i < flds.length; i++) {
					query_str += (query_str == '' ? '' : '&') + 'CRM[' + data.ID + '][' + flds[i] + ']=' + BX.util.urlencode(data[flds[i]]);
				}

				BX.ajax({
					method: 'POST',
					dataType: 'json',
					url: '<?=CUtil::JSEscape($APPLICATION->GetCurPageParam('saveCrm=Y&ajax=Y&' . bitrix_sessid_get()))?>',
					data: query_str,
					onsuccess: CRMRedraw
				});

				if (!!wnd)
					wnd.close();
			}
		}

		function CRMDelete(ID) {
			if (confirm('<?=CUtil::JSEscape(GetMessage('FORM_TAB_CRM_CONFIRM'))?>')) {
				BX.ajax({
					method: 'POST',
					dataType: 'json',
					url: '<?=CUtil::JSEscape($APPLICATION->GetCurPageParam('saveCrm=Y&ajax=Y&' . bitrix_sessid_get()))?>',
					data: 'CRM[' + ID + '][DELETED]=Y',
					onsuccess: CRMRedraw
				});
			}
		}

		function CRMCheck(ID) {
			var c = BX('crm_auth_cell_' + ID);
			if (c) {
				c.innerHTML = '<?=CUtil::JSEscape(GetMessage('FORM_TAB_CRM_CHECK_LOADING'))?>';
			}

			BX.ajax.loadJSON('<?=$APPLICATION->GetCurPage()?>?action=check&ID=' + ID + '&reload=Y&<?=bitrix_sessid_get();?>', function (res) {
				if (!!res) {
					if (res.result == 'ok') {
						BX('crm_auth_cell_' + ID).innerHTML = 'OK';
					}
					else {
						BX('crm_auth_cell_' + ID).innerHTML = '<?=CUtil::JSEscape(GetMessage('FORM_TAB_CRM_CHECK_ERROR'))?>'.replace('#ERROR#', res.error || '');
					}
				}
			});
		}
		<?
		if (count($crmList) > 0):
		?>
		BX.ready(function () {
			BX.ajax({
				method: 'POST',
				dataType: 'json',
				url: '<?=CUtil::JSEscape($APPLICATION->GetCurPageParam('saveCrm=Y&ajax=Y&' . bitrix_sessid_get()))?>',
				onsuccess: CRMRedraw
			});
		});
		<?
		endif;
		?>
	</script>
<?
//echo BeginNote();
//echo Loc::getMessage('ASM_MESSAGE_EDIT_NOTE_1');
//echo EndNote();
//echo $USER_FIELD_MANAGER->ShowScript();
$tabControl->EndPrologContent();


$tabControl->BeginEpilogContent();
?>
<?=bitrix_sessid_post()?>
	<input type="hidden" name="update" value="Y">
	<input type="hidden" name="lang" value="<?=$lang;?>">
<?
$tabControl->EndEpilogContent();

//заголовки закладок
$tabControl->Begin(array('FORM_ACTION' => $APPLICATION->GetCurPage() . "?lang=" . $lang));


//*********************************************************
//                   первая вкладка
//*********************************************************
$tabControl->BeginNextFormTab();

$tabControl->BeginCustomField('CRM', '');
?>
	<tr>
		<td><?=$tabControl->GetCustomLabelHTML()?></td>
		<td>

			<table class="internal" id="crm_table">
				<thead>
				<tr class="heading">
					<td><?=GetMessage('FORM_TAB_CRM_ROW_TITLE');?></td>
					<td><?=GetMessage('FORM_TAB_CRM_ROW_URL');?></td>
					<td><?=GetMessage('FORM_TAB_CRM_ROW_AUTH');?></td>
					<td width="34"></td>
				</tr>
				</thead>
				<tbody>
				<?
				if(count($crmList) <= 0):
					?>
					<tr>
						<td colspan="4" align="center"><?=GetMessage('FORM_TAB_CRM_NOTE');?>
							<a href="javascript:void(0)" onclick="CRM(); return false;"><?=GetMessage('FORM_TAB_CRM_NOTE_LINK');?></a>
						</td>
					</tr>
					<?
				endif;
				?>
				</tbody>
				<tfoot>
				<tr>
					<td colspan="4" align="left">
						<input type="button" onclick="CRM(); return false;" value="<?=htmlspecialcharsbx(GetMessage('FORM_TAB_CRM_ADD_BUTTON'));?>">
					</td>
				</tr>
				</tfoot>
			</table>

		</td>
	</tr>
<?
$tabControl->EndCustomField('CRM');

/*$tabControl->Buttons(array(
	 "disabled" => ($ASM_RIGHT < "W"),
	 "back_url" => "api_message_list.php?lang=" . $lang,
));*/

$tabControl->Show();

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php');
?>