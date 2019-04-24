<?php

return ;

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

use Lema\Common\AssetManager,
    Lema\Common\Request;


//init js & css
$assetManager = new AssetManager();

$assetManager
    ->addCssArray(array(
        SITE_TEMPLATE_PATH . '/js/jquery-ui/jquery-ui.css',
        SITE_TEMPLATE_PATH . '/js/fancybox/jquery.fancybox.css',
        SITE_TEMPLATE_PATH . '/js/owl/owl.carousel.min.css',
        SITE_TEMPLATE_PATH . '/js/owl/owl.theme.default.min.css',
        SITE_TEMPLATE_PATH . '/css/style.css'
    ))

    ->init(array('fx', 'jquery2'))

    ->addJsArray(array(
        SITE_TEMPLATE_PATH . '/js/jquery-ui/jquery-ui.js',
        SITE_TEMPLATE_PATH . '/js/fancybox/jquery.fancybox.pack.js',
        SITE_TEMPLATE_PATH . '/js/owl/owl.carousel.min.js',
        SITE_TEMPLATE_PATH . '/js/scripts.js',
    ));

 /*logo with link or without */ ?>
<?if(Request::get()->getRequestUri() == '/'):?>
    <img src="<?=SITE_TEMPLATE_PATH?>/images/logo.png">
<?else:?>
    <a href="/">
        <img src="<?=SITE_TEMPLATE_PATH?>/images/logo.png">
    </a>
<?endif;?>
