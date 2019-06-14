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
			boolean (��������� ScalarField)
			date (��������� ScalarField)
			datetime (��������� DateField)
			enum (��������� ScalarField)
			float (��������� ScalarField)
			integer (��������� ScalarField)
			string (��������� ScalarField)
			text (��������� StringField)
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