<?php
/**
 * Created by PhpStorm.
 * User: ASDAFF
 * Date: 26.02.2018
 * Time: 20:56
 */

/**
 * Тут описаны все js библиотеки, которые можно подключить через CJsCore
 */

$arLibs = array(

    /**
     * 'Название библиотеки' => array( // Стоит давать осмысленное название так, чтобы оно было понятно всем разработчикам
     *      'js'                => '', // Путь до библиотеки от корня сайта
     *      'css'               => '', // Путь до css файла библиотеки от корня сайта. Может быть массивом
     *      'lang'              => '', // Путь до обычного lang файла с php массивом, который будет транслирован в js
     *      'rel'               => '', // массив библиотек, от которых зависит данная библиотека
     *      'use'               => '', // CJSCore::USE_PUBLIC || CJSCore::USE_ADMIN,
     *      'skip_core'         => '', // отключает необходимость загрузки ядра JS битрикс
     *      'lang_additional'   => '', // Путь до дополнительного lang файла с php массивом, который будет транслирован в js
     * )
     *
     * Для подключения зарегистрированной библиотеки на какой-то странице сайта, используйте конструкцию:
     *      CJSCore::Init(array('library_name'));
     * Ваша библиотека будет подключена с использованием AddHeadScript.
     */

    'jquery_3' => array(
        'js' => PATH_BOWER_COMPONENTS . '/jquery/dist/jquery.min.js',
    ),
    'jquery_fancybox' => array(
        'js' => PATH_BOWER_COMPONENTS . '/fancybox/dist/jquery.fancybox.min.js',
        'css' => PATH_BOWER_COMPONENTS . '/fancybox/dist/jquery.fancybox.min.css',
        'rel' => array('jquery'),
    ),
    'owl_carousel' => array(
        'js' => PATH_BOWER_COMPONENTS . '/owl.carousel/dist/owl.carousel.min.js',
        'css' => PATH_BOWER_COMPONENTS . '/owl.carousel/dist/assets/owl.carousel.min.css',
        'rel' => array('jquery'),
    ),
    'jquery_mousewheel' => array(
        'js' => PATH_LIBRARY . '/jquery_mousewheel/jquery.mousewheel.min.js',
        'rel' => array('jquery'),
    ),
    'jquery_touchSwipe' => array(
        'js' => PATH_BOWER_COMPONENTS . '/jquery-touchswipe/jquery.touchSwipe.min.js',
        'rel' => array('jquery'),
    ),
    'jquery_sudoSlider' => array(
        'js' => PATH_LIBRARY . '/jquery_sudoSlider/jquery.sudoSlider.min.js',
        'rel' => array('jquery'),
    ),
    'mask_input' => array(
        'js' => PATH_LIBRARY . '/mask_input/mask.input.js',
        'rel' => array('jquery'),
    ),
    'jquery_validate' => array(
        'js' => PATH_BOWER_COMPONENTS . '/jquery-validation/dist/jquery.validate.min.js',
        'rel' => array('jquery'),
    )
);

foreach ($arLibs as $libName => $arLib) {
    if (!isset($arLib['skip_core'])) {
        $arLib['skip_core'] = true;
    }
    //Проверка на имя из ядра. Не будем давать подключать библиотеку с неправильным именем
    //чтобы имя всегда соответствовало ключу массива, иначе битрикс его подменит, сделав удаление всех неугодных ему символов
    if (!preg_match('~[a-z0-9_]+~', $libName)) {
        throw new \Exception('Попытка зарегистрировать библиотеку с некорректным именем - "' . $libName . '"');
    }

    if (strlen($arLib['js']) === 0) {
        throw new \Exception('Попытка зарегистрировать библиотеку без js файла - "' . $libName . '"');
    }

    CJSCore::RegisterExt($libName, $arLib);
}