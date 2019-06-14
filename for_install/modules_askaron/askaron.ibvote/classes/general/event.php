<?
##############################################
# Askaron.Ibvote module                      #
# Copyright (c) 2011 Askaron Systems         #
# http://askaron.ru                          #
# mailto:mail@askaron.ru                     #
##############################################


IncludeModuleLangFile(__FILE__); 

class CAllAskaronIbvoteEvent
{
	function err_mess()
	{
		$module_id = "askaron.ibvote";
		return "<br>Module: ".$module_id."<br>Class: CAllAskaronIbvoteEvent<br>File: ".__FILE__;
	}

	//check fields before writing
	function CheckFields(&$arFields)
	{
		//global $DB;
		//$this->LAST_ERROR = "";
		//$aMsg = array();

		
		if ( isset( $arFields["ID"] ) )
			unset($arFields["ID"]);

		if ( isset( $arFields["ANSWER"] ) )
			$arFields["ANSWER"] = intval( $arFields["ANSWER"] );
		
		
		//if(strlen( $arFields["SUCCESS_EXEC"] ) > 1)
		//	$aMsg[] = array("id"=>"NAME", "text"=>"Wrong SUCCESS_EXEC");

		//if (isset($arFields["DUPLICATE"]))
		//{
		//	$arFields["DUPLICATE"] = ($arFields["DUPLICATE"] == "N" ? "N" : "Y");
		//}

		//if(!empty($aMsg))
		//{
		//	$e = new CAdminException($aMsg);
		//	$GLOBALS["APPLICATION"]->ThrowException($e);
		//	$this->LAST_ERROR = $e->GetString();
		//	return false;
		//}
		return true;
	}
	
	function GetByID($ID)
	{		
		$ID = intval($ID);
		$res = CAskaronIbvoteEvent::GetList( array(), array("ID" => $ID), false);
		return $res;		
	}
}
?>
