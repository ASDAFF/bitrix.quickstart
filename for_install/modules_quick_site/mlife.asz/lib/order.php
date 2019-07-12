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

class OrderTable extends Entity\DataManager
{
	public static function getFilePath()
	{
		return __FILE__;
	}
	
	public static function getTableName()
	{
		return 'mlife_asz_order';
	}
	
	public static function getMap()
	{
		return array(
			new Entity\IntegerField('ID', array(
				'primary' => true,
				'autocomplete' => true,
				)
			),
			new Entity\StringField('SITEID', array(
				'required' => true,
				'validation' => function(){
					return array(
						new Validator\LengthFix(2),
					);
				}
				)
			),
			new Entity\IntegerField('USERID', array(
				'required' => false,
				)
			),
			new Entity\IntegerField('STATUS', array(
				'required' => false,
				)
			),
			new Entity\IntegerField('PAY_ID', array(
				'required' => false,
				)
			),
			new Entity\IntegerField('DELIVERY_ID', array(
				'required' => false,
				)
			),
			new Entity\StringField('PRICE', array(
				'required' => true
				)
			),
			new Entity\StringField('DISCOUNT', array(
				'required' => true
				)
			),
			new Entity\StringField('TAX', array(
				'required' => true
				)
			),
			new Entity\StringField('CURRENCY', array(
				'required' => true
				)
			),
			new Entity\StringField('DELIVERY_PRICE', array(
				'required' => true
				)
			),
			new Entity\StringField('PAYMENT_PRICE', array(
				'required' => true
				)
			),
			new Entity\IntegerField('DATE', array(
				'required' => true
				)
			),
			new Entity\StringField('PASSW', array(
				'required' => true
				)
			),
			new Entity\ReferenceField('USER', '\Mlife\Asz\UserTable', 
				array('=this.USERID' => 'ref.UID')
			),
			new Entity\ReferenceField('ADDSTAT', '\Mlife\Asz\OrderstatusTable', 
				array('=this.STATUS' => 'ref.ID')
			),
			new Entity\ReferenceField('ADDPAY', '\Mlife\Asz\PaysystemTable', 
				array('=this.PAY_ID' => 'ref.ID')
			),
			new Entity\ReferenceField('ADDDELIVERY', '\Mlife\Asz\DeliveryTable', 
				array('=this.DELIVERY_ID' => 'ref.ID')
			),
		);
	}
	
	public static function getMapAdmin()
	{
		return array('ID','SITEID','STATUS','PAY_ID','DELIVERY_ID','PRICE','DISCOUNT','TAX','DELIVERY_PRICE','PAYMENT_PRICE','DATE');
	}

}