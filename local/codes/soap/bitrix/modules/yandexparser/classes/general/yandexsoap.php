<?php

class yandexSoap {

    static $thisModuleName = 'yandexparser';
    static $config = false;
    
    private function getConfig(){
    
        foreach(array('SOAP_SERVER',  
                      'IBLOCK', 
                      'PROP_CODE', 
                      'CNT'             // сколько товаров отправляем за раз
                    ) as $optionName)
              self::$config[$optionName] = COption::GetOptionString(self::$thisModuleName, $optionName);
        
    
    }
    
    private function getItemsToSend(){
        
        CModule::IncludeModule('iblock');
        
        $arSelect = Array("ID", 
                          "NAME",
                          "IBLOCK_ID", 
                          "PROPERTY_" . self::$config['PROP_CODE']);
        
        $arFilter = Array("IBLOCK_ID" => self::$config['IBLOCK'], 
                          "ACTIVE"=>"Y",
                          "!PROPERTY_" . self::$config['PROP_CODE'] => false);
 
        $res = CIBlockElement::GetList(Array("RAND" => "ASC"),
                                       $arFilter,
                                       false,
                                       Array("nTopCount"=>self::$config['CNT']),
                                       $arSelect); 
         
        while($ob = $res->GetNextElement()){
            $arFields = $ob->GetFields();
            $arProps = $ob->GetProperties(); 
            $arr[$arFields['ID']] = $arProps[self::$config['PROP_CODE']]['VALUE'];
        }
        
        return $arr;
        
    } 


    function startAgent(){
 
        self::getConfig();

        CModule::IncludeModule('iblock');
        $localServer = true;
        $itemsToSend = self::getItemsToSend(); 
        if ($localServer){
             
            include_once $_SERVER["DOCUMENT_ROOT"].'/yandexparser/api.php'; 
            $parser = new yandexParser(array('minRating' => 4,
                                             'offersCount' => 20)); 
            foreach ($itemsToSend as $id => $modelID)
                $result[$id] = $parser->parse($modelID);
            $ansver = $result;
             
        } else {
            $client = new SoapClient(self::$config['SOAP_SERVER']);
            $itemsToSend = serialize($itemsToSend);
            prent($itemsToSend);
            $ansver = $client->sendItems($itemsToSend);
            $ansver = unserialize($ansver);
        } 
        
        prent($ansver);
        
         
        foreach ($ansver as $k=>$items){ 
            $yp = new yandexPrices();
            $yp->RemoveByItemID($k); 
            foreach($items as $item){    
                yandexPrices::Add(array('ITEM_ID'=>$k,
                                        'SHOP_NAME'=> $item['SHOP'],
                                        'URL' => '',
                                        'PRICE' => $item['PRICE'],
                                        'DELIVERY' => $item['DELIVERY'] ));
            } 
            CIBlockElement::SetPropertyValuesEx($k, false, array('yandexdate' => ConvertTimeStamp()));
        }
    }
    
}