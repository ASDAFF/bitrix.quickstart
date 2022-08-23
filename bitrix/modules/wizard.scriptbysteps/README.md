Модуль позволяет реализовать пошаговую обработку скриптов.

Пошаговое выполнение работает в 3-х режимов - обычный(на странице), cron, ajax.

1) Устанавливаем модуль

2) Для запуска пошаговой обработки необходимо создать файл который будет отвечать за пошаговость(step.php), затем создать папку и в нее уже положить скрипты которые должны выполнять по шагам.

3) Пример
```php

   //Инициализация скрипта
   $documentRoot = (!isset($_SERVER['DOCUMENT_ROOT']) || empty($_SERVER['DOCUMENT_ROOT'])) ? realpath(
   __DIR__ . '/../..'
   ) : $_SERVER['DOCUMENT_ROOT'];//Устанавливаем путь к корневой папке сайта
   $moduleDir = 'local';//bitrix - установка папки с утсановленным модулем
   $pathToModule = $documentRoot . '/' . $moduleDir . '/modules./wizard.scriptbysteps';//путь до модуля
   $pathToModuleInclude = $pathToModule. '/include';//путь до папки с include
   //Подключаем пролог с установленными переменными
   $path = $pathToModuleInclude . '/startAjax.php';
   if (file_exists($path))
   require_once($path);
   else
   die();
   unset($path, $documentRoot);

\Bitrix\Main\Loader::includeSharewareModule('wizard.scriptbysteps');
\Bitrix\Main\Loader::includeSharewareModule('wizard.additional');

//Инициализация логгера
$logger = new Wizard\Additional\Logger;
$logger->activate();
$logger->setType('normalize');

//Шаги выполнения, расположенные по порядку
$arSteps = [
'unzip',//Распаковка архива
'reindex',//Переиндексация
//'updateFacet',//Обновление фасеты
'clearCache',//Очистка кеша
];
//Инициализация параметров для пошаговых скриптов - не обязательно - но это то что будет доступно в скриптах
$arParams = [
'IBLOCK_ID'        => 4,
//Инфоблок товаров
'OFFER_IBLOCK_ID'  => 6,
//Инфорблок торговых предложений
'EXEC_DELETE_FILE' => $_SERVER['DOCUMENT_ROOT'] . '/wizard/files/execDelete.data',
//Путь к файлу для запуска удаления
'BLOCK_PARTIAL'    => $_SERVER['DOCUMENT_ROOT'] . '/wizard/files/blockPartial.data',
//Путь к блокирующему файлу частичной выгрузки
'ARCHIVE_FILE'     => $_SERVER['DOCUMENT_ROOT'] . '/upload/images/123.zip',
//Путь к файлу с архивом
'BEGIN_STEP'       => 'unzip',
//Начальный шаг, обязательный параметр
'PATH_TO_MODULE'   => $pathToModule
//Путь к модулю
];
//Параметры прерывания, устанавливается если есть дополнительное условие на прерывание всей обработки или шага
//доступны параметры begin и step
$arStepParams = [
'cancel' => [
'begin' => (bool)!file_exists($arParams['ARCHIVE_FILE'])
]
];
//Инициализация скрипта
$obScriptBySteps = \Wizard\ScriptBySteps::getInstance();
$obScriptBySteps->setOperationCode('normalize');//Установка типа обработки обычно совпадает с названием файла и обязан совпадать с название папки
$obScriptBySteps->setScriptFilesFolder(__DIR__);//Устанавливаем раздел где будет искаться папка с пошаговыми скриптами
if (is_object($logger))
$obScriptBySteps->setLogger($logger);//Устанавливаем логгер чтобы он был доступен в пошаговых скриптах
$obScriptBySteps->setTimeLimit(60);//Устанавливаем ограничение шага по времени
$obScriptBySteps->setElementLimit(2000);//Устанавливаем ограничение шага по элементам
if (isset($arSteps) && is_array($arSteps) && !empty($arSteps)) {
$obScriptBySteps->setSteps($arSteps);//Устанавливаем шаги
unset($arSteps);
if (isset($arParams) && is_array($arParams) && !empty($arParams)) {
$obScriptBySteps->setParams((array)$arParams);//Устанавливаем параметры
unset($arParams);
$obScriptBySteps->setMainScriptFile(__FILE__);//Устанавливаем основной файл запуска
$obScriptBySteps->execute($arStepParams);//запускаем, запуск может быть без параметров
unset($arStepParams);
}
}

//Завершение скрипта
//define('HAS_WORK_WITH_COOKIE', true); //ставить парметр если используем Куки
$path = $pathToModuleInclude . '/endAjax.php';
if (file_exists($path))
require_once($path);
```

4) Файл обработчик
```php
   $obScriptBySteps = \Wizard\ScriptBySteps::getInstance();
   $arParams = $obScriptBySteps->getParams();//Получаем параметры если нужны
   $stepParams = $obScriptBySteps->getStepParams();//получаем значение шага если нужно
   $logger = $obScriptBySteps->getLogger();//получаем логгер

//Выполняем необходимые действия

\Bitrix\Main\Loader::includeModule('iblock');
if (!\Bitrix\Main\Loader::includeModule('search')) {
if (is_object($logger)) {
$logger->write('Модуль поиска не установлен');
}
//Завершение скрипта - досрочное, т.е. идет полное прерывае скрипта, если нужно пропустить шаг то нужно просто поставить параметр bHaveEls=false
$obScriptBySteps->finishExecute();
die();
}

if (is_object($logger)) {
$logger->activate();
}

//Установка начального времени 5мин + 3секунды
$importTimeBegin = \ConvertTimeStamp(START_SCRIPT_TIME - $arParams['MINUTES'] * 60 + 3, "FULL");
//Получение времени последнего обновления или установка начального времени
$lastUpdate = \Bitrix\Main\Config\Option::get('main', 'last_update_normalizePartial', $importTimeBegin);
if (intval($lastUpdate) == 0) {
$lastUpdate = $importTimeBegin;
}
$lastUpdate = \Bitrix\Main\Type\DateTime::createFromTimestamp($lastUpdate);
//Получение элементов начиная с определенного времени
$arFilter = [
'>TIMESTAMP_X'       => $lastUpdate,
'IBLOCK_ID'          => $arParams['IBLOCK_ID'],
'!IBLOCK_SECTION_ID' => $arParams['OMEGA_SECTION_ID'],
'ACTIVE'             => 'Y'
];

$arSelectFields = ["ID"];
$elementLimit = $obScriptBySteps->getElementLimit();//Получение ограничения по количеству, так же можно получить ограничение по времени
$params = [
'select' => $arSelectFields,
'filter' => $arFilter,
'limit'  => $elementLimit
];
if ($stepParams['iNumPage'] > 1) {
$params['offset'] = $elementLimit * ($stepParams['iNumPage'] - 1);
}
$rsElements = \Bitrix\Iblock\ElementTable::getList($params);

//Переиндексация
$arItems = [];
while ($arElement = $rsElements->fetch()) {
$arItems[] = $arElement['ID'];
}
unset($rsElements);
$countItems = count($arItems);

if ($countItems > 0) {
if ($stepParams['iNumPage'] <= 1) {
$logger->write('Переиндексация элементов начата');
}
//Переиндексация по элементам
$totalEls = 0;
if (!empty($arItems)) {
foreach ($arItems as $id) {
$totalEls++;
//Обновление обычного индекса
\CIBlockElement::UpdateSearch($id, false);
//Обновление фасетного индекса элемента
\Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex($arParams['IBLOCK_ID'], $id);
}
}
//if (is_object($logger)){
//$logger->write('Переиндексировано '.$totalEls.' элементов за '.(time() - START_SCRIPT_TIME).'с');
//$logger->writeEndLine();
//}

//Установка следующего шага
$paramsStep = [
'bHaveEls' => true//параметр true означает что будет запущен этот же шаг повторно но с наращивание счетчика шага
];
$obScriptBySteps->setNewStep($paramsStep);
} else {
if ($stepParams['iNumPage'] <= 1) {
$obScriptBySteps->finishExecute();
die();
} else {
//Установка следующего шага
$paramsStep = [
'bHaveEls' => false//переход на следующий шаг
];
$obScriptBySteps->setNewStep($paramsStep);
$logger->write('Переиндексация элементов завершена');
$logger->writeEndLine();
}
}
```