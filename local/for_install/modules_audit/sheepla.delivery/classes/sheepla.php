<?
CModule::IncludeModule("sale");

include('sheepla_db.php');

Class CSheepla extends CModule{    
    public function getConfig(){
	$sheeplaConfig = array();
        //TODO
        /** Getting data from database */        
        $sheeplaConfig['adminApiKey'] = COption::GetOptionString("sheepla.delivery", "sheepla_adminApiKey");
        $sheeplaConfig['publicApiKey'] = COption::GetOptionString("sheepla.delivery", "sheepla_publicApiKey");
        $sheeplaConfig['apiUrl'] = COption::GetOptionString("sheepla.delivery", "sheepla_apiUrl");
        $sheeplaConfig['jsUrl'] = COption::GetOptionString("sheepla.delivery", "sheepla_jsUrl");
        $sheeplaConfig['cssUrl'] = COption::GetOptionString("sheepla.delivery", "sheepla_cssUrl");
        $sheeplaConfig['syncAll'] = COption::GetOptionString("sheepla.delivery", "sheepla_syncAll");
        $sheeplaConfig['checkout'] = COption::GetOptionString("sheepla.delivery", "sheepla_checkout");            
        $sheeplaConfig['configOk'] = COption::GetOptionString("sheepla.delivery", "sheepla_configOk");
        $sheeplaConfig['jQDeliverySelector'] = COption::GetOptionString("sheepla.delivery", "sheepla_jQDeliverySelector");
        $sheeplaConfig['jQDeliveryCitySelector'] = COption::GetOptionString("sheepla.delivery", "sheepla_jQDeliveryCitySelector");
        $sheeplaConfig['jQDeliverySelectorShort'] = COption::GetOptionString("sheepla.delivery", "sheepla_jQDeliverySelectorShort");
        $sheeplaConfig['jQLocationSelector'] = COption::GetOptionString("sheepla.delivery", "sheepla_jQLocationSelector");
        $sheeplaConfig['jQLabelSelector'] = COption::GetOptionString("sheepla.delivery", "sheepla_jQLabelSelector");
        /** Admin order page */
        $sheeplaConfig['adminOrderViewUrl'] = COption::GetOptionString("sheepla.delivery", "sheepla_adminOrderViewUrl");
        $sheeplaConfig['adminOrderAddUrl'] = COption::GetOptionString("sheepla.delivery", "sheepla_adminOrderAddUrl");
        $sheeplaConfig['adminOrderEditUrl'] = COption::GetOptionString("sheepla.delivery", "sheepla_adminOrderEditUrl");
        $sheeplaConfig['orderViewSheeplaSelector'] = COption::GetOptionString("sheepla.delivery", "sheepla_orderViewSheeplaSelector");
        /** Admin order page jquery selectors */
        $sheeplaConfig['adminOrderjQSelector'] = COption::GetOptionString("sheepla.delivery", "sheepla_adminOrderjQSelector");
        $sheeplaConfig['adminOrderjQSelectorShort'] = COption::GetOptionString("sheepla.delivery", "sheepla_adminOrderjQSelectorShort");
        $sheeplaConfig['adminOrderjQLocationSelector'] = COption::GetOptionString("sheepla.delivery", "sheepla_adminOrderjQLocationSelector");
        $sheeplaConfig['adminOrderjQLabelSelector'] = COption::GetOptionString("sheepla.delivery", "sheepla_adminOrderjQLabelSelector");
    
        
        
        $sheeplaConfig['logPath'] = dirname(__FILE__).'/../logs/warnings.log.php';
        
        return $sheeplaConfig;        
    }
    public function SetConfig($param) {        
        foreach ($param as $key => $value) {
            COption::SetOptionString("sheepla.delivery", "sheepla_" . $key, $value);            
        }
        
        $model = new SheeplaProxyDataModel();
        $client = new SheeplaClient();
        $client->setDebug(false);
        $client->setConfig(self::getConfig());        
        if($client->validAccount()){
           COption::SetOptionString("sheepla.delivery", "sheepla_configOk", '1');
           return true;
        }else{
           COption::SetOptionString("sheepla.delivery", "sheepla_configOk", '0');
           return false; 
        }
    }    
    
    public function DynamicPrice($location,$order,$return_array=false, $profile=''){
        //TODO
        /**
         * Not sure if it is needed 
         * MAPPING FOR CUSTOM FIELDS SETTED IN ADMIN PANEL
        */
        
        /** Validation start */
            if(!array_key_exists('ZIP',$location)){
                if(array_key_exists('LOCATION_ZIP',$location)){
                    $location['ZIP'] = $location['LOCATION_ZIP'];
                }else{
                    $location['ZIP'] = '00000';    
                } 
                self::WriteSheeplaLog('CDeliverySheepla::Compability() CDeliverySheepla::Calculate()','Dynamic Pricing',$result='', 'NO Field ZIP presented in $arOrder - may cause wrong delivery price or no price presented'); 
            } 
            if(!array_key_exists('CITY_NAME',$location)){ self::WriteSheeplaLog('CDeliverySheepla::Compability() CDeliverySheepla::Calculate()','Dynamic Pricing',$result='', 'NO Field CITY_NAME presented $arOrder - may cause wrong delivery price or no price presented'); }
           
            
            if(sizeof($order['ITEMS'])<1){
                $order['ITEMS'][] = $order;
            }
            
            if(sizeof($order['ITEMS'])>0){
                foreach($order['ITEMS'] as $key => $value){
                    if(!array_key_exists('PRICE',$value)){ self::WriteSheeplaLog('CDeliverySheepla::Compability() CDeliverySheepla::Calculate()','Dynamic Pricing',$result='', 'NO Field PRICE presented $arOrder - may cause wrong delivery price or no price presented'); }
                    if(!array_key_exists('WEIGHT',$value)){ self::WriteSheeplaLog('CDeliverySheepla::Compability() CDeliverySheepla::Calculate()','Dynamic Pricing',$result='', 'NO Field WEIGHT presented $arOrder - may cause wrong delivery price or no price presented'); }
                    if(!array_key_exists('DIMENSIONS',$value)){ self::WriteSheeplaLog('CDeliverySheepla::Compability() CDeliverySheepla::Calculate()','Dynamic Pricing',$result='', 'NO Field DIMENSIONS presented $arOrder - may cause wrong delivery price or no price presented'); }                
                    if(!array_key_exists('QUANTITY',$value)){ self::WriteSheeplaLog('CDeliverySheepla::Compability() CDeliverySheepla::Calculate()','Dynamic Pricing',$result='', 'NO Field QUANTITY presented $arOrder - may cause wrong delivery price or no price presented'); }
                    if(!array_key_exists('NAME',$value)){ self::WriteSheeplaLog('CDeliverySheepla::Compability() CDeliverySheepla::Calculate()','Dynamic Pricing',$result='', 'NO Field NAME presented $arOrder - may cause wrong delivery price or no price presented'); }
                    /** Checking if dimentions exist
                     * if NO then wite it to log 
                    */               
                    
                    if(array_key_exists('DIMENSIONS',$value)){   
                        
                        if(is_array($value['DIMENSIONS']) && !array_key_exists('WIDTH',$value['DIMENSIONS'])){
                            $tmp_dimentions = (array)unserialize($value['DIMENSIONS']);
                        }else{
                            $tmp_dimentions = (array)$value['DIMENSIONS'];
                        }                        
                            if(!array_key_exists('WIDTH',$tmp_dimentions)){ self::WriteSheeplaLog('CDeliverySheepla::Compability() CDeliverySheepla::Calculate()','Dynamic Pricing',$result='', 'NO Field WIDTH presented $arOrder - may cause wrong delivery price or no price presented'); }
                            if(!array_key_exists('HEIGHT',$tmp_dimentions)){ self::WriteSheeplaLog('CDeliverySheepla::Compability() CDeliverySheepla::Calculate()','Dynamic Pricing',$result='', 'NO Field HEIGHT presented $arOrder - may cause wrong delivery price or no price presented'); }
                            if(!array_key_exists('LENGTH',$tmp_dimentions)){ self::WriteSheeplaLog('CDeliverySheepla::Compability() CDeliverySheepla::Calculate()','Dynamic Pricing',$result='', 'NO Field LENGTH presented $arOrder - may cause wrong delivery price or no price presented'); }
                            
                    }
                }
            }
        /** Validation end */
        
        $products = array();
        if(sizeof($order['ITEMS'])>0){
            foreach($order['ITEMS'] as $key => $value){
                foreach($value as $pKey => $pValue){                
                    if(strpos(" ".(string)$pKey,"~")<1){
                        $products[strtolower($key)][$pKey] = $pValue;
                    }
                }
            }
        }
        $sheepla_model = new SheeplaProxyDataModel();
        $client = new SheeplaClient($sheepla_model->getConfig(), false);  
           
        if (!isset($_SESSION['dp_hash'])|($_SESSION['dp_hash'] != md5(serialize($products).serialize($location).serialize($_carriers).serialize($cartDiscount)))) {                
               /** Getting delivery prices from Sheepla server */
               $_SESSION['dp_response'] = $client->syncDynamicPricing((array)$products, (array)$location, (array)$_carriers);                        
            }            
            $prices = $_SESSION['dp_response'];            
            $_SESSION['dp_hash'] = md5(serialize($products).serialize($location).serialize($_carriers).serialize($cartDiscount));
        /** Checking what to return - carrier price or list of carriers */    
        if(sizeof($_SESSION['dp_response'])>0){
            $all_carriers = self::GetSheeplaCarriers();
            $available_carriers = array();
            
            if($return_array){
                /** Returning available carrier array */
                foreach($_SESSION['dp_response'] as $key_dp_price => $value_dp_price){
                    foreach($all_carriers as $key_carrier => $value_carrier){                       
                        if($value_dp_price['shipmentTemplateId'] == $value_carrier['SHEEPLA_TEMPLATE']){
                            $available_carriers[] = $key_carrier;
                        }
                    }
                }                
                if(sizeof($available_carriers)>0){
                    return $available_carriers;    
                }else{
                    return false;
                }
                
            }else{
                $available_price = -1;
                /** Returning price for carrier profile */
                foreach($_SESSION['dp_response'] as $key_dp_price => $value_dp_price){
                    foreach($all_carriers as $key_carrier => $value_carrier){                        
                        if(($value_dp_price['shipmentTemplateId'] == $value_carrier['SHEEPLA_TEMPLATE'])&&($key_carrier == $profile)){                            
                            $available_price = $value_dp_price['price'];
                        }
                    }
                }                
                if((float)$available_price>0){                    
                    return (float)$available_price;    
                }elseif($available_price==0){
                    return 0;
                }else{
                    return false;
                }
            }
            
        }else{
            return false;
        }   
        
             
    }
    /**
     * Method to return Sheepla templates defined in panel.sheepla.com
     * 
     * */
    public function GetSheeplaTemplates(){
        $sheepla_model = new SheeplaProxyDataModel();
        $client = new SheeplaClient($sheepla_model->getConfig(), false);
        $templates = $client->getShipmentTemplates();
        return $templates;
    }
    /** Method GetSheeplaCarriers returns all available 
     *  carriers from DB
     *  
    */
    public function GetSheeplaCarriers(){
        $sheepla_profiles = array();
        $profiles = CSheeplaDb::GetCarriersFromDB();  
        if(sizeof($profiles)){
            foreach($profiles as $key => $value){
            $sheepla_profiles['sheepla_'.$value['id'].'_'.$value['template_id']] = 
                    array(
                      "TITLE" => $value['title'],
                      "DESCRIPTION" => $value['description'],
                      "RESTRICTIONS_WEIGHT" => array(0), // без ограничений
                      "RESTRICTIONS_SUM" => array(0), // без ограничений
                      "SHEEPLA_TEMPLATE" => $value['template_id'],
                      "SHEEPLA_DB_ID" => $value['id'],
                      "SHEEPLA_SORT" => $value['sort'],
                    );    
            }    
        }
        return  $sheepla_profiles; 
    }
    public function SetSheeplaCarriers($carriers){
        //TODO
        /** Add code - setting carriers to DB*/        
        $carriersAddList = array();
        $j=0; $k=0;
        
        for($i=0;$i<(sizeof($carriers)/5);$i++){
            
            if($carriers['carrier_sheepla_delete_'.$i]!='1'){
                if($carriers['carrier_sheepla_template_'.$i]!=''&&
                   $carriers['carrier_sheepla_title_'.$i]!=''&&                   
                   $carriers['carrier_sheepla_sort_'.$i]!=''){
                    
                    $carriersAddList['carrier_sheepla_description_'.$j] = mysql_escape_string($carriers['carrier_sheepla_description_'.$i]);
                    $carriersAddList['carrier_sheepla_template_'.$j] = mysql_escape_string($carriers['carrier_sheepla_template_'.$i]);
                    $carriersAddList['carrier_sheepla_title_'.$j] = mysql_escape_string($carriers['carrier_sheepla_title_'.$i]);
                    $carriersAddList['carrier_sheepla_db_id_'.$j] = mysql_escape_string($carriers['carrier_sheepla_db_id_'.$i]);
                    $carriersAddList['carrier_sheepla_sort_'.$j] = mysql_escape_string($carriers['carrier_sheepla_sort_'.$i]);
                    $j++;    
                }
                
            }else{
                $carriersDeleteList['carrier_sheepla_description_'.$k] = mysql_escape_string($carriers['carrier_sheepla_description_'.$i]);
                $carriersDeleteList['carrier_sheepla_template_'.$k] = mysql_escape_string($carriers['carrier_sheepla_template_'.$i]);
                $carriersDeleteList['carrier_sheepla_title_'.$k] = mysql_escape_string($carriers['carrier_sheepla_title_'.$i]);
                $carriersDeleteList['carrier_sheepla_db_id_'.$k] = mysql_escape_string($carriers['carrier_sheepla_db_id_'.$i]);
                $carriersDeleteList['carrier_sheepla_sort_'.$k] = mysql_escape_string($carriers['carrier_sheepla_sort_'.$i]);
                $k++;
            }
        }        
        CSheeplaDb::SaveCarriersToDB($carriersAddList,$carriersDeleteList);
        
    }
    /** Function to return array of carriers */
    public function  GetSheeplaProfiles(){
        $profiles = array();
        $carriers = self::GetSheeplaCarriers();
        foreach($carriers as $key => $value){
            $profiles[] = $key;
        }
        return $profiles;
    }
    /** Method to get orders from sheepla table*/
    public function GetSheeplaOrders(){
        $orders = array();
        $orders = CSheeplaDb::GetSheeplaOrders();
        return $orders;
    }    
    /** Method to get order from sheepla table*/
    public function GetSheeplaOrderById($Id){
        return CSheeplaDb::GetSheeplaOrderById($Id);
    }    
    /** Method to write down logs for Logs Config Tab*/
    public function WriteSheeplaLog($place='',$request='',$result='', $additional=''){
        if(sizeof($result)>0){ $result = @serialize($result); }
        $sheeplaLogFile = self::getConfig();
        $sheeplaLogFile = $sheeplaLogFile['logPath'];        
        $contents = file_get_contents($sheeplaLogFile);
        @file_put_contents($sheeplaLogFile,'<? exit(); ?>'.str_replace('<? exit(); ?>','',$contents).'
'.date('H:i:s d-m-Y ',time()).'<br> 
        PLACE: '.$place.'<br> 
        REQUEST: '.$request.'<br> 
        RESULT: '.$result.'<br>
        ADDITIONAL: '.$additional.'<br>
--------------------------------------------------------------------------------------------------------------------------<br>');        
        
        @clearstatcache(); /** Clearing realpathcache*/
        /** Checking filesize and remove 30% if needed. */       
        if(@filesize($sheeplaLogFile)>512000){
            $contents = file_get_contents($sheeplaLogFile);            
            $contents = '<? exit(); ?> '.substr($contents, ((int)strlen($contents)/3),strlen($contents) ); //cutting 30% of text            
            @file_put_contents($sheeplaLogFile, $contents);
        }
    }
    /** Method to read logs for Logs Config Tab*/
    public function ReadSheeplaLog(){
        $sheeplaLogFile = self::getConfig();
        $sheeplaLogFile = $sheeplaLogFile['logPath'];        
        $contents = file_get_contents($sheeplaLogFile);
        return $contents;
    }
    public function GetCultureId(){        
        $default = 1049;   // Russian language by default
        $lang_id = strtolower(LANGUAGE_ID);        
        $langs = array(
                       'pl' => 1045,
                       'de' => 1031,
                       'en' => 1033,
                       'ru' => 1049,
                       'cs' => 1029
                       );

        return isset($lang[$lang_id]) ? $lang[$lang_id] : $default;
    }
    public function GetProductAdditional($orderId,$productId){
        $productsAdditional = CSheeplaDb::GetProductAdditionalData($orderId,$productId);
        $item = array();
        $item['ean8'] = '';
        $item['ean13'] = '';
        $item['issn'] = '';
        $item['unit'] = '';
        $item['sku'] = '';
        if(sizeof($productsAdditional)>0){
            foreach($productsAdditional as $key => $param){
                    if (preg_match('/bar_code/i', $param['CODE']))
                    {
                      switch (strlen($param['VALUE'])) {
                      case 8:
                        $item['ean8'] = preg_replace("/[^0-9]/","", $param['VALUE']);
                        break;
                      case 13:
                        $item['ean13'] = preg_replace("/[^0-9]/","", $param['VALUE']);
                        break;
                      default:
                        $item['issn'] = preg_replace("/[^0-9]/","", $param['VALUE']);
                        break;
                      }
                    }
        					
                  if (preg_match('/article/i', $param['CODE'])) {
                    $item['sku'] = SheeplaHelper::custom_iconv($param['VALUE']);
                  }
        					
                  if (preg_match('/base\_unit/i', $param['CODE'])) {
                    $item['unit'] = SheeplaHelper::custom_iconv($param['VALUE']);
                  }
            }
            
        }
        if (empty($item['sku'])) {
          $item['sku'] = $productId;
        }
        return $item;
        
    }
    /** Method */
    /**
   * Wrapper for iconv() function.
   */
    public function CustomIconv($string) {        
        $charset = CSheeplaDb::GetDatabaseEncoding($string);
        if(strtoupper($charset['x']) == 'UTF8') {
          $charset['x'] = 'UTF-8';
        }
        return iconv($charset['x'], 'UTF-8', $string);
    }
    
/** Event handlers */
    /** */
    public function OnLocationUpdate($LOCATION_ID){
        $_SESSION['SHEEPLA_LOCATION_ID'] = $LOCATION_ID;
    }
    /** Add header scripts and css*/
    public function OnPageStartAddHeaders(){
        global $APPLICATION;        
        $sheeplaConfig = self::getConfig();        
        if(strpos(' '.$APPLICATION->GetCurPage(),$sheeplaConfig['checkout'])>0){
            $APPLICATION->AddHeadString('<link rel="stylesheet" type="text/css" href="'.$sheeplaConfig['cssUrl'].'" />');
            $APPLICATION->AddHeadString('<script type="text/javascript" src="'.$sheeplaConfig['jsUrl'].'"></script>');
            //$APPLICATION->AddHeadString('<script type="text/javascript">sheepla.init({apikey: \''.$sheeplaConfig['publicApiKey'].'\',cultureId: '.self::GetCultureId().'});</script>');
            $APPLICATION->AddHeadString(self::PrepareCarrierJs());                        
        }elseif( strpos($APPLICATION->GetCurPage(),$sheeplaConfig['adminOrderAddUrl'])>0 || strpos($APPLICATION->GetCurPage(), $sheeplaConfig['adminOrderEditUrl'])>0 ){
            //Add JS code for order edit page
            $APPLICATION->AddHeadString('<link rel="stylesheet" type="text/css" href="'.$sheeplaConfig['cssUrl'].'" />');
            $APPLICATION->AddHeadString('<script type="text/javascript" src="'.$sheeplaConfig['jsUrl'].'"></script>');
            $APPLICATION->AddHeadString(self::PrepageEditPageJs());
	    }elseif(strpos($APPLICATION->GetCurPage(),$sheeplaConfig['adminOrderViewUrl'])>0){
	        //Add JS code for order edit page
            $APPLICATION->AddHeadString('<link rel="stylesheet" type="text/css" href="'.$sheeplaConfig['cssUrl'].'" />');
            $APPLICATION->AddHeadString('<script type="text/javascript" src="'.$sheeplaConfig['jsUrl'].'"></script>');
            $APPLICATION->AddHeadString(self::PrepareAdminOrderViewJs());   
	    }
        //TODO 
        /** Add functionality */
    }
    /**
      * Event for create/edit new order
        */
    public function OnOrderAdd($id, $order) {
        //TODO
        /** Verify if thios function is needed*/
    }
    /**
      * Event for create/edit new order
        */
    public function OnOrderUpdate($orderId, $orderFields) {
        $FUSER_ID = CSaleBasket::GetBasketUserID();
        if($orderId&&!$orderFields['LOCKED_BY']){
            $data = CSheepla::PrepareAdditionalData($_POST);            
            CSheeplaDb::UpdateCartID($FUSER_ID,$orderId,$data);
        }
    }
    public function PrepareAdditionalData($myPost){
        $data = array();
        foreach($myPost as $key => $value){
            if(strpos(' '.$key,'sheepla')>0){
                $data[$key] = $value;
            }
        }
        return $data;
    }
    public function PrepareCarrierJs(){             
        $FUSER_ID = CSaleBasket::GetBasketUserID();
        $sheeplaConfig = self::getConfig();
        $culture = self::GetCultureId();
        $email = '';
        $users_mobile = '';
        $baseURI = '';
        $_carriers = self::GetSheeplaCarriers();
        $cityName = '';
        $recipientName = '';
         
        $carrier_js = '';
        foreach($_carriers as $key => $value){            
            $jQSelector = str_replace('{SHEEPLA_DB_ID}',$value['SHEEPLA_DB_ID'],$sheeplaConfig['jQDeliverySelector']);
            $jQSelector = str_replace('{SHEEPLA_TEMPLATE}',$value['SHEEPLA_TEMPLATE'],$jQSelector);            
            $jQSelectorShort = $sheeplaConfig['jQDeliverySelectorShort'];
            $jQLocationSelector = $sheeplaConfig['jQLocationSelector'];
            $jQLabelSelector = $sheeplaConfig['jQLabelSelector'];
            
            
                    $carrier_js .= <<< JSSTRING
    if( ( (sheepla.query('#sheepla_template_id').val() != {$value['SHEEPLA_TEMPLATE']}) && (typeof sheepla.query('{$jQSelector}').attr('checked') != 'undefined'  ) )
        || ( (typeof sheepla.query('#sheepla_template_id').val() == 'undefined') && (typeof sheepla.query('{$jQSelector}').attr('checked') != 'undefined'  ) )
     
    ){  
        sheepla.query('.sheepla-bitrix-sdk-area').remove();
        if(typeof sheepla.query('{$jQSelector}:checked'){$jQLabelSelector} != 'undefined'){
            sheepla.query('{$jQSelector}:checked'){$jQLabelSelector}.append('<table class="sheepla-bitrix-sdk-area"><tr class="sheepla-sdk-area"><td colspan="100%" id="sheepla-sdk-draw-area"></td></tr><input class="sheepla-sdk-area" id="sheepla_template_id" type="hidden" name="sheepla_template" value="{$value['SHEEPLA_TEMPLATE']}" ></table>');            
        }else{
            if(typeof sheepla.query('{$jQSelector}:checked').parent().children('label') != 'undefined'){
                sheepla.query('{$jQSelector}:checked').parent().children('label').append('<table class="sheepla-bitrix-sdk-area"><tr class="sheepla-sdk-area"><td colspan="100%" id="sheepla-sdk-draw-area"></td></tr><input class="sheepla-sdk-area" id="sheepla_template_id" type="hidden" name="sheepla_template" value="{$value['SHEEPLA_TEMPLATE']}" ></table>');    
            }else{
                sheepla.query('{$jQSelector}:checked').parent().children('label').append('<table class="sheepla-bitrix-sdk-area"><tr class="sheepla-sdk-area"><td colspan="100%" id="sheepla-sdk-draw-area"></td></tr><input class="sheepla-sdk-area" id="sheepla_template_id" type="hidden" name="sheepla_template" value="{$value['SHEEPLA_TEMPLATE']}" ></table>');
            }
        }
        
        if( ((sheepla.query('#sheepla-sdk-draw-area').html() == null) || (sheepla.query('#sheepla-sdk-draw-area').html() == '')) ){            
            var info = {
                carrier: 'SheeplaCarrier',                
                user: '{$FUSER_ID}',
                LOCATION: {$jQLocationSelector},                
                action: 'GetUserCartCity',
            };
            sheepla.query.ajax({
                type: "POST",
                url: '{$baseURI}/bitrix/modules/sheepla.delivery/ajax.php?action=GetUserCartData',
                data: info,
                async: false,
                success: function(data){ sheepla.get_special({$value['SHEEPLA_TEMPLATE']}, '#sheepla-sdk-draw-area', '', '', data); }          
                });
        }    
    }    

    
JSSTRING;
       $carriers_list[] = $key;
            }
       $carrier_js = <<< JSSTRING
       setInterval(function(){
        $carrier_js        
       }, 50);

    
JSSTRING;
            $carriers_list = json_encode($carriers_list);
            
         $string = <<< STRING
<script type="text/javascript">
    sheepla.config = {
        apikey: '{$sheeplaConfig['publicApiKey']}',
        cultureId: {$culture}
    };
    sheepla.init();
    sheepla.share.set_cookie('sheepla-email', '{$email}', 30);
    sheepla.share.set_cookie('sheepla-phone', '{$users_mobile}', 30);
    sheepla.user.after_draw_special = function(area) {
        if (sheepla.query(area).text() == '') {
            sheepla.query(area).parent().hide();
        }
    };

sheepla.user.after_draw_special = function(area) {
       var childsMy2 = sheepla.query("#sheepla-widget-control input[name^='sheepla-widget-']");
       var POPS = '';
         sheepla.query.each(childsMy2, function (index, v) { POPS =  POPS + sheepla.query(v).attr("name")+'='+v.value+'|'; });
        var childsMy3 = sheepla.query("#sheepla-widget-control select[name^='sheepla-widget-']");
        sheepla.query.each(childsMy3, function (index, v) { POPS =  POPS + sheepla.query(v).attr("name")+'='+v.value+'|'; });

        var carriersIds = 'none';
        if(typeof sheepla.query('{$jQSelectorShort}:checked').val() != 'undefined'){
            carriersIds = sheepla.query('{$jQSelectorShort}:checked').val();
        }
        var info = {
                carrier: 'SheeplaCarrier',
                POP: POPS,
                user: '{$FUSER_ID}',
                carrierId: carriersIds,
                action: 'StorePostomat',
            };
        sheepla.query.ajax({
            type: "POST",
            url: '{$baseURI}/bitrix/modules/sheepla.delivery/ajax.php?action=StorePostomat',
            data: info,
            async: true
            });

        if (sheepla.query(area).text() == '') {    sheepla.query(area).parent().hide(); }
    };

sheepla.user.after.ui.unlock_screen = function(area){
        var childsMy2 = sheepla.query("#sheepla-widget-control input[name^='sheepla-widget-']");
        var POPS = '';
        sheepla.query.each(childsMy2, function (index, v) { POPS =  POPS + sheepla.query(v).attr("name")+'='+v.value+'|'; });
        var childsMy3 = sheepla.query("#sheepla-widget-control select[name^='sheepla-widget-']");
        sheepla.query.each(childsMy3, function (index, v) { POPS =  POPS + sheepla.query(v).attr("name")+'='+v.value+'|'; });

        var carriersIds = 'none';
        if(typeof sheepla.query('{$jQSelectorShort}:checked').val() != 'undefined'){
            carriersIds = sheepla.query('{$jQSelectorShort}:checked').val();
        }

        var info = {
                carrier: 'SheeplaCarrier',
                POP: POPS,
                user: '{$FUSER_ID}',
                carrierId: carriersIds,
                action: 'StorePostomat',
            };

        sheepla.query.ajax({
            type: "POST",
            url: '{$baseURI}/bitrix/modules/sheepla.delivery/ajax.php?action=StorePostomat',
            data: info,
            async: true
            });

    };
/* Begin Shop logistics metro station */
sheepla.query('#sheepla-widget-rushoplogistics-metro-station').live('change',function(){ sheepla.user.after.ui.unlock_screen(); });
/* End Shop logistics metro station */

sheepla.call_registry.one = function () {
        sheepla.query('.sheepla-bitrix-sdk-area').remove();
        sheepla.vars.validation = 1;

     {$carrier_js}
    
        sheepla.query('{$jQSelectorShort}').parents().find('form').submit(function () {
            if (( !sheepla.valid_special(false, true) ) || ( sheepla.vars.validation==0)) {  
                sheepla.query('#confirmorder').val('N'); /*return false;*/    
            }else{
                /*sheepla.query('#confirmorder').val('Y'); /*return false;*/
            }
        });
        sheepla.query('body').delegate('.checkout','click',function () {
             if (( !sheepla.valid_special(false, true) ) || ( sheepla.vars.validation==0)) {  
                sheepla.query('#confirmorder').val('N'); /*return false;*/    
            }else{
                /*sheepla.query('#confirmorder').val('Y'); /*return false;*/
            }
        });

        sheepla.query('.bx_ordercart_order_pay_center a').click(function () {
             if (( !sheepla.valid_special(false, true) ) || ( sheepla.vars.validation==0)) {  
                sheepla.query('#confirmorder').val('N'); /*return false;*/    
            }else{
                /*sheepla.query('#confirmorder').val('Y'); /*return false;*/
            }
        });

    };
if( (sheepla.query('.sheepla-bitrix-sdk-area').html() == null) || (sheepla.query('.sheepla-bitrix-sdk-area').html() == '') ){
    sheepla.call_registry.one();
}
</script>

STRING;
return $string;
    }
    
    public function PrepareAdminOrderViewJs(){
        $sheeplaConfig = self::getConfig();
        $culture = self::GetCultureId();
        $order = (int)$_GET['ID'];
        $baseURI = '';
        $successOrderText = 'Order will be synchronized soon.';
        $errorOrderText = 'Error ocurred.';
        //TODO
        /** Get ajax to resync order */
        
        $return = <<< STRING
        <style>
            #sheepla-sdk-area{
                width:500px;
                margin:0 auto;
            }
            .sheepla-resync-link-button{
                float: right; 
            }
        </style>
        <script type="text/javascript">
        sheepla.config = {
    		  apikey: '{$sheeplaConfig['adminApiKey']}',
    		  cultureId: {$culture}
        	};
        	sheepla.init();
        function ResyncOrder(){
            var info = {
                SheeplaKey: '{$sheeplaConfig['adminApiKey']}',                
                action: 'ResendCreateOrder',
                orderId: '{$order}',
            };
            sheepla.query.ajax({
                type: "POST",
                url: '{$baseURI}/bitrix/modules/sheepla.delivery/ajax.php',
                data: info,
                success: function(data){                    
                    if(data=='true'){
                        sheepla.query('.sheepla-resync-link-button').hide();
                        alert('{$successOrderText}');
                    }else{
                        alert('{$errorOrderText}');
                    }
                },                
                async: true            
            });
        }
        sheepla.query(document).ready(function(){            
            sheepla.query('{$sheeplaConfig['orderViewSheeplaSelector']}').after('<tr><td colspan="2"><div id="sheepla-sdk-area">&nbsp;</div> <a href="#" class="sheepla-resync-link-button" onclick="ResyncOrder(); return false;" target="_blank">Resync Order Text</a> <br></td></tr>');            
		    sheepla.get_shipment_status_standard({$order}, '#sheepla-sdk-area', 1);    
        });
		</script>
        
        		     
           
STRING;
        return $return;
    }
    
    

    public function PrepageEditPageJs(){
        $FUSER_ID = CSaleBasket::GetBasketUserID();
        $sheeplaConfig = self::getConfig();
        $culture = self::GetCultureId();
        
        $order = (int)$_GET['ID'];
        
        $email = '';
        $users_mobile = '';
        $baseURI = '';
        $_carriers = self::GetSheeplaCarriers();
        $cityName = '';
        $recipientName = '';
         
        $carrier_js = '';
        foreach($_carriers as $key => $value){
            $jQSelector = $sheeplaConfig['adminOrderjQSelector'];
            $jQSelectorShort = $sheeplaConfig['adminOrderjQSelectorShort'];
            $jQLocationSelector = $sheeplaConfig['adminOrderjQLocationSelector'];
            $jQLabelSelector = $sheeplaConfig['adminOrderjQLabelSelector'];
            
                    $carrier_js .= <<< JSSTRING
                    
    if( ( ( (sheepla.query('#sheepla_template_id').val() != {$value['SHEEPLA_TEMPLATE']}) ) || ( (typeof sheepla.query('#sheepla_template_id').val() == 'undefined') ) )
        && (current_tpl == {$value['SHEEPLA_TEMPLATE']})
        ){  
        sheepla.query('.sheepla-bitrix-sdk-area').remove();
        if(typeof sheepla.query('{$jQSelector} option:selected'){$jQLabelSelector} != 'undefined'){
            sheepla.query('{$jQSelector}'){$jQLabelSelector}.append('<table class="sheepla-bitrix-sdk-area"><tr class="sheepla-sdk-area"><td colspan="100%" id="sheepla-sdk-draw-area"></td></tr><input class="sheepla-sdk-area" id="sheepla_template_id" type="hidden" name="sheepla_template" value="{$value['SHEEPLA_TEMPLATE']}" ></table>');            
        }
        
        if( ((sheepla.query('#sheepla-sdk-draw-area').html() == null) || (sheepla.query('#sheepla-sdk-draw-area').html() == '')) ){            
            var info = {
                carrier: 'SheeplaCarrier',                
                user: '{$FUSER_ID}',
                LOCATION: '{$jQLocationSelector}',                
                action: 'GetUserCartCity',
                orderId: '{$order}',
            };
            sheepla.query.ajax({
                type: "POST",
                url: '{$baseURI}/bitrix/modules/sheepla.delivery/ajax.php?action=GetUserCartData',
                data: info,
                async: false,
                success: function(data){ sheepla.get_special({$value['SHEEPLA_TEMPLATE']}, '#sheepla-sdk-draw-area', '', '', data); }          
                });
        }    
    }    

    
JSSTRING;
       $carriers_list[] = $key;
            }
       $carrier_js = <<< JSSTRING
setInterval(function(){
        current_tpl = '';
        current_tpl = sheepla.query('{$jQSelector} option:selected').val();
        current_tpl = current_tpl.split("_");    
        current_tpl = current_tpl[current_tpl.length-1];
                
        $carrier_js
                
       }, 500);

    
JSSTRING;
            $carriers_list = json_encode($carriers_list);

        $return = <<< STRING
<style>
    #sheepla-widget-control input[type="button"]{
        background: #DD7B24 !important;
    }
</style>
<script type="text/javascript">
    sheepla.config = {
        apikey: '{$sheeplaConfig['publicApiKey']}',
        cultureId: {$culture}
    };
    sheepla.init();
    sheepla.share.set_cookie('sheepla-email', '{$email}', 30);
    sheepla.share.set_cookie('sheepla-phone', '{$users_mobile}', 30);
    sheepla.user.after_draw_special = function(area) {
        if (sheepla.query(area).text() == '') {
            sheepla.query(area).parent().hide();
        }
    };

sheepla.user.after_draw_special = function(area) {
       var childsMy2 = sheepla.query("#sheepla-widget-control input[name^='sheepla-widget-']");
       var POPS = '';
         sheepla.query.each(childsMy2, function (index, v) { POPS =  POPS + sheepla.query(v).attr("name")+'='+v.value+'|'; });
        var childsMy3 = sheepla.query("#sheepla-widget-control select[name^='sheepla-widget-']");
        sheepla.query.each(childsMy3, function (index, v) { POPS =  POPS + sheepla.query(v).attr("name")+'='+v.value+'|'; });

        var carriersIds = 'none';
        if(typeof sheepla.query('{$jQSelectorShort} option:selected').val() != 'undefined'){
            carriersIds = sheepla.query('{$jQSelectorShort} option:selected').val();
        }
        var info = {
                carrier: 'SheeplaCarrier',
                POP: POPS,
                user: '{$FUSER_ID}',
                carrierId: carriersIds,
                action: 'StorePostomat',
                orderId: '{$order}',
            };
        sheepla.query.ajax({
            type: "POST",
            url: '{$baseURI}/bitrix/modules/sheepla.delivery/ajax.php?action=StorePostomat',
            data: info,
            async: true
            });

        if (sheepla.query(area).text() == '') {    sheepla.query(area).parent().hide(); }
    };

sheepla.user.after.ui.unlock_screen = function(area){
        var childsMy2 = sheepla.query("#sheepla-widget-control input[name^='sheepla-widget-']");
        var POPS = '';
        sheepla.query.each(childsMy2, function (index, v) { POPS =  POPS + sheepla.query(v).attr("name")+'='+v.value+'|'; });
        var childsMy3 = sheepla.query("#sheepla-widget-control select[name^='sheepla-widget-']");
        sheepla.query.each(childsMy3, function (index, v) { POPS =  POPS + sheepla.query(v).attr("name")+'='+v.value+'|'; });

        var carriersIds = 'none';
        if(typeof sheepla.query('{$jQSelectorShort} option:selected').val() != 'undefined'){
            carriersIds = sheepla.query('{$jQSelectorShort} option:selected').val();
        }

        var info = {
                carrier: 'SheeplaCarrier',
                POP: POPS,
                user: '{$FUSER_ID}',
                carrierId: carriersIds,
                action: 'StorePostomat',
                orderId: '{$order}',
            };

        sheepla.query.ajax({
            type: "POST",
            url: '{$baseURI}/bitrix/modules/sheepla.delivery/ajax.php?action=StorePostomat',
            data: info,
            async: true
            });

    };
/* Begin Shop logistics metro station */
sheepla.query('#sheepla-widget-rushoplogistics-metro-station').live('change',function(){ sheepla.user.after.ui.unlock_screen(); });
/* End Shop logistics metro station */

sheepla.call_registry.one = function () {
        sheepla.query('.sheepla-bitrix-sdk-area').remove();
        sheepla.vars.validation = 1;

     {$carrier_js}

    
        sheepla.query('{$jQSelectorShort}').parents().find('form').submit(function () {
            if (( !sheepla.valid_special(false, true) ) || ( sheepla.vars.validation==0)) { return false;    }
        });
        sheepla.query('body').delegate('.checkout','click',function () {
            if (( !sheepla.valid_special(false, true) ) || ( sheepla.vars.validation==0)) { return false;    }
        });

        sheepla.query('.bx_ordercart_order_pay_center a').click(function () {

            if (( !sheepla.valid_special(false, true) ) || ( sheepla.vars.validation==0)) { return false;    }
        });

    };
if( (sheepla.query('.sheepla-bitrix-sdk-area').html() == null) || (sheepla.query('.sheepla-bitrix-sdk-area').html() == '') ){
    sheepla.call_registry.one();
}
</script>

STRING;
    return $return;
        

    }
}

?>