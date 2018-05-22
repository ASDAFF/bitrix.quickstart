<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

COption::SetOptionString('fileman', 'propstypes', serialize(array('description'=>GetMessage('MAIN_OPT_DESCRIPTION'), 'keywords'=>GetMessage('MAIN_OPT_KEYWORDS'), 'title'=>GetMessage('MAIN_OPT_TITLE'), 'keywords_inner'=>GetMessage('MAIN_OPT_KEYWORDS_INNER'))), false, $siteID);
COption::SetOptionInt('search', 'suggest_save_days', 250);
COption::SetOptionString('search', 'use_tf_cache', 'Y');
COption::SetOptionString('search', 'use_word_distance', 'Y');
COption::SetOptionString('search', 'use_social_rating', 'Y');
COption::SetOptionString('iblock', 'use_htmledit', 'Y');
COption::SetOptionString('main', 'new_user_registration', 'Y');
COption::SetOptionString('main', 'store_password', 'Y');
COption::SetOptionString('main', 'use_secure_password_cookies', 'Y');
COption::SetOptionString('main', 'auth_comp2', 'Y');
COption::SetOptionString('main', 'captcha_registration', 'Y');
COption::SetOptionString("fileman", "use_code_editor", "N");
COption::SetOptionString("iblock", "combined_list_mode", "Y");
COption::SetOptionString("iblock", "show_xml_id", "Y");
COption::SetOptionString("main", "optimize_css_files", "N");
COption::SetOptionString("main", "optimize_js_files", "N");
COption::SetOptionString("fileman","use_editor_3", "Y");