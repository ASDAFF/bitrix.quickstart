<?php 
/**
	 * get all discount by product id
	 */
	function getAllDiscountByID( $PRODUCT_ID ){
		global $DB;
		global $APPLICATION;
		
		$dbProductDiscounts = CCatalogDiscount::GetList(
	    array("SORT" => "ASC"),
	    array(
	            "+PRODUCT_ID" => $PRODUCT_ID,
	            "ACTIVE" => "Y",
	            "!>ACTIVE_FROM" => $DB->FormatDate(date("Y-m-d H:i:s"), 
	                                               "YYYY-MM-DD HH:MI:SS",
	                                               CSite::GetDateFormat("FULL")),
	            "!<ACTIVE_TO" => $DB->FormatDate(date("Y-m-d H:i:s"), 
	                                             "YYYY-MM-DD HH:MI:SS", 
	                                             CSite::GetDateFormat("FULL")),
	            "COUPON" => ""
	        ),
	    false,
	    false,
	    array(
	            "ID", "SITE_ID", "ACTIVE", "ACTIVE_FROM", "ACTIVE_TO", 
	            "RENEWAL", "NAME", "SORT", "MAX_DISCOUNT", "VALUE_TYPE", 
	    "VALUE", "CURRENCY", "PRODUCT_ID"
	        )
	    );
		$arrAllDiscounts = array();
		while ($arProductDiscounts = $dbProductDiscounts->Fetch()){
			$arrAllDiscounts[] = $arProductDiscounts;				  
		}	
		
		return $arrAllDiscounts;			
	}
?>
