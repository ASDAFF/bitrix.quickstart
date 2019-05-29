<?

class S2U_REDIRECT_CLASS {
    
    const MODULE_ID = 'step2use.redirects';
    
//======================================================================================
	/**
	 * The member function prepares an array of $ arResult, which is actually the contents of the file site_root / S2U_REDIRECT.php
	 * Training includes sorting.
	 * @ Param $ arFilter = array ('old_link', 'new_link'=>, 'date_time_create' =>, 'active' =>, 'comment' =>)
	 * @ Param $ arOrder = array ('ADRESS' => 'ASC' | 'DESC') - passed one of the keys OLD_LINK, NEW_LINK, DATE_TIME_CREATE, STATUS, ACTIVE
	 */
	static function GetList($arFilter = array(), $arOrder = array()) {
		global $APPLICATION;
        
        $DB = CDatabase::GetModuleConnection('step2use.redirects');
        
        $strSql = "
            SELECT 
                *
            FROM
                s2u_redirects
            WHERE
                '1'='1'
        ";
        
        $where = array();
        foreach($arFilter as $field=>$val) {
            $where[] = $field." = '".$DB->ForSql($val)."'";
        }
        
        if(count($where)) $where = ' AND '.implode(' AND ', $where);
        else $where = '';
        
            $arOrderKeys = array_keys($arOrder);
			$orderBy = $arOrderKeys[0];
			$orderDir = $arOrder[$orderBy];        
        
        switch ($orderBy) {
            case 'OLD_LINK':
                $strSqlOrder = 'ORDER BY OLD_LINK';
                break;
            case 'NEW_LINK':
                $strSqlOrder = 'ORDER BY NEW_LINK';
                break;
            case 'DATE_TIME_CREATE':
                $strSqlOrder = 'ORDER BY DATE_TIME_CREATE';
                break;
            case 'STATUS':
                $strSqlOrder = 'ORDER BY STATUS';
                break;
            case 'ACTIVE':
                $strSqlOrder = 'ORDER BY ACTIVE';
                break;
            default:
                $strSqlOrder = "ORDER BY DATE_TIME_CREATE";
                break;
        }

        if ($orderDir!="asc") {
            $strSqlOrder .= " desc ";
            $orderDir="desc";
        }
        else {
            $strSqlOrder .= " asc ";
        }
        
        $rs = $DB->Query($strSql.' '.$where.' '.$strSqlOrder, false, $err_mess.__LINE__);
        $arResult = array();
        while($data = $rs->Fetch()) {
            $arResult[] = $data;
        }
        
        return $arResult;
	}

	/**
	 * Adds a new URL redirect
	 * @param  $arFields = array('ID', 'OLD_LINK','NEW_LINK'=>,'DATE_TIME_CREATE'=>'30.09.2010 12:23', 'ACTIVE'=>, 'COMMENT'=>)
	 */
	static function Add($arFields) {

        $DB = $moduleDB = CDatabase::GetModuleConnection('step2use.redirects');
        
		if (!array_key_exists("SITE_ID", $arFields))
			$arFields["SITE_ID"] = SITE_ID;

		$docRoot = CSite::GetSiteDocRoot($arFields["SITE_ID"]);

        $DB->PrepareFields("s2u_redirects");
        $arFields = array(
            "DATE_TIME_CREATE"             => $DB->GetNowFunction(),
            "SITE_ID"                    => "'".$DB->ForSql($arFields["SITE_ID"], 2)."'",
            "OLD_LINK"                 => "'".$DB->ForSql(trim($arFields["OLD_LINK"]))."'",
            "NEW_LINK"                  => "'".$DB->ForSql(trim($arFields["NEW_LINK"]))."'",
            "STATUS"           => "'".$DB->ForSql(trim($arFields["STATUS"]))."'",
            "ACTIVE"                  => "'".$DB->ForSql(trim($arFields["ACTIVE"]))."'",
            "COMMENT"             => "'".$DB->ForSql(trim($arFields["COMMENT"]))."'",
            );
		
        $ID = $DB->Insert("s2u_redirects", $arFields, $err_mess.__LINE__);
        
        self::generateRules();
        
        return ($ID)? true: false;
	}


	/**
	 * Updates the specified reduction in your rate.
	 * The member function finds the reduction and change its value.
	 * @ Param $ arFilter = array ('OLD_LINK','NEW_LINK'=>,' DATE_TIME_CREATE' => '30 .09.2010 12:23 ',' ACTIVE '=>,' COMMENT '=>)
	 */
	static function Update($ID, $arFields) {
		$DB = $moduleDB = CDatabase::GetModuleConnection('step2use.redirects');
        
		if (!array_key_exists("SITE_ID", $arFields))
			$arFields["SITE_ID"] = SITE_ID;

		$docRoot = CSite::GetSiteDocRoot($arFields["SITE_ID"]);

        $DB->PrepareFields("s2u_redirects");
        $arFields = array(
            "DATE_TIME_CREATE"             => $DB->GetNowFunction(),
            "SITE_ID"                    => "'".$DB->ForSql($arFields["SITE_ID"], 2)."'",
            "OLD_LINK"                 => "'".$DB->ForSql(trim($arFields["OLD_LINK"]))."'",
            "NEW_LINK"                  => "'".$DB->ForSql(trim($arFields["NEW_LINK"]))."'",
            "STATUS"           => "'".$DB->ForSql(trim($arFields["STATUS"]))."'",
            "ACTIVE"                  => "'".$DB->ForSql(trim($arFields["ACTIVE"]))."'",
            "COMMENT"             => "'".$DB->ForSql(trim($arFields["COMMENT"]))."'",
            );
		$DB->StartTransaction();
        
        $updated = $DB->Update("s2u_redirects", $arFields, "WHERE ID='".$ID."'", $err_mess.__LINE__);
        
        self::generateRules();
        
        return ($updated)? true: false;
	}


	/**
	 * Removes the redirect rule
	 * @param int $ID
	 */
	static function Delete($ID) {
		global $APPLICATION, $DB;

        $ID = (int) $ID;
        if(!$ID) return false;
        
		$res = $DB->Query("DELETE FROM s2u_redirects WHERE ID='$ID'");
        self::generateRules();
        
        return $res;
	}
    
    /**
     * Refresh redirect rules in .htaccess
     */
    static function generateRules() {
        $DB = CDatabase::GetModuleConnection('step2use.redirects');
        
        $resSites = CSite::GetList(($v1="sort"), ($v2="asc"));
        while($site = $resSites->Fetch()) {
            $docRoot = CSite::GetSiteDocRoot($site['ID']);
            
            $htaccessPath = $docRoot.'/.htaccess';
            $handle = fopen($htaccessPath, "r");
            $tmpHtaccessPath = tempnam(sys_get_temp_dir(), '');
            $handleTmp = fopen($tmpHtaccessPath, "w"); //tmpfile();
            
            if(file_exists($htaccessPath) && $handle && $handleTmp) {
                $blockStart = "# >> automatically generated redirects for module step2use.redirect (dont modify!)\n";
                $blockEnd = "# << automatically generated redirects for module step2use.redirect (dont modify!)\n";
                
                $inside = false;
                while (($buffer = fgets($handle)) !== false) {
                    if($buffer==$blockStart) $inside = true;
                    
                    if($inside && $buffer==$blockEnd) {
                        $inside = false;
                        continue;
                    }
                    
                    if($inside) continue;
                    
                    fwrite($handleTmp, $buffer);
                }
                
                fwrite($handleTmp, $blockStart);
                $resRedirect = $DB->Query('SELECT * FROM s2u_redirects WHERE SITE_ID="'.$site['ID'].'" AND ACTIVE="Y"');
                while($arRedirect = $resRedirect->Fetch()) {
                    $strRedirect = "#{$arRedirect['ID']}";
                    if($arRedirect['COMMENT']) $strRedirect .= "\n# Comment:\n# ".str_replace("\n", "\n# ", $arRedirect['COMMENT']);
                    $strRedirect .= "\n";
                    $strRedirect .= "Redirect {$arRedirect['STATUS']} {$arRedirect['OLD_LINK']} {$arRedirect['NEW_LINK']}\n";
                    fwrite($handleTmp, $strRedirect);
                }
                fwrite($handleTmp, $blockEnd);
                
                rename($tmpHtaccessPath, $htaccessPath);
            }
            fclose($handle);
            fclose($handleTmp);
        }
    }
    
    static function GetCountRules() {
        $DB = CDatabase::GetModuleConnection('step2use.redirects');
        
        $rs = $DB->Query("SELECT COUNT(*) as CNT FROM s2u_redirects", false, $err_mess.__LINE__);
        $data = $rs->Fetch();
        
        return $data['CNT'];
    }
	
}

?>