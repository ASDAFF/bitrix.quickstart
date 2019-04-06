<?php
if(!check_bitrix_sessid()) return;
IncludeModuleLangFile(__FILE__);

global $errors, $messages;
$errors = is_array($errors) ? $errors : array();
$messages = is_array($messages) ? $messages : array();

if(empty($errors)){
        foreach($messages as $val)
                $alMessages .= $val."<br>";
        echo CAdminMessage::ShowNote($alMessages . GetMessage("MOD_INST_OK"));
}else{
        for($i=0; $i<count($errors); $i++)
                $alErrors .= $errors[$i]."<br>";
        echo CAdminMessage::ShowMessage(Array("TYPE"=>"ERROR", "MESSAGE" =>GetMessage("MOD_UNINST_ERR"), "DETAILS"=>$alErrors, "HTML"=>true));
}

?>
<form action="<?echo $APPLICATION->GetCurPage()?>">
        <input type="hidden" name="lang" value="<?echo LANG?>">
        <input type="submit" name="" value="<?echo GetMessage("MOD_BACK")?>">
</form>
