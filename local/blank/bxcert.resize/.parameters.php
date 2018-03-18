<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$arTemplateParameters = array(
    "BXCERT_IMG_TYPE" => Array(
        "NAME" => GetMessage("BXCERT_IMG_TYPE"),
        "TYPE" => "LIST",
        "VALUES" => array(
            'PREVIEW_PICTURE' => GetMessage('BXCERT_IMG_TYPE_PREVIEW'),
            'DETAIL_PICTURE' => GetMessage('BXCERT_IMG_TYPE_DETAIL')
        ),
    ),
    "BXCERT_IMG_WIDTH" => Array(
        "NAME" => GetMessage("BXCERT_IMG_WIDTH"),
        "TYPE" => "TEXT"
    ),
    "BXCERT_IMG_HEIGHT" => Array(
        "NAME" => GetMessage("BXCERT_IMG_HEIGHT"),
        "TYPE" => "TEXT"
    )
);
?>