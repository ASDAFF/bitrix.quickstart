<?
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Iblock\SectionTable;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class SiteCatalogSections extends CBitrixComponent
{

    /**
     * подключает языковые файлы
     */

    public function onIncludeComponentLang()
    {
        $this->includeComponentLang(basename(__FILE__));
        Loc::loadMessages(__FILE__);
    }

    /**
     * Обработка входных параметров
     *
     * @param mixed[] $arParams
     * @return mixed[] $arParams
     */

    public function onPrepareComponentParams($arParams)
    {
        // время кэширования
        $arParams["CACHE_TIME"] = (int)$arParams["CACHE_TIME"];
        $arParams["FIELDS"] = array_filter($arParams["FIELDS"]);
        return $arParams;
    }


    /**
     * Получение фильтра
     * @return array
     */

    private function getFilter() {
        $arrFilter = array(
            "IBLOCK_ID" => $this->arParams["IBLOCK_ID"],
            "ACTIVE" => "Y"
        );
        $arrFilterRequest = $GLOBALS[$this->arParams["FILTER_NAME"]];
        if(!empty($arrFilterRequest)) {
            $arrFilter = array_merge($arrFilter, $arrFilterRequest);
        }
        return $arrFilter;
    }

    /**
     * получение результатов
     *
     * @return array $arResult
     */

    protected function getResult()
    {

        $arResult = array();
        $arSections = array();

        $rsSections = SectionTable::getList(
            array(
                "select" => $this->arParams["FIELDS"],
                "filter" => $this->getFilter()
            )
        );
        while ($arSection = $rsSections->Fetch()) {
            $arSections[] = $arSection;
        }
        $arResult["SECTIONS"] = $arSections;
        return $arResult;
    }

    /**
     * выполняет логику работы компонента
     *
     * @return void
     */

    public function executeComponent()
    {
        try {
            $arrFilter = $this->getFilter();
            if($this->StartResultCache($this->arParams["CACHE_TIME"], array($arrFilter))){
                $this->arResult = $this->getResult();
                $this->includeComponentTemplate();
            }
        } catch (Exception $e) {
            ShowError($e->getMessage());
        }
    }
}
?>