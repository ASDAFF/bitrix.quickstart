<?
namespace ft;

Class CTPic
{
	/**
	 * Dir path
	 */
	const PATH = '/upload/ft/tpic/'; 
	
	
	/**
	 * Quality pictures
	 */
	const QUALITY = 100;
	
	/**
	 * Options (for check)
	 */
	protected static $_optionTipes = array(
		'portrait',
		'landscape',
		'auto',
		'crop', // middle - middle (x - y)
		// crop's types
		// left left - middle - right
		//	|
		// middle
		//	|
		// right
		'cropll', // left - left
		'cropml', // middle - left
		'croprl', // right - left
		'croprm', // right - middle
		'croprr', // right - right
		'cropmr', // middle - right
		'croplr', // left - right
		'croplm' // left - middle
	);
	
	
	protected static $_image;
	protected static $_width;
	protected static $_height;
	protected static $_imageResized;
	protected static $_pictureName;
	protected static $_extension;
	
	/**
	 * Get picture's path by params
	 *
	 * @return array
	 */
	protected function _getPath() {
		return array(
			'fullPath' => str_replace(
				array('\\', '//'), 
				array('/', '/'), 
				$_SERVER['DOCUMENT_ROOT'] . self::PATH . self::$_pictureName . self::$_extension
			),
			'shortPath' => str_replace(
				array('\\', '//'), 
				array('/', '/'), 
				self::PATH . self::$_pictureName . self::$_extension
			)
		);
	}
	
	/**
	 * Open picture
	 *
	 * @param string 
	 * @return img
	 */
	protected function _openImage($file) {
		
		// Get picture's extension
		//self::$_extension = strtolower(strrchr($file, '.'));

		switch(self::$_extension) {
			case '.jpg':
			case '.jpeg':
			
				$img = @imagecreatefromjpeg($file);
				break;
				
			case '.gif':
			
				$img = @imagecreatefromgif($file);
				break;
				
			case '.png':
			
				$img = @imagecreatefrompng($file);
				break;
				
			default:
			
				$img = false;
				
		}
		
		return $img;
	}

	
	/**
	 * Resize picture
	 * 
	 * @param int $picId
	 * @param string $option
	 * @param int $newWidth
	 * @param int $newHeight
	 * @param boll $returnArray
	 */
	public function resizeImage($picId, $option = 'auto', $newWidth, $newHeight, $returnArray = false) {
		
		if(empty($picId)) {
			return;
		}

		$option = strtolower($option);
		
		//$path = \CFile::GetPath($picId);
		$arImage = \CFile::_GetImgParams($picId);
		
		// get picture's src
		$path = $arImage['SRC'];
		// get picture's width and height
		self::$_width = $arImage['WIDTH'];
		self::$_height = $arImage['HEIGHT'];
		
		$newWidth = intval($newWidth);
		$newHeight = intval($newHeight);
		
		if(empty($path) || !in_array($option, self::$_optionTipes) || (empty($newWidth) && empty($newHeight))) {
			return;
		}
		
		if(empty($newWidth)) {
			$newWidth = $newHeight;
		}
		
		if(empty($newHeight)) {
			$newHeight = $newWidth;
		}
		
		// Get picture's extension
		self::$_extension = strtolower(strrchr($path, '.'));
		
		// Get optimal width and height by option
		$optionArray = self::_getDimensions($newWidth, $newHeight, $option, $picId);
		
		$optimalWidth  = intval($optionArray['optimalWidth']);
		$optimalHeight = intval($optionArray['optimalHeight']);
		
		// if file is exist then return
		$arPaths = self::_getPath();
		if(!file_exists($arPaths['fullPath'])) {
		
			self::$_image = self::_openImage($_SERVER['DOCUMENT_ROOT'] . $path);

			// Create black nigga $optimalWidth x $optimalHeight
			self::$_imageResized = imagecreatetruecolor($optimalWidth, $optimalHeight);

			self::_setTransparency();
			
			// Imposes a one image (self :: $ _ image) to another (self :: $ _ imageResized),
			// interpolating pixel values so as to decrease the size of the image does not decrease its clarity.
			imagecopyresampled(self::$_imageResized, self::$_image, 0, 0, 0, 0, $optimalWidth, $optimalHeight, self::$_width, self::$_height);
		}
		
		// Reduce picture (trim any excess)
		if (preg_match('/^(crop)/', $option)) {
			if(!file_exists($arPaths['fullPath'])) {
				self::_crop($optimalWidth, $optimalHeight, $newWidth, $newHeight, $option);
			}
			$optimalWidth = $newWidth;
			$optimalHeight = $newHeight;
		}
		
		if(!file_exists($arPaths['fullPath'])) {
			$src = self::_saveImage();
		} else {
			$src = $arPaths['shortPath'];
		}
		
		if($returnArray) {
			return array(
				'WIDTH' => $optimalWidth,
				'HEIGHT' => $optimalHeight,
				'SRC' => $src,
			);
		} else {
			return $src;
		}
	}
	
	/**
	 * Resize picture and return html block
	 * 
	 * @param int $picId
	 * @param string $option
	 * @param int $newWidth
	 * @param int $newHeight
	 * @param array $settings
	 */
	public function resizeImageBlock($picId, $option = 'auto', $newWidth, $newHeight, $settings = array()) {
		$pictureBlock = null;
		if(!$pictureBlock = self::resizeImage($picId, $option, $newWidth, $newHeight, true)) {
			return false;
		}
		
		if(isset($settings['CLASSES']) && is_array($settings['CLASSES'])) {
			foreach($settings['CLASSES'] as $key => $classes) {
				$settings['CLASSES'][$key] = trim($classes);
			}
		}
		
		// data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7
		//$pictureBlock = '<div class="ft-image-block"><img src="' . $pictureBlock['SRC'] . '" width="' . $pictureBlock['WIDTH'] . '" height="' . $pictureBlock['HEIGHT'] . '" class="ft-image"></div>';
		$pictureBlock = 
			'<div class="ft-image-block' . (!empty($settings['CLASSES']['DIV']) ? ' ' . $settings['CLASSES']['DIV'] : '') . '">
				<img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="' . $pictureBlock['SRC'] . '" width="' . $pictureBlock['WIDTH'] . '" height="' . $pictureBlock['HEIGHT'] . '" class="ft-image' . (!empty($settings['CLASSES']['IMG']) ? ' ' . $settings['CLASSES']['IMG'] : '') . '"' . (!empty($settings['ALT']) ? ' alt="' . $settings['ALT'] . '"' : '') . (!empty($settings['TITLE']) ? ' title="' . $settings['TITLE'] . '"' : '') . '>
			</div>';
		
		return $pictureBlock;
	}
	
	/**
	 * Play with transparency
	 */
	protected function _setTransparency() {
		
		$newImage = self::$_imageResized;
	
        $transparencyIndex = imagecolortransparent(self::$_imageResized);
		// white color
        $transparencyColor = array('red' => 255, 'green' => 255, 'blue' => 255);
       
        if($transparencyIndex >= 0) {
            $transparencyColor = imagecolorsforindex(self::$_imageResized, $transparencyIndex);  
		}
		
		imagealphablending(self::$_imageResized, false);
		
        imagesavealpha(self::$_imageResized, true);
		
        $transparencyIndex = imagecolorallocatealpha($newImage, $transparencyColor['red'], $transparencyColor['green'], $transparencyColor['blue'], 127);
		
        imagefill($newImage, 0, 0, $transparencyIndex);
		
        imagecolortransparent($newImage, $transparencyIndex);
		
		self::$_imageResized = $newImage;
	}
	
	/**
	 * Calculates optimum width/height depending on the options and generates a picture name
	 *
	 * @param int $newWidth
	 * @param int $newHeight
	 * @param string $option
	 * @param ind $picId
	 *
	 * @return array
	 */
	protected function _getDimensions($newWidth, $newHeight, $option, $picId) {
		
		switch ($option) {				
			case 'portrait':
			
				if(self::$_height < $newHeight) {
				
					$optimalWidth = self::$_width;
					$optimalHeight = self::$_height;
					
				} else {
				
					$optimalWidth = self::_getSizeByFixedHeight($newHeight);
					$optimalHeight = $newHeight;
					
				}
				
				self::$_pictureName = $picId . 'h' . $newHeight . $option;
				break;
				
			case 'landscape':
			
				if(self::$_width < $newWidth) {
				
					$optimalWidth = self::$_width;
					$optimalHeight = self::$_height;
					
				} else {
				
					$optimalWidth = $newWidth;
					$optimalHeight= self::_getSizeByFixedWidth($newWidth);
				}
				
				self::$_pictureName = $picId . 'w' . $newWidth . $option;
				break;
				
			case 'auto':
			
				if(self::$_width < $newWidth && self::$_height < $newHeight) {
				
					$optimalWidth = self::$_width;
					$optimalHeight = self::$_height;
					
				} else {
				
					$optionArray = self::_getSizeByAuto($newWidth, $newHeight);
					$optimalWidth = intval($optionArray['optimalWidth']);
					$optimalHeight = intval($optionArray['optimalHeight']);
					
				}
				
				self::$_pictureName = $picId . 'w' . $newWidth . 'h' . $newHeight . $option;
				break;
				
			case 'crop':
			case 'cropll': // left - left				
			case 'cropml': // middle - left
			case 'croprl': // right - left
			case 'croprm': // right - middle
			case 'croprr': // right - right
			case 'cropmr': // middle - right
			case 'croplr': // left - right
			case 'croplm': // left - middle
			
				$optionArray = self::_getOptimalCrop($newWidth, $newHeight);
				$optimalWidth = intval($optionArray['optimalWidth']);
				$optimalHeight = intval($optionArray['optimalHeight']);
				
				self::$_pictureName = $picId . 'w' . $newWidth . 'h' . $newHeight . $option;
				break;
				
			
		}
		
		return array(
			'optimalWidth' => $optimalWidth, 
			'optimalHeight' => $optimalHeight
		);
	}
	
	/**
	 * Gets the new width for new heights
	 *
	 * @param int $newHeight
	 * @return int
	 */
	protected function _getSizeByFixedHeight($newHeight)
	{
		$ratio = self::$_width / self::$_height;
		$newWidth = intval($newHeight * $ratio);
		return $newWidth;
	}
	
	/**
	 * Gets a new height for the new width
	 *
	 * @param int $newWidth
	 * @return int
	 */
	protected function _getSizeByFixedWidth($newWidth) {
		$ratio = self::$_height / self::$_width;
		$newHeight = intval($newWidth * $ratio);
		return $newHeight;
	}
	
	/**
	 * Automatic detection method for reducing picture
	 *
	 * @param int $newWidth
	 * @param int $newHeight
	 * @return array
	 */
	protected function _getSizeByAuto($newWidth, $newHeight)
	{
		if (self::$_height < self::$_width) {
		
			$optimalWidth = $newWidth;
			$optimalHeight = self::_getSizeByFixedWidth($newWidth);
			
		} elseif (self::$_height > self::$_width) {
		
			$optimalWidth = self::_getSizeByFixedHeight($newHeight);
			$optimalHeight = $newHeight;
			
		} else { //??
		
			if ($newHeight < $newWidth) {
			
				$optimalWidth = $newHeight;
				$optimalHeight= self::_getSizeByFixedWidth($newHeight);
				
			} else if ($newHeight > $newWidth) {
			
				$optimalWidth = self::_getSizeByFixedHeight($newWidth);
				$optimalHeight= $newWidth;
				
			} else {
			
				$optimalWidth = $newWidth;
				$optimalHeight = $newHeight;
				
			}
		}
		
		if($optimalWidth > $newWidth) {
		
			$ratio = $newWidth / $optimalWidth;
			$optimalWidth = round($optimalWidth * $ratio);
			$optimalHeight = round($optimalHeight * $ratio);
			
		}
		
		if($optimalHeight > $newHeight) {
			$ratio = $newHeight / $optimalHeight;
			$optimalHeight = round($optimalHeight * $ratio);
			$optimalWidth = round($optimalWidth * $ratio);
		}
		
		return array(
			'optimalWidth' => $optimalWidth, 
			'optimalHeight' => $optimalHeight
		);
	}
	
	/**
	 * Reducing the pictures on the minimum ratio length_side/new_length_side
	 *
	 * @param int $newWidth
	 * @param int $newHeight
	 * @return array
	 */
	protected function _getOptimalCrop($newWidth, $newHeight) {

		$heightRatio = self::$_height / $newHeight;
		$widthRatio  = self::$_width / $newWidth;

		if ($heightRatio < $widthRatio) {
			$optimalRatio = $heightRatio;
		} else {
			$optimalRatio = $widthRatio;
		}

		$optimalHeight = ceil(self::$_height / $optimalRatio);
		$optimalWidth  = ceil(self::$_width / $optimalRatio);
		return array(
			'optimalWidth' => $optimalWidth, 
			'optimalHeight' => $optimalHeight
		);
	}

	/**
	 * Reduction of the resulting pictures by cutting excess
	 *
	 * @param int $optimalWidth
	 * @param int $optimalHeight
	 * @param int $newWidth
	 * @param int $newHeight
	 */
	protected function _crop($optimalWidth, $optimalHeight, $newWidth, $newHeight, $cropType) {
		
		switch($cropType) {
			case 'cropll': // left - left
				$cropStartX = 0;
				$cropStartY = 0;
				break;
				
			case 'cropml': // middle - left
				$cropStartX = ($optimalWidth / 2) - ($newWidth / 2);
				$cropStartY = 0;
				break;
				
			case 'croprl': // right - left
				$cropStartX = $optimalWidth - $newWidth;
				$cropStartY = 0;
				break;
				
			case 'croprm': // right - middle
				$cropStartX = $optimalWidth - $newWidth;
				$cropStartY = ($optimalHeight / 2) - ($newHeight / 2);
				break;
				
			case 'croprr': // right - right
				$cropStartX = $optimalWidth - $newWidth;
				$cropStartY = $optimalHeight - $newHeight;
				break;
				
			case 'cropmr': // middle - right
				$cropStartX = ($optimalWidth / 2) - ($newWidth / 2);
				$cropStartY = $optimalHeight - $newHeight;
				break;
				
			case 'croplr': // left - right
				$cropStartX = 0;
				$cropStartY = $optimalHeight - $newHeight;
				break;
				
			case 'croplm': // left - middle
				$cropStartX = 0;
				$cropStartY = ($optimalHeight / 2) - ($newHeight / 2);
				break;
			
			default:
				$cropStartX = ($optimalWidth / 2) - ($newWidth / 2);
				$cropStartY = ($optimalHeight / 2) - ($newHeight / 2);
		}
		

		$crop = self::$_imageResized;

		self::$_imageResized = imagecreatetruecolor($newWidth , $newHeight);
		
		if(self::$_extension == '.png') {
			self::_setTransparency();
		}
		
		imagecopyresampled(self::$_imageResized, $crop , 0, 0, $cropStartX, $cropStartY, $newWidth, $newHeight , $newWidth, $newHeight);
	}

	/**
	 * Saving picture
	 *
	 * @return string
	 */
	protected function _saveImage() {
		
		$savePath = self::_getPath();
		
		CheckDirPath($savePath['fullPath']);

		switch(self::$_extension)
		{
			case '.jpg':
			case '.jpeg':
			
				if (imagetypes() & IMG_JPG) {
					imagejpeg(self::$_imageResized, $savePath['fullPath'], self::QUALITY);
				}
				break;

			case '.gif':
				if (imagetypes() & IMG_GIF) {
					imagegif(self::$_imageResized, $savePath['fullPath']);
				}
				break;

			case '.png':
				
				// Calculates the degree of compression of png pictures depending on the desired quality of images
				// 0 - no compression (better quality), 9 - max. compression ratio
				$pngQuality = 9 - round((self::QUALITY/100) * 9);

				if (imagetypes() & IMG_PNG) {
					imagepng(self::$_imageResized, $savePath['fullPath'], $pngQuality);
				}
				break;

			default:

		}

		imagedestroy(self::$_imageResized);
		
		return $savePath['shortPath'];
	}
	
	
	/**
	 * Translation of bytes in Kb, Mb, etc.
	 *
	 * @param int $size
	 * @return int
	 */
	public function getShotSize($size) {
		
		$stepArray = array(
			'b',
			'Kb',
			'Mb',
			'Gb',
			'Tb'
		);
		$step = 0;
		while($size >= 1024) {
			$size = $size/1024;
			$step++;
		}
		
		return round($size) . ' ' . $stepArray[$step];
	}
	
	
	/**
	 * Counting the total number of files in a folder, upload / tpic /
	 * Counting the total size of the files
	 * Calculation of number of irrelevant files
	 * Calculating the size of irrelevant files
	 * Selection of irrelevant files
	 *
	 * @return array
	 */
	
	public function getTPicInfo() {
		global $DB;
		$dirPath = $_SERVER['DOCUMENT_ROOT'] . self::PATH;
		$fileIds = array();
		$obj = $DB->Query('SELECT `ID` from `b_file` WHERE `CONTENT_TYPE` LIKE \'image%\' ORDER BY `ID` ASC;');
		while($file = $obj->Fetch()) {
			$fileIds[] = $file['ID'];
		}

		$allCount = 0;
		$allSize = 0;
		
		$notActualCount = 0;
		$notActualSize = 0;
		$notActualFiles = array();
		
		$dirHandler = opendir($dirPath);
		while($file = readdir($dirHandler)) {
			
			if($file == '.' || $file == '..') {
				continue;
			}
			
			$allSize += filesize($dirPath.$file);
			$allCount++;
			
			$matches = array();
			
			$find = preg_match('/([0-9]+)[w|h].+/', $file, $matches);

			if(!$find || !in_array($matches[1], $fileIds)) {
				$notActualFiles[] = $file;
				$notActualSize += filesize($dirPath.$file);
				$notActualCount++;
			}
			
		}
		
		return array(
			'allCount' => $allCount,
			'allSize' => self::getShotSize($allSize),
			'notActualCount' => $notActualCount,
			'notActualSize' => self::getShotSize($notActualSize),
			'notActualFiles' => $notActualFiles
		);
	}
	
	
	/**
	 * Delete pictures
	 *
	 * @param array $filesArray
	 */
	public function deleteTPicFiles($filesArray = array()) {
		$dirPath = $_SERVER['DOCUMENT_ROOT'] . self::PATH;
		$dirHandler = opendir($dirPath);
		while($file = readdir($dirHandler)) {
			
			if($file == '.' || $file == '..') {
				continue;
			}

			if(!empty($filesArray) && !in_array($file, $filesArray)) {
				continue;
			}

			
			unlink($dirPath.$file);
			
		}
		
		// Clear all cache after delete
		if(empty($filesArray)) {
			BXClearCache(true, '/');
		}
	}
	
}
?>