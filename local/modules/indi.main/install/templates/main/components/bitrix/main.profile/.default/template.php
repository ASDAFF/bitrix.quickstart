<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}
?>

<div class="main-profile main-profile-default" id="user-profile">
	<? ShowError($arResult["strProfileError"]) ?>

	<? if ($arResult['DATA_SAVED'] == 'Y') {
		ShowNote(GetMessage('PROFILE_DATA_SAVED'));
	} ?>

	<form class="form form-profile" method="post" action="<?=$arResult['FORM_TARGET']?>?" enctype="multipart/form-data"
	      role="profile">
		<?=$arResult['BX_SESSION_CHECK']?>
		<input type="hidden" name="lang" value="<?=LANG?>" />
		<input type="hidden" name="ID" id="user-id" value="<?=$arResult['ID']?>" />

		<div class="row">
			<div class="col-md-6">
				<div class="form-group js-file-resize">
					<div class="row">
						<div class="col-sm-12">
							<label class="control-label" for="user-photo"><?=GetMessage('USER_PHOTO')?>:</label>

							<input class="form-control" type="file" name="PERSONAL_PHOTO"
							       id="user-photo" value="" />
							<input class="js-image-input js-user-file" type="hidden" name=""
							       id="image-input" value="<?=$arResult['arUser']['PERSONAL_PHOTO']?>" />
						</div>
						<div class="col-sm-12">
							<? if (strlen($arResult['arUser']['PERSONAL_PHOTO']) > 0) {
								?>
								<img class="js-imagerez" data-newimg="<?=$arResult['arUser']['PERSONAL_PHOTO']?>" style="max-width: 100%"
								     data-oldimg="<?=$arResult['arUser']['PERSONAL_PHOTO']?>"
								     src="<?=\CFile::GetPath($arResult['arUser']['PERSONAL_PHOTO']);?>" />
								<?
							} else {
								?>
								<img class="js-imagerez" data-newimg="" data-oldimg="" style="max-width: 100%"
								     src="<?=SITE_TEMPLATE_PATH?>/kaluga.kuzov-auto.ru/images/nophoto.png" />
							<? } ?>

							<?$this->SetViewTarget('personal_avatar');?>
							<div class="modal fade" data-no-delete="true" id="avatar-modal" tabindex="1" role="dialog"
							     aria-hidden="true">
								<div class="modal-dialog modal-lg avatar">
									<div class="modal-content">
										<div class="modal-header">
											<button aria-label="Close" data-dismiss="modal"
											        class="icon icon-close close"
											        type="button"></button>
										</div>
										<div class="modal-body">
											<div class="cropper-layer">
												<img class="js-image-display"
												     data-id="<?=CFile::GetPath($arResult['arUser']['PERSONAL_PHOTO']);?>"
												     src="">
											</div>
											<a href="javascript:" class="js-crop image-crop btn"
											   style="display: none">Обрезать и сохранить
											</a>

										</div>
									</div>
								</div>
							</div>
							<?$this->EndViewTarget();?>
							<?//TODO _ не забывать проверить, что перед закрывающим тегом body - добавлено
							//$APPLICATION->ShowViewContent('personal_avatar');
							//и подключен скрипт обрезки
							//Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/cropper.min.js');
							//Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . '/css/cropper.min.css')
							?>

						</div>
					</div>
				</div>
			</div>
			<div class="col-md-6">

				<div class="form-group">
					<label class="control-label" for="profile-last-name"><?=GetMessage('LAST_NAME')?>:</label>
					<input class="form-control" type="text" name="LAST_NAME" id="profile-last-name"
					       value="<?=$arResult['arUser']['LAST_NAME']?>" autofocus="autofocus" maxlength="50" />
				</div>

				<div class="form-group">+
					<label class="control-label" for="profile-name"><?=GetMessage('NAME')?>:</label>
					<input class="form-control" type="text" name="NAME" id="profile-name"
					       value="<?=$arResult['arUser']['NAME']?>" maxlength="50" />
				</div>

				<div class="form-group">
					<label class="control-label" for="profile-second-name"><?=GetMessage('SECOND_NAME')?>
						:</label>
					<input class="form-control" type="text" name="SECOND_NAME" id="profile-second-name"
					       value="<?=$arResult['arUser']['SECOND_NAME']?>" maxlength="50" />
				</div>

				<div class="form-group">
					<label class="control-label" for="profile-personal-birthday"><?=GetMessage('USER_BIRTHDAY')?>
						:</label>
					<input class="form-control widget datepicker" type="text" name="PERSONAL_BIRTHDAY"
					       id="profile-personal-birthday" value="<?=$arResult['arUser']['PERSONAL_BIRTHDAY']?>"
					       maxlength="50" />
				</div>
				<div class="form-group">
					<label class="control-label" for="profile-personal-mobile"><?=GetMessage('USER_MOBILE')?>:</label>
					<input class="form-control mask-phone" type="tel" name="PERSONAL_MOBILE"
					       id="profile-personal-mobile"
					       value="<?=$arResult['arUser']['PERSONAL_MOBILE']?>" maxlength="50" />
				</div>
				<div class="form-group">
					<label class="control-label required" for="profile-email"><?=GetMessage('EMAIL')?>:</label>
					<input class="form-control" type="email" name="EMAIL" id="profile-email"
					       value="<?=$arResult['arUser']['EMAIL']?>" maxlength="50" required="" />
				</div>
				<div class="form-group has-feedback">
					<label class="control-label" for="profile-new-pass-req"><?=GetMessage('NEW_PASSWORD_REQ')?>:</label>
					<input class="form-control" type="password" name="NEW_PASSWORD" id="profile-new-pass-req" value=""
					       maxlength="50" autocomplete="off" />

					<? if ($arResult['SECURE_AUTH']) {
						?>
						<noscript>
					<span class="glyphicon glyphicon-unlock form-control-feedback"
					      title="<?=GetMessage('AUTH_NONSECURE_NOTE')?>"></span>
						</noscript>
						<script>
							document.write('<span class="glyphicon glyphicon-lock form-control-feedback" title="<?=GetMessage('AUTH_SECURE_NOTE')?>"></span>');
						</script>
						<?
					} else {
						?><span class="glyphicon glyphicon-eye-open form-control-feedback"
						        title="<?=GetMessage('AUTH_SHOW_PASS')?>"
						        data-hide-title="<?=GetMessage('AUTH_HIDE_PASS')?>"></span><?
					} ?>

					<p class="help-block"><?=$arResult['GROUP_POLICY']['PASSWORD_REQUIREMENTS']?></p>
				</div>
			</div>

		</div>


		<? if ($arResult['USER_PROPERTIES']['SHOW'] == 'Y') {
			foreach ($arResult['USER_PROPERTIES']['DATA'] as $fieldName => $field) {
				$domId = 'profile-prop-' . $field['ID'];
				?>
				<div class="form-group">
					<label
						class="control-label field-<?=strtolower($fieldName)?><?=$field['MANDATORY'] == 'Y' ? ' required' : ''?>"
						for="<?=$domId?>"><?=$field['EDIT_FORM_LABEL']?>:</label>
					<? $APPLICATION->IncludeComponent(
						'bitrix:system.field.edit',
						$field['USER_TYPE']['USER_TYPE_ID'],
						array(
							'bVarsFromForm' => $arResult['bVarsFromForm'],
							'arUserField' => $field,
							'domID' => $domId,
						),
						NULL,
						array(
							'HIDE_ICONS' => 'Y',
						)
					); ?>
				</div>
				<?
			}
		} ?>

		<div class="form-group form-toolbar">
			<input type="hidden" name="LOGIN" value="<?=$arResult['arUser']['LOGIN']?>" />
			<input type="hidden" name="NEW_PASSWORD_CONFIRM" value="" />
			<input type="hidden" name="save" value="y" />
			<button class="btn btn-default"
			        type="submit"><?=$arResult['ID'] > 0 ? GetMessage('SAVE') : GetMessage('ADD')?></button>
		</div>

		<div class="form-group form-info">
			<p class="help-block">
				<span class="required"></span><?=GetMessage("PROFILE_REQ")?>
			</p>
		</div>
	</form>
</div>