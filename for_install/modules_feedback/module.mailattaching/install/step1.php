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
	$bShowCheckbox = false;
	?><form action="<?=$GLOBALS['APPLICATION']->GetCurPage()?>"><?
		echo bitrix_sessid_post();
		?><input type="hidden" name="lang" value="<?=LANG?>" /><?
		?><input type="hidden" name="step" value="2" /><?
		?><input type="hidden" name="id" value="<?=$sModuleId?>" /><?
		?><input type="hidden" name="install" value="Y" /><?
		
		if(function_exists('custom_mail')) {
			echo CAdminMessage::ShowMessage($obModule->GetMessage('NOT_EFFECT_WARNING'));
		}

		?><p><?
		        ?><input type="submit" name="" value="<?=$obModule->GetMessage('MOD_STEP_2')?>" /><?
		?></p><?
	?></form><?
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
			'MESSAGE' => $obModule->GetMessage('MOD_INST_ERR'), 
			'DETAILS' => $sErrors, 
			'HTML' => true
		)
	);
	unset($sErrors);
	?><form action="<?=$GLOBALS['APPLICATION']->GetCurPage()?>"><?
		?><p><?
        		?><input type="hidden" name="lang" value="<?=LANG?>" /><?
			?><input type="submit" name="" value="<?=$obModule->GetMessage('MOD_STEP_LIST')?>" /><?
		?></p><?
	?></form><?
}
