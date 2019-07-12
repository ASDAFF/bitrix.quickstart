<?
namespace Slam\Easyform;
use \Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

class Options
{
    public $arCurOptionValues = array();

    private $module_id = '';
    private $arSites = array();
    private $arTabs = array();
    private $arGroups = array();
    private $arOptions = array();
    private $need_access_tab = false;

    public function __construct($module_id, $aSitesTabs,  $arTabs, $arGroups, $arOptions, $need_access_tab = false)
    {
        $this->module_id = $module_id;
        $this->arSites = $aSitesTabs;
        $this->arTabs = $arTabs;
        $this->arGroups = $arGroups;
        $this->arOptions = $arOptions;
        $this->need_access_tab = $need_access_tab;

        if($need_access_tab)
            $this->arTabs[] = array(
                'DIV' => 'edit_access_tab',
                'TAB' => Loc::getMessage('SLAM_EASYFORM_EDIT_ACCESS_TAB'),
                'ICON' => '',
                'TITLE' => Loc::getMessage('SLAM_EASYFORM_EDIT_ACCESS_TITLE'),
            );

        if($_REQUEST['update'] == 'Y' && check_bitrix_sessid()){
            $this->SaveOptions();
            if($this->need_access_tab)
            {
                $this->SaveGroupRight();
            }
        }


        $this->GetCurOptionValues();
    }

    private function SaveOptions()
    {
        foreach($this->arOptions as $site => $arOptParamsSite)
        {
            foreach($arOptParamsSite as $opt => $arOptParams) {
                if ($arOptParams['TYPE'] != 'CUSTOM') {
                    $val = $_REQUEST[$opt][$site];


                    if ($arOptParams['TYPE'] == 'CHECKBOX' && $val != 'Y')
                        $val = 'N';
                    elseif (is_array($val))
                        $val = serialize($val);

                    \COption::SetOptionString($this->module_id, $opt, $val, false, $site);
                }
            }
        }
    }

    private function SaveGroupRight()
    {
        \CMain::DelGroupRight($this->module_id);
        $GROUP = $_REQUEST['GROUPS'];
        $RIGHT = $_REQUEST['RIGHTS'];

        foreach($GROUP as $k => $v) {
            if($k == 0) {
                \COption::SetOptionString($this->module_id, 'GROUP_DEFAULT_RIGHT', $RIGHT[0], 'Right for groups by default');
            }
            else {
                \CMain::SetGroupRight($this->module_id, $GROUP[$k], $RIGHT[$k]);
            }
        }


    }

    private function GetCurOptionValues()
    {
        foreach($this->arOptions as $site => $arOptParamsSite) {
            foreach ($arOptParamsSite as $opt  => $arOptParams) {

                if ($arOptParams['TYPE'] != 'CUSTOM') {

                    $this->arCurOptionValues[$opt][$site] = \COption::GetOptionString($this->module_id, $opt, $arOptParams['DEFAULT'], $site, true);

                    if (in_array($arOptParams['TYPE'], array('MSELECT')))
                        $this->arCurOptionValues[$opt][$site] = unserialize($this->arCurOptionValues[$opt][$site]);
                }
            }
        }
    }

    public function ShowHTML()
    {
        global $APPLICATION;

        $arP = array();

        foreach($this->arGroups as $group_id => $group_params)
            foreach($this->arSites as  $iTab => $site)
                $arP[$group_params['TAB']][$site['LID']][$group_id] = array();



        if(is_array($this->arOptions))
        {
            foreach($this->arOptions as $site => $arOptParamsSite)
            {
                foreach ($arOptParamsSite as $option => $arOptParams) {
                    $val = $this->arCurOptionValues[$option][$site];

                    if ($arOptParams['SORT'] < 0 || !isset($arOptParams['SORT']))
                        $arOptParams['SORT'] = 0;

                    $label = (isset($arOptParams['TITLE']) && $arOptParams['TITLE'] != '') ? $arOptParams['TITLE'] : '';
                    $opt = htmlspecialchars($option).'['.$site.']';

                    switch ($arOptParams['TYPE']) {
                        case 'CHECKBOX':
                            $input = '<input type="checkbox" name="' . $opt . '" id="' . $opt . '" value="Y"' . ($val == 'Y' ? ' checked' : '') . ' ' . ($arOptParams['REFRESH'] == 'Y' ? 'onclick="document.forms[\'' . $this->module_id . '\'].submit();"' : '') . ' />';
                            break;
                        case 'TEXT':
                            if (!isset($arOptParams['COLS']))
                                $arOptParams['COLS'] = 25;
                            if (!isset($arOptParams['ROWS']))
                                $arOptParams['ROWS'] = 5;
                            $input = '<textarea cols="' . $arOptParams['COLS'] . '" rows="' . $arOptParams['ROWS'] . '" name="' . $opt . '">' . htmlspecialchars($val) . '</textarea>';
                            if ($arOptParams['REFRESH'] == 'Y')
                                $input .= '<input type="submit" name="refresh" value="OK" />';
                            break;
                        case 'SELECT':
                            $input = \SelectBoxFromArray($opt, $arOptParams['VALUES'], $val, '', '', ($arOptParams['REFRESH'] == 'Y' ? true : false), ($arOptParams['REFRESH'] == 'Y' ? $this->module_id : ''));
                            if ($arOptParams['REFRESH'] == 'Y')
                                $input .= '<input type="submit" name="refresh" value="OK" />';
                            break;
                        case 'MSELECT':
                            $input = \SelectBoxMFromArray($opt . '[]', $arOptParams['VALUES'], $val);
                            if ($arOptParams['REFRESH'] == 'Y')
                                $input .= '<input type="submit" name="refresh" value="OK" />';
                            break;
                        case 'COLORPICKER':
                            if (!isset($arOptParams['FIELD_SIZE']))
                                $arOptParams['FIELD_SIZE'] = 25;
                            ob_start();
                            echo '<input id="__CP_PARAM_' . $opt . '" name="' . $opt . '" size="' . $arOptParams['FIELD_SIZE'] . '" value="' . htmlspecialchars($val) . '" type="text" style="float: left;" ' . ($arOptParams['FIELD_READONLY'] == 'Y' ? 'readonly' : '') . ' />
                                    <script>
                                        function onSelect_' . $opt . '(color, objColorPicker)
                                        {
                                            var oInput = BX("__CP_PARAM_' . $opt . '");
                                            oInput.value = color;
                                        }
                                    </script>';
                            $APPLICATION->IncludeComponent('bitrix:main.colorpicker', '', Array(
                                'SHOW_BUTTON' => 'Y',
                                'ID' => $opt,
                                'NAME' => 'color',
                                'ONSELECT' => 'onSelect_' . $opt
                            ), false
                            );
                            $input = ob_get_clean();
                            if ($arOptParams['REFRESH'] == 'Y')
                                $input .= '<input type="submit" name="refresh" value="OK" />';
                            break;
                        case 'FILE':
                            if (!isset($arOptParams['FIELD_SIZE']))
                                $arOptParams['FIELD_SIZE'] = 25;
                            if (!isset($arOptParams['BUTTON_TEXT']))
                                $arOptParams['BUTTON_TEXT'] = '...';
                            \CAdminFileDialog::ShowScript(Array(
                                'event' => 'BX_FD_' . $opt,
                                'arResultDest' => Array('FUNCTION_NAME' => 'BX_FD_ONRESULT_' . $opt),
                                'arPath' => Array(),
                                'select' => 'F',
                                'operation' => 'O',
                                'showUploadTab' => true,
                                'showAddToMenuTab' => false,
                                'fileFilter' => '',
                                'allowAllFiles' => true,
                                'SaveConfig' => true
                            ));
                            $input = '<input id="__FD_PARAM_' . $opt . '" name="' . $opt . '" size="' . $arOptParams['FIELD_SIZE'] . '" value="' . htmlspecialchars($val) . '" type="text" style="float: left;" ' . ($arOptParams['FIELD_READONLY'] == 'Y' ? 'readonly' : '') . ' />
                                        <input value="' . $arOptParams['BUTTON_TEXT'] . '" type="button" onclick="window.BX_FD_' . $opt . '();" />
                                        <script>
                                            setTimeout(function(){
                                                if (BX("bx_fd_input_' . strtolower($opt) . '"))
                                                    BX("bx_fd_input_' . strtolower($opt) . '").onclick = window.BX_FD_' . $opt . ';
                                            }, 200);
                                            window.BX_FD_ONRESULT_' . $opt . ' = function(filename, filepath)
                                            {
                                                var oInput = BX("__FD_PARAM_' . $opt . '");
                                                if (typeof filename == "object")
                                                    oInput.value = filename.src;
                                                else
                                                    oInput.value = (filepath + "/" + filename).replace(/\/\//ig, \'/\');
                                            }
                                        </script>';
                            if ($arOptParams['REFRESH'] == 'Y')
                                $input .= '<input type="submit" name="refresh" value="OK" />';
                            break;
                        case 'CUSTOM':
                            $input = $arOptParams['VALUE'];
                            break;
                        default:
                            if (!isset($arOptParams['SIZE']))
                                $arOptParams['SIZE'] = 25;
                            if (!isset($arOptParams['MAXLENGTH']))
                                $arOptParams['MAXLENGTH'] = 255;
                            $input = '<input type="' . ($arOptParams['TYPE'] == 'INT' ? 'number' : 'text') . '" size="' . $arOptParams['SIZE'] . '" maxlength="' . $arOptParams['MAXLENGTH'] . '" value="' . htmlspecialchars($val) . '" name="' . $opt . '" />';
                            if ($arOptParams['REFRESH'] == 'Y')
                                $input .= '<input type="submit" name="refresh" value="OK" />';
                            break;
                    }


                    if (isset($arOptParams['NOTES']) && $arOptParams['NOTES'] != '')
                        $input .= '<div>
                                        <table cellspacing="0" cellpadding="0" border="0" class="notes">
                                            <tbody>
                                                <tr class="top">
                                                    <td class="left"><div class="empty"></div></td>
                                                    <td><div class="empty"></div></td>
                                                    <td class="right"><div class="empty"></div></td>
                                                </tr>
                                                <tr>
                                                    <td class="left"><div class="empty"></div></td>
                                                    <td class="content">
                                                        ' . $arOptParams['NOTES'] . '
                                                    </td>
                                                    <td class="right"><div class="empty"></div></td>
                                                </tr>
                                                <tr class="bottom">
                                                    <td class="left"><div class="empty"></div></td>
                                                    <td><div class="empty"></div></td>
                                                    <td class="right"><div class="empty"></div></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>';

                    if ($arOptParams['TYPE'] == 'NOTE' && $arOptParams['TITLE']) {
                        $arP[$this->arGroups[$arOptParams['GROUP']]['TAB']][$site][$arOptParams['GROUP']]['OPTIONS'][] = '<tr><td colspan="2" style="text-align: center" align="center"><div class="adm-info-message-wrap" align="center"><div class="adm-info-message">' . $arOptParams['TITLE'] . '</div></div></td></tr>';
                    } else {
                        $arP[$this->arGroups[$arOptParams['GROUP']]['TAB']][$site][$arOptParams['GROUP']]['OPTIONS'][] = $label != '' ? '<tr><td valign="top" width="40%">' . $label . '</td><td valign="top" nowrap>' . $input . '</td></tr>' : '<tr><td valign="top" colspan="2"  style="text-align: center"  align="center">' . $input . '</td></tr>';

                    }
                    $arP[$this->arGroups[$arOptParams['GROUP']]['TAB']][$site][$arOptParams['GROUP']]['OPTIONS_SORT'][] = $arOptParams['SORT'];
                }
            }




            echo '<form name="'.$this->module_id.'" method="POST" action="'.$APPLICATION->GetCurPage().'?mid='.$this->module_id.'&lang='.LANGUAGE_ID.'" enctype="multipart/form-data">'.bitrix_sessid_post();

            $tabControl = new \CAdminTabControl('tabControl', $this->arTabs);
            $tabControl->Begin();


            //    echo '<pre>'; print_r($arP); echo '</pre>';

            $i_site = 0;
            foreach($arP as $tab => $groupsParams)
            {
                $i_site++;
                $tabControl->BeginNextTab();

                echo '<table style="width: 100%;"><tr><td colspan="2">';

                $subTabControl = new \CAdminViewTabControl("site_edit_table_".$tab."_".$i_site,  $this->arSites);
                $subTabControl->Begin();

                foreach($groupsParams as $site => $groups)
                {
                    $subTabControl->BeginNextTab();
                    echo '<table style="width: 100%;">';
                    foreach ($groups as $group_id => $group) {
                        if (sizeof($group['OPTIONS_SORT']) > 0) {
                            echo '<tr class="heading"><td colspan="2">' . $this->arGroups[$group_id]['TITLE'] . '</td></tr>';

                            array_multisort($group['OPTIONS_SORT'], $group['OPTIONS']);
                            foreach ($group['OPTIONS'] as $opt)
                                echo $opt;
                        }
                    }
                    echo '</table>';

                }

                $subTabControl->End();
            }

            if($this->need_access_tab)
            {
                $tabControl->BeginNextTab();
                $module_id = $this->module_id;
                require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");
            }

            $tabControl->Buttons();

            echo     '<input type="hidden" name="update" value="Y" />
                                      <input type="submit" name="save" value="'.Loc::getMessage('SLAM_EASYFORM_SAVE').'" />
                                      <input type="reset" name="reset" value="'.Loc::getMessage('SLAM_EASYFORM_RESET').'" />';

            $tabControl->End();


            echo '</form>';



        }
    }
}
?>