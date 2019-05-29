<?php

namespace Yandex\Market\Export\Xml\Attribute;

use Yandex\Market;
use Bitrix\Main;

Main\Localization\Loc::loadMessages(__FILE__);

class Base extends Market\Export\Xml\Reference\Node
{
	public function getLangKey()
	{
		$nameLang = str_replace(['.', ' ', '-'], '_', $this->id);
		$nameLang = strtoupper($nameLang);

		return 'EXPORT_ATTRIBUTE_' . $nameLang;
	}

	/**
	 * Добавляем аттрибут xml-элемента
	 *
	 * @param                                    $value
	 * @param array                              $context
	 * @param \SimpleXMLElement                  $parent
	 * @param Market\Result\XmlNode|null         $nodeResult
	 * @param array|null                         $settings
	 *
	 * @return null
	 */
	public function exportNode($value, array $context, \SimpleXMLElement $parent, Market\Result\XmlNode $nodeResult = null, $settings = null)
	{
		$attributeName = $this->name;
		$attributeExport = $this->formatValue($value, $context, $nodeResult, $settings);

		@$parent->addAttribute($attributeName, $attributeExport); // sanitize encoding warning (no convert, performance issue)

		return null;
	}

	/**
	 * Удаляем аттрибут xml-элемента
	 *
	 * @param \SimpleXMLElement      $parent
	 * @param \SimpleXMLElement|null $node
	 */
	public function detachNode(\SimpleXMLElement $parent, \SimpleXMLElement $node = null)
	{
		$attributeName = $this->name;
		$attributes = $parent->attributes();

		if (isset($attributes[$attributeName]))
		{
			unset($attributes[$attributeName]);
		}
	}
}
