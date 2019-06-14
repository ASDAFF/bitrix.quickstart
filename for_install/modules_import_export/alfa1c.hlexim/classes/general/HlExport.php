<?
use \Bitrix\Main\Localization\Loc as Loc;
Loc::loadMessages(__FILE__);

Class HlExport{

	/*
	global $APPLICATION;

	$charset = (SITE_CHARSET) ? SITE_CHARSET : 'windows-1251'; 

	$this->hl_block_info_title = $APPLICATION->ConvertCharset(Loc::getMessage("CC_BCIH_XML_COM_INFO"), SITE_CHARSET, "UTF-8");
								
	*/

	public function writeArrayInFile($data, $filename){
	    $serArray = serialize($data);  
	    $file = fopen ($filename ,"a+"); 
	    $result = fputs($file, $serArray); 
	    fclose($file); 
	    return $result;
	}
	

	public function readFileInArray($fileName){
	    $file = fopen($fileName, 'r'); 
	    $str = "";
	    while (($buffer = fgets($file, 128)) !== false) {
	        $str .= $buffer;
	    }

	    $array = unserialize($str); 

	    return $array;
	}

	public function convertArray2anci(&$item, $key)
	{
	    $item = iconv('UTF-8', 'WINDOWS-1251', $item);
	}

	public function convertArray2utf8(&$item, $key)
	{
	    $item = iconv('WINDOWS-1251', 'UTF-8', $item);
	}

}

?>