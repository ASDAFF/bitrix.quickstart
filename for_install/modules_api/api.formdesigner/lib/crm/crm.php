<?

namespace Api\FormDesigner\Crm;

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class CrmTable extends Main\Entity\DataManager
{
	public static function getTableName()
	{
		return 'api_formdesigner_crm';
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
			 new Main\Entity\StringField('NAME'),
			 new Main\Entity\StringField('URL'),
			 new Main\Entity\StringField('HASH'),
		);
	}
}