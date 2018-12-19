<?
use Bitrix\Main\EventManager;

include_once "lib.php";

EventManager::getInstance()->addEventHandler(
    'main',
    'OnEndBufferContent',
    array('\Altasib\Pagespeed\Optimize\Image', 'onEndBufferContentHandler')
);
EventManager::getInstance()->addEventHandler(
    'main',
    'OnProlog',
    array('\Altasib\Pagespeed\Optimize\Image', 'onPrologHandler')
);

