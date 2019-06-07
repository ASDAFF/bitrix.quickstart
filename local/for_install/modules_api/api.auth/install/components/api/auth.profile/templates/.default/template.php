<?

use \Bitrix\Main\Loader,
	 \Bitrix\Main\Page\Asset,
	 \Bitrix\Main\Localization\Loc,
	 \Bitrix\Main\Page\AssetLocation;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */

$this->setFrameMode(true);

$this->addExternalCss($templateFolder . '/styles.css');

$formId = $arResult['FORM_ID'];
$arLang = (array)Loc::getMessage('API_MAIN_PROFILE_TPL_FIELDS');
$post   = $arResult['POST'];
$user   = $arResult['USER'];
?>
	<div id="<?=$formId?>" class="api-auth-profile">

		<? if($arResult['MESSAGE_DANGER']): ?>
			<div class="api_message api_message_danger"><?=$arResult['MESSAGE_DANGER']?></div>
		<? endif ?>
		<? if($arResult['MESSAGE_SUCCESS']): ?>
			<div class="api_message api_message_success"><?=$arResult['MESSAGE_SUCCESS']?></div>
		<? endif ?>

		<form id="<?=$formId?>_form"
		      enctype="multipart/form-data"
		      method="post"
		      action="<?=POST_FORM_ACTION_URI;?>"
		      class="api_form api_form_horizontal">
			<?=bitrix_sessid_post()?>

			<? if($arParams['READONLY_FIELDS']): ?>
				<div class="api_rows api-readonly-fields">
					<div class="api_row">
						<div class="api_controls">
							<? foreach($arParams['READONLY_FIELDS'] as $key): ?>
								<?
								if(!in_array($key, $arParams['USER_FIELDS']))
									continue;

								$name  = $arLang[ $key ];
								$value = (string)$user[ $key ];

								if($value == 'Y' || $value == 'N') {
									$value = Loc::getMessage('API_MAIN_PROFILE_TPL_VALUE_' . $value);
								}
								if($key == 'PERSONAL_GENDER') {
									$value = Loc::getMessage('API_MAIN_PROFILE_TPL_GENDER_' . $value);
								}
								if($key == 'PERSONAL_WWW' || $key == 'WORK_WWW') {
									if($value)
										$value = '<a href="' . $value . '" target="_blank">' . $value . '</a>';
								}
								if($key == 'LANGUAGE_ID') {
									if($value)
										$value = $arResult['LANGUAGE_LIST'][ $value ]['NAME'];
								}
								if($key == 'PERSONAL_COUNTRY' || $key == 'WORK_COUNTRY') {
									if($value)
										$value = $arResult['COUNTRY_LIST'][ $value ];
								}
								if($key == 'PERSONAL_CITY' || $key == 'WORK_CITY') {
									$value = $user[ $key ];
								}
								?>
								<div class="api_control">
									<span class="api_name"><?=$name?></span> <span class="api_value"><?=$value?></span>
								</div>
							<? endforeach; ?>
						</div>
					</div>
				</div>
				<? unset($key, $name, $value); ?>
			<? endif; ?>

			<? if($arParams['USER_FIELDS']): ?>
				<div class="api_rows api-user-fields">
					<? foreach($arParams['USER_FIELDS'] as $key): ?>
						<?
						if(in_array($key, $arParams['READONLY_FIELDS']))
							continue;

						$name  = $arLang[ $key ];
						$value = $post[ $key ];

						$req = ($arParams['REQUIRED_FIELDS'] && in_array($key, $arParams['REQUIRED_FIELDS']));
						?>
						<div class="api_row api_row_group">
							<? if($arParams['SHOW_LABEL'] == 'Y'): ?>
								<div class="api_label"><?=$name?>:<?=($req ? '<span class="api_required">*</span>' : '')?></div>
							<? endif ?>
							<div class="api_controls">
								<? if($key == 'PERSONAL_GENDER'): ?>
									<select name="FIELDS[<?=$key?>]">
										<option value=""><?=Loc::getMessage('API_MAIN_PROFILE_TPL_OPTION_NOT_SET')?></option>
										<option value="M"<?=$value == 'M' ? ' selected' : ''?>><?=Loc::getMessage('API_MAIN_PROFILE_TPL_GENDER_M')?></option>
										<option value="F"<?=$value == 'F' ? ' selected' : ''?>><?=Loc::getMessage('API_MAIN_PROFILE_TPL_GENDER_F')?></option>
									</select>
								<? elseif($key == 'LANGUAGE_ID'): ?>
									<select name="FIELDS[<?=$key?>]">
										<? foreach($arResult['LANGUAGE_LIST'] as $language): ?>
											<option value="<?=$language['LID']?>"<?=($language['DEF'] == 'Y' || $value == $language['LID'] ? ' selected' : '')?>><?=$language['NAME']?></option>
										<? endforeach; ?>
									</select>
								<? elseif($key == 'PERSONAL_COUNTRY' || $key == 'WORK_COUNTRY'): ?>
									<select name="FIELDS[<?=$key?>]">
										<option value=""><?=Loc::getMessage('API_MAIN_PROFILE_TPL_OPTION_NOT_SET')?></option>
										<? foreach($arResult['COUNTRY_LIST'] as $cId => $cName): ?>
											<option value="<?=$cId?>"<?=($value == $cId ? ' selected' : '')?>><?=$cName?></option>
										<? endforeach; ?>
									</select>
								<? elseif($key == 'PERSONAL_PHOTO' || $key == 'WORK_LOGO'): ?>
									<?
									if($fileId = $user[ $key ]) {
										$arFileTmp = CFile::ResizeImageGet($fileId, array("width" => 150, "height" => 150));
										?>
										<div class="api_control">
											<img src="<?=CUtil::GetAdditionalFileURL($arFileTmp['src'], true)?>" alt="">
										</div>
										<?
									}
									?>
									<div class="api_control">
										<?=\CFile::InputFile($key, 20, $fileId, false, 0, 'IMAGE');?>
									</div>
								<? elseif($key == 'PERSONAL_CITY' || $key == 'WORK_CITY'): ?>
									<? if(Loader::includeModule('sale') && \CSaleLocation::isLocationProEnabled()): ?>
										<? \CSaleLocation::proxySaleAjaxLocationsComponent(
											 array(),
											 array(
													"CODE"            => $post[ $key ],
													"INPUT_NAME"      => 'FIELDS[' . $key . ']',
													"PROVIDE_LINK_BY" => 'code',
											 ),
											 '',
											 true,
											 'api_location'
										); ?>
									<? else: ?>
										<input type="text" name="FIELDS[<?=$key?>]" value="<?=$value?>">
									<? endif ?>
								<? elseif($key == 'PERSONAL_NOTES' || $key == 'WORK_NOTES'): ?>
									<textarea name="FIELDS[<?=$key?>]" cols="30" rows="4"><?=$value?></textarea>
								<? elseif($key == 'PASSWORD' || $key == 'CONFIRM_PASSWORD'): ?>
									<input type="password" name="FIELDS[<?=$key?>]" value="<?=$value?>">
									<? if($key == 'PASSWORD' && $arResult['GROUP_POLICY'] && $arResult['GROUP_POLICY']['PASSWORD_REQUIREMENTS']): ?>
										<div class="api-group-policy"><?=$arResult['GROUP_POLICY']['PASSWORD_REQUIREMENTS']?></div>
									<? endif; ?>
								<? else: ?>
									<input type="text" name="FIELDS[<?=$key?>]" value="<?=$value?>">
								<? endif; ?>
							</div>
						</div>
					<? endforeach; ?>

					<? if($arResult['TIME_ZONE_ENABLED']): ?>
						<div class="api_row">
							<div class="api_label"><?=$arLang['AUTO_TIME_ZONE']?>:</div>
							<div class="api_controls">
								<select name="FIELDS[AUTO_TIME_ZONE]">
									<option value=""><?=Loc::getMessage('API_MAIN_PROFILE_TPL_OPTION_DEFULT')?></option>
									<option value="Y"<?=($post['AUTO_TIME_ZONE'] == 'Y' ? ' selected' : '')?>><?=Loc::getMessage('API_MAIN_PROFILE_TPL_AUTO_TIME_ZONE_Y')?></option>
									<option value="N"<?=($post['AUTO_TIME_ZONE'] == 'N' ? ' selected' : '')?>><?=Loc::getMessage('API_MAIN_PROFILE_TPL_AUTO_TIME_ZONE_N')?></option>
								</select>
							</div>
						</div>
						<div class="api_row">
							<div class="api_label"><?=$arLang['TIME_ZONE']?>:</div>
							<div class="api_control">
								<select name="FIELDS[TIME_ZONE]"<?=($post['AUTO_TIME_ZONE'] <> 'N' ? ' disabled=""' : '')?>>
									<? foreach($arResult['TIME_ZONE_LIST'] as $tz => $tz_name): ?>
										<option value="<?=htmlspecialcharsbx($tz)?>"<?=($post['TIME_ZONE'] == $tz ? ' selected' : '')?>><?=htmlspecialcharsbx($tz_name)?></option>
									<? endforeach ?>
								</select>
							</div>
						</div>
					<? endif ?>

				</div>
				<? unset($key, $name, $value, $req); ?>
			<? endif; ?>

			<? if($arParams['CUSTOM_FIELDS']): ?>
				<div class="api_rows api-custom-fields">
					<?
					$arUserFields = $GLOBALS['USER_FIELD_MANAGER']->GetUserFields('USER', $arResult['ID'], LANGUAGE_ID);
					foreach($arUserFields as $key => $arUserField) {
						if(!in_array($key, $arParams['CUSTOM_FIELDS']))
							continue;

						$name = $arUserField['EDIT_FORM_LABEL'] ? $arUserField['EDIT_FORM_LABEL'] : $arUserField['FIELD_NAME'];
						$req  = ($arUserField['MANDATORY'] == 'Y' || ($arParams['REQUIRED_FIELDS'] && in_array($key, $arParams['REQUIRED_FIELDS'])));

						/*if(!$arUserField['VALUE'] && $arUserField['USER_TYPE_ID'] == 'api_auth_location'){
							if(Loader::includeModule('sale')){
								if(class_exists('\Bitrix\Main\Service\GeoIp\Manager') && class_exists('\Bitrix\Sale\Location\GeoIp')) {
									if($ipAddress = \Bitrix\Main\Service\GeoIp\Manager::getRealIp())
										$arUserField['VALUE'] = \Bitrix\Sale\Location\GeoIp::getLocationCode($ipAddress, LANGUAGE_ID);
								}
							}
						}*/
						?>
						<div class="api_row">
							<? if($arParams['SHOW_LABEL'] == 'Y'): ?>
								<div class="api_label"><?=$name?><?=($req ? '<span class="api_required">*</span>' : '')?></div>
							<? endif; ?>
							<div class="api_controls">
								<?
								$APPLICATION->IncludeComponent(
									 'bitrix:system.field.edit',
									 $arUserField['USER_TYPE']['USER_TYPE_ID'],
									 array(
											'bVarsFromForm' => false,
											'arUserField'   => $arUserField
									 ),
									 null,
									 array('HIDE_ICONS' => 'Y')
								); ?>
							</div>
						</div>
						<?
					}
					unset($key, $name, $value, $req, $arUserFields, $arUserField);
					?>
				</div>
			<? endif ?>

			<div class="api_row api_buttons">
				<div class="api_label"></div>
				<div class="api_controls">
					<button type="submit" value="Y" class="api_button api_button_primary"><?=Loc::getMessage('API_MAIN_PROFILE_TPL_BUTTON')?></button>
				</div>
			</div>

		</form>
		<? if(Loader::includeModule('socialservices')): ?>
			<div class="bx-socserv-auth-split">
				<? $APPLICATION->IncludeComponent(
					 "bitrix:socserv.auth.split",
					 "",
					 array(
							"SHOW_PROFILES" => "Y",
							"ALLOW_DELETE"  => "Y",
					 ),
					 false,
					 array('HIDE_ICONS' => 'Y')
				); ?>
			</div>
		<? endif; ?>
	</div>
<?
ob_start();
?>
	<script type="text/javascript">
		jQuery(document).ready(function ($) {
			var form = '#<?=$formId?>';

			<? if($arResult['TIME_ZONE_ENABLED']): ?>
			$(form).on('change', '[name="FIELDS[AUTO_TIME_ZONE]"]', function () {
				if (this.value != 'N') {
					$(form + ' [name="FIELDS[TIME_ZONE]"]').prop('disabled', true).val('');
				}
				else {
					$(form + ' [name="FIELDS[TIME_ZONE]"]').prop('disabled', false);
				}

			});
			<? endif ?>

		});
	</script>
<?
$script = ob_get_contents();
ob_end_clean();
Asset::getInstance()->addString($script, true, AssetLocation::AFTER_JS);
?>