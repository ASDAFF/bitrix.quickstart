<?php

namespace Api\QA;

use \Bitrix\Main;
use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class Tools
{
	/**
	 * @param string $text
	 * @param bool   $bConvert
	 *
	 * @return string
	 */
	public static function formatText($text = '', $bConvert = false)
	{
		if($bConvert)
			$text = htmlspecialcharsbx($text);

		return (preg_match('/<[\/\!]*?[^<>]*?>/im' . BX_UTF_PCRE_MODIFIER, $text) ? $text : nl2br($text));
	}

	public static function formatParams(&$arParams)
	{
		if($arParams) {
			foreach($arParams as $key => $val) {
				if(isset($arParams[ '~' . $key ])) {
					$arParams[ $key ] = $arParams[ '~' . $key ];
				}
				unset($arParams[ '~' . $key ]);
			}
		}
	}

	/**
	 * @param string $text
	 *
	 * @return string (html|text)
	 */
	public static function getTextType($text = '')
	{
		return (preg_match('/<[\/\!]*?[^<>]*?>/im' . BX_UTF_PCRE_MODIFIER, $text) ? 'html' : 'text');
	}

	/**
	 * Format user name
	 *
	 * @param      $userId
	 * @param bool $bEnableId
	 * @param bool $createEditLink
	 *
	 * @return array|string
	 */
	public static function getAdmFormatedUserName($userId, $bEnableId = true, $createEditLink = true)
	{
		static $formattedUsersName = array();
		static $siteNameFormat = '';

		$result   = (!is_array($userId)) ? '' : array();
		$newUsers = array();

		if(is_array($userId)) {
			foreach($userId as $id) {
				if(!isset($formattedUsersName[ $id ]))
					$newUsers[] = $id;
			}
		}
		else if(!isset($formattedUsersName[ $userId ])) {
			$newUsers[] = $userId;
		}

		if(count($newUsers) > 0) {
			$resUsers = \Bitrix\Main\UserTable::getList(
				 array(
						'select' => array('ID', 'NAME', 'LAST_NAME', 'SECOND_NAME', 'LOGIN', 'EMAIL'),
						'filter' => array('ID' => $newUsers),
				 )
			);
			while($arUser = $resUsers->fetch()) {
				if(strlen($siteNameFormat) == 0)
					$siteNameFormat = \CSite::GetNameFormat(false);
				$formattedUsersName[ $arUser['ID'] ] = \CUser::FormatName($siteNameFormat, $arUser, true, true);
			}
		}

		if(is_array($userId)) {
			foreach($userId as $uId) {
				$formatted = '';
				if($bEnableId)
					$formatted = '[<a href="/bitrix/admin/user_edit.php?ID=' . $uId . '&lang=' . LANGUAGE_ID . '">' . $uId . '</a>] ';

				if(\CBXFeatures::IsFeatureEnabled('SaleAccounts') && !$createEditLink)
					$formatted .= '<a href="/bitrix/admin/sale_buyers_profile.php?USER_ID=' . $uId . '&lang=' . LANGUAGE_ID . '">';
				else
					$formatted .= '<a href="/bitrix/admin/user_edit.php?ID=' . $uId . '&lang=' . LANGUAGE_ID . '">';
				$formatted .= $formattedUsersName[ $uId ];

				$formatted .= '</a>';

				$result[ $uId ] = $formatted;
			}
		}
		else {
			if($bEnableId)
				$result .= '[<a href="/bitrix/admin/user_edit.php?ID=' . $userId . '&lang=' . LANGUAGE_ID . '">' . $userId . '</a>] ';

			if(\CBXFeatures::IsFeatureEnabled('SaleAccounts') && !$createEditLink)
				$result .= '<a href="/bitrix/admin/sale_buyers_profile.php?USER_ID=' . $userId . '&lang=' . LANGUAGE_ID . '">';
			else
				$result .= '<a href="/bitrix/admin/user_edit.php?ID=' . $userId . '&lang=' . LANGUAGE_ID . '">';

			$result .= $formattedUsersName[ $userId ];

			$result .= '</a>';
		}

		return $result;
	}

	/**
	 * @param string $format
	 * @param int    $timestamp
	 *
	 * @return string
	 */
	public static function formatDate($format, $timestamp)
	{
		global $DB;

		switch($format) {
			case "SHORT":
				return FormatDate($DB->DateFormatToPHP(FORMAT_DATE), $timestamp);
			case "FULL":
				return FormatDate($DB->DateFormatToPHP(FORMAT_DATETIME), $timestamp);
			default:
				return FormatDate($format, $timestamp);
		}
	}

	/**
	 * Get either a Gravatar URL or complete image tag for a specified email address.
	 *
	 * @param string $email The email address
	 * @param int    $s     Size in pixels, defaults to 80px [ 1 - 2048 ]
	 * @param string $d     Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
	 * @param string $r     Maximum rating (inclusive) [ g | pg | r | x ]
	 * @param bool   $img   True to return a complete IMG tag False for just the URL
	 * @param array  $atts  Optional, additional key/value attributes to include in the IMG tag
	 *
	 * @return String containing either just a URL or a complete image tag
	 * @source https://gravatar.com/site/implement/images/php/
	 */
	public static function getGravatar($email, $s = 36, $d = 'mm', $r = 'g', $img = false, $atts = array())
	{
		$url = 'https://www.gravatar.com/avatar/';
		$url .= md5(strtolower(trim($email)));
		$url .= "?s=$s&d=$d&r=$r";
		if($img) {
			$url = '<img src="' . $url . '"';
			foreach($atts as $key => $val)
				$url .= ' ' . $key . '="' . $val . '"';
			$url .= ' />';
		}
		return $url;
	}

	/**
	 * @var \CBitrixComponent $ob
	 *
	 * @return string
	 */
	public static function getTemplateFile($ob)
	{
		$templateFile = '';
		if($ob->initComponentTemplate()) {
			$templateFolder = &$ob->getTemplate()->GetFolder();
			$templateFile   = $_SERVER['DOCUMENT_ROOT'] . $templateFolder . '/template.php';
		}

		return $templateFile;
	}

	/**
	 * Format user name
	 *
	 * @param      $userId
	 * @param bool $bEnableId
	 * @param bool $createEditLink
	 *
	 * @return array|string
	 */
	public static function getFormatedUserName($userId, $bEnableId = true, $createEditLink = true)
	{
		static $formattedUsersName = array();
		static $siteNameFormat = '';

		$result   = (!is_array($userId)) ? '' : array();
		$newUsers = array();

		if(is_array($userId)) {
			foreach($userId as $id) {
				if(!isset($formattedUsersName[ $id ]))
					$newUsers[] = $id;
			}
		}
		else if(!isset($formattedUsersName[ $userId ])) {
			$newUsers[] = $userId;
		}

		if(count($newUsers) > 0) {
			$resUsers = \Bitrix\Main\UserTable::getList(
				 array(
						'select' => array('ID', 'NAME', 'LAST_NAME', 'SECOND_NAME', 'LOGIN', 'EMAIL'),
						'filter' => array('ID' => $newUsers),
				 )
			);
			while($arUser = $resUsers->fetch()) {
				if(strlen($siteNameFormat) == 0)
					$siteNameFormat = \CSite::GetNameFormat(false);
				$formattedUsersName[ $arUser['ID'] ] = \CUser::FormatName($siteNameFormat, $arUser, true, true);
			}
		}

		if(is_array($userId)) {
			foreach($userId as $uId) {
				$formatted = '';
				if($bEnableId)
					$formatted = '[<a href="/bitrix/admin/user_edit.php?ID=' . $uId . '&lang=' . LANGUAGE_ID . '">' . $uId . '</a>] ';

				if(\CBXFeatures::IsFeatureEnabled('SaleAccounts') && !$createEditLink)
					$formatted .= '<a href="/bitrix/admin/sale_buyers_profile.php?USER_ID=' . $uId . '&lang=' . LANGUAGE_ID . '">';
				else
					$formatted .= '<a href="/bitrix/admin/user_edit.php?ID=' . $uId . '&lang=' . LANGUAGE_ID . '">';
				$formatted .= $formattedUsersName[ $uId ];

				$formatted .= '</a>';

				$result[ $uId ] = $formatted;
			}
		}
		else {
			if($bEnableId)
				$result .= '[<a href="/bitrix/admin/user_edit.php?ID=' . $userId . '&lang=' . LANGUAGE_ID . '">' . $userId . '</a>] ';

			if(\CBXFeatures::IsFeatureEnabled('SaleAccounts') && !$createEditLink)
				$result .= '<a href="/bitrix/admin/sale_buyers_profile.php?USER_ID=' . $userId . '&lang=' . LANGUAGE_ID . '">';
			else
				$result .= '<a href="/bitrix/admin/user_edit.php?ID=' . $userId . '&lang=' . LANGUAGE_ID . '">';

			$result .= $formattedUsersName[ $userId ];

			$result .= '</a>';
		}

		return $result;
	}

	public static function deleteTree($id)
	{
		@set_time_limit(0);

		// Удаляем текущий комментарий
		QuestionTable::delete($id);

		// Получаем потомки комментария
		$records = QuestionTable::getList(array(
			 'filter' => array('=PARENT_ID' => $id),
			 'select' => array('ID'),
		));

		// Если потомки есть - удаляем рекурсивно
		while($row = $records->fetch())
			static::deleteTree($row['ID']);
	}

	//---------- $arComponentParameters ----------//
	public static function addDateParameters($name, $parent)
	{
		global $DB;

		$timestamp = mktime(7, 30, 45, 2, 22, 2007);
		return array(
			 'PARENT'            => $parent,
			 'NAME'              => $name,
			 'TYPE'              => 'LIST',
			 'SIZE'              => 8,
			 'VALUES'            => array(
					'd-m-Y'       => self::formatDate('d-m-Y', $timestamp),//'22-02-2007',
					'd.m.Y'       => self::formatDate('d.m.Y', $timestamp),//'22.02.2007',
					'd.M.Y'       => self::formatDate('d.M.Y', $timestamp),//'22.Feb.2007',
					'j M Y'       => self::formatDate('j M Y', $timestamp),//'22 Feb 2007',
					'j F Y'       => self::formatDate('j F Y', $timestamp),//'22 February 2007',
					'j F \in H:i' => self::formatDate(Loc::getMessage('AQALT_DATE_FORMAT_NEW'), $timestamp),//'22 February in 7:30',
					'd.m.y G:i'   => self::formatDate('d.m.y G:i', $timestamp),//'22.02.07 7:30',
					'd.m.Y H:i'   => self::formatDate('d.m.Y H:i', $timestamp),//'22.02.2007 07:30',
					'SHORT'       => Loc::getMessage('AQALT_DATE_FORMAT_SHORT'),
					'FULL'        => Loc::getMessage('AQALT_DATE_FORMAT_FULL'),
			 ),
			 'DEFAULT'           => $DB->DateFormatToPHP(\CSite::GetDateFormat('SHORT')),
			 'ADDITIONAL_VALUES' => 'Y',
		);
	}
}