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

class PricetipTable extends Entity\DataManager
{
	public static function getFilePath()
	{
		return __FILE__;
	}

	public static function getTableName()
	{
		return 'mlife_asz_pricetip';
	}

	public static function getMap()
	{
		return array(
			new Entity\IntegerField('ID', array(
				'primary' => true,
				'autocomplete' => true,
				)
			),
			new Entity\StringField('CODE', array(
				'required' => true,
				'validation' => function(){
					return array(
						new Validator\LengthFix(4),
					);
				}
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
			new Entity\BooleanField('BASE', array(
				'required' => true,
				'values' => array('N', 'Y'),
				)
			),
			new Entity\TextField('GROUP', array(
				'required' => true,
				'serialized' => true,
				)
			),
			new Entity\StringField('SITE_ID', array(
				'required' => false,
				'validation' => function(){
					return array(
						new Validator\LengthFix(2),
					);
				}
				)
			),
			new Entity\ReferenceField('PRICETIPRIGHT', '\Mlife\Asz\Pricetipright', 
				array('=this.ID' => 'ref.IDTIP')
			),
		);
	}
	
}
?>