<?
use \Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

htmlspecialcharsback(CAdminMessage::ShowMessage(Loc::getMessage('AFEX_INSTALL_DEPENDENCY_ERROR')));
?>