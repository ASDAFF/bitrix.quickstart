<?php

use Bitrix\Main\Localization\Loc;

global $APPLICATION, $errors;

if (empty($errors))
    echo \CAdminMessage::ShowNote(Loc::getMessage("MOD_INST_OK"));
else
    echo \CAdminMessage::ShowMessage(
        array(
            "TYPE"      => "ERROR",
            "MESSAGE"   => Loc::getMessage("MOD_INST_ERR"),
            "DETAILS"   => implode("<br/>", $errors),
            "HTML"      => true
        ));

?><form action="<?echo $APPLICATION->GetCurPage()?>">
	<input type="hidden" name="lang" value="<?=LANG?>">
	<input type="submit" name="" value="<?=Loc::getMessage("MOD_BACK")?>">
<form>