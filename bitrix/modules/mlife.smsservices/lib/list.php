<?php
/**
 * Bitrix Framework
 * @package    Bitrix
 * @subpackage mlife.smsservices
 * @copyright  2015 Zahalski Andrew
 */

namespace Mlife\Smsservices;

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

class ListTable extends Entity\DataManager
{
	public static function getFilePath()
	{
		return __FILE__;
	}

	public static function getTableName()
	{
		return 'mlife_smsservices_list';
	}

	public static function getMap()
	{
		return array(
			new Entity\IntegerField('ID', array(
				'primary' => true,
				'autocomplete' => true,
				)
			),
			new Entity\StringField('PROVIDER', array(
				'required' => false,
				'validation' => function(){
					return array(
						new Entity\Validator\Length(null, 50),
					);
				}
				)
			),
			new Entity\StringField('SMSID', array(
				'required' => false,
				'validation' => function(){
					return array(
						new Entity\Validator\Length(null, 100),
					);
				}
				)
			),
			new Entity\StringField('SENDER', array(
				'required' => false,
				'validation' => function(){
					return array(
						new Entity\Validator\Length(null, 50),
					);
				}
				)
			),
			new Entity\StringField('PHONE', array(
				'required' => false,
				'validation' => function(){
					return array(
						new Entity\Validator\Length(null, 20),
					);
				}
				)
			),
			new Entity\IntegerField('TIME', array(
				'required' => true,
				'validation' => function(){
					return array(
						new Entity\Validator\Length(null, 11),
					);
				}
				)
			),
			new Entity\IntegerField('TIME_ST', array(
				'required' => true,
				'validation' => function(){
					return array(
						new Entity\Validator\Length(null, 11),
					);
				}
				)
			),
			new Entity\StringField('MEWSS', array(
				'required' => true,
				'validation' => function(){
					return array(
						new Entity\Validator\Length(null, 655),
					);
				}
				)
			),
			new Entity\StringField('PRIM', array(
				'required' => false,
				'validation' => function(){
					return array(
						new Entity\Validator\Length(null, 655),
					);
				}
				)
			),
			new Entity\IntegerField('STATUS', array(
				'required' => true,
				'validation' => function(){
					return array(
						new Entity\Validator\Length(null, 2),
					);
				}
				)
			),
			new Entity\StringField('EVENT', array(
				'required' => false,
				'validation' => function(){
					return array(
						new Entity\Validator\Length(null, 100),
					);
				}
				)
			),
			new Entity\StringField('EVENT_NAME', array(
				'required' => false,
				'validation' => function(){
					return array(
						new Entity\Validator\Length(null, 100),
					);
				}
				)
			),
		);
	}
	
}