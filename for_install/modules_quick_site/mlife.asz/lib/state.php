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

class StateTable extends Entity\DataManager
{
	public static function getFilePath()
	{
		return __FILE__;
	}
	
	public static function getTableName()
	{
		return 'mlife_asz_state';
	}
	
	public static function getMap()
	{
		return array(
			new Entity\IntegerField('ID', array(
				'primary' => true,
				'autocomplete' => true,
				)
			),
			new Entity\IntegerField('COUNTRY', array(
				'required' => true,
				)
			),
			new Entity\StringField('NAME', array(
				'required' => true,
				'validation' => function(){
					return array(
						new Entity\Validator\Length(null, 255),
					);
				}
				)
			),
			new Entity\StringField('CODE2', array(
				'required' => true,
				'validation' => function(){
					return array(
						new Validator\LengthFix(2),
					);
				}
				)
			),
			new Entity\StringField('CODE3', array(
				'required' => true,
				'validation' => function(){
					return array(
						new Validator\LengthFix(3),
					);
				}
				)
			),
			new Entity\BooleanField('ACTIVE', array(
				'required' => true,
				'values' => array('N', 'Y'),
				)
			),
			new Entity\IntegerField('SORT', array(
				'required' => false,
				)
			),
			new Entity\ReferenceField('CN', '\Mlife\Asz\CountryTable', 
				array('=this.COUNTRY' => 'ref.ID')
			),
		);
	}
	
}