<?php
/**
 * Created by PhpStorm.
 * User: ASDAFF
 * Date: 12.10.2017
 * Time: 16:50
 */
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if (isset($_GET['AJAX_PAGE'])) {

    $content = ob_get_contents();
    ob_end_clean();

    $APPLICATION->RestartBuffer();

    list(, $content_html) = explode('<!--RestartBuffer-->', $content);

    echo $content_html;

    die();
}