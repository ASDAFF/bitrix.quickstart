<?
namespace Site\Main\Hlblock;
use Bitrix\Main\Type\DateTime;
use \Site\Main\Cache;

/**
 * Класс для работы с hload ом пакетов
 *
 * @category    
 * @package    Hlblock
 */
class Packets extends Prototype
{
	/**
	 * Возращает экземпляр hload пакетов
	 *
	 * @return Example
	 */
	public static function getInstance()
	{
		return parent::getInstance();
	}


	/**
	 * Все возможные статусы пакетов
	 *
	 * @param int $cacheTime
	 *
	 * @return array|mixed
	 */
	public static function getPStatusesValId($cacheTime = 86400)
	{
		$arStatuses = array();
		$cache = new Cache(array(__METHOD__), __CLASS__, $cacheTime);
		if( $cache->start() ) {
			/*Получаем типы пользователей*/
			$rsStatuses = \CUserFieldEnum::GetList(array(), array('USER_FIELD_ID' => '12'));

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
	 * Изменение статуса пакета
	 *
	 * @param        $packetId -  ID пакета
	 * @param string $status - новый статус
	 *
	 * @return object
	 */
	public static function changePacketStatus($packetId, $status = 'Активный')
	{
		if( empty($packetId) ){
		    return;
		}

		$arStatuses = static::getPStatusesValId();
		return static::getInstance()->updateData($packetId, array(
			'UF_STATUS' => $arStatuses[$status],
			'UF_DATE_CHANGE' => new DateTime()
		));
	}


	/**
	 * Привязка пакета к пользователю
	 *
	 * @param $userId - ID пользователя
	 * @param $packetId - ID пакета
	 *
	 * @return bool
	 * @throws \Exception
	 */
	public function linkPacketToUser($userId, $packetId)
	{
		if( empty($userId) || empty($packetId) ){
			throw new \Exception('Некорректные параметры для привязки пакета к пользователю');
		}

		$arStatuses = static::getPStatusesValId();

		/*Привязываем пакет к пользователю*/
		$bPUpdated = $this->updateData($packetId, array('UF_USER' => $userId, 'UF_DATE_CHANGE' => new DateTime(), 'UF_STATUS' => $arStatuses['В работе']));

		return $bPUpdated;
	}


	/**
	 * Отвязка пакета от пользователя
	 *
	 * @param        $packetId -  ID пакета
	 *
	 * @return object
	 */
	public static function unlinkFromUser($packetId)
	{
		if( empty($packetId) ){
			return;
		}

		$arStatuses = static::getPStatusesValId();
		return static::getInstance()->updateData($packetId, array('UF_STATUS' => $arStatuses['Активный'], 'UF_USER' => ''));
	}

	/**
	 * 1. Находит свободный пакет (к которому еще не привязан пользователь)
	 * 2. Возвращает ифнормацию по пакету, доступному для скачивания
	 *
	 * @return string | boolean
	 */

	public function getFreePacket() {
		$arPacket = array();
		$arStatuses = static::getPStatusesValId();
		$arFreePacks = $this->getElements(
			array(
				'filter' => array('UF_USER' => false, 'UF_STATUS' => $arStatuses['Активный']),
				'select' => array('ID', 'UF_NAME', 'UF_PACKET_FILE'),
				'limit' => 1,
				'cacheTime' => 0 // не кешируем запрос, так как методу нужны актуальне данные
			)
		);

		if(!empty($arFreePacks['ITEMS'])) {
			$arPacket = reset($arFreePacks['ITEMS']);
		}

		return $arPacket;
	}


	/**
	 * Получение пакета, находящегося в работе у текущего юзера
	 *
	 * @return array|void
	 * @throws \Exception
	 */
	public function checkPacketInWork()
	{
		$arStatuses = static::getPStatusesValId();
		$arPacketInWork = $this->getElements(
			array(
				'filter' => array('UF_USER' => $GLOBALS['USER']->GetId(), 'UF_STATUS' => $arStatuses['В работе']),
				'select' => array('ID', 'UF_NAME'),
				'limit' => 1,
				'cacheTime' => 0 // не кешируем запрос, так как методу нужны актуальне данные
			)
		);
		$arPacketInWork = reset($arPacketInWork['ITEMS']);
		if( !empty($arPacketInWork) ){
			throw new \Exception('Вы уже работаете над пакетом ' . $arPacketInWork['UF_NAME']);
			return;
		}

		return true;
	}


	/**
	 * Возвращает кол-во пакетов с каждым возможным статусом
	 * 
	 * @return array
	 */
	public function getPacketsStatuses()
	{
		$arResult = $arStatusesInfo = array();
		$arPackets = $this->getData(array(), array('ID', 'UF_STATUS'));
		foreach($arPackets['ITEMS'] as $arPacket){
			$arStatusesInfo[$arPacket['UF_STATUS']]++;
		}
		
		$arResult['STATUSES'] = $arStatusesInfo;
		$arResult['PACKETS_COUNT'] = count($arPackets);

		return $arResult;
	}
}