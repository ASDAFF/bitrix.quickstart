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
use Site\Main\Hlblock\Packets;
use \Site\Main\Amazon;

/**
 * Контроллер для гридов
 *
 * @category
 * @package		MVC
 */
class Grids extends Prototype
{
	


	/**
	 * Обновляет информацию по пакетам
	 *
	 * @return array
	 */
	public function updatePInfoAction()
	{
		$this->view = new Mvc\View\Json();
		$this->returnAsIs = true;
		$arReq = Application::getInstance()->getContext()->getRequest()->toArray();

		/*Получаем все возможные статусы пакета*/
		$bUpdated = false;

		/*Конфиг подключения к амазону*/
		$arConfig = Amazon::$arConfig;
		/*Создаем объект SDK класса*/
		$obSdk = new \Aws\Sdk($arConfig);
		/*Создаем объект S3 класса*/
		$obS3 = $obSdk->createS3();

		/*Получаем информацию по изменяемому пакету*/
		if( !empty($arReq['PACKET_ID']) ){
		    $arPacket = Packets::getInstance()->getData(array('ID' => $arReq['PACKET_ID']), array('UF_NAME', 'UF_PACKET_FILE', 'UF_USER'));
			$arFilename = explode('/', $arPacket[0]['UF_PACKET_FILE']);
		}

		/*Загружаем новый файл на амазон*/
		if( !empty($arReq['PACKET_ID']) && $arReq['ACTION'] == 'CONFIRM_UPLOAD' ){
			$bUpdated = $obS3->putObject(array(
				'Bucket'       => 'all-packets',
				'Key'          => 'finished/' . $_FILES['PACKET_FILE']['name'] ,
				'SourceFile'   => $_FILES['PACKET_FILE']['tmp_name'],
				'ContentType'  => 'text/plain',
			));

			if( !empty($bUpdated) ){
			    $bNewPacketFile = true;
			}
		}

		/*Принимаем пакет*/
		if( (!empty($arReq['PACKET_ID']) && $arReq['ACTION'] == 'CONFIRM') || $bNewPacketFile ){
			$bUpdated = Packets::changePacketStatus((int)$arReq['PACKET_ID'], 'Принят');
			
			if( $bUpdated ){
				/*Переносим пакет в папку принятых пакетов*/
				$bUpdated = $obS3->copyObject(
					array(
						'Bucket' => 'all-packets',
						'Key' => 'finished/' . end($arFilename),
						'CopySource' => 'all-packets/available/' . end($arFilename),
					)
				);

				/*Обновляем рейтинг пользователя*/
				$arUserInfo = Main\User::getUserInfo($arPacket[0]['UF_USER']);
				if( !empty($arUserInfo) ){
					$obUser = new \CUser();
					$bUpdated = $obUser->update($arPacket[0]['UF_USER'],
						array(
							'UF_RATING' => $arUserInfo['UF_RATING'] + Main\User::$positiveDelta,
							'UF_USER_TYPE' => $arUserInfo['UF_USER_TYPE']
						)
					);
				}
			}
		}

		/*Отклоняем пакет*/
		if( !empty($arReq['PACKET_ID']) && $arReq['ACTION'] == 'CANCEL' ){
			$bUpdated = Packets::unlinkFromUser((int)$arReq['PACKET_ID']);

			if( $bUpdated ){
				/*Обновляем рейтинг пользователя*/
				$arUserInfo = Main\User::getUserInfo($arPacket[0]['UF_USER']);
				$obUser = new \CUser();
				$bUpdated = $obUser->update($arPacket[0]['UF_USER'],
					array(
						'UF_RATING' => $arUserInfo['UF_RATING'] - Main\User::$negativeDelta,
						'UF_USER_TYPE' => $arUserInfo['UF_USER_TYPE']
					)
				);
				
				\CEvent::Send('PACKET_CANCELED', 's1', array('PACKET_ID' => $arPacket[0]['UF_NAME'], 'EMAIL' => $arUserInfo['EMAIL']));
			}
		}


		if( $bUpdated ){
			return array('SUCCESS' => true);
		}
		else{
			return array('ERROR' => true);
		}
	}
}