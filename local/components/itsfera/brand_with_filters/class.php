<?
use Bitrix\Main\Loader;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

class CBrandWithFilters extends CBitrixComponent
{

    const COOKIE_NAME = "brand_request";

    public function executeComponent()
    {
        //очищаем куку если нужен другой бренд
        if (isset($_COOKIE[ self::COOKIE_NAME ])) {
            $arTmp = \Bitrix\Main\Web\Json::decode($_COOKIE[ self::COOKIE_NAME ]);

            if ( $arTmp['q'] != $this->arParams['BRAND'] ) {
                unset($_COOKIE[self::COOKIE_NAME]);
                setcookie(self::COOKIE_NAME, "", time() - 3600, "/");
            }
        }

        $this->includeComponentTemplate();
    }

}

?>