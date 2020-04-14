<?php
namespace Helper\Media;

/**
 * ImageResizeFilter
 * Класс реализующий расширенные фильтры для метода CFile::ResizeImageGet Битрикса.
 * Нужно зарегистрировать обработчик в init.php
 * <code>
 * AddEventHandler('main', 'OnAfterResizeImage', Array('ImageResizeFilter', 'add'));
 * </code> 
 * 
 * @package laraver_server
 * @author TheRabbit
 * @copyright 2015
 * @version $Id$
 * @access public
 */
class ImageResizeFilter {
	
	private static $strFile = null;
	
	/**
	 * ImageResizeFilter::add()
	 * ОбрабоФтит события OnAfterResizeImage - добавляет к пережатому изобрадению фильтр
	 * 
	 * @param mixed $arrFile
	 * @param mixed $arrParams
	 * @param mixed $callbackData
	 * @param mixed $strCacheImageFile
	 * @param mixed $strCacheImageFileTmp
	 * @param mixed $arrImageSize
	 * @return void
	 */
	public static function add($arrFile, $arrParams, $callbackData, $strCacheImageFile, $strCacheImageFileTmp, $arrImageSize) {
		
		$arrSize = $arrParams[0];
		$arrFilters = $arrParams[4];
		
		if (file_exists($strCacheImageFileTmp)) {
			
			self::$strFile = $strCacheImageFileTmp;
			
			// Фильтр наложения теста с поддержкой прозрачности и наклона
			if (isset($arrFilters['irf_text']) && is_array($arrFilters['irf_text']))
				self::text($arrFilters['irf_text']);

		}//\\ if
		
		return false;
	}//\\ add
	
	/**
	 * ImageResizeFilter::text()
	 * Фильтр - накладывает полупрозрачный текст по диагонали.
	 * 
	 * @param mixed $arrParams
	 * @return void
	 */
	private static function text($arrParams) {

		$objImg = imagecreatefromjpeg(self::$strFile);	
		
	    //получаем ширину и высоту исходного изображения
	    $intWidth = imagesx($objImg);
	    $intHeight = imagesy($objImg);
	    
	    //угол поворота текста
	    $intAngle =  -rad2deg(atan2((-$intHeight),($intWidth))); 

	    //добавляем пробелы к строке
	    $strText = ' '.$arrParams['text'].' ';
	 
	    $intColor = imagecolorallocatealpha($objImg, $arrParams['red'], $arrParams['green'], $arrParams['blue'], $arrParams['alpha']);
	    
	    $intSize = (($intWidth + $intHeight) / 2) * 2 / strlen($strText);
	    $arrBox  = imagettfbbox($intSize, $intAngle, $arrParams['font'], $strText);
	    $intX = $intWidth / 2 - abs($arrBox[4] - $arrBox[0]) / 2;
	    $intY = $intHeight / 2 + abs($arrBox[5] - $arrBox[1]) / 2;
	 
	    //записываем строку на изображение
	    imagettftext($objImg, $intSize ,$intAngle, $intX, $intY, $intColor, $arrParams['font'], $strText);
	    
	    imagejpeg($objImg, self::$strFile, 100);
	    
	    imagedestroy($objImg);
		
	}//\\ text
	
}//\\ ImageResizeFilter