<?

namespace WL\Form;

class Main {

    // Base curl function
    public static function DoCurl($arParams) {

        $result = false;

        if (empty($arParams["URL"]))
            return $result;

        // set Method
        if (empty($arParams["METHOD"]))
            $arParams["METHOD"] = "GET";

        $ch = curl_init();

        if (!empty($arParams["PARAMS"])) {
            if (empty($arParams["METHOD"]) || $arParams["METHOD"] == "GET") {
                $arParams["URL"] .= strpos($arParams["URL"], "?") > 0 ? "&" : "?";
                $arParams["URL"] .= http_build_query($arParams["PARAMS"]);
            } else if ($arParams["METHOD"] == "POST") {
                $data = http_build_query($arParams["PARAMS"]);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            }
        }

        curl_setopt($ch, CURLOPT_URL, $arParams["URL"]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);

        if ($arParams["CURLOPT_HEADER"] == "Y")
            curl_setopt($ch, CURLOPT_HEADER, true);

        // save cookie on server
        if (!empty($arParams["CURLOPT_COOKIEJAR"])) {
            curl_setopt($ch, CURLOPT_COOKIEJAR, $arParams["CURLOPT_COOKIEJAR"]);
        }

        // set cookie from server
        if (!empty($arParams["CURLOPT_COOKIEFILE"])) {
            curl_setopt($ch, CURLOPT_COOKIEFILE, $arParams["CURLOPT_COOKIEFILE"]);
        }

        $result = curl_exec($ch);

        curl_close($ch);

        return $result;
    }

    public static function PhoneTemplate($phoneInput) {

        if (empty($phoneInput))
            return false;

        $phone = preg_replace('~\D+~', '', $phoneInput);
        if (strlen($phone) < 11)
            $phone = '7' . $phone;

        if ($phone[0] == '8')
            $phone[0] = '7';

        if (!empty($phone))
            return $phone;
        else
            return false;
    }

    public static function Validation($arFields, $arRules, $arParams = Array("ONE_ERROR" => false)) {

        global $APPLICATION;
        $arErrors = Array();

        foreach ($arRules["RULES"] as $key => $arRule) {
            foreach ($arRule as $subKey => $subRule) {
                $tmpError = false;

                switch ($subKey) {
                    case "required":
                        if (is_array($arFields[$key]) && $subRule) {
                            foreach ($arFields[$key] as $itemKey => $item)
                                if (empty($item)) {
                                    $arErrors["ERRORS"][] = Array(
                                        "NAME" => $key . '[' . $itemKey . ']',
                                        "MESSAGE" => !empty($arRules["MESSAGES"][$key][$subKey]) ? $arRules["MESSAGES"][$key][$subKey] : "Error " . $key
                                    );

                                    $tmpError = true;
                                }
                        } else if (empty($arFields[$key]) && $subRule) {
                            $arErrors["ERRORS"][] = Array(
                                "NAME" => $key,
                                "MESSAGE" => !empty($arRules["MESSAGES"][$key][$subKey]) ? $arRules["MESSAGES"][$key][$subKey] : "Error " . $key
                            );

                            $tmpError = true;
                        }
                        break;
                    case "email":
                        if (!empty($arFields[$key]) && is_array($arFields[$key])) {
                            foreach ($arFields[$key] as $itemKey => $item)
                                if (!filter_var($item, FILTER_VALIDATE_EMAIL) && !empty($item)) {
                                    $arErrors["ERRORS"][] = Array(
                                        "NAME" => $key . '[' . $itemKey . ']',
                                        "MESSAGE" => !empty($arRules["MESSAGES"][$key][$subKey]) ? $arRules["MESSAGES"][$key][$subKey] : "Error e-mail"
                                    );

                                    $tmpError = true;
                                }
                        } else if (!filter_var($arFields[$key], FILTER_VALIDATE_EMAIL) && !empty($arFields[$key])) {
                            $arErrors["ERRORS"][] = Array(
                                "NAME" => $key,
                                "MESSAGE" => !empty($arRules["MESSAGES"][$key][$subKey]) ? $arRules["MESSAGES"][$key][$subKey] : "Error e-mail"
                            );

                            $tmpError = true;
                        }
                        break;
                    case "url":
                        if (!filter_var($arFields[$key], FILTER_VALIDATE_URL) && !empty($arFields[$key])) {
                            $arErrors["ERRORS"][] = Array(
                                "NAME" => $key,
                                "MESSAGE" => !empty($arRules["MESSAGES"][$key][$subKey]) ? $arRules["MESSAGES"][$key][$subKey] : "Error url"
                            );

                            $tmpError = true;
                        }
                        break;
                    case "digits":
                        if (is_array($arFields[$key]) && $subRule) {
                            foreach ($arFields[$key] as $itemKey => $item)
                                if (!preg_match('/^[0-9]+$/i', $item) && !empty($item)) {
                                    $arErrors["ERRORS"][] = Array(
                                        "NAME" => $key . '[' . $itemKey . ']',
                                        "MESSAGE" => !empty($arRules["MESSAGES"][$key][$subKey]) ? $arRules["MESSAGES"][$key][$subKey] : "Error digits " . $key
                                    );

                                    $tmpError = true;
                                }
                        } else if (!preg_match('/^[0-9]+$/i', $arFields[$key]) && !empty($arFields[$key]) && $subRule) {
                            $arErrors["ERRORS"][] = Array(
                                "NAME" => $key,
                                "MESSAGE" => !empty($arRules["MESSAGES"][$key][$subKey]) ? $arRules["MESSAGES"][$key][$subKey] : "Error digits " . $key
                            );

                            $tmpError = true;
                        }
                        break;
                    case "max":
                        if (intval($arFields[$key]) > $subRule && !empty($arFields[$key])) {
                            $arErrors["ERRORS"][] = Array(
                                "NAME" => $key,
                                "MESSAGE" => !empty($arRules["MESSAGES"][$key][$subKey]) ? $arRules["MESSAGES"][$key][$subKey] : "Error max " . $key
                            );

                            $tmpError = true;
                        }
                        break;
                    case "min":
                        if (intval($arFields[$key]) < $subRule && !empty($arFields[$key])) {
                            $arErrors["ERRORS"][] = Array(
                                "NAME" => $key,
                                "MESSAGE" => !empty($arRules["MESSAGES"][$key][$subKey]) ? $arRules["MESSAGES"][$key][$subKey] : "Error max " . $key
                            );

                            $tmpError = true;
                        }
                        break;
                    case "rangelength":
                        if ($arParams["INPUT_UTF8"])
                            $tmpStr = $APPLICATION->ConvertCharset($arFields[$key], 'UTF-8', SITE_CHARSET);
                        else
                            $tmpStr = $arFields[$key];
                        if ((strlen($tmpStr) < $subRule[0] || strlen($tmpStr) > $subRule[1]) && !empty($tmpStr)) {
                            $arErrors["ERRORS"][] = Array(
                                "NAME" => $key,
                                "MESSAGE" => !empty($arRules["MESSAGES"][$key][$subKey]) ? $arRules["MESSAGES"][$key][$subKey] : "Error rangelength "
                            );

                            $tmpError = true;
                        }
                        break;
                    case "maxlength":
                        if ($arParams["INPUT_UTF8"])
                            $tmpStr = $APPLICATION->ConvertCharset($arFields[$key], 'UTF-8', SITE_CHARSET);
                        else
                            $tmpStr = $arFields[$key];
                        if (strlen($tmpStr) > $subRule && !empty($tmpStr)) {
                            $arErrors["ERRORS"][] = Array(
                                "NAME" => $key,
                                "MESSAGE" => !empty($arRules["MESSAGES"][$key][$subKey]) ? $arRules["MESSAGES"][$key][$subKey] : "Error maxlength"
                            );

                            $tmpError = true;
                        }
                        break;
                    case "minlength":
                        if ($arParams["INPUT_UTF8"])
                            $tmpStr = $APPLICATION->ConvertCharset($arFields[$key], 'UTF-8', SITE_CHARSET);
                        else
                            $tmpStr = $arFields[$key];
                        if (strlen($tmpStr) < $subRule && !empty($tmpStr)) {
                            $arErrors["ERRORS"][] = Array(
                                "NAME" => $key,
                                "MESSAGE" => !empty($arRules["MESSAGES"][$key][$subKey]) ? $arRules["MESSAGES"][$key][$subKey] : "Error minlength"
                            );

                            $tmpError = true;
                        }
                        break;
                    case "link_fields":
                        $flag = false;
                        foreach ($subRule as $nameField) {
                            if (!empty($arFields[$nameField]))
                                $flag = true;
                        }
                        if (!$flag) {
                            $arErrors["ERRORS"][] = Array(
                                "NAME" => $key,
                                "MESSAGE" => !empty($arRules["MESSAGES"][$key][$subKey]) ? $arRules["MESSAGES"][$key][$subKey] : "Error link"
                            );

                            $tmpError = true;
                        }
                        break;
                    case "pattern":
                        if (!preg_match($subRule, $arFields[$key]) && !empty($arFields[$key])) {
                            $arErrors["ERRORS"][] = Array(
                                "NAME" => $key,
                                "MESSAGE" => !empty($arRules["MESSAGES"][$key][$subKey]) ? $arRules["MESSAGES"][$key][$subKey] : "Error pattern"
                            );

                            $tmpError = true;
                        }
                        break;
                }

                if ($tmpError && $arParams["ONE_ERROR"])
                    break;
            }
        }

        if (!empty($arErrors))
            return $arErrors;
        else
            return true;
    }

}

?>