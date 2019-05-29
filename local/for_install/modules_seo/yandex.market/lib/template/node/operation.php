<?php

namespace Yandex\Market\Template\Node;

use Yandex\Market;
use Bitrix\Main;
use Bitrix\Iblock;

if (!Main\Loader::includeModule('iblock'))
{
	throw new Main\SystemException('require module iblock');
	return;
}

class Operation extends Iblock\Template\NodeFunction
{
	/**
	 * @return array
	 */
	public function getParameters()
	{
		return $this->parameters;
	}
}