<?php
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php"); 
    
    if (isset($_GET['sign']) && CModule::IncludeModule('webdoka.smartrealt'))
    {
        $sToken = SmartRealt_Options::GetToken();
        
        if (md5($sToken) == $_GET['sign'])
        {
            $oCatalogElement = new SmartRealt_CatalogElement();
            $oCatalogElementPhoto = new SmartRealt_CatalogElementPhoto();
            
            $sObjectsLastUpdateDate = "";
            $rsCatalogElement = $oCatalogElement->GetList(array('Limit' => 1), array('UpdateDate' => 'desc')); 
            if ($arCatalogElement = $rsCatalogElement->Fetch())
                $sObjectsLastUpdateDate = $arCatalogElement['UpdateDate'];

            $sPhotosLastUpdateDate = ""; 
            $rsCatalogElementPhoto = $oCatalogElementPhoto->GetList(array('Limit' => 1), array('UpdateDate' => 'desc')); 
            if ($arCatalogElementPhoto = $rsCatalogElementPhoto->Fetch())
                $sPhotosLastUpdateDate = $arCatalogElementPhoto['UpdateDate'];
            
            $oCatalogElement->LoadFromWebservice($sObjectsLastUpdateDate, 0, 20);
            $oCatalogElementPhoto->LoadFromWebservice($sPhotosLastUpdateDate, 0, 5);   
        }
        else
        {
            header("HTTP/1.1 403 Forbidden");
            exit();
        } 
    }
?>
