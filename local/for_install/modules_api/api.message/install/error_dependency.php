<?
use \Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

CAdminMessage::ShowMessage(
	Loc::getMessage('ASM_MODULE_DEPENDENCY_ERROR',array('SM_VERSION'=>SM_VERSION))
);
?>