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

use Bitrix\Main\UserTable;
use \Site\Main as Main;
use Bitrix\Main\Application as Application;
use Site\Main\Mvc as Mvc;

/**
 * Контроллер для пользователей
 *
 * @category
 * @package		MVC
 */
class User extends Prototype
{
	/**
	 * Выводит компонент рейтинга пользователей
	 *
	 * @return string
	 */
	public function getTop10Action()
	{
		$this->view = new Mvc\View\Html();
		$this->returnAsIs = true;
		
		return $this->getComponent(
			'site:users.list', 'rating', array(
				'FIELDS' => array(
					'NAME',
					'LAST_NAME',
					'PERSONAL_COUNTRY',
					'PERSONAL_CITY',
					'PERSONAL_PHOTO'
				),
				'UFIELDS' => array('UF_*'),
				'SHOW_ALL' => 'Y'
			)
		);
	}

	/**
	 * Возвращает все доступные типы пользователей
	 *
	 * @return string
	 */
	public function getUTypesInfoAction()
	{
		$this->view = new Mvc\View\Json();
		$this->returnAsIs = true;

		return Main\User::getUTypesInfo();
	}

	/**
	 * Изменение ифнормации по пользователю
	 *
	 * @return array
	 */
	public function updateUInfoAction()
	{
		$this->view = new Mvc\View\Json();
		$this->returnAsIs = true;

		$arReq = Application::getInstance()->getContext()->getRequest()->toArray();
		$arEditableFields = array('NAME	', 'EMAIL', 'PERSONAL_PHONE', 'PERSONAL_CITY', 'UF_RATING', 'UF_USER_TYPE', 'NEW_PASSWORD');
		$obUser = new \CUser();

		/*Обновляем всю информацию по пользователю*/
		if( !empty($arReq['USER_ID']) && $arReq['ACTION'] == 'UPDATE_ALL' ){
			$arNewValues = array();

			foreach($arReq as $fieldName => $fieldVal){
				$fieldVal = trim($fieldVal);

				if( !in_array($fieldName, $arEditableFields)
					|| ($fieldName == 'NEW_PASSWORD' && empty($fieldVal))){
					continue;
				}

				if( $fieldName == 'NEW_PASSWORD' && !empty($fieldVal) ){
					$arNewValues['PASSWORD'] = $fieldVal;
					$arNewValues['CONFIRM_PASSWORD'] = $fieldVal;
				}

				$arNewValues[$fieldName] = $fieldVal;
			}
			$bUpdated = $obUser->update($arReq['USER_ID'],
				$arNewValues
			);
		}
		elseif( !empty($arReq['USER_ID']) && $arReq['ACTION'] == 'BLOCK_USER' ){
			$bUpdated = $obUser->update($arReq['USER_ID'],
				array(
					'ACTIVE' => $arReq['STATUS'],
				)
			);
		}
		else{
			return array('ERROR' => true);
		}

		if( $bUpdated ){
			return array('SUCCESS' => true);
		}
		else{
			return array('ERROR' => true, 'ERROR_TEXT' => $obUser->LAST_ERROR);
		}
	}


	/**
	 * Смена пароля пользователя
	 * 
	 * @return array
	 */
	public function changePasswordAction()
	{
		$this->view = new Mvc\View\Json();
		$this->returnAsIs = true;
		
		$arReq = Application::getInstance()->getContext()->getRequest()->toArray();
		if( !empty($arReq['USER_EMAIL']) ){
			$arUser = UserTable::getList(Array(
				"filter" => array('LOGIN' => $arReq['USER_EMAIL']),
				"select" => array('ID', 'NAME'),
				"limit" => 1
			))->fetch();

			if( !empty($arUser['ID']) ){
			    $newPwd = randString(6);
				$obUser = new \CUser();
				$bUpdated = $obUser->update($arUser['ID'], array('PASSWORD' => $newPwd, 'CONFIRM_PASSWORD' => $newPwd));
				if( $bUpdated ){
					return array('SUCCESS' => true, 'SUCCESS_MESSAGE' => 'Пароль&nbsp;успешно&nbsp;изменен!<br>Новый&nbsp;пароль&nbsp;отправлен на&nbsp;ваш&nbsp;email');
				}
			}
			else{
				return array('ERROR' => true, 'ERROR_MESSAGE' => 'Такого&nbsp;пользоватьеля&nbsp;не&nbsp;существует!');
			}
		}
	}


	/**
	 * Проверка текущего статуса пользователя заблокирован / разблокирован
	 *
	 * @return array
	 * @throws \Bitrix\Main\ArgumentException
	 */
	public function checkUserStatusAction()
	{
		$this->view = new Mvc\View\Json();
		$this->returnAsIs = true;

		global $USER;
		$arUser = UserTable::getList(
			array(
				'filter' => array('ID' => $USER->GetID()),
				'select' => array('ACTIVE')
			)
		)->fetch();

		if( $arUser['ACTIVE'] == 'N' ){
			$USER->Logout();
			return array('ERROR' => true, 'ERROR_TEXT' => 'Ваш аккаунт заблокирован!');
		}
		
		return array('SUCCESS' => true);
	}
}