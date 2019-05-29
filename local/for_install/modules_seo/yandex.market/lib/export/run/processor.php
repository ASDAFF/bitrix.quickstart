<?php

namespace Yandex\Market\Export\Run;

use Bitrix\Main;
use Yandex\Market;

class Processor
{
	/** @var \Yandex\Market\Export\Setup\Model */
	protected $setup;
	/** @var Writer\Base */
	protected $writer;
	/** @var bool */
	protected $isWriterLocked;
	/** @var bool */
	protected $hasPublicFile;
	/** @var string */
	protected $publicFilePath;
	/** @var Writer\Base */
	protected $publicWriter;
	/** @var array */
	protected $parameters;
	/** @var float */
	protected $startTime;
	/** @var array */
	protected $conlictList;

	public function __construct(Market\Export\Setup\Model $setup, $parameters = [])
	{
		$this->setup = $setup;
		$this->parameters = $parameters;
		$this->startTime = $this->getParameter('startTime') ?: microtime(true);
	}

	public function clear($isStrict = false)
	{
		$steps = Manager::getSteps();

		foreach ($steps as $stepName)
		{
			$step = Manager::getStepProvider($stepName, $this);

			$step->clear($isStrict);
		}
	}

	/**
	 * @param $action string
	 *
	 * @return \Yandex\Market\Result\StepProcessor
	 * @throws \Bitrix\Main\SystemException
	 */
	public function run($action = 'full')
	{
		$result = new Market\Result\StepProcessor();
		$steps = Manager::getSteps();
		$stepsWeightList = Manager::getStepsWeight();
		$requestStep = $this->getParameter('step');
		$hasRequestStep = (in_array($requestStep, $steps));
		$isFoundRequestStep = false;

		$result->setTotal(array_sum($stepsWeightList));

		foreach ($steps as $stepName)
		{
			$isRequestStep = (
				(!$hasRequestStep && !$isFoundRequestStep)
				|| $requestStep === $stepName
			);
			$stepWeight = $stepsWeightList[$stepName];

			if ($isRequestStep || $isFoundRequestStep)
			{
				$isFoundRequestStep = true;
				$stepOffset = $isRequestStep ? trim($this->getParameter('stepOffset')) : '';
				$stepOffset = ($stepOffset !== '' ? $stepOffset : null);
				$stepTotalCount = $isRequestStep ? trim($this->getParameter('stepTotalCount')) : '';
				$stepTotalCount = ($stepTotalCount !== '' ? (int)$stepTotalCount : null);

				// if no lock file or time expired, then break loop

				if (
					!$this->lockWriter()
					|| (!$isRequestStep && $this->isTimeExpired())
				)
				{
					$result->setStep($stepName);
					$result->setStepOffset($stepOffset);

					if ($this->getParameter('progressCount') === true)
					{
						$result->setStepTotalCount($stepTotalCount);
					}

					break;
				}

				// process step

				$step = Manager::getStepProvider($stepName, $this);

				if ($stepOffset === null)
				{
					switch ($action)
					{
						case 'full':
							$step->clear();
						break;

						case 'change':
							$step->invalidate();
						break;
					}
				}

				if ($this->getParameter('progressCount') === true)
				{
					$step->setTotalCount($stepTotalCount); // set totalCount calculated on previous step
				}

				$stepResult = $step->run($action, $stepOffset);

				// if step not finished, then break loop

				if (!$stepResult->isFinished())
				{
					$result->setStep($stepName);
					$result->setStepOffset($stepResult->getOffset());
					$result->increaseProgress($stepResult->getProgressRatio() * $stepWeight);

					if ($this->getParameter('progressCount') === true)
					{
						$result->setStepReadyCount($stepResult->getReadyCount());
						$result->setStepTotalCount($stepResult->getTotalCount());
					}

					break;
				}
				else
				{
					switch ($action)
					{
						case 'change':
							$step->removeInvalid();
						break;

						case 'refresh':
							$step->removeOld();
						break;
					}
				}
			}

			$result->increaseProgress($stepWeight);
		}

		if ($result->isFinished())
		{
			$this->finalize($action);
		}

		$this->releasePublicWriter();
		$this->releaseWriter();

		return $result;
	}

	public function finalize($action)
	{
		/** @var Steps\Root $rootStep */
		$rootStep = Manager::getStepProvider(Manager::STEP_ROOT, $this);

		if ($action !== 'change')
		{
			$this->publishFile();
		}

		$rootStep->updateDate();
	}

	/**
	 * Модель настройки
	 *
	 * @return Market\Export\Setup\Model
	 */
	public function getSetup()
	{
		return $this->setup;
	}

	public function publishFile()
	{
		$this->releasePublicWriter();

		if ($this->publicFilePath !== null)
		{
			$writer = $this->getWriter();

			$writer->move($this->publicFilePath);

			$this->publicFilePath = null;
			$this->hasPublicFile = null;
		}
	}

	/**
	 * @return bool
	 */
	public function hasPublicFile()
	{
		if ($this->publicFilePath !== null && $this->hasPublicFile === null)
		{
			$this->hasPublicFile = file_exists($this->publicFilePath);
		}

		return $this->hasPublicFile;
	}

	/**
	 * @return Writer\Base|null
	 */
	public function getPublicWriter()
	{
		if ($this->publicWriter === null && $this->hasPublicFile())
		{
			$this->publicWriter = $this->loadWriter(true);
		}

		return $this->publicWriter;
	}

	public function releasePublicWriter()
	{
		if ($this->publicWriter !== null)
		{
			$this->publicWriter->destroy();
			$this->publicWriter = null;
		}
	}

	/**
	 * Получаем класс писателя
	 *
	 * @return Market\Export\Run\Writer\Base
	 */
	public function getWriter()
	{
		if ($this->writer === null)
		{
			$this->writer = $this->loadWriter();
		}

		return $this->writer;
	}

	/**
	 * Создаем класс писателя
	 *
	 * @param $isIgnoreTemp bool
	 *
	 * @return Market\Export\Run\Writer\File
	 */
	protected function loadWriter($isIgnoreTemp = false)
	{
		$filePath = $this->getSetup()->getFileAbsolutePath();

		if (!$isIgnoreTemp)
		{
			$tmpFilePath = $filePath . '.tmp';

			if ($this->getParameter('usePublic') === false || file_exists($tmpFilePath))
			{
				$this->publicFilePath = $filePath;

				$filePath = $tmpFilePath;
			}
		}

		$parameters = [
			'filePath' => $filePath
		];

		return new Writer\File($parameters);
	}

	/**
	 * Блокировка файла
	 *
	 * @return bool
	 */
	protected function lockWriter()
	{
		if (!$this->isWriterLocked)
		{
			$writer = $this->getWriter();
			$this->isWriterLocked = $writer->lock();
		}

		return $this->isWriterLocked;
	}

	/**
	 * Выгружаем из памяти класс писателя
	 */
	protected function releaseWriter()
	{
		if ($this->writer !== null)
		{
			if ($this->isWriterLocked)
			{
				$this->isWriterLocked = false;
				$this->writer->unlock();
			}

			$this->writer->destroy();
			$this->writer = null;
		}
	}

	/**
	 * Параметр выполнения
	 *
	 * @param $name
	 *
	 * @return mixed|null
	 */
	public function getParameter($name)
	{
		return isset($this->parameters[$name]) ? $this->parameters[$name] : null;
	}

	/**
	 * Загружаем необходимые для работы модули (модули sale и catalog не является необходимыми, должны быть загружены
	 * при запросе данных)
	 *
	 * @throws \Bitrix\Main\LoaderException
	 * @throws \Bitrix\Main\SystemException
	 */
	protected function loadModules()
	{
		$modules = [ 'iblock' ];

		foreach ($modules as $module)
		{
			if (!Main\Loader::includeModule($module))
			{
				throw new Main\SystemException('require module ' . $module);
			}
		}
	}

	public function getTimeLimit()
	{
		return $this->getParameter('timeLimit') ?: ((int)ini_get('max_execution_time') * 0.75) ?: 30;
	}

	public function isTimeExpired()
	{
		return (microtime(true) - $this->startTime >= $this->getTimeLimit());
	}

	public function getConflicts()
	{
		if ($this->conflictList === null)
		{
			$this->conflictList = $this->findConflicts();
		}

		return $this->conflictList;
	}

	protected function findConflicts()
	{
		$conflictTags = [
			'categoryId' => true
		];
		$conflictSources = [];
		$iblockLinkCollection = $this->setup->getIblockLinkCollection();
		$iblockLinkMap = [];
		$iblockContextList = [];
		$result = [];

		/** @var \Yandex\Market\Export\IblockLink\Model $iblockLink */
		foreach ($iblockLinkCollection as $iblockLink)
		{
			$iblockLinkId = $iblockLink->getId();
			$iblockLinkMap[$iblockLinkId] = $iblockLink;
			$tagDescriptionList = $iblockLink->getTagDescriptionList();

			foreach ($tagDescriptionList as $tagDescription)
			{
				$tagName = $tagDescription['TAG'];

				if (isset($conflictTags[$tagName]))
				{
					if (!isset($conflictSources[$tagName]))
					{
						$conflictSources[$tagName] = [];
					}

					$conflictSources[$tagName][$iblockLinkId] = $tagDescription['VALUE'];
				}
			}
		}

		foreach ($conflictSources as $tagName => $sourceList)
		{
			$fieldTypeList = [];
			$conflictData = null;

			if (count($sourceList) > 1)
			{
				foreach ($sourceList as $iblockLinkId => $sourceMap)
				{
					$iblockContext = null;

					if (isset($iblockContextList[$iblockLinkId]))
					{
						$iblockContext = $iblockContextList[$iblockLinkId];
					}
					else
					{
						$iblockLink = $iblockLinkMap[$iblockLinkId];
						$iblockContext = $iblockLink->getContext();

						$iblockContextList[$iblockLinkId] = $iblockContext;
					}

					$source = Market\Export\Entity\Manager::getSource($sourceMap['TYPE']);
					$sourceFields = $source->getFields($iblockContext);
					$fieldType = null;

					foreach ($sourceFields as $sourceField)
					{
						if ($sourceField['ID'] === $sourceMap['FIELD'])
						{
							$fieldType = $sourceField['TYPE'];
							break;
						}
					}

					if ($fieldType !== null)
					{
						if (!isset($fieldTypeList[$fieldType]))
						{
							$fieldTypeList[$fieldType] = [];
						}

						$fieldTypeList[$fieldType][] = $sourceMap;
					}
				}
			}

			if (count($fieldTypeList) > 1)
			{
				switch ($tagName)
				{
					case 'categoryId':
						$this->resolveConflictForCategoryId($result, $fieldTypeList);
					break;
				}
			}
		}

		return $result;
	}

	protected function resolveConflictForCategoryId(&$result, $fieldTypeList)
	{
		$iblockSectionType = Market\Export\Entity\Data::TYPE_IBLOCK_SECTION;

		if (isset($fieldTypeList[$iblockSectionType]))
		{
			$maxIblockSectionId = $this->getMaxIblockSectionId();
			$gap = 1000000;
			$incrementForOtherTypes = $gap * (round($maxIblockSectionId / $gap) + 1);

			foreach ($fieldTypeList as $fieldType => $sourceMapList)
			{
				if ($fieldType !== $iblockSectionType)
				{
					foreach ($sourceMapList as $sourceMap)
					{
						if (!isset($result[$sourceMap['TYPE']]))
						{
							$result[$sourceMap['TYPE']] = [];
						}

						$result[$sourceMap['TYPE']][$sourceMap['FIELD']] = [
							'TYPE' => 'INCREMENT',
							'VALUE' => $incrementForOtherTypes
						];
					}
				}
			}
		}
	}

	protected function getMaxIblockSectionId()
	{
		$result = 0;

		if (Main\Loader::includeModule('iblock'))
		{
			$queryLastsection = \CIBlockSection::GetList(
				[ 'ID' => 'DESC' ],
				[],
				false,
				[ 'ID' ],
				[ 'nTopCount' => 1 ]
			);

			if ($lastSection = $queryLastsection->Fetch())
			{
				$result = (int)$lastSection['ID'];
			}
		}

		return $result;
	}
}