<?php
/**
 * @var $name
 * @var $title
 * @var $description
 *
 */
?>

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$arComponentDescription = array(
    "NAME" => "<?=$title?>",
    "DESCRIPTION" => "<?=$description?>",
    "PATH" => array(
        "ID" => "utility",
        "CHILD" => array(
            "ID" => "mvc",
            "NAME" => "MVC"
        )
    ),
);
?>