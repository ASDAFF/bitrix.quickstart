<?
#################################################
#   Company developer: ALTASIB                  #
#   Developer: Eremchenko Alexey                #
#   Site: http://www.altasib.ru                 #
#   E-mail: info@altasib.ru                     #
#   Copyright (c) 2006-2014 ALTASIB             #
#################################################
?>
<?
IncludeModuleLangFile(__FILE__);
?>
<form action="<?echo $APPLICATION->GetCurPage()?>">
        <?=bitrix_sessid_post()?>
        <input type="hidden" name="lang" value="<?echo LANG?>">
        <input type="hidden" name="id" value="altasib.breadcrumb">
        <input type="hidden" name="uninstall" value="Y">
        <input type="hidden" name="step" value="2">
        <?echo CAdminMessage::ShowMessage(GetMessage("MOD_UNINST_WARN"))?>
        <input type="submit" name="inst" value="<?echo GetMessage("MOD_UNINST_DEL")?>">
</form>
