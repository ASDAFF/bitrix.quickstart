<?
namespace Site\Main\Hlblock;
use Bitrix\Main\UserTable;
use \Site\Main\Cache;
use Bitrix\Main\Entity\Event;
use \Bitrix\Main\Type\DateTime;
use Site\Main\User;

/**
 * Класс для работы с hload ом пакетов
 *
 * @category
 * @package    Hlblock
 */
class History extends Prototype
{
	/**
	 * Возращает экземпляр hload истории пакетов
	 *
	 * @return Example
	 */
	public static function getInstance()
	{
		return parent::getInstance();
	}

	/**
	 * Все возможные статусы истории пакетов
	 *
	 * @param int $cacheTime
	 *
	 * @return array|mixed
	 */
	public static function getPHStatusesValId($cacheTime = 86400)
	{
		$arStatuses = array();
		$cache = new Cache(array(__METHOD__), __CLASS__, $cacheTime);
		if( $cache->start() ) {
			/*Получаем типы пользователей*/
			$rsStatuses = \CUserFieldEnum::GetList(array(), array('USER_FIELD_ID' => '23'));

			if( $rsStatuses->SelectedRowsCount() > 0 ) {
				while($arStatus = $rsStatuses->fetch()){
					$arStatuses[$arStatus['VALUE']] = $arStatus['ID'];
				}

				$cache->end($arStatuses);
			}
			else {
				$cache->abort();
			}
		}
		else{
			$arStatuses = $cache->getVars();
		}

		return	$arStatuses;
	}

	/**
	 * Получение id статуса по его xml_id
	 *
	 * @param string $statusValXml
	 * @param integer $cacheTime
	 * @return integer mixed
	 */
	public function getStatusValIdByXmlId($statusValXml, $cacheTime) {
		$cache = $this->getCache(array(__METHOD__, $statusValXml), $cacheTime);
		if ($cache->start()) {
			$rsStatuses = \CUserFieldEnum::GetList(array(), array('XML_ID' => $statusValXml));
			$arStatus = $rsStatuses->fetch();
			$statusId = $arStatus['ID'];
			if(!empty($arResult)) {
				$cache->end($statusId);
			}
			else {
				$cache->abort();
			}
		}
		else {
			$statusId = $cache->getVars();
		}
		return $statusId;
	}

	/**
	 * Получение количества пакетов пользователя по различным статусам
	 *
	 * @param integer $userId
	 * @return array
     */
	public function getUserPacketsCount($userId) {
		$arResult = array();

		$arStatuses = $this->getPHStatusesValId();
		$arHistory = $this->getElements(
			array(
				'cacheTime' => 0, // нужны актуальне данные
				'filter' => array('UF_USER' => $userId)
			)
		);
		foreach($arHistory['ITEMS'] as $arHist){
			$status = array_search($arHist['UF_STATUS'], $arStatuses);
			$arResult[$status]++;
		}

		return $arResult;
	}


	/**
	 * Создание новой записи истории
	 *
	 * @param $userId - ID пользователя
	 * @param $packetId - Номер пакета
	 * @param $status - Статус, с которым создается запись
	 *
	 * @return mixed - ID созданной записи или false в случае ошибки
	 */
	public function addHistoryRow($userId, $packetId, $status)
	{
		if( empty($userId) || empty($packetId) || empty($status) ){
			throw new \Exception('Неверные данные для создания записи истории');
		}
		
		$arHistoryStatuses = static::getPHStatusesValId();
		$historyId = $this->addData(
			array(
				'UF_USER' => $userId,
				'UF_PACKET' => $packetId,
				'UF_STATUS' => $arHistoryStatuses[$status],
				'UF_DATE_START' => new DateTime()
			)
		);

		return $historyId;
	}


	/**
	 * Отклонение пакета
	 *
	 * @param        $packetId -  ID пакета
	 * @param        $userId -  ID юзера
	 *
	 * @return object
	 */
	public static function unlinkFromUser($packetId, $userId, $bTimeOut = false)
	{
		if( empty($packetId) || empty($userId) ){
			return;
		}

		$arStatuses = static::getPHStatusesValId();
		
		$arHist = static::getInstance()->getData(array('UF_PACKET' => $packetId, 'UF_USER' => $userId), array('ID'));
		$arHist = reset($arHist['ITEMS']);
		$newStatus = ( $bTimeOut ) ? $arStatuses['Просрочен'] : $arStatuses['Отклонен'];

		return static::getInstance()->updateData($arHist['ID'], array('UF_STATUS' => $newStatus));
	}



	/**
	 * Обработчик обновления истории
	 *
	 * @param Event $event
	 * @throws \Bitrix\Main\NotImplementedException
	 */
	public function UpdateHandler(Event $event) {
		$user = new \CUser;
		// получение данных о элементе
		$id = $event->getParameter("id");
		$arElement = $this->getElements(
			array(
				"select" => array('ID', 'UF_USER'),
				"filter" => array('ID' => $id),
				"cacheTime" => 0
			)
		);

		// Обновление кол-ва проверенных афиш и рейтинга у пользователя
		$arElement = reset($arElement["ITEMS"]);
		$userId = $arElement['UF_USER'];
		$arHistStatuses = $this->getUserPacketsCount($userId);
		$rating = $arHistStatuses['Принят'] * User::$positiveDelta - $arHistStatuses['Отклонен'] * User::$negativeDelta;

		$bUpdated = $user->Update($userId, array('UF_PROGRAM' => $arHistStatuses['Принят'], 'UF_RATING' => $rating));

		if( \COption::GetOptionString('site.main', 'log_user_update_rating') == 'Y' && $bUpdated ){
			\CEventLog::Add(array(
				"SEVERITY" => "SECURITY",
				"AUDIT_TYPE_ID" => 'Рейтинг пользователя обновлен',
				"MODULE_ID" => "main",
				"ITEM_ID" => $userId,
				"DESCRIPTION" => "Рейтинг пользователя " . $userId . " обновлен. Текущий рейтинг " . $rating
			));
		}
	}
}