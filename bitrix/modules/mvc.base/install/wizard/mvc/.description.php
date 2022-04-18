<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

$arWizardDescription = Array(
    "NAME" => GetMessage("MVC_WIZARD"),
    "DESCRIPTION" => GetMessage("MVC_WIZARD_DESCRIPTION"),
    "VERSION" => "0.2",
    "STEPS" => array(
        "DescriptionStep",
        "RoutesStep",
        "ReviewStep",
        "SuccessStep",
        "CancelStep"
    )
);
?>