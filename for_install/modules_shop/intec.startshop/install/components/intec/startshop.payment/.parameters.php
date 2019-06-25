<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?if (!CModule::IncludeModule('intec.startshop')) return;?>
<?
    $arComponentParameters = array(
        "GROUPS" => array(),
        "PARAMETERS" => array(
            "REQUEST_VARIABLE_ACTION" => array(
                "NAME" => GetMessage("SP_REQUEST_VARIABLE_ACTION"),
                "TYPE" => "STRING",
                "DEFAULT" => "action"
            ),
            "REQUEST_VARIABLE_PAYMENT" => array(
                "NAME" => GetMessage("SP_REQUEST_VARIABLE_PAYMENT"),
                "TYPE" => "STRING",
                "DEFAULT" => "payment"
            ),
            "REQUEST_VARIABLE_VALUE_RESULT" => array(
                "NAME" => GetMessage("SP_REQUEST_VARIABLE_VALUE_RESULT"),
                "TYPE" => "STRING",
                "DEFAULT" => "result"
            ),
            "REQUEST_VARIABLE_VALUE_SUCCESS" => array(
                "NAME" => GetMessage("SP_REQUEST_VARIABLE_VALUE_SUCCESS"),
                "TYPE" => "STRING",
                "DEFAULT" => "success"
            ),
            "REQUEST_VARIABLE_VALUE_FAIL" => array(
                "NAME" => GetMessage("SP_REQUEST_VARIABLE_VALUE_FAIL"),
                "TYPE" => "STRING",
                "DEFAULT" => "fail"
            )
        )
    );
?>
