<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
//ini_set('display_errors',1);
//error_reporting(E_ALL);
CModule::IncludeModule('skdylan.timetable');
IncludeModuleLangFile(__FILE__);

class TimetableMain extends CBitrixComponent{

    private $template = '';

    private $arNavParams;

    private $arNavigation;

    public function handlerArParams(){
        $this->arParams['IBLOCK_ID'] = (int)$this->arParams['IBLOCK_ID'];
        $this->arParams["PAGER_TEMPLATE"] = trim($this->arParams["PAGER_TEMPLATE"]);

        $this->arParams["DISPLAY_BOTTOM_PAGER"] = ($this->arParams["DISPLAY_BOTTOM_PAGER"] == null) ? "Y" : $this->arParams["DISPLAY_BOTTOM_PAGER"];
        $this->arParams["ONLY_NOLIMIT"] = ($this->arParams["ONLY_NOLIMIT"] == null) ? "Y" : $this->arParams["ONLY_NOLIMIT"];

        $this->arParams["DISPLAY_TOP_PAGER"] = $this->arParams["DISPLAY_TOP_PAGER"]=="Y";
        $this->arParams["DISPLAY_BOTTOM_PAGER"] = $this->arParams["DISPLAY_BOTTOM_PAGER"]!="N";
        $this->arParams["PAGER_DESC_NUMBERING"] = $this->arParams["PAGER_DESC_NUMBERING"] == "Y";
        $this->arParams["PAGER_DESC_NUMBERING_CACHE_TIME"] = intval($this->arParams["PAGER_DESC_NUMBERING_CACHE_TIME"]);
        $this->arParams["PAGER_SHOW_ALL"] = $this->arParams["PAGER_SHOW_ALL"] == "Y";
        $this->arParams["ONLY_NOLIMIT"]  = $this->arParams["ONLY_NOLIMIT"] == "Y";

        $this->arParams["COLOR_TABLE"] = ($this->arParams["COLOR_TABLE"] == null) ? "#f1f1f1" : $this->arParams["COLOR_TABLE"];
        $this->arParams["COLOR_TABLE_H3"] = ($this->arParams["COLOR_TABLE_H3"] == null) ? "#669" : $this->arParams["COLOR_TABLE_H3"];
        $this->arParams["COLOR_TABLE_TEXT_H"] = ($this->arParams["COLOR_TABLE_TEXT_H"] == null) ? "#6699ff" : $this->arParams["COLOR_TABLE_TEXT_H"];
        $this->arParams["COLOR_B"] = ($this->arParams["COLOR_B"] == null) ? "#4691A4" : $this->arParams["COLOR_B"];
        $this->arParams["COLOR_I"] = ($this->arParams["COLOR_I"] == null) ? "#88D5E9" : $this->arParams["COLOR_I"];
        $this->arParams["COLOR_TABLE_TEXT"] = ($this->arParams["COLOR_TABLE_TEXT"] == null) ? "#669" : $this->arParams["COLOR_TABLE_TEXT"];

        $this->arParams["IBLOCK_COUNT"] = ($this->arParams["IBLOCK_COUNT"] == null) ? 100 : $this->arParams["IBLOCK_COUNT"];
        $this->arParams["ONLY_ACTIVE"] = ($this->arParams["ONLY_ACTIVE"] == "N") ? "" : "Y";
        $this->arParams["PAGER_TEMPLATE"] = ($this->arParams["PAGER_TEMPLATE"] == null) ? ".default" : $this->arParams["PAGER_TEMPLATE"];
        $this->arParams["PAGER_SHOW_ALWAYS"] = ($this->arParams["PAGER_SHOW_ALWAYS"] == null) ? "N" : $this->arParams["PAGER_SHOW_ALWAYS"];

        $this->arParams["CACHE_TYPE"] = ($this->arParams["CACHE_TYPE"] == null) ? "A" : $this->arParams["CACHE_TYPE"];

        $this->arResult["EVENTS"] = array();

        if(!isset($this->arParams["FIELDS"]))
            $this->arParams["FIELDS"] = array("FullName", "Phone", "Email");



//        echo "<pre>".print_r($this->arParams["DISPLAY_BOTTOM_PAGER"])."</pre>";
//        var_dump($this->arParams["DISPLAY_BOTTOM_PAGER"]);
//        exit();

//        $this->arParams["NEWS_COUNT"] = intval($this->arParams["NEWS_COUNT"]);
//        if($this->arParams["NEWS_COUNT"]<=0)
//            $this->arParams["NEWS_COUNT"] = 1;

        if(!isset($this->arParams["CACHE_TIME"]))
            $this->arParams["CACHE_TIME"] = 36000000;

        if(isset($_REQUEST["EID"]))
        {
            if(MainTimeTable::GetEventByID(intval($_REQUEST["EID"])) == null){
                global $APPLICATION;
                LocalRedirect($APPLICATION->GetCurPageParam("", array("EID")));
                return;
            }
            else
                $this->template = 'form';

            $this->arResult['EID'] = $_REQUEST["EID"];
        }

        if($this->arParams["DISPLAY_TOP_PAGER"] || $this->arParams["DISPLAY_BOTTOM_PAGER"]) {
            $this->arNavParams = array(
                "nPageSize" => $this->arParams["IBLOCK_COUNT"],
                "bDescPageNumbering" => $this->arParams["PAGER_DESC_NUMBERING"],
                "bShowAll" => $this->arParams["PAGER_SHOW_ALL"],
            );
            $this->arNavigation = CDBResult::GetNavParams($this->arNavParams);
        }
    }

    public function setarResult(){
        $arFilter = array('ACTIVE' => $this->arParams["ONLY_ACTIVE"]);
        if(isset($this->arResult['EID']))
        {
            $eid = intval($this->arResult['EID']);
            $arFilter = array('ID' => $eid);
            $limit = MainTimeTable::GetLimitOfParticipantByID($eid);
            $this->arResult["LIMIT"] = ($limit <= MainTimeTable::GetCountOfParticipant($eid) && $limit != 0);
            if($this->arResult["LIMIT"]) {
                global $APPLICATION;
                LocalRedirect($APPLICATION->GetCurPageParam("", array("EID")));
            }
        }
        $result = MainTimeTable::GetListOfEvents($this->arParams['IBLOCK_ID'], array("PROPERTY_START_EVENT" => "ASC"), $arFilter, $this->arNavParams);
        while ($item = $result->Fetch()) {
//            if($this->arParams["ONLY_NOLIMIT"]) {
//                $limit = MainTimeTable::GetLimitOfParticipantByID($item["ID"]);
//                if ($limit <= MainTimeTable::GetCountOfParticipant($item["ID"]) && $limit != 0) {
//                    continue;
//                }
//            }
            $dateStart = new DateTime($item["PROPERTY_START_EVENT_VALUE"]);
            $dateEnd = new DateTime($item["PROPERTY_END_EVENT_VALUE"]);
            unset($item["PROPERTY_START_EVENT_VALUE"]);
            unset($item["PROPERTY_END_EVENT_VALUE"]);
            $item["TIME_OF_START"] = $dateStart->format("H:i");
            $item["TIME_OF_END"] = $dateEnd->format("H:i");
            $item["DAY_OF_START"] = $dateStart->format("d ").GetMessage("SKDYLAN_TIMETABLE_".$dateStart->format("n"))." | ".
                GetMessage("SKDYLAN_TIMETABLE_".$dateStart->format("l"));
            unset($dateStart);
            unset($dateEnd);
            $this->arResult["EVENTS"][] = $item;
        }



        $navComponentParameters = array();
        $pagerParameters = array();
        if ($this->arParams["PAGER_BASE_LINK_ENABLE"] === "Y")
        {
            $pagerBaseLink = trim($this->arParams["PAGER_BASE_LINK"]);
            if ($pagerBaseLink === "")
            {
                if (
                    $this->arResult["SECTION"]
                    && $this->arResult["SECTION"]["PATH"]
                    && $this->arResult["SECTION"]["PATH"][0]
                    && $this->arResult["SECTION"]["PATH"][0]["~SECTION_PAGE_URL"]
                )
                {
                    $pagerBaseLink = $this->arResult["SECTION"]["PATH"][0]["~SECTION_PAGE_URL"];
                }
                elseif (
                    isset($arItem) && isset($arItem["~LIST_PAGE_URL"])
                )
                {
                    $pagerBaseLink = $arItem["~LIST_PAGE_URL"];
                }
            }

            if ($pagerParameters && isset($pagerParameters["BASE_LINK"]))
            {
                $pagerBaseLink = $pagerParameters["BASE_LINK"];
                unset($pagerParameters["BASE_LINK"]);
            }

            $navComponentParameters["BASE_LINK"] = CHTTP::urlAddParams($pagerBaseLink, $pagerParameters, array("encode"=>true));
        }

        $this->arResult["NAV_STRING"] = $result->GetPageNavStringEx(
            $navComponentObject, $this->arParams["PAGER_TITLE"], $this->arParams["PAGER_TEMPLATE"], $this->arParams["PAGER_SHOW_ALWAYS"],
            $this->arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],$navComponentParameters);

        $this->arResult["NAV_CACHED_DATA"] = null;
        $this->arResult["NAV_RESULT"] = $result;
        $this->arResult["NAV_PARAM"] = $navComponentParameters;

    }

    function postResult(){
        if($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["submit"] <> '')
        {
            $this->arResult["fullName"] = trim($_POST["FullName"]);
            $this->arResult["Email"] = trim($_POST["Email"]);
            $this->arResult["Phone"] = trim($_POST["Phone"]);
            $this->arResult["Comment"] = trim($_POST["Comment"]);

            $eid = $_POST["EID"];
            if(array_search('FullName', $this->arParams['FIELDS']) !== false) {
                if (strlen($this->arResult["fullName"]) == 0) {
                    $this->arResult['ErrorInput']['FullName'] = true;
                }
            }
            if(array_search('Email', $this->arParams['FIELDS']) !== false) {
                if (strlen($this->arResult["Email"]) == 0) {
                    $this->arResult['ErrorInput']['Email'] = true;
                }
            }
            if(array_search('Phone', $this->arParams['FIELDS']) !== false) {
                if (strlen($this->arResult["Phone"]) == 0) {
                    $this->arResult['ErrorInput']['Phone'] = true;
                }
            }
            if(array_search('Comment', $this->arParams['FIELDS']) !== false) {
                if (strlen($this->arResult["Comment"]) == 0) {
                    $this->arResult['ErrorInput']['Comment'] = true;
                }
            }

            $limit = MainTimeTable::GetLimitOfParticipantByID($eid);
            if($limit <= MainTimeTable::GetCountOfParticipant($eid) && $limit != 0) {
                global $APPLICATION;
                $this->arResult['ErrorInput']['Limit'] = true;
                setcookie("success", 0);
                LocalRedirect($APPLICATION->GetCurPageParam("", array("EID")));
            }

            if(!$this->arResult['ErrorInput']['Phone'] && !$this->arResult['ErrorInput']['Email']) {
                $result = MainTimeTable::CheckParticipantInEvent($eid, array("PROPERTY_PHONE_NUMBER" => $this->arResult["Phone"],
                    "PROPERTY_EMAIL" => $this->arResult["Email"]));

                if($result["PROPERTY_PHONE_NUMBER"] == true)
                    $this->arResult['ErrorInput']['PhoneIsUse'] = true;
                if($result["PROPERTY_EMAIL"] == true)
                    $this->arResult['ErrorInput']['EmailIsUse'] = true;
            }

            if(!isset($this->arResult['ErrorInput'])) {
                MainTimeTable::AddParticipant($eid, $this->arResult["fullName"], $this->arResult["Phone"], $this->arResult["Email"], $this->arResult["Comment"]);
                if($this->arParams["ONLY_NOLIMIT"])
                {
                    if($limit <= MainTimeTable::GetCountOfParticipant($eid) && $limit != 0)
                        MainTimeTable::SetActiveEventByID($eid, "N");
                }
                global $APPLICATION;
                setcookie("success", true);
                LocalRedirect($APPLICATION->GetCurPageParam("", array("EID")));
                return true;
            }
            else return false;
        }
    }

    function post(){
        if(isset($_COOKIE["success"]))
            setcookie("success", "", time()-36000);
    }

    public function executeComponent()
    {
        $this->handlerArParams();

        $this->postResult();

       // if($this->StartResultCache(false, array($this->arNavigation, $this->template, $_REQUEST["EID"], $this->arResult['ErrorInput'],isset($_COOKIE["success"])))) {
            $this->setarResult();



            if ($this->arResult["EVENTS"] == NULL) {
                $this->abortResultCache();
            }

            $this->includeComponentTemplate($this->template);
        //}

        $this->post();

    }
}

?>