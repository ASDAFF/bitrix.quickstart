<?php
if(!check_bitrix_sessid()) return;
IncludeModuleLangFile(__FILE__);

global $errors, $messages;
$errors = is_array($errors) ? $errors : array();
$messages = is_array($messages) ? $messages : array();

if(is_array($errors) && count($errors)>0){
        foreach($errors as $val)
                $alErrors .= $val."<br>";
        echo CAdminMessage::ShowMessage(Array("TYPE"=>"ERROR", "MESSAGE" =>GetMessage("MOD_INST_ERR"), "DETAILS"=>$alErrors, "HTML"=>true));
}else{
        foreach($messages as $val)
                $alMessages .= $val."<br>";
        echo CAdminMessage::ShowNote($alMessages . GetMessage("MOD_INST_OK"));
}

?>
<form action="<?echo $APPLICATION->GetCurPage()?>">
        <input type="hidden" name="lang" value="<?echo LANG?>">
        <input type="submit" name="" value="<?echo GetMessage("MOD_BACK")?>">
</form>

