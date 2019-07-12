<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Каталог");
?>
<?$APPLICATION->IncludeComponent("smartrealt:catalog", ".default", array(
    "SEF_MODE" => "Y",
    "SEF_FOLDER" => "#SITE_DIR#",
    "SET_TITLE" => "Y",
    "SEF_URL_TEMPLATES" => array(
        "list" => "",
        "element" => "",
    )
    ),
    false
);
?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>