<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Web\Json as JS;

$arResult = array('output' => array());

if(isset($arParams["FUNC_NAME"]) && strlen($arParams["FUNC_NAME"]) > 0)
{
    switch($arParams["FUNC_NAME"])
    {
        case "login":
            $arResult['output'] = array("status" => 'ok');
            $error = false;
            if(isset($arParams["LOGIN"]) && strlen($arParams["LOGIN"]) > 0 && isset($arParams["PASSWORD"]) && strlen($arParams["PASSWORD"]) > 0)
            {
                $remember = isset($arParams["REMEMBER"]) && $arParams["REMEMBER"] == 'Y' ? 'Y' : 'N';
                $res = $USER->Login(
                        $arParams["LOGIN"],
                        $arParams["PASSWORD"],
                        $remember
                    );
                if($res === true)
                {
                    $arResult['output'] = array("status" => 'ok');
                }
                else{
                    $arResult['output'] = array("status" => 'error', 'message' => GetMessage("INVALID_LOGIN_PASS"));
                }
            }
            break;
        case "remember_pass":
            if(isset($arParams["EMAIL"]) && strlen($arParams["EMAIL"]) > 0)
            {
                $query = CUser::GetList(($by="ID"), ($order="desc"), array("EMAIL"=>$arParams["EMAIL"]));

                if($user = $query->fetch())
                {
                    CUser::SendPassword(
                        $user['LOGIN'],
                        $user['EMAIL']
                    );
                    $arResult['output'] = array("status" => 'ok');
                }
                else
                {
                    $arResult['output'] = array("status" => 'error', 'message' => GetMessage("NO_USER"));
                }
            }
            else
            {
                $arResult['output'] = array("status" => 'error', 'message' => GetMessage("NO_EMAIL"));
            }
            break;
    }
}
echo JS::encode($arResult['output']);
$this->IncludeComponentTemplate();
?>