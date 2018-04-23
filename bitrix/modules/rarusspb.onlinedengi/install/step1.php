<?
/**
 *
 * Модуль платежного сервиса OnlineDengi для CMS 1С Битрикс.
 * @copyright Сервис OnlineDengi http://www.onlinedengi.ru/ (ООО "КомФинЦентр"), 2010
 *
 */

if(!check_bitrix_sessid()) {
	return;
}

$bAlreadyInstalled = true;
$sModuleId = 'rarusspb.onlinedengi';
$UserPSFilesDirName = 'onlinedengi_payment'; ## д.б. $sModuleId, не хорошо, но всёже
if(empty($GLOBALS['mErrors'])) {
	$bShowCheckbox = false;
	?><form action="<?=$GLOBALS['APPLICATION']->GetCurPage()?>" name="<?=$sModuleId.'_install'?>"><?
		echo bitrix_sessid_post();
       		?><input type="hidden" name="lang" value="<?=LANG?>" /><?
		?><input type="hidden" name="step" value="2" /><?
		?><input type="hidden" name="id" value="<?=$sModuleId?>" /><?
		?><input type="hidden" name="install" value="Y" /><?

		?><br /><?
		echo BeginNote();
		?><p><?
			echo GetMessage('REWRITE_FILES_MSG_1', array('#FILE_PATH#' => $GLOBALS['sPath2UserPSFiles'], '#NAME_DIR#' => $UserPSFilesDirName ));
                ?></p><?
		if(is_dir($_SERVER['DOCUMENT_ROOT'].'/'.$GLOBALS['sPath2UserPSFiles'].'/'.$sModuleId)) {
			?><p><?
				echo '<br /><b>'.GetMessage('MOD_INST_ATTENTION').'</b> ';
				echo GetMessage('REWRITE_FILES_MSG_2');
			?></p><?
			$bShowCheckbox = true;
		}
		echo EndNote();
		if($bShowCheckbox) {
			?><p><?
				?><label><?
					?><input type="checkbox" name="rewrite_files" value="Y" checked="checked" /><?
					echo GetMessage('REWRITE_FILES');
				?></label><?
			?></p><?
		}
		?><p><?
		        ?><input type="submit" name="" value="<?=GetMessage('MOD_STEP_2')?>" /><?
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
			'MESSAGE' => GetMessage('MOD_INST_ERR'), 
			'DETAILS' => $sErrors, 
			'HTML' => true
        	)
        );
        unset($sErrors);
	?><form action="<?=$GLOBALS['APPLICATION']->GetCurPage()?>"><?
		?><p><?
        		?><input type="hidden" name="lang" value="<?=LANG?>" /><?
		        ?><input type="submit" name="" value="<?=GetMessage('MOD_STEP_LIST')?>" /><?
		?></p><?
	?></form><?
}

