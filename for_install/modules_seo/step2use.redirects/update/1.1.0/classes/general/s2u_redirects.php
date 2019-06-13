<?php

class S2uRedirects {
    const MODULE_ID = 'step2use.redirects';
	
	static public function addSlash ($url){
		$arrUrl = parse_url($url);
		if(substr($arrUrl["path"], -4) != ".php" && 
			substr($arrUrl["path"], -5) != ".html" && 
				substr($arrUrl["path"], -1) != "/")
		$arrUrl["path"] .= "/";			
		if(array_key_exists ( "scheme" , $arrUrl )) $newUrl .= $arrUrl["scheme"]."://";
		if(array_key_exists ( "host" , $arrUrl )) $newUrl .= $arrUrl["host"];
		if(array_key_exists ( "path" , $arrUrl )) $newUrl .= $arrUrl["path"];
		if(array_key_exists ( "query" , $arrUrl )) $newUrl .= $arrUrl["query"];
		return $newUrl;		
	}
	
	static public function mainMirror ($url, $main_mirror){
		$arrUrl = parse_url($url);
		$arrNewUrl = parse_url($main_mirror);
		if($arrNewUrl["scheme"] != $arrUrl["scheme"] || $arrNewUrl["host"] != $arrUrl["host"]){
			$url = $arrNewUrl["scheme"]."://".$arrNewUrl["host"].$arrUrl["path"];
			if(array_key_exists ( "query" , $arrUrl )) $url .="?".$arrUrl["query"];
		}
		return $url;		
	}

    static public function handlerOnBeforeProlog() {
		if(!defined('ADMIN_SECTION')) {	
			global $APPLICATION;		
			$redirectIsActive = COption::GetOptionString(self::MODULE_ID, 'REDIRECTS_IS_ACTIVE', 'Y');
			if($redirectIsActive=='Y') 
				$redirect = S2uRedirectsRulesDB::FindRedirect($APPLICATION->GetCurUri(), SITE_ID);
			$_404IsActive = COption::GetOptionString(self::MODULE_ID, '404_IS_ACTIVE', 'Y');
			
			$main_mirror = COption::GetOptionString(self::MODULE_ID, 'main_mirror_' . SITE_ID);
			$slash_redirect = COption::GetOptionString(self::MODULE_ID, 'slash_add_' . SITE_ID);
			
			if($redirect) {
				if($redirect['STATUS'] == "410"){
					header("HTTP/1.0 410 Gone");
				}else{
					$url = $oldUrl = $redirect['NEW_LINK'];
					if($main_mirror != ""){
						$url = self::mainMirror($url, $main_mirror);			
					}
					if($slash_redirect == "Y"){
						$url = self::addSlash($url);
					}
					if($url != $oldUrl){
						LocalRedirect($url, false, $redirect['STATUS']); 
					}
									
				}
				return true;
			} else {	
				$url = $oldUrl = $_SERVER['SCRIPT_URI'];
				if($main_mirror != ""){
					$url = self::mainMirror($url, $main_mirror);					
				}
				if($slash_redirect == "Y"){
					$url = self::addSlash($url);
				}
				if($url != $oldUrl){
					LocalRedirect($url, false, "301 Moved permanently");
				}	
			}		
			
		}
	}


	static public function handlerOnEpilog(){
		global $APPLICATION;
		
		$_404IsActive = COption::GetOptionString(self::MODULE_ID, '404_IS_ACTIVE', 'Y');

		// 404 Not Found
       	 if($_404IsActive=='Y') {
            // запоминаем, если текущий url не стоит в игнорлисте битых ссылок
            $isIgnore = S2uRedirects404IgnoreDB::GetList(array(
                'SITE_ID' => SITE_ID,
                'ACTIVE' => 'Y', 
                'OLD_LINK' => $APPLICATION->GetCurUri()
            ));


            $isIgnore = (bool) count($isIgnore);
            if((defined('ERROR_404') && ERROR_404=='Y') && !defined('ADMIN_SECTION') && !$isIgnore) {
				// try to get guest from statistic module
                $guestID = 0;
                if(CModule::IncludeModule('statistic')) {
                    $guestID = $_SESSION["SESS_GUEST_ID"];
                }
                S2uRedirects404DB::Add(array(
                    'URL' => $APPLICATION->GetCurUri(),
    				'REFERER_URL' => $_SERVER['HTTP_REFERER'],
                    'REDIRECT_STATUS' => $_SERVER['REDIRECT_STATUS'],
    				'DATE_TIME_CREATE' => ConvertTimeStamp(time(), 'FULL'),
                    'SITE_ID' => SITE_ID,
                    'GUEST_ID' => $guestID,
                ));
                
                $rowsLimit = COption::GetOptionInt(self::MODULE_ID, '404_LIMIT', 0);
                if($rowsLimit) {
                    $rowsCnt = S2uRedirects404DB::GetCount();
                    if($rowsCnt>$rowsLimit) S2uRedirects404DB::DeleteOldest();
                }
            }
        }	
	}
	
	static public function handlerOnBeforeIBlockSectionUpdate(&$arFields){		
		global $DB;
		$arIblock = CIBlock::GetArrayByID($arFields["IBLOCK_ID"]);		
		$rsSites = CIBlock::GetSite($arFields["IBLOCK_ID"]);
		while($arSite = $rsSites->Fetch()){			
			if(COption::GetOptionString("step2use.redirects", 'remember_changing_code_'.$arSite["LID"])=="Y"){
				$arSites[] = $arSite["LID"];				
			}			
		}
		if(count($arSites)){
			$rsSections = CIBlockSection::GetList(array(), array("ID" => $arFields["ID"]), false, array("SECTION_PAGE_URL"));
			$rsSections->SetUrlTemplates($arIblock["SECTION_PAGE_URL"]);
			if($arSection = $rsSections->GetNext()){
				if($arFields["CODE"] != $arSection["CODE"]){
					$pos = strrpos($arSection["SECTION_PAGE_URL"], $arSection["CODE"]);
					if($pos !== false)    {
						$NEW_LINK = substr_replace($arSection["SECTION_PAGE_URL"], $arFields["CODE"], $pos, strlen($arSection["CODE"]));
					}
					$COMMENT = "Редирект создан автоматически при изменении символьного кода раздела: ID: ".$arFields["ID"]." инфоблока IBLOCK_ID: ".$arFields["IBLOCK_ID"];
					foreach ($arSites as $site){
						$Res__ = S2uRedirectsRulesDB::Add(array(
							'OLD_LINK' => $arSection["SECTION_PAGE_URL"],
							'NEW_LINK' => trim($NEW_LINK),
							'DATE_TIME_CREATE' => ConvertTimeStamp(time(), 'FULL'),
							'STATUS' => "301",
							'ACTIVE' => "Y",
							'COMMENT' => $COMMENT,
							'SITE_ID' => $site,
							'WITH_INCLUDES' => "N",
							'USE_REGEXP' => "N",
						));	
					}				
				}
			}
		}
	}
	
	static public function handlerOnBeforeIBlockElementUpdate(&$arFields){		
		global $DB;
		$arIblock = CIBlock::GetArrayByID($arFields["IBLOCK_ID"]);
		$rsSites = CIBlock::GetSite($arFields["IBLOCK_ID"]);
		while($arSite = $rsSites->Fetch()){	
			if(COption::GetOptionString("step2use.redirects", 'remember_changing_code_'.$arSite["LID"])=="Y"){
				$arSites[] = $arSite["LID"];				
			}
		}
		if(count($arSites)){
			$rsElements = CIBlockElement::GetList(array(), array("ID" => $arFields["ID"]), false, false, array("DETAIL_PAGE_URL"));
			$rsElements->SetUrlTemplates($arIblock["DETAIL_PAGE_URL"]);
			if($arElement = $rsElements->GetNext()){
				if($arFields["CODE"] != $arElement["CODE"]){
					$pos = strrpos($arElement["DETAIL_PAGE_URL"], $arElement["CODE"]);
					if($pos !== false)    {
						$NEW_LINK = substr_replace($arElement["DETAIL_PAGE_URL"], $arFields["CODE"], $pos, strlen($arElement["CODE"]));
					}
					$COMMENT = "Редирект создан автоматически при изменении символьного кода элемента: ID: ".$arFields["ID"]." инфоблока IBLOCK_ID: ".$arFields["IBLOCK_ID"];
					foreach ($arSites as $site){
						$Res__ = S2uRedirectsRulesDB::Add(array(
							'OLD_LINK' => $arElement["DETAIL_PAGE_URL"],
							'NEW_LINK' => trim($NEW_LINK),
							'DATE_TIME_CREATE' => ConvertTimeStamp(time(), 'FULL'),
							'STATUS' => "301",
							'ACTIVE' => "Y",
							'COMMENT' => $COMMENT,
							'SITE_ID' => $site,
							'WITH_INCLUDES' => "N",
							'USE_REGEXP' => "N",
						));	
					}				
				}		
			}
		}
	}
}

?>
