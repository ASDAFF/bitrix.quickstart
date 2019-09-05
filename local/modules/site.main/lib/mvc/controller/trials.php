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
use Site\Main as Main;
use Bitrix\Main\Application as Application;
use Site\Main\Mvc as Mvc;

/**
 * Контроллер для триалок
 *
 * @category
 * @package		MVC
 */
class Trials extends Prototype
{
	/**
	 * Присваивает триалку пользователю
	 *
	 * @return string
	 */
	public function getTrialAction()
	{
		global $USER;
		$arResult = array();
		$this->view = new Mvc\View\Json();
		$this->returnAsIs = true;
		$docRoot = Application::getInstance()->getDocumentRoot();
		$uid = $USER->GetId();

		if( !$USER->IsAuthorized() ){
			return array('ERROR' => true, 'ERROR_MESSAGE' => 'Доступ запрещен!');
		}

		// Проверяем, есть ли триал у пользователя уже.
		// Если есть, то возвращаем его.
		// Если нет, то получаем новый
		$arUser = UserTable::getList(
			array(
				'select' => array('ID', 'UF_TRIAL'),
				'filter' => array('ID' => $uid),
				'limit' => 1
			)
		)->fetchAll();
		if( !empty($arUser[0]['UF_TRIAL']) ){
			$arResult = array('SUCCESS' => true, 'TRIAL_KEY' => $arUser[0]['UF_TRIAL']);
		}
		else{
			/*Открываем файл триалок для чтения и записи*/
			$trials = file_get_contents($docRoot . "/serials4999.csv");
			if ( !empty($trials) ) {
				$arTrials = preg_split('/[(\r)?\n]/mi', $trials);
				$currentTrial = reset($arTrials);
				$arNewTrials = array();

				/*Присваиваем информацию по триалке пользователю и обновляем файл*/
				if( !empty($currentTrial) ){
					unset($arTrials[0]);

					foreach($arTrials as $key){
						$arNewTrials[] = $key;
					}
					file_put_contents($docRoot . "/serials4999.csv", implode("\n", $arNewTrials));

					$obUser = new \CUser();
					$bUpdated = $obUser->update($uid, array(
						'UF_TRIAL' => $currentTrial
					));

					if( $bUpdated ){
						$arResult = array('SUCCESS' => true, 'TRIAL_KEY' => $currentTrial);
					}
				}
				else{
					$arResult['ERROR'] = true;
				}
			}
		}

		return $arResult;
	}
}