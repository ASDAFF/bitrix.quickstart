<?php

namespace Yandex\Market\Template\Entity;

use Yandex\Market;
use Bitrix\Main;
use Bitrix\Iblock;

if (!Main\Loader::includeModule('iblock'))
{
	throw new Main\SystemException('require module iblock');
	return;
}

class SourceValue extends Iblock\Template\Entity\Base
{
	public function getField($sourceType, $sourceField = '')
	{
		$result = null;

		if (isset($this->fields[$sourceType][$sourceField]))
		{
			$result = $this->fields[$sourceType][$sourceField];
		}

		return $result;
	}
}