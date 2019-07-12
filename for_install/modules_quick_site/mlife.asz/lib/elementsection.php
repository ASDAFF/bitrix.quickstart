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

class ElementSectionTable extends Entity\DataManager
{

	public static function getFilePath()
	{
		return __FILE__;
	}

	public static function getTableName()
	{
		return 'b_iblock_section_element';
	}

	public static function getMap()
	{
		return array(
			new Entity\IntegerField('IBLOCK_SECTION_ID', array(
				'primary' => true,
				)
			),
			new Entity\IntegerField('IBLOCK_ELEMENT_ID', array(
				'primary' => true,
				)
			),
		);
	}
	
}