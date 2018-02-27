<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

$GLOBALS['APPLICATION']->AddHeadScript($templateFolder . '/script.js');
$GLOBALS['APPLICATION']->SetAdditionalCSS($templateFolder . '/style.css');
?>
