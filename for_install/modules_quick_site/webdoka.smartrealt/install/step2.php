<?php
/**
* ###################################
* # Copyright (c) 2012 SmartRealt   #
* # http://www.smartrealt.com       #
* # mailto:info@smartrealt.com      #
* ###################################
*/

$module_id = 'webdoka.smartrealt';

if (! check_bitrix_sessid ())
	return;

if ($ex = $APPLICATION->GetException ()) {
	echo CAdminMessage::ShowMessage ( Array ("TYPE" => "ERROR", "MESSAGE" => GetMessage ( "MOD_INST_ERR" ), "HTML" => true, "DETAILS" => $ex->GetString () ) );
} else {
	echo CAdminMessage::ShowNote ( GetMessage ( "SMARTREALT_MOD_INST_OK" ) );
	
}
$arSessId = explode('=', bitrix_sessid_get());
?>
<form action="/bitrix/admin/wizard_install.php" method="get">
    <input type="hidden" name="lang" value="<?php echo LANG?>"> 
    <input type="hidden" name="<?php echo $arSessId[0]?>" value="<?php echo $arSessId[1]?>"> 
	<input type="hidden" name="wizardName" value="webdoka.smartrealt:webdoka:smartrealt"> 
    <input type="button" onclick="onBackClick()" value="<?php echo GetMessage ( "MOD_BACK" )?>">
	<input type="submit" name="" value="<?php echo GetMessage ( "SMARTREALT_START_MASTER" )?>">
<form>
<script type="text/javascript">
function onBackClick()
{
    window.location.reload();
}
</script>
