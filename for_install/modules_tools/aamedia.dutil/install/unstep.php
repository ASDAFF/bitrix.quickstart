<?//удаление модуля
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

if (!check_bitrix_sessid())
    return;

if ($exception = $APPLICATION->GetException())
    echo CAdminMessage::ShowMessage(array(
        "TYPE" => "ERROR",
        "MESSAGE" => Loc::getMessage("AAM_DUTIL_MOD_UNINSTALL_ERROR"),
        "DETAILS" => $exception->GetString(),
        "HTML" => true
    ));
else
    echo CAdminMessage::ShowNote(Loc::getMessage("AAM_DUTIL_MOD_UNINSTALL_OK"));
?>

<form action="<?=$APPLICATION->GetCurPage(); ?>">
    <input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
    <input type="submit" name="" value="<?=Loc::getMessage("AAM_DUTIL_MOD_BACK")?>">
</form>
