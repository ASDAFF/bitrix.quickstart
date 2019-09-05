<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}
?>

<div class="subscribtion subscribtion-default"><?
	if (!$arResult["NOTES"]) {
		if ( $arParams['SHOW_FORM_TITLE'] ) {
			?><p><?=( $arResult["UNSUBSCRIBE"] == "Y" ) ? $arParams['FORM_UNSUBSCRIBE_TITLE'] : $arParams['FORM_SUBSCRIBE_TITLE'] ?></p><?
		}
		?><form method="POST" action="?#subscribtion-form" role="subscription" class="form form-subscription js-subscribe <?=$arParams['FORM_CLASS']?>">
			<input type="hidden" name="SENT" value="Y"/><?
			if( $arResult["UNSUBSCRIBE"] == "Y" ) {
				?><input type="hidden" name="unsubscribe" value="<?=htmlspecialcharsbx($_REQUEST["unsubscribe"])?>"/><?
			}

			if( $arParams['SHOW_LABEL'] ){
				?><label for="">Подписаться на новости и акции</label><?
			}

			?><input type="email" class="field field_transparent" name="EMAIL" value="<?=htmlspecialcharsbx($_REQUEST["EMAIL"])?>" placeholder="Ваш email"><?

			if ($arResult["UNSUBSCRIBE"] == "Y") {
				?><button class="btn <?=$arParams['BUTTON_CLASS']?>" type="submit">Отписаться</button><?
			} else {
				?><button class="btn <?=$arParams['BUTTON_CLASS']?>" type="submit">Подписаться</button><?
			}
		?></form><?
	}

	else{
		?><div class="js-subscribe-notification js-subscribe-success notification notification_success hidden"><?
				ShowNote(implode("\n", $arResult["NOTES"]));
		?></div><?
	}
	if ($arResult["ERRORS"]) {
		?><div class="js-subscribe-notification js-subscribe-errors notification notification_warning hidden"><?ShowError(implode("\n", $arResult["ERRORS"]))?></div><?
	}
?></div>