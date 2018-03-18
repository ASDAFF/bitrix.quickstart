<?
Class CSheeplaDb{

    public function SaveCarriersToDB($carriersAdd,$carriersDelete){        
        global $DB;
        /** Deleting carriers */
        for($i=0;$i<(sizeof($carriersDelete)/5);$i++){            
            $db_result = $DB->Query("Delete FROM `b_sheepla_carriers` WHERE `id` = " . (int) $carriersDelete['carrier_sheepla_db_id_'.$i]);            
        }
        /** Updating carriers Adding new carriers */
        for($i=0;$i<(sizeof($carriersAdd)/5);$i++){      
            if(isset($carriersAdd['carrier_sheepla_db_id_'.$i]) && $carriersAdd['carrier_sheepla_db_id_'.$i]!='' ){
                $db_result = $DB->Query("UPDATE `b_sheepla_carriers` SET 
                                            `description` = '".$carriersAdd['carrier_sheepla_description_'.$i]."', 
                                            `template_id` = '".(int)$carriersAdd['carrier_sheepla_template_'.$i]."',
                                            `title` = '".$carriersAdd['carrier_sheepla_title_'.$i]."',
                                            `sort` = '".(int)$carriersAdd['carrier_sheepla_sort_'.$i]."'                                            
                                        WHERE `id` = '".(int)$carriersAdd['carrier_sheepla_db_id_'.$i]."'");                
            }else{                 
                $db_result = $DB->Query("INSERT INTO `b_sheepla_carriers` (`id`, `title`, `description`,`template_id`, `sort`) VALUES 
                                                ('', '".$carriersAdd['carrier_sheepla_title_'.$i]."', '".$carriersAdd['carrier_sheepla_description_'.$i]."','".(int)$carriersAdd['carrier_sheepla_template_'.$i]."','".(int)$carriersAdd['carrier_sheepla_sort_'.$i]."')");
            }
            
        }
    }
    
    public function GetCarriersFromDB(){
        global $DB;
        $db_result = $DB->Query("SELECT * FROM `b_sheepla_carriers` WHERE 1 ORDER BY `sort`");
        while ($data = $db_result->Fetch()) {
          $return[] = $data;
        }        
        return $return;
    }
    public function StorePostomatToDb($myPost,$pop){
        global $DB;
        if($myPost['orderId']){
            $where = ' AND `order_id` = "'.(int)$myPost['orderId'].'"';
        }else{
            $where = '';
        }
        $db_result = $DB->Query("SELECT * FROM `b_sheepla_orders` WHERE `fuser_id` = '".(int)$myPost['user']."' ".$where);
        $res = $db_result->Fetch();        
        if($res){
            $query = 'Update `b_sheepla_orders` SET `additional` = "'.addslashes(serialize($pop)).'", `send` = "0" WHERE `id` = "'.(int)$res['id'].'"';
            $result = $DB->Query($query);
            CSheepla::WriteSheeplaLog('CSheeplaDb::StorePostomatToDb()',$query,$result);
            
        }else{
            $query = 'Insert Into `b_sheepla_orders` (`id`, `order_id`, `fuser_id`, `additional`, `send`) VALUES 
                                ("","0","'.(int)$myPost['user'].'","'.addslashes(serialize($pop)).'","0")';
            $result = $DB->Query($query);
            CSheepla::WriteSheeplaLog('CSheeplaDb::StorePostomatToDb()',$query,$result);    
        }
        
    }
    public function UpdateCartID($fuserId,$orderId,$data=''){
        global $DB;
        $db_result = $DB->Query("SELECT * FROM `b_sheepla_orders` WHERE `fuser_id` = '".(int)$fuserId."' AND `order_id` = '0' ");
        $res = $db_result->Fetch();        
        if($res){
            $query = 'Update `b_sheepla_orders` SET `order_id` ="'.(int)$orderId.'" WHERE `id` = "'.(int)$res['id'].'"';
            $result = $DB->Query($query);
            CSheepla::WriteSheeplaLog('CSheeplaDb::UpdateCartID()',$query,$result);
        }else{
                        
            $db_result = $DB->Query("SELECT * FROM `b_sheepla_orders` WHERE `order_id` = '".$orderId."' ");
            $res = $db_result->Fetch();

            if(!$res){
                $query = 'Insert Into `b_sheepla_orders` (`id`, `order_id`, `fuser_id`, `additional`, `send`) VALUES 
                                ("","'.(int)$orderId.'","'.(int)$fuserId.'","'.addslashes(serialize($data)).'","0")';
                $result = $DB->Query($query);
                CSheepla::WriteSheeplaLog('CSheeplaDb::UpdateCartID()',$query,$result);    
            }
        }
    }
    public function GetSheeplaOrders(){
        global $DB;
        $return = array();
        $db_result = $DB->Query("SELECT * FROM `b_sheepla_orders` WHERE ((`send` < 7 AND `send` >1) OR `send` = 0) ORDER BY `order_id` DESC Limit 20");        
        while($res = $db_result->Fetch()){            
            $return[]=$res;
        }          
        return $return; 
    }
    public function GetSheeplaOrdersEx($start=0, $amount=20){
        global $DB;
        $return = array();
	$limit = ($start !== false && $amount !== false) ? " LIMIT {$start}, {$amount}" : "";
        $db_result = $DB->Query("SELECT * FROM `b_sheepla_orders`".$limit);        
        while($res = $db_result->Fetch()){            
            $return[]=$res;
        }          
        return $return; 
    }
    public function GetSheeplaOrderById($Id){
        global $DB;
        $return = array();
        $db_result = $DB->Query("SELECT * FROM `b_sheepla_orders` WHERE `order_id` = '{$Id}' LIMIT 1");        
        while($res = $db_result->Fetch()){            
            return $res;
        }          
    }
    public function GetProductAdditionalData($orderId,$productId){
        global $DB;
        if(!$orderId || !$productId){
            return array();
        }
        $db_product_detail = $DB->Query('SELECT * FROM `b_sale_basket` AS b
					LEFT JOIN `b_iblock_element_property` AS ep ON (b.PRODUCT_ID = ep.IBLOCK_ELEMENT_ID)
					LEFT JOIN `b_iblock_property` AS p ON (ep.IBLOCK_PROPERTY_ID = p.ID)
					WHERE b.ORDER_ID = '.(int)$orderId.' AND b.PRODUCT_ID = ' . (int)$productId);
        $productProperties = array();
        while ($param = $db_product_detail->Fetch()) {
            $productProperties[] = $param;
        }
    }
    public function GetDatabaseEncoding($string){
        global $DB;
        $res = $DB->Query("SELECT CHARSET('".mysql_escape_string($string)."') AS x");
        $charset = $res->Fetch();
        return $charset;
    }
    public function MarkOrderSent($orderId=0){
        global $DB;
        $result = $DB->Query("SELECT `id` FROM `b_sheepla_orders` WHERE `order_id` = " . (int)$orderId);
        $order = $result->Fetch();        
        if(isset($order['id'])){
            $result = $DB->Query("UPDATE `b_sheepla_orders` SET `send` = 1 WHERE `id` = " . (int)$order['id']);
            if($result)  return true;
        }
        return false;
    }
    public function MarkOrderReSend($orderId=0){
        global $DB;
        $result = $DB->Query("SELECT `id` FROM `b_sheepla_orders` WHERE `order_id` = " . (int)$orderId);
        $order = $result->Fetch();        
        if(isset($order['id'])){
            $result = $DB->Query("UPDATE `b_sheepla_orders` SET `send` = 0 WHERE `id` = " . (int)$order['id']);
            if($result)  return true;
        }
        return false;
    }
    public function MarkOrderError($orderId=0){
        global $DB;
        $result = $DB->Query("SELECT `id` FROM `b_sheepla_orders` WHERE `order_id` = " . (int)$orderId);
        $order = $result->Fetch();        
        if(isset($order['id'])){
            $result = $DB->Query("UPDATE `b_sheepla_orders` SET `send` = (`send`+2) WHERE `id` = " . (int)$order['id']);
            if($result)  return true;
        }
        return false;
    }
    public function MarkOrdersToForce($amount=0){
        global $DB;        
            $result = $DB->Query("UPDATE `b_sheepla_orders` SET `send` = 0 WHERE `id` IN(SELECT `id` FROM `b_sheepla_orders` WHERE 1 LIMIT ".(int)$amount.")");
            if($result)  return true;        
        return false;
    }
}
?>