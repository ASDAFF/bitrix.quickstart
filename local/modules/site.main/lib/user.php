<?php
/**
 *  module
 * 
 * @category	
 * @link		http://.ru
 * @revision	$Revision: 2062 $
 * @date		$Date: 2014-10-23 13:18:32 +0300 (Чт, 23 окт 2014) $
 */

namespace Site\Main;

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Web\HttpClient;
use Site\Main\Hlblock\History;
use Site\Main\Hlblock\Packets;

/**
 * Утилиты для работы с пользователями
 */
class User
{
	/**
	 * Префикс констант для хранения идентификаторв групп, соответсвующих их символьным кодам
	 */
	const ID_CONSTANTS_PREFIX = '\GROUP_ID_';
	
	/**
	 * Singleton экземпляры
	 *
	 * @var array
	 */
	protected static $instances = array();
	
	/**
	 * ID пользователя
	 *
	 * @var integer
	 */
	protected $id = 0;
	
	/**
	* Полный список существующих групп
	* 
	* @var array
	*/
	protected static $allGroups = array();
	
	/**
	 * Константы были определены
	 *
	 * @var boolean
	 */
	protected static $constantsDefined = false;

	/**
	 * Положительный прирост рейтинга
	 *
	 * @var int
	 */
	public static $positiveDelta = 5;

	/**
	 * Отрицательный прирост рейтинга
	 *
	 * @var int
	 */
	public static $negativeDelta = 10;

	/**
	 * Конструктор
	 *
	 * @param integer $id Идентификатор пользователя
	 * @return void
	 */
	protected function __construct($id = 0)
	{
		if ($id) {
			$this->id = $id;
		}
		
		if (!$this->id) {
			throw new Exception('User id is undefined');
		}
	}
	
	/**
	 * Возвращает экземпляр пользователя по его ID
	 *
	 * @param integer $id Идентификатор пользователя
	 * @return User
	 */
	public static function getInstance($id = 0)
	{
		if (!$id && \CUser::IsAuthorized()) {
			$id = $GLOBALS['USER']->GetID();
		}
		
		if (!array_key_exists($id, self::$instances)) {
			self::$instances[$id] = new User($id);
		}

		return self::$instances[$id];
	}
	
	/**
	 * Возвращает идентификатор пользователя
	 *
	 * @return integer
	 */
	public function getId()
	{
		return $this->id;
	}
	
	/**
	 * Возвращает данные пользователя
	 * 
	 * @param integer $cacheTime Время кэширования
	 * @return array
	 */
	public function getData($cacheTime = 3600)
	{
		$data = array();
		$cache = new Cache(array(__METHOD__, $this->id), __CLASS__, $cacheTime);
		if ($cache->start()) {
			$data = \Bitrix\Main\UserTable::getRowById($this->id);
			if ($data) {
				$cache->end($data);
			} else {
				$cache->abort();
			}
		} else {
			$data = $cache->getVars();
		}
		
		return $data;
	}
	
	/**
	 * Возвращает значение поля учетной записи пользователя
	 * 
	 * @param string $field Название поля
	 * @return mixed
	 */
	public function getField($field)
	{
		$data = $this->getData();
		
		return array_key_exists($field, $data) ? $data[$field] : null;
	}
	
	/**
	 * Возвращает имя пользователя для вывода
	 * 
	 * @return string
	 */
	public function getName()
	{
		$data = $this->getData();
		
		$name = $data['LOGIN'];
		if (strlen($data['LAST_NAME']) > 0
			|| strlen($data['NAME']) > 0
			|| strlen($data['SECOND_NAME']) > 0
		) {
			$nameParts = array();
			if (strlen($data['LAST_NAME'])) {
				$nameParts[] = $data['LAST_NAME'];
			}
			if (strlen($data['NAME'])) {
				$nameParts[] = $data['NAME'];
			}
			if (strlen($data['SECOND_NAME'])) {
				$nameParts[] = $data['SECOND_NAME'];
			}
			
			$name = implode(' ', $nameParts);
		}
		
		return $name;
	}
	
	/**
	 * Проверяет принадлежность пользователя к группе/группам
	 * 
	 * @param integer|array $groupId Идентификатор(ы) группы
	 * @return boolean
	 */
	public function inGroup($groupId)
	{
		return (\Bitrix\Main\UserGroupTable::query()
			->setFilter(array(
				'USER_ID' => $this->id,
				'GROUP_ID' => $groupId,
			))
			->setSelect(array(
				'GROUP_ID',
			))
			->exec()
			->fetch()
		) ? true : false;
	}
	
	/**
	 * Возвращает список всех существующих групп пользователей
	 * 
	 * @param int $cacheTime Время кэширования
	 * @return array
	 */
	public static function getGroups($cacheTime = 3600)
	{
		if (self::$allGroups) {
			return self::$allGroups;
		}
		$cache = new Cache(__METHOD__, __CLASS__, $cacheTime);
		$result = array();
		if ($cache->start()) {
			$result = array();
			$groups = \Bitrix\Main\GroupTable::getList(array(
				'filter' => array(
					'ACTIVE' => 'Y',
				),
				'order' => 'C_SORT',
			));
			while ($group = $groups->fetch() ) {
				$result[$group['ID']] = $group;
			}
			$cache->end($result); 
		} else {
			$result = $cache->getVars();
		}
		
		self::$allGroups = $result;
		
		return $result;
	}
	
	/**
	 * Возвращает информацию о группе
	 * 
	 * @param integer $id Идентификатор группы
	 * @return array|null
	 */
	public static function getGroupData($id)
	{
		$groups = self::getGroups();
		
		return array_key_exists($id, $groups) ? $groups[$id] : null;
	}
	
	/**
	 * Определяет константы вида GROUP_ID_{CODE} для всех групп пользователей
	* 
	 * @return void
	*/
	public static function defineConstants()
	{
		if (self::$constantsDefined) {
			return;
		}
		
		$groups = self::getGroups();
		foreach ($groups as $group) {
			$code = $group['STRING_ID'];
			if (strlen($code)) {
				$const = __NAMESPACE__ . self::ID_CONSTANTS_PREFIX . $code;
				if (!defined($const)) {
					/**
					 * @ignore
					 */
					define($const, $group['ID']);
				}
			}
		}
		
		self::$constantsDefined = true;
	}
    
    /**
    * Получение списка пользователей через методы UserTable
    *
    * @param mixed $arFilter
    * @param mixed $arParams
    * @param mixed $sortBy
    * @param mixed $orderBy
    * @param mixed $cacheTime
    */
    public function getUserTableList($arFilter=array(), $arSelect=array(), $sortBy = "id", $orderBy = "desc", $cacheTime = 3600)
    {
        $data = array();
        $cache = new Cache(array(__METHOD__, $arFilter, $sortBy, $orderBy, $arSelect), __CLASS__, $cacheTime);
        if ($cache->start()) {
            $res = \Bitrix\Main\UserTable::getList(Array(
                "filter"=>$arFilter,
                "select"=>$arSelect,
                "data_doubling"=>false
            ));
            while($arUser = $res->Fetch()){
                $arResult[] = $arUser['ID'];
            }

            $data = $arResult;
            if ($data) {
                $cache->end($data);
            } else {
                $cache->abort();
            }
        } else {
            $data = $cache->getVars();
        }

        return $data;
    }
    
    
    /**
    * Возвращает данные массива пользователей с постраничкой
    *
    * @param integer $cacheTime Время кэширования, 
    * @param $arFilter - фильтр 
    * @param $arParams - Массив с дополнительными параметрами      
    * @param $sortBy - сортировка по 
    * @param $orderBy - сортировка как 
    * @return array
    */
    public function getList($arFilter=array(), $arParams=array(), $sortBy = "id", $orderBy = "desc", $cacheTime = 3600)
    {
        $data = array();
        $cache = new Cache(array(__METHOD__, $arFilter, $arParams, $sortBy, $orderBy), __CLASS__, $cacheTime);
        if ($cache->start()) {
            
            $arUsers = \CUser::GetList(($by=$sortBy), ($order=$orderBy), $arFilter, $arParams);
            while($arUser = $arUsers->GetNext()){
                $arResult['ITEMS'][$arUser["ID"]] = $arUser;
            }
            $arResult['NAV'] = $arUsers;
            $data = $arResult;
            if ($data) {
                $cache->end($data);
            } else {
                $cache->abort();
            }
        } else {
            $data = $cache->getVars();
        }

        return $data;
    }


	/**
	 * Получение информации о типах пользователей 
	 *
	 * @param     $arUTypesId - ID типов пользователей
	 * @param int $cacheTime - время кеширования
	 *
	 * @return array|mixed
	 */
	public function getUTypesInfo($cacheTime = 86400){
		$arUserTypes = array();
		$cache = new Cache(array(__METHOD__, $arUTypesId), __CLASS__, $cacheTime);
		if ($cache->start()) {
			/*Получаем типы пользователей*/
			$rsUTypes = \CUserFieldEnum::GetList(
				array(),
				array('USER_FIELD_ID' => 5)
			);

			if( $rsUTypes->SelectedRowsCount() > 0 ){
				while($arUType = $rsUTypes->GetNext()){
					$arUserTypes[$arUType['ID']] = $arUType['VALUE'];
				}

				$cache->end($arUserTypes);
			}
			else{
				$cache->abort();
			}
		}
		else{
			$arUserTypes = $cache->getVars();
		}

		return	$arUserTypes;
	}


	/**
	 * Возращает кол-во пользователей
	 * @param int $cacheTime
	 *
	 * @return int|mixed
	 */
	public static function getCount($cacheTime = 86400){
		return \CUser::GetList(($by=$sortBy), ($order=$orderBy), array('ACTIVE' => 'Y'), array(
			'FIELDS' => array('ID'),
		))->SelectedRowsCount();
	}


	/**
	 * Возвращает ифнормацию по пользователю
	 *
	 * @param $id - ID пользователя
	 *
	 * @return array
	 */
	public static function getUserInfo($id)
	{
		if( empty($id) ){
		    retutn;
		}
		
		$arUser = \CUser::GetList(($by = 'id'), ($order = 'asc'),
			array('ID' => $id),
			array(
				'FIELDS' => array('ID', 'EMAIL', 'NAME'),
				'SELECT' => array(
					'UF_RATING',
					'UF_USER_TYPE',
				)
			)
		)->fetch();

		return $arUser;
	}

	/**
	 * Обработчик события до регистрации пользователя
	 * 
	 * @param $arFields
	 */
	public function OnBeforeUserRegisterHandler(&$arFields)
	{
		global $APPLICATION;
		$arFields['LOGIN'] = $arFields['EMAIL'];
		$arFields['CONFIRM_PASSWORD'] = $arFields['PASSWORD'] = randString(6);
		$obContext = Application::getInstance()->getContext();
		$arServer = $obContext->getServer();
		$arReq = $obContext->getRequest()->toArray();
		$docRoot = $arServer->getDocumentRoot();
			
		$request = new HttpClient();
		$post = $request->post("https://www.google.com/recaptcha/api/siteverify", array(
			"secret" => \COption::GetOptionString('site.main', 'recaptcha_private_key'), //Наш секретный ключ от Google
			"response" => $arReq["g-recaptcha-response"], //Сам хеш с формы
			"remoteip" => $arServer["REMOTE_ADDR"] //IP адрес пользователя проходящего проверку
		));
		$post = json_decode($post); //Декодируем ответ от Google

		if ($post->success != 'true' || empty($post->success)) //Если проверка прошла удачно
		{
			$arFields['ERRORS'][] = 'Не пройдена капча';
			$APPLICATION->ThrowException('Не пройдена капча');
			return false;
		}

		/*
		 * Проверяем email по списку запрещенных
		 * */
		$arUnavailableEmails = explode("\n", file_get_contents($docRoot . '/includes/unavailable_emails.csv'));
		foreach( $arUnavailableEmails as $email ){
			if( preg_match('/' . trim($email) . '/', $arFields['EMAIL']) ){
				$arFields['ERRORS'][] = 'Некорректный email';
				$APPLICATION->ThrowException('Некорректный email');
				return false;
			}
		}

		/*Подписываем на рассылку*/
		if( !empty($arReq['REGISTER']['SUBSCRIBE']) ) {
			$arFields['UF_SUBSCRIBE'] = 1;
		}
	}

	/**
	 * Обработчик события после регистрации пользователя
	 *
	 * @param $arFields
	 */
	public function OnAfterUserRegisterHandler(&$arFields)
	{
		if( !empty($arFields['USER_ID']) ){
			$obUser = new \CUser();
			if( !empty($arFields['CONFIRM_PASSWORD']) ) {
				\CEvent::Send('NEW_USER_PASSWORD', 's1', array('EMAIL' => $arFields['EMAIL'], 'PASSWORD' => $arFields['CONFIRM_PASSWORD']));
			}
		}
	}

	/**
	 * Обработчик события до обновления пользователя
	 *
	 * @param $arFields
	 */
	public function OnBeforeUserUpdateHandler(&$arFields)
	{
		/*Сбрасываем каш по тегу user_list*/
		$GLOBALS['CACHE_MANAGER']->ClearByTag('users_list');
	}


	/**
	 * Обработчик события после обновления пользователя
	 *
	 * @param $arFields
	 */
	public function OnAfterUserUpdateHandler(&$arFields)
	{
		$arUser = \CUser::GetList(($by=$sortBy), ($order=$orderBy), array('ID' => $arFields['ID']), array(
			'FIELDS' => array('ID', 'NAME', 'PASSWORD', 'EMAIL', 'ACTIVE'),
			'SELECT' => array('UF_TRIAL')
		))->fetch();

		// Отвязываем все пакеты от пользователя
		if( $arUser['ACTIVE'] == 'N' ){
			// Логируем блокировку
			if( \COption::GetOptionString('site.main', 'log_user_block') == 'Y' ){
				\CEventLog::Add(array(
					"SEVERITY" => "SECURITY",
					"AUDIT_TYPE_ID" => "Блокировка пользователя",
					"MODULE_ID" => "main",
					"ITEM_ID" => '[' . $arUser['ID'] . '] ' . $arUser['NAME'],
					"DESCRIPTION" => "Пользователь заблокирован"
				));
			}

			/*
			 * Отвязка пакетов
			 * */
			$arPStatuses = Packets::getPStatusesValId();
			$arPackets = Packets::getInstance()->getData(
				array(
					'UF_STATUS' => array($arPStatuses['В работе'], $arPStatuses['На проверке']),
					'UF_USER' => $arUser['ID']
				),
				array('ID')
			);
			foreach($arPackets['ITEMS'] as $arPacket){
				Packets::unlinkFromUser($arPacket['ID']);
			}

			/*
			 * Отвязка пакетов в истории
			 * */
			$arHStatuses = History::getPHStatusesValId();
			$arHistory = History::getInstance()->getData(
				array(
					'UF_STATUS' => array($arHStatuses['В работе'], $arHStatuses['На проверке']),
					'UF_USER' => $arUser['ID']
				),
				array('ID', 'UF_PACKET')
			);
			foreach($arHistory['ITEMS'] as $arHist){
				History::unlinkFromUser($arHist['UF_PACKET'], $arUser['ID'], true);
			}
		}

		/*Отправляем нотификацию при изменении пароля*/
		if( !empty($arFields['CONFIRM_PASSWORD']) ){
			\CEvent::Send('UPDATE_USER_PWD', 's1', array('EMAIL' => $arUser['EMAIL'], 'PASSWORD' => $arFields['CONFIRM_PASSWORD']));
		}

		if( $arFields['ACTIVE'] == 'N' ){
			\CEvent::Send('USER_BLOCK', 's1', array('NAME' => $arFields['NAME'], 'EMAIL' => $arUser['EMAIL']));
		}

		// Логируем прочтение инструкции
		if( \COption::GetOptionString('site.main', 'log_user_download_manual') == 'Y' && $arFields['UF_DOWNLOAD_MANUAL'] ){
			\CEventLog::Add(array(
				"SEVERITY" => "SECURITY",
				"AUDIT_TYPE_ID" => "Пользователь скачал инструкцию",
				"MODULE_ID" => "main",
				"ITEM_ID" => "[". $arUser["ID"] . "] " . $arUser['NAME'],
				"DESCRIPTION" => "Пользователь скачал инструкцию"
			));
		}

		// Логируем скачивание FR
		if( \COption::GetOptionString('site.main', 'log_user_install_fr') == 'Y' && $arFields['UF_INSTALL_FR'] ){
			\CEventLog::Add(array(
				"SEVERITY" => "SECURITY",
				"AUDIT_TYPE_ID" => "Пользователь установил FR",
				"MODULE_ID" => "main",
				"ITEM_ID" => "[". $arUser["ID"] . "] " . $arUser['NAME'],
				"DESCRIPTION" => "Пользователь установил FR"
			));
		}

		// Логируем получение триала
		if( \COption::GetOptionString('site.main', 'log_user_get_trial') == 'Y' && $arFields['UF_GET_TRIAL'] ){
			\CEventLog::Add(array(
				"SEVERITY" => "SECURITY",
				"AUDIT_TYPE_ID" => "Пользователь получил триал",
				"MODULE_ID" => "main",
				"ITEM_ID" => "[". $arUser["ID"] . "] " . $arUser['NAME'],
				"DESCRIPTION" => "Пользователь получил триал " . $arUser['UF_TRIAL']
			));
		}
	}
}