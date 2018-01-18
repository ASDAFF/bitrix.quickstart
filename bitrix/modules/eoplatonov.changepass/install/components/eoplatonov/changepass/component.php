<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if($arParams[INCLUDE_JQUERY]=="Y") CJSCore::Init(array("jquery"));
$arResult[IS_AUTHORIZED]=$USER->IsAuthorized();
$arResult[CURRENT_USER]=$USER->GetByID($USER->GetID())->GetNext();


if($_POST[changepass]=="Y"){
    if(check_bitrix_sessid()){
        $last_pass_match=true;
        if($arParams[LAST_PASS]=="Y"){
            $res=$USER->Login((string)$arResult[CURRENT_USER][LOGIN], (string)$_POST[last_pass]);
            $last_pass_match=(!is_array($res))?true:false;
        }
        if(strlen($_POST[new_pass])<6 || strlen($_POST[new_pass2])<6 ) $arResult[ANSWER]=GetMessage("CHPASS_PASS_MIN");
        else{
            if($_POST[new_pass]!=$_POST[new_pass2]) $arResult[ANSWER]=GetMessage("CHPASS_PASS_NOT_MATCH");
            else{
                if(!$last_pass_match) $arResult[ANSWER]=GetMessage("CHPASS_LAST_PASS_NOT_MATCH");
                else{
                    $USER->Update($arResult[CURRENT_USER][ID], array(
                        "PASSWORD"=>$_POST[new_pass],
                        "CONFIRM_PASSWORD"=>$_POST[new_pass]
                    ));
                    $arResult[ANSWER]=GetMessage("CHPASS_CHANGE_SUCCESS");
                }  
            }  
        }
    }else $arResult[ANSWER]=GetMessage("CHPASS_TIME_SESSION");
}
$this->includeComponentTemplate();
?>