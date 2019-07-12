<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
    global $APPLICATION;

   	$APPLICATION->AddHeadScript($templateFolder . '/collection.js' );
  	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/product.js');
   	$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/comments.js');

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
?>