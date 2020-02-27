<?

use Bitrix\Iblock\IblockTable;
use Bitrix\Iblock\InheritedProperty\ElementValues;
use Bitrix\Iblock\InheritedProperty\SectionValues;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Application;


if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class ItemsList extends CBitrixComponent
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
        global $DB;
        // время кэширования
        if (!isset($arParams["CACHE_TIME"]))
            $arParams["CACHE_TIME"] = 36000000;
        $arParams["CACHE_TIME"] = (int)$arParams["CACHE_TIME"];
        $arParams["IBLOCK_TYPE"] = trim($arParams["IBLOCK_TYPE"]);
        $arParams["IBLOCK_ID"] = trim($arParams["IBLOCK_ID"]);

        $arParams["SORT_BY1"] = trim($arParams["SORT_BY1"]);
        if (strlen($arParams["SORT_BY1"]) <= 0)
            $arParams["SORT_BY1"] = "ACTIVE_FROM";
        if (!preg_match('/^(asc|desc|nulls)(,asc|,desc|,nulls){0,1}$/i', $arParams["SORT_ORDER1"]))
            $arParams["SORT_ORDER1"] = "DESC";

        if (strlen($arParams["SORT_BY2"]) <= 0)
            $arParams["SORT_BY2"] = "SORT";
        if (!preg_match('/^(asc|desc|nulls)(,asc|,desc|,nulls){0,1}$/i', $arParams["SORT_ORDER2"]))
            $arParams["SORT_ORDER2"] = "ASC";


        $arParams["CHECK_DATES"] = $arParams["CHECK_DATES"] != "N";

        if (!is_array($arParams["FIELD_CODE"]))
            $arParams["FIELD_CODE"] = array();
        foreach ($arParams["FIELD_CODE"] as $key => $val)
            if (!$val)
                unset($arParams["FIELD_CODE"][$key]);

        if (!is_array($arParams["PROPERTY_CODE"]))
            $arParams["PROPERTY_CODE"] = array();
        foreach ($arParams["PROPERTY_CODE"] as $key => $val)
            if ($val === "")
                unset($arParams["PROPERTY_CODE"][$key]);

        $arParams["DETAIL_URL"] = trim($arParams["DETAIL_URL"]);

        $arParams["NEWS_COUNT"] = intval($arParams["NEWS_COUNT"]);
        if ($arParams["NEWS_COUNT"] <= 0)
            $arParams["NEWS_COUNT"] = 20;

        $arParams["CACHE_FILTER"] = $arParams["CACHE_FILTER"] == "Y";
        if (!$arParams["CACHE_FILTER"])
            $arParams["CACHE_TIME"] = 0;

        $arParams["SET_TITLE"] = $arParams["SET_TITLE"] != "N";
        $arParams["SET_BROWSER_TITLE"] = (isset($arParams["SET_BROWSER_TITLE"]) && $arParams["SET_BROWSER_TITLE"] === 'N' ? 'N' : 'Y');
        $arParams["SET_META_KEYWORDS"] = (isset($arParams["SET_META_KEYWORDS"]) && $arParams["SET_META_KEYWORDS"] === 'N' ? 'N' : 'Y');
        $arParams["SET_META_DESCRIPTION"] = (isset($arParams["SET_META_DESCRIPTION"]) && $arParams["SET_META_DESCRIPTION"] === 'N' ? 'N' : 'Y');
        $arParams["ADD_SECTIONS_CHAIN"] = $arParams["ADD_SECTIONS_CHAIN"] != "N"; //Turn on by default
        $arParams["INCLUDE_IBLOCK_INTO_CHAIN"] = $arParams["INCLUDE_IBLOCK_INTO_CHAIN"] != "N";
        $arParams["ACTIVE_DATE_FORMAT"] = trim($arParams["ACTIVE_DATE_FORMAT"]);
        if (strlen($arParams["ACTIVE_DATE_FORMAT"]) <= 0)
            $arParams["ACTIVE_DATE_FORMAT"] = $DB->DateFormatToPHP(CSite::GetDateFormat("SHORT"));
        $arParams["PREVIEW_TRUNCATE_LEN"] = intval($arParams["PREVIEW_TRUNCATE_LEN"]);
        $arParams["HIDE_LINK_WHEN_NO_DETAIL"] = $arParams["HIDE_LINK_WHEN_NO_DETAIL"] == "Y";

        $arParams["DISPLAY_TOP_PAGER"] = $arParams["DISPLAY_TOP_PAGER"] == "Y";
        $arParams["DISPLAY_BOTTOM_PAGER"] = $arParams["DISPLAY_BOTTOM_PAGER"] != "N";
        $arParams["PAGER_TITLE"] = trim($arParams["PAGER_TITLE"]);
        $arParams["PAGER_SHOW_ALWAYS"] = $arParams["PAGER_SHOW_ALWAYS"] == "Y";
        $arParams["PAGER_TEMPLATE"] = trim($arParams["PAGER_TEMPLATE"]);
        $arParams["PAGER_DESC_NUMBERING"] = $arParams["PAGER_DESC_NUMBERING"] == "Y";
        $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"] = intval($arParams["PAGER_DESC_NUMBERING_CACHE_TIME"]);
        $arParams["PAGER_SHOW_ALL"] = $arParams["PAGER_SHOW_ALL"] == "Y";


        return $arParams;
    }


    /**
     * Подготовка фильтра для кэширования
     */
    private function prepareFilter()
    {
        if (strlen($this->arParams["FILTER_NAME"]) <= 0 || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $this->arParams["FILTER_NAME"])) {
            $this->arrFilter = array();
        } else {
            $this->arrFilter = $GLOBALS[$this->arParams["FILTER_NAME"]];
            if (!is_array($this->arrFilter))
                $this->arrFilter = array();
        }
        return $this->arrFilter;
    }

    /**
     * Подготовка навигации для кэширования
     */
    private function prepareNavigation()
    {
        if($this->arParams["DISPLAY_TOP_PAGER"] ||  $this->arParams["DISPLAY_BOTTOM_PAGER"])
        {
            $this->arNavParams = array(
                "nPageSize" =>  $this->arParams["NEWS_COUNT"],
                "bDescPageNumbering" =>  $this->arParams["PAGER_DESC_NUMBERING"],
                "bShowAll" =>  $this->arParams["PAGER_SHOW_ALL"],
            );
            $this->arNavigation = CDBResult::GetNavParams( $this->arNavParams);
            if( $this->arNavigation["PAGEN"]==0 &&  $this->arParams["PAGER_DESC_NUMBERING_CACHE_TIME"]>0)
                $this->arParams["CACHE_TIME"] =  $this->arParams["PAGER_DESC_NUMBERING_CACHE_TIME"];
        }
        else
        {
            $this->arNavParams = array(
                "nTopCount" =>  $this->arParams["NEWS_COUNT"],
                "bDescPageNumbering" =>  $this->arParams["PAGER_DESC_NUMBERING"],
            );
            $this->arNavigation = false;
        }
	    if (empty($this->arParams["PAGER_PARAMS_NAME"]) || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $this->arParams["PAGER_PARAMS_NAME"]))
	    {
		    $this->pagerParameters = array();
	    }
	    else
	    {
		    $this->pagerParameters = $GLOBALS[$this->arParams["PAGER_PARAMS_NAME"]];
		    if (!is_array($this->pagerParameters))
			    $this->pagerParameters = array();
	    }
        return $this->arNavParams;

    }

    /**
     * получение результатов
     *
     * @return void
     */

    private function getResult()
    {
       $this->getElements();
       return;
    }


    /**
     * Получение непосредственно элментов инфоблока
     */
    protected function getElements()
    {
        $arSelect = array_merge($this->arParams["FIELD_CODE"], array('ID', 'IBLOCK_ID', 'NAME', 'IBLOCK_SECTION_ID'));
        if (count($this->arParams['PROPERTY_CODE']) > 0) {
            foreach ($this->arParams['PROPERTY_CODE'] as $pCode) {
                $arSelect[] = "PROPERTY_" . $pCode;
            }
        }
        $arSort = array(
            $this->arParams["SORT_BY1"] => $this->arParams["SORT_ORDER1"],
            $this->arParams["SORT_BY2"] => $this->arParams["SORT_ORDER2"],
        );

        if (!array_key_exists("ID", $arSort))
            $arSort["ID"] = "DESC";

        $arFilter = array(
            "IBLOCK_ID" => $this->arParams["IBLOCK_ID"],
            "ACTIVE" => "Y",
        );

        if ($this->arParams["CHECK_DATES"])
            $this->arFilter["ACTIVE_FROM"] = "Y";

        $this->arResult["ITEMS"] = array();
        $rsElement = CIBlockElement::GetList($arSort, array_merge($arFilter, $this->arrFilter), false, $this->arNavParams, $arSelect);
        $rsElement->SetUrlTemplates($this->arParams["DETAIL_URL"], "", $this->arParams["IBLOCK_URL"]);
        $obParser = new CTextParser;
        while ($arItem = $rsElement->getNext()) {
            //Кнопки редактирования
            $arButtons = CIBlock::GetPanelButtons(
                $arItem["IBLOCK_ID"],
                $arItem["ID"],
                0,
                array("SECTION_BUTTONS" => false, "SESSID" => false)
            );
            $arItem["EDIT_LINK"] = $arButtons["edit"]["edit_element"]["ACTION_URL"];
            $arItem["DELETE_LINK"] = $arButtons["edit"]["delete_element"]["ACTION_URL"];

            if ($this->arParams["PREVIEW_TRUNCATE_LEN"] > 0)
                $arItem["PREVIEW_TEXT"] = $obParser->html_cut($arItem["PREVIEW_TEXT"], $this->arParams["PREVIEW_TRUNCATE_LEN"]);
            if (strlen($arItem["ACTIVE_FROM"]) > 0)
                $arItem["DISPLAY_ACTIVE_FROM"] = CIBlockFormatProperties::DateFormat($this->arParams["ACTIVE_DATE_FORMAT"], MakeTimeStamp($arItem["ACTIVE_FROM"], CSite::GetDateFormat()));
            else
                $arItem["DISPLAY_ACTIVE_FROM"] = "";


            if (isset($arItem["PREVIEW_PICTURE"])) {
                $arItem["PREVIEW_PICTURE"] = (0 < $arItem["PREVIEW_PICTURE"] ? CFile::GetFileArray($arItem["PREVIEW_PICTURE"]) : false);
                if ($arItem["PREVIEW_PICTURE"]) {
                    $arItem["PREVIEW_PICTURE"]["ALT"] = $arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"];
                    if ($arItem["PREVIEW_PICTURE"]["ALT"] == "")
                        $arItem["PREVIEW_PICTURE"]["ALT"] = $arItem["NAME"];
                    $arItem["PREVIEW_PICTURE"]["TITLE"] = $arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"];
                    if ($arItem["PREVIEW_PICTURE"]["TITLE"] == "")
                        $arItem["PREVIEW_PICTURE"]["TITLE"] = $arItem["NAME"];
                }
            }
            if (isset($arItem["DETAIL_PICTURE"])) {
                $arItem["DETAIL_PICTURE"] = (0 < $arItem["DETAIL_PICTURE"] ? CFile::GetFileArray($arItem["DETAIL_PICTURE"]) : false);
                if ($arItem["DETAIL_PICTURE"]) {
                    $arItem["DETAIL_PICTURE"]["ALT"] = $arItem["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_ALT"];
                    if ($arItem["DETAIL_PICTURE"]["ALT"] == "")
                        $arItem["DETAIL_PICTURE"]["ALT"] = $arItem["NAME"];
                    $arItem["DETAIL_PICTURE"]["TITLE"] = $arItem["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_TITLE"];
                    if ($arItem["DETAIL_PICTURE"]["TITLE"] == "")
                        $arItem["DETAIL_PICTURE"]["TITLE"] = $arItem["NAME"];
                }
            }

            $this->arResult["ITEMS"][] = $arItem;
        }
        $this->getNavigation($rsElement, $arItem);
    }

    /**
     * @param $rsElement
     * @param $arItem
     */
    private function getNavigation($rsElement, $arItem)
    {
        $navComponentParameters = array();
        if ($this->arParams["PAGER_BASE_LINK_ENABLE"] === "Y")
        {
            $pagerBaseLink = trim($this->arParams["PAGER_BASE_LINK"]);
            if ($pagerBaseLink === "")
            {
               if (
                $arItem["~LIST_PAGE_URL"]
                )
                {
                    $pagerBaseLink = $arItem["~LIST_PAGE_URL"];
                }
            }

            if ($this->pagerParameters && isset($this->pagerParameters["BASE_LINK"]))
            {
                $pagerBaseLink = $this->pagerParameters["BASE_LINK"];
                unset($this->pagerParameters["BASE_LINK"]);
            }

            $navComponentParameters["BASE_LINK"] = CHTTP::urlAddParams($pagerBaseLink, $this->pagerParameters, array("encode"=>true));
        }
	    $navComponentObject = '';
        $this->arResult["NAV_STRING"] = $rsElement->GetPageNavStringEx(
            $navComponentObject,
            $this->arParams["PAGER_TITLE"],
            $this->arParams["PAGER_TEMPLATE"],
            $this->arParams["PAGER_SHOW_ALWAYS"],
            $this,
            $navComponentParameters
        );
        $this->arResult["NAV_CACHED_DATA"] = null;
        $this->arResult["NAV_RESULT"] = $rsElement;
    }


    /**
     * выполняет логику работы компонента
     *
     * @return void
     */

    public function executeComponent()
    {

        if ($this->StartResultCache($this->arParams['CACHE_TIME'], array($this->prepareNavigation(), $this->prepareFilter()))) {
            try {
                $this->getResult();
                $this->SetResultCacheKeys(array(
                    "ID",
                    "IBLOCK_TYPE_ID",
                    "LIST_PAGE_URL",
                    "NAV_CACHED_DATA",
                    "NAME",
                    "SECTION",
                    "ELEMENTS",
                    "ITEMS_TIMESTAMP_X",
                ));

            } catch (Exception $e) {
                $this->AbortResultCache();
                ShowError($e->getMessage());
                return;

            }
            $this->includeComponentTemplate($this->page);

        }


        $this->setMetaOptions();
    }

    /**
     * Прописывает title, описание итд
     */
    public function setMetaOptions()
    {
        global $USER;
        global $APPLICATION;
        global $INTRANET_TOOLBAR;

        if (isset($this->arResult["ID"])) {

            $arTitleOptions = null;
            if ($USER->IsAuthorized()) {
                if (
                    $APPLICATION->GetShowIncludeAreas()
                    || (is_object($GLOBALS["INTRANET_TOOLBAR"]) && $this->arParams["INTRANET_TOOLBAR"] !== "N")
                    || $this->arParams["SET_TITLE"]
                ) {
                    if (CModule::IncludeModule("iblock")) {
                        $arButtons = CIBlock::GetPanelButtons(
                            $this->arResult["ID"],
                            0,
                            $this->arParams["PARENT_SECTION"],
                            array("SECTION_BUTTONS" => false)
                        );

                        if ($APPLICATION->GetShowIncludeAreas())
                            $this->AddIncludeAreaIcons(CIBlock::GetComponentMenu($APPLICATION->GetPublicShowMode(), $arButtons));

                        if (
                            is_array($arButtons["intranet"])
                            && is_object($INTRANET_TOOLBAR)
                            && $this->arParams["INTRANET_TOOLBAR"] !== "N"
                        ) {
                            $APPLICATION->AddHeadScript('/bitrix/js/main/utils.js');
                            foreach ($arButtons["intranet"] as $arButton)
                                $INTRANET_TOOLBAR->AddButton($arButton);
                        }

                        if ($this->arParams["SET_TITLE"]) {
                            $arTitleOptions = array(
                                'ADMIN_EDIT_LINK' => $arButtons["submenu"]["edit_iblock"]["ACTION"],
                                'PUBLIC_EDIT_LINK' => "",
                                'COMPONENT_NAME' => $this->GetName(),
                            );
                        }
                    }
                }
            }

            $this->SetTemplateCachedData($this->arResult["NAV_CACHED_DATA"]);
	        return;
        }
    }

}

?>