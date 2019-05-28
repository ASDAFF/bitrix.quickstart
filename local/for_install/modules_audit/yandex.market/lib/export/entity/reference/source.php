<?php

namespace Yandex\Market\Export\Entity\Reference;

use Yandex\Market;
use Bitrix\Main;

Main\Localization\Loc::loadMessages(__FILE__);

abstract class Source
{
	protected $type;

	public function setType($type)
	{
		$this->type = $type;
	}

	public function getType()
	{
		return $this->type;
	}

	/**
	 * Поля сущности
	 *
	 * @param array $context
	 *
	 * @return array
	 */
	abstract public function getFields(array $context = []);

	/**
	 * Варианты значений сущности
	 *
	 * @param       $field
	 * @param array $context
	 *
	 * @return array|null
	 */
	public function getFieldEnum($field, array $context = [])
	{
		$result = null;

		if (!empty($field['TYPE']))
		{
			switch ($field['TYPE'])
			{
				case Market\Export\Entity\Data::TYPE_BOOLEAN:

					$result = [
						[
							'ID' => 'Y',
							'VALUE' => Market\Config::getLang('EXPORT_ENTITY_SOURCE_BOOLEAN_TYPE_ENUM_Y')
						],
						[
							'ID' => 'N',
							'VALUE' => Market\Config::getLang('EXPORT_ENTITY_SOURCE_BOOLEAN_TYPE_ENUM_N')
						]
					];

				break;

				case Market\Export\Entity\Data::TYPE_FILE:

					$result = [
						[
							'ID' => Market\Export\Entity\Data::SPECIAL_VALUE_EMPTY,
							'VALUE' => Market\Config::getLang('EXPORT_ENTITY_SOURCE_FILE_TYPE_ENUM_EMPTY')
						]
					];

				break;

				case Market\Export\Entity\Data::TYPE_SERVICE_CATEGORY:

					$sectionList = Market\Service\Data\Category::getList();
					$currentTree = [];
					$currentTreeDepth = 0;
					$sectionNameCache = [];
					$result = [];

					foreach ($sectionList as $sectionKey => $section)
					{
						if ($section['depth'] < $currentTreeDepth)
						{
							array_splice($currentTree, $section['depth']);
						}

						$currentTree[$section['depth']] = $sectionKey;
						$currentTreeDepth = $section['depth'];
						$sectionFullName = '';

						foreach ($currentTree as $treeKey)
						{
							$treeSection = $sectionList[$treeKey];
							$treeSectionName = null;

							if (isset($sectionNameCache[$treeSection['id']]))
							{
								$treeSectionName = $sectionNameCache[$treeSection['id']];
							}
							else
							{
								$treeSectionName = Market\Service\Data\Category::getTitle($treeSection['id']);
								$sectionNameCache[$treeSection['id']] = $treeSectionName;
							}

							$sectionFullName .= ($sectionFullName === '' ? '' : ' / ') . $treeSectionName;
						}

						$result[] = [
							'ID' => $section['id'],
							'VALUE' => $sectionFullName
						];
					}

				break;
			}
		}

		return $result;
	}

	/**
	 * Название сущности
	 *
	 * @return string
	 */
	public function getTitle()
	{
		$langPrefix = $this->getLangPrefix();

		return Market\Config::getLang($langPrefix . 'TITLE');
	}

	/**
	 * Флаг опредяляющий: не имеет определенных полей, может принимать любое значение
	 *
	 * @return bool
	 */
	public function isVariable()
	{
		return false;
	}

	/**
	 * Является ли шаблоном
	 *
	 * @return bool
	 */
	public function isTemplate()
	{
		return false;
	}

	/**
	 * Может участвовать в выборке
	 *
	 * @return bool
	 */
	public function isSelectable()
	{
		return true;
	}

	/**
	 * Поля select для запроса CIBlockElement::GetList
	 *
	 * @param $select
	 *
	 * @return array
	 */
	public function getQuerySelect($select)
	{
		return [];
	}

	/**
	 * Может ли генерировать фильтр для CIBlockElement::GetList
	 *
	 * @return bool
	 */
	public function isFilterable()
	{
		return false;
	}

	/**
	 * Фильтр для запроса CIBlockElement::GetList
	 *
	 * @param $filter
	 * @param $select
	 *
	 * @return array
	 */
	public function getQueryFilter($filter, $select)
	{
		return [];
	}

	/**
	 * Порядок выполнения при обработке элементов
	 *
	 * @return int
	 * */
	public function getOrder()
	{
		return 500;
	}

	public function initializeQueryContext($select, &$queryContext, &$sourceSelect)
	{
		// nothing by default
	}

	public function releaseQueryContext($select, $queryContext, $sourceSelect)
	{
		// nothing by default
	}

	/**
	 * Выборка значений полей из результатов запроса CIBlockElement::GetList
	 *
	 * @param $elementList
	 * @param $parentList
	 * @param $selectFields
	 * @param $queryContext
	 * @param $sourceValues
	 *
	 * @return array
	 */
	public function getElementListValues($elementList, $parentList, $selectFields, $queryContext, $sourceValues)
	{
		return [];
	}

	/**
	 * Вспомогательный метод для генерации описания полей сущности
	 *
	 * @param $fieldList
	 *
	 * @return array
	 */
	protected function buildFieldsDescription($fieldList)
	{
		$result = [];
		$langPrefix = $this->getLangPrefix();

		foreach ($fieldList as $fieldId => $field)
		{
			$field['ID'] = $fieldId;

			if (!isset($field['VALUE']))
			{
				$field['VALUE'] = Market\Config::getLang($langPrefix . 'FIELD_' . $fieldId);
			}

			if (!isset($field['FILTERABLE']))
			{
				$field['FILTERABLE'] = true;
			}

			if (!isset($field['SELECTABLE']))
			{
				$field['SELECTABLE'] = true;
			}

			$result[] = $field;
		}

		return $result;
	}

	/**
	 * Префикс для языковых фраз класса
	 *
	 * @return string
	 */
	abstract protected function getLangPrefix();
}
