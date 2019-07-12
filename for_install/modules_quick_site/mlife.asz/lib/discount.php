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

class DiscountTable extends Entity\DataManager
{
	public static function getFilePath()
	{
		return __FILE__;
	}

	public static function getTableName()
	{
		return 'mlife_asz_discount';
	}

	public static function getMap()
	{
		return array(
			new Entity\IntegerField('ID', array(
				'primary' => true,
				'autocomplete' => true,
				)
			),
			new Entity\IntegerField('IBLOCK_ID', array(
				'required' => true,
				)
			),
			new Entity\IntegerField('CATEGORY_ID', array(
				'required' => false,
				)
			),
			new Entity\IntegerField('PRODUCT_ID', array(
				'required' => false,
				)
			),
			new Entity\EnumField('TIP', array(
				'required' => true,
				'values' => array(1, 2, 3)
				)
			),
			new Entity\IntegerField('PRIOR', array(
				'required' => false,
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
			new Entity\StringField('DESC', array(
				'required' => false,
				'validation' => function(){
					return array(
						new Entity\Validator\Length(null, 255),
					);
				}
				)
			),
			new Entity\StringField('VALUE', array(
				'required' => false,
				)
			),
			new Entity\StringField('MAXSUMM', array(
				'required' => false,
				)
			),
			new Entity\BooleanField('PRIORFIX', array(
				'required' => false,
				'values' => array('N', 'Y'),
				)
			),
			new Entity\BooleanField('ACTIVE', array(
				'required' => false,
				'values' => array('N', 'Y'),
				)
			),
			new Entity\DatetimeField('DATE_START', array(
				'required' => true,
				)
			),
			new Entity\DatetimeField('DATE_END', array(
				'required' => true,
				)
			),
			new Entity\TextField('GROUPS', array(
				'required' => true,
				'serialized' => true,
				)
			),
		);
	}
	
}
?>