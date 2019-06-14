<?php

if(LANG_CHARSET){ header('Content-Type: text/html; charset='.LANG_CHARSET); }

IncludeModuleLangFile(__FILE__);
$module_id = "sheepla.delivery";
$SHEEPLA_RIGHT = $APPLICATION->GetGroupRight($module_id);

if (!($SHEEPLA_RIGHT >= "R"))
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));


CModule::IncludeModule("sale");
CModule::IncludeModule("sheepla.delivery");

class SheeplaOptions {
    private $model = null;
    private $fields = array('adminApiKey', 
                            'publicApiKey', 
                            'apiUrl', 
                            'jsUrl', 
                            'cssUrl', 
                            'syncAll',
                            'checkout',
                            'jQDeliverySelector',
                            'jQDeliveryCitySelector',
                            'jQDeliverySelectorShort',
                            'jQLocationSelector',
                            'jQLabelSelector',
                            'adminOrderAddUrl',
                            'adminOrderEditUrl',
                            'adminOrderViewUrl',
                            'orderViewSheeplaSelector',
                            'adminOrderjQSelector',
                            'adminOrderjQSelectorShort',
                            'adminOrderjQLocationSelector',
                            'adminOrderjQLabelSelector',
                            );

    public function __construct($model) {
        $this->model = $model;
    }

    public function saveSettings() {
        $config = array();        
        foreach ($this->fields as $field) {
            if ($_POST[$field]!='') {
                $config[$field] = $_POST[$field];
            }
        }
        $this->model->SetConfig($config);
        
        $carriersData = array();
        foreach ($_POST as $key => $value) {
            if (strpos($key,'sheepla_')>0) {
                $carriersData[$key] = $value;
            }
        }
        $this->model->SetSheeplaCarriers($carriersData);        
        self::drawSettingsForm();
    }

    public function drawSettingsForm() {   
        $isSheeplaAdmin = false;
        $sheeplaSettings = CSheepla::getConfig();        
        if(($_GET['SheeplaKey']==$sheeplaSettings['adminApiKey'])&&($sheeplaSettings['configOk'])){
            $isSheeplaAdmin = true;
        }        
        $sheeplaLog =  str_replace("<? exit(); ?>","", $this->model->ReadSheeplaLog());
        
        $sheeplaTemplates = array();        
        if($sheeplaSettings['configOk']=='1'){
            $sheeplaTemplates = $this->model->GetSheeplaTemplates();    
        } 
        $sheeplaCarriers = array();
        $sheeplaCarriers = $this->model->GetSheeplaCarriers();
        include('templates/drawSettingsForm.php');
    }
}

$SO = new SheeplaOptions(new CSheepla());
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $SO->saveSettings();
} else {
    $SO->drawSettingsForm();
}


?>