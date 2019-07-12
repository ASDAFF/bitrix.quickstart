<?
IncludeModuleLangFile(__FILE__);

$aMenu = array(
    "parent_menu" => "global_menu_novagr",
    "section" => "novagr.shop",
    "sort" => 200,
    "text" => GetMessage("novagr_shop_options_text"),
    "title" => GetMessage("novagr_shop_options_title"),
    "icon" => "sys_menu_icon",
    "page_icon" => "sys_page_icon",
    "items_id" => "menu_novagr_shop",
    "items" => array(
		array(
            "text" => GetMessage("novagr_shop_cachaMaker_text"),
            "url" => "novagr.shop_cacheMaker.php?lang=" . LANGUAGE_ID,
            "more_url" => Array("novagr.shop_cacheMaker.php"),
            "title" => GetMessage("novagr_shop_cacheMaker_alt"),
        ),
		array(
            "text" => GetMessage("novagr_shop_search_stat_text"),
            "url" => "novagr.shop_search_stat_clear.php?lang=" . LANGUAGE_ID,
            "more_url" => Array("novagr.shop_search_stat_clear.php"),
            "title" => GetMessage("novagr_shop_search_stat_alt"),
        ),
        array(
            "text" => GetMessage("novagr_fill_tag_products_text"),
            "url" => "novagr.shop_fill_tag_products.php?lang=" . LANGUAGE_ID,
            "more_url" => Array("novagr.shop_fill_tag_products.php"),
            "title" => GetMessage("novagr_fill_tag_products_alt"),
        ),
        array(
            "text" => GetMessage("novagr_fill_tag_images_text"),
            "url" => "novagr.shop_fill_tag_images.php?lang=" . LANGUAGE_ID,
            "more_url" => Array("novagr.shop_fill_tag_images.php"),
            "title" => GetMessage("novagr_fill_tag_images_alt"),
        ),
        array(
            "text" => GetMessage("novagroup_main_banners_text"),
            "url" => "novagr.shop_main_banners.php?lang=" . LANGUAGE_ID,
            "more_url" => Array("novagr.shop_main_banners.php"),
            "title" => GetMessage("novagroup_main_banners_alt"),
        ),
        array(
            "text" => GetMessage("novagroup_comments_text"),
            "url" => "novagr.shop_comments.php?lang=" . LANGUAGE_ID,
            "more_url" => Array("novagr.shop_comments.php"),
            "title" => GetMessage("novagroup_comments_alt"),
        ),
        array(
            "text" => GetMessage("novagroup_detail_text"),
            "url" => "novagr.shop_detail.php?lang=" . LANGUAGE_ID,
            "more_url" => Array("novagr.shop_detail.php"),
            "title" => GetMessage("novagroup_detail_alt"),
        ),
        array(
            "text" => GetMessage("novagroup_sorting_text"),
            "url" => "novagr.shop_sorting.php?lang=" . LANGUAGE_ID,
            "more_url" => Array("novagr.shop_sorting.php"),
            "title" => GetMessage("novagroup_sorting_alt"),
        ),
        array(
            "text" => GetMessage("novagroup_mysql_text"),
            "url" => "novagr.shop_mysql.php?lang=" . LANGUAGE_ID,
            "more_url" => Array("novagr.shop_mysql.php"),
            "title" => GetMessage("novagroup_mysql_alt"),
        ),
        array(
            "text" => GetMessage("novagroup_timetobuy_text"),
            "url" => "novagr.shop_timetobuy.php?lang=" . LANGUAGE_ID,
            "more_url" => Array("novagr.shop_timetobuy.php"),
            "title" => GetMessage("novagroup_timetobuy_alt"),
        ),
    )
);
return $aMenu;

?>
