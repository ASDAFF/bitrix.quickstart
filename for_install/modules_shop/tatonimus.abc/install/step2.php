<?if(!check_bitrix_sessid()) return;?>

<?if(is_array($errors) && count($errors)>0):?>
	<?foreach($errors as $val):?>
		<?$alErrors .= $val."<br>";?>
	<?endforeach;?>
	<?=CAdminMessage::ShowMessage(Array("TYPE"=>"ERROR", "MESSAGE" =>GetMessage('TABC_ERR_INST'), "DETAILS"=>$alErrors, "HTML"=>true));?>
<?else:?>
	<?=CAdminMessage::ShowNote(GetMessage('TABC_SUCC_INST'));?>
<?endif;?>

<form action="<?=$APPLICATION->GetCurPage()?>">
	<input type="hidden" name="lang" value="<?=LANG?>">
	<input type="submit" name="" value="<?=GetMessage('TABC_BACK_TO_LIST')?>">
</form>