<?
CModule::IncludeModule("catalog");
CModule::IncludeModule("iblock");
CModule::IncludeModule("IargaShop");

if (!function_exists('mb_ucfirst') && function_exists('mb_substr')) { 
     function mb_ucfirst($string) {
		 $chs = (BX_UTF=='Y')?"UTF-8":'Windows-1251';
		 //return $string;
          $string = mb_ereg_replace("^[\ ]+","", $string);  
          $string = mb_strtoupper(mb_substr($string, 0, 1, $chs), $chs).mb_substr($string, 1, mb_strlen($string), $chs);  
          return $string;  
     }  
}


function pround($val){
	$por = pow(10,floor(pow($val, 1/10)));
	return round($val/$por)*$por;
}
?>