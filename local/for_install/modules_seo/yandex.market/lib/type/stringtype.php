<?php

namespace Yandex\Market\Type;

use Yandex\Market;
use Bitrix\Main;

Main\Localization\Loc::loadMessages(__FILE__);

class StringType extends AbstractType
{
	public function format($value, array $context = [], Market\Export\Xml\Reference\Node $node = null, Market\Result\XmlNode $nodeResult = null)
	{
		$result = strip_tags(str_replace('&', '&amp;', $value));
		$maxLength = $node ? $node->getMaxLength() : null;

		if ($result !== ' ') // TODO resolve conflict with self-closed offers and categories
		{
			$result = trim($result);
		}

		if ($maxLength !== null)
		{
			$result = $this->truncateText($result, $maxLength);
		}

		return $result;
	}

	protected function truncateText($text, $maxLength)
	{
		$result = $text;

		if ($this->getStringLength($result) > $maxLength)
		{
			$suffix = Market\Config::getLang('TYPE_STRING_TRUNCATE_SUFFIX');
			$suffixLength = 1;

			$result = $this->getSubstring($result, 0, $maxLength - $suffixLength);
			$result = rtrim($result, '.') . $suffix;
		}

		return $result;
	}

	protected function getStringLength($text)
	{
		if (\function_exists('mb_strlen'))
		{
			$result = mb_strlen($text, LANG_CHARSET);
		}
		else
		{
			$result = strlen($text);
		}

		return $result;
	}

	protected function getSubstring($text, $from, $length = null)
	{
		if (\function_exists('mb_substr'))
		{
			$result = mb_substr($text, $from, $length, LANG_CHARSET);
		}
		else
		{
			$result = substr($text, $from, $length);
		}

		return $result;
	}
}