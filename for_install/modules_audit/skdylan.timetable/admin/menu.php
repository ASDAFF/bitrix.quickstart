<?php

use Bitrix\Main\Localization\Loc;

IncludeModuleLangFile(__FILE__);
CModule::IncludeModule('skdylan.timetable');

$arGroups = MainTimeTable::GetGroupList();
$arItems = array();

$number=0;
while ($group = $arGroups->Fetch()) {
    $arItems[$number]["text"] = $group["NAME"];
    $arItems[$number]["url"] = 'skdylan_timetable_events_list.php?SID='.$group["ID"];
    $arItems[$number]["more_url"]  = array('skdylan_timetable_edit_event.php?SID='.$group["ID"], "skdylan_timetable_view_event.php");
    $number++;
}


if ($USER->isAdmin())
{
    $aMenu = array(
        "parent_menu" => "global_menu_services",
        "module_id" => "skdylan.timetable",
        "sort" => 1850,
        "text" => GetMessage("TIMETABLE_MENU_NAME"),
        "title" => GetMessage("TIMETABLE_MENU_NAME"),
        "items_id" => "skdylan_timetable",
        "items" => array(
            array(
                "text" => GetMessage("TIMETABLE_EVENTS"),
                "url" => "skdylan_timetable_list.php?lang=" . LANGUAGE_ID,
                "more_url" => Array("mobile_designer.php"),
                "title" => GetMessage("TIMETABLE_EVENTS"),
                "more_url" => array(
                    "skdylan_timetable_edit_group.php",
                    "skdylan_timetable_events_list.php"
                ),
                "items" => $arItems,
            ),
            array(
                "text" => GetMessage("TIMETABLE_PARTICIPANT"),
                "url" => "skdylan_timetable_participant_event_list.php?lang=" . LANGUAGE_ID,
                "more_url" => Array("skdylan_timetable_edit_participant.php", "skdylan_timetable_view_participant.php"),
                "title" => GetMessage("TIMETABLE_PARTICIPANT"),

            ),
//            array(
//                "text" => GetMessage("TIMETABLE_OPTIONS"),
//                "url" => "s.php?lang=" . LANGUAGE_ID,
//                "more_url" => Array(""),
//                "title" => GetMessage("TIMETABLE_OPTIONS"),
//            )
        ),
    );
    return $aMenu;
}

return false;

?>