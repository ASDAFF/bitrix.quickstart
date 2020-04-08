<?php

namespace Indi\Main\UserField;

use Indi\Main\Iblock;
use Indi\Main\Util;

/**
 * Пример создания поля произвольного типа: строка, число, выпадающий список,
 * связь с любым свойством и т.д.
 * подключить в indi.main/lib/module.php
 * $eventManager->addEventHandler('iblock', 'OnIBlockPropertyBuildList', array('\Indi\Main\UserField\Custom',
 * 'GetIBlockPropertyDescription'));
 *
 * @category       Individ
 * @package        Iblock
 */
class Custom extends Iblock\Prototype
{
	/**
	 * Возвращает описание типа свойства
	 * в примере минимальный набор
	 * больше информации о методах http://dev.1c-bitrix.ru/community/webdev/user/107201/blog/6730/?commentId=51565
	 *
	 * @return array
	 */
	public function GetIBlockPropertyDescription()
	{
		return array(
			"PROPERTY_TYPE"        => "S", // тип поля
			"USER_TYPE"            => "indi_iblock_custom_field", // кодовое обозначение
			"DESCRIPTION"          => "Связь со свойством", // название
			"GetPropertyFieldHtml" => array(__CLASS__, 'GetPropertyFieldHtml'), // отображение в форме редактирования
			"GetPublicViewHTML"    => array(__CLASS__, "GetPublicViewHTML"), // отображение в публичной части
			"ConvertToDB"          => array(__CLASS__, "ConvertToDB"), // сохранение в БД
			"GetAdminListViewHTML" => array(__CLASS__, "GetAdminListViewHTML"), // отображение в списке админраздела
		);
	}

	/**
	 * Возвращает HTML код для вывода поля ввода свойства
	 *
	 * @param $arProperty           Описание типа свойства
	 * @param $value                Значение свойства
	 * @param $strHTMLControlName   UI элемент
	 *
	 * @return string
	 */
	public function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
	{
		$form = "";

		// получаем список атрибутов
		$arData = array(
			1 => array('XML_ID' => 1,
			           'NAME'   => 'Первый элемент'),
			2 => array('XML_ID' => 2,
			            'NAME'   => 'Второй элемент')
		);

		// формируем форму
		$form .= '<select name="' . $strHTMLControlName['VALUE'] . '" style="margin-bottom: 3px">
					<option value="0">(не установлено)</option>';

		foreach ($arData as $item) {
			$form .= '<option value="' . $item['XML_ID'] . '"' . ($item['XML_ID'] == $value['VALUE'] ? ' selected="selected"' : '') . '>' . $item['NAME'] . '</option>';
		}
		$form .= '</select>';

		return $form;
	}

	/**
	 * Преобразует значение св-ва перед в формат, пригодный для записи в БД
	 *
	 * @param array $arProperty Описание типа свойства
	 * @param array $value      Значение свойства
	 *
	 * @return array Преобразованное значение
	 */
	public function ConvertToDB($arProperty, $value)
	{
		if (array_key_exists('VALUE', $value)) {
			$value['VALUE'] = $value['VALUE'];
		} else {
			foreach ($value as &$valueItem) {
				$valueItem = $valueItem['VALUE'];
			}
			unset($valueItem);
		}

		return $value;
	}

	/**
	 * Отображение в админразделе в списке объектов
	 *
	 * @param $arProperty           Описание типа свойства
	 * @param $value                Значение свойства
	 * @param $strHTMLControlName   UI элемент
	 *
	 * @return mixed|string
	 * @throws \Indi\Main\Exception
	 */
	public function GetAdminListViewHTML($arProperty, $value, $strHTMLControlName)
	{
		if (strlen($value["VALUE"]) > 0) {
			$arData = Iblock\Prototype::getInstance(Iblock\ID_Catalog_Goods)->getPropertyByCode($value["VALUE"]);

			return str_replace(" ", "&nbsp;", htmlspecialcharsex($arData["NAME"]));
		} else {
			return '&nbsp;';
		}
	}

	/**
	 * Отображение в публичной части
	 *
	 * @param $arProperty           Описание типа свойства
	 * @param $value                Значение свойства
	 * @param $strHTMLControlName   UI элемент
	 *
	 * @return mixed|string
	 */
	public function GetPublicViewHTML($arProperty, $value, $strHTMLControlName)
	{
		if (strlen($value["VALUE"]) > 0) {
			return str_replace(" ", "&nbsp;", htmlspecialcharsex($value["VALUE"]));
		} else {
			return '';
		}
	}

}