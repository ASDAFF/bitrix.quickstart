<?
IncludeModuleLangFile(__FILE__);

Class CSlobelConnectjq
{
	const MODULE_ID="slobel.connectjq";
	private static $cdn;
	private static $slobelCompressMin;
	
        function JQOnBeforeEndBufferContent()
        {
               global $APPLICATION;
               if (IsModuleInstalled("slobel.connectjq"))
               {
                        if(!defined(ADMIN_SECTION) && ADMIN_SECTION!==true)
                        {
                        	$slobelDir = $APPLICATION->GetCurDir();
                        	if (substr($slobelDir, 0, 8) == "/bitrix/") return false;
                        	
                          	if(trim(COption::GetOptionString("slobel_connectjs", "slobel-compress", ""))=='minified')self::$slobelCompressMin='.min';
                          	
                          	self::$cdn=trim(COption::GetOptionString("slobel_connectjs", "slobel-file", ""));
                          	
                            // js
                            if(trim(COption::GetOptionString("slobel_connectjs", "slobel-jquery", ""))=='Y')
                            	$arSrcJavaScripts[]=self::getFiles("http://code.jquery.com/","","jqcore","jquery-".trim(COption::GetOptionString("slobel_connectjs", "slobel-jquery-ver", "")), 'js');
                            
                            if(trim(COption::GetOptionString("slobel_connectjs", "slobel-jquery-migrate", ""))=='Y')
                            	$arSrcJavaScripts[]=self::getFiles("http://code.jquery.com/","","jqmigrate","jquery-migrate-".trim(COption::GetOptionString("slobel_connectjs", "slobel-jquery-migrate-ver", "")), 'js');
                            
                            if(trim(COption::GetOptionString("slobel_connectjs", "slobel-jquery-ui", ""))=='Y')
                            	$arSrcJavaScripts[]=self::getFiles("http://code.jquery.com/ui/",trim(COption::GetOptionString("slobel_connectjs", "slobel-jquery-ui-ver", ""))."/","jqui/".trim(COption::GetOptionString("slobel_connectjs", "slobel-jquery-ui-ver", "")),"jquery-ui", 'js');

                            if(trim(COption::GetOptionString("slobel_connectjs", "slobel-jquery-mobile", ""))=='Y')
                            	$arSrcJavaScripts[]=self::getFiles("http://code.jquery.com/mobile/",trim(COption::GetOptionString("slobel_connectjs", "slobel-jquery-mobile-ver", ""))."/","jqmobile/".trim(COption::GetOptionString("slobel_connectjs", "slobel-jquery-mobile-ver", "")),"jquery.mobile-".trim(COption::GetOptionString("slobel_connectjs", "slobel-jquery-mobile-ver", "")), 'js');
                            
                            if(trim(COption::GetOptionString("slobel_connectjs", "slobel-jquery-touch-punch", ""))=='Y')
                            	$arSrcJavaScripts[]=self::getFiles("http://rawgithub.com/","furf/jquery-ui-touch-punch/master/","jqtouch","jquery.ui.touch-punch", 'js');
                            
                            
                            if(trim(COption::GetOptionString("slobel_connectjs", "slobel-jquery-color", ""))=='Y')
                            	$arSrcJavaScripts[]=self::getFiles("http://code.jquery.com/color/","","jqcolor","jquery.color-".trim(COption::GetOptionString("slobel_connectjs", "slobel-jquery-color-ver", "")), 'js');
                            
                            if(trim(COption::GetOptionString("slobel_connectjs", "slobel-jquery-color-svg", ""))=='Y')
                            	$arSrcJavaScripts[]=self::getFiles("http://code.jquery.com/color/","","jqcolor","jquery.color.svg-names-".trim(COption::GetOptionString("slobel_connectjs", "slobel-jquery-color-ver", "")), 'js');
                            
                            if(trim(COption::GetOptionString("slobel_connectjs", "slobel-qunit", ""))=='Y')
                            	$arSrcJavaScripts[]=self::getFiles("http://code.jquery.com/qunit/","","qunit","qunit-".trim(COption::GetOptionString("slobel_connectjs", "slobel-qunit-ver", "")), 'js', true);
                            
                            foreach($arSrcJavaScripts as $slobelKey => $slobelVal){
                            	CSlobelConnectjq::addScriptOnSite($slobelVal);
                            	$slobelReturn=true;
                            }
                            
                            // theme
                            if(trim(COption::GetOptionString("slobel_connectjs", "slobel-jquery-ui-theme", ""))!='-')
                            	$arSrcCss[]=self::getFiles("http://code.jquery.com/ui/",trim(COption::GetOptionString("slobel_connectjs", "slobel-jquery-ui-ver", ""))."/themes/".trim(COption::GetOptionString("slobel_connectjs", "slobel-jquery-ui-theme", ""))."/","jqui/".trim(COption::GetOptionString("slobel_connectjs", "slobel-jquery-ui-ver", ""))."/theme/".trim(COption::GetOptionString("slobel_connectjs", "slobel-jquery-ui-theme", "")),"jquery-ui", 'css');
                           
                            
                            if(trim(COption::GetOptionString("slobel_connectjs", "slobel-jquery-mobile-theme", ""))!='-'){
                            	if(trim(COption::GetOptionString("slobel_connectjs", "slobel-jquery-mobile-theme", ""))=='general')
                            		$arSrcCss[]=self::getFiles("http://code.jquery.com/mobile/",trim(COption::GetOptionString("slobel_connectjs", "slobel-jquery-mobile-ver", ""))."/","jqmobile/theme/".trim(COption::GetOptionString("slobel_connectjs", "slobel-jquery-mobile-ver", "")),"jquery.mobile-".trim(COption::GetOptionString("slobel_connectjs", "slobel-jquery-mobile-ver", "")).'.min', 'css', true);
                            	else
                            		$arSrcCss[]=self::getFiles("http://code.jquery.com/mobile/",trim(COption::GetOptionString("slobel_connectjs", "slobel-jquery-mobile-ver", ""))."/","jqmobile/theme/".trim(COption::GetOptionString("slobel_connectjs", "slobel-jquery-mobile-ver", "")),"jquery.mobile.structure-".trim(COption::GetOptionString("slobel_connectjs", "slobel-jquery-mobile-ver", "")).'.min', 'css', true);
                            }
                            
                            if(trim(COption::GetOptionString("slobel_connectjs", "slobel-qunit-theme", ""))!='-')
                            	$arSrcCss[]=self::getFiles("http://code.jquery.com/qunit/","","qunit/theme/".trim(COption::GetOptionString("slobel_connectjs", "slobel-qunit-ver", "")),"qunit-".trim(COption::GetOptionString("slobel_connectjs", "slobel-qunit-ver", "")), 'css', true);

                            foreach($arSrcCss as $slobelKey => $slobelVal){
                            	CSlobelConnectjq::addCssOnSite($slobelVal);
                            	$slobelReturn=true;
                            }

							if($slobelReturn) return true;
							else return false;
                               
                                        
                        
                       }
               }
        }
        
        function getFiles($url, $from="", $to, $name, $type, $noMin=false){
       
        	$path="/bitrix/js/".self::MODULE_ID."/".$to."/";
        	if(!$noMin) $min=self::$slobelCompressMin;
        	$myFile['url']=$url.$from.$name.$min.'.'.$type;
        	$myFile['local']=$path.$name.$min.'.'.$type;

        	if(self::$cdn!="Y" && !file_exists($_SERVER["DOCUMENT_ROOT"].$myFile['local'])){	
        		CheckDirPath($_SERVER["DOCUMENT_ROOT"].$path);
        		copy($myFile['url'], $_SERVER["DOCUMENT_ROOT"].$myFile['local']);
        		return $myFile['local'];
        	}
        	elseif(file_exists($_SERVER["DOCUMENT_ROOT"].$myFile['local'])){
        		return $myFile['local'];
        	}
        	else{
        		return $myFile['url'];
        	}
        }
        
        function addScriptOnSite($slobelJavaScripts)
        {
        	global $APPLICATION;
        	$APPLICATION->AddHeadScript($slobelJavaScripts);
        }
        
        function addCssOnSite($slobelCss)
        {
        	global $APPLICATION;
        	$APPLICATION->SetAdditionalCSS($slobelCss);
        }
}
?>