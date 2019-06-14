<?if(!check_bitrix_sessid()) return;?>
 
<?if(is_array($errors) && count($errors)>0):?>
	<?foreach($errors as $val):?>
		<?$alErrors .= $val."<br>";?>
	<?endforeach;?>
	<?=CAdminMessage::ShowMessage(Array("TYPE"=>"ERROR", "MESSAGE" =>GetMessage('ERR_INST'), "DETAILS"=>$alErrors, "HTML"=>true));?>
<?else:?>
	<?=CAdminMessage::ShowNote(GetMessage('SUCC_INST'));?>
	
	<p><?=GetMessage("HELP_MESS_3")?></p>
	
	<form>
		<input type="button" onclick="WizardWindow.Open('rarus.sms4b:sms4b_module.config','<?=$_REQUEST["sessid"]?>');" value="<?=GetMessage("LOAD_MASTER")?>" />
	</form>
	
	<p><?=GetMessage("INSTALLED")?></p>
	<ul>
		<?if ($_REQUEST["INSTALL_COMPONENTS"] == "Y"):?>
			<li> <b><?=GetMessage('INST_COMP')?></b></li> 
		<?endif;?>
		<?if ($_REQUEST["INSTALL_DEMO"]== "Y"):?>
			<li> <b><?=GetMessage('INST_PUB')?><a href="/sms4b_demo/" target="_blank"><b><?=GetMessage("HERE")?></b></a>;</li>
		<?endif;?>
		<?if ($_REQUEST["INSTALL_HELP"] == "Y"):?>
			<li> <?=GetMessage('INST_HELP')?> </li>
		<?endif;?>
	</ul>
<?endif;?>

<form action="<?=$APPLICATION->GetCurPage()?>">
	<input type="hidden" name="lang" value="<?=LANG?>">
	<input type="submit" name="" value="<?=GetMessage('BACK_TO_LIST')?>">
</form>