<?php

// encoding: WINDOWS-1251

$checkStatus = ' <a href="http://triggmine.cloudapp.net/" target="_blank">Проверьте статус вашей интеграции</a> либо обратитесь в <a href="http://triggmine.cloudapp.net/Support" target="_blank">техподдержку</a>';
$contactSupport = ' Обратитесь в <a href="http://triggmine.cloudapp.net/Support" target="_blank">техподдержку</a> TriggMine.';
$getApiUrl = ' Правильный API URL вы можете узнать в <a href="http://triggmine.cloudapp.net/Integration" target="_blank">настройках интеграции TriggMine</a>';
$getApiKey = ' Ваш ключ вы можете узнать в <a href="http://triggmine.cloudapp.net/Integration" target="_blank">настройках интеграции TriggMine</a>';

$MESS['failed_to_activate'] = 'Не удалось активировать модуль.' . $checkStatus;
$MESS['failed_to_deactivate'] = 'Не удалось деактивировать модуль.' . $checkStatus;
$MESS['low_version'] = 'TriggMine не совместим с Bitrix версии ниже %1';
$MESS['missing_module'] = 'Необходимый модуль Bitrix %1 не установлен';
$MESS['missing_function'] = 'Необходимая Bitrix функция %1 недоступна';
$MESS['no_transport'] = 'Опция \'allow_url_fopen\' выключена в вашем php.ini и расширение cURL не установлено!' . $contactSupport;
$MESS['empty_api_url'] = 'Укажите, пожалуйста, API URL. API URL не может быть пустым.' . $getApiUrl;
$MESS['invalid_api_url'] = '%1 не является корректным API URL.' . $getApiUrl;
$MESS['empty_api_key'] = 'API ключ не может быть пустым. Укажите API ключ для работы модуля.' . $getApiKey;
$MESS['no_access_to_api'] = 'Нет доступа к API URL (%1). Проверьте правильность API URL.' . $getApiUrl;
$MESS['invalid_response_from_api'] = 'Нет доступа к API URL %1.' . $contactSupport;
$MESS['invalid_token'] = 'Указанный API ключ не корректен.' . $getApiKey;
$MESS['api_returns_error'] = 'TriggMine API возвращает ошибку %1.' . $contactSupport;
$MESS['plugin_cannot_be_active'] = 'Модуль не может быть активирован';
$MESS['plugin_can_be_active'] = 'Модуль выключен. Включите модуль, для того чтобы он начал работу.';
$MESS['wrong_cart_url'] = 'Путь к корзине не является корректным URL';