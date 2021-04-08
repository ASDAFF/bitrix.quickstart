<?php
/**
 * Класс для экспорта каталога с использованием XML-файлов
 */

if (!\Bitrix\Main\Loader::IncludeModule('iblock')) {
    return false;
}

class CTWCatalogExport
{
    /**
     * Параметры экспорта
     */
    private $__arSettings = array();

    /**
     * Список строк для экспорта
     */
    private $__rows = array();

    /**
     * $arSettings - массив параметров импорта
     */
    public function __construct(array $arSettings = array())
    {
        $this->__arSettings = array_merge($this->getDefaultSettings(), $arSettings);
    }

    /**
     * Возвращает массив параметров импорта по умолчанию
     */
    public function getDefaultSettings()
    {
        return array(
            // ID инфоблока
            'iblockID' => 5,
            // Куда сохранять json-файл со структурой каталога
            'jsonLocation' => 'export/catalog.json',
            // Свойства элементов, которые не нужно выгружать
            'elForbiddenProps' => array('NAME_CYR', 'NAME_LAT', 'MORE_PHOTO', 'MANUALS', 'VIDEO', 'RELATED', 'OPTIONS', 'POPULAR', 'PREORDER', 'FEATURES'),
        );
    }

    public function generateJson()
    {
        $this->getSections();
        $this->getElements();
        return $this->saveJson();
    }

    /**
     * Получает массив с секциями
     */
    private function getSections()
    {
        $sectionsR = CIBlockSection::GetList(array(), array('ACTIVE' => 'Y', 'IBLOCK_ID' => 5), false, array('UF_CERTS'));

        while ($section = $sectionsR->GetNext()) {
            $ar = array();
            $ar['id'] = $section['ID'];
            $ar['type'] = 'S';
            $ar['parent_id'] = $section['IBLOCK_SECTION_ID'];
            $ar['name'] = $section['NAME'];
            $ar['description'] = $section['~DESCRIPTION'];
            $certs = array();
            if (count($section['UF_CERTS'])) {
                foreach ($section['UF_CERTS'] as $cert) {
                    $file = CFile::GetFileArray($cert);
                    $certs[] = 'http://' . SITE_SERVER_NAME . $file['SRC'];
                }
            }
            $ar['certificates'] = $certs;
            $this->__rows[] = $ar;
        }
    }

    /**
     * Получает массив с элементами
     */
    private function getElements()
    {
        $elementsR = CIBlockElement::GetList(array('SORT' => 'ASC'), array('ACTIVE' => 'Y', 'IBLOCK_ID' => 5), false, false, array('ID', 'IBLOCK_ID', 'NAME', 'IBLOCK_SECTION_ID', 'DETAIL_TEXT', 'DETAIL_PICTURE', 'CATALOG_GROUP_1', 'PROPERTY_*'));

        while ($el = $elementsR->GetNextElement()) {
            $ar = array();
            $arFields = $el->GetFields();
            $arProps = $el->GetProperties();
            $properties = array();
            if (is_array($arProps)) {
                foreach ($arProps as $key => $property) {
                    if (!in_array($key, $this->__arSettings['elForbiddenProps'])) {
                        if (!empty($property['VALUE'])) {
                            $properties[] = array('name' => $property['NAME'], 'value' => $property['VALUE']);
                        }
                    }
                }
            }
            $ar['id'] = $arFields['ID'];
            $ar['type'] = 'E';
            $ar['parent_id'] = $arFields['IBLOCK_SECTION_ID'];
            $ar['name'] = $arFields['NAME'];
            $ar['description'] = $arFields['~DETAIL_TEXT'];
            $ar['price'] = $arFields['CATALOG_PRICE_1'];
            $ar['available'] = ($arFields['CATALOG_QUANTITY'] > 0 ? 1 : 0);
            $photos = array();
            if (isset($arFields['DETAIL_PICTURE'])) {
                $pic = CFile::GetFileArray($arFields['DETAIL_PICTURE']);
                $photos[] = 'http://' . SITE_SERVER_NAME . $pic['SRC'];
            }
            if (count($arProps['MORE_PHOTO']['VALUE'])) {
                if (is_array($arProps['MORE_PHOTO']['VALUE'])) {
                    foreach ($arProps['MORE_PHOTO']['VALUE'] as $value) {
                        $pic = CFile::GetFileArray($value);
                        $photos[] = 'http://' . SITE_SERVER_NAME . $pic['SRC'];
                    }
                }

            }
            $ar['photo'] = $photos;

            $manuals = array();

            if (count($arProps['MANUALS']['VALUE'])) {
                if (is_array($arProps['MANUALS']['VALUE'])) {
                    foreach ($arProps['MANUALS']['VALUE'] as $value) {
                        $man = CFile::GetFileArray($value);
                        $manuals[] = 'http://' . SITE_SERVER_NAME . $man['SRC'];
                    }
                }
            }
            $ar['manuals'] = $manuals;
            $ar['properties'] = $properties;

            $this->__rows[] = $ar;
        }
    }

    private function saveJson()
    {
        $json = json_encode($this->__rows);
        $path = $_SERVER['DOCUMENT_ROOT'] . '/' . $this->__arSettings['jsonLocation'];
        if (file_put_contents($path, $json) === false) {
            return false;
        } else {
            return true;
        }
    }

    public function generateCSV()
    {
        $delimiter = "~";
        $this->getElementsCSV();
        $path = $_SERVER['DOCUMENT_ROOT'] . '/catalog.csv';
        $fh = fopen($path, 'w');
        $str = "ID{$delimiter}1C ID{$delimiter}Название{$delimiter}С этим товаром покупают{$delimiter}Опции{$delimiter}Преимущества{$delimiter}Описание\r\n";
        fwrite($fh, $str);
        foreach ($this->__rows as $row) {
            $str = $row['id'] . $delimiter . $row['xml_id'] . $delimiter . $row['name'] . $delimiter . $row['RELATED'] . $delimiter . $row['OPTIONS'] . $delimiter . $row['FEATURES'] . $delimiter . $row['description'];
            $str = str_replace(array("\r\n", "\r", "\n"), "", $str);
            fwrite($fh, $str . "\r\n");
        }
        fclose($fh);
    }

    private function getElementsCSV()
    {
        $elementsR = CIBlockElement::GetList(array('SORT' => 'ASC'), array('ACTIVE' => 'Y', 'IBLOCK_ID' => 5), false, false, array('ID', 'IBLOCK_ID', 'NAME', 'XML_ID', 'DETAIL_TEXT', 'PROPERTY_*'));

        while ($el = $elementsR->GetNextElement()) {

            $ar = array();
            $arFields = $el->GetFields();
            $arProps = $el->GetProperties();
            $properties = array();
            foreach ($arProps as $key => $property) {
                if (in_array($key, array('RELATED', 'OPTIONS', 'FEATURES'))) {
                    if (!empty($property['VALUE'])) {
                        $properties[$property['CODE']] = implode(',', $property['VALUE']);
                    }
                }
            }

            $ar['id'] = $arFields['ID'];
            $ar['xml_id'] = $arFields['XML_ID'];
            $ar['name'] = $arFields['NAME'];
            $ar['description'] = $arFields['~DETAIL_TEXT'];

            $ar['RELATED'] = $properties['RELATED'];
            $ar['OPTIONS'] = $properties['OPTIONS'];
            $ar['FEATURES'] = $properties['FEATURES'];

            $this->__rows[] = $ar;
            //$i++;
        }
    }
}
