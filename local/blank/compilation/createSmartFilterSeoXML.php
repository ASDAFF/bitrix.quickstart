<?php 
http://failoverconf.ru/conf2016/
CAgent::AddAgent("createSmartFilterSeoXML();");
function createSmartFilterSeoXML(){
	 if(CModule::IncludeModule("iblock")){
	 		
		 $resElDB = CIBlockElement::GetList( Array("SORT"=>"ASC"), Array( 'IBLOCK_ID'=>SEO_IBLOCK ), false, false, Array('PROPERTY_TARGET_ON', 'PROPERTY_INDEX', 'PROPERTY_FOLLOW', 'IBLOCK_ID', 'ID', 'NAME') );
		 $protocol = (CMain::IsHTTPS() ? "https" : "http");
		 $host = $_SERVER['HTTP_HOST'];
	     if($_SERVER['SERVER_PORT'] <> 80 && $_SERVER['SERVER_PORT'] <> 443 && $_SERVER['SERVER_PORT'] > 0 && strpos($_SERVER['HTTP_HOST'], ":") === false){
	       $host .= ":".$_SERVER['SERVER_PORT'];
	     }
		 $curDate = date("Y-m-d\TH:i:s P");			 
		 $strBeginSmartFilter = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
		 
		 $index = 0;
		 while ( $res = $resElDB->fetch() ) {			 	 				
			$exp = explode('*', $res['PROPERTY_TARGET_ON_VALUE']['TEXT']);
			$urlFilter = trim($exp[0]); //clean url of smart filter
			
			if($index == 0){
				$strBeginSmartFilter .= "\t<url>\n\t\t";
				$index ++; 
			}else{
				$strBeginSmartFilter .= "<url>\n\t\t";	
			}							
				$strBeginSmartFilter .= "<loc>".$protocol."://".$host.$urlFilter ."</loc>\n\t\t";
				$strBeginSmartFilter .= "<lastmod>" . $curDate . "</lastmod>\n\t";
			$strBeginSmartFilter .= "</url>";
		 }
		 $strBeginSmartFilter .="</urlset\n>";
		 $smartXmlFileName = 'sitemap_iblock_' . SEO_IBLOCK . '.xml';
		
		$el = fopen($_SERVER['DOCUMENT_ROOT'].'/'.$smartXmlFileName, "w");
		fwrite($el, $strBeginSmartFilter);
		fclose($el);
	
		return 	"createSmartFilterSeoXML();";
	}
}
?>
