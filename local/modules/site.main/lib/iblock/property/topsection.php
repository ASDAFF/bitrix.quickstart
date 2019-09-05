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
 * Тип свойства "Привязка к разделам верхнего уровня"
 * 
 * @category	
 * @package		Iblock
 */
class TopSection extends Prototype
{
	/**
	 * Возвращает описание типа свойства
	 *
	 * @return array
	 */
	public static function getUserTypeDescription()
	{
		return array(
			'PROPERTY_TYPE' => 'G',
			'USER_TYPE' => 'site-iblock-property-top-section',
			'DESCRIPTION' => 'Привязка к разделам верхнего уровня',
			'CheckFields' => array(__CLASS__, 'checkFields'),
			'GetLength' => array(__CLASS__, 'getLength'),
			'ConvertToDB' => array(__CLASS__, 'convertToDB'),
			'ConvertFromDB' => array(__CLASS__, 'convertFromDB'),
			'GetPropertyFieldHtml' => array(__CLASS__, 'getPropertyFieldHtml'),
			'GetPropertyFieldHtmlMulty' => array(__CLASS__, 'getPropertyFieldHtml'),
			'GetAdminListViewHTML' => array(__CLASS__, 'getAdminListViewHTML'),
			'GetPublicViewHTML' => array(__CLASS__, 'getPublicViewHTML'),
			'GetPublicEditHTML' => array(__CLASS__, 'getPublicEditHTML'),
		);
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
		if (array_key_exists('VALUE', $value)) {
			return $value['VALUE'] > 0 ? 1 : 0;
		} else {
			foreach ($value as &$valueItem) {
				if ($value['VALUE'] > 0) {
					return 1;
				}
			}
		}
		
		return 0;
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
		if (array_key_exists('VALUE', $value)) {
			$value['VALUE'] = (int) $value['VALUE'];
		} else {
			foreach ($value as &$valueItem) {
				$valueItem = (int) $valueItem['VALUE'];
			}
			unset($valueItem);
		}
		
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
		if($value['VALUE'] <= 0)
			return '';
		
		$section = \CIBlockSection::GetList(
			array(),
			array(
				'ID' => $value['VALUE'],
			),
			false,
			array(
				'NAME',
			)
		)->GetNext();
		
		return ($section ? $section['NAME'] : '') . ' [' . $value['VALUE'] . ']';
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
		$valuesMap = array();
		if (array_key_exists('VALUE', $value)) {
			$valuesMap[] = $value['VALUE'];
		} else {
			foreach ($value as $valueItem) {
				$valuesMap[] = $valueItem['VALUE'];
			}
		}
		
		$htmlCode = sprintf(
			'<select name="%s"%s>',
			htmlspecialcharsbx($htmlControl['VALUE']) . ($property['MULTIPLE'] == 'Y' ? '[]' : ''),
			$property['MULTIPLE'] == 'Y' ? ' multiple' : ''
		);
		
		if ($property['MULTIPLE'] != 'Y' && $property['IS_REQUIRED'] != 'Y') {
			$htmlCode .= sprintf(
				'<option value="%s">%s</option>',
				0,
				'-----'
			);
		}
		
		$sections = \CIBlockSection::GetList(
			array(
				'sort' => 'asc',
				'name' => 'asc',
			),
			array(
				'IBLOCK_ID' => $property['LINK_IBLOCK_ID'],
				'DEPTH_LEVEL' => 1,
			),
			false,
			array(
				'ID',
				'NAME',
			)
		);
		while ($section = $sections->getNext()) {
			$htmlCode .= sprintf(
				'<option value="%s"%s>%s</option>',
				htmlspecialcharsbx($section['ID']),
				in_array($section['ID'], $valuesMap) ? ' selected' : '',
				htmlspecialcharsbx($section['NAME'])
			);
		}
		
		$htmlCode .= '</select>';
		
		return $htmlCode;
	}
}