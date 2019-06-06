<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();
?>
<?

if (!CModule::IncludeModule("wl.form") || !CModule::IncludeModule("iblock"))
    return false;

use Bitrix\Main\Web\Json;

global $APPLICATION;

$arParams["ADD_JQUERY"] = (!empty($arParams["ADD_JQUERY"])) ? $arParams["ADD_JQUERY"] : "N";
$arParams["ADD_BOOTSTRAP"] = (!empty($arParams["ADD_BOOTSTRAP"])) ? $arParams["ADD_BOOTSTRAP"] : "N";
$arParams["ADD_MASKEDINPUT"] = (!empty($arParams["ADD_MASKEDINPUT"])) ? $arParams["ADD_MASKEDINPUT"] : "N";

if ($arParams["ADD_JQUERY"] == "Y")
    $APPLICATION->AddHeadScript($componentPath . '/assets/js/jquery.min.js');

if ($arParams["ADD_MASKEDINPUT"] == "Y")
    $APPLICATION->AddHeadScript($componentPath . '/assets/js/jquery.maskedinput.js');

if ($arParams["ADD_BOOTSTRAP"] == "Y") {
    $APPLICATION->AddHeadScript($componentPath . '/assets/bootstrap/js/bootstrap.min.js');
    $APPLICATION->SetAdditionalCSS($componentPath . '/assets/bootstrap/css/bootstrap.min.css');
}

$APPLICATION->AddHeadScript($componentPath . '/assets/js/script.js');

if (!empty($_REQUEST["FORM_" . $arParams["ID_FORM"]])) {

    // check before events
    foreach (GetModuleEvents('wl.form', 'OnBeforeProcessing', true) as $arEvent)
        if (ExecuteModuleEventEx($arEvent, array(&$_REQUEST, &$arParams, &$arResult)) === false)
            $arErrors["ERRORS"][] = Array(
                "MESSAGE" => $arResult["BEFORE_ERROR_MESSAGE"],
                "NAME" => $arResult["BEFORE_ERROR_NAME"]
            );

    if (!defined('PUBLIC_AJAX_MODE')) {
        define('PUBLIC_AJAX_MODE', true);
    }

    if (!check_bitrix_sessid())
        $arErrors["ERRORS"][] = Array(
            "MESSAGE" => GetMessage("WL_FORM_BITRIX_SESSION_ID"),
            "NAME" => "BITRIX_SESSION_ID"
        );

    $arRules = Array();
    for ($i = 1; $i <= $arParams["CTN_FIELDS"]; $i++) {
        if ($arParams["FIELD_REQUIRED_" . $i] == "Y") {
            $arRules["RULES"][$arParams["FIELD_CODE_" . $i]] = Array(
                "required" => true,
            );
            if (!empty($arParams["FIELD_ERROR_" . $i])) {
                $arRules["MESSAGES"][$arParams["FIELD_CODE_" . $i]] = Array(
                    "required" => $arParams["FIELD_ERROR_" . $i],
                );
            } else {
                $arRules["MESSAGES"][$arParams["FIELD_CODE_" . $i]] = Array(
                    "required" => GetMessage("WL_FORM_ERROR_FIELD") . ' "' . $arParams["FIELD_NAME_" . $i] . '".',
                );
            }
        }
    }

    if (empty($arErrors) && !empty($arRules))
        $arErrors = \WL\Form\Main::Validation($_REQUEST, $arRules, Array("ONE_ERROR" => true));

    if (!is_array($arErrors)) {
        unset($arErrors);

        $el = new CIBlockElement;

        $arFields = Array(
            "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
            "IBLOCK_ID" => $arParams["IBLOCK_ID"],
            "IBLOCK_SECTION_ID" => (!empty($arParams["IBLOCK_SECTION"])) ? $arParams["IBLOCK_SECTION"] : false,
        );

        // поля инфоблока
        $arFieldsIblock = Array(
            "NAME" => GetMessage("FIELED_SAVE_NAME"),
            "PREVIEW_TEXT" => GetMessage("FIELED_SAVE_PREVIEW_TEXT"),
        );

        // св-ва инфоблока
        if ($arParams["IBLOCK_ID"] > 0) {
            $rsProps = CIBlockProperty::GetList(Array("SORT" => "DESC"), Array("IBLOCK_ID" => $arParams["IBLOCK_ID"]));
            while ($arProp = $rsProps->fetch())
                $arProps["PROPERTY_" . $arProp["CODE"]] = $arProp;
        }

        $arCRM = Array();
        $detailText = '';
        $dtText = '';
        for ($i = 1; $i <= $arParams["CTN_FIELDS"]; $i++) {

            // CRM
            if ($arParams["ENABLE_CRM"] == "Y" && !empty($arParams["URL_CRM"])) {
                if ($arParams["FIELD_CRM_" . $i] == "PHONE")
                    $arCRM[$arParams["FIELD_CRM_" . $i]] = Array(Array("VALUE" => preg_replace('~\D+~', '', $_REQUEST[$arParams["FIELD_CODE_" . $i]]), "VALUE_TYPE" => "WORK"));
                else
                    $arCRM[$arParams["FIELD_CRM_" . $i]] = $_REQUEST[$arParams["FIELD_CODE_" . $i]];
            }

            if ($arParams["FIELD_SAVE_" . $i] == "DETAIL_TEXT") {
                $detailText .= $arParams["FIELD_NAME_" . $i] . ": " . $APPLICATION->ConvertCharset($_REQUEST[$arParams["FIELD_CODE_" . $i]], "UTF-8", SITE_CHARSET) . PHP_EOL;
                $dtText = $APPLICATION->ConvertCharset($_REQUEST[$arParams["FIELD_CODE_" . $i]], "UTF-8", SITE_CHARSET);
                continue;
            }

            // если это св-во
            if (array_key_exists($arParams["FIELD_SAVE_" . $i], $arProps)) {
                if ($arProps[$arParams["FIELD_SAVE_" . $i]]["USER_TYPE"] == "HTML")
                    $arFields["PROPERTY_VALUES"][$arProps[$arParams["FIELD_SAVE_" . $i]]["CODE"]] = Array("VALUE" => Array("TEXT" => $APPLICATION->ConvertCharset($_REQUEST[$arParams["FIELD_CODE_" . $i]], "UTF-8", SITE_CHARSET), "TYPE" => "text"));
                else if ($arProps[$arParams["FIELD_SAVE_" . $i]]["PROPERTY_TYPE"] == "E") {
                    $arFields["PROPERTY_VALUES"][$arProps[$arParams["FIELD_SAVE_" . $i]]["CODE"]] = $_REQUEST[$arParams["FIELD_CODE_" . $i]];
                    $arTmp = CIBlockElement::GetByID($_REQUEST[$arParams["FIELD_CODE_" . $i]])->fetch();
                    if ($arTmp) {
                        $detailText .= $arProps[$arParams["FIELD_SAVE_" . $i]]["NAME"] . ": " . $arTmp["NAME"] . PHP_EOL;
                        continue;
                    }
                } else {
                    $arFields["PROPERTY_VALUES"][$arProps[$arParams["FIELD_SAVE_" . $i]]["CODE"]] = $APPLICATION->ConvertCharset($_REQUEST[$arParams["FIELD_CODE_" . $i]], "UTF-8", SITE_CHARSET);
                }

                $detailText .= $arProps[$arParams["FIELD_SAVE_" . $i]]["NAME"] . ": " . $APPLICATION->ConvertCharset($_REQUEST[$arParams["FIELD_CODE_" . $i]], "UTF-8", SITE_CHARSET) . PHP_EOL;
            } else {
                // если это поле
                $arFields[$arParams["FIELD_SAVE_" . $i]] = $APPLICATION->ConvertCharset($_REQUEST[$arParams["FIELD_CODE_" . $i]], "UTF-8", SITE_CHARSET);
                $detailText .= $arFieldsIblock[$arParams["FIELD_SAVE_" . $i]] . ": " . $APPLICATION->ConvertCharset($_REQUEST[$arParams["FIELD_CODE_" . $i]], "UTF-8", SITE_CHARSET) . PHP_EOL;
            }
        }

        if (empty($arFields["NAME"]))
            $arFields["NAME"] = "Unknown";

        if ($arParams["REWRITE_DETAIL_TEXT"] == "Y")
            $arFields["DETAIL_TEXT"] = $dtText;
        else
            $arFields["DETAIL_TEXT"] = $detailText;

        if ($idElem = $el->Add($arFields)) {

            // send admin notification
            if ($arParams["ADMIN_NOTIFICATION"] == "Y") {
                $arSite = CSite::GetList($by = "sort", $order = "desc", Array("ID" => SITE_ID))->fetch();
                if (!empty($arSite["EMAIL"]))
                    CEvent::Send("WL_FORM_ADMIN_NOTIFICATION", SITE_ID, Array("TEXT" => $detailText));
            }

            // send sms
            if ($arParams["SMS_ENABLE"] == "Y" && COption::GetOptionString("wl.form", "SMS_ENABLE") == "Y") {
                $SMSC_LOGIN = COption::GetOptionString("wl.form", "SMSC_LOGIN", "");
                $SMSC_PASSWORD = COption::GetOptionString("wl.form", "SMSC_PASSWORD", "");
                $SMSC_CHARSET = COption::GetOptionString("wl.form", "SMSC_CHARSET", "windows-1251");
                $SMSC_PHONE = !empty($arParams["SMSC_PHONE"]) ? $arParams["SMSC_PHONE"] : COption::GetOptionString("wl.form", "SMSC_PHONE", "");

                if (!empty($SMSC_LOGIN) && !empty($SMSC_PASSWORD) && !empty($SMSC_CHARSET) && !empty($SMSC_PHONE) && !empty($arParams["SMSC_TEMPLATE"])) {
                    for ($i = 1; $i <= $arParams["CTN_FIELDS"]; $i++) {
                        $arParams["SMSC_TEMPLATE"] = str_replace('%' . $arParams["FIELD_CODE_" . $i] . '%', $_REQUEST[$arParams["FIELD_CODE_" . $i]], $arParams["SMSC_TEMPLATE"]);
                    }
                }

                $ob = new \WL\Form\WlSmsc($SMSC_LOGIN, $SMSC_PASSWORD, $SMSC_CHARSET);
                $ob->send_sms($SMSC_PHONE, $arParams["SMSC_TEMPLATE"]);
            }

            // CallBack
            if ($arParams["CALLBACK_ENABLE"] == "Y" && COption::GetOptionString("wl.form", "CALLBACK_ENABLE") == "Y") {
                $CALLBACK_KEY = COption::GetOptionString("wl.form", "CALLBACK_KEY", "");
                $CALLBACK_SECRET = COption::GetOptionString("wl.form", "CALLBACK_SECRET", "");
                $CALLBACK_PHONE = !empty($arParams['CALLBACK_PHONE']) ? $arParams['CALLBACK_PHONE'] : COption::GetOptionString("wl.form", "CALLBACK_PHONE", "");
                if (!empty($arParams['CALLBACK_INPUT_ID']))
                    $phone = \WL\Form\Main::PhoneTemplate($_REQUEST[$arParams["FIELD_CODE_" . $arParams['CALLBACK_INPUT_ID']]]);

                if (!empty($CALLBACK_KEY) && !empty($CALLBACK_SECRET) && !empty($CALLBACK_PHONE) && !empty($phone)) {
                    $path = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/wl.form/lib/zadarma.php';
                    if (!file_exists($path))
                        $path = $_SERVER['DOCUMENT_ROOT'] . '/local/modules/wl.form/lib/zadarma.php';

                    require_once $path;

                    $zd = new \Zadarma_API\Client($CALLBACK_KEY, $CALLBACK_SECRET);

                    $arCallBackParams = [
                        'from' => $CALLBACK_PHONE, // номер менеджера
                        'to' => $phone, // номер абонента
                    ];

                    $answer = $zd->call('/v1/request/callback/', $arCallBackParams);
                }
            }

            // send CRM
            if (!empty($arCRM)) {
                $arCRM["TITLE"] = "Test";
                $arParamsCRM = Array(
                    "URL" => $arParams["URL_CRM"],
                    "METHOD" => "POST",
                    "PARAMS" => Array(
                        "fields" => $arCRM,
                        "params" => Array("REGISTER_SONET_EVENT" => "Y"),
                    ),
                );

                $lead = \WL\Form\Main::DoCurl($arParamsCRM);
            }

            $arResult = Array(
                "SUCCESS" => 1,
                "REQUEST" => $_REQUEST,
            );
        } else
            $arErrors["ERRORS"][] = Array(
                "MESSAGE" => $el->LAST_ERROR,
                "NAME" => "BITRIX_SAVE_ID"
            );
    }

    if (is_array($arErrors))
        $arResult = $arErrors;

    // check after events
    foreach (GetModuleEvents('wl.form', 'OnAfterProcessing', true) as $arEvent)
        ExecuteModuleEventEx($arEvent, array(&$_REQUEST, &$arParams, &$arResult));

    $APPLICATION->RestartBuffer();
    die(Json::encode($arResult));
}

$this->IncludeComponentTemplate();
?>