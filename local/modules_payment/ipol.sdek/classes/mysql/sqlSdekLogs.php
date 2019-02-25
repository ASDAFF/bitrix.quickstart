<?
	class sqlSdekLogs{
		public static $tableName = 'ipol_sdeklogs';

		public function Add($DATA){	
			global $DB;
			$rec = self::Check($DATA['ACCOUNT']);
			$result = false;
			$DATA['ACTIVE'] = 'Y';

			if($rec){
				self::Update($rec,$DATA);
				$result = true;
			}else{
				self::_add($DATA);
				$result = true;
			}

			return $result;
		}

		private function _add($DATA){
			global $DB;
			$arInsert = $DB->PrepareInsert(self::$tableName, $DATA);
			$strSql =
				"INSERT INTO ".self::$tableName."(".$arInsert[0].") ".
				"VALUES(".$arInsert[1].")";
			$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		}

		public function Check($account){
			global $DB;
			$account = $DB->ForSql($account);

			$strSql =
				"SELECT ID ".
				"FROM ".self::$tableName." ".
				"WHERE ACCOUNT = '".$account."'";

			$res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__)->Fetch();
			return ($res) ? $res['ID'] : false;	
		}

		public function Delete($id){
			global $DB;
			$id = $DB->ForSql($id);
			$strSql =
				"DELETE FROM ".self::$tableName."
				WHERE ID='".$id."'";
			$DB->Query($strSql, true);
			return true; 
		}

		public function setActive($id,$flag='Y'){
			global $DB;
			if(!in_array($flag,array('Y','N')))
				return;
			$id = $DB->ForSql($id);
			$flag = $DB->ForSql($flag);
			return ($DB->Query("UPDATE ".self::$tableName." SET ACTIVE='".$flag."' WHERE ID = '".$id."'", true));
		}

		public function clear(){
			global $DB;
			$strSql =
				"SELECT ID ".
				"FROM ".self::$tableName;
			$res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			while($auth=$res->Fetch())
				self::setActive($auth['ID'],'N');
		}

		public function Update($id,$DATA){
			global $DB;
			foreach($DATA as $key => $val)
				$DATA[$key] = $DB->ForSql($val);
			return ($DB->Query("UPDATE ".self::$tableName." SET ACCOUNT='".$DATA['ACCOUNT']."', SECURE='".$DATA['SECURE']."', ACTIVE='".$DATA['ACTIVE']."', LABEL='".$DATA['LABEL']."' WHERE ID = '".$id."'", true));
		}

		public function getAccountsList($labeled=false,$all=false){
			global $DB;
			$active = ($all) ? '' : "WHERE ACTIVE='Y'";
			$res = $DB->Query("SELECT ID, ACCOUNT, LABEL FROM ".self::$tableName." ".$active, false, "File: ".__FILE__."<br>Line: ".__LINE__);

			$arAccs = array();
			while($acc=$res->Fetch())
				if($labeled)
					$arAccs[$acc['ID']] = array('ACCOUNT' => $acc['ACCOUNT'],'LABEL'=> ($acc['LABEL']) ? $acc['LABEL'] : '');
				else
					$arAccs[$acc['ID']] = $acc['ACCOUNT'];
			return $arAccs;
		}

		public function getById($id){
			global $DB;
			$res = $DB->Query("SELECT * FROM ".self::$tableName." WHERE ID='".$DB->ForSql($id)."'", false, "File: ".__FILE__."<br>Line: ".__LINE__)->Fetch();

			return $res;
		}

		public function select($arFilter=array()){
			global $DB;
			$strSql='';
			$where='';

			if(count($arFilter)>0)
				foreach($arFilter as $field => $value){
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
					elseif(is_array($value)){
						$where.=' and (';
						foreach($value as $val)
							$where.=$field.' = "'.$val.'" or ';
						$where=substr($where,0,strlen($where)-4).")";
					}else
						$where.=' and '.$field.' = "'.$value.'"';
				}
			if($where) 
				$strSql.="
				WHERE ".substr($where,4);

			$strSql.="
				ORDER BY ID ASC";

			$strSql="SELECT * FROM ".self::$tableName." ".$strSql;

			$res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

			return $res;
		}
	}