<?php

namespace Yandex\Market\Export\Run\Steps;

use Bitrix\Main;
use Yandex\Market;

Main\Localization\Loc::loadMessages(__FILE__);

abstract class Base
{
	const STORAGE_STATUS_FAIL = 1;
	const STORAGE_STATUS_SUCCESS = 2;
	const STORAGE_STATUS_INVALID = 3;
	const STORAGE_STATUS_DUPLICATE = 4;

	/** @var Market\Export\Run\Processor */
	protected $processor = null;
	/** @var Market\Export\Xml\Tag\Base */
	protected $tag = null;
	/** @var string|null */
	protected $tagParentName = null;
	/** @var bool */
	protected $isAllowCopyPublic = false;
	/** @var string */
	protected $runAction = null;
	/** @var int|null*/
	protected $totalCount;

	public static function getStorageStatusTitle($status)
	{
		return Market\Config::getLang('EXPORT_RUN_STEP_STORAGE_STATUS_' . $status);
	}

	public function __construct(Market\Export\Run\Processor $processor)
	{
		$this->processor = $processor;
	}

	public function destroy()
	{
		$this->processor = null;
		$this->tag = null;
		$this->tagParentName = null;
	}

	/**
	 * Название шага для событий
	 *
	 * @return mixed
	 */
	abstract public function getName();

	/**
	 * Инвалидируем лог и хранилище шага по изменениям
	 */
	public function invalidate()
	{
		$context = $this->getContext();
		$changes = $this->getParameter('changes');

		$this->invalidateDataStorage($changes, $context);
	}

	/**
	 * Очищаем лог и хранилище шага полностью
	 *
	 * @param $isStrict bool
	 */
	public function clear($isStrict = false)
	{
		$context = $this->getContext();

		$this->clearDataLog($context);
		$this->clearDataStorage($context);
	}

	public function getReadyCount()
	{
		return null;
	}

	public function getTotalCount()
	{
		return $this->totalCount;
	}

	public function setTotalCount($count)
	{
		$this->totalCount = ($count !== null ? (int)$count : null);
	}

	/**
	 * Установить текущий режим выгрузки
	 *
	 * @param $action
	 */
	protected function setRunAction($action)
	{
		$this->runAction = $action;
	}

	/**
	 * Текущий режим выгрузки
	 *
	 * @return string
	 */
	public function getRunAction()
	{
		return $this->runAction;
	}

	/**
	 * Запускаем шаг
	 *
	 * @param $offset
	 *
	 * @return Market\Result\Step
	 */
	abstract public function run($action, $offset = null);

	/**
	 * Записываем данные шага
	 *
	 * @param $tagValuesList Market\Result\XmlValue[]
	 * @param $elementList
	 * @param $context
	 * @param $data
	 */
	protected function writeData($tagValuesList, $elementList, array $context = [], array $data = null)
	{
		$this->extendData($tagValuesList, $elementList, $context, $data);

		$tagResultList = $this->buildTagList($tagValuesList, $context);

		$this->writeDataUserEvent($tagResultList, $elementList, $context, $data);

		$storageResultList = $this->writeDataStorage($tagResultList, $tagValuesList, $elementList, $context);

		$this->writeDataFile($tagResultList, $storageResultList);
		$this->writeDataCopyPublic($tagResultList);
		$this->writeDataLog($tagResultList, $context);
	}

	/**
	 * Расширяем данные шага через $tagValuesList
	 *
	 * @param Market\Result\XmlValue[] $tagValuesList
	 * @param array                    $elementList
	 * @param array                    $context
	 * @param array|null               $data
	 */
	protected function extendData($tagValuesList, $elementList, array $context = [], array $data = null)
	{
		$this->extendDataUserEvent($tagValuesList, $elementList, $context, $data);
	}

	/**
	 * Пользовательское событие для расширения через $tagValuesList
	 *
	 * @param Market\Result\XmlValue[] $tagValuesList
	 * @param array                    $elementList
	 * @param array                    $context
	 * @param array|null               $data
	 */
	protected function extendDataUserEvent($tagValuesList, $elementList, array $context = [], array $data = null)
	{
		$stepName = $this->getName();
		$moduleName = Market\Config::getModuleName();
		$eventName = 'onExport' . ucfirst($stepName) . 'ExtendData';
		$eventData = [
			'TAG_VALUE_LIST' => $tagValuesList,
			'ELEMENT_LIST' => $elementList,
			'CONTEXT' => $context
		];

		if (isset($data))
		{
			$eventData += $data;
		}

		$event = new Main\Event($moduleName, $eventName, $eventData);
		$event->send();
	}

	/**
	 * Генерируем теги
	 *
	 * @param Market\Result\XmlValue[] $tagValuesList
	 * @param array                    $context
	 *
	 * @return Market\Result\XmlNode[]
	 */
	protected function buildTagList($tagValuesList, array $context = [])
	{
		$tag = $this->getTag();
		$document = $tag->exportDocument();
		$result = [];

		foreach ($tagValuesList as $elementId => $tagValue)
		{
			$tagData = $tagValue->getTagData();

			$result[$elementId] = $tag->exportTag($tagData, $context, $document);
		}

		return $result;
	}

	/**
	 * Пользовательское события для модификации результата шага через $tagResultList
	 *
	 * @param $dataList
	 * @param $tagResultList Market\Result\XmlNode[]
	 */
	protected function writeDataUserEvent($tagResultList, $elementList, array $context = [], array $data = null)
	{
		$stepName = $this->getName();
		$moduleName = Market\Config::getModuleName();
		$eventName = 'onExport' . ucfirst($stepName) . 'WriteData';
		$eventData = [
			'TAG_RESULT_LIST' => $tagResultList,
			'ELEMENT_LIST' => $elementList,
			'CONTEXT' => $context
		];

		if (isset($data))
		{
			$eventData += $data;
		}

		$event = new Main\Event($moduleName, $eventName, $eventData);
		$event->send();
	}

	/**
	 * Класс хранилища результовов шага
	 *
	 * @return Market\Reference\Storage\Table
	 */
	protected function getStorageDataClass()
	{
		return null;
	}

	/**
	 * Инвалидириуем результаты выгрузки по изменениям
	 *
	 * @param $changes
	 * @param $context
	 *
	 * @throws \Bitrix\Main\ArgumentException
	 */
	protected function invalidateDataStorage($changes, $context)
	{
		$dataClass = $this->getStorageDataClass();

		if ($dataClass)
		{
			/** @var \Bitrix\Main\Type\DateTime $initTime */
			$initTime = $this->getParameter('initTime');
			$invalidateFilter = $this->getStorageChangesFilter($changes, $context);

			// filter

			$filter = [
				'=SETUP_ID' => $context['SETUP_ID']
			];

			if (!empty($invalidateFilter))
			{
				$filter[] = $invalidateFilter;
			}

			// filter

			$fields = [
				'STATUS' => static::STORAGE_STATUS_INVALID
			];

			if ($initTime)
			{
				$updateTime = clone $initTime;
				$updateTime->add('-PT1S');

				$fields['TIMESTAMP_X'] = $updateTime;
			}

			$dataClass::updateBatch(
				[ 'filter' => $filter ],
				$fields
			);
		}
	}

	/**
	 * Очищаем хранилище результатов выгрузки полностью
	 *
	 * @param $context
	 *
	 * @throws \Bitrix\Main\ArgumentException
	 */
	protected function clearDataStorage($context)
	{
		$dataClass = $this->getStorageDataClass();

		if ($dataClass)
		{
			$dataClass::deleteBatch([
				'filter' => [ '=SETUP_ID' => $context['SETUP_ID'] ]
			]);
		}
	}

	/**
	 * Фильтр по изменениям для хранилища результатов
	 *
	 * @param $changes
	 * @param $context
	 *
	 * @return null
	 */
	protected function getStorageChangesFilter($changes, $context)
	{
		return null; // invalidate all by default
	}

	/**
	 * Записываем результат выгрузки в постоянное хранилище
	 *
	 * @param $tagResultList Market\Result\XmlNode[]
	 * @param $tagValuesList Market\Result\XmlValue[]
	 * @param $elementList
	 * @param $context
	 *
	 * @return array
	 */
	protected function writeDataStorage($tagResultList, $tagValuesList, $elementList, array $context = [])
	{
		$result = [];
		$dataClass = $this->getStorageDataClass();
		$useHashCollision = $this->useHashCollision();

		if ($dataClass)
		{
			$timestamp = new Main\Type\DateTime();
			$existMap = [];
			$hashList = [];
			$hashListMap = [];
			$needCheckHashList = [];

			// query exists

			$queryExists = $dataClass::getList([
				'filter' => [
					'=SETUP_ID' => $context['SETUP_ID'],
					'=ELEMENT_ID' => array_keys($tagResultList)
				],
				'select' => [
					'ELEMENT_ID',
					'HASH',
					'STATUS'
				]
			]);

			while ($row = $queryExists->fetch())
			{
				$existMap[$row['ELEMENT_ID']] = $row;
			}

			// hash list

			foreach ($tagResultList as $elementId => $tagResult)
			{
				if ($tagResult->isSuccess())
				{
					$hash = $this->getTagResultHash($tagResult, $useHashCollision);

					$hashList[$elementId] = $hash;

					if ($useHashCollision && !isset($hashListMap[$hash]))
					{
						$hashListMap[$hash] = $elementId;
					}
				}
			}

			// make update

			$fieldsList = [];

			foreach ($tagResultList as $elementId => $tagResult)
			{
				$rowId = null;
				$writeAction = null;
				$element = isset($elementList[$elementId]) ? $elementList[$elementId] : null;
				$tagValues = isset($tagValuesList[$elementId]) ? $tagValuesList[$elementId] : null;

				$fields = [
					'SETUP_ID' => $context['SETUP_ID'],
					'ELEMENT_ID' => $elementId, // not int, maybe currency
					'STATUS' => static::STORAGE_STATUS_FAIL,
					'HASH' => '',
					'TIMESTAMP_X' => $timestamp
				];

				$additionalData = $this->getStorageAdditionalData($tagResult, $tagValues, $element, $context);

				if (!empty($additionalData))
				{
					$fields += $additionalData;
				}

				if ($tagResult->isSuccess())
				{
					$hash = $hashList[$elementId];

					if (!$useHashCollision)
					{
						$fields['STATUS'] = static::STORAGE_STATUS_SUCCESS;
						$fields['HASH'] = $hash;
					}
					else if ($hashListMap[$hash] === $elementId) // hash is unique
					{
						$needCheckHashList[$hash] = $fields['ELEMENT_ID'];

						$fields['STATUS'] = static::STORAGE_STATUS_SUCCESS;
						$fields['HASH'] = $hash;
					}
					else // match another hash
					{
						$fields['STATUS'] = static::STORAGE_STATUS_DUPLICATE;

						$tagResult->addError(new Market\Error\XmlNode(
							Market\Config::getLang('EXPORT_RUN_STEP_BASE_HASH_COLLISION', [
								'#ELEMENT_ID#' => $hashList[$hash]
							]),
							Market\Error\XmlNode::XML_NODE_HASH_COLLISION
						));
					}
				}

				$fieldsList[] = $fields;
			}

			// check hash collision from already stored data

			if ($useHashCollision && !empty($needCheckHashList))
			{
				$duplicateHashList = $this->checkHashCollision($needCheckHashList, $context);

				if (!empty($duplicateHashList))
				{
					foreach ($fieldsList as &$fields)
					{
						if (
							$fields['STATUS'] === static::STORAGE_STATUS_SUCCESS
							&& isset($duplicateHashList[$fields['HASH']])
						)
						{
							$elementId = $fields['ELEMENT_ID'];
							$tagResult = $tagResultList[$elementId];

							$fields['STATUS'] = static::STORAGE_STATUS_DUPLICATE;
							$fields['HASH'] = '';

							$tagResult->addError(new Market\Error\XmlNode(
								Market\Config::getLang('EXPORT_RUN_STEP_BASE_HASH_COLLISION', [
									'#ELEMENT_ID#' => $duplicateHashList[$fields['HASH']]
								]),
								Market\Error\XmlNode::XML_NODE_HASH_COLLISION
							));
						}
					}
					unset($fields);
				}
			}

			// write to db and build actions

			foreach ($fieldsList as $fields)
			{
				$elementId = $fields['ELEMENT_ID'];
				$prevHash = '';
				$fileAction = null;

				// write to db

				if (isset($existMap[$elementId]))
			    {
			        $prevHash = $existMap[$elementId]['HASH'];

					$updateResult = $dataClass::update(
						[ 'SETUP_ID' => $context['SETUP_ID'],  'ELEMENT_ID' => $elementId ],
						$fields
					);

					$isSuccessWrite = $updateResult->isSuccess();
			    }
			    else
			    {
			        $addResult = $dataClass::add($fields);

					$isSuccessWrite = $addResult->isSuccess();
			    }

			    if (
			        !$isSuccessWrite // fail write to db
			        && $fields['STATUS'] === static::STORAGE_STATUS_SUCCESS // and going write to file
		        )
			    {
			        $fields['STATUS'] = static::STORAGE_STATUS_FAIL;
			        $fields['HASH'] = '';
			    }

			    // write action

			    if ($fields['HASH'] !== $prevHash)
			    {
			        $prevFileAction = ($prevHash !== '' ? 'add' : 'delete');
			        $newFileAction = ($fields['HASH'] !== '' ? 'add' : 'delete');

			        if ($prevFileAction !== $newFileAction)
			        {
			            $fileAction = $newFileAction;
			        }
			        else if ($newFileAction === 'add')
			        {
			            $fileAction = 'update';
			        }
			    }

			    $result[$elementId] = [
                    'ID' => $elementId,
                    'ACTION' => $fileAction
                ];
			}
		}

		return $result;
	}

	/**
	 * Дополнительная информация для сохранения в таблице результатов выгрузки
	 *
	 * @param $tagResult Market\Result\XmlNode
	 * @param $tagValues Market\Result\XmlValue
	 * @param $element array|null
	 * @param $context array
	 *
	 * @return array
	 */
	protected function getStorageAdditionalData($tagResult, $tagValues, $element, $context)
	{
		return null;
	}

	/**
	 * Проверять совпадение хешей при экспорте
	 *
	 * @return bool
	 */
	protected function useHashCollision()
	{
		return false;
	}

	/**
	 * Проверяем наличие выгруженных элементов с указанными хешами
	 *
	 * @param $hashList
	 * @param $context
	 *
	 * @return array
	 * @throws \Bitrix\Main\ArgumentException
	 */
	protected function checkHashCollision($hashList, $context)
	{
		$result = [];
		$dataClass = $this->getStorageDataClass();

		if ($dataClass && !empty($hashList))
		{
			$filter = [
				'=SETUP_ID' => $context['SETUP_ID'],
				'=STATUS' => static::STORAGE_STATUS_SUCCESS,
				'=HASH' => array_keys($hashList)
			];

			switch ($this->getRunAction())
			{
				case 'refresh':
					$filter['>=TIMESTAMP_X'] = $this->getParameter('initTime');
				break;
			}

			$query = $dataClass::getList([
				'filter' => $filter,
				'select' => [
					'ELEMENT_ID',
					'HASH'
				]
			]);

			while ($row = $query->fetch())
			{
				$hash = $row['HASH'];

				if (isset($hashList[$hash]) && $hashList[$hash] != $row['ELEMENT_ID'])
				{
					$result[$hash] = $row['ELEMENT_ID'];
				}
			}
		}

		return $result;
	}

	/**
	 * Хэш результата
	 *
	 * @param $tagResult Market\Result\XmlNode
	 * @param $useHashCollision bool
	 *
	 * @return string
	 */
	protected function getTagResultHash($tagResult, $useHashCollision = false)
	{
		$result = '';
		$xmlContents = $tagResult->getXmlContents();

		if ($xmlContents !== null)
		{
			if ($useHashCollision) // remove id attr for check tag contents
			{
				$xmlContents = preg_replace('/^(<[^ ]+) id="[^"]*?"/', '$1', $xmlContents);
			}

			$result = md5($xmlContents);
		}

		return $result;
	}

	/**
	 * Записываем изменения в файл экспорта
	 *
	 * @param $tagResultList Market\Result\XmlNode[]
	 * @param $storageResultList array
	 */
	protected function writeDataFile($tagResultList, $storageResultList)
	{
		$writer = $this->getWriter();
		$actionDataList = [];

		foreach ($storageResultList as $elementId => $storageResult)
		{
			$actionType = null;
			$actionContents = null;

			switch ($storageResult['ACTION'])
			{
				case 'add':
				case 'update':
					$actionType = $storageResult['ACTION'];
					$actionContents = $tagResultList[$elementId]->getXmlContents();
				break;

				case 'delete':
					$actionType = 'update';
					$actionContents = '';
				break;
			}

			if (isset($actionType))
			{
				if (!isset($actionDataList[$actionType]))
				{
					$actionDataList[$actionType] = [];
				}

				$actionDataList[$actionType][$elementId] = $actionContents;
			}
		}

		foreach ($actionDataList as $action => $actionData)
		{
			switch ($action)
			{
				case 'add':
					$tagParentName = $this->getTagParentName();

					$writer->writeTagList($actionData, $tagParentName);
				break;

				case 'update':
					$tagName = $this->getTag()->getName();

					$writer->updateTagList($tagName, $actionData);
				break;
			}
		}
	}

	/**
	 * Записываем изменения в публичный файл экспорта
	 *
	 * @param $tagResultList Market\Result\XmlNode[]
	 */
	protected function writeDataCopyPublic($tagResultList)
	{
		if (
			$this->getRunAction() === 'change'
			&& ($writer = $this->getPublicWriter())
		)
		{
			$updateList = [];
			$isAllowDelete = $this->isAllowPublicDelete();

			foreach ($tagResultList as $elementId => $tagResult)
			{
				if ($tagResult->isSuccess())
				{
					$updateList[$elementId] = $tagResult->getXmlContents();
				}
				else if ($isAllowDelete)
				{
					$updateList[$elementId] = '';
				}
			}

			if (!empty($updateList))
			{
				$tagName = $this->getTag()->getName();

				$writer->lock(true);

				// update

				$updateResult = $writer->updateTagList($tagName, $updateList);

				// add

				$addList = [];

				foreach ($updateList as $elementId => $contents)
				{
					if ($contents && !isset($updateResult[$elementId]))
					{
						$addList[$elementId] = $contents;
					}
				}

				if (!empty($addList))
				{
					$parentName = $this->getTagParentName();

					$writer->writeTagList($addList, $parentName);
				}

				$writer->unlock();
			}
		}
	}

	protected function isAllowPublicDelete()
	{
		return false;
	}

	/**
	 * Очищаем лог
	 *
	 * @param $context
	 *
	 * @throws \Bitrix\Main\ArgumentException
	 */
	protected function clearDataLog($context)
	{
		$entityType = $this->getDataLogEntityType();

		if ($entityType)
		{
			Market\Logger\Table::deleteBatch([
				'filter' => [
					'=ENTITY_TYPE' => $entityType,
					'=ENTITY_PARENT' => $context['SETUP_ID'],
				]
			]);
		}
	}

	/**
	 * Записываем ошибки и warning в таблицу логов
	 *
	 * @param $tagResultList Market\Result\XmlNode[]
	 * @param $context array
	 */
	protected function writeDataLog($tagResultList, $context)
	{
		$entityType = $this->getDataLogEntityType();

		if ($entityType && !empty($tagResultList))
		{
			$logger = new Market\Logger\Logger();
			$existRows = $logger->getExists($entityType, $context['SETUP_ID'], array_keys($tagResultList));
			$existRowMap = [];

			foreach ($existRows as $existRowIndex => $existRow)
			{
				if (!isset($existRowMap[$existRow['ENTITY_ID']]))
				{
					$existRowMap[$existRow['ENTITY_ID']] = [];
				}

				$existRowMap[$existRow['ENTITY_ID']][$existRow['MESSAGE']] = $existRow['ID'];
			}

			foreach ($tagResultList as $elementId => $tagResult)
			{
				$logContext = [
					'ENTITY_TYPE' => $entityType,
					'ENTITY_PARENT' => $context['SETUP_ID'],
					'ENTITY_ID' => $elementId
				];
				$elementExistMessages = isset($existRowMap[$elementId]) ? $existRowMap[$elementId] : [];

				$errorGroupList = [
					Market\Psr\Log\LogLevel::CRITICAL => $tagResult->getErrors(),
					Market\Psr\Log\LogLevel::WARNING => $tagResult->getWarnings()
				];

				foreach ($errorGroupList as $logLevel => $errorGroup)
				{
					/** @var \Yandex\Market\Error\Base $error */
					foreach ($errorGroup as $error)
					{
						$errorContext = $logContext;
						$message = $error->getMessage();

						if (isset($elementExistMessages[$message]))
						{
							$errorContext['LOG_ID'] = $elementExistMessages[$message];
							unset($elementExistMessages[$message]);
						}

						if ($messageCode = $error->getCode())
						{
							$errorContext['ERROR_CODE'] = $messageCode;
						}

						$logger->log($logLevel, $message, $errorContext);
					}
				}

				// release old

				foreach ($elementExistMessages as $logId)
				{
					$logger->releaseExists($logId);
				}
			}
		}
	}

	/**
	 * Тип сущности для логов
	 *
	 * @return string|null
	 */
	protected function getDataLogEntityType()
	{
		return null;
	}

	/**
	 * Удаляем инвалидированные элементы, которые не попали в выгрузку по изменениям
	 *
	 * @throws \Bitrix\Main\ArgumentException
	 */
	public function removeInvalid()
	{
		$context = $this->getContext();
		$filter = [
			'=SETUP_ID' => $context['SETUP_ID'],
			'=STATUS' => static::STORAGE_STATUS_INVALID,
			'<TIMESTAMP_X' => $this->getParameter('initTime')
		];

		$this->removeByFilter($filter, $context);
	}

	/**
	 * Удаляем необработанные элементы
	 *
	 * @throws \Bitrix\Main\ArgumentException
	 */
	public function removeOld()
	{
		$context = $this->getContext();
		$filter = [
			'=SETUP_ID' => $context['SETUP_ID'],
			'<TIMESTAMP_X' => $this->getParameter('initTime')
		];

		$this->removeByFilter($filter, $context);
	}

	/**
	 * Удаляем элементы по фильтру
	 *
	 * @param $filter
	 * @param $context
	 *
	 * @throws \Bitrix\Main\ArgumentException
	 */
	protected function removeByFilter($filter, $context)
	{
		$dataClass = $this->getStorageDataClass();
		$updateList = [];

		// remove from storage and prepare file array

		if ($dataClass)
		{
			$query = $dataClass::getList([
				'filter' => $filter,
				'select' => [
					'ELEMENT_ID'
				]
			]);

			while ($item = $query->fetch())
			{
				$updateList[$item['ELEMENT_ID']] = '';

				$dataClass::delete([
					'SETUP_ID' => $context['SETUP_ID'],
					'ELEMENT_ID' => $item['ELEMENT_ID']
				]);
			}
		}

		// log

		$logEntityType = $this->getDataLogEntityType();

		if ($logEntityType && !empty($updateList))
		{
			Market\Logger\Table::deleteBatch([
				'filter' => [
					'=ENTITY_TYPE' => $logEntityType,
					'=ENTITY_PARENT' => $context['SETUP_ID'],
					'=ENTITY_ID' => array_keys($updateList)
				]
			]);
		}

		// write to file

		if (!empty($updateList))
		{
			$tagName = $this->getTag()->getName();
			$writer = $this->getWriter();

			$writer->updateTagList($tagName, $updateList);

			// remove from public

			if ($this->getRunAction() === 'change' && $this->isAllowPublicDelete())
			{
				$publicWriter = $this->getPublicWriter();

				if ($publicWriter)
				{
					$publicWriter->updateTagList($tagName, $updateList);
				}
			}
		}
	}

	/**
	 * Контроллер выгрузки
	 *
	 * @return \Yandex\Market\Export\Run\Processor
	 */
	protected function getProcessor()
	{
		return $this->processor;
	}

	/**
	 * Модель настройки выгрузки
	 *
	 * @return \Yandex\Market\Export\Setup\Model
	 */
	protected function getSetup()
	{
		return $this->getProcessor()->getSetup();
	}

	/**
	 * Писатель файла экспорта
	 *
	 * @return \Yandex\Market\Export\Run\Writer\Base
	 */
	protected function getWriter()
	{
		return $this->getProcessor()->getWriter();
	}

	/**
	 * Писатель в публичный файл
	 *
	 * @return \Yandex\Market\Export\Run\Writer\Base|null
	 */
	protected function getPublicWriter()
	{
		return $this->getProcessor()->getPublicWriter();
	}

	/**
	 * Параметр выполнения
	 *
	 * @param $name
	 *
	 * @return mixed|null
	 */
	protected function getParameter($name)
	{
		return $this->getProcessor()->getParameter($name);
	}

	/**
	 * Контекст выполнения
	 *
	 * @return array
	 */
	protected function getContext()
	{
		return $this->getSetup()->getContext();
	}

	protected function getFormat()
	{
		return $this->getSetup()->getFormat();
	}

	/**
	 * Выгружаемый тег
	 *
	 * @return \Yandex\Market\Export\Xml\Tag\Base
	 */
	public function getTag()
	{
		if (!isset($this->tag))
		{
			$format = $this->getFormat();

			$this->tag = $this->getFormatTag($format);
		}

		return $this->tag;
	}

	/**
	 * Название родительского тега
	 *
	 * @return null|string
	 */
	public function getTagParentName()
	{
		if (!isset($this->tagParentName))
		{
			$format = $this->getFormat();

			$this->tagParentName = $this->getFormatTagParentName($format);
		}

		return $this->tagParentName;
	}

	/**
	 * Выгружаемый тег из формата настройки
	 *
	 * @return \Yandex\Market\Export\Xml\Tag\Base
	 */
	abstract function getFormatTag(Market\Export\Xml\Format\Reference\Base $format);

	/**
	 * Название родительского тега из формата настройки
	 *
	 * @return string|null
	 * */
	abstract function getFormatTagParentName(Market\Export\Xml\Format\Reference\Base $format);

	protected function buildTagValuesList($tagDescriptionList, $sourceValuesList)
	{
		$result = [];

		foreach ($sourceValuesList as $elementId => $sourceValues)
		{
			$result[$elementId] = $this->buildTagValues($tagDescriptionList, $sourceValues);
		}

		return $result;
	}

	/**
	 * @param $tagDescriptionList
	 * @param $sourceValues
	 *
	 * @return Market\Result\XmlValue
	 */
	protected function buildTagValues($tagDescriptionList, $sourceValues)
	{
		$result = new Market\Result\XmlValue();

		foreach ($tagDescriptionList as $tagDescription)
		{
			$tagName = $tagDescription['TAG'];

			// get values list

			$tagValues = [];

			if (isset($tagDescription['VALUE']))
			{
				$tagValue = $this->getSourceValue($tagDescription['VALUE'], $sourceValues);

				if (is_array($tagValue))
				{
					$tagValues = $tagValue;
				}
				else
				{
					$tagValues[] = $tagValue;
				}
			}
			else
			{
				$tagValues[] = null;
			}

			// settings

			$tagSettings = isset($tagDescription['SETTINGS']) ? $tagDescription['SETTINGS'] : null;

			if ($tagSettings !== null && is_array($tagSettings))
			{
				foreach ($tagSettings as $settingName => $setting)
				{
					if (isset($setting['TYPE'], $setting['FIELD']))
					{
						if ($setting['TYPE'] === Market\Export\Entity\Manager::TYPE_TEXT)
						{
							$tagSettings[$settingName] = $setting['FIELD'];
						}
						else
						{
							$tagSettings[$settingName] = $this->getSourceValue($setting, $sourceValues);
						}
					}
				}
			}

			// export values

			foreach ($tagValues as $tagValue)
			{
				$isEmptyTagValue = ($tagValue === null || trim($tagValue) === ''); // is empty
				$tagAttributeList = [];

				if (isset($tagDescription['ATTRIBUTES']))
				{
					foreach ($tagDescription['ATTRIBUTES'] as $attributeName => $attributeSourceMap)
					{
						$tagAttributeList[$attributeName] = $this->getSourceValue($attributeSourceMap, $sourceValues);

						if ($tagAttributeList[$attributeName] !== null && trim($tagAttributeList[$attributeName]) !== '') // is not empty
						{
							$isEmptyTagValue = false;
						}
					}
				}

				if (!$isEmptyTagValue && !$result->hasTag($tagName, $tagValue, $tagAttributeList))
				{
					$result->addTag($tagName, $tagValue, $tagAttributeList, $tagSettings);
				}
			}
		}

		return $result;
	}

	protected function getSourceValue($sourceMap, $sourceValues)
	{
		$result = null;

		if (isset($sourceMap['VALUE']))
		{
			$result = $sourceMap['VALUE'];
		}
		else if (isset($sourceValues[$sourceMap['TYPE']][$sourceMap['FIELD']]))
		{
			$result = $sourceValues[$sourceMap['TYPE']][$sourceMap['FIELD']];
		}

		return $result;
	}
}