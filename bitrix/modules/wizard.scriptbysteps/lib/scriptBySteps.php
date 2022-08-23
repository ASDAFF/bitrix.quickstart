<?php
/**
 * Created by PhpStorm.
 * User: Frolov_S
 * Date: 09.06.2016
 * Time: 12:24
 */

namespace Wizard;

use Bitrix\Main\Application;
use \Bitrix\Main\IO\Directory;
use Bitrix\Main\Web\Json;
use Bitrix\Main\Web\Uri;

/**
 * Class ScriptBySteps
 * @package Wizard
 */
class ScriptBySteps
{
	protected $timeLimit = 20; //Ограничение выполнения по времени
	protected $elementLimit = 1000; //Ограничение выполнения по элементам
	protected $arParams = [];
	protected $operationCode = 'base';
	protected $pathToFolder = '';
	protected $files = [];
	protected $stepParams = [];
	protected $pathToModule = '';
	protected $scriptFilesFolder = '';
	protected $arParamsForNextStep = [];
	protected $isContinue = false;
	protected $steps = [];
	/**
	 * @var bool|\Wizard\AfexTruck\Logger
	 */
	protected $obLogger = false;
	protected static $instance = null;
	protected $mainScriptFile = '';
	protected $timeBegin = 0;

	/**
	 * @return \Wizard\ScriptBySteps
	 */
	public static function getInstance()
	{
		if (!isset(static::$instance))
			static::$instance = new static();

		return static::$instance;
	}

	/**
	 * @return array arParams
	 */
	public function getParams()
	{
		return $this->arParams;
	}

	/**
	 * @return array stepParams
	 */
	public function getStepParams()
	{
		return $this->stepParams;
	}

	/**
	 * ScriptBySteps constructor.
	 */
	public function __construct()
	{
		$this->pathToModule = \realpath(__DIR__ . '/..');
		$this->timeBegin = time();
	}

	/**
	 * @param array $arParams
	 */
	public function setParams(array $arParams)
	{
		$this->arParams = $arParams;
	}

	/**
	 * @param int $limit
	 */
	public function setTimeLimit($limit)
	{
		$this->timeLimit = $limit;
	}

	/**
	 * @return int
	 */
	public function getTimeLimit()
	{
		return $this->timeLimit;
	}

	/**
	 * @param int $limit
	 */
	public function setElementLimit($limit)
	{
		$this->elementLimit = $limit;
	}

	/**
	 * @return int
	 */
	public function getElementLimit()
	{
		return $this->elementLimit;
	}

	/**
	 * @param string $code
	 */
	public function setOperationCode($code)
	{
		$this->operationCode = $code;
		$this->setTmpFilesFolder($this->pathToModule . '/tmpFiles/' . $code);
	}

	/**
	 * @param string $path
	 */
	protected function setTmpFilesFolder($path)
	{
		if (!Directory::isDirectoryExists($path)) {
			Directory::createDirectory($path);
		}
		$this->pathToFolder = $path;
	}

	/**
	 * @param string $path
	 */
	public function setScriptFilesFolder($path)
	{
		$this->scriptFilesFolder = $path;
	}

	/**
	 *
	 */
	protected function setPathToFiles()
	{
		$this->files = [
			'block' => $this->pathToFolder . '/block',
			'step'  => $this->pathToFolder . '/step.data',
		];
	}

	/**
	 * @param array $params
	 */
	protected function setStepParams(array $params = [])
	{
		if (file_exists($this->files['step'])) {
			//Прерываем если установлено кастомное прерывание или это не продолжение запускающего скрипта
			if ((!empty($params) && self::checkCustomCancel($params['cancel'], 'step'))
				|| self::checkContinueFlag() === false) {
				self::requireFile($this->pathToModule . '/include/endAjax.php');
			}
			$this->stepParams = unserialize(file_get_contents($this->files['step']));
			//Прерываем если параметры пустые
			if (empty($this->stepParams)) {
				self::requireFile($this->pathToModule . '/include/endAjax.php');
			}
			//Запуск кастомной обработки начала
			self::requireFile(
				$this->scriptFilesFolder . '/' . $this->operationCode . '/customStepScripts/beginStep.php'
			);
		} else {
			//Прерываем если есть блокирующий файл или кастомное прерывание
			if ((!empty($params) && self::checkCustomCancel($params['cancel'], 'begin'))
				|| file_exists(
					$this->files['block']
				)) {
				self::requireFile($this->pathToModule . '/include/endAjax.php');
			}
			//Установка блокировки других запусков текущей обработки
			self::writeBlockFile($this->files['block']);
			//Установка параметров шага
			$this->stepParams = [
				'iNumPage' => 1,
				'ACTION'   => $this->arParams['BEGIN_STEP']
			];
			//Запуск кастомной обработки начала
			self::requireFile($this->scriptFilesFolder . '/' . $this->operationCode . '/customStepScripts/begin.php');
		}
	}

	/**
	 * @return bool
	 */
	public static function checkContinueFlag()
	{
		$ret = false;
		if (defined('REQUEST_TYPE')) {
			switch (\REQUEST_TYPE) {
				case 'cron':
					global $argv;
					if (!empty($argv[1]) && $argv[1] === 'Y') {
						$ret = true;
					}
					break;
				case 'ajax':
				default:
					$request = Application::getInstance()->getContext()->getRequest();
					$sContinue = $request->get('continue');
					if (!empty($sContinue) && $sContinue === 'Y') {
						$ret = true;
					}
					break;
			}
		} else {
			$request = Application::getInstance()->getContext()->getRequest();
			$sContinue = $request->get('continue');
			if (!empty($sContinue) && $sContinue === 'Y') {
				$ret = true;
			}
		}

		return $ret;
	}

	/**
	 * @param string $path
	 */
	public static function writeBlockFile($path)
	{
		$fp = fopen($path, 'w');
		fwrite($fp, 'block');
		fclose($fp);
	}

	/**
	 * @param array $arCancels
	 * @param string $type
	 *
	 * @return bool
	 */
	protected static function checkCustomCancel(array $arCancels, $type)
	{
		if (is_array($arCancels) && !empty($arCancels) && isset($arCancels[$type]) && (bool)$arCancels[$type]) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 *
	 */
	protected function nextStep()
	{
		//Переход на следующий шаг или завершение нормализации
		if ($this->isContinue) {
			//Запись файла с параметрами обработки
			$fp = fopen($this->files['step'], 'w');
			fwrite($fp, serialize($this->arParamsForNextStep));
			fclose($fp);
			//Запуск кастомной обработки завершения шага
			self::requireFile($this->scriptFilesFolder . '/' . $this->operationCode . '/customStepScripts/endStep.php');
			if (defined('REQUEST_TYPE') && REQUEST_TYPE === 'ajax') {
				$res = [
					'status'      => 'continue',
					'nextStep'    => $this->arParamsForNextStep,
					'currentStep' => [
						'index'  => $this->stepParams['iNumPage'],
						'action' => $this->stepParams['ACTION']
					]
				];
				echo Json::encode($res);
			} else {
				if (defined('REQUEST_TYPE') && REQUEST_TYPE === 'cron') {
					if (!empty($this->mainScriptFile)) {
						$filePath = $this->mainScriptFile;
					} else {
						$filePath = __FILE__;
					}
					//$strParams = '';
					$strParams = ' Y';
					exec("/usr/bin/php -f " . $filePath . $strParams . " > /dev/null 2>&1 &");
				} else {
					$uriString = Application::getInstance()->getContext()->getRequest()->getRequestUri();
					$uri = new Uri($uriString);
					$uri->deleteParams(['continue']);
					$uri->addParams(['continue' => 'Y']);
					$uriString = $uri->getUri();
					LocalRedirect($uriString, true, '200 OK');
				}
			}
		} else {
			//Удаление файла с параметрами обработки
			if (file_exists($this->files['step'])) {
				unlink($this->files['step']);
			}
			//Снятие блокировки текущей обработки
			if (file_exists($this->files['block'])) {
				unlink($this->files['block']);
			}
			//Запуск кастомной обработки завершения
			self::requireFile($this->scriptFilesFolder . '/' . $this->operationCode . '/customStepScripts/end.php');
			if (defined('REQUEST_TYPE') && REQUEST_TYPE === 'ajax') {
				$res = [
					'status'      => 'done',
					'currentStep' => [
						'index'  => $this->stepParams['iNumPage'],
						'action' => $this->stepParams['ACTION']
					]
				];
				echo Json::encode($res);
			}
		}
	}

	/**
	 * @param array $arStepParams
	 */
	public function execute(array $arStepParams = [])
	{
		$this->setPathToFiles();
		$this->setStepParams($arStepParams);
		$this->arParamsForNextStep = false;
		$this->isContinue = false;
		//Выбор и выполнение шага
		if (!empty($this->stepParams['ACTION'])) {
			self::requireFile(
				$this->scriptFilesFolder . '/' . $this->operationCode . '/' . $this->stepParams['ACTION'] . '.php'
			);
		}
	}

	/**
	 * @param string $file
	 */
	protected static function requireFile($file)
	{
		//Выбор и выполнение шага
		if (\file_exists($file)) {
			require_once($file);
		}
	}

	/**
	 * @param array $arSteps
	 */
	public function setSteps(array $arSteps)
	{
		$this->steps = $arSteps;
	}

	/**
	 * @param array $params
	 */
	public function setNewStep(array $params = [])
	{
		//Установка следующего шага
		if ($params['bHaveEls']) {
			$this->arParamsForNextStep = [
				'ACTION'   => $this->stepParams['ACTION'],
				'iNumPage' => ($params['isChange']) ? $this->stepParams['iNumPage'] : $this->stepParams['iNumPage'] + 1
			];
			$this->isContinue = true;
			if (!empty($params['mess'])) {
				$this->writeMess($params);
			}
		} else {
			$keyStep = array_search($this->stepParams['ACTION'], $this->steps) + 1;
			if (array_key_exists($keyStep, $this->steps)) {
				$this->arParamsForNextStep = [
					'ACTION'   => $this->steps[$keyStep],
					'iNumPage' => 1
				];
				$this->isContinue = true;
				if (!empty($params['mess'])) {
					$this->writeMess($params);
				}
			} else {
				$this->isContinue = false;
			}
		}
		if (isset($this->stepParams['beginTime'])) {
			$this->arParamsForNextStep['beginTime'] = $this->stepParams['beginTime'];
		} else {
			$this->arParamsForNextStep['beginTime'] = START_SCRIPT_TIME;
		}
		//Переход к следующему шагу
		$this->nextStep();
	}

	/**
	 * @param object $obLogger
	 */
	public function setLogger($obLogger)
	{
		$this->obLogger = $obLogger;
	}

	/**
	 * @return mixed object or bool
	 */
	public function getLogger()
	{
		return $this->obLogger;
	}

	/**
	 * @param array $params
	 */
	protected function writeMess(array $params)
	{
		if (!empty($params['mess'])) {
			foreach ($params['mess'] as $key => $mess) {
				switch ($key) {
					case 'i':
						if ($params['iNumPage'] > 0) {
							if (\is_object($this->obLogger)) {
								$this->obLogger->write(str_replace('#i#', $params['iNumPage'], $mess));
								$this->obLogger->writeSeparator();
							}
						}
						break;
					case 'end':
						if (!$params['bHaveEls']) {
							if (\is_object($this->obLogger)) {
								$this->obLogger->write($mess);
								$this->obLogger->writeEndLine();
							}
						}
						break;
				}
			}
		}
	}

	/**
	 *
	 */
	public function finishExecute()
	{
		$this->isContinue = false;
		$this->stepParams['cancel'] = true;
		//Переход к следующему шагу
		$this->nextStep();
	}

	/**
	 * @param string $file
	 */
	public function setMainScriptFile($file = '')
	{
		if (!empty($file)) {
			$this->mainScriptFile = $file;
		}
	}

	/**
	 * @return int
	 */
	public function getTimeBegin()
	{
		return $this->timeBegin;
	}
}