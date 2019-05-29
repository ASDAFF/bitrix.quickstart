<?php

namespace Yandex\Market\Export\Run\Helper;

use Yandex\Market;

class ExcludeFilter
{
	/** @var \Yandex\Market\Reference\Storage\Table */
	protected $dataClass;

	public $strField;
	public $arFilter;
	public $sSelect;
	public $sFrom;
	public $sWhere;

	/**
	 * ExcludeFilter constructor.
	 *
	 * @param $dataClass Market\Reference\Storage\Table
	 * @param $field string
	 * @param $filter array|null
	 */
	public function __construct($dataClass, $field, $filter = null)
	{
		$this->dataClass = $dataClass;
		$this->strField = $field;
		$this->arFilter = (array)$filter;
	}

	/**
	 * Новая версия CIBlockElement::SubQuery
	 *
	 * @param $select
	 * @param $filter
	 * @param $group
	 * @param $order
	 *
	 * @return string
	 */
	public function prepareSql($select, $filter, $group, $order)
	{
		$dataClass = $this->dataClass;
		$query = $dataClass::query();

		$query->setSelect($select);

		if (!empty($filter))
		{
			$query->setFilter($filter);
		}

		$sql = $query->getQuery();

		$this->sFrom = $this->extractSqlFrom($sql);
		$this->sSelect = $this->extractSqlSelect($sql);
		$this->sWhere = $this->extractSqlWhere($sql);

		return $sql;
	}

	/**
	 * Старая версия CIBlockElement::SubQuery
	 *
	 * @param $field string
	 * @param $operationType string
	 *
	 * @return string
	 * */
	public function _sql_in($field, $operationType)
	{
		$sql = $this->prepareSql([$this->strField], $this->arFilter, null, null);

		return $field . (substr($operationType, 0, 1) == 'N' ? ' NOT' : '') . ' IN (' . $sql . ')';
	}

	protected function extractSqlSelect($sql)
	{
		$result = null;

		if (preg_match('/SELECT\s(.*?)\sFROM\s/is', $sql, $match))
		{
			$result = trim($match[1]);
		}

		return $result;
	}

	protected function extractSqlFrom($sql)
	{
		$result = null;

		if (preg_match('/\sFROM\s(.*?)(\sWHERE\s|\sGROUP BY\s|\sHAVING\s|\sORDER BY\s|$)/is', $sql, $match))
		{
			$result = trim($match[1]);
		}

		return $result;
	}

	protected function extractSqlWhere($sql)
	{
		$result = '';

		if (preg_match('/\sWHERE\s(.*?)(\sGROUP BY\s|\sHAVING\s|\sORDER BY\s|$)/is', $sql, $match))
		{
			$result = 'AND ' . trim($match[1]);
		}

		return $result;
	}
}