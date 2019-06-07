<?php

namespace Api\Export;


class Condition
{
	/**
	 * Return parsed conditions array.
	 *
	 * @param $condition
	 * @param $params
	 *
	 * @return array
	 */
	public function parseCondition($condition, $params)
	{
		$result = array();

		if(!empty($condition) && is_array($condition)) {
			if($condition['CLASS_ID'] === 'CondGroup') {
				if(!empty($condition['CHILDREN'])) {
					foreach($condition['CHILDREN'] as $child) {
						$childResult = $this->parseCondition($child, $params);

						// is group
						if($child['CLASS_ID'] === 'CondGroup') {
							$result[] = $childResult;
						}
						// same property names not overrides each other
						elseif(isset($result[ key($childResult) ])) {
							$fieldName = key($childResult);

							if(!isset($result['LOGIC'])) {
								$result = array(
									 'LOGIC' => $condition['DATA']['All'],
									 array($fieldName => $result[ $fieldName ]),
								);
							}

							$result[][ $fieldName ] = $childResult[ $fieldName ];
						}
						else {
							$result += $childResult;
						}
					}

					if(!empty($result)) {
						$this->parsePropertyCondition($result, $condition, $params);

						if(count($result) > 1) {
							$result['LOGIC'] = $condition['DATA']['All'];
						}
					}
				}
			}
			else {
				$result += $this->parseConditionLevel($condition, $params);
			}
		}

		return $result;
	}

	protected function parseConditionLevel($condition, $params)
	{
		$result = array();

		if(!empty($condition) && is_array($condition)) {
			$name = $this->parseConditionName($condition);
			if(!empty($name)) {
				$operator                    = $this->parseConditionOperator($condition);
				$value                       = $this->parseConditionValue($condition, $name);
				$result[ $operator . $name ] = $value;

				if($name === 'SECTION_ID') {
					$result['INCLUDE_SUBSECTIONS'] = isset($params['INCLUDE_SUBSECTIONS']) && $params['INCLUDE_SUBSECTIONS'] === 'N' ? 'N' : 'Y';

					if(isset($params['INCLUDE_SUBSECTIONS']) && $params['INCLUDE_SUBSECTIONS'] === 'A') {
						$result['SECTION_GLOBAL_ACTIVE'] = 'Y';
					}

					$result = array($result);
				}
			}
		}

		return $result;
	}

	protected function parseConditionName(array $condition)
	{
		$name = '';

		//CondGroup
		//bitrix/modules/catalog/general/catalog_cond.php::2906::GetControls()
		$conditionNameMap = array(
			//Поля и характеристики товара
			'CondIBElement'        => 'ID',
			'CondIBIBlock'         => 'IBLOCK_ID',
			'CondIBSection'        => 'SECTION_ID',
			'CondIBCode'           => 'CODE',
			'CondIBXmlID'          => 'XML_ID',
			'CondIBName'           => 'NAME',
			'CondIBDateActiveFrom' => 'DATE_ACTIVE_FROM',
			'CondIBDateActiveTo'   => 'DATE_ACTIVE_TO',
			'CondIBSort'           => 'SORT',
			'CondIBPreviewText'    => 'PREVIEW_TEXT',
			'CondIBDetailText'     => 'DETAIL_TEXT',
			'CondIBDateCreate'     => 'DATE_CREATE',
			'CondIBCreatedBy'      => 'CREATED_BY',
			'CondIBTimestampX'     => 'TIMESTAMP_X',
			'CondIBModifiedBy'     => 'MODIFIED_BY',
			'CondIBTags'           => 'TAGS',
			'CondCatQuantity'      => 'CATALOG_QUANTITY',
			'CondCatWeight'        => 'CATALOG_WEIGHT',
			'CondCatVatID'         => 'CATALOG_VAT_ID',
			'CondCatVatIncluded'   => 'CATALOG_VAT_INCLUDED',

			//Not Found
			'CondIBActive'         => 'ACTIVE',
		);

		if(isset($conditionNameMap[ $condition['CLASS_ID'] ])) {
			$name = $conditionNameMap[ $condition['CLASS_ID'] ];
		}
		elseif(strpos($condition['CLASS_ID'], 'CondIBProp') !== false) {
			$name = $condition['CLASS_ID'];
		}

		return $name;
	}

	protected function parseConditionOperator($condition)
	{
		$operator = '';

		switch($condition['DATA']['logic']) {
			case 'Equal':
				$operator = '';
				break;
			case 'Not':
				$operator = '!';
				break;
			case 'Contain':
				$operator = '%';
				break;
			case 'NotCont':
				$operator = '!%';
				break;
			case 'Great':
				$operator = '>';
				break;
			case 'Less':
				$operator = '<';
				break;
			case 'EqGr':
				$operator = '>=';
				break;
			case 'EqLs':
				$operator = '<=';
				break;
		}

		return $operator;
	}

	protected function parseConditionValue($condition, $name)
	{
		$value = $condition['DATA']['value'];

		switch($name) {
			case 'DATE_ACTIVE_FROM':
			case 'DATE_ACTIVE_TO':
			case 'DATE_CREATE':
			case 'TIMESTAMP_X':
				$value = ConvertTimeStamp($value, 'FULL');
				break;
		}

		return $value;
	}

	protected function parsePropertyCondition(array &$result, array $condition, $params)
	{
		if(!empty($result)) {
			$subFilter = array();

			foreach($result as $name => $value) {
				if(!empty($result[ $name ]) && is_array($result[ $name ])) {
					$this->parsePropertyCondition($result[ $name ], $condition, $params);
				}
				else {
					if(($ind = strpos($name, 'CondIBProp')) !== false) {
						list($prefix, $iblock, $propertyId) = explode(':', $name);
						$operator = $ind > 0 ? substr($prefix, 0, $ind) : '';

						$catalogInfo = \CCatalogSku::GetInfoByIBlock($iblock);
						if(!empty($catalogInfo)) {
							if(
								 $catalogInfo['CATALOG_TYPE'] != \CCatalogSku::TYPE_CATALOG
								 && $catalogInfo['IBLOCK_ID'] == $iblock
							) {
								//(!) Для инфоблока ТП подзапрос SubQuery() создает проблемы
								//$subFilter[$operator.'PROPERTY_'.$propertyId] = $value;
								$result[ $operator . 'PROPERTY_' . $propertyId ] = $value;
							}
							else {
								$result[ $operator . 'PROPERTY_' . $propertyId ] = $value;
							}
						}

						unset($result[ $name ]);
					}
				}
			}

			if(!empty($subFilter) && !empty($catalogInfo)) {
				$offerPropFilter = array(
					 'IBLOCK_ID'   => $catalogInfo['IBLOCK_ID'],
					 'ACTIVE_DATE' => 'Y',
					 'ACTIVE'      => 'Y',
				);

				if($params['HIDE_NOT_AVAILABLE_OFFERS'] === 'Y') {
					$offerPropFilter['HIDE_NOT_AVAILABLE'] = 'Y';
				}
				elseif($params['HIDE_NOT_AVAILABLE_OFFERS'] === 'L') {
					$offerPropFilter[] = array(
						 'LOGIC'             => 'OR',
						 'CATALOG_AVAILABLE' => 'Y',
						 'CATALOG_SUBSCRIBE' => 'Y',
					);
				}

				if(count($subFilter) > 1) {
					$subFilter['LOGIC'] = $condition['DATA']['All'];
					$subFilter          = array($subFilter);
				}

				$result['=ID'] = \CIBlockElement::SubQuery(
					 'PROPERTY_' . $catalogInfo['SKU_PROPERTY_ID'],
					 $offerPropFilter + $subFilter
				);
			}
		}
	}

}