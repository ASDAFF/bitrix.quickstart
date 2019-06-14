<?
namespace LastWorld\Property;

use Bitrix\Main\Localization\Loc;
use CJSCore;

Loc::loadMessages(__FILE__);

class CColorProperty
{
    public static function GetUserTypeDescription()
    {
        return array(
            'PROPERTY_TYPE' => 'S',
            'USER_TYPE' => 'LWColorProperty',
            'DESCRIPTION' => Loc::getMessage('LW_COLOR_PROPERTY'),
            'GetPropertyFieldHtml' => array('LastWorld\\Property\\CColorProperty', 'GetPropertyFieldHtml'),
            'GetPropertyFieldHtmlMulty' => array('LastWorld\\Property\\CColorProperty', 'GetPropertyFieldHtmlMulty'),
        );
    }

    public static function GetPropertyFieldHtml($arProperty, $value, $control)
    {
        CJSCore::Init(array("lwcolor"));
        ob_start();
        ?>
        <div id="colorSelector<?=$arProperty['ID'];?>"></div>
        <input type="text" name="<?=$control['VALUE'];?>" value="<?=(strlen($value['VALUE']) > 0) ? $value['VALUE'] : '#000000';?>" id="color<?=$arProperty['ID'];?>"/>
        <script type="text/javascript">
            $(document).ready(function() {
                $('#colorSelector<?=$arProperty['ID'];?>').farbtastic('#color<?=$arProperty['ID'];?>');
            });
        </script>
        <?
        $html = ob_get_clean();

        return $html;
    }

    public static function GetPropertyFieldHtmlMulty($arProperty, $value, $control)
    {
        CJSCore::Init(array("lwcolor"));
        ob_start();

        $jsFocuserScript = 'if ($(this).val() == \'\') {$(this).val(\'#000000\');} colorPicker' . $arProperty['ID'] . '.linkTo($(this));';
        $firstValue = 'color' . $arProperty['ID'] . '_new0';
        ?>
        <table cellpadding="0" cellspacing="0" border="0" class="nopadding" width="100%" id="tb<?=md5($control['VALUE'] . 'VALUE')?>">
            <tbody>
            <tr>
                <td>
                    <div id="colorSelector<?=$arProperty['ID'];?>"></div>
                </td>
            </tr>
                <?
                if (is_array($value))
                {
                    foreach($value as $valueId => $valueValue)
                    {
                    ?>
                        <tr>
                            <td>
                                <input type="text" name="<?=$control['VALUE'] . '[' . $valueId . ']';?>" value="<?=$valueValue['VALUE'];?>" id="color<?=$arProperty['ID'];?>_<?=$valueId;?>"  onfocus="<?=$jsFocuserScript;?>"/>
                            </td>
                        </tr>
                    <?
                    }
                }
                ?>
                <tr>
                    <td>
                        <input type="text" name="<?=$control['VALUE'] . '[]';?>" id="color<?=$arProperty['ID'];?>_new0" onfocus="<?=$jsFocuserScript;?>"/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="button" value="<?=Loc::getMessage('LW_COLOR_ADD_ROW');?>" onclick="addNewRow('tb<?=md5($control['VALUE'] . 'VALUE');?>')">
                    </td>
                </tr>
            </tbody>
        </table>
        <script type="text/javascript">
            var colorPicker<?=$arProperty['ID'];?>;
            $(document).ready(function() {
                <?
                $isFirst = true;
                if (is_array($value))
                {
                    foreach($value as $valueId => $valueValue)
                    {
                        if ($isFirst)
                        {
                            $isFirst = false;
                            $firstValue = 'color' . $arProperty['ID'] . '_' . $valueId;
                            ?>
                            colorPicker<?=$arProperty['ID'];?> = $.farbtastic('#colorSelector<?=$arProperty['ID'];?>', '#<?=$firstValue;?>');
                            <?
                        }
                        else
                        {
                            $tmpValue = 'color' . $arProperty['ID'] . '_' . $valueId;
                            ?>
                            colorPicker<?=$arProperty['ID'];?>.linkTo('#<?=$tmpValue?>');
                        <?
                        }
                    }
                }
                ?>
                colorPicker<?=$arProperty['ID'];?>.linkTo('#<?=$firstValue?>');
            });
        </script>
        <?
        $html = ob_get_clean();

        //$html = Loc::getMessage('LW_COLOR_MULTIPLE_NOT_SUPPORTED');
        return $html;
    }
}