<?php

/* preload module info */
if (class_exists('mk_rees46') === false) {
	require __DIR__ . '/install/index.php';
}

//if (class_exists('Composer\\Autoload\\ClassLoader') === false) {
//	require __DIR__ . '/classes/Composer/Autoload/ClassLoader.php';
//}

// unobstructively add autoloader
if (function_exists('__autoload')) { // if we have an old autoload func
	spl_autoload_register('__autoload'); // register it
}

// our own autoloader
//$loader = new \Composer\Autoload\ClassLoader();
$loader = require __DIR__ . '/vendor/autoload.php';
$loader->add('Rees46\\', __DIR__ . '/classes/');
//$loader->register(true);

\Rees46\Functions::showRecommenderCSS();
