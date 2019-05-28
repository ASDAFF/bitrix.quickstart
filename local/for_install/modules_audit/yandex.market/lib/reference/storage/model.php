<?php

namespace Yandex\Market\Reference\Storage;

use Bitrix\Main;
use Yandex\Market;

Main\Localization\Loc::loadMessages(__FILE__);

abstract class Model
{
	protected static $internalIndex = 0;

	/** @var string */
	protected $internalId = null;
	/** @var array */
	protected $fields;
	/** @var Collection */
	protected $collection;
	/** @var Collection[] */
	protected $childCollection = [];

	public static function getClassName()
	{
		return '\\' . get_called_class();
	}

	/**
	 * Загружаем список объектов по параметрам запроса d7
	 *
	 * @param array $parameters
	 *
	 * @return Model[]
	 * @throws \Bitrix\Main\ArgumentException
	 */
	public static function loadList($parameters = array())
	{
		$result = [];
		$tableClass = static::getDataClass();
		$query = $tableClass::getList($parameters);

		while ($itemData = $query->fetch())
		{
			$result[] = new static($itemData);
		}

		return $result;
	}

	/**
	 * Загружаем объект по ид
	 *
	 * @param $id int
	 *
	 * @return Model
	 * @throws Main\ObjectNotFoundException
	 */
	public static function loadById($id)
	{
		$result = null;
		$tableClass = static::getDataClass();
		$query = $tableClass::getById($id);

		if ($itemData = $query->fetch())
		{
			$result = new static($itemData);
		}
		else
		{
			throw new Main\ObjectNotFoundException(Market\Config::getLang('REFERENCE_STORAGE_MODEL_LOAD_NOT_FOUND'));
		}

		return $result;
	}

	public static function initialize($fields)
	{
		return new static($fields);
	}

	/**
	 * @return String|null
	 */
	public static function getParentReferenceField()
	{
		return null;
	}

	/**
	 * @return Table
	 */
	public static function getDataClass()
	{
		throw new Main\SystemException('not implemented');
	}

	protected function __construct(array $fields = [])
	{
		$this->fields = $fields;
	}

	/**
	 * @return array
	 */
	public function getFields()
	{
		return $this->fields;
	}

	/**
	 * @param $name
	 *
	 * @return bool
	 */
	public function hasField($name)
	{
		return array_key_exists($name, $this->fields);
	}

	/**
	 * @param $name
	 *
	 * @return mixed|null
	 */
	public function getField($name)
	{
		return isset($this->fields[$name]) ? $this->fields[$name] : null;
	}

	/**
	 * @return mixed|null
	 */
	public function getId()
	{
		return $this->getField('ID');
	}

	/**
	 * @return mixed
	 */
	public function getInternalId()
	{
		$id = $this->getId();

		if ($id !== null && $id !== '')
		{
			// nothing
		}
		else if ($this->internalId !== null)
		{
			$id = $this->internalId;
		}
		else
		{
			$id = 'n' . static::$internalIndex;
			$this->internalId = $id;

			++static::$internalIndex;
		}

		return $id;
	}

	/**
	 * @param Collection $collection
	 */
	public function setCollection(Collection $collection)
	{
		$this->collection = $collection;
	}

	/**
	 * @return Collection
	 */
	public function getCollection()
	{
		return $this->collection;
	}

	/**
	 * @param $fieldKey
	 *
	 * @return Collection
	 * @throws \Bitrix\Main\SystemException
	 */
	protected function getChildCollection($fieldKey)
	{
		if (!isset($this->childCollection[$fieldKey]))
		{
			$this->childCollection[$fieldKey] = $this->loadChildCollection($fieldKey);
		}

		return $this->childCollection[$fieldKey];
	}

	protected function loadChildCollection($fieldKey)
	{
		$collectionClassName = static::getChildCollectionReference($fieldKey);
		$result = null;

		if (!isset($collectionClassName)) { throw new Main\SystemException('child reference not found'); }

		if ($this->hasField($fieldKey))
		{
			$dataList = (array)$this->getField($fieldKey);
			$result = $collectionClassName::initialize($this, $dataList);
		}
		else if ($this->getId() > 0)
		{
			$tableClass = static::getDataClass();
			$reference = $tableClass::getReference($this->getId());

			if (!isset($reference[$fieldKey]['LINK'])) { throw new Main\SystemException('child reference not found'); }

			$queryParams = [
				'filter' => $tableClass::makeReferenceLinkFilter($reference[$fieldKey]['LINK'])
			];

			if (isset($reference[$fieldKey]['ORDER']))
			{
				$queryParams['order'] = $reference[$fieldKey]['ORDER'];
			}

			$result = $collectionClassName::load($this, $queryParams);
		}
		else
		{
			$result = new $collectionClassName;
			$result->setParent($this);
		}

		return $result;
	}

	/**
	 * @param $fieldKey
	 *
	 * @return Collection
	 */
	protected function getChildCollectionReference($fieldKey)
	{
		return null;
	}
}