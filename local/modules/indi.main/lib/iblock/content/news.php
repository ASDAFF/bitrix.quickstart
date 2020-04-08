<?php
/**
 * Individ module
 * 
 * @category	Individ
 * @package		Iblock
 * @link		http://individ.ru
 * @revision	$Revision$
 * @date		$Date$
 */

namespace Indi\Main\Iblock\Content;

use Indi\Main\Iblock;

/**
 * Инфоблок новостей
 * 
 * @category	Individ
 * @package		Iblock
 */
class News extends Iblock\Prototype
{
	/**
	 * Возвращает инфоблок новостей
	 * 
	 * @return News
	 */
	public static function getInstance()
	{
		return parent::getInstance();
	}
	
	/**
	 * Возвращает последнюю новость, у которой есть детальная картинка
	 *
	 * @param integer $cacheTime Время кэширования
	 * @return array
	 */
	public function getLastElementWithPicture($cacheTime = 3600)
	{
		$cache = $this->getCache(__METHOD__, $cacheTime);
		if ($cache->start()) {
			$element = \CIBlockElement::GetList(
				array(
					'DATE_ACTIVE_FROM' => 'DESC',
					'ID' => 'DESC',
				),
				array(
					'IBLOCK_ID' => $this->id,
					'ACTIVE' => 'Y',
					'ACTIVE_DATE' => 'Y',
					'!DETAIL_PICTURE' => false,
				),
				false,
				false,
				array('ID', 'NAME', 'DETAIL_PICTURE')
			)->GetNext();
			
			if ($element) {
				$element['DETAIL_PICTURE'] = \CFile::GetFileArray($element['DETAIL_PICTURE']);
			}
			
			$cache->end($element);
		} else {
			$element = $cache->getVars();
		}
		
		return $element;
	}
	
	/**
	 * Обработчик для модуля поиска: заменяет поисковую ссылку на значение свойства 'URL'
	 * Вызывается из класса \Indi\Main\Search
	 * 
	 * @param array $data Поля индексируемого документа
	 * @return array Модифицированный или исходный $data
	 */
	/*public function onBeforeSearchIndexElement($data)
	{
		//Допустим, новости с ссылкой на внешний ресурс должны выводиться с этой ссылкой и в результатах поиска
		$element = \CIBlockElement::GetList(
			array(
				'ID' => 'ASC'
			),
			array(
				'IBLOCK_ID' => $this->id,
				'ID' => $data['ITEM_ID'],
			),
			false,
			false,
			array('ID', 'IBLOCK_ID', 'PROPERTY_URL')
		)->GetNext();
		if ($element
			&& ($link = trim($element['PROPERTY_URL_VALUE']))
			&& strpos($link, 'http://') === 0
		) {
			$data['URL'] = $link;
		}
		
		return $data;
	}*/
}