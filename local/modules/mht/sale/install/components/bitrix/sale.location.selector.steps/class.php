<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/**
 * Bitrix Framework
 * @package bitrix
 * @subpackage sale
 * @copyright 2001-2014 Bitrix
 */

use Bitrix\Main\Config;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Bitrix\Main\Data;

use Bitrix\Sale\Location;

CBitrixComponent::includeComponentClass("bitrix:sale.location.selector.search");

Loc::loadMessages(__FILE__);

class CBitrixLocationSelectorStepsComponent extends CBitrixLocationSelectorSearchComponent
{
	protected $connector = null;

	/**
	 * Function checks and prepares all the parameters passed. Everything about $arParam modification is here.
	 * @param mixed[] $arParams List of unchecked parameters
	 * @return mixed[] Checked and valid parameters
	 */
	public function onPrepareComponentParams($arParams)
	{
		$arParams = parent::onPrepareComponentParams($arParams);

		self::tryParseInt($arParams['EXCLUDE_SUBTREE']);

		// about preloading
		self::tryParseBoolean($arParams['PRECACHE_LAST_LEVEL']);

		return $arParams;
	}

	protected function getLocationListParameters()
	{
		return array(
			'select' => array_merge($this->getNodeSelectFields(), array(
				'LNAME' => 'NAME.NAME',
				'SHORT_NAME' => 'NAME.SHORT_NAME',
				'LEFT_MARGIN',
				'RIGHT_MARGIN',
				'CHILD_CNT',
				'TYPE_ID' // cut off?
			)),
			'filter' => array(
				'NAME.LANGUAGE_ID' => LANGUAGE_ID
			)
		);
	}

	protected function obtainCacheDependentData()
	{
		parent::obtainCacheDependentData();

		if(!is_array($this->dbResult['PRECACHED_POOL']))
			$this->dbResult['PRECACHED_POOL'] = array();

		$this->dbResult['BUNDLES_INCOMPLETE'] = array();

		// all bundles that are in PATH are incomplete by the way there were obtained, so ...
		if(is_array($this->dbResult['PATH']))
		{
			foreach($this->dbResult['PATH'] as $levelId => $level)
				$this->dbResult['BUNDLES_INCOMPLETE'][$levelId] = true;
		}

		if($this->arParams['PRECACHE_LAST_LEVEL'])
		{
			$parameters = $this->getLocationListParameters();
			if(!is_array($parameters))
				$parameters = array();
			if(!is_array($parameters['filter']))
				$parameters['filter'] = array();

			$parentId = false;
			if(is_array($this->dbResult['LOCATION']) && intval($this->dbResult['LOCATION']['ID']))
			{
				if($this->dbResult['LOCATION']['CHILD_CNT'] > 0)
					$parentId = intval($this->dbResult['LOCATION']['ID']);
				else
					$parentId = intval($this->dbResult['LOCATION']['PARENT_ID']);
			}
			else
			{
				// actually, we must find the last of TREE_TRUNK
				$lastId = false;
				foreach($this->dbResult['TREE_TRUNK'] as $id => $node)
				{
					$lastId = $node['ID'];
				}

				if($lastId != false)
					$parentId = $lastId;
			}

			if($parentId !== false)
			{
				// ... but if we get some bundle here, then it becomes complete
				unset($this->dbResult['BUNDLES_INCOMPLETE'][$parentId]);

				$parameters['filter']['=PARENT_ID'] = $parentId;
				$res = Location\LocationTable::getList($parameters);
				$res->addReplacedAliases(array('LNAME' => 'NAME'));
				while($item = $res->fetch())
				{
					$this->dbResult['PRECACHED_POOL'][$item['PARENT_ID']][$item['ID']] = $item;
				}
			}
		}
	}

	protected function obtainCachedData(&$cachedData)
	{
		parent::obtainCachedData($cachedData);

		// get tree trunk
		$this->obtainDataTreeTrunk($cachedData);
	}

	protected function obtainDataTreeTrunk(&$cachedData)
	{
		// check for static tree
		$forkItemFilter = false;

		$res = Location\LocationTable::getList(array(
			'group' => array('DEPTH_LEVEL'),
			'select' => array('DEPTH_LEVEL', 'CNT'),
			'order' => array('DEPTH_LEVEL' => 'asc')
		));
		$forkAtLevel = 0;
		while($item = $res->fetch())
		{
			if($item['CNT'] < 2)
				$forkAtLevel = $item['DEPTH_LEVEL'];
			else
				break;
		}

		if($forkAtLevel > 0)
			$forkItemFilter = array('DEPTH_LEVEL' => $forkAtLevel);

		// check for tree filtered by site

		if($this->filterBySite && is_array($cachedData['TEMP']['CONNECTORS']) && !empty($cachedData['TEMP']['CONNECTORS']))
		{
			$dcp = Location\LocationTable::getDeepestCommonParent($cachedData['TEMP']['CONNECTORS'], array('select' => array('ID')))->fetch();

			if(is_array($dcp) && intval($dcp['ID']))
				$forkItemFilter = array('ID' => intval($dcp['ID']));
		}

		$cachedData['TREE_TRUNK'] = array();
		if(is_array($forkItemFilter) && !empty($forkItemFilter)) // get fork item id
		{
			$res = Location\LocationTable::getPathToNodeByCondition($forkItemFilter, array(
				'select' => array_merge($this->getNodeSelectFields(), array('LNAME' => 'NAME.NAME')),
				'filter' => array('=NAME.LANGUAGE_ID' => LANGUAGE_ID)
			));
			$res->addReplacedAliases(array('LNAME' => 'NAME'));
			while($item = $res->fetch())
			{
				$cachedData['TREE_TRUNK'][] = $item;
			}
		}
	}

	protected function obtainDataAdditional()
	{
		parent::obtainDataAdditional();

		if($this->filterBySite && is_array($this->dbResult['PATH']) && !empty($this->dbResult['PATH']))
		{
			$linkTypeMap = Location\SiteLocationTable::getLinkStatusForMultipleNodes($this->dbResult['PATH'], $this->arParams['FILTER_SITE_ID'], $this->dbResult['TEMP']['CONNECTORS']);

			foreach($linkTypeMap as $id => $linkType)
			{
				if(!in_array($linkType, array(Location\SiteLocationTable::LSTAT_IS_CONNECTOR, Location\SiteLocationTable::LSTAT_BELOW_CONNECTOR)))
					$this->dbResult['PATH'][$id]['IS_UNCHOOSABLE'] = true;
			}
		}
	}

	public static function processSearchRequest()
	{
		static::checkRequiredModules();

		$parameters = static::processSearchGetParameters();
		$parameters['order'] = array('NAME.NAME' => 'asc');

		// have to implement post-check for site connection, because we need to know which nodes may have connected children
		// will have a bottleneck here, in case of great links and items number. refactor later and replace with some db query

		$siteId = $_REQUEST['FILTER']['SITE_ID'];

		if(strlen($siteId))
		{
			$points = array();
			$res = Location\SiteLocationTable::getConnectedLocations($siteId, array('select' => array(
					'ID' => 'ID',
					'LEFT_MARGIN' => 'LEFT_MARGIN',
					'RIGHT_MARGIN' => 'RIGHT_MARGIN'
				)
			), array('GET_LINKED_THROUGH_GROUPS' => true));

			while($item = $res->fetch())
				$points[intval($item['ID'])] = $item;

			unset($parameters['filter']['SITE_ID']);
		}

		$result = static::processSearchGetList($parameters);
		$result = static::processSearchGetAdditional($result);

		if(strlen($siteId))
		{
			$res = Location\SiteLocationTable::getLinkStatusForMultipleNodes($result['ITEMS'], $siteId, $points);
			foreach($result['ITEMS'] as $k => &$item)
			{
				if($res[$item['ID']] == Location\SiteLocationTable::LSTAT_IN_NOT_CONNECTED_BRANCH)
					unset($result['ITEMS'][$k]);

				$item['IS_UNCHOOSABLE'] = $res[$item['ID']] == Location\SiteLocationTable::LSTAT_ABOVE_CONNECTOR;
			}
		}

		// drop unwanted data
		foreach($result['ITEMS'] as &$item)
		{
			if(!!$_REQUEST['BEHAVIOUR']['PREFORMAT'])
			{
				$unChoosable = $item['IS_UNCHOOSABLE'];
				$path = $item['PATH'];

				$item = array(
					'DISPLAY' => $item['NAME'],
					'VALUE' => $item['ID'],
					'CODE' => $item['CODE'],
					'IS_PARENT' => $item['CHILD_CNT'] > 0
				);

				if($unChoosable)
					$item['IS_UNCHOOSABLE'] = $unChoosable;
				if(is_array($path))
					$item['PATH'] = $path;
			}
			else
			{
				unset($item['LEFT_MARGIN']);
				unset($item['RIGHT_MARGIN']);
			}
		}

		return $result;
	}

	/*
	protected static function processSearchGetList($parameters)
	{
		//_dump_r($parameters);

		$proxedParams = array(
			'select' => array(
				'ID',
				'CODE',
				'SORT',
				'LNAME' => 'NAME.NAME',
				'LEFT_MARGIN',
				'RIGHT_MARGIN'
			),
			'filter' => array(
			)
		);

		//_dump_r($proxedParams);

		$res = Location\LocationTable::getListFast($parameters);
		
		$result = array();
		while($item = $res->fetch())
			$result[] = $item;

		return $result;
	}
	*/

	protected static function processSearchGetAdditionalPathNodes(&$data)
	{
		if($_REQUEST['SHOW']['PATH'])
		{
			$pathes = static::getPathToNodes($data['ITEMS']);

			foreach($data['ITEMS'] as &$item)
				$item['PATH'] = $pathes['PATH'][$item['ID']];

			$data['ETC']['PATH_ITEMS'] = $pathes['PATH_ITEMS'];
		}
	}

	protected static function getPathToNodes($list)
	{
		$res = Location\LocationTable::getPathToMultipleNodes(
			$list, 
			array(
				'select' => (
					!!$_REQUEST['BEHAVIOUR']['PREFORMAT'] ? 
					array('ID', 'VALUE' => 'ID', 'DISPLAY' => 'NAME.NAME', 'CODE') : 
					array('ID', 'LNAME' => 'NAME.NAME', 'CODE')
				),
				'filter' => array('=NAME.LANGUAGE_ID' => LANGUAGE_ID)
			)
		);

		$pathItems = array();
		$result = array();

		while($path = $res->fetch())
		{
			// format path as required for JSON responce
			$chain = array();
			$itemId = false;

			$i = -1;
			foreach($path['PATH'] as $id => $pItem)
			{
				$i++;

				if(!$i) // we dont need for an item itself in the path chain
				{
					$itemId = $id;
					continue;
				}

				$pathItems[$pItem['ID']] = $pItem;
				$chain[] = intval($pItem['ID']);
			}

			$result['PATH'][$itemId] = $chain;
		}

		$result['PATH_ITEMS'] = $pathItems;

		return $result;
	}

	public function formatResult()
	{
		parent::formatResult();

		if(is_array($this->arResult['PATH']))
		{
			foreach($this->arResult['PATH'] as &$node)
			{
				unset($node['LEFT_MARGIN']);
				unset($node['RIGHT_MARGIN']);
			}
		}

		unset($this->arResult['LOCATION']['LEFT_MARGIN']);
		unset($this->arResult['LOCATION']['RIGHT_MARGIN']);
	}

	protected static function getClassName()
	{
		return __CLASS__;
	}
}