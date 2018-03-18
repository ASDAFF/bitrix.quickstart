<?php
/**
 * Страница с результатом установки модуля.
 * @author r.smoliarenko
 * @author r.sarazhyn
 */
if (!check_bitrix_sessid()) {
	return;
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/uniteller.sale/prolog.php'); // пролог модуля

global $errors;

if ($errors === false || !CModule::IncludeModule('uniteller.sale')) {
	echo CAdminMessage::ShowNote(GetMessage('UNITELLER.SALE_INSTALL_MESSAGE'));
} else {
	for ($i = 0; $i < count($errors); $i++) {
		$alErrors .= $errors[$i] . '<br>';
	}
	echo CAdminMessage::ShowMessage(Array('TYPE' => 'ERROR', 'MESSAGE' => GetMessage('MOD_INST_ERR'), 'DETAILS' => $alErrors, 'HTML' => true));
};

?>
<form action="<?= $APPLICATION->GetCurPage() ?>">
	<input type="hidden" name="lang" value="<?= LANG ?>">
	<input type="submit" name="" value="<?= GetMessage('MOD_BACK') ?>">
<?php if ($errors === false): ?>
	<input type="button" onclick="document.location='/bitrix/admin/settings.php?lang=<?= LANGUAGE_ID ?>&mid_menu=1&mid=uniteller.sale'" value="<?= GetMessage('UNITELLER.SALE_BTN_HELP')?>">
<?php endif; ?>
</form>