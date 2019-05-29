<?php

namespace Yandex\Market\Export\Entity;

use Bitrix\Main;
use Yandex\Market;

class Manager
{
	const TYPE_IBLOCK_ELEMENT_FIELD = 'iblock_element_field';
	const TYPE_IBLOCK_ELEMENT_PROPERTY = 'iblock_element_property';
	const TYPE_IBLOCK_ELEMENT_SEO = 'iblock_element_seo';
	const TYPE_IBLOCK_OFFER_FIELD = 'iblock_offer_field';
	const TYPE_IBLOCK_OFFER_PROPERTY = 'iblock_offer_property';
	const TYPE_IBLOCK_OFFER_SEO = 'iblock_offer_seo';
	const TYPE_IBLOCK_SECTION = 'iblock_section';
	const TYPE_CATALOG_PRICE = 'catalog_price';
	const TYPE_CATALOG_PRODUCT = 'catalog_product';
	const TYPE_CATALOG_STORE = 'catalog_store';
	const TYPE_TEXT = 'text';
	const TYPE_TEMPLATE = 'template';

	protected static $entityCache = [];
	protected static $customEntityList;

	/**
	 * @param $type
	 *
	 * @return Reference\Source
	 * @throws Main\ObjectNotFoundException
	 */
	public static function getSource($type)
	{
		return static::getTypeInstance($type, 'Source');
	}

	public static function getSourceTypeList()
	{
		$result = static::getDefaultTypes();
		$customTypes = static::getCustomEntityList();

		foreach ($customTypes as $customType => $customData)
		{
			$result[] = $customType;
		}

		return $result;
	}

	public static function getEvent($type)
	{
		return static::getTypeInstance($type, 'Event');
	}

	/**
	 * @param $type
	 * @param $className
	 *
	 * @return Reference\Source|Reference\Event
	 * @throws \Bitrix\Main\ObjectNotFoundException
	 */
	protected static function getTypeInstance($type, $className)
	{
		$result = null;
		$cacheKey = $type . ':' . $className;

		if (isset(static::$entityCache[$cacheKey]))
		{
			$result = static::$entityCache[$cacheKey];
		}
		else
		{
			$className = static::getTypeClassName($type, $className);

			if (!class_exists($className))
			{
				throw new Main\ObjectNotFoundException('source not found ' . $type);
			}

			$result = new $className;
			$result->setType($type);

			static::$entityCache[$cacheKey] = $result;
		}

		return $result;
	}

	protected static function getTypeClassName($type, $className)
	{
		$customEntityList = static::getCustomEntityList();
		$result = null;

		if (isset($customEntityList[$type]))
		{
			$result = $customEntityList[$type][$className];
		}
		else
		{
			$namespace = static::getTypeNamespace($type);
			$result = $namespace . '\\' . $className;
		}

		return $result;
	}

	protected static function getTypeNamespace($type)
	{
		$parts = explode('_', $type);

		return __NAMESPACE__ . '\\' . implode('\\', $parts);
	}

	protected static function getCustomEntityList()
	{
		if (!isset(static::$customEntityList))
		{
			static::$customEntityList = static::loadCustomEntityList();
		}

		return static::$customEntityList;
	}

	protected static function loadCustomEntityList()
	{
		$result = [];

		$event = new Main\Event(Market\Config::getModuleName(), 'onExportEntityTypeBuildList');
		$event->send();

		foreach ($event->getResults() as $eventResult)
		{
			$eventData = $eventResult->getParameters();

			if (isset($eventData['TYPE']))
			{
				if (
					!isset($eventData['SOURCE_CLASS_NAME']) // is required
					|| !is_subclass_of($eventData['SOURCE_CLASS_NAME'], 'Yandex\Market\Export\Entity\Reference\Source') // must be child of reference
				)
				{
					throw new Main\ArgumentOutOfRangeException('invalid source class');
				}

				if (
					!isset($eventData['EVENT_CLASS_NAME']) // is required
					|| !is_subclass_of($eventData['EVENT_CLASS_NAME'], 'Yandex\Market\Export\Entity\Reference\Event') // must be child of reference
				)
				{
					throw new Main\ArgumentOutOfRangeException('invalid event class');
				}

				$result[$eventData['TYPE']] = [
					'Source' => $eventData['SOURCE_CLASS_NAME'],
					'Event' => $eventData['EVENT_CLASS_NAME']
				];
			}
		}

		return $result;
	}

	protected static function getDefaultTypes()
	{
		return [
			static::TYPE_IBLOCK_ELEMENT_FIELD,
			static::TYPE_IBLOCK_ELEMENT_PROPERTY,
			static::TYPE_IBLOCK_ELEMENT_SEO,
			static::TYPE_IBLOCK_OFFER_FIELD,
			static::TYPE_IBLOCK_OFFER_PROPERTY,
			static::TYPE_IBLOCK_OFFER_SEO,
			static::TYPE_IBLOCK_SECTION,
			static::TYPE_CATALOG_PRICE,
			static::TYPE_CATALOG_PRODUCT,
			static::TYPE_CATALOG_STORE,
			static::TYPE_TEXT,
			static::TYPE_TEMPLATE,
		];
	}
}