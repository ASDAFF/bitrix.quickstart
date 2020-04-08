<?php
/**
 * Individ module
 * 
 * @category	Individ
 * @link		http://individ.ru
 * @revision	$Revision$
 * @date		$Date$
 */

namespace Indi\Main;

/**
 * Сайт работает в продакшн-режиме
 */
const PRODUCTION_MODE = false;

/**
 * Основной класс модуля
 */
class Module
{
	/**
	 * Обработчик начала отображения страницы
	 *
	 * @return void
	 */
	public static function onPageStart()
	{
		self::checkUnavailableFunctions();
		self::checkTwoLevelsArchitecture();
		self::defineConstants();
		self::setupEventHandlers();
	}
	
	/**
	 * Подключает замену для функций, которые отсутствуют в нативной реализации
	 *
	 * @return void
	 */
	protected static function checkUnavailableFunctions()
	{
		if (!function_exists('json_encode')) {
			include_once \Indi\Main\BASE_DIR . '/functions/json_encode.php';
		}
	}
	
	/**
	 * Определяет вычисляемые константы модуля
	 *
	 * @return void
	 */
	protected static function defineConstants()
	{
		define("EURO_MAIN_TEMPLATE", "/local/templates/main");
		define(__NAMESPACE__ . '\IS_INDEX', \Bitrix\Main\Application::getInstance()->getContext()->getRequest()->getRequestedPage() == '/index.php');
		define(__NAMESPACE__ . '\IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest');
		
		Iblock\Prototype::defineConstants();
		//User::defineConstants();
		//Site::defineConstants();
		
		\Bitrix\Main\EventManager::getInstance()->addEventHandler(
			'main',
			'OnProlog',
			function() {
				if (defined('SITE_TEMPLATE_PATH')) {
					define(__NAMESPACE__ . '\TEMPLATE_IMG', SITE_TEMPLATE_PATH . '/images');
				}
			}
		);

		define("PREFIX_PATH_404", "/404.php");

		\Bitrix\Main\EventManager::getInstance()->addEventHandler(
			'main',
			'OnAfterEpilog',
			function() {
				self::showPage404();
			}
		);
	}
	
	/**
	 * Проверяет конфигурацию в случае 2-уровневой архитектуры (nginx -> apache)
	 *
	 * @return void
	 */
	protected static function checkTwoLevelsArchitecture()
	{
		if ($_SERVER['HTTP_X_FORWARDED_FOR'] && $_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
			if ($p = strrpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',')) {
				$_SERVER['REMOTE_ADDR'] = $REMOTE_ADDR = trim(substr($_SERVER['HTTP_X_FORWARDED_FOR'], $p + 1));
				$_SERVER['HTTP_X_FORWARDED_FOR'] = substr($_SERVER['HTTP_X_FORWARDED_FOR'], 0, $p);
			} else {
				$_SERVER['REMOTE_ADDR'] = $REMOTE_ADDR = $_SERVER['HTTP_X_FORWARDED_FOR'];
			}
		}
	}

	/**
	 * Отображение страницы 404
	 *
	 */
	public static function showPage404() {
		global $APPLICATION;

		// Check if we need to show the content of the 404 page
		if (!defined('ERROR_404') || ERROR_404 != 'Y') {
			return;
		}

		// Display the 404 page unless it is already being displayed
		if ($APPLICATION->GetCurPage() != PREFIX_PATH_404) {
			header('X-Accel-Redirect: '.PREFIX_PATH_404);
			exit();
		}
	}
	
	/**
	 * Добавляет обработчики событий
	 *
	 * @return void
	 */
	protected static function setupEventHandlers()
	{
		$eventManager = \Bitrix\Main\EventManager::getInstance();
		
		/* Forms event handlers */
		//$eventManager->addEventHandler('form', 'onAfterResultAdd', array('\Indi\Main\Form', 'onAfterResultAdd'));
		
		/* Sale event handlers */
		//$eventManager->addEventHandler('sale', 'OnBeforeBasketAdd', array('\Indi\Main\Sale\Basket', 'onBeforeBasketAdd'));
		//$eventManager->addEventHandler('sale', 'OnBasketAdd', array('\Indi\Main\Sale\Basket', 'onBasketAdd'));
		//$eventManager->addEventHandler('sale', 'OnOrderUpdate', array('\Indi\Main\Sale\Order', 'onOrderUpdate'));
		$eventManager->addEventHandler('main', 'OnBeforeEventAdd', array('\Indi\Main\Form', 'OnBeforeEventAddHandler'));
		$eventManager->AddEventHandler('search', 'OnSearchGetFileContent', array('\Indi\Main\Search', 'onBeforeIndex'));
	}
}