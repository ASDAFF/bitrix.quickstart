<?php

namespace Yandex\Market\Result;

use Yandex\Market;
use Bitrix\Main;

class XmlNode extends Base
{
	protected static $replaceIndex = 0;
	protected static $replaceMarker = 'YANDEX_MARKET_XMLNODE_REPLACE_';

	protected $xmlElement;
	protected $xmlContents;
	protected $replaces = [];
	protected $errorTagName = null;
	protected $errorAttributeName = null;

	public function setErrorTagName($name)
	{
		$this->errorTagName = $name;
		$this->errorAttributeName = null;
	}

	public function setErrorAttributeName($name)
	{
		$this->errorAttributeName = $name;
	}

	public function registerError($errorMessage, $errorCode = null)
	{
		if ($this->isErrorStrict)
		{
			$this->addError($this->createError($errorMessage, $errorCode));
		}
		else
		{
			$this->addWarning($this->createError($errorMessage, $errorCode));
		}
	}

	protected function createError($errorMessage, $errorCode = null)
	{
		$result = new Market\Error\XmlNode($errorMessage, $errorCode);

		if ($this->errorTagName !== null)
		{
			$result->setTagName($this->errorTagName);
		}

		if ($this->errorAttributeName !== null)
		{
			$result->setAttributeName($this->errorAttributeName);
		}

		return $result;
	}

	public function addReplace($text, $index = null)
	{
		if ($index === null)
		{
			$index = static::$replaceIndex++;
		}

		$this->replaces[$index] = $text;

		return static::$replaceMarker . $index;
	}

	public function getReplaces()
	{
		return $this->replaces;
	}

	public function setXmlElement(\SimpleXMLElement $xmlElement)
	{
		$this->xmlElement = $xmlElement;
		$this->xmlContents = null; // invalidate contents
	}

	/**
	 * @return \SimpleXMLElement|null
	 */
	public function getXmlElement()
	{
		return $this->xmlElement;
	}

	public function invalidateXmlContents()
	{
		$this->xmlContents = null;
	}

	public function getXmlContents()
	{
		if ($this->xmlContents === null && $this->xmlElement)
		{
			$contents = $this->xmlElement->asXML();

			foreach ($this->replaces as $index => $replace)
			{
				$contents = str_replace(static::$replaceMarker . $index, $replace, $contents);
			}

			$this->xmlContents = $contents;
		}

		return $this->xmlContents;
	}
}