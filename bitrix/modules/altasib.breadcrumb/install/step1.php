<?
#################################################
#   Company developer: ALTASIB                  #
#   Developer: Eremchenko Alexey                #
#   Site: http://www.altasib.ru                 #
#   E-mail: info@altasib.ru                     #
#   Copyright (c) 2006-2014 ALTASIB             #
#################################################
?>
<?if(!check_bitrix_sessid()) return;?>
<?
IncludeModuleLangFile(__FILE__);
global $errors;
$errors = is_array($errors) ? $errors : array();

if(is_array($errors) && count($errors)>0):
        foreach($errors as $val)
                $alErrors .= $val."<br>";
        echo CAdminMessage::ShowMessage(Array("TYPE"=>"ERROR", "MESSAGE" =>GetMessage("MOD_INST_ERR"), "DETAILS"=>$alErrors, "HTML"=>true));
else:
        echo CAdminMessage::ShowNote(GetMessage("MOD_INST_OK"));
endif;

?>
<form action="<?echo $APPLICATION->GetCurPage()?>">
        <input type="hidden" name="lang" value="<?echo LANG?>">
        <input type="submit" name="" value="<?echo GetMessage("MOD_BACK")?>">
</form>
