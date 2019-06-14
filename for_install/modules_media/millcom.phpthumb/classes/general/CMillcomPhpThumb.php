<?php
class CMillcomPhpThumb
{
	static function generateImg($FILE_ID = '', $arParams = array(), $CLEAR_CACHE = false) {
		global $APPLICATION;                          
		if(!is_numeric($arParams) && !is_array($arParams)) {
			return 'Error: '.__LINE__;
		}

    $filePath = 'upload/phpthumb/';

		if(is_numeric($arParams) && ($arParams > 0)) { 
			$filePath .= $arParams.'/';
			$arParams = CMillcomPhpThumbTemplates::GetOptionsByID($arParams, $CLEAR_CACHE);
		} else {
			$filePath .= '0/';
		}

		if(is_numeric($FILE_ID)) {
			$FILE_URL = $_SERVER['DOCUMENT_ROOT'] . '/' . CFile::GetPath($FILE_ID);
			//$filePath .= $FILE_ID.'/';
			$FILE_PREFIX = $FILE_ID.'_';
		} else {
			$FILE_URL = $_SERVER['DOCUMENT_ROOT'] . '/' . trim($FILE_ID,'/');
			$FILE_PREFIX = '';
		}

		if(!file_exists($FILE_URL) || !is_file($FILE_URL)){
			$FILE_URL = $_SERVER['DOCUMENT_ROOT'] . COption::GetOptionString("millcom.phpthumb", "noimage_file");
		}

		if(!isset($arParams['f'])) $arParams['f'] = 'jpg';

		$fileName = $FILE_PREFIX.md5(serialize(array(
			'FILE_URL' => $FILE_URL,
			'arParams' => $arParams
		))).'.'.$arParams['f'];

    $outputFilename = $_SERVER['DOCUMENT_ROOT'].'/'.$filePath.$fileName;

		if (!file_exists($outputFilename)) {

			$tmp = explode("/",$filePath);

			$folder = $_SERVER['DOCUMENT_ROOT'].'/';	

			for($i=0;$i<count($tmp);$i++){
				if ($tmp[$i]=='') continue;
				$folder .= $tmp[$i];
				if(!is_dir($folder)) mkdir($folder);
				$folder .= '/';
			}		
		
			$phpThumb = new phpthumb();
			$phpThumb->setSourceFilename($FILE_URL);

			foreach($arParams as $key => $value) {
				$phpThumb->setParameter($key, $value);
			}

		  if ($phpThumb->GenerateThumbnail()) {
			  $phpThumb->RenderToFile($outputFilename);
		  }
			
		}


		return '/'.$filePath.$fileName;	
	}
}
?>