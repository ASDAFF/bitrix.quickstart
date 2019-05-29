<?php

namespace Yandex\Market\Export\Xml\Reference;

use Bitrix\Main;
use Yandex\Market;

Main\Localization\Loc::loadMessages(__FILE__);

abstract class Node
{
	/** @var array */
	protected $parameters;
	/** @var bool */
	protected $isVisible;
	/** @var bool */
	protected $isRequired;
	/** @var string */
	protected $name;
	/** @var string */
	protected $id;
	/** @var string */
	protected $valueType;
	/** @var int|null */
	protected $maxLength;

	public function __construct(array $parameters = [])
	{
		$this->parameters = $parameters + $this->getDefaultParameters();

		$this->refreshParameters();
	}

	public function getDefaultParameters()
	{
		return [];
	}

	public function extendParameters($parameters)
	{
		$this->parameters = array_merge($this->parameters, $parameters);

		$this->refreshParameters();
	}

	protected function refreshParameters()
	{
		$parameters = $this->parameters;

		$this->name = isset($parameters['name']) ? $parameters['name'] : null; // maybe set in child
		$this->id = isset($parameters['id']) ? $parameters['id'] : $this->name;
		$this->isRequired = !empty($parameters['required']);
		$this->isVisible = !empty($parameters['visible']);
		$this->valueType = !empty($parameters['value_type']) ? $parameters['value_type'] : 'string';
		$this->maxLength = isset($parameters['max_length']) ? (int)$parameters['max_length'] : null;
	}

	abstract public function getLangKey();

	/**
	 * @return string
	 */
	public function getDescription()
	{
		$langKey = static::getLangKey();

		return Market\Config::getLang($langKey . '_DESCRIPTION', null, '');
	}

	/**
	 * Параметры
	 *
	 * @param $key
	 *
	 * @return mixed|null
	 */
	public function getParameter($key)
	{
		return (isset($this->parameters[$key]) ? $this->parameters[$key] : null);
	}

	/**
	 * Показывается ли поле по-умолчанию во вкладке сопоставления полей
	 *
	 * @return bool
	 */
	public function isVisible()
	{
		return $this->isVisible;
	}

	/**
	 * Является обязательным
	 *
	 * @return bool
	 */
	public function isRequired()
	{
		return $this->isRequired;
	}

	/**
	 * Является ли определенным (не может быть изменен в интерфейсе)
	 *
	 * @return bool
	 */
	public function isDefined()
	{
		return ($this->getParameter('defined_value') !== null);
	}

	/**
	 * Источник данных для определенных полей
	 *
	 * @return array|null
	 */
	public function getDefinedSource(array $context = [])
	{
		$result = null;
		$definedValue = $this->getParameter('defined_value');

		if ($definedValue !== null)
		{
			$result = [
				'TYPE' => Market\Export\Entity\Manager::TYPE_TEXT,
				'VALUE' => $definedValue
			];
		}

		return $result;
	}

	/**
	 * Источник данных по умолчанию
	 *
	 * @param $context array
	 *
	 * @return string
	 */
	public function getDefaultSource(array $context = [])
	{
		return Market\Export\Entity\Manager::TYPE_TEXT;
	}

	/**
	 * Список рекомендации для источника данных
	 *
	 * @param $context array
	 *
	 * @return array
	 */
	public function getSourceRecommendation(array $context = [])
	{
		return [];
	}

	/**
	 * Внутренний идентификатор
	 *
	 * @return string
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Название для xml
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Тип значения
	 *
	 * @return mixed|null
	 */
	public function getValueType()
	{
		return $this->valueType;
	}

	/**
	 * Значение по умолчанию
	 *
	 * @param array $context
	 * @param array|null $siblingsValues
	 *
	 * @return mixed|null
	 */
	public function getDefaultValue(array $context = [], $siblingsValues = null)
	{
		return $this->getParameter('default_value');
	}

	/**
	 * Максимальная длина значения
	 *
	 * @return int|null
	 */
	public function getMaxLength()
	{
		return $this->maxLength;
	}

	/**
	 * Валидация значения
	 *
	 * @param                                    $value
	 * @param array                              $context
	 * @param null                               $siblingsValues
	 * @param \Yandex\Market\Result\XmlNode|null $nodeResult
	 * @param array|null                         $settings
	 *
	 * @return bool
	 * @throws \Bitrix\Main\ObjectNotFoundException
	 */
	public function validate($value, array $context, $siblingsValues = null, Market\Result\XmlNode $nodeResult = null, $settings = null)
	{
		if ($value === null || $value === '')
		{
			$result = false;

			if ($nodeResult)
			{
				if ($this->isRequired)
				{
					$nodeResult->registerError(
						Market\Config::getLang('XML_NODE_VALIDATE_EMPTY'),
						Market\Error\XmlNode::XML_NODE_VALIDATE_EMPTY
					);
				}
				else
				{
					$nodeResult->invalidate();
				}
			}
		}
		else
		{
			$valueType = $this->getValueType();
			$typeEntity = Market\Type\Manager::getType($valueType);

			$result = $typeEntity->validate($value, $context, $this, $nodeResult);
		}

		return $result;
	}

	/**
	 * Добавляем xml-элемент к родителю
	 *
	 * @param                                    $value
	 * @param array                              $context
	 * @param \SimpleXMLElement                  $parent
	 * @param Market\Result\XmlNode|null         $nodeResult
	 * @param array|null                         $settings
	 *
	 * @return \SimpleXMLElement|null
	 */
	abstract public function exportNode($value, array $context, \SimpleXMLElement $parent, Market\Result\XmlNode $nodeResult = null, $settings = null);

	/**
	 * Удаляем xml-элемент от родителя
	 *
	 * @param \SimpleXMLElement      $parent
	 * @param \SimpleXMLElement|null $node
	 */
	abstract public function detachNode(\SimpleXMLElement $parent, \SimpleXMLElement $node = null);

	/**
	 * Форматируем значение на основании типа значения
	 *
	 * @param                                    $value
	 * @param                                    $context array
	 * @param Market\Result\XmlNode|null         $nodeResult
	 * @param array|null                         $settings
	 *
	 * @return string
	 */
	protected function formatValue($value, array $context = [], Market\Result\XmlNode $nodeResult = null, $settings = null)
	{
		$valueType = $this->getValueType();
		$typeEntity = Market\Type\Manager::getType($valueType);

		return $typeEntity->format($value, $context, $this, $nodeResult);
	}
}