<?php
/**
 * Created by PhpStorm.
 * User: Vampiref92
 * Date: 16.10.2015
 * Time: 15:25
 */

namespace Wizard\Additional;

use Bitrix\Main\Type\DateTime;
use CUser;
use function in_array;
use function is_array;
use function is_object;

/**
 * Class Main
 * @package Wizard\Additional
 */
Class Main
{
	protected static $instance;
	protected $arCurUser = [];
	protected $arCurLanguage = [];
	protected $arCurPage = [];
	
	/**
	 * @return mixed
	 */
	public static function getInstance()
	{
		if (!isset(self::$instance)) {
			$c = __CLASS__;
			self::$instance = new $c;
		}
		
		return self::$instance;
	}
	
	/**
	 *
	 */
	public function setPageLangValues()
	{
		global $APPLICATION;
		
		if ($this->arCurLanguage['IS_NONE_RU']) {
			$arLanguageProps = array(
				'description' . $this->arCurLanguage['POSTFIX_ORIGINAL']    => '',
				'keywords' . $this->arCurLanguage['POSTFIX_ORIGINAL']       => '',
				'title' . $this->arCurLanguage['POSTFIX_ORIGINAL']          => '',
				'keywords_inner' . $this->arCurLanguage['POSTFIX_ORIGINAL'] => '',
				'main_title' . $this->arCurLanguage['POSTFIX_ORIGINAL']     => ''
			);
			
			foreach ($arLanguageProps as $prop_id => $val) {
				$val = $APPLICATION->GetDirProperty($prop_id);
				$base_prop_id = str_replace($this->arCurLanguage['POSTFIX_ORIGINAL'], '', $prop_id);
				if (!empty($val)) {
					if ($base_prop_id == 'main_title') {
						$APPLICATION->SetTitle($val);
					} else {
						$APPLICATION->SetDirProperty($base_prop_id, $val);
					}
					$arLanguageProps[$prop_id] = $val;
				}
				
				$val = $APPLICATION->GetPageProperty($prop_id);
				if (!empty($val)) {
					if ($base_prop_id == 'main_title') {
						$APPLICATION->SetTitle($val);
					} else {
						$APPLICATION->SetPageProperty($base_prop_id, $val);
					}
					$arLanguageProps[$prop_id] = $val;
				}
			}
		}
	}
	
	/**
	 * @param bool $arAdditional
	 */
	public function setCurrentPage($arAdditional = false)
	{
		global $APPLICATION;
		
		$this->arCurPage['DIR'] = $APPLICATION->GetCurDir();
		$this->arCurPage['PAGE'] = $APPLICATION->GetCurPage();
		$this->arCurPage['PAGE_INDEX'] = $APPLICATION->GetCurPage(true);
		$this->arCurPage['IS_INDEX'] = false;
		if ($this->arCurPage['PAGE'] == SITE_DIR && $this->arCurPage['PAGE_INDEX'] == SITE_DIR . 'index.php')
			$this->arCurPage['IS_INDEX'] = true;
		
		if ($arAdditional != false && is_array($arAdditional) && !empty($arAdditional)) {
			foreach ($arAdditional as $arPath) {
				$this->arCurPage[$arPath['NAME']] = false;
				$this->arCurPage[$arPath['NAME'] . '_INDEX'] = false;
				if (false !== strpos($this->arCurPage['PAGE'], $arPath['PATH'])) {
					$this->arCurPage[$arPath['NAME']] = true;
					if (false !== strpos($this->arCurPage['PAGE_INDEX'], $arPath['PATH'] . 'index.php'))
						$this->arCurPage[$arPath['NAME'] . '_INDEX'] = true;
				}
			}
		}
	}
	
	/**
	 *
	 */
	public function setLanguageVars()
	{
		$this->arCurLanguage['IS_NONE_RU'] = false;
		$this->arCurLanguage['POSTFIX'] = '';
		if (LANGUAGE_ID != 'ru') {
			$this->arCurLanguage['IS_NONE_RU'] = true;
			$this->arCurLanguage['POSTFIX'] = '_' . ToUpper(LANGUAGE_ID);
			$this->arCurLanguage['POSTFIX_ORIGINAL'] = '_' . LANGUAGE_ID;
			$this->arCurLanguage['POSTFIX_MINI'] = '_' . ToLower(LANGUAGE_ID);
		}
	}
	
	/**
	 * @param $arUser
	 * @return string
	 */
	public static function getFullName($arUser)
	{
		if (!empty($arUser['LAST_NAME']) && !empty($arUser['NAME']))
			$fullName = $arUser['LAST_NAME'] . ' ' . $arUser['NAME'];
		elseif (!empty($arUser['NAME']) && !empty($arUser['SECOND_NAME']))
			$fullName = $arUser['NAME'] . ' ' . $arUser['SECOND_NAME'];
		elseif (!empty($arUser['NAME']))
			$fullName = $arUser['NAME'];
		else
			$fullName = $arUser['LOGIN'];
		
		return $fullName;
	}
	
	/**
	 * @param $arItems
	 */
	public static function trimArrayStrings(&$arItems)
	{
		if (is_array($arItems)) {
			foreach ($arItems as $key => $val) {
				if (is_array($val)) {
					self::trimArrayStrings($val);
				} else {
					$arItems[$key] = trim(str_replace(' ', '', $val));
				}
			}
		}
	}
	
	/**
	 * @param bool $arGroups
	 * @param bool $bAddUserGroups
	 */
	public function setCurrentUser($arGroups = false, $bAddUserGroups = false)
	{
		global $USER;
		$this->arCurUser = array();
		$this->arCurUser['AUTH'] = $USER->IsAuthorized();
		if ($this->arCurUser['AUTH']) {
			$this->arCurUser['ID'] = CUser::GetID();
			$arFilter = array('ID' => $this->arCurUser['ID']);
			$arUserParams = array(
				'SELECT' => array('UF_MANAGER'),
				'FIELDS' => array('ID', 'LAST_NAME', 'NAME', 'SECOND_NAME', 'LOGIN')
			);
			$arUser = \CUser::GetList($by, $order, $arFilter, $arUserParams)->Fetch();
			$this->arCurUser['MANAGER'] = $arUser['UF_MANAGER'];
			static::trimArrayStrings($arUser);
			$this->arCurUser['FULL_NAME'] = static::getFullName($arUser);
		}
		if ($arGroups != false && is_array($arGroups) && !empty($arGroups)) {
			if ($this->arCurUser['AUTH']) {
				$user_groups = \CUser::GetUserGroupArray();
				if ($bAddUserGroups) {
					$this->arCurUser['GROUPS'] = $user_groups;
				}
				foreach ($arGroups as $arGroup) {
					$this->arCurUser[$arGroup['NAME']] = false;
					if (in_array($arGroup['ID'], $user_groups)) {
						$this->arCurUser[$arGroup['NAME']] = true;
					}
				}
			} else {
				foreach ($arGroups as $arGroup) {
					if ($arGroup['DEFAULT'] == 'Y') {
						$this->arCurUser[$arGroup['NAME']] = true;
						break;
					}
				}
			}
		}
	}
	
	/**
	 * @param $n
	 * @param $forms
	 * @return mixed
	 */
	public static function pluralForm($n, $forms)
	{
		return $n % 10 == 1 && $n % 100 != 11 ? $forms[0] : ($n % 10 >= 2 && $n % 10 <= 4
		&& ($n % 100 < 10
			|| $n % 100 >= 20) ? $forms[1] : $forms[2]);
	}
	
	/**
	 * @param $size
	 * @param int $round
	 * @return string
	 */
	public static function getFormatedSize($size, int $round = 2)
	{
		$sizes = array('B', 'Kb', 'Mb', 'Gb', 'Tb', 'Pb', 'Eb', 'Zb', 'Yb');
		for ($i = 0; $size > 1024 && $i < count($sizes) - 1; $i++) $size /= 1024;
		
		return round($size, $round) . " " . $sizes[$i];
	}
	
	/**
	 * @param $arr
	 */
	public static function eraseArray(&$arr)
	{
		foreach ($arr as $key => $val) {
			if (is_array($val)) {
				self::eraseArray($val);
				if (empty($val)) {
					unset($arr[$key]);
				}
			}
			if (empty($val)) {
				unset($arr[$key]);
			}
		}
	}
	
	/**
	 * @param array $params
	 * @return array|bool|mixed
	 */
	public static function getUniqueArray($params = [])
	{
		if (!isset($params['arr1'])) {
			return false;
		}
		if (!isset($params['arr2'])) {
			return $params['arr1'];
		}
		if (!isset($params['bReturnFullDiffArray'])) {
			$params['bReturnFullDiffArray'] = false;
		}
		if (!isset($params['isChild'])) {
			$params['isChild'] = false;
		}
		if (!isset($params['skipKeys'])) {
			$params['skipKeys'] = [];
		}
		$arResult = [];
		if ($params['bReturnFullDiffArray'] && $params['isChild']) {
			$arTmp = [];
			$arDiff = [];
		}
		foreach ($params['arr1'] as $key => $val) {
			if ($params['bReturnFullDiffArray'] && $params['isChild']) {
				$arTmp[$key] = $val;
			}
			if (is_array($val)) {
				if (!in_array($key, $params['skipKeys'])) {
					if (!isset($params['arr2'][$key]) || (!empty($val) && empty($params['arr2'][$key]))) {
						if ($params['bReturnFullDiffArray'] && $params['isChild']) {
							$arDiff[$key] = $val;
						} else {
							$arResult[$key] = $val;
						}
					} else {
						$arReturn = self::getUniqueArray(
							[
								'arr1'                 => $val,
								'arr2'                 => $params['arr2'][$key],
								'bReturnFullDiffArray' => $params['bReturnFullDiffArray'],
								'skipKeys'             => $params['skipKeys'],
								'isChild'              => true
							]
						);
						if (!empty($arReturn)) {
							if ($params['bReturnFullDiffArray'] && $params['isChild']) {
								$arDiff[$key] = $arReturn;
							} else {
								$arResult[$key] = $arReturn;
							}
						}
					}
				}
			} else {
				if (!in_array($key, $params['skipKeys'])) {
					if (!isset($params['arr2'][$key])) {
						if ($params['bReturnFullDiffArray'] && $params['isChild']) {
							$arDiff[$key] = $val;
						} else {
							$arResult[$key] = $val;
						}
					} else {
						$tmpVal = '0';
						$tmpArr2Val = '1';
						if (is_object($val)) {
							if (is_a($val, 'Bitrix\Main\Type\DateTime')) {
								/** @var DateTime $val */
								$tmpVal = $val->format(DateTime::getFormat());
								/** @var DateTime $val2 */
								$val2 = $params['arr2'][$key];
								$tmpArr2Val = $val2->format(DateTime::getFormat());
								unset($val2);
							}
						}
						if ((!is_object($val) && $val !== $params['arr2'][$key])
							|| (is_object($val) && $tmpVal !== $tmpArr2Val)) {
							if ($params['bReturnFullDiffArray'] && $params['isChild']) {
								$arDiff[$key] = $val;
							} else {
								$arResult[$key] = $val;
							}
						}
					}
				}
			}
		}
		if (isset($arDiff) && count($arDiff) > 0 && isset($arTmp) && !empty($arTmp)) {
			$arResult = $arTmp;
		}
		
		return $arResult;
	}
	
	/**
	 * @return array
	 */
	public function getCurUser()
	{
		return $this->arCurUser;
	}
	
	/**
	 * @return array
	 */
	public function getCurLanguage()
	{
		return $this->arCurLanguage;
	}
	
	/**
	 * @return array
	 */
	public function getCurPage()
	{
		return $this->arCurPage;
	}
}