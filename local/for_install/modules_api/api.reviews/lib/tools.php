<?php
namespace Api\Reviews;

use \Bitrix\Main;
use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class Tools
{
	/**
	 * @param string $text
	 * @param bool $bConvert
	 *
	 * @return string
	 */
	public static function formatText($text = '', $bConvert = true)
	{
		if($bConvert)
			$text = htmlspecialcharsbx($text);

		return (preg_match('/<[\/\!]*?[^<>]*?>/im' . BX_UTF_PCRE_MODIFIER, $text) ? $text : nl2br($text));
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
	 * @param int $s Size in pixels, defaults to 80px [ 1 - 2048 ]
	 * @param string $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
	 * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
	 * @param bool $img True to return a complete IMG tag False for just the URL
	 * @param array $atts Optional, additional key/value attributes to include in the IMG tag
	 * @return String containing either just a URL or a complete image tag
	 * @source https://gravatar.com/site/implement/images/php/
	 */
	public static function getGravatar( $email, $s = 60, $d = 'mm', $r = 'g', $img = false, $atts = array() ) {
		$url = 'https://www.gravatar.com/avatar/';
		$url .= md5( strtolower( trim( $email ) ) );
		$url .= "?s=$s&d=$d&r=$r";
		if ( $img ) {
			$url = '<img src="' . $url . '"';
			foreach ( $atts as $key => $val )
				$url .= ' ' . $key . '="' . $val . '"';
			$url .= ' />';
		}
		return $url;
	}


	/////////////////////////////////////////////////////////////////////////////
	/// File upload
	/////////////////////////////////////////////////////////////////////////////

	/**
	 * Get file size in bytes form K|M|G
	 *
	 * @param $val
	 *
	 * @return int
	 */
	public static function getFileSizeInBytes($val)
	{
		$val = trim($val);

		if(empty($val))
			return 0;

		preg_match('#([0-9]+)[\s]*([a-z]+)#i', $val, $matches);

		$last = '';
		if(isset($matches[2]))
		{
			$last = $matches[2];
		}

		if(isset($matches[1]))
		{
			$val = (int)$matches[1];
		}

		switch(ToUpper($last))
		{
			case 'T':
			case 'TB':
				$val *= pow(1024, 4);
				break;

			case 'G':
			case 'GB':
				$val *= pow(1024, 3);
				break;

			case 'M':
			case 'MB':
				$val *= pow(1024, 2);
				break;

			case 'K':
			case 'KB':
				$val *= 1024;
				break;

			default:
				$val *= 1;
		}

		return (int)$val;
	}
	/**
	 * Translit cyrillic file name
	 *
	 * @param string $str
	 *
	 * @return string
	 */
	public static function translitFileName($str)
	{
		$str = trim($str);

		$trans_from = explode(",", Loc::getMessage('ARLT_TRANSLIT_FROM'));
		$trans_to   = explode(",", Loc::getMessage('ARLT_TRANSLIT_TO'));

		$str = str_replace($trans_from, $trans_to, $str);

		$str = preg_replace('/\\s+/'. BX_UTF_PCRE_MODIFIER, '_', $str);

		return $str;
	}
	public static function getUniqueFileName($fileName, $propId, $userId){
		return md5(trim($fileName) . bitrix_sessid() . uniqid('api_reviews_',true) . intval($propId) . intval($userId)) . '.' . GetFileExtension($fileName);
	}


	/////////////////////////////////////////////////////////////////////////////
	/// Video upload
	/////////////////////////////////////////////////////////////////////////////
	public static function getVideoUrl($video)
	{
		$url = '#';

		if($video['SERVICE']){
			$videoId = $video['CODE'];
			if($video['SERVICE'] == 'youtube'){
				$url = 'https://www.youtube.com/embed/' . $videoId;
			}
			if($video['SERVICE'] == 'vimeo'){
				$url = 'http://player.vimeo.com/video/' . $videoId;
			}
			if($video['SERVICE'] == 'rutube'){
				$url = 'http://rutube.ru/video/embed/' . $videoId;
			}
		}

		return $url;
	}

	public static function curlExec($url){

		$out = '';
		if($url){
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_FAILONERROR, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			$out = curl_exec($ch);
			curl_close($ch);
		}

		return $out;
	}

	/////////////////////////////////////////////////////////////////////////////
	/// component.php
	/////////////////////////////////////////////////////////////////////////////

	/**
	 * 404 ошибка компонента
	 *
	 * @param string $message        Message to show with bitrix:system.show_message component.
	 * @param bool   $defineConstant If true then ERROR_404 constant defined.
	 * @param bool   $setStatus      If true sets http response status.
	 * @param bool   $showPage       If true then work area will be cleaned and /404.php will be included.
	 * @param string $pageFile       Alternative file to /404.php.
	 *
	 * @return void
	 */
	public static function send404($message = "", $defineConstant = true, $setStatus = true, $showPage = false, $pageFile = "")
	{
		/** @global \CMain $APPLICATION */
		global $APPLICATION;

		if($message <> "") {
			$APPLICATION->includeComponent(
				 "bitrix:system.show_message",
				 ".default",
				 array(
						"MESSAGE" => $message,
						"STYLE"   => "errortext",
				 ),
				 null,
				 array(
						"HIDE_ICONS" => "Y",
				 )
			);
		}

		if($defineConstant && !defined("ERROR_404")) {
			define("ERROR_404", "Y");
		}

		if($setStatus) {
			\CHTTP::SetStatus("404 Not Found");
		}

		if($showPage) {
			if($APPLICATION->RestartWorkarea()) {
				if($pageFile)
					require(\Bitrix\Main\Application::getDocumentRoot() . rel2abs("/", "/" . $pageFile));
				else
					require(\Bitrix\Main\Application::getDocumentRoot() . "/404.php");
				die();
			}
		}
	}

	/**
	 * Обрабатывает шаблоны ЧПУ
	 *
	 * @param $reviewId
	 * @param $template
	 *
	 * @return string
	 */
	public static function makeUrl($reviewId, $template){
		$url = str_replace('#review_id#', $reviewId, $template);
		return $url;
	}


	//---------- $arComponentParameters ----------//
	public static function addPagerParameters(&$arComponentParameters, $pager_title, $bDescNumbering = true, $bShowAllParam = false, $bBaseLink = false, $bBaseLinkEnabled = false)
	{
		$arHiddenTemplates = array(
			 'js' => true,
		);
		if(!isset($arComponentParameters['GROUPS']))
			$arComponentParameters['GROUPS'] = array();
		$arComponentParameters["GROUPS"]["PAGER_SETTINGS"] = array(
			 "NAME" => Loc::getMessage("ARLT_PAGER_SETTINGS"),
		);

		$arTemplateInfo = \CComponentUtil::GetTemplatesList('bitrix:main.pagenavigation');
		if(empty($arTemplateInfo)) {
			$arComponentParameters["PARAMETERS"]["PAGER_TEMPLATE"] = Array(
				 "PARENT"  => "PAGER_SETTINGS",
				 "NAME"    => Loc::getMessage("ARLT_PAGER_TEMPLATE"),
				 "TYPE"    => "STRING",
				 "DEFAULT" => "",
			);
		}
		else {
			sortByColumn($arTemplateInfo, array('TEMPLATE' => SORT_ASC, 'NAME' => SORT_ASC));
			$arTemplateList     = array();
			$arSiteTemplateList = array(
				 '.default' => Loc::getMessage('ARLT_PAGER_TEMPLATE_SITE_DEFAULT'),
			);
			$arTemplateID       = array();
			foreach($arTemplateInfo as &$template) {
				if('' != $template["TEMPLATE"] && '.default' != $template["TEMPLATE"])
					$arTemplateID[] = $template["TEMPLATE"];
				if(!isset($template['TITLE']))
					$template['TITLE'] = $template['NAME'];
			}
			unset($template);

			if(!empty($arTemplateID)) {
				$rsSiteTemplates = \CSiteTemplate::GetList(
					 array(),
					 array("ID" => $arTemplateID),
					 array()
				);
				while($arSitetemplate = $rsSiteTemplates->Fetch()) {
					$arSiteTemplateList[ $arSitetemplate['ID'] ] = $arSitetemplate['NAME'];
				}
			}

			foreach($arTemplateInfo as &$template) {
				if(isset($arHiddenTemplates[ $template['NAME'] ]))
					continue;
				$strDescr                            = $template["TITLE"] . ' (' . ('' != $template["TEMPLATE"] && '' != $arSiteTemplateList[ $template["TEMPLATE"] ] ? $arSiteTemplateList[ $template["TEMPLATE"] ] : Loc::getMessage("ARLT_PAGER_TEMPLATE_SYSTEM")) . ')';
				$arTemplateList[ $template['NAME'] ] = $strDescr;
			}
			unset($template);
			$arComponentParameters["PARAMETERS"]["PAGER_TEMPLATE"] = array(
				 "PARENT"            => "PAGER_SETTINGS",
				 "NAME"              => Loc::getMessage("ARLT_PAGER_TEMPLATE_EXT"),
				 "TYPE"              => "LIST",
				 "VALUES"            => $arTemplateList,
				 "DEFAULT"           => ".default",
				 "ADDITIONAL_VALUES" => "Y",
			);
		}

		$arComponentParameters["PARAMETERS"]["PAGER_THEME"]    = Array(
			 "PARENT"  => "PAGER_SETTINGS",
			 "NAME"    => Loc::getMessage("ARLT_PAGER_THEME"),
			 "TYPE"    => "LIST",
			 "VALUES" => Loc::getMessage("ARLT_PAGER_THEME_VALUES"),
			 "DEFAULT" => array('blue'),
		);
		$arComponentParameters["PARAMETERS"]["DISPLAY_TOP_PAGER"]    = Array(
			 "PARENT"  => "PAGER_SETTINGS",
			 "NAME"    => Loc::getMessage("ARLT_TOP_PAGER"),
			 "TYPE"    => "CHECKBOX",
			 "DEFAULT" => "N",
		);
		$arComponentParameters["PARAMETERS"]["DISPLAY_BOTTOM_PAGER"] = Array(
			 "PARENT"  => "PAGER_SETTINGS",
			 "NAME"    => Loc::getMessage("ARLT_BOTTOM_PAGER"),
			 "TYPE"    => "CHECKBOX",
			 "DEFAULT" => "Y",
		);
		$arComponentParameters["PARAMETERS"]["PAGER_TITLE"]          = Array(
			 "PARENT"  => "PAGER_SETTINGS",
			 "NAME"    => Loc::getMessage("ARLT_PAGER_TITLE"),
			 "TYPE"    => "STRING",
			 "DEFAULT" => $pager_title,
		);
		$arComponentParameters["PARAMETERS"]["PAGER_SHOW_ALWAYS"]    = Array(
			 "PARENT"  => "PAGER_SETTINGS",
			 "NAME"    => Loc::getMessage("ARLT_PAGER_SHOW_ALWAYS"),
			 "TYPE"    => "CHECKBOX",
			 "DEFAULT" => "N",
		);

		if($bDescNumbering) {
			$arComponentParameters["PARAMETERS"]["PAGER_DESC_NUMBERING"]            = Array(
				 "PARENT"  => "PAGER_SETTINGS",
				 "NAME"    => Loc::getMessage("ARLT_PAGER_DESC_NUMBERING"),
				 "TYPE"    => "CHECKBOX",
				 "DEFAULT" => "Y",
			);
			$arComponentParameters["PARAMETERS"]["PAGER_DESC_NUMBERING_CACHE_TIME"] = Array(
				 "PARENT"  => "PAGER_SETTINGS",
				 "NAME"    => Loc::getMessage("ARLT_PAGER_DESC_NUMBERING_CACHE_TIME"),
				 "TYPE"    => "STRING",
				 "DEFAULT" => "31536000",
			);
		}

		if($bShowAllParam) {
			$arComponentParameters["PARAMETERS"]["PAGER_SHOW_ALL"] = Array(
				 "PARENT"  => "PAGER_SETTINGS",
				 "NAME"    => Loc::getMessage("ARLT_SHOW_ALL"),
				 "TYPE"    => "CHECKBOX",
				 "DEFAULT" => "N",
			);
		}

		if($bBaseLink) {
			$arComponentParameters["PARAMETERS"]["PAGER_BASE_LINK_ENABLE"] = Array(
				 "PARENT"  => "PAGER_SETTINGS",
				 "NAME"    => Loc::getMessage("ARLT_BASE_LINK_ENABLE"),
				 "TYPE"    => "CHECKBOX",
				 "REFRESH" => "Y",
				 "DEFAULT" => "N",
			);
			if($bBaseLinkEnabled) {
				$arComponentParameters["PARAMETERS"]["PAGER_BASE_LINK"]   = Array(
					 "PARENT"  => "PAGER_SETTINGS",
					 "NAME"    => Loc::getMessage("ARLT_BASE_LINK"),
					 "TYPE"    => "STRING",
					 "DEFAULT" => "",
				);
				$arComponentParameters["PARAMETERS"]["PAGER_PARAMS_NAME"] = Array(
					 "PARENT"  => "PAGER_SETTINGS",
					 "NAME"    => Loc::getMessage("ARLT_PARAMS_NAME"),
					 "TYPE"    => "STRING",
					 "DEFAULT" => "arrPager",
				);
			}
		}
	}

	public static function add404Parameters(&$arComponentParameters, $arCurrentValues, $bStatus = true, $bPage = true)
	{
		if(!isset($arComponentParameters['GROUPS']))
			$arComponentParameters['GROUPS'] = array();
		$arComponentParameters["GROUPS"]["404_SETTINGS"] = array(
			 "NAME" => Loc::getMessage("ARLT_GROUP_404_SETTINGS"),
		);

		if($bStatus) {
			$arComponentParameters["PARAMETERS"]["SET_STATUS_404"] = array(
				 "PARENT"  => "404_SETTINGS",
				 "NAME"    => Loc::getMessage("ARLT_SET_STATUS_404"),
				 "TYPE"    => "CHECKBOX",
				 "DEFAULT" => "N",
			);
		}

		if($bPage) {
			$arComponentParameters["PARAMETERS"]["SHOW_404"] = array(
				 "PARENT"  => "404_SETTINGS",
				 "NAME"    => Loc::getMessage("ARLT_SHOW_404"),
				 "TYPE"    => "CHECKBOX",
				 "DEFAULT" => "N",
				 "REFRESH" => "Y",
			);
		}

		if($arCurrentValues["SHOW_404"] === "Y") {
			if($bPage) {
				$arComponentParameters["PARAMETERS"]["FILE_404"] = array(
					 "PARENT"  => "404_SETTINGS",
					 "NAME"    => Loc::getMessage("ARLT_FILE_404"),
					 "TYPE"    => "STRING",
					 "DEFAULT" => "",
				);
			}
		}
		else {
			$arComponentParameters["PARAMETERS"]["MESSAGE_404"] = array(
				 "PARENT"  => "404_SETTINGS",
				 "NAME"    => Loc::getMessage("ARLT_MESSAGE_404"),
				 "TYPE"    => "STRING",
				 "DEFAULT" => "",
			);
		}
	}

	public static function addDateParameters($name, $parent)
	{
		global $DB;

		$timestamp = mktime(7, 30, 45, 2, 22, 2007);
		return array(
			 "PARENT"            => $parent,
			 "NAME"              => $name,
			 "TYPE"              => "LIST",
			 "SIZE"              => 8,
			 "VALUES"            => array(
					"d-m-Y"       => self::formatDate("d-m-Y", $timestamp),//"22-02-2007",
					"m-d-Y"       => self::formatDate("m-d-Y", $timestamp),//"02-22-2007",
					"Y-m-d"       => self::formatDate("Y-m-d", $timestamp),//"2007-02-22",
					"d.m.Y"       => self::formatDate("d.m.Y", $timestamp),//"22.02.2007",
					"d.M.Y"       => self::formatDate("d.M.Y", $timestamp),//"22.Feb.2007",
					"m.d.Y"       => self::formatDate("m.d.Y", $timestamp),//"02.22.2007",
					"j M Y"       => self::formatDate("j M Y", $timestamp),//"22 Feb 2007",
					"M j, Y"      => self::formatDate("M j, Y", $timestamp),//"Feb 22, 2007",
					"j F Y"       => self::formatDate("j F Y", $timestamp),//"22 February 2007",
					"f j, Y"      => self::formatDate("f j, Y", $timestamp),//"February 22, 2007",
					"d.m.y g:i A" => self::formatDate("d.m.y g:i A", $timestamp),//"22.02.07 1:30 PM",
					"d.M.y g:i A" => self::formatDate("d.M.y g:i A", $timestamp),//"22.Feb.07 1:30 PM",
					"d.M.Y g:i A" => self::formatDate("d.M.Y g:i A", $timestamp),//"22.Febkate.2007 1:30 PM",
					"d.m.y G:i"   => self::formatDate("d.m.y G:i", $timestamp),//"22.02.07 7:30",
					"d.m.Y H:i"   => self::formatDate("d.m.Y H:i", $timestamp),//"22.02.2007 07:30",
					"SHORT"       => Loc::getMessage('ARLT_DATE_FORMAT_SITE'),
					"FULL"        => Loc::getMessage('ARLT_DATETIME_FORMAT_SITE'),
			 ),
			 "DEFAULT"           => $DB->DateFormatToPHP(\CSite::GetDateFormat("SHORT")),
			 "ADDITIONAL_VALUES" => "Y",
		);
	}

}