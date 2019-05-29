<?php

namespace  Yandex\Market\Reference\Storage;

use Bitrix\Main;
use Yandex\Market;

abstract class Table extends Main\Entity\DataManager
{
	public static function getClassName()
	{
		return '\\' . get_called_class();
	}

	public static function createIndexes(Main\DB\Connection $connection)
	{
		// nothing by default
	}

	public static function isValidData($data)
	{
		return true;
	}

	public static function add(array $data)
	{
		return self::addExtended($data);
	}

	public static function addExtended(array $data)
	{
		$reference = static::saveExtractReference($data);
		$addResult = parent::add($data);

		if ($addResult->isSuccess())
		{
			static::saveApplyReference($addResult->getId(), $reference);
			static::onAfterSave($addResult->getId());
		}

		return $addResult;
	}

	public static function updateBatch($parameters, $data)
	{
		$result = new Main\Entity\UpdateResult();
		$query = static::createBatchQuery($parameters);
		$selectSql = $query->getQuery();

		if (strpos($selectSql, 'JOIN') !== false)
		{
			throw new Main\SystemException('not support join in updateBatch');
		}

		try
		{
			$entity = static::getEntity();
			$connection = $entity->getConnection();
			$helper = $connection->getSqlHelper();

			$tableName = $entity->getDBTableName();

			$dataReplacedColumn = static::replaceFieldName($data);
			$update = $helper->prepareUpdate($tableName, $dataReplacedColumn);
			$where = '';

			if (preg_match('/(\sWHERE\s.*)$/is', $selectSql, $match))
			{
				$where = $match[1];
				$where = str_replace($helper->quote($query->getInitAlias()), $tableName, $where);
			}

			$sql = 'UPDATE ' . $tableName . ' SET ' . $update[0] . $where;
			$connection->queryExecute($sql, $update[1]);

			$result->setAffectedRowsCount($connection);
		}
		catch (\Exception $e)
		{
			// check result to avoid warning
			$result->isSuccess();

			throw $e;
		}

		return $result;
	}

	public static function update($primary, array $data)
	{
		return static::updateExtended($primary, $data);
	}

	public static function updateExtended($primary, array $data)
	{
		$reference = static::saveExtractReference($data);

		static::onBeforeSave($primary);

		if (!empty($data))
		{
			$updateResult = parent::update($primary, $data);
		}
		else
		{
			$updateResult = new Main\Entity\UpdateResult();
			$updateResult->setPrimary($primary);
		}

		if ($updateResult->isSuccess())
		{
			static::saveApplyReference($primary, $reference);
			static::onAfterSave($primary);
		}

		return $updateResult;
	}

	public static function deleteBatch($parameters)
	{
		$result = new Main\Entity\DeleteResult();
		$query = static::createBatchQuery($parameters);

		try
		{
			$selectSql = $query->getQuery();

			if (preg_match('/^SELECT\s.*?\s(FROM\s.*)$/si', $selectSql, $match))
			{
				$entity = static::getEntity();
				$connection = $entity->getConnection();
				$helper = $connection->getSqlHelper();
				$sql = 'DELETE ' . $helper->quote($query->getInitAlias()) . ' ' . $match[1];

				$connection->queryExecute($sql);
			}
			else
			{
				throw new Main\SystemException('invalid deleteBatch query');
			}
		}
		catch (\Exception $exception)
		{
			// check result to avoid warning
			$result->isSuccess();

			throw $exception;
		}

		return $result;
	}

	public static function delete($primary)
	{
		return self::deleteExtended($primary);
	}

	public static function deleteExtended($primary)
	{
		static::onBeforeSave($primary);

		$delResult = parent::delete($primary);

		// delete connected data
		if ($delResult->isSuccess())
		{
			static::deleteReference($primary);
		}

		return $delResult;
	}
	
	public static function loadExternalReference($primaryList, $select = null, $isCopy = false)
	{
		$primaryList = (array)$primaryList;
		$result = [];

		if (!empty($primaryList))
		{
			$referenceList = static::getReference($primaryList);

			foreach ($referenceList as $field => $referenceConfig)
			{
				if (empty($select) || in_array($field, $select))
				{
					/** @var Table $referenceTable */
					$referenceTable = $referenceConfig['TABLE'];
					$referenceLinkField = $referenceConfig['LINK_FIELD'];
					$isReferenceLinkFieldMultiple = is_array($referenceLinkField);
					$referenceFilter = static::makeReferenceLinkFilter($referenceConfig['LINK']);
					$rowList = [];

					$queryParams = [
						'filter' => $referenceFilter
					];

					if (isset($referenceConfig['ORDER']))
					{
						$queryParams['order'] = $referenceConfig['ORDER'];
					}

					$queryRows = $referenceTable::getList($queryParams);

					while ($row = $queryRows->fetch())
					{
						if ($isReferenceLinkFieldMultiple || isset($row[$referenceLinkField]))
						{
							$rowList[$row['ID']] = $row;
						}
					}

					// load reference values

					if (!empty($rowList))
					{
						$externalDataList = $referenceTable::loadExternalReference(array_keys($rowList), null, $isCopy);

						foreach ($externalDataList as $rowId => $externalData)
						{
							$rowList[$rowId] += $externalData;
						}
					}

					// build result

					foreach ($rowList as $row)
					{
						$parentPrimary = '';

						if ($isReferenceLinkFieldMultiple)
						{
							foreach ($referenceLinkField as $referenceLinkFieldPart)
							{
								$parentPrimary .= ($parentPrimary === '' ? '' : ':') . $row[$referenceLinkFieldPart];

								if ($isCopy) { unset($row[$referenceLinkFieldPart]); }
							}
						}
						else
						{
							$parentPrimary = $row[$referenceLinkField];

							if ($isCopy) { unset($row[$referenceLinkField]); }
						}

						if (!isset($result[$parentPrimary]))
						{
							$result[$parentPrimary][$field] = [];
						}

						if ($isCopy) { unset($row['ID']); }

						$result[$parentPrimary][$field][] = $row;
					}
				}
			}
		}

		return $result;
	}

	public static function saveExtractReference(array &$data)
	{
		$referenceList = static::getReference();
		$result = [];

		foreach ($referenceList as $referenceField => $reference)
		{
			if (array_key_exists($referenceField, $data))
			{
				$result[$referenceField] = $data[$referenceField];
				unset($data[$referenceField]);
			}
		}

		return $result;
	}

	protected static function saveApplyReference($primary, $fields)
	{
		if (!empty($fields))
		{
			$referenceList = static::getReference($primary);

			foreach ($referenceList as $referenceField => $referenceConfig)
			{
				if (array_key_exists($referenceField, $fields))
				{
					/** @var Table $referenceTable */
					$referenceTable = $referenceConfig['TABLE'];
					$dataList = (array)$fields[$referenceField];
					$isValidDataList = is_array($dataList);
					$foundRowIds = [];

					// update exist and delete not present

					$idToDataKeyMap = [];

					if ($isValidDataList)
					{
						foreach ($dataList as $dataKey => $data)
						{
							if (!empty($data['ID']) && $referenceTable::isValidData($data))
							{
								$rowId = (int)$data['ID'];
								$idToDataKeyMap[$rowId] = $dataKey;
 							}
						}
					}

					$queryExistRows = $referenceTable::getList([
						'filter' => static::makeReferenceLinkFilter($referenceConfig['LINK']),
						'select' => [ 'ID' ]
					]);

					while ($existRow = $queryExistRows->fetch())
					{
						if (isset($idToDataKeyMap[$existRow['ID']]))
						{
							$foundRowIds[$existRow['ID']] = true;

							$dataKey = $idToDataKeyMap[$existRow['ID']];
							$data = $dataList[$dataKey];

							unset($data['ID']);

							$referenceTable::update($existRow['ID'], $data);
						}
						else
						{
							$referenceTable::delete($existRow['ID']);
						}
					}

					// add new

					if ($isValidDataList)
					{
						foreach ($dataList as $dataKey => $data)
						{
							$isValidForAdd = false;

							if (!$referenceTable::isValidData($data))
							{
								// nothing
							}
							else if (!isset($data['ID']))
							{
								$isValidForAdd = true;
							}
							else if (!isset($foundRowIds[$data['ID']]))
							{
								$isValidForAdd = true;
								unset($data['ID']);
							}

							if ($isValidForAdd)
							{
								if (isset($referenceConfig['LINK']))
								{
									$data += $referenceConfig['LINK'];
								}

								$referenceTable::add($data);
 							}
						}
					}
				}
			}
		}
	}

	protected static function deleteReference($primary)
	{
		$referenceList = static::getReference($primary);

		foreach ($referenceList as $referenceField => $referenceConfig)
		{
			/** @var Table $referenceTable */
			$referenceTable = $referenceConfig['TABLE'];

			$queryExistRows = $referenceTable::getList([
				'filter' => static::makeReferenceLinkFilter($referenceConfig['LINK']),
				'select' => [ 'ID' ]
			]);

			while ($existRow = $queryExistRows->fetch())
			{
				$referenceTable::delete($existRow['ID']);
			}
		}
	}

	protected static function onBeforeSave($primary)
	{
		// nothing
	}

	protected static function onAfterSave($primary)
	{
		// nothing
	}

	/**
	 * Ключ = Поле связи
	 * Значение = Масссив LINK_FIELD => Указатель на текущую сущность, LINK => Поля для связи, TABLE => Table::class
	 *
	 * @param int|int[]|null $primary
	 *
	 * @return array
	 */
	public static function getReference($primary = null)
	{
		return [];
	}

	public static function makeReferenceLinkFilter($link)
	{
		$result = [];

		foreach ($link as $field => $value)
		{
			if (!is_numeric($field))
			{
				$result['=' . $field] = $value;
			}
			else
			{
				$result[$field] = $value;
			}
		}

		return $result;
	}

	/**
	 * Описание полей сущности в формате USER_FIELD_MANAGER
	 *
	 * @return array
	 */
	public static function getMapDescription()
	{
		global $USER_FIELD_MANAGER;

		$entity = static::getEntity();
		$referenceList = static::getReference();
		$result = [];

		/** @var Main\Entity\Field $field */
        foreach ($entity->getFields() as $field)
        {
            $fieldName = $field->getName();
            $userField = [];
            $userType = null;
            $userTypeClassName = null;

            if (isset($result[$fieldName])) { continue; } // reference one to one conflict

            switch (true)
            {
	            case ($field instanceof Main\Entity\EnumField): // enum

					$userType = 'enumeration';
					$userTypeClassName = 'Yandex\Market\Ui\UserField\EnumerationType';
					$userField['VALUES'] = [];

					foreach ($field->getValues() as $option)
					{
						$userField['VALUES'][] = [
							'ID' => $option,
							'VALUE' => static::getFieldEnumTitle($fieldName, $option, $field)
						];
					}

	            break;

	            case ($field instanceof Main\Entity\DateField): // date

	                $userType = 'date';

	            break;

	            case ($field instanceof Main\Entity\DatetimeField): // datetime

	                $userType = 'datetime';

	            break;

	            case ($field instanceof Main\Entity\IntegerField): // int

					$userType = 'integer';

	            break;

	            case ($field instanceof Main\Entity\StringField): // string
	            case ($field instanceof Main\Entity\ExpressionField): // expression

					$userType = 'string';

	            break;

	            case ($field instanceof Main\Entity\BooleanField): // boolean

					$userType = 'boolean';
					$userTypeClassName = 'Yandex\Market\Ui\UserField\BooleanType';
					$userField['SETTINGS'] = [
						'DEFAULT_VALUE' => $field->getDefaultValue()
					];

	            break;

	            case ($field instanceof Main\Entity\ReferenceField):

	                $userType = 'enumeration';
					$userTypeClassName = 'Yandex\Market\Ui\UserField\ReferenceType';
					$userField['MULTIPLE'] = isset($referenceList[$fieldName]) ? 'Y' : 'N';
					$userField['SETTINGS']  = [
						'DATA_CLASS' => $field->getRefEntityName()
					];

	            break;
            }

            if (!isset($userType)) { continue; }

            $userField += [
                'USER_TYPE' => $USER_FIELD_MANAGER->GetUserType($userType),
                'FIELD_NAME' => $fieldName,
                'LIST_COLUMN_LABEL' => $field->getTitle(),
                'MANDATORY' => (method_exists($field, 'isRequired') && $field->isRequired() ? 'Y' : 'N'),
                'MULTIPLE' => 'N',
                'EDIT_IN_LIST' => (method_exists($field, 'isAutocomplete') && $field->isAutocomplete() ? 'N' : 'Y')
            ];

            if (isset($userTypeClassName))
            {
                $userField['USER_TYPE']['CLASS_NAME'] = $userTypeClassName;
            }

            $result[$fieldName] = $userField;
        }

        return $result;
	}

	public static function getScalarMap()
	{
		$result = [];
		$map = static::getMap();

		foreach ($map as $field)
		{
			if ($field instanceof Main\Entity\ScalarField || $field instanceof Main\Entity\ExpressionField)
			{
				$result[] = $field->getName();
			}
		}

		return $result;
	}

	public static function getName()
	{
		$langKey = static::getLangKey();

		return Market\Config::getLang($langKey);
	}

	public static function getLangKey()
	{
		return 'UNKNOWN';
	}

	public static function getFieldEnumTitle($fieldName, $optionValue, Main\Entity\Field $field = null)
	{
		$result = null;

		if ($field === null)
		{
			$entity = static::getEntity();
			$field = $entity->getField($fieldName);
		}

		if ($field)
		{
			$fieldEnumLangKey = $field->getLangCode() . '_ENUM_';
			$optionValueLangKey = str_replace(['.', ' ', '-'], '_', $optionValue);
			$optionValueLangKey = strtoupper($optionValueLangKey);

			$result = Main\Localization\Loc::getMessage($fieldEnumLangKey . $optionValueLangKey);
		}

		if ($result === null)
		{
			$result = $optionValue;
		}

		return $result;
	}

	protected static function extractReferenceField($fieldName)
	{
		$result = $fieldName;

		if (preg_match('/(?:this|ref)\.(.+)$/', $fieldName, $match))
		{
			$result = $match[1];
		}

		return $result;
	}

	protected static function createBatchQuery($parameters)
	{
		$query = static::query();

		foreach ($parameters as $param => $value)
		{
			switch($param)
			{
				case 'filter':
					$query->setFilter($value);
				break;

				case 'runtime':
					foreach ($value as $name => $fieldInfo)
					{
						$query->registerRuntimeField($name, $fieldInfo);
					}
				break;

				default:
					throw new Main\ArgumentException("Unknown parameter: ".$param, $param);
				break;
			}
		}

		return $query;
	}
}