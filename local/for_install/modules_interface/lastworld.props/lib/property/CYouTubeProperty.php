<?php
namespace LastWorld\Property;

use Bitrix\Main\Localization\Loc;
use Lastworld\Helper\CLWLinkHelper;

Loc::loadMessages(__FILE__);

class CYouTubeProperty
{
    public static function GetUserTypeDescription()
    {
        return array(
            'PROPERTY_TYPE' => 'S',
            'USER_TYPE' => 'LWYouTubeProperty',
            'DESCRIPTION' => Loc::getMessage('LW_YOUTUBE_PROPERTY'),
            'ConvertToDB' => array('LastWorld\\Property\\CYouTubeProperty', 'ConvertToDB'),
            'GetPropertyFieldHtml' => array('LastWorld\\Property\\CYouTubeProperty', 'GetPropertyFieldHtml'),
            'GetPropertyFieldHtmlMulty' => array('LastWorld\\Property\\CYouTubeProperty', 'GetPropertyFieldHtmlMulty'),
            'GetSettingsHTML' => array('LastWorld\\Property\\CYouTubeProperty', 'GetSettingsHTML'),
            'PrepareSettings' => array('LastWorld\\Property\\CYouTubeProperty', 'PrepareSettings'),
        );
    }

    public static function ConvertToDB($arProperty, $value)
    {
        $value['VALUE'] = str_replace(' ', '', $value['VALUE']);

        if (strpos($value['VALUE'], 'youtube.com/watch') !== false)
        {
            $linkParser = new CLWLinkHelper();
            $videoId = $linkParser->initUrl($value['VALUE'])->parseUrl()->getParam('v');

            if ($videoId !== false)
            {
                $value['VALUE'] = $videoId;
            }
        }
        elseif (strpos($value['VALUE'], 'youtu.be/') !== false)
        {
            $linkParser = new CLWLinkHelper();
            $videoId = $linkParser->initUrl($value['VALUE'])->parseUrl()->getPath(-1);

            if ($videoId !== false)
            {
                $value['VALUE'] = $videoId;
            }
        }

        return $value;
    }

    public static function GetPropertyFieldHtml($arProperty, $value, $control)
    {
        $html = '<input type="text" name="' . $control['VALUE'] . '" value="' . $value['VALUE'] . '"/>';

        if (strlen($value['VALUE']) > 0)
        {
            $html .= '<br/><br/>';
            $html .= '<iframe width="' . $arProperty["USER_TYPE_SETTINGS"]["ADMIN_PLAYER_WIDTH"] . '" height="' . $arProperty["USER_TYPE_SETTINGS"]["ADMIN_PLAYER_HEIGHT"] . '" src="http://www.youtube-nocookie.com/embed/' . $value["VALUE"] . '" frameborder="0" allowfullscreen></iframe>';
            $html .= '<br/><br/>';
        }

        return $html;
    }

    public static function GetPropertyFieldHtmlMulty($arProperty, $value, $control)
    {
        $html = '<table cellpadding="0" cellspacing="0" border="0" class="nopadding" width="100%" id="tb' . md5($control['VALUE'] . "VALUE") . '">';
        $html .= '<tbody>';

        if (is_array($value))
        {
            foreach($value as $valueId => $valueValue)
            {
                $html .= '<tr><td><input type="text" name="' . $control['VALUE'] . '[' . $valueId . ']" value="' . $valueValue['VALUE'] . '"/> <a href="https://www.youtube.com/watch?v=' . $valueValue['VALUE'] . '" target="_blank">' . Loc::getMessage('LW_YOUTUBE_WATCH_ON_YOUTUBE') . '</a></td></tr>';
            }
        }
        for ($i = 0; $i < 3; $i++)
        {
            $html .= '<tr><td><input type="text" name="' . $control['VALUE'] . '[]" value=""/>';
        }
        $html .= '<tr><td><input type="button" value="' . Loc::getMessage('LW_YOUTUBE_ADD_ROW') . '" onclick="addNewRow(\'tb' . md5($control['VALUE'] . "VALUE") . '\')"></td></tr>';
        $html .= '</tbody></table>';

        return $html;
    }

    public static function GetSettingsHTML($arProperty, $control, &$arPropertyFields)
    {
        $arProperty['USER_TYPE_SETTINGS'] = self::PrepareSettings($arProperty);

        $arPropertyFields = array(
            /*'HIDE' => array(
                'ROW_COUNT', 'COL_COUNT', 'DEFAULT_VALUE', 'SEARCHABLE', 'FILTRABLE', 'MULTIPLE_CNT'
            ),*/
            'USER_TYPE_SETTINGS_TITLE' => Loc::getMessage('LW_YOUTUBE_SETTINGS_TITLE')
        );

        return '
        <tr>
            <td colspan="2" style="text-align: center;"><b>' . Loc::getMessage('LW_YOUTUBE_ADMIN_SIZE') . '</b></td>
        </tr>
        <tr>
            <td>' . Loc::getMessage('LW_YOUTUBE_PLAYER_WIDTH') . '</td>
            <td>
                <input
                    type="text"
                    value="' . htmlspecialcharsbx($arProperty['USER_TYPE_SETTINGS']['ADMIN_PLAYER_WIDTH']) . '"
                    name="' . $control['NAME'] . '[ADMIN_PLAYER_WIDTH]"
                >
            </td>
        </tr>
        <tr>
            <td>' . Loc::getMessage('LW_YOUTUBE_PLAYER_HEIGHT') . '</td>
            <td>
                <input
                    type="text"
                    value="' . htmlspecialcharsbx($arProperty['USER_TYPE_SETTINGS']['ADMIN_PLAYER_HEIGHT']) . '"
                    name="' . $control['NAME'] . '[ADMIN_PLAYER_HEIGHT]"
                >
            </td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center;"><b>' . Loc::getMessage('LW_YOUTUBE_PUBLIC_SIZE') . '</b></td>
        </tr>
        <tr>
            <td>' . Loc::getMessage('LW_YOUTUBE_PLAYER_WIDTH') . '</td>
            <td>
                <input
                    type="text"
                    value="' . htmlspecialcharsbx($arProperty['USER_TYPE_SETTINGS']['PUBLIC_PLAYER_WIDTH']) . '"
                    name="' . $control['NAME'] . '[PUBLIC_PLAYER_WIDTH]"
                >
            </td>
        </tr>
        <tr>
            <td>' . Loc::getMessage('LW_YOUTUBE_PLAYER_HEIGHT') . '</td>
            <td>
                <input
                    type="text"
                    value="' . htmlspecialcharsbx($arProperty['USER_TYPE_SETTINGS']['PUBLIC_PLAYER_HEIGHT']) . '"
                    name="' . $control['NAME'] . '[PUBLIC_PLAYER_HEIGHT]"
                >
            </td>
        </tr>
        ';
    }

    public static function PrepareSettings($arFields)
    {
        $arFields['USER_TYPE_SETTINGS']['ADMIN_PLAYER_WIDTH'] = (int) $arFields['USER_TYPE_SETTINGS']['ADMIN_PLAYER_WIDTH'];
        if (!$arFields['USER_TYPE_SETTINGS']['ADMIN_PLAYER_WIDTH'])
        {
            $arFields['USER_TYPE_SETTINGS']['ADMIN_PLAYER_WIDTH'] = 350;
        }

        $arFields['USER_TYPE_SETTINGS']['ADMIN_PLAYER_HEIGHT'] = (int) $arFields['USER_TYPE_SETTINGS']['ADMIN_PLAYER_HEIGHT'];
        if (!$arFields['USER_TYPE_SETTINGS']['ADMIN_PLAYER_HEIGHT'])
        {
            $arFields['USER_TYPE_SETTINGS']['ADMIN_PLAYER_HEIGHT'] = 200;
        }

        $arFields['USER_TYPE_SETTINGS']['PUBLIC_PLAYER_WIDTH'] = (int) $arFields['USER_TYPE_SETTINGS']['PUBLIC_PLAYER_WIDTH'];
        if (!$arFields['USER_TYPE_SETTINGS']['PUBLIC_PLAYER_WIDTH'])
        {
            $arFields['USER_TYPE_SETTINGS']['PUBLIC_PLAYER_WIDTH'] = 700;
        }

        $arFields['USER_TYPE_SETTINGS']['PUBLIC_PLAYER_HEIGHT'] = (int) $arFields['USER_TYPE_SETTINGS']['PUBLIC_PLAYER_HEIGHT'];
        if (!$arFields['USER_TYPE_SETTINGS']['PUBLIC_PLAYER_HEIGHT'])
        {
            $arFields['USER_TYPE_SETTINGS']['PUBLIC_PLAYER_HEIGHT'] = 400;
        }

//        $arFields["USER_TYPE_SETTINGS"]["RELATED_VIDEO"] = $arFields["USER_TYPE_SETTINGS"]["RELATED_VIDEO"] == "Y" ? "Y" : "N";
//        $arFields["USER_TYPE_SETTINGS"]["ENHANCED_PRIVACY_MODE"] = $arFields["USER_TYPE_SETTINGS"]["ENHANCED_PRIVACY_MODE"] == "Y" ? "Y" : "N";

        return $arFields["USER_TYPE_SETTINGS"];
    }
}