<?php

namespace Api\Reviews;

use \Bitrix\Main;

class VideoTable extends Main\Entity\DataManager
{
	public static function getTableName()
	{
		return 'api_reviews_video';
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
			 )),
			 new Main\Entity\IntegerField('FILE_ID'),
			 new Main\Entity\StringField('CODE'),
			 new Main\Entity\StringField('SERVICE'),
			 new Main\Entity\StringField('TITLE'),
			 new Main\Entity\TextField('DESCRIPTION'),
		);
	}
}