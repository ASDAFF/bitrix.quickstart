<?php

namespace Yandex\Market\Type;

use Yandex\Market;

abstract class AbstractType
{
	/**
	 * @param                                               $value
	 * @param array                                         $context
	 * @param \Yandex\Market\Export\Xml\Reference\Node|null $node
	 * @param \Yandex\Market\Result\XmlNode|null            $nodeResult
	 *
	 * @return bool
	 */
	public function validate($value, array $context = [], Market\Export\Xml\Reference\Node $node = null, Market\Result\XmlNode $nodeResult = null)
	{
		return true; // nothing by default
	}

	/**
	 * @param $value
	 * @param $context array
	 * @param $node Market\Export\Xml\Reference\Node|null
	 * @param $nodeResult Market\Result\XmlNode|null
	 *
	 * @return string
	 */
	abstract public function format($value, array $context = [], Market\Export\Xml\Reference\Node $node = null, Market\Result\XmlNode $nodeResult = null);
}