<?php
/**
 * @category	
 * @link		http://.ru
 * @revision	$Revision: 2062 $
 * @date		$Date: 2014-10-23 14:18:32 +0400 (Чт, 23 окт 2014) $
 */

require_once 'all_prolog.php';

if (!\Bitrix\Main\Loader::includeModule('subscribe')) {
	die();
}

//Генерация выпусков
$maxCount = 10;
while ($maxCount > 0 && \CPostingTemplate::Execute()) {
	$maxCount--;
}

//Рассылка выпусков
$posting = new \CPosting();
$posting->AutoSend();

require_once 'all_epilog.php';