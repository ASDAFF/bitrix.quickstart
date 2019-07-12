<?
if($_REQUEST['site']){
$arSite = CSite::GetByID($_REQUEST['site'])->Fetch();
$dir=$arSite['DIR'];
}
else $dir=SITE_DIR;
$APPLICATION->IncludeComponent("fenixit:sale.basket.basket.small", "", array(
    "PATH_TO_BASKET" => $dir."personal/cart/",
    "PATH_TO_ORDER" => $dir."personal/order/make/",
    "PATH_TO_WISHLIST" => $dir."personal/wishlist/",
    "SHOW_DELAY" => "Y",
    "SHOW_NOTAVAIL" => "N",
    "SHOW_SUBSCRIBE" => "N",
    "DISPLAY_IMG_WIDTH" => "70",
    "DISPLAY_IMG_HEIGHT" => "89",
    "DISPLAY_IMG_PROP" => "Y"
    ),
    false
);?>