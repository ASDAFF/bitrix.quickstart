<?
Class drint_class
{
    function blockAndUp()
    {
        global $APPLICATION;
        if (IsModuleInstalled("drint.blockandarrow"))
        {
            if(!defined(ADMIN_SECTION) && ADMIN_SECTION!==true)
            {				
				$pos = COption::GetOptionString("drint_blockandarrow", "pos", "3");
				$pos_xy = COption::GetOptionString("drint_blockandarrow", "pos_xy", "10");
				$pos_yx = COption::GetOptionString("drint_blockandarrow", "pos_yx", "10");
				$jq = COption::GetOptionString("drint_blockandarrow", "enable_jquery", "Y");
				$link = COption::GetOptionString("drint_blockandarrow", "link", "");
				$type = COption::GetOptionString("drint_blockandarrow", "type", "0");
				$black = COption::GetOptionString("drint_blockandarrow", "backgound_black", "Y");
				$include_block = COption::GetOptionString("drint_blockandarrow", "include_block", "N");
				
				$up_pos = COption::GetOptionString("drint_blockandarrow", "up_pos", "3");
				$up_pos_xy = COption::GetOptionString("drint_blockandarrow", "up_pos_xy", "10");
				$up_pos_yx = COption::GetOptionString("drint_blockandarrow", "up_pos_yx", "10"); 
				$include_up = COption::GetOptionString("drint_blockandarrow", "include_up", "N");
				
				$url_img = COption::GetOptionString("drint_blockandarrow", "up_button", "/bitrix/images/drint.blockandarrow/top.png");
							
                $APPLICATION->AddHeadString("<script>
													url_img = '".$url_img."'; 
													black = '".$black."'; 
													type = '".$type."'; 
													link = '".$link."'; 
													pos ='".$pos."';
													pos_xy = '".$pos_xy."'; 
													pos_yx = '".$pos_yx."'
													up_pos ='".$up_pos."';
													up_pos_xy = '".$up_pos_xy."'; 
													up_pos_yx = '".$up_pos_yx."';
													include_block = '".$include_block."';
													include_up = '".$include_up."';
													</script>",true);
	
                if($jq == "Y")
                {
                    CUtil::InitJSCore(Array("jquery"));
                    $APPLICATION->AddHeadScript("/bitrix/js/drint.blockandarrow/script.js");
                }
                else
                {
                    $APPLICATION->AddHeadScript("/bitrix/js/drint.blockandarrow/script.js");
                }
                $APPLICATION->AddHeadString("<link href='/bitrix/js/drint.blockandarrow/style.css' type='text/css' rel='stylesheet' />",true);
            }
        }
    }
        
}
?>
