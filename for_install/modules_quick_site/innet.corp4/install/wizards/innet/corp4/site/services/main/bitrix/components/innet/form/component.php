<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arResult["PARAMS_HASH"] = md5(serialize($arParams) . $this->GetTemplateName());
$arParams["USE_CAPTCHA"] = (($arParams["USE_CAPTCHA"] != "N" && !$USER->IsAuthorized()) ? "Y" : "N");

if ($arParams["OK_MESSAGE"] == '')
    $arParams["OK_MESSAGE"] = GetMessage("INNET_FORM_OK_MESSAGE");

if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["submit"] <> '' && (!isset($_POST["PARAMS_HASH"]) || $arResult["PARAMS_HASH"] === $_POST["PARAMS_HASH"])) {
    if ($arParams["EMAIL_TO"] == '')
        $arParams["EMAIL_TO"] = COption::GetOptionString("main", "email_from");

    $user_name = $_POST["user_name"];
    $user_email = $_POST["user_email"];
    $user_phone = $_POST["user_phone"];
    $user_comment = $_POST["comment"];
    $location = $_POST['location'];

    $arResult["ERROR_MESSAGE"] = array();

    if (check_bitrix_sessid()) {

        if (empty($arParams["REQUIRED_FIELDS"]) || !in_array("NONE", $arParams["REQUIRED_FIELDS"])) {
            if ((empty($arParams["REQUIRED_FIELDS"]) || in_array("NAME", $arParams["REQUIRED_FIELDS"])) && strlen($user_name) <= 1)
                $arResult["ERROR_MESSAGE"][] = GetMessage("INNET_FORM_NAME");
            if ((empty($arParams["REQUIRED_FIELDS"]) || in_array("EMAIL", $arParams["REQUIRED_FIELDS"])) && strlen($user_email) <= 1)
                $arResult["ERROR_MESSAGE"][] = GetMessage("INNET_FORM_EMAIL");
            if ((empty($arParams["REQUIRED_FIELDS"]) || in_array("PHONE", $arParams["REQUIRED_FIELDS"])) && strlen($user_phone) <= 1)
                $arResult["ERROR_MESSAGE"][] = GetMessage("INNET_FORM_PHONE");
            if ((empty($arParams["REQUIRED_FIELDS"]) || in_array("MESSAGE", $arParams["REQUIRED_FIELDS"])) && strlen($user_comment) <= 1)
                $arResult["ERROR_MESSAGE"][] = GetMessage("INNET_FORM_MESSAGE");
        }

        if (strlen($user_email) > 1 && !check_email($user_email))
            $arResult["ERROR_MESSAGE"][] = GetMessage("INNET_FORM_EMAIL_NOT_VALID");

        if ($arParams["USE_CAPTCHA"] == "Y") {
            include_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/classes/general/captcha.php");
            $captcha_code = $_POST["captcha_sid"];
            $captcha_word = $_POST["captcha_word"];
            $cpt = new CCaptcha();
            $captchaPass = COption::GetOptionString("main", "captcha_password", "");
            if (strlen($captcha_word) > 0 && strlen($captcha_code) > 0) {
                if (!$cpt->CheckCodeCrypt($captcha_word, $captcha_code, $captchaPass))
                    $arResult["ERROR_MESSAGE"][] = GetMessage("INNET_FORM_CAPTCHA_WRONG");
            } else {
                $arResult["ERROR_MESSAGE"][] = GetMessage("INNET_FORM_CAPTHCA_EMPTY");
            }
        }

        if (empty($arResult["ERROR_MESSAGE"])) {
            if (!empty($arParams['INNET_IBLOCK_ID_RECORD'])) {
                if (CModule::IncludeModule("iblock")) {
                    $arFieldsAdd = array(
                        "ACTIVE" => "N",
                        "IBLOCK_ID" => $arParams['INNET_IBLOCK_ID_RECORD'],
                        "DATE_ACTIVE_FROM" => ConvertTimeStamp(time(), 'FULL'),
                        "NAME" => ConvertTimeStamp(time(), 'FULL') . (($arParams['INNET_NAME_ELEMENT']) ? ' - ' . $arParams['INNET_NAME_ELEMENT'] : ''),
                        "CODE" => $user_name . '_' . time(),
                        "PROPERTY_VALUES" => array(
                            "NAME_CLIENT" => $user_name,
                            "EMAIL_CLIENT" => $user_email,
                            "PHONE_CLIENT" => $user_phone,
                            "COMMENT" => array("VALUE" => array("TEXT" => $user_comment, "TYPE" => "text")),
                            "ID_ELEMENT" => $arParams['INNET_ID_ELEMENT'],
                            "NAME_ELEMENT" => $arParams['INNET_NAME_ELEMENT'],
                            "LINK_ELEMENT" => $location,
                            "PRICE_ELEMENT" => $arParams['INNET_PRICE_ELEMENT'],
                        )
                    );

                    $el = new CIBlockElement();
                    if (!$el->Add($arFieldsAdd)) {
                        $arResult["ERROR_MESSAGE"][] = GetMessage("INNET_FORM_ERROR_ADD_IBLOCK");
                    }
                }
            }


            $arFields = Array(
                "NAME_CLIENT" => $user_name,
                "EMAIL_CLIENT" => $user_email,
                "PHONE_CLIENT" => $user_phone,
                "COMMENT" => $user_comment,
                "EMAIL_TO" => $arParams["EMAIL_TO"],
                "ID_ELEMENT" => $arParams['INNET_ID_ELEMENT'],
                "NAME_ELEMENT" => $arParams['INNET_NAME_ELEMENT'],
                "PRICE_ELEMENT" => $arParams['INNET_PRICE_ELEMENT'],
                "LINK_ELEMENT" => $location,
            );

            $arFieldsUser = Array(
                "NAME_CLIENT" => $user_name,
                "EMAIL_CLIENT" => $user_email,
                "PHONE_CLIENT" => $user_phone,
                "COMMENT" => $user_comment,
                "EMAIL_TO" => $user_email,
                "ID_ELEMENT" => $arParams['INNET_ID_ELEMENT'],
                "NAME_ELEMENT" => $arParams['INNET_NAME_ELEMENT'],
                "PRICE_ELEMENT" => $arParams['INNET_PRICE_ELEMENT'],
                "LINK_ELEMENT" => $location,
            );

            if (!empty($arParams["EVENT_MESSAGE_ID"])) {
                foreach ($arParams["EVENT_MESSAGE_ID"] as $v)
                    if (IntVal($v) > 0)
                        CEvent::Send($arParams["EVENT_MESSAGE_TYPE"], SITE_ID, $arFields, "N", IntVal($v));//mail to admin
            } else {
                CEvent::Send($arParams["EVENT_MESSAGE_TYPE"], SITE_ID, $arFields);//mail to admin
            }

            CEvent::Send($arParams["EVENT_MESSAGE_TYPE_USER"], SITE_ID, $arFieldsUser);//mail to user


            $_SESSION["INNET_FORM_NAME"] = htmlspecialcharsbx($user_name);
            $_SESSION["INNET_FORM_EMAIL"] = htmlspecialcharsbx($user_email);
            $_SESSION["INNET_FORM_PHONE"] = htmlspecialcharsbx($user_phone);

            LocalRedirect($APPLICATION->GetCurPageParam("success=" . $arResult["PARAMS_HASH"], Array("success")));
        }

        $arResult["AUTHOR_NAME"] = $user_name;
        $arResult["AUTHOR_EMAIL"] = $user_email;
        $arResult["AUTHOR_PHONE"] = $user_phone;
        $arResult["AUTHOR_MESSAGE"] = $user_comment;

    } else {
        $arResult["ERROR_MESSAGE"][] = GetMessage("FORM_SESS_EXP");
    }

} elseif ($_REQUEST["success"] == $arResult["PARAMS_HASH"]) {
    $arResult["OK_MESSAGE"] = $arParams["OK_MESSAGE"];
}

if (empty($arResult["ERROR_MESSAGE"])) {
    if ($USER->IsAuthorized()) {
        $arResult["AUTHOR_NAME"] = $USER->GetFormattedName(false);
        $arResult["AUTHOR_EMAIL"] = htmlspecialcharsbx($USER->GetEmail());
        $arResult["AUTHOR_PHONE"] = htmlspecialcharsbx($_SESSION["INNET_FORM_PHONE"]);
    } else {
        if (strlen($_SESSION["INNET_FORM_NAME"]) > 0)
            $arResult["AUTHOR_NAME"] = htmlspecialcharsbx($_SESSION["INNET_FORM_NAME"]);
        if (strlen($_SESSION["INNET_FORM_EMAIL"]) > 0)
            $arResult["AUTHOR_EMAIL"] = htmlspecialcharsbx($_SESSION["INNET_FORM_EMAIL"]);
        if (strlen($_SESSION["INNET_FORM_PHONE"]) > 0)
            $arResult["AUTHOR_PHONE"] = htmlspecialcharsbx($_SESSION["INNET_FORM_PHONE"]);
    }
}

if ($arParams["USE_CAPTCHA"] == "Y")
    $arResult["capCode"] = htmlspecialcharsbx($APPLICATION->CaptchaGetCode());

$this->IncludeComponentTemplate();
