<? namespace Wizard\Additional;

//use Wizard\Additional\Errors;
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Entity\Query;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

Loader::includeModule('highloadblock');

/**
 * Class HLAdditional
 * @package Wizard\Additional
 */
Class HLAdditional
{
	protected static $NOT_HL_ID;
	protected static $EMPTY_DATA;
	protected static $EMPTY_ELEMENT_ID;
	protected static $instance = null;
	/**
	 * @var bool|\Bitrix\Main\Entity\DataManager
	 */
	protected $entity_data_class;
	/**
	 * @var bool|\Bitrix\Main\Entity\Base
	 */
	protected $entity;
	protected $hlblock;
	/**
	 * @var bool|\Bitrix\Main\Entity\Query
	 */
	protected $main_query;
	protected $obCache;
	protected $HL_ID;
	protected $obErrors;

	/**
	 *
	 */
	public function __construct()
	{
		static::$NOT_HL_ID = Loc::getMessage("NOT_INSTALL_HL_ID");
		static::$EMPTY_DATA = Loc::getMessage("EMPTY_DATA_HL");
		static::$EMPTY_ELEMENT_ID = Loc::getMessage("EMPTY_ELEMENT_ID_HL");
		global $APPLICATION, $CACHE_MANAGER;
		$this->Application = $APPLICATION;
		$this->CacheManager = $CACHE_MANAGER;
		$this->obCache = new \CPHPCache();
		$this->obErrors = Errors::getInstance();
	}

	/**
	 * @return \Wizard\Additional\HLAdditional
	 */
	public static function getInstance()
	{
		if (!isset(static::$instance))
			static::$instance = new static();

		return static::$instance;
	}

	/**
	 * @param array $params
	 * @return array|bool
	 */
	public function getList($params = [])
	{
		$this->obErrors->clearErrors();

		if ($this->haveValue($params['HL_ID'], 'not') || intval($params['HL_ID']) <= 0) {
			if ($this->haveValue($this->HL_ID, 'not'))
				$this->obErrors->setError(static::$NOT_HL_ID);
		} else {
			$this->HL_ID = $params['HL_ID'];
		}

		if (!$this->obErrors->setErrors())
			return false;

		if ($this->haveValue($params['bReturnObject'], 'not') || $params['bReturnObject'] !== true) {
			$params['bReturnObject'] = false;
		}

		$arCacheParams = [
			"HL_ID"         => $this->HL_ID,
			'bReturnObject' => $params['bReturnObject']
		];

		$arUses = [
			'order'      => false,
			'filter'     => false,
			'select'     => false,
			'groupBy'    => false,
			'navigation' => false
		];

		if ($this->haveValue($params['order']) && is_array($params['order'])) {
			$arCacheParams['ORDER'] = $params['order'];
			$arUses['order'] = true;
		}

		if ($this->haveValue($params['select']) && is_array($params['select'])) {
			$arCacheParams['SELECT'] = $params['select'];
			$arUses['select'] = true;
		}

		if ($this->haveValue($params['filter']) && is_array($params['filter'])) {
			$arCacheParams['FILTER'] = $params['filter'];
			$arUses['filter'] = true;
		}

		if ($this->haveValue($params['groupBy']) && is_array($params['groupBy'])) {
			$arCacheParams['GROUP_BY'] = $params['groupBy'];
			$arUses['groupBy'] = true;
		}

		if ($this->haveValue($params['navigation']) && is_array($params['navigation'])) {
			$arCacheParams['NAVIGATION'] = $params['navigation'];
			$arUses['navigation'] = true;
		}

		//$cacheTime = 36000;
		//$cacheId = serialize($arCacheParams);
		$cachePath = '/hlBlock/class/hlAdditional/data';

		//if ($this->obCache->InitCache($cacheTime, $cacheId, $cachePath)) {
		//	$arResult = $this->obCache->GetVars();
		//} else {
			$this->CacheManager->StartTagCache($cachePath);
			$this->CacheManager->RegisterTag('hl_block_' . $this->HL_ID);

			$this->initHLEntity($this->HL_ID, false, true);

			if ($arUses['select']) {
				$this->main_query->setSelect($params['select']);
			} else {
				$this->main_query->setSelect(['*']);
			}

			if ($arUses['order'])
				$this->main_query->setOrder($params['order']);

			if ($arUses['filter'])
				$this->main_query->setFilter($params['filter']);

			if ($arUses['groupBy'])
				$this->main_query->setGroup($params['groupBy']);

			if ($arUses['navigation']) {
				if ($this->haveValue($params['navigation']['nPageTop'])
					&& intval($params['navigation']['nPageTop']) > 0
				) {
					$this->main_query->setLimit($params['navigation']['nPageTop']);
				} elseif ($this->haveValue($params['navigation']['nPageSize'])
						  && $this->haveValue(
						$params['navigation']['iNumPage']
					)
				) {
					$this->main_query->setLimit($params['navigation']['nPageSize']);
					$this->main_query->setOffset(
						($params['navigation']['iNumPage'] - 1) * $params['navigation']['nPageSize']
					);
				}
			}

			$result = $this->main_query->exec();

			$arResult = false;
			while ($row = $result->fetch()) {
				$arResult[] = $row;
				/*$this->CacheManager->RegisterTag('hl_block_' . $this->HL_ID . '_item_' . $row['ID']);*/
			}

			if ($params['bReturnObject']) {
				$arTmp = $arResult;
				$arResult = [];
				$arResult['ITEMS'] = $arTmp;
				$arResult['res'] = &$result;
			}

			$this->CacheManager->EndTagCache();

			if ($this->obCache->StartDataCache())
				$this->obCache->EndDataCache($arResult);
		//}

		return $arResult;
	}

	/**
	 * @param $value
	 * @param bool|false $not
	 * @return bool
	 */
	public function haveValue($value, $not = false)
	{
		if ($not === false) {
			if (isset($value) && !empty($value)) {
				return true;
			}

			return false;
		} else {
			if (!isset($value) || empty($value)) {
				return true;
			}

			return false;
		}
	}

	/**
	 * @param $HL_ID
	 * @param bool|false $bInitDataClass
	 * @param bool|false $bInitMainQuery
	 * @return bool
	 * @throws \Bitrix\Main\SystemException
	 */
	protected function initHLEntity($HL_ID, $bInitDataClass = false, $bInitMainQuery = false)
	{
		$this->obErrors->clearErrors();

		if ($this->haveValue($HL_ID, 'not') || intval($HL_ID) <= 0)
			$this->obErrors->setError(static::$NOT_HL_ID);

		if (!$this->obErrors->setErrors())
			return false;

		if ($this->haveValue($this->hlblock, 'not') || $this->hlblock['ID'] != $HL_ID) {
			$this->hlblock = HighloadBlockTable::getById($HL_ID)->fetch();
			$this->entity = HighloadBlockTable::compileEntity($this->hlblock);

			if ($bInitMainQuery)
				$this->main_query = new Query($this->entity);

			if ($bInitDataClass)
				$this->entity_data_class = $this->entity->getDataClass();
		} else {
			//if ($bInitMainQuery && $this->haveValue($this->main_query, 'not'))
			if ($bInitMainQuery)
				$this->main_query = new Query($this->entity);

			//if ($bInitDataClass && $this->haveValue($this->entity_data_class, 'not'))
			if ($bInitDataClass)
				$this->entity_data_class = $this->entity->getDataClass();
		}

		return true;
	}

	/**
	 * @param array $params
	 * @return array|bool
	 */
	public function getListCount($params = [])
	{
		$this->obErrors->clearErrors();

		if ($this->haveValue($params['HL_ID'], 'not') || intval($params['HL_ID']) <= 0) {
			if ($this->haveValue($this->HL_ID, 'not'))
				$this->obErrors->setError(static::$NOT_HL_ID);
		} else {
			$this->HL_ID = $params['HL_ID'];
		}

		if (!$this->obErrors->setErrors())
			return false;

		$arCacheParams = [
			"HL_ID" => $this->HL_ID
		];

		$arUses = ['filter' => false];
		if ($this->haveValue($params['filter']) && is_array($params['filter'])) {
			$arCacheParams['FILTER'] = $params['filter'];
			$arUses['filter'] = true;
		}

		//$cacheTime = 36000;
		//$cacheId = serialize($arCacheParams);
		$cachePath = '/hlBlock/class/hlAdditional/dataCount';

		//if ($this->obCache->InitCache($cacheTime, $cacheId, $cachePath)) {
		//	$count = $this->obCache->GetVars();
		//} else {
			$this->CacheManager->StartTagCache($cachePath);
			$this->CacheManager->RegisterTag('hl_block_' . $this->HL_ID);

			$this->initHLEntity($this->HL_ID, false, true);

			if ($arUses['filter']) {
				$this->main_query->setFilter($params['filter']);
			}

			$result = $this->main_query->exec();
			$count = $result->getSelectedRowsCount();

			$this->CacheManager->EndTagCache();

			if ($this->obCache->StartDataCache())
				$this->obCache->EndDataCache($count);
		//}

		return $count;
	}

	/**
	 * @param array $params
	 * @return bool
	 */
	public function add($params = [])
	{
		$this->obErrors->clearErrors();

		if ($this->haveValue($params['HL_ID'], 'not') || intval($params['HL_ID']) <= 0) {
			if ($this->haveValue($this->HL_ID, 'not'))
				$this->obErrors->setError(static::$NOT_HL_ID);
		} else {
			$this->HL_ID = $params['HL_ID'];
		}

		if ($this->haveValue($params['arData'], 'not'))
			$this->obErrors->setError(static::$EMPTY_DATA);

		if (!$this->obErrors->setErrors())
			return false;

		$this->initHLEntity($this->HL_ID, true);

		$edc = $this->entity_data_class;
		//$result = $this->entity_data_class->add($params['arData']);
		$result = $edc::add($params['arData']);

		return $result->getId();
	}

	/**
	 * @param array $params
	 * @return bool
	 */
	public function update($params = [])
	{
		$this->obErrors->clearErrors();

		if ($this->haveValue($params['HL_ID'], 'not') || intval($params['HL_ID']) <= 0) {
			if ($this->haveValue($this->HL_ID, 'not'))
				$this->obErrors->setError(static::$NOT_HL_ID);
		} else {
			$this->HL_ID = $params['HL_ID'];
		}

		if ($this->haveValue($params['arData'], 'not'))
			$this->obErrors->setError(static::$EMPTY_DATA);

		if ($this->haveValue($params['ID'], 'not'))
			$this->obErrors->setError(static::$EMPTY_ELEMENT_ID);

		if (!$this->obErrors->setErrors())
			return false;

		$this->initHLEntity($this->HL_ID, true);

		$edc = $this->entity_data_class;
		//$result = $this->entity_data_class->update($params['ID'], $params['arData']);
		$result = $edc::update($params['ID'], $params['arData']);

		return $result->getId();
	}

	/**
	 * @param array $params
	 * @return bool
	 */
	public function delete($params = [])
	{
		$this->obErrors->clearErrors();

		if ($this->haveValue($params['HL_ID'], 'not') || intval($params['HL_ID']) <= 0) {
			if ($this->haveValue($this->HL_ID, 'not'))
				$this->obErrors->setError(static::$NOT_HL_ID);
		} else {
			$this->HL_ID = $params['HL_ID'];
		}

		if ($this->haveValue($params['ID'], 'not'))
			$this->obErrors->setError(static::$EMPTY_ELEMENT_ID);

		if (!$this->obErrors->setErrors())
			return false;

		$this->initHLEntity($this->HL_ID, true);

		$edc = $this->entity_data_class;
		//$result = $this->entity_data_class->delete($params['ID']);
		$result = $edc::delete($params['ID']);

		return $result->isSuccess();
	}

	/**
	 * @param array $select
	 * @return array|bool
	 * @throws \Bitrix\Main\ArgumentException
	 */
	public function getHLList($select = ['ID', 'NAME'])
	{
		$HLList = false;

		if (!is_array($select) || empty($select))
			$select = ['ID', 'NAME'];

		$res = HighloadBlockTable::getList(['select' => $select]);
		while ($hlItem = $res->fetch()) {
			$HLList[] = $hlItem;
		}

		return $HLList;
	}

	/**
	 * @param $params ['HL_ID']
	 * @return bool|array
	 */
	public function getHLFields($params = [])
	{
		$this->obErrors->clearErrors();

		if ($this->haveValue($params['HL_ID'], 'not') || intval($params['HL_ID']) <= 0) {
			if ($this->haveValue($this->HL_ID, 'not'))
				$this->obErrors->setError(static::$NOT_HL_ID);
		} else {
			$this->HL_ID = $params['HL_ID'];
		}

		if (!$this->obErrors->setErrors())
			return false;

		$arFields = [];

		//$arCacheParams = [
		//	"HL_ID" => $this->HL_ID
		//];

		//$cacheTime = 36000;
		//$cacheId = serialize($arCacheParams);
		$cachePath = '/hlBlock/class/hlAdditional/hlFields';

		//if ($this->obCache->InitCache($cacheTime, $cacheId, $cachePath)) {
		//	$arFields = $this->obCache->GetVars();
		//} else {
			$this->CacheManager->StartTagCache($cachePath);
			$this->CacheManager->RegisterTag('hl_block_' . $this->HL_ID . '_fields');

			$res = \CUserTypeEntity::GetList([], ['ENTITY_ID' => 'HLBLOCK_' . $this->HL_ID, 'LANG' => 'ru']);

			while ($arField = $res->Fetch()) {
				$arFields[] = [
					'CODE'    => $arField['FIELD_NAME'],
					'NAME'    => $arField['LIST_COLUMN_LABEL'],
					'FULL_EL' => $arField
				];
				/*$this->CacheManager->RegisterTag('hl_block_' . $this->HL_ID . '_fields_field_'.$arField['ID']);*/
			}

			$this->CacheManager->EndTagCache();

			if ($this->obCache->StartDataCache())
				$this->obCache->EndDataCache($arFields);
		//}

		return $arFields;
	}

	/**
	 * @param $HL_ID
	 */
	public function setHLID($HL_ID)
	{
		$this->HL_ID = $HL_ID;
		$this->initHLEntity($HL_ID);
	}
}