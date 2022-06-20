
<?
    if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

    $arComponentDescription = array(
        "NAME" => "Наборы",
        "DESCRIPTION" => "Для формирования наборов на главной странице",
        "ICON" => "/images/icon.gif",
        "SORT" => 30,
        "CACHE_PATH" => "Y",
        "PATH" => array(
            "ID" => "CM", // for example "my_project"
            /*"CHILD" => array(
            "ID" => "", // for example "my_project:services"
            "NAME" => "",  // for example "Services"
            ),*/
        ),
        "COMPLEX" => "N",
    );

?>
