<?php
/**
 * Individ module
 * 
 * @category	Individ
 * @package		Sale
 * @link		http://individ.ru
 * @revision	$Revision$
 * @date		$Date$
 */

namespace Indi\Main\Sale;

/**
 * Заказ в магазине
 * 
 * @category	Individ
 * @package		Sale
 */
class Order
{
	/**
	 * Обработчик, выполняемый при обновлении заказа
	 *
	 * @param integer $id Идентификатор заказа
	 * @param array $fields Данные заказа
	 * @return void
	 */
	public static function onOrderUpdate($id, $fields)
	{
	}
}