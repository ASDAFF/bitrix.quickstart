<?php
/**
 * @category	
 * @link		http://.ru
 * @revision	$Revision$
 * @date		$Date$
 */

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

use Site\Main\Iblock;
use Site\Main\Util;

//Получение инстанса инфоблока разными способами
$news = Iblock\Content\News::getInstance();
Util::debug($news);

$news = Iblock\Prototype::getInstance('Content_News');
Util::debug($news);

$news = Iblock\Prototype::getInstance(1);
Util::debug($news);

//Получение данных инфоблока
Util::debug($news->getId());
Util::debug($news->getCode());
Util::debug($news->getData());
Util::debug($news->getProperties());

//Вызов нестанартного метода
Util::debug($news->getLastElementWithPicture());