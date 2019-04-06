<?if(!check_bitrix_sessid()) return;?>
<?IncludeModuleLangFile(__FILE__);?>
<form action="<?echo $APPLICATION->GetCurPage()?>">
	<input type="hidden" name="lang" value="<?echo LANG?>">
	<span style='font-size: small; color:red;'><?=GetMessage("IPOLMO_DELETE_DONE")?></span><br>
	<input type="submit" name="" value="Ok">
</form>