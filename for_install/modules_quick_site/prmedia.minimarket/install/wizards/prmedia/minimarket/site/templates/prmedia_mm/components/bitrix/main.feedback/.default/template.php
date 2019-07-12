<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<?php
/**
 * Bitrix vars
 *
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 * @global CMain $APPLICATION
 * @global CUser $USER
 */
?>

<div class="feedback" id="feedback">
	<?php
	if (!empty($arResult['ERROR_MESSAGE']))
	{
		foreach ($arResult['ERROR_MESSAGE'] as $v)
		{
			ShowError($v);
		}
	}
	if (strlen($arResult['OK_MESSAGE']) > 0)
	{
		?>
		<div class="ok-text"><?php echo $arResult['OK_MESSAGE'] ?></div>
		<?php
	}
	?>

	<form action="<?php echo POST_FORM_ACTION_URI ?>#feedback" method="POST">
		<?php echo bitrix_sessid_post() ?>
		<div class="feedback-field">
			<label for="user_name">
				<?php echo GetMessage('MFT_NAME') ?><?php if (empty($arParams['REQUIRED_FIELDS']) || in_array('NAME', $arParams['REQUIRED_FIELDS'])): ?> <span class="required">*</span><?php endif ?>:
			</label>
			<input id="user_name" type="text" name="user_name" value="<?php echo $arResult['AUTHOR_NAME'] ?>">
		</div>
		<div class="feedback-field">
			<label for="user_email">
				<?php echo GetMessage('MFT_EMAIL') ?><?php if (empty($arParams['REQUIRED_FIELDS']) || in_array('EMAIL', $arParams['REQUIRED_FIELDS'])): ?> <span class="required">*</span><?php endif ?>:
			</label>
			<input id="user_email" type="text" name="user_email" value="<?php echo $arResult['AUTHOR_EMAIL'] ?>">
		</div>
		<div class="feedback-field">
			<label for="message">
				<?php echo GetMessage('MFT_MESSAGE') ?><?php if (empty($arParams['REQUIRED_FIELDS']) || in_array('MESSAGE', $arParams['REQUIRED_FIELDS'])): ?> <span class="required">*</span><?php endif ?>:
			</label>
			<textarea id="message" name="MESSAGE" rows="5" cols="40"><?php echo $arResult['MESSAGE'] ?></textarea>
		</div>
		<?php if ($arParams['USE_CAPTCHA'] == 'Y'): ?>
			<div class="feedback-field feedback-field-captcha">
				<input type="hidden" name="captcha_sid" value="<?php echo $arResult['capCode'] ?>">
				<img src="/bitrix/tools/captcha.php?captcha_sid=<?php echo $arResult['capCode'] ?>" alt="CAPTCHA">
				<label for="captcha_word"><?php echo GetMessage('MFT_CAPTCHA_CODE') ?> <span class="required">*</span>:</label>
				<input id="captcha_word" type="text" name="captcha_word" size="30" maxlength="50" value="">
			</div>
		<?php endif; ?>
		<input type="hidden" name="PARAMS_HASH" value="<?php echo $arResult['PARAMS_HASH'] ?>">
		<input type="submit" name="submit" value="<?php echo GetMessage('MFT_SUBMIT') ?>">
	</form>
</div>