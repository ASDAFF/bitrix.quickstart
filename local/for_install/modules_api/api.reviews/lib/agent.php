<?php
namespace Api\Reviews;

use \Bitrix\Main;
use \Bitrix\Main\Type;
use \Bitrix\Main\Type\DateTime;
use \Bitrix\Main\Application;
use \Bitrix\Main\Entity\AddResult;
use \Bitrix\Main\Config\Option;
use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class AgentTable extends Main\Entity\DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'api_reviews_agent';
	}

	/**
	 * Returns entity map definition.
	 *
	 * @return array
	 */
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
			 new Main\Entity\IntegerField('ID', array(
					'primary'      => true,
					'autocomplete' => true,
					'title'        => Loc::getMessage('ID'),
			 )),
			 new Main\Entity\DatetimeField('DATE_INSERT', array(
					'default_value' => new Type\DateTime(),
					'title'         => Loc::getMessage('DATE_INSERT'),
					'required'      => true,
			 )),
			 new Main\Entity\DatetimeField('DATE_EXEC', array(
					'title' => Loc::getMessage('DATE_EXEC'),
			 )),
			 new Main\Entity\IntegerField('REVIEW_ID', array(
					'format' => '/^[0-9]{1,18}$/',
					'title'  => Loc::getMessage('REVIEW_ID'),
			 )),
			 new Main\Entity\IntegerField('SUBSCRIBERS_CNT', array(
					'format' => '/^[0-9]{1,18}$/',
					'title'  => Loc::getMessage('SUBSCRIBERS_CNT'),
			 )),
			 new Main\Entity\IntegerField('SENDMAIL_CNT', array(
					'format' => '/^[0-9]{1,18}$/',
					'title'  => Loc::getMessage('SENDMAIL_CNT'),
			 )),
			 new Main\Entity\StringField('SITE_ID', array(
					'validation' => array(__CLASS__, 'validateSiteId'),
					'title'      => Loc::getMessage('SITE_ID'),
					'required'   => true,
			 )),
		);
	}

	/**
	 * Returns validators for SITE_ID field.
	 *
	 * @return array
	 */
	public static function validateSiteId()
	{
		return array(
			 new Main\Entity\Validator\Length(null, 2),
		);
	}
}

class Agent
{
	/**
	 * @param $reviewId
	 * @param $siteId
	 */
	public static function add($reviewId, $siteId)
	{
		//Проверяем существование агента, чтобы не запустить дважды
		$arAgent = AgentTable::getRow(array(
			 'filter' => array('=REVIEW_ID' => $reviewId),
		));

		if(!$arAgent) {
			$result = AgentTable::add(array(
				 'REVIEW_ID' => $reviewId,
				 'SITE_ID'   => $siteId,
			));

			if($result->isSuccess()) {

				\CAgent::Add(array(
					 'NAME'           => '\\Api\\Reviews\\Agent::sendSubscribe(' . $result->getId() . ');',
					 'MODULE_ID'      => 'api.reviews',
					 'ACTIVE'         => 'Y',
					 'NEXT_EXEC'      => date('d.m.Y H:i:s', mktime(date('H'), date('i'), date('s'), date('m'), date('j') + 1, date('Y'))),
					 'AGENT_INTERVAL' => 86400,
					 'IS_PERIOD'      => 'Y',
				));
			}
		}
	}

	/**
	 * @param $reviewId
	 */
	public static function delete($reviewId)
	{
		$arAgent = AgentTable::getRow(array(
			 'select' => array('ID', 'REVIEW_ID'),
			 'filter' => array('=REVIEW_ID' => $reviewId),
		));

		if($arAgent['ID']) {
			//Удаляем агента Битрикс
			\CAgent::RemoveAgent("\\Api\\Reviews\\Agent::sendSubscribe(" . $arAgent['ID'] . ");", "api.reviews");

			//Удаляем агента Отзывов
			AgentTable::delete($arAgent['ID']);
		}
	}

	/**
	 * @param $agentId
	 */
	public static function sendSubscribe($agentId)
	{
		$request  = Application::getInstance()->getContext()->getRequest();
		$protocol = $request->isHttps() ? 'https://' : 'http://';

		$arAgent = AgentTable::getRow(array(
			 'filter' => array('=ID' => $agentId, '=DATE_EXEC' => null),
		));

		$updateAgentFields = array(
			 'DATE_EXEC'       => new DateTime(),
			 'SUBSCRIBERS_CNT' => 0,
			 'SENDMAIL_CNT'    => 0,
		);

		if($arAgent['REVIEW_ID']) {

			//В рассылку попадают только активные отзывы, а агент пусть отработает для статистики и удалится
			$arReview = ReviewsTable::getRow(array(
				 'select' => array('ID', 'SITE_ID', 'IBLOCK_ID', 'SECTION_ID', 'ELEMENT_ID', 'ORDER_ID', 'URL', 'GUEST_EMAIL', 'PAGE_URL', 'PAGE_TITLE', 'RATING'),
				 'filter' => array('=ID' => $arAgent['REVIEW_ID'], '=ACTIVE' => 'Y'),
			));

			if($arReview) {

				$siteId = $arReview['SITE_ID'];

				$arSite     = \CSite::GetList($by = 'sort', $order = 'desc', array('ID' => $siteId))->Fetch();
				$siteName   = ($arSite['SITE_NAME'] ? $arSite['SITE_NAME'] : trim(Option::get('main', 'site_name', SITE_SERVER_NAME)));
				$serverName = $protocol . ($arSite['SERVER_NAME'] ? $arSite['SERVER_NAME'] : trim(Option::get('main', 'server_name', SITE_SERVER_NAME)));
				$siteEmail  = ($arSite['EMAIL'] ? $arSite['EMAIL'] : trim(Option::get('main', 'email_from', 'info@' . SITE_SERVER_NAME)));

				//$arFilter
				$arFilter = array('SITE_ID' => $siteId);

				if($arReview['IBLOCK_ID'])
					$arFilter['IBLOCK_ID'] = $arReview['IBLOCK_ID'];

				if($arReview['SECTION_ID'])
					$arFilter['SECTION_ID'] = $arReview['SECTION_ID'];

				if($arReview['ELEMENT_ID'])
					$arFilter['ELEMENT_ID'] = $arReview['ELEMENT_ID'];

				//if($arReview['ORDER_ID'])
					//$arFilter['ORDER_ID'] = $arReview['ORDER_ID'];

				if($arReview['URL'])
					$arFilter['URL'] = $arReview['URL'];

				$rsSubscribe = SubscribeTable::getList(array(
					 'order'  => array('ID' => 'ASC'),
					 'filter' => $arFilter,
					 'select' => array('EMAIL'),
				));

				$updateAgentFields['SUBSCRIBERS_CNT'] = $rsSubscribe->getSelectedRowsCount();

				while($row = $rsSubscribe->fetch()) {

					$arFields = array(
						 'SITE_NAME'  => $siteName,
						 'SITE_HOST'  => $serverName,
						 'EMAIL_FROM' => $siteEmail,
						 'EMAIL_TO'   => trim($row['EMAIL']),
						 'RATING'     => $arReview['RATING'],
						 'ID'         => $arReview['ID'],
						 'PAGE_URL'   => $arReview['PAGE_URL'],
						 'PAGE_TITLE' => $arReview['PAGE_TITLE'],
					);

					if($row['EMAIL'] == $arReview['GUEST_EMAIL']) {
						$updateAgentFields['SENDMAIL_CNT']++;
					}
					else {
						if(Event::send('API_REVIEWS_SUBSCRIBE', $siteId, $arFields)) {
							$updateAgentFields['SENDMAIL_CNT']++;
						}
					}
				}
			}
		}

		AgentTable::update($agentId, $updateAgentFields);



		//Если функция агента возвращает вызов себя, то агент периодический.
		//Если функция агента ничего не возвращает, то агент будет удален из таблицы и больше не запустится. Агент непериодический.
		//return "\\Api\\Reviews\\Agent::sendSubscribe(" . $agentId . ");";
	}
}