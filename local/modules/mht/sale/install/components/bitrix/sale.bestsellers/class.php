<?php
use \Bitrix\Main\Localization\Loc as Loc;
use \Bitrix\Main\SystemException as SystemException;
use \Bitrix\Main\Loader as Loader;
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

CBitrixComponent::includeComponentClass("bitrix:catalog.viewed.products");

class CSaleBestsellersComponent extends CCatalogViewedProductsComponent
{
	/**
	 * @param $params
	 * @override
	 * @return array
	 */
	public function onPrepareComponentParams($params)
	{
		$params = parent::onPrepareComponentParams($params);

		if(!isset($params["CACHE_TIME"]))
			$params["CACHE_TIME"] = 86400;

		$params["DETAIL_URL"] = trim($params["DETAIL_URL"]);

		if(isset($params["BY"]) && is_array($params["BY"]))
		{
			if(count($params["BY"]))
			{
				$params["BY"] = array_values($params["BY"]);
				$params["BY"] = $params["BY"][0];
			}
			else
				$params["BY"] = "AMOUNT";
		}

		if(!isset($params["BY"]) || !strlen(trim($params["BY"])))
			$params["BY"] = "AMOUNT";


		if(isset($params["PERIOD"]))
		{
			if(is_array($params["PERIOD"]))
			{
				if(count($params["PERIOD"]))
				{
					$params["PERIOD"] = array_values($params["PERIOD"]);
					$params["PERIOD"] = $params["PERIOD"][0];
				}
				else
					$params["PERIOD"] = 0;
			}
			else
			{
				$params["PERIOD"] = (int)$params["PERIOD"];
				if($params["PERIOD"] < 0)
					$params["PERIOD"] = 0;
			}
		}
		else
		{
			$params["PERIOD"] = 0;
		}

		if(!isset($params['FILTER']) || empty($params['FILTER']) || !is_array($params['FILTER']))
			$params['FILTER'] = array();

		if(Loader::includeModule("sale"))
		{
			$statuses = array(
				"CANCELED",
				"ALLOW_DELIVERY",
				"PAYED",
				"DEDUCTED"
			);
			$saleStatusIterator = CSaleStatus::GetList(array("SORT" => "ASC"), array("LID" => LANGUAGE_ID), false, false, array("ID"));
			while ($row = $saleStatusIterator->Fetch())
			{
				$statuses[] = $row['ID'];
			}

			foreach($params['FILTER'] as $key => $status)
			{
				if(!in_array($status, $statuses))
					unset($params['FILTER'][$key]);
			}
		}

		return $params;
	}


	/**
	 * @override
	 * @return bool
	 */
	protected function extractDataFromCache()
	{
		if($this->arParams['CACHE_TYPE'] == 'N')
			return false;

		global $USER;
		return !($this->StartResultCache(false, $USER->GetGroups()));
	}

	protected function putDataToCache()
	{

	}

	protected function abortDataCache()
	{
		$this->AbortResultCache();
	}

	/**
	 * @override
	 * @return void
	 */
	protected function formatResult()
	{
		parent::formatResult();
		$this->arResult['PERIOD'] 	= $this->arParams['PERIOD'];
		$this->arResult['BY'] 		= $this->arParams['BY'];
	}


	/**
	 * Returns orders filter for CSaleProduct::GetBestSellerList method.
	 * @return mixed[]
	 */
	protected function getOrdersFilter()
	{
		if (!empty($this->arParams['FILTER']))
		{
			$filter = (defined('SITE_ID') && !SITE_ID ? array('=LID' => SITE_ID) : array());
			$subFilter = array("LOGIC" => "OR");

			$statuses = array(
				"CANCELED" => true,
				"ALLOW_DELIVERY" => true,
				"PAYED" => true,
				"DEDUCTED" => true
			);
			if ($this->arParams['PERIOD'] > 0)
			{
				$date = ConvertTimeStamp(AddToTimeStamp(array("DD" => "-" . $this->arParams['PERIOD'])));
				if (!empty($date))
				{
					foreach ($this->arParams['FILTER'] as &$field)
					{
						if (isset($statuses[$field]))
						{
							$subFilter[] = array(
								">=DATE_{$field}" => $date,
								"={$field}" => "Y"
							);
						}
						else
						{
							$subFilter[] = array(
								"=STATUS_ID" => $field,
								">=DATE_UPDATE" => $date,
							);
						}
					}
					unset($field);
				}
			}
			else
			{
				foreach ($this->arParams['FILTER'] as &$field)
				{
					if (isset($statuses[$field]))
					{
						$subFilter[] = array(
							"={$field}" => "Y"
						);
					}
					else
					{
						$subFilter[] = array(
							"=STATUS_ID" => $field,
						);
					}
				}
				unset($field);
			}
			$filter[] = $subFilter;
			return $filter;
		}

		return array();
	}

	/**
	 * @override
	 * @return integer[]
	 */
	protected function getProductIds()
	{
		$ordersfilter = $this->getOrdersFilter();
		if (!empty($ordersfilter))
		{
			$productIds = array();
			$productIterator = CSaleProduct::GetBestSellerList(
				$this->arParams["BY"],
				array(),
				$ordersfilter,
				$this->arParams["PAGE_ELEMENT_COUNT"]
			);
			while($product = $productIterator->fetch())
			{
				$productIds[] = $product['PRODUCT_ID'];
			}

			return $productIds;
		}

		return array();
	}


	/**
	 * @override
	 * @throws Exception
	 */
	protected function checkModules()
	{
		parent::checkModules();
		if(!$this->isSale)
			throw new SystemException(Loc::getMessage("CVP_SALE_MODULE_NOT_INSTALLED"));
	}
}