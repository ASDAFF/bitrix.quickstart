<?
class CSofteffect {
	function getPlatform($str) {
		$tmpArr = explode(' ', $str);
		if (strpos($tmpArr[count($tmpArr)-1], 'Mac')!==FALSE) {
			return 'mac';
		} elseif (strpos($tmpArr[count($tmpArr)-1], 'Win')!==FALSE) {
			return 'windows';
		} else {
			return false;
		}
	}
	
	
	function CODEvsID ($srt, $iblock, $doEl) {
		$section = trim($srt);
		
		if ($section=='') {
			return false;
		}
		$SECTION_ID=false;
		$arFilter = array('IBLOCK_ID'=>intval($iblock), 'ACTIVE'=>'Y');
		if (intval($section)>0) {
			$arFilter['ID']=intval($section);
		} else {
			$arFilter['CODE']=$section;
		}
		
		$tmpDbSec = CIBlockSection::GetList(array(), $arFilter);
		if ($tmpArSec = $tmpDbSec->GetNext()) {
			$SECTION_ID = $tmpArSec['ID'];
		} else {
			// если что - проверяем и элементы
			$tmpDbSec = CIBlockElement::GetList(array(), $arFilter);
			if ($tmpArSec = $tmpDbSec->GetNext()) {
				if ($doEl) {
					$SECTION_ID = $tmpArSec['ID'];
				} else {
					$SECTION_ID = $tmpArSec['IBLOCK_SECTION_ID'];
				}
			}
		}
		if ($SECTION_ID===false) {
			return FALSE;
		} else {
			return intval($SECTION_ID);
		}
	}
	
	//преобразование даты в нужный для счет вид
	function convertDate($date)
	{
	   $components = explode (" ", $date, 2);
	   $monthes    = array
	   (
	      'января', 
	      'февраля', 
	      'марта', 
	      'апреля', 
	      'мая', 
	      'июня', 
	      'июля', 
	      'августа', 
	      'сентября', 
	      'октября', 
	      'ноября', 
	      'декабря'
	   );
	   $date = explode ('.', $components[0], 3);
	   
	    if (strpos($date[0], '0')===0) {
			$date[0]=trim($date[0], '0');
		} else {
			$date[0]=trim($date[0]);
		}
	   
	   return ($date[0]." ".$monthes[((int)($date[1])-1)]." ".$date[2]." года");
	}
	
	// поиск картинки для товара
	function getPicPath ($elID, $returnID=fasle) {
		$picID=false;
		$picPath=false;
		$dbEl = CIBlockElement::GetList(array('SORT'=>'ASC'), array('ID'=>intval($elID)), FALSE, FALSE, array('IBLOCK_ID', 'ID', 'PREVIEW_PICTURE', 'IBLOCK_SECTION_ID'));
		if ($arEl = $dbEl->GetNext()) {
			if ($arEl['PREVIEW_PICTURE']) {
				$picID = $arEl['PREVIEW_PICTURE'];
				$picPath = CFile::GetPath($arEl['PREVIEW_PICTURE']);
			}
		}
		
		if (!$picID && !$picPath) {
			$dbSec = CIBlockSection::GetNavChain(FALSE, $arEl['IBLOCK_SECTION_ID']);
			while ($arSec = $dbSec->GetNext()) {
				if ($arSec['PICTURE']) {
					$picID = $arSec['PICTURE'];
					$picPath = CFile::GetPath($arSec['PICTURE']);
				}
			}
		}
		
		if ($picID && $picPath) { 
			if ($returnID) {
				return $picID;
			} else {
				return $picPath;
			}
		} else {
			return FALSE;
		}
	}
}
?>