<?

if ( ! defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

class CSeoBrands extends CBitrixComponent
{

    public function executeComponent ()
    {
        $arSelect = Array('ID', 'SORT', 'PREVIEW_PICTURE', 'PROPERTY_COLOR', 'DETAIL_PAGE_URL');
        $arFilter = Array('IBLOCK_ID' => getIBlockIdByCode('brands'), 'ACTIVE' => 'Y');
        $arAllItems = CIBlockElement::GetList(Array('SORT' => 'DESC'), $arFilter, false, Array(), $arSelect);

        while ($row = $arAllItems->fetch()) {

            $prop = CIBlockElement::GetProperty(getIBlockIdByCode('brands'), $row['ID'], array("sort" => "asc"),
                Array("CODE" => "SHOW_ON_MAIN"))->Fetch();

            if ($prop['VALUE_XML_ID'] == 'yes') {
                $this->arResult["ITEMS"][] = [
                    'ID'  => $row['ID'],
                    'SRC' => CFile::GetPath($row['PREVIEW_PICTURE']),
                    'COLOR' => $row['PROPERTY_COLOR_VALUE'],
                ];
            }

        }

        $this->IncludeComponentTemplate();
    }

}

?>