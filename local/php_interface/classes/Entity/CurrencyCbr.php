<?php

namespace Entity;

use Bitrix\Main\Config\Option;

/**
 * Class CurrencyCbr
 * @package Entity
 */
class CurrencyCbr
{
    const CBR_URL = 'https://www.cbr.ru/scripts/XML_daily.asp';
    const AR_CURRENCY_LIST = ['USD', 'EUR'];

    protected static function parseXmlCbr($sUrl = self::CBR_URL)
    {
        $arCurrencyData = [];
        $charCode = null;

        $xmlObj = simplexml_load_file($sUrl . '?date_req=' . date('d.m.Y'));
        foreach ($xmlObj->Valute ?: [] as $itemObj) {
            $charCode = (string)$itemObj->CharCode;
            if (in_array($charCode, self::AR_CURRENCY_LIST)) {
                $arCurrencyData[$charCode]['NOMINAL'] = isset($itemObj->Nominal) ? (string)$itemObj->Nominal : 1;
                $arCurrencyData[$charCode]['VALUE'] = isset($itemObj->Value) ? (string)$itemObj->Value : 0;
            }
        }
        return $arCurrencyData;
    }

    public static function addCurrencyRates()
    {
        $arCbrRates = self::parseXmlCbr();
        foreach ($arCbrRates ?: [] as $currencyCode => $currencyValues) {
            foreach ($currencyValues ?: [] as $key => $value) {
                Option::set('main', $currencyCode . '_' . $key, str_replace(',', '.', $value));
            }
        }
        return __METHOD__ . '();';
    }

    public static function getCurrencyRates()
    {
        $arResult = [];

        foreach (self::AR_CURRENCY_LIST ?: [] as $currencyCode) {
            $arResult[$currencyCode] = [
                'NOMINAL' => Option::get('main', $currencyCode . '_NOMINAL'),
                'VALUE'   => Option::get('main', $currencyCode . '_VALUE'),
            ];
        }

        return $arResult;
    }
}