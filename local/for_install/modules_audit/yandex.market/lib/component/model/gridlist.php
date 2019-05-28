<?php

namespace Yandex\Market\Component\Model;

use Bitrix\Main;
use Yandex\Market;

class GridList extends Market\Component\Data\GridList
{
	public function prepareComponentParams($params)
	{
		$params['MODEL_CLASS_NAME'] = trim($params['MODEL_CLASS_NAME']);

		return $params;
	}

	public function getRequiredParams()
	{
		return [
			'MODEL_CLASS_NAME'
		];
	}

	/**
	 * @return Market\Reference\Storage\Model
	 */
	protected function getModelClass()
	{
		return $this->getComponentParam('MODEL_CLASS_NAME');
	}

	/**
	 * @return Market\Reference\Storage\Table
	 */
	protected function getDataClass()
	{
        $modelClass = $this->getModelClass();

        return $modelClass::getDataClass();
	}
}