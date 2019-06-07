<?php

namespace Api\Core\Iblock;

use \Bitrix\Main,
	 Bitrix\Iblock,
	 Bitrix\Catalog,
	 Bitrix\Main\Loader,
	 Bitrix\Main\Error,
	 Bitrix\Main\ErrorCollection,
	 Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

Loader::includeModule('iblock');

class Property
{
	/** @var ErrorCollection */
	protected $errorCollection;

	protected $storage = array();

	public function __construct($params = array())
	{
		$this->errorCollection = new ErrorCollection();
		$this->storage = $params;
	}

	/**
	 * @param &$result
	 *
	 * @throws \Bitrix\Main\ArgumentException
	 * @throws \Bitrix\Main\ArgumentOutOfRangeException
	 * @throws \Bitrix\Main\ObjectPropertyException
	 * @throws \Bitrix\Main\SystemException
	 */
	public function getPropertyList(&$result)
	{
		//$params(IBLOCK_ID, PROPERTY_ID, PROPERTY_CODE)
		$iblockID    = $this->storage['IBLOCK_ID'];
		$propertyIds = $this->storage['PROPERTY_ID'];
		$propCodes   = $this->storage['PROPERTY_CODE'];

		if(!$iblockID)
			return;

		//$iblockParams = $this->storage['IBLOCK_PARAMS'][ $iblock ];

		//$propCodes  = $iblockParams['PROPERTY_CODE'];
		$elementIds = array_keys($result);

		//$propertyIterator =
		/*if($property['PROPERTY_TYPE'] == Iblock\PropertyTable::TYPE_LIST){
			$existList[] = $property['ID'];
		}*/

		$iblockExtVersion = (\CIBlockElement::GetIBVersion($iblockID) == 2);
		$propertiesList   = array();
		$shortProperties  = array();
		$userTypesList    = array();
		$existList        = array();

		$selectListMultiply = array('SORT' => SORT_ASC, 'VALUE' => SORT_STRING);
		$selectAllMultiply  = array('PROPERTY_VALUE_ID' => SORT_ASC);


		$filter = array(
			 'ID'        => $elementIds,
			 'IBLOCK_ID' => $iblockID,
		);

		$propertyFilter = array(
			 'ID'   => $propertyIds,
			 'CODE' => $propCodes,
		);


		$selectFields = array(
			 'ID',
			 'IBLOCK_ID',
			 'NAME',
			 //'ACTIVE',
			 //'SORT',
			 'CODE',
			 'DEFAULT_VALUE',
			 'PROPERTY_TYPE',
			 //'ROW_COUNT',
			 //'COL_COUNT',
			 'LIST_TYPE',
			 'MULTIPLE',
			 //'XML_ID',
			 //'FILE_TYPE',
			 //'MULTIPLE_CNT',
			 'LINK_IBLOCK_ID',
			 //'WITH_DESCRIPTION',
			 //'SEARCHABLE',
			 //'FILTRABLE',
			 'IS_REQUIRED',
			 //'VERSION',
			 'USER_TYPE',
			 'USER_TYPE_SETTINGS',
			 //'HINT',
		);


		$propertyListFilter = array(
			 '=IBLOCK_ID' => $iblockID,
		);

		$propertyID    = array();
		$usePropertyId = false;

		if(isset($propertyFilter['ID'])) {
			$propertyID = (is_array($propertyFilter['ID']) ? $propertyFilter['ID'] : array($propertyFilter['ID']));
			Main\Type\Collection::normalizeArrayValuesByInt($propertyID);
		}
		if(!empty($propertyID)) {
			$propertyListFilter['=ID'] = $propertyID;
			$usePropertyId             = true;
		}
		elseif(isset($propertyFilter['CODE'])) {
			$usePropertyId = false;

			$propertyFilter['CODE'] = (is_array($propertyFilter['CODE']) ? $propertyFilter['CODE'] : array($propertyFilter['CODE']));

			$propertyCodes = array();
			if(!empty($propertyFilter['CODE'])) {
				foreach($propertyFilter['CODE'] as &$code) {
					if($code !== '')
						$propertyCodes[] = (string)$code;
				}
				unset($code);
			}

			if(!empty($propertyCodes))
				$propertyListFilter['=CODE'] = $propertyCodes;

			unset($propertyCodes);
		}


		$propertyID       = array();
		$propertyIterator = Iblock\PropertyTable::getList(array(
			 'select' => $selectFields,
			 'filter' => $propertyListFilter,
			 //'order'  => array('SORT' => 'ASC', 'ID' => 'ASC'),
		));
		while($property = $propertyIterator->fetch()) {
			$propertyID[] = (int)$property['ID'];

			$code = ($usePropertyId ? $property['ID'] : $property['CODE']);

			if($property['USER_TYPE']) {
				$userType = \CIBlockProperty::GetUserType($property['USER_TYPE']);

				if(isset($userType['ConvertFromDB'])) {
					$userTypesList[ $property['ID'] ] = $userType;
					if(array_key_exists("DEFAULT_VALUE", $property)) {
						$value = array("VALUE" => $property["DEFAULT_VALUE"], "DESCRIPTION" => "");
						$value = call_user_func_array($userType["ConvertFromDB"], array($property, $value));

						$property["DEFAULT_VALUE"] = $value["VALUE"];
					}
				}
				elseif(isset($userType['GetPublicViewHTML'])) {
					$userTypesList[ $property['ID'] ] = $userType;
				}
				unset($userType);
			}

			if($property['USER_TYPE_SETTINGS'] !== '' || $property['USER_TYPE_SETTINGS'] !== null)
				$property['USER_TYPE_SETTINGS'] = unserialize($property['USER_TYPE_SETTINGS']);

			if($property['PROPERTY_TYPE'] == Iblock\PropertyTable::TYPE_LIST) {
				$existList[] = $property['ID'];
			}

			$propertiesList[ $code ] = $property;
		}
		unset($property, $propertyIterator);

		if(empty($propertiesList))
			return;

		if(!empty($existList)) {
			$enumList     = array();
			$enumIterator = Iblock\PropertyEnumerationTable::getList(array(
				 'select' => array('ID', 'PROPERTY_ID', 'VALUE', 'SORT', 'XML_ID'),
				 'filter' => array('PROPERTY_ID' => $existList),
				 //'order'  => array('PROPERTY_ID' => 'ASC', 'SORT' => 'ASC', 'VALUE' => 'ASC'),
			));
			while($enum = $enumIterator->fetch()) {
				if(!isset($enumList[ $enum['PROPERTY_ID'] ])) {
					$enumList[ $enum['PROPERTY_ID'] ] = array();
				}
				$enumList[ $enum['PROPERTY_ID'] ][ $enum['ID'] ] = array(
					 'ID'     => $enum['ID'],
					 'VALUE'  => $enum['VALUE'],
					 'SORT'   => $enum['SORT'],
					 'XML_ID' => $enum['XML_ID'],
				);
			}
			unset($enum, $enumIterator);
		}

		$valuesRes = (
		!empty($propertyID)
			 ? \CIBlockElement::GetPropertyValues($iblockID, $filter, true, array('ID' => $propertyID))
			 : \CIBlockElement::GetPropertyValues($iblockID, $filter, true)
		);
		while($value = $valuesRes->Fetch()) {
			$elementID = $value['IBLOCK_ELEMENT_ID'];

			if(!isset($result[ $elementID ])) {
				continue;
			}

			$elementValues    = array();
			$existDescription = isset($value['DESCRIPTION']);
			foreach($propertiesList as $code => $property) {

				$existElementDescription = isset($value['DESCRIPTION']) && array_key_exists($property['ID'], $value['DESCRIPTION']);
				$existElementPropertyID  = isset($value['PROPERTY_VALUE_ID']) && array_key_exists($property['ID'], $value['PROPERTY_VALUE_ID']);
				//$elementValues[$code] = $shortProperties[$code];
				$elementValues[ $code ] = $property;

				$elementValues[ $code ]['VALUE_ENUM']   = null;
				$elementValues[ $code ]['VALUE_XML_ID'] = null;
				$elementValues[ $code ]['VALUE_SORT']   = null;
				$elementValues[ $code ]['VALUE']        = null;


				if('Y' === $property['MULTIPLE']) {
					//TODO: Оригинал значений свойства
					//$elementValues[ $code ]['PROPERTY_VALUE']    = array();
					$elementValues[ $code ]['PROPERTY_VALUE_ID'] = false;

					if(!isset($value[ $property['ID'] ]) || empty($value[ $property['ID'] ])) {
						$elementValues[ $code ]['DESCRIPTION']  = false;
						$elementValues[ $code ]['VALUE']        = false;
						$elementValues[ $code ]['~DESCRIPTION'] = false;
						$elementValues[ $code ]['~VALUE']       = false;

						if($property['PROPERTY_TYPE'] == Iblock\PropertyTable::TYPE_LIST) {
							$elementValues[ $code ]['VALUE_ENUM_ID'] = false;
							$elementValues[ $code ]['VALUE_ENUM']    = false;
							$elementValues[ $code ]['VALUE_XML_ID']  = false;
							$elementValues[ $code ]['VALUE_SORT']    = false;
						}
					}
					else {

						if($existElementPropertyID) {
							$elementValues[ $code ]['PROPERTY_VALUE_ID'] = $value['PROPERTY_VALUE_ID'][ $property['ID'] ];
						}

						if(isset($userTypesList[ $property['ID'] ])) {
							$userType     = $userTypesList[ $property['ID'] ];
							$userTypeFunc = $userType['ConvertFromDB'] ? $userType['ConvertFromDB'] : $userType['GetPublicViewHTML'];

							if($userTypeFunc) {
								foreach($value[ $property['ID'] ] as $valueKey => $oneValue) {

									//bitrix/modules/main/tools/prop_userid.php \CIBlockPropertyUserID::ConvertFromDB($arProperty,$value)
									if(isset($userType['ConvertFromDB'])) {
										$raw = call_user_func_array(
											 $userTypeFunc,
											 array(
													$property,
													array(
														 'VALUE'       => $oneValue,
														 'DESCRIPTION' => ($existElementDescription ? $value['DESCRIPTION'][ $property['ID'] ][ $valueKey ] : ''),
													),
											 )
										);

										$value[ $property['ID'] ][ $valueKey ] = $raw['VALUE'];
										if(!$existDescription) {
											$value['DESCRIPTION'] = array();
											$existDescription     = true;
										}
										if(!$existElementDescription) {
											$value['DESCRIPTION'][ $property['ID'] ] = array();
											$existElementDescription                 = true;
										}
										$value['DESCRIPTION'][ $property['ID'] ][ $valueKey ] = (string)$raw['DESCRIPTION'];
									}
									//bitrix/modules/highloadblock/classes/general/prop_directory.php \CIBlockPropertyDirectory::GetPublicViewHTML();
									elseif(isset($userType['GetPublicViewHTML'])) {
										$raw = call_user_func_array(
											 $userTypeFunc,
											 array(
													$property,
													array("VALUE" => $oneValue),
													array('MODE' => 'SIMPLE_TEXT'), //SIMPLE_TEXT | CSV_EXPORT | ELEMENT_TEMPLATE
											 )
										);

										$value[ $property['ID'] ][ $valueKey ] = $raw;
									}
								}
								if(isset($oneValue))
									unset($oneValue);
							}
						}

						//Свойство: Привязка к элементам
						if($property['PROPERTY_TYPE'] == Iblock\PropertyTable::TYPE_ELEMENT) {
							if(empty($property['USER_TYPE'])) {

								if($arValues = $value[ $property['ID'] ]) {
									$elementFilter = array(
										 '=ID' => $arValues,
									);
									if($property['LINK_IBLOCK_ID']) {
										$elementFilter['=IBLOCK_ID'] = $property['LINK_IBLOCK_ID'];
									}

									$elRows          = array();
									$elementIterator = Iblock\ElementTable::getList(array(
										 'select' => array('ID', 'NAME'),
										 'filter' => $elementFilter,
									));
									while($elRow = $elementIterator->fetch()) {
										$elRows[ $elRow['ID'] ] = $elRow['NAME'];
									}

									if($elRows) {
										foreach($value[ $property['ID'] ] as $valueKey => $oneValue) {
											$value[ $property['ID'] ][ $valueKey ] = $elRows[ $oneValue ];
										}
									}
								}
								unset($arValues, $elementFilter, $elRow, $elRows, $elementIterator, $oneValue);
							}
						}

						if($property['PROPERTY_TYPE'] == Iblock\PropertyTable::TYPE_LIST) {
							if(empty($value[ $property['ID'] ])) {
								$elementValues[ $code ]['VALUE_ENUM_ID'] = $value[ $property['ID'] ];
								$elementValues[ $code ]['DESCRIPTION']   = ($existElementDescription ? $value['DESCRIPTION'][ $property['ID'] ] : array());
							}
							else {
								$selectedValues = array();
								foreach($value[ $property['ID'] ] as $listKey => $listValue) {
									if(isset($enumList[ $property['ID'] ][ $listValue ])) {
										$selectedValues[ $listKey ]                      = $enumList[ $property['ID'] ][ $listValue ];
										$selectedValues[ $listKey ]['DESCRIPTION']       = (
										$existElementDescription && array_key_exists($listKey, $value['DESCRIPTION'][ $property['ID'] ])
											 ? $value['DESCRIPTION'][ $property['ID'] ][ $listKey ]
											 : ''
										);
										$selectedValues[ $listKey ]['PROPERTY_VALUE_ID'] = (
										$existElementPropertyID && array_key_exists($listKey, $value['PROPERTY_VALUE_ID'][ $property['ID'] ])
											 ? $value['PROPERTY_VALUE_ID'][ $property['ID'] ][ $listKey ]
											 : ''
										);
									}
								}
								if(empty($selectedValues)) {
									$elementValues[ $code ]['VALUE_ENUM_ID'] = $value[ $property['ID'] ];
									$elementValues[ $code ]['DESCRIPTION']   = ($existElementDescription ? $value['DESCRIPTION'][ $property['ID'] ] : array());
								}
								else {
									Main\Type\Collection::sortByColumn($selectedValues, $selectListMultiply);
									$elementValues[ $code ]['VALUE_SORT']        = array();
									$elementValues[ $code ]['VALUE_ENUM_ID']     = array();
									$elementValues[ $code ]['VALUE']             = array();
									$elementValues[ $code ]['VALUE_ENUM']        = array();
									$elementValues[ $code ]['VALUE_XML_ID']      = array();
									$elementValues[ $code ]['DESCRIPTION']       = array();
									$elementValues[ $code ]['PROPERTY_VALUE_ID'] = array();
									foreach($selectedValues as $listValue) {
										$elementValues[ $code ]['VALUE_SORT'][]        = $listValue['SORT'];
										$elementValues[ $code ]['VALUE_ENUM_ID'][]     = $listValue['ID'];
										$elementValues[ $code ]['VALUE'][]             = $listValue['VALUE'];
										$elementValues[ $code ]['VALUE_ENUM'][]        = $listValue['VALUE'];
										$elementValues[ $code ]['VALUE_XML_ID'][]      = $listValue['XML_ID'];
										$elementValues[ $code ]['PROPERTY_VALUE_ID'][] = $listValue['PROPERTY_VALUE_ID'];
										$elementValues[ $code ]['DESCRIPTION'][]       = $listValue['DESCRIPTION'];
									}
									unset($selectedValues);
								}
							}
						}
						else {
							if(empty($value[ $property['ID'] ]) || !$existElementPropertyID || isset($userTypesList[ $property['ID'] ])) {
								$elementValues[ $code ]['VALUE']       = $value[ $property['ID'] ];
								$elementValues[ $code ]['DESCRIPTION'] = ($existElementDescription ? $value['DESCRIPTION'][ $property['ID'] ] : array());
							}
							else {
								$selectedValues = array();
								foreach($value['PROPERTY_VALUE_ID'][ $property['ID'] ] as $propKey => $propValueID) {
									$selectedValues[ $propKey ] = array(
										 'PROPERTY_VALUE_ID' => $propValueID,
										 'VALUE'             => $value[ $property['ID'] ][ $propKey ],
									);
									if($existElementDescription) {
										$selectedValues[ $propKey ]['DESCRIPTION'] = $value['DESCRIPTION'][ $property['ID'] ][ $propKey ];
									}
								}
								unset($propValueID, $propKey);

								Main\Type\Collection::sortByColumn($selectedValues, $selectAllMultiply);
								$elementValues[ $code ]['PROPERTY_VALUE_ID'] = array();
								$elementValues[ $code ]['VALUE']             = array();
								$elementValues[ $code ]['DESCRIPTION']       = array();
								foreach($selectedValues as &$propValue) {
									$elementValues[ $code ]['PROPERTY_VALUE_ID'][] = $propValue['PROPERTY_VALUE_ID'];
									$elementValues[ $code ]['VALUE'][]             = $propValue['VALUE'];
									if($existElementDescription) {
										$elementValues[ $code ]['DESCRIPTION'][] = $propValue['DESCRIPTION'];
									}
								}
								unset($propValue, $selectedValues);
							}
						}
					}

					$elementValues[ $code ]['~VALUE'] = $elementValues[ $code ]['VALUE'];
					if(is_array($elementValues[ $code ]['VALUE'])) {
						foreach($elementValues[ $code ]['VALUE'] as &$oneValue) {
							$isArr = is_array($oneValue);
							if($isArr || ('' !== $oneValue && null !== $oneValue)) {
								if($isArr || preg_match("/[;&<>\"]/", $oneValue)) {
									$oneValue = htmlspecialcharsEx($oneValue);
								}
							}
						}
						if(isset($oneValue))
							unset($oneValue);
					}
					else {
						if('' !== $elementValues[ $code ]['VALUE'] && null !== $elementValues[ $code ]['VALUE']) {
							if(preg_match("/[;&<>\"]/", $elementValues[ $code ]['VALUE'])) {
								$elementValues[ $code ]['VALUE'] = htmlspecialcharsEx($elementValues[ $code ]['VALUE']);
							}
						}
					}

					$elementValues[ $code ]['~DESCRIPTION'] = $elementValues[ $code ]['DESCRIPTION'];
					if(is_array($elementValues[ $code ]['DESCRIPTION'])) {
						foreach($elementValues[ $code ]['DESCRIPTION'] as &$oneDescr) {
							$isArr = is_array($oneDescr);
							if($isArr || (!$isArr && '' !== $oneDescr && null !== $oneDescr)) {
								if($isArr || preg_match("/[;&<>\"]/", $oneDescr)) {
									$oneDescr = htmlspecialcharsEx($oneDescr);
								}
							}
						}
						if(isset($oneDescr))
							unset($oneDescr);
					}
					else {
						if('' !== $elementValues[ $code ]['DESCRIPTION'] && null !== $elementValues[ $code ]['DESCRIPTION']) {
							if(preg_match("/[;&<>\"]/", $elementValues[ $code ]['DESCRIPTION'])) {
								$elementValues[ $code ]['DESCRIPTION'] = htmlspecialcharsEx($elementValues[ $code ]['DESCRIPTION']);
							}
						}
					}
				}
				else {
					$elementValues[ $code ]['VALUE_ENUM'] = ($iblockExtVersion ? '' : null);
					//Оригинал значений свойства
					//$elementValues[ $code ]['PROPERTY_VALUE']    = false;
					$elementValues[ $code ]['PROPERTY_VALUE_ID'] = ($iblockExtVersion ? $elementID . ':' . $property['ID'] : null);

					if(!isset($value[ $property['ID'] ]) || false === $value[ $property['ID'] ]) {
						$elementValues[ $code ]['DESCRIPTION']  = '';
						$elementValues[ $code ]['VALUE']        = '';
						$elementValues[ $code ]['~DESCRIPTION'] = '';
						$elementValues[ $code ]['~VALUE']       = '';
						if($property['PROPERTY_TYPE'] == Iblock\PropertyTable::TYPE_LIST) {
							$elementValues[ $code ]['VALUE_ENUM_ID'] = null;
						}
					}
					else {

						if($existElementPropertyID) {
							$elementValues[ $code ]['PROPERTY_VALUE_ID'] = $value['PROPERTY_VALUE_ID'][ $property['ID'] ];
						}

						if(isset($userTypesList[ $property['ID'] ])) {
							$userType     = $userTypesList[ $property['ID'] ];
							$userTypeFunc = $userType['ConvertFromDB'] ? $userType['ConvertFromDB'] : $userType['GetPublicViewHTML'];

							if(isset($userType['ConvertFromDB'])) {
								$raw = call_user_func_array(
									 $userTypeFunc,
									 array(
											$property,
											array(
												 'VALUE'       => $value[ $property['ID'] ],
												 'DESCRIPTION' => ($existElementDescription ? $value['DESCRIPTION'][ $property['ID'] ] : ''),
											),
									 )
								);

								$value[ $property['ID'] ] = $raw['VALUE'];
								if(!$existDescription) {
									$value['DESCRIPTION'] = array();
									$existDescription     = true;
								}
								$value['DESCRIPTION'][ $property['ID'] ] = (string)$raw['DESCRIPTION'];
								$existElementDescription                 = true;
							}
							elseif(isset($userType['GetPublicViewHTML'])) {
								$raw = call_user_func_array(
									 $userTypeFunc,
									 array(
											$property,
											array("VALUE" => $value[ $property['ID'] ]),
											array('MODE' => 'SIMPLE_TEXT'),
									 )
								);

								$value[ $property['ID'] ] = $raw;
							}
						}

						//Свойство: Привязка к элементам
						if($property['PROPERTY_TYPE'] == Iblock\PropertyTable::TYPE_ELEMENT) {
							if(empty($property['USER_TYPE'])) {

								if($arValues = $value[ $property['ID'] ]) {
									$elementFilter = array(
										 '=ID' => $arValues,
									);
									if($property['LINK_IBLOCK_ID']) {
										$elementFilter['=IBLOCK_ID'] = $property['LINK_IBLOCK_ID'];
									}
									$elementIterator = Iblock\ElementTable::getList(array(
										 'select' => array('ID', 'NAME'),
										 'filter' => $elementFilter,
									));
									if($elRow = $elementIterator->fetch()) {
										$value[ $property['ID'] ] = $elRow['NAME'];
									}
								}
								unset($arValues, $elementFilter, $elRow, $elementIterator);
							}
						}


						if($property['PROPERTY_TYPE'] == Iblock\PropertyTable::TYPE_LIST) {
							$elementValues[ $code ]['VALUE_ENUM_ID'] = $value[ $property['ID'] ];
							if(isset($enumList[ $property['ID'] ][ $value[ $property['ID'] ] ])) {
								$elementValues[ $code ]['VALUE']        = $enumList[ $property['ID'] ][ $value[ $property['ID'] ] ]['VALUE'];
								$elementValues[ $code ]['VALUE_ENUM']   = $elementValues[ $code ]['VALUE'];
								$elementValues[ $code ]['VALUE_XML_ID'] = $enumList[ $property['ID'] ][ $value[ $property['ID'] ] ]['XML_ID'];
								$elementValues[ $code ]['VALUE_SORT']   = $enumList[ $property['ID'] ][ $value[ $property['ID'] ] ]['SORT'];
							}
							$elementValues[ $code ]['DESCRIPTION'] = ($existElementDescription ? $value['DESCRIPTION'][ $property['ID'] ] : null);
						}
						else {
							$elementValues[ $code ]['VALUE']       = $value[ $property['ID'] ];
							$elementValues[ $code ]['DESCRIPTION'] = ($existElementDescription ? $value['DESCRIPTION'][ $property['ID'] ] : '');
						}
					}


					$elementValues[ $code ]['~VALUE'] = $elementValues[ $code ]['VALUE'];

					$isArr = is_array($elementValues[ $code ]['VALUE']);
					if($isArr || ('' !== $elementValues[ $code ]['VALUE'] && null !== $elementValues[ $code ]['VALUE'])) {
						if($isArr || preg_match("/[;&<>\"]/", $elementValues[ $code ]['VALUE'])) {
							$elementValues[ $code ]['VALUE'] = htmlspecialcharsEx($elementValues[ $code ]['VALUE']);
						}
					}


					$elementValues[ $code ]['~DESCRIPTION'] = $elementValues[ $code ]['DESCRIPTION'];

					$isArr = is_array($elementValues[ $code ]['DESCRIPTION']);
					if($isArr || ('' !== $elementValues[ $code ]['DESCRIPTION'] && null !== $elementValues[ $code ]['DESCRIPTION'])) {
						if($isArr || preg_match("/[;&<>\"]/", $elementValues[ $code ]['DESCRIPTION']))
							$elementValues[ $code ]['DESCRIPTION'] = htmlspecialcharsEx($elementValues[ $code ]['DESCRIPTION']);
					}
				}



				//!Обязательно нужно для каталожных условий USE_CONDITIONS по значению свойств
				//(((isset($arItem['PROPERTY_520_VALUE']) && in_array(113, $arItem['PROPERTY_520_VALUE']))))
				if($result[ $elementID ]) {
					$result[ $elementID ][ 'PROPERTY_' . $property['ID'] . '_VALUE' ] = $elementValues[ $code ]['~VALUE'];
				}
			}

			if(isset($result[ $elementID ]['PROPERTIES'])) {
				$result[ $elementID ]['PROPERTIES'] = $elementValues;
			}

			unset($elementValues);
		}

		unset($iblockID, $propCodes, $result);
	}


	/** @deprecated  Allow max memory usage */
	protected
	function modifyDisplayPropertiesOld($iblock, &$iblockElements)
	{
		if(!empty($iblockElements)) {
			$iblockParams         = $this->storage['IBLOCK_PARAMS'][ $iblock ];
			$propertyCodes        = $iblockParams['PROPERTY_CODE'];
			$productProperties    = $iblockParams['CART_PROPERTIES'];
			$getPropertyCodes     = !empty($propertyCodes);
			$getProductProperties = !empty($productProperties);
			$getIblockProperties  = $getPropertyCodes || $getProductProperties;

			if($getIblockProperties || ($this->useCatalog && $this->useDiscountCache)) {
				$propFilter = array(
					 'ID'        => array_keys($iblockElements),
					 'IBLOCK_ID' => $iblock,
				);
				\CIBlockElement::GetPropertyValuesArray(
					 $iblockElements,
					 $iblock,
					 $propFilter,
					 array('CODE' => $propertyCodes),
					 array('USE_PROPERTY_ID' => 'N')
				);

				/*if($getPropertyCodes) {
					$propertyList = $this->getPropertyList($iblock, $propertyCodes);
				}*/

				foreach($iblockElements as &$element) {
					if($this->useCatalog && $this->useDiscountCache) {
						if($this->storage['USE_SALE_DISCOUNTS'])
							Catalog\Discount\DiscountManager::setProductPropertiesCache($element['ID'], $element["PROPERTIES"]);
						else
							\CCatalogDiscount::SetProductPropertiesCache($element['ID'], $element['PROPERTIES']);
					}

					if($getIblockProperties) {
						if(!empty($propertyCodes)) {
							foreach($propertyCodes as $pid) {
								if(!isset($element['PROPERTIES'][ $pid ]))
									continue;

								$prop  = &$element['PROPERTIES'][ $pid ];
								$isArr = is_array($prop['VALUE']);
								if(($isArr && !empty($prop['VALUE'])) || (!$isArr && (string)$prop['VALUE'] !== '')) {
									$element['DISPLAY_PROPERTIES'][ $pid ] = \CIBlockFormatProperties::GetDisplayValue($element, $prop, 'catalog_out');
								}

								//Обязательно нужно для каталожных условий USE_CONDITIONS по значению свойств
								//(((isset($arItem['PROPERTY_520_VALUE']) && in_array(113, $arItem['PROPERTY_520_VALUE']))))
								if($dispProp = $element['PROPERTIES'][ $pid ]) {

									//Фикс для свойств типа список
									//если выбрано одно значение списка, в значении будет строка, а не массив, а $obCond->Generate() смотрит ID значения в массиве используя in_array()
									if($dispProp['PROPERTY_TYPE'] == 'L') {
										if($dispProp['VALUE_ENUM_ID'] && is_string($dispProp['VALUE_ENUM_ID'])) {
											$dispProp['VALUE'] = array($dispProp['VALUE_ENUM_ID']);
										}
									}
									$element[ 'PROPERTY_' . $dispProp['ID'] . '_VALUE' ] = $dispProp['VALUE'];
								}

								unset($prop, $dispProp);
							}
							unset($pid);
						}

						if($getProductProperties) {
							$element['PRODUCT_PROPERTIES'] = \CIBlockPriceTools::GetProductProperties(
								 $iblock,
								 $element['ID'],
								 $productProperties,
								 $element['PROPERTIES']
							);

							if(!empty($element['PRODUCT_PROPERTIES'])) {
								$element['PRODUCT_PROPERTIES_FILL'] = \CIBlockPriceTools::getFillProductProperties($element['PRODUCT_PROPERTIES']);
							}
						}
					}
				}
				unset($element);
			}
		}
	}

	/** @deprecated  Allow max memory usage */
	protected
	function getPropertyListOld($iblock, $propertyCodes)
	{
		$propertyList = array();
		if(empty($propertyCodes))
			return $propertyList;

		$propertyCodes = array_fill_keys($propertyCodes, true);

		//TODO: Исправить получение всех свойств инфоблока или вообще убрать проверку
		$propertyIterator = Iblock\PropertyTable::getList(array(
			 'select' => array('ID', 'CODE'),
			 'filter' => array('=IBLOCK_ID' => $iblock, '=ACTIVE' => 'Y'),
			 'order'  => array('SORT' => 'ASC', 'ID' => 'ASC'),
		));
		while($property = $propertyIterator->fetch()) {
			$code = (string)$property['CODE'];

			if($code == '') {
				$code = $property['ID'];
			}

			if(!isset($propertyCodes[ $code ]))
				continue;

			$propertyList[] = $code;
		}

		return $propertyList;
	}
}