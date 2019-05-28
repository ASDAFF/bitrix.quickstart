<?
/**
 * This class is for internal use only, not a part of public API.
 * It can be changed at any time without notification.
 *
 * @access private
 */

namespace Bitrix\Sale\Location\Admin;

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;

abstract class NameHelper extends Helper
{
	#####################################
	#### Entity settings
	#####################################

	public static function getEntityRoadCode()
	{
		return 'name';
	}

	public static function getColumns($page)
	{
		return array_merge(parent::getColumns($page), self::getMap($page));
	}

	// get part of the whole field map for responsibility zone of the current entity
	// call this only with self::
	public static function getMap($page)
	{
		static $flds;

		if($flds == null)
		{
			$preFlds = static::readMap(self::getEntityRoadCode(), $page);

			$flds = array();
			$languages = self::getLanguageList();

			foreach($languages as $lang)
			{
				foreach($preFlds as $code => $column)
				{
					$tmpCol = $column;

					$tmpCol['title'] = $tmpCol['title'].'&nbsp;('.$lang.')';
					$flds[$code.'_'.ToUpper($lang)] = $tmpCol;
				}
			}
		}

		return $flds;
	}

	#####################################
	#### CRUD wrappers
	#####################################

	// generalized filter to orm filter proxy
	public static function getParametersForList($proxed)
	{
		$parameters = parent::getParametersForList($proxed);

		$fldSubMap = static::readMap(self::getEntityRoadCode(), 'list');
		$roadMap = static::getEntityRoadMap();
		$road = $roadMap[self::getEntityRoadCode()]['name'];
		$class = $road.'Table';
		$languages = self::getLanguageList();

		// select

		foreach($languages as $lang)
		{
			$lang = ToUpper($lang);

			$parameters['runtime']['NAME__'.$lang] = array(
				'data_type' => $road,
				'reference' => array(
					'=this.ID' => 'ref.'.$class::getReferenceFieldName(),
					'=ref.'. $class::getLanguageFieldName() => array('?', ToLower($lang)) // oracle is case-sensitive
				),
				'join_type' => 'left'
			);

			if(!isset($parameters['select']))
				$parameters['select'] = array();
			foreach($fldSubMap as $code => $fld)
				$parameters['select'][$code.'_'.$lang] = 'NAME__'.$lang.'.'.$code;
		}

		// filter
		if(is_array($proxed['FILTER']) && !empty($proxed['FILTER']))
		{
			foreach($languages as $lang)
			{
				$lang = ToUpper($lang);

				foreach($fldSubMap as $code => $fld)
				{
					$key = $code.'_'.$lang;

					if(isset($proxed['FILTER'][$key]))
						$parameters['filter'][static::getFilterModifier($fld['data_type']).'NAME__'.$lang.'.'.$code] = $proxed['FILTER'][$key];
				}
			}
		}

		return $parameters;
	}

	##############################################
	##############################################
	##############################################

	public static function validateUpdateRequest(&$data)
	{
		$errors = parent::validateUpdateRequest($data);

		// formally check language ids in NAME parameter
		if(is_array($data['NAME']) && !empty($data['NAME']))
		{
			$languages = self::getLanguageList();

			foreach($data['NAME'] as $lid => $name)
			{
				if(!isset($languages[$lid]))
				{
					$errors[] = Loc::getMessage('SALE_LOCATION_ADMIN_NAME_HELPER_ENTITY_UNKNOWN_LANGUAGE_ID_ERROR');
					break;
				}
			}
		}

		return $errors;
	}

	public static function proxyUpdateRequest($data)
	{
		$names = static::extractNames($data);
		$data = parent::proxyUpdateRequest($data);

		if(!empty($names))
			$data['NAME'] = $names;

		return $data;
	}

	// an adapter from CAdminList to ORM getList() logic
	public static function proxyListRequest($page)
	{
		$parameters = parent::proxyListRequest($page);

		$fldSubMap = static::readMap(self::getEntityRoadCode(), 'list');
		$roadMap = static::getEntityRoadMap();
		$road = $roadMap[self::getEntityRoadCode()]['name'];
		$class = $road.'Table';
		$languages = self::getLanguageList();

		// select

		foreach($languages as $lang)
		{
			$lang = ToUpper($lang);

			$parameters['runtime']['NAME__'.$lang] = array(
				'data_type' => $road,
				'reference' => array(
					'=this.ID' => 'ref.'.$class::getReferenceFieldName(),
					'=ref.'. $class::getLanguageFieldName() => array('?', ToLower($lang)) // oracle is case-sensitive
				),
				'join_type' => 'left'
			);

			if(!isset($parameters['select']))
				$parameters['select'] = array();
			foreach($fldSubMap as $code => $fld)
				$parameters['select'][$code.'_'.$lang] = 'NAME__'.$lang.'.'.$code;
		}

		// filter
		if(self::checkUseFilter())
		{
			foreach($languages as $lang)
			{
				$lang = ToUpper($lang);

				foreach($fldSubMap as $code => $fld)
				{
					$key = 'find_'.$code.'_'.$lang;

					if(strlen($GLOBALS[$key]))
						$parameters['filter'][static::getFilterModifier($fld['data_type']).'NAME__'.$lang.'.'.$code] = $GLOBALS[$key];
				}
			}
		}

		return $parameters;
	}

	public static function getNameToDisplay($id)
	{
		if(!($id = intval($id)))
			return '';

		$class = static::getEntityClass('main');
		$nameClass = static::getEntityClass(self::getEntityRoadCode());
		$item = $class::getList(array(
			'filter' => array('=ID' => $id, 'NAME.'.$nameClass::getLanguageFieldName() => LANGUAGE_ID),
			'select' => array('LNAME' => 'NAME.NAME')
		))->fetch();

		return $item['LNAME'];
	}

	#####################################
	#### Entity-specific
	#####################################

	public static function getLanguageList()
	{
		static $languages;

		if($languages == null)
		{
			$by = 'sort';
			$order = 'asc';

			$lang = new \CLanguage();
			$res = $lang->GetList($by, $order, array());
			$languages = array();
			while($item = $res->Fetch())
				$languages[$item['LANGUAGE_ID']] = $item['LANGUAGE_ID'];
		}

		return $languages;
	}

	// extracts NAME data from known data, rather than do a separate query for it
	public static function extractNames(&$data)
	{
		$fldSubMap = static::readMap(self::getEntityRoadCode());
		$languages = self::getLanguageList();

		$names = array();
		foreach($languages as $lang)
		{
			foreach($fldSubMap as $code => $fld)
			{
				$langU = ToUpper($lang);

				$key = $code.'_'.$langU;
				if(isset($data[$key]))
					$names[$lang][$code] = $data[$key];

				unset($data[$key]);
			}
		}

		return $names;
	}

	public static function checkIsNameField($code)
	{
		$map = self::getMap('detail');
		return isset($map[$code]);
	}

	public static function getNameMap()
	{
		return static::readMap('name', 'detail');
	}
}
