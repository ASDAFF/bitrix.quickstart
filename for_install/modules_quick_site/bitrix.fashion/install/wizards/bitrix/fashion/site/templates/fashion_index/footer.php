<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
IncludeTemplateLangFile(__FILE__);
?>

    </div><!-- #content -->
</div><!-- #wrapper -->

<div id="footer">
    <?$APPLICATION->IncludeComponent(
        "bitrix:menu",
        "bottom",
        Array(
            "ROOT_MENU_TYPE" => "top",
            "MAX_LEVEL" => "2",
            "CHILD_MENU_TYPE" => "left",
            "USE_EXT" => "N",
            "DELAY" => "N",
            "ALLOW_MULTI_SELECT" => "N",
            "MENU_CACHE_TYPE" => "A",
            "MENU_CACHE_TIME" => "3600",
            "MENU_CACHE_USE_GROUPS" => "Y",
            "MENU_CACHE_GET_VARS" => array()
        ),
    false
    );?>

    <?$APPLICATION->IncludeComponent(
        "bitrix:search.form",
        "",
        Array(
            "USE_SUGGEST" => "N",
            "PAGE" => SITE_DIR."search/index.php"
        ),
    false
    );?>

    <?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/social_btn.php"), false);?>
    <div class="credits">&copy; &laquo;<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/company_name.php"), false);?>&raquo;, <?=date('Y')?></div>
</div><!-- #footer -->

</div>

</body>
</html>