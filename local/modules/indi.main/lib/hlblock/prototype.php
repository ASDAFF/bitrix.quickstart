<?php
/**
 * Individ module
 *
 * @category       Individ
 * @package        Hlblock
 * @link           http://individ.ru
 * @revision    $Revision: 2062 $
 * @date        $Date: 2014-10-23 14:18:32 +0400 (Чт, 23 окт 2014) $
 */
namespace Indi\Main\Hlblock;

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Loader;
use Indi\Main as Main;
use Bitrix\Main\Entity;
use Bitrix\Highloadblock as HL;
use Indi\Main\Util;

if (!Loader::includeModule('highloadblock')) {
	throw new Main\Exception("Highloadblock module is't installed.");
}

/**
 * Прототип highload-блока
 *
 * @category       Individ
 * @package        Hlblock
 */
class Prototype extends Entity\DataManager
{

	/**
	 * Префикс констант для хранения идентификаторов highload-блоков, соответсвующих кодам
	 */
	const ID_CONSTANTS_PREFIX = '\ID_';

	/**
	 * Префикс констант для хранения кодов highload-блоков, соответсвующих идентификаторам
	 */
	const CODE_CONSTANTS_PREFIX = '\CODE_';

	/**
	 * Singleton экземпляры
	 *
	 * @var array
	 */
	protected static $instances = array();

	/**
	 * Константы были определены
	 *
	 * @var boolean
	 */
	protected static $constantsDefined = false;

	/**
	 * ID инфоблока
	 *
	 * @var integer
	 */
	protected $id = 0;

	/**
	 * Конструктор
	 *
	 * @param integer $id ID highload-блока
	 *
	 * @throws \Indi\Main\Exception
	 */
	protected function __construct($id = 0)
	{
		if ($id) {
			$this->id = $id;
		}
		if (!$this->id) {
			throw new Main\Exception('Hlblock ID is undefined.');
		}
	}

	/**
	 * Возвращает highload - блок по его ID или символьному коду
	 * Singleton + Factory
	 *
	 * @param int $idOrCode $id ID или символьный код highload-блока
	 *
	 * @return \Indi\Main\Hlblock\Prototype
	 * @throws \Indi\Main\Exception
	 *
	 */
	public static function getInstance($idOrCode = 0)
	{
		if (!$idOrCode) {
			$idOrCode = str_replace(
				'\\',
				'_',
				substr(
					get_called_class(),
					strlen('\\' . __NAMESPACE__)
				)
			);
		}

		$isId = is_int($idOrCode) || ctype_digit($idOrCode);
		if ($isId) {
			$id = $idOrCode;
		} else {
			$code = $idOrCode;
			$id = self::getIdByCode($code);
		}

		if (!$id) {
			throw new Main\Exception('Highloadblock id is undefined.');
		}
		if (array_key_exists($id, self::$instances)) {
			return self::$instances[$id];
		}

		if ($isId) {
			$code = self::getCodeById($id);
		}

		if (!$code) {
			throw new Main\Exception('Highloadblock code is undefined.');
		}

		$className = self::getClassByCode($code);
		if (!class_exists($className)) {
			$className = '\\' . __CLASS__;
		}
		self::$instances[$id] = new $className($id);

		return self::$instances[$id];
	}


	/**
	 * Возвращает название класса highload-блока по его символьному коду
	 *
	 * @param string $code Символьный код highload-блока
	 * @return string
	 */
	public static function getClassByCode($code)
	{
		return '\\' . __NAMESPACE__  . '\\' . str_replace(array('-', '_', '\\'), '\\', $code);
	}

	/**
	 * Возвращает код highload-блока по его ID
	 *
	 * @param integer $id ID инфоблока
	 * @return string
	 * @throws Exception
	 * @throws Main\Exception
	 */
	public static function getCodeById($id)
	{
		if (!$id) {
			throw new Exception('Highloadblock ID is undefined.');
		}

		self::defineConstants();

		$const = __NAMESPACE__ . self::CODE_CONSTANTS_PREFIX . $id;
		if (!defined($const)) {
			throw new Main\Exception(sprintf("Constant for Highloadblock id '%s' is undefined.", $id));
		}

		return constant($const);
	}

	/**
	 * Возвращает ID highload-блока по его символьному коду
	 *
	 * @param string $code Символьный код highload-блока
	 * @return int
	 * @throws Main\Exception
	 */
	public static function getIdByCode($code)
	{
		if (!$code) {
			throw new Main\Exception('Highloadblock code is undefined.');
		}

		self::defineConstants();

		$const = __NAMESPACE__ . self::ID_CONSTANTS_PREFIX . $code;
		if (!defined($const)) {
			throw new Main\Exception(sprintf("Constant for highload block code '%s' is undefined.", $code));
		}

		return constant($const);
	}

	/**
	 * Определяет константы вида Indi\Main\Hblock\ID_{CODE} и Indi\Main\Hblock\CODE_{ID} для всех highload-блоков
	 *
	 * @param integer $cacheTime Время кэширования
	 * @return void
	 */
	public static function defineConstants($cacheTime = 3600)
	{
		if(self::$constantsDefined)
			return;

		$cache = new Main\Cache(__METHOD__, __CLASS__, $cacheTime);
		if ($cache->start()) {
			$arHblocks = HighloadBlockTable::getList(
				array(
					"select" => array(
						"ID", "NAME"
					)
				)
			);
			while($arHblock = $arHblocks->fetch()) {
				$data[] = array(
					"ID" => $arHblock["ID"],
					"CODE" => $arHblock["NAME"]
				);
			}
			$cache->end($data);
		} else {
			$data = $cache->getVars();
		}

		foreach ($data as $arHblock) {
			$arHblock['CODE'] = trim($arHblock['CODE']);
			if ($arHblock['CODE']) {
				$const = __NAMESPACE__ . self::ID_CONSTANTS_PREFIX . $arHblock['CODE'];
				if (!defined($const)) {
					/**
					 * @ignore
					 */
					define($const, $arHblock['ID']);
				}
			}

			$const = __NAMESPACE__ . self::CODE_CONSTANTS_PREFIX . $arHblock['ID'];
			if (!defined($const)) {
				/**
				 * @ignore
				 */
				define($const, $arHblock['CODE']);
			}
		}

		self::$constantsDefined = true;
	}

	/**
	 * Возвращает массив с данными из hlblock
	 *
	 * @param mixed $arFilter - массив с условиями фильтрации
	 * @param mixed $arSelect - массив со списком свойств для выборки, по умолчанию * - выбираются все поля
	 * @param mixed $arOrder  - сортировок
	 *
	 * @return array {array[]|false[]}
	 * @throws ArgumentException
	 */
	public function getData($arFilter = array(), $arSelect = array('*'), $arOrder = array())
	{
		$hlblock = HighloadBlockTable::getById($this->id)->fetch();
		$entity = HighloadBlockTable::compileEntity($hlblock);
		$entity_data_class = $entity->getDataClass();
		$entity_table_name = $hlblock['TABLE_NAME'];
		$sTableID = 'tbl_' . $entity_table_name;
		$params = array(
			"select" => $arSelect,
			"filter" => $arFilter,
		);
		if (!empty($arOrder)) {
			$params["order"] = $arOrder;
		}
		$rsData = $entity_data_class::getList($params);
		$rsData = new \CDBResult($rsData, $sTableID);
		$arResult = array();
		while ($arRes = $rsData->Fetch()) {
			$arResult[] = $arRes;
		}

		return $arResult;
	}

	/**
	 * Возвращает массив с данными из hlblock (усовершенствованная версия)
	 *
	 * @param array $paramets массив с ключами, аналогичными ключам Bitrix\Main\Entity\DataManager::getList() и дополнительными ключами
	 *      "caheTime" => integer время кэширования
	 * @return array
	 */
	public function getElements($params = array())
	{
		if(key_exists('cacheTime', $params)) {
			$cacheTime = $params['cacheTime'];
			unset($params['cacheTime']);
		}

		$cache = $this->getCache(array(__METHOD__, $params), $cacheTime);
		if ($cache->start()) {
			$hlblock = HighloadBlockTable::getById($this->id)->fetch();
			$entity = HighloadBlockTable::compileEntity($hlblock);
			$entity_data_class = $entity->getDataClass();

			$rsData = $entity_data_class::getList($params);
			$arResult = array();
			while ($arRes = $rsData->Fetch()) {
				$arResult[] = $arRes;
			}

			if(!empty($arResult)) {
				$cache->end($arResult);
			}
			else {
				$cache->abort();
			}
		}
		else {
			$arResult = $cache->getVars();
		}
		return $arResult;
	}



	/**
	 * Обновляет элемент hlblock инфоблока
	 *
	 * @param integer $id       - id элемента
	 * @param array   $arFields - массив с данными для обновления
	 *
	 * @return string Ошибка
	 */
	public function updateData($id, $arFields)
	{
		$entityDataClass = $this->getClassHblock();
		$res = $entityDataClass::update($id, $arFields);

		return $res;
	}

	/**
	 * Добавляет элемент hlblock инфоблока
	 *
	 * @param array $arFields - массив с с данными для обновления
	 *
	 * @return string Ошибка
	 */
	public function addData($arFields)
	{
		$entityDataClass = $this->getClassHblock();
		$res = $entityDataClass::add($arFields);

		return $res;
	}

	/**
	 * Удаляет элемент hlblock инфоблока
	 *
	 * @param integer $id - id элемента
	 *
	 * @return string Ошибка
	 */
	public function deleteData($id)
	{
		$entityDataClass = $this->getClassHblock();
		$res = $entityDataClass::delete($id);
		if (!$res->isSuccess()) { //произошла ошибка
			return $res->getErrorMessages();
		}

		return "";
	}

	/**
	 * Получения экземпляра класса для работы с элементами highload инфоблоков
	 *
	 * @param void
	 *
	 * @return $entity_data_class
	 */
	private function getClassHblock()
	{
		$hlblock = HighloadBlockTable::getById($this->id)->fetch();
		$entity = HighloadBlockTable::compileEntity($hlblock);
		$entity_data_class = $entity->getDataClass();
		return $entity_data_class;
	}

	static public function getPropertyEnum($field)
	{
		$rsValues = \CUserFieldEnum::GetList(array(), array(
			'USER_FIELD_NAME' => $field
		));
		$arValues = array();
		foreach ($rsValues->arResult as $value) {
			$arValues[$value['XML_ID']] = $value;
		}

		return $arValues;
	}

	/**
	 * Возвращает идентификатор инфоблока
	 *
	 * @return integer
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Возвращает модель кэша для hlblock
	 *
	 * @param mixed $cacheId   Идентификатор кэша
	 * @param mixed $cacheTime Время жизни кэша
	 *
	 * @return Main\Cache
	 */
	protected function getCache($cacheId, $cacheTime = 3600)
	{
		$cache = new Main\Cache(
			array(
				$this->id,
				$cacheId
			),
			$this->getCacheDir(),
			$cacheTime
		);

		return $cache;
	}

	/**
	 * Формирует имя каталога для хранения кэшей данного hlblock
	 *
	 * @return string
	 */
	protected function getCacheDir()
	{
		return get_class($this);
	}

	/**
	 * Получает xmlID производителя
	 *
	 * @param string $propCode
	 * @param string $propCodeVal
	 * @param string $xmlIdCode
	 *
	 * @return $arItemXmlID
	 */
	public function getXmlIdByPropCode($propCode, $propCodeVal, $xmlIdCode = "UF_XML_ID")
	{
		$arItem = self::getData(
			array($propCode => $propCodeVal), array($xmlIdCode)
		);
		$arItemXmlID = $arItem[0][$xmlIdCode];

		return $arItemXmlID;
	}

	/**
	 * Добавление / Обновление элемента highload-инфоблока по внешнему коду
	 *
	 * @param array  $arFields   Поля элемента
	 * @param Object $hb         Сущность элемента highload блока
	 * @param string $entityName Название сущности ( для логов )
	 *
	 * @return integer $elementID id элемента
	 */
	protected function addUpdateHblockElement($arFields, $hb, $entityName)
	{
		$result = $hb->getData(
			array("UF_XML_ID" => $arFields["UF_XML_ID"]),
			array("ID")
		);
		if (!empty($result)) { // проверяем существует ли элемент с таким внешним кодом
			$elementID = $result[0]["ID"];
			$res = $hb->updateData($elementID, $arFields);
			if (!$res->isSuccess()) { //произошла ошибка
				Util::log($entityName . " import update: " . $res->getErrorMessages()); // обновляем поля
			}
		} else {
			$res = $hb->addData($arFields);
			if (!$res->isSuccess()) { //произошла ошибка
				Util::log($entityName . " import add: " . $res->getErrorMessages()); // добавляем поля
			}
		}
		$elId = $res->getId();
		if ($elId) {
			return $elId;
		}
	}

}