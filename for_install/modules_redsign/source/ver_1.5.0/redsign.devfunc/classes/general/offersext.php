<?
/************************************
*
* Universal Extension Offers data
* last update 15.07.2014
*
************************************/

IncludeModuleLangFile(__FILE__);

class RSDevFuncOffersExtension
{
	protected static $highLoadInclude = null;
	
	public static function __callStatic($name, $arg)
	{
		if($name == 'GetAllPictures'){
			if(is_array($arg[0]) && is_array($arg[1]) && is_array($arg[2]))
			{
				return self::getPictures($arg[0], $arg[1], $arg[2]);
			} elseif(is_array($arg[0]) && is_array($arg[1]) && is_string($arg[2]) && is_string($arg[3]) && is_string($arg[4])) {
				return self::getPictures($arg[0], $arg[1], array('MORE_PHOTO_CODE' => $arg[2], 'SKU_MORE_PHOTO_CODE' => $arg[3], 'FIRST_PIC_FROM_FIRST_SKU' => $arg[4]));
			}
		}
	}
	// get jsonData for SKU
	public static function GetJSONElement($arElement,$arProps=array(),$arPrices=array(),$params=array())
	{
		$arrReturn = array();
		$arElementsIDs = array($arElement['ID']);
		$ELEMENT_ID = $arElement['ID'];
		$arrElement = array();
		
		$defaultParams = array(
			'SIZES' => array('WIDTH'=>'200','HEIGHT'=>'150'),
			'MORE_PHOTO_CODE' => 'MORE_PHOTO',
			'SKU_MORE_PHOTO_CODE' => 'MORE_PHOTO',
			'SKU_ARTICLE_CODE' => 'CML2_ARTICLE',
		);
		foreach($defaultParams as $key => $value)
            if(!array_key_exists($key, $params))
                $params[$key] = $value;
		
		$arOffers = $arElement['OFFERS'];
		
		if(is_array($arOffers) && count($arOffers)>0)
		{
			$arSizes = $params['SIZES'];
			$arrNewOffers = array();
			$arrNewOffer = array();
			$arrSortProps = array();
			foreach($arOffers as $key1 => $arOffer)
			{
				$arElementsIDs[] = $arOffer['ID'];
				// offer
				$arrNewOffer = array(
					'ID' => $arOffer['ID'],
					'NAME' => $arOffer['NAME'],
					'IMAGES' => self::getPictures($arSizes,$arOffer,array('MORE_PHOTO_CODE'=>$params['SKU_MORE_PHOTO_CODE'])),
					'PROPERTIES' => '',
					'PRICES' => '',
					'CAN_BUY' => $arOffer['CAN_BUY'],
					'ADD_URL' => $arOffer['ADD_URL'],
					'CATALOG_MEASURE_RATIO' => $arOffer['CATALOG_MEASURE_RATIO'],
					'CATALOG_MEASURE_NAME' => $arOffer['CATALOG_MEASURE_NAME'],
				);
				// article
				if( $arOffer['PROPERTIES'][$params['SKU_ARTICLE_CODE']]['VALUE']!='' )
				{
					$arrNewOffer['ARTICLE'] = $arOffer['PROPERTIES'][$params['SKU_ARTICLE_CODE']]['VALUE'];
				}
				// properties
				foreach($arProps as $propCode)
				{
					if( $arOffer['DISPLAY_PROPERTIES'][$propCode]['DISPLAY_VALUE']!='' )
					{
						if(!in_array($propCode,$arrSortProps))
							$arrSortProps[] = $propCode;
						$arrNewOffer['PROPERTIES'][$propCode] = $arOffer['DISPLAY_PROPERTIES'][$propCode]['DISPLAY_VALUE'];
					}
				}
				// prices
				foreach($arPrices as $priceCode)
				{
					if(isset($arOffer['PRICES'][$priceCode]))
					{
						$arrNewOffer['PRICES'][$priceCode] = array(
							'VALUE' => $arOffer['PRICES'][$priceCode]['VALUE'],
							'PRINT_VALUE' => $arOffer['PRICES'][$priceCode]['PRINT_VALUE'],
							'DISCOUNT_VALUE' => $arOffer['PRICES'][$priceCode]['DISCOUNT_VALUE'],
							'PRINT_DISCOUNT_VALUE' => $arOffer['PRICES'][$priceCode]['PRINT_DISCOUNT_VALUE'],
							'DISCOUNT_DIFF' => $arOffer['PRICES'][$priceCode]['DISCOUNT_DIFF'],
							'PRINT_DISCOUNT' => $arOffer['PRICES'][$priceCode]['PRINT_DISCOUNT_DIFF'],
						);
					}
					// min price
					if(isset($arOffer['MIN_PRICE']))
					{
						$arrNewOffer['MIN_PRICE'] = $arOffer['MIN_PRICE'];
					}
				}
				$arrNewOffers[$arOffer['ID']] = $arrNewOffer;
				//RSDevFunc::GetDataForProductItem($arrNewOffers,$params);
			}
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			$iTime = ConvertTimeStamp(time(),'FULL');
			// add quickbuy
			if(CModule::IncludeModule('redsign.quickbuy'))
			{
				$arFilter = array(
					'DATE_FROM' => $iTime,
					'DATE_TO' => $iTime,
					'QUANTITY' => 0,
					'ELEMENT_ID' => $arElementsIDs,
				);
				$dbRes = CRSQUICKBUYElements::GetList( array('ID'=>'SORT'), $arFilter);
				while($arData = $dbRes->Fetch())
				{
					if($arData['ELEMENT_ID']==$ELEMENT_ID)
					{
						$arrElement['QUICKBUY'] = $arData;
						$arrElement['QUICKBUY']['TIMER'] = CRSQUICKBUYMain::GetTimeLimit($arData['DATE_TO']);
					} elseif( isset($arrNewOffers[$arData['ELEMENT_ID']]) )
					{
						$arrNewOffers[$arData['ELEMENT_ID']]['QUICKBUY'] = $arData;
						$arrNewOffers[$arData['ELEMENT_ID']]['QUICKBUY']['TIMER'] = CRSQUICKBUYMain::GetTimeLimit($arData['DATE_TO']);
					}
				}
			}
			// /add quickbuy
			// add da2
			if(CModule::IncludeModule('redsign.daysarticle2'))
			{
				$arFilter = array(
					'DATE_FROM' => $iTime,
					'DATE_TO' => $iTime,
					'QUANTITY' => 0,
					'ELEMENT_ID' => $arElementsIDs,
				);
				$dbRes = CRSDA2Elements::GetList(array('ID'=>'SORT'), $arFilter);
				while($arData = $dbRes->Fetch())
				{
					if($arData['ELEMENT_ID']==$ELEMENT_ID)
					{
						$arrElement['DAYSARTICLE2'] = $arData;
						$arrElement['DAYSARTICLE2']['DINAMICA_EX'] = CRSDA2Elements::GetDinamica($arData['DATE_TO']);
					} elseif( isset($arrNewOffers[$arData['ELEMENT_ID']]) )
					{
						$arrNewOffers[$arData['ELEMENT_ID']]['DAYSARTICLE2'] = $arData;
						$arrNewOffers[$arData['ELEMENT_ID']]['DAYSARTICLE2']['DINAMICA_EX'] = CRSDA2Elements::GetDinamica($arData['DATE_TO']);
					}
				}
			}
			// /add da2
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			$arrReturn = array(
				'ELEMENT' => $arrElement,
				'SORT_PROPS' => $arrSortProps,
				'OFFERS' => $arrNewOffers,
			);
		}
		
		return $arrReturn;
	}

	// get sorted properties
	public static function GetSortedProperties($arOffers,$arProps=array(),$params=array())
	{
		if(CModule::IncludeModule('iblock') && is_array($arOffers) && count($arOffers)>0 && is_array($arProps) && count($arProps)>0 && IntVal($arOffers[0]['IBLOCK_ID'])>0)
		{
			$separator = '__';
			$arrReturn = array();
			$arKeys = array();
			$arProperties = array();
			$arParamsProps = $arProps;
			$arProps = array();
			$arrPropData = array();
			$propRes = CIBlockProperty::GetList(array('SORT'=>'ASC','ID'=>'ASC'),array('IBLOCK_ID'=>$arOffers[0]['IBLOCK_ID'],'ACTIVE'=>'Y','MULTIPLE'=>'N'));
			while($propInfo = $propRes->Fetch())
			{
				if(in_array($propInfo['CODE'],$arParamsProps) && isset($arOffers[0]['DISPLAY_PROPERTIES'][$propInfo['CODE']]))
				{
					if(isset($arOffers[0]['DISPLAY_PROPERTIES'][$propInfo['CODE']]))
					{
						$arProps[] = $propInfo['CODE']; 
					}
					$arPropData = $propInfo;
					if($arPropData['PROPERTY_TYPE']=='S' && $arPropData['USER_TYPE']=='directory')
					{
						// highloadblock
						if (!isset($arPropData['USER_TYPE_SETTINGS']['TABLE_NAME']) || empty($arPropData['USER_TYPE_SETTINGS']['TABLE_NAME']))
							continue;
						if (null === self::$highLoadInclude)
							self::$highLoadInclude = \Bitrix\Main\Loader::includeModule('highloadblock');
						if (!self::$highLoadInclude)
							continue;
						$highBlock = \Bitrix\Highloadblock\HighloadBlockTable::getList(array('filter'=>array('TABLE_NAME'=>$arPropData['USER_TYPE_SETTINGS']['TABLE_NAME'])))->fetch();
						if (!isset($highBlock['ID']))
							continue;
						$entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($highBlock);
						$entityDataClass = $entity->getDataClass();
						$fieldsList = $entityDataClass::getMap();
						if(empty($fieldsList))
						{
							continue;
						} else {
							if(count($fieldsList)===1 && isset($fieldsList['ID']))
							{
								$fieldsList = $entityDataClass::getEntity()->getFields();
							}
						}
						$arPropData['USER_TYPE_SETTINGS']['FIELDS_MAP'] = $fieldsList;
						$arPropData['USER_TYPE_SETTINGS']['ENTITY'] = $entity;
						$arPropData['VALUES'] = self::GetSortedPropertiesValues($arPropData);
					} elseif($arPropData['PROPERTY_TYPE']=='L') {
						// list
						$arPropData['VALUES'] = self::GetSortedPropertiesValues($arPropData);
					}
					$arrPropData[$propInfo['CODE']] = $arPropData;
				}
			}
			// prepare properties
			$arrForFirst = array();
			foreach($arOffers as $key1 => $arOffer)
			{
				$compilKey = array();
				if(is_array($arOffer['DISPLAY_PROPERTIES']) && count($arOffer['DISPLAY_PROPERTIES'])>0)
				{
					foreach($arProps as $propCode)
					{
						$arPropData = $arrPropData[$propCode];
						$arProperty = $arOffer['DISPLAY_PROPERTIES'][$propCode];
						$arProperties[$propCode][$arProperty['DISPLAY_VALUE']] = array(
							'VALUE' => $arProperty['DISPLAY_VALUE'],
							'FIRST_OFFER' => ($arProperty['DISPLAY_VALUE']==$arOffers[0]['DISPLAY_PROPERTIES'][$propCode]['DISPLAY_VALUE'] ? 'Y' : 'N'),
							'DISABLED_FOR_FIRST' => 'Y',
						);
						if($arProperty['DISPLAY_VALUE']==$arOffers[0]['DISPLAY_PROPERTIES'][$propCode]['DISPLAY_VALUE'])
							$arrForFirst[$propCode] = $arProperty['DISPLAY_VALUE'];
						$compilKey[] = $arProperty['DISPLAY_VALUE'];
						$arPropsData[$propCode] = array(
							'ID' => $arProperty['ID'],
							'NAME' => $arProperty['NAME'],
							'CODE' => $arProperty['CODE'],
							'HINT' => $arProperty['HINT'],
						);
					}
					$compilKeyStr = implode($separator,$compilKey);
					$arKeys[$compilKeyStr] = array(
						'KEY' => $key1,
						'OFFER_ID' => $arOffer['ID'],
					);
				}
			}
			// take data for hl and lists
			foreach($arProps as $propCode)
			{
				if( ($arrPropData[$propCode]['PROPERTY_TYPE']=='S' && $arrPropData[$propCode]['USER_TYPE']=='directory') || $arrPropData[$propCode]['PROPERTY_TYPE']=='L' )
				{
					$arSKUPropVALUEs = array_keys($arProperties[$propCode]);
					$arProperties[$propCode] = array();
					foreach($arrPropData[$propCode]['VALUES'] as $arValue)
					{
						if(in_array($arValue['NAME'],$arSKUPropVALUEs))
						{
							$arProperties[$propCode][] = array(
								'VALUE' => $arValue['NAME'],
								'FIRST_OFFER' => ($arValue['NAME']==$arOffers[0]['DISPLAY_PROPERTIES'][$propCode]['DISPLAY_VALUE'] ? 'Y' : 'N'),
								'DISABLED_FOR_FIRST' => 'Y',
								'PICT' => $arValue['PICT'],
							);
						}
					}
				}
			}
			// sort properties
			if(is_array($params['PROP_FOR_SORT']) && count($params['PROP_FOR_SORT'])>0)
			{
				foreach($arProperties as $code => $arProperty)
				{
					if(in_array($code,$params['PROP_FOR_SORT']))
					{
						ksort($arProperty);
						$arProperties[$code] = $arProperty;
					}
				}
			}
			// set enabled props for first offer
			$arrProps = array();
			$arrEnables = array();
			foreach($arProps as $key1 => $propCode)
			{
				if( $key1 != ( count($arProps)-1 ) )
				{
					$arrProps[] = $propCode;
					$next_code = $arProps[($key1+1)];
					foreach($arOffers as $key2 => $arOffer)
					{
						$all_prop_true2 = true;
						foreach($arrProps as $key3 => $propCode2)
						{
							if($arOffer['DISPLAY_PROPERTIES'][$propCode2]['DISPLAY_VALUE']!=$arrForFirst[$propCode2])
							{
								$all_prop_true2 = false;
								break;
							}
						}
						if($all_prop_true2)
						{
							$arrEnables[$next_code][] = $arOffer['DISPLAY_PROPERTIES'][$next_code]['DISPLAY_VALUE'];
						}
					}
				}
			}
			$index = 0;
			foreach($arProperties as $code => $arProperty)
			{
				if($index==0)
				{
					foreach($arProperty as $key1 => $arProp)
						$arProperties[$code][$key1]['DISABLED_FOR_FIRST'] = 'N';
				} else {
					if(is_array($arrEnables))
					{
						foreach($arProperty as $key1 => $arProp)
						{
							if( in_array($arProp['VALUE'],$arrEnables[$code]) )
							{
								$arProperties[$code][$key1]['DISABLED_FOR_FIRST'] = 'N';
							}
						}
					}
				}
				$index++;
			}

			$arrReturn = array(
				'PROPS' => $arPropsData,
				'PROPERTIES' => $arProperties,
				'KEYS' => $arKeys,
			);
		}
		return $arrReturn;
	}
	
	// get values for list and directory
	public static function GetSortedPropertiesValues($arProp)
	{
		$arrReturn = array();
		$values = array();
		$valuesExist = false;
		static $PROP_CACHE = array();
		if(isset($PROP_CACHE[$arProp['ID']]))
		{
			// get from cache
			$arrReturn = $PROP_CACHE[$arProp['ID']];
		} else {
			// get from db
			if($arProp['PROPERTY_TYPE']=='S' && $arProp['USER_TYPE']=='directory')
			{
				// highloadblock
				$pictMode = (isset($arProp['USER_TYPE_SETTINGS']['FIELDS_MAP']['UF_FILE']) ? true : false);
				if(null === self::$highLoadInclude)
					self::$highLoadInclude = \Bitrix\Main\Loader::includeModule('highloadblock');
				if(!self::$highLoadInclude)
					continue;
				$xmlMap = array();
				$sortExist = isset($arProp['USER_TYPE_SETTINGS']['FIELDS_MAP']['UF_SORT']);
				$directorySelect = array('ID', 'UF_NAME', 'UF_XML_ID');
				$directoryOrder = array();
				if($pictMode)
				{
					$directorySelect[] = 'UF_FILE';
				}
				if($sortExist)
				{
					$directorySelect[] = 'UF_SORT';
					$directoryOrder['UF_SORT'] = 'ASC';
				}
				$directoryOrder['UF_NAME'] = 'ASC';
				$sortValue = 100;
				$entityDataClass = $arProp['USER_TYPE_SETTINGS']['ENTITY']->getDataClass();
				$entityGetList = array(
					'select' => $directorySelect,
					'order' => $directoryOrder
				);
				$propEnums = $entityDataClass::getList($entityGetList);
				while($oneEnum = $propEnums->fetch())
				{
					$oneEnum['ID'] = IntVal($oneEnum['ID']);
					$oneEnum['UF_SORT'] = ($sortExist ? IntVal($oneEnum['UF_SORT']) : $sortValue);
					$sortValue += 100;
					if ($pictMode)
					{
						if(!empty($oneEnum['UF_FILE']))
						{
							$arFile = CFile::GetFileArray($oneEnum['UF_FILE']);
							if(!empty($arFile))
							{
								$oneEnum['PICT'] = array(
									'FILE_ID' => $oneEnum['UF_FILE'],
									'SRC' => $arFile['SRC'],
									'WIDTH' => IntVal($arFile['WIDTH']),
									'HEIGHT' => IntVal($arFile['HEIGHT'])
								);
							}
						}
						if(empty($oneEnum['PICT']))
							$oneEnum['PICT'] = $arProp['DEFAULT_VALUES']['PICT'];
					}
					$values[$oneEnum['ID']] = array(
						'ID' => $oneEnum['ID'],
						'NAME' => $oneEnum['UF_NAME'],
						'SORT' => IntVal($oneEnum['UF_SORT']),
						'XML_ID' => $oneEnum['UF_XML_ID'],
						'PICT' => ($pictMode ? $oneEnum['PICT'] : false)
					);
					$valuesExist = true;
					$xmlMap[$oneEnum['UF_XML_ID']] = $oneEnum['ID'];
				}
			} elseif ($arProp['PROPERTY_TYPE']=='L') {
				// list
				$propEnums = CIBlockProperty::GetPropertyEnum($arProp['ID'],array('SORT'=>'ASC','VALUE'=>'ASC'));
				while($oneEnum = $propEnums->Fetch())
				{
					$oneEnum['ID'] = IntVal($oneEnum['ID']);
					$values[$oneEnum['ID']] = array(
						'ID' => $oneEnum['ID'],
						'NAME' => $oneEnum['VALUE'],
						'SORT' => IntVal($oneEnum['SORT']),
						'XML_ID' => $oneEnum['XML_ID'],
						'PICT' => false
					);
					$valuesExist = true;
				}
			}
			if($valuesExist)
			{
				$PROP_CACHE[$arProp['ID']] = $values;
				$arrReturn = $values;
			}
		}
		return $arrReturn;
	}
	
	// get full pictures list
	public static function getPictures($arSizes=array(),$arElement=array(),$params=array())
	{
		$defaultParams = array(
			'MORE_PHOTO_CODE' => 'MORE_PHOTO',
			'SKU_MORE_PHOTO_CODE' => 'MORE_PHOTO',
			'FIRST_PIC_FROM_FIRST_SKU' => 'N',
			'NO_SKU' => 'N',
			'PAGE' => 'section',
		);
		foreach($defaultParams as $key => $value)
		{
            if(!array_key_exists($key, $params))
			{
                $params[$key] = $value;
			}
		}
		$arrReturn = array();
		$arOffers = $arElement['OFFERS'];
		$first_pic_id = 0;
		global $USER;
		if($params['NO_SKU'] == 'N' && $params['FIRST_PIC_FROM_FIRST_SKU']=='Y' && is_array($arOffers) && count($arOffers)>0)
		{
			if(!empty($arOffers[0]['PREVIEW_PICTURE']) && $params['PAGE']!='element')
			{
				$first_pic = $arOffers[0]['PREVIEW_PICTURE'];
				$arrReturn[] = CFile::ResizeImageGet($arOffers[0]['PREVIEW_PICTURE'],array('width'=>$arSizes['WIDTH'],'height'=>$arSizes['HEIGHT']),BX_RESIZE_IMAGE_PROPORTIONAL,true,array());
			} elseif(!empty($arOffers[0]['DETAIL_PICTURE']))
			{
				$first_pic = $arOffers[0]['DETAIL_PICTURE'];
				$arrReturn[] = CFile::ResizeImageGet($arOffers[0]['DETAIL_PICTURE'],array('width'=>$arSizes['WIDTH'],'height'=>$arSizes['HEIGHT']),BX_RESIZE_IMAGE_PROPORTIONAL,true,array());
			} elseif ($params['SKU_MORE_PHOTO_CODE']!='' && IntVal($arOffers[0]['PROPERTIES'][$params['SKU_MORE_PHOTO_CODE']]['VALUE'][0])>0)
			{
				$first_pic = $arOffers[0]['PROPERTIES'][$params['SKU_MORE_PHOTO_CODE']]['VALUE'][0];
				$arrReturn[] = CFile::ResizeImageGet($first_pic,array('width'=>$arSizes['WIDTH'],'height'=>$arSizes['HEIGHT']),BX_RESIZE_IMAGE_PROPORTIONAL,true);
			}
		}
		if(!empty($arElement['PREVIEW_PICTURE']) && $params['PAGE']!='element')
		{
			$arrReturn[] = CFile::ResizeImageGet($arElement['PREVIEW_PICTURE'],array('width'=>$arSizes['WIDTH'],'height'=>$arSizes['HEIGHT']),BX_RESIZE_IMAGE_PROPORTIONAL,true,array());
		}
		if(!empty($arElement['DETAIL_PICTURE']))
		{
			$arrReturn[] = CFile::ResizeImageGet($arElement['DETAIL_PICTURE'],array('width'=>$arSizes['WIDTH'],'height'=>$arSizes['HEIGHT']),BX_RESIZE_IMAGE_PROPORTIONAL,true,array());
		}
		if($params['MORE_PHOTO_CODE']!='' && !empty($arElement['PROPERTIES'][$params['MORE_PHOTO_CODE']]['VALUE']))
		{
			foreach($arElement['PROPERTIES'][$params['MORE_PHOTO_CODE']]['VALUE'] as $picID)
			{
				if(IntVal($picID)>0)
				{
					$arrReturn[] = CFile::ResizeImageGet($picID,array('width'=>$arSizes['WIDTH'],'height'=>$arSizes['HEIGHT']),BX_RESIZE_IMAGE_PROPORTIONAL,true,array());
				}
			}
		}
		if($params['NO_SKU'] == 'N' && is_array($arOffers) && count($arOffers)>0)
		{
			foreach($arOffers as $arOffer)
			{
				if(!empty($arOffer['PREVIEW_PICTURE']) && $arOffer['PREVIEW_PICTURE'] != $first_pic && $params['PAGE'] != 'element')
				{
					$arrReturn[] = CFile::ResizeImageGet($arOffer['PREVIEW_PICTURE'],array('width'=>$arSizes['WIDTH'],'height'=>$arSizes['HEIGHT']),BX_RESIZE_IMAGE_PROPORTIONAL,true,array());
				}
				if(!empty($arOffer['DETAIL_PICTURE']) && $arOffer['DETAIL_PICTURE'] != $first_pic)
				{
					$arrReturn[] = CFile::ResizeImageGet($arOffer['DETAIL_PICTURE'],array('width'=>$arSizes['WIDTH'],'height'=>$arSizes['HEIGHT']),BX_RESIZE_IMAGE_PROPORTIONAL,true,array());
				}
				if($params['SKU_MORE_PHOTO_CODE'] != '' && !empty($arOffer['PROPERTIES'][$params['SKU_MORE_PHOTO_CODE']]['VALUE']))
				{
					foreach($arOffer['PROPERTIES'][$params['SKU_MORE_PHOTO_CODE']]['VALUE'] as $picID)
					{
						if($picID != $first_pic && IntVal($picID)>0)
						{
							$arrReturn[] = CFile::ResizeImageGet($picID,array('width'=>$arSizes['WIDTH'],'height'=>$arSizes['HEIGHT']),BX_RESIZE_IMAGE_PROPORTIONAL,true,array());
						}
					}
				}
			}
		}
		if(is_array($arrReturn) && count($arrReturn)<1)
		{
			$arrReturn[] = RSDevFunc::GetNoPhoto(array('MAX_WIDTH'=>$arSizes['WIDTH'],'MAX_HEIGHT'=>$arSizes['HEIGHT']));
		}
		return $arrReturn;
	}
	
	// add discount and print_discount to PRICES
	public static function AddPrintDiscount($arPrices)
	{
		$arrReturn = array();

		if(is_array($arPrices) && count($arPrices)>0)
		{
			foreach($arPrices as $code => $arPrice)
			{
				$arrReturn[$code] = $arPrice;
				if($arPrice['VALUE']!=$arPrice['DISCOUNT_VALUE'])
				{
					$discount = $arPrice['VALUE'] - $arPrice['DISCOUNT_VALUE'];
					$arrReturn[$code]['DISCOUNT'] = $discount;
					$arrReturn[$code]['PRINT_DISCOUNT'] = FormatCurrency($discount,$arPrice['CURRENCY']);
				}
			}
		}

		return $arrReturn;
	}

	// get min price in offers
	public static function GetMinPrices($arOffers,$considerCanBuy='N')
	{
		$arrReturn = array();
		if(is_array($arOffers) && count($arOffers)>0)
		{
			$arrMinPrices = array();
			foreach($arOffers[0]['PRICES'] as $priceCODE => $arPrice)
			{
				$arrMinPrices[$priceCODE] = array(
					'OFFER_ID' => 0,
					'KEY_IN_ARRAY' => 0,
					'PRINT_PRICE' => '',
					'PRICE' => 1000000000000000,
					'PRINT_DISCOUNT_PRICE' => '',
					'DISCOUNT_PRICE' => 1000000000000000,
				);
			}
			foreach($arOffers as $key1 => $arOffer)
			{
				foreach($arOffer['PRICES'] as $priceCODE => $arPrice)
				{
					if(
						($arPrice['DISCOUNT_VALUE']<$arrMinPrices[$priceCODE]['DISCOUNT_PRICE'] && $considerCanBuy=='N') ||
						($arPrice['DISCOUNT_VALUE']<$arrMinPrices[$priceCODE]['DISCOUNT_PRICE'] && $considerCanBuy=='Y' && $arPrice['CAN_BUY']=='Y')
					)
					{
						$arrMinPrices[$priceCODE] = array(
							'OFFER_ID' => $arOffer['ID'],
							'KEY_IN_ARRAY' => $key1,
							'CAN_BUY' => $arPrice['CAN_BUY'],
							'PRINT_PRICE' => $arPrice['PRINT_VALUE'],
							'PRICE' => $arPrice['VALUE'],
							'PRINT_DISCOUNT_PRICE' => $arPrice['PRINT_DISCOUNT_VALUE'],
							'DISCOUNT_PRICE' => $arPrice['DISCOUNT_VALUE'],
						);
					}
				}
			}
			$arrReturn = $arrMinPrices;
		}
		return $arrReturn;
	}

	// price to property for sorting
	public static function OnAfterIBlockElementAddHandler($arFields)
	{
		$ID = ( IntVal($arFields['ID'])>0 ? $arFields['ID'] : $arFields['RESULT'] );
		$TMP_ID = ( IntVal($arFields['TMP_ID'])>0 ? $arFields['TMP_ID'] : 0);
		self::_PriceToProperty($ID,$TMP_ID);
	}
	public static function OnAfterIBlockElementUpdateHandler($arFields)
	{
		$ID = ( IntVal($arFields['ID'])>0 ? $arFields['ID'] : $arFields['RESULT'] );
		self::_PriceToProperty($ID);
	}
	public static function OnPriceUpdateAddHandler($ID,$arFields)
	{
		self::_PriceToProperty($arFields['PRODUCT_ID']);
	}
	public static function _PriceToProperty($ELEM_ID,$TMP_ID=0)
	{
		if(CModule::IncludeModule('iblock') && CModule::IncludeModule('catalog'))
		{
			$propcode_cml2link = COption::GetOptionString('redsign.devfunc', 'propcode_cml2link', 'CML2_LINK');
			$propcode_fakeprice = COption::GetOptionString('redsign.devfunc', 'propcode_fakeprice', 'PROD_PRICE_FALSE');
			$price_for_fake = COption::GetOptionInt('redsign.devfunc', 'price_for_fake', '0');
			$ELEMENT_ID = IntVal($ELEM_ID);
			$res0 = CIBlockElement::GetByID($ELEMENT_ID);
			if($arElementFields = $res0->GetNext())
			{
				$ELEMENT_IBLOCK_ID = IntVal($arElementFields['IBLOCK_ID']);
				// Check parameters
				if($ELEMENT_ID && $ELEMENT_IBLOCK_ID && $propcode_cml2link != '' && $propcode_fakeprice != '' && $price_for_fake)
				{
					// Get GROUPED ELEMENT ID
					$res1 = CIBlockElement::GetProperty($ELEMENT_IBLOCK_ID, $ELEMENT_ID, array('SORT' => 'ASC'), array('CODE' => $propcode_cml2link));
					if($arFields1 = $res1->Fetch())
					{
						// This is SKU
						$GROUP_ELEMENT_ID = IntVal($arFields1['VALUE']);
						if($GROUP_ELEMENT_ID)
						{
							// Get GROUPED IBLOCK_ID
							$res2 = CIBlockElement::GetByID($GROUP_ELEMENT_ID);
							if($arFields2 = $res2->GetNext())
							{
								$GROUP_IBLOCK_ID = IntVal($arFields2['IBLOCK_ID']);
								if($GROUP_IBLOCK_ID)
								{
									// Check property FAKE_PRICE isset
									$FAKE_PRICE_PROP_ID = self::CheckIssetProperty($GROUP_IBLOCK_ID,$propcode_fakeprice);
									if(IntVal($FAKE_PRICE_PROP_ID)>0)
									{
										// Get one offers with the lowest price
										$res4 = CIBlockElement::GetList(
											array('CATALOG_PRICE_'.$price_for_fake => 'ASC'),
											array('IBLOCK_ID' => $ELEMENT_IBLOCK_ID, 'ACTIVE_DATE' => 'Y', 'ACTIVE' => 'Y', 'PROPERTY_'.$propcode_cml2link => $GROUP_ELEMENT_ID),
											false,
											array('nPageSize'=>1),
											array('ID', 'CATALOG_GROUP_'.$price_for_fake, 'PROPERTY_'.$propcode_cml2link)
										);
										if($arFields4 = $res4->GetNext())
										{
											if((int)$arFields4['CATALOG_PRICE_'.$price_for_fake] > 0)
											{
												CIBlockElement::SetPropertyValues($GROUP_ELEMENT_ID, $GROUP_IBLOCK_ID, (float)$arFields4['CATALOG_PRICE_'.$price_for_fake], $FAKE_PRICE_PROP_ID);
											}
										}
									}
								}
							}
						}
					} else {
						// This is simple (grouped) element
						$FAKE_PRICE_PROP_ID = self::CheckIssetProperty($ELEMENT_IBLOCK_ID,$propcode_fakeprice);
						if(IntVal($FAKE_PRICE_PROP_ID)>0)
						{
							$arFilter = array(
								'LOGIC' => 'OR',
								array( 'PROPERTY_'.$propcode_cml2link => $ELEMENT_ID ),
								array( 'PROPERTY_'.$propcode_cml2link => '-'.$TMP_ID ),
							);
							$res2 = CIBlockElement::GetList(
								array('CATALOG_PRICE_'.$price_for_fake => 'ASC'),
								array('ACTIVE_DATE' => 'Y', 'ACTIVE' => 'Y', $arFilter),
								false,
								array('nPageSize'=>1),
								array('ID', 'CATALOG_GROUP_'.$price_for_fake)
							);
							if($arFields2 = $res2->GetNext())
							{
								// This element have SKU
								if((int)$arFields2['CATALOG_PRICE_'.$price_for_fake] > 0)
								{
									CIBlockElement::SetPropertyValues($ELEMENT_ID, $ELEMENT_IBLOCK_ID, (float)$arFields2['CATALOG_PRICE_'.$price_for_fake], $FAKE_PRICE_PROP_ID);
								}
							} else {
								// This element dont have SKU
								$res4 = CIBlockElement::GetList(
									array(),
									array('IBLOCK_ID' => $ELEMENT_IBLOCK_ID, 'ACTIVE_DATE' => 'Y', 'ACTIVE' => 'Y', 'ID' => $ELEMENT_ID),
									false,
									array('nTopCount' => 1),
									array('*', 'CATALOG_GROUP_'.$price_for_fake)
								);
								if($arFields4 = $res4->GetNext())
								{
									if(IntVal($arFields4['CATALOG_PRICE_'.$price_for_fake])>0)
									{
										CIBlockElement::SetPropertyValues($ELEMENT_ID, $ELEMENT_IBLOCK_ID, (float)$arFields4['CATALOG_PRICE_'.$price_for_fake], $FAKE_PRICE_PROP_ID);
									}
								}
							}
						}
					}
				}
			}
		}
	}
	public static function CheckIssetProperty($IBLOCK_ID,$PROP_CODE='PROD_PRICE_FALSE')
	{
		$return = 0;
		if(IntVal($IBLOCK_ID)>0)
		{
			$res = CIBlockProperty::GetList(array(),array('ACTIVE'=>'Y','IBLOCK_ID'=>$IBLOCK_ID,'CODE'=>$PROP_CODE));
			if($arProp = $res->GetNext())
			{
				// Property isset
				$return = $arProp['ID'];
			} else {
				// Add property
				$arFields = Array(
					'NAME' => GetMessage('RSDEVFUNC_PRICE_FALSE_NAME'),
					'ACTIVE' => 'Y',
					'SORT' => '100000',
					'CODE' => $PROP_CODE,
					'PROPERTY_TYPE' => 'N',
					'IBLOCK_ID' => $IBLOCK_ID,
					'WITH_DESCRIPTION' => 'N',
				);
				$iblockproperty = new CIBlockProperty;
				$PropertyID = $iblockproperty->Add($arFields);
				$return = $PropertyID;
			}
		}
		return $return;
	}
}