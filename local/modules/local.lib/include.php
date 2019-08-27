<?php
/**
 * Copyright (c) 27/8/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

spl_autoload_register(function ($class) {
    $prefix = 'Local\\';
    $base_dir = __DIR__ . '/lib/';

    $len = strlen($prefix);

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});
