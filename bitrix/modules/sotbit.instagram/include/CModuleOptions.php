<?
IncludeModuleLangFile(__FILE__);
class CModuleOptions
{
    public $arCurOptionValues = array();
    
    private $module_id = '';
    private $arTabs = array();
    private $arGroups = array();
    private $arOptions = array();
    private $need_access_tab = false;
    
    public function CModuleOptions($module_id, $arTabs, $arGroups, $arOptions, $need_access_tab = false)
    {
        $this->module_id = $module_id;
        $this->arTabs = $arTabs;
        $this->arGroups = $arGroups;
        $this->arOptions = $arOptions;
        $this->need_access_tab = $need_access_tab;
        
        //if($need_access_tab)
            $this->arTabs[] = array("DIV" => "edit2", "TAB" => GetMessage("MAIN_TAB_RIGHTS"), "ICON" => "", "TITLE" => GetMessage("MAIN_TAB_TITLE_RIGHTS"));

        if(($_REQUEST['Apply'] && strlen($_REQUEST['Apply'])>0) || ($_REQUEST['Update'] && strlen($_REQUEST['Update'])>0) && check_bitrix_sessid())
            $this->SaveOptions();
        
        $this->GetCurOptionValues();
    }

    private function setPosition($type, $width, $height)
    {
        $w = $width/2;
        $h = $height/2;
        $style = "";
        $type = (string)$type;
        switch ($type)
        {
            case "left-top":
                $style = "position:fixed!important;left:0%!important;top:0%!important;";
                break;
            case "center-top":
                $style = "position:fixed!important;left:50%!important;top:0%!important;margin-left:-".$w."px!important;";
                break;
            case "right-top":
                $style = "position:fixed!important;left:auto!important;right:0%!important;top:0%!important;";
                break;

            case "left-middle":
                $style = "position:fixed!important;left:0%!important;top:50%!important;margin-top:-".$h."px!important;";
                break;
            case "center-middle":
                $style = "position:fixed!important;left:50%!important;top:50%!important;margin-left:-".$w."px!important;margin-top:-".$h."px!important;";
                break;
            case "right-middle":
                $style = "position:fixed!important;left:auto!important;right:0%!important;top:50%!important;margin-top:-".$h."px!important;";
                break;

            case "left-bottom":
                $style = "position:fixed!important;left:0%!important;top:auto!important;bottom:0%!important;margin-top:-".$h."px!important;";
                break;
            case "center-bottom":
                $style = "position:fixed!important;left:50%!important;top:auto!important;bottom:0%!important;margin-left:-".$w."px!important;";
                break;
            case "right-bottom":
                $style = "position:fixed!important;left:auto!important;right:0%!important;top:auto!important;bottom:0%!important;";
                break;
        }
        return $style;
    }
    
    private function SaveOptions()
    {   $module_id = "sotbit.instagram";
        foreach($this->arOptions as $opt => $arOptParams)
        {
            if($arOptParams['TYPE'] != 'CUSTOM')
            {
                $val = $_REQUEST[$opt];
    
                if($arOptParams['TYPE'] == 'CHECKBOX' && $val != 'Y')
                    $val = 'N';
                elseif(is_array($val))
                    $val = serialize($val);
                COption::SetOptionString($this->module_id, $opt, $val);


            }
        }

        if($_REQUEST["ACTIVE"]!="Y") unlink($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/themes/.default/".$module_id.".style.css");
        else{
            $arModules = array("IMAGE", "BORDER", "BACKGROUND", "WIDTH", "HEIGHT", "POSITION");
            foreach($arModules as $param)
            {
                $arParams[$param] = COption::GetOptionString($module_id, $param);
            }
            $style = ".bx-core-waitwindow{";

            if($arParams["IMAGE"])
            {
                $style.='background-image:url('.$arParams["IMAGE"].')!important;font-size:0px!important;background-position:50% 50%!important;padding:0px!important;';
            }
            if($arParams["BACKGROUND"])
            {
                $style.='background-color:'.$arParams["BACKGROUND"].'!important;';
            }else{
                $style.='background-color:transparent!important;';
            }
            if($arParams["BORDER"])
            {
                $style.='border-color:'.$arParams["BORDER"].'!important;';
            }
            if($arParams["IMAGE"])
            {
                $arParams["WIDTH"] = $size[0];
                $arParams["HEIGHT"] = $size[1];
                $size = getimagesize($_SERVER["DOCUMENT_ROOT"].$arParams["IMAGE"]);
                $style.="width:".$size[0]."px!important;";
                $style.="height:".$size[1]."px!important;";
            }else{
                $style.="width:".$arParams["WIDTH"]."px!important;";
                $style.="height:".$arParams["HEIGHT"]."px!important;";
            }
            $style.=self::setPosition(trim($arParams["POSITION"]), $arParams["WIDTH"], $arParams["HEIGHT"]);
            $style.="}";

            file_put_contents($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/themes/.default/".$module_id.".style.css", $style);
        }
    }
    
    private function GetCurOptionValues()
    {
        foreach($this->arOptions as $opt => $arOptParams)
        {
            
            if($arOptParams['TYPE'] != 'CUSTOM')
            {
                $this->arCurOptionValues[$opt] = COption::GetOptionString($this->module_id, $opt, $arOptParams['DEFAULT']); 
                if(in_array($arOptParams['TYPE'], array('MSELECT')))
                    $this->arCurOptionValues[$opt] = unserialize($this->arCurOptionValues[$opt]);
            }
        }
    }
    
    public function ShowHTML()
    {
        global $APPLICATION;
        $POST_RIGHT = $APPLICATION->GetGroupRight("sotbit.preloader");
        $arP = array();
        
        foreach($this->arGroups as $group_id => $group_params)
            $arP[$group_params['TAB']][$group_id] = array();
        
        if(is_array($this->arOptions))
        {
            foreach($this->arOptions as $option => $arOptParams)
            {
                $val = $this->arCurOptionValues[$option];

                if($arOptParams['SORT'] < 0 || !isset($arOptParams['SORT']))
                    $arOptParams['SORT'] = 0;
                
                $label = (isset($arOptParams['TITLE']) && $arOptParams['TITLE'] != '') ? $arOptParams['TITLE'] : '';
                $opt = htmlspecialchars($option);

                switch($arOptParams['TYPE'])
                {
                    case 'CHECKBOX':
                        $input = '<input type="checkbox" name="'.$opt.'" id="'.$opt.'" value="Y"'.($val == 'Y' ? ' checked' : '').' '.($arOptParams['REFRESH'] == 'Y' ? 'onclick="document.forms[\''.$this->module_id.'\'].submit();"' : '').' />';
                        break;
                    case 'TEXT':
                        if(!isset($arOptParams['COLS']))
                            $arOptParams['COLS'] = 25;
                        if(!isset($arOptParams['ROWS']))
                            $arOptParams['ROWS'] = 5;
                        $input = '<textarea rows="'.$type[1].'" cols="'.$arOptParams['COLS'].'" rows="'.$arOptParams['ROWS'].'" name="'.$opt.'">'.htmlspecialchars($val).'</textarea>';
                        if($arOptParams['REFRESH'] == 'Y')
                            $input .= '<input type="submit" name="refresh" value="OK" />';
                        break;
                    case 'SELECT':
                        $input = SelectBoxFromArray($opt, $arOptParams['VALUES'], $val, '', '', ($arOptParams['REFRESH'] == 'Y' ? true : false), ($arOptParams['REFRESH'] == 'Y' ? $this->module_id : ''));
                        if($arOptParams['REFRESH'] == 'Y')
                            $input .= '<input type="submit" name="refresh" value="OK" />';
                        break;
                    case 'MSELECT':
                        $input = SelectBoxMFromArray($opt.'[]', $arOptParams['VALUES'], $val);
                        if($arOptParams['REFRESH'] == 'Y')
                            $input .= '<input type="submit" name="refresh" value="OK" />';
                        break;
                    case 'COLORPICKER':
                        if(!isset($arOptParams['FIELD_SIZE']))
                            $arOptParams['FIELD_SIZE'] = 25;
                        ob_start();
                        echo     '<input id="__CP_PARAM_'.$opt.'" name="'.$opt.'" size="'.$arOptParams['FIELD_SIZE'].'" value="'.htmlspecialchars($val).'" type="text" style="float: left;" '.($arOptParams['FIELD_READONLY'] == 'Y' ? 'readonly' : '').' />
                                <script>
                                    function onSelect_'.$opt.'(color, objColorPicker)
                                    {
                                        var oInput = BX("__CP_PARAM_'.$opt.'");
                                        oInput.value = color;
                                    }
                                </script>';
                        $APPLICATION->IncludeComponent('bitrix:main.colorpicker', '', Array(
                                'SHOW_BUTTON' => 'Y',
                                'ID' => $opt,
                                'NAME' => GetMessage("sns.tools1c_choice_color"),
                                'ONSELECT' => 'onSelect_'.$opt
                            ), false
                        );
                        $input = ob_get_clean();
                        if($arOptParams['REFRESH'] == 'Y')
                            $input .= '<input type="submit" name="refresh" value="OK" />';
                        break;
                    case 'FILE':
                        if(!isset($arOptParams['FIELD_SIZE']))
                            $arOptParams['FIELD_SIZE'] = 25;
                        if(!isset($arOptParams['BUTTON_TEXT']))
                            $arOptParams['BUTTON_TEXT'] = '...';
                        CAdminFileDialog::ShowScript(Array(
                            'event' => 'BX_FD_'.$opt,
                            'arResultDest' => Array('FUNCTION_NAME' => 'BX_FD_ONRESULT_'.$opt),
                            'arPath' => Array(),
                            'select' => 'F',
                            'operation' => 'O',
                            'showUploadTab' => true,
                            'showAddToMenuTab' => false,
                            'fileFilter' => '',
                            'allowAllFiles' => true,
                            'SaveConfig' => true
                        ));
                        $input =     '<input id="__FD_PARAM_'.$opt.'" name="'.$opt.'" size="'.$arOptParams['FIELD_SIZE'].'" value="'.htmlspecialchars($val).'" type="text" style="float: left;" '.($arOptParams['FIELD_READONLY'] == 'Y' ? 'readonly' : '').' />
                                    <input value="'.$arOptParams['BUTTON_TEXT'].'" type="button" onclick="window.BX_FD_'.$opt.'();" />
                                    <script>
                                        setTimeout(function(){
                                            if (BX("bx_fd_input_'.strtolower($opt).'"))
                                                BX("bx_fd_input_'.strtolower($opt).'").onclick = window.BX_FD_'.$opt.';
                                        }, 200);
                                        window.BX_FD_ONRESULT_'.$opt.' = function(filename, filepath)
                                        {
                                            var oInput = BX("__FD_PARAM_'.$opt.'");
                                            if (typeof filename == "object")
                                                oInput.value = filename.src;
                                            else
                                                oInput.value = (filepath + "/" + filename).replace(/\/\//ig, \'/\');
                                        }
                                    </script>';
                        if($arOptParams['REFRESH'] == 'Y')
                            $input .= '<input type="submit" name="refresh" value="OK" />';
                            if($val)
                            {
                                $input.= '<br/><img src="'.htmlspecialchars($val).'" alt="">';
                            }
                        break;
                    case 'CUSTOM':
                        $input = $arOptParams['VALUE'];
                        break;
                    default:
                        if(!isset($arOptParams['SIZE']))
                            $arOptParams['SIZE'] = 25;
                        if(!isset($arOptParams['MAXLENGTH']))
                            $arOptParams['MAXLENGTH'] = 255;
                        $input = '<input type="'.($arOptParams['TYPE'] == 'INT' ? 'number' : 'text').'" size="'.$arOptParams['SIZE'].'" maxlength="'.$arOptParams['MAXLENGTH'].'" value="'.htmlspecialchars($val).'" name="'.htmlspecialchars($option).'" />';
                        if($arOptParams['REFRESH'] == 'Y')
                            $input .= '<input type="submit" name="refresh" value="OK" />';
                        break;
                }
                $notes = '';
                if(isset($arOptParams['NOTES']) && $arOptParams['NOTES'] != '')
                    $notes =     '<tr><td class="adm-detail-content-cell-l"></td><td class="adm-detail-content-cell-r">
                                        <div align="center" class="adm-info-message-wrap">
                                            <div style="float:left;margin:0px;" class="adm-info-message">
                                                '.$arOptParams['NOTES'].'                
                                            </div>
                                        </div>
                                    </td></tr>';

                $arP[$this->arGroups[$arOptParams['GROUP']]['TAB']][$arOptParams['GROUP']]['OPTIONS'][] = $label != '' ? '<tr><td style="vertical-align:top" width="50%">'.$label.'</td><td width="50%">'.$input.'</td></tr>'.$notes.' ' : '<tr><td colspan="2" >'.$input.'</td></tr>'.$notes.' ' ;
                $arP[$this->arGroups[$arOptParams['GROUP']]['TAB']][$arOptParams['GROUP']]['OPTIONS_SORT'][] = $arOptParams['SORT'];
            }

            $tabControl = new CAdminTabControl('tabControl', $this->arTabs);
            $tabControl->Begin();
            echo '<form name="'.$this->module_id.'" method="POST" action="'.$APPLICATION->GetCurPage().'?mid='.$this->module_id.'&lang='.LANGUAGE_ID.'" enctype="multipart/form-data">'.bitrix_sessid_post();

            foreach($arP as $tab => $groups)
            {
                $tabControl->BeginNextTab();

                foreach($groups as $group_id => $group)
                {
                    if(sizeof($group['OPTIONS_SORT']) > 0)
                    {
                        echo '<tr class="heading"><td colspan="2">'.$this->arGroups[$group_id]['TITLE'].'</td></tr>';
                        
                        array_multisort($group['OPTIONS_SORT'], $group['OPTIONS']);
                        foreach($group['OPTIONS'] as $opt)
                            echo $opt;
                    }
                }
            }

            //if($this->need_access_tab)
            {
                    $tabControl->BeginNextTab();
                    $module_id = "sotbit.preloader";
                    require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");?>
                    <?$tabControl->Buttons();?>
	                <input <?if ($POST_RIGHT<"W") echo "disabled" ?> type="submit" name="Update" value="<?=GetMessage("MAIN_SAVE")?>" title="<?=GetMessage("MAIN_OPT_SAVE_TITLE")?>">
	                <input <?if ($POST_RIGHT<"W") echo "disabled" ?> type="submit" name="Apply" id="sotbitApply" value="<?=GetMessage("MAIN_OPT_APPLY")?>" title="<?=GetMessage("MAIN_OPT_APPLY_TITLE")?>">
	                <?if(strlen($_REQUEST["back_url_settings"])>0):?>
		                <input <?if ($POST_RIGHT<"W") echo "disabled" ?> type="button" name="Cancel" value="<?=GetMessage("MAIN_OPT_CANCEL")?>" title="<?=GetMessage("MAIN_OPT_CANCEL_TITLE")?>" onclick="window.location='<?echo htmlspecialchars(CUtil::addslashes($_REQUEST["back_url_settings"]))?>'">
		                <input type="hidden" name="back_url_settings" value="<?=htmlspecialchars($_REQUEST["back_url_settings"])?>">
	                <?endif?>
	                <input <?if ($POST_RIGHT<"W") echo "disabled" ?> type="submit" name="RestoreDefaults" title="<?echo GetMessage("MAIN_HINT_RESTORE_DEFAULTS")?>" OnClick="return confirm('<?echo AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING"))?>')" value="<?echo GetMessage("MAIN_RESTORE_DEFAULTS")?>">

                    </form>
                    <?$tabControl->End();?>
                    <?
            }

            /*$tabControl->Buttons();

            echo     '<input type="hidden" name="update" value="Y" />
                    <input type="submit" name="save" value="' .GetMessage("sns.tools1c_submit_save"). '" />
                    <input type="reset" name="reset" value="' .GetMessage("sns.tools1c_submit_cancel"). '" />
                    </form>';

            $tabControl->End();*/
        }
    }
}
?>