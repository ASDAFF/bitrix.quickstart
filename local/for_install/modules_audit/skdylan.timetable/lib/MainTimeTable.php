<?php
use Bitrix\Main\Config\Option;
use Bitrix\Main\Page\Asset;

class MainTimeTable
{
    public static function AddGroup($name_iBlock)
    {
        if (CModule::IncludeModule('iblock')) {
            $iB = new CIBlock;

            $IBLOCK_TYPE = "timetable"; // ��� ���������
            $SITE_ID = "s1"; // ID �����

            // ��������� �����, ������� ����� ������ ������ �� ��������
//        $contentGroupId = $this->GetGroupIdByCode("CONTENT");
//        $editorGroupId = $this->GetGroupIdByCode("EDITOR");
//        $ownerGroupId = $this->GetGroupIdByCode("OWNER");

            $arFields = Array(
                "ACTIVE" => "Y",
                "NAME" => $name_iBlock,
                //"CODE" => "catalog",
                "IBLOCK_TYPE_ID" => $IBLOCK_TYPE,
                "SITE_ID" => $SITE_ID,
                "SORT" => "5",
                //"GROUP_ID" => $arAccess, // ����� �������
                "FIELDS" => array(
                    "DETAIL_PICTURE" => array(
                        "IS_REQUIRED" => "N", // �� ������������
                    ),
                    "PREVIEW_PICTURE" => array(
                        "IS_REQUIRED" => "N", // �� ������������
                    ),
                    "SECTION_PICTURE" => array(
                        "IS_REQUIRED" => "N", // �� ������������
                    ),
                    "DETAIL_TEXT_TYPE" => array(      // ��� ���������� ��������
                        "DEFAULT_VALUE" => "html",
                    ),
                    "SECTION_DESCRIPTION_TYPE" => array(
                        "DEFAULT_VALUE" => "html",
                    ),
                    "IBLOCK_SECTION" => array(         // �������� � �������� ������������
                        "IS_REQUIRED" => "N",
                    ),

                ),
                // ������� �������
                "LIST_PAGE_URL" => "#SITE_DIR#/events/",
                "SECTION_PAGE_URL" => "#SITE_DIR#/events/#SECTION_CODE#/",
                "DETAIL_PAGE_URL" => "#SITE_DIR#/events/#SECTION_CODE#/#ELEMENT_CODE#/",
                "INDEX_SECTION" => "Y", // ������������� ������� ��� ������ ������
                "INDEX_ELEMENT" => "Y", // ������������� �������� ��� ������ ������

                "VERSION" => 1, // �������� ��������� � ����� �������

                "ELEMENT_NAME" => GetMessage("SKDYLAN_TIMETABLE_SOBYTIE"),
                "ELEMENTS_NAME" => GetMessage("SKDYLAN_TIMETABLE_SOBYTIA"),
                "ELEMENT_ADD" => GetMessage("SKDYLAN_TIMETABLE_DOBAVITQ_SOBYTIE"),
                "ELEMENT_EDIT" => GetMessage("SKDYLAN_TIMETABLE_IZMENITQ_SOBYTIE"),
                "ELEMENT_DELETE" => GetMessage("SKDYLAN_TIMETABLE_UDALITQ_SOBYTIE"),
                "SECTION_NAME" => GetMessage("SKDYLAN_TIMETABLE_GRUPPA"),
                "SECTIONS_NAME" => GetMessage("SKDYLAN_TIMETABLE_GRUPPY"),
                "SECTION_ADD" => GetMessage("SKDYLAN_TIMETABLE_DOBAVITQ_GRUPPU"),
                "SECTION_EDIT" => GetMessage("SKDYLAN_TIMETABLE_IZMENITQ_GRUPPU"),
                "SECTION_DELETE" => GetMessage("SKDYLAN_TIMETABLE_UDALITQ_GRUPPU"),
            );

            $ID = $iB->Add($arFields);
            if($ID == false )
                return false;

            // ���������� �������

            $ibp = new CIBlockProperty;

            $arFields = Array(
                Array(
                    "NAME" => GetMessage("SKDYLAN_TIMETABLE_DATA_NACALA"),
                    "ACTIVE" => "Y",
                    //"SORT" => -777, // ����������
                    "CODE" => "start_event",
                    "PROPERTY_TYPE" => "S", // ������
                    "USER_TYPE" => "DateTime",
                    "FILTRABLE" => "Y", // �������� �� �������� ������ ��������� ���� ��� ���������� �� ����� ��������
                    "IBLOCK_ID" => $ID,
                    "IS_REQUIRED" => "Y"
                ),
                Array(
                    "NAME" => GetMessage("SKDYLAN_TIMETABLE_DATA_OKONCANIA"),
                    "ACTIVE" => "Y",
                    //"SORT" => -777, // ����������
                    "CODE" => "end_event",
                    "PROPERTY_TYPE" => "S", // ������
                    "USER_TYPE" => "DateTime",
                    "FILTRABLE" => "Y", // �������� �� �������� ������ ��������� ���� ��� ���������� �� ����� ��������
                    "IBLOCK_ID" => $ID,
                    "IS_REQUIRED" => "Y"
                ),
                Array(
                    "NAME" => GetMessage("SKDYLAN_TIMETABLE_KOLICESTVO_UCASTNIKO"),
                    "ACTIVE" => "Y",
                    //"SORT" => -777, // ����������
                    "CODE" => "count_participant",
                    "PROPERTY_TYPE" => "N", // ������
                    "FILTRABLE" => "Y", // �������� �� �������� ������ ��������� ���� ��� ���������� �� ����� ��������
                    "IBLOCK_ID" => $ID,
                    "IS_REQUIRED" => "Y",
                    "VALUES" => array(
                        "VALUE" => 0,
                    ),
                )
            );

            foreach ($arFields as $item)
            {
                $propId = $ibp->Add($item);
                if($propId == false )
                    return false;
            }

            return true;

//            if ($ID > 0) {
//                echo "&mdash; �������� \"������� �������\" ������� ������<br />";
//            } else {
//                echo "&mdash; ������ �������� ��������� \"������� �������\"<br />";
//                return false;
//            }

        }
    }

    public static function AddParticipantGroup()
    {
        $group_name = GetMessage("SKDYLAN_TIMETABLE_UCASTNIKI");
        if (CModule::IncludeModule('iblock')) {
            $iB = new CIBlock;

            $IBLOCK_TYPE = "participant"; // ��� ���������
            $SITE_ID = "s1"; // ID �����

            // ��������� �����, ������� ����� ������ ������ �� ��������
//        $contentGroupId = $this->GetGroupIdByCode("CONTENT");
//        $editorGroupId = $this->GetGroupIdByCode("EDITOR");
//        $ownerGroupId = $this->GetGroupIdByCode("OWNER");

            $arFields = Array(
                "ACTIVE" => "Y",
                "NAME" => $group_name,
                //"CODE" => "catalog",
                "IBLOCK_TYPE_ID" => $IBLOCK_TYPE,
                "SITE_ID" => $SITE_ID,
                "SORT" => "5",
                //"GROUP_ID" => $arAccess, // ����� �������
                "FIELDS" => array(
                    "DETAIL_PICTURE" => array(
                        "IS_REQUIRED" => "N", // �� ������������
                    ),
                    "PREVIEW_PICTURE" => array(
                        "IS_REQUIRED" => "N", // �� ������������
                    ),
                    "SECTION_PICTURE" => array(
                        "IS_REQUIRED" => "N", // �� ������������
                    ),
                    "DETAIL_TEXT_TYPE" => array(      // ��� ���������� ��������
                        "DEFAULT_VALUE" => "html",
                    ),
                    "SECTION_DESCRIPTION_TYPE" => array(
                        "DEFAULT_VALUE" => "html",
                    ),
                    "IBLOCK_SECTION" => array(         // �������� � �������� ������������
                        "IS_REQUIRED" => "N",
                    ),

                ),
                // ������� �������
                "LIST_PAGE_URL" => "",
                "SECTION_PAGE_URL" => "",
                "DETAIL_PAGE_URL" => "",
                "INDEX_SECTION" => "N", // ������������� ������� ��� ������ ������
                "INDEX_ELEMENT" => "N", // ������������� �������� ��� ������ ������

                "VERSION" => 1, // �������� ��������� � ����� �������

                "ELEMENT_NAME" => GetMessage("SKDYLAN_TIMETABLE_SOBYTIE"),
                "ELEMENTS_NAME" => GetMessage("SKDYLAN_TIMETABLE_SOBYTIA"),
                "ELEMENT_ADD" => GetMessage("SKDYLAN_TIMETABLE_DOBAVITQ_SOBYTIE"),
                "ELEMENT_EDIT" => GetMessage("SKDYLAN_TIMETABLE_IZMENITQ_SOBYTIE"),
                "ELEMENT_DELETE" => GetMessage("SKDYLAN_TIMETABLE_UDALITQ_SOBYTIE"),
                "SECTION_NAME" => GetMessage("SKDYLAN_TIMETABLE_GRUPPA"),
                "SECTIONS_NAME" => GetMessage("SKDYLAN_TIMETABLE_GRUPPY"),
                "SECTION_ADD" => GetMessage("SKDYLAN_TIMETABLE_DOBAVITQ_GRUPPU"),
                "SECTION_EDIT" => GetMessage("SKDYLAN_TIMETABLE_IZMENITQ_GRUPPU"),
                "SECTION_DELETE" => GetMessage("SKDYLAN_TIMETABLE_UDALITQ_GRUPPU"),
            );

            $ID = $iB->Add($arFields);
            if($ID == false )
                return false;

            // ���������� �������

            $ibp = new CIBlockProperty;

            $arFields = Array(
                Array(
                    "NAME" => GetMessage("SKDYLAN_TIMETABLE_FIO"),
                    "ACTIVE" => "Y",
                    //"SORT" => -777, // ����������
                    "CODE" => "full_name",
                    "PROPERTY_TYPE" => "S", // ������
                    "FILTRABLE" => "Y", // �������� �� �������� ������ ��������� ���� ��� ���������� �� ����� ��������
                    "IBLOCK_ID" => $ID,
                    "IS_REQUIRED" => "N"
                ),
                Array(
                    "NAME" => GetMessage("SKDYLAN_TIMETABLE_NOMER_TELEFONA"),
                    "ACTIVE" => "Y",
                    //"SORT" => -777, // ����������
                    "CODE" => "phone_number",
                    "PROPERTY_TYPE" => "S", // ������
                    "FILTRABLE" => "Y", // �������� �� �������� ������ ��������� ���� ��� ���������� �� ����� ��������
                    "IBLOCK_ID" => $ID,
                    "IS_REQUIRED" => "N"
                ),
                Array(
                    "NAME" => "E-mail",
                    "ACTIVE" => "Y",
                    //"SORT" => -777, // ����������
                    "CODE" => "Email",
                    "PROPERTY_TYPE" => "S", // ������
                    "FILTRABLE" => "Y", // �������� �� �������� ������ ��������� ���� ��� ���������� �� ����� ��������
                    "IBLOCK_ID" => $ID,
                    "IS_REQUIRED" => "N"
                ),
                Array(
                    "NAME" => GetMessage("SKDYLAN_TIMETABLE_KOMMENTARIY"),
                    "ACTIVE" => "Y",
                    //"SORT" => -777, // ����������
                    "CODE" => "comment",
                    "PROPERTY_TYPE" => "S", // ������
                    "IBLOCK_ID" => $ID,
                    "IS_REQUIRED" => "N"
                ),
            );

            foreach ($arFields as $item)
            {
                $propId = $ibp->Add($item);
                if($propId == false )
                    return false;
            }

            return true;

        }
    }

    public static function GetGroupList($arOrder = array(), $arFilter = array())
    {
        if (CModule::IncludeModule('iblock')) {

            $arFilter = array_merge($arFilter, Array("TYPE" => "timetable"));
            $dbEl = CIBlock::GetList($arOrder, $arFilter);

            return $dbEl;
        }
        return null;
    }

    public static function GetGroupByID($id){
        if (CModule::IncludeModule('iblock')) {

            $res = CIBlock::GetByID($id);
            if($ar_res = $res->GetNext()) {
                return $ar_res;
            }
        }
        return null;
    }

    public static function SetGroup($id, $arFields){
        if (CModule::IncludeModule('iblock')){
            $fields = CIBlock::getFields($id);

            $fields["NAME"] = "test";
            CIBlock::setFields($id, $fields);

            $arPICTURE = $_FILES["PICTURE"];
            $ib = new CIBlock;
            $arFields = Array(
                "ACTIVE" => $arFields["ACTIVE"],
                "NAME" => $arFields["NAME"],
                "SORT" => $arFields["SORT"],
            );

            $res = $ib->Update($id, $arFields);

            return $res;
        }
    }

    public static function GetEventByID($id){
        if (CModule::IncludeModule('iblock')) {
            $res = CIBlockElement::GetByID($id);
            if($res->DB->db_Conn->affected_rows > 0) {
                if ($ar_res = $res->GetNext())
                if($ar_res["IBLOCK_TYPE_ID"] == "timetable") {
                    $property = CIBlockElement::GetProperty($ar_res["IBLOCK_ID"], $id);
                    while ($ob = $property->GetNext()) {
                        $ar_res["PROPERTY"][$ob["CODE"]] = $ob;
                    }
                    return $ar_res;
                }
            }
        }
        return null;
    }

    static function GetIdIBlockParticipant()
    {
        if(CModule::IncludeModule('iblock')) {
            $dbEl = CIBlock::GetList(Array(), Array("TYPE" => "participant", "CHECK_PERMISSIONS" => "N"));
            $participantIBlock = $dbEl->Fetch();
            if (isset($participantIBlock["ID"]))
                return IntVal($participantIBlock["ID"]);
        }
        return false;
    }

    public static function GetParticipant($arOrder = array(), $arFilter = array(), $arNavStartParams = array())
    {
        if($arOrder == null)
            $arOrder = array();
            
        if (CModule::IncludeModule('iblock')) {

            if($idBlock = self::GetIdIBlockParticipant()) {

                if(array_key_exists("EVENT", $arOrder)) {
                    array_unshift($arOrder, array("PROPERTY_EVENT" =>$arOrder["EVENT"]));
                    unset($arOrder["EVENT"]);
                }

                $arSelect = Array("ID", "NAME", "PROPERTY_PHONE_NUMBER", "PROPERTY_EMAIL", "PROPERTY_COMMENT", "PROPERTY_EVENT");
                $arFilterResult = array_merge(Array("IBLOCK_ID" => $idBlock), $arFilter);

                $res = CIBlockElement::GetList($arOrder, $arFilterResult, false, $arNavStartParams, $arSelect);

                return $res;
            }
        }
        return null;
    }

    public static function GetParticipantByID($id){
        if (CModule::IncludeModule('iblock')) {
            $res = CIBlockElement::GetByID(intval($id));
            if ($ar_res = $res->GetNext()) {
                $property = CIBlockElement::GetProperty($ar_res["IBLOCK_ID"],$id);
                while ($ob = $property->GetNext())
                {
                    $ar_res["PROPERTY"][$ob["CODE"]] = $ob;
                }
                return $ar_res;

            }
        }

        return null;
    }

    public static function AddParticipant($event_id , $name, $phone, $email, $comment){
        if($idBlock = self::GetIdIBlockParticipant()){
            $el = new CIBlockElement;
            var_dump($idBlock);
            $props = array(
                "phone_number" => $phone,
                "email" => $email,
                "comment" => $comment,
                "event" => $event_id
            );

            $arLoadProductArray = Array(
                //"MODIFIED_BY"    => USER->GetID(), // ������� ������� ������� �������������
                "IBLOCK_SECTION_ID" => false,          // ������� ����� � ����� �������
                "IBLOCK_ID" => $idBlock,
                "PROPERTY_VALUES" => $props,
                "NAME" => $name,
                "ACTIVE" => "Y",            // �������
                "DETAIL_TEXT" => $comment,
            );

            if ($PRODUCT_ID = $el->Add($arLoadProductArray))
                return $PRODUCT_ID;
        }
        return false;
    }

    public static function DeleteGroup($iblock_id){
        if (CModule::IncludeModule('iblock')) {
            $result = CIBlock::Delete($iblock_id);
            return $result;
        }
        return null;
    }

    public static function DeleteEvent($event_id){
        if (CModule::IncludeModule('iblock')) {
            CIBlockElement::Delete($event_id);
        }
    }

    public static function DeleteParticipant($participant_id){
        if (CModule::IncludeModule('iblock')) {
            CIBlockElement::Delete($participant_id);
        }
    }

    public static function GetListOfEvents($group_id, $arOrder = array(), $arFilter = array(), $arNavStartParams = array()){
        if (CModule::IncludeModule('iblock'))
        {
            $arSelect = Array("ID", "NAME", "PROPERTY_START_EVENT", "PROPERTY_END_EVENT", "PROPERTY_COUNT_PARTICIPANT", "ACTIVE", "DETAIL_TEXT");
            if($group_id != false)
                $arFilterResult = array_merge(Array("IBLOCK_ID"=>IntVal($group_id), $arFilter));
            else
                $arFilterResult = array_merge(Array("IBLOCK_TYPE"=>"timetable", $arFilter));

            $res = CIBlockElement::GetList($arOrder, $arFilterResult, false, $arNavStartParams, $arSelect);

            return $res;
        }
        return null;
    }

    public static function AddEvent($group_id, $name, $timeStart, $timeEnd, $count, $comment){
        $el = new CIBlockElement;

        $props = array(
            "start_event" => $timeStart,
            "end_event" => $timeEnd,
            "count_participant" => $count
        );

        $arLoadProductArray = Array(
            //"MODIFIED_BY"    => USER->GetID(), // ������� ������� ������� �������������
            "IBLOCK_SECTION_ID" => false,          // ������� ����� � ����� �������
            "IBLOCK_ID"      => intval($group_id),
            "PROPERTY_VALUES"=> $props,
            "NAME"           => $name,
            "ACTIVE"         => "Y",            // �������
            "DETAIL_TEXT"    => $comment,
        );

        if($PRODUCT_ID = $el->Add($arLoadProductArray))
            return $PRODUCT_ID;
        else
            return false;
    }

    public static function SetEvent($event_id, $name, $timeStart, $timeEnd, $count, $comment){
        $el = new CIBlockElement;

        $PROP = array(
            "start_event" => $timeStart,
            "end_event" => $timeEnd,
            "count_participant" => $count
        );

        $arLoadProductArray = Array(
            //"MODIFIED_BY"    => $USER->GetID(), // ������� ������� ������� �������������
            "IBLOCK_SECTION" => false,          // ������� ����� � ����� �������
            "PROPERTY_VALUES"=> $PROP,
            "NAME"           => $name,
            "DETAIL_TEXT"    => $comment,
        );

        $res = $el->Update($event_id, $arLoadProductArray);
        return $res;
    }

    public static function SetActiveEventByID($event_id, $active){
        $el = new CIBlockElement;
        $arLoadProductArray = Array(
          "ACTIVE" => $active
        );
        $res = $el->Update($event_id, $arLoadProductArray);
        return $res;
    }

    public static function SetParticipant($participant_id, $event_id, $name, $phone = "", $email ="", $comment = ""){
        $el = new CIBlockElement;

        $PROP = array(
            "phone_number" => $phone,
            "email" => $email,
            "comment" => $comment,
            "event" => $event_id
        );

        $arLoadProductArray = Array(
            //"MODIFIED_BY"    => $USER->GetID(), // ������� ������� ������� �������������
            "IBLOCK_SECTION" => false,          // ������� ����� � ����� �������
            "PROPERTY_VALUES"=> $PROP,
            "NAME"           => $name,
        );

        $res = $el->Update($participant_id, $arLoadProductArray);
        return $res;
    }

    public static function CheckEvents($group_id){
        $arIBlock = false;
        if (CModule::IncludeModule('iblock')) {
            $arIBlock = CIBlock::GetArrayByID($group_id);
            if($arIBlock != false)
                $arIBlock = true;
        }
        return $arIBlock;
    }

    public static function GetGroupNameByID($group_id){
        if (CModule::IncludeModule('iblock')) {
            $res = CIBlock::GetByID($group_id);
            if ($ar_res = $res->GetNext())
                return $ar_res['NAME'];
        }
    }

    public static function GetElementNameByID($id){
        if (CModule::IncludeModule('iblock')) {
            $res = CIBlockElement::GetByID(intval($id));
            if($ar_res = $res->GetNext())
                return $ar_res['NAME'];
            else
                return "";
        }
    }

    public static function GetLimitOfParticipantByID($id){
        $participant = self::GetListOfEvents(array(),array(),array("ID" => $id))->Fetch();
        return $participant["PROPERTY_COUNT_PARTICIPANT_VALUE"];
    }

    public static function GetCountOfParticipant($event_id){
        $participant = self::GetParticipant(array(),array("PROPERTY_EVENT" => $event_id));
        return $participant->DB->db_Conn->affected_rows;
    }

    public static function CheckParticipantInEvent($event_id, $fields){
        $listOfParticipant = self::GetParticipant(array(), array("PROPERTY_EVENT" => $event_id));
        $result = array();
        while ($item = $listOfParticipant->Fetch()) {
            foreach ($fields as $key => $value){
                if($item[$key."_VALUE"] == $value) {
                    $result[$key] = true;
                }
            }
        }
        return $result;
    }
}