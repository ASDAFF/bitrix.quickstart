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

class CurencyTable extends Entity\DataManager
{
	public static function getFilePath()
	{
		return __FILE__;
	}

	public static function getTableName()
	{
		return 'mlife_asz_curency';
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
						new Validator\LengthFix(3),
					);
				}
				)
			),
			new Entity\BooleanField('BASE', array(
				'required' => true,
				'values' => array('N', 'Y'),
				)
			),
			new Entity\StringField('CURS', array(
				'required' => true
				)
			),
			new Entity\StringField('SITEID', array(
				'required' => false,
				'validation' => function(){
					return array(
						new Validator\LengthFix(2),
					);
				}
				)
			)
		);
	}
	
}
?>