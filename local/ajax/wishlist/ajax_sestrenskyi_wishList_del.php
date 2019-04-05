<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
define('ITUA_WISH_LIST', 4);
global $USER;

use Bitrix\Highloadblock as HL; 
use Bitrix\Main\Entity; 
use Bitrix\Main\Loader;
use Bitrix\Main\Application;
use Bitrix\Main\SystemException; 


Loader::includeModule('highloadblock');
$request = Application::getInstance()->getContext()->getRequest(); 

$idProduct  = (int)$request->getPost('idProduct');
$idUser 	= (int)$request->getPost('idUser'); 

if( $idProduct &&  $idUser ){

	$hlblock = HL\HighloadBlockTable::getById(ITUA_WISH_LIST)->fetch(); 
	$entity  = HL\HighloadBlockTable::compileEntity($hlblock); 
	$entity_data_class = $entity->getDataClass();  
	$rsData = $entity_data_class::getList(array(
	   "select" => array("*"),
	   "order" => array("ID" => "ASC"),
	   "filter" => array('UF_USER' => $idUser,
	                     'UF_PRODUCT' => $idProduct)
	));
	while ($arData = $rsData->Fetch()) {
		$entity_data_class::Delete($arData['ID']); 
	}
}else{
	 throw new SystemException("Error with parameters - idProduct and idUser"); 
}





