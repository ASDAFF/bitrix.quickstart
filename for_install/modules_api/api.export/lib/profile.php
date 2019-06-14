<?php

namespace Api\Export;

use \Bitrix\Main;
use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class ProfileTable extends Main\Entity\DataManager
{
	protected static $exportFilePath  = '/bitrix/catalog_export/api_export_#id#.xml';
	protected static $commaFields     = array(
		 'SECTION_ID',
	);
	protected static $serializeFields = array(
		 'CURRENCY', 'ELEMENTS_CONDITION', 'OFFERS_CONDITION', 'FIELDS', 'DELIVERY', 'ELEMENTS_FILTER', 'OFFERS_FILTER', 'UTM_TAGS'
	);

	public static function getTableName()
	{
		return 'api_export_profile';
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
			 new Main\Entity\IntegerField('ID', array(
					'title'        => Loc::getMessage('ID'),
					'primary'      => true,
					'autocomplete' => true,
			 )),
			 new Main\Entity\StringField('NAME', array(
					'title'      => Loc::getMessage('NAME'),
					'validation' => array(__CLASS__, 'validateName'),
			 )),
			 new Main\Entity\BooleanField('ACTIVE', array(
					'title'         => Loc::getMessage('ACTIVE'),
					'values'        => array('Y', 'N'),
					'default_value' => 'Y',
			 )),
			 new Main\Entity\IntegerField('SORT', array(
					'title'         => Loc::getMessage('SORT'),
					'format'        => '/^[0-9]{1,11}$/',
					'default_value' => 500,
					'required'      => true,
			 )),
			 new Main\Entity\DatetimeField('DATE_CREATE', array(
					'title'         => Loc::getMessage('DATE_CREATE'),
					'default_value' => new Main\Type\DateTime(),
			 )),
			 new Main\Entity\DatetimeField('TIMESTAMP_X', array(
					'title'         => Loc::getMessage('TIMESTAMP_X'),
					'default_value' => new Main\Type\DateTime(),
			 )),
			 new Main\Entity\StringField('CHARSET', array(
					'title' => Loc::getMessage('CHARSET'),
			 )),
			 new Main\Entity\StringField('FILE_PATH', array(
					'title' => Loc::getMessage('FILE_PATH'),
			 )),
			 new Main\Entity\StringField('SHOP_NAME', array(
					'title' => Loc::getMessage('SHOP_NAME'),
			 )),
			 new Main\Entity\StringField('SHOP_COMPANY', array(
					'title' => Loc::getMessage('SHOP_COMPANY'),
			 )),
			 new Main\Entity\StringField('SHOP_URL', array(
					'title' => Loc::getMessage('SHOP_URL'),
			 )),
			 new Main\Entity\IntegerField('PRICE_TYPE', array(
					'format' => '/^[0-9]{1,11}$/',
					'title'  => Loc::getMessage('PRICE_TYPE'),
			 )),
			 new Main\Entity\BooleanField('PRICE_VAT_INCLUDE', array(
				  'title'         => Loc::getMessage('PRICE_VAT_INCLUDE'),
				  'values'        => array('Y', 'N'),
				  'default_value' => 'N',
			 )),
			 new Main\Entity\BooleanField('CONVERT_CURRENCY', array(
				  'title'         => Loc::getMessage('CONVERT_CURRENCY'),
				  'values'        => array('Y', 'N'),
				  'default_value' => 'N',
			 )),
			 new Main\Entity\StringField('CURRENCY_ID', array(
				  'title' => Loc::getMessage('CURRENCY_ID'),
			 )),
			 new Main\Entity\TextField('CURRENCY', array(
					'title' => Loc::getMessage('CURRENCY'),
			 )),
			 new Main\Entity\TextField('DELIVERY', array(
					'title' => Loc::getMessage('DELIVERY'),
			 )),
			 new Main\Entity\TextField('DIMENSIONS', array(
					'title' => Loc::getMessage('DIMENSIONS'),
			 )),
			 new Main\Entity\TextField('UTM_TAGS', array(
					'title' => Loc::getMessage('UTM_TAGS'),
			 )),
			 new Main\Entity\TextField('STOP_WORDS', array(
					'title' => Loc::getMessage('STOP_WORDS'),
			 )),
			 new Main\Entity\BooleanField('USE_CATALOG', array(
					'title'         => Loc::getMessage('USE_CATALOG'),
					'values'        => array('Y', 'N'),
					'default_value' => 'Y',
			 )),
			 new Main\Entity\BooleanField('USE_SUBSECTIONS', array(
					'title'         => Loc::getMessage('USE_SUBSECTIONS'),
					'values'        => array('Y', 'N'),
					'default_value' => 'N',
			 )),
			 new Main\Entity\StringField('IBLOCK_TYPE_ID', array(
					'title' => Loc::getMessage('IBLOCK_TYPE_ID'),
			 )),
			 new Main\Entity\StringField('IBLOCK_ID', array(
					'title' => Loc::getMessage('IBLOCK_ID'),
			 )),
			 new Main\Entity\TextField('SECTION_ID', array(
					'title' => Loc::getMessage('SECTION_ID'),
			 )),
			 new Main\Entity\TextField('ELEMENTS_FILTER', array(
					'title' => Loc::getMessage('ELEMENTS_FILTER'),
			 )),
			 new Main\Entity\TextField('OFFERS_FILTER', array(
					'title' => Loc::getMessage('OFFERS_FILTER'),
			 )),
			 new Main\Entity\TextField('ELEMENTS_CONDITION', array(
				  'title' => Loc::getMessage('ELEMENTS_CONDITION'),
			 )),
			 new Main\Entity\TextField('OFFERS_CONDITION', array(
				  'title' => Loc::getMessage('OFFERS_CONDITION'),
			 )),
			 new Main\Entity\StringField('TYPE', array(
					'title' => Loc::getMessage('TYPE'),
			 )),
			 new Main\Entity\TextField('FIELDS', array(
					'title' => Loc::getMessage('FIELDS'),
			 )),
			 new Main\Entity\IntegerField('LAST_SECTION_ID', array(
					'format' => '/^[0-9]{1,11}$/',
					'title'  => Loc::getMessage('LAST_SECTION_ID'),
			 )),
			 new Main\Entity\IntegerField('LAST_ELEMENT_ID', array(
					'format' => '/^[0-9]{1,11}$/',
					'title'  => Loc::getMessage('LAST_ELEMENT_ID'),
			 )),
			 new Main\Entity\DatetimeField('LAST_START', array(
					'title' => Loc::getMessage('LAST_START'),
			 )),
			 new Main\Entity\DatetimeField('LAST_END', array(
					'title' => Loc::getMessage('LAST_END'),
			 )),
			 new Main\Entity\IntegerField('STEP_LIMIT', array(
				  'title'         => Loc::getMessage('STEP_LIMIT'),
				  'format'        => '/^[0-9]{1,11}$/',
				  'default_value' => 500,
				  'required'      => true,
			 )),
			 new Main\Entity\IntegerField('TOTAL_ITEMS', array(
					'title'  => Loc::getMessage('TOTAL_ITEMS'),
			 )),
			 new Main\Entity\IntegerField('TOTAL_ELEMENTS', array(
					//'format' => '/^[0-9]{1,11}$/',
					'title'  => Loc::getMessage('TOTAL_ELEMENTS'),
			 )),
			 new Main\Entity\IntegerField('TOTAL_OFFERS', array(
					'title'  => Loc::getMessage('TOTAL_OFFERS'),
			 )),
			 new Main\Entity\IntegerField('TOTAL_SECTIONS', array(
				  'title'  => Loc::getMessage('TOTAL_SECTIONS'),
			 )),
			 new Main\Entity\StringField('TOTAL_RUN_TIME', array(
					'title' => Loc::getMessage('TOTAL_RUN_TIME'),
			 )),
			 new Main\Entity\StringField('TOTAL_MEMORY', array(
					'title' => Loc::getMessage('TOTAL_MEMORY'),
			 )),
			 new Main\Entity\IntegerField('MODIFIED_BY', array(
					'title'  => Loc::getMessage('MODIFIED_BY'),
			 )),
			 new Main\Entity\StringField('SITE_ID', array(
					'title'      => Loc::getMessage('SITE_ID'),
					'validation' => array(__CLASS__, 'validateSiteId'),
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

	/**
	 * Returns validators for NAME field.
	 *
	 * @return array
	 */
	public static function validateName()
	{
		return array(
			 new Main\Entity\Validator\Length(null, 255),
		);
	}

	public static function decodeFields(&$fields)
	{
		if($fields) {
			foreach($fields as $key => &$val) {
				if(is_string($val)) {
					if(in_array($key, self::$commaFields))
						$val = (strlen($val) > 0 ? explode(',', $val) : '');
					elseif(in_array($key, self::$serializeFields))
						$val = (strlen($val) > 0 ? unserialize($val) : array());
					else
						$val = htmlspecialcharsbx($val);
				}
			}
		}
	}

	public static function encodeFields(&$fields)
	{
		if($fields) {
			foreach($fields as $key => &$val) {
				if(in_array($key, self::$commaFields))
					$val = !empty($val) ? implode(',', $val) : '';

				if(in_array($key, self::$serializeFields))
					$val = !empty($val) ? serialize($val) : '';
			}
		}
	}


	///////////////////////////////////////////////////////////////////////////
	/// DataManager event handlers
	///////////////////////////////////////////////////////////////////////////

	//«апишет в профиль относительный путь до файла экспорта на сайте
	public static function OnAfterAdd(Main\Entity\Event $event)
	{
		if($id = $event->getParameter('id')) {
			static::update($id, array(
				 'FILE_PATH' => str_replace('#id#', $id, static::$exportFilePath),
			));
		}
	}

	/*public static function OnBeforeUpdate(Main\Entity\Event $event){

		$result = new Main\Entity\EventResult();
		$result->modifyFields(array(
			 'TIMESTAMP_X' => new Main\Type\DateTime()
		));

		return $result;
	}*/
}