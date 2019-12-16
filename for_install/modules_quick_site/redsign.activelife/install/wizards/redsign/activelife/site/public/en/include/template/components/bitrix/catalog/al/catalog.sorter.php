<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();?>

<?$APPLICATION->IncludeComponent(
    "redsign:catalog.sorter", 
    "catalog", 
    array(
        "COMPONENT_TEMPLATE" => "catalog",
        "ALFA_ACTION_PARAM_NAME" => "alfaction",
        "ALFA_ACTION_PARAM_VALUE" => "alfavalue",
        "ALFA_CHOSE_TEMPLATES_SHOW" => "N",
        "ALFA_DEFAULT_TEMPLATE" => "catalog_blocks",
        "ALFA_SORT_BY_SHOW" => "Y",
        "ALFA_SORT_BY_NAME" => array(
            0 => "sort",
            1 => "name",
            2 => "PROPERTY_PROD_PRICE_FALSE",
            3 => "",
        ),
        "ALFA_SORT_BY_DEFAULT" => "sort_asc",
        "ALFA_OUTPUT_OF_SHOW" => "Y",
        "ALFA_OUTPUT_OF" => array(
            0 => "20",
            1 => "16",
            2 => "40",
            3 => "80",
            4 => "",
        ),
        "ALFA_OUTPUT_OF_DEFAULT" => "16",
        "ALFA_OUTPUT_OF_SHOW_ALL" => "Y",
        "ALFA_SHORT_SORTER" => "N",
        "TEMPLATE_AJAXID" => "catalog"
    ),
    null
);?>