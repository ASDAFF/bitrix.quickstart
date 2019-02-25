<?
	class CTCSBank extends CTCSExchange
	{
		var $sModuleID;
		var $sHost;
		var $sButtonURL;
		var $iKoef;
		
		function CTCSBank()
		{
			$this->sModuleID = "tcsbank.kupivkredit";
			//$this->sHost = "https://kupivkredit-test-api.tcsbank.ru:8100";
			$this->iKoef = 0.351/12;
			RegisterModuleDependences("sale", "OnSaleCancelOrder", "tcsbank.kupivkredit", "CTCSBank", "OnCancelOrder");
			RegisterModuleDependences("sale", "OnBeforeOrderDelete", "tcsbank.kupivkredit", "CTCSBank", "OnDeleteOrder");			
		}
		
		function OnDeleteOrder($iOrderID)
		{
			$obModule = new CTCSBank;
			if($obModule->CheckOrderPaySystem($iOrderID)) $obModule->CancelOrder($iOrderID, false, "shop_decline");
		}
		
		function OnCancelOrder($iOrderID, $sType)
		{
			if($sType=="Y")
			{
				$obModule = new CTCSBank;
				if($obModule->CheckOrderPaySystem($iOrderID)) $obModule->CancelOrder($iOrderID, false, "shop_decline");
			}
		}		

		function GetApiHostBySiteID($sSiteID)
		{
			return COption::GetOptionString($this->sModuleID, "{$sSiteID}_host_api",$this->GetHost("test","API"));
		}
		
		function GetRoundBySiteID($sSiteID)
		{
			return COption::GetOptionString($this->sModuleID, "{$sSiteID}_round","round");
		}
		
		function Round($iSumm, $sSiteID)
		{
			$sFunctionName = $this->GetRoundBySiteID($sSiteID);
			switch($sFunctionName)
			{
				case "ceil":
					return ceil(FloatVal($iSumm));
				break;
				case "floor":
					return floor(FloatVal($iSumm));
				break;
				default:
					return round(FloatVal($iSumm),0);
				break;				
			}
		}
		
		function GetHostBySiteID($sSiteID)
		{
			return COption::GetOptionString($this->sModuleID, "{$sSiteID}_host",$this->GetHost("test","SRC"));
		}
		
		function CheckSiteRights($arParams)
		{
			if(isset($arParams["SITE_ID"]))
			{
				$sSiteID = $arParams["SITE_ID"];
			}
			else
			{
				$obOrder = CSaleOrder::GetList(Array(),Array("ID"=>IntVal($arParams["ORDER_ID"])),false,false,Array("LID"));
				if(!($arOrder = $obOrder->Fetch())) 
				{
					return false;
				}
				$sSiteID = $arOrder["LID"];			
			}
			
			$sActive = COption::GetOptionString($this->sModuleID, "{$sSiteID}_active","n");
			if($sActive=="y") return true;
			return false;
		}
		
		function GetHost($sHostCode, $sKey=false)
		{
			include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->sModuleID."/constants.php");
			if(isset($arHosts[$sHostCode]))
			{
				if($sKey) return $arHosts[$sHostCode][$sKey];
				else return $arHosts[$sHostCode];
			}
			return false;
		}
		
		function CalculateMonthPayment($iMonths)
		{
			$iKoef = $this->iKoef;
			$iMonths = IntVal($iMonths);
			return $iKoef*pow((1+$iKoef),$iMonths)/(pow(1+$iKoef,$iMonths)-1);		
		}
		
		function MonthPayment($iMonths, $iCreditSumm)
		{
			return ceil(CTCSBank::CalculateMonthPayment($iMonths)*$iCreditSumm/10)*10;
		}
		
		function ShowScripts()
		{
			global $APPLICATION;
			$sModuleID = $this->sModuleID;
			$APPLICATION->AddHeadScript("/bitrix/js/{$sModuleID}/jquery-1.7.1.min.js");
			$APPLICATION->AddHeadScript("/bitrix/js/{$sModuleID}/jquery-ui-1.8.18.custom.min.js");
			$APPLICATION->AddHeadScript("/bitrix/js/{$sModuleID}/scripts.js");
		}
		
		function PHPArrayToJS($arDest)
		{
			if (is_array($arDest))
			{
				foreach ($arDest as $k=>$v)
				{
					$arDest[$k] = '"'.$k.'":'.$this->PHPArrayToJS($v);
				}
				$arDest = '{'.implode(',', $arDest).'}';
			} 
			else 
			{
				$jsrep = array(
					array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'),
					array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"')
				);
		
				$arDest = '"'.str_replace($jsrep[0], $jsrep[1], $arDest).'"';
			}
			return $arDest;
		}	
		
		function GetGroupRight()
		{
			global $APPLICATION, $USER;
			return $APPLICATION->GetGroupRight($this->sModuleID);
		}
		
		
		function PluralForm($n, $forms) {
			return $n%10==1&&$n%100!=11?$forms[0]:($n%10>=2&&$n%10<=4&&($n%100<10||$n%100>=20)?$forms[1]:$forms[2]);
		}
		
		function CheckOrderPaySystem($iOrderID)
		{
			$obOrder = CSaleOrder::GetList(Array(),Array("ORDER_ID"=>$iOrderID,"PAY_SYSTEM_ID"=>$this->GetPaySystemsID()),false,false,Array("ID"));
			if($obOrder->SelectedRowsCount()) return true;
			return false;
		}
		
		function GetOrderTable($arFilter)
		{
			global $DB;
			IncludeModuleLangFile(__FILE__);
			foreach($_REQUEST as $sKey=>$sValue)
			{
				$$sKey = $sValue;
			}
		
			$sTableID = "tbl_tcs_orders";
			$oSort = new CAdminSorting($sTableID, "ID", "asc");
			$lAdmin = new CAdminList($sTableID, $oSort);
			$arFilterFields = array(
				"filter_id_from",
				"filter_id_to",
				"filter_buyer",
				"filter_date_from",
				"filter_date_to",
				"filter_date_update_from",
				"filter_date_update_to",
				"filter_decision"
			);		
			$lAdmin->InitFilter($arFilterFields);	
			$arFilter["PAY_SYSTEM_ID"] = $this->GetPaySystemsID();

			if (IntVal($filter_id_from)>0) $arFilter[">=ID"] = IntVal($filter_id_from);
			if (IntVal($filter_id_to)>0) $arFilter["<=ID"] = IntVal($filter_id_to);
			if (strlen($filter_date_from)>0) $arFilter["DATE_FROM"] = Trim($filter_date_from);
			if (strlen($filter_date_to)>0)
			{
				if ($arDate = ParseDateTime($filter_date_to, CSite::GetDateFormat("FULL", SITE_ID)))
				{
					if (StrLen($filter_date_to) < 11)
					{
						$arDate["HH"] = 23;
						$arDate["MI"] = 59;
						$arDate["SS"] = 59;
					}
					$iTimeTo = mktime($arDate["HH"], $arDate["MI"], $arDate["SS"], $arDate["MM"], $arDate["DD"], $arDate["YYYY"]);
					$filter_date_to = date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL", SITE_ID)), mktime($arDate["HH"], $arDate["MI"], $arDate["SS"], $arDate["MM"], $arDate["DD"], $arDate["YYYY"]));
					$arFilter["DATE_TO"] = $filter_date_to;
				}
				else
				{
					$filter_date_to = "";
				}
			}
			if (strlen($filter_buyer)>0) $arFilter["%BUYER"] = $this->Decode(Trim($filter_buyer));
			if($filter_date_from_DAYS_TO_BACK)
			{
				$iTime = (!$iTimeTo)?time():$iTimeTo;
				$arFilter["DATE_FROM"] = GetTime($iTime-86400*$filter_date_from_DAYS_TO_BACK);
			}	
			if($iBankStatus = IntVal($filter_bank_status))
			{
				$arOrdersIDs = CTCSOrder::GetOrdersIDByBankStatus($iBankStatus);

				if(empty($arOrdersIDs))
				{
					$arCurrentOrderIDs=Array();
				}
				else
				{
					if(IntVal($arCurrentOrderIDs)) $arCurrentOrderIDs = Array($arFilter["ID"]);
					if(is_array($arFilter["ID"]) && !empty($arFilter["ID"]))
					{
						$arCurrentOrderIDs = array_intersect($arFilter["ID"],$arOrdersIDs);
					}	
					else $arCurrentOrderIDs = $arOrdersIDs;
				}
				if(!empty($arCurrentOrderIDs)) $arFilter["ID"] = array_values($arCurrentOrderIDs);
				else $arFilter["ID"] = 0;
			}
			if($iOrderStatus = IntVal($filter_order_status))
			{
				$arOrdersIDs = CTCSOrder::GetOrdersIDByOrderStatus($iOrderStatus);
				if(empty($arOrdersIDs))
				{
					$arCurrentOrderIDs=Array();
				}
				else
				{
					if(IntVal($arCurrentOrderIDs)) $arCurrentOrderIDs = Array($arFilter["ID"]);
					if(is_array($arFilter["ID"]) && !empty($arFilter["ID"]))
					{
						
						$arCurrentOrderIDs = array_intersect($arFilter["ID"],$arOrdersIDs);
					}	
					else $arCurrentOrderIDs = $arOrdersIDs;
				}
				if(!empty($arCurrentOrderIDs)) $arFilter["ID"] = array_values($arCurrentOrderIDs);
				else $arFilter["ID"] = 0;
			}
			$arFilter["!CANCELED"]="Y";
			if(!$by)
			{
				$arSort = array("id" => "desc");
			}
			else $arSort = array($by => $order);
			$dbOrderList = CSaleOrder::GetList(
				$arSort,
				$arFilter,
				false,
				array("nPageSize"=>CAdminResult::GetNavSize($sTableID)),
				Array("ID","PRICE","SUM_PAID","DATE_INSERT","LID","PERSON_TYPE_ID","CURRENCY")
			);

			$obTCSOrder = new CTCSOrder();
			$dbOrderList = new CAdminResult($dbOrderList, $sTableID);
			$dbOrderList->NavStart();

			$lAdmin->NavText($dbOrderList->GetNavPrint(GetMessage("TCS_ORDERS")));
			
			$lAdmin->AddHeaders(array(
				Array(  
					"id" => "ID",
					"content" => "ID",
					"sort" => "id",
					"default" =>true,
				),
				Array(  
					"id" => "REFRESH",
					"content" => GetMessage("TCS_REFRESH"),
					"sort" => false,
					"default" =>true,
				),
				Array(  
					"id" => "LID",
					"content" => GetMessage("TCS_SITE"),
					"sort" => "lid",
					"default" =>true,
				),
				Array(  
					"id" => "FIO",
					"content" => GetMessage("TCS_FIO"),
					"sort" => false,
					"default" =>true,
				),
				Array(  
					"id" => "PHONE",
					"content" => GetMessage("TCS_PHONE"),
					"sort" => false,
					"default" =>true,
				),
				Array(  
					"id" => "DOWN_PAYMENT",
					"content" => GetMessage("TCS_FIRST_AMMOUNT"),
					"sort" => false,
					"default" =>true,
				),
				Array(  
					"id" => "ORDER_PRICE",
					"content" => GetMessage("TCS_ORDER_PRICE"),
					"sort" => false,
					"default" =>true,
				),
				Array(  
					"id" => "BANK_DECISION",
					"content" => GetMessage("TCS_BANK_DECISION"),
					"sort" => false,
					"default" =>true,
				),
				Array(  
					"id" => "STATUS",
					"content" => GetMessage("TCS_STATUS"),
					"sort" => false,
					"default" =>true,
				),
				Array(  
					"id" => "SIGNING_DEADLINE",
					"content" => GetMessage("TCS_SIGNING_DEADLINE"),
					"sort" => false,
					"default" =>true,
				),
				Array(  
					"id" => "DELIVERY_DEADLINE",
					"content" => GetMessage("TCS_DELIVERY_DEADLINE"),
					"sort" => false,
					"default" =>true,
				)
			));	
			
			//$obModule->GetDecision(8);
			while ($arOrder = $dbOrderList->NavNext(true, "f_"))
			{
				if(!$arTCSOrder = $obTCSOrder->TouchOrder($arOrder["ID"],Array()))
				{
					$lAdmin->AddUpdateError($obTCSOrder->LAST_ERROR);
				}
				$iPriceToPay = $this->PriceToPay($arOrder);
				$row =&$lAdmin->AddRow($f_ID, $arOrder, "javascript:ShowOrder({$arOrder["ID"]}, this)", GetMessage("TCS_OPEN_ORDER"));
				$row->AddField("ID", "<b><a id = 'iOrderID{$arOrder["ID"]}' href = 'javascript:void(0)' onclick = 'return ShowOrder({$arOrder["ID"]})'>{$arOrder["ID"]}</a></b><br/>{$arOrder["DATE_INSERT"]}");
				$row->AddField("REFRESH","<a class = 'aRefreshRow' onclick = 'RefreshRow({$arOrder["ID"]},this); return false;' href='javascript:void(0)'></a>");
				$row->AddField("LID",$arOrder["LID"]);
				$arFio = $this->GetParam($arOrder["ID"],$arOrder["LID"], $arOrder["PERSON_TYPE_ID"],"FIO");
				$arEmail = $this->GetParam($arOrder["ID"],$arOrder["LID"], $arOrder["PERSON_TYPE_ID"],"EMAIL");
				$arPhone = $this->GetParam($arOrder["ID"],$arOrder["LID"], $arOrder["PERSON_TYPE_ID"],"PHONE");
				$row->AddField("FIO",$arFio[0]);
				$row->AddField("PHONE",$arPhone[0]);
				$row->AddField("ORDER_PRICE",CurrencyFormat($iPriceToPay,"RUB"));
				$row->AddField("BANK_DECISION",GetMessage("TCS_bank_status_{$arTCSOrder["STATUS"]}"));
				$sAddon="";
				if($arTCSOrder["STATUS"]=="agr")
				{
					$sAddon="<br/>".($arTCSOrder["APPROVED"]=="Y"?"<font color='#00845F'>".GetMessage("TCS_conrirmed")."</font>":"<font color='#8A0000'>".GetMessage("TCS_not_conrirmed")."</font>");
				}
				
				$row->AddField("STATUS",GetMessage("TCS_order_status_{$arTCSOrder["STATUS"]}").$sAddon);
				$sSigningDeadLine="";
				$sDeliveryDeadLine="";
				if(strlen($arTCSOrder["SIGNING_DEADLINE"]) && ($arTCSOrder["SIGNING_DEADLINE"]!="0000-00-00"))
				{
					if(IntVal($arTCSOrder["SIGNING_DAYS"])>=0)
					{
						$sSigningDeadLine = abs($arTCSOrder["SIGNING_DAYS"])." ".$this->PluralForm($arTCSOrder["SIGNING_DAYS"],Array(GetMessage("TCS_DAY1"),GetMessage("TCS_DAY2"),GetMessage("TCS_DAY3")));
					}
					else $sSigningDeadLine = "<span class = 'sError'>".GetMessage("TCS_EXCEEDED")."</span>";
					$sSigningDeadLine .= "<br/>".GetMessage("TCS_DAYS_TO")." ".CDataBase::FormatDate($arTCSOrder["SIGNING_DEADLINE"],"YYYY-MM-DD","DD.MM.YYYY");
				}
				if(strlen($arTCSOrder["DELIVERY_DEADLINE"]) && ($arTCSOrder["DELIVERY_DEADLINE"]!="0000-00-00"))
				{
					if(IntVal($arTCSOrder["DELIVERY_DAYS"])>=0)
					{				
						$sDeliveryDeadLine = $arTCSOrder["DELIVERY_DAYS"]." ".$this->PluralForm($arTCSOrder["DELIVERY_DAYS"],Array(GetMessage("TCS_DAY1"),GetMessage("TCS_DAY2"),GetMessage("TCS_DAY3")));
					}
					else $sDeliveryDeadLine = "<span class = 'sError'>".GetMessage("TCS_EXCEEDED")."</span>";
					$sDeliveryDeadLine .= "<br/>".GetMessage("TCS_DAYS_TO")." ".CDataBase::FormatDate($arTCSOrder["DELIVERY_DEADLINE"],"YYYY-MM-DD","DD.MM.YYYY");
				}
				$row->AddField("SIGNING_DEADLINE",$sSigningDeadLine);
				$row->AddField("DELIVERY_DEADLINE",$sDeliveryDeadLine);
				if(FloatVal($arTCSOrder["DOWN_PAYMENT"])<=0) $arTCSOrder["DOWN_PAYMENT"]="";
				else $arTCSOrder["DOWN_PAYMENT"] = CurrencyFormat($arTCSOrder["DOWN_PAYMENT"],"RUB");
				$row->AddField("DOWN_PAYMENT",$arTCSOrder["DOWN_PAYMENT"]);
			}
			$arGroupActionsTmp["export_".$export_name] = array(
				"action" => "exportData('".$export_name."')",
				"value" => "export_".$export_name,
				"name" => GetMessage("TCS_EXPORT_TO_EXCEL")
			);	
			$lAdmin->AddGroupActionTable($arGroupActionsTmp);			
			return $lAdmin;
		
		}
		
		function GetSiteSecret($sSiteID)
		{
			$arReturn = array(
				"partnerId" => COption::GetOptionString($this->sModuleID, "{$sSiteID}_partner_id",""),
				"apiKey" => COption::GetOptionString($this->sModuleID, "{$sSiteID}_api_key",""),
				"salt" => COption::GetOptionString($this->sModuleID, "{$sSiteID}_salt","")
			);
			return $arReturn;
		}
		
		function GetPaySystemsID()
		{
			$obPaySystems = CSalePaySystem::GetList(Array(),Array(),false,false,Array("ID","PSA_ACTION_FILE"));		
			$arResult = Array();
			while($arPaySystem = $obPaySystems->Fetch())
			{
				if(preg_match("#".$this->sModuleID."#",$arPaySystem["PSA_ACTION_FILE"])) 
				{
					$arResult[] = $arPaySystem["ID"];
				}
			}
			return (empty($arResult))?false:$arResult;
		}
		
		function PriceToPay($arParams)
		{
			IncludeModuleLangFile(__FILE__);
			if(!is_array($arParams))
			{
				$obOrder = CSaleOrder::GetList(Array(),Array("ID"=>IntVal($arParams)),false,false,Array("ID","CURRENCY","PRICE","SUM_PAID"));
				if($arOrder = $obOrder->Fetch()) 
				{
					return $this->PriceToPay($arOrder);
				}
				else 
				{
					$this->LAST_ERROR = GetMessage("TCS_ORDER_NOT_FOUND");
					return false;
				}
			}
			else
			{
				$arKeys = array_keys($arParams);
				if(!($iOrderID = IntVal($arParams["ID"])))
				{
					$this->LAST_ERROR = GetMessage("TCS_ORDER_NOT_FOUND");
					return false;					
				}
				if(!(in_array("CURRENCY",$arKeys) && in_array("PRICE",$arKeys) && in_array("SUM_PAID",$arKeys)))
				{
					return $this->PriceToPay($iOrderID);
				}
				return CCurrencyRates::ConvertCurrency($arParams["PRICE"]-$arParams["SUM_PAID"], $arParams["CURRENCY"],"RUB");
				

			}
		
		}
		
		function GetSitesData($sSiteID=false)
		{
			$arOptions = Array();
			$arFilter = ARray();
			if($sSiteID) $arFilter["LID"] = $sSiteID;
			$obSites = CSite::GetList($by="sort", $order="asc", $arFilter);
			$arSites = Array();
			$sModuleID = $this->sModuleID;
			while($arSite = $obSites->Fetch())
			{
				$arParams = Array(
					"site_info"=>$arSite,
					"partner_id"=>COption::GetOptionString($sModuleID, "{$arSite["LID"]}_partner_id",""),
					"partner_name"=>COption::GetOptionString($sModuleID, "{$arSite["LID"]}_partner_name",$arSite["NAME"]),
					"api_key"=>COption::GetOptionString($sModuleID, "{$arSite["LID"]}_api_key",""),
					"open_widget"=>COption::GetOptionString($sModuleID, "{$arSite["LID"]}_open_widget","n"),
					"courier_mode"=>COption::GetOptionString($sModuleID, "{$arSite["LID"]}_courier_mode","both"),
					"salt"=>COption::GetOptionString($sModuleID, "{$arSite["LID"]}_salt",""),
					"button"=>COption::GetOptionString($sModuleID, "{$arSite["LID"]}_button","1"),
					"active"=>COption::GetOptionString($sModuleID, "{$arSite["LID"]}_active","n"),
					"round"=>COption::GetOptionString($sModuleID, "{$arSite["LID"]}_round","round"),
					"host"=>COption::GetOptionString($sModuleID, "{$arSite["LID"]}_host",$this->GetHost("test","SRC")),
					"host_api"=>COption::GetOptionString($sModuleID, "{$arSite["LID"]}_host_api",$this->GetHost("test","API")),
					"host_type"=>COption::GetOptionString($sModuleID, "{$arSite["LID"]}_host_type","test"),
				);
				$obPersonTypes = CSalePersonType::GetList(
					Array("SORT"=>"ASC"),
					Array("LID"=>$arSite["LID"]),
					false, false,
					array("ID","NAME")
				);
				while($arPersonType = $obPersonTypes->Fetch())
				{
					$sKey = "person_{$arPersonType["ID"]}_data";
					if(strlen($sData = (COption::GetOptionString($sModuleID, $sKey,""))))
					{
						$arData = unserialize($sData);
					}
					else $arData = array();
					$arParams["person_types"][$arPersonType["ID"]] = Array(
						"ID"=>$arPersonType["ID"],
						"NAME"=>$arPersonType["NAME"],
						"KEY"=>$sKey,
						"DATA"=>$arData
					);
				}
				
				$arOptions[$arSite["LID"]] = $arParams;
			}
			
			if($sSiteID) return $arOptions[$sSiteID];
			return $arOptions;
		}
		
		function GetButton($iOrderID, $bNoButton=false)
		{
			global $APPLICATION;
			IncludeModuleLangFile(__FILE__);
			$iTime = microtime(true);
			$sResult = "";
			if(!$bNoButton) $sResult.='<div id = "KupiVkreditButton"></div>';
			$obOrder = CSaleOrder::GetList(Array(),Array("ID"=>$iOrderID),false,false,Array("ID","CURRENCY","PRICE","SUM_PAID","PERSON_TYPE_ID","LID"));
			if($arOrder = $obOrder->Fetch())
			{
				if(!$this->CheckValid($arOrder["LID"])) 
				{
					$sResult.="<font color = '#FF0000'>".GetMessage("TCS_UNAVAILABLE_SERVICE")."</form>";
					return $sResult;
				}
				$order = Array();
				
				$iPriceToPay = $this->PriceToPay($arOrder);
				$obTCSOrder = new CTCSOrder;
				$arData = $obTCSOrder->GetItemsArray($arOrder["ID"]);
				foreach($arData["ITEMS"] as $arItem)
				{
					if(IntVal($arItem["PRODUCT_ID"]))
					{
						$obElement = CIBLockElement::GetList(Array(),Array("ID"=>$arItem["PRODUCT_ID"]),false,false,Array("ID","IBLOCK_SECTION_ID"));
						if($arElement = $obElement->Fetch())
						{
							$obSection = CIBlockSection::GetList(Array(),Array("ID"=>$arElement["IBLOCK_SECTION_ID"]),false,Array("NAME"));
							$sSectionName = "";
							if($arSection = $obSection->Fetch())
							{
								$sSectionName = $arSection["NAME"];
							}
							$arProduct = Array(
								"title"=>$arItem["NAME"],
								"category"=>$sSectionName,
								"qty"=>$arItem["TCS_QUANTITY"],
								"price"=>$arItem["TCS_PRICE_RUB"]
							);
						}
					}
					else 
					{
						$arProduct = Array(
							"title"=>$arItem["NAME"],
							"category"=>"",
							"qty"=>$arItem["TCS_QUANTITY"],
							"price"=>$arItem["TCS_PRICE_RUB"]
						);					
					}
					$order["items"][] = $arProduct;
					
				}
				$details = Array();
				$arFio = ($this->GetParam($iOrderID,$arOrder["LID"], $arOrder["PERSON_TYPE_ID"],"FIO"));
				$arEmail = ($this->GetParam($iOrderID,$arOrder["LID"], $arOrder["PERSON_TYPE_ID"],"EMAIL"));
				$arPhone = ($this->GetParam($iOrderID,$arOrder["LID"], $arOrder["PERSON_TYPE_ID"],"PHONE"));
				$order["details"] = Array(
					//"lastname"=>$arFio[0],
					"lastname"=>"",
					"firstname"=>"",
					"middlename"=>"",
					"email"=>$arEmail[0],
					"cellphone"=>$arPhone[0]
				);
				$arSiteData = $this->GetSiteSecret($arOrder["LID"]);
				$order["partnerId"] = $arSiteData["partnerId"];
				
				$arSiteData = $this->GetSitesData($arOrder["LID"]);
				
				$order["partnerName"] = $arSiteData["partner_name"];
				$order["partnerOrderId"] = $arOrder["ID"];
				$order["deliveryType"] = "";
				$APPLICATION->AddHeadScript($arSiteData["host"]."/widget/vkredit.js");
				array_walk_recursive($order, Array($this,"Encode"));
				$sBase64 = base64_encode(json_encode($order));
				$sSig = $this->SignMessage($sBase64,$arSiteData["salt"]);
				if(!IntVal($arSiteData["button"])) $arSiteData["button"]=1;
				$bOpenWidget = $arSiteData["open_widget"]=="y"?true:false;
				$sResult .= "
					<script>
						vkredit = new VkreditWidget({$arSiteData["button"]}, {$iPriceToPay},  {
								order: '{$sBase64}',
								sig: '{$sSig}'
						});
						".(!$bNoButton?"vkredit.insertButton('KupiVkreditButton');":"")."
						".($bOpenWidget?"vkredit.openWidget();":"")."
					</script>					
				";
				return $sResult;
			}
			
			else return "";
		}

		function GetParam($iOrderID,$iSiteID, $iPersonType,$sField)
		{
			$sModuleID = $this->sModuleID;
			require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/{$sModuleID}/constants.php");
			$arTableOptions = (unserialize(COption::GetOptionString($sModuleID,"person_{$iPersonType}_data")));
			if(!isset($arTableOptions[$sField]))
			{
				$arData = Array($arOptionDefaults[$sField]);
			}
			else $arData = $arTableOptions[$sField];
			$arReturn = Array();
			foreach($arData as $arOption)
			{
				switch($arOption["TYPE"])
				{
					case "ANOTHER":
						$arReturn[] = $arOption["VALUE"];
					break;
					case "USER":
						$obOrder = CSaleOrder::GetList(Array(),Array("ID"=>$iOrderID),false,false,Array("ID","USER_ID"));
						$arOrder = $obOrder->Fetch();
						$obUser = CUser::GetByID($arOrder["USER_ID"]);
						if($arUser = $obUser->Fetch())
						{
							if($arOption["VALUE"]=="USER_FIO")
							{
								$arReturn[] = $arUser["LAST_NAME"].($arUser["NAME"]?" ".$arUser["NAME"]:"").($arUser["SECOND_NAME"]?" ".$arUser["SECOND_NAME"]:"");
							}
							elseif(strlen($arUser[$arOption["VALUE"]])) 
							{
								$arReturn[] = $arUser[$arOption["VALUE"]];
							}
						}
					break;
					case "ORDER":
						$arOrder = CSaleOrder::GetByID($iOrderID);
						$arReturn[] = $arOrder[$arOption["VALUE"]];
					break;
					case "PROPERTY":
						$obProperty = CSaleOrderPropsValue::GetList(
							Array(),
							Array("ORDER_ID"=>$iOrderID,"ORDER_PROPS_ID"=>$arOption["VALUE"]),
							false,false,
							array("VALUE")
						);
						if($arProperty = $obProperty->Fetch())
						{
							if(strlen($arProperty["VALUE"])>0) $arReturn[] = $arProperty["VALUE"];
						}
					break;
				
				}
			
			}
			return $arReturn;
		
		}	

		function RefreshOrder($iOrderID)
		{
			$arReturn = $this->GetDecision($iOrderID);
			$obTCSOrder = new CTCSOrder;
			if($arReturn["status"]=="FAILED")
			{
				$this->LAST_ERROR = $arReturn["result"];
				return false;
			}
			global $DB;
			$arResponseOrder = Array(
				"ORDER_ID"=>$iOrderID,
				"STATUS"=>$arReturn["result"]["OrderStatus"],
				"APPROVED"=>$arReturn["result"]["IsConfirmed"]?"Y":"N",
				"LOAN_AMOUNT"=>$arReturn["result"]["LoanAmount"], // Общая сумма кредита
				"MAX_LOAN_AMOUNT"=>$arReturn["result"]["MaxPossibleLoanAmount"], // Максимально возможная сумма кредита
				"MONTHLY_PAYMENT"=>$arReturn["result"]["MonthlyPayment"], // Сумма месячного платежа
				"DOWN_PAYMENT"=>$arReturn["result"]["Downpayment"], // сумма первоначального взноса
				"SIGNING_DEADLINE"=>$DB->FormatDate($arReturn["result"]["ContractSigningDeadline"],"YYYY-MM-DD","DD.MM.YYYY"), // сумма первоначального взноса
				"DELIVERY_DEADLINE"=>$DB->FormatDate($arReturn["result"]["ContractDeliveryDeadline"],"YYYY-MM-DD","DD.MM.YYYY"), // сумма первоначального взноса
				"PAYMENT_COUNT"=>$arReturn["result"]["PaymentCount"], // число платежей
				"COMISSION"=>$arReturn["result"]["Commission"], // комиссия за выдачу кредита (включена в LoanAmount)
				"PROCESSING"=>IntVal($arReturn["result"]["BeingProcessed"]),
				"SIGNING_TYPE"=>strlen($arReturn["result"]["ActualSigningType"])?$arReturn["result"]["ActualSigningType"]:$arReturn["result"]["SigningType"],
				"SCHEDULER_URL"=>strlen(trim($arReturn["result"]["SchedulerUrl"]))?trim($arReturn["result"]["SchedulerUrl"]):false,
				"POSSIBLE_SIGNING_TYPES"=>$arReturn["result"]["PossibleSigningTypes"]
			);
			
			if($arResponseOrder["STATUS"]=="can")
			{
				$obOrder = new CTCSOrder;
				$arBXOrder = $obOrder->TouchOrder($arResponseOrder["ORDER_ID"]);
				if($arBXOrder["CANCELED"]!="Y")
				{
					$arResponseOrder["CANCELED"]="Y";
					$arResponseOrder["CANCEL_STATUS"]="error_offer";
				}
			}
			
			$obTCSOrder->Update($iOrderID, $arResponseOrder);
			if(!$arTCSOrder = $obTCSOrder->TouchOrder($iOrderID,Array()))
			{
				$this->LAST_ERROR = $obTCSOrder->LAST_ERROR;
				return false;
			}		
			foreach($arTCSOrder as $sKey=>$sValue) $arResponseOrder[$sKey]=$sValue;
			return $arResponseOrder;
		
		}
	}
?>