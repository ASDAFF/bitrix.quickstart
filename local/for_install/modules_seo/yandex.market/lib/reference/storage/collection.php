<?php

namespace Yandex\Market\Reference\Storage;

use Bitrix\Main;
use Bitrix\Sale;

abstract class Collection implements \ArrayAccess, \Countable, \IteratorAggregate
{
	/** @var Model[] */
	protected $collection = [];
	/** @var Model */
	protected $parent;

	public static function getClassName()
	{
		return '\\' . get_called_class();
	}

	/**
	 * Ссылка на класс модели
	 *
	 * @return Model
	 */
	abstract public static function getItemReference();

	/**
	 * Загружаем коллекцию для родительской сущности
	 *
	 * @param \Yandex\Market\Reference\Storage\Model $model
	 * @param array $filter
	 *
	 * @return \Yandex\Market\Reference\Storage\Collection
	 * @throws \Bitrix\Main\SystemException
	 */
	public static function load(Model $parent, $filter)
	{
		$collection = new static();
		$collection->setParent($parent);

		if ($parent->getId() > 0)
		{
			$modelClassName = static::getItemReference();

			if (!isset($modelClassName)) { throw new Main\SystemException('reference item not defined'); }

			$modelList = $modelClassName::loadList($filter);
			/** @var Model $model */
			foreach ($modelList as $model)
			{
				$model->setCollection($collection);
				$collection->addItem($model);
			}
		}

		return $collection;
	}

	public static function initialize(Model $parent, $dataList)
	{
		$collection = new static();
		$collection->setParent($parent);

		$modelClassName = static::getItemReference();

		if (!isset($modelClassName)) { throw new Main\SystemException('reference item not defined'); }

		/** @var Model $model */
		foreach ($dataList as $data)
		{
			$model = $modelClassName::initialize($data);

			$model->setCollection($collection);
			$collection->addItem($model);
		}

		return $collection;
	}

	public function getParent()
	{
		return $this->parent;
	}

	public function setParent(Model $model)
	{
		$this->parent = $model;
	}

	public function addItem(Model $model)
	{
		$this->collection[] = $model;
	}

	/*
	 * Array iterator
	 * */
	public function getIterator()
	{
		return new \ArrayIterator($this->collection);
	}

	/**
	 * Whether a offset exists
	 */
	public function offsetExists($offset)
	{
		return isset($this->collection[$offset]) || array_key_exists($offset, $this->collection);
	}

	/**
	 * Offset to retrieve
	 */
	public function offsetGet($offset)
	{
		if (isset($this->collection[$offset]) || array_key_exists($offset, $this->collection))
		{
			return $this->collection[$offset];
		}

		return null;
	}

	/**
	 * Offset to set
	 */
	public function offsetSet($offset, $value)
	{
		if($offset === null)
		{
			$this->collection[] = $value;
		}
		else
		{
			$this->collection[$offset] = $value;
		}
	}

	/**
	 * Offset to unset
	 */
	public function offsetUnset($offset)
	{
		unset($this->collection[$offset]);
	}

	/**
	 * Count elements of an object
	 */
	public function count()
	{
		return count($this->collection);
	}

	/**
	 * Return the current element
	 */
	public function current()
	{
		return current($this->collection);
	}

	/**
	 * Move forward to next element
	 */
	public function next()
	{
		return next($this->collection);
	}

	/**
	 * Return the key of the current element
	 */
	public function key()
	{
		return key($this->collection);
	}

	/**
	 * Checks if current position is valid
	 */
	public function valid()
	{
		$key = $this->key();
		return $key !== null;
	}

	/**
	 * Rewind the Iterator to the first element
	 */
	public function rewind()
	{
		return reset($this->collection);
	}
}