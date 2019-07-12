<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

class WizDescription extends CWizardStep
{
    function InitStep()
    {
        $this->SetStepID("wiz_correction_description");
        $this->SetTitle(GetMessage("V1RT_WIZ_CORRECTION_DESCRIPTION_TITLE"));
        $this->SetSubTitle(GetMessage("V1RT_WIZ_CORRECTION_DESCRIPTION_SUBTITLE"));
        $this->SetNextStep("wiz_correction_step_1");
        $this->SetCancelStep("wiz_correction_cancel");
        $wizard =& $this->GetWizard();
    }

    function ShowStep()
    {
        $this->content .= GetMessage("V1RT_WIZ_CORRECTION_DESCRIPTION");
        $wizard =& $this->GetWizard();
        $formName = $wizard->GetFormName();
        $nextButton = $wizard->GetNextButtonID();
    }
}

class WizCorrectionStep1 extends CWizardStep
{
    function InitStep()
    {
        $this->SetStepID("wiz_correction_step_1");
        $this->SetTitle(GetMessage("V1RT_WIZ_CORRECTION_STEP_1_TITLE"));
        $this->SetSubTitle(GetMessage("V1RT_WIZ_CORRECTION_STEP_1_SUBTITLE"));
        $this->SetPrevStep("wiz_correction_description");
        $this->SetNextStep("wiz_correction_step_2");
        $this->SetCancelStep("wiz_correction_cancel");
        $wizard =& $this->GetWizard();
    }

    function ShowStep()
    {
        $arTpl = array();
        $wizard =& $this->GetWizard();
        $this->content .= '<table class="wizard-data-table">';
        /**
         * Смотрим демо данные
         */
        if(COption::GetOptionString("v1rt.personal", "v1rt_personal_demo_data") == "Y")
        {
            $this->content .= '<tr><td>';
            $this->content .= $this->ShowCheckboxField("delete_demo_data", "Y", array("id" => "delete_demo_data")).'<label for="delete_demo_data">'.GetMessage("V1RT_WIZ_DELETE_STEP_1_DEMO_DATA").'</label>';
            $this->content .= '</td></tr>';
        }
        /**
         * Смотрим есть ли папка files в корне модуля, если есть то даем галочку для ее удаления
         */
        if(file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/v1rt.personal/files/"))
        {
            $this->content .= '<tr><td>';
            $this->content .= $this->ShowCheckboxField("folder_files", "Y", array("id" => "folder_files")).'<label for="folder_files">'.GetMessage("V1RT_WIZ_CORRECTION_STEP_1_FOLDER_FILES").'</label>';
            $this->content .= '</td></tr>';
        }
        /**
         * Удалим папку bitrix из /bitrix/modules/v1rt.personal/install/bitrix/
         */
        if(file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/v1rt.personal/install/bitrix/"))
        {
            $this->content .= '<tr><td>';
            $this->content .= $this->ShowCheckboxField("folder_bitrix", "Y", array("id" => "folder_bitrix")).'<label for="folder_bitrix">'.GetMessage("V1RT_WIZ_CORRECTION_STEP_1_FOLDER_BITRIX").'</label>';
            $this->content .= '</td></tr>';
        }
        /**
         * Удалим папку classes из /bitrix/modules/v1rt.personal/install/classes/
         */
        if(file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/v1rt.personal/install/classes/"))
        {
            $this->content .= '<tr><td>';
            $this->content .= $this->ShowCheckboxField("folder_classes", "Y", array("id" => "folder_classes")).'<label for="folder_classes">'.GetMessage("V1RT_WIZ_CORRECTION_STEP_1_FOLDER_CLASSES").'</label>';
            $this->content .= '</td></tr>';
        }
        /**
         * Обработаем ситуацию, когда уже выбран наш шаблон
         * В данной ситуации смотрим, есть ли в папке /bitrix/templates/ наши шаблоны которые используются
         */
        $tpl = array();
        $defaultTplV1 = array("personal_green", "personal_blue");
        $defaultTplV2 = array("personal_v2_black", "personal_v2_green", "personal_v2_yellow");
        $sites = CSite::GetList($by = "sort", $order = "desc", array());
        while($site = $sites->Fetch())
        {
            $rsTemplates = CSite::GetTemplateList($site["LID"]);
            while($arTemplate = $rsTemplates->Fetch())
               $tpl[] = $arTemplate["TEMPLATE"];
        }
        if(count($tpl))
        {
            $tpl = array_unique($tpl);
            foreach($tpl as $nameTemplateSite)
            {
                if(array_search(substr($nameTemplateSite, 0, 17), $defaultTplV2) !== false || array_search(substr($nameTemplateSite, 0, 18), $defaultTplV2) !== false)
                {
                    if(file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/templates/".$nameTemplateSite."/"))
                    {
                        $arTpl[$nameTemplateSite] = "Y";
                        $this->content .= '<tr><td>';
                        $this->content .= $this->ShowCheckboxField("default_templates_set[$nameTemplateSite]", "Y", array("id" => "default_templates_set_$nameTemplateSite")).'<label for="default_templates_set_'.$nameTemplateSite.'"><strong>'.$nameTemplateSite.'</strong>'.GetMessage("V1RT_WIZ_CORRECTION_STEP_1_DEFAULT_TEMPLATES_SET").'</label>';
                        $this->content .= '</td></tr>';
                    }
                }
                if(array_search(substr($nameTemplateSite, 0, 13), $defaultTplV1) !== false || array_search(substr($nameTemplateSite, 0, 14), $defaultTplV1) !== false)
                {
                    if(file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/templates/".$nameTemplateSite."/"))
                    {
                        $arTpl[$nameTemplateSite] = "Y";
                        $this->content .= '<tr><td>';
                        $this->content .= $this->ShowCheckboxField("default_templates_set[$nameTemplateSite]", "Y", array("id" => "default_templates_set_$nameTemplateSite")).'<label for="default_templates_set_'.$nameTemplateSite.'"><strong>'.$nameTemplateSite.'</strong>'.GetMessage("V1RT_WIZ_CORRECTION_STEP_1_DEFAULT_TEMPLATES_SET").'</label>';
                        $this->content .= '</td></tr>';
                    }
                }
            }
        }
        /**
         * Посмотри есть ли в папке /bitrix/templates/ наши шаблоны, не учтенные прошлым методом
         */
        foreach($defaultTplV1 as $t)
        {
            if(file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/templates/".$t."/"))
            {
                if($arTpl[$t] != "Y")
                {
                    $this->content .= '<tr><td>';
                    $this->content .= $this->ShowCheckboxField("default_templates_unset[$t]", "Y", array("id" => "default_templates_unset_$t")).'<label for="default_templates_unset_'.$t.'"><strong>'.$t.'</strong>'.GetMessage("V1RT_WIZ_CORRECTION_STEP_1_DEFAULT_TEMPLATES_UNSET").'</label>';
                    $this->content .= '</td></tr>';
                }
            }
        }
        unset($t);
        foreach($defaultTplV2 as $t)
        {
            if(file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/templates/".$t."/"))
            {
                if($arTpl[$t] != "Y")
                {
                    $this->content .= '<tr><td>';
                    $this->content .= $this->ShowCheckboxField("default_templates_unset[$t]", "Y", array("id" => "default_templates_unset_$t")).'<label for="default_templates_unset_'.$t.'"><strong>'.$t.'</strong>'.GetMessage("V1RT_WIZ_CORRECTION_STEP_1_DEFAULT_TEMPLATES_UNSET").'</label>';
                    $this->content .= '</td></tr>';
                }
            }
        }
        $this->content .= '</table>';
    }
}

class WizCorrectionStep2 extends CWizardStep
{
    function InitStep()
    {
        $this->SetStepID("wiz_correction_step_2");
        $this->SetTitle(GetMessage("V1RT_WIZ_CORRECTION_STEP_2_TITLE"));
        $this->SetSubTitle(GetMessage("V1RT_WIZ_CORRECTION_STEP_2_SUBTITLE"));
        $this->SetPrevStep("wiz_correction_step_1");
        $this->SetNextStep("wiz_correction_finish");
        $this->SetNextCaption(GetMessage("V1RT_WIZ_CORRECTION_STEP_2_DELETE"));
        $this->SetCancelStep("wiz_correction_cancel");
    }

    function OnPostForm()
    {
        $wizard =& $this->GetWizard();
        if(!$wizard->IsNextButtonClick())
            return;
        
        if($wizard->GetVar("folder_files") == "Y")
        {
            if(!DeleteDirFilesEx("/bitrix/modules/v1rt.personal/files"))
                $wizard->SetError(GetMessage("V1RT_WIZ_CORRECTION_STEP_2_FOLDER_FILES_ERROR"));
        }
        
        if($wizard->GetVar("folder_bitrix") == "Y")
        {
            if(!DeleteDirFilesEx("/bitrix/modules/v1rt.personal/install/bitrix"))
                $wizard->SetError(GetMessage("V1RT_WIZ_CORRECTION_STEP_2_FOLDER_BITRIX_ERROR"));
        }
        
        if($wizard->GetVar("folder_classes") == "Y")
        {
            if(!DeleteDirFilesEx("/bitrix/modules/v1rt.personal/install/classes"))
                $wizard->SetError(GetMessage("V1RT_WIZ_CORRECTION_STEP_2_FOLDER_CLASSES_ERROR"));
        }
        
        $default_templates_set = $wizard->GetVar("default_templates_set");
        $default_templates_unset = $wizard->GetVar("default_templates_unset");
        
        if(count($default_templates_set))
        {
            foreach($default_templates_set as $tpl=>$dts)
            {
                if($dts == "Y")
                    if(!DeleteDirFilesEx("/bitrix/templates/".$tpl))
                        $this->SetError(GetMessage("V1RT_WIZ_CORRECTION_STEP_2_DELETE_TEMPLATE_ERROR", array("#TEMPLATE#" => $tpl)));
            }
        }
        
        if(count($default_templates_unset))
        {
            foreach($default_templates_unset as $tpl=>$dtus)
            {
                if($dtus == "Y")
                    if(!DeleteDirFilesEx("/bitrix/templates/".$tpl))
                        $this->SetError(GetMessage("V1RT_WIZ_CORRECTION_STEP_2_DELETE_TEMPLATE_ERROR", array("#TEMPLATE#" => $tpl)));
            }
        }
        
        /**
         * Удаление демо данных
         */
        if($wizard->GetVar("delete_demo_data") == "Y" && COption::GetOptionString("v1rt.personal", "v1rt_personal_demo_data") == "Y")
        {
            $arRecords = array();
            CModule::IncludeModule("iblock");
            CModule::IncludeModule("fileman");
            CMedialib::Init();
            $SITE_ID_INSTALL = COption::GetOptionString("v1rt.personal", "v1rt_personal_site_id");
            //Удаление записей из блога
            $res = CIBlock::GetList(array(), array('TYPE' => 'personal', 'SITE_ID' => $SITE_ID_INSTALL, 'ACTIVE' => 'Y', "CODE" => 'news_'.$SITE_ID_INSTALL), false);
            if($ar_res = $res->Fetch())
            {
                if($ar_res["ID"] > 0)
                {
                    $arSelect = Array("ID");
                    $arFilter = Array("IBLOCK_ID" => $ar_res["ID"], "ACTIVE" => "Y", "NAME" => GetMessage("V1RT_DEMO_DELETE_1"));
                    $res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>1), $arSelect);
                    while($ob = $res->GetNextElement())
                    {
                        $arFields = $ob->GetFields();
                        $arRecords[] = $arFields["ID"];
                        CIBlockElement::Delete($arFields["ID"]);
                    }
                    
                    $arSelect = Array("ID");
                    $arFilter = Array("IBLOCK_ID" => $ar_res["ID"], "ACTIVE" => "Y", "NAME" => GetMessage("V1RT_DEMO_DELETE_2"));
                    $res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>1), $arSelect);
                    while($ob = $res->GetNextElement())
                    {
                        $arFields = $ob->GetFields();
                        $arRecords[] = $arFields["ID"];
                        CIBlockElement::Delete($arFields["ID"]);
                    }
                }
            }
            //Удаление комментариев
            $res = CIBlock::GetList(array(), array('TYPE' => 'personal', 'SITE_ID' => $SITE_ID_INSTALL, 'ACTIVE' => 'Y', "CODE" => 'comments_'.$SITE_ID_INSTALL), false);
            if($ar_res = $res->Fetch())
            {
                if($ar_res["ID"] > 0)
                {
                    if(count($arRecords))
                    {
                        foreach($arRecords as $record)
                        {
                            if($record > 0 && is_numeric($record))
                            {
                                $arSelect = Array("ID");
                                $arFilter = Array("IBLOCK_ID" => $ar_res["ID"], "ACTIVE" => "Y", "PROPERTY_ID_RECORD_VALUE" => $record);
                                $res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>1), $arSelect);
                                while($ob = $res->GetNextElement())
                                {
                                    $arFields = $ob->GetFields();
                                    CIBlockElement::Delete($arFields["ID"]);
                                }
                            }
                        }
                    }
                }
            }
            //Удаление фотогалереи и слайдера
            $ar = CMedialibCollection::GetList(array('arFilter' => array('NAME' => GetMessage("V1RT_DEMO_DELETE_5"))));
            if($ar[0]["ID"] > 0)
            {
                $r = CMedialibItem::GetList(array("arCollections" => array($ar[0]["ID"])));
                $c = count($r);
                for($i = 0; $i <= $c - 1; $i++)
                    CMedialibItem::Delete($r[$i]["ID"], true, $ar[0]["ID"]);
            }
            
            $ar = CMedialibCollection::GetList(array('arFilter' => array('NAME' => GetMessage("V1RT_DEMO_DELETE_6"))));
            if($ar[0]["ID"] > 0)
            {
                $ar = CMedialibCollection::GetList(array('arFilter' => array('PARENT_ID' => $ar[0]["ID"])));
                $c = count($ar);
                for($i = 0; $i <= $c - 1; $i++)
                    CMedialibCollection::Delete($ar[$i]["ID"]);
            }
            
            $rsSites = CSite::GetByID($SITE_ID_INSTALL);
            $arSite = $rsSites->Fetch();
            $text = '<?
                    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
                    $APPLICATION->SetTitle("'.GetMessage("V1RT_DEMO_DELETE_7").'");
                    ?>
                    
                    <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>';
            file_put_contents($_SERVER["DOCUMENT_ROOT"].$arSite["DIR"]."index.php", $text);
            
            COption::SetOptionString("v1rt.personal", "v1rt_personal_demo_data", "N");
            if(COption::GetOptionString("v1rt.personal", "v1rt_personal_type_header") == 0)
                COption::SetOptionString("v1rt.personal", "v1rt_personal_type_header", 2);
        }
    }

    function ShowStep()
    {
        $this->content .= GetMessage("V1RT_WIZ_CORRECTION_STEP_2_SETTINGS")."<br /><br />";
        $wizard =& $this->GetWizard();
        
        if($wizard->GetVar("delete_demo_data") == "Y")
            $this->content .= '<p>'.GetMessage("V1RT_WIZ_DELETE_STEP_2_DELETE_DEMO_DATA")."</p>";
        
        if($wizard->GetVar("folder_files") == "Y")
            $this->content .= '<p>'.GetMessage("V1RT_WIZ_CORRECTION_STEP_2_DELETE_FOLDER_FILES")."</p>";
        
        if($wizard->GetVar("folder_bitrix") == "Y")
            $this->content .= '<p>'.GetMessage("V1RT_WIZ_CORRECTION_STEP_2_DELETE_FOLDER_BITRIX")."</p>";
        
        if($wizard->GetVar("folder_classes") == "Y")
            $this->content .= '<p>'.GetMessage("V1RT_WIZ_CORRECTION_STEP_2_DELETE_FOLDER_CLASSES")."</p>";
        
        $default_templates_set = $wizard->GetVar("default_templates_set");
        $default_templates_unset = $wizard->GetVar("default_templates_unset");
        
        if(count($default_templates_set))
        {
            foreach($default_templates_set as $tpl=>$dts)
            {
                if($dts == "Y")
                    $this->content .= '<p>'.GetMessage("V1RT_WIZ_CORRECTION_STEP_2_DELETE_TEMPLATE", array("#TEMPLATE#" => $tpl))."</p>";
            }
        }
        
        if(count($default_templates_unset))
        {
            foreach($default_templates_unset as $tpl=>$dtus)
            {
                if($dtus == "Y")
                    $this->content .= '<p>'.GetMessage("V1RT_WIZ_CORRECTION_STEP_2_DELETE_TEMPLATE", array("#TEMPLATE#" => $tpl))."</p>";
            }
        }
    }
}

class WizFinish extends CWizardStep
{
    function InitStep()
    {
        $this->SetStepID("wiz_correction_finish");
        $this->SetTitle(GetMessage("V1RT_WIZ_CORRECTION_FINISH_TITLE"));
        $this->SetCancelStep("wiz_correction_finish");
        $this->SetCancelCaption(GetMessage("V1RT_WIZ_CORRECTION_FINISH_SUCCESS"));
    }

    function ShowStep()
    {
        $this->content .= '<p>'.GetMessage("V1RT_WIZ_CORRECTION_FINISH").'</p>';
        $this->content .= '<p><a href="/bitrix/admin/wizard_install.php?wizardName=v1rt.personal:v1rt:personal&'.bitrix_sessid_get().'" target="_blank">'.GetMessage("V1RT_WIZ_CORRECTION_FINISH_START_SETUP").'</a></p>';
    }
}

class WizCancelStep extends CWizardStep
{
    function InitStep()
    {
        $this->SetTitle(GetMessage("V1RT_WIZ_CORRECTION_CANCEL_TITLE"));
        $this->SetStepID("wiz_correction_cancel");
        $this->SetCancelStep("wiz_correction_cancel");
        $this->SetCancelCaption(GetMessage("V1RT_WIZ_CORRECTION_CANCEL_CLOSE"));
    }

    function ShowStep()
    {
        $this->content .= GetMessage("V1RT_WIZ_CORRECTION_CANCEL");
    }
}
?>