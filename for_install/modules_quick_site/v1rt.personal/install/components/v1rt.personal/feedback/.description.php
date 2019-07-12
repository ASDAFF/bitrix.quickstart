<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$arComponentDescription = array(
        "NAME" => GetMessage("V1RT_NAME"),
        "DESCRIPTION" => GetMessage("V1RT_DESC"),
        "ICON" => "/images/component.gif",
        "SORT" => 20,
        "CACHE_PATH" => "Y",
        "PATH" => array(
                "ID" => "v1rt.ru",
                "SORT" => 2000,
                "CHILD" => array(
                        "ID" => "v1rt_service",
                        "NAME" => GetMessage("V1RT_LISTERG_DISC_NAME_CHILD"),
                        "SORT" => 30,
                        "CHILD" => array(
                                "ID" => "v1rt_feedback",
                        ),
                ),
        ),
);
?>