<?

namespace Api\Auth\UserType;

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

//bitrix/modules/main/lib/urlpreview/urlpreviewusertype.php
//bitrix/modules/main/classes/general/usertypeelement.php
class Location
{

	public static function getUserTypeDescription()
	{
		return array(
			 'USER_TYPE_ID'  => 'api_auth_location',
			 'CLASS_NAME'    => __CLASS__,
			 'DESCRIPTION'   => Loc::getMessage('API_AUTH_USER_TYPE_DESCRIPTION'),
			 'BASE_TYPE'     => 'string',//\CUserTypeManager::BASE_TYPE_STRING,
			 'EDIT_CALLBACK' => array(__CLASS__, 'getPublicEdit'),
			 'VIEW_CALLBACK' => array(__CLASS__, 'getPublicView'),
		);
	}

	public static function getDBColumnType($arUserField)
	{
		global $DB;
		switch(strtolower($DB->type)) {
			case 'mysql':
				return 'varchar(255)';
			case 'oracle':
				return 'varchar2(255 char)';
			case 'mssql':
				return 'varchar(255)';
		}
	}

	public static function prepareSettings($arUserField)
	{
		return array(
			 'DEFAULT_VALUE' => $arUserField['SETTINGS']['DEFAULT_VALUE'],
		);
	}

	public static function getSettingsHTML($arUserField = false, $arHtmlControl, $bVarsFromForm)
	{
		$html = '';

		if($bVarsFromForm) {
			$value = $GLOBALS[ $arHtmlControl['NAME'] ]['DEFAULT_VALUE'];
		}
		elseif(is_array($arUserField)) {
			$value = trim($arUserField['SETTINGS']['DEFAULT_VALUE']);
		}
		else {
			$value = '';
		}


		if(Loader::IncludeModule('sale') && \CSaleLocation::isLocationProEnabled()) {
			$html .= '<tr><td>' . Loc::getMessage('API_AUTH_USER_TYPE_LOCATION_DEF') . ':</td><td>';

			ob_start();
			\CSaleLocation::proxySaleAjaxLocationsComponent(
				 array(),
				 array(
						'CODE'            => $value,
						'INPUT_NAME'      => $arHtmlControl['NAME'] . '[DEFAULT_VALUE]',
						'PROVIDE_LINK_BY' => 'code',
				 ),
				 '',
				 true,
				 'api_auth_location_selector'
			);
			$html .= ob_get_contents();
			ob_end_clean();

			$html .= '</td></tr>';
		}
		else {
			$html .= '
				<tr>
					<td>Test:</td>
					<td>
						<select name="" id="">
							<option value="1">1</option>
						</select>
					</td>
				</tr>
			';
		}

		return $html;
	}

	public static function getEditFormHTML($arUserField, $arHtmlControl = array())
	{
		$name  = $arHtmlControl['NAME'];
		$value = $arHtmlControl['VALUE'];

		if($arUserField) {
			$name  = $arUserField['FIELD_NAME'];
			$value = ($arUserField['VALUE'] ? $arUserField['VALUE'] : $arUserField['SETTINGS']['DEFAULT_VALUE']);
		}

		$content = '';
		if(Loader::IncludeModule('sale') && \CSaleLocation::isLocationProEnabled()) {

			ob_start();
			\CSaleLocation::proxySaleAjaxLocationsComponent(
				 array(),
				 array(
						'CODE'            => $value,
						'INPUT_NAME'      => $name,
						'PROVIDE_LINK_BY' => 'code',
				 ),
				 '',
				 true,
				 'api_auth_location_selector'
			);
			$content = ob_get_contents();
			ob_end_clean();
		}

		return $content;
	}

	public static function getPublicEdit($arUserField)
	{
		return self::getEditFormHTML($arUserField);
	}

	public static function getPublicView($arUserField)
	{
		$html = '';
		if(Loader::includeModule('sale') && \CSaleLocation::isLocationProEnabled()) {

			$code = trim($arUserField['VALUE']);
			$lang = LANGUAGE_ID;

			//Вернет полный адрес
			$arLocations = \Bitrix\Sale\Location\LocationTable::getList(array(
				 'filter' => array(
						'=CODE'                          => $code,
						'=PARENTS.NAME.LANGUAGE_ID'      => $lang,
						'=PARENTS.TYPE.NAME.LANGUAGE_ID' => $lang,
				 ),
				 'select' => array(
						'I_ID'            => 'PARENTS.ID',
						'I_NAME_' . $lang => 'PARENTS.NAME.NAME',
						'I_TYPE_CODE'     => 'PARENTS.TYPE.CODE',
						'I_TYPE_NAME_RU'  => 'PARENTS.TYPE.NAME.NAME',
				 ),
				 'order'  => array(
						'PARENTS.DEPTH_LEVEL' => 'asc',
				 ),
			))->fetchAll();


			$userAddress = '';
			if($arLocations) {
				foreach($arLocations as $arLocation) {
					$location = $arLocation[ 'I_NAME_' . $lang ];
					if(strlen($location) > 0)
						$userAddress .= $location . ', ';
				}

				$userAddress = TrimEx($userAddress, ',');
			}
			$html = $userAddress;
		}


		return $html;
	}


}