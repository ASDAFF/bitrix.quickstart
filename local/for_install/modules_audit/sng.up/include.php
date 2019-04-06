<?
#################################################
#   Developer: Semen Golikov                    #
#   Site: http://www.sng-it.ru                  #
#   E-mail: info@sng-it.ru                      #
#   Copyright (c) 2009-2014 Semen Golikov       #
#################################################

Class CSngUp 
{
	function AddScriptUp()
	{
		global $APPLICATION; 
		if(CModule::IncludeModule('sng.up'))
		{		
            if(!defined(ADMIN_SECTION) && ADMIN_SECTION!==true)
            {		
					//Take values
					if(COption::GetOptionString('sng.up', 'sng_up_jquery'.'_'.SITE_ID, 'Y')=='Y')
					{
						CUtil::InitJSCore(Array("jquery"));
					}			
					$sng_up_position=COption::GetOptionString('sng.up', 'sng_up_position'.'_'.SITE_ID, 'right');
					if($sng_up_position!='center')
					{
						$sng_up_position_indent_x=COption::GetOptionString('sng.up', 'sng_up_pos_x'.'_'.SITE_ID, '20');		
					}		
					$sng_up_position_indent_y=COption::GetOptionString('sng.up', 'sng_up_pos_y'.'_'.SITE_ID, '55');					
					$sng_up_button = COption::GetOptionString("sng.up", "sng_up_button".'_'.SITE_ID, "/bitrix/images/sng.up/up1.png");
					$sng_up_button_opacity=COption::GetOptionString('sng.up', 'sng_up_button_opacity'.'_'.SITE_ID,'1');	
					$APPLICATION->AddHeadString("<script>sng_up_button_opacity='".$sng_up_button_opacity."';sng_up_button_width='".$sng_up_button_width."';sng_up_button='".$sng_up_button."'; sng_up_position ='".$sng_up_position."';sng_up_position_indent_x = '".$sng_up_position_indent_x."';sng_up_position_indent_y = '".$sng_up_position_indent_y."'</script>",true);			
					$APPLICATION->AddHeadScript("/bitrix/js/sng.up/script-up.js");			
					$APPLICATION->AddHeadString("<link href='/bitrix/js/sng.up/style-up.css' type='text/css' rel='stylesheet' />",true);
			}		
		}
	}	
}
?>