<?php

namespace Yandex\Market\Component\Data;

use Bitrix\Main;
use Yandex\Market;

class GridList extends Market\Component\Base\GridList
{
	public function prepareComponentParams($params)
	{
		$params['DATA_CLASS_NAME'] = trim($params['DATA_CLASS_NAME']);

		return $params;
	}

	public function getRequiredParams()
	{
		return [
			'DATA_CLASS_NAME'
		];
	}

	public function getDefaultSort()
	{
		return [];
	}

	public function getDefaultFilter()
	{
		return [];
	}

	public function getFields(array $select = [])
	{
		$dataClass = $this->getDataClass();
		$fields = $dataClass::getMapDescription();
		$result = null;

		if (empty($select))
		{
			$result = $fields;
		}
		else
		{
			$result = [];

			foreach ($select as $fieldName)
			{
				if (isset($fields[$fieldName]))
				{
					$result[$fieldName] = $fields[$fieldName];
				}
			}
		}

		return $result;
	}

	public function load(array $queryParameters = [])
	{
		$dataClass = $this->getDataClass();
		$result = [];

        if ($dataClass)
        {
            $index = 0;
            $mapPrimaryToIndex = [];
            $externalSelect = null;
            $primary = $this->getComponentParam('PRIMARY');
            $primaryList = [];
            $isMultiplePrimary = (count($primary) > 1);

            if (isset($queryParameters['filter']))
            {
                $queryParameters['filter'] = $this->normalizeQueryFilter((array)$queryParameters['filter']);
            }

            if (isset($queryParameters['order']))
            {
                $queryParameters['order'] = $this->normalizeQueryOrder((array)$queryParameters['order']);
            }

            if (isset($queryParameters['select']))
            {
                list($scalarSelect, $externalSelect) = $this->normalizeQuerySelect((array)$queryParameters['select']);

                $queryParameters['select'] = $scalarSelect;
            }

            $hasExternalSelect = !empty($externalSelect);

			// row data

            $query = $dataClass::getList($queryParameters);

            while ($item = $query->fetch())
            {
                $result[] = $this->normalizeQueryResult($item);

                if ($hasExternalSelect)
                {
	                $itemPrimary = $isMultiplePrimary ? [] : null;

	                foreach ($primary as $field)
	                {
	                    if ($isMultiplePrimary)
	                    {
	                        $itemPrimary[$field] = $item[$field];
	                    }
	                    else
	                    {
	                        $itemPrimary = $item[$field];
	                    }
	                }

					$primaryList[] = $itemPrimary;

	                if ($isMultiplePrimary)
	                {
	                    $mapPrimaryToIndex[implode(':', $itemPrimary)] = $index;
                    }
                    else
                    {
                        $mapPrimaryToIndex[$itemPrimary] = $index;
                    }
                }

                $index++;
            }

            // external data

            if ($hasExternalSelect && !empty($primaryList))
            {
	            $externalDataList = $dataClass::loadExternalReference($primaryList, $externalSelect);

	            foreach ($externalDataList as $id => $externalData)
	            {
	                if (isset($mapPrimaryToIndex[$id]))
	                {
	                    $index = $mapPrimaryToIndex[$id];

	                    $result[$index] += $externalData;
	                }
	            }
            }
        }

        return $result;
	}

	public function loadTotalCount(array $queryParameters = [])
	{
		$result = null;
		$dataClass = $this->getDataClass();

		if (isset($queryParameters['limit'])) { unset($queryParameters['limit']); }
		if (isset($queryParameters['offset'])) { unset($queryParameters['offset']); }
		if (isset($queryParameters['order'])) { unset($queryParameters['order']); }

		if (isset($queryParameters['filter']))
        {
            $queryParameters['filter'] = $this->normalizeQueryFilter((array)$queryParameters['filter']);
        }

		$queryParameters['select'] = [ 'CNT' ];
		$queryParameters['runtime'] = [
			new Main\Entity\ExpressionField('CNT', 'COUNT(1)')
		];

		$query = $dataClass::getList($queryParameters);

		if ($row = $query->fetch())
		{
			$result = (int)$row['CNT'];
		}

		return $result;
	}

	public function deleteItem($id)
	{
		$dataClass = $this->getDataClass();

		$dataClass::delete($id);
	}

	public function processAjaxAction($action, $data)
	{
		switch ($action)
		{
			case 'delete':
				$this->processDeleteAction($data);
			break;

			default:
				parent::processAjaxAction($action, $data);
			break;
		}
	}

	protected function processDeleteAction($data)
	{
		$idList = (array)$data['ID'];
		$isForAll = !empty($data['IS_ALL']);

		if (empty($idList) && !$isForAll)
		{
			// nothing
		}
		else if ($this->isAllowBatch())
		{
			$dataClass = $this->getDataClass();
			$parameters = [];

			if (!$isForAll)
			{
				$parameters['filter'] = [
					'=ID' => $idList
				];
			}
			else if (!empty($data['FILTER']))
			{
				$parameters['filter'] = $this->normalizeQueryFilter((array)$data['FILTER']);
			}

			$dataClass::deleteBatch($parameters);
		}
		else
		{
			if ($isForAll)
			{
				$idList = [];
				$parameters = [
					'select' => [ 'ID' ]
				];

				if (!empty($data['FILTER']))
				{
					$parameters['filter'] = $this->normalizeQueryFilter((array)$data['FILTER']);
				}

				$items = $this->load($parameters);

				foreach ($items as $item)
				{
					$idList[] = (int)$item['ID'];
				}
			}

			foreach ($idList as $id)
			{
				$this->deleteItem($id);
			}
		}
	}

	protected function isAllowBatch()
	{
		return $this->getComponentParam('ALLOW_BATCH') === 'Y';
	}

	/**
	 * @return Market\Reference\Storage\Table
	 */
	protected function getDataClass()
	{
        return $this->getComponentParam('DATA_CLASS_NAME');
	}

	protected function normalizeQuerySelect(array $select)
	{
		$scalarFields = $this->getScalarFields();
		$scalarFieldsMap = array_flip($scalarFields);
		$linkList = $this->getReferenceFields();
		$scalarResult = [];
		$externalResult = [];

		foreach ($select as $fieldName)
		{
			if (isset($linkList[$fieldName]))
			{
				$externalResult[] = $fieldName;
			}
			else if (!isset($scalarFieldsMap[$fieldName]))
			{
				$scalarResult[$fieldName . '_REF'] = $fieldName . '.ID';
			}
			else
			{
				$scalarResult[] = $fieldName;
			}
		}

		return [ $scalarResult, $externalResult ];
	}

	protected function normalizeQueryFilter(array $filter)
	{
		$scalarFields = $this->getScalarFields();
		$scalarFieldsMap = array_flip($scalarFields);
		$newFilter = $filter;

		foreach ($filter as $filterName => $filterValue)
		{
			if (!is_numeric($filterName))
			{
				$fieldName = $filterName;

				if (preg_match('/^[^A-Za-z]+(.+)$/', $filterName, $match))
				{
					$fieldName = $match[1];
				}

				if (!isset($scalarFieldsMap[$fieldName]) && !preg_match('/\.ID$/', $fieldName))
				{
					$newFilter[$filterName . '.ID'] = $filterValue;
					unset($newFilter[$fieldName]);
				}
			}
		}

		return $newFilter;
	}

	protected function normalizeQueryOrder(array $order)
	{
		$scalarFields = $this->getScalarFields();
		$scalarFieldsMap = array_flip($scalarFields);
		$newOrder = $order;

		foreach ($order as $fieldName => $orderDirection)
		{
			if (!isset($scalarFieldsMap[$fieldName]) && !preg_match('/\.ID$/', $fieldName))
			{
				$newOrder[$fieldName . '.ID'] = $orderDirection;
				unset($newOrder[$fieldName]);
			}
		}

		return $newOrder;
	}

	protected function normalizeQueryResult($item)
	{
		$referenceMarker = '_REF';
		$result = $item;

		foreach ($item as $key => $value)
		{
			$referencePosition = strrpos($key, $referenceMarker);

			if ($referencePosition !== false)
			{
				$keyWithoutReference = substr($key, 0, $referencePosition);

				if ($keyWithoutReference . $referenceMarker === $key)
				{
					$result[$keyWithoutReference] = $value;
					unset($result[$key]);
				}
			}
		}

		return $result;
	}

	protected function getScalarFields()
	{
		$dataClass = $this->getDataClass();

		return $dataClass::getScalarMap();
	}

	protected function getReferenceFields()
	{
		$dataClass = $this->getDataClass();

		return $dataClass::getReference();
	}
}