<?php
/**
 * Individ module
 * 
 * @category	Individ
 * @package		MVC
 * @link		http://individ.ru
 * @revision	$Revision$
 * @date		$Date$
 */

namespace Indi\Main\Mvc\Controller;

use Indi\Main as Main;
use Indi\Main\Mvc as Mvc;

/**
 * Контроллер для веб-форм
 * 
 * @category	Individ
 * @package		MVC
 */
class Form extends Prototype
{
	/**
	 * Выводит форму обратной связи
	 *
	 * @return string
	 */
	public function feedbackAction()
	{
		return $this->getForm('FEEDBACK', "callback", "N");
	}


	/**
	 * Выводит форму обратной связи
	 *
	 * @return string
	 */
	public function feedbackmodalAction()
	{
		return $this->getForm('FEEDBACK', "feedback", "Y");
	}

	/**
	 * Выводит форму заявка на запчасти
	 *
	 * @return string
	 */
	public function sparemodalAction()
	{
		return $this->getForm('SPARE', "spare", "Y");
	}

	/**
	 * Выводит форму заявки вызова сервис инженера
	 *
	 * @return string
	 */
	public function mechanicmodalAction()
	{
		return $this->getForm('MECHANIC', "mechanic", "Y");
	}

	/**
	 * Выводит форму Заявка в сервисный центр
	 *
	 * @return string
	 */
	public function servicemodalAction()
	{
		return $this->getForm('REQUEST_SERVICE', "services", "Y");
	}

	/**
	 * Выводит форму Заявка в сервисный центр
	 *
	 * @return string
	 */
	public function courcemodalAction()
	{
		return $this->getForm('REQUEST_COURE', "learn-cource", "Y");
	}
	
	/**
	 * Выводит форму обратного звонка
	 *
	 * @return string
	 */
	public function callbackAction()
	{
		return $this->getForm('CALLBACK', "callback", "N");
	}

	/**
	 * Выводит форму обратного звонка
	 *
	 * @return string
	 */
	public function callbackmodalAction()
	{
		return $this->getForm('CALLBACK', "callback", "Y");
	}
	
	/**
	 * Выводит форму по параметру в запросе
	 *
	 * @return string
	 */
	/*public function addAction()
	{
		return $this->getForm($this->getParam('sid'));
	}*/
	
	/**
	 * Выводит компонент добавления результата формы
	 *
	 * @param integer $sid Символьный код формы
	 * @return string
	 */
	protected function getForm($sid, $template = ".default", $popup = 'Y')
	{
		$this->view = new Mvc\View\Html();
		$this->returnAsIs = true;
		
		$sid = trim($sid);
		if (!$sid) {
			throw new Main\Exception('Form SID is undefined.');
		}
		
		\Bitrix\Main\Loader::includeModule('form');
		$form = \CForm::GetBySID($sid)->Fetch();
		if (!$form) {
			throw new Main\Exception('The form is not found.');
		}
		
		return $this->getComponent(
			'bitrix:form.result.new',
			$template,
			array(
				'WEB_FORM_ID' => $form['ID'],
				'IGNORE_CUSTOM_TEMPLATE' => 'N',
				'USE_EXTENDED_ERRORS' => 'Y',
				'SEF_MODE' => 'N',
				'SEF_FOLDER' => '/',
				'CACHE_TYPE' => 'A',
				'CACHE_TIME' => '3600',
				'LIST_URL' => '',
				'EDIT_URL' => '',
				'SUCCESS_URL' => '',
				'CHAIN_ITEM_TEXT' => '',
				'CHAIN_ITEM_LINK' => '',
				'HIDE_TITLE' => 'N',
				'POPUP_MODE' => $popup,
				'VARIABLE_ALIASES' => array(
					'WEB_FORM_ID' => 'WEB_FORM_ID',
					'RESULT_ID' => 'RESULT_ID',
				)
			)
		);
	}
	
	/**
	 * Выводит результат заполнения формы
	 *
	 * @return array
	 */
	public function resultAction()
	{
		$this->view = new Mvc\View\Php('form/result.php');
		
		return array(
			'result' => $this->getParam('formresult'),
			'resultID' => (int) $this->getParam('RESULT_ID'),
			'formID' => (int) $this->getParam('WEB_FORM_ID'),
		);
	}


	/**
	 * Выводит результаты действий в форме подписки
	 *
	 * @return string
	 */
	protected function subscribeAction()
	{
		$this->view = new Mvc\View\Html();
		$this->returnAsIs = true;

		return $this->getComponent(
			"indi:subscribtion",
			"",
			array()
		);
	}
}