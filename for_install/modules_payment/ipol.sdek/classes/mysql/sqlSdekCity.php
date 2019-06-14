<?
	class sqlSdekCity{
		public static $tableName = 'ipol_sdekcities';
		
		public static $lastGetCity = false;
		
		public function Add($DATA){
			 // = $Data = format:
			// BITRIX_ID - id местоположения bitrix
			// SDEK_ID - id местоположения sdek
			// NAME - имя города
			// REGION - название региона
			
			// true - Город добавлялся в таблицу, false - уже был
			global $DB;
			$rec = self::CheckCity($DATA['BITRIX_ID'],$DATA['SDEK_ID']);

			if(is_array($rec)){ // если рассинхронизация пошла: запись есть, но bId != sId
				foreach($rec as $city)
					self::Delete($city['ID']);
				self::_add($DATA);
				return true;
			}
			elseif($rec === false){
				self::_add($DATA);
				return true;
			}else{ // изменилась платежка
				if($DATA['PAYNAL'] != self::$lastGetCity['PAYNAL']){
					$strUpdate = $DB->PrepareUpdate(self::$tableName,array('PAYNAL'=>$DATA['PAYNAL']));
					$strSql = "UPDATE ".self::$tableName." SET ".$strUpdate." WHERE ID=".self::$lastGetCity['ID'];
					$DB->Query($strSql, false, $err_mess.__LINE__);
				}
				return false;
			}
		}
		
		public function _add($DATA){
			global $DB;
			$arInsert = $DB->PrepareInsert(self::$tableName, $DATA);
			$strSql =
				"INSERT INTO ".self::$tableName."(".$arInsert[0].") ".
				"VALUES(".$arInsert[1].")";
			$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		}
		
		public function CheckCity($bxId,$sdekId){
			global $DB;
			$bxId   = $DB->ForSql($bxId);
			$sdekId = $DB->ForSql($sdekId);

			$strSql =
				"SELECT * ".
				"FROM ".self::$tableName." ".
				"WHERE BITRIX_ID = '".$bxId."' and SDEK_ID = '".$sdekId."'";
		
			$res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__)->Fetch();
			if($res){
				self::$lastGetCity = $res;
				return true;
			}
			
			self::$lastGetCity = false;

			$strSql =
				"SELECT ID, SDEK_ID, BITRIX_ID, NAME, REGION ".
				"FROM ".self::$tableName." ".
				"WHERE BITRIX_ID = '".$bxId."' or SDEK_ID = '".$sdekId."'";
			
			$res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			$arTrouble = array();
			while($element=$res->Fetch())
				$arTrouble[]=$element;
			if(count($arTrouble))
				return $arTrouble;
			
			return false;		
		}
		
		public function Delete($id){
			global $DB;
			$orderId = $DB->ForSql($orderId);
			$strSql =
				"DELETE FROM ".self::$tableName."
				WHERE ID='".$id."'";
			$DB->Query($strSql, true);
			return true; 
		}
		
		public function getByBId($bid){
			global $DB;
			$bid = $DB->ForSql($bid);
			$strSql =
				"SELECT * ".
				"FROM ".self::$tableName." ".
				"WHERE BITRIX_ID = '".$bid."'";
			$res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

			return $res->Fetch();
		}
		
		public function getBySId($sid){
			global $DB;
			$sid = $DB->ForSql($sid);
			$strSql =
				"SELECT * ".
				"FROM ".self::$tableName." ".
				"WHERE SDEK_ID = '".$sid."'";
			$res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

			return $res->Fetch();
		}
		
		public function getCityPM($sid,$mode='SDEK_ID'){
			if($mode != 'SDEK_ID' && $mode != 'BITRIX_ID')
				return false;
			global $DB;
			$sid = $DB->ForSql($sid);
			$strSql =
				"SELECT PAYNAL ".
				"FROM ".self::$tableName." ".
				"WHERE $mode = '".$sid."'";
			$res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__)->Fetch();
			if($res){
				if($res['PAYNAL'] == '')
					$res = true;
				else{
					switch($res['PAYNAL']){
						case 'no limit': $res = true; break;
						case '0.00': $res = false; break;
						default: $res = $res['PAYNAL']; break;
					}
				}
			}
			
			return $res;
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
			if($where) 
				$strSql.="
				WHERE ".substr($where,4);

			if(in_array($arOrder[0],array('ID','BITRIX_ID','SDEK_ID','NAME','REGION'))&&($arOrder[1]=='ASC'||$arOrder[1]=='DESC'))
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

		public function getCitiesByCountry($country=false,$doNav=false){
			global $DB;
			if($country){
				if(is_array($country)){
					$where = 'WHERE ';
					foreach($country as $country)
						if($country == 'rus')
							$where .= 'COUNTRY = "rus" or COUNTRY <=> NULL or ';
						else
							$where .= 'COUNTRY = "'.$country.'" or ';
					$where = substr($where,0,strlen($where)-3);
				}else{
					if($country == 'rus')
						$where = 'WHERE COUNTRY = "rus" or COUNTRY <=> NULL';
					else
						$where = 'WHERE COUNTRY = "'.$country.'"';
				}
			}else
				$where = '';

			if($doNav){
				$cnt=$DB->Query("SELECT COUNT(*) as C FROM ".self::$tableName." ".$where, false, $err_mess.__LINE__)->Fetch();
				$req = new CDBResult();
				$req->NavQuery("SELECT * FROM ".self::$tableName." ".$where." ORDER BY REGION ASC",$cnt['C'],array('nPageSize'=>$cnt['C']));
			}else
				$req=$DB->Query("SELECT * FROM ".self::$tableName." ".$where." ORDER BY REGION ASC", false, $err_mess.__LINE__);

			return $req;
		}
	}
?>