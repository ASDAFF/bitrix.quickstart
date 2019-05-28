<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/**
 * Bitrix Framework
 * @package bitrix
 * @subpackage sale
 * @copyright 2001-2014 Bitrix
 */

use Bitrix\Main;
use Bitrix\Main\DB;
use Bitrix\Main\Config;
use Bitrix\Main\Localization\Loc;
use Bitrix\Sale\Location;
use Bitrix\Main\Data;

CBitrixComponent::includeComponentClass("bitrix:sale.location.selector.search");

Loc::loadMessages(__FILE__);

class CBitrixLocationSelectorSystemComponent extends CBitrixLocationSelectorSearchComponent
{
	const ID_BLOCK_LEN = 			90;
	const HUGE_TAIL_LEN = 			30;
	const PAGE_SIZE = 				10;
	const LOCATION_ENTITY_NAME = 	'\Bitrix\Sale\Location\LocationTable';

	protected $entityClass = false;
	protected $useGroups = true;
	protected $useCodes = false;

	protected $dbResult = array();
	
	private $locationsFromRequest = false;
	private $groupsFromRequest = false;

	/**
	 * Function checks and prepares all the parameters passed. Everything about $arParam modification is here.
	 * @param mixed[] $arParams List of unchecked parameters
	 * @return mixed[] Checked and valid parameters
	 */
	public function onPrepareComponentParams($arParams)
	{
		//$arParams = parent::onPrepareComponentParams($arParams);

		self::tryParseString($arParams['LINK_ENTITY_NAME']);
		self::tryParseString($arParams['INPUT_NAME']);
		self::tryParseString($arParams['ENTITY_PRIMARY']);
		self::tryParseString($arParams['ENTITY_VARIABLE_NAME'], 'id');
		self::tryParseInt($arParams['CACHE_TIME'], false, true);
		self::tryParseString($arParams['EDIT_MODE_SWITCH'], 'loc_selector_mode');

		return $arParams;
	}

	protected function obtainCachedData(&$cachedData)
	{
		$this->obtainDataLocationTypes($cachedData);
		$this->obtainDataGroups($cachedData);
		$this->obtainDataLevelOneLocations($cachedData);
	}

	protected function obtainCacheDependentData()
	{
		$this->obtainDataAdditional();
		$this->obtainDataConnectors();
	}

	protected function obtainDataLocationTypes(&$cachedData)
	{
		$res = Location\TypeTable::getList(array(
			'select' => array(
				'*',
				'LNAME' => 'NAME.NAME'
			),
			'filter' => array(
				'NAME.LANGUAGE_ID' => LANGUAGE_ID
			),
			'order' => array(
				'SORT' => 'asc',
				'NAME.NAME' => 'asc'
			)
		));
		$res->addReplacedAliases(array('LNAME' => 'NAME'));
		$types = array();
		while($item = $res->fetch())
			$types[$item['ID']] = $item;

		$cachedData['TYPES'] = $types;
	}

	protected function obtainDataGroups(&$cachedData)
	{
		$groups = array();

		if($this->useGroups)
		{
			$res = Location\GroupTable::getList(array(
				'select' => array('ID', 'CODE', 'LNAME' => 'NAME.NAME'),
				'filter' => array('NAME.LANGUAGE_ID' => LANGUAGE_ID)
			));
			$res->addReplacedAliases(array('LNAME' => 'NAME'));
			while($item = $res->fetch())
			{
				$item['ID'] = intval($item['ID']);
				$groups[$item['ID']] = $item;
			}
		}

		$cachedData['GROUPS'] = $groups;
	}

	protected function obtainDataLevelOneLocations(&$cachedData)
	{
		// here we require a tag cache

		$res = Location\LocationTable::getList(
			array(
				'filter' => array('PARENT_ID' => 0, 'NAME.LANGUAGE_ID' => LANGUAGE_ID),
				'select' => array('LNAME' => 'NAME.NAME', 'CODE', 'ID', 'CHILD_CNT')
			)
		);
		$res->addReplacedAliases(array('LNAME' => 'NAME'));
		$cachedData['LOCATIONS'] = array();
		while($item = $res->fetch())
		{
			$cachedData['LOCATIONS'][] = array(
				'ID' => $item['ID'],
				'NAME' => $item['NAME'],
				'IS_PARENT' => $item['CHILD_CNT'] > 0
			);
		}
	}

	/**
	 * Returns a list of items by supplying a set of their IDs or CODEs
	 * 
	 * @param string $entityClass
	 * @param string[]|integer[] $list
	 * @param mixed[] $parameters
	 * @param string $fieldCode identify what type of linking is used. Only two of legal values allowed: ID and CODE
	 *
     * @return Bitrix\Main\DB\ArrayResult $result
	 */
	protected static function getEntityListByListOfPrimary($entityClass, $list = array(), $parameters = array(), $fieldCode = 'ID')
	{
		$result = array();

        if(is_array($list) && !empty($list))
        {
            $block = array();
            $cnt = count($list);
            for($i = 0, $j = 0; $i < $cnt; $i++, $j++)
            {
                if($j == self::ID_BLOCK_LEN)
                {
                    $parameters['filter']['='.$fieldCode] = $block;
                    $res = $entityClass::getList($parameters);
                    while($item = $res->fetch())
                        $result[] = $item;

                    $block = array();
                    $j = 0;
                }

                $block[] = $list[$i];
            }

            if(!empty($block))
            {
                $parameters['filter']['='.$fieldCode] = $block;
                $res = $entityClass::getList($parameters);
                while($item = $res->fetch())
                    $result[] = $item;
            }
        }

		return new DB\ArrayResult($result);
	}

	protected function obtainDataConnectors()
	{
		if(!$this->arParams['LINK_ENTITY_NAME'])
		{
			$this->errors['FATAL'][] = Loc::getMessage('SALE_SLSS_LINK_ENTITY_NAME_NOT_SET');
			return;
		}

		$class = $this->entityClass;
		$parameters = array(
			'select' => array(
				'ID',
				'CODE',
				'LEFT_MARGIN',
				'RIGHT_MARGIN',
				'SORT',
				'TYPE_ID',
				'LNAME' => 'NAME.NAME'
			),
			'filter' => array(
				'NAME.LANGUAGE_ID' => LANGUAGE_ID
			)
		);

		$linkFld = $this->useCodes ? 'CODE' : 'ID';

		$res = false;
		$points = array();

		// get locations to display
		if($this->locationsFromRequest !== false) // get from request when form save fails or smth
			$res = self::getEntityListByListOfPrimary(self::LOCATION_ENTITY_NAME, $this->locationsFromRequest, $parameters, $linkFld);
		elseif(strlen($this->arParams['ENTITY_PRIMARY'])) // get from database, if entity exists
			$res = $class::getConnectedLocations($this->arParams['ENTITY_PRIMARY'], $parameters);

		if($res !== false)
		{
			$res->addReplacedAliases(array('LNAME' => 'NAME'));

			while($item = $res->fetch())
				$points[$item['ID']] = $item;
		}


		if(!empty($points))
		{
			// same algorythm repeated on client side - fetch PATH for only visible items
			if((count($points) - static::PAGE_SIZE) > static::HUGE_TAIL_LEN)
				$pointsToGetPath = array_slice($points, 0, static::PAGE_SIZE);
			else
				$pointsToGetPath = $points;

			$res = Location\LocationTable::getPathToMultipleNodes($pointsToGetPath, array(
				'select' => array(
					//'ID',
					//'CODE',
					'LNAME' => 'NAME.NAME'
				),
				'filter' => array(
					'NAME.LANGUAGE_ID' => LANGUAGE_ID
				)
			));

			while($item = $res->Fetch())
			{
				$item['ID'] = intval($item['ID']);

				foreach($item['PATH'] as &$node)
				{
					$node['NAME'] = $node['LNAME'];
					unset($node['LNAME']);
				}

				$points[$item['ID']]['PATH'] = $item['PATH'];
			}

			foreach($points as &$location)
			{
				unset($location['LEFT_MARGIN']); // system fields should not figure in $arResult
				unset($location['RIGHT_MARGIN']); // same
			}
			unset($location);
		}

		$this->dbResult['CONNECTIONS']['LOCATION'] = $points;

		if($this->useGroups)
		{
			$parameters = array('select' => array(
					'ID',
					'CODE',
					'LNAME' => 'NAME.NAME'
				),
				'filter' => array(
					'NAME.LANGUAGE_ID' => LANGUAGE_ID
				)
			);

			$res = false;
			$points = array();

			if($this->groupsFromRequest !== false)
				$res = self::getEntityListByListOfPrimary('Bitrix\Sale\Location\GroupTable', $this->groupsFromRequest, $parameters, $linkFld);
			elseif(strlen($this->arParams['ENTITY_PRIMARY']))
				$res = $class::getConnectedGroups($this->arParams['ENTITY_PRIMARY'], $parameters);

			if($res !== false)
			{
				$res->addReplacedAliases(array('LNAME' => 'NAME'));

				while($item = $res->fetch())
				{
					$item['ID'] = intval($item['ID']);
					$points[$item['ID']] = $item;
				}
			}

			$this->dbResult['CONNECTIONS']['GROUP'] = $points;
		}
	}

	// override for do-nothing
	protected function obtainDataLocation()
	{
	}

	protected function checkParameters()
	{
		$result = parent::checkParameters();

		if(!$this->arParams['LINK_ENTITY_NAME'])
		{
			$this->errors['FATAL'][] = Loc::getMessage('SALE_SLSS_ENTITY_PRIMARY_NOT_SET');
			return false;
		}
		else
		{
			$this->entityClass = $this->arParams['LINK_ENTITY_NAME'].'Table';
			if(!class_exists($this->entityClass, true))
			{
				$this->errors['FATAL'][] = Loc::getMessage('SALE_SLSS_LINK_ENTITY_CLASS_UNKNOWN');
				return false;
			}

			$class = $this->entityClass;

			$isInstace = false;
			try
			{
				$a = new $class();
				$isInstace = ($a instanceof \Bitrix\Sale\Location\Connector);
			}
			catch(\Exception $e)
			{
			}

			if(!$isInstace)
			{
				$this->errors['FATAL'][] = Loc::getMessage('SALE_SLSS_WRONG_LINK_CLASS');
				return false;
			}

			$this->useGroups = $class::getUseGroups();
			$this->useCodes = $class::getUseCodes();
		}

		// selected in request
		if(is_array($this->arParams['SELECTED_IN_REQUEST']['L']))
			$this->locationsFromRequest = $this->normalizeList($this->arParams['SELECTED_IN_REQUEST']['L'], !$this->useCodes);

		if(is_array($this->arParams['SELECTED_IN_REQUEST']['G']))
			$this->groupsFromRequest = $this->normalizeList($this->arParams['SELECTED_IN_REQUEST']['G'], !$this->useCodes);

		return $result;
	}

	protected function getCacheDependences()
	{
		return array_merge(parent::getCacheDependences(), array(self::getStrForVariable($this->useGroups)));
	}

	/**
	 * Move data read from database to a specially formatted $arResult
	 * @return void
	 */
	protected function formatResult()
	{
		$this->arResult =& $this->dbResult;
		$this->arResult['ERRORS'] =& $this->errors;

		$this->arResult['RANDOM_TAG'] = rand(999, 99999).rand(999, 99999).rand(999, 99999);

		$this->arResult['USE_GROUPS'] = $this->useGroups;
		$this->arResult['USE_CODES'] = $this->useCodes;
	}

	protected static function processSearchGetFilter()
	{
		if(intval($_REQUEST['FILTER']['GROUP_ID']))
		{
			return array(
				'NAME.LANGUAGE_ID' => strlen($_REQUEST['BEHAVIOUR']['LANGUAGE_ID']) ? $_REQUEST['BEHAVIOUR']['LANGUAGE_ID'] : LANGUAGE_ID
			);
		}

		return parent::processSearchGetFilter();
	}

	protected static function processSearchGetSelect()
	{
		$select = parent::processSearchGetSelect();

		if(intval($_REQUEST['FILTER']['GROUP_ID'])){
			$select = array_merge($select , array(
				'ID', 'CODE', 'LEFT_MARGIN', 'RIGHT_MARGIN', 'TYPE_ID'
			));
			$select['LNAME'] = 'NAME.NAME';
		}

		return $select;
	}

	protected static function processSearchGetList($parameters)
	{
		if($gId = intval($_REQUEST['FILTER']['GROUP_ID']))
		{
			$res = Location\GroupLocationTable::getConnectedLocations($gId, $parameters);
			$res->addReplacedAliases(array('LNAME' => 'NAME'));
			$result = array();
			while($item = $res->fetch())
			{
				$result[$item['ID']] = $item;
			}
		}
		else
			$result = parent::processSearchGetList($parameters);

		return $result;
	}

	protected static function processSearchGetAdditional($result)
	{
		$result = parent::processSearchGetAdditional($result);

		// get common path of parent
		if(isset($_REQUEST['FILTER']['PARENT_ID']))
		{
			$path = array();
			$result['ETC']['PATH_NAMES'] = array();

			if($pId = intval($_REQUEST['FILTER']['PARENT_ID']))
			{
				$res = Location\LocationTable::getPathToNode($pId, array(
					'select' => array(
						'ID',
						'CODE',
						'TYPE_ID',
						'LNAME' => 'NAME.NAME'
					),
					'filter' => array(
						'NAME.LANGUAGE_ID' => static::processSearchRequestGetLang()
					)
				));
				$res->addReplacedAliases(array('LNAME' => 'NAME'));

				$node = array();
				while($item = $res->fetch())
				{
					$path[] = intval($item['ID']);
					$result['ETC']['PATH_NAMES'][$item['ID']] = $item['NAME'];

					$node = $item;
				}

				$node['PATH'] = array_reverse($path);
				$result['ETC']['PARENT_ITEM'] = $node;
			}
		}

		return $result;
	}

	protected static function normalizeList($list, $expectNumeric = true)
	{
		$list = array_unique(array_values($list));
		foreach($list as $i => $id)
		{
			if($expectNumeric)
			{
				if(intval($id) != $id)
					unset($list[$i]);

				$list[$i] = intval($id);
				if(!$list[$i])
					unset($list[$i]);
			}
			else
			{
				if(!strlen($list[$i]))
					unset($list[$i]);
			}
		}

		return $list;
	}

	public static function processGetPathRequest()
	{
		$idList = $_REQUEST['ITEMS'];

		if(!is_array($idList) || empty($idList))
			throw new Main\SystemException('Empty array passed'); // todo: assert here later

		$result = array();

		$idList = array_unique($idList);
		$items = array();

		$res = self::getEntityListByListOfPrimary(self::LOCATION_ENTITY_NAME, $idList, array('select' => array('ID', 'LEFT_MARGIN', 'RIGHT_MARGIN')), 'ID');
		while($item = $res->fetch())
			$items[] = $item;

		if(empty($items))
			return $result;

		$result = static::getPathToNodes($items);

		return $result;
	}
}