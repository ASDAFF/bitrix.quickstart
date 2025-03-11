<?php
class sest{	
	public static function dd( $data, $exit = false ){
	//global $USER;
	//if ($USER->IsAdmin()){
	    if($_SERVER['REMOTE_ADDR'] == '217.20.169.205') {
	        echo "<pre style='background: #000000; color: gold; font-size: 14px;'>";
	        print_r($data);
	        echo "</pre>";
	        if ($exit)
	            exit;
	    }
		//}
	}
	
	/**
	 * @param array $array
	 * @param $path
	 * @param $filename
	 */
	public static function wd( $array=array(), $path, $filename ){
	    $str = '';
	    foreach($array as $key => $val){
	        $str .= $key.'=>'.json_encode($val)."\n";
	    }
	
	    file_put_contents($path . '/' . $filename, $str);
	}
	
	
	
	
		
	function __construct($argument) {
		//self::$site = $_SERVER['SERVER_NAME'];
		//self::$fullUrl = $_SERVER['SERVER_NAME'] . $this->url();
	}
		
	/**
	 * checkout of submit form
	 */
	public static function subForm( $fild ){
		if( isset($_REQUEST[$fild]) && !empty($_REQUEST[$fild]) ){
			return true;
		}else {
			return false;
		}		
	}
	
	/**
	 * get value of form input 
	 */
	public static function getFormInputVal( $fild ){
		if( isset($_REQUEST[$fild]) && !empty($_REQUEST[$fild]) ){
			return $_REQUEST[$fild];
		}else{
			return false;
		}		
	}
		
	/**
	 * get current url
	 */
	public static function url($full) {
			 if (isset($_SERVER['REQUEST_URI'])) {
			    $uri = $_SERVER['REQUEST_URI'];
			  }
			  else {
			    if (isset($_SERVER['argv'])) {
			      $uri = $_SERVER['SCRIPT_NAME'] .'?'. $_SERVER['argv'][0];
			    }
			    elseif (isset($_SERVER['QUERY_STRING'])) {
			      $uri = $_SERVER['SCRIPT_NAME'] .'?'. $_SERVER['QUERY_STRING'];
			    }
			    else {
			      $uri = $_SERVER['SCRIPT_NAME'];
			    }
			  }
			  // Prevent multiple slashes to avoid cross site requests via the FAPI.
			  $uri = '/'. ltrim($uri, '/');
			  
			   
			   
			   $host = $_SERVER['HTTP_HOST'];
		       if($_SERVER['SERVER_PORT'] <> 80 && $_SERVER['SERVER_PORT'] <> 443 && $_SERVER['SERVER_PORT'] > 0 && strpos($_SERVER['HTTP_HOST'], ":") === false)
		       {
		           $host .= ":".$_SERVER['SERVER_PORT'];
		       }
		
		       $protocol = (CMain::IsHTTPS() ? "https" : "http");
		
			  
			  if($full){
			  	$uri =  $protocol."://".$_SERVER['SERVER_NAME']. $uri;
			  }
		
	  return $uri;
	}
	
	
	/**
	 * getFullUrl() - get full site url
	 */
	public static function getFullUrl(){		
		return  $_SERVER['SERVER_NAME'].self::url();
	}	
	
	
	/**
	 * getHost() - get host of site
	 */
	public static function getHost(){		
		return  $_SERVER['SERVER_NAME'];
	}	
	
	
	/**
	 * check params
	 */
	public static function checkPar( $arg ){
		if ( isset($arg) && !empty($arg) ){
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * checkReq() - check REQUEST params 
	 */
	public static function checkReq( $arg ){
		if ( isset($_REQUEST[$arg]) && !empty($_REQUEST[$arg]) )
			return true;
		else
			return false;	
	}
	
	/**
	 * getCleanStr() - clean string
	 * $arg - string
	 */
	public static function getCleanStr( $arg ){
		return htmlspecialchars(trim($arg));
	}
	
	
	
	/**
	 * getPropIDs() - get IDs by property code
	 * 
	 * $propertyCode - property code TYPE OF PROPERTY = LIST
	 * $iblockId - id info block
	 * $propVal - value of property
	 */
	public static function getPropIDs( $propertyCode, $iblockId, $propVal ){
		$propName = 'PROPERTY_' . $propertyCode .'_VALUE';
		$resDB = CIBlockElement::GetList( Array("SORT"=>"ASC"), Array('IBLOCK_ID'=>$iblockId, $propName => $propVal), false, false, Array() );		
		$arrIDs = array();		
		while ($arRes = $resDB->fetch()) {
			$arrIDs[] = $arRes['ID'];
		}	
		
		return $arrIDs;
	}
	
	
	/**
	 * getSectionIDs - get ids of elements by section id
	 * $iblockId - IBLOCK_ID
	 * $sectionId - SECTION_ID
	 */
	public static function getSectionIDs( $iblockId, $sectionId ){
		 $resElDB = CIBlockElement::GetList( Array("SORT"=>"ASC"), Array('IBLOCK_ID'=>$iblockId, 'SECTION_ID'=>$sectionId), false, false, Array() );
		 $arrIDs = array();
		 while ( $res = $resElDB->fetch() ) {				 
			 $arrIDs[] = $res['ID'];
		 }
		
		return 	$arrIDs;
	}
	
	
	/**
	 * getPropIDsByID() - get IDs by property code for TYPE OF PROPERTY = binding to the information block elements
	 * 
	 * $propertyCode - property code 
	 * $iblockId - id info block 
	 */
	public static function getPropIDsByID( $propertyCode, $iblockId, $idProduct ){
		$propName = 'PROPERTY_' . $propertyCode;
		$propNameValue = 'PROPERTY_' . $propertyCode .'_VALUE';
		$resDB = CIBlockElement::GetList( Array("SORT"=>"ASC"), Array('IBLOCK_ID'=>$iblockId, 'ID'=>$idProduct), false, false, Array('ID', 'NAME', 'IBLOCK_ID', $propName) );				
		$arrIDs = array();		
		while ($arRes = $resDB->fetch()) {
			$arrIDs[] = $arRes[$propNameValue];
		}	
		
		return $arrIDs;
	}	
	

	/*
	 * getCountIDsHL() - get count of ids products of current user
	 * 
	 * $idHL - id hightload block
	 */
	public static function getCountIDsHL( $idHL ){		
		global $USER;
	    CModule::IncludeModule("highloadblock"); 
			    
	    $hlbl = $idHL; 
	    $hlblock = Bitrix\Highloadblock\HighloadBlockTable::getById($hlbl)->fetch(); 
	    $entity = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock); 
	    $entity_data_class = $entity->getDataClass();   
	    
	    $rsData = $entity_data_class::getList(array(
	       "select" => array("*"),
	       "order" => array("ID" => "ASC"),
	       "filter" => array('UF_USER' => $USER->GetID())
	    ));
	    while($arData = $rsData->Fetch())
	       $arResult['WISH_LIST'][] = $arData;   //весь мой список желаний  конкретного user
	    
	    foreach ($arResult['WISH_LIST'] as $key => $value) 
	        $arIDs[] = $value['UF_PRODUCT'];
	    		
		return $arIDs;
	}
	
	
	/**
	 * getDeliveries() - get all active deliveries
	 */
	public static function getDeliveries(){
		$db_dtype = CSaleDelivery::GetList( array( "SORT" => "ASC", "NAME" => "ASC" ), array( "LID" => SITE_ID, "ACTIVE" => "Y"), false, false, array() );
		$arrDeliveries = array();
		while ( $ar_dtype = $db_dtype->Fetch()  ) {
			$arrDeliveries[] = $ar_dtype;
		}
		
		return $arrDeliveries;
	}
	
	/**
	 * getDeliveryById() - get active delivery by id
	 * $id - id delivery
	 * $lid - id site
	 */
	public static function getDeliveryById ( $id, $lid ){		
		return CSaleDelivery::GetList( array( "SORT" => "ASC", "NAME" => "ASC" ), array( "LID" => $lid, 'ID'=>$id, "ACTIVE" => "Y"), false, false, array() )->Fetch();
	} 
	
	
	/**
	 * getAllOrdersByUserId() - get all  orders by user id
	 * $userId - user id
	 */
	public static function getAllOrdersByUserId ( $userId ){
		$resDB = CSaleOrder::GetList( Array("ID"=>"DESC"), Array("USER_ID" => $userId ), false, false, array() );
		$arrOrders = array();
		while ( $res = $resDB->fetch() ) {
			$arrOrders[] = $res;				
		}
		
		return $arrOrders;
	}
	
	
	/**
	 * getAddrCountrShipment() - get address and country shipment from USER_DESCRIPTION
	 * $allOrders - array data of last order
	 */
	public static function getAddrCountrShipment( $allOrders ){
		$arrAddressCountry = array();
		$strArr = explode(';', $allOrders[0]['USER_DESCRIPTION']);
		foreach ($strArr as $key => $value) {
			if( stripos($value, 'Country') ){						
				$arCountry = explode('-', $value);
				$arrAddressCountry['country'] = $arCountry[1];						
			}elseif(stripos($value, 'SAddress')){
				$arShipment = explode('-', $value);
				$arrAddressCountry['shipmentAddress'] = $arShipment[1];	
			}		
		}	
		
		return $arrAddressCountry;
	}
	
	
	/**
	 * getPaymentSystem() - get all active payment systems
	 */
	public static function getPaymentSystem(){
		$db_ptype = CSalePaySystem::GetList( Array("SORT"=>"ASC"), Array("ACTIVE"=>"Y"), false, false, array() );
		$arr = [];
		while ( $ptype = $db_ptype->Fetch() ) {
			$arr[] = $ptype;	
		}
		
		return $arr;
	}
	
	
	
	/**
	 * getAllCountries() - get all cities from DB
	 * $langId - id site version  (ru, en ...)
	 */
	public static function getAllCountries( $langId = LANGUAGE_ID ){
		$resLocDB = CSaleLocation::GetCountryList( Array("NAME_LANG"=>"ASC"), Array(), $langId );
		$allCountries = array();		
		while ( $resLoc = $resLocDB->fetch() ) {
			$allCountries[] = $resLoc;					
		}
		
		return $allCountries;
	}
	
	
	/**
	 * checkGET() - check REQUEST params 
	 */
	public static function checkGET( $arg ){
		if ( isset($_GET[$arg]) && !empty($_GET[$arg]) )
			return true;
		else
			return false;	
	}
	
	
	/**
	 * getUserData() - get data about user
	 */
	public static function getUserData( $userId ){
		global $USER;
		$rsUser = CUser::GetByID($userId); //$USER->GetID()
		$arUser = $rsUser->Fetch();
			
		$userData = array(
			'firstName' => CUser::GetFirstName(),
			'lastName' => CUser::GetLastName(),
			'email' => CUser::GetEmail(),
			'personalPhone' => 	$arUser['PERSONAL_PHONE']	
			);
		
		return $userData;
	}
	
	
	/**
	 * checkUrl() - fundtion of checkout of url string
	 * 
	 * $detString - symbol of seperation
	 * $includeSymbol - symbol that include in url array
	 * $countArUrl - count of element of url array
	 */
	public static function checkUrl ( $detString, $includeSymbol, $countArUrl ){
		$cUrl  = sest::url();
		$arUrl = explode($detString, $cUrl);
		$includeVal = in_array($includeSymbol, $arUrl);
		
		if( $includeVal && count($arUrl) > $countArUrl )
			return true;
		else
			return false;					
	}
	
	
	/**
	 * getSectionDataById() - get section data by section id 
	 * $iblockId - IBLOCK_ID
	 * $sectionId - section id
	 */
	public static function getSectionDataById( $iblockId, $sectionId ){
		if(CModule::IncludeModule("iblock")){ 
	    	return CIBlockSection::GetList( Array("SORT"=>"ASC"), Array('IBLOCK_ID'=>$iblockId, 'ID'=>$sectionId), false, Array(), false )->fetch(); 
	   } 
	}


	/**
	 * getDeliveryStatusByCode() - get delivery status by delivery code
	 * $deliveryCode - delivery code ,  array('N', 'Y')
	 */
	public static function getDeliveryStatusByCode( $deliveryCode ){
		if( $deliveryCode == 'N' )
			return 'Доставка не разрешена';
		elseif( $deliveryCode == 'Y' )
			return 'Доставка разрешена';
		else 
			return false; 
	}
	
	 /**
	* recursive delete folder
	* @param $dir
	* @return bool
	*/
	public static function delFolder($dir){
		$files = array_diff(scandir($dir), array('.','..'));
		foreach ($files as $file) {
		(is_dir("$dir/$file")) ? delFolder("$dir/$file") : unlink("$dir/$file");
		}
		return rmdir($dir);
	}
	
	
	
}//end class sest    
?>
