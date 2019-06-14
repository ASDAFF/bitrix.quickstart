<?
class S2uRedirects404IgnoreDB {
    
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
        $url = $DB->ForSql($arFilter['OLD_LINK']);
        $siteID = $DB->ForSql($arFilter['SITE_ID']);
        $strSql = "
            SELECT 
                *
            FROM
                s2u_redirects_404_ignore
        ";
        
        $where = array();
        foreach($arFilter as $field=>$val) {
			$where[] = GetFilterQuery($field, $val,"N");
        }
        
		if(count($where)) $where = " WHERE ".GetFilterSqlSearch($where);
        else $where = '';
        
            $arOrderKeys = array_keys($arOrder);
			$orderBy = $arOrderKeys[0];
			$orderDir = $arOrder[$orderBy];        
        
        switch ($orderBy) {
            case 'OLD_LINK':
                $strSqlOrder = 'ORDER BY OLD_LINK';
                break;
            case 'DATE_TIME_CREATE':
                $strSqlOrder = 'ORDER BY DATE_TIME_CREATE';
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

        if(!$arResult){
            $strSql = "
            SELECT
                *
            FROM
                s2u_redirects_404_ignore
            WHERE
                SITE_ID='{$siteID}' AND ACTIVE='Y' AND WITH_INCLUDES='Y' AND '{$url}' LIKE CONCAT(OLD_LINK, '%')
            ORDER BY OLD_LINK ASC
            LIMIT 1
        ";
            $rs = $DB->Query($strSql, false, $err_mess.__LINE__);
            while($data = $rs->Fetch()) {
                $arResult[] = $data;
            }
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

        $DB->PrepareFields("s2u_redirects_404_ignore");
        $arFields = array(
            "DATE_TIME_CREATE"             => $DB->GetNowFunction(),
            "SITE_ID"                    => "'".$DB->ForSql($arFields["SITE_ID"], 2)."'",
            "OLD_LINK"                 => "'".$DB->ForSql(trim($arFields["OLD_LINK"]))."'",
            "ACTIVE"                  => "'".$DB->ForSql(trim($arFields["ACTIVE"]))."'",
            "COMMENT"             => "'".$DB->ForSql(trim($arFields["COMMENT"]))."'",
            "WITH_INCLUDES"    => "'".$DB->ForSql(trim($arFields["WITH_INCLUDES"]))."'",
            );
		
        $ID = $DB->Insert("s2u_redirects_404_ignore", $arFields, $err_mess.__LINE__);
        
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

        $DB->PrepareFields("s2u_redirects_404_ignore");
        $arFields = array(
            "DATE_TIME_CREATE"             => $DB->GetNowFunction(),
            "SITE_ID"                    => "'".$DB->ForSql($arFields["SITE_ID"], 2)."'",
            "OLD_LINK"                 => "'".$DB->ForSql(trim($arFields["OLD_LINK"]))."'",
            "ACTIVE"                  => "'".$DB->ForSql(trim($arFields["ACTIVE"]))."'",
            "COMMENT"             => "'".$DB->ForSql(trim($arFields["COMMENT"]))."'",
            "WITH_INCLUDES"    => "'".$DB->ForSql(trim($arFields["WITH_INCLUDES"]))."'",
            );
        $updated = $DB->Update("s2u_redirects_404_ignore", $arFields, "WHERE ID='".$ID."'", $err_mess.__LINE__);
        
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
        
		$res = $DB->Query("DELETE FROM s2u_redirects_404_ignore WHERE ID='$ID'");
        
        return $res;
	}
}

?>