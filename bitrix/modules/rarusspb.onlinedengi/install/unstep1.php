<?
/**
 *
 * Модуль платежного сервиса OnlineDengi для CMS 1С Битрикс.
 * @copyright Сервис OnlineDengi http://www.onlinedengi.ru/ (ООО "КомФинЦентр"), 2010
 *
 */

$sCurModule = 'rarusspb.onlinedengi';

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
			'MESSAGE' =>GetMessage('MOD_UNINST_ERR'), 
			'DETAILS' => $sErrors, 
			'HTML' => true
        	)
        );

	?><form action="<?=$GLOBALS['APPLICATION']->GetCurPage()?>"><?
		?><p><?
	        	?><input type="hidden" name="lang" value="<?=LANG?>" /><?
	        	?><input type="submit" name="" value="<?=GetMessage('MOD_BACK')?>" /><?
		?></p><?
	?><form><?
} else {
	?><form action="<?=$GLOBALS['APPLICATION']->GetCurPage()?>"><?
		echo bitrix_sessid_post();
		?><input type="hidden" name="lang" value="<?=LANG?>" /><?
		?><input type="hidden" name="id" value="<?=$sCurModule?>" /><?
		?><input type="hidden" name="uninstall" value="Y" /><?
		?><input type="hidden" name="step" value="2" /><?
		echo CAdminMessage::ShowMessage(GetMessage('MOD_UNINST_WARN'));
		?><p><input type="checkbox" name="savefiles" id="savefiles" value="Y" /><label for="savefiles"><?=GetMessage('MOD_UNINST_SAVE_FILES', array('#FILE_PATH#' => $GLOBALS['sPath2UserPSFiles']))?></label></p><?
		?><input type="submit" name="inst" value="<?echo GetMessage("MOD_UNINST_DEL")?>" /><?
	?></form><?
}