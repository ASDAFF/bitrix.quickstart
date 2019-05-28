<?

if ( ! defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Application;


class CBHActions extends CBitrixComponent
{

    public function executeComponent ()
    {

        $requestUri = Application::getInstance()->getContext()->getRequest()->getRequestUri();

        $arSelect   = Array(
            'ID',
            'NAME',
            'PREVIEW_TEXT',
            'DETAIL_PICTURE',
            'PROPERTY_TYPE',
            'PROPERTY_DISCOUNT',
            'PROPERTY_PRICE_OLD',
            'PROPERTY_PRICE',
            'PROPERTY_COLOR',
            'PROPERTY_HREF',
            'PROPERTY_HREF_TITLE',
            'PROPERTY_SECTION_URL',
        );
        $arFilter   = Array(
            'IBLOCK_ID'             => getIBlockIdByCode('sections_banners'),
            'ACTIVE'                => 'Y',
            '=PROPERTY_SECTION_URL' => $requestUri,
        );
        $arAllItems = CIBlockElement::GetList(Array('SORT' => 'DESC'), $arFilter, false, Array(),
            $arSelect);

        while ($row = $arAllItems->fetch()) {
            $arBanner = [];
            switch ($row['PROPERTY_TYPE_VALUE']) {
                case 'Большой баннер':
                    $this->arResult['BIG_BANNER']['IMAGE']       = CFile::GetPath($row['DETAIL_PICTURE']);
                    $this->arResult['BIG_BANNER']['NAME']        = $row['NAME'];
                    $this->arResult['BIG_BANNER']['DESCRIPTION'] = $row['PREVIEW_TEXT'];
                    $this->arResult['BIG_BANNER']['HREF']        = $row['PROPERTY_HREF_VALUE'];
                    $this->arResult['BIG_BANNER']['BUTTON']      = $row['PROPERTY_HREF_TITLE_VALUE'];
                    break;
                case 'Новинка':
                    $arBanner['IMAGE']           = CFile::GetPath($row['DETAIL_PICTURE']);
                    $arBanner['NAME']            = $row['NAME'];
                    $arBanner['DESCRIPTION']     = $row['PREVIEW_TEXT'];
                    $arBanner['TYPE']            = $row['PROPERTY_TYPE_VALUE'];
                    $arBanner['HREF']            = $row['PROPERTY_HREF_VALUE'];
                    $arBanner['COLOR']           = $row['PROPERTY_COLOR_VALUE'];
                    $this->arResult['BANNERS'][] = $arBanner;
                    break;
                case 'Акция':
                    $arBanner['IMAGE']           = CFile::GetPath($row['DETAIL_PICTURE']);
                    $arBanner['NAME']            = $row['NAME'];
                    $arBanner['DESCRIPTION']     = $row['PREVIEW_TEXT'];
                    $arBanner['TYPE']            = $row['PROPERTY_TYPE_VALUE'];
                    $arBanner['HREF']            = $row['PROPERTY_HREF_VALUE'];
                    $arBanner['PRICE']           = $row['PROPERTY_PRICE_VALUE'];
                    $arBanner['PRICE_OLD']       = $row['PROPERTY_PRICE_OLD_VALUE'];
                    $arBanner['DISCOUNT']        = $row['PROPERTY_DISCOUNT_VALUE'];
                    $arBanner['COLOR']           = $row['PROPERTY_COLOR_VALUE'];
                    $this->arResult['BANNERS'][] = $arBanner;
                    break;

            }
            unset($arBanner);
        }

        if ($this->arResult['BANNERS'] && $this->arResult['BIG_BANNER']) {
            $this->IncludeComponentTemplate();
        }
    }

}

?>