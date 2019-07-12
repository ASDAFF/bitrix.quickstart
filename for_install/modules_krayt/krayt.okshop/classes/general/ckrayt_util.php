<?php
/**
 * Created by PhpStorm.
 * User: aleks
 * Date: 10.02.15
 * Time: 10:41
 */
IncludeModuleLangFile(__FILE__);
class CKraytUtilit {

    //загрузка выбора цветовой схемы, добавления кнопок в меню
    //управления цветовоми схемами 
    public static function LoadColorChem($post)
    {
        global $APPLICATION,$USER;
        
        if($USER->IsAdmin()):
        if(!empty($_REQUEST['k_color']))
        {
            $k_color = $_REQUEST['k_color'];
            if(    $k_color == 'blue'
                || $k_color == 'green'
        		|| $k_color == 'emerland'	
                || $k_color == "orange"
                || $k_color == "purple"
                || $k_color == "red"
                || $k_color == "violet"		
            )
            {
                
              COption::SetOptionString("krayt.okshop", "k_color", $k_color); 
                        
            }       
        }
        
        if(!empty($_REQUEST['k_template']))
        {
            $k_t = $_REQUEST['k_template'];
            if( $k_t == 1)
            {
             COption::SetOptionString("krayt.okshop", "k_layout", $k_t);
                        
            }elseif($k_t == 2 )
            {
             COption::SetOptionString("krayt.okshop", "k_layout", $k_t);
                
            }       
        }
    
        $APPLICATION->AddPanelButton(
                        Array(
                            "ID" => "krayt_color", //определяет уникальность кнопки
                            "TEXT" => GetMessage("K_MENU_COLOR"),
                            "TYPE" => "BIG", //BIG - большая кнопка, иначе маленькая
                            "MAIN_SORT" => 100, //индекс сортировки для групп кнопок
                            "SORT" => 10, //сортировка внутри группы
                            "HREF" => "", //или javascript:MyJSFunction())
                            "ICON" => "icon-class", //название CSS-класса с иконкой кнопки
                            "SRC" => SITE_TEMPLATE_PATH."/images/brush.png",                
                            "MENU" => Array(
                                            Array( //массив пунктов контекстного меню
                                                "TEXT" => GetMessage("K_BLUE_TEXT"),
                                                "TITLE" => GetMessage("K_BLUE_TITLE"),
                                                "SORT" => 10, //индекс сортировки пункта
                                                "ICON" => "blue-icon", //иконка пункта
                                                "ACTION" => "KraytOkShop.color('blue')",                           
                                                "DEFAULT" => true, //пункт по умолчанию?                
                                                ),
                                              Array( //массив пунктов контекстного меню
                                                "TEXT" => GetMessage("K_GREEN_TEXT"),
                                                "TITLE" => GetMessage("K_GREEN_TITLE"),
                                                "SORT" => 10, //индекс сортировки пункта
                                                "ICON" => "", //иконка пункта
                                                "ACTION" => "KraytOkShop.color('green')",                           
                                                               
                                                ),
                                                Array( //массив пунктов контекстного меню
                                                "TEXT" => GetMessage("K_IZU_TEXT"),
                                                "TITLE" => GetMessage("K_IZU_TITLE"),
                                                "SORT" => 10, //индекс сортировки пункта
                                                "ICON" => "", //иконка пункта
                                                "ACTION" => "KraytOkShop.color('emerland')",                           
                                                               
                                                ),
                                                Array( //массив пунктов контекстного меню
                                                "TEXT" => GetMessage("K_ORANGE_TEXT"),
                                                "TITLE" => GetMessage("K_ORANGE_TITLE"),
                                                "SORT" => 10, //индекс сортировки пункта
                                                "ICON" => "", //иконка пункта
                                                "ACTION" => "KraytOkShop.color('orange')",                           
                                                                
                                                ),
                                                Array( //массив пунктов контекстного меню
                                                "TEXT" => GetMessage("K_PUR_TEXT"),
                                                "TITLE" => GetMessage("K_PUR_TITLE"),
                                                "SORT" => 10, //индекс сортировки пункта
                                                "ICON" => "", //иконка пункта
                                                "ACTION" => "KraytOkShop.color('purple')",                           
                                                                
                                                ),
                                                Array( //массив пунктов контекстного меню
                                                "TEXT" => GetMessage("K_RED_TEXT"),
                                                "TITLE" =>  GetMessage("K_RED_TITLE"),
                                                "SORT" => 10, //индекс сортировки пункта
                                                "ICON" => "", //иконка пункта
                                                "ACTION" => "KraytOkShop.color('red')",                           
                                                               
                                                ),
                                                Array( //массив пунктов контекстного меню
                                                "TEXT" => GetMessage("K_FIOLET_TEXT"),
                                                "TITLE" => GetMessage("K_FIOLET_TITLE"),
                                                "SORT" => 10, //индекс сортировки пункта
                                                "ICON" => "", //иконка пункта
                                                "ACTION" => "KraytOkShop.color('violet')",                                                                                        
                                                ),     
                                )
                            )
                    
                    );	

        $APPLICATION->AddPanelButton(
                        Array(
                            "ID" => "krayt_layout", //определяет уникальность кнопки
                            "TEXT" => GetMessage("K_MENU_LAYOUT"),
                            "TYPE" => "BIG", //BIG - большая кнопка, иначе маленькая
                            "MAIN_SORT" => 100, //индекс сортировки для групп кнопок
                            "SORT" => 10, //сортировка внутри группы
                            "HREF" => "", //или javascript:MyJSFunction())
                            "ICON" => "icon-class", //название CSS-класса с иконкой кнопки
                            "SRC" => SITE_TEMPLATE_PATH."/images/layout.png",                
                            "MENU" => Array(
                                            Array( //массив пунктов контекстного меню
                                                "TEXT" => GetMessage("K_L2_TEXT"),
                                                "TITLE" => GetMessage("K_L2_TITLE"),
                                                "SORT" => 10, //индекс сортировки пункта
                                                "ICON" => "blue-icon", //иконка пункта
                                                "ACTION" => "KraytOkShop.layout(1)",                           
                                                "DEFAULT" => true, //пункт по умолчанию?                
                                                ),
                                              Array( //массив пунктов контекстного меню
                                                "TEXT" => GetMessage("K_L1_TEXT"),
                                                "TITLE" => GetMessage("K_L1_TITLE"),
                                                "SORT" => 10, //индекс сортировки пункта
                                                "ICON" => "", //иконка пункта
                                                "ACTION" => "KraytOkShop.layout(2)",                           
                                                               
                                                ),                                                
                                )
                            )
                    
                    );	
        endif;
        
    } 

} 

