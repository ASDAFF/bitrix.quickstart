<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
IncludeTemplateLangFile(__FILE__);
?>
                </div><!-- #content-->
        </div><!-- #container-->
        <?if (!$isDetailPage) {?>
        <?$APPLICATION->IncludeComponent(
            "bitrix:main.include",
            "",
            Array(
                "AREA_FILE_SHOW" => "sect",
                "AREA_FILE_SUFFIX" => "inc",
                "AREA_FILE_RECURSIVE" => "N",
                "EDIT_MODE" => "html",
            ),
            false,
            Array('HIDE_ICONS' => 'Y')
        );?>
        <?}?>
    </div><!-- #middle-->

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
    </div><!-- #footer -->

    <div id="footer-2">
        <div class="payments">
            <div class="wrapper">
            <ul>
                <li class="visa">VISA</li>
                <li class="mastercard">MasterCard</li>
            </ul>
            </div>
        </div>

        <div class="vcard">
            <address>
            <p class="adr">
                <address itemprop = "address"><?=GetMessage("ADDRESS")?> <span class="locality"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/locality.php"), false);?></span>, <span class="street-address"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/street_address.php"), false);?></span></address>
            </p>
            <p>
                <?=GetMessage("PHONE")?>:
                <abbr itemprop = "telephone" class="tel"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/telephone.php"), false);?></abbr>
            </p>
            </address>
        </div>

        <div class="credits">
            <div class="wrapper">
            &copy; &laquo;<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/company_name.php"), false);?>&raquo;, <?=date('Y')?>
            </div>
        </div>
    </div>

    </div><!-- #wrapper -->

    </div><!-- .back-2 -->
</div><!-- .back -->

</body>
</html>