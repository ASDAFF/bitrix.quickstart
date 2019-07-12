<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Карта сайта");
$APPLICATION->AddChainItem("Карта сайта");
?>
<div class="map link-mas">
    <h3>Карта сайта</h3>
    <?$APPLICATION->IncludeComponent(
        "novagroup:map",
        "",
        Array(),
        false
    );?>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>