<?
header('Content-Type: text/html; charset=utf-8');

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/include.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/prolog.php");
CModule::IncludeModule("sale");

include('sheepla_db.php');



class SheeplaAjaxController{
    protected function StorePostomat($myPost = ''){        
        if( (!$myPost['carrierId']|$myPost['carrierId']=='') || 
            (!$myPost['user']|$myPost['user']=='') || 
            (!$myPost['carrier']|$myPost['carrier']=='') || 
            (!$myPost['POP']|$myPost['POP']=='') 
            ){
            return false;
        }
        $data = explode("|", $myPost['POP']); 
        for($i = 0; $i < sizeof($data); $i++){
            $temp = explode("=", $data[$i]);
            if($temp[0])
                $pop[$temp[0]] = $temp[1];
        }        
        CSheeplaDb::StorePostomatToDb($myPost, $pop);        
    }
	

    protected function GetUserCartCity($myPost){        
        $arLocation = CSaleLocation::GetByID((int)$myPost['LOCATION'], 'ru');
        echo $arLocation['CITY_NAME'];
    }   
    /**
	 * Main controller function
	 * */
    public function Run()
    {
        if (isset($_POST) && !empty($_POST['action'])){            
            foreach($_POST as $key => $value){
                $myPost[$key] = mysql_escape_string(mb_convert_encoding($value, 'cp1251', 'auto') );
            }       
    			switch ($_POST['action']) {
    				case 'StorePostomat':
                        self::StorePostomat($myPost);
                    break;
                    case 'GetUserCartCity':
                        self::GetUserCartCity($myPost);
                    break;
                    case 'ResendCreateOrder':
                        $sheeplaConfig = CSheepla::getConfig();                        
                        $data = array();
                        if(($myPost['SheeplaKey']==$sheeplaConfig['adminApiKey'])&&(isset($myPost['orderId']))){
                            $sheepla_model = new SheeplaProxyDataModel();
                            if($sheepla_model->markOrderToResent((int)$myPost['orderId'])){
                                echo 'true';    
                            }else{
                                echo 'false';
                            }
                                
                        }else{
                            echo 'false';
                        }
                        
                    break;
                }
        }
     }
	/**
	 * Small helper for using class without creating new instances
	 * */
    public function Create()
	{
		return new self;
	}
}


$APPLICATION->RestartBuffer();

SheeplaAjaxController::Create()->Run();


?>