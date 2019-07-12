<?
$pathJS = SITE_TEMPLATE_PATH . '/js/';

// nook перенести в модуль
$arTemplateCoreConfig = array(
	'modernizr' => array(
		'js' => $pathJS.'modernizr-2.6.2.min.js',
		'use' => CJSCore::USE_PUBLIC,
	),
	'fancybox' => array(
		'js' => $pathJS.'fancyBox/source/jquery.fancybox.pack.js',
		'css' => $pathJS.'fancyBox/source/jquery.fancybox.css',
		'rel' => array('jquery'),
		'use' => CJSCore::USE_PUBLIC,
	),
	'carousellib' => array(
		'js' => $pathJS.'jquery.carouFredSel-6.0.4-packed.js',
		'rel' => array('jquery'),
		'use' => CJSCore::USE_PUBLIC,
	),
	'carousel' => array(
		'js' => $pathJS.'carousel.js',
		'rel' => array('carousellib'),
		'use' => CJSCore::USE_PUBLIC,
	),
	'yandexmaps_2_1' => array(
		'js' => '//api-maps.yandex.ru/2.1/?lang=ru_RU',
	),
	'realtyAddress' => array(
		'js' => $pathJS.'jquery.citrusRealtyAddress.js',
		'rel' => array('jquery', 'yandexmaps_2_1'),
		'lang' => BX_ROOT . '/modules/citrus.realty/lang/' . LANGUAGE_ID .'/js_messages.php',
		'use' => CJSCore::USE_PUBLIC,
	),
	'citrus_realty' => array(
		'js' => $pathJS.'script.js',
		'rel' => array('jquery', 'fancybox'),
		'lang' => BX_ROOT . '/modules/citrus.realty/lang/' . LANGUAGE_ID .'/js_messages.php',
		'use' => CJSCore::USE_PUBLIC,
	),
);

foreach ($arTemplateCoreConfig as $ext => $arExt)
	CJSCore::RegisterExt($ext, $arExt);
