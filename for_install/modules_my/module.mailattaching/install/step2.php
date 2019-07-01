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
$bAlreadyInstalled = true;

if(empty($GLOBALS['mErrors'])) {
	echo CAdminMessage::ShowNote($obModule->GetMessage('INSTALL_COMPLETE_OK'));
} else {
	$sErrors = 'Error while installing';
	if(is_array($GLOBALS['mErrors'])) {
		$sErrors = '';
		foreach($GLOBALS['mErrors'] as $sErrMsg) {
			$sErrors .= $sErrMsg.'<br />';
		}
	}
	echo CAdminMessage::ShowMessage(
		array(
			'TYPE' => 'ERROR', 
			'MESSAGE' => $obModule->GetMessage('INSTALL_COMPLETE_ERROR'), 
			'DETAILS' => $sErrors, 
			'HTML' => true
		)
	);
	unset($sErrors);
}

?><form action="<?=$GLOBALS['APPLICATION']->GetCurPage()?>"><?
	?><p><?
		?><input type="hidden" name="lang" value="<?=LANG?>" /><?
		?><input type="submit" name="" value="<?=$obModule->GetMessage('INSTALL_BACK')?>" /><?
	?></p><?
?></form><?
