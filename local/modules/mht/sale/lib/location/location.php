<?php
/**
 * Bitrix Framework
 * @package Bitrix\Sale\Location
 * @subpackage sale
 * @copyright 2001-2014 Bitrix
 */
namespace Bitrix\Sale\Location;

use Bitrix\Main;
use Bitrix\Main\DB;
use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
use Bitrix\Sale\Location\Name;

use Bitrix\Sale\Location\DB\Helper;

Loc::loadMessages(__FILE__);

final class LocationTable extends TreeEntity
{
	public static function getFilePath()
	{
		return __FILE__;
	}

	public static function getTableName()
	{
		return 'b_sale_location';
	}

	/**
	* Returns location with the specified code.
	*
	* @param string $code location code to search for
	*
	* @throws Bitrix\Main\ArgumentNullException
	*
	* @return Bitrix\Main\DB\Result location
	*/
	public static function getByCode($code = '', $parameters = array())
	{
		$code = Assert::expectStringNotNull($code, false, Loc::getMessage('SALE_LOCATION_LOCATION_ENTITY_BAD_ARGUMENT_CODE_UNSET_EXCEPTION'));

		if(!is_array($parameters))
			$parameters = array();

		$parameters['filter']['=CODE'] = $code;
		$parameters['limit'] = 1;

		return self::getList($parameters);
	}

	public static function checkFields($result, $primary, array $data)
	{
		parent::checkFields($result, $primary, $data);

		foreach(static::getEntity()->getFields() as $field)
		{
			$error = false;

			if($field->getName() == 'LATITUDE' && strlen($data['LATITUDE']))
			{
				// latitude is set in data and not empty, it must lay between -90 and 90
				if(!is_numeric($data['LATITUDE']))
					$error = Loc::getMessage('SALE_LOCATION_LOCATION_ENTITY_LATITUDE_TYPE_ERROR');
				elseif(($latitude = floatval($data['LATITUDE'])) && ($latitude < -90 || $latitude > 90))
					$error = Loc::getMessage('SALE_LOCATION_LOCATION_ENTITY_LATITUDE_RANGE_ERROR');
			}

			if($field->getName() == 'LONGITUDE' && strlen($data['LONGITUDE']))
			{
				// longitude is set in data and not empty, it must lay between -180 and 180
				if(!is_numeric($data['LONGITUDE']))
					$error = Loc::getMessage('SALE_LOCATION_LOCATION_ENTITY_LONGITUDE_TYPE_ERROR');
				elseif(($longitude = floatval($data['LONGITUDE'])) && ($longitude < -180 || $longitude > 180))
					$error = Loc::getMessage('SALE_LOCATION_LOCATION_ENTITY_LONGITUDE_RANGE_ERROR');
			}

			if($error !== false)
			{
				$result->addError(new Entity\FieldError(
					$field,
					$error,
					Entity\FieldError::INVALID_VALUE
				));
			}
		}
	}

	/**
	* Adds a new location
	*
	* @param mixed[] $data to be added. Additional data keys could be passed:
	*
	*	<ul>
	*		<li>
	*			NAME string[] : add name string to a newly created location
	*		</li>
	*		<li>
	*			EXTERNAL string[] : add external data records to a newly created location
	*		</li>
	*	</ul>
	*
	* @param mixed[] $behaviour an additional behaviour flags:
	*
	*	<ul>
	*		<li>
	*			REBALANCE boolean (default: true) : do rebalancing after add
	*		</li>
	*	</ul>
	*
	* @return Bitrix\Main\Entity\AddResult the result of add operation
	*/
	public static function add($data = array(), $behaviour = array('REBALANCE' => true, 'RESET_LEGACY' => true))
	{
		if(!is_array($behaviour))
			$behaviour = array();
		if(!isset($behaviour['REBALANCE']))
			$behaviour['REBALANCE'] = true;
		if(!isset($behaviour['RESET_LEGACY']))
			$behaviour['RESET_LEGACY'] = true;

		if(isset($data['EXTERNAL']))
		{
			$external = $data['EXTERNAL'];
			unset($data['EXTERNAL']);
		}

		if(isset($data['NAME']))
		{
			$name = $data['NAME'];
			unset($data['NAME']);
		}

		// force code to lowercase
		if(isset($data['CODE']))
			$data['CODE'] = ToLower($data['CODE']);

		// you are not allowed to modify tree data over LocationTable::add()
		self::applyRestrictions($data);

		// store tree data and basic
		$addResult = parent::add($data, $behaviour);

		// add connected data
		if($addResult->isSuccess())
		{
			$primary = $addResult->getId();

			// external
			if(isset($external))
				ExternalTable::addMultipleForOwner($primary, $external);

			// names
			if(isset($name))
				Name\LocationTable::addMultipleForOwner($primary, $name);

			if($behaviour['RESET_LEGACY'] && intval($data['TYPE_ID']))
			{
				$type = TypeTable::getList(array('filter' => array('=ID' => $data['TYPE_ID']), 'select' => array('CODE')))->fetch();
				if(strlen($type['CODE']) && in_array($type['CODE'], array('COUNTRY', 'REGION', 'CITY')))
					static::resetLegacyPath();
			}

			$GLOBALS['CACHE_MANAGER']->ClearByTag('sale-location-data');
		}

		$GLOBALS['CACHE_MANAGER']->ClearByTag('sale-location-data');

		return $addResult;
	}

	/**
	* Updates an existed location
	*
	* @param integer $primary location primary key of a element being updated
	* @param mixed[] $data new data to set. Additional data keys could be passed:
	*
	*	<ul>
	*		<li>
	*			NAME string[] : update name string for specified location
	*		</li>
	*		<li>
	*			EXTERNAL string[] : update external data records for specified location
	*		</li>
	*	</ul>
	*
	* @param mixed[] $behaviour an additional behaviour flags:
	*
	*	<ul>
	*		<li>
	*			REBALANCE boolean (default: true) : do rebalancing after add
	*		</li>
	*	</ul>
	*
	* @return Bitrix\Main\Entity\UpdateResult the result of update operation
	*/
	public static function update($primary, $data = array(), $behaviour = array('REBALANCE' => true))
	{
		$primary = Assert::expectIntegerPositive($primary, false, Loc::getMessage('SALE_LOCATION_LOCATION_ENTITY_BAD_ARGUMENT_PRIMARY_UNSET_EXCEPTION'));
		if(!is_array($behaviour))
			$behaviour = array();
		if(!isset($behaviour['REBALANCE']))
			$behaviour['REBALANCE'] = true;
		if(!isset($behaviour['RESET_LEGACY']))
			$behaviour['RESET_LEGACY'] = true;

		// first update parent, and if it succeed, do updates of the connected data

		if(isset($data['EXTERNAL']))
		{
			$external = $data['EXTERNAL'];
			unset($data['EXTERNAL']);
		}

		if(isset($data['NAME']))
		{
			$name = $data['NAME'];
			unset($data['NAME']);
		}

		// force code to lowercase
		if(isset($data['CODE']))
			$data['CODE'] = ToLower($data['CODE']);

		// you are not allowed to modify tree data over LocationTable::update()
		self::applyRestrictions($data);

		$updResult = parent::update($primary, $data, $behaviour);

		// update connected data
		if($updResult->isSuccess())
		{
			// external
			if(isset($external))
				ExternalTable::updateMultipleForOwner($primary, $external);

			// names
			if(isset($name))
				Name\LocationTable::updateMultipleForOwner($primary, $name);

			if($behaviour['RESET_LEGACY'] && (intval($data['TYPE_ID']) || isset($data['PARENT_ID'])))
			{
				$type = TypeTable::getList(array('filter' => array('=ID' => $data['TYPE_ID']), 'select' => array('CODE')))->fetch();
				if(strlen($type['CODE']) && in_array($type['CODE'], array('COUNTRY', 'REGION', 'CITY')))
					static::resetLegacyPath();
			}

			$GLOBALS['CACHE_MANAGER']->ClearByTag('sale-location-data');
		}

		return $updResult;
	}

	/**
	* Deletes location from the tree
	*
	*
	*/
	public static function delete($primary, $behaviour = array('REBALANCE' => true, 'DELETE_SUBTREE' => true))
	{
		$primary = Assert::expectIntegerPositive($primary, false, Loc::getMessage('SALE_LOCATION_LOCATION_ENTITY_BAD_ARGUMENT_PRIMARY_UNSET_EXCEPTION'));
		if(!is_array($behaviour))
			$behaviour = array();
		if(!isset($behaviour['REBALANCE']))
			$behaviour['REBALANCE'] = true;
		if(!isset($behaviour['RESET_LEGACY']))
			$behaviour['RESET_LEGACY'] = true;
		if(!isset($behaviour['DELETE_SUBTREE']))
			$behaviour['DELETE_SUBTREE'] = true;

		// delete connected data of sub-nodes
		if($behaviour['DELETE_SUBTREE'])
		{
			$rangeSql = parent::getSubtreeRangeSqlForNode($primary);

			Name\LocationTable::deleteMultipleByParentRangeSql($rangeSql);
			ExternalTable::deleteMultipleByParentRangeSql($rangeSql);
		}

		if($behaviour['RESET_LEGACY'])
			$data = static::getList(array('filter' => array('=ID' => $primary), 'select' => array('TYPE_ID')))->fetch();

		$delResult = parent::delete($primary, $behaviour);

		// delete connected data
		if($delResult->isSuccess())
		{
			Name\LocationTable::deleteMultipleForOwner($primary);
			ExternalTable::deleteMultipleForOwner($primary);

			if($behaviour['RESET_LEGACY'] && intval($data['TYPE_ID']))
			{
				$type = TypeTable::getList(array('filter' => array('=ID' => $data['TYPE_ID']), 'select' => array('CODE')))->fetch();
				if(strlen($type['CODE']) && in_array($type['CODE'], array('COUNTRY', 'REGION', 'CITY')))
					static::resetLegacyPath();
			}

			$GLOBALS['CACHE_MANAGER']->ClearByTag('sale-location-data');
		}

		return $delResult;
	}

	/**
	*
	*
	*
	*/
	public static function getExternalData($primary, $parameters = array())
	{
		$primary = Assert::expectIntegerPositive($primary, false, Loc::getMessage('SALE_LOCATION_LOCATION_ENTITY_BAD_ARGUMENT_PRIMARY_UNSET_EXCEPTION'));

		if(!is_array($parameters) || empty($parameters))
			$parameters = array();

		$parameters['filter']['LOCATION_ID'] = $primary;

		return ExternalTable::getList($parameters);
	}

	// todo: make getList with SITE_ID parameter to have an ability to filter by SITE_ID using orm (even slowly)

	/**
	 * Fetches a parent chain of a specified node, using its code
	 * 
	 * Available keys in $behaviour
	 * SHOW_LEAF : if set to true, return node itself in the result
	 */
	public static function getPathToNodeByCode($code, $parameters, $behaviour = array('SHOW_LEAF' => true))
	{
		$code = Assert::expectStringNotNull($code, false, Loc::getMessage('SALE_LOCATION_LOCATION_ENTITY_BAD_ARGUMENT_CODE_UNSET_EXCEPTION'));

		return self::getPathToNodeByCondition(array('=CODE' => $code), $parameters, $behaviour);
	}

	public static function checkNodeIsParentOfNode($primary, $childPrimary, $behaviour = array('ACCEPT_CODE' => false, 'CHECK_DIRECT' => false))
	{
		if(!$behaviour['ACCEPT_CODE'])
			return static::checkNodeIsParentOfNodeById($primary, $childPrimary, $behaviour);

		$primary = Assert::expectStringNotNull($primary, false, Loc::getMessage('SALE_LOCATION_LOCATION_ENTITY_BAD_ARGUMENT_CODE_UNSET_EXCEPTION'));
		$childPrimary = Assert::expectStringNotNull($childPrimary, false, Loc::getMessage('SALE_LOCATION_LOCATION_ENTITY_BAD_ARGUMENT_CODE_UNSET_EXCEPTION'));

		return static::checkNodeIsParentOfNodeByFilters(array('=CODE' => $primary), array('=CODE' => $childPrimary), $behaviour);
	}

	//////////////////////////////////////
	// High-load queries
	//////////////////////////////////////

	// fast version of self::getList(), where functionality sacrificed to the sake of speed
	// supports: filter and select keys (limited) in $parameters

	// todo: rewrite this function for better $parameters support
	/**
	*
	*
	* @param
	*
	* @return
	*/
	public static function getListFast($parameters = array())
	{
		$dbConnection = Main\HttpApplication::getConnection();
		$dbHelper = $dbConnection->getSqlHelper();

		// we require autocomplete to answer ASAP, so say hello to direct query

		// tables
		$locationTable = LocationTable::getTableName();
		$locationNameTable = Name\LocationTable::getTableName();
		$locationGroupTable = GroupLocationTable::getTableName();
		$locationSiteTable = SiteLocationTable::getTableName();
		$locationTypeTable = TypeTable::getTableName();

		//////////////////////////////////
		// sql parameters prepare
		//////////////////////////////////

		if(strlen($parameters['filter']['SITE_ID']))
		{
			$filterSite = $dbHelper->forSql(substr($parameters['filter']['SITE_ID'], 0, 2));

			$hasLocLinks = SiteLocationTable::checkLinkUsage($filterSite, 'L');
			$hasGrpLinks = SiteLocationTable::checkLinkUsage($filterSite, 'G');

			if($hasLocLinks || $hasGrpLinks)
				$doFilterBySite = true;
		}
		if(strlen($parameters['filter']['NAME']))
		{
			$doFilterByMainParams = true;
			$doFilterByName = true;

			// user-typed '%' are not allowed in like expression - ddos risk
			$filterName = ToUpper($dbHelper->forSql(str_replace('%', '', $parameters['filter']['NAME'])));
		}

		if(strlen($parameters['filter']['PRIMARY']))
		{
			$doFilterByMainParams = true;
			$doFilterByPrimaryCode = true;

			// user-typed '%' are not allowed in like expression - ddos risk
			$filterPrimaryCode = ToLower($dbHelper->forSql(str_replace('%', '', $parameters['filter']['PRIMARY'])));

			if(is_numeric($parameters['filter']['PRIMARY']) && $parameters['filter']['PRIMARY'] == intval($parameters['filter']['PRIMARY']))
			{
				$doFilterByPrimaryId = true;
				$filterPrimaryId = intval($parameters['filter']['PRIMARY']);
			}
		}
		if(intval($parameters['filter']['ID']))
		{
			$doFilterById = true;
			$filterId = intval($parameters['filter']['ID']);
		}
		if(strlen($parameters['filter']['LANGUAGE_ID']))
		{
			$filterLang = $dbHelper->forSql(substr($parameters['filter']['LANGUAGE_ID'], 0, 2));
		}
		if(isset($parameters['filter']['PARENT_ID']) && intval($parameters['filter']['PARENT_ID']) >= 0)
		{
			$doFilterByParent = true;
			$filterParentId = intval($parameters['filter']['PARENT_ID']);
		}
		if(intval($parameters['filter']['TYPE_ID']))
		{
			$doFilterByType = true;
			$filterTypeId = intval($parameters['filter']['TYPE_ID']);
		}
		if(intval($parameters['filter']['EXCLUDE_SUBTREE']))
		{
			$doFilterByExclude = true;
			$filterExclude = intval($parameters['filter']['EXCLUDE_SUBTREE']);

			$res = self::getById($filterExclude)->fetch();
			if($res)
			{
				$excludeMarginLeft = $res['LEFT_MARGIN'];
				$excludeMarginRight = $res['RIGHT_MARGIN'];
			}
			else
				$doFilterByExclude = false;
		}

		// filter select fields
		if(!is_array($parameters['select']))
			$parameters['select'] = array();

		$map = self::getMap();
		foreach($parameters['select'] as $k => $field)
		{
			if($field == 'CHILD_CNT')
				$doCountChildren = true;

			if(in_array($field, array('ID', 'CODE', 'SORT', 'LEFT_MARGIN', 'RIGHT_MARGIN')) || !isset($map[$field]) || !in_array($map[$field]['data_type'], array('integer', 'string', 'float', 'boolean')))
				unset($parameters['select'][$k]);
		}

		//////////////////////////////////
		// sql query build
		//////////////////////////////////

		$fields = array(
			'L.ID' => 'L.ID',
			'L.CODE' => 'L.CODE',
			'L.SORT' => 'L.SORT',
			'LT.SORT' => 'LT_SORT',
			'LN.NAME' => 'LN.NAME',
			'L.LEFT_MARGIN' => 'L.LEFT_MARGIN',
			'L.RIGHT_MARGIN' => 'L.RIGHT_MARGIN'
		);
		$groupFields = $fields;

		// for select fields and group fields
		foreach($parameters['select'] as $fld)
		{
			if($fld == 'CHILD_CNT')
			{
				$fields['COUNT(LC.ID)'] = 'CHILD_CNT';
			}
			else
			{
				$lFld = 'L.'.$fld;
				if(isset($fields[$lFld]))
					continue;

				$fields[$lFld] = $lFld;
				$groupFields[$lFld] = $lFld;
			}
		}

		// make select sql
		$selectSql = array();
		foreach($fields as $fld => $alias)
		{
			if($fld == $alias)
				$selectSql[] = $fld;
			else
				$selectSql[] = $fld.' as '.$alias;
		}

		$selectSql = implode(', ', $selectSql);
		$groupSql = implode(', ', array_keys($groupFields));

		$mainSql = "select {$selectSql}
						from {$locationTable} L 
							inner join {$locationNameTable} LN on L.ID = LN.LOCATION_ID
							inner join {$locationTypeTable} LT on L.TYPE_ID = LT.ID ".

							($doCountChildren ? "
								left join {$locationTable} LC on L.ID = LC.PARENT_ID
							" : "")." 

						%SITE_FILTER_CONDITION%

						where 
							LN.LANGUAGE_ID = '{$filterLang}'

							%MAIN_FILTER_CONDITION%

							".

							($doFilterByParent ? "
								and L.PARENT_ID = {$filterParentId}
							" : "").

							($doFilterById ? "
								and L.ID = {$filterId}
							" : "").

							($doFilterByExclude ? "
								and not(L.LEFT_MARGIN <= {$excludeMarginLeft} and L.RIGHT_MARGIN >= {$excludeMarginRight})
							" : "").

							($doFilterByType ? "
								and L.TYPE_ID = {$filterTypeId}
							" : "").

							($doCountChildren ? "
								group by {$groupSql}
							" : "");

		// todo: when search by ID or CODE, the better way is to break query onto UNIONs: first union stands for NAME_UPPER search, second - for ID exact match and the third - for CODE
		if($doFilterByMainParams)
		{
			$mp = array();

			if($doFilterByPrimaryId)
				$mp[] = "L.ID = {$filterPrimaryId}";
			if($doFilterByPrimaryCode)
				$mp[] = "L.CODE like '{$filterPrimaryCode}%'";
			if($doFilterByName)
				$mp[] = "LN.NAME_UPPER like '{$filterName}%'";

			$cnt = count($mp);
			$mainSql = str_replace('%MAIN_FILTER_CONDITION%', 

				' and '.
					($cnt > 1 ? '(' : '').
						implode(' or ', $mp).
					($cnt > 1 ? ')' : ''),

			$mainSql);
		}
		else
			$mainSql = str_replace('%MAIN_FILTER_CONDITION%', '', $mainSql);

		if(!$doFilterBySite)
			$sql = str_replace('%SITE_FILTER_CONDITION%', '', $mainSql);
		else
		{
			$sql = array();
			if($hasLocLinks)
			{
				$sql[] = str_replace('%SITE_FILTER_CONDITION%', "

					inner join {$locationTable} L2 on L2.LEFT_MARGIN <= L.LEFT_MARGIN and L2.RIGHT_MARGIN >= L.RIGHT_MARGIN
					inner join {$locationSiteTable} LS2 on L2.ID = LS2.LOCATION_ID and LS2.LOCATION_TYPE = 'L' and LS2.SITE_ID = '{$filterSite}'

				", $mainSql);
			}
			if($hasGrpLinks)
			{
				$sql[] = str_replace('%SITE_FILTER_CONDITION%', "

					inner join {$locationTable} L2 on L2.LEFT_MARGIN <= L.LEFT_MARGIN and L2.RIGHT_MARGIN >= L.RIGHT_MARGIN
					inner join {$locationGroupTable} LG on LG.LOCATION_ID = L2.ID
					inner join {$locationSiteTable} LS2 on LG.LOCATION_GROUP_ID = LS2.LOCATION_ID and LS2.LOCATION_TYPE = 'G' and LS2.SITE_ID = '{$filterSite}'

				", $mainSql);
			}

			$cnt = count($sql);
			$sql = ($cnt > 1 ? '(' : '').implode(') union (', $sql).($cnt > 1 ? ')' : '');
		}

		if(!is_array($parameters['order']))
			$sql .= " order by 3, 4 desc, 5";
		else
		{
			// currenly spike
			if(isset($parameters['order']['NAME.NAME']))
				$sql .= " order by 5 ".($parameters['order']['NAME.NAME'] == 'asc' ? 'asc' : 'desc');
		}

		$artificialNav = false;
		$offset = intval($parameters['offset']);
		$limit = intval($parameters['limit']);

		if($limit)
		{
			if($dbConnection->getType() != 'mssql')
			{
				$sql = $dbHelper->getTopSql($sql, $limit, $offset);
			}
			else
			{
				// have no idea how to use limit-offset in UNION for transact
				$artificialNav = true;
			}
		}

		$res = $dbConnection->query($sql);

		if($artificialNav)
		{
			$result = array();
			$i = -1;
			while($item = $res->fetch())
			{
				$i++;

				if($i < $offset)
					continue;

				if($i >= $offset + $limit)
					break;

				$result[] = $item;
			}

			return new DB\ArrayResult($result);
		}
		else
			return $res;
	}

	public static function resetLegacyPath()
	{
		$dbConnection = Main\HttpApplication::getConnection();
		$locTable = static::getTableName();

		$types = array();
		$res = TypeTable::getList(array(
			'filter' => array('CODE' => array('COUNTRY', 'REGION', 'CITY')),
			'select' => array('ID', 'CODE')
		));
		while($item = $res->fetch())
			$types[$item['CODE']] = $item['ID'];

		if(!empty($types))
		{
			if(!$dbConnection->isTableExists('b_sale_loc_rebind'))
				$dbConnection->query("create table b_sale_loc_rebind (TARGET_ID ".Helper::getSqlForDataType('int').", LOCATION_ID ".Helper::getSqlForDataType('int').")");
			else
				$dbConnection->query("truncate table b_sale_loc_rebind");

			$sqlWhere = array();
			foreach($types as $code => $id)
				$sqlWhere[] = "'".intval($id)."'";

			$dbConnection->query("update ".$locTable." set COUNTRY_ID = NULL, REGION_ID = NULL, CITY_ID = NULL where TYPE_ID in (".implode(', ', $sqlWhere).")");

			if(intval($types['REGION']) && intval($types['COUNTRY']))
			{
				// countries for regions
				$dbConnection->query("insert into b_sale_loc_rebind (TARGET_ID, LOCATION_ID) select A.ID as ONE, B.ID as TWO from ".$locTable." A inner join ".$locTable." B on A.TYPE_ID = '".intval($types['REGION'])."' and B.TYPE_ID = '".intval($types['COUNTRY'])."' and B.LEFT_MARGIN <= A.LEFT_MARGIN and B.RIGHT_MARGIN >= A.RIGHT_MARGIN");
				Helper::mergeTables($locTable, 'b_sale_loc_rebind', array('COUNTRY_ID' => 'LOCATION_ID'), array('ID' => 'TARGET_ID'));
				$dbConnection->query("truncate table b_sale_loc_rebind");
			}

			if(intval($types['REGION']) && intval($types['CITY']))
			{
				// regions for cities
				$dbConnection->query("insert into b_sale_loc_rebind (TARGET_ID, LOCATION_ID) select A.ID as ONE, B.ID as TWO from ".$locTable." A inner join ".$locTable." B on A.TYPE_ID = '".intval($types['CITY'])."' and B.TYPE_ID = '".intval($types['REGION'])."' and B.LEFT_MARGIN <= A.LEFT_MARGIN and B.RIGHT_MARGIN >= A.RIGHT_MARGIN");
				Helper::mergeTables($locTable, 'b_sale_loc_rebind', array('REGION_ID' => 'LOCATION_ID'), array('ID' => 'TARGET_ID'));
				$dbConnection->query("truncate table b_sale_loc_rebind");
			}

			if(intval($types['COUNTRY']) && intval($types['CITY']))
			{
				// countries for cities
				$dbConnection->query("insert into b_sale_loc_rebind (TARGET_ID, LOCATION_ID) select A.ID as ONE, B.ID as TWO from ".$locTable." A inner join ".$locTable." B on A.TYPE_ID = '".intval($types['CITY'])."' and B.TYPE_ID = '".intval($types['COUNTRY'])."' and B.LEFT_MARGIN <= A.LEFT_MARGIN and B.RIGHT_MARGIN >= A.RIGHT_MARGIN");
				Helper::mergeTables($locTable, 'b_sale_loc_rebind', array('COUNTRY_ID' => 'LOCATION_ID'), array('ID' => 'TARGET_ID'));
			}

			Helper::dropTable('b_sale_loc_rebind');

			if(intval($types['COUNTRY']))
				$dbConnection->query("update ".$locTable." set COUNTRY_ID = ID where TYPE_ID = '".intval($types['COUNTRY'])."'");

			if(intval($types['REGION']))
				$dbConnection->query("update ".$locTable." set REGION_ID = ID where TYPE_ID = '".intval($types['REGION'])."'");

			if(intval($types['CITY']))
				$dbConnection->query("update ".$locTable." set CITY_ID = ID where TYPE_ID = '".intval($types['CITY'])."'");
		}
	}

	public static function getCodeValidators()
	{
		return array(
			new Entity\Validator\Unique(),
		);
	}

	public static function getMap()
	{
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
			'PARENTS' => array(
				'data_type' => 'Bitrix\Sale\Location\Location',
				'reference' => array(
					'<=ref.LEFT_MARGIN' => 'this.LEFT_MARGIN',
					'>=ref.RIGHT_MARGIN' => 'this.RIGHT_MARGIN'
				)
			),
			'CHILD' /*rename to CHILDREN*/ => array(
				'data_type' => 'Bitrix\Sale\Location\Location',
				'reference' => array(
					'=this.ID' => 'ref.PARENT_ID'
				)
			),
			'CHILD_CNT' /*rename to CHILDREN_CNT*/ => array(
				'data_type' => 'integer',
				'expression' => array(
					'count(%s)', 
					'CHILD.ID'
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

			'CNT' => array(
				'data_type' => 'integer',
				'expression' => array(
					'count(*)'
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
	}
}

