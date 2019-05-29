<?php

namespace Yandex\Market\Type;

use Yandex\Market;
use Bitrix\Main;

Main\Localization\Loc::loadMessages(__FILE__);

class BooleanType extends AbstractType
{
	protected $negativeValues;

	public function format($value, array $context = [], Market\Export\Xml\Reference\Node $node = null, Market\Result\XmlNode $nodeResult = null)
	{
		$result = 'false';

		if (!empty($value))
		{
			if ($value === 'N')
			{
				// nothing
			}
			else if ($value === 'Y')
			{
				$result = 'true';
			}
			else
			{
				$value = trim(strtoupper($value));

				if ($this->negativeValues === null)
				{
					$this->negativeValues = $this->loadNegativeValues();
				}

				if (!isset($this->negativeValues[$value]))
				{
					$result = 'true';
				}
			}
		}

		return $result;
	}

	public function loadNegativeValues()
	{
		$messages = ['NEGATIVE_RU', 'NEGATIVE_EN'];
		$result = [
			'N' => true,
			'FALSE' => true
		];

		foreach ($messages as $message)
		{
			$message = Market\Config::getLang('TYPE_BOOLEAN_' . $message);

			$result[$message] = true;
		}

		return $result;
	}
}