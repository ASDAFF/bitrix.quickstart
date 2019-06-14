<?php
use \Bitrix\Main\EventManager;

EventManager::getInstance()->addEventHandler(
    'main',
    'OnEndBufferContent',
    array('\Altasib\Starterkit\Debug\Functions', 'clearPre')
);

EventManager::getInstance()->addEventHandler(
    'main',
    'OnProlog',
    array('\Altasib\Starterkit\Debug\Functions', 'changeDevStatus')
);

EventManager::getInstance()->addEventHandler(
    'main',
    'OnEpilog',
    array('\Altasib\Starterkit\Debug\Functions', 'devTaskOnEpilog')
);

EventManager::getInstance()->addEventHandler(
    'main',
    'OnPageStart',
    array('\Altasib\Starterkit\Debug\Functions', 'devTaskOnPageStart')
);