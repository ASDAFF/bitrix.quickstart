<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();
if (!IsModuleInstalled("highloadblock") && file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/highloadblock/"))
{
	$installFile = $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/highloadblock/install/index.php";
	if (!file_exists($installFile))
		return false;

	include_once($installFile);

	$moduleIdTmp = str_replace(".", "_", "highloadblock");
	if (!class_exists($moduleIdTmp))
		return false;

	$module = new $moduleIdTmp;
	if (!$module->InstallDB())
		return false;
	$module->InstallEvents();
	if (!$module->InstallFiles())
		return false;
}

if (!CModule::IncludeModule("highloadblock"))
	return;

WizardServices::IncludeServiceLang("ref_my.php", LANGUAGE_ID);

use Bitrix\Highloadblock as HL;

/***Create hiload***/
//read file json list hiload 
 $hlFile = file_get_contents($_SERVER['DOCUMENT_ROOT'].WIZARD_SERVICE_RELATIVE_PATH.'/hl.json');
if(!$hlFile)
{
    echo WIZARD_SERVICE_RELATIVE_PATH;
    echo "No file json Hiload";
    die();
}
$jsonHl = json_decode($hlFile);
if(!$jsonHl)
{
    echo "No decode json file";
    die();
}
foreach($jsonHl as $key=>$hl)
{
    $dbHblock = HL\HighloadBlockTable::getList(
	array(
		"filter" => array("NAME" => $hl->NAME)
	));
    
    if (!$dbHblock->Fetch())
    {
        $data = array(
		'NAME' => $hl->NAME,
		'TABLE_NAME' => $hl->TABLE_NAME,
	   );        
    $result = HL\HighloadBlockTable::add($data);
	$ID = $result->getId();
	
	$hldata = HL\HighloadBlockTable::getById($ID)->fetch();
	$hlentity = HL\HighloadBlockTable::compileEntity($hldata);
    
        if(isset($hl->FIELD) && count($hl->FIELD))
        {
            $obUserField  = new CUserTypeEntity;
            foreach($hl->FIELD as $hlFeild)
            {            
                $arUserFields = array (
        			'ENTITY_ID' => "HLBLOCK_".$ID,
        			'FIELD_NAME' => $hlFeild->FIELD_NAME,
        			'USER_TYPE_ID' => $hlFeild->USER_TYPE_ID,
        			'XML_ID' => $hlFeild->XML_ID,
        			'SORT' => $hlFeild->SORT,
        			'MULTIPLE' => $hlFeild->MULTIPLE,
        			'MANDATORY' => $hlFeild->MANDATORY,
        			'SHOW_FILTER' => $hlFeild->SHOW_FILTER,
        			'SHOW_IN_LIST' => $hlFeild->SHOW_IN_LIST,
        			'EDIT_IN_LIST' => $hlFeild->EDIT_IN_LIST,
        			'IS_SEARCHABLE' => $hlFeild->IS_SEARCHABLE,
                    "EDIT_FORM_LABEL" =>  GetMessage($hlFeild->EDIT_FORM_LABEL),
                    "LIST_COLUMN_LABEL" => GetMessage($hlFeild->LIST_COLUMN_LABEL),
                    "LIST_FILTER_LABEL" => GetMessage($hlFeild->LIST_FILTER_LABEL)
                    );    
                    $ID_USER_FIELD = $obUserField->Add($arUserFields);                                    
            }    	       
            
        }
    }
}

