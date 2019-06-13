<?php
namespace Api\Reviews;

use Bitrix\Main,
	 Bitrix\Main\Type,
	 Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

//bitrix/modules/sender/lib/contact.php
class SubscribeTable extends Main\Entity\DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'api_reviews_subscribe';
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
			 new Main\Entity\StringField('EMAIL', array(
					'title'    => Loc::getMessage('EMAIL'),
					'required' => true,
			 )),
			 new Main\Entity\IntegerField('USER_ID', array(
					'format' => '/^[0-9]{1,18}$/',
					'title'  => Loc::getMessage('USER_ID'),
			 )),
			 new Main\Entity\IntegerField('IBLOCK_ID', array(
					'format' => '/^[0-9]{1,18}$/',
					'title'  => Loc::getMessage('IBLOCK_ID'),
			 )),
			 new Main\Entity\IntegerField('SECTION_ID', array(
					'format' => '/^[0-9]{1,18}$/',
					'title'  => Loc::getMessage('SECTION_ID'),
			 )),
			 new Main\Entity\IntegerField('ELEMENT_ID', array(
					'format' => '/^[0-9]{1,18}$/',
					'title'  => Loc::getMessage('ELEMENT_ID'),
			 )),
			 new Main\Entity\StringField('URL', array(
					'title' => Loc::getMessage('URL'),
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