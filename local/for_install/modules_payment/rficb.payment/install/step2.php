<?if(!check_bitrix_sessid()) return;?>
<?

if(is_array($errors) && count($errors)>0):
	foreach($errors as $val)
		$alErrors .= $val."<br>";
	echo CAdminMessage::ShowMessage(Array("TYPE"=>"ERROR", "MESSAGE" =>GetMessage("MOD_INST_ERR"), "DETAILS"=>$alErrors, "HTML"=>true));
else:
	echo CAdminMessage::ShowNote(GetMessage("MOD_INST_OK"));
endif;
?>
<form action="<?echo $APPLICATION->GetCurPage()?>">
<p>
	<input type="hidden" name="lang" value="<?echo LANG?>">
	<a href="/bitrix/admin/settings.php?lang=ru&mid=rficb.payment&mid_menu=1">
		<?=GetMessage("RFICB.PAYMENT_GO_TO")?>
	</a>
	<br/>
	<br/>
	<input type="submit" name="" value="<?echo GetMessage("MOD_BACK")?>">
</p>
</form>