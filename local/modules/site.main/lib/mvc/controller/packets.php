<?
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

require $_SERVER['DOCUMENT_ROOT'] . '/local/modules/site.main/lib/aws/aws-autoloader.php';

use Bitrix\Main\Application;
use Site\Main as Main;
use Site\Main\Mvc as Mvc;
use Site\Main\Mvc\View;
use \Bitrix\Main\Loader;
use \Site\Main\Amazon;
use Site\Main\Hlblock\Packets as hlPackets;
use Site\Main\Hlblock\History as hlHistory;
use \Bitrix\Main\Type\DateTime;

/**
 * Контроллер для пакетов
 *
 * @category
 * @package		MVC
 */
class Packets extends Prototype
{
	/**
	 * Обновляет информацию по пакетам
	 *
	 * @return array
	 */
	public function updatePInfoAction()
	{
		global $USER;
		$this->view = new Mvc\View\Json();
		$this->returnAsIs = true;
		$arReq = Application::getInstance()->getContext()->getRequest()->toArray();

		/*Получаем все возможные статусы пакета*/
		$bUpdated = false;
		$errors = '';

		/*Конфиг подключения к амазону*/
		$arConfig = Amazon::getConfig();
		/*Создаем объект SDK класса*/
		$obSdk = new \Aws\Sdk($arConfig);
		/*Создаем объект S3 класса*/
		$obS3 = $obSdk->createS3();
		$amazonDirectory = \COption::getOptionString('site.main', 'amazon_directory');
		$currentUid = $USER->GetID();
		$arUserGroups = $USER->GetUserGroup($currentUid);
		$bUserAdmin = in_array(\Site\Main\GROUP_ID_PORTAL_ADMIN, $arUserGroups);

		/*
		 * Получаем информацию по изменяемому пакету
		 * */
		if( !empty($arReq['PACKET_ID']) ){
			$arPackets = hlPackets::getInstance()->getData(array('ID' => $arReq['PACKET_ID']), array('ID', 'UF_NAME', 'UF_PACKET_FILE', 'UF_USER'));
			$arPacket = reset($arPackets['ITEMS']);
			$arPacketHistory = hlHistory::getInstance()->getData(
				array(
					'UF_USER' => $arPacket['UF_USER'],
					'UF_PACKET' => $arPacket['ID']
				),
				array('ID'),
				array('ID' => 'DESC')
			);
			$arPacketHistory = reset($arPacketHistory['ITEMS']);

			// Статусы истории изменений пакетов
			$arHistStatuses = hlHistory::getPHStatusesValId();
		}


		/*
		 * Сдаем пакет на проверку
		 * */
		if( !empty($arReq['PACKET_ID']) && $arReq['ACTION'] == 'PASS' ){
			if( $arPacket['UF_USER'] != $currentUid ){
				return array('ERROR' => true, 'ERROR_MESSAGE' => 'Вы не можете сдать пакет, не принадлежащий вам!');
			}
			if( $arPacket['UF_NAME'] != $_FILES['PACKET_FILE']['name'] ){
				$bUpdated = false;
				$errors .= 'Имя загружаемого файла не соответствует исходному';
			}
			else{
				$bUploaded = $obS3->putObject(array(
					'Bucket'       => $amazonDirectory,
					'Key'          => 'check/' . $_FILES['PACKET_FILE']['name'] ,
					'SourceFile'   => $_FILES['PACKET_FILE']['tmp_name'],
					'ContentType'  => 'text/plain',
					'ACL' => 'public-read'
				));

				$bHistUpdated = hlHistory::getInstance()->updateData(
					(int)$arPacketHistory['ID'],
					array(
						'UF_STATUS' => $arHistStatuses['На проверке'],
					)
				);

				$bPacketUpdated = hlPackets::changePacketStatus((int)$arReq['PACKET_ID'], 'На проверке');

				if( $bUploaded && $bHistUpdated && $bPacketUpdated ){
					$bUpdated = true;
				}

				// Логируем отправку пакета на проверку
				if( \COption::GetOptionString('site.main', 'log_packets_check') == 'Y' && $bUpdated ){
					\CEventLog::Add(array(
						"SEVERITY" => "SECURITY",
						"AUDIT_TYPE_ID" => 'Пакет отправлен на проверку',
						"MODULE_ID" => "main",
						"ITEM_ID" => "[". $arPacket['ID'] . "] ",
						"DESCRIPTION" => "Пакет [" . $arReq['PACKET_ID'] . "] " . $arPacket['UF_NAME'] . " пользователя " . $arPacket['UF_USER'] . " отправлен на проверку"
					));
				}
			}
		}


		/*
		 * Загружаем новый файл на амазон
		 * */
		if( !empty($arReq['PACKET_ID']) && $arReq['ACTION'] == 'CONFIRM_UPLOAD' ){
			if( !$bUserAdmin ){
				return array('ERROR' => true, 'ERROR_MESSAGE' => 'Доступ запрещен!');
			}
			if( $arPacket['UF_NAME'] != $_FILES['PACKET_FILE']['name'] ){
				$bUpdated = false;
				$errors .= 'Имя загружаемого файла не соответсвует исходному';
			}
			else{
				$bUpdated = $obS3->putObject(array(
					'Bucket'       => $amazonDirectory,
					'Key'          => 'finished/' . $_FILES['PACKET_FILE']['name'] ,
					'SourceFile'   => $_FILES['PACKET_FILE']['tmp_name'],
					'ContentType'  => 'text/plain',
				));
			}

			if( !empty($bUpdated) ){
				$bNewPacketFile = true;
			}
		}


		/*
		 * Принимаем пакет
		 * */
		if( (!empty($arReq['PACKET_ID']) && $arReq['ACTION'] == 'CONFIRM') || $bNewPacketFile ){
			if( !$bUserAdmin ){
				return array('ERROR' => true, 'ERROR_MESSAGE' => 'Доступ запрещен!');
			}
			$bPacketUpdated = hlPackets::changePacketStatus((int)$arReq['PACKET_ID'], 'Принят');
			$bHistoryUpdated = hlHistory::getInstance()->updateData(
				(int)$arPacketHistory['ID'],
				array('UF_STATUS' => $arHistStatuses['Принят'])
			);

			$bUpdated = $bPacketUpdated && $bHistoryUpdated;

			// Логируем принятие пакета
			if( \COption::GetOptionString('site.main', 'log_packets_confirm') == 'Y' && $bUpdated ){
				$desc = "Пакет [" . $arReq['PACKET_ID'] . "] " . $arPacket['UF_NAME'] . " пользователя " . $arPacket['UF_USER'] . " принят";
				$desc = ( $bNewPacketFile ) ? $desc . ' и загружен' : $desc;
				\CEventLog::Add(array(
					"SEVERITY" => "SECURITY",
					"AUDIT_TYPE_ID" => ( $bNewPacketFile ) ? 'Пакет принят и загружен' : 'Пакет принят',
					"MODULE_ID" => "main",
					"ITEM_ID" => "[". $arPacket['ID'] . "] ",
					"DESCRIPTION" => $desc
				));
			}

			if( $bUpdated
				&& $arReq['ACTION'] != 'CONFIRM_UPLOAD'
				&& $obS3->doesObjectExist($amazonDirectory, 'check/' . $arPacket['UF_NAME'])
				&& $obS3->doesObjectExist($amazonDirectory, 'available/' . $arPacket['UF_NAME']) ){
				/*Переносим пакет в папку принятых пакетов*/
				$bUpdated = $obS3->copyObject(
					array(
						'Bucket' => $amazonDirectory,
						'Key' => 'finished/' . $arPacket['UF_NAME'],
						'CopySource' => $amazonDirectory . '/check/' . $arPacket['UF_NAME'],
					)
				);
			}

			/*Обновляем рейтинг пользователя*/
			$arUserInfo = Main\User::getUserInfo($arPacket['UF_USER']);
			if( !empty($arUserInfo) ){
				/*Уведомляем пользователя о принятии пакета*/
				$bUpdated = \CEvent::Send('PACKET_CONFIRM', 's1',
					array(
						'PACKET_ID' => $arPacket['UF_NAME'],
						'EMAIL' => $arUserInfo['EMAIL'],
						'NAME' => $arUserInfo['NAME'],
						'RATING' => $arUserInfo['UF_RATING']
					)
				);
			}
		}


		/*Удаляем исходник*/
		if( !empty($arReq['PACKET_ID'])
			&& $bUpdated
			&& $obS3->doesObjectExist($amazonDirectory, 'available/' . $arPacket['UF_NAME'])
			&& in_array($arReq['ACTION'], array('CONFIRM', 'CONFIRM_UPLOAD'))
		){
			$bUpdated = $obS3->deleteObject(array(
				'Bucket'  => $amazonDirectory,
				'Key' => 'available/' . $arPacket['UF_NAME']
			));
		}

		/*
		 * При приеме или отклонении пакета, удаляем его из папки проверки
		 * */
		if( !empty($arReq['PACKET_ID']) &&
			($arReq['ACTION'] == 'CANCEL' || $arReq['ACTION'] == 'CONFIRM' || $bNewPacketFile)
		){
			if( $obS3->doesObjectExist($amazonDirectory, 'check/' . $arPacket['UF_NAME']) ){
				$bUpdated = $obS3->deleteObject(
					array(
						'Bucket'  => $amazonDirectory,
						'Key' => 'check/' . $arPacket['UF_NAME']
					)
				);
			}
		}


		/*
		 * Отклоняем пакет
		 * */
		if( !empty($arReq['PACKET_ID']) && $arReq['ACTION'] == 'CANCEL' ){
			if( !$bUserAdmin ){
				return array('ERROR' => true, 'ERROR_MESSAGE' => 'Доступ запрещен!');
			}

			$bUpdated = hlPackets::unlinkFromUser((int)$arReq['PACKET_ID']);

			if( $bUpdated ){
				/*Обновляем рейтинг пользователя*/
				$arUserInfo = Main\User::getUserInfo($arPacket['UF_USER']);
				$obUser = new \CUser();
				$newRating = $arUserInfo['UF_RATING'] - Main\User::$negativeDelta;
				$bUserBlocked = ( $newRating <= -70 ) ? true : false;
				$bUpdated = $obUser->update($arPacket['UF_USER'],
					array(
						'UF_RATING' => $newRating,
						'ACTIVE' => ( $bUserBlocked ) ? 'N' : 'Y',
						'UF_USER_TYPE' => $arUserInfo['UF_USER_TYPE'],
					)
				);
				$bUpdated = hlHistory::getInstance()->updateData(
					(int)$arPacketHistory['ID'],
					array(
						'UF_STATUS' => $arHistStatuses['Отклонен'],
					)
				);

				// Логируем отклонение пакета
				if( \COption::GetOptionString('site.main', 'log_packets_cancel') == 'Y' && $bUpdated ){
					\CEventLog::Add(array(
						"SEVERITY" => "SECURITY",
						"AUDIT_TYPE_ID" => "Пакет отклонен",
						"MODULE_ID" => "main",
						"ITEM_ID" => "[". $arPacket['ID'] . "] ",
						"DESCRIPTION" => "Пакет [" . $arReq['PACKET_ID'] . "] " . $arPacket['UF_NAME'] . " пользователя " . $arPacket['UF_USER'] . " отклонен"
					));
				}

				/*Уведомляем пользователя об отклонении пакета*/
				\CEvent::Send('PACKET_CANCELED', 's1',
					array(
						'PACKET_ID' => $arPacket['UF_NAME'],
						'EMAIL' => $arUserInfo['EMAIL'],
						'NAME' => $arUserInfo['NAME'],
						'RATING' => $newRating,
						'REASONS' => ' - ' . str_replace('|', '<br> - ', $arReq['REASONS'])
					)
				);
			}
		}

		if( $bUserBlocked ){
			return array('ERROR' => true, 'ERROR_MESSAGE' => 'Ваш пользователь заблокирован!');
		}
		elseif( $bUpdated ){
			return array('SUCCESS' => true);
		}
		else{
			return array('ERROR' => true, 'ERROR_MESSAGE' => $errors);
		}
	}


	/**
	 * Возвращает форму выбора причины отклонения пакета
	 *
	 * @return string
	 */
	public function getCancelReasonsAction()
	{
		$this->view = new Mvc\View\Html();
		$this->returnAsIs = true;

		$reasons = '';
		$arReq = Application::getInstance()->getContext()->getRequest()->toArray();

		if( !empty($arReq['PACKET_ID']) ){
		    $reasons = new View\Php('grids/packet-cancel-actions.php', array('PACKET_ID' => $arReq['PACKET_ID']));
			$reasons = $reasons->render();
		}

		return $reasons;
	}

	/**
	 * Получение пользователем пакета
	 *
	 * @return string
	 */
	public function getPacketAction()
	{
		global $USER;

		if( !$USER->IsAuthorized() ){
			return array('ERROR' => true, 'ERROR_MESSAGE' => 'Доступ запрещен!');
		}

		$this->view = new Mvc\View\Json();
		$this->returnAsIs = true;
		$userId = $USER->GetID();
		$arReq = Application::getInstance()->getContext()->getRequest()->toArray();
		$docRoot = Application::getInstance()->getDocumentRoot();
		$obPackets = hlPackets::getInstance();
		$obHistory = hlHistory::getInstance();
		
		try{
			$bPInWork = $obPackets->checkPacketInWork();
		}
		catch(\Exception $e){
			return array('ERROR' => true, 'ERROR_MESSAGE' => $e->getMessage());
		}
		
		/*Получаем свободный пакет и привязываем к пользователю*/
		$arPacket = $obPackets->getFreePacket();
		try{
			$bPacketLinked = $obPackets->linkPacketToUser($userId, $arPacket['ID']);
			if( $bPacketLinked ){
				$obUser = new \CUser();
				$obUser->update($userId, array('UF_GET_PACK' => 'Y'));
			}
			sleep(rand(1, 3));
			$arPacketOwner = $obPackets->getData(array('ID' => $arPacket['ID']), array('ID', 'UF_USER', 'UF_NAME'), array(), null, 1);
			$arPacketOwner = reset($arPacketOwner['ITEMS']);

			if( $arPacketOwner['UF_USER'] != $userId ){
				return array('ERROR' => true, 'ERROR_MESSAGE' => 'К сожалению пакет достался другому пользователю. Нажмите кнопку еще раз');
			}
			else{
				$bHistoryAdded = $obHistory->addHistoryRow($userId, $arPacket['ID'], 'В работе');

				// Логируем получение пакета
				if( \COption::GetOptionString('site.main', 'log_packets_get') == 'Y' ){
					\CEventLog::Add(array(
						"SEVERITY" => "SECURITY",
						"AUDIT_TYPE_ID" => "Пакет получен пользователем",
						"MODULE_ID" => "main",
						"ITEM_ID" => "[". $arPacket['ID'] . "] ",
						"DESCRIPTION" => "Пакет " . "[" . $arPacket['ID'] . "] " . $arPacketOwner['UF_NAME'] . " получен пользователем " . $userId
					));
				}
			}
		}
		catch(\Exception $e){
			return array('ERROR' => true, 'ERROR_MESSAGE' => $e->getMessage());
		}

		return array('SUCCESS' => true, 'FILE_SRC' => $arPacket['UF_PACKET_FILE']);
	}
}