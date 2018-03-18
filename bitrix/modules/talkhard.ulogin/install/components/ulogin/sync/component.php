<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arResult = $arParams;

global $USER;
global $APPLICATION;

if (!empty($_POST['token']) && $USER->isAuthorized()) {
    $UserID = $USER->GetID();
    $current_user = $USER->GetByID($UserID)->GetNext();
    $networks = explode(',', $current_user['ADMIN_NOTES']);

    $s = file_get_contents('http://ulogin.ru/token.php?token=' . $_POST['token'] . '&host=' . $_SERVER['HTTP_HOST']);
    $profile = json_decode($s, true);

    if (count($profile) && !isset($profile['error'])){

        list($d, $m, $y) = explode('.', $profile['bdate']);

        $arResult['USER']['LOGIN'] = Ulogin::genNickname($profile);
        $arResult['USER']['NAME'] = $profile['first_name'];
        $arResult['USER']['LAST_NAME'] = $profile['last_name'];
        $arResult['USER']['EMAIL'] = $profile['email'];
        $arResult['USER']['PERSONAL_GENDER'] = ($profile['sex'] == 2 ? 'M' : 'F');
        $arResult['USER']['PERSONAL_CITY'] = $profile['city'];
        $arResult['USER']['PERSONAL_BIRTHDAY'] = $d . '.' . $m . '.' . $y;
        $arResult['USER']['EXTERNAL_AUTH_ID'] = $profile['identity'];
        $arResult['USER']['PHOTO'] = $profile['photo'];
        $arResult['USER']['PHOTO_BIG'] = $profile['photo_big'];
        $arResult['USER']['NETWORK'] = $profile['network'];

        // провер€ем есть ли пользователь в Ѕƒ.	≈сли есть - то авторизуем, нет  - регистрируем и авторизуем
        $rsUsers = CUser::GetList(
            ($by = "email"),
            ($order = "desc"),
            array(
                "EXTERNAL_AUTH_ID" => $arResult['USER']["EXTERNAL_AUTH_ID"],
            )
        );
        $arUser = $rsUsers->GetNext();


        if ($arUser["EXTERNAL_AUTH_ID"] == $arResult['USER']["EXTERNAL_AUTH_ID"]) {

            // такой пользователь есть

            $ID_INFO   = explode('=',$arUser['ADMIN_NOTES']);
            $UloginID = $arUser['ID'];

            if ($arResult['USER']['NETWORK'] == $ID_INFO[0] && $arUser['ACTIVE'] == 'Y'){//старый формат хранени€ аккаунтов, конвертируем
                $USER->Update($arUser['ID'], array('EXTERNAL_AUTH_ID'=>''));
                $ID_INFO[1] = $arUser['ID'];
                $UloginID = Ulogin::createUloginAccount($arResult['USER'], $arUser['ID']);
            }

            if ($UserID != $ID_INFO[1] && !in_array($ID_INFO[0], $networks)) { //не текущий аккаунт

                $networks = implode(',',$networks).','.$ID_INFO[0];
                $second_user = CUser::GetByID($ID_INFO[1])->GetNext();
                $second_networks = explode(',',$second_user['ADMIN_NOTES']);
                unset($second_networks[array_search($ID_INFO[0],$second_networks)]);

                if (!count($second_networks)){
                    $USER->Delete($ID_INFO[1]);
                }else{
                    $USER->Update($ID_INFO[1],array('ADMIN_NOTES' => implode(',',$second_networks)));
                }

                Ulogin::updateUloginAccount($UloginID, $UserID, $arResult['USER']['NETWORK']);
                $USER->Update($UserID, array('ADMIN_NOTES'=>$networks));

            }

        }else{

            if (!in_array($arResult['USER']['NETWORK'], $networks)){ //добавл€ем, если такого сервиса нет
                Ulogin::createUloginAccount($arResult['USER'], $UserID);
                $networks = implode(',', $networks). ','.$arResult['USER']['NETWORK'];
                $USER->Update($UserID, array('ADMIN_NOTES' => $networks));
            }

        }
    }else{
        if (isset($profile['error']))
            ShowMessage(array("TYPE" => "ERROR", "MESSAGE" => $profile['error']));
    }

}


if (!isset($GLOBALS['ULOGIN_OK'])) {
    $GLOBALS['ULOGIN_OK'] = 1;
}
else
{
    $GLOBALS['ULOGIN_OK']++;
}

$code = '<div id="uLogin' . $GLOBALS['ULOGIN_OK'] . '" x-ulogin-params="display=' . $arParams['TYPE'] . '&fields=email' .
    '&providers=' . $arParams['PROVIDERS'] . '&hidden=' . $arParams['HIDDEN'] . '&redirect_uri=' . urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) . '"></div>';
$code = '<script src="http://ulogin.ru/js/ulogin.js"></script>' . $code;


$arResult['ULOGIN_CODE'] = $code;


$this->IncludeComponentTemplate();
?>