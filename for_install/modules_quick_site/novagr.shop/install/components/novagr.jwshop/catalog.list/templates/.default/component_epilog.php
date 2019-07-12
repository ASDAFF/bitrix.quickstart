<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

    $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/product.js');

    if(count($ar_res = $arResult['META_DATA'])>0)
    {
        //если установка мета-данных страницы не нужна
        if(trim($ar_res['UF_META_DESCRIPTION'])<>"")
        {
            $APPLICATION->SetPageProperty("description",$ar_res['UF_META_DESCRIPTION']);   
        }
        if(trim($ar_res['UF_KEYWORDS'])<>"")
        {
            $APPLICATION->SetPageProperty("keywords",$ar_res['UF_KEYWORDS']);   
        }

        if(trim($ar_res['UF_BROWSER_TITLE'])<>"")
            $APPLICATION->SetTitle($ar_res['UF_BROWSER_TITLE']);   
        else 
            $APPLICATION->SetTitle($ar_res['NAME']);
    }
?>