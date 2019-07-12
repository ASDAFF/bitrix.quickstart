<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
    global $APPLICATION;
    
    if (!empty($arParams['ELEMENT_CODE'])) {
    	$APPLICATION->AddHeadScript($templateFolder . '/collection.js');
    	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/product.js');
    	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/comments.js');
        $APPLICATION->AddHeadScript('/local/js/novagroup/jquery.lwtCountdown-1.0.js');
    }
    
    if(is_array($ITEMS = $arResult['ITEMS']))
    {
        $description = $keywords = array();
        foreach($ITEMS as $item)
        {
            if(trim($item['PROPERTY_META_DESCRIPTION_VALUE'])<>"")
                $description[] = $item['PROPERTY_META_DESCRIPTION_VALUE'];
            if(trim($item['PROPERTY_KEYWORDS_VALUE'])<>"")
                $keywords[] = $item['PROPERTY_KEYWORDS_VALUE'];    
        }   

        if(trim($APPLICATION->GetPageProperty("description"))<>"")
            $description[] = $APPLICATION->GetPageProperty("description"); 
        if(trim($APPLICATION->GetPageProperty("keywords"))<>"")
            $keywords[] = $APPLICATION->GetPageProperty("keywords");

        if(count($description)>0) 
            $APPLICATION->SetPageProperty("description", implode(', ',$description));   

        if(count($keywords)>0) 
            $APPLICATION->SetPageProperty("keywords", implode(', ',$keywords));   
    }

if (!empty($arParams['ELEMENT_CODE'])) {

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
    $APPLICATION->AddHeadString('<meta property="og:description" content="' . htmlspecialcharsbx($arResult['PROPERTY_META_DESCRIPTION_VALUE']) . '">');

    if(!empty($_REQUEST['image']))
    {
        $APPLICATION->AddHeadString('<meta property="og:image" content="' . $scheme . $HOST . strip_tags($_REQUEST['image']) . '">');
        $APPLICATION->AddHeadString('<meta property="og:url" content="' . $scheme . $HOST . getenv("REQUEST_URI") . "&time=" . time() .'">');
    } else {
        $APPLICATION->AddHeadString('<meta property="og:url" content="' . $scheme. $HOST . $APPLICATION->GetCurPage() . "?time=" . time() . '">');
        if (is_array($arResult['ORIGINAL']['PROPERTY_PHOTOS_VALUE'])) {
            foreach ($arResult['ORIGINAL']['PROPERTY_PHOTOS_VALUE'] as $image) {
                $APPLICATION->AddHeadString('<meta property="og:image" content="http://' . getenv('HTTP_HOST') . $image . '">');
            }
        }
    }
}
?>