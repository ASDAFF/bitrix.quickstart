<?php

namespace Yandex\Market\Type;

use Yandex\Market;

class HtmlType extends StringType
{
	public function format($value, array $context = [], Market\Export\Xml\Reference\Node $node = null, Market\Result\XmlNode $nodeResult = null)
	{
		$result = strip_tags($value, '<h3><br><ul><ol><li><p>');
		$maxLength = $node ? $node->getMaxLength() : null;

		if (strpos($result, '<') !== false) // has tags
		{
			$result = $this->stripTagAttributes($result);

			if ($maxLength !== null)
			{
				$textParser = new \CTextParser();
				$suffixLength = 3;

				$result = $textParser->html_cut($result, $maxLength - $suffixLength);
			}

			if ($nodeResult !== null)
			{
				$cdata = $this->makeCData($result);

				$result = $nodeResult->addReplace($cdata);
			}
			else
			{
				$result = trim($result);
				$result = str_replace('&', '&amp;', $result);
			}
		}
		else
		{
			$result = trim($result);
			$result = str_replace('&', '&amp;', $result);

			if ($maxLength !== null)
			{
				$result = $this->truncateText($result, $maxLength);
			}
		}

		return $result;
	}

	protected function makeCData($contents)
	{
		$contents = str_replace(
			['<![CDATA[', ']]>'],
			['&lt;![CDATA[', ']]&gt;'],
			$contents
		);

		return '<![CDATA[' . PHP_EOL .  $contents . PHP_EOL . ']]>';
	}

	protected function stripTagAttributes($contents)
	{
		return preg_replace('/<([a-z][a-z0-9]*) [^>]+?(\/?>)/i', '<$1$2', $contents);
	}
}