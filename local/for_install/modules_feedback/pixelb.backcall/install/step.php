<?php
if(!check_bitrix_sessid()) return;


use Bitrix\Main\Localization\Loc;


Loc::loadMessages(__FILE__);

echo CAdminMessage::ShowNote(Loc::getMessage('MODULE_INSTALL_SUCCESS'));
?>