<?// IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/askaron.ibvote/install/install.php"); ?>
<form action="<?echo $APPLICATION->GetCurPage()?>">
<?=bitrix_sessid_post()?>
	<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
	<input type="hidden" name="id" value="askaron.agents">
	<input type="hidden" name="uninstall" value="Y">
	<input type="hidden" name="step" value="2">
	<?echo CAdminMessage::ShowMessage(GetMessage("MOD_UNINST_WARN"))?>
	
	
	<?$check_agents = COption::GetOptionString("main", "check_agents", "Y");?>
	
	<?if ($check_agents==="Y"):?>
		<p><?=GetMessage("ASKARON_AGENTS_CHECK_ENABLED")?></p>
	<?else:?>
		<p><?=GetMessage("ASKARON_AGENTS_CHECK_DISABLED")?></p>
		<p><input type="checkbox" name="check_agents" id="check_agents" value="Y" checked="checked" />&nbsp;<label for="check_agents"><?echo GetMessage("ASKARON_AGENTS_CHECK_AGENTS")?></label></p>		
	<?endif?>
		
	<input type="submit" name="inst" value="<?echo GetMessage("MOD_UNINST_DEL")?>">
</form>