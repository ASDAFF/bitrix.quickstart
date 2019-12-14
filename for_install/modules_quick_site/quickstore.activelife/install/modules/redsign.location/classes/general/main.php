<?
IncludeModuleLangFile(__FILE__);

class CRS_Location
{
	function GetCityName()
	{
		global $APPLICATION;
		if (\Bitrix\Main\Loader::includeModule('statistic')) // business
		{
			$name = array();
			$obCity = new CCity();
			$arCity = $obCity->GetFullInfo();
			foreach ($arCity as $FIELD_ID => $arField)
			{
				if ($FIELD_ID == 'IP_ADDR'){
					$name['IP_ADDR'] = $arField['VALUE'];
				}
				elseif ($FIELD_ID == 'COUNTRY_CODE')
				{
					$name['COUNTRY_CODE'] = $arField['VALUE'];
				}
				elseif ($FIELD_ID == 'COUNTRY_NAME')
				{
					if ($name['COUNTRY_CODE'] == 'RU')
					{
						$name['COUNTRY_NAME'] = getMessage('COUNTRY_NAME_RU');
					}
					elseif ($name['COUNTRY_CODE'] == 'UK')
					{
						$name['COUNTRY_NAME'] = getMessage('COUNTRY_NAME_UK');
					}
					else
					{
						$name['COUNTRY_NAME'] = $arField['VALUE'];
					}
				}
				elseif ($FIELD_ID == 'REGION_NAME')
				{
					$name['REGION_NAME'] = $arField['VALUE'];
				}
				elseif ($FIELD_ID == 'CITY_NAME')
				{
					$name['CITY_NAME'] = $arField['VALUE'];
				}
			}
		}
		else
		{
			$gb = new IPGeoBase();
			$data = $gb->getRecord();
			if ($data['cc'] == 'RU')
			{
				$name['COUNTRY_NAME'] = getMessage('COUNTRY_NAME_RU');
			}
			elseif($data['cc'] == 'UK')
			{
				$name['COUNTRY_NAME'] = getMessage('COUNTRY_NAME_UK');
			}
			$name['COUNTRY_CODE'] = $data['cc'];
			$name['CITY_NAME'] = $data['city'];
			$name['REGION_NAME'] = $data['region'];
		}
		return $name;
	}

	function OnSaleComponentOrderOneStepOrderProps(&$arResult, &$arUserResult, $arParams)
	{
		if (1 > intval($arUserResult['DELIVERY_LOCATION']) || 1 > intval($arUserResult['TAX_LOCATION'])) {
			if (!\Bitrix\Main\Loader::includeModule('redsign.location') ||
                !\Bitrix\Main\Loader::includeModule('sale')) {
				return;
			}
			$COM_SESS_PREFIX = 'RSLOCATION';
			$iDetectedLocID = intval($_SESSION[$COM_SESS_PREFIX]['LOCATION']['ID']);

			if (0 >=  $iDetectedLocID) {
				$detected = CRS_Location::GetCityName();

				if (isset($detected['CITY_NAME'])) {
					$dbRes = CSaleLocation::GetList(
						array('SORT' => 'ASC', 'CITY_NAME_LANG' => 'ASC'),
						array('LID' => LANGUAGE_ID, 'CITY_NAME' => $detected['CITY_NAME'])
					);
					if ($arFields = $dbRes->Fetch()) {
						$iDetectedLocID = $arFields['ID'];
					}
				}
			}

			if (0 <  $iDetectedLocID) {
				foreach ($arResult['ORDER_PROP']['USER_PROPS_Y'] as $iOrderPropKey => $arOrderProps) {
					if ($arOrderProps['TYPE']=='LOCATION' &&
                        ($arOrderProps['IS_LOCATION']=='Y' || $arOrderProps['IS_LOCATION4TAX']=='Y') &&
                        1 > intval($arOrderProps['VALUE'])) {

						$arResult['ORDER_PROP']['USER_PROPS_Y'][$iOrderPropKey]['VALUE'] = $iDetectedLocID;
                        $arLocationSelected = false;

						foreach ($arOrderProps['VARIANTS'] as $iVariantKey => $arVariant) {
							if ($iDetectedLocID == $arVariant['ID']) {
                                $arLocationSelected = &$arResult['ORDER_PROP']['USER_PROPS_Y'][$iOrderPropKey]['VARIANTS'][$iVariantKey];
								break;
							}
						}

                        $arLocationSelected['SELECTED'] = 'Y';

                        if (!empty($arLocationSelected['CODE'])) {
                            $arUserResult['DELIVERY_LOCATION_BCODE'] = $arLocationSelected['CODE'];
                        }
						$arUserResult['ORDER_PROP'][$arOrderProps['ID']] = $iDetectedLocID;

						if ($arOrderProps['IS_LOCATION'] == 'Y') {
							$arUserResult['DELIVERY_LOCATION'] = $iDetectedLocID;
						}
						if ($arOrderProps['IS_LOCATION4TAX'] == 'Y') {
							$arUserResult['TAX_LOCATION'] = $iDetectedLocID;
						}
					}
				}
			}
		}
	}
}
