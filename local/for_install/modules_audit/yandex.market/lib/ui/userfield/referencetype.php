<?php

namespace Yandex\Market\Ui\UserField;

use Bitrix\Main;

class ReferenceType extends \CUserTypeEnum
{
	function GetList($arUserField)
	{
		static $values = null;

		if ($values === null)
		{
			$values = [];

			/** @var Main\Entity\DataManager $dataClass*/
			$dataClass = Main\Entity\Base::normalizeEntityClass($arUserField['SETTINGS']['DATA_CLASS']);

			$query = $dataClass::getList([
				'select' => [
					'ID',
					'NAME'
				]
			]);

			while ($row = $query->fetch())
			{
				$values[] = [
					'ID' => $row['ID'],
					'VALUE' => '[' . $row['ID'] . '] ' . $row['NAME']
				];
			}
		}

		$result = new \CDBResult();
		$result->InitFromArray($values);

		return $result;
	}
}