<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$arTemplateParameters = array(

    "PHRASE_COUNT" => array(
        "NAME" => GetMessage("404_PHRASE_COUNT"),
        "TYPE" => "STRING",
        "DEFAULT" => '2',
        "REFRESH" => "Y",
    ),
    'PHRASE_1' => array(
        "NAME" => GetMessage("404_PHRASE").'1',
        "TYPE" => "STRING",
        "DEFAULT" => GetMessage("STREAM_PAGE404_PROSTITE_VELIKODUSN"),
    ),
    'PHRASE_2' => array(
        "NAME" => GetMessage("404_PHRASE").'2',
        "TYPE" => "STRING",
        "DEFAULT" => GetMessage("STREAM_PAGE404_NO_KAJETSA_ZDESQ_T"),
    ),
);

if (!empty($arCurrentValues["PHRASE_COUNT"]) && intval(($arCurrentValues["PHRASE_COUNT"])) > 2)
{
    for ($i = $arCurrentValues['PHRASE_COUNT'] - 2; $i <= $arCurrentValues["PHRASE_COUNT"]; $i++)
    {
        $arTemplateParameters['PHRASE_'.$i] = array(
            "PARENT" => "PHRASES",
            "NAME" => GetMessage("404_PHRASE").$i,
            "TYPE" => "STRING",
            "DEFAULT" => '',
        );
    }
}
?>
