<?
use \Bitrix\Main\Localization\Loc;
// подключим все необходимые файлы:
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php"); // первый общий пролог
// подключим языковой файл
\Bitrix\Main\Loader::includeModule("rinsvent.fastauth");
Loc::loadLanguageFile(__FILE__);
global $APPLICATION;

$arAnswer = array();

$isUseFa = $APPLICATION->get_cookie("UF_RINSVENT_FA_USE",false);
if(!$isUseFa){
    $arAnswer["STATUS"] = "DONTUSE";
}

if($_REQUEST["TYPE_AJAX"] == "UPDATEPASSWORD"){
    if($USER->IsAuthorized()){
        $userData = \CUser::GetByID($USER->GetID())->Fetch();
        if($userData["UF_RINSVENT_FA_USE"] == "1"){
            $salt = substr($userData['UF_RINSVENT_FA'], 0, (strlen($userData['UF_RINSVENT_FA']) - 32));
            $realPassword = substr($userData['UF_RINSVENT_FA'], -32);
            $password = json_encode($_REQUEST["POINTS"]);
            $_SESSION["UF_RINSVENT_FA"] = $password;

            $arAnswer["STATUS"] = "REPEATPASSWORD";
        }else{
            $arAnswer["STATUS"] = "DONTUSE";
        }
    }else{
        $arAnswer["STATUS"] = "DONTAUTH";
    }
}

if($_REQUEST["TYPE_AJAX"] == "REPEATPASSWORD"){
    if($USER->IsAuthorized()){
        $userData = \CUser::GetByID($USER->GetID())->Fetch();
        if($userData["UF_RINSVENT_FA_USE"] == "1"){
            $salt = substr($userData['UF_RINSVENT_FA'], 0, (strlen($userData['UF_RINSVENT_FA']) - 32));
            $realPassword = substr($userData['UF_RINSVENT_FA'], -32);
            $password = json_encode($_REQUEST["POINTS"]);

            if($_SESSION["UF_RINSVENT_FA"] == $password){
                $password = md5($salt.$password);
                $user = new CUser;
                $fields = Array(
                    "UF_RINSVENT_FA" => $password,
                );
                $user->Update($USER->GetID(), $fields);
                $APPLICATION->set_cookie("UF_RINSVENT_FA_USER",$USER->GetID());
                $APPLICATION->set_cookie("UF_RINSVENT_FA_USE","Y");
                unset($_SESSION["UF_RINSVENT_FA"]);

                $arAnswer["STATUS"] = "SUCCESSPASSWORD";
            }else{
                $arAnswer["STATUS"] = "DONTREPEATSUCCESSPASSWORD";
            }
        }else{
            $arAnswer["STATUS"] = "DONTUSE";
        }
    }else{
        $arAnswer["STATUS"] = "DONTAUTH";
    }
}
if($_REQUEST["TYPE_AJAX"] == "CHECK"){
    if(!$USER->IsAuthorized()){
        $userId = $APPLICATION->get_cookie("UF_RINSVENT_FA_USER",false);
        if($userId){
            $userData = \CUser::GetByID($userId)->Fetch();
            if($userData["UF_RINSVENT_FA_USE"] == "1"){
                $salt = substr($userData['UF_RINSVENT_FA'], 0, (strlen($userData['UF_RINSVENT_FA']) - 32));
                $realPassword = substr($userData['UF_RINSVENT_FA'], -32);
                $password = json_encode($_REQUEST["POINTS"]);
                $password = md5($salt.$password);

                if($userData['UF_RINSVENT_FA'] == $password){
                    $arAnswer["STATUS"] = "SUCCESSAUTH";
                    $USER->Authorize($userId);
                }else{
                    $arAnswer["STATUS"] = "FAILEDAUTH";
                }
            }else{
                $arAnswer["STATUS"] = "DONTUSE";
            }
        }else{
            $arAnswer["STATUS"] = "DONTHAVECOOKIE";
        }
    }else{
        $arAnswer["STATUS"] = "AUTHYET";
    }
}

$APPLICATION->RestartBuffer();
echo json_encode($arAnswer);
die();