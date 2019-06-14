<?php

namespace Yandex\Market\Export\Param;

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;
use Yandex\Market;

Loc::loadMessages(__FILE__);

class Model extends Market\Reference\Storage\Model
{
	/** @var Market\Export\ParamValue\Collection */
	protected $valueCollection;

	/**
	 * Название класса таблицы
	 *
	 * @return Table
	 */
	public static function getDataClass()
	{
		return Table::getClassName();
	}

	public function getSettings()
	{
		$fieldValue = $this->getField('SETTINGS');

		return is_array($fieldValue) ? $fieldValue : null;
	}

	/**
	 * @return \Yandex\Market\Export\ParamValue\Collection
	 */
	public function getValueCollection()
	{
		return $this->getChildCollection('PARAM_VALUE');
	}

	protected function getChildCollectionReference($fieldKey)
	{
		$result = null;

		switch ($fieldKey)
		{
			case 'PARAM_VALUE':
				$result = Market\Export\ParamValue\Collection::getClassName();
			break;
		}

		return $result;
	}
}