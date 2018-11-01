<?php
class sestHL extends sest{
	
	/**
	 * getDataHL() - get all data from highload block
	 * $HL_ID - id
	 */
	public static function getDataHL( $HL_ID ){
		CModule::IncludeModule("highloadblock"); 
							    
		$hlbl = $HL_ID; 
		$hlblock = Bitrix\Highloadblock\HighloadBlockTable::getById($hlbl)->fetch(); 
		$entity = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock); 
		$entity_data_class = $entity->getDataClass();   
					    
		$rsData = $entity_data_class::getList(array(
			"select" => array("*"),
			"order" => array("ID" => "ASC")					      
		));
		$res = array();
		while($arData = $rsData->Fetch()){
			$res[] = $arData;   	
		}
		
		return $res;
	}
	
	
	/**
	 * getByIdHL() - get data from HL from ID
	 * $HL_ID - id HL
	 * $idElement - id element
	 */
	public static function getByIdHL( $HL_ID, $idElement, $field = false, $valField ){
		CModule::IncludeModule("highloadblock"); 
							    
		$hlbl = $HL_ID; 
		$hlblock = Bitrix\Highloadblock\HighloadBlockTable::getById($hlbl)->fetch(); 
		if (!empty($hlblock)){
			$entity = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock); 
			$entity_data_class = $entity->getDataClass(); 
			$row = $entity_data_class::getById($idElement)->fetch();
			if(!empty($row)){
				if($field){
					return $row[$valField];
				}else{
					return $row;
				}				
			}else{
				return false;
			}
		}
	}
	
	
	
	
	
} //end HL_sest class   
?>
