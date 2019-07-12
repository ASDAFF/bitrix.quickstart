<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("О компании");
?>

<div class="cols2">
    <div class="col1">
        <?$APPLICATION->IncludeComponent("bitrix:menu", "left", array(
                "ROOT_MENU_TYPE" => "left",
                "MENU_CACHE_TYPE" => "A",
                "MENU_CACHE_TIME" => "3600",
                "MENU_CACHE_USE_GROUPS" => "Y",
                "MENU_CACHE_GET_VARS" => array(),
                "MAX_LEVEL" => "1",
                "CHILD_MENU_TYPE" => "",
                "USE_EXT" => "Y",
                "DELAY" => "N",
            ),
            false
        );?>
    </div>

    <div class="col2 catalog-v2">
        <?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array( "AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/company/about.php", "EDIT_TEMPLATE" => "" ), false );?>

        <div class="subscibe-form1">
            <h3>подпишитесь на рассылку нашего сайта </h3>
            <p>Подпишитесь на рассылку, и станьте одним из первых, кто будет в курсе всех новостей</p>
            <form action="">
                <div class="name-cont"><input type="text" placeholder="Ваше имя"></div>
                <div class="email-cont"><input type="text" placeholder="Ваше E-mail"></div>
                <div class="submit-cont">
                    <button type="button" name="button">Подписаться</button>
                </div>
                <div class="clearfix"></div>
            </form>
        </div>
    </div>
</div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
