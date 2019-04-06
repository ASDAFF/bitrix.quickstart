<?if(!check_bitrix_sessid()) return;?>

<?if(is_array($errors) && count($errors)>0):?>
	<?foreach($errors as $val):?>
		<?$alErrors .= $val."<br>";?>
	<?endforeach;?>
	<?=CAdminMessage::ShowMessage(Array("TYPE"=>"ERROR", "MESSAGE" =>GetMessage('TTSML_ERR_INST'), "DETAILS"=>$alErrors, "HTML"=>true));?>
<?else:?>
	<?=CAdminMessage::ShowNote(GetMessage('TTSML_SUCC_INST'));?>
<?endif;?>

<form action="<?=$APPLICATION->GetCurPage()?>">
	<input type="hidden" name="lang" value="<?=LANG?>">
	<input type="submit" name="" value="<?=GetMessage('TTSML_BACK_TO_LIST')?>">
</form>