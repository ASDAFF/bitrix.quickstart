<?php
/**
 * @category	
 * @link		http://.ru
 * @revision	$Revision$
 * @date		$Date$
 */

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

use Site\Main;

/**
* Использование обработчика изображений
*/

//Ресайз, источник - файл (обновляется original.jpg)
$handler = new Main\Image\Handler();
$handler->useOriginal = false;
$handler->width = 200;
$handler->height = 200;
$handler->originalFileName = __DIR__ . '/images/original.jpg';
$handler->donorFileName = __DIR__ . '/images/donor.jpg';
$handler->defaultFileName = __DIR__ . '/images/default.jpg';
$handler->execute();
Main\Util::debug(\CFile::MakeFileArray($handler->originalFileName));

//Ресайз, источник - класс \CFile (создается новая запись \CFile)
$element = Main\Iblock\Content\News::getInstance()->getElementById(1);
$handler = new Main\Image\Handler();
$handler->useOriginal = false;
$handler->width = 100;
$handler->height = 100;
$handler->originalFileId = 0;
$handler->donorFileId = $element['DETAIL_PICTURE']['ID'];
$handler->defaultFileId = $element['PREVIEW_PICTURE']['ID'];
$handler->execute();
Main\Util::debug(\CFile::MakeFileArray($handler->originalFileId));
Main\Util::debug(\CFile::GetFileArray($handler->originalFileId));

//Ресайз, источник - элемент инфоблока (обновляется св-во PICT)
$handler = new Main\Image\Handler();
$handler->useOriginal = true;
$handler->width = 50;
$handler->height = 50;
$handler->iblockElementId = 1;
$handler->originalIblockField = 'PICT';
$handler->donorIblockField = 'DETAIL_PICTURE';
$handler->defaultIblockField = 'PREVIEW_PICTURE';
$handler->execute();
Main\Util::debug($handler);

//Автоматический ресайз для любого элемента инфоблока (обновляется св-во PICT)
$handler = new Main\Image\Handler();
$handler->width = 150;
$handler->height = 150;
$handler->originalIblockField = 'PICT';
$handler->donorIblockField = 'DETAIL_PICTURE';
$handler->defaultIblockField = 'PREVIEW_PICTURE';
Main\Iblock\Content\News::getInstance()->addImageHandler($handler);