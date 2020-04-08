<?php
/**
 * Individ module
 *
 * @category	Individ
 * @package		Iblock
 * @link		http://individ.ru
 * @revision	$Revision$
 * @date		$Date$
 */

namespace Indi\Main\Iblock\Product;

use Indi\Main\Cache;
use Indi\Main\Hlblock\Country;
use Indi\Main\Iblock;
use Indi\Main\Util;

/**
 * Инфоблок плитка
 *
 * @category	Individ
 * @package		Iblock
 */
class Plitka extends Iblock\Prototype
{
    /**
     * Возвращает инфоблок плитка
     *
     * @return Plitka
     */
    public static function getInstance()
    {
        return parent::getInstance();
    }

    /**
     * Добавление / Обновление элемента инфоблока по внешнему коду
     *
     * @param array $arFields Поля элемента
     * @param CIBlockElement $el объект класса Элемент Инфоблока
     * @param string $entityName Название сущности ( для логов )
     * @param array $arProps Массив свойств
     *
     * @return integer $elementID id элемента
     */

    public static function addUpdateElement($arFields, $el, $entityName, $arProps = array()) {
        $ElemId = parent::addUpdateElement($arFields, $el, $entityName, $arProps = array());
        return $ElemId;
    }

    /**
     * Возвращает секции инфоблока с параметром UF_NEW UF_STOCK UF_HIT
     *
     * @return integer $countSection количество элементов
     */

    public function getSectionsListUfHitStockNew($uf = '') {
        if(!$uf)
            return '';
        $arSelect = array("ID");
        $arFilter= array("IBLOCK_ID" => $this->id, $uf => 1);
        $arNavStartParams = array("nTopCount" => 1000);
        $arResult['PLITKA']['SECTIONS']['NEW'] = parent::getSectionsList('', $arFilter, $arSelect, $arNavStartParams);
        return count($arResult['PLITKA']['SECTIONS']['NEW']);
    }

    /**
     * Импорт Плитки
     * importCatalog
     *
     * @param $fileName
     *
     * @return bool
     */
    public function importCatalog($fileName)
    {
//        BXClearCache(true, "/Indi/");
        $xml = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/upload/import/'.$fileName);
        $arXml = \Indi\Main\Xml\XmlToArray::createArray($xml);

        try {
            foreach ($arXml["Каталог"]["СписокЭлементов"] as $arElement) {
                switch($arElement['@attributes']['Имя']) {
                    case 'Коллекции':
                        $this->importSections($arElement['Элемент']);
                        break;
                    case 'Плитка':
                        if($arXml["Каталог"]["СписокЭлементов"][2]['@attributes']['Имя'] === 'КоллекцияЦветаМН'){
                            $this->importElements($arElement['Элемент'], $arXml["Каталог"]["СписокЭлементов"][2]);
                        } else {
                            $this->importElements($arElement['Элемент']);
                        }
                        break;
                }
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
            return false;
        }

//        echo 'Обработка завершена';
        return true;
    }

    /**
     * Импорт элементов Плитки
     * importElements
     * @param $arElements
     */
    protected function importElements($arElements, $arMainColor)
    {
        global $USER;
        $brand = \Indi\Main\Iblock\Prototype::getInstance(\Indi\Main\Iblock\ID_Product_Brands);
        $brands = $brand->getList();

        $idBrand = 0;

        $plitka = \Indi\Main\Iblock\Product\Plitka::getInstance();

        $el = new \CIblockElement;
        foreach ($arElements as $arElement) {
            $arProps = array();
            foreach ($arElement["Реквизиты"]["Реквизит"] as $prop) {
                switch ($prop["Наименование"]) {
                    case 'Артикул':
                        $arProps['ARTICUL'] = $prop["Значение"]["@value"];
                        break;
                    case 'Бренд' :
                        $arProps['BRAND'] = $prop;
                        foreach ($brands as $keyBrand => $arBrand){
                            if($prop["Значение"]['ID'] === $arBrand['XML_ID']){
                                $arProps['BRAND'] = $arBrand['ID'];
                            } else {
                                $arFieldsBrand = array(
                                    "MODIFIED_BY"    => $USER->GetID(), // элемент изменен текущим пользователем
                                    "IBLOCK_SECTION_ID" => '',          // элемент лежит в корне раздела
                                    "IBLOCK_ID"      => \Indi\Main\Iblock\ID_Product_Brands,
                                    "PROPERTY_VALUES"=> array(
                                        45 => 17
                                    ),
                                    'NAME'           => $prop["Значение"]['Наименование'],
                                    "ACTIVE"         => 'Y',            // активен
                                    "PREVIEW_TEXT"   => "текст для списка элементов",
                                    "DETAIL_TEXT"    => "текст для детального просмотра",
                                    "XML_ID"         => $prop["Значение"]['ID'],
                                );

                                $elBrand = new \CIBlockElement;
                                try {
                                    $idBrand = $this->addUpdateElement($arFieldsBrand, $elBrand, '', array());
                                    $arProps['BRAND'] = $idBrand;
//                                    echo "New ID_BRAND: " . $idBrand;
                                } catch (ErrorException $e) {
                                    echo "Error: " . $e;
                                }
                            }
                        }
                        unset($brands);
                        break;
                    case 'Активность':
                        $arProps['ACTIVE'] = $prop["Значение"]["@value"] == 'true' ? 'Y' : 'N';
                        break;
                    case 'Акция':
                        $arProps['STOCK'] = $prop["Значение"]["@value"] == 'true' ? 22 : 0;
                        break;
                    case 'Изображение':
                        if(file_exists($_SERVER['DOCUMENT_ROOT'].'/upload/import/images/plitka/'.$prop['Значение']['@value'])){
                            $arProps['IMG'] = $_SERVER['DOCUMENT_ROOT'].'/upload/import/images/plitka/'.$prop['Значение']['@value'];
                        }
                        break;
                    case 'Коллекция':
                        $arSections = array();
                        $sections = $plitka->getSections('', array('LEFT_MARGIN' => 'ASC', 'SORT' => 'ASC'), 0);
                        foreach ($sections as $keySect=> $arSect){
                            $arSections[] = $arSect["XML_ID"];
                        }
                        if(in_array($prop["Значение"]["ID"], $arSections)){
                            $sections1 = $plitka->getSections('', array('LEFT_MARGIN' => 'ASC', 'SORT' => 'ASC'), 0);
                            foreach ($sections1 as $keySect=> $arSect){
                                if($prop['Значение']['ID'] === $arSect["XML_ID"]){
                                    $arProps['SECT'] = $arSect['ID'];
                                    /*Util::debug("Добавляем");
                                    Util::debug($arProps['SECT']);*/
                                }
                            }
                        } else {
                            $arFieldsSect = array(
                                "MODIFIED_BY" => $USER->GetID(), // элемент изменен текущим пользователем
                                "IBLOCK_SECTION_ID" => false,          // элемент лежит в корне раздела
                                "IBLOCK_ID" => \Indi\Main\Iblock\ID_Product_Plitka,
                                'NAME' => $prop['Значение']["Наименование"],
                                "ACTIVE" => "N",            // активен
                                "CODE" => \Indi\Main\Util::translit($prop['Значение']["Наименование"]),
                                "XML_ID" => $prop["Значение"]["ID"],
                            );

                            $el = new \CIBlockSection;
                            try {
                                $SECT_ID = $plitka->addUpdateSection($arFieldsSect, $el, '');
                                /*Util::debug($arFieldsSect);
                                Util::debug("Добавляем");
                                Util::debug($SECT_ID);*/
                                $arProps['SECT'] = $SECT_ID;
                            } catch (ErrorException $e) {
                                \Indi\Main\Util::log($entityName . " import update: " . $el->LAST_ERROR); // обновляем поля
                            }
                        }
                        unset($sections, $sections1, $arSections);
                        break;
                    case 'Материал':
                        $arFilterMaterial = array(
                            "UF_XML_ID" => $prop["Значение"]['ID']
                        );

                        $material = \Indi\Main\Hlblock\Material::getInstance();

                        if(count($material->getData($arFilterMaterial)) == 0){
                            $residMaterial = $material->addData(
                                array(
                                    "UF_XML_ID" => $prop["Значение"]['ID'],
                                    "UF_NAME" => $prop["Значение"]['Наименование'],
                                )
                            );
                        }

                        $materials = $material->getElements();

                        foreach ($materials as $keyMaterial => $arMaterial) {
                            if ($arMaterial['UF_XML_ID'] === $prop["Значение"]['ID']) {
                                $arProps['MATERIAL'] = $arMaterial['UF_XML_ID'];
                            }
                        }
                        unset($arFilterMaterial, $material, $materials);
                        break;
                    case 'ТипЭлемента':
                        $arFilterItemType = array(
                            "UF_XML_ID" => $prop["Значение"]['ID']
                        );

                        $itemtype = \Indi\Main\Hlblock\Itemtype::getInstance();

                        if(count($itemtype->getData($arFilterItemType)) == 0){
                            $residMaterial = $itemtype->addData(
                                array(
                                    "UF_XML_ID" => $prop["Значение"]['ID'],
                                    "UF_NAME" => $prop["Значение"]['Наименование'],
                                )
                            );
                        }

                        $itemtypes = $itemtype->getElements();

                        foreach ($itemtypes as $keyItemType => $arItemType) {
                            if ($arItemType['UF_XML_ID'] === $prop["Значение"]['ID']) {
                                $arProps['ITEM_TYPE'] = $arItemType['UF_XML_ID'];
                            }
                        }
                        unset($arFilterItemType, $itemtype, $itemtypes);
                        break;
                    case 'Рисунок':
                        $arFilterPicture = array(
                            "UF_XML_ID" => $prop["Значение"]['ID']
                        );

                        $picture = \Indi\Main\Hlblock\Picture::getInstance();

                        if(count($picture->getData($arFilterPicture)) == 0){
                            $residPicture = $picture->addData(
                                array(
                                    "UF_XML_ID" => $prop["Значение"]['ID'],
                                    "UF_NAME" => $prop["Значение"]['Наименование'],
                                )
                            );
                        }

                        $pictures = $picture->getElements();

                        foreach ($pictures as $keyPicture => $arPicture) {
                            if ($arPicture['UF_XML_ID'] === $prop["Значение"]['ID']) {
                                $arProps['PICTURE'] = $arPicture['UF_XML_ID'];
                            }
                        }
                        unset($arFilterPicture, $picture, $pictures);
                        break;
                    case 'Цвет':
                        $xmlId =  $prop["Значение"]["ID"];
                        $arFilterColor = array(
                            "UF_XML_ID" => $xmlId
                        );

                        $color = \Indi\Main\Hlblock\Color::getInstance();

                        if(count($color->getData($arFilterColor)) == 0){
                            $residColor = $color->addData(
                                array(
                                    "UF_XML_ID" => $xmlId,
                                    "UF_NAME" => $prop['Значение']['Наименование'],
                                    "UF_FILE" =>  \CFile::MakeFileArray($_SERVER['DOCUMENT_ROOT'].'/upload/import/images/plitka/colors/'.$arElement["Реквизиты"]["Реквизит"][10]['Значение']['@value'])
                                )
                            );
                        }

                        $colors = $color->getElements();

                        foreach ($colors as $keyColor => $arColor) {
                            if ($arColor['UF_XML_ID'] === $xmlId) {
                                $arProps['COLOR'] = $arColor['UF_XML_ID'];
                            }
                        }
                        unset($arFilterColor, $color, $colors);
                        break;
                    case 'Назначение':
                        $arFilterPurpose = array(
                            "UF_XML_ID" => $prop["Значение"]['ID']
                        );

                        $purpose = \Indi\Main\Hlblock\Purposeoftiles::getInstance();

                        if(count($purpose->getData($arFilterPurpose)) == 0){
                            $residPurpose = $purpose->addData(
                                array(
                                    "UF_XML_ID" => $prop["Значение"]['ID'],
                                    "UF_NAME" => $prop["Значение"]['Наименование'],
                                )
                            );
                        }

                        $purposes = $purpose->getElements();

                        foreach ($purposes as $keyPurpose => $arPurpose) {
                            if ($arPurpose['UF_XML_ID'] === $prop["Значение"]['ID']) {
                                $arProps['PURPOSE'] = $arPurpose['UF_XML_ID'];
                            }
                        }
                        unset($arFilterPurpose, $purpose, $purposes);
                        break;
                    case 'ДлинаШтуки':
                        $arProps['LENGTH_PIECES'] = $prop["Значение"]["@value"];
                        break;
                    case 'ШиринаШтуки':
                        $arProps['WIDTH_PIECES'] = $prop["Значение"]["@value"];
                        break;
                    case 'ТолщинаШтуки':
                        $arProps['THICKNESS_PIECES'] = $prop["Значение"]["@value"];
                        break;
                    case 'Морозостойкость':
                        $arProps['FROST'] = $prop["Значение"]["@value"] == 'true' ? 3 : 'N';
                        break;
                    case 'Сорт':
                        $arFilterSort = array(
                            "UF_XML_ID" => $prop["Значение"]['ID']
                        );

                        $sort = \Indi\Main\Hlblock\Sort::getInstance();

                        if(count($sort->getData($arFilterSort)) == 0){
                            $residSort = $sort->addData(
                                array(
                                    "UF_XML_ID" => $prop["Значение"]['ID'],
                                    "UF_NAME" => $prop["Значение"]['Наименование'],
                                )
                            );
                        }

                        $sorts = $sort->getElements();

                        foreach ($sorts as $keySort => $arSort) {
                            if ($arSort['UF_XML_ID'] === $prop["Значение"]['ID']) {
                                $arProps['SORT'] = $arSort['UF_XML_ID'];
                            }
                        }
                        unset($arFilterSort, $sort, $sorts);
                        break;
                    case 'БазоваяЕдиницаИзмерения':
                        $arFilterBasic = array(
                            "UF_XML_ID" => $prop["Значение"]['ID']
                        );

                        $basic = \Indi\Main\Hlblock\Basic::getInstance();

                        if(count($basic->getData($arFilterBasic)) == 0){
                            $residBasic = $basic->addData(
                                array(
                                    "UF_XML_ID" => $prop["Значение"]['ID'],
                                    "UF_NAME" => $prop["Значение"]['Наименование'],
                                )
                            );
                        }

                        $basics = $basic->getElements();

                        foreach ($basics as $keyBasic => $arBasic) {
                            if ($arBasic['UF_XML_ID'] === $prop["Значение"]['ID']) {
                                $arProps['BASIC'] = $arBasic['UF_XML_ID'];
                            }
                        }
                        unset($arFilterBasic, $basic, $basics);
                        break;
                    case 'ШтукВКоробке':
                        $arProps['IN_BOX'] = $prop["Значение"]["@value"];
                        break;
                    case 'ШтукВПаллете':
                        $arProps['IN_PALLETE'] = $prop["Значение"]["@value"];
                        break;
                    case 'ВесКоробки':
                        $arProps['BOX_WEIGHT'] = $prop["Значение"]["@value"];
                        break;
                    case 'ВесШтуки':
                        $arProps['ITEM_WEIGHT'] = $prop["Значение"]["@value"];
                        break;
                    case 'ОбъемВКоробке':
                        $arProps['QUANTITY_IN_BOX'] = $prop["Значение"]["@value"];
                        break;
                    case 'ВысотаКор':
                        $arProps['BOX_HEIGHT'] = $prop["Значение"]["@value"];
                        break;
                    case 'ДлинаКор':
                        $arProps['LENGTH_BOX'] = $prop["Значение"]["@value"];
                        break;
                    case 'ШиринаКор':
                        $arProps['WIDTH_BOX'] = $prop["Значение"]["@value"];
                        break;
                    case 'Новинка':
                        $arProps['NEW'] = $prop["Значение"]["@value"] == 'true' ? 21 : 0;
                        break;
                    case 'ХитПродаж':
                        $arProps['HIT'] = $prop["Значение"]["@value"] == 'true' ? 23 : 0;
                        break;
                    case 'Страна':
                        $arFilterCountry = array(
                            "UF_XML_ID" => $prop["Значение"]['ID']
                        );

                        $country = \Indi\Main\Hlblock\Country::getInstance();

                        if(count($country->getData($arFilterCountry)) == 0){
                            $residCountry = $country->addData(
                                array(
                                    "UF_XML_ID" => $prop["Значение"]['ID'],
                                    "UF_NAME" => $prop["Значение"]['Наименование'],
                                )
                            );
                        }

                        $countries = $country->getElements();

                        foreach ($countries as $keyCountry => $arCountry) {
                            if ($arCountry['UF_XML_ID'] === $prop["Значение"]['ID']) {
                                $arProps['COUNTRY'] = $arCountry['UF_XML_ID'];
                            }
                        }

                        unset($arFilterCountry, $country, $countries);
                        break;
                }

            }
//            $SectId = $plitka->getSectionByCode($arProps['SECT']);

            $arFieldsElem = array(
                "MODIFIED_BY"    => $USER->GetID(), // элемент изменен текущим пользователем
                "IBLOCK_SECTION_ID" => $arProps['SECT'],          // элемент лежит в корне раздела
                "IBLOCK_ID"      => \Indi\Main\Iblock\ID_Product_Plitka,
                "PROPERTY_VALUES"=> array(
                    63 => $arProps['ARTICUL'],
                    16 => $idBrand,
                    34 => $arProps['STOCK'],
                    17 => $arProps['MATERIAL'],
                    18 => $arProps['ITEM_TYPE'],
                    19 => $arProps['PICTURE'],
                    20 => $arProps['COLOR'],
                    21 => $arProps['PURPOSE'],
                    56 => $arProps['LENGTH_PIECES'],
                    57 => $arProps['WIDTH_PIECES'],
                    58 => $arProps['THICKNESS_PIECES'],
                    24 => $arProps['FROST'],
                    25 => $arProps['SORT'],
                    26 => $arProps['BASIC'],
                    27 => $arProps['IN_BOX'],
                    28 => $arProps['IN_PALLETE'],
                    29 => $arProps['BOX_WEIGHT'],
                    30 => $arProps['ITEM_WEIGHT'],
                    31 => $arProps['QUANTITY_IN_BOX'],
                    33 => $arProps['NEW'],
                    35 => $arProps['HIT'],
                    59 => $arProps['BOX_HEIGHT'],
                    60 => $arProps['LENGTH_BOX'],
                    61 => $arProps['WIDTH_BOX'],
                    89 => $arProps['COUNTRY'],
                ),
                'NAME'           => $arElement['Наименование'],
                "ACTIVE"         => $arProps['ACTIVE'],            // активен
                "PREVIEW_TEXT"   => "текст для списка элементов",
                "DETAIL_TEXT"    => "текст для детального просмотра",
                "CODE"           => \Indi\Main\Util::translit($arProps['ARTICUL']),
                "XML_ID"         => $arElement['ID'],
                "PREVIEW_PICTURE" => \CFile::MakeFileArray($arProps['IMG']),
                "DETAIL_PICTURE" => \CFile::MakeFileArray($arProps['IMG']),
            );

            $el = new \CIBlockElement;
            try {
                $PRODUCT_ID = $plitka->addUpdateElement($arFieldsElem, $el, '', array());
                \Indi\Main\Util::debug($arFieldsElem);
            } catch (ErrorException $e) {
                \Indi\Main\Util::log($entityName . " import update: " . $el->LAST_ERROR); // обновляем поля
            }
        }
    }

    /**
     * Импорт разделов коллекций
     * importSections
     * @param $arSections
     */
    protected function importSections($arSections)
    {
        global $USER;
        $brand = \Indi\Main\Iblock\Prototype::getInstance(\Indi\Main\Iblock\ID_Product_Brands);
        $brands = $brand->getList();

        $idBrand = 0;

        $plitka = \Indi\Main\Iblock\Product\Plitka::getInstance();

        $el = new \CIBlockSection;
        foreach ($arSections as $arElement) {
            $arProps = array();
            foreach ($arElement["Реквизиты"]["Реквизит"] as $prop) {
                switch ($prop["Наименование"]) {
                    case 'Бренд' :
                        $arProps['BRAND'] = $prop;
                        foreach ($brands as $keyBrand => $arBrand){
                            if($prop["Значение"]['ID'] == $arBrand['XML_ID']){
                                $arResultElem['BRAND'] = $arBrand['ID'];
                            } else {
                                $arFieldsBrand = array(
                                    "MODIFIED_BY"    => $USER->GetID(), // элемент изменен текущим пользователем
                                    "IBLOCK_SECTION_ID" => '',          // элемент лежит в корне раздела
                                    "IBLOCK_ID"      => \Indi\Main\Iblock\ID_Product_Brands,
                                    "PROPERTY_VALUES"=> array(
                                        45 => 17
                                    ),
                                    'NAME'           => $prop["Значение"]['Наименование'],
                                    "ACTIVE"         => 'Y',            // активен
                                    "PREVIEW_TEXT"   => "текст для списка элементов",
                                    "DETAIL_TEXT"    => "текст для детального просмотра",
                                    "XML_ID"         => $prop["Значение"]['ID'],
                                );

                                $elBrand = new \CIBlockElement;
                                try {
                                    $idBrand = $this->addUpdateElement($arFieldsBrand, $elBrand, '', array());
//                                    echo "New ID_BRAND: " . $idBrand;
                                } catch (ErrorException $e) {
                                    echo "Error: " . $e;
                                }
                            }
                        }
                        unset($brands);
                        break;
                    case 'Активность':
                        $arProps['ACTIVE'] = $prop["Значение"]["@value"] == 'true' ? 'Y' : 'N';
                        break;
                    case 'Морозостойкость':
                        $arProps['FROST'] = $prop["Значение"]["@value"] == 'true' ? 'Y' : 'N';
                        break;
                    case 'НаименованиеДляСайта':
                        $arProps['NAME'] = $prop["Значение"]["@value"];
                        break;
                    case 'Описание':
                        $arProps['DESC'] = $prop["Значение"]["@value"];
                        break;
                    case 'НаименованиеАнглийское':
                        $arProps['NAME_EN'] = $prop["Значение"]["@value"];
                        break;
                    case 'Назначение':
                        $arFilterPurpose = array(
                            "UF_XML_ID" => $prop["Значение"]['ID']
                        );

                        $purpose = \Indi\Main\Hlblock\Purposeoftiles::getInstance();

                        if(count($purpose->getData($arFilterPurpose)) == 0){
                            $residPurpose = $purpose->addData(
                                array(
                                    "UF_XML_ID" => $prop["Значение"]['ID'],
                                    "UF_NAME" => $prop["Значение"]['Наименование'],
                                )
                            );
                        }

                        $purposes = $purpose->getElements();

                        foreach ($purposes as $keyPurpose => $arPurpose) {
                            if ($arPurpose['UF_XML_ID'] === $prop["Значение"]['ID']) {
                                $arProps['PURPOSE'] = $arPurpose['ID'];
                            }
                        }
                        unset($arFilterPurpose, $purpose, $purposes);
                        break;
                    case 'ЦеноваяКатегория':
                        $arFilterCategory = array(
                            "UF_XML_ID" => $prop["Значение"]['ID']
                        );

                        $category = \Indi\Main\Hlblock\Price::getInstance();

                        if(count($category->getData($arFilterCategory)) == 0){
                            $residPrice = $category->addData(
                                array(
                                    "UF_XML_ID" => $prop["Значение"]['ID'],
                                    "UF_NAME" => $prop["Значение"]['Наименование'],
                                )
                            );
                        }

                        $categories = $category->getElements();

                        foreach ($categories as $keyCategories => $arCategory) {
                            if ($arCategory['UF_XML_ID'] === $prop["Значение"]['ID']) {
                                $arProps['CATEGORY_PRICE'] = $arCategory['ID'];
                            }
                        }
                        unset($arFilterCategory, $category, $categories);
                        break;
                    case 'Новинка':
                        $arProps['NEW'] = $prop["Значение"]["@value"] == 'true' ? 1 : 0;
                        break;
                    case 'Акция':
                        $arProps['STOCK'] = $prop["Значение"]["@value"] == 'true' ? 1 : 0;
                        break;
                    case 'ХитПродаж':
                        $arProps['HIT'] = $prop["Значение"]["@value"] == 'true' ? 1 : 0;
                        break;
                    case 'ТипКоллекции':
                        $arFilterTypeCollection = array(
                            "UF_XML_ID" => $prop["Значение"]['ID']
                        );

                        $typecolletction = \Indi\Main\Hlblock\Typecollection::getInstance();

                        if(count($typecolletction->getData($arFilterTypeCollection)) == 0){
                            $residTypeCollection = $typecolletction->addData(
                                array(
                                    "UF_XML_ID" => $prop["Значение"]['ID'],
                                    "UF_NAME" => $prop["Значение"]['Наименование'],
                                )
                            );
                        }

                        $typecolletctions = $typecolletction->getElements();

                        foreach ($typecolletctions as $keyTypeCollection => $arTypeCollection) {
                            if ($arTypeCollection['UF_XML_ID'] === $prop["Значение"]['ID']) {
                                $arProps['TYPE_COLLECTION'] = $arTypeCollection['ID'];
                            }
                        }
                        unset($arFilterTypeCollection, $typecolletction, $typecolletctions);
                        break;
                    case 'Страна':
                        $arFilterCountry = array(
                            "UF_XML_ID" => $prop["Значение"]['ID']
                        );

                        $country = \Indi\Main\Hlblock\Country::getInstance();

                        if(count($country->getData($arFilterCountry)) == 0){
                            $residCountry = $country->addData(
                                array(
                                    "UF_XML_ID" => $prop["Значение"]['ID'],
                                    "UF_NAME" => $prop["Значение"]['Наименование'],
                                )
                            );
                        }

                        $countries = $country->getElements();

                        foreach ($countries as $keyCountry => $arCountry) {
                            if ($arCountry['UF_XML_ID'] === $prop["Значение"]['ID']) {
                                $arProps['COUNTRY'] = $arCountry['ID'];
                            }
                        }
                        unset($arFilterCountry, $country, $countries);
                        break;
                    case 'ФорматКоллекции':
                        $arFilterFormat = array(
                            "UF_XML_ID" => $prop["Значение"]['ID']
                        );

                        $formatcollection = \Indi\Main\Hlblock\Formatcollection::getInstance();

                        if(count($formatcollection->getData($arFilterFormat)) == 0){
                            $residFormat = $formatcollection->addData(
                                array(
                                    "UF_XML_ID" => $prop["Значение"]['ID'],
                                    "UF_NAME" => $prop["Значение"]['Наименование'],
                                )
                            );
                        }

                        $formatcollections = $formatcollection->getElements();

                        foreach ($formatcollections as $keyFormat => $arFormat) {
                            if ($arFormat['UF_XML_ID'] === $prop["Значение"]['ID']) {
                                $arProps['FORMAT_COLLECTIONS'] = $arFormat['ID'];
                            }
                        }
                        unset($arFilterFormat, $formatcollection, $formatcollections);
                        break;
                    case 'Материал':
                        $arFilterMaterial = array(
                            "UF_XML_ID" => $prop["Значение"]['ID']
                        );

                        $material = \Indi\Main\Hlblock\Material::getInstance();

                        if(count($material->getData($arFilterMaterial)) == 0){
                            $residMaterial = $material->addData(
                                array(
                                    "UF_XML_ID" => $prop["Значение"]['ID'],
                                    "UF_NAME" => $prop["Значение"]['Наименование'],
                                )
                            );
                        }

                        $materials = $material->getElements();

                        foreach ($materials as $keyMaterial => $arMaterial) {
                            if ($arMaterial['UF_XML_ID'] === $prop["Значение"]['ID']) {
                                $arProps['MATERIAL'] = $arMaterial['ID'];
                            }
                        }
                        unset($arFilterMaterial, $material, $materials);
                        break;
                    case 'ТипПоверхности':
                        $arFilterTypeofsurface = array(
                            "UF_XML_ID" => $prop["Значение"]['ID']
                        );

                        $typeofsurface = \Indi\Main\Hlblock\Typeofsurface::getInstance();

                        if(count($typeofsurface->getData($arFilterTypeofsurface)) == 0){
                            $residTypeofsurface = $typeofsurface->addData(
                                array(
                                    "UF_XML_ID" => $prop["Значение"]['ID'],
                                    "UF_NAME" => $prop["Значение"]['Наименование'],
                                )
                            );
                        }

                        $typeofsurfaces = $typeofsurface->getElements();

                        foreach ($typeofsurfaces as $keyTypeofsurface => $arTypeofsurface) {
                            if ($arTypeofsurface['UF_XML_ID'] === $prop["Значение"]['ID']) {
                                $arProps['TYPE_OF_SURFACE'] = $arTypeofsurface['ID'];
                            }
                        }
                        unset($arFilterTypeofsurface, $typeofsurface, $typeofsurfaces);
                        break;
                    case 'Рисунок':
                        $arFilterPicture = array(
                            "UF_XML_ID" => $prop["Значение"]['ID']
                        );

                        $picture = \Indi\Main\Hlblock\Picture::getInstance();

                        if(count($picture->getData($arFilterPicture)) == 0){
                            $residPicture = $picture->addData(
                                array(
                                    "UF_XML_ID" => $prop["Значение"]['ID'],
                                    "UF_NAME" => $prop["Значение"]['Наименование'],
                                )
                            );
                        }

                        $pictures = $picture->getElements();

                        foreach ($pictures as $keyPicture => $arPicture) {
                            if ($arPicture['UF_XML_ID'] === $prop["Значение"]['ID']) {
                                $arProps['PICTURE'] = $arPicture['ID'];
                            }
                        }
                        unset($arFilterPicture, $picture, $pictures);
                        break;
                }
            }

            $arColors[$arElement["ID"]] = array();

            foreach ($arElement["КоллекцииЗначений"]['КоллекцияЗначений'] as $arCollection){
                switch ($arCollection['@attributes']['Имя']){
                    case 'ИзображенияИЦвета':
                        foreach ($arCollection['СтрокаКоллекции'] as $arRecvizit){
                            /*                            foreach ($arRecvizits['Реквизиты'] as $arRecvizit) {
                                                            $id = $arRecvizit[1]['Значение']['ID'];

                                                            $arFilterColor = array(
                                                                "UF_XML_ID" => $id
                                                            );

                                                            $color = \Indi\Main\Hlblock\Color::getInstance();

                                                            if(count($color->getData($arFilterColor)) == 0 && file_exists($_SERVER['DOCUMENT_ROOT'].'/upload/import/images/plitka/colors/'.$arRecvizit[0]['Значение']['@value'])){
                                                                $residColor = $color->addData(
                                                                    array(
                                                                        "UF_XML_ID" => $id,
                                                                        "UF_NAME" => $arRecvizit[1]['Значение']['Наименование'],
                                                                        "UF_FILE" =>  \CFile::MakeFileArray($_SERVER['DOCUMENT_ROOT'].'/upload/import/images/plitka/colors/'.$arRecvizit[0]['Значение']['@value'])
                                                                    )
                                                                );
                                                            }

                                                            $colors = $color->getElements();

                                                            foreach ($colors as $keyColor => $arColor) {
                                                                if ($arColor['UF_XML_ID'] === $id) {
                                                                    $arProps['COLORS'][$arElement['ID']][] = $arColor['ID'];
                                                                }
                                                            }

                                                            $arProps['IMAGES'][$arElement['ID']][] =  \CFile::MakeFileArray($_SERVER['DOCUMENT_ROOT'].'/upload/import/images/plitka/images_for_colors/'.$arRecvizit[2]['Значение']['@value']);
                                                        }*/

                            /*$id = $arRecvizit['Реквизит'][1]['Значение']['ID'];

                            $arFilterColor = array(
                                "UF_XML_ID" => $id
                            );

                            $color = \Indi\Main\Hlblock\Color::getInstance();

                            if(count($color->getData($arFilterColor)) == 0 && file_exists($_SERVER['DOCUMENT_ROOT'].'/upload/import/images/plitka/colors/'.$arRecvizit['Реквизит'][0]['Значение']['@value']) && $id != ''){
                                $residColor = $color->addData(
                                    array(
                                        "UF_XML_ID" => $id,
                                        "UF_NAME" => $arRecvizit['Реквизит'][1]['Значение']['Наименование'],
                                        "UF_FILE" =>  \CFile::MakeFileArray($_SERVER['DOCUMENT_ROOT'].'/upload/import/images/plitka/colors/'.$arRecvizit['Реквизит'][0]['Значение']['@value'])
                                    )
                                );
                            }

                            $colors = $color->getElements();

                            foreach ($colors as $keyColor => $arColor) {
                                if ($arColor['UF_XML_ID'] === $id) {
                                    $arProps['COLORS'][$arElement['ID']][] = $arColor['ID'];
                                }
                            }
                            if($arRecvizit['Реквизит'][2]['Наименование'] === 'ИмяКартинки'){
                                $arProps['IMAGES'][$arElement['ID']][] =  \CFile::MakeFileArray($_SERVER['DOCUMENT_ROOT'].'/upload/import/images/plitka/images_for_colors/'.$arRecvizit['Реквизит'][2]['Значение']['@value']);
                            }*/

                            if($arRecvizit['Реквизит']){
                                $id = $arRecvizit['Реквизит'][1]['Значение']['ID'];
                            } else {
                                $id = $arRecvizit[1]['Значение']['ID'];
                            }

                            $arFilterColor = array(
                                "UF_XML_ID" => $id
                            );

                            $color = \Indi\Main\Hlblock\Color::getInstance();

                            if(count($color->getData($arFilterColor)) == 0 && file_exists($_SERVER['DOCUMENT_ROOT'].$arRecvizit['Реквизит'][0]['Значение']['@value']) && $id != ''){
                                if($arRecvizit['Реквизит']){
                                    $residColor = $color->addData(
                                        array(
                                            "UF_XML_ID" => $id,
                                            "UF_NAME" => $arRecvizit['Реквизит'][1]['Значение']['Наименование'],
                                            "UF_FILE" =>  \CFile::MakeFileArray($_SERVER['DOCUMENT_ROOT'].$arRecvizit['Реквизит'][0]['Значение']['@value'])
                                        )
                                    );
                                } else {

                                    $residColor = $color->addData(
                                        array(
                                            "UF_XML_ID" => $id,
                                            "UF_NAME" => $arRecvizit[1]['Значение']['Наименование'],
                                            "UF_FILE" =>  \CFile::MakeFileArray($_SERVER['DOCUMENT_ROOT'].$arRecvizit[0]['Значение']['@value'])
                                        )
                                    );

                                }
                            }

                            $colors = $color->getElements();

                            foreach ($colors as $keyColor => $arColor) {
//                                    Util::debug($arColor['UF_XML_ID']);
                                if ($arColor['UF_XML_ID'] === $id) {
                                    $arProps['COLORS'][$arElement['ID']][] = $arColor['ID'];
                                }
                            }

                                if($arRecvizit['Реквизит'][2]['Наименование'] === 'ИмяИзображения'){
//                                    $arProps['IMAGES'][$arElement['ID']][] =  \CFile::MakeFileArray($_SERVER['DOCUMENT_ROOT'].'/upload/import/images/plitka/images_for_colors/'.$arRecvizit['Реквизит'][2]['Значение']['@value']);
                                    $arProps['IMAGES'][$arElement['ID']][] =  \CFile::MakeFileArray($_SERVER['DOCUMENT_ROOT'].$arRecvizit['Реквизит'][2]['Значение']['@value']);
                                } elseif($arRecvizit[2]['Наименование'] === 'ИмяИзображения'){
//                                    $arProps['IMAGES'][$arElement['ID']][] =  \CFile::MakeFileArray($_SERVER['DOCUMENT_ROOT'].'/upload/import/images/plitka/images_for_colors/'.$arRecvizit[2]['Значение']['@value']);
                                    $arProps['IMAGES'][$arElement['ID']][] =  \CFile::MakeFileArray($_SERVER['DOCUMENT_ROOT'].$arRecvizit[2]['Значение']['@value']);
                                }

                        }
                        unset($arFilterColor, $color, $colors);
                        break;
                    case 'ПохожиеКоллекции':
                        $sections = $plitka->getSections('', array('LEFT_MARGIN' => 'ASC', 'SORT' => 'ASC'), 0);
//                        foreach ($arCollection['СтрокаКоллекции'] as $arRecvizitsCollection){
//                            if($arRecvizitsCollection['Реквизиты']){
//                                Util::debug("ВЕРХ");
//                                Util::debug($arRecvizitsCollection);
//                                foreach ($arRecvizitsCollection['Реквизиты'] as $arRecvizitCollection) {
//                                    foreach ($arRecvizitCollection/*['Реквизит']*/ as $arValCollection){
//                                        if($arValCollection["Наименование"] === 'АналогКоллекции'){
//                                            $arSectRelated = array();
//                                            foreach ($sections as $keySect=> $arSect){
//                                                $arSectRelated[] = $arSect["XML_ID"];
//                                            }
//                                            if(in_array($arValCollection['Значение']['ID'], $arSectRelated)){
//                                                $sections1 = $plitka->getSections('', array('LEFT_MARGIN' => 'ASC', 'SORT' => 'ASC'), 0);
//                                                foreach ($sections1 as $keySect=> $arSect){
//                                                    if($arValCollection['Значение']['ID'] === $arSect["XML_ID"]){
//                                                        $arProps['RELATED_COLLECTIONS'][$arElement['ID']][] = $arSect['ID'];
//                                                    }
//                                                }
//                                            } else {
//                                                $arFieldsSectRelated = array(
//                                                    "MODIFIED_BY" => $USER->GetID(), // элемент изменен текущим пользователем
//                                                    "IBLOCK_SECTION_ID" => false,          // элемент лежит в корне раздела
//                                                    "IBLOCK_ID" => \Indi\Main\Iblock\ID_Product_Plitka,
//                                                    'NAME' => $arValCollection['Значение']["Наименование"],
//                                                    "ACTIVE" => "N",            // активен
//                                                    "CODE" => \Indi\Main\Util::translit($arValCollection['Значение']["Наименование"]),
//                                                    "XML_ID" => $arValCollection['Значение']['ID'],
//                                                );
//
//                                                $el = new \CIBlockSection;
//                                                try {
//                                                    $arProps['RELATED_COLLECTIONS'][$arElement['ID']][] = $plitka->addUpdateSection($arFieldsSectRelated, $el, '');
//                                                } catch (ErrorException $e) {
//                                                    \Indi\Main\Util::log($entityName . " import update: " . $el->LAST_ERROR); // обновляем поля
//                                                }
//                                            }
//                                        }
//                                    }
//                                }
//                            } else {
//                                Util::debug("НИЗ");
//                                Util::debug($arRecvizitsCollection);
//                                foreach ($arRecvizitsCollection['Реквизит'] as $arValCollection){
//                                    if($arValCollection["Наименование"] === 'АналогКоллекции'){
//                                        $arSectRelated = array();
//                                        foreach ($sections as $keySect=> $arSect){
//                                            $arSectRelated[] = $arSect["XML_ID"];
//                                        }
//                                        if(in_array($arValCollection['Значение']['ID'], $arSectRelated)){
//                                            $sections1 = $plitka->getSections('', array('LEFT_MARGIN' => 'ASC', 'SORT' => 'ASC'), 0);
//                                            foreach ($sections1 as $keySect=> $arSect){
//                                                if($arValCollection['Значение']['ID'] === $arSect["XML_ID"]){
//                                                    $arProps['RELATED_COLLECTIONS'][$arElement['ID']][] = $arSect['ID'];
//                                                }
//                                            }
//                                        } else {
//                                            $arFieldsSectRelated = array(
//                                                "MODIFIED_BY" => $USER->GetID(), // элемент изменен текущим пользователем
//                                                "IBLOCK_SECTION_ID" => false,          // элемент лежит в корне раздела
//                                                "IBLOCK_ID" => \Indi\Main\Iblock\ID_Product_Plitka,
//                                                'NAME' => $arValCollection['Значение']["Наименование"],
//                                                "ACTIVE" => "N",            // активен
//                                                "CODE" => \Indi\Main\Util::translit($arValCollection['Значение']["Наименование"]),
//                                                "XML_ID" => $arValCollection['Значение']['ID'],
//                                            );
//
//                                            $el = new \CIBlockSection;
//                                            try {
//                                                $arProps['RELATED_COLLECTIONS'][$arElement['ID']] = $plitka->addUpdateSection($arFieldsSectRelated, $el, '');
//                                            } catch (ErrorException $e) {
//                                                \Indi\Main\Util::log($entityName . " import update: " . $el->LAST_ERROR); // обновляем поля
//                                            }
//                                        }
//                                    }
//                                }
//                            }
//                        }
                        foreach ($arCollection['СтрокаКоллекции'] as $arRecvizitsCollection){
                            if($arRecvizitsCollection['Реквизит']){
                                foreach ($arRecvizitsCollection as $arRecvizitCollection) {
                                    if($arRecvizitCollection["Наименование"] === 'АналогКоллекции'){
                                        $arSectRelated = array();
                                        foreach ($sections as $keySect=> $arSect){
                                            $arSectRelated[] = $arSect["XML_ID"];
                                        }
                                        if(in_array($arRecvizitCollection['Значение']['ID'], $arSectRelated)){
//                                            $sections1 = $plitka->getSections('', array('LEFT_MARGIN' => 'ASC', 'SORT' => 'ASC'), 0);
                                            foreach ($sections as $keySect=> $arSect){
                                                if($arRecvizitCollection['Значение']['ID'] === $arSect["XML_ID"]){
                                                    $arProps['RELATED_COLLECTIONS'][$arElement['ID']][] = $arSect['ID'];
                                                }
                                            }
                                        } else {
                                            $arFieldsSectRelated = array(
                                                "MODIFIED_BY" => $USER->GetID(), // элемент изменен текущим пользователем
                                                "IBLOCK_SECTION_ID" => false,          // элемент лежит в корне раздела
                                                "IBLOCK_ID" => \Indi\Main\Iblock\ID_Product_Plitka,
                                                'NAME' => $arRecvizitCollection['Значение']["Наименование"],
//                                                "ACTIVE" => "N",            // активен
                                                "CODE" => \Indi\Main\Util::translit($arRecvizitCollection['Значение']["Наименование"]),
                                                "XML_ID" => $arRecvizitCollection['Значение']['ID'],
                                            );

                                            $el = new \CIBlockSection;
                                            try {
                                                $arProps['RELATED_COLLECTIONS'][$arElement['ID']][] = $plitka->addUpdateSection($arFieldsSectRelated, $el, '');
                                            } catch (ErrorException $e) {
                                                \Indi\Main\Util::log($entityName . " import update: " . $el->LAST_ERROR); // обновляем поля
                                            }
                                        }
                                    }
                                }
                            } else {
                                if($arRecvizitsCollection["Наименование"] === 'АналогКоллекции'){
                                    $arSectRelated = array();
                                    foreach ($sections as $keySect=> $arSect){
                                        $arSectRelated[] = $arSect["XML_ID"];
                                    }
                                    if(in_array($arRecvizitsCollection['Значение']['ID'], $arSectRelated)){
                                        $sections1 = $plitka->getSections('', array('LEFT_MARGIN' => 'ASC', 'SORT' => 'ASC'), 0);
                                        foreach ($sections1 as $keySect=> $arSect){
                                            if($arRecvizitsCollection['Значение']['ID'] === $arSect["XML_ID"]){
                                                $arProps['RELATED_COLLECTIONS'][$arElement['ID']][] = $arSect['ID'];
                                            }
                                        }
                                    } else {
                                        $arFieldsSectRelated = array(
                                            "MODIFIED_BY" => $USER->GetID(), // элемент изменен текущим пользователем
                                            "IBLOCK_SECTION_ID" => false,          // элемент лежит в корне раздела
                                            "IBLOCK_ID" => \Indi\Main\Iblock\ID_Product_Plitka,
                                            'NAME' => $arRecvizitsCollection['Значение']["Наименование"],
//                                            "ACTIVE" => "N",            // активен
                                            "CODE" => \Indi\Main\Util::translit($arRecvizitsCollection['Значение']["Наименование"]),
                                            "XML_ID" => $arRecvizitsCollection['Значение']['ID'],
                                        );

                                        $el = new \CIBlockSection;
                                        try {
                                            $arProps['RELATED_COLLECTIONS'][$arElement['ID']][] = $plitka->addUpdateSection($arFieldsSectRelated, $el, '');
                                        } catch (ErrorException $e) {
                                            \Indi\Main\Util::log($entityName . " import update: " . $el->LAST_ERROR); // обновляем поля
                                        }
                                    }
                                }
                            }
                        }
                        unset($sections, $sections1);
                        break;
                    case 'Изображения':
                        foreach ($arCollection['СтрокаКоллекции'] as $keyRecvizits => $arRecvizits){
                            if($keyRecvizits === 'Реквизит'){
//                                $arProps['IMAGES_SLIDER'][$arElement['ID']][] =  \CFile::MakeFileArray($_SERVER['DOCUMENT_ROOT'].'/upload/import/images/plitka/images_for_slider/'.$arRecvizits['Значение']['@value'])
                                $arProps['IMAGES_SLIDER'][$arElement['ID']][] =  \CFile::MakeFileArray($_SERVER['DOCUMENT_ROOT'].$arRecvizits['Значение']['@value']);
                            } else {
//                                $arProps['IMAGES_SLIDER'][$arElement['ID']][] =  \CFile::MakeFileArray($_SERVER['DOCUMENT_ROOT'].'/upload/import/images/plitka/images_for_slider/'.$arRecvizits['Реквизит']['Значение']['@value']);
                                $arProps['IMAGES_SLIDER'][$arElement['ID']][] =  \CFile::MakeFileArray($_SERVER['DOCUMENT_ROOT'].$arRecvizits['Реквизит']['Значение']['@value']);
                            }
                        }
                        break;
                }
            }

            $arFieldsSect = array(
                "MODIFIED_BY" => $USER->GetID(), // элемент изменен текущим пользователем
                "IBLOCK_SECTION_ID" => false,          // элемент лежит в корне раздела
                "IBLOCK_ID" => \Indi\Main\Iblock\ID_Product_Plitka,
                'NAME' => $arProps['NAME'],
                "ACTIVE" => $arProps['ACTIVE'],            // активен
                "DESCRIPTION" => $arProps['DESC'],
                "CODE" => \Indi\Main\Util::translit($arProps['NAME']),
                "XML_ID" => $arElement['ID'],
                "UF_EN_NAME" => $arProps['NAME_EN'],
                "UF_COLOR" => $arProps['COLORS'][$arElement['ID']],
                "UF_NEW" => $arProps['NEW'],
                "UF_STOCK" => $arProps['STOCK'],
                "UF_HIT" => $arProps['HIT'],
                "UF_CATEGORY_PRICE" => $arProps['CATEGORY_PRICE'],
                "UF_PURPOSE" => $arProps['PURPOSE'],
                "UF_PICTURE" => $arProps['PICTURE'],
                "UF_MATERIAL" => $arProps['MATERIAL'],
                "UF_TYPE_OF_SURFACE" => $arProps['TYPE_OF_SURFACE'],
                "UF_TYPE_COLLECTION" => $arProps['TYPE_COLLECTION'],
                "UF_FORMAT_COLLECTION" => $arProps['FORMAT_COLLECTIONS'],
                "UF_COUNTRY" => $arProps['COUNTRY'],
                "UF_BRAND" => $idBrand,
                "UF_FROST" => $arProps['FROST'],
                "UF_SIMILAR" => $arProps['RELATED_COLLECTIONS'][$arElement['ID']],
                "UF_IMAGES_SMALL"  => $arProps['IMAGES'][$arElement['ID']],
                "UF_IMAGES" => $arProps['IMAGES_SLIDER'][$arElement['ID']],
                "PICTURE" => $arProps['IMAGES_SLIDER'][$arElement['ID']][0],
            );

            $el = new \CIBlockSection;
            try {
                $SECT_ID = $plitka->addUpdateSection($arFieldsSect, $el, '');
                Util::debug($arFieldsSect);
            } catch (ErrorException $e) {
                \Indi\Main\Util::log($entityName . " import update: " . $el->LAST_ERROR); // обновляем поля
            }
        }
    }
}