<?php

namespace Api\Core\Iblock;

use \Bitrix\Main,
	 Bitrix\Main\Loader,
	 Bitrix\Main\Error,
	 Bitrix\Main\ErrorCollection,
	 Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

abstract class Element extends Base
{
	public function __construct($component = null)
	{
		parent::__construct($component);
		$this->errorCollection = new ErrorCollection();
	}

	/**
	 * Processing parameters unique to catalog.element component.
	 *
	 * @param array $params		Component parameters.
	 * @return array
	 */
	public function onPrepareComponentParams($params)
	{
		$params = parent::onPrepareComponentParams($params);

		//Параметры профиля
		if($params['PROFILE'])
			$this->profile = (array)$params['PROFILE'];

		$this->setDefaultParams();
		$this->setDefaultStorage();

		return $params;
	}
}