<?
/************************************
*
* mysql mail class
* last update 21.01.2015
*
************************************/

IncludeModuleLangFile(__FILE__);

class CRSQUICKBUYElements
{
	protected static $tableName = 'b_redsign_quckbuy';
	
	function GetList($aSort=array(), $aFilter=array())
	{
		$bitrix_default_quantity_trace = COption::GetOptionString('catalog', 'default_quantity_trace', 'N');
		global $DB;
		$arFilter = array();
		foreach($aFilter as $key=>$val)
		{
			if(!is_array($val) && strlen($val)<=0)
				continue;
			switch(strtoupper($key))
			{
				case 'ID':
					if(is_array($val))
					{
						$arFilter[] = "(QB.ID='".implode("' or QB.ID='",$val)."')";
					} else {
						$arFilter[] = "QB.ID='".$DB->ForSql($val)."'";
					}
					break;
				case 'ELEMENT_ID':
					if(is_array($val))
					{
						$arFilter[] = "(QB.ELEMENT_ID='".implode("' or QB.ELEMENT_ID='",$val)."')";
					} else {
						$arFilter[] = "QB.ELEMENT_ID='".$DB->ForSql($val)."'";
					}
					break;
				case 'FOR_OFFERS':
					$arFilter[] = "QB.FOR_OFFERS='".($val='Y'?'Y':'N')."'";
					break;
				case 'ACTIVE':
					$arFilter[] = "QB.ACTIVE='".($val=='Y'?'Y':'N')."'";
					break;
				case 'DATE_FROM':
					$arFilter[] = "QB.DATE_FROM < ".$DB->CharToDateFunction( $DB->ForSql($val) )."";
					break;
				case 'DATE_TO':
					$arFilter[] = "QB.DATE_TO > ".$DB->CharToDateFunction( $DB->ForSql($val) )."";
					break;
				case 'DISCOUNT':
					$arFilter[] = "QB.DISCOUNT LIKE='".$DB->ForSql($val)."'";
					break;
				case 'VALUE_TYPE':
					$arFilter[] = "QB.VALUE_TYPE='".$DB->ForSql($val)."'";
					break;
				case 'CURRENCY':
					$arFilter[] = "QB.CURRENCY='".$DB->ForSql($val)."'";
					break;
				case 'QUANTITY':
					if($bitrix_default_quantity_trace=='Y')
					{
						$arFilter[] = "QB.QUANTITY>'".$DB->ForSql($val)."'";
					}
					break;
				case 'AUTO_RENEWAL':
					$arFilter[] = "QB.AUTO_RENEWAL='".($val=='Y'?'Y':'N')."'";
					break;
			}
		}

		$arOrder = array();
		foreach($aSort as $key=>$val)
		{
			$ord = (strtoupper($val) <> 'ASC'?'DESC':'ASC');
			switch(strtoupper($key))
			{
				case 'ID':
					$arOrder[] = "QB.ID ".$ord;
					break;
				case 'ELEMENT_ID':
					$arOrder[] = "QB.ELEMENT_ID ".$ord;
					break;
				case 'DATE_FROM':
					$arOrder[] = "QB.DATE_FROM ".$ord;
					break;
				case 'DATE_TO':
					$arOrder[] = "QB.DATE_TO ".$ord;
					break;
				case 'DISCOUNT':
					$arOrder[] = "QB.DISCOUNT ".$ord;
					break;
				case 'QUANTITY':
					$arOrder[] = "QB.QUANTITY ".$ord;
					break;
			}
		}
		if(count($arOrder) == 0)
			$arOrder[] = "QB.ID DESC";
		$sOrder = "\nORDER BY ".implode(", ",$arOrder);

		if(count($arFilter) == 0)
			$sFilter = "";
		else
			$sFilter = "\nWHERE ".implode("\nAND ", $arFilter);

		$strSql = "
			SELECT
				QB.*,
				".$DB->DateToCharFunction("QB.DATE_FROM")." DATE_FROM,
				".$DB->DateToCharFunction("QB.DATE_TO")." DATE_TO
			FROM
				".self::$tableName." QB
			".$sFilter.$sOrder;
		
		return $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
	}
	
	function GetByID($ID)
	{
		global $DB;
		
		return self::GetList(array("ID"=>"SORT"),array("ID"=>$ID));
	}
	
	function GetByElementID($ID)
	{
		global $DB;
		
		return self::GetList(array("ID"=>"SORT"),array("ELEMENT_ID"=>$ID));
	}
	
	function Delete($ID)
	{
		global $DB;
		
		CModule::IncludeModule('catalog');
		
		$ID = intval($ID);
		
		$resource = self::GetByID($ID);
		if($data = $resource->Fetch())
		{
			/////////////////////////////// OFFERS and SIMPLE
			$arrDiscountArrayID = unserialize( $data["DISCOUNT_ID_ARRAY"] );
			foreach($arrDiscountArrayID as $DISCOUNT_ID)
			{
				CCatalogDiscount::Delete($DISCOUNT_ID);
			}
			$DB->StartTransaction();
			
			$res = $DB->Query("DELETE FROM ".self::$tableName." WHERE ID=".$ID, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			
			if($res)
				$DB->Commit();
			else
				$DB->Rollback();
		}
		return $res;
	}
	
	function Add($arFields)
	{
		global $DB;
		
		CModule::IncludeModule('iblock');
		CModule::IncludeModule('catalog');
		
		if(isset($arFields["ID"]))
			unset($arFields["ID"]);
		
		$arFields["ACTIVE"] = $arFields["ACTIVE"]=="Y"?"Y":"N";
		
		$res = CIBlockElement::GetByID($arFields["ELEMENT_ID"]);
		if($ar_res = $res->GetNext())
		{
			$IBlockID = $ar_res["IBLOCK_ID"];
			$IBLOCK_ID_LIDS = $IBlockID;
			$arCatalog = CCatalog::GetByIDExt($IBlockID);
			if(IntVal($arCatalog["OFFERS_IBLOCK_ID"])>0 && IntVal($arCatalog["OFFERS_PROPERTY_ID"])>0)
			{
				$IBLOCK_ID_LIDS = IntVal($arCatalog["OFFERS_IBLOCK_ID"]);
				$arrOffersID = array();
				$arFilter = Array(
					"IBLOCK_ID" => $IBLOCK_ID_LIDS,
					"PROPERTY_".$arCatalog["OFFERS_PROPERTY_ID"]."_VALUE" => $arFields["ELEMENT_ID"], 
				);
				$res = CIBlockElement::GetList(Array("ID"=>"ASC"), $arFilter, Array("ID"));
				while($arElemFields = $res->GetNext())
				{
					$arrOffersID[] = $arElemFields["ID"];
				}
			}
			$arrLIDs = array();
			$rsSites = CIBlock::GetSite($IBLOCK_ID_LIDS);
			while($arSite = $rsSites->Fetch())
			{
				$arrLIDs[] = $arSite['LID'];
			}
			$arrDiscountArrayID = array();
			if(is_array($arrOffersID) && count($arrOffersID)>0)
			{
				/////////////////////////////// OFFERS
				$arFields['FOR_OFFERS'] = 'Y';
				foreach($arrLIDs as $LID)
				{
					foreach($arrOffersID as $PRODUCT_ID)
					{
						$arFieldsDiscount = array(
							'SITE_ID' => $LID,
							'ACTIVE' => 'Y',
							'NAME' => GetMessage('RSQB.DISCOUNT_NAME').' | Product ID = '.$arFields['ELEMENT_ID'].' [SKU ID = '.$PRODUCT_ID.']',
							'COUPON' => '',
							'SORT' => 100,
							'MAX_DISCOUNT' => 0,
							'VALUE_TYPE' => $arFields['VALUE_TYPE'],
							'VALUE' => round($arFields['DISCOUNT']),
							'CURRENCY' => $arFields['CURRENCY'],
							'RENEWAL' => 'N',
							'ACTIVE_FROM' => $arFields['DATE_FROM'],
							'ACTIVE_TO' => $arFields['DATE_TO'],
							'PRODUCT_IDS' => array($PRODUCT_ID),
							'SECTION_IDS' => array(),
							'GROUP_IDS' => array(),
							'CATALOG_GROUP_IDS' => array(),
							'CATALOG_COUPONS' => array(),
						);
						$DISCOUNT_ID = CCatalogDiscount::Add($arFieldsDiscount);
						if($DISCOUNT_ID>0)
						{
							$arrDiscountArrayID[] = $DISCOUNT_ID;
						}
					}
				}
			} elseif($arCatalog['CATALOG']=='Y') {
				/////////////////////////////// NO OFFERS
				$arFields['FOR_OFFERS'] = 'N';
				foreach($arrLIDs as $LID)
				{
					$arFieldsDiscount = array(
						'SITE_ID' => $LID,
						'ACTIVE' => 'Y',
						'NAME' => GetMessage('RSQB.DISCOUNT_NAME').' | Product ID = '.$arFields['ELEMENT_ID'],
						'COUPON' => '',
						'SORT' => 100,
						'MAX_DISCOUNT' => 0,
						'VALUE_TYPE' => $arFields['VALUE_TYPE'],
						'VALUE' => round($arFields['DISCOUNT']),
						'CURRENCY' => $arFields['CURRENCY'],
						'RENEWAL' => 'N',
						'ACTIVE_FROM' => $arFields['DATE_FROM'],
						'ACTIVE_TO' => $arFields['DATE_TO'],
						'PRODUCT_IDS' => array($arFields['ELEMENT_ID']),
						'SECTION_IDS' => array(),
						'GROUP_IDS' => array(),
						'CATALOG_GROUP_IDS' => array(),
						'CATALOG_COUPONS' => array(),
					);
					$DISCOUNT_ID = CCatalogDiscount::Add($arFieldsDiscount);
					if($DISCOUNT_ID>0)
					{
						$arrDiscountArrayID[] = $DISCOUNT_ID;
					}
				}
			}
			if(is_array($arrDiscountArrayID) && count($arrDiscountArrayID)>0)
			{
				$arFields['DISCOUNT_ID_ARRAY'] = serialize($arrDiscountArrayID);
				$ID = $DB->Add(self::$tableName, $arFields);
			}
		}
		return $ID;
	}
	
	function Update($ID, $arFields)
	{
		global $DB;
		
		CModule::IncludeModule('iblock');
		CModule::IncludeModule('catalog');
		
		$ID = intval($ID);
		
		if(isset($arFields["ID"]))
			unset($arFields["ID"]);
		
		$res = CIBlockElement::GetByID($arFields["ELEMENT_ID"]);
		if($arElement = $res->GetNext())
		{
			$resource = self::GetByID($ID);
			if($data = $resource->Fetch())
			{
				$arFields['FOR_OFFERS'] = $data['FOR_OFFERS'];
				if(empty($arFields['DISCOUNT']))
					$arFields['DISCOUNT'] = $data['DISCOUNT'];
				if(empty($arFields['VALUE_TYPE']))
					$arFields['VALUE_TYPE'] = $data['VALUE_TYPE'];
				if(empty($arFields['CURRENCY']))
					$arFields['CURRENCY'] = $data['CURRENCY'];
				
				$strUpdate = $DB->PrepareUpdate(self::$tableName, $arFields);
				if($strUpdate!="")
				{
					/////////////////////////////// OFFERS and SIMPLE
					$DB->Query("UPDATE ".self::$tableName." SET ".$strUpdate." WHERE ID=".$ID, false, "File: ".__FILE__."<br>Line: ".__LINE__);
					$arrDiscountArrayID = unserialize( $data["DISCOUNT_ID_ARRAY"] );
					foreach($arrDiscountArrayID as $DISCOUNT_ID)
					{
						if( $arDiscData = CCatalogDiscount::GetByID($DISCOUNT_ID) ) {
							$arFields['SITE_ID'] = $arDiscData['SITE_ID'];
							self::UpdateDiscount($DISCOUNT_ID, $arFields);
						}
					}
				}
			}
		}
		return true;
	}
	
	function UpdateDiscount($DISCOUNT_ID, $arFields)
	{
		$arFieldsDiscount = array(
			'SITE_ID' => $arFields['SITE_ID'],
			'VALUE' => round($arFields['DISCOUNT']),
			'VALUE_TYPE' => $arFields['VALUE_TYPE'],
			'CURRENCY' => $arFields['CURRENCY'],
			'ACTIVE_FROM' => $arFields['DATE_FROM'],
			'ACTIVE_TO' => $arFields['DATE_TO'],
		);

		$res = CCatalogDiscount::Update($DISCOUNT_ID, $arFieldsDiscount);
		return TRUE;
	}
	
	function CheckAutoRenewal()
	{
		if(!defined("ADMIN_SECTION") || ADMIN_SECTION !== true)
		{
			global $DB;
			
			$time = ConvertTimeStamp(time(),"FULL");
			$sFilter = "\nWHERE QB.AUTO_RENEWAL='Y' AND QB.DATE_TO < ".$DB->CharToDateFunction($time)."";
			$sOrder = "\nORDER BY QB.ID DESC";
			$strSql = "
				SELECT
					QB.*,
					".$DB->DateToCharFunction("QB.DATE_FROM")." DATE_FROM,
					".$DB->DateToCharFunction("QB.DATE_TO")." DATE_TO
				FROM
					".self::$tableName." QB
				".$sFilter.$sOrder;
			
			$res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			while($arData = $res->Fetch())
			{
				$TS_DATE_FROM = MakeTimeStamp($arData["DATE_FROM"],"DD.MM.YYYY HH:MI:SS");
				$TS_DATE_TO = MakeTimeStamp($arData["DATE_TO"],"DD.MM.YYYY HH:MI:SS");
				$NEW_DATE_TO_ = $TS_DATE_TO + ( $TS_DATE_TO - $TS_DATE_FROM );
				$NEW_DATE_TO = ConvertTimeStamp($NEW_DATE_TO_, "FULL", "ru");
				$arFields = array(
					"ELEMENT_ID" => $arData["ELEMENT_ID"],
					"DATE_FROM" => $arData["DATE_TO"],
					"DATE_TO" => $NEW_DATE_TO,
				);
				self::Update($arData["ID"],$arFields);
			}
		}
	}
}