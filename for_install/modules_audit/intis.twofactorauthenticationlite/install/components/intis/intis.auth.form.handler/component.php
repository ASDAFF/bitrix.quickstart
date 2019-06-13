<?php

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')))
{
    CModule::IncludeModule("intis.twofactorauthenticationlite");

    $class = new CIntisTwoFactorAuthentificationLite();

    if (isset($_POST['REQUESTLOGIN']) && isset($_POST['REQUESTPASS']))
    {
        $rsUser = CUser::GetByLogin(htmlspecialchars($_POST['REQUESTLOGIN']));
        if ($arUser = $rsUser->Fetch())
        {
            if(strlen($arUser["PASSWORD"]) > 32)
            {
                $salt = substr($arUser["PASSWORD"], 0, strlen($arUser["PASSWORD"]) - 32);
                $db_password = substr($arUser["PASSWORD"], -32);
            }
            else
            {
                $salt = "";
                $db_password = $arUser["PASSWORD"];
            }

            $user_password =  md5($salt.$_POST['REQUESTPASS']);

            if ( $user_password == $db_password )
            {
                $user = new CUser;

                $arGroupAvalaible = explode(",", substr($class->GetGroup(), 0, strlen($class->GetGroup())-0));
                $arGroups = CUser::GetUserGroup(1);
                $result_intersect = array_intersect($arGroupAvalaible, $arGroups);

                if(!empty($result_intersect))
                {
                    $oneTimePassword = $class->GenPass($class->GetPassTemplateSymbol());

                    if ($class->BindingIpCheck()=="on")
                    {
                        $userIP = $_SERVER['REMOTE_ADDR'];
                    }else{
                        $userIP = "";
                    }

                    $oneTimePassField = Array(
                        "UF_INTIS_ONETIMEPASS" => md5($userIP.$oneTimePassword),
                    );
                    $user->Update($arUser['ID'], $oneTimePassField);

                    $showPhone = $class->ValidatePhone($arUser[$class->GetUserPhoneField()]);

                    $class->Send(GetMessage("TWOFACTORAUTHENTIFICATION_YOUR_PASS"), $showPhone, $class->GetTokenField(), $oneTimePassword);

                    $arResult['ONE_TIME_PASS_FORM'] = true;

                    $arResult['PHONE'] = substr_replace($showPhone, "*****", 3).substr($showPhone, 8);
                }else{
                    $class->DeleteElement($_SERVER['REMOTE_ADDR']."---".$arUser['LOGIN'], $class->CreateIblockId());
                    $class->DeleteElementIfNonSecurity($_SERVER['REMOTE_ADDR']."***".$arUser['LOGIN'], $class->CreateIblockId());

                    $user->Authorize($arUser['ID']);
                    echo GetMessage("TWOFACTORAUTHENTIFICATION_HI").", ".$arUser['LOGIN']."<br />";
                    echo '<a href="javascript:window.location.reload()">'.GetMessage("TWOFACTORAUTHENTIFICATION_NEXT").'</a>';
                }
            }else{
                if($class->GetAdminPhone()==true)
                {
                    $adminAlert = "Y";
                    if ($class->IpBlockCheck()!=="on")
                    {
                        $class->__GetElementIfNonSecurity($_SERVER['REMOTE_ADDR'], $arUser['LOGIN'], $class->CreateIblockId());
                    }
                }

                if ($class->IpBlockCheck()=="on")
                {
                    if ($class->__GetElement($_SERVER['REMOTE_ADDR'], $arUser['LOGIN'], $class->CreateIblockId(), $adminAlert)!=="HIDE_FORM")
                    {
                        $arResult['LAST_LOGIN'] = htmlspecialchars($_POST['REQUESTLOGIN']);
                        $arResult['WRONG_PASS'] = true;
                        $arResult['SHOW_ALERT'] = true;
                    }
                }else{
                    $arResult['LAST_LOGIN'] = htmlspecialchars($_POST['REQUESTLOGIN']);
                    $arResult['WRONG_PASS'] = true;
                    $arResult['SHOW_ALERT'] = false;
                }
            }
            $arResult['WRONG_LOGIN'] = false;
        }else{
            $arResult['WRONG_LOGIN'] = true;

            if($class->GetAdminPhone()==true)
            {
                $adminAlert = "Y";
                if ($class->IpBlockCheck()!=="on")
                {
                    $class->__GetElementIfNonSecurity($_SERVER['REMOTE_ADDR'], htmlspecialchars($_POST['REQUESTLOGIN']), $class->CreateIblockId());
                }
            }

            if ($class->IpBlockCheck()=="on")
            {
                if ($class->__GetElement($_SERVER['REMOTE_ADDR'], htmlspecialchars($_POST['REQUESTLOGIN']), $class->CreateIblockId(), $adminAlert)!=="HIDE_FORM")
                {
                    $arResult['SHOW_ALERT'] = true;
                }
            }else{
                $arResult['SHOW_ALERT'] = false;
            }
        }
    }

    if (isset($_POST['REQUESTONETIME']))
    {
        if ($class->BindingIpCheck()=="on")
        {
            $userIP = $_SERVER['REMOTE_ADDR'];
        }else{
            $userIP = "";
        }

        $filterGroup = Array
        (
            "ACTIVE" => "Y"
        );
        $arParams["SELECT"] = array("UF_INTIS_ONETIMEPASS");

        $rsUsers = CUser::GetList($by, $order, $filterGroup, $arParams);

        while ($arUSR = $rsUsers->Fetch())
        {
            if ($arUSR['UF_INTIS_ONETIMEPASS']==md5($userIP.$_POST['REQUESTONETIME']))
            {
                $user = new CUser;
                $oneTimePassField = Array(
                    "UF_INTIS_ONETIMEPASS" => "",
                );
                $user->Update($arUSR['ID'], $oneTimePassField);
                $user->Authorize($arUSR['ID']);

                $class->DeleteElement($_SERVER['REMOTE_ADDR']."---".$arUSR['LOGIN'], $class->CreateIblockId());
                $class->DeleteElementIfNonSecurity($_SERVER['REMOTE_ADDR']."***".$arUSR['LOGIN'], $class->CreateIblockId());

                $arResult['ONE_TIME_PASS_DONE'] = true;
                $arResult['USER_LOGIN'] = $arUSR['LOGIN'];
                break;
            }
        }
    }
}

$this->IncludeComponentTemplate();
