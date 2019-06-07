<?php

namespace Api\QA;

use Bitrix\Main;
use Bitrix\Main\Type;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class QuestionTable extends Main\Entity\DataManager
{
	public static function getTableName()
	{
		return 'api_qa_question';
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
					'primary'      => true,
					'autocomplete' => true,
					'title'        => Loc::getMessage('ID'),
			 )),
			 new Main\Entity\StringField('ACTIVE', array(
				  'title' => Loc::getMessage('ACTIVE'),
			 )),
			 new Main\Entity\DatetimeField('DATE_CREATE', array(
					'default_value' => new Type\DateTime(),
					'title'         => Loc::getMessage('DATE_CREATE'),
					'required'      => true,
			 )),
			 new Main\Entity\BooleanField('TYPE', array(
					'values' => array('Q', 'A', 'C'),
					'title'  => Loc::getMessage('TYPE'),
			 )),
			 new Main\Entity\IntegerField('PARENT_ID', array(
					'format'        => '/^[0-9]{1,18}$/',
					'default_value' => 0,
					'title'         => Loc::getMessage('PARENT_ID'),
			 )),
			 new Main\Entity\IntegerField('LEVEL', array(
					'format'        => '/^[0-9]{1,18}$/',
					'default_value' => 0,
					'title'         => Loc::getMessage('LEVEL'),
			 )),
			 new Main\Entity\StringField('GUEST_NAME', array(
				  'title' => Loc::getMessage('GUEST_NAME'),
			 )),
			 new Main\Entity\StringField('GUEST_EMAIL', array(
				  'title' => Loc::getMessage('GUEST_EMAIL'),
			 )),
			 new Main\Entity\TextField('TEXT', array(
				  'title' => Loc::getMessage('TEXT'),
			 )),
			 new Main\Entity\BooleanField('NOTIFY', array(
					'values'        => array('N', 'Y'),
					'default_value' => 'Y',
					'title'         => Loc::getMessage('NOTIFY'),
			 )),
			 new Main\Entity\IntegerField('USER_ID', array(
				  'format' => '/^[0-9]{1,18}$/',
				  'title'  => Loc::getMessage('USER_ID'),
			 )),
			 new Main\Entity\StringField('LOCATION', array(
				  'title' => Loc::getMessage('LOCATION'),
			 )),
			 new Main\Entity\StringField('PAGE_URL', array(
				  'title' => Loc::getMessage('PAGE_URL'),
			 )),
			 new Main\Entity\StringField('PAGE_TITLE', array(
				  'title' => Loc::getMessage('PAGE_TITLE'),
			 )),


			 new Main\Entity\IntegerField('IBLOCK_ID', array(
					'format' => '/^[0-9]{1,18}$/',
					'title'  => Loc::getMessage('IBLOCK_ID'),
			 )),
			 new Main\Entity\IntegerField('ELEMENT_ID', array(
					'format' => '/^[0-9]{1,18}$/',
					'title'  => Loc::getMessage('ELEMENT_ID'),
			 )),
			 new Main\Entity\StringField('XML_ID', array(
					'title' => Loc::getMessage('XML_ID'),
			 )),
			 new Main\Entity\StringField('CODE', array(
					'title' => Loc::getMessage('CODE'),
			 )),
			 new Main\Entity\IntegerField('VOTE_UP', array(
				  'format'        => '/^[0-9]{1,11}$/',
				  'default_value' => 0,
				  'title'         => Loc::getMessage('VOTE_UP'),
			 )),
			 new Main\Entity\IntegerField('VOTE_DO', array(
				  'format'        => '/^[0-9]{1,11}$/',
				  'default_value' => 0,
				  'title'         => Loc::getMessage('VOTE_DO'),
			 )),
			 new Main\Entity\StringField('SITE_ID', array(
				  'validation' => array(__CLASS__, 'validateSiteId'),
				  'title'      => Loc::getMessage('SITE_ID'),
			 )),
			 new Main\Entity\StringField('IP', array(
					'title' => Loc::getMessage('IP'),
			 )),
		);
	}

	public static function validateSiteId()
	{
		return array(
			 new Main\Entity\Validator\Length(null, 2),
		);
	}
}