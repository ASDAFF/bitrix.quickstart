<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?
    $sCookieValue = CStartShopVariables::Get("1C_COMMON_COOKIE_VALUE", null);

    if (!empty($sCookieName) && !empty($sCookieValue)) {
        echo "success\n";
        echo $sCookieName."\n";
        echo $sCookieValue."\n";
    } else {
        echo "failure\n";
    }
?>