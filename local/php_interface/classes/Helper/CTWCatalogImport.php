<?php
/**
 * Класс для импорта каталога с использованием XML-файлов
 */
namespace Helper;

use Bitrix\Main\Config\Option;

if (!\Bitrix\Main\Loader::IncludeModule('iblock')) {
    return false;
}

class CTWCatalogImport
{
    /**
     * Парамерты импорта
     */
    private $__arSettings = array();

    /**
     * Список файлов для импорта
     */
    private $__arImportFilesList = array();

    /**
     *
     */
    private $__obXmlData = null;

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
        $json_daily_file = $_SERVER["DOCUMENT_ROOT"] . '/upload/daily.json';
        if (!is_file($json_daily_file) || filemtime($json_daily_file) < time() - 3600) {
            if ($json_daily = file_get_contents('https://www.cbr-xml-daily.ru/daily_json.js')) {
                file_put_contents($json_daily_file, $json_daily);
            }
        }

        $rates = json_decode(file_get_contents($json_daily_file));

        $ratesUSD = $rates->Valute->USD->Value;


        return array(
            // Директория, содержащая XML-файлы импорта
            'import_remote_base_address' => $_SERVER["DOCUMENT_ROOT"] . '/upload/in/',
            // Директория, содержащая XML-файлы импорта
            //'import_dir_path' => '/' . COption::GetOptionString('main', 'upload_dir', 'upload') . '/catalog_exchange/import/catalog',
            // Имена файлов, участвующих в импорте
            'import_file_names' => array(
                // Разделы
                'sections' => 'structure_goods_.xml',
                // Элементы
                'elements' => 'goods_.xml',
                // Остатки
                'amount' => 'remains_of_goods.xml',
            ),
            // Символьные коды характеристик товара
            'element_properties_map' => array(
                // Свойство "Артикул"
                'article' => 'ARTICLE',
                // Элементы
                'elements' => 'goods_.xml',
                // Остатки
                'amount' => 'remains_of_goods.xml',
            ),
            // Курс валюты
            'valute' => array(
                // Код валюты цены по умолчанию (USD)
                'num_сode_default' => 840,
                // Добавочный процент к курсу
                'extra_value_percent' => 1,
                // Курсы валют
                'curses_map' => array(
                    '840' => $ratesUSD,
                ),
            ),
            // Единицы измерения
            'measures_map' => array(
                'м' => 1,
                'л.' => 2,
                'г' => 3,
                'кг' => 4,
                'шт.' => 5,
            ),
        );
    }

    /**
     * Возвращает символьный код указанной характеритики товара
     */
    public function getElementPropertyCode($sProperty)
    {
        return $this->__arSettings['element_properties_map'][$sProperty];
    }

    /**
     * Скачивает файлы с удаленного сайта в локальную директорию обмена
     */
    public function downloadExchangeFiles()
    {
        foreach ($this->__arSettings['import_file_names'] as $sFileName) {
            if (($sContent = file_get_contents($this->__arSettings['import_remote_base_address'] . $sFileName)) !== false) {
                if (file_put_contents($_SERVER['DOCUMENT_ROOT'] . $this->__arSettings['import_dir_path'] . '/' . $sFileName, $sContent) === false) {
                    return false;
                }
            } else {
                return false;
            }
        }

        return true;
    }

    public function saveImportFilesList()
    {
        $sFilePattern = $_SERVER['DOCUMENT_ROOT'] . $this->__arSettings['import_dir_path'] . '/{' . implode(',', $this->__arSettings['import_file_names']) . '}';
        $this->__arImportFilesList = $sFilePattern;
        //$this->__arImportFilesList = glob($sFilePattern, GLOB_BRACE);
        //echo "<pre>";print_r($sFilePattern);echo "</pre>";
    }

    public function getImportFilePath($sType)
    {
        return $this->__arImportFilesList[array_search($sType, array_keys($this->__arSettings['import_file_names']))];
    }

    /**
     * Импорт разделов каталога
     * $sFilePath - путь к XML-файлу structure_goods_.xml
     */
    public function importSections($sFilePath)
    {
        // Считываем файл в память
        $this->__obXmlData = simplexml_load_file($sFilePath);

        if ($this->__obXmlData) {
            // Обрабатываем считанные значения
            if ($this->__obXmlData->{'Элемент'}->count()) {
                foreach ($this->__obXmlData->{'Элемент'} as $obSection) {
                    $arSection = array(
                        'xml_id' => (string)$obSection['КодГруппы'],
                        'name' => (string)$obSection['НаименованиеГруппы'],
                        'parent_xml_id' => (string)$obSection['КодРодителя'],
                    );

                    $this->importSection($arSection);
                }
            }

        }
    }

    public function importSection(array $arSection)
    {
        $arExistingSection = $this->getIBlockSectionByXmlId($arSection['xml_id']);

        $arSectionFields = array(
            'NAME' => $arSection['name'],
        );

        // Проверяем существование раздела-родителя
        $arExistingParentSection = $this->getIBlockSectionByXmlId($arSection['parent_xml_id']);
        if (!empty($arExistingParentSection)) {
            $arSectionFields['IBLOCK_SECTION_ID'] = $arExistingParentSection['ID'];
        }
        unset($arExistingParentSection);

        $obIBlockSection = new CIBlockSection();
        $bExchangeResult = false;

        if (empty($arExistingSection)) {
            // Если новый раздел, то
            // дополняем массив обязательными полями
            $arSectionFields['CODE'] = CUtil::translit($arSection['name'], LANGUAGE_ID);
            $arSectionFields['IBLOCK_ID'] = $this->__arSettings['import_iblock_id'];
            $arSectionFields['XML_ID'] = $arSection['xml_id'];

            $bExchangeResult = $obIBlockSection->Add($arSectionFields);
        } else {
            // Раздел уже сущесвует,
            // обновляем поля
            $bExchangeResult = $obIBlockSection->Update($arExistingSection['ID'], $arSectionFields);
        }

        unset($obIBlockSection);

        return $bExchangeResult;
    }

    private function getIBlockSectionByXmlId($sXmlId, array $arSelect = array('ID'))
    {
        if (!empty($sXmlId)) {
            if (!empty($arSelect) && !in_array('ID', $arSelect)) {
                $arSelect[] = 'ID';
            }

            $dbSection = CIBlockSection::GetList(
                array('ID' => 'ASC'),
                array(
                    'IBLOCK_ID' => $this->__arSettings['import_iblock_id'],
                    'XML_ID' => $sXmlId,
                ),
                false,
                $arSelect
            );

            return $dbSection->Fetch();
        }

        return false;
    }

    public function importElements($sFilePath)
    {
        // Считываем файл в память
        $this->__obXmlData = simplexml_load_file($sFilePath);

        if ($this->__obXmlData) {
            // Обрабатываем считанные значения
            if ($this->__obXmlData->{'Элемент'}->count()) {
                foreach ($this->__obXmlData->{'Элемент'} as $obElement) {
                    $arElement = array(
                        'xml_id' => (string)$obElement['КодТовара'],
                        'article' => (string)$obElement['АртикулТовара'],
                        'name' => (string)$obElement['НаименованиеТовара'],
                        'parent_xml_id' => (string)$obElement['КодРодителя'],
                        'measure' => (string)$obElement['ЕдиницаИзмерения'],
                        'price' => (float)$obElement['Цена'],
                        'valute_num_code' => ((int)$obElement['ВалютаЦены'] > 0) ? (int)$obElement['ВалютаЦены'] : $this->__arSettings['valute_numCode_default'],
                    );

                    $this->importElement($arElement);
                }
            }
        }
    }

    public function importElement(array $arElement)
    {
        $arElementFields = array(
            // Здесь обновляется имя
            //'NAME' => $arElement['name'],
        );

        // Проверяем существование раздела-родителя
        //
        // К разделам из БД не привязываемся
        //
        /*$arExistingParentSection = $this->getIBlockSectionByXmlId($arElement['parent_xml_id']);
        if (!empty($arExistingParentSection)) {
            $arElementFields['IBLOCK_SECTION_ID'] = $arExistingParentSection['ID'];
        }
        unset($arExistingParentSection);*/

        // Проверяем существование элемента в каталоге
        $arExistingElement = $this->getIBlockElementByXmlId($arElement['xml_id']);

        $obIBlockElement = new CIBlockElement();
        $bExchangeResult = false;

        if (empty($arExistingElement)) {
            // Если новый элемент, то дополняем массив обязательными полями
            //
            // Новые товары из 1С не добавляем.
            //
            /*$arElementFields['CODE'] = CUtil::translit($arElement['name'], LANGUAGE_ID);
            $arElementFields['IBLOCK_ID'] = $this->__arSettings['import_iblock_id'];
            $arElementFields['XML_ID'] = $arElement['xml_id'];

            $bExchangeResult = $arExistingElement['ID'] = $obIBlockElement->Add($arElementFields);*/
        } else {
            // Элемент уже существует, обновляем поля
            $bExchangeResult = $obIBlockElement->Update($arExistingElement['ID'], $arElementFields);
        }

        //
        // Отключено
        //
        /*unset($obIBlockSection);*/

        if ($bExchangeResult && ($arExistingElement['ID'] > 0)) {
            // Обновляем значения свойств
            // Артикул
            //
            // Зачем? Есть же внешний код
            //
            /*CIBlockElement::SetPropertyValuesEx(
                $arExistingElement['ID'],
                $this->__arSettings['import_iblock_id'],
                array($this->getElementPropertyCode('article') => $arElement['article'])
            );*/
        }

        // Обновляем параметры товара
        if (CModule::IncludeModule('catalog')) {
            if ($bExchangeResult) {
                $arCatalogProductFields = array(
                    'ID' => $arExistingElement['ID'],
                    'MEASURE' => $this->__arSettings['measures_map'][$arElement['measure']],
                );

                $bExchangeResult = CCatalogProduct::Add($arCatalogProductFields);
            }

            // Обновляем цену товара
            if ($bExchangeResult) {
                // Получаем курс валюты
                $fValuteCurs = .0;
                if (($fValuteCurs = $this->getValuteCurs($arElement['valute_num_code'])) > .0) {
                    $bExchangeResult = CPrice::SetBasePrice(
                        $arExistingElement['ID'],
                        $arElement['price'] * $fValuteCurs/* * 1.01*/, // todo: Процент к цене
                        'RUB'
                    );
                    // Устанавливаем "НДС включен в цену"
                    CCatalogProduct::Update($arExistingElement['ID'], array("VAT_INCLUDED" => 'Y'));
                } else {
                    //$bExchangeResult = false;
                    // Если не удалось получить цену - просто устанавливаем нулевую
                    $bExchangeResult = CPrice::SetBasePrice(
                        $arExistingElement['ID'],
                        0,
                        'RUB'
                    );
                }
            }
        } else {
            $bExchangeResult = false;
        }

        return $bExchangeResult;
    }

    private function getIBlockElementByXmlId($sXmlId, array $arSelect = array('ID'))
    {
        if (!empty($sXmlId)) {
            if (!empty($arSelect) && !in_array('ID', $arSelect)) {
                $arSelect[] = 'ID';
            }

            $dbElement = CIBlockElement::GetList(
                array('ID' => 'ASC'),
                array(
                    'IBLOCK_ID' => $this->__arSettings['import_iblock_id'],
                    'XML_ID' => $sXmlId,
                ),
                false,
                array('nTopCount' => 1),
                $arSelect
            );

            return $dbElement->Fetch();
        }

        return false;
    }

    /**
     * Возвращает курс валюты
     */
    public function getValuteCurs($sValuteNumCode = 840)
    {
        if (empty($this->__arSettings['valute']['curses_map'][$sValuteNumCode])) {
            if (!$this->updateValuteCurses($sValuteNumCode)) {
                return false;
            }
        }
        //AddMessage2Log("ok", "");
//echo "<pre>";print_r($this->__arSettings['valute']['curses_map'][$sValuteNumCode]);echo "</pre>";
        return $this->__arSettings['valute']['curses_map'][$sValuteNumCode];
    }

    /**
     * Получает курс валюты с сайта ЦБ РФ
     */
    public function updateValuteCurses()
    {

        if (($sContent = file_get_contents('http://www.cbr.ru/scripts/XML_daily.asp?date_req=' . date('d/m/Y')))) {
            $obValutesCurses = new SimpleXMLElement($sContent);
            unset($sContent);

            if ($obValutesCurses) {
                // Обрабатываем считанные значения
                if ($obValutesCurses->Valute->count()) {
                    foreach ($obValutesCurses->Valute as $obValute) {
                        $this->__arSettings['element_properties_map']['valute']['curses_map'][(string)$obValute->NumCode] = (float)str_replace(',', '.', (string)$obValute->Value);
                    }
                    return true;
                }
            }
        }

        return false;
    }

    // Возвращает информацию о разделе по его внешнему коду

    public function importElementsQuantity($sFilePath)
    {
        // Считываем файл в память
        $this->__obXmlData = simplexml_load_file($sFilePath);

        if ($this->__obXmlData) {
            // Обрабатываем считанные значения
            if ($this->__obXmlData->{'Элемент'}->count()) {
                foreach ($this->__obXmlData->{'Элемент'} as $obElement) {
                    $arElement = array(
                        'xml_id' => trim((string)$obElement['КодТовара']),
                        'stock' => (int)$obElement['КоличествоНаСкладе'] - (int)$obElement['КоличествоВРезерве'],
                        'reserved' => (int)$obElement['КоличествоВРезерве'],
                    );

                    $this->importElementQuantity($arElement);
                }
            }

        }
    }

    // Возвращает информацию об элементе по его внешнему коду

    public function importElementQuantity(array $arElement)
    {
        // Проверяем существование элемента в каталоге
        $arExistingElement = $this->getIBlockElementByXmlId($arElement['xml_id']);

        if (!empty($arExistingElement) && CModule::IncludeModule('catalog')) {
            // Обновляем параметры товара
            $arCatalogProductFields = array(
                'ID' => $arExistingElement['ID'],
                'QUANTITY' => (int)$arElement['stock'],
                'QUANTITY_RESERVED' => (int)$arElement['reserved'],
            );

            $bExchangeResult = CCatalogProduct::Add($arCatalogProductFields);
        }

        return $bExchangeResult;
    }

    public function import()
    {
    }

    public function __destruct()
    {
    }
}