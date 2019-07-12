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

class UserTable extends Entity\DataManager
{
	public static function getFilePath()
	{
		return __FILE__;
	}

	public static function getTableName()
	{
		return 'mlife_asz_user';
	}

	public static function getMap()
	{
		return array(
			new Entity\IntegerField('UID', array(
				'primary' => true,
				'autocomplete' => true,
				)
			),
			new Entity\IntegerField('TIME', array(
				'required' => true,
				)
			),
			new Entity\IntegerField('BX_UID', array(
				'required' => false,
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
		);
	}
	
}