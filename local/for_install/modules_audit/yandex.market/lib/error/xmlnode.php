<?php

namespace Yandex\Market\Error;

use Bitrix\Main;
use Yandex\Market\Config;

Main\Localization\Loc::loadMessages(__FILE__);

class XmlNode extends Base
{
	protected $tagName;
	protected $attributeName;

	public function getUniqueKey()
	{
		return parent::getUniqueKey() . '|' . $this->tagName . '|' . $this->attributeName;
	}

	public function setTagName($tagName)
	{
		$this->tagName = $tagName;
	}

	public function getTagName()
	{
		return $this->tagName;
	}

	public function hasTagName()
	{
		return $this->tagName !== null;
	}

	public function setAttributeName($attributeName)
	{
		$this->attributeName = $attributeName;
	}

	public function getAttributeName()
	{
		return $this->attributeName;
	}

	public function hasAttributeName()
	{
		return $this->attributeName !== null;
	}

	/**
	 * @return string
	 */
	public function getMessage()
	{
		$result = null;

		if ($this->hasAttributeName())
		{
			$result = Config::getLang('ERROR_XMLNODE_ATTRIBUTE', [
				'#ATTRIBUTE_NAME#' => $this->getAttributeName(),
				'#TAG_NAME#' => $this->getTagName(),
				'#MESSAGE#' => $this->message
			]);
		}
		else if ($this->hasTagName())
		{
			$result = Config::getLang('ERROR_XMLNODE_TAG', [
				'#TAG_NAME#' => $this->getTagName(),
				'#MESSAGE#' => $this->message
			]);
		}
		else
		{
			$result = $this->message;
		}

		return $result;
	}
}