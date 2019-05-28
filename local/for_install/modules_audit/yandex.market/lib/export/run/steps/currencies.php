<?php

namespace Yandex\Market\Export\Run\Steps;

use Bitrix\Main;
use Bitrix\Currency;
use Yandex\Market;

class Currencies extends Base
{
	public function getName()
	{
		return 'currency';
	}

	public function run($action, $offset = null)
	{
		$result = new Market\Result\Step();
		$context = $this->getContext();
		$currencyList = $this->getCurrencyList($context);

		$tagValuesList = $this->buildTagValuesList([], $currencyList);

		$this->setRunAction($action);
		$this->writeData($tagValuesList, $currencyList, $context);

		return $result;
	}

	public function getFormatTag(Market\Export\Xml\Format\Reference\Base $format)
	{
		return $format->getCurrency();
	}

	public function getFormatTagParentName(Market\Export\Xml\Format\Reference\Base $format)
	{
		return $format->getCurrencyParentName();
	}

	protected function getStorageDataClass()
	{
		return Market\Export\Run\Storage\CurrencyTable::getClassName();
	}

	protected function getDataLogEntityType()
	{
		return Market\Logger\Table::ENTITY_TYPE_EXPORT_RUN_CURRENCY;
	}

	protected function buildTagValues($tagDescription, $currencyData)
	{
		$result = new Market\Result\XmlValue();

		$result->addTag('currency', '', [
			'id' => $currencyData['CURRENCY'],
			'rate' => $currencyData['RATE']
		]);

		return $result;
	}

	protected function getCurrencyList($context)
	{
		$currencyIds = $this->getUsedCurrencyIds($context);
		$result = [];

		if (!empty($currencyIds))
		{
			$rates = $this->getRates($currencyIds);
			$type = $this->getType();

			// format used values

			foreach ($currencyIds as $currencyId)
			{
				$currencyFormatted = $type->format($currencyId);

				$result[$currencyFormatted] = [
					'CURRENCY' => $currencyFormatted,
					'RATE' => isset($rates['RATES'][$currencyId]) ? $rates['RATES'][$currencyId] : $this->getAutomaticRate($currencyFormatted)
				];
			}

			// push base currency, if not set

			$baseCurrency = ($rates['BASE'] !== null ? $rates['BASE'] : $type->getDefaultBase());

			if (!in_array($baseCurrency, $currencyIds))
			{
				$baseCurrencyFormatted = $type->format($baseCurrency);

				$result[$baseCurrencyFormatted] = [
					'CURRENCY' => $baseCurrencyFormatted,
					'RATE' => 1
				];
			}
		}

		return $result;
	}

	protected function getUsedCurrencyIds($context)
	{
		$result = [];

		$query = Market\Export\Run\Storage\OfferTable::getList([
			'group' => [ 'CURRENCY_ID' ],
			'select' => [ 'CURRENCY_ID' ],
			'filter' => [
				'=SETUP_ID' => $context['SETUP_ID'],
				'=STATUS' => static::STORAGE_STATUS_SUCCESS
			]
		]);

		while ($row = $query->fetch())
		{
			$currencyId = trim($row['CURRENCY_ID']);

			if (strlen($currencyId) > 0)
			{
				$result[] = $currencyId;
			}
		}

		return $result;
	}

	protected function getRates($currencyIds)
	{
		$result = [
			'BASE' => null,
			'RATES' => []
		];
		$leftRatesCount = count($currencyIds);
		$methods = [
			'getCurrencyModuleRates',
			'getAutomaticRates'
		];

		foreach ($methods as $method)
		{
			$rateResult = $this->{$method}($currencyIds, $result['BASE']);

			if ($result['BASE'] === null && isset($rateResult['BASE']))
			{
				$result['BASE'] = $rateResult['BASE'];
			}

			foreach ($rateResult['RATES'] as $currency => $rate)
			{
				if (!isset($result['RATES'][$currency]))
				{
					$result['RATES'][$currency] = $rate;
					$leftRatesCount--;
				}
			}

			if ($leftRatesCount <= 0)
			{
				break;
			}
		}

		return $result;
	}

	protected function getCurrencyModuleRates($usedCurrencyList, $baseCurrency = null)
	{
		$result = [
			'BASE' => null,
			'RATES' => []
		];

		if (Main\Loader::includeModule('currency'))
		{
			$currencyList = [];
			$usedCurrencyListMap = array_flip($usedCurrencyList);

			// query currency list and detect base

			$query = Currency\CurrencyTable::getList([
				'select' => [
					'CURRENCY',
					'BASE'
				],
				'order' => [
					'SORT' => 'asc'
				]
			]);

			while ($currency = $query->fetch())
			{
				if (isset($usedCurrencyListMap[$currency['CURRENCY']]))
				{
					$currencyList[] = $currency['CURRENCY'];
				}

				if ($currency['BASE'] === 'Y' && $baseCurrency === null)
				{
					$baseCurrency = $currency['CURRENCY'];
				}
			}

			$baseCurrency = $this->normalizeBaseCurrency($baseCurrency, $currencyList);

			if (isset($baseCurrency))
			{
				$result['BASE'] = $baseCurrency;

				foreach ($currencyList as $currency)
				{
					if ($currency === $baseCurrency)
					{
						$result['RATES'][$currency] = 1;
					}
					else
					{
						$currencyRate = \CCurrencyRates::GetConvertFactor($currency, $baseCurrency);

						if ($currencyRate > 0) // invalid result
						{
							$result['RATES'][$currency] = $currencyRate;
						}
					}
				}
			}
		}

		return $result;
	}

	protected function getAutomaticRates($usedCurrencyList, $baseCurrency = null)
	{
		$baseCurrency = $this->normalizeBaseCurrency($baseCurrency, $usedCurrencyList);
		$result = [
			'BASE' => $baseCurrency,
			'RATES' => []
		];

		foreach ($usedCurrencyList as $currency)
		{
			$currencyRate = null;

			if ($currency === $baseCurrency)
			{
				$currencyRate = 1;
			}
			else
			{
				$currencyRate = $this->getAutomaticRate($currency);
			}

			$result['RATES'][$currency] = $currencyRate;
		}

		return $result;
	}

	protected function getAutomaticRate($currency)
	{
		return 'CB';
	}

	protected function normalizeBaseCurrency($baseCurrency, $currencyList)
	{
		$type = $this->getType();
		$result = null;

		if ($type->isBase($baseCurrency))
		{
			$result = $baseCurrency;
		}
		else
		{
			foreach ($currencyList as $currency)
			{
				if ($type->isBase($currency))
				{
					$result = $currency;
					break;
				}
			}
		}

		return $result;
	}

	/**
	 * @return Market\Type\CurrencyType
	 */
	protected function getType()
	{
		return Market\Type\Manager::getType(
			Market\Type\Manager::TYPE_CURRENCY
		);
	}
}