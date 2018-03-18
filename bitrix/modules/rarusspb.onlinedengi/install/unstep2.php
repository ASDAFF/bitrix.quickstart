<?
/**
 *
 * Модуль платежного сервиса OnlineDengi для CMS 1С Битрикс.
 * @copyright Сервис OnlineDengi http://www.onlinedengi.ru/ (ООО "КомФинЦентр"), 2010
 *
 */

if(empty($GLOBALS['mErrors'])) {
        echo CAdminMessage::ShowNote(GetMessage('MOD_UNINST_OK'));
} else {
	$sErrors = 'Error while uninstalling';
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
        unset($sErrors);
}

?><form action="<?=$GLOBALS['APPLICATION']->GetCurPage()?>"><?
	?><p><?
        	?><input type="hidden" name="lang" value="<?=LANG?>" /><?
	        ?><input type="submit" name="" value="<?=GetMessage('MOD_BACK')?>" /><?
	?></p><?
?><form><?
