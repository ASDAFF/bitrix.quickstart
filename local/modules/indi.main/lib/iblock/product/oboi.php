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

use Indi\Main\Iblock;
use Indi\Main\Util;

/**
 * Инфоблок Обои
 *
 * @category	Individ
 * @package		Iblock
 */
class Oboi extends Iblock\Prototype
{
    /**
     * Возвращает инфоблок новостей
     *
     * @return Oboi
     */
    public static function getInstance()
    {
        return parent::getInstance();
    }

    /**
     * Импорт обоев
     * importCatalog
     *
     * @param $fileName
     *
     * @return bool
     */
    public function importCatalog($fileName)
    {

        $xml = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/upload/import/'.$fileName);
        $arXml = \Indi\Main\Xml\XmlToArray::createArray($xml);

        try {
            foreach ($arXml["Каталог"]["СписокЭлементов"] as $arElement) {
                switch($arElement['@attributes']['Имя']) {
                    case 'Обои':
                        $this->importElements($arElement['Элемент']);
                        break;
                    case 'Дизайн':
                        $this->importSections($arElement['Элемент']);
                        break;
                }
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
            return false;
        }

//		echo 'Обработка завершена';
        return true;
    }

    /**
     * Импорт разделов коллекций
     * importSections
     * @param $arSections
     */
    protected function importSections($arSections)
    {
        global $USER;
        $oboi = \Indi\Main\Iblock\Product\Oboi::getInstance();
        foreach ($arSections as $arElement) {
            foreach ($arElement["Реквизиты"]["Реквизит"] as $prop){
                switch ($prop["Наименование"]){
                    case 'Активность':
                        $arProps['ACTIVE'] = $prop["Значение"]["@value"] == 'true' ? 'Y' : 'N';
                        break;
                    case 'Описание':
                        $arProps['DESC'] = $prop["Значение"]["@value"];
                        break;
                    case 'СпособПроизводства':
                        $xmlId =  Util::translit($prop["Значение"]["@value"]);
                        $xmlId = mb_strtolower($xmlId);
                        $arFilterProduction = array(
                            "UF_XML_ID" => $xmlId
                        );

                        $production = \Indi\Main\Hlblock\Modeproduction::getInstance();

                        if(count($production->getData($arFilterProduction)) == 0){
                            $residProduction = $production->addData(
                                array(
                                    "UF_XML_ID" => $xmlId,
                                    "UF_NAME" => $prop["Значение"]['@value'],
                                )
                            );
                        }

                        $productions = $production->getElements();

                        foreach ($productions as $keyProduction => $arProduction) {
                            if ($arProduction['UF_XML_ID'] === $xmlId) {
                                $arProps['MODE_PRODUCTION'] = $arProduction['ID'];
                            }
                        }
                        unset($arFilterProduction, $production, $productions);

                        /*$arParams = array("replace_space"=>"-","replace_other"=>"-");
                        $propId = \CUtil::translit($prop["Значение"]["@value"], "ru", $arParams);
                        $prop["Значение"] = array(
                            "ID"  => $propId,
                            "Значение" => $prop["Значение"]["@value"]
                        );

                        $arProps['PRODUCTION'] =  $this->getHlValue('Modeproduction', $prop);*/

                        break;
                    case 'Основа':
                        $xmlId =  Util::translit($prop["Значение"]["@value"]);
                        $xmlId = mb_strtolower($xmlId);
                        $arFilterBasis = array(
                            "UF_XML_ID" => $xmlId
                        );

                        $basis = \Indi\Main\Hlblock\Basis::getInstance();

                        if(count($basis->getData($arFilterBasis)) == 0){
                            $residrBasis = $basis->addData(
                                array(
                                    "UF_XML_ID" => $xmlId,
                                    "UF_NAME" => $prop["Значение"]['@value'],
                                )
                            );
                        }

                        $basises = $basis->getElements();

                        foreach ($basises as $keyBasis => $arBasis) {
                            if ($arBasis['UF_XML_ID'] === $xmlId) {
                                $arProps['BASIS'] = $arBasis['ID'];
                            }
                        }

                        unset($arFilterBasis, $basis, $basises);
                        break;
                    case 'Новинка':
                        $arProps['NEW'] = $prop["Значение"]["@value"] == 'true' ? 1 : 0;
                        break;
                    case 'ХитПродаж':
                        $arProps['HIT'] = $prop["Значение"]["@value"] == 'true' ? 1 : 0;
                        break;
                    case 'Распродажа':
                        $arProps['STOCK'] = $prop["Значение"]["@value"] == 'true' ? 1 : 0;
                        break;
                    case 'Эконом':
                        $arProps['ECONOM'] = $prop["Значение"]["@value"] == 'true' ? 1 : 0;
                        break;
                    case 'Бренд' :
                        $brand = \Indi\Main\Iblock\Prototype::getInstance(\Indi\Main\Iblock\ID_Product_Brands);
                        $brands = $brand->getList();
                        foreach ($brands as $keyBrand => $arBrand){
                            if($prop["Значение"]['ID'] === $arBrand['XML_ID']){
                                $arProps['BRAND'] = $arBrand['ID'];
                            } else {
                                $arFieldsBrand = array(
                                    "MODIFIED_BY"    => $USER->GetID(), // элемент изменен текущим пользователем
                                    "IBLOCK_SECTION_ID" => '',          // элемент лежит в корне раздела
                                    "IBLOCK_ID"      => \Indi\Main\Iblock\ID_Product_Brands,
                                    "PROPERTY_VALUES"=> array(
                                        46 => 19
                                    ),
                                    'NAME'           => $prop["Значение"]['Наименование'],
                                    "ACTIVE"         => 'Y',            // активен
                                    "PREVIEW_TEXT"   => "текст для списка элементов",
                                    "DETAIL_TEXT"    => "текст для детального просмотра",
                                    "XML_ID"         => $prop["Значение"]['ID'],
                                );

                                $elBrand = new \CIBlockElement;
                                try {
                                    $arProps['BRAND'] = $this->addUpdateElement($arFieldsBrand, $elBrand, '', array());
//                                    echo "New ID_BRAND: " . $idBrand;
                                } catch (ErrorException $e) {
                                    echo "Error: " . $e;
                                }
                            }
                        }
                        unset($brands);
                        break;
                    case 'Длина':
                        $xmlId =  Util::translit($prop["Значение"]["@value"]);
                        $xmlId = mb_strtolower($xmlId);
                        $arFilterLength = array(
                            "UF_XML_ID" => $xmlId
                        );

                        $length = \Indi\Main\Hlblock\Lengthpieces::getInstance();

                        if(count($length->getData($arFilterLength)) == 0){
                            $residLength = $length->addData(
                                array(
                                    "UF_XML_ID" => $xmlId,
                                    "UF_NAME" => $prop["Значение"]['@value'],
                                )
                            );
                        }

                        $lengths = $length->getElements();

                        foreach ($lengths as $keyLength => $arLength) {
                            if ($arLength['UF_XML_ID'] === $xmlId) {
                                $arProps['LENGTH'] = $arLength['ID'];
                            }
                        }
                        unset($arFilterLength, $length, $lengths);
                        break;
                    case 'Ширина':
                        $xmlId =  Util::translit($prop["Значение"]["@value"]);
                        $xmlId = mb_strtolower($xmlId);
                        $arFilterWidth = array(
                            "UF_XML_ID" => $xmlId
                        );

                        $width = \Indi\Main\Hlblock\Widthpieces::getInstance();

                        if(count($width->getData($arFilterWidth)) == 0){
                            $residWidth = $width->addData(
                                array(
                                    "UF_XML_ID" => $xmlId,
                                    "UF_NAME" => $prop["Значение"]['@value'],
                                )
                            );
                        }

                        $widths = $width->getElements();

                        foreach ($widths as $keyWidth => $arWidth) {
                            if ($arWidth['UF_XML_ID'] === $xmlId) {
                                $arProps['WIDTH'] = $arWidth['ID'];
                            }
                        }
                        unset($arFilterLength, $width, $widths);
                        break;
                    case 'Раппорт':
                        $xmlId =  Util::translit($prop["Значение"]["@value"]);
                        $xmlId = mb_strtolower($xmlId);
                        $arFilterRapport = array(
                            "UF_XML_ID" => $xmlId
                        );

                        $rapport = \Indi\Main\Hlblock\Rapport::getInstance();

                        if(count($rapport->getData($arFilterRapport)) == 0){
                            $residRapport = $rapport->addData(
                                array(
                                    "UF_XML_ID" => $xmlId,
                                    "UF_NAME" => $prop["Значение"]['@value'],
                                )
                            );
                        }

                        $rapports = $rapport->getElements();

                        foreach ($rapports as $keyRapport => $arRapport) {
                            if ($arRapport['UF_XML_ID'] === $xmlId) {
                                $arProps['RAPPORT'] = $arRapport['ID'];
                            }
                        }
                        unset($arFilterRapport, $rapport, $rapports);
                        break;
                    case 'Рисунок':
                        $xmlId =  $prop["Значение"]["ID"];
                        $xmlId = mb_strtolower($xmlId);
                        $arFilterPicture = array(
                            "UF_XML_ID" => $xmlId
                        );

                        $picture = \Indi\Main\Hlblock\Risunok::getInstance();

                        if(count($picture->getData($arFilterPicture)) == 0){
                            $residPicture = $picture->addData(
                                array(
                                    "UF_XML_ID" => $xmlId,
                                    "UF_NAME" => $prop["Значение"]['Наименование'],
                                )
                            );
                        }

                        $pictures = $picture->getElements();

                        foreach ($pictures as $keyPicture => $arPicture) {
                            if ($arPicture['UF_XML_ID'] === $xmlId) {
                                $arProps['PICTURE'] = $arPicture['ID'];
                            }
                        }
                        unset($arFilterPicture, $picture, $pictures);
                        break;
                    case 'Страна':
                        $xmlId =  $prop["Значение"]["ID"];
                        $xmlId = mb_strtolower($xmlId);
                        $arFilterCountry = array(
                            "UF_XML_ID" => $xmlId
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
                            if ($arCountry['UF_XML_ID'] === $xmlId) {
                                $arProps['COUNTRY'] = $arCountry['ID'];
                            }
                        }
                        unset($arFilterCountry, $country, $countries);
                        break;
                    case 'ЦеноваяКатегория':
                        $xmlId =  $prop["Значение"]["ID"];
                        $xmlId = mb_strtolower($xmlId);
                        $arFilterCategory = array(
                            "UF_XML_ID" => $xmlId
                        );

                        $category = \Indi\Main\Hlblock\Tsenovayakategoriya::getInstance();

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
                            if ($arCategory['UF_XML_ID'] === $xmlId) {
                                $arProps['PRICE'] = $arCategory['ID'];
                            }
                        }
                        unset($arFilterCategory, $category, $categories);
                        break;
                    case 'НаименованиеАнглийское':
                        $arProps['NAME_EN'] = $prop["Значение"]["@value"];
                        break;
                }
            }
            foreach ($arElement["КоллекцииЗначений"]['КоллекцияЗначений'] as $arCollection) {
                switch ($arCollection['@attributes']['Имя']) {
                    case 'ИзображенияИЦвета':
                        foreach ($arCollection['СтрокаКоллекции'] as $arRecvizit){
                            /*$id = $arRecvizit[1]['Значение']['ID'];

                            $arFilterColor = array(
                                "UF_XML_ID" => $id
                            );

                            $color = \Indi\Main\Hlblock\Tsvetoboev::getInstance();

                            if(count($color->getData($arFilterColor)) == 0 && file_exists($_SERVER['DOCUMENT_ROOT'].'/upload/import/images/oboi/colors/'.$arRecvizit[0]['Значение']['@value'])){
                                $residColor = $color->addData(
                                    array(
                                        "UF_XML_ID" => $id,
                                        "UF_NAME" => $arRecvizit[1]['Значение']['Наименование'],
                                        "UF_FILE" =>  \CFile::MakeFileArray($_SERVER['DOCUMENT_ROOT'].'/upload/import/images/oboi/colors/'.$arRecvizit[0]['Значение']['@value'])
                                    )
                                );
                            }

                            $colors = $color->getElements();

                            foreach ($colors as $keyColor => $arColor) {
                                if ($arColor['UF_XML_ID'] === $id) {
                                    $arProps['COLORS'][$arElement['ID']][] = $arColor['ID'];
                                }
                            }

                            $arProps['IMAGES'][$arElement['ID']][] =  \CFile::MakeFileArray($_SERVER['DOCUMENT_ROOT'].'/upload/import/images/oboi/images_for_colors/'.$arRecvizit[2]['Значение']['@value']);*/

//                            Util::debug($arRecvizit['Реквизит'][1]['Значение']['ID']);
                            if($arRecvizit['Реквизит']){
                                $id = $arRecvizit['Реквизит'][1]['Значение']['ID'];
                            } else {
                                $id = $arRecvizit[1]['Значение']['ID'];
                            }

                            $arFilterColor = array(
                                "UF_XML_ID" => $id
                            );

                            $color = \Indi\Main\Hlblock\Tsvetoboev::getInstance();

                            if(count($color->getData($arFilterColor)) == 0 && file_exists($_SERVER['DOCUMENT_ROOT'].$arRecvizit['Реквизит'][0]['Значение']['@value']) && $id != ''){
                                if($arRecvizit['Реквизит']){
                                    $residColor = $color->addData(
                                        array(
                                            "UF_XML_ID" => $id,
                                            "UF_NAME" => $arRecvizit['Реквизит'][1]['Значение']['Наименование'],
                                            "UF_FILE" =>  \CFile::MakeFileArray($_SERVER['DOCUMENT_ROOT'].$arRecvizit['Реквизит'][0]['Значение']['@value'])
                                        )
                                    );
                                    Util::debug(
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
                                    Util::debug(
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
                                if ($arColor['UF_XML_ID'] === $id) {
                                    $arProps['COLORS'][$arElement['ID']][] = $arColor['ID'];
                                }
                            }

                            if($arRecvizit['Реквизит'][2]['Наименование'] === 'ИмяКартинки'){
                                $arProps['IMAGES'][$arElement['ID']][] =  \CFile::MakeFileArray($_SERVER['DOCUMENT_ROOT'].$arRecvizit['Реквизит'][2]['Значение']['@value']);
                            } elseif($arRecvizit[2]['Наименование'] === 'ИмяКартинки'){
                                $arProps['IMAGES'][$arElement['ID']][] =  \CFile::MakeFileArray($_SERVER['DOCUMENT_ROOT'].$arRecvizit[2]['Значение']['@value']);
                            }
                        }
                        unset($arFilterColor, $color, $colors);
                        break;
                    case 'ПохожиеДизайны':
                        $sections = $oboi->getSections('', array('LEFT_MARGIN' => 'ASC', 'SORT' => 'ASC'), 0);
                        $arSections = array();
                        foreach ($sections as $arSection){
                            $arSections[] = $arSection['XML_ID'];
                        }
                        if($arCollection['СтрокаКоллекции']['Реквизит']){
                            if(in_array($arCollection['СтрокаКоллекции']['Реквизит']['Значение']['@value'] ,$arSections)){
                                foreach ($sections as $arrSect){
                                    if($arrSect['XML_ID'] === $arCollection['СтрокаКоллекции']['Реквизит']['Значение']['@value']){
                                        $arProps['RELATED_COLLECTIONS'][$arElement['ID']][] = $arrSect['ID'];
                                    }
                                }
                            } else {
                                $arFieldsSectRelated = array(
                                    "MODIFIED_BY" => $USER->GetID(), // элемент изменен текущим пользователем
                                    "IBLOCK_SECTION_ID" => false,          // элемент лежит в корне раздела
                                    "IBLOCK_ID" => \Indi\Main\Iblock\ID_Product_Oboi,
                                    'NAME' => $arCollection['СтрокаКоллекции']['Реквизит']['Значение']['@value'],
                                    "ACTIVE" => "N",            // активен
                                    "CODE" => \Indi\Main\Util::translit($arCollection['СтрокаКоллекции']['Реквизит']['Значение']['@value']),
                                    "XML_ID" => $arCollection['СтрокаКоллекции']['Реквизит']['Значение']['@value'],
                                );

                                $el = new \CIBlockSection;
                                try {
                                    $arProps['RELATED_COLLECTIONS'][$arElement['ID']][] = $oboi->addUpdateSection($arFieldsSectRelated, $el, '');
                                } catch (ErrorException $e) {
                                    \Indi\Main\Util::log($entityName . " import update: " . $el->LAST_ERROR); // обновляем поля
                                }
                            }
                        } else {
                            foreach ($arCollection['СтрокаКоллекции'] as $arRecvizitsCollection){
                                if(in_array($arRecvizitsCollection['Реквизит']['Значение']['@value'] ,$arSections)){
                                    foreach ($sections as $arrSect){
                                        if($arRecvizitsCollection['Реквизит']['Значение']['@value'] === $arrSect['XML_ID']){
                                            $arProps['RELATED_COLLECTIONS'][$arElement['ID']][] = $arrSect['ID'];
                                        }
                                    }
                                } else {
                                    $arFieldsSectRelated = array(
                                        "MODIFIED_BY" => $USER->GetID(), // элемент изменен текущим пользователем
                                        "IBLOCK_SECTION_ID" => false,          // элемент лежит в корне раздела
                                        "IBLOCK_ID" => \Indi\Main\Iblock\ID_Product_Oboi,
                                        'NAME' => $arRecvizitsCollection['Реквизит']['Значение']['@value'],
                                        "ACTIVE" => "N",            // активен
                                        "CODE" => \Indi\Main\Util::translit($arRecvizitsCollection['Реквизит']['Значение']['@value']),
                                        "XML_ID" => $arRecvizitsCollection['Реквизит']['Значение']['@value'],
                                    );

                                    $el = new \CIBlockSection;
                                    try {
                                        $arProps['RELATED_COLLECTIONS'][$arElement['ID']][] = $oboi->addUpdateSection($arFieldsSectRelated, $el, '');
                                    } catch (ErrorException $e) {
                                        \Indi\Main\Util::log($entityName . " import update: " . $el->LAST_ERROR); // обновляем поля
                                    }
                                }
                            }

                        }
                        /*                        foreach ($arCollection['СтрокаКоллекции'] as $arRecvizitsCollection){
                                                    if($arRecvizitsCollection['Реквизит']){
                                                        foreach ($arRecvizitsCollection as $arRecvizitCollection) {
                                                            if($arRecvizitCollection["Наименование"] === 'АналогКоллекции'){
                                                                $arSectRelated = array();
                                                                foreach ($sections as $keySect=> $arSect){
                                                                    $arSectRelated[] = $arSect["XML_ID"];
                                                                }
                                                                if(in_array($arRecvizitCollection['Значение']['ID'], $arSectRelated)){
                                                                    $sections1 = $oboi->getSections('', array('LEFT_MARGIN' => 'ASC', 'SORT' => 'ASC'), 0);
                                                                    foreach ($sections1 as $keySect=> $arSect){
                                                                        if($arRecvizitCollection['Значение']['ID'] === $arSect["XML_ID"]){
                                                                            $arProps['RELATED_COLLECTIONS'][$arElement['ID']][] = $arSect['ID'];
                                                                        }
                                                                    }
                                                                } else {
                                                                    $arFieldsSectRelated = array(
                                                                        "MODIFIED_BY" => $USER->GetID(), // элемент изменен текущим пользователем
                                                                        "IBLOCK_SECTION_ID" => false,          // элемент лежит в корне раздела
                                                                        "IBLOCK_ID" => \Indi\Main\Iblock\ID_Product_Oboi,
                                                                        'NAME' => $arRecvizitCollection['Значение']["Наименование"],
                                                                        "ACTIVE" => "N",            // активен
                                                                        "CODE" => \Indi\Main\Util::translit($arRecvizitCollection['Значение']["Наименование"]),
                                                                        "XML_ID" => $arRecvizitCollection['Значение']['ID'],
                                                                    );

                                                                    $el = new \CIBlockSection;
                                                                    try {
                                                                        $arProps['RELATED_COLLECTIONS'][$arElement['ID']][] = $oboi->addUpdateSection($arFieldsSectRelated, $el, '');
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
                                                                $sections1 = $oboi->getSections('', array('LEFT_MARGIN' => 'ASC', 'SORT' => 'ASC'), 0);
                                                                foreach ($sections1 as $keySect=> $arSect){
                                                                    if($arRecvizitsCollection['Значение']['ID'] === $arSect["XML_ID"]){
                                                                        $arProps['RELATED_COLLECTIONS'][$arElement['ID']][] = $arSect['ID'];
                                                                    }
                                                                }
                                                            } else {
                                                                $arFieldsSectRelated = array(
                                                                    "MODIFIED_BY" => $USER->GetID(), // элемент изменен текущим пользователем
                                                                    "IBLOCK_SECTION_ID" => false,          // элемент лежит в корне раздела
                                                                    "IBLOCK_ID" => \Indi\Main\Iblock\ID_Product_Oboi,
                                                                    'NAME' => $arRecvizitsCollection['Значение']["Наименование"],
                                                                    "ACTIVE" => "N",            // активен
                                                                    "CODE" => \Indi\Main\Util::translit($arRecvizitsCollection['Значение']["Наименование"]),
                                                                    "XML_ID" => $arRecvizitsCollection['Значение']['ID'],
                                                                );

                                                                $el = new \CIBlockSection;
                                                                try {
                                                                    $arProps['RELATED_COLLECTIONS'][$arElement['ID']][] = $oboi->addUpdateSection($arFieldsSectRelated, $el, '');
                                                                } catch (ErrorException $e) {
                                                                    \Indi\Main\Util::log($entityName . " import update: " . $el->LAST_ERROR); // обновляем поля
                                                                }
                                                            }
                                                        }
                                                    }
                                                }*/

                        unset($sections, $sections1, $arSections);
                        break;
                    case 'Изображения':
                        foreach ($arCollection['СтрокаКоллекции'] as $keyRecvizits => $arRecvizits){
                            if($keyRecvizits === 'Реквизит'){
//                                Util::debug($arRecvizits['Значение']['@value']);
                                $arProps['IMAGES_SLIDER'][$arElement['ID']][] =  \CFile::MakeFileArray($_SERVER['DOCUMENT_ROOT'].$arRecvizits['Значение']['@value']);
                            } else {
//                                Util::debug($arRecvizits['Реквизит']['Значение']['@value']);
                                $arProps['IMAGES_SLIDER'][$arElement['ID']][] =  \CFile::MakeFileArray($_SERVER['DOCUMENT_ROOT'].$arRecvizits['Реквизит']['Значение']['@value']);
                            }
                        }
                        break;
                }
            }

            $arFieldsSect = array(
                "MODIFIED_BY" => $USER->GetID(), // элемент изменен текущим пользователем
                "IBLOCK_SECTION_ID" => false,          // элемент лежит в корне раздела
                "IBLOCK_ID" => \Indi\Main\Iblock\ID_Product_Oboi,
                'NAME' => $arElement['Наименование'],
                "ACTIVE" => $arProps['ACTIVE'],            // активен
                "DESCRIPTION" => $arProps['DESC'],
                "CODE" => \Indi\Main\Util::translit($arElement['Наименование']),
                "XML_ID" => $arElement['ID'],
                "UF_EN_NAME" => $arProps['NAME_EN'],
                "UF_COLOR" => $arProps['COLORS'][$arElement['ID']],
                "UF_NEW" => $arProps['NEW'],
                "UF_STOCK" => $arProps['STOCK'],
                "UF_HIT" => $arProps['HIT'],
                "UF_ECONOM" => $arProps['ECONOM'],
                "UF_PRICE" => $arProps['PRICE'],
                "UF_BASIS" => $arProps['BASIS'],
                "UF_PICTURE" => $arProps['PICTURE'],
                "UF_MODE_PRODUCTION" => $arProps['MODE_PRODUCTION'],
                "UF_WIDTH" => $arProps['WIDTH'],
                "UF_LENGTH" => $arProps['LENGTH'],
                "UF_RAPPORT" => $arProps['RAPPORT'],
                "UF_COUNTRY" => $arProps['COUNTRY'],
                "UF_BRAND" =>  $arProps['BRAND'],
                "UF_SIMILAR" => $arProps['RELATED_COLLECTIONS'][$arElement['ID']],
                "UF_IMAGES_SMALL"  => $arProps['IMAGES'][$arElement['ID']],
                "UF_IMAGES"  => $arProps['IMAGES_SLIDER'][$arElement['ID']],
                "PICTURE" => $arProps['IMAGES'][$arElement['ID']][0],
            );

            $el = new \CIBlockSection;
            try {
//                $SECT_ID = $oboi->addUpdateSection($arFieldsSect, $el, '');
//                Util::debug($arFieldsSect);
            } catch (ErrorException $e) {
                \Indi\Main\Util::log($entityName . " import update: " . $el->LAST_ERROR); // обновляем поля
            }

//            Util::debug($arProps);
        }
    }

    /**
     * Импорт элементов обоев
     * importElements
     * @param $arElements
     */
    protected function importElements($arElements)
    {
        global $USER;
        $el = new \CIblockElement;
        $oboi = \Indi\Main\Iblock\Product\Oboi::getInstance();
        foreach ($arElements as $arElement) {

//			Util::dump($arElement);
            $arProps = array();
            foreach ($arElement["Реквизиты"]["Реквизит"] as $prop)
            {
                switch($prop["Наименование"]) {
                    case 'Активность':
                        $arProps['ACTIVE'] = $prop["Значение"]["@value"] == 'true' ? 'Y' : 'N';
						break;
					case 'Артикул':
						$arProps['ARTICUL'] = $prop["Значение"]["@value"];
						break;
                    case 'Описание':
                        $arProps['DESC'] = $prop["Значение"]["@value"];
                        break;
                    case 'Ширина':
                        $xmlId =  Util::translit($prop["Значение"]["@value"]);
                        $xmlId = mb_strtolower($xmlId);
                        $arFilterWidth = array(
                            "UF_XML_ID" => $xmlId
                        );

                        $width = \Indi\Main\Hlblock\Widthpieces::getInstance();

                        if(count($width->getData($arFilterWidth)) == 0){
                            $residWidth = $width->addData(
                                array(
                                    "UF_XML_ID" => $xmlId,
                                    "UF_NAME" => $prop["Значение"]['@value'],
                                )
                            );
                        }

                        $widths = $width->getElements();

                        foreach ($widths as $keyWidth => $arWidth) {
                            if ($arWidth['UF_XML_ID'] === $xmlId) {
                                $arProps['WIDTH'] = $arWidth['UF_XML_ID'];
                            }
                        }
                        unset($arFilterLength, $width, $widths);
                        break;
                    case 'Длина':
                        $xmlId =  Util::translit($prop["Значение"]["@value"]);
                        $xmlId = mb_strtolower($xmlId);
                        $arFilterLength = array(
                            "UF_XML_ID" => $xmlId
                        );

                        $length = \Indi\Main\Hlblock\Lengthpieces::getInstance();

                        if(count($length->getData($arFilterLength)) == 0){
                            $residLength = $length->addData(
                                array(
                                    "UF_XML_ID" => $xmlId,
                                    "UF_NAME" => $prop["Значение"]['@value'],
                                )
                            );
                        }

                        $lengths = $length->getElements();

                        foreach ($lengths as $keyLength => $arLength) {
                            if ($arLength['UF_XML_ID'] === $xmlId) {
                                $arProps['LENGTH'] = $arLength['UF_XML_ID'];
                            }
                        }
                        unset($arFilterLength, $length, $lengths);
                        break;
                    case 'Раппорт':
                        $xmlId =  Util::translit($prop["Значение"]["@value"]);
                        $xmlId = mb_strtolower($xmlId);
                        $arFilterRapport = array(
                            "UF_XML_ID" => $xmlId
                        );

                        $rapport = \Indi\Main\Hlblock\Rapport::getInstance();

                        if(count($rapport->getData($arFilterRapport)) == 0){
                            $residRapport = $rapport->addData(
                                array(
                                    "UF_XML_ID" => $xmlId,
                                    "UF_NAME" => $prop["Значение"]['@value'],
                                )
                            );
                        }

                        $rapports = $rapport->getElements();

                        foreach ($rapports as $keyRapport => $arRapport) {
                            if ($arRapport['UF_XML_ID'] === $xmlId) {
                                $arProps['RAPPORT'] = $arRapport['UF_XML_ID'];
                            }
                        }
                        unset($arFilterRapport, $rapport, $rapports);
                        break;
                    case 'Новинка':
                        $arProps['NEW'] = $prop["Значение"]["@value"] == 'true' ? 24 : false;
                        break;
                    case 'ХитПродаж':
                        $arProps['HIT'] = $prop["Значение"]["@value"] == 'true' ? 25 : false;
                        break;
                    case 'Распродажа':
                        $arProps['STOCK'] = $prop["Значение"]["@value"] == 'true' ? 26 : false;
                        break;
                    case 'Строителям':
                        $arProps['BUILDERS'] = $prop["Значение"]["@value"] ? 27 : false;
                        break;
                    case 'Изображение':
                        if(file_exists($_SERVER['DOCUMENT_ROOT'].'/upload/import/images/oboi/'.$prop['Значение']['@value'])){
                            $arProps['IMG'] = $_SERVER['DOCUMENT_ROOT'].'/upload/import/images/oboi/'.$prop['Значение']['@value'];
                        }
                        break;
                    case 'Цвет':

                        $xmlId =  $prop["Значение"]["ID"];
                        $arFilterColor = array(
                            "UF_XML_ID" => $xmlId
                        );

                        $color = \Indi\Main\Hlblock\Tsvetoboev::getInstance();

                        if(count($color->getData($arFilterColor)) == 0){
                            $residColor = $color->addData(
                                array(
                                    "UF_XML_ID" => $xmlId,
                                    "UF_NAME" => $prop['Значение']['Наименование'],
                                    "UF_FILE" =>  \CFile::MakeFileArray($_SERVER['DOCUMENT_ROOT'].$arElement["Реквизиты"]["Реквизит"][7]['Значение']['@value'])
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
                    case 'СпособПроизводства':
                        $xmlId =  Util::translit($prop["Значение"]["@value"]);
                        $xmlId = mb_strtolower($xmlId);
                        $arFilterProduction = array(
                            "UF_XML_ID" => $xmlId
                        );

                        $production = \Indi\Main\Hlblock\Modeproduction::getInstance();

                        if(count($production->getData($arFilterProduction)) == 0){
                            $residProduction = $production->addData(
                                array(
                                    "UF_XML_ID" => $xmlId,
                                    "UF_NAME" => $prop["Значение"]['@value'],
                                )
                            );
                        }

                        $productions = $production->getElements();

                        foreach ($productions as $keyProduction => $arProduction) {
                            if ($arProduction['UF_XML_ID'] === $xmlId) {
                                $arProps['MODE_PRODUCTION'] = $arProduction['UF_XML_ID'];
                            }
                        }
                        unset($arFilterProduction, $production, $productions);
                        break;
                    case 'Дизайн':
                        $arSections = array();
                        $sections = $oboi->getSections('', array('LEFT_MARGIN' => 'ASC', 'SORT' => 'ASC'), 0);
                        foreach ($sections as $keySect=> $arSect){
                            $arSections[] = $arSect["XML_ID"];
                        }

                        if(in_array($prop["Значение"]['@value'], $arSections)){
                            $sections1 = $oboi->getSections('', array('LEFT_MARGIN' => 'ASC', 'SORT' => 'ASC'), 0);
                            foreach ($sections1 as $keySect=> $arSect){
                                if($prop['Значение']['@value'] === $arSect["XML_ID"]){
                                    $arProps['SECT'] = $arSect['ID'];
                                }
                            }
                        } else {
                            $arFieldsSect = array(
                                "MODIFIED_BY" => $USER->GetID(), // элемент изменен текущим пользователем
                                "IBLOCK_SECTION_ID" => false,          // элемент лежит в корне раздела
                                "IBLOCK_ID" => \Indi\Main\Iblock\ID_Product_Oboi,
                                'NAME' => $prop["Значение"]['@value'],
                                "ACTIVE" => "N",            // активен
                                "CODE" => \Indi\Main\Util::translit($prop['Значение']['@value']),
                                "XML_ID" => $prop["Значение"]['@value'],
                            );

                            $el = new \CIBlockSection;
                            try {
                                $SECT_ID = $oboi->addUpdateSection($arFieldsSect, $el, '');
                                $arProps['SECT'] = $SECT_ID;
                            } catch (ErrorException $e) {
                                \Indi\Main\Util::log($entityName . " import update: " . $el->LAST_ERROR); // обновляем поля
                            }
                        }
                        unset($sections, $sections1, $arSections);
                        break;
                    case 'Рисунок':
                        $xmlId =  $prop["Значение"]["ID"];
                        $xmlId = mb_strtolower($xmlId);
                        $arFilterPicture = array(
                            "UF_XML_ID" => $xmlId
                        );

                        $picture = \Indi\Main\Hlblock\Risunok::getInstance();

                        if(count($picture->getData($arFilterPicture)) == 0){
                            $residPicture = $picture->addData(
                                array(
                                    "UF_XML_ID" => $xmlId,
                                    "UF_NAME" => $prop["Значение"]['Наименование'],
                                )
                            );
                        }

                        $pictures = $picture->getElements();

                        foreach ($pictures as $keyPicture => $arPicture) {
                            if ($arPicture['UF_XML_ID'] === $xmlId) {
                                $arProps['PICTURE'] = $arPicture['UF_XML_ID'];
                            }
                        }
                        unset($arFilterPicture, $picture, $pictures);
                        break;
                    case 'Эконом':
                        $arProps['ECONOM'] = $prop["Значение"]["@value"] ? 28 : false;
                        break;
                    case 'Бренд':
                        $brand = \Indi\Main\Iblock\Prototype::getInstance(\Indi\Main\Iblock\ID_Product_Brands);
                        $brands = $brand->getList();
                        foreach ($brands as $keyBrand => $arBrand){
                            if($prop["Значение"]['ID'] === $arBrand['XML_ID']){
                                $arProps['BRAND'] = $arBrand['ID'];
                            } else {
                                $arFieldsBrand = array(
                                    "MODIFIED_BY"    => $USER->GetID(), // элемент изменен текущим пользователем
                                    "IBLOCK_SECTION_ID" => '',          // элемент лежит в корне раздела
                                    "IBLOCK_ID"      => \Indi\Main\Iblock\ID_Product_Brands,
                                    "PROPERTY_VALUES"=> array(
                                        46 => 19
                                    ),
                                    'NAME'           => $prop["Значение"]['Наименование'],
                                    "ACTIVE"         => 'Y',            // активен
                                    "PREVIEW_TEXT"   => "текст для списка элементов",
                                    "DETAIL_TEXT"    => "текст для детального просмотра",
                                    "XML_ID"         => $prop["Значение"]['ID'],
                                );

                                $elBrand = new \CIBlockElement;
                                try {
                                    $arProps['BRAND'] = $this->addUpdateElement($arFieldsBrand, $elBrand, '', array());
//                                    echo "New ID_BRAND: " . $idBrand;
                                } catch (ErrorException $e) {
                                    echo "Error: " . $e;
                                }
                            }
                        }
                        unset($brands);
                        break;
                    case 'Страна':
                        $xmlId =  $prop["Значение"]["ID"];
                        $xmlId = mb_strtolower($xmlId);
                        $arFilterCountry = array(
                            "UF_XML_ID" => $xmlId
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
                            if ($arCountry['UF_XML_ID'] === $xmlId) {
                                $arProps['COUNTRY'] = $arCountry['UF_XML_ID'];
                            }
                        }
                        unset($arFilterCountry, $country, $countries);
                        break;
                    case 'Основа':
                        $xmlId =  Util::translit($prop["Значение"]["@value"]);
                        $xmlId = mb_strtolower($xmlId);
                        $arFilterBasis = array(
                            "UF_XML_ID" => $xmlId
                        );

                        $basis = \Indi\Main\Hlblock\Basis::getInstance();

                        if(count($basis->getData($arFilterBasis)) == 0){
                            $residrBasis = $basis->addData(
                                array(
                                    "UF_XML_ID" => $xmlId,
                                    "UF_NAME" => $prop["Значение"]['@value'],
                                )
                            );
                        }

                        $basises = $basis->getElements();

                        foreach ($basises as $keyBasis => $arBasis) {
                            if ($arBasis['UF_XML_ID'] === $xmlId) {
                                $arProps['BASIS'] = $arBasis['UF_XML_ID'];
                            }
                        }

                        unset($arFilterBasis, $basis, $basises);
                        break;
                    case 'ЦеноваяКатегория':
                        $xmlId =  $prop["Значение"]["ID"];
                        $xmlId = mb_strtolower($xmlId);
                        $arFilterCategory = array(
                            "UF_XML_ID" => $xmlId
                        );

                        $category = \Indi\Main\Hlblock\Tsenovayakategoriya::getInstance();

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
                            if ($arCategory['UF_XML_ID'] === $xmlId) {
                                $arProps['PRICE'] = $arCategory['UF_XML_ID'];
                            }
                        }
                        unset($arFilterCategory, $category, $categories);
                        break;
                   case 'РулоновВКоробке':
                        $arProps['RULONIBOX'] = $prop["Значение"]["@value"];
                        break;
                    case 'КоробокВПаллете':
                        $arProps['BOXIPALLET'] = $prop["Значение"]["@value"];
                        break;
                    case 'ВесКоробки':
                        $arProps['BOXWEIGHT'] = $prop["Значение"]["@value"];
                        break;
                }
            }

            foreach ($arElement["КоллекцииЗначений"] as $arCollections) {
                foreach ($arCollections as $keyCollection => $arCollection){
                    switch ($arCollection['@attributes']['Имя']) {
                        case 'РазмерыКоробки':
                            foreach ($arCollection['СтрокаКоллекции']['Реквизит'] as $keyRecvizit => $arRecvizit){
                                if($arRecvizit['Наименование'] === 'ДлинаКоробки'){
                                    $arProps['BOXLENGTH'] = $arRecvizit['Значение']['@value'];
                                } elseif ($arRecvizit['Наименование'] === 'ШиринаКоробки'){
                                    $arProps['BOXWIDTH'] = $arRecvizit["Значение"]["@value"];
                                } elseif ($arRecvizit['Наименование'] === 'ВысотаКоробки'){
                                    $arProps['BOXHEIGHT'] = $arRecvizit["Значение"]["@value"];
                                }
                            }
                            break;
                        case 'СвойствоДизайн':
                            /*$xmlId =  $arCollection['СтрокаКоллекции']['Реквизит']["Значение"]["ID"];
                            $xmlId = mb_strtolower($xmlId);
                            $arFilterDisegn = array(
                                "UF_XML_ID" => $xmlId
                            );

                            $disegn = \Indi\Main\Hlblock\Designs::getInstance();

                            if(count($disegn->getData($arFilterDisegn)) == 0){
                                $residDisegn = $disegn->addData(
                                    array(
                                        "UF_XML_ID" => $xmlId,
                                        "UF_NAME" => $arCollection['СтрокаКоллекции']['Реквизит']["Значение"]['Наименование'],
                                    )
                                );
                            }

                            $disegns = $disegn->getElements();

                            foreach ($disegns as $keyDisegn => $arDisegn) {
                                if ($arDisegn['UF_XML_ID'] === $xmlId) {
                                    $arProps['DISEGN'] = $arDisegn['UF_XML_ID'];
                                }
                            }*/
                            $arParamsCompanion['filter'] = array(
                                'IBLOCK_SECTION_ID' => $arProps['SECT']
                            );
                            $companions = $oboi->getList($arParamsCompanion, 0);
                            $arCompanions = array();
                            foreach ($companions as $comp){
                                $arCompanions[] = $comp['XML_ID'];
                            }

                            if($arCollection['СтрокаКоллекции']['Реквизит']){
                                if(in_array($arCollection['СтрокаКоллекции']['Реквизит']['Значение']['ID'],$arCompanions)){
                                    foreach ($companions as $keyCompanions => $valCompanions){
                                        if($arCollection['СтрокаКоллекции']['Реквизит']['Значение']['ID'] === $valCompanions['XML_ID']){
                                            $arProps['COMPANION'][] = $valCompanions['ID'];
                                        }
                                    }
                                } else {
                                    $arFieldsElem = array(
                                        "MODIFIED_BY"    => $USER->GetID(), // элемент изменен текущим пользователем
//                                        "IBLOCK_SECTION_ID" => $arProps['SECT'],          // элемент лежит в корне раздела
                                        "IBLOCK_ID"      => \Indi\Main\Iblock\ID_Product_Oboi,
                                        'NAME'           => $arCollection['СтрокаКоллекции']['Реквизит']['Значение']['Наименование'],
//                                        "ACTIVE"         => 'N',            // активен
                                        "PREVIEW_TEXT"   => "текст для списка элементов",
                                        "DETAIL_TEXT"    => "текст для детального просмотра",
                                        "CODE"           => \Indi\Main\Util::translit($arCollection['СтрокаКоллекции']['Реквизит']['Значение']['Наименование']),
                                        "XML_ID"         => $arCollection['СтрокаКоллекции']['Реквизит']['Значение']['ID'],
                                    );

                                    $el = new \CIBlockElement;
                                    try {
                                        $PRODUCT_ID = $oboi->addUpdateElement($arFieldsElem, $el, '', array());
                                        $arProps['COMPANION'][] = $PRODUCT_ID;
                                    } catch (ErrorException $e) {
                                        \Indi\Main\Util::log($entityName . " import update: " . $el->LAST_ERROR); // обновляем поля
                                    }
                                }
                            } else {
                                foreach ($arCollection['СтрокаКоллекции'] as $keyCompanion => $arCompanion){
                                    if(in_array($arCompanion['Реквизит']['Значение']['ID'],$arCompanions)){
                                        foreach ($companions as $keyCompanions => $valCompanions){
                                            if($arCompanion['Реквизит']['Значение']['ID'] === $valCompanions['XML_ID']){
                                                $arProps['COMPANION'][] = $valCompanions['ID'];
                                            }
                                        }
                                    } else {
                                        $arFieldsElem = array(
                                            "MODIFIED_BY"    => $USER->GetID(), // элемент изменен текущим пользователем
//                                            "IBLOCK_SECTION_ID" => $arProps['SECT'],          // элемент лежит в корне раздела
                                            "IBLOCK_ID"      => \Indi\Main\Iblock\ID_Product_Oboi,
                                            'NAME'           => $arCompanion['Реквизит']['Значение']['Наименование'],
//                                            "ACTIVE"         => 'N',            // активен
                                            "PREVIEW_TEXT"   => "текст для списка элементов",
                                            "DETAIL_TEXT"    => "текст для детального просмотра",
                                            "CODE"           => \Indi\Main\Util::translit($arCompanion['Реквизит']['Значение']['Наименование']),
                                            "XML_ID"         => $arCompanion['Реквизит']['Значение']['ID'],
                                        );

                                        $el = new \CIBlockElement;
                                        try {
                                            $PRODUCT_ID = $oboi->addUpdateElement($arFieldsElem, $el, '', array());
                                            $arProps['COMPANION'][] = $PRODUCT_ID;
                                        } catch (ErrorException $e) {
                                            \Indi\Main\Util::log($entityName . " import update: " . $el->LAST_ERROR); // обновляем поля
                                        }
                                    }
                                }
                            }
                            unset($arParamsCompanion, $companions);
                            break;
                    }
                }
            }

            $arFieldsElem = array(
                "MODIFIED_BY"    => $USER->GetID(), // элемент изменен текущим пользователем
                "IBLOCK_SECTION_ID" => $arProps['SECT'],          // элемент лежит в корне раздела
                "IBLOCK_ID"      => \Indi\Main\Iblock\ID_Product_Oboi,
                "PROPERTY_VALUES"=> array(
                    66 => $arProps['ARTICUL'],
                    67 => $arProps['WIDTH'],
                    68 => $arProps['LENGTH'],
                    69 => $arProps['RAPPORT'],
                    73 => $arProps['BOXLENGTH'],
                    74 => $arProps['BOXWIDTH'],
                    75 => $arProps['BOXHEIGHT'],
                    76 => $arProps['NEW'],
                    77 => $arProps['HIT'],
                    78 => $arProps['STOCK'],
                    79 => $arProps['BUILDERS'],
                    80 => $arProps['COLOR'],
                    81 => $arProps['MODE_PRODUCTION'],
                    82 => $arProps['COMPANION'],
                    85 => $arProps['PICTURE'],
                    86 => $arProps['ECONOM'],
                    87 => $arProps['BRAND'],
                    88 => $arProps['COUNTRY'],
                    90 => $arProps['BASIS'],
                    92 => $arProps['PRICE'],
                    93 => $arProps['RULONIBOX'],
                    94 => $arProps['BOXIPALLET'],
                    95 => $arProps['BOXWEIGHT'],
                    98 => $arProps['DISEGN'],
                    96 => $arProps['BOXLENGTH'] . ' x ' . $arProps['BOXWIDTH'] . ' x ' . $arProps['BOXHEIGHT'],
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
                $PRODUCT_ID = $oboi->addUpdateElement($arFieldsElem, $el, '', array());
                \Indi\Main\Util::debug($arFieldsElem);
            } catch (ErrorException $e) {
                \Indi\Main\Util::log($entityName . " import update: " . $el->LAST_ERROR); // обновляем поля
            }
        }
    }

    /**
     * Получает значение справочника, создает его, если не указано
     * getHlValue
     * @param $hlCode
     * @param $prop
     *
     * @return int
     */
    protected function getHlValue($hlCode, $prop)
    {
        $pID = $prop["Значение"]["ID"];
        $hl = \Indi\Main\Hlblock\Prototype::getInstance($hlCode);
        $i = $hl->getXmlIdByPropCode('UF_XML_ID', $pID);
        if(!$i) {
            $arHFields = array(
                'UF_XML_ID' => $pID,
                'UF_NAME' => $prop["Значение"]["Значение"],
            );
            $arProps = $hl->addUpdateHblockElement($arHFields, $hl, $hlCode);
        } else {
            $arProps = $pID;
        }

        return $arProps;
    }

    /**
     * Возвращает секции инфоблока с параметром UF_NEW UF_STOCK UF_HIT
     *
     * @return integer $countSection количество элементов
     */

    public function getSectionsListUfHitStockNew($uf = '') {
        if(!$uf)
            return '';
        $arSelect = array("UF_*");
        $arFilter= array("IBLOCK_ID" => $this->id, $uf => 1);
        $arNavStartParams = array("nTopCount" => 1000);
        $arResult['PLITKA']['SECTIONS']['NEW'] = parent::getSectionsList('', $arFilter, $arSelect, $arNavStartParams);
        return count($arResult['PLITKA']['SECTIONS']['NEW']);
    }
}