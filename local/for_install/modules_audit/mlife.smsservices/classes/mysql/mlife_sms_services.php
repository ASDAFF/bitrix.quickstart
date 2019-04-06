<?

class CMlifeSmsServicesSql {
	
	public function getList($arOrder=array(), $arFilter=array(), $arSelect=array()) {
	
		//массив доступных полей в базе
		$arrFields = array ("id","provider","smsid","sender","phone","time","time_st","mess","prim","status");
	
		//print_r($arOrder);
	
		global $DB;
		$strSql = "SELECT ";

		if(count($arSelect)>0) {
			$strSql .= implode(',',$arSelect);
		}else {
			$strSql .= "*";
		}
		$strSql .= " FROM b_mlife_smsservices_list";
		
		//фильтр
		$strWHERE = '';
		foreach ($arFilter as $fkey => $fval)
		{
			if($fval)
			{

				if($strWHERE == ''){
					$strWHERE = ' WHERE ';
				}
				else {
					$strWHERE .= ' AND ';
				}

				if (in_array($fkey,$arrFields))
				{
					if(is_array($fval)) {
						$strWHERE .= $fkey.' IN ("'.implode('","',$fval).'") ';
					}
					else{
						if($fkey=='status'){
							$strWHERE .= $fkey.' = '.$fval.' ';
						}else{
							$strWHERE .= $fkey.' = "'.$fval.'" ';
						}
					}
				}
				else if($fkey=='sendfrom') {
				$fkey = 'time';
					$strWHERE .= $fkey.' > '.strtotime($fval).' ';
				}
				else if($fkey=='sendto') {
				$fkey = 'time';
					$strWHERE .= $fkey.' < '.strtotime($fval).' ';
				}
				else if($fkey=='sendto_unix') {
				$fkey = 'time';
					$strWHERE .= $fkey.' < '.$fval.' ';
				}
				
			}
		}
		$strSql .= $strWHERE;
		
		//сортировка
		foreach ($arOrder as $by => $order) {
			if ((strtoupper($order) == 'DESC' || strtoupper($order) == 'ASC') && in_array($by,$arrFields))
				$strSql .= ' ORDER BY '.$by.' '.$order;
		}
		//echo $strSql;
		$res = $DB->Query($strSql);

		if($res)
			return($res);
		else
			return false;
	}
	
	public function DeleteSms($id){
	
		global $DB;
		$id=intval($id);
		$strSql = "DELETE FROM b_mlife_smsservices_list WHERE id=".$id;

		$res = $DB->Query($strSql);
		
		if($res)
			return($res);
		else
			return false;
	
	}
	
	public function addSms($arFields) {
	
		global $DB;
		
		$val = '';
		$keys = '';
		$strSql = "INSERT INTO b_mlife_smsservices_list ";
		$i=0;
		$count = count($arFields);
		foreach($arFields as $key=>$value) {
			$i++;
			$keys .= "`".$key."`";
			$val .= "'".$value."'";
			if($count!=$i) {
				$keys .= ", ";
				$val .= ", ";
			}
		}
		$strSql .= "(".$keys.") VALUES (".$val.")";
		
		$res = $DB->Query($strSql);
		
		if($res)
			return($res);
		else
			return false;
	}
	
	public function updateSmsStatus($id,$status_code,$sms_id=false, $time=false) {

		global $DB;
		
		$arFields['status'] = $status_code;
		if($time) {
			$arFields['time_st'] = $time;
		}
		else{
			$time=time();
		}
		if((string)$sms_id) $arFields['smsid'] = "'".trim($sms_id)."'";
		
		$where = "WHERE id='".$id."'";
		$res = $DB->Update("b_mlife_smsservices_list", $arFields, $where);
		
		if($res)
			return($res);
		else
			return false;
	
	}
}

?>