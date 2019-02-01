<?
$APPLICATION->AddPanelButton(
    Array(
        "ID" => "400", //определяет уникальность кнопки
        "TEXT" => "Редактировать левое меню",
        "MAIN_SORT" => 1000, //индекс сортировки для групп кнопок
        "SORT" => 10, //сортировка внутри группы
        "HREF" => "/bitrix/admin/fileman_file_edit.php?path=%2Finclude%2Fmenu%2Flinks.php&full_src=Y", //или javascript:MyJSFunction())
        "ALT" => "Редактировать левое меню", //старый вариант
        ),
    $bReplace = false //заменить существующую кнопку?
);	
?>