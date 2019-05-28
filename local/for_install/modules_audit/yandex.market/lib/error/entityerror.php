<?php

namespace Yandex\Market\Error;

use Bitrix\Main;

class EntityError extends Main\Entity\EntityError
{
	protected $customData;

	public function __construct($message, $code = 'BX_ERROR', $customData = null)
	{
		parent::__construct($message, $code);

		$this->customData = $customData;
	}

	public function getCustomData()
	{
		return $this->customData;
	}
}