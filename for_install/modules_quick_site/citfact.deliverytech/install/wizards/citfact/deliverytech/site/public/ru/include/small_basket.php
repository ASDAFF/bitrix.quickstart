<? if (strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) == "xmlhttprequest" && $_REQUEST["update_small_basket"] == "Y") { require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php"); } ?>
<?$APPLICATION->IncludeComponent("studiofact:small_basket", "", array(
		"PATH_TO_BASKET" => SITE_DIR."personal/cart/",
	),
	false,
	array()
);?>
<? if (strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) == "xmlhttprequest" && $_REQUEST["update_small_basket"] == "Y") { require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php"); } ?>