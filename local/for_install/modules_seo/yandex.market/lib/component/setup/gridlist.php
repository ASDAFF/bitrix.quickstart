<?php

namespace Yandex\Market\Component\Setup;

use Bitrix\Main;
use Yandex\Market;

class GridList extends Market\Component\Model\GridList
{
	protected function getReferenceFields()
	{
		$result = parent::getReferenceFields();
		$result['IBLOCK'] = [];

		return $result;
	}
}