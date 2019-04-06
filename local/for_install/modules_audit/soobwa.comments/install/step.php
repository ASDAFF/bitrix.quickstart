<?if(!check_bitrix_sessid()) return;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

echo CAdminMessage::ShowNote(Loc::getMessage('SOOBWA_COMMENTS_INSTALL_STEP_MESSAGE'));
?>