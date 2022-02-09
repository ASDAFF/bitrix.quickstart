<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
CModule::IncludeModule('tc');
if(isset($_REQUEST['card'])){
    $card = trim($_REQUEST['card']); 
    $user = new CUser;
    $user->Update($USER->GetID(),
                  Array("UF_CARD"           => $card , 
                        "UF_CARD_MODERATED" => false));
    CEvent::Send("CARD_ADD", 's1', array("USER_ID" => $USER->GetID())); 
    $arResult['ADDED'] = true;
}
$rsUser = CUser::GetByID($USER->GetID());
$arUser = $rsUser->Fetch();
if($arUser['UF_CARD_MODERATED']){
    $tcCards = new tcCards();
    $res = $tcCards->GetByNum($arUser['UF_CARD']);
    if($card = $res->Fetch()){ 
        //array(7) {
        //  ["id"]=>
        //  string(6) "191949"
        //  ["vladelec"]=>
        //  string(50) "ВАХРУШЕН СЕРГЕЙ ВИКТОРОВИЧ"
        //  ["ostatok"]=>
        //  string(1) "0"
        //  ["summa"]=>
        //  string(1) "0"
        //  ["procent"]=>
        //  string(1) "2"
        //  ["nomer"]=>
        //  string(6) "004756"
        //  ["tipsidki"]=>
        //  string(39) "Накопительная скидка" 
        $arResult['CARD'] = $card;   
    } 
}
$this->IncludeComponentTemplate();