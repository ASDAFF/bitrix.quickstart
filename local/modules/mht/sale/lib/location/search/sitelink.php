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

final class SiteLinkTable extends Entity\DataManager
{
	public static function getFilePath()
	{
		return __FILE__;
	}

	public static function getTableName()
	{
		return 'b_sale_loc_search_sitelink';
	}

	public static function cleanUp()
	{
		$dbConnection = Main\HttpApplication::getConnection();

		try
		{
			$dbConnection->query('truncate table '.static::getTableName());
		}
		catch(\Bitrix\Main\DB\SqlQueryException $e)
		{
		}
	}

	public static function createTable()
	{
			$sql = "create table b_sale_loc_search_sitelink 
				(
					LOCATION_ID int,
					SITE_ID char(2),

					primary key (LOCATION_ID, SITE_ID)
				)";

		Main\HttpApplication::getConnection()->query($sql);
	}

	public static function createIndex()
	{
		$sql = 'create index IX_B_SALE_SITELINK_SITE on b_sale_loc_search_sitelink (

				SITE_ID
		)';

		Main\HttpApplication::getConnection()->query($sql);
	}

	const STEP_SIZE = 100;

	public static function initData($parameters = array())
	{
		static::cleanUp();

		$sql = "
			insert into b_sale_loc_search_sitelink 
				(LOCATION_ID, SITE_ID) 
			select distinct LC.ID, LS.SITE_ID
				from b_sale_loc_2site LS
					inner join b_sale_location L on LS.LOCATION_ID = L.ID
					inner join b_sale_location LC on LC.LEFT_MARGIN >= L.LEFT_MARGIN and LC.RIGHT_MARGIN <= L.RIGHT_MARGIN
		";

		Main\HttpApplication::getConnection()->query($sql);
	}

	public static function getMap()
	{
		return array(

			'LOCATION_ID' => array(
				'data_type' => 'integer',
				'primary' => true // tmp
			),
			'TYPE_ID' => array(
				'data_type' => 'integer',
			),


			'REGION_ID' => array(
				'data_type' => 'integer',
			),

			'SUBREGION_ID' => array(
				'data_type' => 'integer',
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

			'W_1' => array(
				'data_type' => 'string',
			),
			'W_2' => array(
				'data_type' => 'string',
			),
			'W_3' => array(
				'data_type' => 'string',
			),
			'W_4' => array(
				'data_type' => 'string',
			),
			'W_5' => array(
				'data_type' => 'string',
			),
			'W_6' => array(
				'data_type' => 'string',
			),
			'W_7' => array(
				'data_type' => 'string',
			),
			'W_8' => array(
				'data_type' => 'string',
			),
			'W_9' => array(
				'data_type' => 'string',
			),
			'W_10' => array(
				'data_type' => 'string',
			),

			'WORD_COUNT' => array(
				'data_type' => 'integer',
			),

			'TYPE_SORT' => array(
				'data_type' => 'integer',
			),
			'SORT' => array(
				'data_type' => 'integer',
			),
		);
	}
}

