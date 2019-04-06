<link rel="stylesheet" type="text/css" href="<?= str_replace($_SERVER['DOCUMENT_ROOT'], "", __DIR__) . "/settings/kladr.css" ?>"/>
<?
CJSCore::Init(array("jquery"));

$arComponentParameters = array(
    "GROUPS" => array(
    ),
    "PARAMETERS" => array(
        "RETURN" => Array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("NP_GEOLOCATION_REPLACER_PARAM_RETURN"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
        ),
        "CITIES" => array(
            "PARENT" => "BASE",
            "REFRESH" => "N",
            "NAME" => GetMessage("NT_GEOLOCATION_REPLACER_PARAM_CITIES"),
            "TYPE" => "CUSTOM",
            "JS_FILE" => str_replace($_SERVER['DOCUMENT_ROOT'], "", __DIR__) . "/settings/settings.js?hash=" . time(),
            "JS_EVENT" => "OnNextypeGeolocationEdit",
            "DEFAULT" => base64_encode(\Bitrix\Main\Web\Json::encode(Array(
                Array(
                    'city' => '*',
                    'region' => '*',
                    'text' => 'All cities'
                ),
                
            ))),
        ),
    )
);
?>