<?php

IncludeModuleLangFile(__FILE__);

if (!check_bitrix_sessid()) {
	return;
}

CAdminMessage::ShowNote(GetMessage('REES_MODULE_INSTALLED'));

?>
<form action="<?= $APPLICATION->GetCurPage() ?>">
	<input type="hidden" name="lang" value="<?= LANG ?>">
	<input type="submit" value="<?= GetMessage('MOD_BACK') ?>">
<form>
