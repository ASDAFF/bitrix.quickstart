<?php
class SheeplaProxyDataModel implements ISheeplaProxyDataModel {
  public $debug = true;
	
  public function getPaymentMethods() {}
  public function getShippingMethods() {}
	
  public function getConfig() {
    $_Model = new CSheepla();
    return $_Model->getConfig();
  }
	
  public function markOrderAsSent($orderId=0){
        return CSheeplaDb::MarkOrderSent($orderId);
  }
  public function markOrderToResent($orderId=0){
        return CSheeplaDb::MarkOrderReSend($orderId);
  }
  public function markOrderToForseSync($amount=0){        
        return CSheeplaDb::MarkOrdersToForce($amount);
  }
  public function markOrderAsError($orderId=0){
    	return CSheeplaDb::MarkOrderError($orderId);
  }
  public function getBasketDB($fuser_id = null) {
    $_Model = new CSheepla();		        
    return $_Model->getBasketDB($fuser_id);
  }
    
  public function getCarriers($flag) {
    $_Model = new CSheepla();		
    return $_Model->getCarriers($flag);
  }
    
  public function getOrders() {
    $return = array();
    $ordersToSync = array();
    $sheeplaCarriers = CSheepla::GetSheeplaCarriers();
	$sheeplaOrders = CSheepla::GetSheeplaOrders();
    $sheeplaConfig = self::getConfig();
    if(sizeof($sheeplaOrders)>0){
        foreach($sheeplaOrders as $key => $value){
            $arr = array();
            /** Sheepla values */
            $arr['SHEEPLA'] = $value;
            /** Order products */
            $dbItemsInOrder = CSaleBasket::GetList(array("ID" => "ASC"), array("ORDER_ID" => $value['order_id']));
            while ($arItems = $dbItemsInOrder->Fetch()){
                $arr['PRODUCTS'][] = $arItems;
            }
            if(CSaleOrder::GetByID($value['order_id'])){
				if(array_merge_recursive(CSaleOrder::GetByID($value['order_id']), $arr)){
                	$ordersToSync[] = array_merge_recursive(CSaleOrder::GetByID($value['order_id']), $arr);
				}else{
					$ordersToSync[] = CSaleOrder::GetByID($value['order_id']);
				}
			}else{
				SheeplaProxyDataModel::MarkOrderAsError($value['order_id']);
			}

        }
    }
    
    $n=0;
    foreach($ordersToSync as $orderKey => $orderValue){
       //TODO         
        /** Sheepla Data AND Delivery Data */
        if(strpos(' '.$orderValue['DELIVERY_ID'],'sheepla')>0){
            $sheeplaData = explode(":",$orderValue['DELIVERY_ID']);
            $sheeplaData = $sheeplaData[1];
            $sheeplaCarriers = CSheepla::GetSheeplaCarriers();      
            $orderValue['DELIVERY_NAME'] = $sheeplaCarriers[$sheeplaData]['TITLE'];
            $orderValue['DELIVERY_TEMPLATE_ID'] = $sheeplaCarriers[$sheeplaData]['SHEEPLA_TEMPLATE'];
            
            if(!$orderValue['DELIVERY_NAME']){
                foreach($sheeplaCarriers as $key=>$value){
                    if($value['SHEEPLA_DB_ID']==$sheeplaData){
                        $orderValue['DELIVERY_NAME'] = $value['TITLE'];
                        $orderValue['DELIVERY_TEMPLATE_ID'] = $value['SHEEPLA_TEMPLATE'];
                    }
                }  
            } 
            
        }else{
            if($sheeplaConfig['syncAll']==0){
                SheeplaProxyDataModel::MarkOrderAsError($orderValue['ID']);
                continue;
            }
            /** Delivery type data*/
            $deliverySystem = CSaleDelivery::GetByID($orderValue['DELIVERY_ID']);            
            if($deliverySystem){                
                $orderValue['DELIVERY_NAME'] = $deliverySystem['NAME'];
                $orderValue['DELIVERY_TEMPLATE_ID'] = null;
            }else{
                $orderValue['DELIVERY_ID'] = 0;
                $orderValue['DELIVERY_NAME'] = 'none';
                $orderValue['DELIVERY_TEMPLATE_ID'] = null;
            }    
        }
        
        /** Payment system data */
        $paySystem = CSalePaySystem::GetByID($orderValue['PAY_SYSTEM_ID']);        
        if($paySystem) {            
            $orderValue['PAY_SYSTEM_NAME'] = $paySystem['NAME'];
        }else{
            $orderValue['PAY_SYSTEM_ID'] = 0;
            $orderValue['PAY_SYSTEM_NAME'] = 'none';
        }
        /** ORDER Properties  */
        $return[$n] = array(
                          'orderValue'               => (isset($orderValue['PRICE']) ? $orderValue['PRICE'] : 0),
                          'orderValueCurrency'       => $orderValue['CURRENCY'],
                          //TODO
                          /** Discount type */
                          /*'discountType'             => $orderValue['DISCOUNT_VALUE'],
                          'discountValue'            => $orderValue['DISCOUNT_VALUE'],*/
                          'externalDeliveryTypeId'   => (isset($orderValue['DELIVERY_ID']) ? $orderValue['DELIVERY_ID'] : 'undefined'),
                          'externalDeliveryTypeName' => (isset($orderValue['DELIVERY_NAME']) ? $orderValue['DELIVERY_NAME'] : 'undefined'),
                          'externalPaymentTypeId'    => (isset($orderValue['PAY_SYSTEM_ID']) ? $orderValue['PAY_SYSTEM_ID'] : 'undefined'),
                          'externalPaymentTypeName'  => (isset($orderValue['PAY_SYSTEM_NAME']) ? $orderValue['PAY_SYSTEM_NAME'] : 'undefined'),
                          'externalBuyerId'          => (isset($orderValue['USER_ID']) ? $orderValue['USER_ID'] : '0'),
                          'externalOrderId'          => (isset($orderValue['ID']) ? $orderValue['ID'] : '0'),
                          'shipmentTemplate'         => (isset($orderValue['DELIVERY_TEMPLATE_ID']) ? $orderValue['DELIVERY_TEMPLATE_ID'] : null),
                          'comments'                 => (isset($orderValue['USER_DESCRIPTION']) ? $orderValue['USER_DESCRIPTION'] : ''),
                          'createdOn'                => (isset($orderValue['DATE_INSERT']) ? date('c', strtotime($orderValue['DATE_INSERT'])) : date('c', time())),
                          'deliveryPrice'            => $orderValue['PRICE_DELIVERY'],
                          'deliveryPriceCurrency'    => $orderValue['CURRENCY']
                          );
        /** Sheepla Delivery options*/
        if(!empty($orderValue['SHEEPLA']['additional'])){
                $specials = unserialize($orderValue['SHEEPLA']['additional']);
                /** Getting cod */
                $cod = null;
                $temp = array_keys($specials);
                foreach($temp as $key => $value){                    
                    if(strpos($value,'is-postpay')>0){
                        $cod = (isset($orderValue['PRICE']) ? $orderValue['PRICE'] : null);        
                    }
                }
                /** Setting delivery option */
                $return[$n]['deliveryOptions'] = array(
                    'cod' => $cod,
                    'plInPost' => array('popName' => (isset($specials['sheepla-widget-plinpost-paczkomat']) ? $specials['sheepla-widget-plinpost-paczkomat'] : null), // InPost's "paczkomat" code name
                            ),
                    'ruQiwiPost' => array('popName' => (isset($specials['sheepla-widget-ruqiwipost-paczkomat']) ? $specials['sheepla-widget-ruqiwipost-paczkomat'] : null) // information for QiwiPost about postomat
                            ),
                    'ruCdek' => array(                        
                        'popName' => (isset($specials['sheepla-widget-rucdek-paczkomat']) ? $specials['sheepla-widget-rucdek-paczkomat'] : null), // information for CDEK about postomat
                        ),
                    'ruLogiBox' => array(
                        /*'popId' => (isset($specials['sheepla-widget-rulogibox-paczkomat']) ? $specials['sheepla-widget-rulogibox-paczkomat'] : null),*/
                        'popName' => (isset($specials['sheepla-widget-rulogibox-paczkomat']) ? $specials['sheepla-widget-rulogibox-paczkomat'] : null), // information for LogiBox about postomat
                        ),
                    'ruBoxBerry' => array(
                        'popId' => (isset($specials['sheepla-widget-ruboxberry-paczkomat']) ? $specials['sheepla-widget-ruboxberry-paczkomat'] : null),
                        'popName' => (isset($specials['sheepla-widget-ruboxberry-paczkomat']) ? $specials['sheepla-widget-ruboxberry-paczkomat'] : null), // information for BoxBerry about postomat
                        ),
                    'ruTopDelivery' => array('popName' => (isset($specials['sheepla-widget-rutopdelivery-paczkomat']) ? $specials['sheepla-widget-rutopdelivery-paczkomat'] : null), // information for TopDelivery about postomat
                            ),
                    'ruShopLogistics' => array(
                        'metroStationId' => ((isset($specials['sheepla-widget-rushoplogistics-metro-station'])&&($Address->city=='Москва')) ? $specials['sheepla-widget-rushoplogistics-metro-station'] : null), // information for ShopLogistics about metro station only for Moscow
                        'popId' => (isset($specials['sheepla-widget-rushoplogistics-paczkomat']) ? $specials['sheepla-widget-rushoplogistics-paczkomat'] : null), // information for ShopLogistics about postomat                        
                        ),
                    'ruIMLogistics' => array('pickupPointCarrierCode' => (isset($specials['sheepla-widget-ruimlogistics-paczkomat']) ? $specials['sheepla-widget-ruimlogistics-paczkomat'] : null), // information for ImLogistics about postomat
                            ),
                    'ruPickPoint' => array('pickupPointCarrierCode' => (isset($specials['sheepla-widget-rupickpoint-paczkomat']) ? $specials['sheepla-widget-rupickpoint-paczkomat'] : null), // information for PickPoint about postomat
                            ),                    
                    'plPolishPost' => array('pickupPointCarrierCode' => (isset($specials['sheepla-widget-plpolishpost-paczkomat']) ? $specials['sheepla-widget-plpolishpost-paczkomat'] : null), // information for PolishPost about postomat
                            ),
                    'plOwnCarrier' => array('deliveryPointId' => (isset($specials['sheepla-widget-plowncarrier-paczkomat']) ? $specials['sheepla-widget-plowncarrier-paczkomat'] : null), ));
            }
        /** Getting additional properties where delivery address is*/
        $_props_res = CSaleOrderPropsValue::GetOrderProps($orderValue['ID']);
        $params = array();
    			
          while ($tmp_param = $_props_res->Fetch()) {
            // if code field is empty for email
            if ($tmp_param['IS_EMAIL'] == "Y" && $tmp_param['ACTIVE'] == "Y") {
              $params['EMAIL'] = $tmp_param['VALUE'];
            } else {
              $params[ str_replace('F_', '',$tmp_param['CODE']) ] = $tmp_param['VALUE'];
            }
          }      
        /** Getting Location from Bitrix */ 
        $arLocation = CSaleLocation::GetByID($params['LOCATION'], 'ru');
        $countryAlphaCode = CSaleLocation::GetCountryLangByID($arLocation['COUNTRY_ID']);		
    	$dbZipList = CSaleLocation::GetLocationZIP($params['LOCATION']);
        $arZip = $dbZipList->Fetch();
        if(@array_merge_recursive($arZip,$arLocation)){
            $arLocation = array_merge_recursive($arZip,$arLocation);    
        }
        if(!$params['FIO']){
            $params['FIO'] = $params['CONTACT_PERSON'];
        } 
        if(!$params['FIO']){
            $params['FIO'] = $params['NAME'];
        }
        /** Exploding data*/
        $tmp = explode(' ',$params['FIO']);
        $params['FIRST_NAME'] = $tmp[0];
        $params['LAST_NAME'] = $tmp[sizeof($tmp)-1];
        //print_r($arLocation);
        //print_r($params);     
        if(!$arLocation['ZIP']){
            $arLocation['ZIP'] = $params['INDEX'];
            $params['ZIP'] = $params['INDEX'];
        }      
        
        /** Begin Parsing adress */
            
            $numbers = (isset($params['ADDRESS']) && !empty($params['ADDRESS']) ? $params['ADDRESS'] : $params['LOCATION']);
            $numbers = explode(" ",$numbers);
             
            for($i=0;$i<sizeof($numbers);$i++){
                if(intval($numbers[$i])!=0){
                    if(strpos(' '.$numbers[$i],"/")>0){
                        $temp = explode("/",$numbers[$i]);                        
                        for($j=0;$j<sizeof($temp);$j++){
                            if(intval($temp[$j])!=0){
                                $arr_numbers[] = $temp[$j];
                            }
                        }
                    }else{
                        $arr_numbers[] = $numbers[$i];
                    }
                }
            }
            arsort($arr_numbers);
            foreach($arr_numbers as $key => $value){                
                $ExtendedAddress['address'] = str_replace($value, '', (isset($params['ADDRESS']) && !empty($params['ADDRESS']) ? $params['ADDRESS'] : $params['LOCATION']));
            }
            $ExtendedAddress['address'] = str_replace('/', '', $ExtendedAddress['address']);
            
            $ExtendedAddress['buildingNumber'] = '';
            $ExtendedAddress['housing'] = '';
            $ExtendedAddress['building'] = '';                
            $ExtendedAddress['flat'] = '';
            
            if(sizeof($arr_numbers)==5){
                $ExtendedAddress['buildingNumber'] = $arr_numbers[1];
                $ExtendedAddress['housing'] = $arr_numbers[2];
                $ExtendedAddress['building'] = $arr_numbers[3];                
                $ExtendedAddress['flat'] = $arr_numbers[4];
            }
            
            if(sizeof($arr_numbers)==4){
                $ExtendedAddress['buildingNumber'] = $arr_numbers[0];
                $ExtendedAddress['housing'] = $arr_numbers[1];
                $ExtendedAddress['building'] = $arr_numbers[2];                
                $ExtendedAddress['flat'] = $arr_numbers[3];
            }
            
            if(sizeof($arr_numbers)==3){
                $ExtendedAddress['buildingNumber'] = $arr_numbers[0];
                $ExtendedAddress['housing'] = $arr_numbers[1];
                $ExtendedAddress['flat'] = $arr_numbers[2];
            }
            
            if(sizeof($arr_numbers)==2){
                $ExtendedAddress['buildingNumber'] = $arr_numbers[0];                
                $ExtendedAddress['flat'] = $arr_numbers[1];
            }
            
            if(sizeof($arr_numbers)==1){
                $ExtendedAddress['buildingNumber'] = $arr_numbers[0];
            }
        
        /** END Parsing adress */
        
        
        /** Delivery Adress */                  
        $return[$n]['deliveryAddress'] = array(
                                             'street'            => (isset($params['ADDRESS']) && !empty($params['ADDRESS']) ? $params['ADDRESS'] : $params['LOCATION']),
                                             'buildingNumber'    => (isset($params['HOME']) ? $params['HOME'] : $ExtendedAddress['buildingNumber']),
                                             'housing'           => (isset($params['HOUSING']) ? $params['HOUSING'] : $ExtendedAddress['housing']),
                                             'building'          => (isset($params['BUILDING']) ? $params['BUILDING'] : $ExtendedAddress['building']),
                                             'flat'              => (isset($params['FLAT']) ? $params['FLAT'] : $ExtendedAddress['flat']),
                                             'zipCode'           => (isset($params['ZIP']) && !empty($params['ZIP']) ? $params['ZIP'] : $arLocation['ZIP']), 
                                             'city'              => (isset($arLocation) ? $arLocation['CITY_NAME'] : $params['CITY']),
                                             'countryAlpha2Code' => (isset($countryAlphaCode['LID']) && $countryAlphaCode['LID'] !== false ? $countryAlphaCode['LID'] : 'ru'),
                                             'firstName'         => $params['FIRST_NAME'], 
                                             'lastName'          => $params['LAST_NAME'], 
                                             'companyName'       => (isset($params['COMPANY_NAME']) && !empty($params['COMPANY_NAME']) ? $params['COMPANY_NAME'] : (isset($params['COMPANY']) && !empty($params['COMPANY']) ? $params['COMPANY'] : null)),
                                             'phone'             => (isset($params['PHONE']) && !empty($params['PHONE']) ? $params['PHONE'] : null), 
                                             'email'             => $params['EMAIL']
                                             );
        /** ORDER Products */  
        $return[$n]['orderItems'] = array();
        if(sizeof($orderValue['PRODUCTS'])>0){                                                   
            foreach($orderValue['PRODUCTS'] as $productKey => $productValue){                
                //print_r($productValue);
                /** Product dimentions */
                $dimensions = array();
                if($productValue['DIMENSIONS']){
                    $dimensions = unserialize($productValue['DIMENSIONS']);
                }else{
                    //TODO 
                    /** Put log */
                    $dimensions["WIDTH"] = 0;
                    $dimensions["HEIGHT"]= 0;
                    $dimensions["LENGTH"]= 0;
                }
                /** Product additional keys data */
                $skuKeys = CSheepla::GetProductAdditional($orderValue['ID'],$productValue['ID']);
                
                $item = array(
                          'name'       => $productValue['NAME'],
                          'sku'        => $skuKeys['sku'],
                          'qty'        => $productValue['QUANTITY'],
                          'unit'       => $skuKeys['unit'],
                          'weight'     => $productValue['WEIGHT'],
                          'width'      => $dimensions["WIDTH"], 
                          'height'     => $dimensions["HEIGHT"],
                          'length'     => $dimensions["LENGTH"],
                          'volume'     => $productValue["VOLUME"],
                          'priceGross' => $productValue['PRICE'],
                          'ean13'      => $skuKeys['ean13'],
                          'ean8'       => $skuKeys['ean8'],
                          'issn'       => $skuKeys['issn']
                          );
                $return[$n]['orderItems'][] = $item;    
            }
        }        
        $n++; 
    }    
    return $return;
  }
	
  public function getCountAllOrders() {
    $list = CSaleOrder::GetList(Array("ID"=>"DESC"), array('DATE_FROM' => date('Y-m-d', time() - 2592000)));
    return count($list);
  }
}