<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if( !CModule::IncludeModule('iblock') || $arParams['RSFLYAWAY_PROP_FILE']=='' )
	return;

$sPropCode = $arParams['RSFLYAWAY_PROP_FILE'];

if( !function_exists('RSFLYAWAY_getFileInfo') ) {
	function RSFLYAWAY_getFileInfo($fileID) {
		$result = false;
		if( IntVal($fileID)>0 ) {
			$rsFile = CFile::GetByID($fileID);
			if($arFile = $rsFile->Fetch()) {
				$tmp = explode('.', $arFile['FILE_NAME']);
				$tmp = end($tmp);
				$type = 'other';
				switch($tmp){
					case 'docx':
						$type = 'word';
						break;
					case 'doc':
						$type = 'word';
						break;
					case 'pdf':
						$type = 'pdf';
						break;
					case 'xls':
						$type = 'excel';
						break;
					case 'xlsx':
						$type = 'excel';
						break;
				}
				$result = $arFile;
				$result['FULL_PATH'] = '/upload/'.$arFile['SUBDIR'].'/'.$arFile['FILE_NAME'];
				$result['TYPE'] = $type;
				$result['EXTENSION'] = $tmp;
				$result['SIZE'] = CFile::FormatSize($arFile['FILE_SIZE'],1);
			}
		}
		return $result;
	}
}

foreach($arResult["ITEMS"] as $key1 => $arItem) {
	if( is_array($arItem['PROPERTIES'][$sPropCode]['VALUE']) ) {
		foreach($arItem['PROPERTIES'][$sPropCode]['VALUE'] as $keyF => $fileID) {
			$arResult["ITEMS"][$key1]['PROPERTIES'][$sPropCode]['VALUE'][$keyF] = RSFLYAWAY_getFileInfo($fileID);
		}
	} else {
		$fileID = $arItem['PROPERTIES'][$sPropCode]['VALUE'];
		$arResult["ITEMS"][$key1]['PROPERTIES'][$sPropCode]['VALUE'] = array();
		$arResult["ITEMS"][$key1]['PROPERTIES'][$sPropCode]['VALUE'][0] = RSFLYAWAY_getFileInfo($fileID);
	}
}