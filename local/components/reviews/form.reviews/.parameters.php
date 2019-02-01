<?/*
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$iblockTypes = CIBlockParameters::GetIBlockTypes();

$iblockSort = array();
$iblockFilter = array('TYPE' => $arCurrentValues['IBLOCK_TYPE']);
$iblockRes = CIBlock::GetList($iblockSort, $iblockFilter);
while($iblock = $iblockRes->Fetch())
{
    $iblocks[$iblock["ID"]] = $iblock["NAME"];
}

$emailEventsRes = CEventType::GetList();
while($emailEvent = $emailEventsRes->Fetch())
{
    $emailEvents[$emailEvent["ID"]] = $emailEvent["NAME"];
}


$arComponentParameters = array(
    "GROUPS" => array(
    ),
    "PARAMETERS" => array(
        "IBLOCK_TYPE" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("IBLOCK_TYPE"),
            "TYPE" => "LIST",
            "VALUES" => $iblockTypes,
            "DEFAULT" => '',
            "ADDITIONAL_VALUES" => "N",
            "REFRESH" => "Y",
        ),

        "IBLOCK_ID" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("T_IBLOCK_DESC_LIST_ID"),
            "TYPE" => "LIST",
            "VALUES" => $iblocks,
            "DEFAULT" => '',
            "ADDITIONAL_VALUES" => "Y",
            "REFRESH" => "N",
        ),

        "EMAIL_EVENT" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("EMAIL_EVENT"),
            "TYPE" => "LIST",
            "VALUES" => $emailEvents,
            "DEFAULT" => '',
            "ADDITIONAL_VALUES" => "N",
            "REFRESH" => "N",
        ),

//        "FIELDS_COUNT" => array(
//            "PARENT" => "BASE",
//            "NAME" => GetMessage("FIELDS_COUNT"),
//            "TYPE" => "STRING",
//            "DEFAULT" => '3',
//            "REFRESH" => "Y",
//        ),


        
        // "код параметра" => array(
        //     "PARENT" => "код группы",  // если нет - ставится ADDITIONAL_SETTINGS
        //     "NAME" => "название параметра на текущем языке",
        //     "TYPE" => "тип элемента управления, в котором будет устанавливаться параметр",
        //         STRING - текстовое поле ввода.
        //         CHECKBOX - да/нет.
        //         CUSTOM - позволяет создавать кастомные элементы управления.
        //         FILE - выбор файла.
        //     "REFRESH" => "перегружать настройки или нет после выбора (N/Y)",
        //     "MULTIPLE" => "одиночное/множественное значение (N/Y)",
        //     "VALUES" => "массив значений для списка (TYPE = LIST)",
        //     "ADDITIONAL_VALUES" => "показывать поле для значений, вводимых вручную (Y/N)",
        //     "SIZE" => "число строк для списка (если нужен не выпадающий список)",
        //     "DEFAULT" => "значение по умолчанию",
        //     "COLS" => "ширина поля в символах",
        // ),
        
    ),
);

//if(intval($arCurrentValues['FIELDS_COUNT']) > 0)
//{
//    for($i == 0; $i < intval($arCurrentValues['FIELDS_COUNT']); $i++)
//    {
//        $arComponentParameters["PARAMETERS"]["FIELD_".$i] = array(
//                "PARENT" => "BASE",
//                "NAME" => GetMessage("FIELDS_COUNT"),
//                "TYPE" => "STRING",
//                "DEFAULT" => '3',
//                "REFRESH" => "Y",
//        );
//
//    }
//}


*/?>