<?php

namespace Yandex\Market\Template\Node;

use Bitrix\Iblock\Template\Entity;
use Yandex\Market;
use Bitrix\Main;
use Bitrix\Iblock;

if (!Main\Loader::includeModule('iblock'))
{
	throw new Main\SystemException('require module iblock');
}
else if (!class_exists('\Bitrix\Iblock\Template\NodeEntityField')) // force load sibling class, cause original class not registered in iblock loader
{
	throw new Main\SystemException('cant load iblock template engine');
}

class Field extends Iblock\Template\NodeBase
{
	protected $sourceType;
	protected $sourceField;

	function __construct($sourceType = '', $sourceField = '')
	{
		$this->sourceType = $sourceType;
		$this->sourceField = $sourceField;
	}

	public function process(Entity\Base $entity)
	{
		return $entity->getField($this->sourceType, $this->sourceField);
	}

	/**
	 * @return string
	 */
	public function getSourceType()
	{
		return $this->sourceType;
	}

	/**
	 * @return string
	 */
	public function getSourceField()
	{
		return $this->sourceField;
	}
}