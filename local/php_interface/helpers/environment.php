<?php
/**
 * Created by PhpStorm.
 * User: ASDAFF
 * Date: 02.02.2019
 * Time: 4:01
 */

/**
 *
 * Если используется apache - то необходимо прописать в .htaccess в виде строчки (требуется модуль apache - mod_env)
 * SetEnv BITRIX_ENV "dev.local"
 *
 * Если используется php-fpm то в конфигубиции nginx прописать
 * fastcgi_param BITRIX_ENV "dev.local";
 *
 */

// Определим все возможные окружения
Environment::add(array(
    'dev' => array('dev.local', 'release.local'), // Тестовые сервера
    'www' => array('www.local'), // Боевой сервер
));

// Если это окружение разработки
if (Environment::has('dev')) {

}//\\ if