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

class PriceTable extends Entity\DataManager
{
	public static function getFilePath()
	{
		return __FILE__;
	}

	public static function getTableName()
	{
		return 'mlife_asz_price';
	}

	public static function getMap()
	{
		return array(
			new Entity\IntegerField('ID', array(
				'primary' => true,
				'autocomplete' => true,
				)
			),
			new Entity\StringField('IBLOCK', array(
				'required' => true
				)
			),
			new Entity\StringField('PRODID', array(
				'required' => true
				)
			),
			new Entity\StringField('PRICEID', array(
				'required' => true
				)
			),
			new Entity\StringField('PRICEVAL', array(
				'required' => false
				)
			),
			new Entity\StringField('SORTVAL', array(
				'required' => false
				)
			),
			new Entity\StringField('PRICECUR', array(
				'required' => true,
				'validation' => function(){
					return array(
						new Validator\LengthFix(3),
					);
				}
				)
			),
		);
	}
	
	public static function deleteprice($priceId,$elId)
	{

		$entity = static::getEntity();
		$result = new Entity\Result();
		
		//TODO сделать нормальную проверку
		if(intval($priceId)<1 || intval($elId)<1) return false;
		
		// delete
		$connection = \Bitrix\Main\Application::getConnection();
		$helper = $connection->getSqlHelper();

		$tableName = $entity->getDBTableName();

		$where = 'PRICEID='.$priceId.' AND PRODID='.$elId;

		$sql = "DELETE FROM ".$tableName." WHERE ".$where;
		
		//print_r($sql);//die();
		
		$connection->queryExecute($sql);


		return $result;
	}
	
	public static function deletepriceProd($elId)
	{

		$entity = static::getEntity();
		$result = new Entity\Result();
		
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