<?

namespace Redsign\DevFunc\Catalog;

/** @global \CMain $APPLICATION */
use Bitrix\Main\Localization\Loc,
	Bitrix\Main,
	Bitrix\Currency,
	Bitrix\Catalog,
	Bitrix\Sale;

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/catalog/general/product.php");

// Loc::loadMessages(__FILE__);

class Product extends \CAllCatalogProduct
{

	/**
	 * @param int $intProductID
	 * @param int|float $quantity
	 * @param array $arUserGroups
	 * @param string $renewal
	 * @param array $priceList
	 * @param bool|string $siteID
	 * @param bool|array $arDiscountCoupons
	 * @return array|bool
	 */
	public static function GetOptimalPrice($intProductID, $quantity = 1, $arUserGroups = array(), $renewal = "N", $priceList = array(), $siteID = false, $arDiscountCoupons = false, $arRegion = array())
	{
        global $APPLICATION;
        
		$intProductID = (int)$intProductID;
		if ($intProductID <= 0)
		{
			$APPLICATION->ThrowException(Loc::getMessage("BT_MOD_CATALOG_PROD_ERR_PRODUCT_ID_ABSENT"), "NO_PRODUCT_ID");
			return false;
		}

		$quantity = (float)$quantity;
		if ($quantity <= 0)
		{
			$APPLICATION->ThrowException(Loc::getMessage("BT_MOD_CATALOG_PROD_ERR_QUANTITY_ABSENT"), "NO_QUANTITY");
			return false;
		}

		if (!is_array($arUserGroups) && (int)$arUserGroups.'|' == (string)$arUserGroups.'|')
			$arUserGroups = array((int)$arUserGroups);

		if (!is_array($arUserGroups))
			$arUserGroups = array();
		if (!in_array(2, $arUserGroups))
			$arUserGroups[] = 2;
		Main\Type\Collection::normalizeArrayValuesByInt($arUserGroups);
		$renewal = ($renewal == 'Y' ? 'Y' : 'N');
		if ($siteID === false)
			$siteID = SITE_ID;
		$resultCurrency = Catalog\Product\Price\Calculation::getCurrency();
		if (empty($resultCurrency))
		{
			$APPLICATION->ThrowException(Loc::getMessage("BT_MOD_CATALOG_PROD_ERR_NO_RESULT_CURRENCY"));
			return false;
		}

		$intIBlockID = (int)\CIBlockElement::GetIBlockByID($intProductID);
		if ($intIBlockID <= 0)
		{
			$APPLICATION->ThrowException(
				Loc::getMessage(
					'BT_MOD_CATALOG_PROD_ERR_ELEMENT_ID_NOT_FOUND',
					array('#ID#' => $intProductID)
				),
				'NO_ELEMENT'
			);
			return false;
		}

		if (!isset($priceList) || !is_array($priceList))
			$priceList = array();

		if (empty($priceList))
		{
			$priceTypeList = self::getAllowedPriceTypes($arUserGroups);

			if (is_array($arRegion['LIST_PRICES']) && reset($arRegion['LIST_PRICES']) !== 'component') {

				$priceTypeList = array_filter(
					$priceTypeList,
					function ($v) use ($arRegion) {
						return in_array($v, array_column($arRegion['LIST_PRICES'], 'ID'));
					}
				);
			}

			if (empty($priceTypeList))
				return false;

			$iterator = Catalog\PriceTable::getList(array(
				'select' => array('ID', 'CATALOG_GROUP_ID', 'PRICE', 'CURRENCY'),
				'filter' => array(
					'=PRODUCT_ID' => $intProductID,
					'@CATALOG_GROUP_ID' => $priceTypeList,
					array(
						'LOGIC' => 'OR',
						'<=QUANTITY_FROM' => $quantity,
						'=QUANTITY_FROM' => null
					),
					array(
						'LOGIC' => 'OR',
						'>=QUANTITY_TO' => $quantity,
						'=QUANTITY_TO' => null
					)
				)
			));
			while ($row = $iterator->fetch())
			{
				$row['ELEMENT_IBLOCK_ID'] = $intIBlockID;
				$priceList[] = $row;
			}
			unset($row, $iterator);
			unset($priceTypeList);
		}
		else
		{
			foreach (array_keys($priceList) as $priceIndex)
				$priceList[$priceIndex]['ELEMENT_IBLOCK_ID'] = $intIBlockID;
			unset($priceIndex);
		}

		if (empty($priceList))
			return false;

		$iterator = \CCatalogProduct::GetVATInfo($intProductID);
		$vat = $iterator->Fetch();
		if (!empty($vat))
		{
			$vat['RATE'] = (float)$vat['RATE'] * 0.01;
		}
		else
		{
			$vat = array('RATE' => 0.0, 'VAT_INCLUDED' => 'N');
		}
		unset($iterator);

		$isNeedDiscounts = Catalog\Product\Price\Calculation::isAllowedUseDiscounts();
		$resultWithVat = Catalog\Product\Price\Calculation::isIncludingVat();
		if ($isNeedDiscounts)
		{
			if ($arDiscountCoupons === false)
				$arDiscountCoupons = \CCatalogDiscountCoupon::GetCoupons();
		}

//		$boolDiscountVat = ('N' != COption::GetOptionString('catalog', 'discount_vat', 'Y'));
		$boolDiscountVat = true;

		$minimalPrice = array();

		// if (self::$saleIncluded === null)
			// self::initSaleSettings();
		// $isNeedleToMinimizeCatalogGroup = self::isNeedleToMinimizeCatalogGroup($priceList);

		foreach ($priceList as $priceData)
		{
			$priceData['VAT_RATE'] = $vat['RATE'];
			$priceData['VAT_INCLUDED'] = $vat['VAT_INCLUDED'];

			$currentPrice = (float)$priceData['PRICE'];
			if ($boolDiscountVat)
			{
				if ($priceData['VAT_INCLUDED'] == 'N')
					$currentPrice *= (1 + $priceData['VAT_RATE']);
			}
			else
			{
				if ($priceData['VAT_INCLUDED'] == 'Y')
					$currentPrice /= (1 + $priceData['VAT_RATE']);
			}
			if ($priceData['CURRENCY'] != $resultCurrency)
				$currentPrice = \CCurrencyRates::ConvertCurrency($currentPrice, $priceData['CURRENCY'], $resultCurrency);
			$currentPrice = \Bitrix\Catalog\Product\Price\Calculation::roundPrecision($currentPrice);

			$result = array(
				'BASE_PRICE' => $currentPrice,
				'COMPARE_PRICE' => $currentPrice,
				'PRICE' => $currentPrice,
				'CURRENCY' => $resultCurrency,
				'DISCOUNT_LIST' => array(),
				'USE_ROUND' => self::$useSaleDiscount !== true,
				'RAW_PRICE' => $priceData
			);
			if ($isNeedDiscounts)
			{
				$arDiscounts = \CCatalogDiscount::GetDiscount(
					$intProductID,
					$intIBlockID,
					$priceData['CATALOG_GROUP_ID'],
					$arUserGroups,
					$renewal,
					$siteID,
					$arDiscountCoupons
				);

				$discountResult = \CCatalogDiscount::applyDiscountList($currentPrice, $resultCurrency, $arDiscounts);
				unset($arDiscounts);
				if ($discountResult === false)
					return false;
				$result['PRICE'] = $discountResult['PRICE'];
				$result['COMPARE_PRICE'] = $discountResult['PRICE'];
				$result['DISCOUNT_LIST'] = $discountResult['DISCOUNT_LIST'];
				$result['USE_ROUND'] = true;
				unset($discountResult);
			}
			elseif($isNeedleToMinimizeCatalogGroup)
			{
				$calculateData = $priceData;
				$calculateData['PRICE'] = $currentPrice;
				$calculateData['CURRENCY'] = $resultCurrency;
				$possibleSalePrice = self::getPossibleSalePrice(
					$intProductID,
					$calculateData,
					$quantity,
					$siteID,
					$arUserGroups
				);
				unset($calculateData);
				if ($possibleSalePrice === null)
					return false;
				$result['USE_ROUND'] = false;
				$result['COMPARE_PRICE'] = $possibleSalePrice;
				unset($possibleSalePrice);
			}

			if ($boolDiscountVat)
			{
				if (!$resultWithVat)
				{
					$result['PRICE'] /= (1 + $priceData['VAT_RATE']);
					$result['COMPARE_PRICE'] /= (1 + $priceData['VAT_RATE']);
					$result['BASE_PRICE'] /= (1 + $priceData['VAT_RATE']);
				}
			}
			else
			{
				if ($resultWithVat)
				{
					$result['PRICE'] *= (1 + $priceData['VAT_RATE']);
					$result['COMPARE_PRICE'] *= (1 + $priceData['VAT_RATE']);
					$result['BASE_PRICE'] *= (1 + $priceData['VAT_RATE']);
				}
			}

			$result['UNROUND_PRICE'] = $result['PRICE'];
			$result['UNROUND_BASE_PRICE'] = $result['BASE_PRICE'];
			$result['ROUND_RULE'] = array();

			if ($result['USE_ROUND'])
			{
				$result['ROUND_RULE'] = Catalog\Product\Price::searchRoundRule(
					$priceData['CATALOG_GROUP_ID'],
					$result['PRICE'],
					$resultCurrency
				);
				if (!empty($result['ROUND_RULE']))
				{
					$result['PRICE'] = Catalog\Product\Price::roundValue(
						$result['PRICE'],
						$result['ROUND_RULE']['ROUND_PRECISION'],
						$result['ROUND_RULE']['ROUND_TYPE']
					);

					$result['COMPARE_PRICE'] = $result['PRICE'];
				}
			}

			if (
				empty($result['DISCOUNT_LIST'])
				|| Catalog\Product\Price\Calculation::compare($result['BASE_PRICE'], $result['PRICE'], '<=')
			)
			{
				$result['BASE_PRICE'] = $result['PRICE'];
			}

			if (empty($minimalPrice) || $minimalPrice['COMPARE_PRICE'] > $result['COMPARE_PRICE'])
			{
				$minimalPrice = $result;
			}

			unset($currentPrice, $result);
		}
		unset($priceData);
		unset($vat);

		$discountValue = ($minimalPrice['BASE_PRICE'] > $minimalPrice['PRICE'] ? $minimalPrice['BASE_PRICE'] - $minimalPrice['PRICE'] : 0);

		$arResult = array(
			'PRICE' => $minimalPrice['RAW_PRICE'],
			'RESULT_PRICE' => array(
				'PRICE_TYPE_ID' => $minimalPrice['RAW_PRICE']['CATALOG_GROUP_ID'],
				'BASE_PRICE' => $minimalPrice['BASE_PRICE'],
				'DISCOUNT_PRICE' => $minimalPrice['PRICE'],
				'CURRENCY' => $resultCurrency,
				'DISCOUNT' => $discountValue,
				'PERCENT' => (
					$minimalPrice['BASE_PRICE'] > 0 && $discountValue > 0
					? roundEx((100*$discountValue)/$minimalPrice['BASE_PRICE'], CATALOG_VALUE_PRECISION)
					: 0
				),
				'VAT_RATE' => $minimalPrice['RAW_PRICE']['VAT_RATE'],
				'VAT_INCLUDED' => ($resultWithVat ? 'Y' : 'N'),
				'UNROUND_BASE_PRICE' => $minimalPrice['UNROUND_BASE_PRICE'],
				'UNROUND_DISCOUNT_PRICE' => $minimalPrice['UNROUND_PRICE'],
				'ROUND_RULE' => $minimalPrice['ROUND_RULE']
			),
			'DISCOUNT_PRICE' => $minimalPrice['PRICE'],
			'DISCOUNT' => array(),
			'DISCOUNT_LIST' => array(),
			'PRODUCT_ID' => $intProductID
		);
		if (!empty($minimalPrice['DISCOUNT_LIST']))
		{
			reset($minimalPrice['DISCOUNT_LIST']);
			$arResult['DISCOUNT'] = current($minimalPrice['DISCOUNT_LIST']);
			$arResult['DISCOUNT_LIST'] = $minimalPrice['DISCOUNT_LIST'];
		}
		unset($minimalPrice);

		return $arResult;
	}
	/**
	 * @param array $userGroups
	 * @return array
	 */
	private static function getAllowedPriceTypes(array $userGroups)
	{
		static $priceTypeCache = array();

		Main\Type\Collection::normalizeArrayValuesByInt($userGroups, true);
		if (empty($userGroups))
			return array();

		$cacheKey = 'U'.implode('_', $userGroups);
		if (!isset($priceTypeCache[$cacheKey]))
		{
			$priceTypeCache[$cacheKey] = array();
			$priceIterator = Catalog\GroupAccessTable::getList(array(
				'select' => array('CATALOG_GROUP_ID'),
				'filter' => array('@GROUP_ID' => $userGroups, '=ACCESS' => Catalog\GroupAccessTable::ACCESS_BUY),
				'order' => array('CATALOG_GROUP_ID' => 'ASC')
			));
			while ($priceType = $priceIterator->fetch())
			{
				$priceTypeId = (int)$priceType['CATALOG_GROUP_ID'];
				$priceTypeCache[$cacheKey][$priceTypeId] = $priceTypeId;
				unset($priceTypeId);
			}
			unset($priceType, $priceIterator);
		}

		return $priceTypeCache[$cacheKey];
	}
}