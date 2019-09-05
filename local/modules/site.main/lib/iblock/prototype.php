<?php
/**
 *  module
 * 
 * @category	
 * @package		Iblock
 * @link		http://.ru
 * @revision	$Revision$
 * @date		$Date$
 */

namespace Site\Main\Iblock;

use Bitrix\Main\Loader;
use Site\Main as Main;
use Site\Main\Util;

if (!Loader::includeModule('iblock')) {
	throw new Main\Exception("Infoblock module is't installed.");
}

/**
 * Прототип инфоблока
 * 
 * @category	
 * @package		Iblock
 */
class Prototype
{
	/**
	 * Префикс констант для хранения идентификаторов инфоблоков, соответсвующих кодам
	 */
	const ID_CONSTANTS_PREFIX = '\ID_';
	
	/**
	 * Префикс констант для хранения кодов инфоблоков, соответсвующих идентификаторам
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
	 * Данные инфоблока
	 *
	 * @var array|null
	 */
	protected $data = null;
	
	/**
	 * Свойства инфоблока
	 *
	 * @var array|null
	 */
	protected $properties = null;
	
	/**
	 * Секции инфоблока
	 *
	 * @var array|null
	 */
	protected $sections = null;
	
	/**
	 * Обработчики изображений
	 *
	 * @var array
	 */
	protected $imageHandlers = array();
	
	/**
	 * Конструктор
	 *
	 * @param integer $id ID инфоблока
	 * @return void
	 */
	protected function __construct($id = 0)
	{
		if ($id) {
			$this->id = $id;
		}
		
		if (!$this->id) {
			throw new Main\Exception('Infoblock ID is undefined.');
		}
	}
	
	/**
	 * Возвращает инфоблок по его ID или символьному коду
	 * Singleton + Factory
	 *
	 * @param integer|string $idOrCode ID или символьный код инфоблока
	 * @return Prototype
	 */
	public static function getInstance($idOrCode = 0)
	{
		/*
		* Если не был передан $idOrCode, определяем символьный код по названию класса
		* Символьный код желательно задавать в формате ТипИнфоблока_КодИнфоблока
		*/
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
			throw new Main\Exception('Infoblock id is undefined.');
		}
		
		if (array_key_exists($id, self::$instances)) {
			return self::$instances[$id];
		}
		
		if ($isId) {
			$code = self::getCodeById($id);
		}
		
		if (!$code) {
			throw new Main\Exception('Infoblock code is undefined.');
		}
		
		$className = self::getClassByCode($code);
		if (!class_exists($className)) {
			$className = '\\' . __CLASS__;
		}
		
		self::$instances[$id] = new $className($id);
		
		return self::$instances[$id];
	}
	
	/**
	 * Возвращает экземпляр инфоблока по ID элемента
	 * Симбиоз шаблонов Singleton + Factory
	 *
	 * @param integer $id ID элемента
	 * @return Prototype
	 */
	public static function getInstanceByElement($id)
	{
		$element = \CIBlockElement::GetList(
			array(),
			array(
				'ID' => $id,
			),
			false,
			false,
			array(
				'IBLOCK_ID',
			)
		)->Fetch();
		
		if (!$element) {
			throw new Main\Exception(sprintf('Infoblock element %d is not found.', $id));
		}
		
		return self::getInstance($element['IBLOCK_ID']);
	}
	
	/**
	 * Возвращает экземпляр инфоблока по ID раздела
	 * Симбиоз шаблонов Singleton + Factory
	 *
	 * @param integer $id ID раздела
	 * @return Prototype
	 */
	public static function getInstanceBySection($id)
	{
		$section = \CIBlockSection::GetList(
			array(),
			array(
				'ID' => $id,
			),
			false,
			array(
				'IBLOCK_ID',
			)
		)->Fetch();
		
		if (!$section) {
			throw new Main\Exception(sprintf('Infoblock section %d is not found.', $id));
		}
		
		return self::getInstance($section['IBLOCK_ID']);
	}
	
	/**
	 * Возвращает экземпляр инфоблока по его URL
	 * Симбиоз шаблонов Singleton + Factory
	 *
	 * @param string $url URL инфоблока
	 * @param integer $siteId ID сайта
	 * @param integer $cacheTime Время кэширования
	 * @return Prototype
	 */
	public static function getInstanceByURL($url, $siteId = SITE_ID, $cacheTime = 3600)
	{
		$cache = new Main\Cache(array(__METHOD__, $siteId), __CLASS__, $cacheTime);
		if ($cache->start()) {
			$data = array();
			
			$iblocks = \CIBlock::GetList(
				array(
					'SORT' => 'ASC',
					'SORT' => 'NAME'
				),
				array(
					'ACTIVE' => 'Y',
					'SITE_ID' => $siteId
				)
			);
			while ($iblock = $iblocks->Fetch()) {
				$data[] = array(
					'ID' => $iblock['ID'],
					'LIST_PAGE_URL' => $iblock['LIST_PAGE_URL'],
				);
			}
			
			$cache->end($data);
		} else {
			$data = $cache->getVars();
		}
		
		foreach ($data as $iblock) {
			if ($iblock['LIST_PAGE_URL']) {
				$iblockUrl = preg_replace(
					'/[\/]+/',
					'/',
					Main\Util::parseTemplate($iblock['LIST_PAGE_URL'], array('SITE_ID' => $siteID))
				);
				if (substr($url, 0, strlen($iblockUrl)) == $iblockUrl) {
					return self::getInstance($iblock['ID']);
				}
			}
		}
		
		throw new Main\Exception(sprintf('Inforblock for URL "%s" is not found.', $url));
	}
	
	/**
	 * Возвращает ID инфоблока по его символьному коду
	 *
	 * @param string $code Символьный код инфоблока
	 * @return integer
	 */
	public static function getIdByCode($code)
	{
		if (!$code) {
			throw new Main\Exception('Infoblock code is undefined.');
		}
		
		self::defineConstants();
		
		$const = __NAMESPACE__ . self::ID_CONSTANTS_PREFIX . $code;
		if (!defined($const)) {
			throw new Main\Exception(sprintf("Constant for infoblock code '%s' is undefined.", $code));
		}
		
		return constant($const);
	}
	
	/**
	 * Возвращает код инфоблока по его ID
	 *
	 * @param integer $id ID инфоблока
	 * @return string
	 */
	public static function getCodeById($id)
	{
		if (!$id) {
			throw new Exception('Infoblock ID is undefined.');
		}
		
		self::defineConstants();
		
		$const = __NAMESPACE__ . self::CODE_CONSTANTS_PREFIX . $id;
		if (!defined($const)) {
			throw new Main\Exception(sprintf("Constant for infoblock id '%s' is undefined.", $id));
		}
		
		return constant($const);
	}
	
	/**
	 * Возвращает название класса инфоблока по его символьному коду
	 *
	 * @param string $code Символьный код инфоблока
	 * @return string
	 */
	public static function getClassByCode($code)
	{
		return '\\' . __NAMESPACE__  . '\\' . str_replace(array('-', '_', '\\'), '\\', $code);
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
	 * Возвращает символьный код инфоблока
	 *
	 * @return string
	 */
	public function getCode()
	{
		$data = $this->getData();
		
		return $data['CODE'];
	}
	
	/**
	 * Возвращает данные инфоблока
	 *
	 * @param integer $cacheTime Время кэширования
	 * @return array
	 */
	public function getData($cacheTime = 3600)
	{
		if ($this->data !== null) {
			return $this->data;
		}
		
		$cache = $this->getCache(array(__METHOD__), $cacheTime);
		if ($cache->start()) {
			$data = \CIBlock::GetList(
				array(
					'SORT' => 'ASC',
				),
				array(
					'ID' => $this->id,
				)
			)->Fetch();
			
			if ($data) {
				$data['MESSAGES'] = \CIBlock::GetMessages($this->id);
				$data['PICTURE'] = \CFile::GetFileArray($data['PICTURE']);
				
				$cache->end($data);
			} else {
				$cache->abort();
			}
		} else {
			$data = $cache->getVars();
		}
		
		$this->data = $data;
		
		return $data;
	}
	
	/**
	 * Возвращает свойство инфоблока по его символьному коду
	 *
	 * @param string $code Код свойства
	 * @param integer $cacheTime Время кэширования
	 * @return array
	 */
	public function getPropertyByCode($code, $cacheTime = 3600)
	{
		$code = trim($code);
		if (!$code) {
			return array();
		}
		
		$properties = $this->getProperties('CODE', $cacheTime);
		
		return array_key_exists($code, $properties) ? $properties[$code] : array();
	}
	
	/**
	 * Возвращает свойство инфоблока по идентификатору
	 *
	 * @param integer $id Идентификатор свойства
	 * @param integer $cacheTime Время кэширования
	 * @return array
	 */
	public function getPropertyById($id, $cacheTime = 3600)
	{
		$id = intval($id);
		if (!$id) {
			return array();
		}
		
		$properties = $this->getProperties('ID', $cacheTime);
		
		return array_key_exists($id, $properties) ? $properties[$id] : array();
	}
	
	/**
	 * Возвращает свойства инфоблока
	 *
	 * @param string $keyName Название поля св-ва, значение которого будет являться ключем результирующего массива
	 * @param integer $cacheTime Время кэширования
	 * @return array
	 */
	public function getProperties($keyName = 'CODE', $cacheTime = 3600)
	{
		$keyName = strtoupper($keyName);
		
		if ($this->properties === null) {
			$allowedKeys = array('ID', 'CODE', 'XML_ID');
			if (!in_array($keyName, $allowedKeys)) {
				$keyName = $allowedKeys[0];
			}
			
			$cache = $this->getCache(array(__METHOD__), $cacheTime);
			if ($cache->start()) {
				$props = \CIBlockProperty::GetList(
					array(
						'SORT' => 'ASC',
					),
					array(
						'IBLOCK_ID' => $this->id,
						'ACTIVE' => 'Y',
					)
				);
				$data = array();
				while ($prop = $props->Fetch()) {
					$prop['ENUM'] = array();
					if ($prop['PROPERTY_TYPE'] == 'L') {
						$enums = \CIBlockProperty::GetPropertyEnum(
							$prop['ID'],
							array(
								'SORT' => 'ASC',
							),
							array(
								'IBLOCK_ID' => $this->id,
							)
						);
						while ($enum = $enums->GetNext()) {
							$prop['ENUM'][] = $enum;
						}
					}
					
					$data[$prop['ID']] = $prop;
				}
				
				$cache->end($data);
			} else {
				$data = $cache->getVars();
			}
			
			$this->properties = $data;
		} else {
			$data = $this->properties;
		}
		
		if ($keyName == 'ID') {
			return $data;
		}
		
		$dataNew = array();
		foreach ($data as $item) {
			if ($item[$keyName]) {
				$dataNew[$item[$keyName]] = $item;
			} else {
				$dataNew[] = $item;
			}
		}
		
		return $dataNew;
	}
	
	/**
	 * Возвращает вариант значения свойства типа "Cписок" по его ID
	 *
	 * @param array $property Свойство (значение, возвращаемое методами getPropertyByCode, getPropertyById)
	 * @param string $id Идентификатор значения свойства (ID)
	 * @return array
	 */
	public static function getPropertyListItemById($property, $id)
	{
		return self::getPropertyListItem($property, 'ID', $id);
	}
	
	/**
	 * Возвращает вариант значения свойства типа "Cписок" по его VALUE
	 *
	 * @param array $property Свойство (значение, возвращаемое методами getPropertyByCode, getPropertyById)
	 * @param string $value Значение свойства (VALUE)
	 * @return array
	 */
	public static function getPropertyListItemByValue($property, $value)
	{
		return self::getPropertyListItem($property, 'VALUE', $value);
	}
	
	/**
	 * Возвращает вариант значения свойства типа "Cписок" по его XML_ID
	 *
	 * @param array $property Свойство (значение, возвращаемое методами getPropertyByCode, getPropertyById)
	 * @param string $xmlID Код значения свойства (XML_ID)
	 * @return array
	 */
	public static function getPropertyListItemByCode($property, $xmlId)
	{
		return self::getPropertyListItem($property, 'XML_ID', $xmlId);
	}
	
	/**
	 * Возвращает вариант значения свойства типа "Cписок"
	 *
	 * @param array $property Свойство (значение, возвращаемое методами getPropertyByCode, getPropertyById)
	 * @param string $field Поле варианта значения, по которому ищем
	 * @param string $value Значение варианта, по которому ищем
	 * @return array
	 */
	public static function getPropertyListItem($property, $field, $value)
	{
		if (isset($property['ENUM']) && is_array($property['ENUM'])) {
			foreach ($property['ENUM'] as $item) {
				if (isset($item[$field]) && $item[$field] == $value) {
					return $item;
				}
			}
		}
		
		return array();
	}
	
	/**
	 * Возвращает значение св-ва элемента инфоблока из общего массива свойств
	 * Со множественными списками может врать
	 * 
	 * @param array $values Значения свойств элемента инфоблока (ключами являются коды свойств)
	 * @param string $code Код свойства
	 * @return mixed
	 */
	public static function getPropertyValueFromCollection($values, $code)
	{
		return self::getPropertyValueByLink($values, $code);
	}
	
	/**
	 * Возвращает значение св-ва элемента инфоблока из общего массива свойств по ссылке
	 * Со множественными списками может врать
	 * 
	 * @param array $values Значения свойств элемента инфоблока (ключами являются коды свойств)
	 * @param string $code Код свойства
	 * @return mixed
	 */
	public static function &getPropertyValueFromCollectionByLink(&$values, $code)
	{
		$code = trim($code);
		
		if (!is_array($values) || !array_key_exists($code, $values)) {
			return null;
		}
		
		if (is_array($values[$code]) && !array_key_exists('VALUE', $values[$code])) {
			$value = &$values[$code][0];
		} else {
			$value = &$values[$code];
		}
		
		if (is_array($value) && array_key_exists('VALUE', $value)) {
			return $value['VALUE'];
		}
		
		return $value;
	}
	
	/**
	* Подготавливает св-в элемента инфоблока типа текст/html для вывода в шаблоне
	*
	* @param array $data Данные элемента инфоблока
	* @param array|string $properties Коды св-в
	* @return void
	*/
	public static function prepareHTMLProperties(&$data, $properties)
	{
		$properties = (array) $properties;
		
		foreach ($properties as $property) {
			if (!array_key_exists($property, $data)) {
				continue;
			}
			
			$propertyRAWValue = $data['~' . $property];
			$propertyValue = &$data[$property];
			if (is_array($propertyValue) && array_key_exists('VALUE', $propertyValue)) {
				$propertyRAWValue = $propertyValue['~VALUE'];
				$propertyValue = &$propertyValue['VALUE'];
			}
			
			if (is_array($propertyValue) && !array_key_exists('TYPE', $propertyValue)) {
				foreach ($propertyValue as $num => &$value) {
					self::prepareHTMLProperty($value, $propertyRAWValue[$num]);
				}
				unset($value);
			} else {
				self::prepareHTMLProperty($propertyValue, $propertyRAWValue);
			}
		}
	}
	
	/**
	* Подготавливает св-ва элемента инфоблока типа текст/html для вывода в шаблоне
	*
	* @param array $value Значение свойства
	* @param array $rawValue Необработанное значение свойства
	* @return void
	*/
	public static function prepareHTMLProperty(&$value, $rawValue)
	{
		if (array_key_exists('TYPE', $value)) {
			if ($value['TYPE'] == 'html') {
				if(array_key_exists('TEXT', $rawValue))
					$value['TEXT'] = $rawValue['TEXT'];
			} else {
				$value['TEXT'] = nl2br($value['TEXT']);
			}
		}
	}
	
	/**
	 * Возвращает элемент инфоблока по его идентификатору
	 *
	 * @param integer $id Идентификатор элемента
	 * @param integer $cacheTime Время кэширования
	 * @return array
	 */
	public function getElementById($id, $cacheTime = 3600)
	{
		return $this->getElement('ID', intval($id), $cacheTime);
	}
	
	/**
	 * Возвращает элемент инфоблока по его символьному коду
	 *
	 * @param string $code Символьный код элемента
	 * @param integer $cacheTime Время кэширования
	 * @return array
	 */
	public function getElementByCode($code, $cacheTime = 3600)
	{
		return $this->getElement('CODE', $code, $cacheTime);
	}
	
	/**
	 * Возвращает элемент инфоблока
	 *
	 * @param string $field Название поля, по которому ищем
	 * @param mixed $value Значение поля, по которому ищем
	 * @param integer $cacheTime Время кэширования
	 * @return array
	 */
	protected function getElement($field, $value, $cacheTime = 3600)
	{
		$cache = $this->getCache(array(__METHOD__, $field, $value), $cacheTime);
		if ($cache->start()) {
			$element = \CIBlockElement::GetList(
				array(),
				array(
					'IBLOCK_ID' => $this->id,
					$field => $value
				),
				false,
				array(
					'nTopCount' => 1
				),
				array(
					'ID',
					'IBLOCK_ID',
					'NAME',
					'CODE',
					'TAGS',
					'ACTIVE_FROM',
					'ACTIVE_TO',
					'DATE_CREATE',
					'CREATED_BY',
					'SHOW_COUNTER',
					'IBLOCK_SECTION_ID',
					'DETAIL_PAGE_URL',
					'DETAIL_TEXT',
					'DETAIL_TEXT_TYPE',
					'DETAIL_PICTURE',
					'PREVIEW_TEXT',
					'PREVIEW_TEXT_TYPE',
					'PREVIEW_PICTURE',
				)
			)->GetNextElement();
			
			$data = array();
			if ($element) {
				$data = $element->GetFields();
				$data['PROPERTIES'] = $element->GetProperties();
				
				foreach ($data['PROPERTIES'] as &$property) {
					//Для множественных св-в в VALUE_XML_ID пишется только первое значение - исправим это
					if ($property['PROPERTY_TYPE'] == 'L' && $property['MULTIPLE'] == 'Y') {
						$propertyData = $this->getPropertyById($property['ID']);
						$property['VALUE_XML_ID'] = array();
						foreach ($property['VALUE_ENUM_ID'] as $valueId) {
							$propertyItem = $this->getPropertyListItem($propertyData, 'ID', $valueId);
							$property['VALUE_XML_ID'][] = $propertyItem['XML_ID'];
						}
					}
				}
				
				$data['PREVIEW_PICTURE'] = \CFile::GetFileArray($data['PREVIEW_PICTURE']);
				$data['DETAIL_PICTURE'] = \CFile::GetFileArray($data['DETAIL_PICTURE']);
				
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
	 * Возвращает количество элементов инфоблока
	 *
	 * @param integer $cacheTime Время кэширования
	 * @return integer
	 */
	public function getElementsCount($cacheTime = 3600)
	{
		$cache = $this->getCache(array(__METHOD__), $cacheTime);
		if ($cache->start()) {
			$count = \CIBlock::GetElementCount($this->id);
			$cache->end($count);
		} else {
			$count = $cache->getVars();
		}
		
		return $count;
	}
	
	/**
	 * Возвращает секцию инфоблока по ее идентификатору
	 *
	 * @param integer $id Идентификатор секции
	 * @param integer $cacheTime Время кэширования
	 * @return array
	 */
	public function getSectionById($id, $cacheTime = 3600)
	{
		return $this->getSection('ID', intval($id), $cacheTime);
	}
	
	/**
	 * Возвращает секцию инфоблока по ее сивольному коду
	 *
	 * @param integer $code Символьный код секции
	 * @param integer $cacheTime Время кэширования
	 * @return array
	 */
	public function getSectionByCode($code, $cacheTime = 3600)
	{
		return $this->getSection('CODE', $code, $cacheTime);
	}
	
	/**
	 * Возвращает секцию инфоблока
	 *
	 * @param string $field Название поля, по которому ищем
	 * @param mixed $value Значение поля, по которому ищем
	 * @param integer $cacheTime Время кэширования
	 * @return array
	 */
	protected function getSection($field, $value, $cacheTime = 3600)
	{
		$cache = $this->getCache(array(__METHOD__, $field, $value), $cacheTime);
		if ($cache->start()) {
			$data = \CIBlockSection::GetList(
				array(),
				array(
					'IBLOCK_ID' => $this->id,
					$field => $value,
				),
				false
			)->GetNext();
			
			if ($data) {
				//Добавим пользовательские поля секции
				$data['UF'] = $GLOBALS['USER_FIELD_MANAGER']->GetUserFields('IBLOCK_' . $this->id . '_SECTION', $data['ID'], LANGUAGE_ID);
				if (is_array($data['UF'])) {
					foreach ($data['UF'] as &$userField) {
						switch ($userField['USER_TYPE_ID']) {
							//Для полей типа "Список" достаем дополнительную информацию
							case 'enumeration':
								$enumMultiple = is_array($userField['VALUE']);
								foreach ((array) $userField['VALUE'] as $enumKey => $enumValue) {
									$enumData = \CUserFieldEnum::GetList(array(), array('ID' => $enumValue))->GetNext();
									if ($enumMultiple) {
										$userField['ENUM'][$enumKey] = $enumData;
									} else {
										$userField['ENUM'] = $enumData;
									}
								}
								break;
						}
					}
				}
				
				$paths = GetIBlockSectionPath($this->id, $data['ID']);
				while ($path = $paths->GetNext()) {
					$data['PATH'][] = $path;
				}
				
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
	 * Возвращает секции инфоблока
	 *
	 * @param string $keyName Название поля св-ва, значение которого будет являться ключем результирующего массива
	 * @param integer $cacheTime Время кэширования
	 * @return array
	 */
	public function getSections($keyName = '', $cacheTime = 3600)
	{
		if ($this->sections === null) {
			$cache = $this->getCache(array(__METHOD__), $cacheTime);
			if ($cache->start()) {
				$data = array();
				$sections = \CIBlockSection::GetList(
					array(
						'LEFT_MARGIN' => 'ASC',
						'SORT' => 'ASC'
					),
					array(
						'IBLOCK_ID' => $this->id
					)
				);
				while ($section = $sections->GetNext()) {
					$data[$section['ID']] = $section;
				}
				
				$cache->end($data);
			} else {
				$data = $cache->getVars();
			}
			
			$this->sections = $data;
		} else {
			$data = $this->sections;
		}
		
		if (!$keyName || $keyName == 'ID') {
			return $data;
		}
		
		$dataNew = array();
		foreach ($data as &$section) {
			if ($section[$keyName]) {
				$dataNew[$section[$keyName]] = &$section;
			} else {
				$dataNew[] = &$section;
			}
		}
		unset($section);
		
		return $dataNew;
	}
	
	/**
	 * Возвращает список элемент инфоблока, готовых для использования в качестве меню
	 * Пример использования (.sub.menu_ext.php):
	 * $aMenuLinks = array_merge($aMenuLinks, \Site\Main\Iblock\Prototype::getInstance('Content-News')->getElementsForMenu());
	 * 
	 * @param array $filter Фильтр
	 * @param array $sort Порядок сортировки
	 * @param integer $cacheTime Время кэширования
	 * @return array
	 */
	public function getElementsForMenu($filter = array(), $sort = array('SORT' => 'ASC', 'NAME' => 'ASC'), $cacheTime = 3600)
	{
		$cache = $this->getCache(array(__METHOD__, $filter, $sort), $cacheTime);
		if ($cache->start()) {
			$elementsRecordset = \CIBlockElement::GetList(
				$sort,
				array_merge(array(
					'IBLOCK_ID' => $this->id,
					'ACTIVE' => 'Y',
				), $filter),
				false,
				false,
				array(
					'ID',
					'IBLOCK_ID',
					'NAME',
					'CODE',
					'DETAIL_PAGE_URL',
					'PREVIEW_TEXT',
					'PREVIEW_TEXT_TYPE',
					'PREVIEW_PICTURE',
				)
			);
			
			$elements = array();
			while ($element = $elementsRecordset->GetNext()) {
				$element['PREVIEW_PICTURE'] = \CFile::GetFileArray($element['PREVIEW_PICTURE']);
				
				$elements[] = array(
					$element['NAME'],
					$element['DETAIL_PAGE_URL'],
					array(),
					array(
						'ICON' => $element['PREVIEW_PICTURE'] ? $element['PREVIEW_PICTURE']['SRC'] : '',
						'TEXT' => $element['PREVIEW_TEXT'],
					),
				);
			}
			
			$cache->end($elements);
		} else {
			$elements = $cache->getVars();
		}
		
		return $elements;
	}
	
	/**
	 * Обработчик картинок для полей элемента инфоблока
	 *
	 * @param array $fields Поля элемента
	 * @return boolean
	 */
	public function onAfterIBlockElementAddUpdate(&$fields)
	{
		//Проверяем, что элемент принадлежит этому инфоблоку
		$fields['IBLOCK_ID'] = intval($fields['IBLOCK_ID']);
		if ($fields['IBLOCK_ID'] == 0) {
			$fields['ID'] = intval($fields['ID']);
			if ($fields['ID'] == 0) {
				return true;
			}
			
			$element = \CIBlockElement::GetList(
				array(
					'ID' => 'ASC',
				),
				array(
					'IBLOCK_ID' => $this->id,
					'ID' => $fields['ID'],
				),
				false,
				array(
					'nTopCount' => 1,
				),
				array(
					'ID',
					'IBLOCK_ID',
				)
			)->Fetch();
			
			if (!$element) {
				return true;
			}
		} elseif ($fields['IBLOCK_ID'] != $this->id) {
			return true;
		}
		
		//Запускаем обработчики
		foreach ($this->imageHandlers as $handler) {
			try {
				$handler->iblockElementId = $fields['ID'];
				$handler->execute();
			} catch(Exception $e) {
				//
			}
		}

		return true;
	}
	
	/**
	 * Определяет константы вида Site\Main\Iblock\ID_{CODE} и Site\Main\Iblock\CODE_{ID} для всех инфоблоков
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
			$iblocks = \CIBlock::GetList(
				array(
					'sort' => 'asc',
				),
				array(
					'ACTIVE' => 'Y',
					'CHECK_PERMISSIONS' => 'N',
				)
			);
			$data = array();
			while ($iblock = $iblocks->Fetch()) {
				$data[] = $iblock;
			}
			
			$cache->end($data);
		} else {
			$data = $cache->getVars();
		}
		
		foreach ($data as $iblock) {
			$iblock['CODE'] = trim($iblock['CODE']);
			if ($iblock['CODE']) {
				$const = __NAMESPACE__ . self::ID_CONSTANTS_PREFIX . $iblock['CODE'];
				if (!defined($const)) {
					/**
					 * @ignore
					 */
					define($const, $iblock['ID']);
				}
			}
			
			$const = __NAMESPACE__ . self::CODE_CONSTANTS_PREFIX . $iblock['ID'];
			if (!defined($const)) {
				/**
				 * @ignore
				 */
				define($const, $iblock['CODE']);
			}
		}
		
		self::$constantsDefined = true;
	}
	
	/**
	 * Возвращает модель кэша для инфоблока
	 *
	 * @param mixed $cacheId Идентификатор кэша
	 * @param mixed $cacheTime Время жизни кэша
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
	 * Формирует имя каталога для хранения кэшей данного инфоблока
	 *
	 * @return string
	 */
	protected function getCacheDir()
	{
		return get_class($this);
	}

    /**
     * Проверяет есть ли элемент с переданным внишним кодом в БД
     *
     * @param string $iblockID
     * @param string $xmlID
     * @return mixed false/elementID
     */

    private static function isDublicateElementXmlID($iblockID, $xmlID) {
        // проверяем есть ли на сайте элемент с таким element id и если есть то обновляем его
        $arSelect = array("ID");
        $arFilter = array("XML_ID" => $xmlID, "IBLOCK_ID" => $iblockID);
        $rsElements = \CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
        if($arElement = $rsElements->GetNext()) {
            return $arElement["ID"]; // такой элемент уже есть, вернем его id
        }
        return false; // такого элемента еще нет
    }

    /**
     * Проверяет есть ли раздел с переданным внишним кодом в БД
     *
     * @param string $iblockID
     * @param string $xmlID
     * @return mixed false/sectionID
     */

    public static function isDublicateSectionXmlID($iblockID, $xmlID) {

        // проверяем есть ли на сайте элемент с таким element id и если есть то обновляем его

        $arSelect = array("ID");
        $arFilter = array("XML_ID" => $xmlID, "IBLOCK_ID" => $iblockID);
        $rsSections = \CIBlockSection::GetList(array(), $arFilter, false, false, $arSelect);

        if($rsSection = $rsSections->GetNext()) {
            return $rsSection["ID"]; // такой раздел уже есть, вернем его id
        }

        return false; // такого раздела еще нет
    }

    /**
     * Добавление / Обновление элемента инфоблока по внешнему коду
     *
     * @param array $arFields Поля элемента
     * @param CIBlockElement $el объект класса Элемент Инфоблока
     * @param string $entityName Название сущности ( для логов )
     * @param array $arProps Массив свойств
     *
     * @return integer $elementID id элемента
     */

    protected static function addUpdateElement($arFields, $el, $entityName, $arProps = array()) {
        $existedElID = self::isDublicateElementXmlID($arFields["IBLOCK_ID"], $arFields["XML_ID"]);
        $elementID = $existedElID;
        if($existedElID) { // если есть, обновляем элемент
            $res = $el->Update($existedElID, $arFields, false, false, true);
            if(!$res) { // если fail пишем ошибку в лог
                Util::log($entityName . " import update: " . $el->LAST_ERROR); // обновляем поля
            }
            else { // все ок, обновляем свойства
                $el::SetPropertyValuesEx($existedElID, false, $arProps);
            }
        }
        else {  // если нет, добавляем
            $res = $el->Add($arFields, false, false, true);
            if(!$res) {
                Util::log($entityName . " import add: " . $el->LAST_ERROR); // добавляем поля
            }
            else { // все ок, добавляем свойства
                if(!empty($arProps)) {
                    $el::SetPropertyValuesEx($res, false, $arProps);
                }
                $elementID = $res;
            }
        }

        return $elementID;
    }

    /**
     * Добавление / Обновление раздела инфоблока по внешнему коду
     *
     * @param array $arFields Поля элемента
     * @param CIBlockSection $bs объект класса Раздел Инфоблока
     * @param string $entityName Название сущности ( для логов )
     *
     * @return integer $sectionID id элемента
     */

    protected static function addUpdateSection($arFields, $bs, $entityName) {
        $existedSecID = self::isDublicateSectionXmlID($arFields["IBLOCK_ID"], $arFields["XML_ID"]);
        $sectionID = $existedSecID;
        if($sectionID) { // если есть, обновляем раздел
            $res = $bs->Update($sectionID, $arFields);
            if(!$res) { // если fail пишем ошибку в лог
                Util::log($entityName . " import update: " . $bs->LAST_ERROR); // обновляем поля
            }
        }
        else {  // если нет, добавляем
            $res = $bs->Add($arFields);
            if(!$res) {
                Util::log($entityName . " import add: " . $bs->LAST_ERROR); // добавляем поля
            }
            $sectionID = $res;
        }
        return $sectionID;
    }
	
	/**
	 * кэшированная обертка для
	 * Получения простого списка элементов (только поля) D7
	 *
	 * @param $arParams
	 * @param integer $cacheTime
	 *
	 * @return array|mixed
	 * @internal param array $arSelect
	 * @internal param array $arFilter
	 */
	public function getList($arParams = array(), $cacheTime = 3600)
	{
		if(isset($arParams['filter']) && !empty($arParams['filter'])) {
			$arParams['filter'] = array_merge($arParams['filter'], array(
				'ACTIVE' => 'Y',
				'IBLOCK_ID' => $this->id
			));
		} else {
			$arParams['filter'] = array(
				'ACTIVE' => 'Y',
				'IBLOCK_ID' => $this->id
			);
		}
		$cache = $this->getCache(array(__METHOD__, $arParams, $this->id), $cacheTime);
		if ($cache->start()) {
			$result = ElementTable::getList(
				$arParams
			)->fetchAll();
			if($result && !empty($result)) {
				$cache->end($result);
			} else {
				$cache->abort();
				return array();
			}
		} else {
			$result = $cache->getVars();
		}
		return $result;
	}


	/**
	 * Транслитерация названий для формирования символьных кодов эл-тов ИБ партнеров
	 *
	 * @param $arFields
	 */
	public function onBeforeIblockElementUpdateHandler(&$arFields){
		if( $arFields['IBLOCK_ID'] == 1 && empty($arFields['CODE']) ){
			$arParams = array("replace_space" => "-", "replace_other" => "-");
			$trans = \Cutil::translit($arFields["NAME"], "ru", $arParams);

			$arFields["CODE"] = $trans;
		}
	}
}