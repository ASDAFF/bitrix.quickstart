<?
IncludeModuleLangFile(__FILE__);

class CAllCatalogDiscount
{
	public function CheckFields($ACTION, &$arFields, $ID = 0)
	{
		global $APPLICATION;
		global $DB;

		if ((is_set($arFields, "SITE_ID") || $ACTION=="ADD") && empty($arFields["SITE_ID"]))
		{
			$APPLICATION->ThrowException(GetMessage("KGD_EMPTY_SITE"), "SITE_ID");
			return false;
		}

		if ((is_set($arFields, "CURRENCY") || $ACTION=="ADD") && empty($arFields["CURRENCY"]))
		{
			$APPLICATION->ThrowException(GetMessage("KGD_EMPTY_CURRENCY"), "CURRENCY");
			return false;
		}

		if ((is_set($arFields, "NAME") || $ACTION=="ADD") && empty($arFields["NAME"]))
		{
			$APPLICATION->ThrowException(GetMessage("KGD_EMPTY_NAME"), "NAME");
			return false;
		}

		if ((is_set($arFields, "ACTIVE") || $ACTION=="ADD") && $arFields["ACTIVE"] != "N")
			$arFields["ACTIVE"] = "Y";
		if ((is_set($arFields, "ACTIVE_FROM") || $ACTION=="ADD") && (!$DB->IsDate($arFields["ACTIVE_FROM"], false, LANG, "FULL")))
			$arFields["ACTIVE_FROM"] = false;
		if ((is_set($arFields, "ACTIVE_TO") || $ACTION=="ADD") && (!$DB->IsDate($arFields["ACTIVE_TO"], false, LANG, "FULL")))
			$arFields["ACTIVE_TO"] = false;

		if ((is_set($arFields, "RENEWAL") || $ACTION=="ADD") && $arFields["RENEWAL"] != "Y")
			$arFields["RENEWAL"] = "N";

		if ((is_set($arFields, "MAX_USES") || $ACTION=="ADD") && intval($arFields["MAX_USES"]) <= 0)
			$arFields["MAX_USES"] = 0;
		if ((is_set($arFields, "COUNT_USES") || $ACTION=="ADD") && intval($arFields["COUNT_USES"]) <= 0)
			$arFields["COUNT_USES"] = 0;

		if ((is_set($arFields, "CATALOG_COUPONS") || $ACTION=="ADD") && !is_array($arFields['CATALOG_COUPONS']) && empty($arFields["CATALOG_COUPONS"]))
			$arFields["CATALOG_COUPONS"] = false;

		if ((is_set($arFields, "SORT") || $ACTION=="ADD") && intval($arFields["SORT"]) <= 0)
			$arFields["SORT"] = 100;

		if (is_set($arFields, "MAX_DISCOUNT") || $ACTION=="ADD")
		{
			$arFields["MAX_DISCOUNT"] = str_replace(",", ".", $arFields["MAX_DISCOUNT"]);
			$arFields["MAX_DISCOUNT"] = doubleval($arFields["MAX_DISCOUNT"]);
		}

		if ((is_set($arFields, "VALUE_TYPE") || $ACTION=="ADD") && !in_array($arFields["VALUE_TYPE"],array("F","P","S")))
			$arFields["VALUE_TYPE"] = "P";

		if (is_set($arFields, "VALUE") || $ACTION=="ADD")
		{
			$arFields["VALUE"] = str_replace(",", ".", $arFields["VALUE"]);
			$arFields["VALUE"] = doubleval($arFields["VALUE"]);
			if (!(0 < $arFields["VALUE"]))
			{
				$APPLICATION->ThrowException(GetMessage("BT_MOD_CATALOG_DISC_ERR_BAD_VALUE"), "VALUE");
				return false;
			}
		}

		if (isset($arFields["VALUE_TYPE"]) && $arFields["VALUE"])
		{
			if ('P' == $arFields["VALUE_TYPE"] && 100 < $arFields["VALUE"])
			{
				$APPLICATION->ThrowException(GetMessage("BT_MOD_CATALOG_DISC_ERR_BAD_VALUE"), "VALUE");
				return false;
			}
		}

		if (is_set($arFields, "MIN_ORDER_SUM") || $ACTION=="ADD")
		{
			$arFields["MIN_ORDER_SUM"] = str_replace(",", ".", $arFields["MIN_ORDER_SUM"]);
			$arFields["MIN_ORDER_SUM"] = doubleval($arFields["MIN_ORDER_SUM"]);
		}

		if ((is_set($arFields, 'PRIORITY') || $ACTION == 'ADD') && intval($arFields['PRIORITY']) <= 0)
			$arFields['PRIORITY'] = 1;
		if ((is_set($arFields, 'LAST_DISCOUNT') || $ACTION == 'ADD') && $arFields["LAST_DISCOUNT"] != "N")
			$arFields["LAST_DISCOUNT"] = 'Y';

		$arFields['TYPE'] = DISCOUNT_TYPE_STANDART;
		$arFields['VERSION'] = CATALOG_DISCOUNT_NEW_VERSION;

		if (is_set($arFields, 'UNPACK'))
			unset($arFields['UNPACK']);

		if (is_set($arFields, 'CONDITIONS') || $ACTION == 'ADD')
		{
			if (empty($arFields['CONDITIONS']))
			{
				$APPLICATION->ThrowException(GetMessage("BT_MOD_CATALOG_DISC_ERR_EMPTY_CONDITIONS"), "CONDITIONS");
				return false;
			}
			else
			{
				if (!is_array($arFields['CONDITIONS']))
				{
					if (!CheckSerializedData($arFields['CONDITIONS']))
					{
						$APPLICATION->ThrowException(GetMessage("BT_MOD_CATALOG_DISC_ERR_BAD_CONDITIONS"), "CONDITIONS");
						return false;
					}
					$arFields['CONDITIONS'] = unserialize($arFields['CONDITIONS']);
					if (!is_array($arFields['CONDITIONS']) || empty($arFields['CONDITIONS']))
					{
						$APPLICATION->ThrowException(GetMessage("BT_MOD_CATALOG_DISC_ERR_BAD_CONDITIONS"), "CONDITIONS");
						return false;
					}
				}
				$obCond = new CCatalogCondTree();
				$boolCond = $obCond->Init(BT_COND_MODE_GENERATE, BT_COND_BUILD_CATALOG, array());
				if (!$boolCond)
				{
					return false;
				}
				$strEval = $obCond->Generate($arFields['CONDITIONS'], array('FIELD' => '$arProduct'));
				if (empty($strEval) || 'false' == $strEval)
				{
					$APPLICATION->ThrowException(GetMessage("BT_MOD_CATALOG_DISC_ERR_BAD_CONDITIONS"), "CONDITIONS");
					return false;
				}
				$arFields['UNPACK'] = $strEval;
				$arFields['CONDITIONS'] = serialize($arFields['CONDITIONS']);
			}
		}

		return true;
	}

	public function Add($arFields)
	{
		global $DB;

		$mxRows = self::__ParseArrays($arFields);
		if (!is_array($mxRows) || empty($mxRows))
			return false;

		$boolNewVersion = true;
		if (!is_set($arFields, 'CONDITIONS'))
		{
			self::__ConvertOldConditions('ADD', $arFields);
			$boolNewVersion = false;
		}

		$ID = CCatalogDiscount::_Add($arFields);
		$ID = intval($ID);
		if ($ID <= 0)
			return false;

		if ($boolNewVersion)
		{
			self::__GetOldOneEntity($arFields, 'IBLOCK_IDS', 'CondIBIBlock');
			self::__GetOldOneEntity($arFields, 'SECTION_IDS', 'CondIBSection');
			self::__GetOldOneEntity($arFields, 'PRODUCT_IDS', 'CondIBElement');
		}

		if (!CCatalogDiscount::__UpdateSubdiscount($ID, $mxRows))
			return false;

		CCatalogDiscount::__UpdateOldEntities($ID, $arFields, false);

		if (is_set($arFields, "CATALOG_COUPONS"))
		{
			if (!is_array($arFields["CATALOG_COUPONS"]))
				$arFields["CATALOG_COUPONS"] = array("DISCOUNT_ID" => $ID, "ACTIVE" => "Y", "ONE_TIME" => "Y", "COUPON" => $arFields["CATALOG_COUPONS"], "DATE_APPLY" => false);

			$arKeys = array_keys($arFields["CATALOG_COUPONS"]);
			if (!is_array($arFields["CATALOG_COUPONS"][$arKeys[0]]))
				$arFields["CATALOG_COUPONS"] = array($arFields["CATALOG_COUPONS"]);

			foreach ($arFields["CATALOG_COUPONS"] as &$arOneCoupon)
			{
				if (!empty($arOneCoupon['COUPON']))
				{
					$arOneCoupon['DISCOUNT_ID'] = $ID;
					CCatalogDiscountCoupon::Add($arOneCoupon, false);
				}
				if (isset($arOneCoupon))
					unset($arOneCoupon);
			}
		}


		CCatalogDiscount::SaveFilterOptions();

		$events = GetModuleEvents("catalog", "OnDiscountAdd");
		while ($arEvent = $events->Fetch())
			ExecuteModuleEventEx($arEvent, array($ID, $arFields));

		return $ID;
	}

	public function Update($ID, $arFields)
	{
		global $DB;

		$ID = intval($ID);

		$boolUpdateRestrictions = false;
		if (
			(isset($arFields['GROUP_IDS']) && is_array($arFields['GROUP_IDS']) && !empty($arFields['GROUP_IDS']))
			|| (isset($arFields['CATALOG_GROUP_IDS']) && is_array($arFields['CATALOG_GROUP_IDS']) && !empty($arFields['CATALOG_GROUP_IDS']))
		)
		{
			$boolUpdateRestrictions = true;
		}

		if ($boolUpdateRestrictions)
		{
			$mxRows = self::__ParseArrays($arFields);
			if (!is_array($mxRows) || empty($mxRows))
				return false;
		}

		$boolNewVersion = true;
		if (!is_set($arFields, 'CONDITIONS'))
		{
			self::__ConvertOldConditions('UPDATE', $arFields);
			$boolNewVersion = false;
		}

		if (!CCatalogDiscount::_Update($ID, $arFields))
			return false;

		if ($boolNewVersion)
		{
			self::__GetOldOneEntity($arFields, 'IBLOCK_IDS', 'CondIBIBlock');
			self::__GetOldOneEntity($arFields, 'SECTION_IDS', 'CondIBSection');
			self::__GetOldOneEntity($arFields, 'PRODUCT_IDS', 'CondIBElement');
		}

		if ($boolUpdateRestrictions)
		{
			if (!CCatalogDiscount::__UpdateSubdiscount($ID, $mxRows))
				return false;
		}

		CCatalogDiscount::__UpdateOldEntities($ID, $arFields, true);

		if (is_set($arFields, "CATALOG_COUPONS"))
		{
			if (!is_array($arFields["CATALOG_COUPONS"]))
				$arFields["CATALOG_COUPONS"] = array("DISCOUNT_ID" => $ID, "ACTIVE" => "Y", "ONE_TIME" => "Y", "COUPON" => $arFields["CATALOG_COUPONS"], "DATE_APPLY" => false);

			$arKeys = array_keys($arFields["CATALOG_COUPONS"]);
			if (!is_array($arFields["CATALOG_COUPONS"][$arKeys[0]]))
				$arFields["CATALOG_COUPONS"] = array($arFields["CATALOG_COUPONS"]);

			foreach ($arFields["CATALOG_COUPONS"] as &$arOneCoupon)
			{
				if (!empty($arOneCoupon['COUPON']))
				{
					$arOneCoupon['DISCOUNT_ID'] = $ID;
					CCatalogDiscountCoupon::Add($arOneCoupon, false);
				}
				if (isset($arOneCoupon))
					unset($arOneCoupon);
			}
		}

		CCatalogDiscount::SaveFilterOptions();

		$events = GetModuleEvents("catalog", "OnDiscountUpdate");
		while ($arEvent = $events->Fetch())
			ExecuteModuleEventEx($arEvent, array($ID, $arFields));

		return $ID;
	}

	public function SetCoupon($coupon)
	{
		return CCatalogDiscountCoupon::SetCoupon($coupon);
	}

	public function GetCoupons()
	{
		return CCatalogDiscountCoupon::GetCoupons();
	}

	public function EraseCoupon($strCoupon)
	{
		return CCatalogDiscountCoupon::EraseCoupon($strCoupon);
	}

	public function ClearCoupon()
	{
		CCatalogDiscountCoupon::ClearCoupon();
	}

	public function SetCouponByManage($intUserID,$strCoupon)
	{
		return CCatalogDiscountCoupon::SetCouponByManage($intUserID,$strCoupon);
	}

	public function GetCouponsByManage($intUserID)
	{
		return CCatalogDiscountCoupon::GetCouponsByManage($intUserID);
	}

	public function EraseCouponByManage($intUserID,$strCoupon)
	{
		return CCatalogDiscountCoupon::EraseCouponByManage($intUserID,$strCoupon);
	}

	public function ClearCouponsByManage($intUserID)
	{
		return CCatalogDiscountCoupon::ClearCouponsByManage($intUserID);
	}

	public function OnCurrencyDelete($Currency)
	{
		global $DB;
		if (empty($Currency)) return false;

		$dbDiscounts = CCatalogDiscount::GetList(array(), array("CURRENCY" => $Currency), false, false, array("ID"));
		while ($arDiscounts = $dbDiscounts->Fetch())
		{
			CCatalogDiscount::Delete($arDiscounts["ID"]);
		}

		return true;
	}

	public function OnGroupDelete($GroupID)
	{
		global $DB;
		$GroupID = intval($GroupID);

		return $DB->Query("DELETE FROM b_catalog_discount2group WHERE GROUP_ID = ".$GroupID." ", true);
	}

	public function GenerateDataFile($ID)
	{
	}

	public function ClearFile($ID, $strDataFileName = false)
	{
	}

	public function GetDiscountByPrice($productPriceID, $arUserGroups = array(), $renewal = "N", $siteID = false, $arDiscountCoupons = false)
	{
		global $DB;
		global $APPLICATION;

		$events = GetModuleEvents("catalog", "OnGetDiscountByPrice");
		while ($arEvent = $events->Fetch())
		{
			$mxResult = ExecuteModuleEventEx($arEvent, array($productPriceID, $arUserGroups, $renewal, $siteID, $arDiscountCoupons));
			if (true !== $mxResult)
				return $mxResult;
		}

		$productPriceID = intval($productPriceID);
		if ($productPriceID <= 0)
		{
			$APPLICATION->ThrowException(GetMessage("BT_MOD_CATALOG_DISC_ERR_PRICE_ID_ABSENT"), "NO_PRICE_ID");
			return false;
		}

		if (!is_array($arUserGroups) && intval($arUserGroups)."|" == $arUserGroups."|")
			$arUserGroups = array(intval($arUserGroups));

		if (!is_array($arUserGroups))
			$arUserGroups = array();

		if (!in_array(2, $arUserGroups))
			$arUserGroups[] = 2;

		$renewal = (($renewal == "N") ? "N" : "Y");

		if ($siteID === false)
			$siteID = SITE_ID;

		if ($arDiscountCoupons === false)
			$arDiscountCoupons = CCatalogDiscountCoupon::GetCoupons();

		$dbPrice = CPrice::GetListEx(
			array(),
			array("ID" => $productPriceID),
			false,
			false,
			array("ID", "PRODUCT_ID", "CATALOG_GROUP_ID", "ELEMENT_IBLOCK_ID")
		);
		if ($arPrice = $dbPrice->Fetch())
		{
			return CCatalogDiscount::GetDiscount($arPrice["PRODUCT_ID"], $arPrice["ELEMENT_IBLOCK_ID"], $arPrice["CATALOG_GROUP_ID"], $arUserGroups, $renewal, $siteID, $arDiscountCoupons);
		}
		else
		{
			$APPLICATION->ThrowException(str_replace("#ID#", $productPriceID, GetMessage("BT_MOD_CATALOG_DISC_ERR_PRICE_ID_NOT_FOUND")), "NO_PRICE");
			return false;
		}
	}

	public function GetDiscountByProduct($productID = 0, $arUserGroups = array(), $renewal = "N", $arCatalogGroups = array(), $siteID = false, $arDiscountCoupons = false)
	{
		global $DB;
		global $APPLICATION;

		$events = GetModuleEvents("catalog", "OnGetDiscountByProduct");
		while ($arEvent = $events->Fetch())
		{
			$mxResult = ExecuteModuleEventEx($arEvent, array($productID, $arUserGroups, $renewal, $arCatalogGroups, $siteID, $arDiscountCoupons));
			if (true !== $mxResult)
				return $mxResult;
		}

		$productID = intval($productID);

		if (isset($arCatalogGroups))
		{
			if (is_array($arCatalogGroups))
			{
				array_walk($arCatalogGroups, create_function("&\$item", "\$item=intval(\$item);"));
				$arCatalogGroups = array_unique($arCatalogGroups);
			}
			else
			{
				if (intval($arCatalogGroups)."|" == $arCatalogGroups."|")
					$arCatalogGroups = array(intval($arCatalogGroups));
				else
					$arCatalogGroups = array();
			}
		}
		else
		{
			$arCatalogGroups = array();
		}

		if (!is_array($arUserGroups) && intval($arUserGroups)."|" == $arUserGroups."|")
			$arUserGroups = array(intval($arUserGroups));

		if (!is_array($arUserGroups))
			$arUserGroups = array();

		if (!in_array(2, $arUserGroups))
			$arUserGroups[] = 2;

		$renewal = (($renewal == "N") ? "N" : "Y");

		if ($siteID === false)
			$siteID = SITE_ID;

		if ($arDiscountCoupons === false)
			$arDiscountCoupons = CCatalogDiscountCoupon::GetCoupons();

		$dbElement = CIBlockElement::GetList(array(), array("ID"=>$productID), false, false, array("ID","IBLOCK_ID"));
		if (!($arElement = $dbElement->Fetch()))
		{
			$APPLICATION->ThrowException(str_replace("#ID#", $productID, GetMessage("BT_MOD_CATALOG_DISC_ERR_ELEMENT_ID_NOT_FOUND")), "NO_ELEMENT");
			return false;
		}

		return CCatalogDiscount::GetDiscount($productID, $arElement["IBLOCK_ID"], $arCatalogGroups, $arUserGroups, $renewal, $siteID, $arDiscountCoupons);
	}

	public function GetDiscount($intProductID, $intIBlockID, $arCatalogGroups = array(), $arUserGroups = array(), $strRenewal = "N", $siteID = false, $arDiscountCoupons = false, $boolSKU = true, $boolGetIDS = false)
	{
		global $DB;
		global $APPLICATION;
		global $stackCacheManager;

		$events = GetModuleEvents("catalog", "OnGetDiscount");
		while ($arEvent = $events->Fetch())
		{
			$mxResult = ExecuteModuleEventEx($arEvent, array($intProductID, $intIBlockID, $arCatalogGroups, $arUserGroups, $strRenewal, $siteID, $arDiscountCoupons, $boolSKU, $boolGetIDS));
			if (true !== $mxResult)
				return $mxResult;
		}

		$boolSKU = (true === $boolSKU ? true : false);
		$boolGetIDS = (true === $boolGetIDS ? true : false);

		$intProductID = intval($intProductID);
		if (0 >= $intProductID)
		{
			$APPLICATION->ThrowException(GetMessage("BT_MOD_CATALOG_DISC_ERR_PRODUCT_ID_ABSENT"), "NO_PRODUCT_ID");
			return false;
		}

		$intIBlockID = intval($intIBlockID);
		if (0 >= $intIBlockID)
		{
			$APPLICATION->ThrowException(GetMessage("BT_MOD_CATALOG_DISC_ERR_IBLOCK_ID_ABSENT"), "NO_IBLOCK_ID");
			return false;
		}

		if (isset($arCatalogGroups))
		{
			if (is_array($arCatalogGroups))
			{
				array_walk($arCatalogGroups, create_function("&\$item", "\$item=intval(\$item);"));
				$arCatalogGroups = array_unique($arCatalogGroups);
			}
			else
			{
				if (intval($arCatalogGroups)."|" == $arCatalogGroups."|")
					$arCatalogGroups = array(intval($arCatalogGroups));
				else
					$arCatalogGroups = array();
			}
		}
		else
		{
			$arCatalogGroups = array();
		}

		if (!is_array($arUserGroups) && intval($arUserGroups)."|" == $arUserGroups."|")
			$arUserGroups = array(intval($arUserGroups));

		if (!is_array($arUserGroups))
			$arUserGroups = array();

		if (!in_array(2, $arUserGroups))
			$arUserGroups[] = 2;

		$strRenewal = (($strRenewal == "Y") ? "Y" : "N");

		if ($siteID === false)
			$siteID = SITE_ID;

		if ($arDiscountCoupons === false)
			$arDiscountCoupons = CCatalogDiscountCoupon::GetCoupons();

		$arSKU = false;
		if ($boolSKU)
		{
			$arSKU = CCatalogSKU::GetProductInfo($intProductID,$intIBlockID);
			if (!is_array($arSKU))
			{
				$boolSKU = false;
			}
		}

		$arResult = array();
		$arResultID = array();

		$intCacheTime = CATALOG_CACHE_DEFAULT_TIME;
		if (defined("CATALOG_CACHE_TIME"))
			$intCacheTime = intval(CATALOG_CACHE_TIME);

		if ($boolGetIDS)
			$strCacheKey = "I-IDS";
		else
			$strCacheKey = "I";

		$strPriceTypeFilter = COption::GetOptionString("catalog", "do_use_discount_cat_group", "Y");
		$strUserGroupFilter = COption::GetOptionString("catalog", "do_use_discount_group", "Y");

		$strCacheKey .= "_".$intProductID;

		$arProductSections = array();
		$strCacheKeyGroups = $intProductID."_".$intIBlockID;

		$stackLengthGroups = 200;
		if (defined("CATALOG_STACK_ELEMENT_LENGTH"))
			$stackLengthGroups = intval(CATALOG_STACK_ELEMENT_LENGTH);

		$stackCacheManager->SetLength("catalog_element_groups", $stackLengthGroups);
		$stackCacheManager->SetTTL("catalog_element_groups", $intCacheTime);
		if ($stackCacheManager->Exist("catalog_element_groups", $strCacheKeyGroups))
		{
			$arProductSections = $stackCacheManager->Get("catalog_element_groups", $strCacheKeyGroups);
		}
		else
		{
			$arProductSections = array();
			$dbElementSections = CIBlockElement::GetElementGroups($intProductID, true);
			while ($arElementSections = $dbElementSections->Fetch())
			{
				$arProductSections[] = intval($arElementSections["ID"]);
				$rsChains = CIBlockSection::GetNavChain($intIBlockID,$arElementSections["ID"]);
				while ($arChain = $rsChains->Fetch())
				{
					$arProductSections[] = intval($arChain["ID"]);
				}
			}
			if (!empty($arProductSections))
			{
				$arProductSections = array_unique($arProductSections);
				sort($arProductSections);
			}
			$stackCacheManager->Set("catalog_element_groups", $strCacheKeyGroups, $arProductSections);
		}

		if (!empty($arProductSections))
			$strCacheKey .= '_'.implode('-',$arProductSections);
		else
			$strCacheKey .= '_x';

		if ('Y' == $strPriceTypeFilter)
		{
			if (!empty($arCatalogGroups))
				$strCacheKey .= '_'.implode('-',$arCatalogGroups);
			else
				$strCacheKey .= '_x';
		}
		else
		{
			$strCacheKey .= "_x";
		}

		if ('Y' == $strUserGroupFilter)
		{
			if (!empty($arUserGroups))
				$strCacheKey .= '_'.implode('-',$arUserGroups);
			else
				$strCacheKey .= '_x';
		}
		else
		{
			$strCacheKey .= "_x";
		}

		$strCacheKey .= "_".$intIBlockID;

		$strCacheKey .= "_".$strRenewal;
		$strCacheKey .= "_".$siteID;
		if (!empty($arDiscountCoupons))
		{
			$strCacheKey .= (is_array($arDiscountCoupons) ? '_'.implode('-',$arDiscountCoupons) : '_'.$arDiscountCoupons);
		}
		else
		{
			$strCacheKey .= "_x";
		}

		if ($boolSKU)
		{
			$strCacheKey .= '_'.$arSKU['ID'];
		}
		else
		{
			$strCacheKey .= '_x';
		}

		$stackLength = 100;
		if (defined("CATALOG_STACK_DISCOUNT_LENGTH"))
			$stackLength = intval(CATALOG_STACK_DISCOUNT_LENGTH);

		$arFilter = array(
			'PRICE_TYPE_ID' => $arCatalogGroups,
			'USER_GROUP_ID' => $arUserGroups,
		);

		$arDiscountIDs = CCatalogDiscount::__GetDiscountID($arFilter);

		if (!empty($arDiscountIDs))
		{
			$arProduct = array();
			$arSelect = array('ID', 'IBLOCK_ID', 'CODE', 'XML_ID', 'NAME', 'ACTIVE', 'DATE_ACTIVE_FROM', 'DATE_ACTIVE_TO',
				'SORT', 'PREVIEW_TEXT', 'DETAIL_TEXT', 'DATE_CREATE', 'CREATED_BY', 'TIMESTAMP_X', 'MODIFIED_BY', 'TAGS', 'CATALOG_QUANTITY');
			$rsProducts = CIBlockElement::GetList(array(), array('ID' => $intProductID), false, false, $arSelect);
			if (!($obProduct = $rsProducts->GetNextElement(false,false)))
				return false;

			$arProduct = $obProduct->GetFields();
			$arProduct['SECTION_ID'] = $arProductSections;

			$arProps = $obProduct->GetProperties(array(), array('ACTIVE' => 'Y'));
			foreach ($arProps as &$arOneProp)
			{
				if ('F' == $arOneProp['PROPERTY_TYPE'])
					continue;
				if ('N' == $arOneProp['MULTIPLE'])
				{
					if ('L' == $arOneProp['PROPERTY_TYPE'])
					{
						$arOneProp['VALUE_ENUM_ID'] = intval($arOneProp['VALUE_ENUM_ID']);
						if (0 < $arOneProp['VALUE_ENUM_ID'])
							$arProduct['PROPERTY_'.$arOneProp['ID'].'_VALUE'] = $arOneProp['VALUE_ENUM_ID'];
						else
							$arProduct['PROPERTY_'.$arOneProp['ID'].'_VALUE'] = -1;
					}
					elseif ('E' == $arOneProp['PROPERTY_TYPE'] || 'G' == $arOneProp['PROPERTY_TYPE'])
					{
						$arOneProp['VALUE'] = intval($arOneProp['VALUE']);
						if (0 < $arOneProp['VALUE'])
							$arProduct['PROPERTY_'.$arOneProp['ID'].'_VALUE'] = $arOneProp['VALUE'];
						else
							$arProduct['PROPERTY_'.$arOneProp['ID'].'_VALUE'] = -1;
					}
					else
					{
						$arProduct['PROPERTY_'.$arOneProp['ID'].'_VALUE'] = $arOneProp['VALUE'];
					}
				}
				else
				{
					if ('L' == $arOneProp['PROPERTY_TYPE'])
					{
						$arValues = array();
						if (is_array($arOneProp['VALUE_ENUM_ID']) && !empty($arOneProp['VALUE_ENUM_ID']))
						{
							foreach ($arOneProp['VALUE_ENUM_ID'] as &$intOneValue)
							{
								$intOneValue = intval($intOneValue);
								if (0 < $intOneValue)
									$arValues[] = $intOneValue;
							}
							if (isset($intOneValue))
								unset($intOneValue);
						}
						if (empty($arValues))
							$arValues = array(-1);
						$arProduct['PROPERTY_'.$arOneProp['ID'].'_VALUE'] = $arValues;
					}
					elseif ('E' == $arOneProp['PROPERTY_TYPE'] || 'G' == $arOneProp['PROPERTY_TYPE'])
					{
						$arValues = array();
						if (is_array($arOneProp['VALUE']) && !empty($arOneProp['VALUE']))
						{
							foreach ($arOneProp['VALUE'] as &$intOneValue)
							{
								$intOneValue = intval($intOneValue);
								if (0 < $intOneValue)
									$arValues[] = $intOneValue;
							}
							if (isset($intOneValue))
								unset($intOneValue);
						}
						if (empty($arValues))
							$arValues = array(-1);
						$arProduct['PROPERTY_'.$arOneProp['ID'].'_VALUE'] = $arValues;
					}
					else
					{
						$arProduct['PROPERTY_'.$arOneProp['ID'].'_VALUE'] = $arOneProp['VALUE'];
					}
				}
				if (!is_array($arProduct['PROPERTY_'.$arOneProp['ID'].'_VALUE']))
					$arProduct['PROPERTY_'.$arOneProp['ID'].'_VALUE'] = array($arProduct['PROPERTY_'.$arOneProp['ID'].'_VALUE']);
			}
			if (isset($arOneProp))
				unset($arOneProp);

			$arSelect = array(
				"ID", "TYPE", "SITE_ID", "ACTIVE", "ACTIVE_FROM", "ACTIVE_TO",
				"RENEWAL", "NAME", "SORT", "MAX_DISCOUNT", "VALUE_TYPE", "VALUE", "CURRENCY",
				"PRIORITY", "LAST_DISCOUNT",
				"COUPON", "COUPON_ONE_TIME", "COUPON_ACTIVE", 'UNPACK'
			);
			$strDate = date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL")));
			$arFilter = array(
				"ID" => $arDiscountIDs,
				"SITE_ID" => $siteID,
				"TYPE" => DISCOUNT_TYPE_STANDART,
				"ACTIVE" => "Y",
				"RENEWAL" => $strRenewal,
				"+<=ACTIVE_FROM" => $strDate,
				"+>=ACTIVE_TO" => $strDate,
			);

			if (is_array($arDiscountCoupons))
			{
				$arFilter["+COUPON"] = $arDiscountCoupons;
			}

			$rsPriceDiscounts = CCatalogDiscount::GetList(
				array(),
				$arFilter,
				false,
				false,
				$arSelect
			);
			while ($arPriceDiscount = $rsPriceDiscounts->Fetch())
			{
				if ($arPriceDiscount['COUPON_ACTIVE'] != 'N')
				{
					if (CCatalogDiscount::__Unpack($arProduct, $arPriceDiscount['UNPACK']))
					{
						unset($arPriceDiscount['UNPACK']);
						$arResult[] = $arPriceDiscount;
						$arResultID[] = $arPriceDiscount['ID'];
					}
				}
			}
		}

		if ($boolSKU)
		{
			$arDiscountParent = CCatalogDiscount::GetDiscount($arSKU['ID'], $arSKU['IBLOCK_ID'], $arCatalogGroups, $arUserGroups, $strRenewal, $siteID, $arDiscountCoupons, false, false);
			if (!empty($arDiscountParent))
			{
				if (empty($arResult))
				{
					$arResult = $arDiscountParent;
				}
				else
				{
					foreach ($arDiscountParent as &$arOneParentDiscount)
					{
						if (in_array($arOneParentDiscount['ID'], $arResultID))
							continue;
						$arResult[] = $arOneParentDiscount;
						$arResultID[] = $arOneParentDiscount['ID'];
					}
					if (isset($arOneParentDiscount))
						unset($arOneParentDiscount);
				}
			}
		}

		if (!$boolGetIDS)
		{
			$arDiscSave = CCatalogDiscountSave::GetDiscount(array(
				'USER_ID' => 0,
				'USER_GROUPS' => $arUserGroups,
				'SITE_ID' => $siteID
			));
			if (!empty($arDiscSave))
			{
				$arResult = (!empty($arResult) ? array_merge($arResult, $arDiscSave) : $arDiscSave);
			}
		}
		else
		{
			$arResult = $arResultID;
		}

		$events = GetModuleEvents("catalog", "OnGetDiscountResult");
		while ($arEvent = $events->Fetch())
			ExecuteModuleEventEx($arEvent, array(&$arResult));

		return $arResult;
	}

	public function HaveCoupons($ID, $excludeID = 0)
	{
		$ID = intval($ID);
		if ($ID <= 0)
			return false;

		$arFilter = array("DISCOUNT_ID" => $ID);

		$excludeID = intval($excludeID);
		if ($excludeID > 0)
			$arFilter["!ID"] = $excludeID;

		$dbRes = CCatalogDiscountCoupon::GetList(array(), $arFilter, false, array("nTopCount" => 1), array("ID"));
		if ($dbRes->Fetch())
			return true;
		else
			return false;
	}

	public function OnSetCouponList($intUserID, $arCoupons, $arModules)
	{
		return CCatalogDiscountCoupon::OnSetCouponList($intUserID, $arCoupons, $arModules);
	}

	public function OnClearCouponList($intUserID, $arCoupons, $arModules)
	{
		return CCatalogDiscountCoupon::OnClearCouponList($intUserID, $arCoupons, $arModules);
	}

	public function OnDeleteCouponList($intUserID, $arModules)
	{
		return CCatalogDiscountCoupon::OnDeleteCouponList($intUserID, $arModules);
	}

	protected function __ParseArrays(&$arFields)
	{
		global $APPLICATION;

		$arMsg = array();
		$boolResult = true;

		$arResult = array(
		);

		if (!self::__CheckOneEntity($arFields, 'GROUP_IDS'))
		{
			$arMsg[] = array('id' => 'GROUP_IDS', "text" => GetMessage('BT_MOD_CATALOG_DISC_ERR_PARSE_USER_GROUP'));
			$boolResult = false;
		}
		if (!self::__CheckOneEntity($arFields, 'CATALOG_GROUP_IDS'))
		{
			$arMsg[] = array('id' => 'CATALOG_GROUP_IDS', "text" => GetMessage('BT_MOD_CATALOG_DISC_ERR_PARSE_PRICE_TYPE'));
			$boolResult = false;
		}

		if ($boolResult)
		{
			$arTempo = array(
				'USER_GROUP_ID' => $arFields['GROUP_IDS'],
				'PRICE_TYPE_ID' => $arFields['CATALOG_GROUP_IDS'],
			);

			$arOrder = array(
				'USER_GROUP_ID',
				'PRICE_TYPE_ID',
			);

			self::__ArrayMultiple($arOrder, $arResult, $arTempo);
			unset($arTempo);
		}

		if (!$boolResult)
		{
			$obError = new CAdminException($arMsg);
			$APPLICATION->ResetException();
			$APPLICATION->ThrowException($obError);
			return $boolResult;
		}
		else
		{
			return $arResult;
		}
	}

	protected function __CheckOneEntity(&$arFields, $strEntityID)
	{
		$boolResult = false;
		$strEntityID = trim(strval($strEntityID));
		if (!empty($strEntityID))
		{
			if (is_array($arFields) && !empty($arFields))
			{
				if (is_set($arFields, $strEntityID))
				{
					if (!is_array($arFields[$strEntityID]))
						$arFields[$strEntityID] = array($arFields[$strEntityID]);
					$arValid = array();
					foreach ($arFields[$strEntityID] as &$value)
					{
						$value = intval($value);
						if ($value > 0)
							$arValid[] = $value;
					}
					if (isset($value))
						unset($value);
					if (!empty($arValid))
					{
						$arValid = array_unique($arValid);
					}
					$arFields[$strEntityID] = $arValid;

					if (empty($arFields[$strEntityID]))
					{
						$arFields[$strEntityID] = array(-1);
					}
				}
				else
				{
					$arFields[$strEntityID] = array(-1);
				}
			}
			else
			{
				$arFields[$strEntityID] = array(-1);
			}
			$boolResult = true;
		}
		return $boolResult;
	}

	protected function __ArrayMultiple($arOrder, &$arResult, $arTuple, $arTemp = array())
	{
		if (empty($arTuple))
		{
			$arResult[] = array(
				'EQUAL' => array_combine($arOrder, $arTemp),
			);
		}
		else
		{
			$head = array_shift($arTuple);
			$arTemp[] = false;
			if (is_array($head))
			{
				if (empty($head))
				{
					$arTemp[count($arTemp)-1] = -1;
					self::__ArrayMultiple($arOrder, $arResult, $arTuple, $arTemp);
				}
				else
				{
					foreach ($head as &$value)
					{
						$arTemp[count($arTemp)-1] = $value;
						self::__ArrayMultiple($arOrder, $arResult, $arTuple, $arTemp);
					}
					if (isset($value))
						unset($value);
				}
			}
			else
			{
				$arTemp[count($arTemp)-1] = $head;
				self::__ArrayMultiple($arOrder, $arResult, $arTuple, $arTemp);
			}
		}
	}

	protected function __Unpack($arProduct, $strUnpack)
	{
		if (empty($strUnpack))
			return false;
		return eval('return '.$strUnpack.';');
	}

	protected function __ConvertOldConditions($strAction, &$arFields)
	{
		$strAction = ToUpper($strAction);
		if (!is_set($arFields, 'CONDITIONS'))
		{
			$arIBlockList = array();
			$arSectionList = array();
			$arElementList = array();
			$arConditions = array(
				'CLASS_ID' => 'CondGroup',
				'DATA' => array(
					'All' => 'AND',
					'True' => 'True',
				),
				'CHILDREN' => array(),
			);
			$intEntityCount = 0;

			$arIBlockList = self::__ConvertOldOneEntity($arFields, 'IBLOCK_IDS');
			if (!empty($arIBlockList))
			{
				$intEntityCount++;
			}

			$arSectionList = self::__ConvertOldOneEntity($arFields, 'SECTION_IDS');
			if (!empty($arSectionList))
			{
				$intEntityCount++;
			}

			$arElementList = self::__ConvertOldOneEntity($arFields, 'PRODUCT_IDS');
			if (!empty($arElementList))
			{
				$intEntityCount++;
			}

			if (0 < $intEntityCount)
			{
				self::__AddOldOneEntity($arConditions, 'CondIBIBlock', $arIBlockList, (1 == $intEntityCount));
				self::__AddOldOneEntity($arConditions, 'CondIBSection', $arSectionList, (1 == $intEntityCount));
				self::__AddOldOneEntity($arConditions, 'CondIBElement', $arElementList, (1 == $intEntityCount));
			}

			if ('ADD' == $strAction)
			{
				$arFields['CONDITIONS'] = $arConditions;
			}
			else
			{
				if (0 < $intEntityCount)
				{
					$arFields['CONDITIONS'] = $arConditions;
				}
			}
		}
	}

	protected function __ConvertOldOneEntity(&$arFields, $strEntityID)
	{
		$arResult = false;
		if (!empty($strEntityID))
		{
			$arResult = array();
			if (isset($arFields[$strEntityID]))
			{
				if (!is_array($arFields[$strEntityID]))
					$arFields[$strEntityID] = array($arFields[$strEntityID]);
				foreach ($arFields[$strEntityID] as &$value)
				{
					$value = intval($value);
					if ($value > 0)
						$arResult[] = $value;
				}
				if (isset($value))
					unset($value);
				if (!empty($arResult))
				{
					$arResult = array_values(array_unique($arResult));
				}
			}
		}
		return $arResult;
	}

	protected function __AddOldOneEntity(&$arConditions, $strCondID, $arEntityValues, $boolOneEntity)
	{
		if (!empty($strCondID))
		{
			$boolOneEntity = (true == $boolOneEntity ? true : false);
			if (!empty($arEntityValues))
			{
				if (1 < count($arEntityValues))
				{
					$arList = array();
					foreach ($arEntityValues as &$intItemID)
					{
						$arList[] = array(
							'CLASS_ID' => $strCondID,
							'DATA' => array(
								'logic' => 'Equal',
								'value' => $intItemID
							),
						);
					}
					if (isset($intItemID))
						unset($intItemID);
					if ($boolOneEntity)
					{
						$arConditions = array(
							'CLASS_ID' => 'CondGroup',
							'DATA' => array(
								'All' => 'OR',
								'True' => 'True',
							),
							'CHILDREN' => $arList,
						);
					}
					else
					{
						$arConditions['CHILDREN'][] = array(
							'CLASS_ID' => 'CondGroup',
							'DATA' => array(
								'All' => 'OR',
								'True' => 'True',
							),
							'CHILDREN' => $arList,
						);
					}
				}
				else
				{
					$arConditions['CHILDREN'][] = array(
						'CLASS_ID' => $strCondID,
						'DATA' => array(
							'logic' => 'Equal',
							'value' => current($arEntityValues)
						),
					);
				}
			}
		}
	}

	protected function __GetOldOneEntity(&$arFields, $strEntityID, $strCondID)
	{
		if (isset($arFields['CONDITIONS']) && !empty($arFields['CONDITIONS']))
		{
			$arConditions = false;
			if (!is_array($arFields['CONDITIONS']))
			{
				if (CheckSerializedData($arFields['CONDITIONS']))
				{
					$arConditions = unserialize($arFields['CONDITIONS']);
				}
			}
			else
			{
				$arConditions = $arFields['CONDITIONS'];
			}
			if (is_array($arConditions) && !empty($arConditions))
			{

			}
		}
	}

	protected function __UpdateOldOneEntity($intID, &$arFields, $arParams, $boolUpdate)
	{
		global $DB;

		$boolUpdate = (false === $boolUpdate ? false : true);
		$intID = intval($intID);
		if (0 >= $intID)
			return;
		if (is_array($arParams) && !empty($arParams))
		{
			if (!empty($arParams['ENTITY_ID']) && !empty($arParams['TABLE_ID']) && !empty($arParams['FIELD_ID']))
			{
				if (isset($arFields[$arParams['ENTITY_ID']]))
				{
					if ($boolUpdate)
					{
						$DB->Query("DELETE FROM ".$arParams['TABLE_ID']." WHERE DISCOUNT_ID = ".$intID, false, "File: ".__FILE__."<br>Line: ".__LINE__);
					}
					if (!empty($arFields[$arParams['ENTITY_ID']]))
					{
						foreach ($arFields[$arParams['ENTITY_ID']] as &$intValue)
						{
							$strSql = "INSERT INTO ".$arParams['TABLE_ID']."(DISCOUNT_ID, ".$arParams['FIELD_ID'].") VALUES(".$intID.", ".$intValue.")";
							$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
						}
						if (isset($intValue))
							unset($intValue);
					}
				}
			}
		}
	}
}
?>