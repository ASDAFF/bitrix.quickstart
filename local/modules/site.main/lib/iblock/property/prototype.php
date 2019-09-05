<?
/**
 *  module
 * 
 * @category	
 * @package		Iblock
 * @link		http://.ru
 * @revision	$Revision$
 * @date		$Date$
 */

namespace Site\Main\Iblock\Property;

/**
 * Прототип собственных типов свойств для инфблоков
 * 
 * @category	
 * @package		Iblock
 */
abstract class Prototype
{
	/**
	 * Возвращает описание типа свойства
	 *
	 * @return array
	 */
	public static function getUserTypeDescription()
	{
		return array(
			'PROPERTY_TYPE' => 'S',
			'USER_TYPE' => 'site-iblock-property-abstract',
			'DESCRIPTION' => 'Abstract property',
			'CheckFields' => array(__CLASS__, 'checkFields'),
			'GetLength' => array(__CLASS__, 'getLength'),
			'ConvertToDB' => array(__CLASS__, 'convertToDB'),
			'ConvertFromDB' => array(__CLASS__, 'convertFromDB'),
			'GetPropertyFieldHtml' => array(__CLASS__, 'getPropertyFieldHtml'),
			'GetAdminListViewHTML' => array(__CLASS__, 'getAdminListViewHTML'),
			'GetPublicViewHTML' => array(__CLASS__, 'getPublicViewHTML'),
			'GetPublicEditHTML' => array(__CLASS__, 'getPublicEditHTML'),
		);
	}
	
	/**
	 * Валидирует значение св-ва перед cохранением
	 *
	 * @param array $property Описание типа свойства
	 * @param array $value Значение свойства
	 * @return array Сообщения об ошибках
	 */
	public static function checkFields($property, $value)
	{
		return array();
	}
	
	/**
	 * Возвращает размер введенного значения для проверки заполненности
	 *
	 * @param array $property Описание типа свойства
	 * @param array $value Значение свойства
	 * @return array Сообщения об ошибках
	 */
	public static function getLength($property, $value)
	{
		return strlen(trim($value['VALUE']));
	}
	
	/**
	 * Преобразует значение св-ва перед в формат, пригодный для записи в БД
	 *
	 * @param array $property Описание типа свойства
	 * @param array $value Значение свойства
	 * @return array Преобразованное значение
	 */
	public static function convertToDB($property, $value)
	{
		$value['VALUE'] = (string) $value['VALUE'];
		
		return $value;
	}
	
	/**
	 * Преобразует значение св-ва из формата БД в оперативный формат
	 *
	 * @param array $property Описание типа свойства
	 * @param array $value Значение свойства
	 * @return array Преобразованное значение
	 */
	public static function convertFromDB($property, $value)
	{
		return $value;
	}
	
	/**
	 * Возвращает HTML код для вывода поля ввода свойства
	 *
	 * @param array $property Описание типа свойства
	 * @param array $value Значение свойства
	 * @param array $htmlControl UI элемент
	 * @return string
	 */
	public static function getPropertyFieldHtml($property, $value, $htmlControl)
	{
		return self::GetPublicEditHTML($property, $value, $htmlControl);
	}
	
	/**
	 * Возвращает HTML код для вывода значения свойства в списке элементов административного раздела
	 *
	 * @param array $property Описание типа свойства
	 * @param array $value Значение свойства
	 * @param array $htmlControl UI элемент
	 * @return string
	 */
	public static function getAdminListViewHTML($property, $value, $htmlControl)
	{
		return self::GetPublicViewHTML($property, $value, $htmlControl);
	}
	
	/**
	 * Возвращает HTML код для вывода значения свойства в публичном разделе
	 *
	 * @param array $property Описание типа свойства
	 * @param array $value Значение свойства
	 * @param array $htmlControl UI элемент
	 * @return string
	 */
	public static function getPublicViewHTML($property, $value, $htmlControl)
	{
		return $value['VALUE'];
	}
	
	/**
	 * Возвращает HTML код для редактирования значения свойства в публичном разделе
	 *
	 * @param array $property Описание типа свойства
	 * @param array $value Значение свойства
	 * @param array $htmlControl UI элемент
	 * @return string
	 */
	public static function getPublicEditHTML($property, $value, $htmlControl)
	{
		return sprintf(
			'<input type="text" name="%s" value="%s"/>',
			htmlspecialcharsbx($htmlControl['VALUE']),
			htmlspecialcharsbx($value['VALUE'])
		);
	}
}