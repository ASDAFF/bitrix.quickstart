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

use Bitrix\Fileman\Block;

//use Bitrix\Main\Mail\Internal\EventMessageTable;

define('ADMIN_MODULE_NAME', 'api.mail');
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');
Loc::loadMessages(__FILE__);

$bReadOnly = ($APPLICATION->GetGroupRight(ADMIN_MODULE_NAME) < 'W');

if($bReadOnly)
	$APPLICATION->AuthForm(Loc::getMessage('ACCESS_DENIED'));

if(!Loader::includeModule(ADMIN_MODULE_NAME))
	$APPLICATION->AuthForm(Loc::getMessage('ACCESS_DENIED'));


CJSCore::Init(array('jquery2'));

$errorMsgs = null;

use Api\Mail\SettingsTable as Settings;
use Api\Mail\Tools;


$cache   = Application::getInstance()->getManagedCache();
$context = Application::getInstance()->getContext();
$request = $context->getRequest();

//---------- Сайты ----------//
$siteName   = Bitrix\Main\Config\Option::get('main', 'site_name', $_SERVER['SERVER_NAME']);
$serverName = Bitrix\Main\Config\Option::get('main', 'server_name', $_SERVER['SERVER_NAME']);

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


//---------- Табы ----------//
$formId = 'api_mail';
foreach($arSites as $val) {
	$aTabs[] = Array(
		 'DIV'   => 'tab_site_' . $val['LID'],
		 'TAB'   => '[' . $val['LID'] . '] ' . $val['SITE_NAME'],
		 'TITLE' => Loc::getMessage('AMAS_TAB_TITLE') . ' "' . $val['SITE_NAME'] . '"',
	);
}
$tabControl = new CAdminTabControl($formId, $aTabs, true, true);


//---------- Сохраняем настройки ----------//
if($request->isPost() && strlen($save) > 0) {
	$arPostFields = $request['FIELDS'];

	foreach($arSites as $arSite) {
		$siteId = $arSite['LID'];
		if($arPostFields[$siteId]){
			$arPostFields[$siteId]['MAIL_HTML'] = trim($_REQUEST['MAIL_HTML_'.$siteId]);
			$arPostFields[$siteId]['MAIL_TYPE'] = trim($_REQUEST['MAIL_TYPE_'.$siteId]);
		}
	}
	unset($siteId,$arSite);

	Settings::saveToFile($arPostFields, true);

	if($arPostFields) {
		foreach($arPostFields as $siteId => $field) {
			if(is_array($field)) {
				foreach($field as $name => $value) {
					$value = is_array($value) ? serialize($value) : trim($value);

					$arData = array(
						 'NAME'    => $name,
						 'VALUE'   => $value,
						 'SITE_ID' => $siteId,
					);
					Settings::addEx($arData);
				}
			}
		}
	}

	if(!$errorMsgs)
		LocalRedirect('/bitrix/admin/api_mail_settings.php?lang=' . LANGUAGE_ID . '&' . $tabControl->ActiveTabParam());
}


//---------- Настройки для вывода ----------//
$arSettings = (array)Settings::getFromFile();

$APPLICATION->SetTitle(Loc::getMessage('AMAS_PAGE_TITLE'));
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
?>
	<form method="POST" enctype="multipart/form-data" action="<?=$APPLICATION->GetCurPage()?>?lang=<?=LANGUAGE_ID?>">
		<?=bitrix_sessid_post()?>
		<?
		$tabControl->Begin();

		foreach($arSites as $arSite) {
			$tabControl->BeginNextTab();
			?>
			<tr>
				<td colspan="2" valign="top">
					<?
					$siteId = $arSite['LID'];

					$aTabs2      = (array)Loc::getMessage('AMAS_TAB_LIST');
					foreach($aTabs2 as $k=>&$v){
						$v['DIV'] .= '_'. $siteId;
					}
					$tabControl2 = new CAdminViewTabControl($formId . '_'. $siteId, $aTabs2);
					$tabControl2->Begin();

					$headingStyle = 'height: 30px;text-transform: uppercase;font-size: 16px';
					$hintStyle    = 'border-radius: 3px;background: #fbfae2;box-shadow: 0 0 0 1px #d4d5d6;color: #000;padding:3px 5px;margin:5px 0';

					$FIELD = 'FIELDS[' . $siteId . ']';
					$VALUE = $arSettings[ $siteId ];
					?>
					<?
					$tabControl2->BeginNextTab();
					?>
					<table cellpadding="2" cellspacing="2" border="0" width="100%" align="center" id="<?=$tabControl2->selectedTab?>_table">
						<colgroup>
							<col style="width: 50%">
							<col style="width: 50%">
						</colgroup>
						<tr>
							<td class="adm-detail-content-cell-l"><?=Loc::getMessage('AMAS_MAIL_ON');?></td>
							<td class="adm-detail-content-cell-r">
								<input type="hidden" name="<?=$FIELD?>[MAIL_ON]" value="N">
								<input type="checkbox" name="<?=$FIELD?>[MAIL_ON]" value="Y" <?=$VALUE['MAIL_ON'] == 'Y' ? 'checked' : '';?>>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<?
								/*
								 * === Блочный редактор ===
								Loader::includeModule('fileman');

								$fieldName = $FIELD . '[MAIL_HTML]';
								$fieldId   = 'API_MAIL_HTML_' . $siteId;
								$fieldValue = htmlspecialcharsbx($VALUE['MAIL_HTML']);

								$editorParams = array(
									 'id'                  => $fieldId,
									 'charset'             => LANG_CHARSET,
									 'site'                => $siteId,
									 'own_result_id'       => $fieldId,
									 'url'                 => '/bitrix/components/bitrix/sender.template.selector/ajax.php?lang=ru&template_type=BASE&template_id=mail_inc_profits&action=getTemplate&sessid=' . bitrix_sessid(),
									 //'previewUrl'          => '', //'/bitrix/components/bitrix/sender.mail.editor/ajax.php?action=preview&sessid=' . bitrix_sessid(),
									 //'saveFileUrl'         => '', //'bitrix/components/bitrix/sender.mail.editor/ajax.php?action=saveFile&sessid=' . bitrix_sessid(),
									 'templateType'        => '',
									 'templateId'          => '',
									 'isTemplateMode'      => true,
									 'isUserHavePhpAccess' => true,
									 'useLightTextEditor'  => false,
								);

								$isBlock = true;
								?>
								<div class="sender-message-editor-mail-wrapper">
									<div data-bx-editor-plain="" style="<?=($isBlock ? 'display: none;' : '')?>">
								<textarea data-bx-input=""
							          id="<?=$fieldId?>"
							          name="<?=$fieldName?>"
							          style="height: 320px; width: 100%;"
							          class="typearea"><?=$fieldValue?></textarea>
									</div>
									<div data-bx-editor-block="" style="<?=(!$isBlock ? 'display: none;' : '')?>">
										<?=Block\EditorMail::show($editorParams);?>
									</div>
								</div>
								<?*/?>
								<?
								//$isUserHavePhpAccess = $USER->CanDoOperation('edit_php');
								$isUserHavePhpAccess = false;
								Loader::includeModule('fileman');

								if(empty($VALUE['MAIL_HTML'])){
									$VALUE['MAIL_HTML'] = Loc::getMessage('AMAS_MAIL_DEFAULT_THEME');
								}

								\CFileMan::AddHTMLEditorFrame(
									 'MAIL_HTML_' . $siteId,
									 htmlspecialcharsbx($VALUE['MAIL_HTML']),
									 'MAIL_TYPE_'.$siteId,
									 htmlspecialcharsbx($VALUE['MAIL_TYPE']),
									 array(
											'height' => 500,
											'width'  => '100%',
									 ),
									 "N",
									 0,
									 "",
									 "onfocus=\"t=this\"",
									 $siteId,
									 !$isUserHavePhpAccess,
									 false,
									 array(
										 //'saveEditorKey' => $IBLOCK_ID,
										 //'site_template_type' => 'mail',
										 //'templateID' => $str_SITE_TEMPLATE_ID,
										 'componentFilter' => array('TYPE' => 'mail'),
										 //'limit_php_access' => !$isUserHavePhpAccess
									 )
								); ?>
								<script type="text/javascript" language="JavaScript">
									BX.addCustomEvent('OnEditorInitedAfter', function(editor){editor.components.SetComponentIcludeMethod('EventMessageThemeCompiler::includeComponent'); });
								</script>
								<br>
								<br>
								<div><?=Loc::getMessage('AMAS_MAIL_MACROS_HINT')?></div>
							</td>
						</tr>
					</table>
					<?
					$tabControl2->BeginNextTab();
					?>
					<table cellpadding="2" cellspacing="2" border="0" width="100%" align="center" id="am_table_<?=$siteId?>">
						<tr>
							<td class="adm-detail-content-cell-l"><?=Loc::getMessage('AMAS_DKIM_ON');?></td>
							<td class="adm-detail-content-cell-r">
								<input type="hidden" name="<?=$FIELD?>[DKIM_ON]" value="N">
								<input type="checkbox" name="<?=$FIELD?>[DKIM_ON]" value="Y" <?=$VALUE['DKIM_ON'] == 'Y' ? 'checked' : '';?>>
							</td>
						</tr>
						<tr>
							<td class="adm-detail-content-cell-l"><?=Loc::getMessage('AMAS_DKIM_d');?></td>
							<td class="adm-detail-content-cell-r">
								<input type="text" name="<?=$FIELD?>[DKIM_KEYS][d]" value="<?=$VALUE['DKIM_KEYS']['d'];?>" placeholder="<?=trim($arSite['SERVER_NAME'])?>">
							</td>
						</tr>
						<tr>
							<td class="adm-detail-content-cell-l"><?=Loc::getMessage('AMAS_DKIM_s');?></td>
							<td class="adm-detail-content-cell-r">
								<input type="text" name="<?=$FIELD?>[DKIM_KEYS][s]" value="<?=$VALUE['DKIM_KEYS']['s'];?>" placeholder="mail">
							</td>
						</tr>
						<tr>
							<td class="adm-detail-content-cell-l"><?=Loc::getMessage('AMAS_DKIM_i');?></td>
							<td class="adm-detail-content-cell-r">
								<input type="text" name="<?=$FIELD?>[DKIM_KEYS][i]" value="<?=$VALUE['DKIM_KEYS']['i'];?>" placeholder="noreply@<?=trim($arSite['SERVER_NAME'])?>">
							</td>
						</tr>
						<tr>
							<td class="adm-detail-content-cell-l"><?=Loc::getMessage('AMAS_DKIM_h');?></td>
							<td class="adm-detail-content-cell-r">
								<?
								$defValue = (array)Loc::getMessage('AMAS_DKIM_h_values');
								$curValue = (array)$VALUE['DKIM_KEYS']['h'];
								?>
								<select name="<?=$FIELD?>[DKIM_KEYS][h][]" multiple size="3">
									<? foreach($defValue as $val): ?>
										<option value="<?=$val?>"<?=($curValue && in_array($val, $curValue) ? ' selected' : '')?>><?=$val?></option>
									<? endforeach; ?>
								</select>
							</td>
						</tr>
						<tr>
							<td class="adm-detail-content-cell-l"><?=Loc::getMessage('AMAS_RSA_PUBLIC_KEY');?></td>
							<td class="adm-detail-content-cell-r">
									<textarea name="<?=$FIELD?>[RSA_PUBLIC_KEY]"
									          cols="85" rows="5"
									          placeholder="<?=Loc::getMessage('AMAS_RSA_PUBLIC_KEY_EXAMPLE');?>"><?=$VALUE['RSA_PUBLIC_KEY'];?></textarea>
							</td>
						</tr>
						<tr>
							<td class="adm-detail-content-cell-l"><?=Loc::getMessage('AMAS_RSA_PRIVATE_KEY');?></td>
							<td class="adm-detail-content-cell-r">
									<textarea name="<?=$FIELD?>[RSA_PRIVATE_KEY]"
									          cols="85" rows="14"
									          placeholder="<?=Loc::getMessage('AMAS_RSA_PRIVATE_KEY_EXAMPLE');?>"><?=$VALUE['RSA_PRIVATE_KEY'];?></textarea>
							</td>
						</tr>
					</table>
					<?
					$tabControl2->End();
					?>
				</td>
			</tr>
			<?
		}

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