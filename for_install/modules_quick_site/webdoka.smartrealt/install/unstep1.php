<?php
$module_id = 'webdoka.smartrealt';
?>

<form action="<?php echo $APPLICATION->GetCurPage ()?>">
	<?php echo bitrix_sessid_post ()?>
	<input type="hidden" name="lang" value="<? echo LANGUAGE_ID?>"> 
	<input type="hidden" name="id" value="<?php	echo $module_id?>"> 
	<input type="hidden" name="uninstall" value="Y"> 
	<input type="hidden" name="step" value="2">
	<?php echo CAdminMessage::ShowMessage ( GetMessage ( "SMARTREALT_MOD_UNINST_WARN" ) )?>
	<p><?php echo GetMessage ( "MOD_UNINST_SAVE" )?></p>
	<p>
		<input type="checkbox" name="savedata" id="savedata" value="Y" checked>
		<label for="savedata"><? echo GetMessage ( "SMARTREALT_MOD_UNINST_SAVE_TABLES" )?></label>
	</p>
	<input type="submit" name="inst" value="<? echo GetMessage ( "SMARTREALT_MOD_UNINST_DEL" )?>">
</form>