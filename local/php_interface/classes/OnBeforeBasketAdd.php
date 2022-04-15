<?php
/**
 * Copyright (c) 15/4/2022 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

class OnBeforeBasketAdd
{
    function OnBeforeBasketAdd(&$arFields){
        global $USER;
        if($USER->IsAuthorized()){
            CModule::IncludeModule('tc');
            $rsUser = CUser::GetByID($USER->GetID());
            $arUser = $rsUser->Fetch();
            if($arUser['UF_CARD_MODERATED']){
                $tcCards = new tcCards();
                $res = $tcCards->GetByNum($arUser['UF_CARD']);
                if($card = $res->Fetch()){
                    $cardnomer = $card["nomer"];
                    $proc = $card["procent"];
                    if($proc){
                        $arFields["PRICE"] = $arFields["PRICE"] / 100 * (100 - $proc);
                        $arFields["CALLBACK_FUNC"] = '';
                    }
                }
            }
        }
    }
}