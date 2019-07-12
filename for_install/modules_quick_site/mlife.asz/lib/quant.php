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

class QuantTable extends Entity\DataManager
{
	public static function getFilePath()
	{
		return __FILE__;
	}

	public static function getTableName()
	{
		return 'mlife_asz_quant';
	}

	public static function getMap()
	{
		return array(
			new Entity\IntegerField('PRODID', array(
				'primary' => true,
				'autocomplete' => false,
				)
			),
			new Entity\IntegerField('IBLOCKID', array(
				'required' => true,
				)
			),
			new Entity\IntegerField('KOL', array(
				'required' => true,
				)
			),
			new Entity\IntegerField('ZAK', array(
				'required' => true,
				)
			),
			new Entity\ReferenceField('EL', '\Mlife\Asz\ElementTable', 
				array('=this.PRODID' => 'ref.ID')
			),
		);
	}
	
	public static function deletequant($elId)
	{

		$entity = static::getEntity();
		$result = new Entity\Result();
		
		//TODO сделать нормальную проверку
		if(intval($elId)<1) return false;
		
		// delete
		$connection = \Bitrix\Main\Application::getConnection();
		$helper = $connection->getSqlHelper();

		$tableName = $entity->getDBTableName();

		$where = 'PRODID='.$elId;

		$sql = "DELETE FROM ".$tableName." WHERE ".$where;
		
		//print_r($sql);//die();
		
		$connection->queryExecute($sql);


		return $result;
	}
	
}
?>