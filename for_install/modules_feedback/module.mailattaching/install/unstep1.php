<?
if(!check_bitrix_sessid()) {
	return;
}
if(!empty($GLOBALS['_INSTALLING_MODULE_OBJ_'])) {
	$obModule =& $GLOBALS['_INSTALLING_MODULE_OBJ_'];
	$sModuleId = $obModule->MODULE_ID;
} else {
	return;
}

if(!empty($GLOBALS['mErrors'])) {
	if(is_array($GLOBALS['mErrors'])) {
		$sErrors = '';
		foreach($GLOBALS['mErrors'] as $sErrMsg) {
			$sErrors .= $sErrMsg.'<br />';
		}
	}
	echo CAdminMessage::ShowMessage(
		array(
			'TYPE' => 'ERROR', 
			'MESSAGE' => $obModule->GetMessage('UNINSTALL_ERROR'), 
			'DETAILS' => $sErrors, 
			'HTML' => true
		)
	);

	?><form action="<?=$GLOBALS['APPLICATION']->GetCurPage()?>"><?
		?><p><?
			?><input type="hidden" name="lang" value="<?=LANG?>" /><?
			?><input type="submit" name="" value="<?=$obModule->GetMessage('INSTALL_BACK')?>" /><?
		?></p><?
	?><form><?
} else {
	?><form action="<?=$GLOBALS['APPLICATION']->GetCurPage()?>"><?
		echo bitrix_sessid_post();
		?><input type="hidden" name="lang" value="<?=LANG?>" /><?
		?><input type="hidden" name="id" value="<?=$sModuleId?>" /><?
		?><input type="hidden" name="uninstall" value="Y" /><?
		?><input type="hidden" name="step" value="2" /><?
		echo CAdminMessage::ShowMessage($obModule->GetMessage('UNINSTALL_WARNING'));
		?><input type="submit" name="inst" value="<?=$obModule->GetMessage('UNINSTALL_DEL')?>" /><?
	?></form><?
}
