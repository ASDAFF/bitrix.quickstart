<?php

class SmartLogs {
    protected static $props = array();
    protected static $propsOffer = array();
    protected static $itemProperty = array();
    protected static $itemPropertyOffer = array();
    
    public static function saveOldValues($isElement=null, $settings=array(), $iblock_id=null, $id=null){
        if($settings["enabled"]!="Y")
            return false;        
        
        SmartLogs::$props = array();
        SmartLogs::$props['OLD_PRICE'] = null;
        SmartLogs::$props['old_count'] = null;     
        if(!$isElement){       
            if($settings["settings"]["save_props"]=="Y"){         
                    $old_prop = CIBlockElement::GetProperty($iblock_id, $id);
                    SmartLogs::$itemProperty = array();
                    while($ar_props = $old_prop->Fetch())
                        SmartLogs::$itemProperty[$ar_props['CODE']] = '';  
            }
        } else {
            if($settings["settings"]["save_props"]=="Y"){        
                $old_prop = CIBlockElement::GetProperty($iblock_id,$isElement);
                SmartLogs::$itemProperty = array();
                while($ar_props = $old_prop->Fetch()){  
                    if($ar_props['PROPERTY_TYPE']=='F') {
                        if(!empty($ar_props['VALUE']))
                            SmartLogs::$itemProperty[$ar_props['CODE']][] = $ar_props['VALUE'];
                    } elseif($ar_props['PROPERTY_TYPE']=='L') {
                        SmartLogs::$itemProperty[$ar_props['CODE']] = $ar_props['VALUE'];
                    } else {
                        SmartLogs::$itemProperty[$ar_props['CODE']] = $ar_props['VALUE'];
                    }                                                  
                }                                                                 
            }                                    
            
            if($settings["settings"]["save_price"]=="Y"){
                if(CModule::IncludeModule('catalog')){
                    $price_old= CPrice::GetBasePrice($isElement);  
                    if(!$price_old){
                        $arInfo = CCatalogSKU::GetInfoByProductIBlock($iblock_id); 
                        if (is_array($arInfo)) { 
                            $rsOffers = CIBlockElement::GetList(array(),array('IBLOCK_ID' => $arInfo['IBLOCK_ID'], 'PROPERTY_'.$arInfo['SKU_PROPERTY_ID'] => $id)); 
                            $arOffer = $rsOffers->GetNext();
                            if(isset($arOffer["ID"])){
                                $price_old= CPrice::GetBasePrice($arOffer["ID"]);                        
                            }
                            SmartLogs::$props['OLD_PRICE'] = isset($price_old["PRICE"])?$price_old["PRICE"]:'';  
                        }
                    } else {
                        SmartLogs::$props['OLD_PRICE'] = isset($price_old["PRICE"])?$price_old["PRICE"]:'';                    
                    }
                }
            } else {
                SmartLogs::$props['OLD_PRICE'] = null;
            }
            
            if($settings["settings"]["save_count"]=="Y"){
                if(CModule::IncludeModule('catalog')){
                    $count_old = CCatalogProduct::GetByID($isElement);
                    if($count_old['QUANTITY']==0){
                        $arInfo = CCatalogSKU::GetInfoByProductIBlock($iblock_id); 
                        if (is_array($arInfo)) { 
                            $rsOffers = CIBlockElement::GetList(array(),array('IBLOCK_ID' => $arInfo['IBLOCK_ID'], 'PROPERTY_'.$arInfo['SKU_PROPERTY_ID'] => $id)); 
                            $arOffer = $rsOffers->GetNext();
                            if(isset($arOffer["ID"])){
                                $count_old=CCatalogProduct::GetByID($arOffer["ID"]);            
                            }
                            SmartLogs::$props['old_count'] = isset($count_old["QUANTITY"])?$count_old["QUANTITY"]:'';  
                        }
                    } else {
                        SmartLogs::$props['old_count'] = isset($count_old["QUANTITY"])?$count_old["QUANTITY"]:'';                    
                    }
                }
                SmartLogs::$props['old_count'] = isset($count_old["QUANTITY"])?$count_old["QUANTITY"]:'';
            } else {
                SmartLogs::$props['old_count'] = null;
            }
        }
        
        $el = CIBlockElement::GetList(array(),array('ID'=>intval($id)));
        $el = $el->fetch();    
        
        if($settings["settings"]['save_descr']=='Y'){
            SmartLogs::$props['descr']['detail']['text']=$el['DETAIL_TEXT'];
            SmartLogs::$props['descr']['detail']['type']=$el['DETAIL_TEXT_TYPE'];
        } else {
            SmartLogs::$props['descr']['detail']['text']=null;
            SmartLogs::$props['descr']['detail']['type']=null;
        }
        if($settings["settings"]['save_prev_descr']=='Y'){
            SmartLogs::$props['descr']['prev']['text']=$el['PREVIEW_TEXT'];
            SmartLogs::$props['descr']['prev']['type']=$el['PREVIEW_TEXT_TYPE'];
        } else {
            SmartLogs::$props['descr']['prev']['text']=null;
            SmartLogs::$props['descr']['prev']['type']=null;
        }
        if($settings["settings"]['save_img']=='Y') {
             SmartLogs::$props['images']['detail'] = $el['DETAIL_PICTURE'];
        } else {
            SmartLogs::$props['images']['detail'] = null;
        }
        if($settings["settings"]['save_prev_img']=='Y') {
             SmartLogs::$props['images']['prev'] = $el['PREVIEW_PICTURE'];
        } else {
            SmartLogs::$props['images']['prev'] = null;
        }
        if($settings["settings"]['save_addit_img']=='Y') {
             SmartLogs::$props['images']['addit'] = $el['PREVIEW_PICTURE'];
        } else {
            SmartLogs::$props['images']['addit'] = null;
        }
        if($settings["settings"]['save_set_catalog']=='Y' && CModule::IncludeModule('catalog')){
            $cat = CCatalogProduct::GetByID($id);
            SmartLogs::$props['catalog']['WEIGHT']['old'] = $cat['WEIGHT'];
            SmartLogs::$props['catalog']['WIDTH']['old'] = $cat['WIDTH'];
            SmartLogs::$props['catalog']['LENGTH']['old'] = $cat['LENGTH'];
            SmartLogs::$props['catalog']['HEIGHT']['old'] = $cat['HEIGHT'];
        }
    }
    
    public static function saveNewValues($elementID, $settings, $arFields, $price_new=null, $arProduct){                           
        if($settings["enabled"]!="Y")
            return false;                      
            
        $isset = \Bitrix\Shs\ParserResultProductTable::getList(array(
            'filter'=>array(
                'RESULT_ID' => $settings['result_id'],
                'PRODUCT_ID' => $elementID,
            ),
        ))->fetch();
        if($isset) return;
            
        if($settings["settings"]["save_price"]=="Y"){
            SmartLogs::$props['NEW_PRICE'] = isset($price_new)?$price_new:SmartLogs::$props['OLD_PRICE'];            
        } else {
            SmartLogs::$props['NEW_PRICE'] = null;
        }
        
        if($settings["settings"]["save_count"]=="Y"){
            SmartLogs::$props['new_count'] = isset($arFields["AVAILABLE_PREVIEW"])?$arFields["AVAILABLE_PREVIEW"]:SmartLogs::$props['old_count'];
        } else {
            SmartLogs::$props['new_count'] = null;
        }
        
        $props = array();
        if($settings["settings"]["save_props"]=="Y"){
            $new_props = array();
            foreach($arFields["PROPERTY_VALUES"] as $key => $value){    
                if(is_array($value))
                    continue;
                $new_props[strtoupper($key)] = $value;
            }
            foreach(SmartLogs::$itemProperty as $name => $old_value){             
                if((isset($new_props[strtoupper($name)]) && $new_props[strtoupper($name)]!='') || $old_value!='') {
                    $props[strtoupper($name)] = array(
                        'old' => $old_value,
                        'new' => (isset($new_props[strtoupper($name)])&& $new_props[strtoupper($name)]!='')?$new_props[strtoupper($name)]:$old_value,
                    );
                } 
            }
            $props=array(
                'properties' =>$props,
            );
        }                    
        if($settings["settings"]['save_descr']=='Y' && $arFields['DETAIL_TEXT']!==null){
            SmartLogs::$props['descr']['detail']['text']=$arFields['DETAIL_TEXT'];
            SmartLogs::$props['descr']['detail']['type']=$arFields['DETAIL_TEXT_TYPE'];
        }
        if($settings["settings"]['save_prev_descr']=='Y' && $arFields['PREVIEW_TEXT']!==null){
            SmartLogs::$props['descr']['prev']['text']=$arFields['PREVIEW_TEXT'];
            SmartLogs::$props['descr']['prev']['type']=$arFields['PREVIEW_TEXT_TYPE'];
        }
        if($settings["settings"]['save_img']=='Y' && $arFields['DETAIL_PICTURE']!==null) {
             SmartLogs::$props['images']['detail'] = $arFields['DETAIL_PICTURE'];
        }
        if($settings["settings"]['save_prev_img']=='Y' && $arFields['PREVIEW_PICTURE']!==null) {
             SmartLogs::$props['images']['prev'] = $arFields['PREVIEW_PICTURE'];
        }
        if($settings["settings"]['save_addit_img']=='Y' && $arFields['PREVIEW_PICTURE']!==null) {
             SmartLogs::$props['images']['addit'] = $arFields['PREVIEW_PICTURE'];
        }        
        
        if($settings["settings"]['save_set_catalog']=='Y'){
            foreach(SmartLogs::$props['catalog'] as $code => $arr){
                SmartLogs::$props['catalog'][$code]['new'] = (isset($arProduct[$code]))?$arProduct[$code]:SmartLogs::$props['catalog'][$code]['old'];
            }
        }
        
        $props['count'] = array(
                            'old'=>SmartLogs::$props['old_count'],
                            'new'=>SmartLogs::$props['new_count'],
                        ); 
        $props['images'] = SmartLogs::$props['images'];
        $props['descr'] = SmartLogs::$props['descr'];
        $props['catalog'] = SmartLogs::$props['catalog']; 
        $props['type'] = 'product';                             
        if($elementID){        
            $res = \Bitrix\Shs\ParserResultProductTable::add(array(
                'RESULT_ID' => $settings['result_id'],
                'PRODUCT_ID' => $elementID,
                'UPDATE_TIME' => new \Bitrix\Main\Type\DateTime(),
                'OLD_PRICE' => SmartLogs::$props['OLD_PRICE'],
                'NEW_PRICE' => SmartLogs::$props['NEW_PRICE'],
                'PROPERTIES' => base64_encode(serialize($props)),
            ));
        }                                                                   
    }
    
    public static function saveOldValuesOffer($isElement=null, $settings=array(), $iblock_id=null, $id=null){
        if($settings["enabled"]!="Y")
            return false;        
        
        SmartLogs::$propsOffer = array();
        SmartLogs::$propsOffer['OLD_PRICE'] = null;
        SmartLogs::$propsOffer['old_count'] = null;     
        if(!$isElement){       
            if($settings["settings"]["save_props"]=="Y"){     
                $el = CIBlockElement::GetByID($id)->Fetch();
                $iblock_id = $el['IBLOCK_ID'];        
                $old_prop = CIBlockElement::GetProperty($iblock_id, $id);
                SmartLogs::$itemPropertyOffer = array();
                while($ar_props = $old_prop->Fetch())
                    SmartLogs::$itemPropertyOffer[$ar_props['CODE']] = '';  
            }
        } else {
            if($settings["settings"]["save_props"]=="Y"){     
                $el = CIBlockElement::GetByID($id)->Fetch();
                $iblock_id = $el['IBLOCK_ID'];   
                $old_prop = CIBlockElement::GetProperty($iblock_id,$isElement);
                SmartLogs::$itemPropertyOffer = array();
                while($ar_props = $old_prop->Fetch()){  
                    if($ar_props['PROPERTY_TYPE']=='F') {
                        if(!empty($ar_props['VALUE']))
                            SmartLogs::$itemPropertyOffer[$ar_props['CODE']][] = $ar_props['VALUE'];
                    } elseif($ar_props['PROPERTY_TYPE']=='L') {
                        SmartLogs::$itemPropertyOffer[$ar_props['CODE']] = $ar_props['VALUE'];
                    } else {
                        SmartLogs::$itemPropertyOffer[$ar_props['CODE']] = $ar_props['VALUE'];
                    }                                                
                }                                                                 
            }                                    
            
            if($settings["settings"]["save_price"]=="Y"){
                if(CModule::IncludeModule('catalog')){
                    $price_old= CPrice::GetBasePrice($isElement);  
                    if(!$price_old){
                        $arInfo = CCatalogSKU::GetInfoByProductIBlock($iblock_id); 
                        if (is_array($arInfo)) { 
                            $rsOffers = CIBlockElement::GetList(array(),array('IBLOCK_ID' => $arInfo['IBLOCK_ID'], 'PROPERTY_'.$arInfo['SKU_PROPERTY_ID'] => $id)); 
                            $arOffer = $rsOffers->GetNext();
                            if(isset($arOffer["ID"])){
                                $price_old= CPrice::GetBasePrice($arOffer["ID"]);                        
                            }
                            SmartLogs::$propsOffer['OLD_PRICE'] = isset($price_old["PRICE"])?$price_old["PRICE"]:'';  
                        }
                    } else {
                        SmartLogs::$propsOffer['OLD_PRICE'] = isset($price_old["PRICE"])?$price_old["PRICE"]:'';                    
                    }
                }
            } else {
                SmartLogs::$propsOffer['OLD_PRICE'] = null;
            }
            
            if($settings["settings"]["save_count"]=="Y"){
                if(CModule::IncludeModule('catalog')){
                    $count_old = CCatalogProduct::GetByID($isElement);
                    if($count_old['QUANTITY']==0){
                        $arInfo = CCatalogSKU::GetInfoByProductIBlock($iblock_id); 
                        if (is_array($arInfo)) { 
                            $rsOffers = CIBlockElement::GetList(array(),array('IBLOCK_ID' => $arInfo['IBLOCK_ID'], 'PROPERTY_'.$arInfo['SKU_PROPERTY_ID'] => $id)); 
                            $arOffer = $rsOffers->GetNext();
                            if(isset($arOffer["ID"])){
                                $count_old=CCatalogProduct::GetByID($arOffer["ID"]);            
                            }
                            SmartLogs::$propsOffer['old_count'] = isset($count_old["QUANTITY"])?$count_old["QUANTITY"]:'';  
                        }
                    } else {
                        SmartLogs::$propsOffer['old_count'] = isset($count_old["QUANTITY"])?$count_old["QUANTITY"]:'';                    
                    }
                }
                SmartLogs::$propsOffer['old_count'] = isset($count_old["QUANTITY"])?$count_old["QUANTITY"]:'';
            } else {
                SmartLogs::$propsOffer['old_count'] = null;
            }
        }
        
        $el = CIBlockElement::GetList(array(),array('ID'=>intval($id)));
        $el = $el->fetch();    
        
        if($settings["settings"]['save_descr']=='Y'){
            SmartLogs::$propsOffer['descr']['detail']['text']=$el['DETAIL_TEXT'];
            SmartLogs::$propsOffer['descr']['detail']['type']=$el['DETAIL_TEXT_TYPE'];
        } else {
            SmartLogs::$propsOffer['descr']['detail']['text']=null;
            SmartLogs::$propsOffer['descr']['detail']['type']=null;
        }
        if($settings["settings"]['save_prev_descr']=='Y'){
            SmartLogs::$propsOffer['descr']['prev']['text']=$el['PREVIEW_TEXT'];
            SmartLogs::$propsOffer['descr']['prev']['type']=$el['PREVIEW_TEXT_TYPE'];
        } else {
            SmartLogs::$propsOffer['descr']['prev']['text']=null;
            SmartLogs::$propsOffer['descr']['prev']['type']=null;
        }
        if($settings["settings"]['save_img']=='Y') {
             SmartLogs::$propsOffer['images']['detail'] = $el['DETAIL_PICTURE'];
        } else {
            SmartLogs::$propsOffer['images']['detail'] = null;
        }
        if($settings["settings"]['save_prev_img']=='Y') {
             SmartLogs::$propsOffer['images']['prev'] = $el['PREVIEW_PICTURE'];
        } else {
            SmartLogs::$propsOffer['images']['prev'] = null;
        }
        if($settings["settings"]['save_addit_img']=='Y') {
             SmartLogs::$propsOffer['images']['addit'] = $el['PREVIEW_PICTURE'];
        } else {
            SmartLogs::$propsOffer['images']['addit'] = null;
        }
        if($settings["settings"]['save_set_catalog']=='Y' && CModule::IncludeModule('catalog')){
            $cat = CCatalogProduct::GetByID($id);
            SmartLogs::$propsOffer['catalog']['WEIGHT']['old'] = $cat['WEIGHT'];
            SmartLogs::$propsOffer['catalog']['WIDTH']['old'] = $cat['WIDTH'];
            SmartLogs::$propsOffer['catalog']['LENGTH']['old'] = $cat['LENGTH'];
            SmartLogs::$propsOffer['catalog']['HEIGHT']['old'] = $cat['HEIGHT'];
        }
    }
    
    public static function saveNewValuesOffer($elementID, $settings, $arFields, $price_new=null, $arProduct){                           
        if($settings["enabled"]!="Y")
            return false;                      
            
        $isset = \Bitrix\Shs\ParserResultProductTable::getList(array(
            'filter'=>array(
                'RESULT_ID' => $settings['result_id'],
                'PRODUCT_ID' => $elementID,
            ),
        ))->fetch();
        if($isset) return;
            
        if($settings["settings"]["save_price"]=="Y"){
            SmartLogs::$propsOffer['NEW_PRICE'] = isset($price_new)?$price_new:SmartLogs::$propsOffer['OLD_PRICE'];            
        } else {
            SmartLogs::$propsOffer['NEW_PRICE'] = null;
        }
        
        if($settings["settings"]["save_count"]=="Y"){
            SmartLogs::$propsOffer['new_count'] = isset($arFields["AVAILABLE_PREVIEW"])?$arFields["AVAILABLE_PREVIEW"]:SmartLogs::$propsOffer['old_count'];            
        } else {
            SmartLogs::$propsOffer['new_count'] = null;
        }
        
        $props = array();
        if($settings["settings"]["save_props"]=="Y"){
            $new_props = array();
            foreach($arFields["PROPERTY_VALUES"] as $key => $value){    
                if(is_array($value))
                    continue;
                $new_props[strtoupper($key)] = $value;
            }
            foreach(SmartLogs::$itemPropertyOffer as $name => $old_value){             
                if((isset($new_props[strtoupper($name)]) && $new_props[strtoupper($name)]!='') || $old_value!='') {
                    $props[strtoupper($name)] = array(
                        'old' => $old_value,
                        'new' => (isset($new_props[strtoupper($name)])&& $new_props[strtoupper($name)]!='')?$new_props[strtoupper($name)]:$old_value,
                    );
                } 
            }
            $props=array(
                'properties' =>$props,
            );
        }                    
        if($settings["settings"]['save_descr']=='Y' && $arFields['DETAIL_TEXT']!==null){
            SmartLogs::$propsOffer['descr']['detail']['text']=$arFields['DETAIL_TEXT'];
            SmartLogs::$propsOffer['descr']['detail']['type']=$arFields['DETAIL_TEXT_TYPE'];
        }
        if($settings["settings"]['save_prev_descr']=='Y' && $arFields['PREVIEW_TEXT']!==null){
            SmartLogs::$propsOffer['descr']['prev']['text']=$arFields['PREVIEW_TEXT'];
            SmartLogs::$propsOffer['descr']['prev']['type']=$arFields['PREVIEW_TEXT_TYPE'];
        }
        if($settings["settings"]['save_img']=='Y' && $arFields['DETAIL_PICTURE']!==null) {
             SmartLogs::$propsOffer['images']['detail'] = $arFields['DETAIL_PICTURE'];
        }
        if($settings["settings"]['save_prev_img']=='Y' && $arFields['PREVIEW_PICTURE']!==null) {
             SmartLogs::$propsOffer['images']['prev'] = $arFields['PREVIEW_PICTURE'];
        }
        if($settings["settings"]['save_addit_img']=='Y' && $arFields['PREVIEW_PICTURE']!==null) {
             SmartLogs::$propsOffer['images']['addit'] = $arFields['PREVIEW_PICTURE'];
        }        
        
        if($settings["settings"]['save_set_catalog']=='Y'){
            foreach(SmartLogs::$propsOffer['catalog'] as $code => $arr){
                SmartLogs::$propsOffer['catalog'][$code]['new'] = (isset($arProduct[$code]))?$arProduct[$code]:SmartLogs::$propsOffer['catalog'][$code]['old'];
            }
        }
        
        $props['count'] = array(
                            'old'=>SmartLogs::$propsOffer['old_count'],
                            'new'=>SmartLogs::$propsOffer['new_count'],
                        ); 
        $props['images'] = SmartLogs::$propsOffer['images'];
        $props['descr'] = SmartLogs::$propsOffer['descr'];
        $props['catalog'] = SmartLogs::$propsOffer['catalog']; 
        $props['type'] = 'offer';                             
        if($elementID){        
            $res = \Bitrix\Shs\ParserResultProductTable::add(array(
                'RESULT_ID' => $settings['result_id'],
                'PRODUCT_ID' => $elementID,
                'UPDATE_TIME' => new \Bitrix\Main\Type\DateTime(),
                'OLD_PRICE' => SmartLogs::$propsOffer['OLD_PRICE'],
                'NEW_PRICE' => SmartLogs::$propsOffer['NEW_PRICE'],
                'PROPERTIES' => base64_encode(serialize($props)),
            ));
        }                                                                   
    }
}