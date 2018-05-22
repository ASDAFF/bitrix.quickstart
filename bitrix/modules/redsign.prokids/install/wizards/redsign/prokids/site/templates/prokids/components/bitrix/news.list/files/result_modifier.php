<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if( !CModule::IncludeModule('iblock') || $arParams['PROP_CODE_FILE']=='' )
	return;

$sPropCode = $arParams['PROP_CODE_FILE'];
foreach($arResult["ITEMS"] as $key1 => $arItem)
{
	if( is_array($arItem['PROPERTIES'][$sPropCode]['VALUE']) )
	{
		foreach($arItem['PROPERTIES'][$sPropCode]['VALUE'] as $keyF => $fileID)
		{
			$rsFile = CFile::GetByID($fileID);
			if($arFile = $rsFile->Fetch())
			{
				$arResult["ITEMS"][$key1]['PROPERTIES'][$sPropCode]['VALUE'][$keyF] = $arFile;
				$arResult["ITEMS"][$key1]['PROPERTIES'][$sPropCode]['VALUE'][$keyF]['FULL_PATH'] = '/upload/'.$arFile['SUBDIR'].'/'.$arFile['FILE_NAME'];
				$tmp = explode('.', $arFile['FILE_NAME']);
				$tmp = end($tmp);
				$type = 'other';
				$type2 = '';
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
				$arResult["ITEMS"][$key1]['PROPERTIES'][$sPropCode]['VALUE'][$keyF]['TYPE'] = $type;
				$arResult["ITEMS"][$key1]['PROPERTIES'][$sPropCode]['VALUE'][$keyF]['SIZE'] = CFile::FormatSize($arFile['FILE_SIZE'],1);
			}
		}
	} else {
		$fileID = $arItem['PROPERTIES'][$sPropCode]['VALUE'];
		$rsFile = CFile::GetByID($fileID);
		if($arFile = $rsFile->Fetch())
		{
			$arResult["ITEMS"][$key1]['PROPERTIES'][$sPropCode]['VALUE'] = array();
			$arResult["ITEMS"][$key1]['PROPERTIES'][$sPropCode]['VALUE'][0] = $arFile;
			$arResult["ITEMS"][$key1]['PROPERTIES'][$sPropCode]['VALUE'][0]['FULL_PATH'] = '/upload/'.$arFile['SUBDIR'].'/'.$arFile['FILE_NAME'];
			$tmp = explode('.', $arFile['FILE_NAME']);
			$tmp = end($tmp);
			$type = 'other';
			$type2 = '';
			switch($tmp){
				case 'docx':
					$type = 'doc';
					break;
				case 'doc':
					$type = 'doc';
					break;
				case 'pdf':
					$type = 'pdf';
					break;
				case 'xml':
					$type = 'excel';
					break;
				case 'xlsx':
					$type = 'excel';
					break;
			}
			$arResult["ITEMS"][$key1]['PROPERTIES'][$sPropCode]['VALUE'][0]['TYPE'] = $type;
			$arResult["ITEMS"][$key1]['PROPERTIES'][$sPropCode]['VALUE'][0]['SIZE'] = CFile::FormatSize($arFile['FILE_SIZE'],1);
		}
	}
}