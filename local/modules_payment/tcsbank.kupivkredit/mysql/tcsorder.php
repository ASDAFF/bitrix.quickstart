<?
	class CTCSOrderAll
	{
	
		function GetOrdersIDByBankStatus($iBankStatus)
		{
			$arOrdersIDs=Array();
			if($iBankStatus=IntVal($iBankStatus))
			{
				global $DB;
				$sModuleID = $this->sModuleID;
				require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/{$sModuleID}/constants.php");
				$arStatuses = $arTCSBankStatuses[$iBankStatus]["STATUS"];
				if(!empty($arStatuses))
				{
					$sQuery = "SELECT ORDER_ID FROM b_tcs_order WHERE STATUS IN ('".implode("','",$arStatuses)."')";
					$obOrders = $DB->Query($sQuery);
					while($arOrder = $obOrders->Fetch())
					{
						$arOrdersIDs[] = $arOrder["ORDER_ID"];
					}
					
				}
			}
			return $arOrdersIDs;
		}	
		function GetOrdersIDByOrderStatus($iBankStatus)
		{
			$arOrdersIDs=Array();
			if($iBankStatus=IntVal($iBankStatus))
			{
				global $DB;
				$sModuleID = $this->sModuleID;
				require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/{$sModuleID}/constants.php");
				$arStatuses = $arTCSOrderStatuses[$iBankStatus]["STATUS"];
				if(!empty($arStatuses))
				{
					$sQuery = "SELECT ORDER_ID FROM b_tcs_order WHERE STATUS IN ('".implode("','",$arStatuses)."')";
					$obOrders = $DB->Query($sQuery);
					while($arOrder = $obOrders->Fetch())
					{
						$arOrdersIDs[] = $arOrder["ORDER_ID"];
					}
					
				}
			}
			return $arOrdersIDs;
		}		
	
		function TouchOrder($iOrderID,$arSelect = Array("ID"))
		{
			global $DB;
			IncludeModuleLangFile(__FILE__);
			if($iOrderID = IntVal($iOrderID))
			{
				$obExists = $this->GetList(Array(),Array("ORDER_ID"=>$iOrderID),$arSelect);
				if($arExists = $obExists->Fetch()) 
				{
					if(count($arExists)>1) 
					{
						if($iTS = MakeTimeStamp($arExists["SIGNING_DEADLINE"],"YYYY-MM-DD"))
						{
							$arExists["SIGNING_DAYS"]=ceil(($iTS-time())/86400);
						}
						if($iTS = MakeTimeStamp($arExists["DELIVERY_DEADLINE"],"YYYY-MM-DD"))
						{
							$arExists["DELIVERY_DAYS"]=ceil(($iTS-time())/86400);
						}
						return $arExists;
					}
					else return current($arExists);
				}
				else
				{
					return $this->Add(Array("ORDER_ID"=>$iOrderID),true);
				}
				
			}
			else 
			{
				$this->LAST_ERROR = GetMessage("WRONG_ID");
				return false;
			}
		}
	
		function Add($arFields, $bReturnAll = false)
		{
			IncludeModuleLangFile(__FILE__);
			if(!$arFields["STATUS"]) $arFields["STATUS"]=$this->GetDefaultStatus();
			if(!IntVal($arFields["ORDER_ID"]))
			{
				$this->LAST_ERROR = GetMessage("WRONG_ID");
				return false;
			}
			global $DB;
			foreach($arFields as $sField=>&$sValue)
			{
				switch($sField)
				{
					case "ORDER_ID":				
					case "MONTHLY_PAYMENT":
					case "PAYMENT_COUNT":
					case "DOWN_PAYMENT":
					case "MAX_LOAN_AMOUNT":
						$sValue = "'".FloatVal(trim($sValue))."'";
					break;
					case "SIGNING_TYPE":
					case "PRINTED":
					case "CANCELED":
					case "CANCEL_STATUS":
					case "APPROVED":
					case "SUBSCRIBED":
					case "STATUS":
					case "IS_CONFIRMED":
					case "COMMENT":
						$sValue = "'".trim($sValue)."'";
					break;
					case "SIGNING_DEADLINE":
						if (CheckDateTime($sValue))
						{
							$sValue = $DB->CharToDateFunction($sValue, "SIGNING_DEADLINE");
						}
						else unset($arFields[$sField]);
					break;
					case "DELIVERY_DEADLINE":
						if (CheckDateTime($sValue))
						{
							$sValue = $DB->CharToDateFunction($sValue, "DELIVERY_DEADLINE");
						}
						else unset($arFields[$sField]);
					break;						
					default:
						unset($arFields[$sField]);
					break;
				}
			}
			if($iID = $DB->Insert("b_tcs_order", $arFields))
			{
				if(!$bReturnAll) return $iID;
				$obElement = $this->GetList(Array(),Array("ID"=>$iID),Array());
				return ($arElement = $obElement->Fetch());
			}
			else
			{
				$this->LAST_ERROR = GetMessage("ADD_ERROR",Array("ORDER_ID"=>$arFields["ORDER_ID"]));
				return false;
			}
		}
		function GetList($arSort = Array(), $arFilter = Array(), $arSelect = Array())
		{
			IncludeModuleLangFile(__FILE__);
			global $DB;
			$arFields = Array(
				"ID",
				"ORDER_ID",
				"STATUS"			
			);
			
			$filter_keys = array_keys($arFilter);
			for($i=0; $i<count($filter_keys); $i++)
			{
				$val = $arFilter[$filter_keys[$i]];
				$key = $filter_keys[$i];
				$res = CAllIBlock::MkOperationFilter($key);
				$key = $res["FIELD"];
				$cOperationType = $res["OPERATION"];
				$key = strtoupper($key);
				switch($key)
				{
					case "STATUS":
						$arSqlSearch[] = CAllIBlock::FilterCreate($key, $val, "string", $cOperationType);
						break;
					case "ORDER_ID":
					case "ID":
						$arSqlSearch[] = CAllIBlock::FilterCreate($key, $val, "number", $cOperationType);
						break;
				}
			}			
			$strSqlSearch = implode(" AND ",array_diff($arSqlSearch,Array("")));
			if(!empty($arSort))
			{
				$sSort =" ORDER BY ";
				$arSTemp=Array();
				foreach($arSort as $sKey=>$sSortDirection)
				{
					$arSTemp[] = "{$sKey} {$sSortDirection}";
				}
				$sSort.=implode(", ",$arSTemp);
				$strSqlSearch.=$sSort;
			}
			
			
			$arSelectResult = array_intersect($arFields, $arSelect);
			$sSelect = empty($arSelect)?"*":(empty($arSelectResult)?"*":implode(",",$arSelectResult));
			$sQuery = "SELECT {$sSelect} FROM `b_tcs_order`";
			if(strlen($strSqlSearch))
			{
				$sQuery.=" WHERE ".$strSqlSearch;
			}
			return $DB->Query($sQuery);
		}
		
		
		function Update($ID, $arFields)
		{
			IncludeModuleLangFile(__FILE__);
			global $DB;
			$arFields["SITE_ID"] = $iSiteID;
			if(IntVal($ID))
			{
				if(!isset($arFields["STATUS"]) || !strlen(trim($arFields["STATUS"])))
				{
					$arFields["STATUS"] = $this->GetDefaultStatus();
				}
				foreach($arFields as $sField=>&$sValue)
				{
					switch($sField)
					{
						case "MONTHLY_PAYMENT":
						case "PAYMENT_COUNT":
						case "DOWN_PAYMENT":
						case "MAX_LOAN_AMOUNT":
							$sValue = "'".FloatVal(trim($sValue))."'";
						break;
						case "SIGNING_TYPE":
						case "PRINTED":
						case "CANCELED":
						case "CANCEL_STATUS":
						case "APPROVED":
						case "SUBSCRIBED":
						case "IS_CONFIRMED":
						case "STATUS":
						case "COMMENT":
							$sValue = "'".trim($sValue)."'";
						break;
						case "SIGNING_DEADLINE":
							if (CheckDateTime($sValue))
							{
								$sValue = $DB->CharToDateFunction($sValue, "SIGNING_DEADLINE");
							}
							else unset($arFields[$sField]);
						break;
						case "DELIVERY_DEADLINE":
							if (CheckDateTime($sValue))
							{
								$sValue = $DB->CharToDateFunction($sValue, "DELIVERY_DEADLINE");
							}
							else unset($arFields[$sField]);
						break;							
						default:
							unset($arFields[$sField]);
						break;
					}
				}
				if($ID = $DB->Update("b_tcs_order", $arFields, "WHERE ORDER_ID='".$ID."'",  $err_mess.__LINE__, false)) return $ID;
			}
			else
			{
				$this->LAST_ERROR = GetMessage("WRONG_ID");
				return false;
			}
		}			
		
		
	}
	
	

?>