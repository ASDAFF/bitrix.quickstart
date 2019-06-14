<?php

namespace Yandex\Market\Ui\UserField;

class EnumerationType extends \CUserTypeEnum
{
	function GetList($arUserField)
	{
		$result = new \CDBResult();
		$result->InitFromArray($arUserField['VALUES']);

		return $result;
	}
}