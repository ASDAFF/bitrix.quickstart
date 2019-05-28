<?php

namespace Yandex\Market\Export\Xml\Tag;

use Yandex\Market;
use Bitrix\Main;

Main\Localization\Loc::loadMessages(__FILE__);

class Url extends Base
{
	public function getDefaultParameters()
	{
		return [
			'name' => 'url',
			'value_type' => Market\Type\Manager::TYPE_URL
		];
	}

	public function getSourceRecommendation(array $context = [])
	{
		$result = [
			[
				'TYPE' => Market\Export\Entity\Manager::TYPE_IBLOCK_ELEMENT_FIELD,
				'FIELD' => 'DETAIL_PAGE_URL',
			]
		];

		if (isset($context['OFFER_IBLOCK_ID']))
		{
			$result[] = [
				'TYPE' => Market\Export\Entity\Manager::TYPE_IBLOCK_OFFER_FIELD,
				'FIELD' => 'DETAIL_PAGE_URL',
			];
		}

		return $result;
	}

	protected function formatValue($value, array $context = [], Market\Result\XmlNode $nodeResult = null, $settings = null)
	{
		$hasValue = ($value !== null && $value !== '');

		if ($hasValue && $settings !== null)
		{
			$queryParams = [];
			$hasQueryParams = false;
			$utmFields = [
				'utm_source' => 'UTM_SOURCE',
				'utm_medium' => 'UTM_MEDIUM',
				'utm_campaign' => 'UTM_CAMPAIGN',
			];

			foreach ($utmFields as $utmRequest => $utmField)
			{
				if (isset($settings[$utmField]) && is_string($settings[$utmField]))
				{
					$utmValue = trim($settings[$utmField]);

					if ($utmValue !== '')
					{
						$hasQueryParams = true;
						$queryParams[$utmRequest] = $utmValue;
					}
				}
			}

			if ($hasQueryParams)
			{
				$value .= (strpos($value, '?') === false ? '?' : '&') . http_build_query($queryParams);
			}
		}

		return parent::formatValue($value, $context, $nodeResult, $settings);
	}

	public function getSettingsDescription()
	{
		$langKey = $this->getLangKey();

		$result = [
			'UTM_SOURCE' => [
				'TITLE' => Market\Config::getLang($langKey . '_SETTINGS_UTM_SOURCE'),
				'TYPE' => 'param'
			],
			'UTM_MEDIUM' => [
				'TITLE' => Market\Config::getLang($langKey . '_SETTINGS_UTM_MEDIUM'),
				'TYPE' => 'param'
			],
			'UTM_CAMPAIGN' => [
				'TITLE' => Market\Config::getLang($langKey . '_SETTINGS_UTM_CAMPAIGN'),
				'TYPE' => 'param'
			]
		];

		return $result;
	}
}
