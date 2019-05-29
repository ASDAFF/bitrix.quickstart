<?php
/**
 * Copyright (c) 2019 Created by ASDAFF asdaff.asad@yandex.ru
 */

class seo2Redirects {
    const MODULE_ID = 'asdaff.redirects';
    
    static public function handlerOnBeforeProlog() {
        global $APPLICATION;
        
        $redirectIsActive = COption::GetOptionString(self::MODULE_ID, 'REDIRECTS_IS_ACTIVE', 'Y');
        $_404IsActive = COption::GetOptionString(self::MODULE_ID, '404_IS_ACTIVE', 'Y');
        
        if($redirectIsActive=='Y') {
            $redirect = seo2RedirectsRulesDB::FindRedirect($APPLICATION->GetCurUri(), SITE_ID);
            if($redirect) {
                LocalRedirect($redirect['NEW_LINK'], false, $redirect['STATUS']); 
                return true;
            }
        }
        
        // 404 Not Found
        if($_404IsActive=='Y') {
            // запоминаем, если текущий url не стоит в игнорлисте битых ссылок
            $isIgnore = seo2Redirects404IgnoreDB::GetList(array(
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
                seo2Redirects404DB::Add(array(
                    'URL' => $APPLICATION->GetCurUri(),
    				'REFERER_URL' => $_SERVER['HTTP_REFERER'],
                    'REDIRECT_STATUS' => $_SERVER['REDIRECT_STATUS'],
    				'DATE_TIME_CREATE' => ConvertTimeStamp(time(), 'FULL'),
                    'SITE_ID' => SITE_ID,
                    'GUEST_ID' => $guestID,
                ));
                
                $rowsLimit = COption::GetOptionInt(self::MODULE_ID, '404_LIMIT', 0);
                if($rowsLimit) {
                    $rowsCnt = seo2Redirects404DB::GetCount();
                    if($rowsCnt>$rowsLimit) seo2Redirects404DB::DeleteOldest();
                }
            }
        }
    }
}

?>
