<?
//http://dev.1c-bitrix.ru/community/webdev/group/78/blog/1657/
IncludeModuleLangFile(__FILE__);

class CASDcurrencyrate
{
	public static function OnAdminListDisplayHandler(&$list)
	{
		if ($GLOBALS['APPLICATION']->GetCurPage()=='/bitrix/admin/currencies_rates.php')
		{
			$list->context->items[-2] = array(
										'ICON' => 'btn_refresh',
										'TEXT' => GetMessage('ASD_ACTION_GET'),
										'TITLE' => GetMessage('ASD_ACTION_GET_TITLE'),
										'LINK' => $GLOBALS['APPLICATION']->GetCurPageParam('asd_get_rate=Y', array('mode')),
									);
			$list->context->items[-1] = array('SEPARATOR' => 1);
			ksort($list->context->items);
		}
	}

	public static function OnBeforePrologHandler()
	{
		if ($GLOBALS['APPLICATION']->GetCurPage()=='/bitrix/admin/currencies_rates.php' && $_REQUEST['asd_get_rate']=='Y' &&
			$GLOBALS['APPLICATION']->GetGroupRight('currency')>'D')
		{
			self::UpdateRates(false);
			LocalRedirect($GLOBALS['APPLICATION']->GetCurPageParam('', array('asd_get_rate')));
		}
	}

	public static function UpdateRates($bAgent=true)
	{
		if (CModule::IncludeModule('currency'))
		{
			$arCurr = array();
			$rsRate = CCurrency::GetList($by='currency', $order='asc');
			while ($arRate = $rsRate->Fetch())
			{
				if ($arRate['CURRENCY']!='RUB' && $arRate['CURRENCY']!='RUR')
					$arCurr[] = $arRate['CURRENCY'];
			}

			if (!empty($arCurr))
			{
				$queryStr = date('d.m.Y');
				$adminDate = date($GLOBALS['DB']->DateFormatToPHP(CLang::GetDateFormat('SHORT')));
				require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/classes/general/xml.php');
				$strQueryText = QueryGetData('www.cbr.ru', 80, '/scripts/XML_daily.asp', $queryStr, $errno, $errstr);
				$objXML = new CDataXML();
				if ($objXML->LoadString($strQueryText))
				{
					$arData = $objXML->GetArray();
					if (!empty($arData) && is_array($arData))
					{
						foreach ($arData['ValCurs']['#']['Valute'] as $arC)
						{
							if (in_array($arC["#"]["CharCode"][0]["#"], $arCurr))
							{
								$arNewRate = array(
											'CURRENCY' => $arC["#"]["CharCode"][0]["#"],
											'RATE_CNT' => intval($arC['#']['Nominal'][0]['#']),
											'RATE' => doubleval(str_replace(',', '.', $arC['#']['Value'][0]['#'])),
											'DATE_RATE' => $adminDate,
										);
								if (!CCurrencyRates::GetList($by='id', $order='desc', $arNewRate)->Fetch())
									CCurrencyRates::Add($arNewRate);
							}
						}
					}
				}
			}
		}

		if ($bAgent)
			return 'CASDcurrencyrate::UpdateRates();';
	}
}
?>