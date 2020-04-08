<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}
?>

<div class="system-auth-confirmation system-auth-confirmation-default">
	<?
	ShowNote($arResult['MESSAGE_TEXT']);
	
	//here you can place your own messages
	switch ($arResult['MESSAGE_CODE']) {
		case 'E01':
			?><? //When user not found
			break;
		case 'E02':
			?><? //User was successfully authorized after confirmation
			break;
		case 'E03':
			?><? //User already confirm his registration
			break;
		case 'E04':
			?><? //Missed confirmation code
			break;
		case 'E05':
			?><? //Confirmation code provided does not match stored one
			break;
		case 'E06':
			?><? //Confirmation was successfull
			break;
		case 'E07':
			?><? //Some error occured during confirmation
			break;
	}
	
	if ($arResult['SHOW_FORM']) {
		?>
		<form class="form form-confirmation" method="post" action="<?=$arResult['FORM_ACTION']?>" role="confirmation">
			<div class="form-group">
				<label class="control-label required" for="confirmation-login"><?=GetMessage('CT_BSAC_LOGIN')?>:</label>
				<input class="form-control" type="email" name="<?=$arParams['LOGIN']?>" id="confirmation-login" value="<?=$arResult['LOGIN'] ? $arResult['LOGIN'] : $arResult['USER']['LOGIN']?>" maxlength="50" required=""/>
			</div>
			<div class="form-group">
				<label class="control-label required" for="confirmation-code"><?=GetMessage('CT_BSAC_CONFIRM_CODE')?>:</label>
				<input class="form-control" type="text" name="<?=$arParams['CONFIRM_CODE']?>" id="confirmation-code" value="<?=$arResult['CONFIRM_CODE']?>" maxlength="50" required=""/>
			</div>
			<div class="form-group form-toolbar">
				<input class="btn btn-default" type="submit" value="<?=GetMessage('CT_BSAC_CONFIRM')?>"/>
				<input type="hidden" name="<?=$arParams['USER_ID']?>" value="<?=$arResult['USER_ID']?>"/>
			</div>
		</form>
		<?
	} elseif (!$USER->IsAuthorized()) {
		$APPLICATION->IncludeComponent(
			'bitrix:system.auth.authorize',
			'',
			array()
		);
	}
	?>
</div>