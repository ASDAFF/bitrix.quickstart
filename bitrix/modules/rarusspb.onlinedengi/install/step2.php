<?
/**
 *
 * Модуль платежного сервиса OnlineDengi для CMS 1С Битрикс.
 * @copyright Сервис OnlineDengi http://www.onlinedengi.ru/ (ООО "КомФинЦентр"), 2010
 *
 */

if(!check_bitrix_sessid()) return;

$bAlreadyInstalled = true;

if(empty($GLOBALS['mErrors'])) {
        echo CAdminMessage::ShowNote(GetMessage('MOD_INST_OK'));
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
			'MESSAGE' =>GetMessage('MOD_INST_ERR'), 
			'DETAILS' => $sErrors, 
			'HTML' => true
        	)
        );
        unset($sErrors);
}

?><form action="<?=$GLOBALS['APPLICATION']->GetCurPage()?>"><?
	?><p><?
        	?><input type="hidden" name="lang" value="<?=LANG?>" /><?
	        ?><input type="submit" name="" value="<?=GetMessage('MOD_BACK')?>" /><?
	?></p><?
?></form><?