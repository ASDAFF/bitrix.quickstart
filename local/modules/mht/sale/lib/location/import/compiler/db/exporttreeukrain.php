<?php
/**
 * This class is for internal use only, not a part of public API.
 * It can be changed at any time without notification.
 *
 * @access private
 */

namespace Bitrix\Sale\Location\Import\Compiler\Db;

use Bitrix\Main;
use Bitrix\Main\Entity;
use Bitrix\Sale\Location;

class ExportTreeUkrainTable extends ExportTreeTable
{
	public static function getFilePath()
	{
		return __FILE__;
	}

	public static function getTableName()
	{
		return 'b_tmp_export_tree_ukrain';
	}

	protected $settlementParent = array();
	protected $types = false;

	protected $typeMap = array(
		1 => 'CITY', 		//| місто              | город                  |
		2 => 'VILLAGE', 	//| смт                | пгт                    |
		3 => 'VILLAGE', 	//| селище             | поселок                |
		4 => 'VILLAGE', 	//| село               | село                   |
		5 => 'VILLAGE', 	//| хутір              | хутор                  |
		6 => 'VILLAGE', 	//| ст.                | ст.                    |
		7 => 'VILLAGE', 	//| санат.             | санат.                 |
		8 => 'VILLAGE', 	//| радгосп            | совхоз                 |
		9 => 'VILLAGE', 	//| вокзал             | вокзал                 |
		10 => 'VILLAGE', 	//| лісництво          | лесничество            |
		11 => 'VILLAGE', 	//| док                | док                    |
		12 => 'VILLAGE', 	//| поселення          | поселение              |
	);

	public function getMappedType($typeId)
	{
		$dbConnection = Main\HttpApplication::getConnection();

		if($this->types == false)
		{
			$res = $dbConnection->query('select ID, NAME, NAME_RU from b_tmp_ukrain_settlement_type');
			while($item = $res->fetch())
			{
				$this->types[$item['ID']] = array('NAME' => array(
					'ua' => array('NAME' => $item['NAME']),
					'ru' => array('NAME' => $item['NAME_RU'])
				));
			}
		}

		return $this->typeMap[$typeId];
	}

	public function addNode($data)
	{
		$data['LANGNAMES'] = serialize($data['NAME']);
		$data['NAME'] = $data['NAME']['ru']['NAME'];
		$data['CODE'] = $this->formatCode($this->exportOffset);

		$data['SYS_CODE'] = 'U_'.intval($data['ID']);
		unset($data['ID']);

		if(isset($data['ZIP']))
		{
			$data['EXTERNALS'] = serialize(array(
				'ZIP' => $data['ZIP']
			));
		}

		$res = self::add($data);
		if($res->isSuccess())
		{
			$this->exportOffset++;
			return $data['CODE'];
		}

		return false;
	}

	public function addRegion($params)
	{
		$dbConnection = Main\HttpApplication::getConnection();
		$item = $dbConnection->query("select NAME, NAME_RU from b_tmp_ukrain_region where ID = '".intval($params['ID'])."'")->fetch();

		return $this->addNode(array(
			'ID' => $params['ID'],
			'TYPE_CODE' => 'REGION',
			'PARENT_CODE' => $params['PARENT_CODE'],
			'NAME' => $this->getNames($params['ID'], 'REGION')
		));
	}

	public function addArea($params)
	{
		$dbConnection = Main\HttpApplication::getConnection();
		$item = $dbConnection->query("select NAME, NAME_RU from b_tmp_ukrain_area where ID = '".intval($params['ID'])."'")->fetch();

		return $this->addNode(array(
			'ID' => $params['ID'],
			'TYPE_CODE' => 'AREA',
			'PARENT_CODE' => $params['PARENT_CODE'],
			'NAME' => $this->getNames($params['ID'], 'AREA')
		));
	}

	public function getNames($id, $type)
	{
		$dbConnection = Main\HttpApplication::getConnection();

		switch($type)
		{
			case 'REGION':
				$table = 'b_tmp_ukrain_region';
				break;
			case 'AREA':
				$table = 'b_tmp_ukrain_area';
				break;
			case 'CITY':
				$table = 'b_tmp_ukrain_city';
				break;
			case 'VILLAGE':
				$table = 'b_tmp_ukrain_village';
				break;
		}

		$item = $dbConnection->query("select NAME, NAME_RU from ".$table." where ID = '".intval($id)."'")->fetch();

		$replaceFrom = 	array('обл.', 'р-н');
		$replaceTo = 	array('область', 'район');

		return array(
			'ua' => array('NAME' => str_replace($replaceFrom, $replaceTo, $item['NAME'])),
			'ru' => array('NAME' => str_replace($replaceFrom, $replaceTo, $item['NAME_RU']))
		);
	}

	public function getSettlementParentCode($params)
	{
		$key = intval($params['AREA_ID']) ? $params['AREA_ID'] : $params['REGION_ID'];

		if(!isset($this->settlementParent[$key]))
		{
			if(!isset($this->settlementParent[$params['REGION_ID']]))
			{
				// new region!
				$code = $this->addRegion(array(
					'ID' => $params['REGION_ID'],
					'PARENT_CODE' => '',
				));

				$this->settlementParent[$params['REGION_ID']] = $code;
			}

			if(intval($params['AREA_ID']))
			{
				if(!isset($this->settlementParent[$params['AREA_ID']]))
				{
					// new area!
					$code = $this->addArea(array(
						'ID' => $params['AREA_ID'],
						'PARENT_CODE' => $this->settlementParent[$params['REGION_ID']],
					));

					$this->settlementParent[$key] = $code;
				}
			}
		}

		return $this->settlementParent[$key];
	}

	public function buildFromUADB($options)
	{
		if(isset($options['NEXT_FREE_CODE']))
			$this->exportOffset = intval($options['NEXT_FREE_CODE']);

		$dbConnection = Main\HttpApplication::getConnection();

		// settlements
		$res = $dbConnection->query('select ID, ZIP, ZIP_TO, TYPE_ID, CITY_ID, REGION_ID, AREA_ID, VILLAGE_ID from b_tmp_ukrain_settlement');
		while($item = $res->fetch())
		{
			$code = $this->getSettlementParentCode(array(
				'REGION_ID' => $item['REGION_ID'],
				'AREA_ID' => $item['AREA_ID']
			));

			// now there can be several situations
			$type = $this->getMappedType($item['TYPE_ID']);

			// records where CITY_ID and VILLAGE_ID filled both
			if(intval($item['CITY_ID']) && intval($item['VILLAGE_ID']))
			{
				$type = 'VILLAGE';

				// must be attached to CITY
				$code = $this->settlementParent[$item['CITY_ID']];
				$id = $item['VILLAGE_ID'];

				//$item['VILLAGE_ID']
			}
			elseif(intval($item['CITY_ID']))
			{
				$type = 'CITY';
				$id = $item['CITY_ID'];
			}

			$this->settlementParent[$key] = $this->addNode(array(
				'ID' => $item['ID'],
				'TYPE_CODE' => $type,
				'PARENT_CODE' => $code,
				'NAME' => $this->getNames($id, $type),
				'ZIP' => $item['ZIP'],
				'ZIP_TO' => $item['ZIP_TO'],
			));
		}

	}

	public function create()
	{
		$dbConnection = Main\HttpApplication::getConnection();

		$table = static::getTableName();

		global $DB;

		if(!$DB->query('select * from '.$table.' where 1=0', true))
		{
			$dbConnection->query("create table ".$table." (

				ID int not null auto_increment primary key,

				CODE varchar(100),
				PARENT_CODE varchar(100),

				SYS_CODE varchar(100),

				TYPE_CODE varchar(20),
				FIAS_TYPE varchar(10),

				NAME varchar(100) not null,
				NAME_UA varchar(100) not null,
				ZIP varchar(10),
				ZIP_TO varchar(10),

				LANGNAMES varchar(300),
				EXTERNALS varchar(200),

				LATITUDE varchar(30),
				LONGITUDE varchar(30),

				ALTERNATE_COORDS varchar(100),
				BOUNDED_WITH varchar(100),

				SOURCE varchar(2) default 'U'
			)");

			// SYS_CODE will be U_ + settlement id

			$this->restoreIndexes();
		}
	}

	/*
	public function dropCodeIndex()
	{
		unset($this->codeIndex);

		if(!empty($this->regionCodeIndex))
			$this->codeIndex = $this->regionCodeIndex;
	}

	public function insert($data)
	{
		if(isset($this->codeIndex[$data['SYS_CODE']])) // already in there
			return;

		if($data['TYPE_CODE'] == 'REGION')
			$this->regionCodeIndex[$data['SYS_CODE']] = $this->formatCode($this->exportOffset);

		$this->codeIndex[$data['SYS_CODE']] = $this->formatCode($this->exportOffset);

		$data['CODE'] = $this->codeIndex[$data['SYS_CODE']];
		$data['PARENT_CODE'] = strlen($data['PARENT_SYS_CODE']) ? $this->codeIndex[$data['PARENT_SYS_CODE']] : '';

		unset($data['PARENT_SYS_CODE']);

		if(is_array($data['LANGNAMES']))
			$data['LANGNAMES'] = serialize($data['LANGNAMES']);

		if(is_array($data['EXTERNALS']))
			$data['EXTERNALS'] = serialize($data['EXTERNALS']);

		$this->exportOffset++;

		$this->inserter->insert($data);
	}

	public static function getMap()
	{
		$map = parent::getMap();
		$map['ZIP'] = array(
			'data_type' => 'string',
		);

		return $map;
	}
	*/

	public static function getMap()
	{
		$map = parent::getMap();

		$map['ZIP'] = array(
			'data_type' => 'string',
		);
		$map['ZIP_TO'] = array(
			'data_type' => 'string',
		);

		return $map;
	}
}
