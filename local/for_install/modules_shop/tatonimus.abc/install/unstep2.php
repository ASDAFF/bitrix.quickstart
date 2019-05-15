<?if(!check_bitrix_sessid()) return;?>

<?if($errors === false):?>
	<?=CAdminMessage::ShowNote(GetMessage('TABC_UNINSTALL_SUCCESS'));?>
<?else:?>
	<?for($i=0; $i<count($errors); $i++)
		$alErrors .= $errors[$i]."<br>";?>
	<?=CAdminMessage::ShowMessage(Array("TYPE"=>"ERROR", "MESSAGE" =>GetMessage('TABC_UNINSTALL_ERROR'), "DETAILS"=>$alErrors, "HTML"=>true));?>
<?endif;?>

<form action="<?=$APPLICATION->GetCurPage()?>">
	<input type="hidden" name="lang" value="<?=LANG?>">
	<input type="submit" name="" value="<?=GetMessage('TABC_BACK_TO_MOD_LIST')?>">
</form>
