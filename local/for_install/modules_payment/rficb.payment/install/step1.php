<?if(!check_bitrix_sessid()) return;?>
<?
if(is_array($errors) && count($errors)>0):
	foreach($errors as $val)
		$alErrors .= $val."<br>";
	echo CAdminMessage::ShowMessage(Array("TYPE"=>"ERROR", "MESSAGE" =>
            GetMessage("MOD_INST_ERR"), "DETAILS"=>$alErrors, "HTML"=>true));
endif;

$arAllOptions = Array(
    Array("key", GetMessage("RFICB.PAYMENT_OPTIONS_KEY")." ",
         GetMessage("RFICB.PAYMENT_OPTIONS_KEY_DESC")),
    Array("secret_key", GetMessage("RFICB.PAYMENT_OPTIONS_SECRET_KEY")." ",
         GetMessage("RFICB.PAYMENT_OPTIONS_SECRET_KEY_DESC")),
);
?>
<form action="<?echo $APPLICATION->GetCurPage()?>" name="form1">
<?=bitrix_sessid_post()?>
<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
<input type="hidden" name="id" value="rficb.payment">
<input type="hidden" name="install" value="Y">
<input type="hidden" name="step" value="2">
<input type="submit" name="inst" value="<?= GetMessage("MOD_INSTALL")?>">
</form>
