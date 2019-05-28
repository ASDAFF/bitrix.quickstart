<?php
/**
 * Bitrix Framework
 * @package Bitrix\Sale\Location
 * @subpackage sale
 * @copyright 2001-2014 Bitrix
 */
namespace Bitrix\Sale\Location\Search;

use Bitrix\Main;
use Bitrix\Main\DB;
use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
use Bitrix\Sale\Location;

Loc::loadMessages(__FILE__);

final class ChainTable extends Entity\DataManager
{
	public static function getFilePath()
	{
		return __FILE__;
	}

	public static function getTableName()
	{
		return 'b_sale_loc_chains';
	}

	public static function cleanUp()
	{
		Main\HttpApplication::getConnection()->query('truncate table '.static::getTableName());
	}

	const STEP_SIZE = 100;

	public static function reInitData()
	{
		static::cleanUp();

		$offset = 0;
		$stat = array();

		$types = array();
		$typeSort = array();
		$res = Location\TypeTable::getList(array('select' => array('ID', 'CODE', 'SORT')));
		while($item = $res->fetch())
		{
			if($item['CODE'] == 'CITY' || $item['CODE'] == 'VILLAGE' || $item['CODE'] == 'STREET')
				$types[$item['CODE']] = $item['ID'];

			$typeSort[$item['ID']] = $item['SORT'];
		}

		$typesBack = array_flip($types);

		//_print_r($types);
		//_print_r($typeSort);

		while(true)
		{
			$res = Location\LocationTable::getList(array(
				'select' => array(
					'ID', 'TYPE_ID'
				),
				'filter' => array(
					'=TYPE_ID' => array_values($types)
				),
				'limit' => self::STEP_SIZE,
				'offset' => $offset
			));

			$cnt = 0;
			while($item = $res->fetch())
			{
				$resPath = Location\LocationTable::getPathToNode($item['ID'], array(
					'select' => array('ID', 'TYPE_ID'),
					'filter' => array('=TYPE_ID' => array_values($types))
				));
				$path = array();
				while($pItem = $resPath->fetch())
				{
					$path[$typesBack[$pItem['TYPE_ID']]] = $pItem['ID'];
				}

				//_print_r($path);

				$data = array(
					'CITY_ID' => isset($path['CITY']) ? $path['CITY'] : 0,
					'VILLAGE_ID' => isset($path['VILLAGE']) ? $path['VILLAGE'] : 0,
					'STREET_ID' => isset($path['STREET']) ? $path['STREET'] : 0,

					'TYPE_SORT' => $typeSort[$item['TYPE_ID']],

					'LOCATION_ID' => $item['ID']
				);

				//_print_r($data);
				foreach($data as &$value)
					$value = "'".$value."'";

				//static::add($data);
				$GLOBALS['DB']->query("insert into ".static::getTableName()." (CITY_ID, VILLAGE_ID, STREET_ID, TYPE_SORT, LOCATION_ID) values (".implode(', ', $data).")");

				$cnt++;
			}

			if(!$cnt)
				break;

			$offset += self::STEP_SIZE;
		}
	}

	public static function search($words)
	{
		$dbConnection = Main\HttpApplication::getConnection();
		$dbHelper = Main\HttpApplication::getConnection()->getSqlHelper();

		$wordStatTableName = WordStatTable::getTableName();

		$preparedLike = array();
		foreach($words as $word)
			$preparedLike[] = "%TABLE_NAME%.WORD like '".$dbHelper->forSql($word)."%'";

		$preparedLike = implode(' or ', $preparedLike);

		$sql = "
			select C.*, WS_CITY.WORD as CWORD, WS_VILLAGE.WORD as VWORD, WS_STREET.WORD as SWORD from ".static::getTableName()." C

				inner join b_sale_loc_word_stat WS_STREET on 

					(
						WS_STREET.TYPE_ID = '7'
						and
						WS_STREET.LOCATION_ID = C.STREET_ID
						and
						(
							(".str_replace(array('%TABLE_NAME%'), array('WS_STREET'), $preparedLike).")
							or
							(WS_STREET.LOCATION_ID = '0')
						)
					)

				inner join b_sale_loc_word_stat WS_VILLAGE on 

					(
						WS_VILLAGE.TYPE_ID = '6'
						and
						WS_VILLAGE.LOCATION_ID = C.VILLAGE_ID
						and
						(
							(".str_replace(array('%TABLE_NAME%'), array('WS_VILLAGE'), $preparedLike).")
							or
							(WS_VILLAGE.LOCATION_ID = '0')
						)
					)

				inner join b_sale_loc_word_stat WS_CITY on 

					(
						WS_CITY.TYPE_ID = '3'
						and
						WS_CITY.LOCATION_ID = C.CITY_ID
						and
						(
							(".str_replace(array('%TABLE_NAME%'), array('WS_CITY'), $preparedLike).")
							or
							(WS_CITY.LOCATION_ID = '0')
						)
					)

			order by C.TYPE_SORT desc
			limit 5
		";

		/*
		$sql = "
			select * from ".static::getTableName()." C
				where 
					(
						C.CITY_ID = 0 or C.CITY_ID in (
							select LOCATION_ID from b_sale_loc_word_stat 
								where
									TYPE_ID = 3
									and
									(".$preparedLike.")
						)
					)

					and 

					(
						C.VILLAGE_ID = 0 or C.VILLAGE_ID in (
							select LOCATION_ID from b_sale_loc_word_stat 
								where
									TYPE_ID = 6
									and
									(".$preparedLike.")
						)
					)

					and

					(
						C.STREET_ID = 0 or C.STREET_ID in (
							select LOCATION_ID from b_sale_loc_word_stat 
								where
									TYPE_ID = 7
									and
									(".$preparedLike.")
						)
					)

			order by C.TYPE_SORT desc
			limit 5
		";
		*/

		print('<pre>');
		print_r($sql);
		print('</pre>');

		return $dbConnection->query($sql);
	}

	public static function getMap()
	{
		return array(

			'ID' => array(
				'data_type' => 'integer',
				'primary' => true
			),

			'CITY_ID' => array(
				'data_type' => 'integer',
			),
			'VILLAGE_ID' => array(
				'data_type' => 'integer',
			),
			'STREET_ID' => array(
				'data_type' => 'integer',
			),
			'TYPE_SORT' => array(
				'data_type' => 'integer',
			),
			'LOCATION_ID' => array(
				'data_type' => 'integer',
			),
		);

		/*
		return array(

			'ID' => array(
				'data_type' => 'integer',
				'primary' => true,
				'autocomplete' => true,
				'title' => 'ID'
			),
			'CODE' => array(
				'data_type' => 'string',
				'title' => Loc::getMessage('SALE_LOCATION_LOCATION_ENTITY_CODE_FIELD'),
				'required' => true,
				'validation' => array(__CLASS__, 'getCodeValidators')
			),

			'LEFT_MARGIN' => array(
				'data_type' => 'integer',
			),
			'RIGHT_MARGIN' => array(
				'data_type' => 'integer',
			),
			'DEPTH_LEVEL' => array(
				'data_type' => 'integer',
			),
			'SORT' => array(
				'data_type' => 'integer',
				'default' => 100,
				'title' => Loc::getMessage('SALE_LOCATION_LOCATION_ENTITY_SORT_FIELD')
			),
			'PARENT_ID' => array(
				'data_type' => 'integer',
				'default' => 0,
				'title' => Loc::getMessage('SALE_LOCATION_LOCATION_ENTITY_PARENT_ID_FIELD')
			),
			'TYPE_ID' => array(
				'data_type' => 'integer',
				'required' => true,
				'title' => Loc::getMessage('SALE_LOCATION_LOCATION_ENTITY_TYPE_ID_FIELD')
			),
			'LATITUDE' => array(
				'data_type' => 'float',
				'title' => Loc::getMessage('SALE_LOCATION_LOCATION_ENTITY_LATITUDE_FIELD')
			),
			'LONGITUDE' => array(
				'data_type' => 'float',
				'title' => Loc::getMessage('SALE_LOCATION_LOCATION_ENTITY_LONGITUDE_FIELD')
			),

			// virtual
			'TYPE' => array(
				'data_type' => 'Bitrix\Sale\Location\Type',
				'reference' => array(
					'=this.TYPE_ID' => 'ref.ID'
				),
				'join_type' => "inner"
			),
			'NAME' => array(
				'data_type' => 'Bitrix\Sale\Location\Name\Location',
				'reference' => array(
					'=this.ID' => 'ref.LOCATION_ID'
				),
				'join_type' => "inner"
			),
			'PARENT' => array(
				'data_type' => 'Bitrix\Sale\Location\Location',
				'reference' => array(
					'=this.PARENT_ID' => 'ref.ID'
				)
			),
			'CHILD' => array(
				'data_type' => 'Bitrix\Sale\Location\Location',
				'reference' => array(
					'=this.ID' => 'ref.PARENT_ID'
				)
			),
			'CHILD_CNT' => array(
				'data_type' => 'integer',
				'expression' => array(
					'count(%s)', 
					'CHILD.ID'
				)
			),
			'CNT' => array(
				'data_type' => 'integer',
				'expression' => array(
					'count(*)'
				)
			),
			'EXTERNAL' => array(
				'data_type' => 'Bitrix\Sale\Location\External',
				'reference' => array(
					'=this.ID' => 'ref.LOCATION_ID'
				)
			),
			'DEFAULT_SORT' => array(
				'data_type' => 'Bitrix\Sale\Location\DefaultSiteTable',
				'reference' => array(
					'=this.CODE' => 'ref.LOCATION_CODE'
				)
			),

			// do not remove unless you want migrator to be dead
			'COUNTRY_ID' => array(
				'data_type' => 'integer',
			),
			'REGION_ID' => array(
				'data_type' => 'integer',
			),
			'CITY_ID' => array(
				'data_type' => 'integer',
			),
			'LOC_DEFAULT' => array(
				'data_type' => 'string',
			),

		);
		*/
	}
}

