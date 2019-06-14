<?
// S2U_REDIRECT_RULES_DB

IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'] . BX_ROOT . '/modules/step2use.redirects/classes/mysql/s2u_redirects_rules.php');

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
        $arSqlSearch = array();
		
        $strSql = "
            SELECT 
                *
            FROM
                s2u_redirects_rules
            WHERE
        ";		
		
		if (is_array($arFilter))
        {
            foreach ($arFilter as $key => $val)
            {
                if (strlen($val)<=0 || $val=="NOT_REF")
                    continue;
                switch(strtoupper($key))
                {
                    case "DATE_TIME_CREATE_FROM":
						$arSqlSearch[] = "DATE_TIME_CREATE>=".$DB->CharToDateFunction($val);
                    break;
					case "DATE_TIME_CREATE_TO":
						$arSqlSearch[] = "DATE_TIME_CREATE<".$DB->CharToDateFunction($val);
                    break;
                    case "ACTIVE":
					    $arSqlSearch[] = GetFilterQuery($key, $val,"N");
					    break;
					case "OLD_LINK":
					    $arSqlSearch[] = GetFilterQuery($key, $val,"N");
					    break;
					case "NEW_LINK":
					    $arSqlSearch[] = GetFilterQuery($key, $val,"N");
					    break;
					case "SITE_ID":
					    $arSqlSearch[] = GetFilterQuery($key, $val,"N");
					    break;
					case "COMMENT":
						 //$arSqlSearch[] = GetFilterQuery($key, $val,"N");
						 $arSqlSearch[] = "(COMMENT='".$DB->ForSql($val)."')";
					break;
					default:
						$arSqlSearch[] = $key." = '".$DB->ForSql($val)."'";
					break;
                }
            }
        }
		
		$strSqlSearch = GetFilterSqlSearch($arSqlSearch);
        
            $arOrderKeys = array_keys($arOrder);
			$orderBy = $arOrderKeys[0];
			$orderDir = $arOrder[$orderBy];        
        
        switch ($orderBy) {
			case 'SITE_ID':
                $strSqlOrder = 'ORDER BY SITE_ID';
                break;
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
			case 'COMMENT':
                $strSqlOrder = 'ORDER BY COMMENT';
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
        $rs = $DB->Query($strSql.' '.$strSqlSearch.' '.$strSqlOrder, false, $err_mess.__LINE__);
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

        // не добавляем цикличные редиректы
        if(trim($arFields["OLD_LINK"])==trim($arFields["NEW_LINK"])) {
            return false;
        }
        
        //-----------------------------------------------
        // Деактивируем конфликтные редиректы
        $needRepair = COption::GetOptionString(self::MODULE_ID, 'REPAIR_CONFLICTS', 'N');
        if($needRepair=='Y') {
            $redirect = self::FindRedirect($arFields["OLD_LINK"], $arFields["SITE_ID"]);

            $ReverseRedirect = self::FindReverseRedirect($arFields, $arFields["SITE_ID"]);
            if($redirect && $redirect["ACTIVE"] == "Y") {
                self::Update($redirect['ID'], array("ACTIVE"=>"N", "COMMENT"=>$redirect["COMMENT"]."\n".date("d.m.Y H:i:s")." ".GetMessage("S2U_MAIN_AUTO_REPAIR")));
            }
            if($ReverseRedirect && $ReverseRedirect["ACTIVE"] == "Y") {
                self::Update($ReverseRedirect['ID'], array("ACTIVE"=>"N", "COMMENT"=> $ReverseRedirect["COMMENT"]."\n".date("d.m.Y H:i:s")." ".GetMessage("S2U_MAIN_AUTO_REPAIR")))	;
            }
        }
        //-----------------------------------------------
        
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
        
        // обновляем кеш regexp
        self::refreshCachedRegexpRules();
        
        return ($ID)? true: false;
	}


	/**
	 * Updates the specified reduction in your rate.
	 * The member function finds the reduction and change its value.
	 * @ Param $ arFilter = array ('OLD_LINK','NEW_LINK'=>,' DATE_TIME_CREATE' => '30 .09.2010 12:23 ',' ACTIVE '=>,' COMMENT '=>)
	 */
	static function Update($ID, $arFields) {
        
		$DB = $moduleDB = CDatabase::GetModuleConnection('step2use.redirects');

        $DB->PrepareFields("s2u_redirects_rules");			
		if((count($arFields) == 1 && array_key_exists("ACTIVE", $arFields)) ||
            (count($arFields) == 2 && array_key_exists("ACTIVE", $arFields) && array_key_exists("COMMENT", $arFields))){
			$arFields = array(
				"ACTIVE"                  => "'".$DB->ForSql(trim($arFields["ACTIVE"]))."'",
                "COMMENT"             => "'".$DB->ForSql(trim($arFields["COMMENT"]))."'",
			);			
		} else {			
			if (!array_key_exists("SITE_ID", $arFields))
				$arFields["SITE_ID"] = SITE_ID;

			$docRoot = CSite::GetSiteDocRoot($arFields["SITE_ID"]);
			
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
		}
		
        $updated = $DB->Update("s2u_redirects_rules", $arFields, "WHERE ID='".$ID."'", $err_mess.__LINE__);
        
        // обновляем кеш regexp
        self::refreshCachedRegexpRules();
        
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
        
        // обновляем кеш regexp
        self::refreshCachedRegexpRules();
        
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
	
	/**
	 * Return count all rules
	 */
    
    static function GetCountRules() {
        $DB = CDatabase::GetModuleConnection('step2use.redirects');
        
        $rs = $DB->Query("SELECT COUNT(*) as CNT FROM s2u_redirects_rules", false, $err_mess.__LINE__);
        $data = $rs->Fetch();
        
        return $data['CNT'];
    }
	
	/**
	 * Find the redirect rules by 'OLD_LINK'
	 * @param string $url
	 * @param string $siteID
	 */    
    
    static function FindRedirect($url, $siteID, $skipSlash = false) {
	    $DB = CDatabase::GetModuleConnection('step2use.redirects');  
		  	
		$url=self::utfDecode($url);
	
        // Оператор для регистроЗАВИСИМОГО поиска
        $useBinarySQL = (COption::GetOptionString(self::MODULE_ID, 'USE_CASE_SENSITIVITY', 'N')=='Y')? " BINARY ": "";

        $url = $DB->ForSql($url);
        if($skipSlash){
            $url[0] = '';
        }
        $siteID = $DB->ForSql($siteID);
        
        $strSql = "
            SELECT 
                *
            FROM
                s2u_redirects_rules
            WHERE
                SITE_ID='{$siteID}' and ACTIVE='Y' AND $useBinarySQL OLD_LINK='{$url}'
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
                SITE_ID='{$siteID}' and ACTIVE='Y' AND WITH_INCLUDES='Y' AND '{$url}' LIKE $useBinarySQL CONCAT(OLD_LINK, '%')				
            ORDER BY OLD_LINK ASC
            LIMIT 1
        ";        
        $rs = $DB->Query($strSql, false, $err_mess.__LINE__);
        $data = $rs->Fetch();
        
        if($data) return $data;
        
        // смотрим закешированные правила с регулярками и обрабатываем их в цикле
        if(COption::GetOptionString(self::MODULE_ID, 'CACHE_REGEXP_RULES')=='Y') {
            $cachedRegexpRules = self::GetCachedRegexpRules();
            if($cachedRegexpRules && is_array($cachedRegexpRules) && count($cachedRegexpRules)) {
                
                foreach($cachedRegexpRules as $rule) {
                    $pattern = "#{$rule['OLD_LINK']}#i";
                    if(preg_match($pattern, $url)) {
                        $rule['NEW_LINK'] = preg_replace($pattern, $rule['NEW_LINK'], $url);
                        return $rule;
                    }
                    
                }
            }
        }
        // если кеширование regexp-правил отключено - ищем их через БД
        else {
            // СЃ РёСЃРїРѕР»СЊР·РѕРІР°РЅРёРµРј regexp
            $strSql = "
                SELECT 
                    *
                FROM
                    s2u_redirects_rules
                WHERE
                    SITE_ID='{$siteID}' and ACTIVE='Y' AND USE_REGEXP='Y' AND '{$url}' REGEXP $useBinarySQL OLD_LINK
                ORDER BY OLD_LINK ASC
                LIMIT 1
            ";        
            $rs = $DB->Query($strSql, false, $err_mess.__LINE__);
            $data = $rs->Fetch();
            if($data) {
                //$pattern = "/".str_replace('/', '\/', $data['OLD_LINK'])."/i";
                $pattern = "#{$data['OLD_LINK']}#i";
                
                $data['NEW_LINK'] = preg_replace($pattern, $data['NEW_LINK'], $url);
                
                return $data;
            }
        }
        
        return false;
    }

	/**
	 * Find the reverse redirect rules by 'NEW_LINK'
	 * @param string $url
	 * @param string $siteID
	 */
	static function FindReverseRedirect($arFields, $siteID) {
        $DB = CDatabase::GetModuleConnection('step2use.redirects');
        
        $url = $DB->ForSql($arFields["OLD_LINK"]);
        $urlNew = $DB->ForSql($arFields["NEW_LINK"]);
        $siteID = $DB->ForSql($siteID);
        
        $strSql = "
            SELECT 
                *
            FROM
                s2u_redirects_rules
            WHERE
                SITE_ID='{$siteID}' and ACTIVE='Y' AND OLD_LINK='{$urlNew}' AND NEW_LINK='{$url}'
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
                SITE_ID='{$siteID}' and ACTIVE='Y' AND WITH_INCLUDES='Y' AND OLD_LINK='{$urlNew}' AND '{$url}' LIKE CONCAT(NEW_LINK, '%')
            ORDER BY NEW_LINK ASC
            LIMIT 1
        ";        
        $rs = $DB->Query($strSql, false, $err_mess.__LINE__);
        $data = $rs->Fetch();
        
        if($data) return $data;
        
        // СЃ РёСЃРїРѕР»СЊР·РѕРІР°РЅРёРµРј regexp
        $strSql = "
            SELECT 
                *
            FROM
                s2u_redirects_rules
            WHERE
                SITE_ID='{$siteID}' and ACTIVE='Y' AND USE_REGEXP='Y' AND OLD_LINK='{$urlNew}' AND '{$url}' REGEXP NEW_LINK
            ORDER BY NEW_LINK ASC
            LIMIT 1
        ";        
        $rs = $DB->Query($strSql, false, $err_mess.__LINE__);
        $data = $rs->Fetch();
        if($data) {
            
            //$pattern = "/".str_replace('/', '\/', $data['OLD_LINK'])."/i";
            $pattern = "#{$data['NEW_LINK']}#i";
            
            
            $data['NEW_LINK'] = preg_replace($pattern, $data['NEW_LINK'], $url);
            
            return $data;
        }
        
        return false;
    }
	
	/**
	 * Check exists, update and delete redirect and reverse redirect.  
	 * @param string $url
	 * @param string $siteID
	 * @param array $arFields
	 */
	static function RepairConflicts($arFields) {
        /*$redirect = self::FindRedirect($arFields["OLD_LINK"], $arFields["SITE_ID"]);

        $ReverseRedirect = self::FindReverseRedirect($arFields, $arFields["SITE_ID"]);
        if($redirect && $redirect["ACTIVE"] == "Y") {
            self::Update($redirect['ID'], array("ACTIVE"=>"N", "COMMENT"=>$redirect["COMMENT"]."\n".date("d.m.Y H:i:s")." ".GetMessage("S2U_MAIN_AUTO_REPAIR")));
        }
        if($ReverseRedirect && $ReverseRedirect["ACTIVE"] == "Y") {
            self::Update($ReverseRedirect['ID'], array("ACTIVE"=>"N", "COMMENT"=> $ReverseRedirect["COMMENT"]."\n".date("d.m.Y H:i:s")." ".GetMessage("S2U_MAIN_AUTO_REPAIR")))	;
        }*/
        self::Add($arFields);
    }
    
    /**
     * Генерируем кеш со списком редиректов, у которых отмечено USE_REGEXP=Y
     * И возвращаем массив редиректов
     */
    public static function GetCachedRegexpRules() {
        
        
        //---------------
        $cache = new CPHPCache();
        $cache_time = 3600*24*30;
        $cache_id = 'regexp_rules';
        $cache_path = '/'.self::MODULE_ID.'/regexp_rules/';
        if($cache->InitCache($cache_time, $cache_id, $cache_path)) {
		    $rules = $cache->GetVars();
            if(!isset($rules[SITE_ID]) || !$rules[SITE_ID] || !is_array($rules[SITE_ID])) {
                $rules[SITE_ID] = array();
            }
            return $rules[SITE_ID];
		}
        
        $regexpRules = self::GetList(array(
            'USE_REGEXP' => 'Y',
        ));
        
        $result = array();
        foreach($regexpRules as $rule) {
            $siteID = $rule['SITE_ID'];
            if(!isset($result[$siteID])) {
                $result[$siteID] = array();
            }
            $result[$siteID][] = $rule;
        }
        
        // кешируем
        $cache->StartDataCache($cache_time, $cache_id, $cache_path);
        $cache->EndDataCache($result);
        //----------------
        
        if(!isset($result[SITE_ID]) || !$result[SITE_ID] || !is_array($result[SITE_ID])) {
            return array();
        }
        
        return $result[SITE_ID];
    }
    
    /**
     * Чистим и обновляем кеш со списком редиректов, у которых отмечено USE_REGEXP=Y
     */
    public static function refreshCachedRegexpRules() {
        BXClearCache(true, '/step2use.redirects/regexp_rules/');
        self::GetCachedRegexpRules();
    }
	
	protected static function utfDecode($str)
	{
		$str=urldecode($str);
		if(strtolower(SITE_CHARSET) != 'utf-8')
			$str = Bitrix\Main\Text\Encoding::convertEncoding($str, 'UTF-8', SITE_CHARSET);
		return $str;
	}		

}	
?>
