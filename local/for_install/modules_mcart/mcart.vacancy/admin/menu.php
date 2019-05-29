<?

IncludeModuleLangFile(__FILE__);
if($APPLICATION->GetGroupRight("mcart.vacancy")!="D"){
    $aMenu = array(
        "parent_menu" => "global_menu_services",
        "section" => "mcart.vacancy",
        "sort" => 10,
        "text" => GetMessage("VACANCY_TITLE"),
        "title" => GetMessage("VACANCY_TITLE"),
        "url" => "export_vacancies.php?lang=".LANGUAGE_ID,          
        "icon" => "mcart_vacancy_menu_icon",
        "page_icon" => "mcart_vacancy_page_icon",
        "items_id" => "menu_mcartvacancy",
		
    );
    return $aMenu;
	
}
return false;


?>