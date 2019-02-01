<?
class sqlSdekOrders
{
	public function toLog($wat,$sign){sdekHelper::toLog($wat,$sign);}
	private static $tableName = "ipol_sdek";
	public static function Add($Data)
    {
        // = $Data = format:
		// PARAMS - ALL INFO
		// ORDER_ID - corresponding order
		// STATUS - response from iml
		// MESSAGE - info from server
		// OK - 0 / 1 - was confirmed
		// UPTIME - время добавления
		
		global $DB;
        
		if(!$Data['STATUS'])
			$Data['STATUS']='NEW';
		if($Data['STATUS']=='NEW')
			$Data['MESSAGE']='';
		if(is_array($Data['PARAMS'])) {
			$Data['PARAMS'] = serialize($Data['PARAMS']);
		}
		
		$Data['UPTIME']=mktime();
			
		$rec = self::CheckRecord($Data['ORDER_ID'],$Data['SOURCE']);
		if($rec)
		{
			$strUpdate = $DB->PrepareUpdate(self::$tableName, $Data);
			$strSql = "UPDATE ".self::$tableName." SET ".$strUpdate." WHERE ID=".$rec['ID'];
			$DB->Query($strSql, false, $err_mess.__LINE__);
		}
		else
		{
			$arInsert = $DB->PrepareInsert(self::$tableName, $Data);
			$strSql =
				"INSERT INTO ".self::$tableName."(".$arInsert[0].") ".
				"VALUES(".$arInsert[1].")";
			$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		}
		return self::CheckRecord($Data['ORDER_ID'],$Data['SOURCE']); 
    }
	
	public static function select($arOrder=array("ID","DESC"),$arFilter=array(),$arNavStartParams=array())
	{
		global $DB;
		
		$strSql='';
		
		$where='';
		if(strpos($arFilter['>=UPTIME'],".")!==false)
			$arFilter['>=UPTIME']=strtotime($arFilter['>=UPTIME']);
		if(strpos($arFilter['<=UPTIME'],".")!==false)
			$arFilter['<=UPTIME']=strtotime($arFilter['<=UPTIME']);

	 	if(count($arFilter)>0)
			foreach($arFilter as $field => $value)
			{
				if($field == 'SOURCE' && $value == 0)
					$where.= ' and '.self::getSource('order');
				else{
					if(strpos($field,'!')!==false)
						$where.=' and '.substr($field,1).' != "'.$value.'"';
					elseif(strpos($field,'<=')!==false)
						$where.=' and '.substr($field,2).' <= "'.$value.'"';				
					elseif(strpos($field,'>=')!==false)
						$where.=' and '.substr($field,2).' >= "'.$value.'"';
					elseif(strpos($field,'>')!==false)
						$where.=' and '.substr($field,1).' > "'.$value.'"';				
					elseif(strpos($field,'<')!==false)
						$where.=' and '.substr($field,1).' < "'.$value.'"';
					else
					{
						if(is_array($value))
						{
							$where.=' and (';
							foreach($value as $val)
								$where.=$field.' = "'.$val.'" or ';
							$where=substr($where,0,strlen($where)-4).")";
						}
						else
							$where.=' and '.$field.' = "'.$value.'"';
					}
				}
			}
		if($where) 
			$strSql.="
			WHERE ".substr($where,4);
			
		if(in_array($arOrder[0],array('ID','ORDER_ID','STATUS','UPTIME'))&&($arOrder[1]=='ASC'||$arOrder[1]=='DESC'))
			$strSql.="
			ORDER BY ".$arOrder[0]." ".$arOrder[1];
		
		$cnt=$DB->Query("SELECT COUNT(*) as C FROM ".self::$tableName." ".$strSql, false, $err_mess.__LINE__)->Fetch();
		
		if($arNavStartParams['nPageSize']==0)
			$arNavStartParams['nPageSize']=$cnt['C'];
		
		$strSql="SELECT * FROM ".self::$tableName." ".$strSql;

		$res = new CDBResult();
		$res->NavQuery($strSql,$cnt['C'],$arNavStartParams);

		return $res;
	}
		
	public static function Delete($orderId,$mode='order'){
		global $DB;
		$orderId = $DB->ForSql($orderId);
		$strSql =
            "DELETE FROM ".self::$tableName." 
            WHERE ORDER_ID='".$orderId."' && ".self::getSource($mode);
		$DB->Query($strSql, true);
        
        return true; 
    }
	
	public static function GetByOI($orderId){
		global $DB;
		$orderId=$DB->ForSql($orderId);
		$strSql =
            "SELECT PARAMS, STATUS, SDEK_ID, MESSAGE, OK, MESS_ID, ORDER_ID ".
            "FROM ".self::$tableName." ".
			"WHERE ORDER_ID = '".$orderId."'  && ".self::getSource('order');
		$res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		$arReturn=array();
		if($arr = $res->Fetch())
			return $arr;
		else return false;
	}

	public static function GetBySI($shipmentId){
		global $DB;
		$shipmentId=$DB->ForSql($shipmentId);
		$strSql =
            "SELECT PARAMS, STATUS, SDEK_ID, MESSAGE, OK, MESS_ID, ORDER_ID ".
            "FROM ".self::$tableName." ".
			"WHERE ORDER_ID = '".$shipmentId."'  && ".self::getSource('shipment');
		$res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		$arReturn=array();
		if($arr = $res->Fetch())
			return $arr;
		else return false;
	}
	
	public static function CheckRecord($orderId,$mode=0){
		global $DB;

		$source = (is_numeric($mode)) ? "SOURCE = '".$mode."'" : self::getSource($mode);
		
		$orderId = $DB->ForSql($orderId);
        $strSql =
            "SELECT ID, STATUS ".
            "FROM ".self::$tableName." ".
			"WHERE ORDER_ID = '".$orderId."' && ".$source;
		$res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		if($res && $arr = $res->Fetch())
			return $arr;
		return false;
	}

	public static function updateStatus($arParams){
		global $DB;
		foreach($arParams as $key => $val)
			$arParams[$key] = $DB->ForSql($val);

		$okStat='';
		if($arParams["STATUS"]=='OK')
			$okStat=" OK='1',";
		elseif($arParams["STATUS"]=='DELETE')
			$okStat=" OK='',";

		$setStr = "STATUS ='".$arParams["STATUS"]."', MESSAGE = '".$arParams["MESSAGE"]."',";
		if($arParams["SDEK_ID"])
			$setStr.="SDEK_ID = '".$arParams["SDEK_ID"]."',";
		if($arParams["MESS_ID"])
			$setStr.="MESS_ID = '".$arParams["MESS_ID"]."',";

		$setStr.=$okStat." UPTIME= '".mktime()."'";

		if(array_key_exists('SOURCE',$arParams) && $arParams['SOURCE'])
			$source = "SOURCE = '".$arParams['SOURCE']."'";
		elseif(array_key_exists('SOURCE',$arParams) && $arParams['SOURCE'] === '')
			$source = "SOURCE <=> NULL";
		elseif(array_key_exists('mode',$arParams))
			$source = self::getSource($arParams['mode']);
		else
			$source = "SOURCE = 0";

		$strSql =
            "UPDATE ".self::$tableName." 
			SET ".$setStr."
			WHERE ORDER_ID = '".$arParams["ORDER_ID"]."' && $source";

		if($DB->Query($strSql, true))
			return true;
		else 
			return false;
	}

	private function getSource($mode='order'){
		return ($mode == 'order' || $mode == '') ? '(SOURCE <=> NULL || SOURCE = 0)' : "SOURCE = '1'";
	}
}
?>