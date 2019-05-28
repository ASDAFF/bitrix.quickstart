<?
use Bitrix\Iblock;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Loader;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (!function_exists("getProductByProps"))
{
	function getProductByProps($iblockID, $arSkuProps, $extMode = false)
	{
		$extMode = ($extMode === true);
		$result = false;
		$arOfFilter = array(
			"IBLOCK_ID" => $iblockID,
		);

		$directoryList = array();
		$propertyIterator = Iblock\PropertyTable::getList(array(
			'select' => array('ID', 'CODE', 'PROPERTY_TYPE', 'USER_TYPE', 'USER_TYPE_SETTINGS', 'XML_ID'),
			'filter' => array(
				'IBLOCK_ID' => $iblockID,
				'ACTIVE' => 'Y',
				'=PROPERTY_TYPE' => array(Iblock\PropertyTable::TYPE_ELEMENT, Iblock\PropertyTable::TYPE_LIST, Iblock\PropertyTable::TYPE_STRING)
			),
			'order' => array('SORT' => 'ASC', 'ID' => 'ASC')
		));
		while ($property = $propertyIterator->fetch())
		{
			$property['CODE'] = (string)$property['CODE'];
			if ($property['CODE'] == '')
				$property['CODE'] = $property['ID'];
			if (!isset($arSkuProps[$property['CODE']]))
				continue;
			if ($property['PROPERTY_TYPE'] == Iblock\PropertyTable::TYPE_LIST)
			{
				$arOfFilter['PROPERTY_'.$property['ID'].'_VALUE'] = $arSkuProps[$property['CODE']];
			}
			elseif ($property['PROPERTY_TYPE'] == Iblock\PropertyTable::TYPE_ELEMENT)
			{
				$arOfFilter['PROPERTY_'.$property['ID']] = $arSkuProps[$property['CODE']];
			}
			elseif ($property['PROPERTY_TYPE'] == Iblock\PropertyTable::TYPE_STRING && $property['USER_TYPE'] == 'directory')
			{
				$arOfFilter['PROPERTY_'.$property['ID']] = $arSkuProps[$property['CODE']];
				if (!empty($property['USER_TYPE_SETTINGS']))
					$directoryList[$property['ID']] = $property;
			}
		}

		$rsOffers = CIBlockElement::GetList(
			array(),
			$arOfFilter,
			false,
			false,
			array('ID', 'IBLOCK_ID', 'IBLOCK_SECTION_ID','XML_ID')
		);
		if ($arOffer = $rsOffers->Fetch())
		{
			$result = ($extMode ? $arOffer : $arOffer['ID']);
		}
		elseif (!empty($directoryList) && Loader::includeModule('highloadblock'))
		{
			$newSearch = false;
			foreach ($directoryList as &$property)
			{
				if (!CheckSerializedData($property['USER_TYPE_SETTINGS']))
					continue;
				$property['USER_TYPE_SETTINGS'] = unserialize($property['USER_TYPE_SETTINGS']);
				if (empty($property['USER_TYPE_SETTINGS']['TABLE_NAME']))
					continue;
				$hlblock = HL\HighloadBlockTable::getList(array('filter' => array('=TABLE_NAME' => $property['USER_TYPE_SETTINGS']['TABLE_NAME'])))->fetch();
				if ($hlblock)
				{
					$value = $arOfFilter['PROPERTY_'.$property['ID']];
					$entity = HL\HighloadBlockTable::compileEntity($hlblock);
					$entityDataClass = $entity->getDataClass();
					$dataIterator = $entityDataClass::getList(array(
						'select' => array('ID', 'UF_NAME', 'UF_XML_ID'),
						'filter' => array(array(
							'LOGIC' => 'OR',
							'=UF_XML_ID' => $value,
							'=UF_NAME' => $value
						))
					));
					while ($data = $dataIterator->fetch())
					{
						if ($data['UF_XML_ID'] == $value)
							break;
						if ($data['UF_NAME'] == $value)
						{
							$arOfFilter['PROPERTY_'.$property['ID']] = $data['UF_XML_ID'];
							$newSearch = true;
							break;
						}
					}
					unset($data, $dataIterator, $entityDataClass, $entity);
				}
				unset($hlblock);
			}
			unset($property);

			if ($newSearch)
			{
				$rsOffers = CIBlockElement::GetList(
					array(),
					$arOfFilter,
					false,
					false,
					array('ID', 'IBLOCK_ID', 'IBLOCK_SECTION_ID','XML_ID')
				);
				if ($arOffer = $rsOffers->Fetch())
				{
					$result = ($extMode ? $arOffer : $arOffer['ID']);
				}
			}
		}

		return $result;
	}
}
?>