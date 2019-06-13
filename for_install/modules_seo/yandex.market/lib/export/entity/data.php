<?php

namespace Yandex\Market\Export\Entity;

use Bitrix\Main;
use Yandex\Market;

Main\Localization\Loc::loadMessages(__FILE__);

class Data
{
	const TYPE_STRING = 'S';
	const TYPE_NUMBER = 'N';
	const TYPE_ENUM = 'L';
	const TYPE_IBLOCK_ELEMENT = 'E';
	const TYPE_IBLOCK_SECTION = 'G';
	const TYPE_SERVICE_CATEGORY = 'SERVICE_CATEGORY';
	const TYPE_FILE = 'F';
	const TYPE_BOOLEAN = 'BOOLEAN';
	const TYPE_URL = 'URL';
	const TYPE_DATE = 'DATE';
	const TYPE_DATETIME = 'DATETIME';
	const TYPE_CURRENCY = 'CURRENCY';

	const COMPARE_EQUAL = 'equal';
	const COMPARE_NOT_EQUAL = 'notEqual';
	const COMPARE_MORE = 'more';
	const COMPARE_MORE_OR_EQUAL = 'moreOrEqual';
	const COMPARE_LESS = 'less';
	const COMPARE_LESS_OR_EQUAL = 'lessOrEqual';
	const COMPARE_LIKE = 'like';
	const COMPARE_NOT_LIKE = 'notLike';
	const COMPARE_IN = 'in';
	const COMPARE_NOT_IN = 'notIn';
	const COMPARE_EMPTY = 'empty';
	const COMPARE_NOT_EMPTY = 'notEmpty';

	const SPECIAL_VALUE_EMPTY = 'special:empty';

	public static function getType($type)
	{
		$typeList = static::getTypeList();

		if (!isset($typeList[$type]))
		{
			$langPrefix = static::getLangPrefix();

			throw new Main\SystemException(
				Market\Config::getLang($langPrefix . 'TYPE_UNDEFINED')
			);
		}

		return $typeList[$type];
	}
	
	public static function getTypeList()
	{
		$stringDataTypes = [
			Market\Type\Manager::TYPE_STRING,
			Market\Type\Manager::TYPE_NUMBER,
			Market\Type\Manager::TYPE_VAT,
			Market\Type\Manager::TYPE_HTML,
			Market\Type\Manager::TYPE_DATE,
			Market\Type\Manager::TYPE_DATEPERIOD,
			Market\Type\Manager::TYPE_CURRENCY,
			Market\Type\Manager::TYPE_BOOLEAN
		];

		return [
			static::TYPE_STRING => [
				'DATA' => $stringDataTypes,
				'COMPARE' => [
					static::COMPARE_EQUAL,
					static::COMPARE_NOT_EQUAL,
					static::COMPARE_LIKE,
					static::COMPARE_NOT_LIKE,
					static::COMPARE_EMPTY,
					static::COMPARE_NOT_EMPTY
				]
			],
			static::TYPE_NUMBER => [
				'DATA' => [
					Market\Type\Manager::TYPE_NUMBER,
					Market\Type\Manager::TYPE_VAT,
					Market\Type\Manager::TYPE_BOOLEAN,
					Market\Type\Manager::TYPE_STRING
				],
				'COMPARE' => [
					static::COMPARE_EQUAL,
					static::COMPARE_NOT_EQUAL,
					static::COMPARE_MORE,
					static::COMPARE_MORE_OR_EQUAL,
					static::COMPARE_LESS,
					static::COMPARE_LESS_OR_EQUAL
				]
			],
			static::TYPE_ENUM => [
				'DATA' => $stringDataTypes,
				'COMPARE' => [
					static::COMPARE_IN,
					static::COMPARE_NOT_IN,
					static::COMPARE_EMPTY,
					static::COMPARE_NOT_EMPTY
				]
			],
			static::TYPE_IBLOCK_ELEMENT => [
				'DATA' => $stringDataTypes,
				'COMPARE' => [
					static::COMPARE_IN,
					static::COMPARE_NOT_IN,
					static::COMPARE_EMPTY,
					static::COMPARE_NOT_EMPTY
				]
			],
			static::TYPE_IBLOCK_SECTION => [
				'DATA' => [
					Market\Type\Manager::TYPE_CATEGORY
				],
				'COMPARE' => [
					static::COMPARE_IN,
					static::COMPARE_NOT_IN,
					static::COMPARE_EMPTY,
					static::COMPARE_NOT_EMPTY
				]
			],
			static::TYPE_SERVICE_CATEGORY => [
				'DATA' => [
					Market\Type\Manager::TYPE_CATEGORY
				],
				'COMPARE' => [
					static::COMPARE_IN,
					static::COMPARE_NOT_IN,
					static::COMPARE_EMPTY,
					static::COMPARE_NOT_EMPTY
				]
			],
			static::TYPE_FILE => [
				'DATA' => [
					Market\Type\Manager::TYPE_FILE
				],
				'COMPARE' => [
					static::COMPARE_EQUAL,
					static::COMPARE_NOT_EQUAL
				]
			],
			static::TYPE_BOOLEAN => [
				'DATA' => $stringDataTypes,
				'COMPARE' => [
					static::COMPARE_EQUAL,
					static::COMPARE_NOT_EQUAL
				]
			],
			static::TYPE_URL => [
				'DATA' => [
					Market\Type\Manager::TYPE_URL
				],
				'COMPARE' => [
					static::COMPARE_EQUAL,
					static::COMPARE_NOT_EQUAL,
					static::COMPARE_LIKE,
					static::COMPARE_NOT_LIKE,
				]
			],
			static::TYPE_DATE => [
				'DATA' => [
					Market\Type\Manager::TYPE_DATE,
					Market\Type\Manager::TYPE_STRING
				],
				'COMPARE' => [
					static::COMPARE_EQUAL,
					static::COMPARE_NOT_EQUAL,
					static::COMPARE_MORE,
					static::COMPARE_MORE_OR_EQUAL,
					static::COMPARE_LESS,
					static::COMPARE_LESS_OR_EQUAL
				]
			],
			static::TYPE_DATETIME => [
				'DATA' => [
					Market\Type\Manager::TYPE_DATE,
					Market\Type\Manager::TYPE_STRING
				],
				'COMPARE' => [
					static::COMPARE_EQUAL,
					static::COMPARE_NOT_EQUAL,
					static::COMPARE_MORE,
					static::COMPARE_MORE_OR_EQUAL,
					static::COMPARE_LESS,
					static::COMPARE_LESS_OR_EQUAL
				]
			],
			static::TYPE_CURRENCY => [
				'DATA' => [
					Market\Type\Manager::TYPE_CURRENCY
				],
				'COMPARE' => [
					static::COMPARE_IN,
					static::COMPARE_NOT_IN
				]
			]
		];
	}

	public static function getDataTypes($dataType)
	{
		$typeList = static::getTypeList();
		$result = [];

		foreach ($typeList as $type => $config)
		{
			if (!isset($config['DATA']) || in_array($dataType, $config['DATA']))
			{
				$result[] = $type;
			}
		}

		return $result;
	}

	public static function getCompare($compare)
	{
		$compareList = static::getCompareList();

		if (!isset($compareList[$compare]))
		{
			$langPrefix = static::getLangPrefix();

			throw new Main\SystemException(
				Market\Config::getLang($langPrefix . 'COMPARE_UNDEFINED')
			);
		}

		return $compareList[$compare];
	}

	public static function getCompareTypes($compare)
	{
		$typeList = static::getTypeList();
		$result = [];

		foreach ($typeList as $type => $data)
		{
			foreach ($data['COMPARE'] as $typeCompare)
			{
				if ($typeCompare === $compare)
				{
					$result[] = $type;
					break;
				}
			}
		}

		return $result;
	}

	public static function getCompareList()
	{
		return [
			static::COMPARE_EQUAL => [
				'QUERY' => '=',
				'MULTIPLE' => false
			],
			static::COMPARE_NOT_EQUAL => [
				'QUERY' => '!',
				'MULTIPLE' => false
			],
			static::COMPARE_MORE => [
				'QUERY' => '>',
				'MULTIPLE' => false
			],
			static::COMPARE_MORE_OR_EQUAL => [
				'QUERY' => '>=',
				'MULTIPLE' => false
			],
			static::COMPARE_LESS => [
				'QUERY' => '<',
				'MULTIPLE' => false
			],
			static::COMPARE_LESS_OR_EQUAL => [
				'QUERY' => '<=',
				'MULTIPLE' => false
			],
			static::COMPARE_LIKE => [
				'QUERY' => '%',
				'MULTIPLE' => false
			],
			static::COMPARE_NOT_LIKE => [
				'QUERY' => '!%',
				'MULTIPLE' => false
			],
			static::COMPARE_IN => [
				'QUERY' => '',
				'MULTIPLE' => true
			],
			static::COMPARE_NOT_IN => [
				'QUERY' => '!',
				'MULTIPLE' => true
			],
			static::COMPARE_EMPTY => [
				'QUERY' => '',
				'MULTIPLE' => false,
				'DEFINED' => static::SPECIAL_VALUE_EMPTY
			],
			static::COMPARE_NOT_EMPTY => [
				'QUERY' => '!',
				'MULTIPLE' => false,
				'DEFINED' => static::SPECIAL_VALUE_EMPTY
			]
		];
	}

	public static function getCompareTitle($compare)
	{
		$langPrefix = static::getLangPrefix();

		return Market\Config::getLang($langPrefix . 'COMPARE_' . strtoupper($compare));
	}

	public static function convertUserTypeToDataType($userTypeId)
	{
		$map = [
			'string' => static::TYPE_STRING,
			'integer' => static::TYPE_NUMBER,
			'double' => static::TYPE_NUMBER,
			'enumeration' => static::TYPE_ENUM,
			'hlblock' => static::TYPE_ENUM,
			'url' => static::TYPE_URL,
			'file' => static::TYPE_FILE,
			'boolean' => static::TYPE_BOOLEAN,
			'date' => static::TYPE_DATE,
			'datetime' => static::TYPE_DATETIME,
			'iblock_element' => static::TYPE_IBLOCK_ELEMENT,
			'iblock_section' => static::TYPE_IBLOCK_SECTION,
			'ym_service_category' => static::TYPE_SERVICE_CATEGORY
		];

		return isset($map[$userTypeId]) ? $map[$userTypeId] : static::TYPE_STRING;
	}

	protected static function getLangPrefix()
	{
		return 'EXPORT_ENTITY_DATA_';
	}
}