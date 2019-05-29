<?php

namespace Yandex\Market\Ui\UserField;

use Bitrix\Iblock;
use Bitrix\Main;
use Bitrix\Catalog;
use Yandex\Market;

Main\Localization\Loc::loadMessages(__FILE__);

class IblockType extends \CUserTypeEnum
{
	/**
	 * @param bool $arUserField
	 *
	 * @return \CDBResult
	 * @throws \Bitrix\Main\ArgumentException
	 * @throws \Bitrix\Main\LoaderException
	 */
	function GetList($arUserField)
	{
		static $result = null;

		if (!isset($result))
		{
			$values = [];
			$iblockMap = [];

			if (Main\Loader::includeModule('iblock'))
			{
				$iblockIndex = 0;
				$queryIblockList = Iblock\IblockTable::getList([
					'filter' => [
						'=ACTIVE' => 'Y'
					]
				]);

				while ($iblock = $queryIblockList->Fetch())
				{
					$values[] = [
						'ID' => $iblock['ID'],
						'VALUE' => htmlspecialcharsbx('[' . $iblock['ID'] . '] ' . $iblock['NAME']),
						'CATALOG_TYPE' => null
					];

					$iblockMap[$iblock['ID']] = $iblockIndex;
					$iblockIndex++;
				}
			}

			if (!empty($iblockMap) && Main\Loader::includeModule('catalog'))
			{
				$queryCatalogList = Catalog\CatalogIblockTable::getList([
					'filter' => [
						'=IBLOCK_ID' => array_keys($iblockMap)
					]
				]);

				while ($catalog = $queryCatalogList->fetch())
				{
					if (isset($iblockMap[$catalog['IBLOCK_ID']]))
					{
						$iblockIndex = $iblockMap[$catalog['IBLOCK_ID']];

						if (!empty($catalog['SKU_PROPERTY_ID'])) // is offer iblock
						{
							unset($values[$iblockIndex]);

							if (isset($iblockMap[$catalog['PRODUCT_IBLOCK_ID']]))
							{
								$catalogIndex = $iblockMap[$catalog['PRODUCT_IBLOCK_ID']];

								$values[$catalogIndex]['CATALOG_TYPE'] = 'PRODUCT';
							}
						}
						else
						{
							$values[$iblockIndex]['CATALOG_TYPE'] = 'PRODUCT';
						}
					}
				}
			}

			$result = $values;
		}

		$queryResult = new \CDBResult();
		$queryResult->InitFromArray($result);

		return $queryResult;
	}

	/**
	 * @param $arUserField
	 * @param $arHtmlControl
	 *
	 * @return string
	 * @throws \Bitrix\Main\ArgumentException
	 * @throws \Bitrix\Main\LoaderException
	 */
	public function GetEditFormHTMLMulty($arUserField, $arHtmlControl)
	{
		$queryOptions = self::GetList($arUserField);
		$options = [];
		$existsGroups = [];

		while ($option = $queryOptions->fetch())
		{
			$existsGroups[$option['CATALOG_TYPE']] = true;

			$options[] = $option;
		}

		$returnHtml = '';
		$hasFewGroups = (count($existsGroups) > 1);
		$groups = [
			'CATALOG' => 'PRODUCT',
			'OTHER' => null
		];

		foreach ($groups as $groupType => $catalogType)
		{
			$groupHtml = (
				$hasFewGroups
					? '<div class="adm-iblock-section-' . strtolower($groupType) . '">' . Market\Config::getLang('USER_FIELD_IBLOCK_TYPE_GROUP_' . $groupType) . '</div>'
					: ''
			);
			$groupIndex = 0;

			foreach ($options as $option)
			{
				if ($option['CATALOG_TYPE'] === $catalogType)
				{
					$randStr = randString(5);
					$isChecked = !empty($arHtmlControl['VALUE']) && in_array($option['ID'], $arHtmlControl['VALUE']);

					$groupHtml .=
						'<div>'
						. '<input class="adm-designed-checkbox" type="checkbox" name="' . $arHtmlControl['NAME'] . '" value="' . $option['ID'] . '" ' . ($isChecked ? 'checked' : '') . ' id="checkbox_' . $randStr . '">'
						. '<label class="adm-designed-checkbox-label" for="checkbox_'.$randStr.'"></label>'
						. '<label for="checkbox_'.$randStr.'"> ' . $option['VALUE'] . '</label>'
						. '</div>';

					$groupIndex++;
				}
			}

			if ($groupIndex > 0)
			{
				$returnHtml .= $groupHtml;
			}
		}

		return $returnHtml;
	}
}