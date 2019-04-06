<?if(!check_bitrix_sessid()) return;?>

<?if($errors === false):?>
	<?=CAdminMessage::ShowNote(GetMessage('UNINST_SUCCESS'));?>
<?else:?>
	<?for($i=0; $i<count($errors); $i++)
		$alErrors .= $errors[$i]."<br>";?>
	<?=CAdminMessage::ShowMessage(Array("TYPE"=>"ERROR", "MESSAGE" =>GetMessage('UNINSTALL_ERROR'), "DETAILS"=>$alErrors, "HTML"=>true));?>
<?endif;?>

<form action="<?=$APPLICATION->GetCurPage()?>">
	<input type="hidden" name="lang" value="<?=LANG?>">
	<input type="submit" name="" value="<?=GetMessage('BACK_TO_MOD_LIST')?>">	
</form>
