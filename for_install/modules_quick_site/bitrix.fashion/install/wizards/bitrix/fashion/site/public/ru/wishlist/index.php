<? include($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php"); ?>
<? $APPLICATION->SetTitle(!empty($_REQUEST["LOGIN"]) ? "Вишлист пользователя ". $_REQUEST["LOGIN"] : "Вишлист"); ?>

<? if($USER->IsAuthorized()): ?>
<div class="step">
    <span class="current">
        <a href="<?=SITE_DIR?>personal/">Профиль пользователя</a>
        |&nbsp;<a href="<?=SITE_DIR?>personal/order/">Мои заказы</a>
        |</span>&nbsp;Вишлист
</div>
<? endif; ?>

<?$APPLICATION->IncludeComponent(
    "fashion:wishlist",
    ".default",
    Array(
        "CACHE_TYPE" => $arParams["CACHE_TYPE"],
        "CACHE_TIME" => $arParams["CACHE_TIME"],
        "LOGIN" => !empty($_REQUEST["LOGIN"]) ? $_REQUEST["LOGIN"] : "",
        "BASKET_URL" => SITE_DIR."personal/cart/"
    )
);?>
<? include($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php"); ?>