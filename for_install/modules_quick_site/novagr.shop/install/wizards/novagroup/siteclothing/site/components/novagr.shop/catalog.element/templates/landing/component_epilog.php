<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

//if(isset($_REQUEST['CAJAX']) and $_REQUEST['CAJAX']=='1') return;

//global $APPLICATION;
//$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/product.js');
//$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/comments.js');
/*
if (isset($arResult["ID"]) && $arResult["ID"] > 0) {

    $scheme = $APPLICATION->IsHTTPS() ? "https://" : "http://";
    $HTTP_HOST = $scheme.getenv('HTTP_HOST');
    $PARSE_HOST = parse_url($HTTP_HOST);
    if (isset($PARSE_HOST['port']) and $PARSE_HOST['port'] == '80') {
        $HOST = $PARSE_HOST['host'];
    } elseif(isset($PARSE_HOST['port'])  and $PARSE_HOST['port'] == '443') {
        $HOST = $PARSE_HOST['host'];
    } elseif(isset($PARSE_HOST['port'])) {
        $HOST = $PARSE_HOST['host'] . ":" . $PARSE_HOST['port'];
    } else {
        $HOST = $PARSE_HOST['host'];
    }

    $APPLICATION->AddHeadString('<meta property="og:title" content="' . $arResult['NAME'] . '">');
    $APPLICATION->AddHeadString('<meta property="og:url" content="' . $scheme . $HOST . $APPLICATION->GetCurPage() . "?time=" . time() . '">');
    $APPLICATION->AddHeadString('<meta property="og:description" content="' . htmlspecialcharsbx($arResult['PROPERTY_META_DESCRIPTION_VALUE']) . '">');

    if (is_array($arResult['SOC_IMAGES'])) {
        foreach ($arResult['SOC_IMAGES'] as $images) {
            if(is_array($images['curPhotosBig']))
            {
                foreach($images['curPhotosBig'] as $image){
                    $APPLICATION->AddHeadString('<meta property="og:image" content="' . $scheme . $HOST . $image . '">');
                }
            }
        }
    }
}*/
?>