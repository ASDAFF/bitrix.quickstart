<?php
/**
 * Created by PhpStorm.
 * User: aleks
 * Date: 10.02.15
 * Time: 10:41
 */
IncludeModuleLangFile(__FILE__);
class CKraytUtilit {

    //�������� ������ �������� �����, ���������� ������ � ����
    //���������� ��������� ������� 
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
                            "ID" => "krayt_color", //���������� ������������ ������
                            "TEXT" => GetMessage("K_MENU_COLOR"),
                            "TYPE" => "BIG", //BIG - ������� ������, ����� ���������
                            "MAIN_SORT" => 100, //������ ���������� ��� ����� ������
                            "SORT" => 10, //���������� ������ ������
                            "HREF" => "", //��� javascript:MyJSFunction())
                            "ICON" => "icon-class", //�������� CSS-������ � ������� ������
                            "SRC" => SITE_TEMPLATE_PATH."/images/brush.png",                
                            "MENU" => Array(
                                            Array( //������ ������� ������������ ����
                                                "TEXT" => GetMessage("K_BLUE_TEXT"),
                                                "TITLE" => GetMessage("K_BLUE_TITLE"),
                                                "SORT" => 10, //������ ���������� ������
                                                "ICON" => "blue-icon", //������ ������
                                                "ACTION" => "KraytOkShop.color('blue')",                           
                                                "DEFAULT" => true, //����� �� ���������?                
                                                ),
                                              Array( //������ ������� ������������ ����
                                                "TEXT" => GetMessage("K_GREEN_TEXT"),
                                                "TITLE" => GetMessage("K_GREEN_TITLE"),
                                                "SORT" => 10, //������ ���������� ������
                                                "ICON" => "", //������ ������
                                                "ACTION" => "KraytOkShop.color('green')",                           
                                                               
                                                ),
                                                Array( //������ ������� ������������ ����
                                                "TEXT" => GetMessage("K_IZU_TEXT"),
                                                "TITLE" => GetMessage("K_IZU_TITLE"),
                                                "SORT" => 10, //������ ���������� ������
                                                "ICON" => "", //������ ������
                                                "ACTION" => "KraytOkShop.color('emerland')",                           
                                                               
                                                ),
                                                Array( //������ ������� ������������ ����
                                                "TEXT" => GetMessage("K_ORANGE_TEXT"),
                                                "TITLE" => GetMessage("K_ORANGE_TITLE"),
                                                "SORT" => 10, //������ ���������� ������
                                                "ICON" => "", //������ ������
                                                "ACTION" => "KraytOkShop.color('orange')",                           
                                                                
                                                ),
                                                Array( //������ ������� ������������ ����
                                                "TEXT" => GetMessage("K_PUR_TEXT"),
                                                "TITLE" => GetMessage("K_PUR_TITLE"),
                                                "SORT" => 10, //������ ���������� ������
                                                "ICON" => "", //������ ������
                                                "ACTION" => "KraytOkShop.color('purple')",                           
                                                                
                                                ),
                                                Array( //������ ������� ������������ ����
                                                "TEXT" => GetMessage("K_RED_TEXT"),
                                                "TITLE" =>  GetMessage("K_RED_TITLE"),
                                                "SORT" => 10, //������ ���������� ������
                                                "ICON" => "", //������ ������
                                                "ACTION" => "KraytOkShop.color('red')",                           
                                                               
                                                ),
                                                Array( //������ ������� ������������ ����
                                                "TEXT" => GetMessage("K_FIOLET_TEXT"),
                                                "TITLE" => GetMessage("K_FIOLET_TITLE"),
                                                "SORT" => 10, //������ ���������� ������
                                                "ICON" => "", //������ ������
                                                "ACTION" => "KraytOkShop.color('violet')",                                                                                        
                                                ),     
                                )
                            )
                    
                    );	

        $APPLICATION->AddPanelButton(
                        Array(
                            "ID" => "krayt_layout", //���������� ������������ ������
                            "TEXT" => GetMessage("K_MENU_LAYOUT"),
                            "TYPE" => "BIG", //BIG - ������� ������, ����� ���������
                            "MAIN_SORT" => 100, //������ ���������� ��� ����� ������
                            "SORT" => 10, //���������� ������ ������
                            "HREF" => "", //��� javascript:MyJSFunction())
                            "ICON" => "icon-class", //�������� CSS-������ � ������� ������
                            "SRC" => SITE_TEMPLATE_PATH."/images/layout.png",                
                            "MENU" => Array(
                                            Array( //������ ������� ������������ ����
                                                "TEXT" => GetMessage("K_L2_TEXT"),
                                                "TITLE" => GetMessage("K_L2_TITLE"),
                                                "SORT" => 10, //������ ���������� ������
                                                "ICON" => "blue-icon", //������ ������
                                                "ACTION" => "KraytOkShop.layout(1)",                           
                                                "DEFAULT" => true, //����� �� ���������?                
                                                ),
                                              Array( //������ ������� ������������ ����
                                                "TEXT" => GetMessage("K_L1_TEXT"),
                                                "TITLE" => GetMessage("K_L1_TITLE"),
                                                "SORT" => 10, //������ ���������� ������
                                                "ICON" => "", //������ ������
                                                "ACTION" => "KraytOkShop.layout(2)",                           
                                                               
                                                ),                                                
                                )
                            )
                    
                    );	
        endif;
        
    } 

} 

