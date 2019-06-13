<?php

namespace Yandex\Market\Export\Xml\Format;

use Bitrix\Main;
use Yandex\Market;

Main\Localization\Loc::loadMessages(__FILE__);

class Manager
{
	const EXPORT_SERVICE_YANDEX_MARKET = 'Yandex.Market';
	const EXPORT_SERVICE_BERU_RU = 'Beru.ru';

	const EXPORT_FORMAT_SIMPLE = 'simple';
	const EXPORT_FORMAT_VENDOR_MODEL = 'vendor.model';
	const EXPORT_FORMAT_BOOK = 'book';
	const EXPORT_FORMAT_AUDIOBOOK = 'audiobook';
	const EXPORT_FORMAT_ARTIST_TITLE = 'artist.title';
	const EXPORT_FORMAT_EVENT_TICKET = 'event-ticket';
	const EXPORT_FORMAT_MEDICINE = 'medicine';
	const EXPORT_FORMAT_TOUR = 'tour';
	const EXPORT_FORMAT_ALCO = 'alco';

	public static function getServiceList()
	{
		return [
			static::EXPORT_SERVICE_YANDEX_MARKET,
			static::EXPORT_SERVICE_BERU_RU
		];
	}

	public static function getServiceTitle($service)
	{
		$serviceLangKey = str_replace(['.', ' ', '-'], '_', $service);
		$serviceLangKey = strtoupper($serviceLangKey);

		return Market\Config::getLang('EXPORT_XML_FORMAT_SERVICE_' . $serviceLangKey);
	}

	public static function getTypeTitle($type)
	{
		$typeLangKey = str_replace(['.', ' ', '-'], '_', $type);
		$typeLangKey = strtoupper($typeLangKey);

		return Market\Config::getLang('EXPORT_XML_FORMAT_TYPE_' . $typeLangKey, null, $type);
	}

	public static function getTypeList($service)
	{
		$result = null;

		switch ($service)
		{
			case static::EXPORT_SERVICE_YANDEX_MARKET:
				$result = [
					static::EXPORT_FORMAT_VENDOR_MODEL,
					static::EXPORT_FORMAT_SIMPLE,
					static::EXPORT_FORMAT_BOOK,
					static::EXPORT_FORMAT_AUDIOBOOK,
					static::EXPORT_FORMAT_ARTIST_TITLE,
					static::EXPORT_FORMAT_EVENT_TICKET,
					static::EXPORT_FORMAT_MEDICINE,
					static::EXPORT_FORMAT_TOUR,
					static::EXPORT_FORMAT_ALCO
				];
			break;

			case static::EXPORT_SERVICE_BERU_RU:
				$result = [
					static::EXPORT_FORMAT_VENDOR_MODEL
				];
			break;
		}

		return $result;
	}

	/**
	 * @param $type string
	 *
	 * @return Reference\Base
	 */
	public static function getEntity($service, $type)
	{
		$result = null;

		$className = __NAMESPACE__ . '\\' . str_replace('.', '', $service) . '\\' . str_replace(['.', '-'], '', $type);

		if (class_exists($className))
		{
			$result = new $className;
		}
		else
		{
			throw new Main\ObjectNotFoundException('format not found');
		}

		return $result;
	}
}