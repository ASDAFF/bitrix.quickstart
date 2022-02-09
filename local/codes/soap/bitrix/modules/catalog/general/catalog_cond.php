<?
IncludeModuleLangFile(__FILE__);

define('BT_COND_LOGIC_EQ', 0);						// = (equal)
define('BT_COND_LOGIC_NOT_EQ', 1);					// != (not equal)
define('BT_COND_LOGIC_GR', 2);						// > (great)
define('BT_COND_LOGIC_LS', 3);						// < (less)
define('BT_COND_LOGIC_EGR', 4);						// => (great or equal)
define('BT_COND_LOGIC_ELS', 5);						// =< (less or equal)
define('BT_COND_LOGIC_CONT', 6);					// contain
define('BT_COND_LOGIC_NOT_CONT', 7);				// not contain

define('BT_COND_MODE_DEFAULT', 0);					// full mode
define('BT_COND_MODE_PARSE', 1);					// parsing mode
define('BT_COND_MODE_GENERATE', 2);					// generate mode
define('BT_COND_MODE_SQL', 3);						// generate getlist mode

define('BT_COND_BUILD_CATALOG', 0);					// catalog conditions
define('BT_COND_BUILD_SALE', 1);					// sale conditions

class CCatalogCondCtrl
{
	public function __construct()
	{

	}

	public function __destruct()
	{

	}

	public function GetControlDescr()
	{
		return array();
	}

	function GetControlShow($arParams)
	{

	}

	function Parse($arOneCondition)
	{

	}

	public function OnBuildConditionAtomList()
	{

	}

	function GetConditionShow($arParams)
	{

	}

	function GetAtoms()
	{
		return array(
		);
	}

	function IsGroup($strControlID = false)
	{
		return 'N';
	}

	function GetLogic($arOperators = false)
	{
		$arOperatorsList = array(
			BT_COND_LOGIC_EQ => array(
				'ID' => BT_COND_LOGIC_EQ,
				'OP' => array(
					'Y' => 'in_array(#VALUE#, #FIELD#)',
					'N' => '#FIELD# == #VALUE#',
				),
				'VALUE' => 'Equal',
				'LABEL' => GetMessage('BT_COND_LOGIC_EQ_LABEL')
			),
			BT_COND_LOGIC_NOT_EQ => array(
				'ID' => BT_COND_LOGIC_NOT_EQ,
				'OP' => array(
					'Y' => '!in_array(#VALUE#, #FIELD#)',
					'N' => '#FIELD# != #VALUE#',
				),
				'VALUE' => 'Not',
				'LABEL' => GetMessage('BT_COND_LOGIC_NOT_EQ_LABEL')
			),
			BT_COND_LOGIC_GR => array(
				'ID' => BT_COND_LOGIC_GR,
				'OP' => array(
					'N' => '#FIELD# > #VALUE#',
				),
				'VALUE' => 'Great',
				'LABEL' => GetMessage('BT_COND_LOGIC_GR_LABEL')
			),
			BT_COND_LOGIC_LS => array(
				'ID' => BT_COND_LOGIC_LS,
				'TYPE' => BT_COND_LOGIC_TYPE_OP,
				'OP' => array(
					'N' => '#FIELD# < #VALUE#',
				),
				'VALUE' => 'Less',
				'LABEL' => GetMessage('BT_COND_LOGIC_LS_LABEL')
			),
			BT_COND_LOGIC_EGR => array(
				'ID' => BT_COND_LOGIC_EGR,
				'OP' => array(
					'N' => '#FIELD# >= #VALUE#',
				),
				'VALUE' => 'EqGr',
				'LABEL' => GetMessage('BT_COND_LOGIC_EGR_LABEL')
			),
			BT_COND_LOGIC_ELS => array(
				'ID' => BT_COND_LOGIC_ELS,
				'OP' => array(
					'N' => '#FIELD# <= #VALUE#',
				),
				'VALUE' => 'EqLs',
				'LABEL' => GetMessage('BT_COND_LOGIC_ELS_LABEL')
			),
			BT_COND_LOGIC_CONT => array(
				'ID' => BT_COND_LOGIC_CONT,
				'OP' => array(
					'N' => 'false !== strpos(#FIELD#, #VALUE#)'
				),
				'VALUE' => 'Contain',
				'LABEL' => GetMessage('BT_COND_LOGIC_CONT_LABEL')
			),
			BT_COND_LOGIC_NOT_CONT => array(
				'ID' => BT_COND_LOGIC_NOT_CONT,
				'OP' => array(
					'N' => 'false === strpos(#FIELD#, #VALUE#)'
				),
				'VALUE' => 'NotCont',
				'LABEL' => GetMessage('BT_COND_LOGIC_NOT_CONT_LABEL')
			),
		);

		$boolSearch = false;
		$arSearch = array();
		if (is_array($arOperators) && !empty($arOperators))
		{
			foreach ($arOperators as &$intOneOp)
			{
				if (array_key_exists($intOneOp, $arOperatorsList))
				{
					$boolSearch = true;
					$arSearch[$intOneOp] = $arOperatorsList[$intOneOp];
				}
			}
			if (isset($intOneOp))
				unset($intOneOp);
		}
		return ($boolSearch ? $arSearch : $arOperatorsList);
	}

	function GetLogicAtom($arLogic)
	{
		if (is_array($arLogic) && !empty($arLogic))
		{
			$arValues = array();
			foreach ($arLogic as &$arOneLogic)
			{
				$arValues[$arOneLogic['VALUE']] = $arOneLogic['LABEL'];
			}
			if (isset($arOneLogic))
				unset($arOneLogic);
			$arResult = array(
				'id' => 'logic',
				'name' =>  'logic',
				'type' => 'select',
				'values' => $arValues,
				'defaultText' => current($arValues),
				'defaultValue' => key($arValues),
			);
			return $arResult;
		}
		else
		{
			return false;
		}
	}

	function GetValueAtom($arValue)
	{
		if (!is_array($arValue) || empty($arValue) || !isset($arValue['type']))
		{
			$arResult = array(
				'type' => 'input',
			);
		}
		else
		{
			$arResult = $arValue;
		}
		$arResult['id'] = 'value';
		$arResult['name'] = 'value';

		return $arResult;
	}

	function CheckLogic($strValue, $arLogic, $boolShow = false)
	{
		$boolShow = (true === $boolShow);
		if (!is_array($arLogic) || empty($arLogic))
			return false;
		$strResult = '';
		foreach ($arLogic as &$arOneLogic)
		{
			if ($strValue == $arOneLogic['VALUE'])
			{
				$strResult = $arOneLogic['VALUE'];
				break;
			}
		}
		if (isset($arOneLogic))
			unset($arOneLogic);
		if ('' == $strResult)
		{
			if ($boolShow)
			{
				$arOneLogic = current($arLogic);
				$strResult = $arOneLogic['VALUE'];
			}
		}
		return ('' == $strResult ? false : $strResult);
	}

	function SearchLogic($strValue, $arLogic)
	{
		$mxResult = false;
		if (!is_array($arLogic) || empty($arLogic))
			return $mxResult;
		foreach ($arLogic as &$arOneLogic)
		{
			if ($strValue == $arOneLogic['VALUE'])
			{
				$mxResult = $arOneLogic;
				break;
			}
		}
		if (isset($arOneLogic))
			unset($arOneLogic);
		return $mxResult;
	}

	function Check($arOneCondition, $arParams, $arControl, $boolShow)
	{
		global $DB;

		$boolShow = (true === $boolShow);
		$arResult = array();
		$boolError = false;
		$boolFatalError = false;
		$arMsg = array();

		$arValues = array(
			'logic' => '',
			'value' => ''
		);
		$arLabels = array();

		if ($boolShow)
		{
			if (!isset($arOneCondition['logic']))
			{
				$arOneCondition['logic'] = '';
				$boolError = true;
			}
			if (!isset($arOneCondition['value']))
			{
				$arOneCondition['value'] = '';
				$boolError = true;
			}
			$strLogic = self::CheckLogic($arOneCondition['logic'], $arControl['LOGIC'], $boolShow);
			if (false === $strLogic)
			{
				$boolError = true;
				$boolFatalError = true;
				$arMsg[] = GetMessage('BT_MOD_COND_ERR_CHECK_DATA_LOGIC_ABSENT');
			}
			else
			{
				$arValues['logic'] = $strLogic;
			}

			$boolValueError = false;
			switch ($arControl['FIELD_TYPE'])
			{
				case 'int':
					if (is_array($arOneCondition['value']))
						$boolValueError = !array_walk($arOneCondition['value'], create_function("&\$item", "\$item=intval(\$item);"));
					else
						$arOneCondition['value'] = intval($arOneCondition['value']);
					break;
				case 'double':
					if (is_array($arOneCondition['value']))
						$boolValueError = !array_walk($arOneCondition['value'], create_function("&\$item", "\$item=doubleval(\$item);"));
					else
						$arOneCondition['value'] = doubleval($arOneCondition['value']);
					break;
				case 'char':
					if (is_array($arOneCondition['value']))
						$boolValueError = !array_walk($arOneCondition['value'], create_function("&\$item", "\$item=substr(\$item, 0, 1);"));
					else
						$arOneCondition['value'] = substr($arOneCondition['value'], 0 ,1);
					break;
				case 'string':
					$intMaxLen = intval(isset($arControl['FIELD_LENGTH']) && 0 < intval($arControl['FIELD_LENGTH']) ? $arControl['FIELD_LENGTH'] : 255);
					if (is_array($arOneCondition['value']))
						$boolValueError = !array_walk($arOneCondition['value'], create_function("&\$item", "\$item=substr(\$item, 0, ".$intMaxLen.");"));
					else
						$arOneCondition['value'] = substr($arOneCondition['value'], 0, $intMaxLen);
					break;
				case 'text':
					break;
				case 'date':
					if (is_array($arOneCondition['value']))
						foreach ($arOneCondition['value'] as $strValue)
						{
							if (!$DB->IsDate($strValue, false, false, 'SHORT'))
							{
								$boolValueError = true;
							}
						}
					else
						$boolValueError = !$DB->IsDate($arOneCondition['value'], false, false, 'SHORT');
					break;
				case 'datetime':
					if (is_array($arOneCondition['value']))
						foreach ($arOneCondition['value'] as $strValue)
						{
							if (!$DB->IsDate($strValue, false, false, 'FULL'))
							{
								$boolValueError = true;
							}
						}
					else
						$boolValueError = !$DB->IsDate($arOneCondition['value'], false, false, 'FULL');
					break;
				default:
					$boolValueError = true;
					break;
			}
			if (is_array($arOneCondition['value']))
			{
				if (!$boolValueError)
					$arOneCondition['value'] = array_values(array_unique($arOneCondition['value']));
			}

			if (!$boolValueError)
			{
				if (isset($arControl['PHP_VALUE']) && is_array($arControl['PHP_VALUE']) && isset($arControl['PHP_VALUE']['VALIDATE']) && !empty($arControl['PHP_VALUE']['VALIDATE']))
				{
					$arValidate = self::Validate($arOneCondition, $arParams, $arControl, $boolShow);
					if (false === $arValidate)
					{
						$boolValueError = true;
					}
					else
					{
						if (isset($arValidate['err_cond']) && 'Y' == $arValidate['err_cond'])
						{
							$boolValueError = true;
							if (isset($arValidate['err_cond_mess']) && !empty($arValidate['err_cond_mess']))
								$arMsg = array_merge($arMsg, $arValidate['err_cond_mess']);
						}
						else
						{
							$arValues['value'] = $arValidate['values'];
							if (isset($arValidate['labels']))
								$arLabels['value'] = $arValidate['labels'];
						}
					}
				}
				else
				{
					$arValues['value'] = $arOneCondition['value'];
				}
			}

			if ($boolValueError)
				$boolError = $boolValueError;
		}
		else
		{
			if (!isset($arOneCondition['logic']) || !isset($arOneCondition['value']))
			{
				$boolError = true;
			}
			else
			{
				$strLogic = self::CheckLogic($arOneCondition['logic'], $arControl['LOGIC'], $boolShow);
				if (!$strLogic)
				{
					$boolError = true;
				}
				else
				{
					$arValues['logic'] = $arOneCondition['logic'];
				}
			}

			if (!$boolError)
			{
				switch ($arControl['FIELD_TYPE'])
				{
					case 'int':
						if (is_array($arOneCondition['value']))
							$boolError = !array_walk($arOneCondition['value'], create_function("&\$item", "\$item=intval(\$item);"));
						else
							$arOneCondition['value'] = intval($arOneCondition['value']);
						break;
					case 'double':
						if (is_array($arOneCondition['value']))
							$boolError = !array_walk($arOneCondition['value'], create_function("&\$item", "\$item=doubleval(\$item);"));
						else
							$arOneCondition['value'] = doubleval($arOneCondition['value']);
						break;
					case 'char':
						if (is_array($arOneCondition['value']))
							$boolError = !array_walk($arOneCondition['value'], create_function("&\$item", "\$item=substr(\$item, 0, 1);"));
						else
							$arOneCondition['value'] = substr($arOneCondition['value'], 0 ,1);
						break;
					case 'string':
						$intMaxLen = intval(isset($arControl['FIELD_LENGTH']) && 0 < intval($arControl['FIELD_LENGTH']) ? $arControl['FIELD_LENGTH'] : 255);
						if (is_array($arOneCondition['value']))
							$boolError = !array_walk($arOneCondition['value'], create_function("&\$item", "\$item=substr(\$item, 0, ".$intMaxLen.");"));
						else
							$arOneCondition['value'] = substr($arOneCondition['value'], 0, $intMaxLen);
						break;
					case 'text':
						break;
					case 'date':
						if (is_array($arOneCondition['value']))
							foreach ($arOneCondition['value'] as $strValue)
							{
								if (!$DB->IsDate($strValue, false, false, 'SHORT'))
								{
									$boolError = true;
									break;
								}
							}
						else
							$boolError = !$DB->IsDate($arOneCondition['value'], false, false, 'SHORT');
						break;
					case 'datetime':
						if (is_array($arOneCondition['value']))
							foreach ($arOneCondition['value'] as $strValue)
							{
								if (!$DB->IsDate($strValue, false, false, 'FULL'))
								{
									$boolError = true;
									break;
								}
							}
						else
							$boolError = !$DB->IsDate($arOneCondition['value'], false, false, 'FULL');
						break;
					default:
						$boolError = true;
						break;
				}
				if (is_array($arOneCondition['value']))
				{
					if (!$boolError)
						$arOneCondition['value'] = array_values(array_unique($arOneCondition['value']));
				}
			}

			if (!$boolError)
			{
				if (isset($arControl['PHP_VALUE']) && is_array($arControl['PHP_VALUE']) && isset($arControl['PHP_VALUE']['VALIDATE']) && !empty($arControl['PHP_VALUE']['VALIDATE']))
				{
					$arValidate = self::Validate($arOneCondition, $arParams, $arControl, $boolShow);
					if (false === $arValidate)
					{
						$boolError = true;
					}
					else
					{
						$arValues['value'] = $arValidate['values'];
						if (isset($arValidate['labels']))
							$arLabels['value'] = $arValidate['labels'];
					}
				}
				else
				{
					$arValues['value'] = $arOneCondition['value'];
				}
			}
		}

		if ($boolShow)
		{
			$arResult = array(
				'id' => $arParams['COND_NUM'],
				'controlId' => $arControl['ID'],
				'values' => $arValues,
			);
			if (!empty($arLabels))
				$arResult['labels'] = $arLabels;
			if ($boolError)
			{
				$arResult['err_cond'] = 'Y';
				if ($boolFatalError)
					$arResult['fatal_err_cond'] = 'Y';
				if (!empty($arMsg))
					$arResult['err_cond_mess'] = implode('. ', $arMsg);
			}

			return $arResult;
		}
		else
		{
			$arResult = $arValues;
			return (!$boolError ? $arResult : false);
		}
	}

	function Validate($arOneCondition, $arParams, $arControl, $boolShow)
	{
		$boolShow = (true === $boolShow);
		$boolError = false;
		$arMsg = array();

		$arResult = array(
			'values' => '',
		);

		if (!(isset($arControl['PHP_VALUE']) && is_array($arControl['PHP_VALUE']) && isset($arControl['PHP_VALUE']['VALIDATE']) && !empty($arControl['PHP_VALUE']['VALIDATE'])))
		{
			$boolError = true;
		}

		if (!$boolError)
		{
			if ($boolShow)
			{
				// validate for show
				switch($arControl['PHP_VALUE']['VALIDATE'])
				{
					case 'element':
						$rsItems = CIBlockElement::GetList(array(), array('ID' => $arOneCondition['value']), false, false, array('ID', 'NAME'));
						if (is_array($arOneCondition['value']))
						{
							$arCheckResult = array();
							while ($arItem = $rsItems->Fetch())
							{
								$arCheckResult[intval($arItem['ID'])] = $arItem['NAME'];
							}
							if (!empty($arCheckResult))
							{
								$arResult['values'] = array_keys($arCheckResult);
								$arResult['labels'] = array_values($arCheckResult);
							}
							else
							{
								$boolError = true;
								$arMsg[] = GetMessage('BT_MOD_COND_ERR_CHECK_DATA_ELEMENT_ABSENT_MULTI');
							}
						}
						else
						{
							if ($arItem = $rsItems->Fetch())
							{
								$arResult['values'] = intval($arItem['ID']);
								$arResult['labels'] = $arItem['NAME'];
							}
							else
							{
								$boolError = true;
								$arMsg[] = GetMessage('BT_MOD_COND_ERR_CHECK_DATA_ELEMENT_ABSENT');
							}
						}
						break;
					case 'section':
						$rsSections = CIBlockSection::GetList(array(), array('ID' => $arOneCondition['value']), false, array('ID', 'NAME'));
						if (is_array($arOneCondition['value']))
						{
							$arCheckResult = array();
							while ($arSection = $rsSections->Fetch())
							{
								$arCheckResult[intval($arSection['ID'])] = $arSection['NAME'];
							}
							if (!empty($arCheckResult))
							{
								$arResult['values'] = array_keys($arCheckResult);
								$arResult['labels'] = array_values($arCheckResult);
							}
							else
							{
								$boolError = true;
								$arMsg[] = GetMessage('BT_MOD_COND_ERR_CHECK_DATA_SECTION_ABSENT_MULTI');
							}
						}
						else
						{
							if ($arSection = $rsSections->Fetch())
							{
								$arResult['values'] = intval($arSection['ID']);
								$arResult['labels'] = $arSection['NAME'];
							}
							else
							{
								$boolError = true;
								$arMsg[] = GetMessage('BT_MOD_COND_ERR_CHECK_DATA_SECTION_ABSENT');
							}
						}
						break;
					case 'iblock':
						if (is_array($arOneCondition['value']))
						{
							$arCheckResult = array();
							foreach ($arOneCondition['value'] as &$intIBlockID)
							{
								$strName = CIBlock::GetArrayByID($intIBlockID, 'NAME');
								if (false !== $strName && !is_null($strName))
								{
									$arCheckResult[$intIBlockID] = $strName;
								}
							}
							if (isset($intIBlockID))
								unset($intIBlockID);
							if (!empty($arCheckResult))
							{
								$arResult['values'] = array_keys($arCheckResult);
								$arResult['labels'] = array_values($arCheckResult);
							}
							else
							{
								$boolError = true;
								$arMsg[] = GetMessage('BT_MOD_COND_ERR_CHECK_DATA_IBLOCK_ABSENT_MULTI');
							}
						}
						else
						{
							$strName = CIBlock::GetArrayByID($arOneCondition['value'], 'NAME');
							if (false !== $strName && !is_null($strName))
							{
								$arResult['values'] = $arOneCondition['value'];
								$arResult['labels'] = $strName;
							}
							else
							{
								$boolError = true;
								$arMsg[] = GetMessage('BT_MOD_COND_ERR_CHECK_DATA_IBLOCK_ABSENT');
							}
						}
						break;
					case 'user':
						if (is_array($arOneCondition['value']))
						{
							$arCheckResult = array();
							foreach ($arOneCondition['value'] as &$intUserID)
							{
								$rsUsers = CUser::GetList(($by2 = 'ID'),($order2 = 'ASC'),array('ID_EQUAL_EXACT' => $intUserID),array('FIELDS' => array('ID', 'LOGIN', 'NAME', 'LAST_NAME')));
								if ($arUser = $rsUsers->Fetch())
								{
									$strName = trim($arUser['NAME'].' '.$arUser['LAST_NAME']);
									if ('' == $strName)
										$strName = $arUser['LOGIN'];
									$arCheckResult[$intUserID] = $strName;
								}
							}
							if (isset($intUserID))
								unset($intUserID);
							if (!empty($arCheckResult))
							{
								$arResult['values'] = array_keys($arCheckResult);
								$arResult['labels'] = array_values($arCheckResult);
							}
							else
							{
								$boolError = true;
								$arMsg[] = GetMessage('BT_MOD_COND_ERR_CHECK_DATA_USER_ABSENT_MULTI');
							}
						}
						else
						{
							$rsUsers = CUser::GetList(($by2 = 'ID'),($order2 = 'ASC'),array('ID_EQUAL_EXACT' => $arOneCondition['value']),array('FIELDS' => array('ID', 'LOGIN', 'NAME', 'LAST_NAME')));
							if ($arUser = $rsUsers->Fetch())
							{
								$arResult['values'] = $arOneCondition['value'];
								$arResult['labels'] = trim($arUser['NAME'].' '.$arUser['LAST_NAME']);
								if ('' == $arResult['labels'])
									$arResult['labels'] = $arUser['LOGIN'];
							}
							else
							{
								$boolError = true;
								$arMsg[] = GetMessage('BT_MOD_COND_ERR_CHECK_DATA_USER_ABSENT');
							}
						}
						break;
					case 'list':
						if (isset($arControl['JS_VALUE']) && is_array($arControl['JS_VALUE']) && isset($arControl['JS_VALUE']['values']) && !empty($arControl['JS_VALUE']['values']))
						{
							if (is_array($arOneCondition['value']))
							{
								$arCheckResult = array();
								foreach ($arOneCondition['value'] as &$strValue)
								{
									if (array_key_exists($strValue, $arControl['JS_VALUE']['values']))
										$arCheckResult[] = $strValue;
								}
								if (isset($strValue))
									unset($strValue);
								if (!empty($arCheckResult))
								{
									$arResult['values'] = $arCheckResult;
								}
								else
								{
									$boolError = true;
									$arMsg[] = GetMessage('BT_MOD_COND_ERR_CHECK_DATA_LIST_ABSENT_MULTI');
								}
							}
							else
							{
								if (array_key_exists($arOneCondition['value'], $arControl['JS_VALUE']['values']))
								{
									$arResult['values'] = $arOneCondition['value'];
								}
								else
								{
									$boolError = true;
									$arMsg[] = GetMessage('BT_MOD_COND_ERR_CHECK_DATA_LIST_ABSENT');
								}
							}
						}
						else
						{
							$boolError = true;
						}
						break;
				}
			}
			else
			{
				// validate for save
				switch($arControl['PHP_VALUE']['VALIDATE'])
				{
					case 'element':
						$rsItems = CIBlockElement::GetList(array(), array('ID' => $arOneCondition['value']), false, false, array('ID'));
						if (is_array($arOneCondition['value']))
						{
							$arCheckResult = array();
							while ($arItem = $rsItems->Fetch())
							{
								$arCheckResult[] = intval($arItem['ID']);
							}
							if (!empty($arCheckResult))
							{
								$arResult['values'] = $arCheckResult;
							}
							else
							{
								$boolError = true;
							}
						}
						else
						{
							if ($arItem = $rsItems->Fetch())
							{
								$arResult['values'] = intval($arItem['ID']);
							}
							else
							{
								$boolError = true;
							}
						}
						break;
					case 'section':
						$rsSections = CIBlockSection::GetList(array(), array('ID' => $arOneCondition['value']), false, array('ID'));
						if (is_array($arOneCondition['value']))
						{
							$arCheckResult = array();
							while ($arSection = $rsSections->Fetch())
							{
								$arCheckResult[] = intval($arSection['ID']);
							}
							if (!empty($arCheckResult))
							{
								$arResult['values'] = $arCheckResult;
							}
							else
							{
								$boolError = true;
							}
						}
						else
						{
							if ($arSection = $rsSections->Fetch())
							{
								$arResult['values'] = intval($arSection['ID']);
							}
							else
							{
								$boolError = true;
							}
						}
						break;
					case 'iblock':
						if (is_array($arOneCondition['value']))
						{
							$arCheckResult = array();
							foreach ($arOneCondition['value'] as &$intIBlockID)
							{
								$strName = CIBlock::GetArrayByID($intIBlockID, 'NAME');
								if (false !== $strName && !is_null($strName))
								{
									$arCheckResult[] = $intIBlockID;
								}
							}
							if (isset($intIBlockID))
								unset($intIBlockID);
							if (!empty($arCheckResult))
							{
								$arResult['values'] = $arCheckResult;
							}
							else
							{
								$boolError = true;
							}
						}
						else
						{
							$strName = CIBlock::GetArrayByID($arOneCondition['value'], 'NAME');
							if (false !== $strName && !is_null($strName))
							{
								$arResult['values'] = $arOneCondition['value'];
							}
							else
							{
								$boolError = true;
							}
						}
						break;
					case 'user':
						if (is_array($arOneCondition['value']))
						{
							$arCheckResult = array();
							foreach ($arOneCondition['value'] as &$intUserID)
							{
								$rsUsers = CUser::GetList(($by2 = 'ID'),($order2 = 'ASC'),array('ID_EQUAL_EXACT' => $intUserID),array('FIELDS' => array('ID', 'LOGIN', 'NAME', 'LAST_NAME')));
								if ($arUser = $rsUsers->Fetch())
								{
									$arCheckResult[] = $intUserID;
								}
							}
							if (isset($intUserID))
								unset($intUserID);
							if (!empty($arCheckResult))
							{
								$arResult['values'] = $arCheckResult;
							}
							else
							{
								$boolError = true;
							}
						}
						else
						{
							$rsUsers = CUser::GetList(($by2 = 'ID'),($order2 = 'ASC'),array('ID_EQUAL_EXACT' => $arOneCondition['value']),array('FIELDS' => array('ID', 'LOGIN', 'NAME', 'LAST_NAME')));
							if ($arUser = $rsUsers->Fetch())
							{
								$arResult['values'] = $arOneCondition['value'];
							}
							else
							{
								$boolError = true;
							}
						}
						break;
					case 'list':
						if (isset($arControl['JS_VALUE']) && is_array($arControl['JS_VALUE']) && isset($arControl['JS_VALUE']['values']) && !empty($arControl['JS_VALUE']['values']))
						{
							if (is_array($arOneCondition['value']))
							{
								$arCheckResult = array();
								foreach ($arOneCondition['value'] as &$strValue)
								{
									if (array_key_exists($strValue, $arControl['JS_VALUE']['values']))
										$arCheckResult[] = $strValue;
								}
								if (isset($strValue))
									unset($strValue);
								if (!empty($arCheckResult))
								{
									$arResult['values'] = $arCheckResult;
								}
								else
								{
									$boolError = true;
								}
							}
							else
							{
								if (array_key_exists($arOneCondition['value'], $arControl['JS_VALUE']['values']))
								{
									$arResult['values'] = $arOneCondition['value'];
								}
								else
								{
									$boolError = true;
								}
							}
						}
						else
						{
							$boolError = true;
						}
						break;
				}
			}
		}

		if ($boolShow)
		{
			if ($boolError)
			{
				$arResult['err_cond'] = 'Y';
				$arResult['err_cond_mess'] = $arMsg;
			}
			return $arResult;
		}
		else
		{
			return (!$boolError ? $arResult : false);
		}
	}

	function Generate($arOneCondition, $arParams, $arControl, $arSubs = false)
	{

	}
}

class CCatalogCondCtrlComplex extends CCatalogCondCtrl
{
	public function __construct()
	{
		parent::__construct();
	}

	public function __destruct()
	{
		parent::__destruct();
	}

	function IsGroup($strControlID = false)
	{
		return 'N';
	}

	function GetControls($strControlID = false)
	{
		return false;
	}

	function GetConditionShow($arParams)
	{
		if (!isset($arParams['ID']))
			return false;
		$arControl = self::GetControls($arParams['ID']);
		if (false === $arControl)
			return false;
		return self::Check($arParams['DATA'], $arParams, $arControl, true);
	}

	function Parse($arOneCondition)
	{
		if (!isset($arOneCondition['controlId']))
			return false;
		$arControl = self::GetControls($arOneCondition['controlId']);
		if (false === $arControl)
			return false;
		return self::Check($arOneCondition, $arOneCondition, $arControl, false);
	}

	function Generate($arOneCondition, $arParams, $arControl, $arSubs = false)
	{
		$strResult = '';
		$boolError = false;

		if (is_string($arControl))
		{
			$arControl = self::GetControls($arControl);
		}
		$boolError = !is_array($arControl);

		if (!$boolError)
		{
			$arValues = self::Check($arOneCondition, $arOneCondition, $arControl, false);
			if (false === $arValues)
			{
				$boolError = true;
			}
		}

		if (!$boolError)
		{
			$arLogic = self::SearchLogic($arValues['logic'], $arControl['LOGIC']);
			if (!isset($arLogic['OP'][$arControl['MULTIPLE']]) || empty($arLogic['OP'][$arControl['MULTIPLE']]))
			{
				$boolError = true;
			}
			else
			{
				$strField = $arParams['FIELD'].'[\''.$arControl['FIELD'].'\']';
				switch ($arControl['FIELD_TYPE'])
				{
					case 'int':
					case 'double':
						$strResult = str_replace(array('#FIELD#', '#VALUE#'), array($strField, $arValues['value']), $arLogic['OP'][$arControl['MULTIPLE']]);
						break;
					case 'char':
					case 'string':
					case 'text':
						$strResult = str_replace(array('#FIELD#', '#VALUE#'), array($strField, '"'.EscapePHPString($arValues['value']).'"'), $arLogic['OP'][$arControl['MULTIPLE']]);
						break;
					case 'date':
					case 'datetime':
						$strResult = str_replace(array('#FIELD#', '#VALUE#'), array($strField, '"'.EscapePHPString($arValues['value']).'"'), $arLogic['OP'][$arControl['MULTIPLE']]);
						break;
				}
			}
		}

		return (!$boolError ? $strResult : false);
	}
}

class CCatalogCondCtrlGroup extends CCatalogCondCtrl
{
	public function __construct()
	{
		parent::__construct();
	}

	public function __destruct()
	{
		parent::__destruct();
	}

	public function GetControlDescr()
	{
		return array(
			"ID" => "CondGroup",
			"GROUP" => "Y",
			"GetControlShow" => array(__CLASS__, "GetControlShow"),
			"GetConditionShow" => array(__CLASS__, "GetConditionShow"),
			"IsGroup" => array(__CLASS__, "IsGroup"),
			"Parse" => array(__CLASS__, "Parse"),
			"Generate" => array(__CLASS__, "Generate"),
		);
	}

	function GetControlShow($arParams)
	{
		$arAtoms = self::GetAtoms();
		return array(
			'controlId' => 'CondGroup',
			'group' => true,
			'label' => GetMessage('BT_MOD_CATALOG_COND_GROUP_LABEL'),
			'defaultText' => GetMessage('BT_MOD_CATALOG_COND_GROUP_DEF_TEXT'),
			'showIn' => array_values($arParams['SHOW_IN_GROUPS']),
			'visual' => self::GetVisual(),
			'control' => array(
				$arAtoms['All'],
				$arAtoms['True'],
			),
		);
	}

	function GetConditionShow($arParams)
	{
		$boolError = false;
		$arAtoms = self::GetAtoms();
		$arValues = array();
		foreach ($arAtoms as &$arOneAtom)
		{
			if (!isset($arParams['DATA'][$arOneAtom['id']]))
			{
				$boolError = true;
			}
			elseif (!is_string($arParams['DATA'][$arOneAtom['id']]))
			{
				$boolError = true;
			}
			elseif (!array_key_exists($arParams['DATA'][$arOneAtom['id']], $arOneAtom['values']))
			{
				$boolError = true;
			}
			if (!$boolError)
			{
				$arValues[$arOneAtom['id']] = $arParams['DATA'][$arOneAtom['id']];
			}
			else
			{
				$arValues[$arOneAtom['id']] = '';
			}
		}
		if (isset($arOneAtoms))
			unset($arOneAtom);

		$arResult = array(
			'id' => $arParams['COND_NUM'],
			'controlId' => 'CondGroup',
			'values' => $arValues,
		);
		if ($boolError)
			$arResult['err_cond'] = 'Y';

		return $arResult;
	}

	function GetAtoms()
	{
		return array(
			'All' => array(
				'id' => 'All',
				'name' => 'aggregator',
				'type' => 'select',
				'values' => array(
					'AND' => GetMessage('BT_MOD_CATALOG_COND_GROUP_SELECT_ALL'),
					'OR' => GetMessage('BT_MOD_CATALOG_COND_GROUP_SELECT_ANY'),
				),
				'defaultText' => GetMessage('BT_MOD_CATALOG_COND_GROUP_SELECT_DEF'),
				'defaultValue' => 'AND',
				'first_option' => '...',
			),
			'True' => array(
				'id' => 'True',
				'name' => 'value',
				'type' => 'select',
				'values' => array(
					'True' => GetMessage('BT_MOD_CATALOG_COND_GROUP_SELECT_TRUE'),
					'False' => GetMessage('BT_MOD_CATALOG_COND_GROUP_SELECT_FALSE'),
				),
				'defaultText' => GetMessage('BT_MOD_CATALOG_COND_GROUP_SELECT_DEF'),
				'defaultValue' => 'True',
				'first_option' => '...',
			),
		);
	}

	function GetVisual()
	{
		return array(
			'controls' => array(
				'All',
				'True',
			),
			'values' => array(
				array(
					'All' => 'AND',
					'True' => 'True',
				),
				array(
					'All' => 'AND',
					'True' => 'False',
				),
				array(
					'All' => 'OR',
					'True' => 'True',
				),
				array(
					'All' => 'OR',
					'True' => 'False',
				),
			),
			'logic' => array(
				array(
					'style' => 'condition-logic-and',
					'message' => GetMessage('BT_MOD_CATALOG_COND_GROUP_LOGIC_AND'),
				),
				array(
					'style' => 'condition-logic-and',
					'message' => GetMessage('BT_MOD_CATALOG_COND_GROUP_LOGIC_NOT_AND'),
				),
				array(
					'style' => 'condition-logic-or',
					'message' => GetMessage('BT_MOD_CATALOG_COND_GROUP_LOGIC_OR'),
				),
				array(
					'style' => 'condition-logic-or',
					'message' => GetMessage('BT_MOD_CATALOG_COND_GROUP_LOGIC_NOT_OR'),
				),
			)
		);
	}

	function IsGroup($strControlID = false)
	{
		return 'Y';
	}

	function Parse($arOneCondition)
	{
		$boolError = false;
		$arResult = array();
		$arAtoms = self::GetAtoms();
		foreach ($arAtoms as &$arOneAtom)
		{
			if (!isset($arOneCondition[$arOneAtom['name']]))
			{
				$boolError = true;
			}
			elseif (!is_string($arOneCondition[$arOneAtom['name']]))
			{
				$boolError = true;
			}
			elseif (!array_key_exists($arOneCondition[$arOneAtom['name']], $arOneAtom['values']))
			{
				$boolError = true;
			}
			if (!$boolError)
			{
				$arResult[$arOneAtom['id']] = $arOneCondition[$arOneAtom['name']];
			}
		}
		if (isset($arOneAtom))
			unset($arOneAtom);

		return (!$boolError ? $arResult : false);
	}

	function Generate($arOneCondition, $arParams, $arControl, $arSubs = false)
	{
		$mxResult = '';
		$boolError = false;

		$arAtoms = self::GetAtoms();

		foreach ($arAtoms as &$arOneAtom)
		{
			if (!isset($arOneCondition[$arOneAtom['id']]))
			{
				$boolError = true;
			}
			elseif (!is_string($arOneCondition[$arOneAtom['id']]))
			{
				$boolError = true;
			}
			elseif (!array_key_exists($arOneCondition[$arOneAtom['id']], $arOneAtom['values']))
			{
				$boolError = true;
			}
		}
		if (isset($arOneAtom))
			unset($arOneAtom);

		if (!isset($arSubs) || !is_array($arSubs))
		{
			$boolError = true;
		}
		elseif (empty($arSubs))
		{
			return '(1 == 1)';
		}

		if (!$boolError)
		{
			$strPrefix = '';
			$strLogic = '';
			$strItemPrefix = '';

			if ('AND' == $arOneCondition['All'])
			{
				$strPrefix = '';
				$strLogic = ' && ';
				$strItemPrefix = ('True' == $arOneCondition['True'] ? '' : '!');
			}
			else
			{
				$strItemPrefix = '';
				if ('True' == $arOneCondition['True'])
				{
					$strPrefix = '';
					$strLogic = ' || ';
				}
				else
				{
					$strPrefix = '!';
					$strLogic = ' && ';
				}
			}

			$strEval = $strItemPrefix.implode($strLogic.$strItemPrefix, $arSubs);
			if ('' != $strPrefix)
				$strEval = $strPrefix.'('.$strEval.')';
			$mxResult = $strEval;
		}

		return $mxResult;
	}
}

class CCatalogCondCtrlIBlockFields extends CCatalogCondCtrlComplex
{
	public function __construct()
	{
		parent::__construct();
	}

	public function __destruct()
	{
		parent::__destruct();
	}

	function GetControlDescr()
	{
		return array(
			'COMPLEX' => 'Y',
			"GetControlShow" => array(__CLASS__, "GetControlShow"),
			"GetConditionShow" => array(__CLASS__, "GetConditionShow"),
			"IsGroup" => array(__CLASS__, "IsGroup"),
			"Parse" => array(__CLASS__, "Parse"),
			"Generate" => array(__CLASS__, "Generate"),
			'CONTROLS' => self::GetControls(),
		);
	}

	function GetControlShow($arParams)
	{
		$arControls = self::GetControls();
		$arResult = array(
			'controlgroup' => true,
			'group' =>  false,
			'label' => GetMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_CONTROLGROUP_LABEL'),
			'showIn' => array_values($arParams['SHOW_IN_GROUPS']),
			'children' => array()
		);
		foreach ($arControls as &$arOneControl)
		{
			$arLogic = self::GetLogicAtom($arOneControl['LOGIC']);
			$arValue = self::GetValueAtom($arOneControl['JS_VALUE']);
			$arResult['children'][] = array(
				'controlId' => $arOneControl['ID'],
				'group' => false,
				'label' => $arOneControl['LABEL'],
				'showIn' => array_values($arParams['SHOW_IN_GROUPS']),
				'control' => array(
					$arOneControl['PREFIX'],
					$arLogic,
					$arValue,
				),
			);
		}
		if (isset($arOneControl))
			unset($arOneControl);

		return $arResult;
	}

	function GetControls($strControlID = false)
	{
		global $CATALOG_TIME_PERIOD_TYPES;

		$arVatList = array();
		$arFilter = array('ACTIVE' => 'Y');
		$rsVats = CCatalogVat::GetList(array(), $arFilter, array('ID', 'NAME'));
		while ($arVat = $rsVats->Fetch())
		{
			$arVatList[$arVat['ID']] = $arVat['NAME'];
		}

		$arControlList = array(
			'CondIBElement' => array(
				'ID' => 'CondIBElement',
				'FIELD' => 'ID',
				'FIELD_TYPE' => 'int',
				'MULTIPLE' => 'N',
				'GROUP' => 'N',
				'LABEL' => GetMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_ELEMENT_ID_LABEL'),
				'PREFIX' => GetMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_ELEMENT_ID_PREFIX'),
				'LOGIC' => self::GetLogic(array(BT_COND_LOGIC_EQ, BT_COND_LOGIC_NOT_EQ)),
				'JS_VALUE' => array(
					'type' => 'popup',
					'popup_url' =>  '/bitrix/admin/iblock_element_search.php',
					'popup_params' => array(
						'lang' => LANGUAGE_ID,
					),
					'param_id' => 'n',
				),
				'PHP_VALUE' => array(
					'VALIDATE' => 'element'
				),
			),
			'CondIBIBlock' => array(
				'ID' => 'CondIBIBlock',
				'FIELD' => 'IBLOCK_ID',
				'FIELD_TYPE' => 'int',
				'MULTIPLE' => 'N',
				'GROUP' => 'N',
				'LABEL' => GetMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_IBLOCK_ID_LABEL'),
				'PREFIX' => GetMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_IBLOCK_ID_PREFIX'),
				'LOGIC' => self::GetLogic(array(BT_COND_LOGIC_EQ, BT_COND_LOGIC_NOT_EQ)),
				'JS_VALUE' => array(
					'type' => 'popup',
					'popup_url' =>  '/bitrix/admin/cat_iblock_search.php',
					'popup_params' => array(
						'lang' => LANGUAGE_ID,
					),
					'param_id' => 'n',
				),
				'PHP_VALUE' => array(
					'VALIDATE' => 'iblock'
				),
			),
			'CondIBSection' => array(
				'ID' => 'CondIBSection',
				'FIELD' => 'SECTION_ID',
				'FIELD_TYPE' => 'int',
				'MULTIPLE' => 'Y',
				'GROUP' => 'N',
				'LABEL' => GetMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_SECTION_ID_LABEL'),
				'PREFIX' => GetMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_SECTION_ID_PREFIX'),
				'LOGIC' => self::GetLogic(array(BT_COND_LOGIC_EQ, BT_COND_LOGIC_NOT_EQ)),
				'JS_VALUE' => array(
					'type' => 'popup',
					'popup_url' =>  '/bitrix/admin/cat_section_search.php',
					'popup_params' => array(
						'lang' => LANGUAGE_ID,
					),
					'param_id' => 'n',
				),
				'PHP_VALUE' => array(
					'VALIDATE' => 'section'
				),
			),
			'CondIBCode' => array(
				'ID' => 'CondIBCode',
				'FIELD' => 'CODE',
				'FIELD_TYPE' => 'string',
				'MULTIPLE' => 'N',
				'GROUP' => 'N',
				'LABEL' => GetMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_CODE_LABEL'),
				'PREFIX' => GetMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_CODE_PREFIX'),
				'LOGIC' => self::GetLogic(array(BT_COND_LOGIC_EQ, BT_COND_LOGIC_NOT_EQ, BT_COND_LOGIC_CONT, BT_COND_LOGIC_NOT_CONT)),
				'JS_VALUE' => array(
					'type' => 'input',
				),
				'PHP_VALUE' => '',
			),
			'CondIBXmlID' => array(
				'ID' => 'CondIBXmlID',
				'FIELD' => 'XML_ID',
				'FIELD_TYPE' => 'string',
				'FIELD_LENGTH' => 255,
				'MULTIPLE' => 'N',
				'GROUP' => 'N',
				'LABEL' => GetMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_XML_ID_LABEL'),
				'PREFIX' => GetMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_XML_ID_PREFIX'),
				'LOGIC' => self::GetLogic(array(BT_COND_LOGIC_EQ, BT_COND_LOGIC_NOT_EQ, BT_COND_LOGIC_CONT, BT_COND_LOGIC_NOT_CONT)),
				'JS_VALUE' => array(
					'type' => 'input',
				),
				'PHP_VALUE' => '',
			),
			'CondIBName' => array(
				'ID' => 'CondIBName',
				'FIELD' => 'NAME',
				'FIELD_TYPE' => 'string',
				'MULTIPLE' => 'N',
				'GROUP' => 'N',
				'LABEL' => GetMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_NAME_LABEL'),
				'PREFIX' => GetMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_NAME_PREFIX'),
				'LOGIC' => self::GetLogic(array(BT_COND_LOGIC_EQ, BT_COND_LOGIC_NOT_EQ, BT_COND_LOGIC_CONT, BT_COND_LOGIC_NOT_CONT)),
				'JS_VALUE' => array(
					'type' => 'input',
				),
				'PHP_VALUE' => '',
			),
			'CondIBActive' => array(
				'ID' => 'CondIBActive',
				'FIELD' => 'ACTIVE',
				'FIELD_TYPE' => 'char',
				'MULTIPLE' => 'N',
				'GROUP' => 'N',
				'LABEL' => GetMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_ACTIVE_LABEL'),
				'PREFIX' => GetMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_ACTIVE_PREFIX'),
				'LOGIC' => self::GetLogic(array(BT_COND_LOGIC_EQ, BT_COND_LOGIC_NOT_EQ)),
				'JS_VALUE' => array(
					'type' => 'select',
					'values' => array(
						'Y' => GetMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_ACTIVE_VALUE_YES'),
						'N' => GetMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_ACTIVE_VALUE_NO'),
					),
				),
				'PHP_VALUE' => array(
					'VALIDATE' => 'list'
				),
			),
			'CondIBDateActiveFrom' => array(
				'ID' => 'CondIBDateActiveFrom',
				'FIELD' => 'DATE_ACTIVE_FROM',
				'FIELD_TYPE' => 'datetime',
				'MULTIPLE' => 'N',
				'GROUP' => 'N',
				'LABEL' => GetMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_DATE_ACTIVE_FROM_LABEL'),
				'PREFIX' => GetMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_DATE_ACTIVE_FROM_PREFIX'),
				'LOGIC' => self::GetLogic(array(BT_COND_LOGIC_EQ, BT_COND_LOGIC_GR, BT_COND_LOGIC_LS, BT_COND_LOGIC_EGR, BT_COND_LOGIC_ELS)),
				'JS_VALUE' => array(
					'type' => 'datetime',
				),
				'PHP_VALUE' => ''
			),
			'CondIBDateActiveTo' => array(
				'ID' => 'CondIBDateActiveTo',
				'FIELD' => 'DATE_ACTIVE_TO',
				'FIELD_TYPE' => 'datetime',
				'MULTIPLE' => 'N',
				'GROUP' => 'N',
				'LABEL' => GetMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_DATE_ACTIVE_TO_LABEL'),
				'PREFIX' => GetMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_DATE_ACTIVE_TO_PREFIX'),
				'LOGIC' => self::GetLogic(array(BT_COND_LOGIC_EQ, BT_COND_LOGIC_GR, BT_COND_LOGIC_LS, BT_COND_LOGIC_EGR, BT_COND_LOGIC_ELS)),
				'JS_VALUE' => array(
					'type' => 'datetime',
				),
				'PHP_VALUE' => '',
			),
			'CondIBSort' => array(
				'ID' => 'CondIBSort',
				'FIELD' => 'SORT',
				'FIELD_TYPE' => 'int',
				'MULTIPLE' => 'N',
				'GROUP' => 'N',
				'LABEL' => GetMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_SORT_LABEL'),
				'PREFIX' => GetMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_SORT_PREFIX'),
				'LOGIC' => self::GetLogic(array(BT_COND_LOGIC_EQ, BT_COND_LOGIC_NOT_EQ, BT_COND_LOGIC_GR, BT_COND_LOGIC_LS, BT_COND_LOGIC_EGR, BT_COND_LOGIC_ELS)),
				'JS_VALUE' => array(
					'type' => 'input',
				),
				'PHP_VALUE' => '',
			),
			'CondIBPreviewText' => array(
				'ID' => 'CondIBPreviewText',
				'FIELD' => 'PREVIEW_TEXT',
				'FIELD_TYPE' => 'text',
				'MULTIPLE' => 'N',
				'GROUP' => 'N',
				'LABEL' => GetMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_PREVIEW_TEXT_LABEL'),
				'PREFIX' => GetMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_PREVIEW_TEXT_PREFIX'),
				'LOGIC' => self::GetLogic(array(BT_COND_LOGIC_EQ, BT_COND_LOGIC_NOT_EQ, BT_COND_LOGIC_CONT, BT_COND_LOGIC_NOT_CONT)),
				'JS_VALUE' => array(
					'type' => 'input',
				),
				'PHP_VALUE' => '',
			),
			'CondIBDetailText' => array(
				'ID' => 'CondIBDetailText',
				'FIELD' => 'DETAIL_TEXT',
				'FIELD_TYPE' => 'text',
				'MULTIPLE' => 'N',
				'GROUP' => 'N',
				'LABEL' => GetMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_DETAIL_TEXT_LABEL'),
				'PREFIX' => GetMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_DETAIL_TEXT_PREFIX'),
				'LOGIC' => self::GetLogic(array(BT_COND_LOGIC_EQ, BT_COND_LOGIC_NOT_EQ, BT_COND_LOGIC_CONT, BT_COND_LOGIC_NOT_CONT)),
				'JS_VALUE' => array(
					'type' => 'input',
				),
				'PHP_VALUE' => '',
			),
			'CondIBDateCreate' => array(
				'ID' => 'CondIBDateCreate',
				'FIELD' => 'DATE_CREATE',
				'FIELD_TYPE' => 'datetime',
				'MULTIPLE' => 'N',
				'GROUP' => 'N',
				'LABEL' => GetMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_DATE_CREATE_LABEL'),
				'PREFIX' => GetMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_DATE_CREATE_PREFIX'),
				'LOGIC' => self::GetLogic(array(BT_COND_LOGIC_EQ, BT_COND_LOGIC_GR, BT_COND_LOGIC_LS, BT_COND_LOGIC_EGR, BT_COND_LOGIC_ELS)),
				'JS_VALUE' => array(
					'type' => 'datetime',
				),
				'PHP_VALUE' => '',
			),
			'CondIBCreatedBy' => array(
				'ID' => 'CondIBCreatedBy',
				'FIELD' => 'CREATED_BY',
				'FIELD_TYPE' => 'int',
				'MULTIPLE' => 'N',
				'GROUP' => 'N',
				'LABEL' => GetMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_CREATED_BY_LABEL'),
				'PREFIX' => GetMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_CREATED_BY_PREFIX'),
				'LOGIC' => self::GetLogic(array(BT_COND_LOGIC_EQ, BT_COND_LOGIC_NOT_EQ)),
				'JS_VALUE' => array(
					'type' => 'input',
				),
				'PHP_VALUE' => array(
					'VALIDATE' => 'user'
				),
			),
			'CondIBTimestampX' => array(
				'ID' => 'CondIBTimestampX',
				'FIELD' => 'TIMESTAMP_X',
				'FIELD_TYPE' => 'datetime',
				'MULTIPLE' => 'N',
				'GROUP' => 'N',
				'LABEL' => GetMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_TIMESTAMP_X_LABEL'),
				'PREFIX' => GetMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_TIMESTAMP_X_PREFIX'),
				'LOGIC' => self::GetLogic(array(BT_COND_LOGIC_EQ, BT_COND_LOGIC_GR, BT_COND_LOGIC_LS, BT_COND_LOGIC_EGR, BT_COND_LOGIC_ELS)),
				'JS_VALUE' => array(
					'type' => 'datetime',
				),
				'PHP_VALUE' => '',
			),
			'CondIBModifiedBy' => array(
				'ID' => 'CondIBModifiedBy',
				'FIELD' => 'MODIFIED_BY',
				'FIELD_TYPE' => 'int',
				'MULTIPLE' => 'N',
				'GROUP' => 'N',
				'LABEL' => GetMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_MODIFIED_BY_LABEL'),
				'PREFIX' => GetMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_MODIFIED_BY_PREFIX'),
				'LOGIC' => self::GetLogic(array(BT_COND_LOGIC_EQ, BT_COND_LOGIC_NOT_EQ)),
				'JS_VALUE' => array(
					'type' => 'input',
				),
				'PHP_VALUE' => array(
					'VALIDATE' => 'user'
				),
			),
			'CondIBTags' => array(
				'ID' => 'CondIBTags',
				'FIELD' => 'TAGS',
				'FIELD_TYPE' => 'string',
				'MULTIPLE' => 'N',
				'GROUP' => 'N',
				'LABEL' => GetMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_TAGS_LABEL'),
				'PREFIX' => GetMessage('BT_MOD_CATALOG_COND_CMP_IBLOCK_TAGS_PREFIX'),
				'LOGIC' => self::GetLogic(array(BT_COND_LOGIC_EQ, BT_COND_LOGIC_NOT_EQ, BT_COND_LOGIC_CONT, BT_COND_LOGIC_NOT_CONT)),
				'JS_VALUE' => array(
					'type' => 'input',
				),
				'PHP_VALUE' => '',
			),
			'CondCatQuantity' => array(
				'ID' => 'CondCatQuantity',
				'FIELD' => 'CATALOG_QUANTITY',
				'FIELD_TYPE' => 'double',
				'MULTIPLE' => 'N',
				'GROUP' => 'N',
				'LABEL' => GetMessage('BT_MOD_CATALOG_COND_CMP_CATALOG_QUANTITY_LABEL'),
				'PREFIX' => GetMessage('BT_MOD_CATALOG_COND_CMP_CATALOG_QUANTITY_PREFIX'),
				'LOGIC' => self::GetLogic(array(BT_COND_LOGIC_EQ, BT_COND_LOGIC_NOT_EQ, BT_COND_LOGIC_GR, BT_COND_LOGIC_LS, BT_COND_LOGIC_EGR, BT_COND_LOGIC_ELS)),
				'JS_VALUE' => array(
					'type' => 'input',
				),
			),
			'CondCatWeight' => array(
				'ID' => 'CondCatWeight',
				'FIELD' => 'CATALOG_WEIGHT',
				'FIELD_TYPE' => 'double',
				'MULTIPLE' => 'N',
				'GROUP' => 'N',
				'LABEL' => GetMessage('BT_MOD_CATALOG_COND_CMP_CATALOG_WEIGHT_LABEL'),
				'PREFIX' => GetMessage('BT_MOD_CATALOG_COND_CMP_CATALOG_WEIGHT_PREFIX'),
				'LOGIC' => self::GetLogic(array(BT_COND_LOGIC_EQ, BT_COND_LOGIC_NOT_EQ, BT_COND_LOGIC_GR, BT_COND_LOGIC_LS, BT_COND_LOGIC_EGR, BT_COND_LOGIC_ELS)),
				'JS_VALUE' => array(
					'type' => 'input'
				),
				'PHP_VALUE' => '',
			),
			'CondCatVatID' => array(
				'ID' => 'CondCatVatID',
				'FIELD' => 'CATALOG_VAT_ID',
				'FIELD_TYPE' => 'int',
				'MULTIPLE' => 'N',
				'GROUP' => 'N',
				'LABEL' => GetMessage('BT_MOD_CATALOG_COND_CMP_CATALOG_VAT_ID_LABEL'),
				'PREFIX' => GetMessage('BT_MOD_CATALOG_COND_CMP_CATALOG_VAT_ID_PREFIX'),
				'LOGIC' => self::GetLogic(array(BT_COND_LOGIC_EQ, BT_COND_LOGIC_NOT_EQ)),
				'JS_VALUE' => array(
					'type' => 'select',
					'values' => $arVatList,
				),
				'PHP_VALUE' => array(
					'VALIDATE' => 'list',
				),
			),
			'CondCatVatIncluded' => array(
				'ID' => 'CondCatVatIncluded',
				'FIELD' => 'CATALOG_VAT_INCLUDED',
				'FIELD_TYPE' => 'char',
				'MULTIPLE' => 'N',
				'GROUP' => 'N',
				'LABEL' => GetMessage('BT_MOD_CATALOG_COND_CMP_CATALOG_VAT_INCLUDED_LABEL'),
				'PREFIX' => GetMessage('BT_MOD_CATALOG_COND_CMP_CATALOG_VAT_INCLUDED_PREFIX'),
				'LOGIC' => self::GetLogic(array(BT_COND_LOGIC_EQ, BT_COND_LOGIC_NOT_EQ)),
				'JS_VALUE' => array(
					'type' => 'select',
					'values' => array(
						'Y' => GetMessage('BT_MOD_CATALOG_COND_CMP_CATALOG_VAT_INCLUDED_VALUE_YES'),
						'N' => GetMessage('BT_MOD_CATALOG_COND_CMP_CATALOG_VAT_INCLUDED_VALUE_NO'),
					),
				),
				'PHP_VALUE' => array(
					'VALIDATE' => 'list'
				),
			),
		);

		if (false === $strControlID)
		{
			return $arControlList;
		}
		elseif (array_key_exists($strControlID, $arControlList))
		{
			return $arControlList[$strControlID];
		}
		else
		{
			return false;
		}
	}

	function GetConditionShow($arParams)
	{
		if (!isset($arParams['ID']))
			return false;
		$arControl = self::GetControls($arParams['ID']);
		if (false === $arControl)
			return false;
		return self::Check($arParams['DATA'], $arParams, $arControl, true);
	}

	function Parse($arOneCondition)
	{
		if (!isset($arOneCondition['controlId']))
			return false;
		$arControl = self::GetControls($arOneCondition['controlId']);
		if (false === $arControl)
			return false;
		return self::Check($arOneCondition, $arOneCondition, $arControl, false);
	}

	function Generate($arOneCondition, $arParams, $arControl, $arSubs = false)
	{
		$strResult = '';
		$boolError = false;

		if (is_string($arControl))
		{
			$arControl = self::GetControls($arControl);
		}
		$boolError = !is_array($arControl);

		if (!$boolError)
		{
			$arValues = self::Check($arOneCondition, $arOneCondition, $arControl, false);
			if (false === $arValues)
			{
				$boolError = true;
			}
		}

		if (!$boolError)
		{
			$arLogic = self::SearchLogic($arValues['logic'], $arControl['LOGIC']);
			if (!isset($arLogic['OP'][$arControl['MULTIPLE']]) || empty($arLogic['OP'][$arControl['MULTIPLE']]))
			{
				$boolError = true;
			}
			else
			{
				$strField = $arParams['FIELD'].'[\''.$arControl['FIELD'].'\']';
				switch ($arControl['FIELD_TYPE'])
				{
					case 'int':
					case 'double':
						$strResult = str_replace(array('#FIELD#', '#VALUE#'), array($strField, $arValues['value']), $arLogic['OP'][$arControl['MULTIPLE']]);
						break;
					case 'char':
					case 'string':
					case 'text':
						$strResult = str_replace(array('#FIELD#', '#VALUE#'), array($strField, '"'.EscapePHPString($arValues['value']).'"'), $arLogic['OP'][$arControl['MULTIPLE']]);
						break;
					case 'date':
					case 'datetime':
						$strResult = str_replace(array('#FIELD#', '#VALUE#'), array($strField, '"'.EscapePHPString($arValues['value']).'"'), $arLogic['OP'][$arControl['MULTIPLE']]);
						break;
				}
			}
		}

		return (!$boolError ? $strResult : false);
	}
}

class CCatalogCondCtrlIBlockProps extends CCatalogCondCtrlComplex
{
	public function __construct()
	{
		parent::__construct();
	}

	public function __destruct()
	{
		parent::__destruct();
	}

	function GetControls($strControlID = false)
	{
		$arControlList = array();
		$arIBlockList = array();
		$rsIBlocks = CCatalog::GetList(array(), array(), false, false, array('IBLOCK_ID', 'PRODUCT_IBLOCK_ID'));
		while ($arIBlock = $rsIBlocks->Fetch())
		{
			$arIBlock['IBLOCK_ID'] = intval($arIBlock['IBLOCK_ID']);
			$arIBlock['PRODUCT_IBLOCK_ID'] = intval($arIBlock['PRODUCT_IBLOCK_ID']);
			if (0 < $arIBlock['IBLOCK_ID'])
				$arIBlockList[] = $arIBlock['IBLOCK_ID'];
			if (0 < $arIBlock['PRODUCT_IBLOCK_ID'])
				$arIBlockList[] = $arIBlock['PRODUCT_IBLOCK_ID'];
		}
		if (!empty($arIBlockList))
		{
			$arIBlockList = array_values(array_unique($arIBlockList));
			foreach ($arIBlockList as &$intIBlockID)
			{
				$strName = CIBlock::GetArrayByID($intIBlockID, 'NAME');
				if (false !== $strName)
				{
					$boolSep = true;
					$rsProps = CIBlockProperty::GetList(array('SORT' => 'ASC', 'NAME' => 'ASC'), array('IBLOCK_ID' => $intIBlockID, '!XML_ID' => 'CML2_LINK'));
					while ($arProp = $rsProps->Fetch())
					{
						if ('F' == $arProp['PROPERTY_TYPE'])
							continue;
						if ('L' == $arProp['PROPERTY_TYPE'])
						{
							$arProp['VALUES'] = array();
							$rsPropEnums = CIBlockPropertyEnum::GetList(array('DEF' => 'DESC', 'SORT' => 'ASC'), array('PROPERTY_ID' => $arProp['ID']));
							while ($arPropEnum = $rsPropEnums->Fetch())
							{
								$arProp['VALUES'][] = $arPropEnum;
							}
							if (empty($arProp['VALUES']))
								continue;
						}

						$strFieldType = '';
						$arLogic = array();
						$arValue = array();
						$arPhpValue = '';

						switch ($arProp['PROPERTY_TYPE'])
						{
							case 'N':
								$strFieldType = 'double';
								$arLogic = self::GetLogic(array(BT_COND_LOGIC_EQ, BT_COND_LOGIC_NOT_EQ, BT_COND_LOGIC_GR, BT_COND_LOGIC_LS, BT_COND_LOGIC_EGR, BT_COND_LOGIC_ELS));
								$arValue = array('type' => 'input');
								break;
							case 'S':
								$strFieldType = 'text';
								$arLogic = self::GetLogic(array(BT_COND_LOGIC_EQ, BT_COND_LOGIC_NOT_EQ, BT_COND_LOGIC_CONT, BT_COND_LOGIC_NOT_CONT));
								$arValue = array('type' => 'input');
								break;
							case 'L':
								$strFieldType = 'int';
								$arLogic = self::GetLogic(array(BT_COND_LOGIC_EQ, BT_COND_LOGIC_NOT_EQ));
								$arValue = array(
									'type' => 'select',
									'values' => array(),
								);
								foreach ($arProp['VALUES'] as &$arOnePropValue)
								{
									$arValue['values'][$arOnePropValue['ID']] = $arOnePropValue['VALUE'];
								}
								if (isset($arOnePropValue))
									unset($arOnePropValue);
								break;
								$arPhpValue = array('VALIDATE' => 'list');
							case 'E':
								$strFieldType = 'int';
								$arLogic = self::GetLogic(array(BT_COND_LOGIC_EQ, BT_COND_LOGIC_NOT_EQ));
								$arValue = array(
									'type' => 'popup',
									'popup_url' =>  '/bitrix/admin/iblock_element_search.php',
									'popup_params' => array(
										'lang' => LANGUAGE_ID,
										'IBLOCK_ID' => $arProp['LINK_IBLOCK_ID']
									),
									'param_id' => 'n',
								);
								$arPhpValue = array('VALIDATE' => 'element');
								break;
							case 'G':
								$strFieldType = 'int';
								$arLogic = self::GetLogic(array(BT_COND_LOGIC_EQ, BT_COND_LOGIC_NOT_EQ));
								$arValue = array(
									'type' => 'popup',
									'popup_url' =>  '/bitrix/admin/cat_section_search.php',
									'popup_params' => array(
										'lang' => LANGUAGE_ID,
										'IBLOCK_ID' => $arProp['LINK_IBLOCK_ID']
									),
									'param_id' => 'n',
								);
								$arPhpValue = array('VALIDATE' => 'section');
								break;
						}
						$arControlList["CondIBProp:".$intIBlockID.':'.$arProp['ID']] = array(
							"ID" => "CondIBProp:".$intIBlockID.':'.$arProp['ID'],
							"IBLOCK_ID" => $intIBlockID,
							"FIELD" => "PROPERTY_".$arProp['ID']."_VALUE",
							"FIELD_TYPE" => $strFieldType,
							'MULTIPLE' => 'Y',
							'GROUP' => 'N',
							'SEP' => ($boolSep ? 'Y' : 'N'),
							'SEP_LABEL' => ($boolSep ? str_replace(array('#ID#', '#NAME#'), array($intIBlockID, $strName), GetMessage('BT_MOD_CATALOG_COND_CMP_CATALOG_PROP_LABEL')) : ''),
							'LABEL' => $arProp['NAME'],
							'PREFIX' => str_replace(array('#NAME#', '#IBLOCK_ID#', '#IBLOCK_NAME#'), array($arProp['NAME'], $intIBlockID, $strName), GetMessage('BT_MOD_CATALOG_COND_CMP_CATALOG_ONE_PROP_PREFIX')),
							'LOGIC' => $arLogic,
							'JS_VALUE' => $arValue,
							'PHP_VALUE' => $arPhpValue,
						);

						$boolSep = false;
					}
				}
			}
			if (isset($intIBlockID))
				unset($intIBlockID);
		}

		if (false === $strControlID)
		{
			return $arControlList;
		}
		elseif (array_key_exists($strControlID, $arControlList))
		{
			return $arControlList[$strControlID];
		}
		else
		{
			return false;
		}
	}

	function GetControlDescr()
	{
		return array(
			'COMPLEX' => 'Y',
			"GetControlShow" => array(__CLASS__, "GetControlShow"),
			"GetConditionShow" => array(__CLASS__, "GetConditionShow"),
			"IsGroup" => array(__CLASS__, "IsGroup"),
			"Parse" => array(__CLASS__, "Parse"),
			"Generate" => array(__CLASS__, "Generate"),
			'CONTROLS' => self::GetControls(),
		);
	}

	function GetControlShow($arParams)
	{
		$arControls = self::GetControls();
		$arResult = array();
		$intCount = -1;
		foreach ($arControls as &$arOneControl)
		{
			if (isset($arOneControl['SEP']) && 'Y' == $arOneControl['SEP'])
			{
				$intCount++;
				$arResult[$intCount] = array(
					'controlgroup' => true,
					'group' =>  false,
					'label' => $arOneControl['SEP_LABEL'],
					'showIn' => array_values($arParams['SHOW_IN_GROUPS']),
					'children' => array()
				);
			}
			$arLogic = self::GetLogicAtom($arOneControl['LOGIC']);
			$arValue = self::GetValueAtom($arOneControl['JS_VALUE']);

			$arResult[$intCount]['children'][] = array(
				'controlId' => $arOneControl['ID'],
				'group' => false,
				'label' => $arOneControl['LABEL'],
				'showIn' => array_values($arParams['SHOW_IN_GROUPS']),
				'control' => array(
					$arOneControl['PREFIX'],
					$arLogic,
					$arValue,
				),
			);
		}
		if (isset($arOneControl))
			unset($arOneControl);

		return $arResult;
	}

	function GetConditionShow($arParams)
	{
		if (!isset($arParams['ID']))
			return false;
		$arControl = self::GetControls($arParams['ID']);
		if (false === $arControl)
			return false;
		return self::Check($arParams['DATA'], $arParams, $arControl, true);
	}

	function Parse($arOneCondition)
	{
		if (!isset($arOneCondition['controlId']))
			return false;
		$arControl = self::GetControls($arOneCondition['controlId']);
		if (false === $arControl)
			return false;
		return self::Check($arOneCondition, $arOneCondition, $arControl, false);
	}

	function Generate($arOneCondition, $arParams, $arControl, $arSubs = false)
	{
		$strResult = '';
		$boolError = false;

		if (is_string($arControl))
		{
			$arControl = self::GetControls($arControl);
		}
		$boolError = !is_array($arControl);

		if (!$boolError)
		{
			$arValues = self::Check($arOneCondition, $arOneCondition, $arControl, false);
			if (false === $arValues)
			{
				$boolError = true;
			}
		}

		if (!$boolError)
		{
			$arLogic = self::SearchLogic($arValues['logic'], $arControl['LOGIC']);
			if (!isset($arLogic['OP'][$arControl['MULTIPLE']]) || empty($arLogic['OP'][$arControl['MULTIPLE']]))
			{
				$boolError = true;
			}
			else
			{
				$strField = $arParams['FIELD'].'[\''.$arControl['FIELD'].'\']';
				switch ($arControl['FIELD_TYPE'])
				{
					case 'int':
					case 'double':
						$strResult = str_replace(array('#FIELD#', '#VALUE#'), array($strField, $arValues['value']), $arLogic['OP'][$arControl['MULTIPLE']]);
						break;
					case 'char':
					case 'string':
					case 'text':
						$strResult = str_replace(array('#FIELD#', '#VALUE#'), array($strField, '"'.EscapePHPString($arValues['value']).'"'), $arLogic['OP'][$arControl['MULTIPLE']]);
						break;
					case 'date':
					case 'datetime':
						$strResult = str_replace(array('#FIELD#', '#VALUE#'), array($strField, '"'.EscapePHPString($arValues['value']).'"'), $arLogic['OP'][$arControl['MULTIPLE']]);
						break;
				}

				if ('' != $strResult)
				{
					$strResult = $arParams['FIELD'].'[\'IBLOCK_ID\'] == '.intval($arControl['IBLOCK_ID']).' && isset('.$strField.') && '.$strResult;
				}
			}
		}

		return (!$boolError ? $strResult : false);
	}
}

class CCatalogCondTree
{
	protected $intMode = BT_COND_MODE_DEFAULT;			// work mode
	protected $arEvents = array();						// events ID
	protected $arInitParams = array();					// start params
	protected $boolError = false;						// error flag
	protected $arMsg = array();							// messages (errors)

	protected $strFormName = '';						// form name
	protected $strFormID = '';							// form id
	protected $strContID = '';							// container id
	protected $strJSName = '';							// js object var name
	protected $boolCreateForm = false;					// need create form
	protected $boolCreateCont = false;					// need create container
	protected $strPrefix = 'rule';						// prefix for input
	protected $strSepID = '__';							// separator for id

	private $arAtomList = null;					// atom list cache
	private $arAtomJSPath = null;				// atom js files
	private $arControlList = null;				// control list cache
	private $arShowControlList = null;			// control show method list
	private $arShowInGroups = null;				// showin group list

	private $arConditions = null;				// conditions array

	public function __construct()
	{
		$arTreeDescr = array(
			'js' => '/bitrix/js/catalog/core_tree.js',
			'css' => '/bitrix/themes/.default/catalog_cond.css',
			'lang' => '/bitrix/modules/catalog/lang/'.LANGUAGE_ID.'/js_core_tree.php',
			//'rel' => array('popup', 'ajax', 'fx', 'ls', 'date', 'scroll')
		);

		CJSCore::RegisterExt('cat_tree', $arTreeDescr);

		CUtil::InitJSCore(array("cat_tree"));
	}

	public function __destruct()
	{

	}

	public function OnConditionAtomBuildList()
	{
		if (!$this->boolError)
		{
			if (!isset($this->arAtomList))
			{
				$this->arAtomList = array();
				$this->arAtomJSPath = array();
				foreach (GetModuleEvents($this->arEvents['ATOMS']['MODULE_ID'], $this->arEvents['ATOMS']['EVENT_ID'], true) as $arEvent)
				{
					$arRes = ExecuteModuleEventEx($arEvent);
					$this->arAtomList[$arRes["ID"]] = $arRes;
					if (!empty($arRes['JS_SRC']))
					{
						if (!in_array($arRes['JS_SRC'], $this->arAtomJSPath))
							$this->arAtomJSPath[] = $arRes['JS_SRC'];
					}
				}
			}
		}
	}

	public function OnConditionControlBuildList()
	{
		global $APPLICATIONS;
		if (!$this->boolError)
		{
			if (!isset($this->arControlList))
			{
				$this->arControlList = array();
				$this->arShowInGroups = array();
				$this->arShowControlList = array();
				foreach (GetModuleEvents($this->arEvents['CONTROLS']['MODULE_ID'], $this->arEvents['CONTROLS']['EVENT_ID'], true) as $arEvent)
				{
					$arRes = ExecuteModuleEventEx($arEvent);
					if (!is_array($arRes))
						continue;
					if (isset($arRes['ID']))
					{
						$arRes['GROUP'] = (isset($arRes['GROUP']) && 'Y' == $arRes['GROUP'] ? 'Y' : 'N');
						$this->arControlList[$arRes["ID"]] = $arRes;
						if ('Y' == $arRes['GROUP'])
							$this->arShowInGroups[] = $arRes["ID"];
						if (array_key_exists('GetControlShow', $arRes) && !empty($arRes['GetControlShow']))
						{
							if (!in_array($arRes['GetControlShow'], $this->arShowControlList))
								$this->arShowControlList[] = $arRes['GetControlShow'];
						}
					}
					elseif (isset($arRes['COMPLEX']) && 'Y' == $arRes['COMPLEX'])
					{
						if (isset($arRes['CONTROLS']) && is_array($arRes['CONTROLS']) && !empty($arRes['CONTROLS']))
						{
							$arInfo = $arRes;
							unset($arInfo['COMPLEX']);
							unset($arInfo['CONTROLS']);
							foreach ($arRes['CONTROLS'] as &$arOneControl)
							{
								if (isset($arOneControl['ID']))
								{
									$arInfo['ID'] = $arOneControl['ID'];
									$arInfo['GROUP'] = 'N';
									$this->arControlList[$arInfo['ID']] = $arInfo;
								}
							}
							if (isset($arOneControl))
								unset($arOneControl);
							if (array_key_exists('GetControlShow', $arRes) && !empty($arRes['GetControlShow']))
							{
								if (!in_array($arRes['GetControlShow'], $this->arShowControlList))
									$this->arShowControlList[] = $arRes['GetControlShow'];
							}
						}
					}
					else
					{
						foreach ($arRes as &$arOneRes)
						{
							if (is_array($arOneRes) && isset($arOneRes['ID']))
							{
								$arOneRes['GROUP'] = (isset($arOneRes['GROUP']) && 'Y' == $arOneRes['GROUP'] ? 'Y' : 'N');
								$this->arControlList[$arOneRes["ID"]] = $arOneRes;
								if ('Y' == $arOneRes['GROUP'])
									$this->arShowInGroups[] = $arOneRes["ID"];
								if (array_key_exists('GetControlShow', $arOneRes) && !empty($arOneRes['GetControlShow']))
								{
									if (!in_array($arOneRes['GetControlShow'], $this->arShowControlList))
										$this->arShowControlList[] = $arOneRes['GetControlShow'];
								}
							}
						}
						if (isset($arOneRes))
							unset($arOneRes);
					}
				}
				if (empty($this->arControlList))
				{
					$this->arMsg[] = array('id' => 'CONTROLS', text => 'BT_MOD_COND_ERR_CONTROLS_EMPTY');
					$this->boolError = true;
				}
			}
		}
	}

	protected function GetModeList()
	{
		return array(
			BT_COND_MODE_DEFAULT,
			BT_COND_MODE_PARSE,
			BT_COND_MODE_GENERATE,
			BT_COND_MODE_SQL
		);
	}

	protected function GetEventList($intEventID)
	{
		$arEventList = array(
			BT_COND_BUILD_CATALOG => array(
				'ATOMS' => array(
					'MODULE_ID' => 'catalog',
					'EVENT_ID' => 'OnCondCatAtomBuildList'
				),
				'CONTROLS' => array(
					'MODULE_ID' => 'catalog',
					'EVENT_ID' => 'OnCondCatControlBuildList'
				),
			),
			BT_COND_BUILD_SALE => array(
				'ATOMS' => array(
					'MODULE_ID' => 'sale',
					'EVENT_ID' => 'OnCondSaleAtomBuildList'
				),
				'CONTROLS' => array(
					'MODULE_ID' => 'sale',
					'EVENT_ID' => 'OnCondSaleControlBuildList'
				),
			),
		);

		return (isset($arEventList[$intEventID]) ? $arEventList[$intEventID] : false);
	}

	protected function CheckEvent($arEvent)
	{
		if (!is_array($arEvent))
			return false;
		if (!isset($arEvent['MODULE_ID']) || empty($arEvent['MODULE_ID']) || !is_string($arEvent['MODULE_ID']))
			return false;
		if (!isset($arEvent['EVENT_ID']) || empty($arEvent['EVENT_ID']) || !is_string($arEvent['EVENT_ID']))
			return false;
		return true;
	}

	public function Init($intMode, $mxEvent, $arParams = array())
	{
		global $APPLICATION;
		$this->arMsg = array();

		$intMode = intval($intMode);
		if (!in_array($intMode, self::GetModeList()))
			$intMode = BT_COND_MODE_DEFAULT;
		$this->intMode = $intMode;

		$arEvent = false;
		if (is_array($mxEvent))
		{
			if (isset($mxEvent['CONTROLS']) && self::CheckEvent($mxEvent['CONTROLS']))
			{
				$arEvent['CONTROLS'] = $mxEvent['CONTROLS'];
			}
			else
			{
				$this->boolError = true;
				$this->arMsg[] = array('id' => 'EVENT','text' => GetMessage('BT_MOD_COND_ERR_EVENT_BAD'));
			}
			if (isset($mxEvent['ATOMS']) && self::CheckEvent($mxEvent['ATOMS']))
			{
				$arEvent['ATOMS'] = $mxEvent['ATOMS'];
			}
			else
			{
				$this->boolError = true;
				$this->arMsg[] = array('id' => 'EVENT','text' => GetMessage('BT_MOD_COND_ERR_EVENT_BAD'));
			}
		}
		else
		{
			$mxEvent = intval($mxEvent);
			if (0 <= $mxEvent)
			{
				$arEvent = self::GetEventList($mxEvent);
			}
		}
		if (false === $arEvent)
		{
			$this->boolError = true;
			$this->arMsg[] = array('id' => 'EVENT','text' => GetMessage('BT_MOD_COND_ERR_EVENT_BAD').'333'.mydump($arEvent).mydump($mxEvent));
		}
		else
		{
			$this->arEvents = $arEvent;
		}

		$this->arInitParams = $arParams;

		if (BT_COND_MODE_DEFAULT == $this->intMode)
		{
			if (is_array($arParams) && !empty($arParams))
			{
				if (isset($arParams['FORM_NAME']) && !empty($arParams['FORM_NAME']))
					$this->strFormName = $arParams['FORM_NAME'];
				if (isset($arParams['FORM_ID']) && !empty($arParams['FORM_ID']))
					$this->strFormID = $arParams['FORM_ID'];
				if (isset($arParams['CONT_ID']) && !empty($arParams['CONT_ID']))
					$this->strContID = $arParams['CONT_ID'];
				if (isset($arParams['JS_NAME']) && !empty($arParams['JS_NAME']))
					$this->strJSName = $arParams['JS_NAME'];

				$this->boolCreateForm = (isset($arParams['CREATE_FORM']) && 'Y' == $arParams['CREATE_FORM']);
				$this->boolCreateCont = (isset($arParams['CREATE_CONT']) && 'Y' == $arParams['CREATE_CONT']);
			}

			if (empty($this->strJSName))
			{
				if (empty($this->strContID))
				{
					$this->boolError = true;
					$this->arMsg[] = array('id' => 'JS_NAME','text' => GetMessage('BT_MOD_COND_ERR_JS_NAME_BAD'));
				}
				else
				{
					$this->strJSName = md5($this->strContID);
				}
			}
		}
		if (BT_COND_MODE_DEFAULT == $this->intMode || BT_COND_MODE_PARSE == $this->intMode)
		{
			if (is_array($arParams) && !empty($arParams))
			{
				if (isset($arParams['PREFIX']) && !empty($arParams['PREFIX']))
					$this->strPrefix = $arParams['PREFIX'];
				if (isset($arParams['SEP_ID']) && !empty($arParams['SEP_ID']))
					$this->strSepID = $arParams['SEP_ID'];
			}
		}

		self::OnConditionAtomBuildList();
		self::OnConditionControlBuildList();

		if ($this->boolError)
		{
			$obError = new CAdminException($this->arMsg);
			$APPLICATION->ThrowException($obError);
		}
		else
		{
			return true;
		}
	}

	public function Show($arConditions)
	{
		global $APPLICATION;
		$this->arMsg = array();

		if (!$this->boolError)
		{
			if (!empty($arConditions))
			{
				if (!is_array($arConditions))
				{
					if (!CheckSerializedData($arConditions))
					{
						$this->boolError = true;
						$this->arMsg[] = array('id' => '', 'text' => '');
					}
					else
					{
						$arConditions = unserialize($arConditions);
						if (!is_array($arConditions))
						{
							$this->boolError = true;
							$this->arMsg[] = array('id' => '', 'text' => '');
						}
					}
				}
			}
		}

		if (!$this->boolError)
		{
			$this->arConditions = (!empty($arConditions) ? $arConditions : self::GetDefaultConditions());

			$strResult = '';

			self::ShowScripts();

			if ($this->boolCreateForm)
			{

			}
			if ($this->boolCreateCont)
			{

			}

			$strResult .= '<script type="text/javascript">'."\n";
			$strResult .= 'var '.$this->strJSName.' = new BX.TreeConditions('."\n";
			$strResult .= self::ShowParams().",\n";
			$strResult .= self::ShowConditions().",\n";
			$strResult .= self::ShowControls()."\n";

			$strResult .= ');'."\n";
			$strResult .= '</script>'."\n";

			if ($this->boolCreateCont)
			{

			}
			if ($this->boolCreateForm)
			{

			}

			echo $strResult;
		}
	}

	public function GetDefaultConditions()
	{
		return array(
			'CLASS_ID' => 'CondGroup',
			'DATA' => array('All' => 'AND', 'True' => 'True'),
			'CHILDREN' => array(),
		);
	}

	public function Parse($arData = '', $arParams = false)
	{
		global $APPLICATION;
		$this->arMsg = array();

		if (!$this->boolError)
		{
			if (empty($arData) || !is_array($arData))
			{
				if (isset($_POST[$this->strPrefix]) && is_array($_POST[$this->strPrefix]) && !empty($_POST[$this->strPrefix]))
				{
					$arData = $_POST[$this->strPrefix];
				}
				else
				{
					$this->boolError = true;
					$this->arMsg[] = array('id' => 'CONDITIONS', 'text' => GetMessage('BT_MOD_COND_ERR_PARSE_DATA_EMPTY'));
				}
			}
		}

		if (!$this->boolError)
		{
			$arResult = array();
			foreach ($arData as $strKey => $value)
			{
				$arKeys = self::__ConvertKey($strKey);
				if (empty($arKeys))
				{
					$this->boolError = true;
					$this->arMsg[] = array('id' => 'CONDITIONS', 'text' => GetMessage('BT_MOD_COND_ERR_PARSE_DATA_BAD_KEY'));
					break;
				}

				if (!isset($value['controlId']) || empty($value['controlId']))
				{
					$this->boolError = true;
					$this->arMsg[] = array('id' => 'CONDITIONS', 'text' => GetMessage('BT_MOD_COND_ERR_PARSE_DATA_EMPTY_CONTROLID'));
					break;
				}

				if (!array_key_exists($value['controlId'], $this->arControlList))
				{
					$this->boolError = true;
					$this->arMsg[] = array('id' => 'CONDITIONS', 'text' => GetMessage('BT_MOD_COND_ERR_PARSE_DATA_BAD_CONTROLID'));
					break;
				}

				if (!array_key_exists('Parse', $this->arControlList[$value['controlId']]))
				{
					$this->boolError = true;
					$this->arMsg[] = array('id' => 'CONDITIONS', 'text' => GetMessage('BT_MOD_COND_ERR_PARSE_DATA_CONTROL_PARSE_ABSENT'));
					break;
				}

				$arOneCondition = call_user_func_array($this->arControlList[$value['controlId']]['Parse'],
					array(
						$value
					)
				);
				if (false === $arOneCondition)
				{
					$this->boolError = true;
					$this->arMsg[] = array('id' => 'CONDITIONS', 'text' => GetMessage('BT_MOD_COND_ERR_PARSE_DATA_CONTROL_BAD_VALUE'));
					break;
				}

				$arItem = array(
					'CLASS_ID' => $value['controlId'],
					'DATA' => $arOneCondition
				);
				if (!self::__SetCondition($arResult, $arKeys, 0, $arItem))
				{
					$this->boolError = true;
					$this->arMsg[] = array('id' => 'CONDITIONS', 'text' => GetMessage('BT_MOD_COND_ERR_PARSE_DATA_DOUBLE_KEY'));
					break;
				}
			}
		}

		if ($this->boolError)
		{
			$obError = new CAdminException($this->arMsg);
			$APPLICATION->ThrowException($obError);
			return '';
		}
		else
		{
			return $arResult;
		}
	}

	public function ShowScripts()
	{
		if (!$this->boolError)
		{
			self::ShowAtoms();
		}
	}

	public function ShowAtoms()
	{
		global $APPLICATION;

		if (!$this->boolError)
		{
			if (!isset($this->arAtomList))
			{
				self::OnConditionAtomBuildList();
			}
			if (isset($this->arAtomJSPath) && !empty($this->arAtomJSPath))
			{
				foreach ($this->arAtomJSPath as &$strJSPath)
				{
					$APPLICATION->AddHeadScript($strJSPath);
				}
				if (isset($strJSPath))
					unset($strJSPath);
			}
		}
	}

	public function ShowParams()
	{
		if (!$this->boolError)
		{
			$arParams = array(
				'parentContainer' => $this->strContID,
				'form' => $this->strFormID,
				'formName' => $this->strFormName,
				'sepID' => $this->strSepID,
				'prefix' => $this->strPrefix,
			);

			return CUtil::PhpToJSObject($arParams);
		}
		else
		{
			return '';
		}
	}

	public function ShowControls()
	{
		if (!$this->boolError)
		{
			$arResult = array();
			if (isset($this->arShowControlList))
			{
				foreach ($this->arShowControlList as &$arOneControl)
				{
					$arShowControl = call_user_func_array($arOneControl,
						array(
							array(
								'SHOW_IN_GROUPS' => $this->arShowInGroups
							)
						)
					);
					if (is_array($arShowControl) && !empty($arShowControl))
					{
						if (isset($arShowControl['controlId']) || isset($arShowControl['controlgroup']))
						{
							$arResult[] = $arShowControl;
						}
						else
						{
							$arResult = array_merge($arResult, $arShowControl);
						}

					}
				}
				if (isset($arOneControl))
					unset($arOneControl);
			}

			return CUtil::PhpToJSObject($arResult);
		}
		else
		{
			return '';
		}
	}

	public function ShowLevel(&$arLevel, $boolFirst = false)
	{
		$boolFirst = (true === $boolFirst ? true : false);
		$arResult = array();
		if (empty($arLevel) || !is_array($arLevel))
			return $arResult;
		$intCount = 0;
		if ($boolFirst)
		{
			if (isset($arLevel['CLASS_ID']) && !empty($arLevel['CLASS_ID']))
			{
				if (isset($this->arControlList[$arLevel['CLASS_ID']]))
				{
					$arOneControl = $this->arControlList[$arLevel['CLASS_ID']];
					if (array_key_exists('GetConditionShow', $arOneControl))
					{
						$arParams = array(
							'COND_NUM' => $intCount,
							'DATA' => $arLevel['DATA'],
							'ID' => $arOneControl['ID'],
						);
						$arOneResult = call_user_func_array($arOneControl["GetConditionShow"],
							array(
								$arParams,
							)
						);
						if ('Y' == $arOneControl['GROUP'])
						{
							$arOneResult['children'] = array();
							if (isset($arLevel['CHILDREN']))
								$arOneResult['children'] = self::ShowLevel($arLevel['CHILDREN'], false);
						}
						$arResult[] = $arOneResult;
						$intCount++;
					}
				}
			}
		}
		else
		{
			foreach ($arLevel as &$arOneCondition)
			{
				if (isset($arOneCondition['CLASS_ID']) && !empty($arOneCondition['CLASS_ID']))
				{
					if (isset($this->arControlList[$arOneCondition['CLASS_ID']]))
					{
						$arOneControl = $this->arControlList[$arOneCondition['CLASS_ID']];
						if (array_key_exists('GetConditionShow', $arOneControl))
						{
							$arParams = array(
								'COND_NUM' => $intCount,
								'DATA' => $arOneCondition['DATA'],
								'ID' => $arOneControl['ID'],
							);
							$arOneResult = call_user_func_array($arOneControl["GetConditionShow"],
								array(
									$arParams,
								)
							);
							if ('Y' == $arOneControl['GROUP'] && isset($arOneCondition['CHILDREN']))
							{
								$arOneResult['children'] = self::ShowLevel($arOneCondition['CHILDREN'], false);
							}
							$arResult[] = $arOneResult;
							$intCount++;
						}
					}
				}
			}
			if (isset($arOneCondition))
				unset($arOneCondition);
		}
		return $arResult;
	}

	public function ShowConditions()
	{
		if (!$this->boolError)
		{
			if (empty($this->arConditions))
				$this->arConditions = self::GetDefaultConditions();

			$arResult = self::ShowLevel($this->arConditions, true);

			return CUtil::PhpToJSObject(current($arResult));
		}
		else
		{
			return '';
		}
	}

	public function Generate($arConditions, $arParams)
	{
		if (!$this->boolError)
		{
			$strResult = '';
			if (is_array($arConditions) && !empty($arConditions))
			{
				$arResult = self::GenerateLevel($arConditions, $arParams, true);
				if (false === $arResult || empty($arResult))
				{
					$strResult = '';
					$this->boolError = true;
				}
				else
				{
					$strResult = current($arResult);
				}
			}
			else
			{
				$this->boolError = true;
			}
			return $strResult;
		}
		else
		{
			return '';
		}
	}

	public function GenerateLevel(&$arLevel, $arParams, $boolFirst = false)
	{
		$arResult = array();
		$boolError = false;
		$boolFirst = (true === $boolFirst ? true : false);
		if (!is_array($arLevel) || empty($arLevel))
		{
			return $arResult;
		}
		if ($boolFirst)
		{
			if (isset($arLevel['CLASS_ID']) && !empty($arLevel['CLASS_ID']))
			{
				if (isset($this->arControlList[$arLevel['CLASS_ID']]))
				{
					$arOneControl = $this->arControlList[$arLevel['CLASS_ID']];
					if (array_key_exists('Generate', $arOneControl))
					{
						$strEval = false;
						if (isset($arOneControl['GROUP']) && 'Y' == $arOneControl['GROUP'])
						{
							$arSubEval = self::GenerateLevel($arLevel['CHILDREN'], $arParams);
							if (false === $arSubEval || !is_array($arSubEval))
								return false;
							$strEval = call_user_func_array($arOneControl['Generate'],
								array($arLevel['DATA'], $arParams, $arLevel['CLASS_ID'], $arSubEval)
							);
						}
						else
						{
							$strEval = call_user_func_array($arOneControl['Generate'],
								array($arLevel['DATA'], $arParams, $arLevel['CLASS_ID'])
							);
						}
						if (false === $strEval || !is_string($strEval) || 'false' === $strEval)
							return false;
						$arResult[] = '('.$strEval.')';
					}
				}
			}
		}
		else
		{
			foreach ($arLevel as &$arOneCondition)
			{
				if (isset($arOneCondition['CLASS_ID']) && !empty($arOneCondition['CLASS_ID']))
				{
					if (isset($this->arControlList[$arOneCondition['CLASS_ID']]))
					{
						$arOneControl = $this->arControlList[$arOneCondition['CLASS_ID']];
						if (array_key_exists('Generate', $arOneControl))
						{
							$strEval = false;
							if (isset($arOneControl['GROUP']) && 'Y' == $arOneControl['GROUP'])
							{
								$arSubEval = self::GenerateLevel($arOneCondition['CHILDREN'], $arParams);
								if (false === $arSubEval || !is_array($arSubEval))
									return false;
								$strEval = call_user_func_array($arOneControl['Generate'],
									array($arOneCondition['DATA'], $arParams, $arOneCondition['CLASS_ID'], $arSubEval)
								);
							}
							else
							{
								$strEval = call_user_func_array($arOneControl['Generate'],
									array($arOneCondition['DATA'], $arParams, $arOneCondition['CLASS_ID'])
								);
							}
							if (false === $strEval || !is_string($strEval) || 'false' === $strEval)
								return false;
							$arResult[] = '('.$strEval.')';
						}
					}
				}
			}
			if (isset($arOneCondition))
				unset($arOneCondition);
		}

		if (!empty($arResult))
		{
			foreach ($arResult as $key => $value)
			{
				if (0 >= strlen ($value) || '()' == $value)
					unset($arResult[$key]);
			}
		}
		if (!empty($arResult))
			$arResult = array_values($arResult);

		return $arResult;
	}

	function __ConvertKey($strKey)
	{
		if ('' !== $strKey)
		{
			$arKeys = explode($this->strSepID, $strKey);
			if (is_array($arKeys))
			{
				foreach ($arKeys as &$intOneKey)
				{
					$intOneKey = intval($intOneKey);
				}
			}
			return $arKeys;
		}
		else
		{
			return false;
		}
	}

	function __SetCondition(&$arResult, $arKeys, $intIndex, $arOneCondition)
	{
		if (0 == $intIndex)
		{
			if (1 == sizeof($arKeys))
			{
				$arResult = $arOneCondition;
				return true;
			}
			else
			{
				return self::__SetCondition($arResult, $arKeys, $intIndex + 1, $arOneCondition);
			}
		}
		else
		{
			if (!isset($arResult['CHILDREN']))
			{
				$arResult['CHILDREN'] = array();
			}
			if (!isset($arResult['CHILDREN'][$arKeys[$intIndex]]))
			{
				$arResult['CHILDREN'][$arKeys[$intIndex]] = array();
			}
			if (($intIndex + 1) < sizeof($arKeys))
			{
				return self::__SetCondition($arResult['CHILDREN'][$arKeys[$intIndex]], $arKeys, $intIndex + 1, $arOneCondition);
			}
			else
			{
				if (!empty($arResult['CHILDREN'][$arKeys[$intIndex]]))
				{
					return false;
				}
				else
				{
					$arResult['CHILDREN'][$arKeys[$intIndex]] = $arOneCondition;
					return true;
				}
			}
		}
	}
}
?>