<?php
// Дата рождения
if($arResult["USER"]["PERSONAL_BIRTHDAY"]) {
    $arResult["USER"]["PERSONAL_BIRTHDAY_FORMATED"] =  FormatDate("d F Y", MakeTimeStamp($arResult["USER"]["PERSONAL_BIRTHDAY"]));
}