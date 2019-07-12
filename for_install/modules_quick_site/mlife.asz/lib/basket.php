<?php
/**
 * Bitrix Framework
 * @package    Bitrix
 * @subpackage mlife.asz
 * @copyright  2014 Zahalski Andrew
 */

namespace Mlife\Asz;

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

class BasketTable extends Entity\DataManager
{
	//если при добавлении либо изменении записи корзины не требуется пересчет скидок, нужно установить false
	public static $discountHandler = true;

	public static function getFilePath()
	{
		return __FILE__;
	}

	public static function getTableName()
	{
		return 'mlife_asz_basket';
	}

	public static function getMap()
	{
		return array(
			new Entity\IntegerField('ID', array(
				'primary' => true,
				'autocomplete' => true,
				)
			),
			new Entity\IntegerField('USERID', array(
				'required' => true,
				)
			),
			new Entity\IntegerField('PROD_ID', array(
				'required' => true,
				)
			),
			new Entity\IntegerField('PARENT_PROD_ID', array(
				'required' => false,
				)
			),
			new Entity\StringField('PRICE_VAL', array(
				'required' => true
				)
			),
			new Entity\StringField('PRICE_CUR', array(
				'required' => true,
				'validation' => function(){
					return array(
						new Entity\Validator\Length(null, 3),
					);
				}
				)
			),
			new Entity\IntegerField('UPDATE', array(
				'required' => true,
				)
			),
			new Entity\StringField('QUANT', array(
				'required' => true,
				)
			),
			new Entity\StringField('DISCOUNT_VAL', array(
				'required' => false,
				)
			),
			new Entity\StringField('DISCOUNT_CUR', array(
				'required' => false,
				'validation' => function(){
					return array(
						new Entity\Validator\Length(null, 3),
					);
				}
				)
			),
			new Entity\StringField('SITE_ID', array(
				'required' => false,
				'validation' => function(){
					return array(
						new Entity\Validator\Length(null, 2),
					);
				}
				)
			),
			new Entity\StringField('PROD_NAME', array(
				'required' => true,
				'validation' => function(){
					return array(
						new Entity\Validator\Length(null, 255),
					);
				}
				)
			),
			new Entity\StringField('PROD_DESC', array(
				'required' => false,
				'validation' => function(){
					return array(
						new Entity\Validator\Length(null, 255),
					);
				}
				)
			),
			new Entity\IntegerField('ORDER_ID', array(
				'required' => false,
				)
			),
			new Entity\StringField('PROD_LINK', array(
				'required' => false,
				'validation' => function(){
					return array(
						new Entity\Validator\Length(null, 255),
					);
				}
				)
			)
		);
	}
	
}