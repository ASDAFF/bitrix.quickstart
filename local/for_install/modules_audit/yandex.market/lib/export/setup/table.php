<?php

namespace Yandex\Market\Export\Setup;

use Bitrix\Main;
use Yandex\Market;

Main\Localization\Loc::loadMessages(__FILE__);

class Table extends Market\Reference\Storage\Table
{
	public static function getTableName()
	{
		return 'yamarket_export_setup';
	}

	public static function getUfId()
	{
		return 'YAMARKET_EXPORT_SETUP';
	}

	public static function getMap()
	{
		return [
			new Main\Entity\IntegerField('ID', [
				'autocomplete' => true,
				'primary' => true
			]),
			new Main\Entity\StringField('NAME', [
				'required' => true
			]),
			new Main\Entity\StringField('DOMAIN', [
				'required' => true
			]),
			new Main\Entity\BooleanField('HTTPS', [
				'values' => ['0', '1']
			]),
			new Main\Entity\BooleanField('ENABLE_AUTO_DISCOUNTS', [
				'values' => ['0', '1'],
				'default_value' => '1'
			]),
			new Main\Entity\BooleanField('AUTOUPDATE', [
				'values' => ['0', '1'],
				'default_value' => '1'
			]),
			new Main\Entity\IntegerField('REFRESH_PERIOD'),
			new Main\Entity\StringField('EXPORT_SERVICE', [
				'required' => true,
				'size' => 20
			]),
			new Main\Entity\StringField('EXPORT_FORMAT', [
				'required' => true,
				'size' => 20
			]),
			new Main\Entity\StringField('FILE_NAME', [
				'required' => true,
				'format' => '/^[0-9A-Za-z-_.]+$/',
				'size' => 20
			]),
			new Main\Entity\StringField('SALES_NOTES', [
				'size' => 50
			]),
			new Main\Entity\ReferenceField('IBLOCK_LINK', Market\Export\IblockLink\Table::getClassName(), [
				'=this.ID' => 'ref.SETUP_ID'
			]),
			new Main\Entity\ReferenceField('IBLOCK', 'Bitrix\Iblock\Iblock', [
				'=this.IBLOCK_LINK.IBLOCK_ID' => 'ref.ID',
			]),
			new Main\Entity\ReferenceField('DELIVERY', Market\Export\Delivery\Table::getClassName(), [
				'=this.ID' => 'ref.ENTITY_ID',
				'=ref.ENTITY_TYPE' => ['?', Market\Export\Delivery\Table::ENTITY_TYPE_SETUP],
			]),
			new Main\Entity\TextField('SHOP_DATA', [
				'serialized' => true
			])
		];
	}

	public static function getMapDescription()
	{
		global $USER_FIELD_MANAGER;

		$result = parent::getMapDescription();

		// iblock

		if (isset($result['IBLOCK']))
		{
			$result['IBLOCK']['MANDATORY'] = 'Y';
			$result['IBLOCK']['MULTIPLE'] = 'Y';
			$result['IBLOCK']['USER_TYPE']['CLASS_NAME'] = 'Yandex\Market\Ui\UserField\IblockType';
		}

		// file name

		if (isset($result['FILE_NAME']))
		{
			$result['FILE_NAME']['USER_TYPE']['CLASS_NAME'] = 'Yandex\Market\Ui\UserField\ExportFileType';
		}

		// refresh period

		if (isset($result['REFRESH_PERIOD']))
		{
			$result['REFRESH_PERIOD']['EDIT_IN_LIST'] = (Market\Utils::isAgentUseCron() ? 'Y' : 'N');
			$result['REFRESH_PERIOD']['USER_TYPE'] = $USER_FIELD_MANAGER->GetUserType('enumeration');
			$result['REFRESH_PERIOD']['USER_TYPE']['CLASS_NAME'] = 'Yandex\Market\Ui\UserField\EnumerationType';
			$result['REFRESH_PERIOD']['VALUES'] = [];
			$refreshPeriodVariants = [
				604800, // week
				259200, // three days
				86400, // one day
				43200, // half day
				21600, // six hours
				10800, // three hours
				7200, // two hours
				3600 // one hour
			];

			foreach ($refreshPeriodVariants as $refreshPeriodVariant)
			{
				$result['REFRESH_PERIOD']['VALUES'][] = [
					'ID' => $refreshPeriodVariant,
					'VALUE' => static::getFieldEnumTitle('REFRESH_PERIOD', $refreshPeriodVariant)
				];
			}
		}

		// export service

		if (isset($result['EXPORT_SERVICE']))
		{
			$result['EXPORT_SERVICE']['USER_TYPE'] = $USER_FIELD_MANAGER->GetUserType('enumeration');
			$result['EXPORT_SERVICE']['USER_TYPE']['CLASS_NAME'] = 'Yandex\Market\Ui\UserField\EnumerationType';
			$result['EXPORT_SERVICE']['VALUES'] = [];

			$serviceList = Market\Export\Xml\Format\Manager::getServiceList();

			foreach ($serviceList as $service)
			{
				$result['EXPORT_SERVICE']['VALUES'][] = [
					'ID' => $service,
					'VALUE' => Market\Export\Xml\Format\Manager::getServiceTitle($service)
				];
			}
		}

		// export format

		if (isset($result['EXPORT_FORMAT']))
		{
			$result['EXPORT_FORMAT']['USER_TYPE'] = $USER_FIELD_MANAGER->GetUserType('enumeration');
			$result['EXPORT_FORMAT']['USER_TYPE']['CLASS_NAME'] = 'Yandex\Market\Ui\UserField\EnumerationType';
			$result['EXPORT_FORMAT']['VALUES'] = [];

			$serviceList = Market\Export\Xml\Format\Manager::getServiceList();
			$usedTypeList = [];

			foreach ($serviceList as $service)
			{
				$serviceTypeList = Market\Export\Xml\Format\Manager::getTypeList($service);

				foreach ($serviceTypeList as $type)
				{
					if (!isset($usedTypeList[$type]))
					{
						$usedTypeList[$type] = true;

						$result['EXPORT_FORMAT']['VALUES'][] = [
							'ID' => $type,
							'VALUE' => $type
						];
					}
				}
			}
		}

		return $result;
	}

	/**
	 * Ключ = Поле связи
	 * Значение = Масссив FILTER => Фильтр, LINK => Поля для связи
	 *
	 * @param int|int[]|null $primary
	 *
	 * @return array
	 */
	public static function getReference($primary = null)
	{
		return [
			'IBLOCK_LINK' => [
				'TABLE' => Market\Export\IblockLink\Table::getClassName(),
				'LINK_FIELD' => 'SETUP_ID',
				'LINK' => [
					'SETUP_ID' => $primary,
				],
			],
			'DELIVERY' => [
				'TABLE' => Market\Export\Delivery\Table::getClassName(),
				'LINK_FIELD' => 'ENTITY_ID',
				'LINK' => [
					'ENTITY_TYPE' => Market\Export\Delivery\Table::ENTITY_TYPE_SETUP,
					'ENTITY_ID' => $primary,
				],
			],
		];
	}

	public static function loadExternalReference($setupIds, $select = null, $isCopy = false)
	{
		$result = parent::loadExternalReference($setupIds, $select, $isCopy);

		if (!empty($setupIds))
		{
			$referenceMap = [
				'IBLOCK' => 'loadExternalReferenceIblock',
			];

			foreach ($referenceMap as $field => $method)
			{
				if (empty($select) || in_array($field, $select))
				{
					$referenceDataList = static::$method($setupIds);

					foreach ($referenceDataList as $setupId => $referenceValue)
					{
						if (!isset($result[$setupId]))
						{
							$result[$setupId] = [];
						}

						$result[$setupId][$field] = $referenceValue;
					}
				}
			}
		}

		return $result;
	}

	protected static function loadExternalReferenceIblock($setupIds)
	{
		$result = [];

		// load row data

		$query = Market\Export\IblockLink\Table::getList([
			'filter' => [
				'=SETUP_ID' => $setupIds,
			],
			'select' => [
				'ID',
				'IBLOCK_ID',
				'SETUP_ID',
			],
		]);

		while ($row = $query->fetch())
		{
			if (!isset($result[$row['SETUP_ID']]))
			{
				$result[$row['SETUP_ID']] = [];
			}

			$result[$row['SETUP_ID']][$row['ID']] = $row['IBLOCK_ID'];
		}

		return $result;
	}

	public static function saveExtractReference(array &$data)
	{
		$result = parent::saveExtractReference($data);

		if (array_key_exists('IBLOCK', $data))
		{
			unset($data['IBLOCK']);
		}

		return $result;
	}

	public static function deleteReference($primary)
	{
		parent::deleteReference($primary);

		// run storage

		$runDataClassList = [
			Market\Export\Run\Storage\CategoryTable::getClassName(),
			Market\Export\Run\Storage\CurrencyTable::getClassName(),
			Market\Export\Run\Storage\OfferTable::getClassName(),
		];

		foreach ($runDataClassList as $runDataClass)
		{
			$runDataClass::deleteBatch([
				'filter' => [
					'=SETUP_ID' => $primary
				]
			]);
		}

		// clear log

		Market\Logger\Table::deleteBatch([
			'filter' => [
				'=ENTITY_TYPE' => [
					Market\Logger\Table::ENTITY_TYPE_EXPORT_RUN_ROOT,
					Market\Logger\Table::ENTITY_TYPE_EXPORT_RUN_OFFER,
					Market\Logger\Table::ENTITY_TYPE_EXPORT_RUN_CATEGORY,
					Market\Logger\Table::ENTITY_TYPE_EXPORT_RUN_CURRENCY,
				],
				'=ENTITY_PARENT' => $primary
			]
		]);
	}

	protected static function onBeforeSave($primary)
	{
		$model = Model::loadById($primary);

		$model->onBeforeSave();
	}

	protected static function onAfterSave($primary)
	{
		$model = Model::loadById($primary);

		$model->onAfterSave();
	}
}
