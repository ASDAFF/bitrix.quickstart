<?php

namespace Api\Message;

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Class HistoryTable
 *
 * Fields:
 * <ul>
 * <li> IBLOCK_ID int mandatory
 * <li> YANDEX_EXPORT bool optional default 'N'
 * <li> SUBSCRIPTION bool optional default 'N'
 * <li> VAT_ID int optional
 * <li> PRODUCT_IBLOCK_ID int mandatory
 * <li> SKU_PROPERTY_ID int mandatory
 * </ul>
 *
 * @package Api\OrderStatus
 **/
class MessageTable extends Main\Entity\DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'api_message';
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
			 'ID'           => array(
					'data_type'    => 'integer',
					'primary'      => true,
					'autocomplete' => true,
					'title'        => Loc::getMessage('ASM_LM_ID'),
			 ),
			 'ACTIVE'       => array(
					'data_type' => 'boolean',
					'values'    => array('N', 'Y'),
					'title'     => Loc::getMessage('ASM_LM_ACTIVE'),
			 ),
			 'ACTIVE_FROM'  => array(
					'data_type' => 'datetime',
					'title'     => Loc::getMessage('ASM_LM_ACTIVE_FROM'),
			 ),
			 'ACTIVE_TO'    => array(
					'data_type' => 'datetime',
					'title'     => Loc::getMessage('ASM_LM_ACTIVE_TO'),
			 ),
			 'SORT'         => array(
					'data_type'     => 'integer',
					'default_value' => 500,
					'format'        => '/^[0-9]{1,11}$/',
					'title'         => Loc::getMessage('ASM_LM_SORT'),
			 ),
			 'NAME'         => array(
					'data_type' => 'string',
					'required'  => true,
					'title'     => Loc::getMessage('ASM_LM_NAME'),
			 ),
			 'EXPIRES'      => array(
					'data_type' => 'integer',
					'format'    => '/^[0-9]{1,11}$/',
					'title'     => Loc::getMessage('ASM_LM_EXPIRES'),
			 ),
			 'TYPE'         => array(
					'data_type' => 'string',
					'title'     => Loc::getMessage('ASM_LM_TYPE'),
			 ),
			 'COLOR'        => array(
					'data_type' => 'string',
					'title'     => Loc::getMessage('ASM_LM_COLOR'),
			 ),
			 'MESSAGE'      => array(
					'data_type' => 'text',
					'required'  => true,
					'title'     => Loc::getMessage('ASM_LM_MESSAGE'),
			 ),
			 'MESSAGE_TYPE' => array(
					'data_type' => 'string',
					'title'     => Loc::getMessage('ASM_LM_MESSAGE_TYPE'),
			 ),
			 'CLOSE_TEXT'   => array(
					'data_type' => 'string',
					'title'     => Loc::getMessage('ASM_LM_CLOSE_TEXT'),
			 ),
			 'CLOSE_CLASS'  => array(
					'data_type' => 'string',
					'title'     => Loc::getMessage('ASM_LM_CLOSE_CLASS'),
			 ),
			 'SITE_ID'      => array(
					'data_type' => 'string',
					'required'  => true,
					'title'     => Loc::getMessage('ASM_LM_SITE_ID'),
			 ),
			 'GROUP_ID'     => array(
					'data_type' => 'string',
					'title'     => Loc::getMessage('ASM_LM_GROUP_ID'),
			 ),
			 'USER_ID'      => array(
					'data_type' => 'string',
					'title'     => Loc::getMessage('ASM_LM_USER_ID'),
			 ),
			 'PAGE_URL'     => array(
					'data_type' => 'text',
					'title'     => Loc::getMessage('ASM_LM_PAGE_URL'),
			 ),
			 'BLOCK'        => array(
					'data_type'     => 'boolean',
					'values'        => array('N', 'Y'),
					'default_value' => 'N',
					'title'         => Loc::getMessage('ASM_LM_BLOCK'),
			 ),
			 'TIMESTAMP_X'  => array(
					'data_type' => 'datetime',
					'title'     => Loc::getMessage('ASM_LM_TIMESTAMP_X'),
			 ),
			 'MODIFIED_BY'  => array(
					'data_type' => 'integer',
					'title'     => Loc::getMessage('ASM_LM_MODIFIED_BY'),
			 ),
		);
	}

	public static function getData($siteId, $cfg = array())
	{
		if(!$siteId)
			return false;

		$result = array();


		//---------- Cahe settings ----------//
		$cacheTime = intval($cfg['CACHE_TTL']); //7 days
		$cacheId   = 'api_message_' . $siteId;
		$cacheDir  = '/' . $siteId . '/api/message/';

		$obCache = new \CPHPCache();
		if($obCache->InitCache($cacheTime, $cacheId, $cacheDir)) {
			$result = $obCache->GetVars();
		}
		elseif($obCache->StartDataCache()) {
			if(defined('BX_COMP_MANAGED_CACHE')) {
				global $CACHE_MANAGER;
				$CACHE_MANAGER->StartTagCache($cacheDir);
			}

			$arMessage = self::getList(array(
				 'order'  => array('SORT' => 'ASC', 'ID' => 'ASC'),
				 'select' => array('ID', 'TYPE', 'COLOR', 'MESSAGE', 'GROUP_ID', 'USER_ID', 'BLOCK', 'CLOSE_TEXT', 'CLOSE_CLASS', 'PAGE_URL', 'EXPIRES'),
				 'filter' => array(
						'=ACTIVE'  => 'Y',
						'?SITE_ID' => $siteId,
						'LOGIC'    => 'AND',
						array(
							 'LOGIC'       => 'OR',
							 '>=ACTIVE_TO' => new \Bitrix\Main\Type\DateTime(),
							 'ACTIVE_TO'   => null,
						),
						array(
							 'LOGIC'         => 'OR',
							 '<=ACTIVE_FROM' => new \Bitrix\Main\Type\DateTime(),
							 'ACTIVE_FROM'   => null,
						),
				 ),
			))->fetchAll();

			//foreach($arMessage as $arMess)
			//$result[$arMess['TYPE']][] = $arMess;

			foreach($arMessage as $arMess)
				$result[ $arMess['ID'] ] = $arMess;


			if(defined('BX_COMP_MANAGED_CACHE')) {
				$CACHE_MANAGER->RegisterTag($cacheId);
				$CACHE_MANAGER->EndTagCache();
			}

			$obCache->EndDataCache($result);
		}

		return $result;
	}
}