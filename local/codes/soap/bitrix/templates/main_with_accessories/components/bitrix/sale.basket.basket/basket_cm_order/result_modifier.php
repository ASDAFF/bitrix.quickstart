<?php
 
 foreach($arResult["ITEMS"]["AnDelCanBuy"] as &$arBasketItems){
     if(!$arBasketItems['PREVIEW_PICTURE']){
         
         $ar_res = CIBlockElement::GetByID($arBasketItems["PRODUCT_ID"])->GetNext();
         if($ar_res['PREVIEW_PICTURE']) 
             $arBasketItems['PREVIEW_PICTURE'] = $ar_res['PREVIEW_PICTURE'];
     }
         
 }
      
     
     
switch (true){
    case isset($_REQUEST["contButton"]) && $_REQUEST["CurrentStep"] == 2:
    case isset($_REQUEST["backButton"]) && $_REQUEST["CurrentStep"] == 5: 
        $arResult['STEP'] = 3;
        break;  
    case isset($_REQUEST["contButton"]) && $_REQUEST["CurrentStep"] == 3:
    case $_REQUEST["CurrentStep"] == 7: 
        $arResult['STEP'] = 4;
        break;        
    case isset($_REQUEST["backButton"]) && $_REQUEST["CurrentStep"] == 3:  // мутый момент
        $arResult['STEP'] = 2;
        break;
    case isset($_REQUEST["BITRIX_SM_LOGIN"]): 
        $arResult['STEP'] = 2;
        break; 
//    case $arResult["CurrentStep"] == 4:
//        $arResult['STEP'] = 4;
//        break;    
    default: 
        $arResult['STEP'] = 2;
        break;
}
 