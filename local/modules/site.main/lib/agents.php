<?
use Site\Main\Hlblock\Packets;
use Site\Main\Hlblock\History;
use Site\Main\User;

/**
 *  module
 *
 * @category
 * @link		http://.ru
 * @revision	$Revision$
 * @date		$Date$
 */

namespace Site\Main;

/**
 * Модель кэша
 */
class Agents
{
	public static function sendNotification()
	{
		$obPackets = \Site\Main\Hlblock\Packets::getInstance();
		/*Получаем статусы по всем пакетам*/
		$arPackets = $obPackets->getPacketsStatuses();
		/*Получаем информацию по возможным статусам пакетов*/
		$arStatusesInfo = $obPackets->getPStatusesValId();

		/*Кол-во доступных пакетов*/
		$activePacketsCnt = $arPackets['STATUSES'][$arStatusesInfo['Активный']];

		// Отправляем пользователям нотификацию о том, что до конца верификации осталось менее 2 часов
		$arStatusesInfo = \Site\Main\Hlblock\History::getPHStatusesValId();
		$arPacketsHistory = \Site\Main\Hlblock\History::getInstance()->getData(
			array(
				'UF_STATUS' => $arStatusesInfo['В работе'],
				'<=UF_DATE_START' => ConvertTimeStamp(time() - (2 * 86400) + (2 * 3600), 'FULL')
			)
		);
		$arUsersId = $arTimeOutPackets = $arDeadlinePackets = $arPackets = array();
		foreach($arPacketsHistory['ITEMS'] as $arHist){
			$arPackets[$arHist['UF_PACKET']] = array();
			$arUsersId[$arHist['UF_USER']] = array();
			$date = $arHist['UF_DATE_START']->toString(new \Bitrix\Main\Context\Culture(array("FORMAT_DATETIME" => "DD.MM HH:MI:SS")));
			$datetime = \DateTime::createFromFormat('d.m H:i:s', $date);
			$packetTimestamp = $datetime->getTimestamp();

			if( time() - ($packetTimestamp + 2 * 86400) >= 0){
				$arTimeOutPackets[$arHist['UF_USER']] = array(
					'PACKET_ID' => $arHist['UF_PACKET']
				);
			}
			else{
				$arDeadlinePackets[$arHist['UF_USER']] = array(
					'PACKET_ID' => $arHist['UF_PACKET'],
					'USER_ID' => $arHist['UF_USER']
				);
			}
		}
		if( !empty($arPackets) ){
			$arPackets = \Site\Main\Hlblock\Packets::getInstance()->getData(array('ID' => array_keys($arPackets)), array('ID', 'UF_NAME'));
		}
		if( !empty($arUsersId) ){
			$arUsersId = implode('|', array_keys($arUsersId));
			$arUsers = \Site\Main\User::getList(
				array('ID' => $arUsersId),
				array(
					'FIELDS' => array(
						'ID',
						'EMAIL',
						'NAME'
					),
				)
			);

			$bLogTimeoutPackets = \COption::GetOptionString('site.main', 'log_packets_timeout') == 'Y';
			foreach($arTimeOutPackets as $userId => $arPacket){
				\Site\Main\Hlblock\Packets::unlinkFromUser($arPacket['PACKET_ID']);
				\Site\Main\Hlblock\History::unlinkFromUser($arPacket['PACKET_ID'], $userId, true);

				if( $bLogTimeoutPackets ){
					\CEventLog::Add(array(
						"SEVERITY" => "SECURITY",
						"AUDIT_TYPE_ID" => 'Пакет просрочен',
						"MODULE_ID" => "main",
						"ITEM_ID" => "[". $arPacket['PACKET_ID'] . "] ",
						"DESCRIPTION" => "Пакет [" . $arPacket['PACKET_ID'] . "] " . $arPackets['ITEMS'][$arPacket['PACKET_ID']]['UF_NAME'] . " пользователя " . $userId . " просрочен"
					));
				}

				\CEvent::Send('VERIFICATION_TIME_OUT_BLOCK', 's1', array(
					'NAME' => $arUsers['ITEMS'][$userId]['NAME'],
					'EMAIL' => $arUsers['ITEMS'][$userId]['EMAIL'],
					'PACKET' => $arPackets['ITEMS'][$arPacket['PACKET_ID']]['UF_NAME']
				));
			}

			foreach($arDeadlinePackets as $arPacket){
				\CEvent::Send('VERIFICATION_TIME_OUT', 's1', array(
					'NAME' => $arUsers['ITEMS'][$arPacket['USER_ID']]['NAME'],
					'EMAIL' => $arUsers['ITEMS'][$arPacket['USER_ID']]['EMAIL'],
					'PACKET' => $arPackets['ITEMS'][$arPacket['PACKET_ID']]['UF_NAME']
				));
			}
		}

		return 'Site\Main\Agents::sendNotification();';
	}

}
