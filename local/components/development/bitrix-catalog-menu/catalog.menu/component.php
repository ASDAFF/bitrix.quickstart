<?php

    /**
     * @author Gennadiy Hatuntsev
     * @package catalog.menu
     *
     * @var array $arParams
     * @var array $arResult
     * @var CDatabase $DB
     */

    if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
        die();
    }

    $arResult = array();
    $iblockID = intval($arParams['IBLOCK_ID']);
    $iblock = CIBlock::GetByID($iblockID)->Fetch();

    if ($iblock) {

        $sortField = (isset($arParams["SORT_FIELD"]) && in_array($arParams["SORT_FIELD"], array("NAME", "SORT")))? $arParams["SORT_FIELD"]: "NAME";
        $sortOrder = (isset($arParams["SORT_ORDER"]) && in_array($arParams["SORT_ORDER"], array("ASC", "DESC")))? $arParams["SORT_ORDER"]: "ASC";

        // Разделы первого уровня
        $sort = array(
            $sortField => $sortOrder
        );
        $filter = array(
            'IBLOCK_ID' => $iblockID,
            'ACTIVE' => 'Y',
            'DEPTH_LEVEL' => 1
        );
        $result = CIBlockSection::GetList($sort, $filter);
        while ($section = $result->GetNext()) {
            $arResult[1][$section["ID"]] = $section;
        }

        // Разделы второго уровня
        $sql = "
            SELECT T.*
            FROM `b_iblock_section` AS T
                JOIN `b_iblock_section` AS SUB
                    ON T.IBLOCK_SECTION_ID = SUB.ID
            WHERE
                T.ACTIVE = 'Y' AND
                T.IBLOCK_ID = ".$iblockID."
            ORDER BY
                SUB.LEFT_MARGIN, T.".$sortField." ".$sortOrder.", T.LEFT_MARGIN
        ";

        $result = $DB->Query($sql);

        while ($section = $result->GetNext()) {
            $replace = array(
                "#SITE_DIR#" => '',
                "#SECTION_ID#" => $section["ID"],
                "#SECTION_CODE#" => $section["CODE"]
            );
            $section["SECTION_PAGE_URL"] = str_ireplace(array_keys($replace), array_values($replace), $iblock["SECTION_PAGE_URL"]);
            $arResult[$section["DEPTH_LEVEL"]][$section["IBLOCK_SECTION_ID"]][$section["ID"]] = $section;
        }

    }

    $this->IncludeComponentTemplate();