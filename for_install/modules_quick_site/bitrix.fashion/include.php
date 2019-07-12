<?
IncludeModuleLangFile(__FILE__);
class CSiteFashionStore
{
	function FilterShow($arParams){
		CModule::IncludeModule('iblock');
		
		$ar_system_prop = array('item_color', 'item_size');
		$arResult['PRICE'] = array(0,0, 'ID'=>0);
		$arResult['DATA_FILTER'] = $arResult['DATA_OFFERS_FILTER'] = $arResult['DATA_SYS_FILTER'] = array();
		
		$arPrices = CIBlockPriceTools::GetCatalogPrices($arParams["IBLOCK_ID"], array($arParams['PRICE']));

		$arResult['PRICE']['ID'] = $arPrices[$arParams['PRICE']]['ID'];
			
		$arFilter = Array('IBLOCK_ID'=>$arParams["IBLOCK_ID"], 'GLOBAL_ACTIVE'=>'Y', 'CODE'=>$arParams['SECTION_CODE']);
		$db_list = CIBlockSection::GetList(array(), $arFilter, false, array('ID', 'DEPTH_LEVEL'));
		if(($ar_result = $db_list->GetNext())&&$ar_result['DEPTH_LEVEL']>=2){
			$arSelect = Array("ID", "NAME", "IBLOCK_ID");
			$arFilter = Array("IBLOCK_ID"=>$arParams["IBLOCK_ID"], "ACTIVE"=>"Y", "SECTION_ID"=>$ar_result['ID']);
			$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
			while($ob = $res->GetNextElement())
			{
				$arFields = $ob->GetFields();

				$arProps = $ob->GetProperties();
				foreach($arProps as $prop){
					if(substr($prop['CODE'], 0, 4)!='fil_') continue;

					if($prop['PROPERTY_TYPE']=='L'){
						if(strlen($prop['VALUE'])>0){
							if(!array_key_exists($prop['ID'], $arResult['DATA_FILTER']))
								$arResult['DATA_FILTER'][$prop['ID']] = array('NAME' => $prop['NAME'], 'VALUES' => array());

							if(!array_key_exists($prop['VALUE_ENUM_ID'], $arResult['DATA_FILTER'][$prop['ID']]['VALUES']))
								$arResult['DATA_FILTER'][$prop['ID']]['VALUES'][$prop['VALUE_ENUM_ID']] = $prop['VALUE'];
						}
					}
				}

				$arOffers = CIBlockPriceTools::GetOffersArray(
					$arParams["IBLOCK_ID"]
					,$arFields
					,array(
						"ID" => "DESC",
					)
					,array('ID')
					,array('*')
					,''
					,$arPrices
				);

				foreach($arOffers as $offer){
					if($arResult['PRICE'][0]>$offer['PRICES'][$arParams['PRICE']]['DISCOUNT_VALUE']||$arResult['PRICE'][0]==0) $arResult['PRICE'][0] = $offer['PRICES'][$arParams['PRICE']]['DISCOUNT_VALUE'];
					if($arResult['PRICE'][1]<$offer['PRICES'][$arParams['PRICE']]['DISCOUNT_VALUE']||$arResult['PRICE'][1]==0) $arResult['PRICE'][1] = $offer['PRICES'][$arParams['PRICE']]['DISCOUNT_VALUE'];

					foreach($offer['PROPERTIES'] as $prop){
						if(substr($prop['CODE'], 0, 4)!='fil_'&&!in_array($prop['CODE'], $ar_system_prop)) continue;

						if($prop['CODE']=='item_color'){
							if(!isset($arResult['DATA_SYS_FILTER'][$prop['CODE']]))
								$arResult['DATA_SYS_FILTER'][$prop['CODE']] = array('NAME' => $prop['NAME'], 'VALUES' => array());

							if(!array_key_exists($prop['VALUE'], $arResult['DATA_SYS_FILTER'][$prop['ID']]['VALUES'])){
								$arSelectColor = Array("ID", "NAME", "IBLOCK_ID", "PROPERTY_hex", 'DETAIL_PICTURE');
								$arFilterColor = Array("IBLOCK_ID"=>$arParams["IBLOCK_COLOR_ID"], "ACTIVE"=>"Y", "ID"=>$prop['VALUE']);
								$rsColor = CIBlockElement::GetList(Array(), $arFilterColor, false, Array("nTopCount"=>1), $arSelectColor);
								if($obColor = $rsColor->GetNextElement())
								{
									$arFieldsColor = $obColor->GetFields();
									$arTmp = array('PICTURE'=>'', 'CODE'=>'', 'NAME'=>$arFieldsColor['NAME']);
									if($arFieldsColor['DETAIL_PICTURE']){
										$arTmp['PICTURE'] = CFile::ResizeImageGet($arFieldsColor['DETAIL_PICTURE'], array('width'=>38, 'height'=>38), BX_RESIZE_IMAGE_PROPORTIONAL, true);
									}else{
										$arTmp['CODE'] = $arFieldsColor['PROPERTY_HEX_VALUE'];
									}

									$arResult['DATA_SYS_FILTER'][$prop['CODE']]['VALUES'][$prop['VALUE']] = $arTmp;
								}
							}
						}

						if($prop['CODE']=='item_size'){
							if(!isset($arResult['DATA_SYS_FILTER'][$prop['CODE']]))
								$arResult['DATA_SYS_FILTER'][$prop['CODE']] = array('NAME' => $prop['NAME'], 'VALUES' => array());

							if(!array_key_exists($prop['VALUE'], $arResult['DATA_SYS_FILTER'][$prop['ID']]['VALUES'])){
								$arSelectSize = Array("ID", "NAME");
								$arFilterSize = Array("IBLOCK_ID"=>$arParams["IBLOCK_SIZE_ID"], "ACTIVE"=>"Y", "ID"=>$prop['VALUE']);
								$rsSize = CIBlockElement::GetList(Array(), $arFilterSize, false, Array("nTopCount"=>1), $arSelectSize);
								if($obSize = $rsSize->GetNextElement())
								{
									$arFieldsSize = $obSize->GetFields();
									$arResult['DATA_SYS_FILTER'][$prop['CODE']]['VALUES'][$prop['VALUE']] = $arFieldsSize['NAME'];
								}
							}
						}

						if(!in_array($prop['CODE'], $ar_system_prop)){
							if($prop['PROPERTY_TYPE']=='L'){
								if(strlen($prop['VALUE'])>0){
									if(!array_key_exists($prop['ID'], $arResult['DATA_OFFERS_FILTER']))
										$arResult['DATA_OFFERS_FILTER'][$prop['ID']] = array('NAME' => $prop['NAME'], 'VALUES' => array());

									if(!array_key_exists($prop['VALUE_ENUM_ID'], $arResult['DATA_OFFERS_FILTER'][$prop['ID']]['VALUES']))
										$arResult['DATA_OFFERS_FILTER'][$prop['ID']]['VALUES'][$prop['VALUE_ENUM_ID']] = $prop['VALUE'];
								}
							}
						}
					}
				}
			}
		}
		
		return $arResult;
	}
	
	function dvsSetCurFilter($t){
	   global $dvs_cur_filter;
	   $dvs_cur_filter = $t;
	}
	
	function dvsShowCurFilter(){
		global $APPLICATION;
		echo $APPLICATION->AddBufferContent("dvsGetCurFilter");
	}
	
	function SectionResModifier ($items){
		$arOut = array();
		
		$dbPriceType = CCatalogGroup::GetList(
			array("SORT" => "ASC"),
			array("BASE" => "Y")
		);

		if ($arPriceType = $dbPriceType->Fetch()) {
			$basePriceType = $arPriceType["NAME"];
			$arOut["BASE_PRICES"] = $basePriceType;
		}

		//foreach ($arResult["ITEMS"] as $key => $arElement) {
		foreach ($items as $key => $arElement) {
			foreach ($arElement["OFFERS"] as &$arOffer) {
				$res = CIBlockElement::GetByID($arOffer["PROPERTIES"]["item_size"]["VALUE"]);
				if ($arRes = $res->GetNext()) {
					$arOffer["PROPERTIES"]["item_size"]["VALUE"] = $arRes["NAME"];
					$arOffer["PROPERTIES"]["item_size"]["SORT_VALUE"] = $arRes["SORT"];
				}
				
				$res = CIBlockElement::GetList(array('sort'=>'asc'), array("IBLOCK_ID"=>intval($arOffer["PROPERTIES"]["item_color"]["LINK_IBLOCK_ID"]), "ACTIVE"=>"Y", "ID"=>intval($arOffer["PROPERTIES"]["item_color"]["VALUE"])), false, Array("nPageSize"=>1), array("ID", "NAME", "PROPERTY_hex", "DETAIL_PICTURE", "CODE"));
				if ($ob = $res->GetNextElement()) {
					$arFields = $ob->GetFields();
					
					$arOffer["PROPERTIES"]["item_color"]["VALUE"] = $arFields;
				}
			}
		}
		$arOut['ITEMS'] = $items;
		
		return $arOut;
	}
	
	function ElementResModifier ($offers){
		/*
		BASE_PRICES
		DEFAULT_COLOR
		OFFERS_COMPACT
		*/	
	
		$arOut = array();
		
		$dbPriceType = CCatalogGroup::GetList(
			array("SORT" => "ASC"),
			array("BASE" => "Y")
		);

		if ($arPriceType = $dbPriceType->Fetch()) {
			$basePriceType = $arPriceType["NAME"];
			$arOut["BASE_PRICES"] = $basePriceType;
		}
		
		// --- --- ---
		
		$tempArr = array();
		$isDefaultColor = true;

		$arOut["DEFAULT_COLOR"] = "";

		foreach ($offers as &$arOffer) {
			$res = CIBlockElement::GetList(array('sort'=>'asc'), array("IBLOCK_ID"=>intval($arOffer["PROPERTIES"]["item_color"]["LINK_IBLOCK_ID"]), "ACTIVE"=>"Y", "ID"=>intval($arOffer["PROPERTIES"]["item_color"]["VALUE"])), false, Array("nPageSize"=>1), array("ID", "NAME", "PROPERTY_hex", "DETAIL_PICTURE", "CODE"));
			if ($ob = $res->GetNextElement()) {
				$arFields = $ob->GetFields();
				
				$arOffer["PROPERTIES"]["item_color"]["VALUE"] = $arFields;
				
				if ($isDefaultColor) {
					$arOut["DEFAULT_COLOR"] = $arFields["CODE"];
					$isDefaultColor = false;
				}
			}
			//articles
			$tempArr[$arFields["CODE"]]["ARTICLE"] = $arOffer["PROPERTIES"]["item_article"]["VALUE"];
			//colors
			$tempArr[$arFields["CODE"]]["COLORS"]  = array("ID" => $arFields["ID"], "COLOR" => $arFields["NAME"], "HEX" => $arFields["PROPERTY_HEX_VALUE"], "PICTURE" =>$arFields["DETAIL_PICTURE"]);
			
			$res = CIBlockElement::GetByID($arOffer["PROPERTIES"]["item_size"]["VALUE"]);
			if ($arRes = $res->GetNext()) {
				$arOffer["PROPERTIES"]["item_size"]["VALUE"] = $arRes["NAME"];
				$arOffer["PROPERTIES"]["item_size"]["SORT_VALUE"] = $arRes["SORT"];
			}
			//sizes & prices
			$tempArr[$arFields["CODE"]]["SIZES"][$arRes["SORT"]] = array(
				"PRODUCT_ID" => $arOffer["ID"],
				"SIZE" => $arRes["NAME"],
				"PRICE" => $arOffer["PRICES"][$arOut["BASE_PRICES"]]["VALUE"],
				"PRINT_PRICE" => $arOffer["PRICES"][$arOut["BASE_PRICES"]]["PRINT_VALUE"],
				"DISCOUNT" => $arOffer["PRICES"][$arOut["BASE_PRICES"]]["DISCOUNT_VALUE"],
				"PRINT_DISCOUNT" => $arOffer["PRICES"][$arOut["BASE_PRICES"]]["PRINT_DISCOUNT_VALUE"]
			);
			//photos
			$tempArr[$arFields["CODE"]]["PHOTOS"] = $arOffer["DISPLAY_PROPERTIES"]["item_more_photo"]["FILE_VALUE"];
			if (count($arOffer["DISPLAY_PROPERTIES"]["item_more_photo"]["VALUE"]) == 1) {
				$tempArr[$arFields["CODE"]]["PHOTOS"] = array();
				$tempArr[$arFields["CODE"]]["PHOTOS"][] = $arOffer["DISPLAY_PROPERTIES"]["item_more_photo"]["FILE_VALUE"];
			}
		}

		foreach ($tempArr as &$arr) {
			ksort($arr["SIZES"]);
			$arr["SIZES"] = array_values($arr["SIZES"]);
		}

		$arOut["OFFERS_COMPACT"] = $tempArr;
		
		return $arOut;
	}
	
	function ElementResModifierMore($IBLOCK_ID, $arSimProp, $arViewProp, $viewedProductId, $SET_ID = null, $OFFERS_ID = null, $currentColor = null, $defaultColor = null, $offers = null, $offerCompact = null){
		$arOut = array('SIMILAR_PRODUCTS'=>array(), 'VIEWED_PRODUCTS'=>array(), 'VIEW_PRODUCTS'=>array());
		
		//similar products
		$res = CIBlockElement::GetList(array(), array("IBLOCK_ID"=>intval($arSimProp["LINK_IBLOCK_ID"]), "ACTIVE"=>"Y", "ID"=>$arSimProp["VALUE"]), false, false, array("ID", "NAME", "DETAIL_PAGE_URL"));
		while ($arFields = $res->GetNext()) {
			$arOffers = CIBlockPriceTools::GetOffersArray(
			   intval($arSimProp["LINK_IBLOCK_ID"])
			   ,array($arFields["ID"])
			   ,array("sort" => "asc")
			   ,array($arFields["NAME"])
			   ,array("item_more_photo")
			   ,array()
			   ,$arResultPrices
			   ,array()
			);
			
			$arOffers[0]["NAME"] = $arFields["NAME"];
			$arOffers[0]["DETAIL_PAGE_URL"] = $arFields["DETAIL_PAGE_URL"];
			
			$arOut["SIMILAR_PRODUCTS"][] = $arOffers[0];
		}

		if(!empty($arViewProp['VALUE'])){
			foreach($arViewProp['VALUE'] as $key => $id){
				$arIDs[$id] = $arViewProp['DESCRIPTION'][$key];
			}

			arsort($arIDs);
			$arIDs = array_slice($arIDs, 0, 6, true);
			//viewed
			$res = CIBlockElement::GetList(array(), array("IBLOCK_ID"=>$IBLOCK_ID, "ACTIVE"=>"Y", "ID"=>array_keys($arIDs)), false, false, array("ID", "NAME", "DETAIL_PAGE_URL"));
			while ($arFields = $res->GetNext()) {
				$arOffers = CIBlockPriceTools::GetOffersArray(
				   intval($IBLOCK_ID)
				   ,array($arFields["ID"])
				   ,array("sort" => "asc")
				   ,array($arFields["NAME"])
				   ,array("item_more_photo")
				   ,array()
				   ,$arResultPrices
				   ,array()
				);

				$arOffers[0]["NAME"] = $arFields["NAME"];
				$arOffers[0]["DETAIL_PAGE_URL"] = $arFields["DETAIL_PAGE_URL"];

				$arOut["VIEWED_PRODUCTS"][] = array_merge($arOffers[0], array('index'=>$arIDs[$arFields["ID"]]));
			}

			$arOut["VIEWED_PRODUCTS"] = CSiteFashionStore::array_sort_DVS($arOut["VIEWED_PRODUCTS"], 'index', SORT_DESC);
		}
		
		if(!empty($viewedProductId))
		{
			global $APPLICATION;
			$products = $APPLICATION->get_cookie("VIEW_PRODUCTS");
			$products = unserialize($products);
			if(empty($products)) {
				$products = array();
			}
			if (is_array($products) && !in_array($viewedProductId, $products)) {
				$products[] = $viewedProductId;
				$APPLICATION->set_cookie("VIEW_PRODUCTS", serialize($products), time()+60*60*24*30);
			}
			foreach($products as $key => $value)
			{
				if($value == $viewedProductId)
				{
					unset($products[$key]);
				}
			}
            shuffle($products);
            $products = array_slice($products, 0, 10);
			
			if(!empty($products)){
				$res = CIBlockElement::GetList(array(), array("IBLOCK_ID"=>$IBLOCK_ID, "ACTIVE"=>"Y", "ID"=>$products), false, array("nTopCount" => 10), array("ID", "NAME", "DETAIL_PAGE_URL"));
				while ($arFields = $res->GetNext()) {
					$arOffers = CIBlockPriceTools::GetOffersArray(
					   intval($IBLOCK_ID)
					   ,array($arFields["ID"])
					   ,array("sort" => "asc")
					   ,array($arFields["NAME"])
					   ,array("item_more_photo")
					   ,array()
					   ,$arResultPrices
					   ,array()
					);

					$arOffers[0]["NAME"] = $arFields["NAME"];
					$arOffers[0]["DETAIL_PAGE_URL"] = $arFields["DETAIL_PAGE_URL"];

					$arOut["VIEW_PRODUCTS"][] = $arOffers[0];
				}
			}
		}
		
        $arOut["SET"] = array();
        if(!empty($currentColor))
        {
            $offerId = $offerCompact[$currentColor]["SIZES"][0]["PRODUCT_ID"];
        }
        else
        {
            $offerId = $offerCompact[$defaultColor]["SIZES"][0]["PRODUCT_ID"];
        }

        $elems = CIBlockElement::GetList(array(), array("IBLOCK_ID" => $SET_ID, "ACTIVE" => "Y", "PROPERTY_products" => $offerId), false, false);
        while($elem = $elems->GetNextElement())
        {
            $fields = $elem->GetFields();
            $properties = $elem->GetProperties();
            $products = array();
            foreach($properties["products"]["VALUE"] as $productId)
            {
                $product = CIBlockElement::GetByID($productId);
                $product = $product->GetNextElement();
                $productFields = $product->GetFields();
                $productProperties = $product->GetProperties();
                $image = CFile::ResizeImageGet($productProperties["item_more_photo"]["VALUE"][0], array("width" => 80, "height" => 80), BX_RESIZE_IMAGE_PROPORTIONAL, false);
                $productFields["IMAGE"] = $image["src"];
                $e = CIBlockElement::GetByID($productProperties["model"]["VALUE"]);
                $e = $e->GetNext();
                $c = CIBlockElement::GetByID($productProperties["item_color"]["VALUE"]);
                $c = $c->GetNext();
                $productFields["DETAIL_PAGE_URL"] = $e["DETAIL_PAGE_URL"].$c["CODE"]."/";
                $size = CIBlockElement::GetByID($productProperties["item_size"]["VALUE"]);
                $size = $size->GetNext();
                $productFields["SIZE_NAME"] = $size["NAME"];
                $color = CIBlockElement::GetByID($productProperties["item_color"]["VALUE"]);
                $color = $color->GetNextElement();
                $colorProps = $color->GetProperties();
                $colorFields = $color->GetFields();
                $img = CFile::GetPath($colorFields["DETAIL_PICTURE"]);
                if(!$img)
                {
                    $productFields["BACKGROUND"] = "#".$colorProps["hex"]["VALUE"];
                }
                else
                {
                    $productFields["BACKGROUND"] = "url(".CFile::GetPath($colorFields["DETAIL_PICTURE"]).") no-repeat";
                }
                $products[] = array("FIELDS" => $productFields, "PROPERTIES" => $productProperties);
            }
            $arOut["SET"][] = array("PRODUCTS" => $products, "OLD_PRICE" => $properties["old_price"]["VALUE"], "NEW_PRICE" => $properties["new_price"]["VALUE"], "ID" => $fields["ID"], "NAME" => $fields["NAME"]);
        }

        foreach($offers as $offer)
        {
            $offerId = $offer["ID"];
            $elems = CIBlockElement::GetList(array(), array("IBLOCK_ID" => $SET_ID, "ACTIVE" => "Y", "PROPERTY_products" => $offerId), false, false);
            while($elem = $elems->GetNextElement())
            {
                $fields = $elem->GetFields();
                $properties = $elem->GetProperties();
                $products = array();
                foreach($properties["products"]["VALUE"] as $productId)
                {
                    $product = CIBlockElement::GetByID($productId);
                    $product = $product->GetNextElement();
                    $productFields = $product->GetFields();
                    $productProperties = $product->GetProperties();
                    $image = CFile::ResizeImageGet($productProperties["item_more_photo"]["VALUE"][0], array("width" => 80, "height" => 80), BX_RESIZE_IMAGE_PROPORTIONAL, false);
                    $productFields["IMAGE"] = $image["src"];
                    $e = CIBlockElement::GetByID($productProperties["model"]["VALUE"]);
                    $e = $e->GetNext();
                    $c = CIBlockElement::GetByID($productProperties["item_color"]["VALUE"]);
                    $c = $c->GetNext();
                    $productFields["DETAIL_PAGE_URL"] = $e["DETAIL_PAGE_URL"].$c["CODE"]."/";
                    $size = CIBlockElement::GetByID($productProperties["item_size"]["VALUE"]);
                    $size = $size->GetNext();
                    $productFields["SIZE_NAME"] = $size["NAME"];
                    $color = CIBlockElement::GetByID($productProperties["item_color"]["VALUE"]);
                    $color = $color->GetNextElement();
                    $colorProps = $color->GetProperties();
                    $colorFields = $color->GetFields();
                    $img = CFile::GetPath($colorFields["DETAIL_PICTURE"]);
                    if(!$img)
                    {
                        $productFields["BACKGROUND"] = "#".$colorProps["hex"]["VALUE"];
                    }
                    else
                    {
                        $productFields["BACKGROUND"] = "url(".CFile::GetPath($colorFields["DETAIL_PICTURE"]).") no-repeat";
                    }
                    $products[] = array("FIELDS" => $productFields, "PROPERTIES" => $productProperties);
                }
                $arOut["SETS"][$offerId][] = array("PRODUCTS" => $products, "OLD_PRICE" => $properties["old_price"]["VALUE"], "NEW_PRICE" => $properties["new_price"]["VALUE"], "NAME" => $fields["NAME"]);
            }
        }
		return $arOut;
	}
	
	function formatMoney($number, $cents = 1) {
	    if (is_numeric($number)) {
	        if (!$number) {
	            $money = ($cents == 2 ? '0.00' : '0');
	        } else {
	            if (floor($number) == $number) {
	                $money = number_format($number, ($cents == 2 ? 2 : 0), '.', ' ');
	            } else {
	                $money = number_format(round($number, 2), ($cents == 0 ? 0 : 2), '.', ' ');
	            }
	        }

	        return $money;
	    }
	}

	function declOfNum($number, $titles){
	    $cases = array (2, 0, 1, 1, 1, 2);
	    return $number . " " . $titles[ ($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)] ];
	}

	function dvsGetCurFilter(){
		global $APPLICATION;
		global $dvs_cur_filter;
		global $dvs_clear_text;

		$out = '';
		if(!empty($dvs_cur_filter)){
			$out = '<div class="filters"><ul>';
			foreach($dvs_cur_filter as $id => $ar){
				$out .= '<li><a title="'.$ar['NAME'].'" alt="'.$ar['NAME'].'" href="'.$APPLICATION->GetCurPageParam('', array($id)).'"><span>'.$ar['VALUE'].'</span></a></li>';
			}
			$out .= '<li class="reset-all"><a href="'.$APPLICATION->GetCurPageParam('', array_keys($dvs_cur_filter)).'"><span>'.$dvs_clear_text.'</span></li></a>';

			$out .= '</ul></div>';
		}

		return $out;
	}

	function array_sort_DVS($array, $on, $order=SORT_ASC)
	{
	    $new_array = array();
	    $sortable_array = array();

	    if (count($array) > 0) {
	        foreach ($array as $k => $v) {
	            if (is_array($v)) {
	                foreach ($v as $k2 => $v2) {
	                    if ($k2 == $on) {
	                        $sortable_array[$k] = $v2;
	                    }
	                }
	            } else {
	                $sortable_array[$k] = $v;
	            }
	        }

	        switch ($order) {
	            case SORT_ASC:
	                asort($sortable_array);
	            break;
	            case SORT_DESC:
	                arsort($sortable_array);
	            break;
	        }

	        foreach ($sortable_array as $k => $v) {
	            $new_array[$k] = $array[$k];
	        }
	    }

	    return $new_array;
	}
	
	function DVSAdd2BasketByProductID($PRODUCT_ID, $QUANTITY = 1, $arProductParams = array())
	{
		global $APPLICATION;

		$PRODUCT_ID = IntVal($PRODUCT_ID);
		if ($PRODUCT_ID <= 0)
		{
			//$GLOBALS["APPLICATION"]->ThrowException("Empty product field", "EMPTY_PRODUCT_ID");
			$APPLICATION->ThrowException(GetMessage('CATALOG_ERR_EMPTY_PRODUCT_ID'), "EMPTY_PRODUCT_ID");
			return false;
		}

		$QUANTITY = DoubleVal($QUANTITY);
		if ($QUANTITY <= 0)
			$QUANTITY = 1;

		if (!CModule::IncludeModule("sale"))
		{
			//$GLOBALS["APPLICATION"]->ThrowException("Sale module is not installed", "NO_SALE_MODULE");
			$APPLICATION->ThrowException(GetMessage('CATALOG_ERR_NO_SALE_MODULE'), "NO_SALE_MODULE");
			return false;
		}

		if (CModule::IncludeModule("statistic") && IntVal($_SESSION["SESS_SEARCHER_ID"])>0)
		{
			//$GLOBALS["APPLICATION"]->ThrowException("Searcher can not buy anything", "SESS_SEARCHER");
			$APPLICATION->ThrowException(GetMessage('CATALOG_ERR_SESS_SEARCHER'), "SESS_SEARCHER");
			return false;
		}

		$arProduct = CCatalogProduct::GetByID($PRODUCT_ID);
		if ($arProduct === false)
		{
			//$GLOBALS["APPLICATION"]->ThrowException("Product is not found", "NO_PRODUCT");
			$APPLICATION->ThrowException(GetMessage('CATALOG_ERR_NO_PRODUCT'), "NO_PRODUCT");
			return false;
		}

		if ($arProduct["QUANTITY_TRACE"]=="Y" && DoubleVal($arProduct["QUANTITY"])<=0)
		{
			//$GLOBALS["APPLICATION"]->ThrowException("Product is run out", "PRODUCT_RUN_OUT");
			$APPLICATION->ThrowException(GetMessage('CATALOG_ERR_PRODUCT_RUN_OUT'), "PRODUCT_RUN_OUT");
			return false;
		}

		$CALLBACK_FUNC = "CatalogBasketCallback";
		$arCallbackPrice = CSaleBasket::ReReadPrice($CALLBACK_FUNC, "catalog", $PRODUCT_ID, $QUANTITY);
		//if (!is_array($arCallbackPrice) || count($arCallbackPrice) <= 0)
		if (!is_array($arCallbackPrice) || empty($arCallbackPrice))
		{
			//$GLOBALS["APPLICATION"]->ThrowException("Product price is not found", "NO_PRODUCT_PRICE");
			$APPLICATION->ThrowException(GetMessage('CATALOG_PRODUCT_PRICE_NOT_FOUND'), "NO_PRODUCT_PRICE");
			return false;
		}

	//	$arIBlockElement = GetIBlockElement($PRODUCT_ID);
		$dbIBlockElement = CIBlockElement::GetList(array(), array(
						"ID" => $PRODUCT_ID,
						"ACTIVE_DATE" => "Y",
						"ACTIVE" => "Y",
						"CHECK_PERMISSIONS" => "Y",
					), false, false, array(
						"ID",
						"IBLOCK_ID",
						"XML_ID",
						"NAME",
						"DETAIL_PAGE_URL",
						"PROPERTY_model"
		));
		$arIBlockElement = $dbIBlockElement->GetNext();
		
		if(intval($arIBlockElement['PROPERTY_MODEL_VALUE'])>0){
			$rsLink = CIBlockElement::GetByID($arIBlockElement['PROPERTY_MODEL_VALUE']);
			if($arLink = $rsLink->GetNext())
				$arIBlockElement['DETAIL_PAGE_URL'] = $arLink['DETAIL_PAGE_URL'];
		}

		if ($arIBlockElement == false)
		{
			//$GLOBALS["APPLICATION"]->ThrowException("Infoblock element is not found", "NO_IBLOCK_ELEMENT");
			$APPLICATION->ThrowException(GetMessage('CATALOG_ERR_NO_IBLOCK_ELEMENT'), "NO_IBLOCK_ELEMENT");
			return false;
		}

		$arProps = array();

		$dbIBlock = CIBlock::GetList(
				array(),
				array("ID" => $arIBlockElement["IBLOCK_ID"])
			);
		if ($arIBlock = $dbIBlock->Fetch())
		{
			$arProps[] = array(
					"NAME" => "Catalog XML_ID",
					"CODE" => "CATALOG.XML_ID",
					"VALUE" => $arIBlock["XML_ID"]
				);
	/*		$arCatalog = CCatalog::GetByID($arIBlock['ID']);
			if ((true == is_array($arCatalog)) && (0 < intval($arCatalog['PRODUCT_IBLOCK_ID'])) && (0 < intval($arCatalog['SKU_PROPERTY_ID'])))
			{
				$rsSKUProps = CIBlockElement::GetProperty($arIBlock['ID'],$arIBlockElement['ID'],array(),array('ID' => $arCatalog['SKU_PROPERTY_ID']));
				if ($arSKUProp = $rsSKUProps->Fetch())
				{
					if (0 < intval($arSKUProp['VALUE']))
					{
						$dbIBlockParent = CIBlockElement::GetList(
							array(),
							array(
								"IBLOCK_ID" => $arCatalog['PRODUCT_IBLOCK_ID'],
								"ID" => $arSKUProp['VALUE'],
								"ACTIVE_DATE" => "Y",
								"ACTIVE" => "Y",
								"CHECK_PERMISSIONS" => "Y",
							),
							false,
							false,
							array(
								'ID', 'IBLOCK_ID', 'DETAIL_PAGE_URL', 'NAME',
							)
						);
						if ($arParent = $dbIBlockParent->GetNext())
						{
							$arIBlockElement["DETAIL_PAGE_URL"] = $arParent['DETAIL_PAGE_URL'];
						}
					}
				}
			} */
		}

		$arProps[] = array(
				"NAME" => "Product XML_ID",
				"CODE" => "PRODUCT.XML_ID",
				"VALUE" => $arIBlockElement["XML_ID"]
			);

		$arPrice = CPrice::GetByID($arCallbackPrice["PRODUCT_PRICE_ID"]);

		$arFields = array(
				"PRODUCT_ID" => $PRODUCT_ID,
				"PRODUCT_PRICE_ID" => $arCallbackPrice["PRODUCT_PRICE_ID"],
				"PRICE" => $arCallbackPrice["PRICE"],
				"CURRENCY" => $arCallbackPrice["CURRENCY"],
				"WEIGHT" => $arProduct["WEIGHT"],
				"QUANTITY" => $QUANTITY,
				"LID" => SITE_ID,
				"DELAY" => "N",
				"CAN_BUY" => "Y",
				"NAME" => $arIBlockElement["~NAME"],
				"CALLBACK_FUNC" => $CALLBACK_FUNC,
				"MODULE" => "catalog",
				//"NOTES" => $arProduct["CATALOG_GROUP_NAME"],
				"NOTES" => $arPrice["CATALOG_GROUP_NAME"],
				"ORDER_CALLBACK_FUNC" => "CatalogBasketOrderCallback",
				"CANCEL_CALLBACK_FUNC" => "CatalogBasketCancelCallback",
				"PAY_CALLBACK_FUNC" => "CatalogPayOrderCallback",
				"DETAIL_PAGE_URL" => $arIBlockElement["DETAIL_PAGE_URL"],
				"CATALOG_XML_ID" => $arIBlock["XML_ID"],
				"PRODUCT_XML_ID" => $arIBlockElement["XML_ID"],
				"VAT_RATE" => $arCallbackPrice['VAT_RATE'],
			);

		if ($arProduct["QUANTITY_TRACE"]=="Y")
		{
			if (IntVal($arProduct["QUANTITY"])-$QUANTITY < 0)
				$arFields["QUANTITY"] = DoubleVal($arProduct["QUANTITY"]);
		}

		//if (is_array($arProductParams) && count($arProductParams) > 0)
		if (is_array($arProductParams) && !empty($arProductParams))
		{
	/*		for ($i = 0; $i < count($arProductParams); $i++)
			{
				$arProps[] = array(
						"NAME" => $arProductParams[$i]["NAME"],
						"CODE" => $arProductParams[$i]["CODE"],
						"VALUE" => $arProductParams[$i]["VALUE"],
						"SORT" => $arProductParams[$i]["SORT"]
					);
			} */
			foreach ($arProductParams as &$arOneProductParams)
			{
				$arProps[] = array(
						"NAME" => $arOneProductParams["NAME"],
						"CODE" => $arOneProductParams["CODE"],
						"VALUE" => $arOneProductParams["VALUE"],
						"SORT" => $arOneProductParams["SORT"]
					);
			}
		}
		$arFields["PROPS"] = $arProps;

		$addres = CSaleBasket::Add($arFields);
		if ($addres)
		{
			if (CModule::IncludeModule("statistic"))
				CStatistic::Set_Event("sale2basket", "catalog", $arFields["DETAIL_PAGE_URL"]);
		}

		return $addres;
	}
}
?>
