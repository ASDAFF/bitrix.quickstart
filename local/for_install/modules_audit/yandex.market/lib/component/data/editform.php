<?php

namespace Yandex\Market\Component\Data;

use Bitrix\Main;
use Yandex\Market;

class EditForm extends Market\Component\Base\EditForm
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

	public function modifyRequest($request)
	{
		return $request;
	}

	public function getFields(array $select = [], $item = null)
	{
        $dataClass = $this->getDataClass();

		return $this->loadTableFields($dataClass, $select, $item);
    }

    protected function loadTableFields($dataClass, $select, $row)
    {
        /** @var \Yandex\Market\Reference\Storage\Table $dataClass */
        $tableFields = $dataClass::getMapDescription();
        $referenceSelectList = [];
        $result = [];

        if (empty($select)) { $select = array_keys($tableFields); }

        foreach ($select as $fieldKey)
        {
            if (isset($tableFields[$fieldKey]))
            {
                $result[$fieldKey] = $tableFields[$fieldKey];
            }
            else if ($fieldKeyDotPosition = strpos($fieldKey, '.')) // reference
            {
                $fieldKeyTablePart = substr($fieldKey, 0, $fieldKeyDotPosition);
                $fieldKeyReferencePart = substr($fieldKey, $fieldKeyDotPosition + 1);

                if (!isset($referenceSelectList[$fieldKeyTablePart]))
                {
                    $referenceSelectList[$fieldKeyTablePart] = [];
                }

                $referenceSelectList[$fieldKeyTablePart][] = $fieldKeyReferencePart;
            }
        }

        if (!empty($referenceSelectList))
        {
	        /** @var \Bitrix\Main\Entity\Field[] $tableColumns */
	        $tableReferenceList = $dataClass::getReference();

	        foreach ($referenceSelectList as $columnKey => $referenceSelect)
	        {
	            if (isset($tableReferenceList[$columnKey]) && !empty($row[$columnKey]))
	            {
	                $rowValues = $row[$columnKey];
	                $tableReference = $tableReferenceList[$columnKey];

		            foreach ($rowValues as $rowValueIndex => $rowValue)
		            {
		                /** @var \Yandex\Market\Reference\Storage\Table $tableReferenceDataClass */
		                $tableReferenceDataClass = $tableReference['TABLE'];
		            	$referenceFields = $this->loadTableFields($tableReferenceDataClass, $referenceSelect, $rowValue);
		            	$referenceLinks = $tableReferenceDataClass::getReference();

		            	$parent = $rowValue;

                        foreach ($referenceLinks as $referenceLinkKey => $referenceLink)
                        {
                            if (isset($parent[$referenceLinkKey]))
	                        {
	                            unset($parent[$referenceLinkKey]);
	                        }
                        }

		                foreach ($referenceFields as $referenceFieldKey => $referenceField)
		                {
		                    $referenceFieldInputName = $referenceField['FIELD_NAME'];

		                    if (preg_match('/^([^[]+)(.*)$/', $referenceFieldInputName, $matches))
		                    {
		                        $referenceFieldInputName = '[' . $matches[1] . ']' . $matches[2];
		                    }

		                    $referenceFieldInputName = $columnKey . '[' . $rowValueIndex .  ']' . $referenceFieldInputName;
		                    $referenceField['FIELD_NAME'] = $referenceFieldInputName;
		                    $referenceField['FIELD_GROUP'] = $columnKey . '.' . $referenceFieldKey;

		                    if (!isset($referenceField['PARENT']))
		                    {
								$referenceField['PARENT'] = [];
		                    }

		                    $referenceField['PARENT'][$columnKey] = $parent;

		                    $result[$columnKey . '_' . $rowValueIndex . '_' . $referenceFieldKey] = $referenceField;
		                }

		                if (!empty($rowValue['ID']))
		                {
			                $result[$columnKey . '_' . $rowValueIndex . '_ID'] = [
			                    'VALUE' => $rowValue['ID'],
			                    'FIELD_NAME' => $columnKey . '[' . $rowValueIndex . '][ID]',
			                    'HIDDEN' => 'Y',
			                    'FIELD_GROUP' => $columnKey . '.' . $referenceFieldKey
			                ];
		                }
	                }
	            }
	        }
        }

        return $result;
    }

	public function load($primary, array $select = [], $isCopy = false)
	{
		$dataClass = $this->getDataClass();
        $query = $dataClass::getByPrimary($primary);
        $result = null;

        if ($result = $query->fetch())
        {
            if (method_exists($dataClass, 'loadExternalReference'))
            {
                $externalData = $dataClass::loadExternalReference($result['ID'], null, $isCopy);

                if (isset($externalData[$result['ID']]))
                {
                    $result = array_merge(
                        $result,
                        $externalData[$result['ID']]
                    );
                }
            }

            if ($isCopy) { unset($result['ID']); }
        }
        else
        {
            throw new Main\SystemException($this->getComponentLang('ITEM_NOT_FOUND'));
        }

        return $result;
	}

	public function extend($data, array $select = [])
	{
		return $data;
	}

	public function validate($data, array $fields = null)
	{
		$dataClass = $this->getDataClass();
		$validateResult = new Main\Entity\Result();
		$result = null;

		$dataClass::saveExtractReference($data); // remove reference field from validation
		$dataClass::checkFields($validateResult, null, $data);

		if ($fields === null)
		{
			$result = $validateResult;
		}
		else
		{
			$result = new Main\Entity\Result();
			$fieldsMap = [];

			foreach ($fields as $field)
			{
				$fieldsMap[$field['FIELD_NAME']] = true;
			}

			foreach ($validateResult->getErrors() as $error)
			{
				$entityField = $error->getField();
				$fieldName = $entityField->getName();

				if (isset($fieldsMap[$fieldName]))
				{
					$result->addError($error);
				}
			}
		}

		return $result;
	}

	public function add($fields)
	{
		$dataClass = $this->getDataClass();

		return $dataClass::add($fields);
	}

	public function update($primary, $fields)
	{
		$dataClass = $this->getDataClass();

		return $dataClass::update($primary, $fields);
	}

	/**
	 * @return Market\Reference\Storage\Table
	 */
	protected function getDataClass()
	{
        return $this->getComponentParam('DATA_CLASS_NAME');
	}
}