<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if (!CModule::IncludeModule("highloadblock"))
	return;

//adding rows
WizardServices::IncludeServiceLang("references.php", LANGUAGE_ID);

use Bitrix\Highloadblock as HL;
global $USER_FIELD_MANAGER;

//read file json list hiload 
 $hlFile = file_get_contents($_SERVER['DOCUMENT_ROOT'].WIZARD_SERVICE_RELATIVE_PATH.'/hl_data.json');
if(!$hlFile)
{
    echo WIZARD_SERVICE_RELATIVE_PATH;
    echo "No file json Hiload";
    die();
}
$jsonHl = Bitrix\Main\Web\Json::decode($hlFile);
if(!$jsonHl)
{
    echo "No decode json file";
    die();
}

foreach($jsonHl as $key=>$hl)
{
    
    $dbHblock = HL\HighloadBlockTable::getList(
	array(
		"filter" => array("NAME" => $hl['NAME'])
	))->Fetch();
    
     if ($dbHblock)
    {        
        $hldata = HL\HighloadBlockTable::getById($dbHblock["ID"])->fetch();
        $hlentity = HL\HighloadBlockTable::compileEntity($hldata);

	    $entity_data_class = $hlentity->getDataClass();      
       if(isset($hl['DATA']) && count($hl['DATA'])>0)
       {                 
    	foreach($hl['DATA'] as $data)
           {	               
                $arFields = array();
                foreach($data as $key=>$val)
                {                 
                    if(is_array($val))
                    {
                       $val['tmp_name'] = WIZARD_ABSOLUTE_PATH."/site/services/iblock".$val['tmp_name'];                    
                    }                                
                    if($key != "ID")
                    $arFields[$key] = $val;                                                
                }                       
                $USER_FIELD_MANAGER->EditFormAddFields('HLBLOCK_'.$hldata['ID'], $arFields);
    	        $USER_FIELD_MANAGER->checkFields('HLBLOCK_'.$hldata['ID'], null, $arFields);
    
                $result = $entity_data_class::add($arFields);           
            }        	       
       } 
    }
}
