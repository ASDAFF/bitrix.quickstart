<?php

namespace Api\Reviews;

use Bitrix\Main;
use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class ReviewsTable extends Main\Entity\DataManager
{
	public static function getTableName()
	{
		return 'api_reviews';
	}

	public static function getMap()
	{
		/*
			boolean (наследует ScalarField)
			date (наследует ScalarField)
			datetime (наследует DateField)
			enum (наследует ScalarField)
			float (наследует ScalarField)
			integer (наследует ScalarField)
			string (наследует ScalarField)
			text (наследует StringField)
		 */
		return array(
			 'ID'               => array(
					'data_type'    => 'integer',
					'primary'      => true,
					'autocomplete' => true,
					'title'        => Loc::getMessage('ARLR_ID'),
			 ),
			 'ACTIVE'           => array(
					'data_type' => 'boolean',
					'values'    => array('N', 'Y'),
					'title'     => Loc::getMessage('ARLR_ACTIVE'),
			 ),
			 'TIMESTAMP_X'      => array(
					'data_type'     => 'datetime',
					'default_value' => new Main\Type\DateTime(),
					'title'         => Loc::getMessage('ARLR_TIMESTAMP_X'),
			 ),
			 'ACTIVE_FROM'      => array(
					'data_type' => 'datetime',
					'title'     => Loc::getMessage('ARLR_ACTIVE_FROM'),
			 ),
			 'DATE_CREATE'      => array(
					'data_type' => 'datetime',
					'title'     => Loc::getMessage('ARLR_DATE_CREATE'),
			 ),
			 /*'PUBLISH'        => array(
					'data_type' => 'boolean',
					'required'  => true,
					'values'    => array('A', 'U', 'G', 'N'),
					'title'     => Loc::getMessage('ARLR_PUBLISH'),
			 ),*/
			 'RATING'           => array(
					'data_type' => 'integer',
					'format'    => '/^[1-5]{1}$/',
					'title'     => Loc::getMessage('ARLR_RATING'),
			 ),
			 'THUMBS_UP'        => array(
					'data_type'     => 'integer',
					'default_value' => 0,
					'format'        => '/^[0-9]{1,11}$/',
					'title'         => Loc::getMessage('ARLR_THUMBS_UP'),
			 ),
			 'THUMBS_DOWN'      => array(
					'data_type'     => 'integer',
					'default_value' => 0,
					'format'        => '/^[0-9]{1,11}$/',
					'title'         => Loc::getMessage('ARLR_THUMBS_DOWN'),
			 ),
			 'SITE_ID'          => array(
					'data_type'  => 'string',
					'title'      => Loc::getMessage('ARLR_SITE_ID'),
					'validation' => array(__CLASS__, 'validateSiteId'),
					'required'   => true,
			 ),
			 'IBLOCK_ID'        => array(
					'data_type' => 'integer',
					'format'    => '/^[0-9]{1,11}$/',
					'title'     => Loc::getMessage('ARLR_IBLOCK_ID'),
			 ),
			 'SECTION_ID'       => array(
					'data_type' => 'integer',
					'format'    => '/^[0-9]{1,11}$/',
					'title'     => Loc::getMessage('ARLR_SECTION_ID'),
			 ),
			 'ELEMENT_ID'       => array(
					'data_type' => 'integer',
					'format'    => '/^[0-9]{1,11}$/',
					'title'     => Loc::getMessage('ARLR_ELEMENT_ID'),
			 ),
			 'ORDER_ID'         => array(
					'data_type' => 'string',
					'title'     => Loc::getMessage('ARLR_ORDER_ID'),
			 ),
			 'USER_ID'          => array(
					'data_type' => 'integer',
					'format'    => '/^[0-9]{1,11}$/',
					'title'     => Loc::getMessage('ARLR_USER_ID'),
			 ),
			 'URL'              => array(
					'data_type' => 'text',
					'title'     => Loc::getMessage('ARLR_URL'),
			 ),
			 'DELIVERY'         => array(
					'data_type' => 'integer',
					'format'    => '/^[0-9]{1,11}$/',
					'title'     => Loc::getMessage('ARLR_DELIVERY'),
			 ),
			 'CITY'             => array(
					'data_type' => 'string',
					'title'     => Loc::getMessage('ARLR_CITY'),
			 ),
			 'GUEST_NAME'       => array(
					'data_type' => 'string',
					'title'     => Loc::getMessage('ARLR_GUEST_NAME'),
			 ),
			 'GUEST_EMAIL'      => array(
					'data_type' => 'string',
					'title'     => Loc::getMessage('ARLR_GUEST_EMAIL'),
			 ),
			 'GUEST_PHONE'      => array(
					'data_type' => 'string',
					'title'     => Loc::getMessage('ARLR_GUEST_PHONE'),
			 ),
			 'TITLE'            => array(
					'data_type' => 'string',
					'title'     => Loc::getMessage('ARLR_TITLE'),
			 ),
			 'COMPANY'          => array(
					'data_type' => 'string',
					'title'     => Loc::getMessage('ARLR_COMPANY'),
			 ),
			 'WEBSITE'          => array(
					'data_type' => 'string',
					'title'     => Loc::getMessage('ARLR_WEBSITE'),
			 ),
			 'ADVANTAGE'        => array(
					'data_type' => 'text',
					'title'     => Loc::getMessage('ARLR_ADVANTAGE'),
			 ),
			 'DISADVANTAGE'     => array(
					'data_type' => 'text',
					'title'     => Loc::getMessage('ARLR_DISADVANTAGE'),
			 ),
			 'ANNOTATION'       => array(
					'data_type' => 'text',
					'title'     => Loc::getMessage('ARLR_ANNOTATION'),
			 ),
			 'REPLY'            => array(
					'data_type' => 'text',
					'title'     => Loc::getMessage('ARLR_REPLY'),
			 ),
			 'REPLY_SEND'       => array(
					'data_type' => 'boolean',
					'values'    => array('N', 'Y'),
					'title'     => Loc::getMessage('ARLR_REPLY_SEND'),
			 ),
			 'SUBSCRIBE_SEND'   => array(
					'data_type' => 'boolean',
					'values'    => array('N', 'Y'),
					'title'     => Loc::getMessage('ARLR_SUBSCRIBE_SEND'),
			 ),
			 'PAGE_URL'         => array(
					'data_type' => 'text',
					'title'     => Loc::getMessage('ARLR_PAGE_URL'),
			 ),
			 'PAGE_TITLE'       => array(
					'data_type' => 'string',
					'title'     => Loc::getMessage('ARLR_PAGE_TITLE'),
			 ),
			 'EULA_ACCEPTED'    => array(
					'data_type'     => 'boolean',
					'title'         => Loc::getMessage('ARLR_EULA_ACCEPTED'),
					'values'        => array('N', 'Y'),
					'default_value' => 'Y',
			 ),
			 'PRIVACY_ACCEPTED' => array(
					'data_type'     => 'boolean',
					'title'         => Loc::getMessage('ARLR_PRIVACY_ACCEPTED'),
					'values'        => array('N', 'Y'),
					'default_value' => 'Y',
			 ),
			 'FILES'            => array(
					'data_type' => 'text',
					'title'     => Loc::getMessage('ARLR_FILES'),
			 ),
			 'VIDEOS'           => array(
					'data_type' => 'text',
					'title'     => Loc::getMessage('ARLR_VIDEOS'),
			 ),
			 'IP'               => array(
					'data_type' => 'string',
					'title'     => Loc::getMessage('ARLR_IP'),
			 ),
		);
	}

	public static function validateSiteId()
	{
		return array(
			 new Main\Entity\Validator\Length(null, 2),
		);
	}


	/**
	 * @param $ID
	 * @param $VOTE
	 *
	 * @return bool|int
	 */
	public static function addVote($ID, $VOTE)
	{
		global $DB;
		$return = $res = false;
		$ID     = intval($ID);
		$VOTE   = ($VOTE == -1 ? -1 : 1);

		if(!$ID)
			return false;

		$sess_code   = 'API_REVIEWS_VOTE';
		$cookie_code = $sess_code . '_' . $ID;

		if(!is_array($_SESSION[ $sess_code ]))
			$_SESSION[ $sess_code ] = array();

		if(in_array($ID, $_SESSION[ $sess_code ]) || isset($_COOKIE[ $cookie_code ]))
			return false;

		$_SESSION[ $sess_code ][] = $ID;
		setcookie($cookie_code, $VOTE, (time() + 86400 * 365), '/');

		if($VOTE == -1) {
			$strSql = "UPDATE " . self::getTableName() . " SET `THUMBS_DOWN` =  " . $DB->IsNull("THUMBS_DOWN", 0) . " - 1 " . "  WHERE `ID`=" . $ID;
			if($DB->Query($strSql, true, __LINE__)) {
				$strSql = "SELECT `THUMBS_DOWN` FROM " . self::getTableName() . "  WHERE `ID`=" . $ID;
				$res    = $DB->Query($strSql, true, __LINE__);
			}
		}
		else {
			$strSql = "UPDATE " . self::getTableName() . " SET `THUMBS_UP` =  " . $DB->IsNull("THUMBS_UP", 0) . " + 1 " . "  WHERE `ID`=" . $ID;
			if($DB->Query($strSql, true, __LINE__)) {
				$strSql = "SELECT `THUMBS_UP` FROM " . self::getTableName() . "  WHERE `ID`=" . $ID;
				$res    = $DB->Query($strSql, true, __LINE__);
			}
		}

		if($res) {
			$ar_vote = $res->Fetch();

			$return = ($ar_vote['THUMBS_UP'] ? intval($ar_vote['THUMBS_UP']) : intval(trim($ar_vote['THUMBS_DOWN'], '-')));
		}

		return $return;
	}

	public static function setReviewCount($reviewId,$arReview = array())
	{
		///////////////////////////////////////////////////////
		/// Счетчик комментариев
		/// /bitrix/components/bitrix/forum.topic.reviews/action.php:133
		///////////////////////////////////////////////////////

		if(!Main\Loader::includeModule('iblock'))
			return;

		if(empty($reviewId))
			return;

		if(empty($arReview)){
			$arReview = ReviewsTable::getRow(array(
				 'select' => array('ID', 'IBLOCK_ID', 'SECTION_ID', 'ELEMENT_ID', 'SITE_ID'),
				 'filter' => array('=ID' => $reviewId),
			));
		}

		if($arReview) {
			$siteId   = trim($arReview['SITE_ID']);
			$iblockId = intval($arReview['IBLOCK_ID']);

			if($iblockId) {
				$propCountCode = defined('API_REVIEWS_COUNT_PROP') && API_REVIEWS_COUNT_PROP ? API_REVIEWS_COUNT_PROP : 'API_REVIEWS_COUNT';
				$propRatingCode = defined('API_REVIEWS_RATING_PROP') && API_REVIEWS_RATING_PROP ? API_REVIEWS_RATING_PROP : 'API_REVIEWS_RATING';

				//$propCountCode  = 'API_REVIEWS_COUNT';
				//$propRatingCode = 'API_REVIEWS_RATING';

				//СЧЕТЧИК ОТЗЫВОВ ДЛЯ ЭЛЕМЕНТА ИНФОБЛОКА
				if($elementId = intval($arReview['ELEMENT_ID'])) {
					$needProperty = array();

					//1. Сначала проверяем наличие необходимых свойств и создаем при необходимости
					$res = \CIBlockElement::GetProperty($iblockId, $elementId, false, false, array('CODE' => $propCountCode));
					if(!$res->fetch()) {
						$needProperty[] = $propCountCode;
					}
					$res = \CIBlockElement::GetProperty($iblockId, $elementId, false, false, array('CODE' => $propRatingCode));
					if(!$res->fetch()) {
						$needProperty[] = $propRatingCode;
					}

					if(!empty($needProperty)) {
						$obProperty = new \CIBlockProperty;
						foreach($needProperty as $nameProperty) {
							$sName = Loc::getMessage('ARLR_PROP_' . $nameProperty);
							$sName = (empty($sName) ? $nameProperty : $sName);

							$propFields = array(
								 'IBLOCK_ID'     => $iblockId,
								 'ACTIVE'        => 'Y',
								 'PROPERTY_TYPE' => 'N',
								 'MULTIPLE'      => 'N',
								 'SORT'          => 5000,
								 'NAME'          => $sName,
								 'CODE'          => $nameProperty,
							);

							if($obProperty->Add($propFields)) {
								${strToUpper($nameProperty)} = 0;
							}
						}
					}

					/*$API_REVIEWS_COUNT  = ReviewsTable::getCount(array(
						 '=IBLOCK_ID'  => $iblockId,
						 '=ELEMENT_ID' => $elementId,
					));*/

					//2. Потом записываем значения рейтинга и счетчик в свойство
					$arRating           = self::getElementRating($iblockId, $elementId, $siteId);
					$API_REVIEWS_COUNT  = $arRating['COUNT'];
					$API_REVIEWS_RATING = $arRating['RATING'];

					\CIBlockElement::SetPropertyValues($elementId, $iblockId, $API_REVIEWS_COUNT, $propCountCode);
					\CIBlockElement::SetPropertyValues($elementId, $iblockId, $API_REVIEWS_RATING, $propRatingCode);

					\CIBlock::clearIblockTagCache($iblockId);
				}
			}
		}
	}

	public static function getElementRating($IBLOCK_ID, $ELEMENT_ID, $SITE_ID)
	{
		$filter = array(
			 '=ACTIVE'     => 'Y',
			 '=IBLOCK_ID'  => $IBLOCK_ID,
			 '=ELEMENT_ID' => $ELEMENT_ID,
			 '=SITE_ID'    => $SITE_ID,
		);

		$rsRating = ReviewsTable::getList(array(
			 'select' => array('RATING'),
			 'filter' => $filter,
		));

		$rating  = 0;
		$arItems = array();

		while($arItem = $rsRating->fetch()) {
			$rating += intval($arItem['RATING']);

			$arItems[] = $arItem;
		}

		$countItems    = count($arItems);
		$averageRating = ($countItems > 0) ? round(($rating / $countItems), 1) : $countItems;
		$fullRating    = ($countItems > 0) ? round(($rating / $countItems) * 20, 1) : $countItems;

		$arReturn = array(
			 'RATING'  => $averageRating,
			 'PERCENT' => $fullRating . '%',
			 'COUNT'   => $countItems,
		);

		return $arReturn;
	}


	/////////////////////////////////////////////////////////////////////////////
	/// General component actions
	/////////////////////////////////////////////////////////////////////////////



	/////////////////////////////////////////////////////////////////////////////
	/// General module events
	/////////////////////////////////////////////////////////////////////////////

	public static function OnBeforeUpdate(Entity\Event $event)
	{
		$result = new Entity\EventResult();
		$result->modifyFields(array(
			 'TIMESTAMP_X' => new Main\Type\DateTime(),
		));

		return $result;
	}

	public static function OnAfterUpdate(Entity\Event $event)
	{
		$primary = $event->getParameter('primary');
		self::setReviewCount($primary['ID']);
	}

	public static function OnAfterAdd(Entity\Event $event)
	{
		$primary = $event->getParameter('primary');
		self::setReviewCount($primary['ID']);
	}


	//Запоминаем для удаления отзыва и пересчета рейтинга
	protected static $arReview;

	public static function OnAfterDelete(Entity\Event $event)
	{
		$primary = $event->getParameter('primary');

		self::setReviewCount($primary['ID'],self::$arReview);
	}

	public static function OnDelete(Entity\Event $event)
	{
		$primary = $event->getParameter("primary");

		if($id = intval($primary['ID'])) {

			//Получим необходимые поля для удаления файлов, видео и пересчета рейтинга
			$row = self::getRow(array(
				 'select' => array('ID', 'FILES', 'VIDEOS', 'IBLOCK_ID', 'SECTION_ID', 'ELEMENT_ID', 'SITE_ID'),
				 'filter' => array('=ID' => $id),
			));

			self::$arReview = $row;

			//Удаляем файлы отзыва из b_file
			if($row['FILES']) {
				if($arFiles = explode(',', $row['FILES'])) {
					foreach($arFiles as $fileId)
						\CFile::Delete($fileId);
				}
			}

			//Удаляем видео из api_reviews_video и превью видео из b_file
			if($row['VIDEOS']) {
				if($arVideos = explode(',', $row['VIDEOS'])) {
					foreach($arVideos as $videoId) {
						$row2 = VideoTable::getRow(array(
							 'select' => array('FILE_ID'),
							 'filter' => array('=ID' => $videoId),
						));
						if($row2['FILE_ID']) {
							\CFile::Delete($row2['FILE_ID']);
						}
						VideoTable::delete($videoId);
					}
				}
			}
		}
	}
}