<?
/**
 * Bitrix vars
 *
 * @var CUser $USER
 * @var CMain $APPLICATION
 *
 */

use Bitrix\Main\Loader,
	 Bitrix\Main\Application,
	 Bitrix\Main\SiteTable,
	 Bitrix\Main\Localization\Loc,
	 Bitrix\Main\Config\Option;

//use Bitrix\Main\Mail\Internal\EventMessageTable;

define('ADMIN_MODULE_NAME', 'api.auth');
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');
Loc::loadMessages(__FILE__);

$bReadOnly = ($APPLICATION->GetGroupRight(ADMIN_MODULE_NAME) < 'W');

if($bReadOnly)
	$APPLICATION->AuthForm(Loc::getMessage('ACCESS_DENIED'));

if(!Loader::includeModule(ADMIN_MODULE_NAME))
	$APPLICATION->AuthForm(Loc::getMessage('ACCESS_DENIED'));


CJSCore::Init(array('jquery2'));

$errorMsgs = null;

use \Api\Auth\SettingsTable;


$context = Application::getInstance()->getContext();
$request = $context->getRequest();


//---------- Табы ----------//
$formId  = 'api_auth_settings';
$aTabs[] = Array(
	 'DIV'   => 'tab_general',
	 'TAB'   => Loc::getMessage('AAAS_TAB_NAME_GENERAL'),
	 'TITLE' => Loc::getMessage('AAAS_TAB_NAME_GENERAL'),
);
$aTabs[] = Array(
	 'DIV'   => 'tab_login',
	 'TAB'   => Loc::getMessage('AAAS_TAB_NAME_LOGIN'),
	 'TITLE' => Loc::getMessage('AAAS_TAB_NAME_LOGIN'),
);
$aTabs[] = Array(
	 'DIV'   => 'tab_register',
	 'TAB'   => Loc::getMessage('AAAS_TAB_NAME_REGISTER'),
	 'TITLE' => Loc::getMessage('AAAS_TAB_NAME_REGISTER'),
);

$tabControl = new CAdminTabControl($formId, $aTabs, true, true);


//---------- Сохраняем настройки ----------//
if($request->isPost() && strlen($save) > 0 && check_bitrix_sessid()) {

	if($arFields = $request->get('FIELDS')) {
		foreach($arFields as $key => $value) {

			if(is_array($value)){
				foreach($value as $k=>$v){
					if($v == '')
						unset($value[$k]);
				}
			}

			SettingsTable::setOption(array(
				 'NAME'  => $key,
				 'VALUE' => is_array($value) ? serialize($value) : $value,
			));
		}
	}


	//---------- LOGIN ----------//
	$LOGIN = $request->get('AUTH_FIELDS');
	if(!$LOGIN)
		$LOGIN = array('LOGIN', 'EMAIL');

	SettingsTable::setOption(array(
		 'NAME'  => 'AUTH_FIELDS',
		 'VALUE' => serialize($LOGIN),
	));


	//---------- REGISTER ----------//
	$REGISTER = $request->get('REGISTER');

	$REGISTER['SHOW_FIELDS']     = array_diff((array)$REGISTER['SHOW_FIELDS'], array(''));
	$REGISTER['REQUIRED_FIELDS'] = array_diff((array)$REGISTER['REQUIRED_FIELDS'], array(''));
	$REGISTER['USER_FIELDS']     = array_diff((array)$REGISTER['USER_FIELDS'], array(''));
	$REGISTER['GROUP_ID']        = array_diff((array)$REGISTER['GROUP_ID'], array(''));

	SettingsTable::setOption(array(
		 'NAME'  => 'REGISTER',
		 'VALUE' => serialize($REGISTER),
	));


	//Clear cache
	SettingsTable::getEntity()->cleanCache();

	if(!$errorMsgs) {
		LocalRedirect('/bitrix/admin/api_auth_settings.php?lang=' . LANGUAGE_ID . '&' . $tabControl->ActiveTabParam());
	}
}


//---------- REGISTER ----------//
$arFormFields = array(
	 'TITLE'               => 1,
	 'LAST_NAME'           => 1,
	 'NAME'                => 1,
	 'SECOND_NAME'         => 1,
	 'LOGIN'               => 1,
	 'PASSWORD'            => 1,
	 'CONFIRM_PASSWORD'    => 1,
	 'EMAIL'               => 1,
	 'AUTO_TIME_ZONE'      => 1,
	 'PERSONAL_PROFESSION' => 1,
	 'PERSONAL_WWW'        => 1,
	 'PERSONAL_ICQ'        => 1,
	 'PERSONAL_GENDER'     => 1,
	 'PERSONAL_BIRTHDAY'   => 1,
	 'PERSONAL_PHOTO'      => 1,
	 'PERSONAL_PHONE'      => 1,
	 'PERSONAL_FAX'        => 1,
	 'PERSONAL_MOBILE'     => 1,
	 'PERSONAL_PAGER'      => 1,
	 'PERSONAL_STREET'     => 1,
	 'PERSONAL_MAILBOX'    => 1,
	 'PERSONAL_CITY'       => 1,
	 'PERSONAL_STATE'      => 1,
	 'PERSONAL_ZIP'        => 1,
	 'PERSONAL_COUNTRY'    => 1,
	 'PERSONAL_NOTES'      => 1,
	 'WORK_COMPANY'        => 1,
	 'WORK_DEPARTMENT'     => 1,
	 'WORK_POSITION'       => 1,
	 'WORK_WWW'            => 1,
	 'WORK_PHONE'          => 1,
	 'WORK_FAX'            => 1,
	 'WORK_PAGER'          => 1,
	 'WORK_STREET'         => 1,
	 'WORK_MAILBOX'        => 1,
	 'WORK_CITY'           => 1,
	 'WORK_STATE'          => 1,
	 'WORK_ZIP'            => 1,
	 'WORK_COUNTRY'        => 1,
	 'WORK_PROFILE'        => 1,
	 'WORK_LOGO'           => 1,
	 'WORK_NOTES'          => 1,
);

if(!CTimeZone::Enabled())
	unset($arFormFields['AUTO_TIME_ZONE']);

$arUserFields = array('' => Loc::getMessage('AAAS_NOT_SET'));
foreach($arFormFields as $value => $dummy) {
	$arUserFields[ $value ] = Loc::getMessage('AAAS_FIELD_' . $value);
}

$arRes       = $GLOBALS['USER_FIELD_MANAGER']->GetUserFields('USER', 0, LANGUAGE_ID);
$arUserProps = array('' => Loc::getMessage('AAAS_NOT_SET'));
if(!empty($arRes)) {
	foreach($arRes as $key => $val)
		$arUserProps[ $val['FIELD_NAME'] ] = (strLen($val['EDIT_FORM_LABEL']) > 0 ? $val['EDIT_FORM_LABEL'] : $val['FIELD_NAME']);
}


//Группы пользователей
$arGroups = array('' => Loc::getMessage('AAAS_NOT_SET'));
$rsGroups = CGroup::GetList($by = "c_sort", $order = "asc", Array("ACTIVE" => "Y"));
while($arGroup = $rsGroups->Fetch()) {
	$arGroups[ $arGroup["ID"] ] = $arGroup["NAME"];
}
$countGroups = count($arGroups);



//Подготовим все настройки модуля для вывода
$arSettings = array();
$rsConfig   = SettingsTable::getList();
while($setting = $rsConfig->fetch()) {
	$arSettings[ $setting['NAME'] ] = $setting['VALUE'];
}


$APPLICATION->SetTitle(Loc::getMessage('AAAS_PAGE_TITLE'));
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

// VIEW ////////////////////////////////////////////////////////////////////////////////////////////////////////////////


//Выводим сообщения
if($errorMsgs) {
	$m = new CAdminMessage(array(
		 'TYPE'    => 'ERROR',
		 'MESSAGE' => implode('<br>\n', $errorMsgs),
		 'HTML'    => true,
	));

	echo $m->Show();
}

$authData      = (array)unserialize($arSettings['AUTH_FIELDS']);
$regData       = (array)unserialize($arSettings['REGISTER']);
$agreementData = (array)unserialize($arSettings['USER_CONSENT_ID']);
?>
<? if(Loader::includeModule('api.core')): ?>
	<?
	CUtil::InitJSCore('api_redactor2');
	?>
	<style type="text/css">
		.redactor-air li a.re-button-icon,
		.redactor-toolbar li a.re-button-icon{ padding: 10px; height: auto }
		#api_auth_settings_layout .adm-detail-content-cell-l{ width: 30% }
	</style>
	<script type="text/javascript">
		jQuery(document).ready(function ($) {
			var wysiwyg_cfg = {
				lang: '<?=LANGUAGE_ID?>',
				minHeight: 100,
				maxHeight: 300,
				//imageUpload: '/tmp/api_formdesigner/images',
				//fileUpload: '/tmp/api_formdesigner/files',
				imageResizable: true,
				imagePosition: true,
				linkSize: 250,
				convertLinks: false,
				linkify: true,
				linkNofollow: true,
				pasteLinkTarget: '_blank',
				placeholder: 'Enter a text...',
				script: true,
				structure: true,
				overrideStyles: true,
				toolbarFixed: false,
				//preClass: 'api-highlighted',
				preSpaces: 2,
				//videoContainerClass: 'video-container',
				//scrollTarget: '#my-scrollable-layer'
				buttons: ['format', 'bold', 'italic', 'deleted', 'lists', 'orderedlist', 'alignment', 'link', 'horizontalrule'], //'image', 'file'
				//buttonsHide: ['html'],
				formatting: ['p', 'blockquote', 'pre', 'h2', 'h3', 'h4', 'h5'],
				plugins: ['source', 'bufferbuttons', 'underline', 'inlinestyle', 'alignment', 'counter', 'fontcolor', 'fontfamily', 'fontsize', 'table', 'video', 'scriptbuttons', 'fullscreen'] //'source','imagemanager','filemanager',
			};

			$('#API_MESS_PRIVACY').redactor(wysiwyg_cfg);
		});

	</script>
<? endif ?>
	<form method="POST" enctype="multipart/form-data" action="<?=$APPLICATION->GetCurPage()?>?lang=<?=LANGUAGE_ID?>">
		<?=bitrix_sessid_post()?>
		<?
		$tabControl->Begin();

		$sectionStyle = 'height: 30px;text-transform: uppercase;font-size: 16px';
		$hintStyle    = 'border-radius: 3px;background: #fbfae2;box-shadow: 0 0 0 1px #d4d5d6;color: #000;padding:3px 5px;margin:5px 0';
		?>

		<!---------- GENERAL ---------->
		<?
		$tabControl->BeginNextTab();
		?>
		<tr>
			<td class="adm-detail-content-cell-l"><?=Loc::getMessage('AAAS_GENERAL_USE_JQUERY');?></td>
			<td class="adm-detail-content-cell-r">
				<?
				$usejQuery = $arSettings['USE_JQUERY'];
				?>
				<select name="FIELDS[USE_JQUERY]">
					<option value=""><?=Loc::getMessage('AAAS_NOT_SET')?></option>
					<option value="1.8"<?=($usejQuery == '1.8' ? ' selected' : '')?>>1.8</option>
					<option value="2.1"<?=($usejQuery == '2.1' ? ' selected' : '')?>>2.1</option>
				</select>
			</td>
		</tr>

		<tr class="heading">
			<td colspan="2"><?=Loc::getMessage('AAAS_GENERAL_GROUP_USER_CONSENT')?></td>
		</tr>
		<tr>
			<td class="adm-detail-content-cell-l"></td>
			<td class="adm-detail-content-cell-r">
				<?
				echo BeginNote();
				echo Loc::getMessage('AAAS_GENERAL_USER_CONSENT_INFO');
				echo EndNote();
				?>
				<p>
					<a href="/bitrix/admin/agreement_edit.php?ID=0&lang=<?=LANGUAGE_ID?>" target="_blank"><?=Loc::getMessage('AAAS_GENERAL_USER_CONSENT_CREATE')?></a>
				</p>
			</td>
		</tr>
		<tr>
			<td class="adm-detail-content-cell-l"></td>
			<td class="adm-detail-content-cell-r">
				<?
				$agreementList = \Bitrix\Main\UserConsent\Agreement::getActiveList();
				?>
				<? if($agreementList): ?>
					<select name="FIELDS[USER_CONSENT_ID][]" multiple size="<?=(count($agreementList) > 10 ? 10 : count($agreementList) + 1)?>">
						<option value=""><?=Loc::getMessage('AAAS_NOT_SET')?></option>
						<? foreach($agreementList as $key => $text): ?>
							<option value="<?=$key?>"<?=(in_array($key, $agreementData) ? ' selected' : '')?>>[<?=$key?>]&nbsp;<?=$text?></option>
						<? endforeach; ?>
					</select>
				<? endif ?>
			</td>
		</tr>

		<tr class="heading">
			<td colspan="2"><?=Loc::getMessage('AAAS_GENERAL_GROUP_PRIVACY')?></td>
		</tr>
		<tr>
			<td class="adm-detail-content-cell-l"><?=Loc::getMessage('AAAS_GENERAL_USE_PRIVACY');?></td>
			<td class="adm-detail-content-cell-r">
				<input type="hidden" name="FIELDS[USE_PRIVACY]" value="N">
				<input type="checkbox" name="FIELDS[USE_PRIVACY]" value="Y" <?=$arSettings['USE_PRIVACY'] == 'Y' ? 'checked' : '';?>>
			</td>
		</tr>
		<tr>
			<td class="adm-detail-content-cell-l"><?=Loc::getMessage('AAAS_GENERAL_MESS_PRIVACY');?></td>
			<td class="adm-detail-content-cell-r">
				<?
				if(!isset($arSettings['MESS_PRIVACY']))
					$arSettings['MESS_PRIVACY'] = Loc::getMessage('AAAS_GENERAL_MESS_PRIVACY_DEFAULT');
				?>
				<?
				echo BeginNote();
				echo Loc::getMessage('AAAS_GENERAL_MESS_PRIVACY_MACROS');
				echo EndNote();
				?>
				<textarea id="API_MESS_PRIVACY" name="FIELDS[MESS_PRIVACY]" style="width: 100%"><?=$arSettings['MESS_PRIVACY'];?></textarea>
			</td>
		</tr>
		<tr>
			<td class="adm-detail-content-cell-l"><?=Loc::getMessage('AAAS_GENERAL_MESS_PRIVACY_LINK');?></td>
			<td class="adm-detail-content-cell-r">
				<input type="text" name="FIELDS[MESS_PRIVACY_LINK]" value="<?=$arSettings['MESS_PRIVACY_LINK'];?>" style="width: 100%">
			</td>
		</tr>
		<tr>
			<td class="adm-detail-content-cell-l"><?=Loc::getMessage('AAAS_GENERAL_MESS_PRIVACY_CONFIRM');?></td>
			<td class="adm-detail-content-cell-r">
				<?
				if(!isset($arSettings['MESS_PRIVACY_CONFIRM']))
					$arSettings['MESS_PRIVACY_CONFIRM'] = Loc::getMessage('AAAS_GENERAL_MESS_PRIVACY_CONFIRM_DEFAULT');
				?>
				<textarea id="API_MESS_PRIVACY_CONFIRM" name="FIELDS[MESS_PRIVACY_CONFIRM]" style="width: 100%;" rows="2"><?=$arSettings['MESS_PRIVACY_CONFIRM'];?></textarea>
			</td>
		</tr>

		<!---------- LOGIN ---------->
		<?
		$tabControl->BeginNextTab();
		?>
		<tr>
			<td class="adm-detail-content-cell-l"><?=Loc::getMessage('AAAS_AUTH_FIELDS');?></td>
			<td class="adm-detail-content-cell-r">
				<?
				$arAuthFields = (array)Loc::getMessage('AAAS_AUTH_FIELDS_VALUES');
				?>
				<select name="AUTH_FIELDS[]" multiple size="2">
					<? foreach($arAuthFields as $key => $val): ?>
						<option value="<?=$key?>"<?=(in_array($key, $authData) ? ' selected' : '')?>><?=$val?></option>
					<? endforeach; ?>
				</select>
			</td>
		</tr>

		<!---------- REGISTER ---------->
		<?
		$tabControl->BeginNextTab();
		?>

		<tr>
			<td class="adm-detail-content-cell-l"><?=Loc::getMessage('AAAS_SHOW_FIELDS');?></td>
			<td class="adm-detail-content-cell-r">
				<select name="REGISTER[SHOW_FIELDS][]" multiple size="<?=(count($arUserFields) > 10 ? 10 : count($arUserFields))?>">
					<? foreach($arUserFields as $key => $val): ?>
						<option value="<?=$key?>"<?=($regData['SHOW_FIELDS'] && in_array($key, $regData['SHOW_FIELDS']) ? ' selected' : '')?>><?=$val?></option>
					<? endforeach; ?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="adm-detail-content-cell-l"><?=Loc::getMessage('AAAS_REQUIRED_FIELDS');?></td>
			<td class="adm-detail-content-cell-r">
				<select name="REGISTER[REQUIRED_FIELDS][]" multiple size="<?=(count($arUserFields) > 10 ? 10 : count($arUserFields))?>">
					<? foreach($arUserFields as $key => $val): ?>
						<option value="<?=$key?>"<?=($regData['REQUIRED_FIELDS'] && in_array($key, $regData['REQUIRED_FIELDS']) ? ' selected' : '')?>><?=$val?></option>
					<? endforeach; ?>
				</select>
			</td>
		</tr>
		<? if($arUserProps): ?>
			<tr>
				<td class="adm-detail-content-cell-l"><?=Loc::getMessage('AAAS_USER_FIELDS');?></td>
				<td class="adm-detail-content-cell-r">
					<select name="REGISTER[USER_FIELDS][]" multiple size="<?=(count($arUserProps) > 10 ? 10 : count($arUserProps))?>">
						<? foreach($arUserProps as $key => $val): ?>
							<option value="<?=$key?>"<?=($regData['USER_FIELDS'] && in_array($key, $regData['USER_FIELDS']) ? ' selected' : '')?>><?=$val?></option>
						<? endforeach; ?>
					</select>
				</td>
			</tr>
		<? endif ?>
		<tr>
			<td class="adm-detail-content-cell-l"><?=Loc::getMessage('AAAS_REGISTER_GROUP_ID');?></td>
			<td class="adm-detail-content-cell-r">
				<select name="REGISTER[GROUP_ID][]" multiple size="<?=(count($arGroups) > 10 ? 10 : count($arGroups))?>">
					<? foreach($arGroups as $key => $val): ?>
						<option value="<?=$key?>"<?=($regData['GROUP_ID'] && in_array($key, $regData['GROUP_ID']) ? ' selected' : '')?>><?=$val?></option>
					<? endforeach; ?>
				</select>
			</td>
		</tr>
		<?
		$tabControl->Buttons(
			 array(
					"disabled"      => ($bReadOnly),
					'btnSave'       => true,
					'btnApply'      => false,
					'btnCancel'     => false,
					'btnSaveAndAdd' => false,
			 )
		);
		$tabControl->End();
		?>
	</form>
<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
?>