<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

echo '<form id="'.$arResult['FORM_ID'].'" class="unif-form" action="'.$arResult['COMPONENT_PATH'].'/ajax.php" method="POST" enctype="multipart/form-data">
		<div class="success">'.$arParams['OK_TEXT'].'</div>';

// поля
foreach($arResult['FIELDS'] as $fieldName => $fieldData)
{
	if ($fieldName != 'EMAIL_TO')
	{
		if ($fieldData['TYPE'] != 'HIDDEN')
		{
			echo    '<div id="'.ToLower($fieldName).'" class="field"'.($arParams[$fieldName.'_MASK'] ? ' data-mask="'.$arParams[$fieldName.'_MASK'].'"' : '').'>
						<span class="label'.($fieldData['REQUIRED'] ? ' required' : '').'">'.$fieldData['LABEL'].'</span>
						'.$fieldData['HTML'].'
						<span class="errors"></span>
					</div>';
		}
		else
		{
			echo $fieldData['HTML'];
		}
	}
}

// Поля для антиспама
if ($arParams['ENABLE_HIDDEN_ANTISPAM_FIELDS'] == 'Y')
{
	foreach($arResult['ANTISPAM_FIELDS'] as $antiSpamFieldName)
	{
		echo '<div id="'.ToLower($fieldName).'" class="field importantField">
				<input type="text" name="FIELDS['.$antiSpamFieldName.']">
			 </div>';
	}
}

// подписка
if($arResult['SIGN'] != '')
{
	echo    '<div class="field sign">
				<span class="label">'.$arResult['SIGN']['LABEL'].'</span>
				'.$arResult['SIGN']['HTML'].'
			</div>';
}

// капча
if($arParams['USE_CAPTCHA'])
{
	echo	'<div id="captcha" class="field captcha">
				<input type="hidden" name="CAPTCHA_SID" value="'.$arResult['CAPTCHA_CODE'].'">
				<span class="label required">'.GetMessage('UNIF_CAPTCHA_CODE').'</span>
				<input type="text" name="CAPTCHA_WORD" value="">
				<span class="errors"></span>
				<img src="/bitrix/tools/captcha.php?captcha_sid='.$arResult['CAPTCHA_CODE'].'" width="180" height="40" alt="CAPTCHA">
				<span class="new-captcha">'.GetMessage('UNIF_NEW_CAPTCHA').'</span>
			</div>';
}

echo    '<div class="field submit">
			<input type="submit" name="submit" value="'.GetMessage('UNIF_SUBMIT').'">
		</div>
		<input type="hidden" name="REQUEST_TYPE" value="SEND">
		<input type="hidden" name="FORM_ID" value="'.$arResult['FORM_ID'].'">
		'.bitrix_sessid_post().'
		</form>

		<script type="text/javascript">
			var unifMessages = {
				FIELD_EMPTY: \''.GetMessage('UNIF_FIELD_EMPTY').'\',
				EMAIL_WRONG: \''.GetMessage('UNIF_EMAIL_WRONG').'\',
				DATE_TIME_INTERVAL_WRONG: \''.GetMessage('UNIF_DATE_TIME_INTERVAL_WRONG').'\',
				DATE_TIME_WRONG: \''.GetMessage('UNIF_DATE_TIME_WRONG').'\',
				FILE_WRONG: \''.GetMessage('UNIF_FILE_WRONG').'\',
				CAPTCHA_WRONG: \''.GetMessage('UNIF_CAPTCHA_WRONG').'\',
				CAPTCHA_EMPTY: \''.GetMessage('UNIF_CAPTCHA_EMPTY').'\'
			};
		</script>';
?>