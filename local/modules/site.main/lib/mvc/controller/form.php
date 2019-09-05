<?php
/**
 *  module
 * 
 * @category	
 * @package		MVC
 * @link		http://.ru
 * @revision	$Revision$
 * @date		$Date$
 */

namespace Site\Main\Mvc\Controller;

use Bitrix\Main\Application;
use Site\Main as Main;
use Site\Main\Mvc as Mvc;

/**
 * Контроллер для веб-форм
 * 
 * @category	
 * @package		MVC
 */
class Form extends Prototype
{
	public function getRegFormAction()
	{
		$this->view = new Mvc\View\Html();
		$this->returnAsIs = true;
		$bMobile = Application::getInstance()->getContext()->getRequest()->getPost('MOBILE');

		return $this->getComponent(
			'bitrix:main.register',
			'',
			array(
				"SHOW_FIELDS" => array(0 => 'NAME', 1 => 'EMAIL', 2 => 'PERSONAL_COUNTRY', 3 => 'PERSONAL_CITY', 4 => 'PERSONAL_PHONE'),
				"REQUIRED_FIELDS" => array('NAME', 'EMAIL', 'PERSONAL_COUNTRY', 'PERSONAL_CITY', 'PASSWORD'),
				"USER_PROPERTY" => array(),
				"MOBILE" => $bMobile,
				"AUTH" => "N"
			)
		);
	}

	public function getAuthFormAction()
	{
		$this->view = new Mvc\View\Html();
		$this->returnAsIs = true;


		return $this->getComponent(
			'bitrix:system.auth.authorize',
			'',
			array(

			)
		);
	}


	/**
	 * Выводит форму запроса пароля
	 *
	 * @return string
	 */
	public function getForgotFormAction()
	{
		$this->view = new Mvc\View\Html();
		$this->returnAsIs = true;


		return $this->getComponent(
			'bitrix:system.auth.forgotpasswd',
			'',
			array(

			)
		);
	}


	public function registerAction()
	{
		$arUserFields = $this->request->toArray();
		
		
	}
}