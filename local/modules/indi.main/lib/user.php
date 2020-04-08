<?php
/**
 * Individ module
 * 
 * @category	Individ
 * @link		http://individ.ru
 * @revision	$Revision: 2062 $
 * @date		$Date: 2014-10-23 13:18:32 +0300 (Чт, 23 окт 2014) $
 */

namespace Indi\Main;

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
	 * Получение пользователей d7
	 *
	 * @param array $arParams аналогичен обычному ORM getList. Отличие в добавлении ключа cacheTime
	 *
	 * @return array|mixed
	 */
	public static function getUsersList($arParams)
	{
		if (empty($arParams) || !is_array($arParams)) {
			return false;
		}
		$cacheTime = in_array('cacheTime', $arParams) ? $arParams['cacheTime'] : 3600;
		unset($arParams['cacheTime']);
		$indexArray = false;
		if (key_exists('indexArray', $arParams)) {
			$indexArray = $arParams['indexArray'];
			unset($arParams['indexArray']);
		}

		$cache = new Cache(array(__METHOD__, $arParams), __CLASS__, $cacheTime);
		if ($cache->start()) {
			$rsUsers = UserTable::getList(
				$arParams
			);
			$arUsers = array();
			for ($index = 0; $arUser = $rsUsers->fetch(); $index++) {
				if($indexArray) {
					$arUser['INDEX'] = $index;
					$arUsers[$arUser[$indexArray]] = $arUser;
				}
				else {
					$arUsers[] = $arUser;
				}
			}
			if (!empty($arUsers)) {
				$cache->end($arUsers);
			} else {
				$cache->abort();
			}
		} else {
			$arUsers = $cache->getVars();
		}

		return $arUsers;
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
}