<?php
/**
 * Copyright (c) 3/2/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

namespace Helper;

/**
 * Пользовательское свойcтво тип HTML
 * http://marketplace.1c-bitrix.ru/solutions/d2mg.ufhtml/
 */

class PropertyHTML{
    function GetUserTypeDescription()
    {
        return array(
            "USER_TYPE_ID" => "html",
            "CLASS_NAME" => "PropertyHTML",
            "DESCRIPTION" => "HTML",
            "BASE_TYPE" => "string",
        );
    }

    /**
     * Эта функция вызывается при добавлении нового свойства.
     *
     * <p>Эта функция вызывается для конструирования SQL запроса
     * создания колонки для хранения не множественных значений свойства.</p>
     * <p>Значения множественных свойств хранятся не в строках, а столбиках (как в инфоблоках)
     * и тип такого поля в БД всегда text.</p>
     * @param array $arUserField Массив описывающий поле
     * @return string
     * @static
     */
    function GetDBColumnType($arUserField)
    {
        global $DB;
        switch(strtolower($DB->type))
        {
            case "mysql":
                return "text";
            case "oracle":
                return "varchar2(2000 char)";
            case "mssql":
                return "varchar(2000)";
        }
    }

    /**
     * Эта функция вызывается перед сохранением метаданных свойства в БД.
     *
     * <p>Она должна "очистить" массив с настройками экземпляра типа свойства.
     * Для того что бы случайно/намеренно никто не записал туда всякой фигни.</p>
     * @param array $arUserField Массив описывающий поле. <b>Внимание!</b> это описание поля еще не сохранено в БД!
     * @return array Массив который в дальнейшем будет сериализован и сохранен в БД.
     * @static
     */
    function PrepareSettings($arUserField)
    {
        return array(
            "DEFAULT_VALUE" => $arUserField["SETTINGS"]["DEFAULT_VALUE"]
        );
    }

    /**
     * Эта функция вызывается при выводе формы настройки свойства.
     *
     * <p>Возвращает html для встраивания в 2-х колоночную таблицу.
     * в форму usertype_edit.php</p>
     * <p>т.е. tr td bla-bla /td td edit-edit-edit /td /tr </p>
     * @param array $arUserField Массив описывающий поле. Для нового (еще не добавленного поля - <b>false</b>)
     * @param array $arHtmlControl Массив управления из формы. Пока содержит только один элемент NAME (html безопасный)
     * @return string HTML для вывода.
     * @static
     */
    function GetSettingsHTML($arUserField = false, $arHtmlControl, $bVarsFromForm)
    {
        $result = '';
        if($bVarsFromForm)
            $value = htmlspecialcharsbx($GLOBALS[$arHtmlControl["NAME"]]["DEFAULT_VALUE"]);
        elseif(is_array($arUserField))
            $value = htmlspecialcharsbx($arUserField["SETTINGS"]["DEFAULT_VALUE"]);
        else
            $value = "";
        $result .= '
		<tr>
			<td>Значение по умолчанию:</td>
			<td>
				<input type="text" name="'.$arHtmlControl["NAME"].'[DEFAULT_VALUE]" size="20"  maxlength="225" value="'.$value.'">
			</td>
		</tr>
		';

        return $result;
    }

    function GetAdminListEditHTML($arUserField, $arHtmlControl)
    {
        if($arUserField["SETTINGS"]["ROWS"] < 2)
            return '<input type="text" '.
                'name="'.$arHtmlControl["NAME"].'" '.
                'size="'.$arUserField["SETTINGS"]["SIZE"].'" '.
                ($arUserField["SETTINGS"]["MAX_LENGTH"]>0? 'maxlength="'.$arUserField["SETTINGS"]["MAX_LENGTH"].'" ': '').
                'value="'.$arHtmlControl["VALUE"].'" '.
                '>';
        else
            return '<textarea '.
                'name="'.$arHtmlControl["NAME"].'" '.
                'cols="'.$arUserField["SETTINGS"]["SIZE"].'" '.
                'rows="'.$arUserField["SETTINGS"]["ROWS"].'" '.
                ($arUserField["SETTINGS"]["MAX_LENGTH"]>0? 'maxlength="'.$arUserField["SETTINGS"]["MAX_LENGTH"].'" ': '').
                '>'.$arHtmlControl["VALUE"].'</textarea>';
    }

    /**
     * Эта функция вызывается при выводе формы редактирования значения свойства.
     *
     * <p>Возвращает html для встраивания в ячейку таблицы.
     * в форму редактирования сущности (на вкладке "Доп. свойства")</p>
     * <p>Элементы $arHtmlControl приведены к html безопасному виду.</p>
     * @param array $arUserField Массив описывающий поле.
     * @param array $arHtmlControl Массив управления из формы. Содержит элементы NAME и VALUE.
     * @return string HTML для вывода.
     * @static
     */
    function GetEditFormHTML($arUserField, $arHtmlControl)
    {
        //	if($arUserField["ENTITY_VALUE_ID"]<1 && strlen($arUserField["SETTINGS"]["DEFAULT_VALUE"])>0)
        //$arHtmlControl["VALUE"] = htmlspecialcharsbx($arUserField["SETTINGS"]["DEFAULT_VALUE"]);

        $id = preg_replace("/[^a-z0-9]/i", '', $arHtmlControl['NAME']);

        ob_start();
        ?><table><?
        if(COption::GetOptionString("iblock", "use_htmledit", "Y")=="Y" && CModule::IncludeModule("fileman")):
            ?><tr>
            <td colspan="2" align="center">
                <input type="hidden" name="<?=$arHtmlControl["NAME"]?>" value="" />
                <?if (is_null($arUserField["VALUE"])){
                    $arHtmlControl["VALUE"] = $arUserField["SETTINGS"]["DEFAULT_VALUE"];
                }?>
                <?
                $text_type = preg_replace("/([^a-z0-9])/is", "_", $arHtmlControl["NAME"]."[TYPE]");
                CFileMan::AddHTMLEditorFrame($arHtmlControl["NAME"], $arHtmlControl["VALUE"], $text_type, strToLower("html"), $settings['height'], "N", 0, "", "");
                ?>
            </td>
            </tr>
        <?else:?>
            <tr>
                <td><?echo GetMessage("IBLOCK_DESC_TYPE")?></td>
                <td>
                    <input type="radio" name="<?=$strHTMLControlName["VALUE"]?>[TYPE]" id="<?=$strHTMLControlName["VALUE"]?>[TYPE][TEXT]" value="text" <?if($ar["TYPE"]!="html")echo " checked"?>>
                    <label for="<?=$strHTMLControlName["VALUE"]?>[TYPE][TEXT]"><?echo GetMessage("IBLOCK_DESC_TYPE_TEXT")?></label> /
                    <input type="radio" name="<?=$strHTMLControlName["VALUE"]?>[TYPE]" id="<?=$strHTMLControlName["VALUE"]?>[TYPE][HTML]" value="html"<?if($ar["TYPE"]=="html")echo " checked"?>>
                    <label for="<?=$strHTMLControlName["VALUE"]?>[TYPE][HTML]"><?echo GetMessage("IBLOCK_DESC_TYPE_HTML")?></label>
                </td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    <textarea cols="60" rows="10" name="<?=$strHTMLControlName["VALUE"]?>[TEXT]" style="width:100%"><?=$ar["TEXT"]?></textarea></td>
            </tr>
        <?endif;
        if (($arProperty["WITH_DESCRIPTION"]=="Y") && ('' != trim($strHTMLControlName["DESCRIPTION"]))):?>
            <tr>
                <td colspan="2">
                    <span title="<?echo GetMessage("IBLOCK_PROP_HTML_DESCRIPTION_TITLE")?>"><?echo GetMessage("IBLOCK_PROP_HTML_DESCRIPTION_LABEL")?>:<input type="text" name="<?=$strHTMLControlName["DESCRIPTION"]?>" value="<?=$value["DESCRIPTION"]?>" size="18"></span>
                </td>
            </tr>
        <?endif;?>
        </table>
        <?
        $return = ob_get_contents();
        ob_end_clean();
        return  $return;

    }

    /**
     * Эта функция вызывается при выводе значения свойства в списке элементов.
     *
     * <p>Возвращает html для встраивания в ячейку таблицы.</p>
     * <p>Элементы $arHtmlControl приведены к html безопасному виду.</p>
     * @param array $arUserField Массив описывающий поле.
     * @param array $arHtmlControl Массив управления из формы. Содержит элементы NAME и VALUE.
     * @return string HTML для вывода.
     * @static
     */
    function GetAdminListViewHTML($arUserField, $arHtmlControl)
    {
        if(strlen($arHtmlControl["VALUE"])>0)
            return $arHtmlControl["VALUE"];
        else
            return '&nbsp;';
    }
}