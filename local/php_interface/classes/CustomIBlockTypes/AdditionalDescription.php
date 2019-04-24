<?
use Bitrix\Main\Diag\Debug;

class AdditionalDescription
{
    /**
     * Описывает поведение пользовательского свойства.
     *
     * @return array
     */
    public function GetUserTypeDescription()
    {
        return array(
            'PROPERTY_TYPE' => 'S',
            'USER_TYPE' => 'TWO_COLUMN',
            'DESCRIPTION' => 'Дополнительное описание',
            'GetPropertyFieldHtml' => array('AdditionalDescription', 'GetPropertyFieldHtml'),
            'ConvertToDB' => array('AdditionalDescription', 'ConvertToDB'),
            'ConvertFromDB' => array('AdditionalDescription', 'ConvertFromDB')
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

        return "
        <input type='text' name='".$strHTMLControlName["VALUE"]."[titleField]' value='".$value["VALUE"]["titleField"]."'>
        <input type='text' name='". $strHTMLControlName["VALUE"] ."[valueField]' value='".$value["VALUE"]["valueField"]."'>
        ";
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
        $isEmpty = true;
        foreach ($value["VALUE"] as $v) {
            if ($v) {
                $isEmpty = false;
            }
        }

        if ($isEmpty) {
            return null;
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
}