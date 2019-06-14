<?php

namespace BxSol;

/**
 * Mineev Aleksey (2016 ©)
 * alekseym@bxsolutions.ru
 */

use Bitrix\Main\EventManager;
use Bitrix\Main\LoaderException as Exception;
use ReflectionClass;
use ReflectionException;


class CDebugEvents
{
	protected $loadedModules = array();

	protected $documentRoot;

	protected $moduleFolders = array('/bitrix/modules', '/local/modules');

	protected static $words = array('SUBSCRIPTION', 'BEFOREINDEX', 'PERMISSIONS', 'ATTRIBUTES', 'OPERATIONS', 'CONNECTOR', 'COMPONENT', 'RECURRING', 'DEPENDENT', 'INSTALLED', 'CALCULATE', 'PROVIDERS', 'VALIDATOR', 'ATTRIBUTE', 'AVAILABLE', 'AFFILIATE', 'INFORMER', 'GENERATE', 'REGISTER', 'TEMPLATE', 'LANGUAGE', 'DOCUMENT', 'DELIVERY', 'HANDLERS', 'REDIRECT', 'FEATURES', 'EXTERNAL', 'DESIGNER', 'MODERATE', 'UPLOADED', 'QUESTION', 'TRACKING', 'DISCOUNT', 'LOCATION', 'CURRENCY', 'ACTIVATE', 'SERVICES', 'COMPLETE', 'PASSWORD', 'QUANTITY', 'PROPERTY', 'RESPONSE', 'PROVIDER', 'SHOPPING', 'RESTART', 'RATINGS', 'DISPLAY', 'CONFIGS', 'REINDEX', 'PREPARE', 'ACTIONS', 'MAILING', 'INCLUDE', 'PRODUCT', 'CATALOG', 'BARCODE', 'CONTROL', 'INSTALL', 'CONVERT', 'CONTEXT', 'HISTORY', 'SHARING', 'PRMEDIA', 'RESERVE', 'SUCCESS', 'INITIAL', 'CONTENT', 'ACCOUNT', 'SERVICE', 'PATTERN', 'CLASSES', 'RESTORE', 'TRIGGER', 'OPTIMAL', 'STORAGE', 'COUNTER', 'PROCESS', 'OBJECTS', 'GALLERY', 'DEFAULT', 'PREVIEW', 'NEAREST', 'COMMENT', 'CHANNEL', 'REMOVED', 'ELEMENT', 'CHANGED', 'SECTION', 'MESSAGE', 'COUPON', 'RIGHTS', 'CHANGE', 'RATING', 'APPEND', 'SCRIPT', 'ANSWER', 'FORMAT', 'PRESET', 'SYSTEM', 'RESULT', 'EDITOR', 'FILTER', 'REPORT', 'NUMBER', 'ACTION', 'LIGHTS', 'SEARCH', 'VALUES', 'SCHEMA', 'EXTEND', 'VOTING', 'PROLOG', 'REMIND', 'REGION', 'DELETE', 'STATIC', 'INSERT', 'PARSER', 'NOTIFY', 'UPLOAD', 'FORUMS', 'CUSTOM', ' EXTRA', 'IMPORT', 'BEFORE', 'EPILOG', 'CREATE', 'MOBILE', 'GLOBAL', 'PERSON', 'CANCEL', 'STATUS', 'MODULE', 'UPDATE', 'BASKET', 'DEDUCT', 'BUFFER', 'RESIZE', 'LOGOUT', 'IBLOCK', 'FOLDER', 'MEDIA', 'PERMS', 'TOPIC', 'EMAIL', 'STORE', 'GROUP', 'BUILD', 'SMILE', 'START', 'ADMIN', 'PROPS', 'VIDEO', 'ORDER', 'FINAL', 'ERROR', 'ALLOW', 'LOCAL', 'PHOTO', 'FORUM', 'AUDIT', 'CLEAR', 'ITEMS', 'INDEX', 'SHORT', 'CHECK', 'AGENT', 'CACHE', 'EVENT', 'PANEL', 'RIGHT', 'ARRAY', 'TABLE', 'TYPES', 'TRACE', 'COUNT', 'PRICE', 'OWNER', 'LOGIN', 'DAILY', 'AFTER', 'RESET', 'BEGIN', 'IMAGE', 'TITLE', 'TYPE', 'DOCS', 'SEND', 'RULE', 'OPTS', 'WITH', 'COND', 'FIND', 'PAGE', 'LOCK', 'MAIL', 'COPY', 'INIT', 'MAKE', 'INFO', 'FILE', 'CITY', 'STEP', 'AUTO', 'PUSH', 'SERV', 'DROP', 'TIME', 'HASH', 'ITEM', 'VIEW', 'LANG', 'LIST', 'TEXT', 'AJAX', 'DATA', 'MENU', 'BLOG', 'SHOW', 'RATE', 'SAVE', 'FORM', 'AUTH', 'POST', 'CART', 'VOTE', 'HTTP', 'FULL', 'RUNS', 'EDIT', 'USER', 'SALE', 'SITE', 'TASK', 'TAB', 'TAG', 'LOG', 'SUB', 'DAY', 'NEW', 'SET', 'END', 'MAN', 'AND', 'SOC', 'LIB', 'URI', 'ADD', 'NET', 'GET', 'PAY', 'ONE', 'UN', 'ID', 'IP', 'DO', 'IS', 'BY',);

	protected static $wordsSorted = array();
	protected static $wordsReplace = array();

	public function __construct()
	{

		try
		{
			$reflection = new ReflectionClass(EventManager::getInstance());

			$property = $reflection->getProperty('handlers');
			$property->setAccessible(true);

			$this->handlers = $property->getValue(EventManager::getInstance());

			if (is_null($this->handlers) || !is_array($this->handlers))
			{
				throw New Exception();
			}

		} catch (ReflectionException $e)
		{
			throw New Exception('Can\'t load events.');
		}

		$this->documentRoot = $_SERVER['DOCUMENT_ROOT'];

	}

	protected function loadModule($moduleId)
	{
		$moduleId = strtolower($moduleId);

		foreach ($this->moduleFolders as $folder)
		{

			$handle = @opendir($this->documentRoot . $folder);

			if ($handle)
			{

				if (is_dir($this->documentRoot . $folder . '/' . $moduleId))
				{

					if ($info = $this->createModuleObject($moduleId, $this->documentRoot . $folder))
					{
						$module["MODULE_ID"] = $info->MODULE_ID;
						$module["MODULE_NAME"] = $info->MODULE_NAME;
						$module["MODULE_DESCRIPTION"] = $info->MODULE_DESCRIPTION;
						closedir($handle);
						return $module;
					}
				}

				closedir($handle);
			}
		}
	}

	public function getModuleInfo($moduleId)
	{

		if (!isset($this->loadedModules[$moduleId]))
		{

			$this->loadedModules[$moduleId] = $this->loadModule($moduleId);

		}

		return $this->loadedModules[$moduleId];

	}

	public static function createModuleObject($moduleId, $folder)
	{
		$moduleId = trim($moduleId);
		$moduleId = preg_replace("/[^a-zA-Z0-9_.]+/i", "", $moduleId);

		if ($moduleId == '')
		{
			return false;
		}

		if (!is_file($moduleFile = $folder . '/' . $moduleId . '/install/index.php'))
		{
			return false;
		}

		include_once($moduleFile);

		$className = str_replace(".", "_", $moduleId);

		if (!class_exists($className))
		{
			return false;
		}

		return new $className;
	}

	public static function formatEvent($name)
	{
		if (empty(static::$wordsSorted))
		{
			static::$wordsSorted = static::$words;

			/**
			 * Sort by length desc
			 */
			usort(static::$wordsSorted, function ($a, $b)
			{
				return strlen($b) - strlen($a);
			});

			static::$wordsReplace = array_map(function ($w)
			{

				return ucfirst(strtolower($w));

			}, static::$wordsSorted);
		}

		if (substr($name, 0, 2) == 'ON')
		{
			$name = 'on' . substr($name, 2);
		}

		return str_replace(static::$wordsSorted, static::$wordsReplace, $name);
	}

	/**
	 * Some classes don't loads automatic
	 */
	public function fixBugs($class)
	{

		$classesMap = array('\Bitrix\Form\SenderEventHandler' => '/bitrix/modules/sender/lib/connector.php',);

		if (isset($classesMap[$class]))
		{
			include_once $this->documentRoot . $classesMap[$class];
		}

	}

	public function getEvents()
	{

		if (empty($this->handlers))
		{
			$this->loadEventHandlers();
		}

		return $this->handlers;
	}
}
