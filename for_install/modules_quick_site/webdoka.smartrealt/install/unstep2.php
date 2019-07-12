<?php
$module_id = 'webdoka.smartrealt';
?>
<?php

if (! check_bitrix_sessid ())
	return;
?>
<?php

if ($ex = $APPLICATION->GetException ()) {
	echo CAdminMessage::ShowMessage ( Array ("TYPE" => "ERROR", "MESSAGE" => GetMessage ( "MOD_UNINST_ERR" ), "HTML" => true, "DETAILS" => $ex->GetString () ) );
} else {
	echo CAdminMessage::ShowNote ( GetMessage ( "SMARTREALT_MOD_UNINST_OK" ) );
}

?>
<form action="<?php echo $APPLICATION->GetCurPage ()?>">
	<input type="hidden" name="lang" value="<?php echo LANG?>"> 
	<input type="submit" name="" value="<?php echo GetMessage ( "MOD_BACK" )?>">
<form>