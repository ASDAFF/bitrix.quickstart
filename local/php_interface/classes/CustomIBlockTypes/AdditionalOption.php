<?php

use Bitrix\Main\IO\File;
use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Page\Asset;

class AdditionalOption
{
    private static $firstCall = true;

    private static function IncludeJs()
    {
        Asset::getInstance()->addJs("/bitrix/js/main/jquery/jquery-2.1.3.min.js");
        $optionTemplateFile = new File(__DIR__ . "/option.js", SITE_ID);
        echo "<script>";
        echo $optionTemplateFile->getContents();
        echo "</script>";
    }

    /**
     * Описывает поведение пользовательского свойства.
     *
     * @return array
     */
    public function GetUserTypeDescription()
    {
        return array(
            'PROPERTY_TYPE' => 'S',
            'USER_TYPE' => 'AdditionalOption',
            'DESCRIPTION' => 'Дополнительная опция',
            'GetPropertyFieldHtml' => array('AdditionalOption', 'GetPropertyFieldHtml'),
            'ConvertToDB' => array('AdditionalOption', 'ConvertToDB'),
            'ConvertFromDB' => array('AdditionalOption', 'ConvertFromDB')
        );
    }

    /**
     * Формирует HTML-код полей для формы.
     *
     * @param $arProperty
     * @param $value
     * @param $strHTMLControlName
     * @return string
     */
    function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
    {
        if (self::$firstCall) {
            self::IncludeJs();
            self::$firstCall = false;
        }

        $optionTemplateFile = new File(__DIR__ . "/option.html", SITE_ID);
        $optionTemplate = $optionTemplateFile->getContents(__DIR__ . "/option.html");

        $propName = $strHTMLControlName["VALUE"];
        if (empty($value["VALUE"]["type"])) {
            $value["VALUE"]["type"] = "L";
        }

        $optionTemplate = str_replace(
            [
                '$strHTMLControlName',
                '$valuesInList',
                '$type',
                'value="' . $value["VALUE"]["type"] . '"',
                '$optionTitle'
            ],
            [
                $propName,
                self::GetInputs($value, $propName),
                $value["VALUE"]["type"],
                'value="' . $value["VALUE"]["type"] . '" selected="selected"',
                $value["VALUE"]["optionTitle"]
            ],
            $optionTemplate
        );

        return $optionTemplate;
    }

    /**
     * Конверертирует данные с фомры в формат для хранения в БД.
     *
     * @param $arProperty
     * @param $value
     * @return null
     */
    function ConvertToDB($arProperty, $value)
    {
        if ($value["VALUE"]["name"]) {
            return null;
        }

        if ($value["VALUE"]["type"] == "L") {
            $isEmpty = true;
            foreach ($value["VALUE"]["value"] as $v) {
                if ($v) {
                    $isEmpty = false;
                }
            }

            if ($isEmpty) {
                return null;
            }
        }

        $value["VALUE"] = serialize($value["VALUE"]);
        return $value;
    }

    /**
     * Конвертиреут данные из базы в формат для вывода на форму.
     *
     * @param $arProperty
     * @param $value
     * @return mixed
     */
    function ConvertFromDB($arProperty, $value)
    {
        $value["VALUE"] = unserialize($value["VALUE"]);
        return $value;
    }

    /**
     * @param $value
     * @param $propName
     * @return string
     */
    private static function GetInputs($value, $propName)
    {
        $inputs = "";
        if ($value["VALUE"]["type"] == "L" && count($value["VALUE"]["value"]) > 0) {

            foreach ($value["VALUE"]["value"] as $key => $item) {
                $inputs .= "<input class=\"js-value-in-list\" placeholder=\"Значение $key\" type=\"text\" name=\"" . $propName . "[value][$key]\" value=\"$item\"><br>";
            }

        } else {
            for ($i = 0; $i < 3; $i++) {
                $inputs .= "<input class=\"js-value-in-list\" placeholder=\"Значение $i\" type=\"text\" name=\"" . $propName . "[value][$i]\"><br>";
            }
        }
        return $inputs;
    }
}