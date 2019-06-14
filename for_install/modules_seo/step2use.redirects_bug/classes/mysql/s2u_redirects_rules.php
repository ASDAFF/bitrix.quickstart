<?


// S2U_REDIRECT_RULES_DB
class S2uRedirectsRulesDB {
    
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
                s2u_redirects_rules
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

        $DB->PrepareFields("s2u_redirects_rules");
        $arFields = array(
            "DATE_TIME_CREATE"             => $DB->GetNowFunction(),
            "SITE_ID"                    => "'".$DB->ForSql($arFields["SITE_ID"], 2)."'",
            "OLD_LINK"                 => "'".$DB->ForSql(trim($arFields["OLD_LINK"]))."'",
            "NEW_LINK"                  => "'".$DB->ForSql(trim($arFields["NEW_LINK"]))."'",
            "STATUS"           => "'".$DB->ForSql(trim($arFields["STATUS"]))."'",
            "ACTIVE"                  => "'".$DB->ForSql(trim($arFields["ACTIVE"]))."'",
            "COMMENT"             => "'".$DB->ForSql(trim($arFields["COMMENT"]))."'",
            "WITH_INCLUDES"             => "'".$DB->ForSql(trim($arFields["WITH_INCLUDES"]))."'",
            "USE_REGEXP"             => "'".$DB->ForSql(trim($arFields["USE_REGEXP"]))."'",
            );
		
        $ID = $DB->Insert("s2u_redirects_rules", $arFields, $err_mess.__LINE__);
        
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

        $DB->PrepareFields("s2u_redirects_rules");
        $arFields = array(
            "DATE_TIME_CREATE"             => $DB->GetNowFunction(),
            "SITE_ID"                    => "'".$DB->ForSql($arFields["SITE_ID"], 2)."'",
            "OLD_LINK"                 => "'".$DB->ForSql(trim($arFields["OLD_LINK"]))."'",
            "NEW_LINK"                  => "'".$DB->ForSql(trim($arFields["NEW_LINK"]))."'",
            "STATUS"           => "'".$DB->ForSql(trim($arFields["STATUS"]))."'",
            "ACTIVE"                  => "'".$DB->ForSql(trim($arFields["ACTIVE"]))."'",
            "COMMENT"             => "'".$DB->ForSql(trim($arFields["COMMENT"]))."'",
            "WITH_INCLUDES"             => "'".$DB->ForSql(trim($arFields["WITH_INCLUDES"]))."'",
            "USE_REGEXP"             => "'".$DB->ForSql(trim($arFields["USE_REGEXP"]))."'",
            );
        $updated = $DB->Update("s2u_redirects_rules", $arFields, "WHERE ID='".$ID."'", $err_mess.__LINE__);
        
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
        
		$res = $DB->Query("DELETE FROM s2u_redirects_rules WHERE ID='$ID'");
        
        return $res;
	}
    
    /**
     * Refresh redirect rules in .htaccess
     */
    /*static function generateRules() {
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
    }*/
    
    static function GetCountRules() {
        $DB = CDatabase::GetModuleConnection('step2use.redirects');
        
        $rs = $DB->Query("SELECT COUNT(*) as CNT FROM s2u_redirects_rules", false, $err_mess.__LINE__);
        $data = $rs->Fetch();
        
        return $data['CNT'];
    }
    
    static function FindRedirect($url, $siteID) {
        $DB = CDatabase::GetModuleConnection('step2use.redirects');
        
        $url = $DB->ForSql($url);
        $siteID = $DB->ForSql($siteID);
        
        $strSql = "
            SELECT 
                *
            FROM
                s2u_redirects_rules
            WHERE
                SITE_ID='{$siteID}' and ACTIVE='Y' AND OLD_LINK='{$url}'
            LIMIT 1
        ";        
        $rs = $DB->Query($strSql, false, $err_mess.__LINE__);
        $data = $rs->Fetch();
        
        if($data) return $data;
        
        $strSql = "
            SELECT 
                *
            FROM
                s2u_redirects_rules
            WHERE
                SITE_ID='{$siteID}' and ACTIVE='Y' AND '{$url}' LIKE CONCAT(OLD_LINK, '%') AND WITH_INCLUDES='Y'
            ORDER BY OLD_LINK ASC
            LIMIT 1
        ";        
        $rs = $DB->Query($strSql, false, $err_mess.__LINE__);
        $data = $rs->Fetch();
        
        if($data) return $data;
        
        // с использованием regexp
        $strSql = "
            SELECT 
                *
            FROM
                s2u_redirects_rules
            WHERE
                SITE_ID='{$siteID}' and ACTIVE='Y' AND '{$url}' REGEXP OLD_LINK AND USE_REGEXP='Y'
            ORDER BY OLD_LINK ASC
            LIMIT 1
        ";        
        $rs = $DB->Query($strSql, false, $err_mess.__LINE__);
        $data = $rs->Fetch();
        //var_dump($data);exit;
        if($data) {
            
            //$pattern = "/".str_replace('/', '\/', $data['OLD_LINK'])."/i";
            $pattern = "#{$data['OLD_LINK']}#i";
            
            //var_dump($pattern);
            //var_dump($data['NEW_LINK']);
            //var_dump($url);
            
            $data['NEW_LINK'] = preg_replace($pattern, $data['NEW_LINK'], $url);
            
            return $data;
        }
        
        return false;
    }
	
}

?>