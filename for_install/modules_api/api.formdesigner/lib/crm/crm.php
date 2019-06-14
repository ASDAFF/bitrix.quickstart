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
			 new Main\Entity\StringField('NAME'),
			 new Main\Entity\StringField('URL'),
			 new Main\Entity\StringField('HASH'),
		);
	}
}