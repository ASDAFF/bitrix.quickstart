<?php
/**
 * Created by Artmix.
 * User: Oleg Maksimenko <oleg.39style@gmail.com>
 * Date: 02.06.2016. Time: 14:14
 */

namespace Artmix\UserTypeFileExt;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/iblock/admin_tools.php';

/**
 * Class UserTypeFileExt
 * @package Artmix\UserTypeFileExt
 */
class UserTypeFileExt extends \CUserTypeFile
{

    /**
     * @return array
     */
    function getUserTypeDescription()
    {
        return array(
            'USER_TYPE_ID' => 'file_ext_artmix',
            'CLASS_NAME' => __CLASS__,
            'DESCRIPTION' => Loc::getMessage('ARTMIX_USERTYPEFILEEXT_PROP_DESCRIPTION'),
            'BASE_TYPE' => 'file'
        );
    }

    /**
     * @param array $arUserField
     * @return array
     */
    function PrepareSettings($arUserField)
    {
        $settings = parent::PrepareSettings($arUserField);

        $settings['WITH_DESCRIPTION'] = isset($arUserField['SETTINGS']['WITH_DESCRIPTION'])
        && in_array($arUserField['SETTINGS']['WITH_DESCRIPTION'], array('N', 'Y'))
            ? $arUserField['SETTINGS']['WITH_DESCRIPTION']
            : 'N';

        return $settings;
    }

    /**
     * @param array $arUserField
     * @param array $arHtmlControl
     * @return string
     */
    function GetEditFormHTML($arUserField, $arHtmlControl)
    {
        return static::getEditFormHTMLBase($arUserField, $arHtmlControl);
    }

    /**
     * @param $arUserField
     * @param $arHtmlControl
     * @return string
     */
    function GetEditFormHTMLMulty($arUserField, $arHtmlControl)
    {
        return static::getEditFormHTMLBase($arUserField, $arHtmlControl);
    }

    /**
     * @param array $arUserField
     * @param array $arHtmlControl
     * @return string
     */
    protected static function getEditFormHTMLBase($arUserField, $arHtmlControl)
    {
        ob_start();

        \_ShowFilePropertyField(
            (
                $arUserField['MULTIPLE'] == 'Y'
                    ? str_replace('[]', '', $arHtmlControl['NAME'])
                    : $arHtmlControl['NAME']
            ),
            array(
                'MULTIPLE' => $arUserField['MULTIPLE'],
                'WITH_DESCRIPTION' => $arUserField['SETTINGS']['WITH_DESCRIPTION'],
                'FILE_TYPE' => $arUserField['SETTINGS']['EXTENSIONS'],
            ),
            (!is_array($arHtmlControl['VALUE']) && !empty($arHtmlControl['VALUE'])
                ? array($arHtmlControl['VALUE'])
                : $arHtmlControl['VALUE'])
        );

        $result = ob_get_clean();

        return $result;
    }

    function OnBeforeSave($arUserField, $value, $user_id)
    {

        $filesData = reset($value);

        $filesData['MODULE_ID'] = 'artmix.usertypefileext';

        $r = parent::OnBeforeSave($arUserField, $filesData, $user_id);

        return $r;

    }

    /**
     * @param array $arUserField
     * @param $value
     * @param $user_id
     * @return array
     */
    function OnBeforeSaveAll($arUserField, $value, $user_id)
    {
        
        $delFilesData = isset($_POST[$arUserField['FIELD_NAME'] . '_del']) && is_array($_POST[$arUserField['FIELD_NAME'] . '_del'])
            ? $_POST[$arUserField['FIELD_NAME'] . '_del']
            : array();

        $descriptionFilesData = isset($_POST[$arUserField['FIELD_NAME'] . '_descr']) && is_array($_POST[$arUserField['FIELD_NAME'] . '_descr'])
            ? $_POST[$arUserField['FIELD_NAME'] . '_descr']
            : array();

        $filesData = isset($_POST[$arUserField['FIELD_NAME']]) && is_array($_POST[$arUserField['FIELD_NAME']])
            ? $_POST[$arUserField['FIELD_NAME']]
            : array();

        $files = array();

        foreach ($filesData as $key => $fileData) {

            $delFile = false;

            if (is_array($fileData)) {

                $tempFile = $_SERVER['DOCUMENT_ROOT'] . $fileData['tmp_name'];

                if (isset($fileData['error']) && $fileData['error'] > 0) {

                } else {

                    $files[] = \CFile::SaveFile(
                        array_merge(
                            $fileData,
                            array(
                                'tmp_name' => $tempFile,
                                'MODULE_ID' => 'artmix.usertypefileext',
                            )
                        ),
                        'uf'
                    );

                }

                @unlink($tempFile);

                @rmdir(dirname($tempFile));

            } elseif (intval($fileData) > 0) {

                if (isset($delFilesData[$key])) {
                    \CFile::Delete($fileData);

                    $delFile = true;
                } else {
                    $files[] = $fileData;
                }

            }

            if (
                !$delFile
                && !is_array($fileData)
                && intval($fileData) > 0
                && isset($descriptionFilesData[$key])
                && strlen(trim($descriptionFilesData[$key]))
            ) {
                \CFile::UpdateDesc($fileData, trim($descriptionFilesData[$key]));
            }

        }

        $files = array_values(
            array_filter(
                array_map('trim', $files)
            )
        );

        return $files;

    }

    /**
     * @param bool|false $arUserField
     * @param $arHtmlControl
     * @param $bVarsFromForm
     * @return string
     */
    function GetSettingsHTML($arUserField = false, $arHtmlControl, $bVarsFromForm)
    {
//        $result = parent::GetSettingsHTML($arUserField, $arHtmlControl, $bVarsFromForm);

        $result = '';

        if ($bVarsFromForm) {
            $value = htmlspecialcharsbx($GLOBALS[$arHtmlControl['NAME']]['EXTENSIONS']);
            $result .= '
			<tr>
				<td>' . Loc::getMessage('USER_TYPE_FILE_EXTENSIONS') . ':</td>
				<td>
					<input type="text" size="20" name="' . $arHtmlControl['NAME'] . '[EXTENSIONS]" value="' . $value . '">
				</td>
			</tr>
			';
        } else {
            if (is_array($arUserField)) {
                $arExt = $arUserField['SETTINGS']['EXTENSIONS'];
            } else {
                $arExt = '';
            }

            $value = array();

            if (is_array($arExt)) {
                foreach ($arExt as $ext => $flag) {
                    $value[] = htmlspecialcharsbx($ext);
                }
            }

            $result .= '
			<tr>
				<td>' . Loc::getMessage('USER_TYPE_FILE_EXTENSIONS') . ':</td>
				<td>
					<input type="text" size="20" name="' . $arHtmlControl['NAME'] . '[EXTENSIONS]" value="' . implode(', ', $value) . '">
				</td>
			</tr>
			';
        }


        if ($bVarsFromForm) {
            $withDescription = ($GLOBALS[$arHtmlControl['NAME']]['WITH_DESCRIPTION'] == 'Y');
        } elseif (is_array($arUserField)) {
            $withDescription = ($arUserField['SETTINGS']['WITH_DESCRIPTION'] == 'Y');
        } else {
            $withDescription = false;
        }

        $result .= '
		<tr>
			<td><label>' . Loc::getMessage('ARTMIX_USERTYPEFILEEXT_PROP_SETTINGS_WITH_DESCRIPTION') . '</label></td>
			<td>
				<input type="hidden" name="' . $arHtmlControl['NAME'] . '[WITH_DESCRIPTION]" value="N">
				<input type="checkbox" name="' . $arHtmlControl['NAME'] . '[WITH_DESCRIPTION]" value="Y"' . ($withDescription ? ' checked' : '') . '>
			</td>
		</tr>
		';

        return $result;
    }

    /**
     * @param array $arUserField
     * @param array $arHtmlControl
     * @return string
     */
    function GetAdminListEditHTMLMulty($arUserField, $arHtmlControl)
    {
        return '';
    }

    /**
     * @param array $arUserField
     * @param array $arHtmlControl
     * @return string
     */
    function GetAdminListEditHTML($arUserField, $arHtmlControl)
    {
        return '';
    }


    /**
     * @param array $arUserField
     * @param array $arHtmlControl
     * @return string
     */
    function GetAdminListViewHTML($arUserField, $arHtmlControl)
    {
        return '';
    }

    /**
     * @param array $arUserField
     * @return string
     */
    function OnSearchIndex($arUserField)
    {
        return '';
    }

}