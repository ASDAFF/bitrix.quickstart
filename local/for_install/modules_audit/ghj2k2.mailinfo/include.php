<?php
global $DB;
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/ghj2k2.mailinfo/classes/general/mailinfo.php");

function getStatus ($status='N') {
	
	$returnStatus='';
	switch($status) {
		case 'Y':
		  $returnStatus = '<img src="/bitrix/themes/.default/icons/ghj2k2.mailinfo/task-done.png">';
		  break;
		case 'F';
		  $returnStatus = '<img src="/bitrix/themes/.default/icons/ghj2k2.mailinfo/task-error.png">';
		  break;
		case 'P':
		  $returnStatus = '<img src="/bitrix/themes/.default/icons/ghj2k2.mailinfo/task-alert.png">';
		  break;
		case '0':
		  $returnStatus = '<img src="/bitrix/themes/.default/icons/ghj2k2.mailinfo/task-none.png">';
		  break;
		case 'N':
		  $returnStatus = '<img src="/bitrix/themes/.default/icons/ghj2k2.mailinfo/task-wait.png">';
		  break;    
	}
	return $returnStatus;
}
?>